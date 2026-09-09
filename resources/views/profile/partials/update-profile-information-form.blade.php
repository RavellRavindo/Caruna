<section>
    <header class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-gray-900">Informasi profil</h2>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">Pastikan data ini sesuai agar proses booking dan komunikasi berjalan lancar.</p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-7 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Nama lengkap')" class="font-bold text-gray-700" />
                <x-text-input id="name" name="name" type="text" class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="font-bold text-gray-700" />
                <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3">
                    <p class="text-xs leading-relaxed text-amber-800">
                        Email belum diverifikasi.
                        <button form="send-verification" class="font-bold underline underline-offset-2 hover:text-amber-950">Kirim ulang tautan verifikasi</button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-green-600">Tautan verifikasi baru telah dikirim.</p>
                    @endif
                </div>
            @endif
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4 sm:p-5">
            <p class="text-xs font-black uppercase tracking-widest text-indigo-600">Kontak & lokasi</p>
            <p class="mt-1 text-sm text-gray-500">Data ini membantu pasangan booking menghubungi Anda setelah pembayaran berhasil.</p>

            <div class="mt-5 space-y-5">
                <div>
                    <x-input-label for="phone_number" :value="__('Nomor WhatsApp')" class="font-bold text-gray-700" />
                    <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('phone_number', $user->phone_number)" autocomplete="tel" placeholder="Contoh: 081234567890" />
                    <p class="mt-2 text-xs text-gray-500">Nomor ini hanya dibuka kepada pasangan booking setelah pembayaran berhasil.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                </div>

                <div>
                    <x-input-label for="address" :value="__('Alamat domisili')" class="font-bold text-gray-700" />
                    <textarea id="address" name="address" rows="3" class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="street-address" placeholder="Alamat ini akan menjadi isian awal alamat layanan saat membuat booking.">{{ old('address', $user->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </div>

        <div class="flex flex-col items-stretch gap-3 border-t border-gray-100 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                <i class="fa-solid fa-floppy-disk"></i> Simpan perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-green-600"
                >Perubahan tersimpan.</p>
            @endif
        </div>
    </form>
</section>
