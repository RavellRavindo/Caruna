<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Daftar Pasien Saya') }}
            </h2>
            <a href="{{ route('patients.create') }}" class="w-full rounded bg-green-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-green-700 sm:w-auto sm:py-2">
                + Tambah Pasien
            </a>
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-container">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="table-scroll">
                <table class="min-w-[650px] w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($patients as $patient)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $patient->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $patient->gender }}</td>
                            <td class="px-6 py-4">{{ Str::limit($patient->health_condition, 50) }}</td>
                            <td class="px-6 py-4">
                                <a href="#" class="text-blue-600 hover:text-blue-900">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
