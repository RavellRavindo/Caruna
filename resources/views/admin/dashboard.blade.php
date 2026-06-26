<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Welcome Banner -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 border-l-4 border-indigo-600">
                <div class="p-6 text-gray-900 flex justify-between items-center">
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

            <!-- Konten Khusus Admin -->
            <div class="mb-8">
                <h4 class="text-gray-500 font-bold mb-4 uppercase text-sm tracking-wider">Ringkasan Sistem</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Stat Card 1: Withdrawals -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Butuh Proses</p>
                            <h4 class="text-3xl font-black text-gray-900">{{ $pendingWithdrawals }}</h4> <!-- Ganti variabel -->
                            <a href="{{ route('admin.withdrawals.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 mt-1 inline-flex items-center gap-1">
                                Cek Penarikan <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Stat Card 2: Caregivers -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Perawat Aktif</p>
                            <h4 class="text-3xl font-black text-gray-900">{{ $activeCaregivers }}</h4> <!-- Ganti variabel -->
                            <p class="text-sm font-medium text-gray-500 mt-1">Terdaftar di sistem</p>
                        </div>
                    </div>

                    <!-- Stat Card 3: Bookings -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pesanan</p>
                            <h4 class="text-3xl font-black text-gray-900">{{ $completedBookings }}</h4> <!-- Ganti variabel -->
                            <p class="text-sm font-medium text-gray-500 mt-1">Berhasil diselesaikan</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>