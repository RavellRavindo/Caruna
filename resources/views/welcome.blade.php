<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CARUNA - Solusi Perawatan Keluarga Terpercaya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-800 font-sans selection:bg-indigo-100 selection:text-indigo-900">

    <!-- NAVBAR (Sticky & Glassmorphism) -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between sm:h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-200">
                        C
                    </div>
                    <span class="text-xl font-black tracking-tight text-gray-900 sm:text-2xl">CARUNA<span class="text-indigo-600">.</span></span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-8">
                    <a href="#beranda" class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition">Beranda</a>
                    <a href="#layanan" class="text-sm font-bold text-gray-500 hover:text-indigo-600 transition">Layanan</a>
                    <a href="#cara-kerja" class="text-sm font-bold text-gray-500 hover:text-indigo-600 transition">Cara Kerja</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-gray-700 hover:text-indigo-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-gray-900 transition hidden sm:block">Masuk</a>
                            <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white shadow-md shadow-indigo-200 transition-all hover:-translate-y-0.5 hover:bg-indigo-700 sm:px-5 sm:py-2.5 sm:text-sm">
                                <span class="sm:hidden">Daftar</span><span class="hidden sm:inline">Daftar Sekarang</span>
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="beranda" class="relative overflow-hidden pb-16 pt-24 sm:pb-20 sm:pt-32 lg:pb-32 lg:pt-48">
        <!-- Background Ornaments -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-50 blur-3xl opacity-70 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-50 blur-3xl opacity-70 pointer-events-none"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <span class="inline-block py-1 px-3 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-extrabold uppercase tracking-widest mb-6">
                Platform Caregiver #1
            </span>
            
            <h1 class="mb-6 text-4xl font-black leading-tight tracking-tight text-gray-900 sm:text-5xl md:text-6xl lg:text-7xl">
                Hadirkan <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Belas Kasih</span><br class="hidden sm:block"> di Rumah Anda.
            </h1>
            
            <p class="text-lg md:text-xl text-gray-500 mb-10 max-w-2xl mx-auto leading-relaxed">
                Temukan perawat tersertifikasi untuk pendampingan lansia dan pemulihan pasca-operasi. Aman, transparan, dan dapat diandalkan.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('register') }}" class="w-full sm:w-auto bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-2xl hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                    Cari Perawat Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="#cara-kerja" class="w-full sm:w-auto bg-white border-2 border-gray-100 text-gray-700 px-8 py-4 rounded-2xl font-bold hover:bg-gray-50 hover:border-gray-200 transition-all flex items-center justify-center">
                    Pelajari Cara Kerja
                </a>
            </div>

            <!-- Stats (Social Proof) -->
            <div class="mt-20 pt-10 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div>
                    <h3 class="text-3xl font-black text-gray-900">100+</h3>
                    <p class="text-sm font-bold text-gray-500 mt-1 uppercase tracking-wider">Perawat Aktif</p>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-gray-900">4.9/5</h3>
                    <p class="text-sm font-bold text-gray-500 mt-1 uppercase tracking-wider">Rata-rata Rating</p>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-gray-900">Aman</h3>
                    <p class="text-sm font-bold text-gray-500 mt-1 uppercase tracking-wider">Sistem Pembayaran</p>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-gray-900">24/7</h3>
                    <p class="text-sm font-bold text-gray-500 mt-1 uppercase tracking-wider">Dukungan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="layanan" class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Mengapa Memilih Caruna?</h2>
                <p class="text-gray-500 text-lg">Kami merancang ekosistem yang melindungi hak pasien sekaligus menghargai profesionalisme perawat.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Fitur 1 -->
                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tersertifikasi & Terverifikasi</h3>
                    <p class="text-gray-500 leading-relaxed">Semua perawat dalam katalog kami telah melalui proses verifikasi identitas dan keahlian secara ketat.</p>
                </div>

                <!-- Fitur 2 -->
                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Transaksi Aman</h3>
                    <p class="text-gray-500 leading-relaxed">Dana Anda aman di sistem kami dan baru akan diteruskan ke perawat setelah pekerjaan diselesaikan dengan baik.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Ulasan Transparan</h3>
                    <p class="text-gray-500 leading-relaxed">Lihat rating dan ulasan asli dari klien sebelumnya untuk membantu Anda memilih perawat terbaik.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="cara-kerja" class="bg-gray-900 py-16 text-white sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black mb-4">Sangat Mudah Digunakan</h2>
                <p class="text-gray-400 text-lg">Hanya 3 langkah mudah untuk mendapatkan perawatan berkualitas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <!-- Garis Penghubung (Hidden di mobile) -->
                <div class="hidden md:block absolute top-8 left-[15%] right-[15%] h-0.5 bg-gray-700 z-0"></div>

                <div class="relative z-10 text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center font-black text-2xl mx-auto mb-6 shadow-xl border-4 border-gray-900">1</div>
                    <h3 class="text-xl font-bold mb-2">Pilih Perawat</h3>
                    <p class="text-gray-400">Jelajahi katalog, lihat profil, rating, dan spesialisasi perawat yang sesuai.</p>
                </div>
                <div class="relative z-10 text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center font-black text-2xl mx-auto mb-6 shadow-xl border-4 border-gray-900">2</div>
                    <h3 class="text-xl font-bold mb-2">Buat Pesanan</h3>
                    <p class="text-gray-400">Tentukan tanggal, durasi, dan selesaikan pembayaran dengan sistem aman kami.</p>
                </div>
                <div class="relative z-10 text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center font-black text-2xl mx-auto mb-6 shadow-xl border-4 border-gray-900">3</div>
                    <h3 class="text-xl font-bold mb-2">Beri Ulasan</h3>
                    <p class="text-gray-400">Setelah perawatan selesai, konfirmasi pekerjaan dan berikan penilaian Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-gray-100 bg-white py-10 sm:py-12">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 text-center sm:px-6 md:flex-row md:text-left lg:px-8">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-sm">C</div>
                <span class="text-xl font-black text-gray-900">CARUNA.</span>
            </div>
            <p class="text-gray-500 text-sm font-medium">© {{ date('Y') }} Caregiver Utility and Nursing App. Hak Cipta Dilindungi.</p>
            <div class="flex space-x-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Aman • Terpercaya • Profesional</span>
            </div>
        </div>
    </footer>

</body>
</html>
