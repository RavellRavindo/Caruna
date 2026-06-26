<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Pasien Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap Pasien</label>
                        <input type="text" name="full_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
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

                    <div class="flex items-center justify-end">
                        <a href="{{ route('patients.index') }}" class="text-gray-600 mr-4 hover:underline">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Simpan Pasien
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>