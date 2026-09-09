<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="page-shell min-h-screen bg-gray-50/50">
        <div class="page-container max-w-6xl">
            <div class="mb-6 sm:mb-8">
                <p class="mb-2 text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Pengaturan akun</p>
                <h1 class="text-2xl font-black tracking-tight text-gray-900 sm:text-3xl">Profil & Keamanan</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-gray-500 sm:text-base">Kelola identitas, informasi kontak, dan keamanan akun Caruna Anda.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:gap-8">
                <aside class="space-y-5">
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-xl shadow-indigo-100 sm:p-8">
                        <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                        <div class="absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-white/10"></div>

                        <div class="relative z-10">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-2xl font-black ring-4 ring-white/10">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-indigo-100">Akun {{ $user->role }}</p>
                            <h2 class="mt-1 break-words text-2xl font-black">{{ $user->name }}</h2>
                            <p class="mt-2 break-all text-sm text-indigo-100">{{ $user->email }}</p>

                            <div class="mt-6 border-t border-white/20 pt-5 text-sm">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-shield-halved text-indigo-200"></i>
                                    <span>Kelola data Anda dengan aman.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-5 sm:p-6">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Privasi kontak</h3>
                                <p class="mt-1 text-sm leading-relaxed text-gray-600">Nomor WhatsApp dan alamat Anda hanya dibuka kepada pasangan booking setelah pembayaran berhasil.</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="space-y-5 lg:col-span-2 lg:space-y-6">
                    <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm sm:p-8">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    @if ($user->role === 'caregiver' && $user->caregiver)
                        <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm sm:p-8">
                            <div class="max-w-2xl">
                                @include('profile.partials.update-caregiver-rate-form')
                            </div>
                        </div>
                    @endif

                    <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm sm:p-8">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="rounded-3xl border border-red-100 bg-white p-5 shadow-sm sm:p-8">
                        <div class="max-w-2xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
