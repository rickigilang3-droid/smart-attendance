<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Fingerprint Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-6 lg:p-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Virtual Fingerprint Simulator 🖐️</h1>
                <p class="text-xs text-slate-500 mt-1">Simulasi tap gerbang masuk/pulang siswa & guru secara real-time.</p>
            </div>
            <a href="/admin/dashboard" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                ← Kembali ke Dashboard
            </a>
        </div>

        <!-- Toast Alert Container -->
        <div id="alert-container" class="hidden p-4 rounded-2xl text-xs font-bold border shadow-sm transition-all duration-300"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Mesin Scan Form -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm space-y-6 h-fit">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Mesin Scan Gerbang</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih tipe presensi dan masukkan NISN/NIS siswa atau NIP/Kode guru.</p>
                </div>
                <form id="gate-scan-form" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <!-- Radio Pilihan Tipe Presensi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">TIPE PRESENSI</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center p-3 border rounded-xl cursor-pointer text-xs font-bold transition hover:bg-slate-50 border-indigo-200 bg-indigo-50/50 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700">
                                <input type="radio" name="role" value="student" id="role-student" checked class="mr-2 text-indigo-600 focus:ring-indigo-500"> 👨‍🎓 Siswa
                            </label>
                            <label class="flex items-center justify-center p-3 border rounded-xl cursor-pointer text-xs font-bold transition hover:bg-slate-50 border-slate-200 bg-slate-50/50 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700">
                                <input type="radio" name="role" value="teacher" id="role-teacher" class="mr-2 text-indigo-600 focus:ring-indigo-500"> 👨‍🏫 Guru
                            </label>
                        </div>
                    </div>

                    <!-- Input NIP / NISN -->
                    <div>
                        <label id="identifier-label" class="block text-xs font-bold text-slate-700 mb-2">NISN / NIS / ID Fingerprint</label>
                        <input type="text" id="identifier-input" name="identifier" autofocus required placeholder="Masukkan NISN / NIS..."
                            class="w-full text-sm font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-mono">
                    </div>

                    <button type="submit" id="submit-btn" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-indigo-200 transition flex justify-center items-center gap-2">
                        <span>👆 TAP SENSOR GERBANG</span>
                    </button>
                </form>
            </div>

            <!-- Live Log Tap Gerbang Hari Ini -->
            <div class="lg:col-span-2 p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Riwayat Tap Gerbang Hari Ini 📋</h2>
                        <p class="text-xs text-slate-500">Log kehadiran jam masuk & pulang real-time.</p>
                    </div>

                    <!-- Tab Switcher -->
                    <div class="flex bg-slate-100 p-1 rounded-xl gap-1 text-xs font-bold">
                        <button type="button" id="tab-student-btn" onclick="switchTab('student')" class="px-3 py-1.5 rounded-lg bg-white shadow-sm text-indigo-600">
                            👨‍🎓 Siswa (<?php echo e(count($todayLogs ?? [])); ?>)
                        </button>
                        <button type="button" id="tab-teacher-btn" onclick="switchTab('teacher')" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-800">
                            👨‍🏫 Guru (<?php echo e(count($teacherLogs ?? [])); ?>)
                        </button>
                    </div>
                </div>

                <!-- TABEL LOG SISWA -->
                <div id="tab-student-content" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                                <th class="py-3 px-4">Siswa</th>
                                <th class="py-3 px-4">Jam Masuk</th>
                                <th class="py-3 px-4">Jam Pulang</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = ($todayLogs ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $isLateStudent =$log->status === 'late'
                                    || ($log->created_at && $log->created_at->format('H:i:s') > '07:15:00');$hasClockOut = $log->updated_at &&$log->created_at && $log->updated_at->ne($log->created_at);
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    <?php echo e(optional($log->student)->name ?? 'Siswa Tidak Ditemukan'); ?>

                                    <span class="block text-xs font-normal text-slate-400">
                                        <?php echo e(optional(optional($log->student)->schoolClass)->name ?? '-'); ?> (NISN: <?php echo e(optional($log->student)->nisn ?? '-'); ?>)
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs font-bold <?php echo e($isLateStudent ? 'text-rose-600' : 'text-emerald-600'); ?>">
                                    <?php echo e($log->created_at ? $log->created_at->format('H:i:s') : '-'); ?>

                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs font-bold text-slate-600">
                                    <?php echo e($hasClockOut ? $log->updated_at->format('H:i:s') : '-'); ?>

                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php if($isLateStudent): ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-700 border border-rose-200">terlambat</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">present</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">Belum ada aktivitas tap gerbang siswa hari ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABEL LOG GURU -->
                <div id="tab-teacher-content" class="overflow-x-auto hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                                <th class="py-3 px-4">Guru</th>
                                <th class="py-3 px-4">Jam Masuk</th>
                                <th class="py-3 px-4">Jam Pulang</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = ($teacherLogs ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tLog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $teacherObj =$tLog->teacher ?? (object) [];
                                $userObj    =$tLog->user ?? (object) [];
                                $teacherName = $tLog->teacher->name ?? ($tLog->user->name ?? 'Guru');
                                $teacherNip  =$tLog->teacher->nip ?? ($tLog->teacher->code ?? ($tLog->user->email ?? '-'));

                                $isLateTeacher =$tLog->is_late
                                    || $tLog->status === 'Terlambat'
                                    || $tLog->status === 'late'
                                    || ($tLog->scan_in &&$tLog->scan_in > '07:00:00')
                                    || ($tLog->created_at &&$tLog->created_at->format('H:i:s') > '07:00:00');
                                
                                $hasTeacherClockOut =$tLog->scan_out 
                                    || ($tLog->updated_at &&$tLog->created_at && $tLog->updated_at->ne($tLog->created_at));
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    <?php echo e($teacherName); ?>

                                    <span class="block text-xs font-normal text-slate-400">
                                        NIP / ID: <?php echo e($teacherNip); ?>

                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs font-bold <?php echo e($isLateTeacher ? 'text-rose-600' : 'text-emerald-600'); ?>">
                                    <?php echo e($tLog->scan_in ?? ($tLog->created_at ? $tLog->created_at->format('H:i:s') : '-')); ?>

                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs font-bold text-slate-600">
                                    <?php echo e($tLog->scan_out ?? ($hasTeacherClockOut ? $tLog->updated_at->format('H:i:s') : '-')); ?>

                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php if($isLateTeacher): ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-700 border border-rose-200">terlambat</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">hadir</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">Belum ada aktivitas tap gerbang guru hari ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script AJAX Submit & Persistence Status Role -->
    <script>
        // Restore/Set Pilihan Role saat Pertama Dimuat
        document.addEventListener("DOMContentLoaded", function () {
            const savedRole = localStorage.getItem('gate_active_role') || 'student';
            const radioToSelect = document.querySelector(`input[name="role"][value="${savedRole}"]`);
            if (radioToSelect) {
                radioToSelect.checked = true;
                updateRoleUI(savedRole);
            }
        });

        // Handler Pergantian Radio Button
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function () {
                localStorage.setItem('gate_active_role', this.value);
                updateRoleUI(this.value);
            });
        });

        function updateRoleUI(role) {
            const label = document.getElementById('identifier-label');
            const input = document.getElementById('identifier-input');
            if (role === 'teacher') {
                label.innerText = 'NIP / Kode Guru / Email Guru';
                input.placeholder = 'Contoh: 198602082011012013';
                switchTab('teacher');
            } else {
                label.innerText = 'NISN / NIS / ID Fingerprint';
                input.placeholder = 'Contoh: 1234567890';
                switchTab('student');
            }
        }

        function switchTab(type) {
            const studentContent = document.getElementById('tab-student-content');
            const teacherContent = document.getElementById('tab-teacher-content');
            const studentBtn = document.getElementById('tab-student-btn');
            const teacherBtn = document.getElementById('tab-teacher-btn');

            if (type === 'teacher') {
                studentContent.classList.add('hidden');
                teacherContent.classList.remove('hidden');
                teacherBtn.classList.add('bg-white', 'shadow-sm', 'text-indigo-600');
                teacherBtn.classList.remove('text-slate-600');
                studentBtn.classList.remove('bg-white', 'shadow-sm', 'text-indigo-600');
                studentBtn.classList.add('text-slate-600');
            } else {
                teacherContent.classList.add('hidden');
                studentContent.classList.remove('hidden');
                studentBtn.classList.add('bg-white', 'shadow-sm', 'text-indigo-600');
                studentBtn.classList.remove('text-slate-600');
                teacherBtn.classList.remove('bg-white', 'shadow-sm', 'text-indigo-600');
                teacherBtn.classList.add('text-slate-600');
            }
        }

        document.getElementById('gate-scan-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const input = document.getElementById('identifier-input');
            const checkedRole = document.querySelector('input[name="role"]:checked');
            const selectedRole = checkedRole ? checkedRole.value : 'student';
            const alertBox = document.getElementById('alert-container');
            const submitBtn = document.getElementById('submit-btn');

            // Simpan role aktif di localStorage sebelum kirim
            localStorage.setItem('gate_active_role', selectedRole);

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');

            fetch("<?php echo e(route('admin.gate-attendance.scan')); ?>", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    identifier: input.value,
                    role: selectedRole
                })
            })
            .then(response => response.json())
            .then(data => {
                alertBox.classList.remove('hidden', 'bg-rose-50', 'border-rose-200', 'text-rose-700', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                const isSuccess = data.status && (
                    data.status.toString().toLowerCase() === 'success' ||
                    data.status.toString().toLowerCase() === 'ok' ||
                    data.success === true
                );

                if (isSuccess) {
                    alertBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                    alertBox.innerHTML = `✅ <b>BERHASIL:</b> ${data.message}`;
                    input.value = '';
                    
                    // Reload halaman dan tetap di role saat ini
                    setTimeout(() => location.reload(), 500);
                } else {
                    alertBox.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-700');
                    alertBox.innerHTML = `❌ <b>GAGAL:</b> ${data.message || 'Data tidak ditemukan!'}`;
                }
            })
            .catch(err => {
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-700');
                alertBox.innerHTML = `⚠️ <b>ERROR:</b> Terjadi kesalahan sistem.`;
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
                input.focus();
            });
        });
    </script>
</body>
</html><?php /**PATH C:\laragon\www\smart-attendance\resources\views/admin/attendances/gate_simulator.blade.php ENDPATH**/ ?>