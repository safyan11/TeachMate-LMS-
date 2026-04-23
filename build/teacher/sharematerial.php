<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

// Handle form submission for file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    if (!empty($_POST['title']) && !empty($_POST['course_id']) && isset($_FILES['material_file']) && $_FILES['material_file']['error'] === 0) {
        $title = $conn->real_escape_string($_POST['title']);
        $course_id = intval($_POST['course_id']);
        $file = $_FILES['material_file'];

        $dir = 'uploads/materials/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($file['name']));
        $target_file = $dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $filesize = $file['size'];
            $sql = "INSERT INTO materials (title, filename, filesize, uploaded_by, course_id) VALUES ('$title', '$filename', $filesize, $teacher_id, $course_id)";
            if ($conn->query($sql)) {
                $message = "File '$title' successfully uploaded to the library.";
        } else {
            $error = "Upload error: " . $conn->error;
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
$materials_res = $conn->query("SELECT m.*, c.title as course_title FROM materials m LEFT JOIN courses c ON m.course_id = c.id WHERE m.uploaded_by = $teacher_id ORDER BY m.uploaded_at DESC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Materials Library</h1>
                <p class="text-slate-500 font-medium">Share study materials and resources with your students.</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-cloud-check"></i> <?= $message ?>
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
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-share-nodes"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Upload Material</h2>
            </div>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 items-end">
                <div class="lg:col-span-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Select Course</label>
                    <select name="course_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-indigo-600 font-bold">
                        <option value="">Choose Course...</option>
                        <?php while($c = $courses_result->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">File Title</label>
                    <input type="text" name="title" placeholder="e.g. Lecture Notes - Module 2" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="lg:col-span-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Select File</label>
                    <input type="file" name="material_file" required class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                </div>
                <div>
                    <button type="submit" class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl shadow-slate-100 italic">
                        Upload
                    </button>
                </div>
            </form>
        </div>

        <!-- Inventory List -->
        <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Active Resources</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php if ($materials_res && $materials_res->num_rows > 0): ?>
                <?php while ($row = $materials_res->fetch_assoc()): 
                    $file_ext = strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION));
                    $icon = 'fa-file-lines';
                    if (in_array($file_ext, ['pdf'])) $icon = 'fa-file-pdf';
                    elseif (in_array($file_ext, ['doc', 'docx'])) $icon = 'fa-file-word';
                    elseif (in_array($file_ext, ['zip', 'rar'])) $icon = 'fa-file-zipper';
                ?>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-indigo-500/5 transition duration-300 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center text-xl border border-slate-100 group-hover:bg-indigo-600 group-hover:text-white transition">
                                <i class="fa-solid <?= $icon ?>"></i>
                            </div>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">
                                <?= htmlspecialchars($row['course_title'] ?? 'Generic') ?>
                            </span>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 mb-1 leading-tight"><?= htmlspecialchars($row['title']) ?></h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mb-6"><?= number_format($row['filesize'] / 1024, 1) ?> KB • <?= date('d M Y', strtotime($row['uploaded_at'])) ?></p>
                        
                        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                            <p class="text-[10px] font-black text-blue-500 truncate max-w-[120px]"><?= htmlspecialchars($row['filename']) ?></p>
                            <a href="<?= 'uploads/materials/' . urlencode($row['filename']) ?>" download class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-emerald-500 transition shadow-lg shadow-slate-200">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center border-2 border-dashed border-slate-100 rounded-[2.5rem]">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-100 mb-4"></i>
                    <p class="text-slate-400 font-black uppercase tracking-widest text-[10px]">No materials found</p>
                </div>
            <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
