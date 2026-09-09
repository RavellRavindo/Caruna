<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Rekonsiliasi Refund Pembayaran
            </h2>
            <p class="mt-1 text-sm text-gray-500">Tindak lanjuti pembayaran yang sukses setelah booking dibatalkan.</p>
        </div>
    </x-slot>

    <div class="page-shell min-h-screen bg-gray-50/50">
        <div class="page-container">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
                    {{ session('error') ?? 'Data refund belum valid. Periksa kembali nomor referensi refund.' }}
                </div>
            @endif

            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900 sm:p-5">
                <p class="font-bold">Tindakan manual diperlukan.</p>
                <p class="mt-1">Proses refund terlebih dahulu di Midtrans Dashboard atau kanal pembayaran terkait. Setelah dana dikembalikan, simpan nomor referensinya di sini. Tombol di bawah hanya mencatat bukti refund; tidak mengirim dana otomatis.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm sm:rounded-3xl">
                <div class="border-b border-gray-100 px-5 py-5 sm:px-7">
                    <h3 class="font-bold text-gray-900">Refund yang perlu diproses</h3>
                    <p class="mt-1 text-sm text-gray-500">Booking tetap dibatalkan agar layanan tidak aktif kembali tanpa persetujuan baru.</p>
                </div>

                <div class="table-scroll">
                    <table class="min-w-[980px] w-full text-left">
                        <thead class="border-b border-gray-100 bg-gray-50/70">
                            <tr>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Booking & Klien</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Order Midtrans</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Nominal</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Callback</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Catat Refund</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($payments as $payment)
                                <tr class="align-top">
                                    <td class="px-5 py-5">
                                        <p class="font-bold text-gray-900">Booking #{{ $payment->booking->id }}</p>
                                        <p class="mt-1 text-sm text-gray-600">{{ $payment->booking->user->name }}</p>
                                        <p class="mt-1 text-xs text-gray-400">Caregiver: {{ $payment->booking->caregiver->user->name }}</p>
                                    </td>
                                    <td class="px-5 py-5">
                                        <p class="max-w-52 break-all font-mono text-xs text-gray-700">{{ $payment->order_id }}</p>
                                        <p class="mt-2 text-xs font-semibold text-amber-700">{{ $payment->midtrans_status ?? 'success' }}</p>
                                    </td>
                                    <td class="px-5 py-5">
                                        <p class="font-black text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-5 py-5">
                                        <p class="text-sm font-semibold text-gray-800">{{ $payment->last_callback_at?->format('d M Y, H:i') ?? '-' }}</p>
                                        <p class="mt-1 max-w-56 text-xs leading-relaxed text-gray-500">{{ $payment->reconciliation_note }}</p>
                                    </td>
                                    <td class="px-5 py-5">
                                        <form method="POST" action="{{ route('admin.payments.reconciliation.refunded', $payment) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="sr-only" for="refund_reference_{{ $payment->id }}">Nomor referensi refund</label>
                                            <input id="refund_reference_{{ $payment->id }}" name="refund_reference" required maxlength="255" class="w-56 rounded-lg border-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nomor referensi refund">
                                            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700" onclick="return confirm('Pastikan refund telah diproses di kanal pembayaran sebelum mencatatnya.')">
                                                Tandai Refund Selesai
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-14 text-center">
                                        <p class="font-semibold text-gray-800">Tidak ada refund pembayaran yang perlu diproses.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($payments->hasPages())
                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
