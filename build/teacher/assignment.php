<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

$teacher_id = $_SESSION['user_id'] ?? 1; 
$message = '';
$error = '';

// Handle assignment upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_assignment'])) {
    if (!empty($_POST['title']) && !empty($_POST['course_id']) && isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === 0) {
        $title = $conn->real_escape_string($_POST['title']);
        $course_id = intval($_POST['course_id']);
        $file = $_FILES['assignment_file'];

        $dir = 'uploads/assignments/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($file['name']));
        $target_file = $dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $filesize = $file['size'];
            $sql = "INSERT INTO assignments (title, filename, filesize, uploaded_by, course_id) VALUES ('$title', '$filename', $filesize, $teacher_id, $course_id)";
            if ($conn->query($sql)) {
                $message = "Assignment '$title' successfully uploaded.";
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Upload failure: Check folder permissions.";
    }
} else {
    $error = "Missing information: Title and file required.";
}
}

// Fetch data
$courses_result = $conn->query("SELECT * FROM courses ORDER BY title ASC");
$assignments = $conn->query("SELECT a.*, c.title as course_title FROM assignments a LEFT JOIN courses c ON a.course_id = c.id WHERE a.uploaded_by = $teacher_id ORDER BY a.uploaded_at DESC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Assignment Management</h1>
                <p class="text-slate-500 font-medium">Create and manage your course assignments.</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-file-circle-check"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Issuance Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 mb-12">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Create Assignment</h2>
            </div>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 items-end">
                <div class="lg:col-span-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Subject</label>
                    <select name="course_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                        <option value="">Choose Course...</option>
                        <?php while($c = $courses_result->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Assignment Title</label>
                    <input type="text" name="title" placeholder="e.g. Unit 4 Research Paper" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                </div>
                <div class="lg:col-span-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Select File</label>
                    <input type="file" name="assignment_file" required class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>
                <div>
                    <button type="submit" name="upload_assignment" class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] py-4 rounded-2xl hover:bg-blue-600 transition shadow-xl shadow-slate-100">
                        Upload
                    </button>
                </div>
            </form>
        </div>

        <!-- Inventory List -->
        <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8 px-4">All Assignments</h3>
        <div class="space-y-6">
            <?php if ($assignments && $assignments->num_rows > 0): ?>
                <?php while ($assignment = $assignments->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:shadow-slate-200/50 transition duration-300 group">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                            <div class="flex gap-4">
                                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-[1.25rem] flex items-center justify-center border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition">
                                    <i class="fa-solid fa-file-invoice text-xl"></i>
                                </div>
                                <div class="max-w-md">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h4 class="text-lg font-black text-slate-800 leading-tight"><?= htmlspecialchars($assignment['title']) ?></h4>
                                        <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-100">
                                            <?= htmlspecialchars($assignment['course_title'] ?? 'Global') ?>
                                        </span>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Uploaded On <?= date('d M, Y \• H:i', strtotime($assignment['uploaded_at'])) ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="uploads/assignments/<?= urlencode($assignment['filename']) ?>" class="bg-slate-50 text-slate-400 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition flex items-center gap-3 border border-slate-100">
                                    <i class="fa-solid fa-download"></i> Source
                                </a>
                                <button onclick="document.getElementById('subs-<?= $assignment['id'] ?>').classList.toggle('hidden')" class="bg-blue-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition shadow-lg shadow-blue-100">
                                    View Submissions
                                </button>
                            </div>
                        </div>

                        <!-- Submissions Drawer -->
                        <div id="subs-<?= $assignment['id'] ?>" class="hidden mt-8 pt-8 border-t border-slate-50">
                            <?php
                            $aid = (int)$assignment['id'];
                            $subs = $conn->query("SELECT s.*, u.name as std_name, u.email as std_email FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.assignment_id = $aid ORDER BY s.submitted_at DESC");
                            ?>
                            <?php if ($subs && $subs->num_rows > 0): ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-max w-full text-left text-xs">
                                        <thead class="bg-slate-50 rounded-xl overflow-hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-6 py-4">Student Name</th>
                                                <th class="px-6 py-4">Submitted At</th>
                                                <th class="px-6 py-4">File Name</th>
                                                <th class="px-6 py-4 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 font-medium">
                                            <?php while ($sub = $subs->fetch_assoc()): ?>
                                                <tr class="hover:bg-slate-50/50 transition">
                                                    <td class="px-6 py-4">
                                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($sub['std_name']) ?></p>
                                                        <p class="text-[9px] text-slate-400 font-bold lowercase"><?= htmlspecialchars($sub['std_email']) ?></p>
                                                    </td>
                                                    <td class="px-6 py-4 text-slate-400 font-bold"><?= date('d M, Y \• H:i', strtotime($sub['submitted_at'])) ?></td>
                                                    <td class="px-6 py-4 text-blue-600 font-black tracking-tighter"><?= htmlspecialchars($sub['filename']) ?></td>
                                                    <td class="px-6 py-4 text-center">
                                                        <a href="uploads/submissions/<?= urlencode($sub['filename']) ?>" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition mx-auto" download>
                                                            <i class="fa-solid fa-file-download"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center py-6 text-slate-400 font-bold italic">No submissions for this assignment yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="py-20 text-center border-2 border-dashed border-slate-100 rounded-[2.5rem]">
                    <i class="fa-solid fa-folder-open text-4xl text-slate-100 mb-4"></i>
                    <p class="text-slate-400 font-black uppercase tracking-widest text-[10px]">No assignments found</p>
                </div>
            <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
