<x-app-layout>
    <div class="page-shell min-h-screen bg-gray-50/50">
        <div class="page-container">
            
            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border-l-4 border-green-500 bg-green-100 p-4 text-green-700 shadow-sm">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <div>
                        <p class="font-bold">Berhasil</p>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border-l-4 border-red-500 bg-red-100 p-4 text-red-700 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    <div>
                        <p class="font-bold">Gagal</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="relative mb-6 flex flex-col gap-6 overflow-hidden rounded-2xl border border-indigo-500 bg-gradient-to-r from-indigo-600 to-purple-600 p-5 text-white shadow-xl sm:mb-8 sm:rounded-3xl sm:p-8 md:flex-row md:items-center md:justify-between">
                <div class="absolute top-0 right-0 opacity-10 pointer-events-none">
                    <svg class="w-64 h-64 -mt-10 -mr-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0l12 12-12 12L0 12z"/></svg>
                </div>

                <div class="relative z-10 flex items-center gap-3 sm:gap-5">
                    <div class="bg-white/20 p-4 rounded-xl backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-white">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-indigo-100 text-sm font-medium mb-1 tracking-wide">Total Saldo Pendapatan</p>
                        <h3 class="break-words text-3xl font-black tracking-tight sm:text-4xl">
                            Rp {{ number_format(Auth::user()->caregiver->balance ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                
                <div class="w-full md:w-auto text-center relative z-10">
                    <button type="button" onclick="document.getElementById('withdrawModal').classList.remove('hidden')" class="w-full md:w-auto bg-white text-indigo-700 hover:bg-gray-100 font-bold py-3 px-8 rounded-xl shadow-md transition-all hover:scale-105 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        Tarik Dana
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm sm:rounded-3xl">
                <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6 md:p-8">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-indigo-500">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Riwayat Mutasi Saldo
                    </h3>
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Semua Transaksi</span>
                </div>

                <div class="table-scroll">
                    <table class="min-w-[620px] w-full border-collapse text-left">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($mutations as $mutation)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap w-48">
                                        <p class="text-sm font-bold text-gray-900">{{ $mutation->created_at->format('d M Y') }}</p>
                                        <p class="text-xs font-medium text-gray-400">{{ $mutation->created_at->format('H:i') }} WIB</p>
                                    </td>
                                    
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            @if($mutation->type === 'credit')
                                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $mutation->description }}</p>
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-widest mt-0.5">TRX-{{ str_pad($mutation->id, 5, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        @if($mutation->type === 'credit')
                                            <p class="text-lg font-black text-green-600">+ Rp {{ number_format($mutation->amount, 0, ',', '.') }}</p>
                                        @else
                                            <p class="text-lg font-black text-red-600">- Rp {{ number_format($mutation->amount, 0, ',', '.') }}</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-400 mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada riwayat transaksi di dompet Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($mutations, 'hasPages') && $mutations->hasPages())
                    <div class="border-t border-gray-100 bg-gray-50/50 px-4 py-4 sm:px-6">
                        {{ $mutations->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div id="withdrawModal" class="fixed inset-0 z-50 hidden flex h-full w-full items-end justify-center overflow-y-auto bg-gray-900/60 backdrop-blur-sm transition-all sm:items-center">
        <div class="relative m-0 max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl sm:m-4 sm:rounded-3xl sm:p-8">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Form Penarikan Dana</h3>
                <button type="button" onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('withdrawals.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $withdrawalIdempotencyKey }}">
                
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Penarikan (Rp)</label>
                    <input type="number" name="amount" min="50000" step="1" max="{{ Auth::user()->caregiver->balance ?? 0 }}" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold" placeholder="Contoh: 150000">
                    <p class="text-xs text-indigo-600 mt-2 font-semibold">Saldo Maksimal: Rp {{ number_format(Auth::user()->caregiver->balance ?? 0, 0, ',', '.') }}</p>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Bank Tujuan</label>
                    <select name="bank_name" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Bank / E-Wallet --</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                        <option value="GoPay">GoPay</option>
                        <option value="OVO">OVO</option>
                        <option value="Dana">DANA</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Rekening / E-Wallet</label>
                    <input type="text" name="account_number" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 1234567890">
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Pemilik Rekening</label>
                    <input type="text" name="account_name" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Sesuai nama di buku tabungan">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-200 transition">
                    Ajukan Penarikan Sekarang
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
