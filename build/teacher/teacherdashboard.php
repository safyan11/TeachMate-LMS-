<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;

// ── Metrics ───────────────────────────────────────────────
$total_students  = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='student'")->fetch_assoc()['c'] ?? 0;
$active_courses  = $conn->query("SELECT COUNT(*) AS c FROM courses")->fetch_assoc()['c'] ?? 0;
$materials_shared= $conn->query("SELECT COUNT(*) AS c FROM materials")->fetch_assoc()['c'] ?? 0;
$pending_grading = $conn->query("SELECT COUNT(*) AS c FROM grading WHERE status='pending'")->fetch_assoc()['c'] ?? 0;
$total_assign    = $conn->query("SELECT COUNT(*) AS c FROM assignments WHERE uploaded_by=$teacher_id")->fetch_assoc()['c'] ?? 0;

// ── Grading Status for Donut ──────────────────────────────
$graded_count   = $conn->query("SELECT COUNT(*) AS c FROM grading WHERE status='graded'")->fetch_assoc()['c'] ?? 0;
$pending_g_count= $pending_grading;

// ── Recent submissions chart (last 7 days) ─────────────────
$sub_labels = [];
$sub_values = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $sub_labels[] = date('D', strtotime($d));
    $v = $conn->query("SELECT COUNT(*) as c FROM submissions WHERE DATE(submitted_at)='$d'")->fetch_assoc()['c'] ?? 0;
    $sub_values[] = $v;
}

// ── Teacher name ──────────────────────────────────────────
$teacher_info = $conn->query("SELECT name FROM users WHERE id=$teacher_id")->fetch_assoc();
$teacher_name = $teacher_info['name'] ?? 'Teacher';
?>

<body class="bg-slate-100" style="font-family:'Outfit',sans-serif;">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <?php require_once "inc/sidebar.php"; ?>

    <!-- Main Content (offset by sidebar w-64 = 16rem) -->
    <div class="flex-1 flex flex-col md:ml-64">
        <?php require_once "inc/topbar.php"; ?>

        <main class="p-6 lg:p-8 space-y-8">

            <!-- Welcome Banner -->
            <div class="rounded-2xl p-7 flex items-center justify-between" style="background:linear-gradient(135deg,#064e3b 0%,#059669 100%);">
                <div>
                    <p class="text-emerald-200 text-sm font-semibold mb-1">Faculty Portal 🎓</p>
                    <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($teacher_name) ?></h1>
                    <p class="text-emerald-300 text-sm mt-1">Manage your courses, students & evaluations.</p>
                </div>
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-center bg-white/10 rounded-xl px-5 py-3">
                        <p class="text-3xl font-bold text-white"><?= $total_students ?></p>
                        <p class="text-emerald-200 text-xs mt-1">Students</p>
                    </div>
                    <div class="text-center bg-white/10 rounded-xl px-5 py-3">
                        <p class="text-3xl font-bold text-white"><?= $active_courses ?></p>
                        <p class="text-emerald-200 text-xs mt-1">Courses</p>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-users text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Total Students</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $total_students ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Active Courses</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $active_courses ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-amber-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Pending Grading</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $pending_grading ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                        <i class="fas fa-share-alt text-purple-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Materials Shared</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $materials_shared ?></h3>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Submissions Over 7 Days -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h2 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-emerald-500"></i> Submissions (Last 7 Days)
                    </h2>
                    <canvas id="submissionsChart" height="200"></canvas>
                </div>

                <!-- Grading Status Donut -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h2 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-amber-500"></i> Grading Status
                    </h2>
                    <div class="flex items-center justify-center gap-8">
                        <div class="relative" style="width:140px;height:140px;">
                            <canvas id="gradingDonut" width="140" height="140"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-slate-800"><?= $graded_count + $pending_g_count ?></span>
                                <span class="text-xs text-slate-400">Total</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Graded</p>
                                    <p class="font-bold text-slate-700"><?= $graded_count ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Pending</p>
                                    <p class="font-bold text-slate-700"><?= $pending_g_count ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="studentstatus.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-emerald-50 group-hover:bg-emerald-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-users text-emerald-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Student<br>Status</span>
                </a>
                <a href="sharematerial.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-purple-50 group-hover:bg-purple-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-share-alt text-purple-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Share<br>Material</span>
                </a>
                <a href="assignment.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-amber-50 group-hover:bg-amber-500 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-file-upload text-amber-500 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Upload<br>Assignment</span>
                </a>
                <a href="grading.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-blue-50 group-hover:bg-blue-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-graduation-cap text-blue-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Grading<br>Assignment</span>
                </a>
                <a href="onlineclass.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-rose-50 group-hover:bg-rose-500 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-video text-rose-500 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Online<br>Class</span>
                </a>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Submissions Line Chart
new Chart(document.getElementById('submissionsChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($sub_labels) ?>,
        datasets: [{
            label: 'Submissions',
            data: <?= json_encode($sub_values) ?>,
            borderColor: '#059669',
            backgroundColor: 'rgba(5,150,105,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#059669',
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } },
        plugins: { legend: { display: false } }
    }
});

// Grading Donut
new Chart(document.getElementById('gradingDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Graded', 'Pending'],
        datasets: [{
            data: [<?= $graded_count ?>, <?= $pending_g_count ?>],
            backgroundColor: ['#10b981','#f59e0b'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '72%',
        plugins: { legend: { display: false } },
        responsive: false
    }
});
</script>

</body>
</html>
