<section>
    <header class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-gray-900">Tarif layanan</h2>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">Atur tarif harian yang akan terlihat oleh klien pada booking baru.</p>
        </div>
    </header>

    <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 sm:p-5">
        <p class="text-xs font-black uppercase tracking-widest text-emerald-700">Tarif saat ini</p>
        <p class="mt-1 text-2xl font-black text-emerald-700 sm:text-3xl">Rp {{ number_format($user->caregiver->price_per_day, 0, ',', '.') }} <span class="text-sm font-bold text-emerald-600">/ hari</span></p>
        <p class="mt-2 text-xs leading-relaxed text-emerald-800">Perubahan tarif tidak mengubah nilai booking yang sudah dibuat atau sudah dibayar.</p>
    </div>

    <form method="post" action="{{ route('profile.caregiver-rate.update') }}" class="mt-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="price_per_day" :value="__('Tarif baru per hari (Rp)')" class="font-bold text-gray-700" />
            <x-text-input id="price_per_day" name="price_per_day" type="number" class="mt-2 block w-full rounded-xl border-gray-200 px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('price_per_day', (int) $user->caregiver->price_per_day)" min="1" max="9999999999" step="1" required inputmode="numeric" />
            <p class="mt-2 text-xs text-gray-500">Masukkan nominal rupiah tanpa titik atau koma. Contoh: 250000.</p>
            <x-input-error class="mt-2" :messages="$errors->get('price_per_day')" />
        </div>

        <div class="mt-6 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:justify-between">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto">
                <i class="fa-solid fa-tag"></i> Perbarui tarif
            </button>

            @if (session('status') === 'caregiver-rate-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-green-600"
                >Tarif berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
