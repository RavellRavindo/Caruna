<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Formulir Pemesanan Caregiver') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl">
                <div class="p-8 text-gray-900 flex flex-col md:flex-row gap-10">
                    
                    <div class="w-full md:w-2/3">
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-6">Detail Pemesanan</h3>

                        @if(isset($busyBookings) && $busyBookings->isNotEmpty())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm">
                                <h4 class="text-sm font-bold text-red-800 mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    Jadwal Perawat Penuh / Tidak Tersedia:
                                </h4>
                                <ul class="list-disc list-inside text-sm text-red-700 font-medium space-y-1 ml-1">
                                    @foreach($busyBookings as $busy)
                                        @php
                                            $start = \Carbon\Carbon::parse($busy->start_date);
                                            $end = $start->copy()->addDays((int) $busy->total_days - 1);
                                        @endphp
                                        <li>{{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }}</li>
                                    @endforeach
                                </ul>
                                <p class="text-xs text-red-600 mt-3 italic">*Silakan pilih tanggal mulai di luar rentang jadwal di atas.</p>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('bookings.store') }}">
                            @csrf
                            
                            <input type="hidden" name="caregiver_id" value="{{ $caregiver->id }}">

                            <div class="mb-6">
                                <x-input-label for="patient_id" :value="__('Untuk Siapa Layanan Ini? (Pilih Pasien)')" class="text-lg font-bold" />
                                <select id="patient_id" name="patient_id" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                                    <option value="" disabled selected>-- Pilih data pasien --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <x-input-label for="start_date" :value="__('Tanggal Mulai Perawatan')" class="text-lg font-bold" />
                                <x-text-input id="start_date" class="block mt-2 w-full rounded-lg" type="date" name="start_date" required min="{{ date('Y-m-d') }}" />
                            </div>

                            <div class="mb-6">
                                <x-input-label for="total_days" :value="__('Durasi Perawatan (Hari)')" class="text-lg font-bold" />
                                <x-text-input id="total_days" class="block mt-2 w-full rounded-lg" type="number" name="total_days" required min="1" placeholder="Contoh: 3" oninput="calculateTotal()" />
                            </div>

                            <button type="submit" class="mt-8 w-full bg-indigo-600 text-white font-extrabold text-lg py-4 rounded-xl shadow-lg hover:bg-indigo-700 hover:shadow-xl transition duration-300">
                                Konfirmasi & Buat Pesanan
                            </button>
                        </form>
                    </div>

                    <div class="w-full md:w-1/3 bg-gray-50 p-6 rounded-2xl border border-gray-200 h-fit">
                        <h4 class="text-xl font-extrabold text-gray-900 mb-6">Ringkasan Layanan</h4>
                        
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                            <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-2xl shadow-inner">
                                {{ substr($caregiver->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">{{ $caregiver->user->name }}</p>
                                <p class="text-sm text-indigo-600 font-semibold">{{ $caregiver->specialization }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Tarif per Hari</span>
                                <span class="font-semibold" id="price_per_day" data-price="{{ $caregiver->price_per_day }}">
                                    Rp {{ number_format($caregiver->price_per_day, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Durasi Layanan</span>
                                <span class="font-semibold"><span id="display_days">0</span> Hari</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 flex justify-between items-center">
                            <span class="font-bold text-gray-900 text-lg">Total Pembayaran</span>
                            <span class="text-2xl font-black text-green-600" id="total_price">Rp 0</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            // Ambil nilai hari yang diinput Klien
            let days = document.getElementById('total_days').value;
            // Ambil data harga perawat dari atribut data-price
            let pricePerDay = document.getElementById('price_per_day').getAttribute('data-price');
            
            // Konversi ke angka, jika kosong set jadi 0
            days = days ? parseInt(days) : 0;
            
            // Hitung total harga
            let total = days * parseFloat(pricePerDay);
            
            // Tampilkan ke layar UI
            document.getElementById('display_days').innerText = days;
            document.getElementById('total_price').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</x-app-layout>