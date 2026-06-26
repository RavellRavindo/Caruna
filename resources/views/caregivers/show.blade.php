<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('caregivers.index') }}" class="text-gray-500 hover:text-indigo-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profil Caregiver') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- KOLOM KIRI (Detail & Ulasan) -->
                <div class="md:col-span-2 space-y-8">
                    
                    <!-- Kartu Info Utama -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-6 items-start">
                        <!-- Avatar -->
                        <div class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-black text-3xl shrink-0">
                            {{ substr($caregiver->user->name, 0, 2) }}
                        </div>
                        
                        <!-- Detail Nama & Spesialisasi -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h1 class="text-3xl font-black text-gray-900">{{ $caregiver->user->name }}</h1>
                                    <span class="inline-block mt-2 px-3 py-1 bg-indigo-50 text-indigo-700 text-sm font-bold rounded-lg">
                                        {{ $caregiver->specialization }}
                                    </span>
                                </div>
                                @if($caregiver->gender === 'Perempuan')
                                    <span class="px-3 py-1 bg-pink-50 text-pink-600 text-xs font-bold rounded-full border border-pink-100">Perempuan</span>
                                @else
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full border border-blue-100">Laki-laki</span>
                                @endif
                            </div>

                            <!-- KOMPONEN BINTANG -->
                            <div class="flex items-center gap-2 mt-4 bg-yellow-50 inline-flex px-3 py-1.5 rounded-lg border border-yellow-100">
                                <i class="fa-solid fa-star text-yellow-400"></i>
                                <span class="font-extrabold text-gray-900 text-lg">{{ number_format($caregiver->average_rating, 1) }}</span>
                                <span class="text-gray-500 text-sm font-medium">({{ $caregiver->total_reviews }} Ulasan)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Tentang Perawat -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Tentang Perawat</h3>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $caregiver->about_me ?? 'Perawat ini belum menuliskan deskripsi tentang dirinya.' }}
                        </p>
                    </div>

                    <!-- DAFTAR ULASAN KLIEN -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-comments text-indigo-500"></i> Apa Kata Klien?
                        </h3>

                        <div class="grid grid-cols-1 gap-4">
                            @forelse($caregiver->reviews as $review)
                                <div class="bg-gray-50 border border-gray-100 p-5 rounded-2xl">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                {{ substr($review->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-sm">{{ $review->user->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex text-yellow-400 text-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        {{ $review->comment ?? 'Klien memberikan penilaian tanpa menyertakan komentar teks.' }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fa-regular fa-comment-dots text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-medium">Belum ada ulasan. Jadilah klien pertama yang memesan!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN (Aksi Pemesanan) -->
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-3xl shadow-lg border border-indigo-100 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pemesanan</h3>
                        
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">Pengalaman</span>
                            <span class="font-bold text-gray-900">{{ $caregiver->experience_years }} Tahun</span>
                        </div>

                        <div class="mb-6">
                            <span class="block text-gray-500 text-sm mb-1">Tarif per Hari</span>
                            <span class="text-3xl font-black text-green-600">Rp {{ number_format($caregiver->price_per_day, 0, ',', '.') }}</span>
                        </div>

                        <!-- TOMBOL MENUJU FORM BOOKING -->
                        <a href="{{ route('bookings.create', ['caregiver_id' => $caregiver->id]) }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-bold py-4 px-6 rounded-xl shadow-md transition-all hover:scale-[1.02]">
                            Pesan Layanan Sekarang
                        </a>
                        
                        <p class="text-xs text-center text-gray-400 mt-4">
                            <i class="fa-solid fa-shield-halved"></i> Transaksi dijamin aman melalui sistem Caruna.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>