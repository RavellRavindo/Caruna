<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h2 class="text-2xl font-bold mb-4">Selesaikan Pembayaran Anda</h2>
                <p class="text-gray-600 mb-6">Total tagihan untuk perawatan pasien <b>{{ $booking->patient->full_name }}</b> adalah:</p>
                
                <h3 class="text-4xl font-black text-green-600 mb-8">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h3>

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
