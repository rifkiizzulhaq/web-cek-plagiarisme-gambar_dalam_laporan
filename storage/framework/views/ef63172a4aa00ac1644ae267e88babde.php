

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Tambah Akun Mahasiswa Baru</h1>

    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
        <form id="data-form" action="<?php echo e(route('admin.mahasiswa.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="space-y-6">
                
                <div>
                    <label for="nim" class="block text-sm font-medium mb-2 dark:text-white">NIM</label>
                    <input type="text" name="nim" id="nim" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="<?php echo e(old('nim')); ?>" placeholder="Contoh: 21030..." required>
                    <?php $__errorArgs = ['nim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="name" class="block text-sm font-medium mb-2 dark:text-white">Nama</label>
                    <input type="text" name="name" id="name" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="<?php echo e(old('name')); ?>" placeholder="Masukkan nama lengkap" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                
                <div>
                    <label for="prodi" class="block text-sm font-medium mb-2 dark:text-white">Program Studi</label>
                    <select id="prodi" name="prodi" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                        <option value="" disabled selected>Pilih Program Studi</option>
                        <?php $__currentLoopData = $prodiOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($prodi); ?>" <?php echo e(old('prodi') == $prodi ? 'selected' : ''); ?>><?php echo e($prodi); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['prodi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <div>
                        <label for="angkatan" class="block text-sm font-medium mb-2 dark:text-white">Tahun Angkatan</label>
                        <input type="number" name="angkatan" id="angkatan" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="<?php echo e(old('angkatan')); ?>" placeholder="Contoh: 2021" required>
                        <?php $__errorArgs = ['angkatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="kelas_detail" class="block text-sm font-medium mb-2 dark:text-white">Detail Kelas</label>
                        <div class="flex rounded-lg shadow-sm">
                            <div class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                <span id="prodi-prefix" class="text-sm text-gray-500 dark:text-neutral-400">Pilih Prodi</span>
                            </div>
                            <input type="text" name="kelas_detail" id="kelas_detail" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm" value="<?php echo e(old('kelas_detail')); ?>" placeholder="Contoh: 4A" required>
                        </div>
                        <?php $__errorArgs = ['kelas_detail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div>
                    <label for="email_username" class="block text-sm font-medium mb-2 dark:text-white">Email</label>
                    <div class="flex rounded-lg shadow-sm">
                        <input type="text" id="email_username" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-s-lg text-sm" placeholder="Nama Email" required>
                        <div class="px-4 inline-flex items-center min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                            <span class="text-sm text-gray-500 dark:text-neutral-400">@gmail.com</span>
                        </div>
                    </div>
                    <input type="hidden" name="email" id="full_email">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="password" class="block text-sm font-medium mb-2 dark:text-white">Password</label>
                    <input type="password" name="password" id="password" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" placeholder="Masukkan password" required>
                     <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2 dark:text-white">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" placeholder="Ketik ulang password" required>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-x-2">
                <a href="<?php echo e(route('admin.mahasiswa.index')); ?>" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const prodiSelect = document.getElementById('prodi');
    const prodiPrefixSpan = document.getElementById('prodi-prefix');
    const emailUsernameInput = document.getElementById('email_username');
    const fullEmailInput = document.getElementById('full_email');
    const form = document.getElementById('data-form');
    
    const prodiAbbreviations = <?php echo json_encode($prodiAbbreviations, 15, 512) ?>;

    if (!form) return;

    let isFormDirty = false;

    form.addEventListener('input', function() {
        isFormDirty = true;
    });
    form.addEventListener('submit', function() {
        isFormDirty = false;
    });

    window.addEventListener('beforeunload', function (e) {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    function updatePrefix() {
        const selectedProdi = prodiSelect.value;
        const prefix = prodiAbbreviations[selectedProdi] || 'Pilih Prodi';
        prodiPrefixSpan.textContent = prefix + '-';
    }

    function updateFullEmail() {
        if(emailUsernameInput && fullEmailInput) {
            let username = emailUsernameInput.value;
            if (username.includes('@gmail.com')) {
                fullEmailInput.value = username;
            } else {
                fullEmailInput.value = username + '@gmail.com';
            }
        }
    }

    if(emailUsernameInput) {
        emailUsernameInput.addEventListener('input', updateFullEmail);
        updateFullEmail(); 
    }

    updatePrefix();
    prodiSelect.addEventListener('change', updatePrefix);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/Admin/mahasiswa/create.blade.php ENDPATH**/ ?>