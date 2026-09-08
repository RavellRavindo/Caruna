<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Penarikan Dana') }}
        </h2>
    </x-slot>

    <div class="page-shell min-h-screen bg-gray-50/50">
        <div class="page-container">

            <!-- Pesan Sukses / Error -->
            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 p-4 text-green-700 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="mb-6 sm:mb-8">
                <div>
                    <h3 class="text-xl font-black tracking-tight text-gray-900 sm:text-2xl">Daftar Pengajuan Pencairan</h3>
                    <p class="text-gray-500 mt-1">Kelola dan proses permintaan penarikan dana dari perawat.</p>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm sm:rounded-3xl">
                <div class="table-scroll">
                    <table class="min-w-[900px] w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Pengajuan</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Perawat</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rekening Tujuan</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nominal</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            
                            <!-- Perulangan Data Penarikan -->
                            @forelse ($withdrawals as $withdrawal)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <!-- Tanggal -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-gray-900">{{ $withdrawal->created_at->format('d M Y') }}</p>
                                        <p class="text-xs font-medium text-gray-400">{{ $withdrawal->created_at->format('H:i') }} WIB</p>
                                    </td>
                                    
                                    <!-- Info Perawat -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-bold text-gray-900">{{ $withdrawal->caregiver->user->name ?? 'User Tidak Ditemukan' }}</p>
                                        <p class="text-xs font-medium text-gray-500">Saldo saat ini: Rp {{ number_format($withdrawal->caregiver->balance ?? 0, 0, ',', '.') }}</p>
                                    </td>

                                    <!-- Bank Tujuan -->
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-900">{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }}</p>
                                        <p class="text-xs font-medium text-gray-500 uppercase">A.N: {{ $withdrawal->account_name }}</p>
                                    </td>

                                    <!-- Nominal -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-black text-indigo-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($withdrawal->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200 uppercase tracking-wide">
                                                Pending
                                            </span>
                                        @elseif($withdrawal->status === 'completed' || $withdrawal->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-50 text-green-700 border border-green-200 uppercase tracking-wide">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 text-red-700 border border-red-200 uppercase tracking-wide">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi Tombol Approve/Reject -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($withdrawal->status === 'pending')
                                            <div class="flex items-center gap-2">
                                                <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda sudah mentransfer uangnya ke rekening perawat?');">
                                                    @csrf
                                                    <button type="submit" title="Setujui" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-600 hover:bg-green-600 hover:text-white transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak penarikan ini dan mengembalikan saldo perawat?');">
                                                    @csrf
                                                    <button type="submit" title="Tolak (Refund)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-400 mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada permintaan penarikan dana saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(method_exists($withdrawals, 'hasPages') && $withdrawals->hasPages())
                    <div class="border-t border-gray-100 bg-gray-50/50 px-4 py-4 sm:px-6">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
