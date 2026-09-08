<x-app-layout>
    <div class="page-shell">
        <div class="page-container max-w-3xl text-center">
            <div class="rounded-2xl bg-white p-5 shadow-sm sm:p-8">
                <h2 class="mb-4 text-xl font-bold sm:text-2xl">Selesaikan Pembayaran Anda</h2>
                <p class="text-gray-600 mb-6">Total tagihan untuk perawatan pasien <b>{{ $booking->patient->full_name }}</b> adalah:</p>
                
                <h3 class="mb-8 break-words text-3xl font-black text-green-600 sm:text-4xl">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h3>

                <button id="pay-button" class="bg-indigo-600 text-white font-bold px-8 py-4 rounded-xl hover:bg-indigo-700 transition w-full md:w-auto shadow-lg">
                    <i class="fa-solid fa-credit-card mr-2"></i> Pilih Metode Pembayaran
                </button>
            </div>
        </div>
    </div>

    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            // Memanggil pop-up Snap Midtrans dengan token yang digenerate di Controller
            snap.pay({{ Illuminate\Support\Js::from($snapToken) }}, {
                onSuccess: function(result){
                    // Callback Midtrans diproses secara asynchronous. Halaman berikutnya
                    // akan menunggu sampai status di server benar-benar telah diperbarui.
                    window.location.href = "{{ route('bookings.payment.success', $booking->id) }}";
                },
                onPending: function(result){
                    alert("Menunggu pembayaran Anda!"); console.log(result);
                },
                onError: function(result){
                    alert("Pembayaran gagal!"); console.log(result);
                },
                onClose: function(){
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran');
                }
            });
        };
    </script>
</x-app-layout>
