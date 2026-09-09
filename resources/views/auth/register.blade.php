<x-guest-layout>
    <!-- Header Branding CARUNA -->
    <div class="mb-6 text-center sm:mb-8">
        <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Buat Akun Baru</h2>
        <p class="text-gray-600 mt-2">Bergabunglah dengan komunitas <span class="text-indigo-600 font-bold">CARUNA</span></p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: @js(old('role', 'client')) }">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Daftar Sebagai')" />
            <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="client">Klien (Mencari Layanan)</option>
                <option value="caregiver">Caregiver (Pemberi Layanan)</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div x-show="role === 'caregiver'" x-cloak class="mt-6 rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 sm:p-5">
            <div class="mb-4">
                <h3 class="font-bold text-gray-900">Data Profesional Caregiver</h3>
                <p class="mt-1 text-sm text-gray-600">Data ini akan ditinjau admin. Akun baru belum dapat menerima pesanan sampai disetujui.</p>
            </div>

            <div>
                <x-input-label for="specialization" :value="__('Spesialisasi')" />
                <x-text-input id="specialization" class="mt-1 block w-full" type="text" name="specialization" :value="old('specialization')" autocomplete="organization-title" />
                <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="price_per_day" :value="__('Tarif per Hari (Rp)')" />
                    <x-text-input id="price_per_day" class="mt-1 block w-full" type="number" name="price_per_day" :value="old('price_per_day')" min="0" step="1000" inputmode="numeric" />
                    <x-input-error :messages="$errors->get('price_per_day')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="experience_years" :value="__('Lama Pengalaman (Tahun)')" />
                    <x-text-input id="experience_years" class="mt-1 block w-full" type="number" name="experience_years" :value="old('experience_years')" min="0" max="80" inputmode="numeric" />
                    <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="Laki-laki" @selected(old('gender') === 'Laki-laki')>Laki-laki</option>
                    <option value="Perempuan" @selected(old('gender') === 'Perempuan')>Perempuan</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="about_me" :value="__('Tentang Anda (opsional)')" />
                <textarea id="about_me" name="about_me" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('about_me') }}</textarea>
                <x-input-error :messages="$errors->get('about_me')" class="mt-2" />
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a class="underline text-sm text-gray-600 hover:text-indigo-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Masuk') }}
            </a>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-6 py-3 font-bold text-white shadow-md transition duration-150 ease-in-out hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900 sm:w-auto">
                {{ __('DAFTAR') }}
            </button>
        </div>
    </form>
</x-guest-layout>
