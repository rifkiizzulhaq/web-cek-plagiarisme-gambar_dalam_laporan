@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="lg:text-2xl font-bold text-gray-800 dark:text-white">Kelola Akun Mahasiswa</h1>
        
        <div class="flex gap-x-2">
            @if ($mahasiswas->isNotEmpty())
                <a href="{{ route('admin.mahasiswa.export') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-green-600 text-white hover:bg-green-700">
                    Export Excel
                </a>
            @endif
            <a href="{{ route('admin.mahasiswa.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Tambah Mahasiswa
            </a>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
                    <div class="py-3 px-4 flex justify-between items-center">
                        {{-- Form Pencarian di Kiri --}}
                        <form action="{{ route('admin.mahasiswa.index') }}" method="GET">
                            <div class="relative max-w-xs">
                                <label class="sr-only">Search</label>
                                <input type="text" name="search" id="search-input" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg sm:text-sm ..." placeholder="Cari nama atau email..." value="{{ request('search') }}">
                                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                    <svg class="size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>
                        
                        <form id="bulk-delete-form" action="{{ route('admin.mahasiswa.bulkDestroy') }}"
                            method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="ids" id="ids-to-delete">
                            <button type="button" id="delete-selected-btn" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700">
                                Hapus yang Terpilih
                            </button>
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th scope="col" class="py-3 ps-4 pe-0">
                                        <div class="flex items-center h-5">
                                            <input id="hs-table-pagination-checkbox-all" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500">
                                            <label for="hs-table-pagination-checkbox-all" class="sr-only">Checkbox</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">No.</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Email</th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse ($mahasiswas as $mahasiswa)
                                    <tr>
                                        <td class="py-3 ps-4">
                                            <div class="flex items-center h-5">
                                                <input id="checkbox-{{ $mahasiswa->id }}" type="checkbox" value="{{ $mahasiswa->id }}" class="row-checkbox border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500">
                                                <label for="checkbox-{{ $mahasiswa->id }}" class="sr-only">Checkbox</label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-800 dark:text-neutral-200">{{ $mahasiswas->firstItem() + $loop->index }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $mahasiswa->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            <div>
                                                {{ $mahasiswa->email }}
                                                @if ($mahasiswa->google_id)
                                                    <span class="block bg-gray-300 dark:bg-white w-14 px-2 text-xs rounded-full text-left text-blue-600 dark:text-blue-500">
                                                        Google
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <div class="row-actions invisible opacity-0 transition-opacity duration-300">
                                                <div class="flex justify-end gap-x-2">
                                                    <a href="{{ route('admin.mahasiswa.show', $mahasiswa->id) }}" class="text-blue-600 hover:text-blue-800">Riwayat</a>
                                                    <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->id) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                                    <form action="{{ route('admin.mahasiswa.destroy', $mahasiswa->id) }}" method="POST" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada data mahasiswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($mahasiswas->total() > $mahasiswas->perPage())
                        <div class="py-1 px-4">
                            {{ $mahasiswas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    const selectAllCheckbox = document.getElementById('hs-table-pagination-checkbox-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const deleteForms = document.querySelectorAll('.delete-form');
    const bulkActionsDiv = document.getElementById('bulk-delete-form');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const idsToDeleteInput = document.getElementById('ids-to-delete');
    const storageKey = 'checkedMahasiswaIds';

    function saveCheckedState() {
        const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        sessionStorage.setItem(storageKey, JSON.stringify(checkedIds));
    }

    function loadCheckedState() {
        const checkedIds = JSON.parse(sessionStorage.getItem(storageKey)) || [];
        rowCheckboxes.forEach(checkbox => {
            if (checkedIds.includes(checkbox.value)) {
                checkbox.checked = true;
            }
        });
    }
    
    function updateUiState() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;

        rowCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row) {
                const actions = row.querySelector('.row-actions');
                if (actions) {
                    actions.classList.toggle('invisible', !checkbox.checked);
                    actions.classList.toggle('opacity-0', !checkbox.checked);
                    actions.classList.toggle('opacity-100', checkbox.checked);
                }
            }
        });
        
        
        if (bulkActionsDiv) {
            bulkActionsDiv.classList.toggle('hidden', checkedCount < 2);
        }

        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = rowCheckboxes.length > 0 && checkedCount === rowCheckboxes.length;
        }
    }

    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateUiState();
            saveCheckedState();
        });
    }

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            updateUiState();
            saveCheckedState();
        });
    });
    
    
    loadCheckedState();
    updateUiState();

    
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                          .map(cb => cb.value);
            Swal.fire({
                title: `Hapus ${checkedIds.length} mahasiswa?`,
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionStorage.removeItem(storageKey);
                    idsToDeleteInput.value = JSON.stringify(checkedIds);
                    bulkDeleteForm.submit();
                }
            });
        });
    }
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionStorage.removeItem(storageKey);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush