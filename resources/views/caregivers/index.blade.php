<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Caregiver') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10 text-center sm:text-left">
                <h3 class="text-3xl font-black text-gray-900 tracking-tight">Pilih Perawat untuk Pasien Anda</h3>
                <p class="text-gray-500 mt-2 text-lg">Temukan pendamping profesional yang tepat untuk kenyamanan keluarga Anda.</p>
            </div>

            <!-- Grid Katalog: 3 Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                @forelse ($caregivers as $caregiver)
                    <div class="group bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                        <div class="p-7 flex-1">
                            <!-- Header Card -->
                            <div class="flex items-center gap-5 mb-6">
                                <!-- Avatar Tanpa Dot Online -->
                                <div class="shrink-0">
                                    <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-2xl shadow-inner transition-transform group-hover:scale-105">
                                        {{ substr($caregiver->user->name, 0, 2) }}
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-lg font-extrabold text-gray-900 truncate">{{ $caregiver->user->name }}</h4>
                                        @if($caregiver->gender === 'Laki-laki')
                                            <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-2 py-0.5 rounded-full border border-blue-100 uppercase tracking-tighter">L</span>
                                        @else
                                            <span class="bg-pink-50 text-pink-600 text-[10px] font-black px-2 py-0.5 rounded-full border border-pink-100 uppercase tracking-tighter">P</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Rating -->
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-400">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-900 font-black text-sm">{{ number_format($caregiver->average_rating, 1) }}</span>
                                        <span class="text-gray-400 text-xs font-medium">({{ $caregiver->total_reviews }})</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Spesialisasi -->
                            <div class="mb-4">
                                <span class="bg-indigo-50 text-indigo-700 text-[11px] font-extrabold px-3 py-1.5 rounded-lg border border-indigo-100 uppercase tracking-wide">
                                    {{ $caregiver->specialization }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-500 mb-6 leading-relaxed line-clamp-3 italic">
                                "{{ $caregiver->about_me }}"
                            </p>

                            <!-- Statistik -->
                            <div class="grid grid-cols-2 gap-4 py-4 border-t border-gray-50 mb-4">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Pengalaman</p>
                                    <p class="text-sm font-black text-gray-800">{{ $caregiver->experience_years }} Tahun</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tarif /Hari</p>
                                    <p class="text-sm font-black text-green-600">Rp {{ number_format($caregiver->price_per_day, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="px-7 pb-7">
                            <a href="{{ route('caregivers.show', $caregiver->id) }}" 
                               class="flex items-center justify-center gap-2 w-full bg-indigo-600 text-white font-bold py-3.5 rounded-2xl hover:bg-indigo-700 active:scale-95 transition-all shadow-lg shadow-indigo-100">
                                <span>Lihat Profil</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-300 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900">Belum Ada Caregiver</h3>
                        <p class="text-gray-500">Saat ini belum ada perawat yang tersedia.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</x-app-layout>