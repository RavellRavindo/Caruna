<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pesanan Masuk') }}
        </h2>
    </x-slot>

    <div class="page-shell">
        <div class="page-container">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm">
                    <p class="font-bold">Berhasil</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-transparent overflow-hidden">
                @if($bookings->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
                        <div class="inline-flex items-center justify-center bg-gray-50 w-20 h-20 rounded-full mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700">Belum Ada Pesanan</h3>
                        <p class="text-gray-500 mt-2">Saat ini belum ada permintaan layanan dari klien.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 sm:gap-6">
                        @foreach($bookings as $booking)
                            @php
                                $deadline = null;
                                if ($booking->status === 'pending') {
                                    $deadline = \Carbon\Carbon::parse($booking->created_at)->addHours(12);
                                } elseif ($booking->status === 'approved') {
                                    $deadline = \Carbon\Carbon::parse($booking->updated_at)->addHours(12);
                                }

                                $statusColors = [
                                    'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'paid' => 'bg-green-50 text-green-700 border-green-200',
                                    'ongoing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'waiting_confirmation' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'completed' => 'bg-teal-50 text-teal-700 border-teal-200',
                                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                    'canceled' => 'bg-gray-50 text-gray-700 border-gray-200',
                                ];
                                $color = $statusColors[$booking->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                $contactIsAvailable = in_array($booking->status, ['paid', 'ongoing', 'waiting_confirmation', 'completed'], true);
                            @endphp

                            <div class="flex flex-col items-start gap-5 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-lg sm:p-6 lg:flex-row lg:items-center lg:gap-6">
                                
                                <div class="w-full lg:w-2/5 border-b lg:border-b-0 lg:border-r border-gray-100 pb-4 lg:pb-0 lg:pr-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">
                                            ID: #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-medium">{{ $booking->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="mb-1 break-words text-lg font-extrabold text-gray-900 sm:text-xl">Pasien: {{ $booking->patient->full_name }}</h4>
                                    <p class="text-sm text-gray-500 mb-4">Dipesan oleh: <span class="font-bold text-gray-700">{{ $booking->user->name }}</span></p>
                                    
                                    <div class="flex flex-wrap gap-2">
                                        {{-- Badge Tanggal --}}
                                        <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 px-3 py-2 rounded-xl shadow-sm">
                                            <i class="fa-solid fa-calendar-day text-indigo-600 text-[10px]"></i>
                                            <span class="text-xs font-extrabold text-indigo-900">
                                                {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}
                                            </span>
                                        </div>

                                        {{-- Badge Durasi --}}
                                        <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 px-3 py-2 rounded-xl shadow-sm">
                                            <i class="fa-solid fa-hourglass-half text-indigo-600 text-[10px]"></i>
                                            <span class="text-xs font-extrabold text-indigo-900">
                                                {{ $booking->total_days }} Hari
                                            </span>
                                        </div>
                                    </div>

                                    @if($contactIsAvailable)
                                        <div class="mt-4 space-y-2 rounded-xl border border-green-100 bg-green-50 p-3 text-sm">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-green-700">Kontak & Lokasi Layanan</p>

                                            @if($booking->user->whatsapp_url)
                                                <a href="{{ $booking->user->whatsapp_url }}?text={{ rawurlencode('Halo, saya '.$booking->caregiver->user->name.' terkait booking #'.$booking->id.' di Caruna.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-bold text-green-700 hover:text-green-800">
                                                    <i class="fa-brands fa-whatsapp text-base"></i>
                                                    {{ $booking->user->phone_number }}
                                                </a>
                                            @else
                                                <p class="text-xs font-medium text-gray-500">Nomor WhatsApp klien belum tersedia.</p>
                                            @endif

                                            <p class="break-words text-xs leading-relaxed text-gray-600">
                                                <span class="font-bold text-gray-700">Alamat:</span>
                                                {{ $booking->service_address ?: 'Belum diisi oleh klien.' }}
                                            </p>
                                            <p class="break-words text-xs leading-relaxed text-gray-600">
                                                <span class="font-bold text-gray-700">Kontak darurat:</span>
                                                {{ $booking->patient->emergency_contact }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="w-full lg:w-1/4 flex flex-col justify-center items-center lg:items-start px-2">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Estimasi Pendapatan</p>
                                    <p class="break-words text-xl font-black text-green-600 sm:text-2xl">
                                        Rp {{ number_format($booking->total_amount * 0.90, 0, ',', '.') }}
                                    </p>
                                    <div class="mt-1 flex gap-2 text-[10px] text-gray-400 font-medium">
                                        <span>Total: Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                                        <span>•</span>
                                        <span class="text-red-500">Fee: 10%</span>
                                    </div>
                                </div>

                                <div class="w-full lg:w-1/3 flex flex-col items-center lg:items-end gap-3">
                                    
                                    @if($booking->status === 'pending')
                                        <div class="flex gap-2 w-full lg:justify-end">
                                            <form action="{{ route('bookings.updateStatus', $booking->id) }}" method="POST" class="flex-1 lg:flex-none">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="w-full bg-white border-2 border-red-500 text-red-600 hover:bg-red-50 font-bold py-2.5 px-6 rounded-xl transition-all text-sm uppercase tracking-wide" onclick="return confirm('Yakin ingin menolak pesanan ini?')">
                                                    Tolak
                                                </button>
                                            </form>
                                            <form action="{{ route('bookings.updateStatus', $booking->id) }}" method="POST" class="flex-1 lg:flex-none">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-md shadow-indigo-100 transition-all text-sm uppercase tracking-wide" onclick="return confirm('Terima pesanan layanan ini?')">
                                                    Terima
                                                </button>
                                            </form>
                                        </div>

                                    @elseif($booking->status === 'paid')
                                        <form action="{{ route('bookings.start', $booking->id) }}" method="POST" class="w-full lg:w-auto">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-10 rounded-xl shadow-md shadow-teal-100 transition-all flex items-center justify-center gap-2 uppercase text-sm tracking-wide">
                                                <i class="fa-solid fa-play"></i> Mulai Tugas
                                            </button>
                                        </form>

                                    @elseif($booking->status === 'ongoing')
                                        <form action="{{ route('bookings.request_finish', $booking->id) }}" method="POST" class="w-full lg:w-auto">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-md shadow-orange-100 transition-all flex items-center justify-center gap-2 uppercase text-sm tracking-wide">
                                                <i class="fa-solid fa-flag-checkered"></i> Ajukan Selesai
                                            </button>
                                        </form>

                                    @else
                                        <span class="{{ $color }} px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest border shadow-sm">
                                            {{ str_replace('_', ' ', $booking->status) }}
                                        </span>
                                    @endif

                                    {{-- Deadline Info --}}
                                    @if($deadline)
                                        <div class="flex flex-col items-center lg:items-end">
                                            @if($booking->status === 'pending' && now()->lessThan($deadline))
                                                <p class="text-[9px] text-red-500 font-black uppercase animate-pulse mb-0.5 tracking-tighter">Segera Respon!</p>
                                                <p class="text-[11px] text-gray-500 font-bold bg-red-50 px-3 py-1 rounded-full border border-red-100">
                                                    Batas: {{ now()->diff($deadline)->format('%h jam %i menit') }}
                                                </p>
                                            @elseif($booking->status === 'approved' && now()->lessThan($deadline))
                                                <p class="text-[9px] text-indigo-500 font-black uppercase mb-0.5 tracking-tighter">Menunggu Pembayaran</p>
                                                <p class="text-[11px] text-gray-500 font-bold bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
                                                    Sisa: {{ now()->diff($deadline)->format('%h jam %i menit') }}
                                                </p>
                                            @elseif(now()->greaterThanOrEqualTo($deadline) && in_array($booking->status, ['pending', 'approved']))
                                                <p class="text-[11px] text-gray-400 font-bold italic">Waktu Habis</p>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
