<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

// ✅ Auto-Cleanup Logic: Purge classes from previous days
$conn->query("DELETE FROM online_classes WHERE class_date < CURDATE()");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_title'])) {
    $class_title = $conn->real_escape_string(trim($_POST['class_title']));
    $course_id = intval($_POST['course_id']);
    $class_description = $conn->real_escape_string(trim($_POST['class_description']));
    $meet_link = $conn->real_escape_string(trim($_POST['meet_link']));
    $class_date = $_POST['class_date'];
    $class_time = $_POST['class_time'];

    if (empty($class_title) || empty($meet_link) || empty($class_date) || empty($class_time)) {
        $error = "Error: All fields are required.";
    } else {
        $sql = "INSERT INTO online_classes (teacher_id, course_id, class_title, class_description, meet_link, class_date, class_time)
                VALUES ($teacher_id, $course_id, '$class_title', '$class_description', '$meet_link', '$class_date', '$class_time')";
        if ($conn->query($sql)) {
            $message = "Online class '$class_title' successfully created.";
        } else {
            $error = "Error: Could not create class. " . $conn->error;
        }
    }
}

// Fetch courses for dropdown
$courses_result = $conn->query("SELECT * FROM courses ORDER BY title ASC");

// Fetch all classes for this teacher
$sql_fetch = "SELECT oc.*, c.title as course_title FROM online_classes oc LEFT JOIN courses c ON oc.course_id = c.id WHERE oc.teacher_id = $teacher_id ORDER BY oc.class_date ASC, oc.class_time ASC";
$classes = $conn->query($sql_fetch);
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Online Classes</h1>
                <p class="text-slate-500 font-medium">Create and manage your live online sessions.</p>
            </div>
            <div class="bg-blue-50 text-blue-600 px-6 py-2 rounded-2xl font-black text-[10px] uppercase tracking-widest border border-blue-100">
                Auto-Cleanup Active
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-tower-broadcast"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Creation Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 mb-12">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-video"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Schedule Class</h2>
            </div>

            <form method="POST" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Subject</label>
                        <select name="course_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            <option value="">Choose Course...</option>
                            <?php while($c = $courses_result->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Class Title</label>
                        <input type="text" name="class_title" placeholder="e.g. Advanced Q&A Session" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Join Link (Meet/Zoom)</label>
                        <input type="url" name="meet_link" placeholder="https://meet.google.com/..." required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Class Date</label>
                        <input type="date" name="class_date" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Class Time</label>
                        <input type="time" name="class_time" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] py-4 rounded-2xl hover:bg-blue-600 transition shadow-xl shadow-slate-100">
                            Create Class
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Class Inventory -->
        <h3 class="text-xl font-black text-slate-900 tracking-tight mb-6">Upcoming Sessions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php if ($classes && $classes->num_rows > 0): ?>
                <?php while ($row = $classes->fetch_assoc()): ?>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-blue-500/5 transition duration-300">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest">
                                <?= htmlspecialchars($row['course_title'] ?? 'Core') ?>
                            </span>
                            <div class="flex gap-2 text-slate-300 font-bold text-[10px]">
                                <i class="fa-solid fa-calendar mr-1"></i> <?= $row['class_date'] ?>
                            </div>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 mb-2"><?= htmlspecialchars($row['class_title']) ?></h4>
                        <p class="text-sm text-slate-500 mb-8 font-medium line-clamp-2"><?= htmlspecialchars($row['class_description'] ?: 'No description provided for this session.') ?></p>
                        
                        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                            <div class="flex items-center gap-2 font-black text-slate-900 text-xs">
                                <i class="fa-solid fa-clock text-blue-600"></i>
                                <?= substr($row['class_time'], 0, 5) ?>
                            </div>
                            <div class="flex gap-3">
                                <a href="<?= htmlspecialchars($row['meet_link']) ?>" target="_blank" class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-blue-600 transition shadow-lg shadow-slate-200">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <form method="POST" action="delete_online_class.php" onsubmit="return confirm('Abort this scheduled session?');">
                                    <input type="hidden" name="class_id" value="<?= $row['id'] ?>" />
                                    <button type="submit" class="w-10 h-10 bg-rose-50 text-rose-400 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center border-2 border-dashed border-slate-100 rounded-[2.5rem]">
                    <i class="fa-solid fa-ghost text-4xl text-slate-100 mb-4"></i>
                    <p class="text-slate-400 font-black uppercase tracking-widest text-xs">No Scheduled Classes</p>
                </div>
            <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
