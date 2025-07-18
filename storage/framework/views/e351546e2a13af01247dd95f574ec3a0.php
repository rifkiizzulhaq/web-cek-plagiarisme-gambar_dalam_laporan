<?php $__env->startSection('content'); ?>

<?php if($user->hasRole('mahasiswa') && (!$user->nim || !$user->prodi || !$user->angkatan || !$user->kelas)): ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 dark:bg-yellow-800/20 dark:border-yellow-600">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.03-1.742 3.03H4.42c-1.53 0-2.493-1.696-1.743-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                    Profil Anda belum lengkap. Silakan lengkapi data di halaman <a href="<?php echo e(route('profile.edit')); ?>" class="font-bold underline hover:text-yellow-600">Profil Saya</a> untuk bisa mengunggah dokumen.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
                
                <form id="uploadForm" action="<?php echo e(route('mahasiswa.upload.file')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="grid gap-y-4">
                        
                        <div class="flex justify-center">
                            <div class="w-full">
                                <div id="drop-area" class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition-colors duration-200 ease-in-out dark:hover:bg-neutral-700">
                                    <div class="flex justify-center mb-4">
                                        
                                        <svg class="size-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" x2="12" y1="3" y2="15"></line>
                                        </svg>
                                    </div>
                                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">
                                        Drop your file here or <span class="text-blue-600">browse</span>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Format file .docx
                                    </p>
                                    <input type="file" id="fileInput" name="file" class="hidden" accept=".docx">
                                </div>
                            </div>
                        </div>

                        
                        <div id="progress-area" class="hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center"></p>
                        </div>
                    </div>
                </form>

                <div class="mt-4 space-y-4">
                    <?php if(session('success')): ?>
                    <div class="p-4 border border-green-200 rounded-lg bg-green-50 dark:bg-green-800/10 dark:border-green-900/10">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="size-4 text-green-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <div class="ms-3">
                                <p class="text-sm text-green-700 dark:text-green-400">
                                    <?php echo e(session('success')); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                    <div class="p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-800/10 dark:border-red-900/10">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="size-4 text-red-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                            </div>
                            <div class="ms-3">
                                <p class="text-sm text-red-700 dark:text-red-400">
                                    <?php echo e($errors->first()); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('fileInput');
    const form = document.getElementById('uploadForm');
    const progressArea = document.getElementById('progress-area');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        if (dropArea) {
            dropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        }
    });

    function highlight(e) {
        dropArea.classList.add('bg-gray-50', 'dark:bg-neutral-700');
    }

    function unhighlight(e) {
        dropArea.classList.remove('bg-gray-50', 'dark:bg-neutral-700');
    }

    if (dropArea) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        dropArea.addEventListener('drop', handleDrop, false);
        dropArea.addEventListener('click', () => fileInput.click());
    }

    if (fileInput) {
        fileInput.addEventListener('change', handleFiles);
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files: files } });
    }

    function handleFiles(e) {
        const files = e.target.files;
        if (files.length) {
            uploadFile(files[0]);
        }
    }

    function uploadFile(file) {
        if (!file.name.toLowerCase().endsWith('.docx')) {
            showError('Hanya file .docx yang diperbolehkan');
            return;
        }

        const formData = new FormData(form);
        formData.set('file', file);

        progressArea.classList.remove('hidden');
        progressText.textContent = 'Mengupload...';

        const existingError = form.parentNode.querySelector('.error-message-container');
        if (existingError) {
            existingError.remove();
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                progressBar.style.width = '100%';
                progressText.textContent = 'Upload berhasil! Mengalihkan...';
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan yang tidak diketahui.');
            }
        })
        .catch(error => {
            // 'data' mungkin tidak ada, jadi kita beri objek kosong
            showError(error.message, error.responseJSON || {}); 
            progressBar.style.width = '0%';
            progressArea.classList.add('hidden');
        });
    }

    function showError(message, data = {}) {
        const errorContainer = form.parentNode;
        const existingError = errorContainer.querySelector('.error-message-container');

        if (data.action === 'redirect_to_profile') {
            Swal.fire({
                title: 'Profil Tidak Lengkap',
                text: message,
                icon: 'warning',
                confirmButtonText: 'Lengkapi Profil',
                showCancelButton: true,
                cancelButtonText: 'Nanti Saja'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = data.redirect_url;
                }
            });
        } else {
            // Notifikasi error biasa
            Swal.fire({
                title: 'Terjadi Kesalahan',
                text: message,
                icon: 'error'
            });
        }

        if (existingError) {
            existingError.remove();
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message-container mt-4 p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-800/10 dark:border-red-900/10';
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
        errorContainer.appendChild(errorDiv);
    }
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/CekPlagiarisme.blade.php ENDPATH**/ ?>