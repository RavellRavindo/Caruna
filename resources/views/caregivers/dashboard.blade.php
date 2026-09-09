<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="page-shell">
        <div class="page-container">
            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Welcome Banner -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 border-l-4 border-indigo-600">
                <div class="flex flex-col items-start gap-4 p-4 text-gray-900 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h3 class="text-xl font-bold">Selamat datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Anda masuk sebagai: <span class="font-bold uppercase text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ Auth::user()->role }}</span>
                        </p>
                    </div>
                    <div class="hidden md:block text-right">
                        <p class="text-sm text-gray-500">Tanggal Hari Ini</p>
                        <p class="font-bold text-gray-800">{{ date('d F Y') }}</p>
                    </div>
                </div>
            </div>

            @if (! $caregiver)
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800 shadow-sm sm:rounded-3xl sm:p-7">
                    <h3 class="text-lg font-bold">Profil caregiver belum tersedia</h3>
                    <p class="mt-2 text-sm leading-relaxed">Akun ini belum memiliki data profesional caregiver. Hubungi administrator agar profil dapat dilengkapi sebelum menerima pesanan.</p>
                </div>
            @elseif (! $caregiver->isVerified())
                @php
                    $isRejected = $caregiver->verification_status === \App\Models\Caregiver::VERIFICATION_REJECTED;
                @endphp
                <div class="rounded-2xl border {{ $isRejected ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50' }} p-5 shadow-sm sm:rounded-3xl sm:p-7">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="{{ $isRejected ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }} flex h-12 w-12 shrink-0 items-center justify-center rounded-xl font-black">
                            !
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $isRejected ? 'Pendaftaran caregiver belum disetujui' : 'Pendaftaran caregiver sedang ditinjau' }}
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-700">
                                @if ($isRejected)
                                    Admin belum dapat menyetujui pendaftaran Anda. Perbaiki data yang diminta lalu hubungi administrator untuk pengajuan ulang.
                                @else
                                    Data profesional Anda sudah tersimpan dan sedang menunggu verifikasi admin. Selama proses ini, pesanan masuk dan dompet belum dapat diakses.
                                @endif
                            </p>
                            @if ($isRejected && $caregiver->rejection_reason)
                                <p class="mt-3 rounded-lg bg-white/70 px-3 py-2 text-sm text-red-800">
                                    <span class="font-bold">Catatan admin:</span> {{ $caregiver->rejection_reason }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
            <!-- Konten Khusus Caregiver -->
            <a href="{{ route('caregiver.wallet') }}" class="block group mb-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-300 transition-all">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                            <div class="bg-indigo-50 p-3 rounded-xl group-hover:bg-indigo-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-indigo-600 group-hover:text-white transition-colors">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500 font-medium">Saldo Dompet Saya</p>
                                <h4 class="text-2xl font-black text-gray-900 group-hover:text-indigo-700 transition-colors">
                                    Rp {{ number_format(Auth::user()->caregiver->balance ?? 0, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                        <div class="text-gray-400 group-hover:text-indigo-600 transition-colors flex items-center gap-2">
                            <span class="text-sm font-semibold hidden md:inline-block">Tarik Dana</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
                
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-indigo-500 hover:shadow-md transition">
                <div class="flex flex-col items-start justify-between gap-6 p-5 sm:p-8 md:flex-row md:items-center">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Area Caregiver</h4>
                        <p class="text-gray-600 mb-4">Periksa permintaan layanan baru yang masuk dari klien Anda.</p>
                        <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm font-bold uppercase border border-green-200">
                            <i class="fa-solid fa-check-circle mr-1"></i> Akun Aktif
                        </div>
                    </div>
                    <div class="w-full md:w-auto">
                        <a href="{{ route('caregiver.bookings') }}" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-8 py-3 font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 md:w-auto">
                            Cek Pesanan Masuk
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
