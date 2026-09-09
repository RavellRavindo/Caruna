<section class="space-y-6">
    <header class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-gray-900">Zona berbahaya</h2>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">Penghapusan akun bersifat permanen dan seluruh data terkait akan dihapus.</p>
        </div>
    </header>

    <div class="rounded-2xl border border-red-100 bg-red-50 p-4 sm:p-5">
        <p class="text-sm leading-relaxed text-red-800">Pastikan tidak ada booking aktif atau data yang masih ingin Anda simpan sebelum melanjutkan.</p>
    </div>

    <button type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-bold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    ><i class="fa-solid fa-trash"></i> Hapus akun</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-7">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-gray-900">
                Hapus akun secara permanen?
            </h2>

            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi untuk mengonfirmasi penghapusan akun.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata sandi') }}" class="font-bold text-gray-700" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="Masukkan kata sandi Anda"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-secondary-button class="w-full justify-center rounded-xl px-5 py-3 sm:w-auto" x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button class="w-full justify-center rounded-xl px-5 py-3 sm:ms-3 sm:w-auto">
                    Hapus permanen
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
