<?php
require_once "inc/header.php";
require_once "../inc/db.php";

// ── Stats ─────────────────────────────────────────────────
$total_users      = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'] ?? 0;
$total_teachers   = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='teacher'")->fetch_assoc()['c'] ?? 0;
$total_students   = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'] ?? 0;
$total_admins     = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='admin'")->fetch_assoc()['c'] ?? 0;
$pending_complaints = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status='pending'")->fetch_assoc()['c'] ?? 0;
$total_certificates = $conn->query("SELECT COUNT(*) as c FROM certificates")->fetch_assoc()['c'] ?? 0;
$total_courses    = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'] ?? 0;
$pending_users    = $conn->query("SELECT COUNT(*) as c FROM users WHERE verify_status='pending'")->fetch_assoc()['c'] ?? 0;

// ── Visit stats last 7 days ───────────────────────────────
$visitsLabels = [];
$visitsValues = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $visitsLabels[] = date('D', strtotime($d));
    $v = $conn->query("SELECT visit_count FROM visit_statistics WHERE visit_date='$d'")->fetch_assoc()['visit_count'] ?? 0;
    $visitsValues[] = $v;
}

// ── Role distribution ─────────────────────────────────────
$roleData = [$total_admins, $total_teachers, $total_students];
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
            <div class="rounded-2xl p-7 flex items-center justify-between" style="background:linear-gradient(135deg,#1e1b4b 0%,#4f46e5 100%);">
                <div>
                    <p class="text-indigo-200 text-sm font-semibold mb-1">Administration Panel ⚙️</p>
                    <h1 class="text-2xl font-bold text-white">Dashboard Overview</h1>
                    <p class="text-indigo-300 text-sm mt-1">Monitor users, complaints, and platform activity.</p>
                </div>
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-center bg-white/10 rounded-xl px-5 py-3">
                        <p class="text-3xl font-bold text-white"><?= $total_users ?></p>
                        <p class="text-indigo-200 text-xs mt-1">Total Users</p>
                    </div>
                    <div class="text-center bg-white/10 rounded-xl px-5 py-3">
                        <p class="text-3xl font-bold text-white"><?= $total_courses ?></p>
                        <p class="text-indigo-200 text-xs mt-1">Courses</p>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Total Users</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $total_users ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-comment-dots text-amber-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Pending Complaints</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $pending_complaints ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-certificate text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Certificates Issued</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $total_certificates ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <i class="fas fa-user-clock text-red-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Pending Users</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $pending_users ?></h3>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Visit Statistics Line Chart -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h2 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-indigo-500"></i> Visit Statistics (Last 7 Days)
                    </h2>
                    <canvas id="visitsChart" height="200"></canvas>
                </div>

                <!-- User Roles Donut -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h2 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-indigo-500"></i> User Roles Distribution
                    </h2>
                    <div class="flex items-center justify-center gap-8">
                        <div class="relative" style="width:140px;height:140px;">
                            <canvas id="rolesChart" width="140" height="140"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-slate-800"><?= $total_users ?></span>
                                <span class="text-xs text-slate-400">Users</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-indigo-600 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Admins</p>
                                    <p class="font-bold text-slate-700"><?= $total_admins ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Teachers</p>
                                    <p class="font-bold text-slate-700"><?= $total_teachers ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Students</p>
                                    <p class="font-bold text-slate-700"><?= $total_students ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="adduser.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-indigo-50 group-hover:bg-indigo-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-user-plus text-indigo-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Manage Users</span>
                </a>
                <a href="addcourse.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-blue-50 group-hover:bg-blue-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-book text-blue-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Add Course</span>
                </a>
                <a href="complaints.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-amber-50 group-hover:bg-amber-500 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-comment-dots text-amber-500 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Complaints</span>
                </a>
                <a href="certification.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-emerald-50 group-hover:bg-emerald-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-certificate text-emerald-500 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Certifications</span>
                </a>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Visit Statistics
new Chart(document.getElementById('visitsChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($visitsLabels) ?>,
        datasets: [{
            label: 'Daily Visits',
            data: <?= json_encode($visitsValues) ?>,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4f46e5',
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } },
        plugins: { legend: { display: false } }
    }
});

// Roles Donut
new Chart(document.getElementById('rolesChart'), {
    type: 'doughnut',
    data: {
        labels: ['Admins', 'Teachers', 'Students'],
        datasets: [{
            data: <?= json_encode($roleData) ?>,
            backgroundColor: ['#4f46e5','#10b981','#60a5fa'],
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
