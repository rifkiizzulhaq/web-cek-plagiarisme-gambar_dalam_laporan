@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-xl dark:bg-neutral-800">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Cek Plagiarisme</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Upload file yang ingin Anda cek tingkat plagiarismenya
                </p>
            </div>

            <div class="mt-5">
                <form id="uploadForm" action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid gap-y-4">
                        <!-- File Upload Area -->
                        <div class="flex justify-center">
                            <div class="w-full">
                                <div id="drop-area" class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition-colors duration-200 ease-in-out dark:hover:bg-neutral-700">
                                    <!-- Icon -->
                                    <div class="flex justify-center mb-4">
                                        <svg class="size-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" x2="12" y1="3" y2="15"></line>
                                        </svg>
                                    </div>
                                    
                                    <!-- Text -->
                                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">
                                        Drop your file here or <span class="text-blue-600">browse</span>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Format file .docx
                                    </p>

                                    <!-- Hidden File Input -->
                                    <input type="file" id="fileInput" name="file" class="hidden" accept=".docx">
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar (Hidden by default) -->
                        <div id="progress-area" class="hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center"></p>
                        </div>
                    </div>
                </form>

                @if(session('success'))
                <div class="mt-4 p-4 border border-green-200 rounded-lg bg-green-50 dark:bg-green-800/10 dark:border-green-900/10">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="size-4 text-green-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-green-700 dark:text-green-400">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mt-4 p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-800/10 dark:border-red-900/10">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="size-4 text-red-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-red-700 dark:text-red-400">
                                {{ $errors->first() }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('fileInput');
    const form = document.getElementById('uploadForm');
    const progressArea = document.getElementById('progress-area');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropArea.addEventListener('drop', handleDrop, false);

    // Handle clicked files
    dropArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFiles);

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropArea.classList.add('bg-gray-50', 'dark:bg-neutral-700');
    }

    function unhighlight(e) {
        dropArea.classList.remove('bg-gray-50', 'dark:bg-neutral-700');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFiles(e) {
        const files = e.target?.files || e;
        if (files.length) {
            const file = files[0];
            uploadFile(file);
        }
    }

    function uploadFile(file) {
        // Validate file type
        if (!file.name.toLowerCase().endsWith('.docx')) {
            showError('Hanya file .docx yang diperbolehkan');
            return;
        }

        const formData = new FormData(form);
        formData.set('file', file);

        progressArea.classList.remove('hidden');
        progressText.textContent = 'Mengupload...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                progressBar.style.width = '100%';
                progressText.textContent = 'Upload berhasil!';
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            showError(error.message);
            progressArea.classList.add('hidden');
        });
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mt-4 p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-800/10 dark:border-red-900/10';
        errorDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="size-4 text-red-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                </div>
                <div class="ms-3">
                    <p class="text-sm text-red-700 dark:text-red-400">${message}</p>
                </div>
            </div>
        `;
        
        const existingError = document.querySelector('.border-red-200');
        if (existingError) {
            existingError.remove();
        }
        form.parentNode.appendChild(errorDiv);
    }
});
</script>
@endpush

@endsection

