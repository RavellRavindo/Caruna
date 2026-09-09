<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    public function create(string $caregiver_id)
    {
        $caregiver = Caregiver::with('user')->findOrFail($caregiver_id);

        if (! $caregiver->isVerified() || ! $caregiver->is_available) {
            return redirect()
                ->route('caregivers.index')
                ->with('error', 'Caregiver ini sedang tidak tersedia untuk dipesan.');
        }

        $patients = Patient::where('user_id', Auth::id())->get();

        if ($patients->isEmpty()) {
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
        $data = $request->validate([
            'caregiver_id' => [
                'required',
                Rule::exists('caregivers', 'id')->where(
                    fn ($query) => $query
                        ->where('verification_status', Caregiver::VERIFICATION_VERIFIED)
                        ->where('is_available', true),
                ),
            ],
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id')->where('user_id', Auth::id()),
            ],
            'service_address' => 'required|string|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'total_days' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {
            // Mengunci caregiver menyerialkan booking pada caregiver yang sama dan
            // memastikan statusnya tidak berubah saat request diproses.
            $caregiver = Caregiver::query()
                ->whereKey($data['caregiver_id'])
                ->where('verification_status', Caregiver::VERIFICATION_VERIFIED)
                ->where('is_available', true)
                ->lockForUpdate()
                ->first();

            if (! $caregiver) {
                throw ValidationException::withMessages([
                    'caregiver_id' => 'Caregiver ini sedang tidak tersedia untuk dipesan.',
                ]);
            }

            $newStartDate = Carbon::parse($data['start_date']);
            $newEndDate = $newStartDate->copy()->addDays((int) $data['total_days'] - 1);

            $activeBookings = Booking::query()
                ->where('caregiver_id', $caregiver->id)
                ->whereIn('status', ['pending', 'approved', 'paid', 'ongoing', 'waiting_confirmation'])
                ->lockForUpdate()
                ->get();

            foreach ($activeBookings as $activeBooking) {
                $existingStart = Carbon::parse($activeBooking->start_date);
                $existingEnd = $existingStart->copy()->addDays((int) $activeBooking->total_days);

                if ($newStartDate->lessThan($existingEnd) && $newEndDate->greaterThan($existingStart)) {
                    throw ValidationException::withMessages([
                        'start_date' => 'Maaf, perawat ini sudah dipesan pada tanggal tersebut. Silakan pilih tanggal lain.',
                    ]);
                }
            }

            $totalAmount = $caregiver->price_per_day * $data['total_days'];

            Booking::create([
                'user_id' => Auth::id(),
                'caregiver_id' => $caregiver->id,
                'patient_id' => $data['patient_id'],
                'service_address' => $data['service_address'],
                'start_date' => $data['start_date'],
                'total_days' => $data['total_days'],
                'snapshot_price' => $caregiver->price_per_day,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);
        });

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi dari caregiver.');
    }

    public function index()
    {
        $bookings = Booking::with([
            'caregiver.user',
            'patient',
            'review',
            'payments' => fn ($query) => $query->latest('id'),
        ])
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

        if ($booking->caregiver_id !== Auth::user()->caregiver->id) {
            abort(403);
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Status pesanan sudah berubah.');
        }

        $booking->update(['status' => $request->status]);
        $pesan = $request->status === 'approved' ? 'Pesanan berhasil diterima!' : 'Pesanan telah ditolak.';

        return back()->with('success', $pesan);
    }

    public function payment(string $id)
    {
        [$booking, $payment, $expired] = DB::transaction(function () use ($id) {
            $booking = Booking::with('patient')->lockForUpdate()->findOrFail($id);

            if ($booking->user_id !== Auth::id()) {
                abort(403);
            }

            if ($booking->status !== 'approved') {
                abort(422, 'Pesanan belum disetujui atau sudah tidak dapat dibayar.');
            }

            if ($booking->updated_at->lte(now()->subHours(12))) {
                $booking->update(['status' => 'canceled']);

                return [$booking, null, true];
            }

            $payment = Payment::query()
                ->where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_amount,
                    'payment_method' => 'midtrans',
                    'status' => 'pending',
                    'order_id' => 'CARUNA-'.$booking->id.'-'.Str::lower(Str::random(16)),
                ]);
            }

            return [$booking, $payment, false];
        });

        if ($expired) {
            return redirect()
                ->route('bookings.index')
                ->with('error', 'Batas waktu pembayaran telah berakhir. Booking dibatalkan dan tidak dapat dibayar.');
        }

        if (! config('services.midtrans.server_key') || ! config('services.midtrans.client_key')) {
            Log::error('Midtrans keys have not been configured.');

            return back()->with('error', 'Layanan pembayaran belum dapat digunakan. Silakan hubungi administrator.');
        }

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        if (! $payment->snap_token) {
            $params = [
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => $payment->amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ];

            try {
                $payment->update(['snap_token' => Snap::getSnapToken($params)]);
            } catch (\Throwable $exception) {
                report($exception);

                return back()->with('error', 'Gagal menyiapkan pembayaran. Silakan coba kembali.');
            }
        }

        $snapToken = $payment->snap_token;

        return view('bookings.payment', compact('booking', 'snapToken'));
    }

    public function paymentSuccess(string $id)
    {
        $booking = Booking::query()
            ->whereKey($id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (in_array($booking->status, ['paid', 'ongoing', 'waiting_confirmation', 'completed'], true)) {
            return redirect()->route('bookings.index')->with('success', 'Pembayaran telah dikonfirmasi.');
        }

        return view('bookings.payment-success', compact('booking'));
    }

    public function paymentStatus(string $id): JsonResponse
    {
        $booking = Booking::query()
            ->whereKey($id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $payment = $booking->payments()
            ->latest('id')
            ->first(['id', 'status', 'midtrans_status', 'reconciliation_status']);

        return response()
            ->json([
                'booking_status' => $booking->status,
                'payment_status' => $payment?->status,
                'midtrans_status' => $payment?->midtrans_status,
                'reconciliation_status' => $payment?->reconciliation_status,
                'payment_confirmed' => in_array(
                    $booking->status,
                    ['paid', 'ongoing', 'waiting_confirmation', 'completed'],
                    true,
                ),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function startService(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->caregiver_id !== Auth::user()->caregiver->id) {
            abort(403);
        }
        if ($booking->status !== 'paid') {
            return back()->with('error', 'Pesanan harus dibayar terlebih dahulu.');
        }

        $booking->update(['status' => 'ongoing']);

        return back()->with('success', 'Layanan telah dimulai. Selamat bertugas!');
    }

    public function requestFinish(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->caregiver_id !== Auth::user()->caregiver->id) {
            abort(403);
        }
        if ($booking->status !== 'ongoing') {
            return back()->with('error', 'Layanan belum dimulai.');
        }

        $booking->update(['status' => 'waiting_confirmation']);

        return back()->with('success', 'Permintaan penyelesaian telah dikirim ke Klien.');
    }

    public function confirmFinish(Request $request, string $id)
    {
        $booking = Booking::with('caregiver')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->status !== 'waiting_confirmation') {
            return back()->with('error', 'Status pesanan tidak valid.');
        }

        try {
            $completed = DB::transaction(function () use ($booking) {
                $lockedBooking = Booking::query()
                    ->lockForUpdate()
                    ->findOrFail($booking->id);

                if ($lockedBooking->status !== 'waiting_confirmation') {
                    return false;
                }

                $commission = round((float) $lockedBooking->total_amount * 0.10, 2);
                $netAmount = round((float) $lockedBooking->total_amount - $commission, 2);

                $lockedBooking->update(['status' => 'completed']);

                $caregiver = Caregiver::where('id', $lockedBooking->caregiver_id)->lockForUpdate()->firstOrFail();
                $caregiver->balance += $netAmount;
                $caregiver->save();

                WalletTransaction::create([
                    'user_id' => $caregiver->user_id,
                    'type' => 'credit',
                    'amount' => $netAmount,
                    'description' => 'Pendapatan layanan pesanan #'.$lockedBooking->id,
                    'reference_id' => $lockedBooking->id,
                ]);

                return true;
            });

            if (! $completed) {
                return back()->with('error', 'Status pesanan tidak valid.');
            }

            return redirect()->route('bookings.index')->with('success', 'Layanan selesai! Saldo telah diteruskan ke perawat.');
        } catch (\Throwable $exception) {
            Log::error('Booking completion could not be processed.', [
                'booking_id' => $booking->id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem saat menyelesaikan layanan.');
        }
    }
}
