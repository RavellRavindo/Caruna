<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="page-shell">
        <div class="page-container">
            
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

            <!-- Konten Khusus Client -->
            <div class="mb-8">
                <h4 class="text-gray-500 font-bold mb-4 uppercase text-sm tracking-wider">Layanan Utama</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Pasien Keluarga -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl hover:shadow-lg transition duration-300 border border-gray-100">
                        <div class="p-5 sm:p-8">
                            <div class="flex items-center mb-4">
                                <div class="bg-indigo-100 p-4 rounded-xl mr-4">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-900">Pasien Keluarga</h4>
                                    <p class="text-indigo-600 font-semibold text-sm">Kelola Profil Kesehatan</p>
                                </div>
                            </div>
                            <p class="text-gray-500 mb-6 line-clamp-2">Tambahkan atau perbarui data medis anggota keluarga yang membutuhkan pendampingan perawat.</p>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('patients.index') }}" class="flex-1 text-center bg-indigo-50 text-indigo-700 px-4 py-3 rounded-xl font-bold hover:bg-indigo-100 transition">Lihat Daftar</a>
                                <a href="{{ route('patients.create') }}" class="flex-none bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-md">+ Pasien</a>
                            </div>
                        </div>
                    </div>

                    <!-- Pesan Caregiver -->
                    <div class="bg-white shadow-sm sm:rounded-2xl hover:shadow-lg transition duration-300 border border-gray-100 border-t-4 border-t-green-500 relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 bg-green-50 w-40 h-40 rounded-full z-0 group-hover:scale-110 transition duration-500"></div>
                        <div class="relative z-10 p-5 sm:p-8">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-100 p-4 rounded-xl mr-4">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-900">Pesan Caregiver</h4>
                                    <p class="text-green-600 font-semibold text-sm">Temukan Perawat Profesional</p>
                                </div>
                            </div>
                            <p class="text-gray-500 mb-6 line-clamp-2">Cari perawat berdasarkan spesialisasi, lihat profil lengkap, dan lakukan pemesanan secara instan.</p>
                            <a href="{{ route('caregivers.index') }}" class="block w-full text-center bg-green-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-green-700 transition shadow-md shadow-green-200">Buka Katalog Caregiver</a>
                        </div>
                    </div>

                </div>
            </div>

            <div>
                <h4 class="text-gray-500 font-bold mb-4 uppercase text-sm tracking-wider">Aktivitas & Monitoring</h4>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl hover:shadow-md transition duration-300 border border-gray-100 border-l-4 border-l-orange-500">
                    <div class="flex flex-col items-start justify-between gap-5 p-5 sm:p-6 md:flex-row md:items-center md:p-8">
                        <div class="flex w-full items-start md:w-auto md:items-center">
                            <div class="mr-4 shrink-0 rounded-xl bg-orange-100 p-3 sm:mr-6 sm:p-4">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Riwayat Pesanan & Transaksi</h4>
                                <p class="text-gray-500 mt-1">Pantau status pesanan yang sedang berjalan, menunggu pembayaran, atau yang sudah selesai.</p>
                            </div>
                        </div>
                        <div class="w-full md:w-auto shrink-0">
                            <a href="{{ route('bookings.index') }}" class="flex items-center justify-center w-full md:w-auto bg-white border-2 border-orange-500 text-orange-600 px-8 py-3 rounded-xl font-bold hover:bg-orange-50 transition">
                                Lihat Riwayat <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
