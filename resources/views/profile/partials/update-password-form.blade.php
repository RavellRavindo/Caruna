<section>
    <header class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
            <i class="fa-solid fa-key"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-gray-900">Keamanan kata sandi</h2>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">Gunakan kata sandi yang panjang dan unik untuk menjaga keamanan akun Anda.</p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-7 space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata sandi saat ini')" class="font-bold text-gray-700" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Kata sandi baru')" class="font-bold text-gray-700" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi kata sandi baru')" class="font-bold text-gray-700" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col items-stretch gap-3 border-t border-gray-100 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-violet-100 transition hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 sm:w-auto">
                <i class="fa-solid fa-key"></i> Perbarui kata sandi
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-green-600"
                >Kata sandi diperbarui.</p>
            @endif
        </div>
    </form>
</section>
