<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Pasien Baru') }}
        </h2>
    </x-slot>

    <div class="page-shell">
        <div class="page-container max-w-3xl">
            <div class="overflow-hidden rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <form action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap Pasien</label>
                        <input type="text" name="full_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Kondisi Kesehatan</label>
                        <textarea name="health_condition" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Pasca Operasi, Diabetes, dll" required></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Kontak Darurat</label>
                        <input type="text" name="emergency_contact" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="0812xxxxxxxx" required>
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('patients.index') }}" class="text-center text-gray-600 hover:underline sm:mr-4">Batal</a>
                        <button type="submit" class="w-full rounded bg-blue-600 px-4 py-3 font-bold text-white transition duration-200 hover:bg-blue-700 sm:w-auto sm:py-2">
                            Simpan Pasien
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
