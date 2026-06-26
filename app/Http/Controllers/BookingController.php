<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Booking;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create(string $caregiver_id)
    {
        $caregiver = Caregiver::with('user')->findOrFail($caregiver_id);
        $patients = Patient::where('user_id', Auth::id())->get();

        if($patients->isEmpty()) {
            return redirect()->route('patients.create')
                ->with('error', 'Silakan tambahkan data pasien terlebih dahulu sebelum memesan perawat.');
        }

        // AMBIL JADWAL SIBUK PERAWAT INI
        $busyBookings = Booking::where('caregiver_id', $caregiver_id)
            ->whereIn('status', ['pending', 'approved', 'paid', 'ongoing', 'waiting_confirmation'])
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->get();

        return view('bookings.create', compact('caregiver', 'patients', 'busyBookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'caregiver_id' => 'required|exists:caregivers,id',
            'patient_id' => 'required|exists:patients,id',
            'start_date' => 'required|date|after_or_equal:today',
            'total_days' => 'required|integer|min:1',
        ]);

        // CEK JADWAL BENTROK
        $newStartDate = Carbon::parse($request->start_date);
        // Tambahkan (int) agar teks "3" berubah jadi angka 3
        $newEndDate = $newStartDate->copy()->addDays((int) $request->total_days); 

        $activeBookings = Booking::where('caregiver_id', $request->caregiver_id)
            ->whereIn('status', ['pending', 'approved', 'paid', 'ongoing', 'waiting_confirmation'])
            ->get();

        foreach ($activeBookings as $b) {
            $existingStart = Carbon::parse($b->start_date);
            // Tambahkan (int) di sini juga untuk berjaga-jaga
            $existingEnd = $existingStart->copy()->addDays((int) $b->total_days);

            if ($newStartDate->lessThan($existingEnd) && $newEndDate->greaterThan($existingStart)) {
                return back()->with('error', 'Maaf, perawat ini sudah dipesan pada tanggal tersebut. Silakan pilih tanggal lain.');
            }
        }

        $caregiver = Caregiver::findOrFail($request->caregiver_id);
        $totalAmount = $caregiver->price_per_day * $request->total_days;

        Booking::create([
            'user_id' => Auth::id(),
            'caregiver_id' => $request->caregiver_id,
            'patient_id' => $request->patient_id,
            'start_date' => $request->start_date,
            'total_days' => $request->total_days,
            'snapshot_price' => $caregiver->price_per_day,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi dari caregiver.');
    }

    public function index()
    {
        $bookings = Booking::with(['caregiver.user', 'patient', 'review'])
                    ->where('user_id', Auth::id())
                    ->latest() 
                    ->get();
        return view('bookings.index', compact('bookings'));
    }

    public function incomingBookings()
    {
        $caregiver = Caregiver::where('user_id', Auth::id())->firstOrFail();
        $bookings = Booking::with(['user', 'patient'])
                    ->where('caregiver_id', $caregiver->id)
                    ->latest()
                    ->get();
        return view('caregivers.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $booking = Booking::findOrFail($id);

        if ($booking->caregiver_id !== Auth::user()->caregiver->id) abort(403);
        if ($booking->status !== 'pending') return back()->with('error', 'Status pesanan sudah berubah.');

        $booking->update(['status' => $request->status]);
        $pesan = $request->status === 'approved' ? 'Pesanan berhasil diterima!' : 'Pesanan telah ditolak.';
        return back()->with('success', $pesan);
    }

    public function payment(string $id)
    {
        $booking = Booking::with('patient')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) abort(403);
        if ($booking->status !== 'approved') return back()->with('error', 'Pesanan belum disetujui atau sudah dibayar.');

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'CARUNA-' . $booking->id . '-' . time(),
                'gross_amount' => $booking->total_amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        return view('bookings.payment', compact('booking', 'snapToken'));
    }

    public function paymentSuccess(string $id)
    {
        return redirect()->route('bookings.index')->with('success', 'Pembayaran sedang dikonfirmasi oleh sistem!');
    }

    public function startService(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->caregiver_id !== Auth::user()->caregiver->id) abort(403);
        if ($booking->status !== 'paid') return back()->with('error', 'Pesanan harus dibayar terlebih dahulu.');

        $booking->update(['status' => 'ongoing']);
        return back()->with('success', 'Layanan telah dimulai. Selamat bertugas!');
    }

    public function requestFinish(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->caregiver_id !== Auth::user()->caregiver->id) abort(403);
        if ($booking->status !== 'ongoing') return back()->with('error', 'Layanan belum dimulai.');

        $booking->update(['status' => 'waiting_confirmation']);
        return back()->with('success', 'Permintaan penyelesaian telah dikirim ke Klien.');
    }

    public function confirmFinish(Request $request, string $id)
    {
        $booking = Booking::with('caregiver')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) abort(403);
        if ($booking->status !== 'waiting_confirmation') return back()->with('error', 'Status pesanan tidak valid.');
        
        try {
            DB::transaction(function () use ($booking) {
                $commission = $booking->total_amount * 0.10;
                $netAmount = $booking->total_amount - $commission;

                $booking->update(['status' => 'completed']);

                $caregiver = Caregiver::where('id', $booking->caregiver_id)->lockForUpdate()->firstOrFail();
                $caregiver->balance += $netAmount;
                $caregiver->save();

                WalletTransaction::create([
                    'user_id' => $caregiver->user_id,
                    'type' => 'credit',
                    'amount' => $netAmount,
                    'description' => 'Pendapatan layanan pesanan #' . $booking->id,
                    'reference_id' => $booking->id
                ]);
            });

            return redirect()->route('bookings.index')->with('success', 'Layanan selesai! Saldo telah diteruskan ke perawat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}