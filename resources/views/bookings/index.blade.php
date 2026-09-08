<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pesanan Saya') }}
        </h2>
    </x-slot>

    <div class="page-shell">
        <div class="page-container">
            
            @if(session('success'))
                <div class="mb-6 flex items-start rounded-xl border border-green-200 bg-green-50 px-4 py-4 text-green-800 shadow-sm sm:items-center sm:px-5">
                    <i class="fa-solid fa-circle-check mr-3 mt-0.5 text-xl text-green-500 sm:mt-0"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                
                @if($bookings->isEmpty())
                    <div class="text-center py-16 border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50">
                        <i class="fa-solid fa-clock-rotate-left text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700">Belum Ada Riwayat</h3>
                        <p class="text-gray-500 mt-1 mb-4">Anda belum memiliki riwayat pemesanan layanan.</p>
                        <a href="{{ route('caregivers.index') }}" class="inline-block bg-indigo-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-indigo-700 transition">Cari Caregiver Sekarang</a>
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="min-w-[820px] w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase tracking-widest">
                                    <th class="pb-4 px-2 font-black">Caregiver</th>
                                    <th class="pb-4 px-2 font-black">Pasien</th>
                                    <th class="pb-4 px-2 font-black">Jadwal</th>
                                    <th class="pb-4 px-2 font-black">Durasi</th>
                                    <th class="pb-4 px-2 font-black text-right">Total Biaya</th>
                                    <th class="pb-4 px-2 font-black text-center">Status & Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    @php
                                        // 1. Logika Warna Status
                                        $statusColors = [
                                            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'waiting_payment' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'paid' => 'bg-green-50 text-green-700 border-green-200',
                                            'ongoing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'waiting_confirmation' => 'bg-yellow-100 text-yellow-900 border-yellow-400',
                                            'completed' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                            'canceled' => 'bg-gray-50 text-gray-700 border-gray-200',
                                        ];
                                        $color = $statusColors[$booking->status] ?? 'bg-gray-50 text-gray-700';

                                        // 2. Logika Hitung Mundur (Deadline)
                                        $deadline = null;
                                        if ($booking->status === 'pending') {
                                            $deadline = \Carbon\Carbon::parse($booking->created_at)->addHours(12);
                                        } elseif ($booking->status === 'approved') {
                                            $deadline = \Carbon\Carbon::parse($booking->updated_at)->addHours(12);
                                        }
                                    @endphp

                                    <tr class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden hover:bg-gray-50 transition-all">
                                        <td class="py-5 px-3">
                                            <p class="font-extrabold text-gray-900">{{ $booking->caregiver->user->name }}</p>
                                            <p class="text-[10px] text-indigo-600 font-bold uppercase tracking-tighter">{{ $booking->caregiver->specialization }}</p>
                                        </td>
                                        <td class="py-5 px-2 text-gray-700 font-bold">{{ $booking->patient->full_name }}</td>
                                        <td class="py-5 px-2 text-gray-600 text-sm font-medium">{{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}</td>
                                        <td class="py-5 px-2 text-gray-600 text-sm font-bold">{{ $booking->total_days }} Hari</td>
                                        <td class="py-5 px-2 font-black text-green-600 text-right text-lg">
                                            Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                        </td>
                                        
                                        <td class="py-5 px-4">
                                            <div class="flex flex-col items-center gap-2">
                                                {{-- Badge Status --}}
                                                <span class="{{ $color }} border text-[10px] font-black px-3 py-1 rounded-md uppercase tracking-wider shadow-sm">
                                                    {{ str_replace('_', ' ', $booking->status) }}
                                                </span>

                                                {{-- Tampilan Hitung Mundur (Deadline) --}}
                                                @if($deadline && now()->lessThan($deadline))
                                                    <div class="flex flex-col items-center animate-pulse">
                                                        <p class="text-[9px] text-red-500 font-black uppercase tracking-tighter">Batas Waktu:</p>
                                                        <p class="text-[10px] text-gray-600 font-extrabold">
                                                            {{ now()->diff($deadline)->format('%h jam %i menit') }}
                                                        </p>
                                                    </div>
                                                @elseif($deadline && now()->greaterThanOrEqualTo($deadline))
                                                    <p class="text-[10px] text-gray-400 font-bold italic tracking-tighter text-center">Waktu Habis</p>
                                                @endif

                                                {{-- Tombol Aksi --}}
                                                @if($booking->status === 'approved')
                                                    <a href="{{ route('bookings.payment', $booking->id) }}" 
                                                    class="bg-green-600 hover:bg-green-700 text-white text-[10px] font-black px-4 py-2 rounded-lg transition-all shadow-md shadow-green-100 uppercase flex items-center gap-1 w-full justify-center">
                                                        <i class="fa-solid fa-wallet"></i> Bayar
                                                    </a>
                                                    
                                                @elseif($booking->status === 'waiting_confirmation')
                                                    <form action="{{ route('bookings.confirm_finish', $booking->id) }}" method="POST" class="w-full">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-black px-3 py-2 rounded-lg transition-all shadow-md uppercase flex items-center justify-center gap-1" onclick="return confirm('Konfirmasi pekerjaan selesai? Dana akan diteruskan ke perawat.')">
                                                            <i class="fa-solid fa-check-double"></i> Selesaikan
                                                        </button>
                                                    </form>

                                                @elseif($booking->status === 'completed' && !$booking->review)
                                                    <button type="button" onclick="document.getElementById('reviewModal-{{ $booking->id }}').classList.remove('hidden')" class="w-full bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-[10px] font-black px-3 py-2 rounded-lg transition-all shadow-md uppercase flex items-center justify-center gap-1">
                                                        <i class="fa-solid fa-star"></i> Nilai
                                                    </button>

                                                    {{-- Modal Review --}}
                                                    <div id="reviewModal-{{ $booking->id }}" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full flex justify-center items-center text-left">
                                                        <div class="relative m-3 w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl sm:m-4 sm:rounded-3xl sm:p-8">
                                                            <div class="flex justify-between items-center mb-6">
                                                                <h3 class="text-xl font-black text-gray-900">Nilai Perawat</h3>
                                                                <button type="button" onclick="document.getElementById('reviewModal-{{ $booking->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500 transition">
                                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('reviews.store', $booking->id) }}" method="POST">
                                                                @csrf
                                                                <div class="mb-5">
                                                                    <label class="block text-gray-700 text-xs font-black uppercase mb-2 tracking-widest">Rating Bintang</label>
                                                                    <select name="rating" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-yellow-500 focus:ring-yellow-500 font-bold text-lg">
                                                                        <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                                                        <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                                                        <option value="3">⭐⭐⭐ (3/5)</option>
                                                                        <option value="2">⭐⭐ (2/5)</option>
                                                                        <option value="1">⭐ (1/5)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-8">
                                                                    <label class="block text-gray-700 text-xs font-black uppercase mb-2 tracking-widest">Ulasan Anda</label>
                                                                    <textarea name="comment" rows="4" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ceritakan pengalaman Anda..."></textarea>
                                                                </div>
                                                                <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-black py-4 px-4 rounded-2xl shadow-lg transition uppercase tracking-widest text-sm">
                                                                    Kirim Ulasan
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(in_array($booking->status, ['paid', 'ongoing', 'waiting_confirmation', 'completed'], true))
                                                    @if($booking->caregiver->user->whatsapp_url)
                                                        <a href="{{ $booking->caregiver->user->whatsapp_url }}?text={{ rawurlencode('Halo, saya '.$booking->user->name.' terkait booking #'.$booking->id.' di Caruna.') }}" target="_blank" rel="noopener noreferrer" class="flex w-full items-center justify-center gap-1 rounded-lg bg-green-500 px-3 py-2 text-[10px] font-black uppercase text-white shadow-md shadow-green-100 transition-all hover:bg-green-600">
                                                            <i class="fa-brands fa-whatsapp text-sm"></i> Hubungi via WhatsApp
                                                        </a>
                                                    @else
                                                        <p class="text-center text-[10px] font-medium text-gray-400">Nomor WhatsApp caregiver belum tersedia.</p>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
