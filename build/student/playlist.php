<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

$courseId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch enrolled courses for the student
$enrolled_courses = $conn->query("
    SELECT c.* 
    FROM courses c 
    JOIN course_enrollments ce ON c.id = ce.course_id 
    WHERE ce.student_id = $student_id
");

// If a specific course is selected, fetch its details
if ($courseId > 0) {
    $res = $conn->query("SELECT * FROM courses WHERE id = $courseId");
    if ($res->num_rows > 0) {
        $course = $res->fetch_assoc();
        $enrollment = $conn->query("SELECT * FROM course_enrollments WHERE student_id = $student_id AND course_id = $courseId")->fetch_assoc();
        $progress = $enrollment ? (int)$enrollment['progress_percentage'] : 0;
        $materials = $conn->query("SELECT * FROM materials WHERE course_id = $courseId ORDER BY uploaded_at ASC");
    } else {
        $courseId = 0; // Invalid ID, fallback to list
    }
}
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col md:ml-72 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        
        <?php if ($courseId > 0): ?>
            <!-- --- Course Player View --- -->
            <div class="mb-6 flex items-center justify-between">
                <a href="playlist.php" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 flex items-center gap-2 transition">
                    <i class="fa-solid fa-arrow-left"></i> My Library
                </a>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] italic">Current Session: <?= htmlspecialchars($course['title']) ?></span>
            </div>

            <div class="flex flex-col lg:flex-row gap-10">
                <div class="lg:w-2/3 space-y-8">
                    <div class="bg-white rounded-[3rem] overflow-hidden border border-slate-100 shadow-sm transition hover:shadow-2xl duration-700 group">
                        <div class="relative h-[25rem] overflow-hidden">
                            <img src="../admin/<?= htmlspecialchars($course['thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-1000">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                            <div class="absolute bottom-10 left-10 right-10">
                                <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight italic"><?= htmlspecialchars($course['title']) ?></h1>
                            </div>
                        </div>

                        <div class="p-10">
                            <div class="bg-slate-50 rounded-[2rem] p-8 mb-10 border border-slate-100 shadow-inner">
                                <div class="flex justify-between items-center mb-4 px-2">
                                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest italic">Learning Progress</span>
                                    <span class="text-sm font-black text-blue-600 italic"><?= $progress ?>% Complete</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-blue-600 h-full rounded-full shadow-lg shadow-blue-500/20 transition-all duration-1000" style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>

                            <div class="prose prose-slate max-w-none">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-3 italic">
                                    Syllabus Overview
                                    <span class="flex-1 h-[1px] bg-slate-100"></span>
                                </h4>
                                <p class="text-slate-600 font-medium leading-relaxed mb-8 italic">
                                    <?= nl2br(htmlspecialchars($course['full_description'])) ?>
                                </p>
                            </div>

                            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl mt-10">
                                <h4 class="text-lg font-black mb-6 italic flex items-center gap-3">
                                    <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                                    What you will learn
                                </h4>
                                <ul class="space-y-4">
                                    <?php
                                    $lessons = explode("\n", $course['what_you_will_learn']);
                                    foreach ($lessons as $lesson): if(trim($lesson)): ?>
                                        <li class="flex items-start gap-4 text-xs font-bold text-slate-300 leading-normal italic">
                                            <i class="fa-solid fa-circle-check text-blue-400 mt-0.5"></i>
                                            <?= htmlspecialchars(trim($lesson)) ?>
                                        </li>
                                    <?php endif; endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="lg:w-1/3 space-y-6">
                    <h2 class="text-2xl font-black tracking-tight italic">Materials</h2>
                    <div class="space-y-4">
                        <?php if ($materials && $materials->num_rows > 0): ?>
                            <?php while ($mat = $materials->fetch_assoc()): ?>
                                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center gap-4 hover:border-blue-200 transition group">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-black text-slate-800 line-clamp-1 italic"><?= htmlspecialchars($mat['title']) ?></h4>
                                    </div>
                                    <a href="../teacher/uploads/<?= urlencode($mat['filename']) ?>" download class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-10 text-center bg-white rounded-[2rem] border border-dashed border-slate-100 italic text-slate-300 text-xs uppercase font-black">No materials found.</div>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>

        <?php else: ?>
            <!-- --- Library View --- -->
            <div class="mb-10 text-center lg:text-left">
                <h1 class="text-4xl font-black tracking-tight italic">My Course Library</h1>
                <p class="text-slate-500 font-medium">Continue your journey where you left off.</p>
            </div>

            <?php if ($enrolled_courses && $enrolled_courses->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php while ($row = $enrolled_courses->fetch_assoc()): ?>
                        <a href="playlist.php?id=<?= $row['id'] ?>" class="group bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition duration-500">
                            <div class="h-48 overflow-hidden relative">
                                <img src="../admin/<?= htmlspecialchars($row['thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-1000">
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-500">
                                    <div class="w-14 h-14 bg-white/90 backdrop-blur-md rounded-full flex items-center justify-center text-blue-600 shadow-2xl">
                                        <i class="fas fa-play ml-1"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8 text-center">
                                <h3 class="text-xl font-black text-slate-800 mb-2 italic"><?= htmlspecialchars($row['title']) ?></h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 italic"><?= htmlspecialchars($row['instructor_name']) ?></p>
                                <span class="inline-block px-6 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest group-hover:bg-blue-600 transition">Resume Course</span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-24 bg-white rounded-[3rem] border border-dashed border-slate-200">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                        <i class="fas fa-book-open text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-4 italic">Library Empty</h3>
                    <p class="text-slate-400 font-medium mb-10 max-w-sm mx-auto">You haven't enrolled in any courses yet. Start your journey today by exploring our curriculum!</p>
                    <a href="../courses.php" class="inline-flex items-center gap-3 px-10 py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-900 transition-all shadow-xl shadow-blue-100 italic">
                        Explore Courses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
