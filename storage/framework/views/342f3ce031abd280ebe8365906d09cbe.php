<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Profil Saya</h1>

    <div class="space-y-6">
        
        <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">
                Informasi Profil
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Perbarui informasi profil dan alamat email akun Anda.
            </p>

            <form id="data-form" method="post" action="<?php echo e(route('profile.update')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('patch'); ?>

                
                <?php if(Auth::user()->hasRole('mahasiswa')): ?>
                    <div>
                        <label for="nim" class="block text-sm font-medium mb-2 dark:text-white">NIM</label>
                        
                        <input id="nim" name="nim" type="text" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="<?php echo e(old('nim', $user->nim)); ?>" placeholder="Contoh: 21030..." required>
                        <?php $__errorArgs = ['nim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                <?php endif; ?>
                
                <div>
                    <label for="name" class="block text-sm font-medium mb-2 dark:text-white">Nama</label>
                    <input id="name" name="name" type="text" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="<?php echo e(old('name', $user->name)); ?>" placeholder="Masukkan nama lengkap" required autofocus>
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
                    <label for="email" class="block text-sm font-medium mb-2 dark:text-white">Email</label>
                    
                    <?php if(Auth::user()->google_id): ?>
                        
                        <input id="email" name="email" type="email" 
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm bg-gray-100 dark:bg-neutral-700" 
                            value="<?php echo e($user->email); ?>" 
                            readonly>
                        <p class="text-xs text-gray-500 mt-1">Email tidak bisa diubah karena terhubung dengan akun Google.</p>
                    
                    <?php else: ?>
                        
                        <?php
                            $emailParts = explode('@', old('email', $user->email));
                            $emailUsername = $emailParts[0];
                        ?>
                        <div class="flex rounded-lg shadow-sm">
                            <input type="text" id="email_username" 
                                class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-s-lg text-sm" 
                                value="<?php echo e($emailUsername); ?>" 
                                placeholder="nama.unik" required>
                            <div class="px-4 inline-flex items-center min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                <span class="text-sm text-gray-500 dark:text-neutral-400">@gmail.com</span>
                            </div>
                        </div>
                        <input type="hidden" name="email" id="full_email" value="<?php echo e(old('email', $user->email)); ?>">
                    <?php endif; ?>

                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <?php if(Auth::user()->hasRole('mahasiswa')): ?>
                    <div>
                        <label for="prodi" class="block text-sm font-medium mb-2 dark:text-white">Program Studi</label>
                        
                        <select id="prodi" name="prodi" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                            <option value="">Pilih Program Studi</option>
                            <?php $__currentLoopData = $prodiOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($prodi); ?>" <?php echo e(old('prodi', $user->prodi) == $prodi ? 'selected' : ''); ?>><?php echo e($prodi); ?></option>
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
                            
                            <input id="angkatan" name="angkatan" type="number" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="<?php echo e(old('angkatan', $user->angkatan)); ?>" placeholder="Contoh: 2021" required>
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
                            <?php
                                $prodiAbbr = $prodiAbbreviations[$user->prodi] ?? 'PRODI';
                                $kelasDetail = $user->kelas ? str_replace($prodiAbbr . '-', '', $user->kelas) : '';
                            ?>
                            <label for="kelas_detail" class="block text-sm font-medium mb-2 dark:text-white">Detail Kelas</label>
                            <div class="flex rounded-lg shadow-sm">
                                <div class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                    <span id="prodi-prefix" class="text-sm text-gray-500 dark:text-neutral-400"><?php echo e($prodiAbbr); ?>-</span>
                                </div>
                                <input type="text" name="kelas_detail" id="kelas_detail" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm" value="<?php echo e(old('kelas_detail', $kelasDetail)); ?>" placeholder="Contoh: 4A" required>
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
                <?php endif; ?>

                <div class="mt-6 flex justify-between items-center">
                    
                    <!-- <div>
                        <?php if(session('status') === 'profile-updated'): ?>
                            <p class="text-sm text-green-600 dark:text-green-400"
                            x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                                Tersimpan.
                            </p>
                        <?php endif; ?>
                    </div> -->

                    
                    <div class="flex gap-x-2">
                        <a href="<?php echo e(Auth::user()->hasRole('admin') ? route('admin.admin-halaman-utama') : route('mahasiswa.cek-plagiarisme')); ?>" 
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                            Batal
                        </a>
                        <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        
        <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
            <?php echo $__env->make('profile.partials.update-password-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        
        <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
            <?php echo $__env->make('profile.partials.delete-user-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('data-form');
    if (!form) return;

    let isFormDirty = false;
    form.addEventListener('input', () => { isFormDirty = true; });
    form.addEventListener('submit', () => { isFormDirty = false; });
    window.addEventListener('beforeunload', (e) => {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Cek apakah ini halaman untuk mahasiswa
    <?php if(Auth::user()->hasRole('mahasiswa')): ?>
        // --- LOGIKA UNTUK PRODI & KELAS ---
        const prodiSelect = document.getElementById('prodi');
        const prodiPrefixSpan = document.getElementById('prodi-prefix');
        const prodiAbbreviations = <?php echo json_encode($prodiAbbreviations ?? [], 15, 512) ?>;

        function updatePrefix() {
            if (prodiSelect && prodiPrefixSpan) {
                const selectedProdi = prodiSelect.value;
                const prefix = prodiAbbreviations[selectedProdi] || 'Pilih Prodi';
                prodiPrefixSpan.textContent = prefix + '-';
            }
        }

        if (prodiSelect) {
            prodiSelect.addEventListener('change', updatePrefix);
            // Panggil sekali saat halaman dimuat untuk menampilkan prefix awal
            updatePrefix();
        }

        // --- LOGIKA UNTUK EMAIL (KHUSUS NON-GOOGLE) ---
        const emailUsernameInput = document.getElementById('email_username');
        const fullEmailInput = document.getElementById('full_email');
        
        function updateFullEmail() {
            if (emailUsernameInput && fullEmailInput) {
                let username = emailUsernameInput.value;
                if (username.includes('@')) {
                    fullEmailInput.value = username;
                } else {
                    fullEmailInput.value = username + '@gmail.com';
                }
            }
        }

        if(emailUsernameInput) {
            emailUsernameInput.addEventListener('input', updateFullEmail);
            // Panggil sekali saat halaman dimuat untuk memastikan nilai awal benar
            updateFullEmail(); 
        }
    <?php endif; ?>
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/profile/edit.blade.php ENDPATH**/ ?>