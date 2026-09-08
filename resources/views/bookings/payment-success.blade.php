<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Konfirmasi Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                </div>

                <h2 class="text-2xl font-black text-gray-900">Memverifikasi pembayaran</h2>
                <p id="payment-status-message" class="mt-3 text-gray-600" role="status" aria-live="polite">
                    Pembayaran Anda sedang dikonfirmasi. Mohon jangan tutup halaman ini.
                </p>
                <p id="payment-status-detail" class="mt-2 text-sm text-gray-400">
                    Status akan diperbarui otomatis begitu notifikasi dari Midtrans diterima.
                </p>

                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                    <button id="check-payment-status" type="button" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Cek status sekarang
                    </button>
                    <a href="{{ route('bookings.index') }}" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                        Kembali ke pesanan saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const statusUrl = {{ Illuminate\Support\Js::from(route('bookings.payment.status', $booking->id)) }};
            const bookingsUrl = {{ Illuminate\Support\Js::from(route('bookings.index')) }};
            const message = document.getElementById('payment-status-message');
            const detail = document.getElementById('payment-status-detail');
            const checkButton = document.getElementById('check-payment-status');
            const maximumAttempts = 45;
            let attempts = 0;
            let isChecking = false;
            let timer = null;

            const stopPolling = () => {
                if (timer) {
                    window.clearTimeout(timer);
                    timer = null;
                }
            };

            const scheduleNextCheck = () => {
                if (attempts < maximumAttempts) {
                    timer = window.setTimeout(checkPaymentStatus, 2000);
                    return;
                }

                detail.textContent = 'Konfirmasi masih diproses. Anda dapat menekan tombol cek status atau kembali beberapa saat lagi.';
            };

            const checkPaymentStatus = async () => {
                if (isChecking) {
                    return;
                }

                isChecking = true;
                checkButton.disabled = true;
                attempts += 1;

                try {
                    const response = await window.fetch(statusUrl, {
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Status pembayaran tidak dapat diambil.');
                    }

                    const status = await response.json();

                    if (status.payment_confirmed) {
                        stopPolling();
                        message.textContent = 'Pembayaran berhasil dikonfirmasi. Mengalihkan ke pesanan Anda...';
                        detail.textContent = 'Status booking telah diperbarui menjadi paid.';
                        window.setTimeout(() => window.location.replace(bookingsUrl), 500);
                        return;
                    }

                    if (status.payment_status === 'failed') {
                        stopPolling();
                        message.textContent = 'Pembayaran tidak berhasil dikonfirmasi.';
                        detail.textContent = 'Silakan kembali ke pesanan Anda untuk mencoba pembayaran kembali.';
                        return;
                    }

                    message.textContent = 'Pembayaran sedang dikonfirmasi oleh Midtrans.';
                    scheduleNextCheck();
                } catch (error) {
                    detail.textContent = 'Belum dapat memeriksa status. Sistem akan mencoba kembali secara otomatis.';
                    scheduleNextCheck();
                } finally {
                    isChecking = false;
                    checkButton.disabled = false;
                }
            };

            checkButton.addEventListener('click', () => {
                stopPolling();
                checkPaymentStatus();
            });

            checkPaymentStatus();
        })();
    </script>
</x-app-layout>
