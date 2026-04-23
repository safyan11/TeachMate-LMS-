<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// ── Metrics ──────────────────────────────────────────────
$res = $conn->query("SELECT COUNT(*) as c FROM course_enrollments WHERE student_id = $student_id");
$enrolled_count = $res ? $res->fetch_assoc()['c'] : 0;

$res = $conn->query("
    SELECT COUNT(*) as c 
    FROM assignments a 
    JOIN course_enrollments ce ON a.course_id = ce.course_id 
    WHERE ce.student_id = $student_id 
    AND NOT EXISTS (SELECT 1 FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = $student_id)
");
$pending_tasks = $res ? $res->fetch_assoc()['c'] : 0;


$res = $conn->query("SELECT COUNT(*) as c FROM certificates WHERE student_id = $student_id");
$certificates_earned = $res ? $res->fetch_assoc()['c'] : 0;

// ── Submitted Assignments ─────────────────────────────────
$res = $conn->query("SELECT COUNT(*) as c FROM submissions WHERE student_id = $student_id");
$submitted_count = $res ? $res->fetch_assoc()['c'] : 0;


// ── Recent Courses ────────────────────────────────────────
$courses_result = $conn->query("
    SELECT c.* FROM courses c 
    JOIN course_enrollments ce ON c.id = ce.course_id 
    WHERE ce.student_id = $student_id 
    ORDER BY ce.id DESC LIMIT 4
");
if (!$courses_result || $courses_result->num_rows == 0) {
    $courses_result = $conn->query("SELECT * FROM courses ORDER BY id DESC LIMIT 4");
}

// ── Student Info ──────────────────────────────────────────
$student_info = $conn->query("SELECT name FROM users WHERE id = $student_id")->fetch_assoc();
$student_name = $student_info['name'] ?? 'Student';
?>

<body class="bg-slate-100" style="font-family:'Outfit',sans-serif;">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <?php require_once "inc/sidebar.php"; ?>

    <!-- Main Content (offset by sidebar width w-72 = 18rem) -->
    <div class="flex-1 flex flex-col md:ml-72">
        <?php require_once "inc/topbar.php"; ?>

        <main class="p-6 lg:p-8 space-y-8">

            <!-- Welcome Banner -->
            <div class="rounded-2xl p-7 flex items-center justify-between" style="background:linear-gradient(135deg,#1e3a5f 0%,#0f6fcd 100%);">
                <div>
                    <p class="text-blue-200 text-sm font-semibold mb-1">Welcome back 👋</p>
                    <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($student_name) ?></h1>
                    <p class="text-blue-300 text-sm mt-1">Keep learning — every day counts!</p>
                </div>
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-center bg-white/10 rounded-xl px-5 py-3">
                        <p class="text-3xl font-bold text-white"><?= $enrolled_count ?></p>
                        <p class="text-blue-200 text-xs mt-1">Enrolled</p>
                    </div>
                    <div class="text-center bg-white/10 rounded-xl px-5 py-3">
                        <p class="text-3xl font-bold text-white"><?= $certificates_earned ?></p>
                        <p class="text-blue-200 text-xs mt-1">Certificates</p>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-book-open text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Enrolled Courses</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $enrolled_count ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-amber-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Pending Tasks</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $pending_tasks ?></h3>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold">Submitted</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $submitted_count ?></h3>
                    </div>
                </div>

            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">



                <!-- Progress Donut -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h2 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-tasks text-emerald-500"></i> Assignment Progress
                    </h2>
                    <?php
                    $total_tasks = $pending_tasks + $submitted_count;
                    $completion_pct = $total_tasks > 0 ? round(($submitted_count/$total_tasks)*100) : 0;
                    ?>
                    <div class="flex items-center justify-center gap-8">
                        <div class="relative" style="width:140px;height:140px;">
                            <canvas id="assignDonut" width="140" height="140"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold text-slate-800"><?= $completion_pct ?>%</span>
                                <span class="text-xs text-slate-400">Done</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Submitted</p>
                                    <p class="font-bold text-slate-700"><?= $submitted_count ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Pending</p>
                                    <p class="font-bold text-slate-700"><?= $pending_tasks ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-purple-400 inline-block"></span>
                                <div>
                                    <p class="text-xs text-slate-400">Certificates</p>
                                    <p class="font-bold text-slate-700"><?= $certificates_earned ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="playlist.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-blue-50 group-hover:bg-blue-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-play text-blue-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Course<br>Playlist</span>
                </a>
                <a href="material.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-purple-50 group-hover:bg-purple-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-file-alt text-purple-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Materials</span>
                </a>
                <a href="assignment.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-amber-50 group-hover:bg-amber-500 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-tasks text-amber-500 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Assignments</span>
                </a>
                <a href="onlineclass.php" class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-1 transition group">
                    <div class="w-12 h-12 bg-emerald-50 group-hover:bg-emerald-600 rounded-xl flex items-center justify-center transition">
                        <i class="fas fa-video text-emerald-600 group-hover:text-white transition"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 text-center">Online<br>Classes</span>
                </a>
            </div>

            <!-- My Courses -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800">My Courses</h2>
                    <a href="playlist.php" class="text-xs text-blue-600 font-bold hover:underline">View All →</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <?php if ($courses_result && $courses_result->num_rows > 0): ?>
                        <?php while ($row = $courses_result->fetch_assoc()): ?>
                            <a href="playlist.php?id=<?= $row['id'] ?>" class="bg-white rounded-2xl overflow-x-auto w-full border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition group">
                                <div class="h-36 overflow-hidden">
                                    <img src="../admin/<?= $row['thumbnail'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <h4 class="font-bold text-sm text-slate-800 line-clamp-1"><?= htmlspecialchars($row['title']) ?></h4>
                                    <p class="text-slate-400 text-xs mt-1 line-clamp-1"><?= htmlspecialchars($row['short_description']) ?></p>
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-xs text-blue-600 font-bold"><?= $row['video_hours'] ?> hrs</span>
                                        <span class="text-xs text-slate-400"><?= htmlspecialchars($row['instructor_name']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-4 text-center py-12 text-slate-400 italic">No courses found.</div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Assignment Donut
new Chart(document.getElementById('assignDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Submitted', 'Pending', 'Certified'],
        datasets: [{
            data: [<?= $submitted_count ?>, <?= $pending_tasks ?>, <?= $certificates_earned ?>],
            backgroundColor: ['#10b981','#f59e0b','#a78bfa'],
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
