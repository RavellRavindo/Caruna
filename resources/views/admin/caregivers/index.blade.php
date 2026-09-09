<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Verifikasi Caregiver
            </h2>
            <p class="mt-1 text-sm text-gray-500">Tinjau data pendaftaran sebelum caregiver dapat menerima pesanan.</p>
        </div>
    </x-slot>

    <div class="page-shell bg-gray-50/50">
        <div class="page-container">
            @if (session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-bold">Perubahan belum dapat disimpan.</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm sm:rounded-3xl">
                <div class="border-b border-gray-100 px-5 py-5 sm:px-7">
                    <h3 class="font-bold text-gray-900">Daftar Caregiver</h3>
                    <p class="mt-1 text-sm text-gray-500">Caregiver hanya akan tampil kepada klien setelah statusnya disetujui.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($caregivers as $caregiver)
                        @php
                            $status = $caregiver->verification_status;
                            $statusClasses = match ($status) {
                                'verified' => 'border-green-200 bg-green-50 text-green-700',
                                'rejected' => 'border-red-200 bg-red-50 text-red-700',
                                default => 'border-yellow-200 bg-yellow-50 text-yellow-700',
                            };
                            $statusLabel = match ($status) {
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                                'pending' => 'Menunggu Verifikasi',
                                default => 'Belum Diajukan',
                            };
                        @endphp

                        <article class="p-5 sm:p-7">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h4 class="break-words text-lg font-extrabold text-gray-900">{{ $caregiver->user->name }}</h4>
                                        <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <p class="mt-1 break-all text-sm text-gray-500">{{ $caregiver->user->email }}</p>

                                    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3 sm:gap-5">
                                        <div>
                                            <dt class="font-medium text-gray-400">Spesialisasi</dt>
                                            <dd class="mt-1 font-semibold text-gray-800">{{ $caregiver->specialization }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-medium text-gray-400">Pengalaman</dt>
                                            <dd class="mt-1 font-semibold text-gray-800">{{ $caregiver->experience_years }} tahun</dd>
                                        </div>
                                        <div>
                                            <dt class="font-medium text-gray-400">Tarif per hari</dt>
                                            <dd class="mt-1 font-semibold text-gray-800">Rp {{ number_format($caregiver->price_per_day, 0, ',', '.') }}</dd>
                                        </div>
                                    </dl>

                                    @if ($caregiver->about_me)
                                        <p class="mt-4 max-w-3xl text-sm leading-relaxed text-gray-600">{{ $caregiver->about_me }}</p>
                                    @endif

                                    @if ($caregiver->verification_status === 'rejected' && $caregiver->rejection_reason)
                                        <p class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                                            <span class="font-bold">Alasan penolakan:</span> {{ $caregiver->rejection_reason }}
                                        </p>
                                    @endif
                                </div>

                                <div class="w-full shrink-0 space-y-3 lg:w-80">
                                    @if (! $caregiver->isVerified())
                                        <form method="POST" action="{{ route('admin.caregivers.verification.update', $caregiver) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="verification_status" value="verified">
                                            <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-green-700">
                                                Setujui Caregiver
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.caregivers.verification.update', $caregiver) }}" class="rounded-xl border border-red-100 bg-red-50/50 p-3">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="verification_status" value="rejected">
                                            <label for="rejection_reason_{{ $caregiver->id }}" class="text-xs font-bold text-red-700">Alasan penolakan</label>
                                            <textarea id="rejection_reason_{{ $caregiver->id }}" name="rejection_reason" rows="2" required class="mt-1 w-full rounded-lg border-red-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: data profesional belum lengkap."></textarea>
                                            <button type="submit" class="mt-2 w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-bold text-red-700 transition hover:bg-red-100">
                                                Tolak Pendaftaran
                                            </button>
                                        </form>
                                    @else
                                        <p class="rounded-xl border border-green-100 bg-green-50 p-3 text-center text-sm font-semibold text-green-700">
                                            Dapat menerima pesanan
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="p-12 text-center">
                            <p class="font-semibold text-gray-800">Belum ada caregiver terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $caregivers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
