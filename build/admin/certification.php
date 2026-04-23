<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

$message = "";
$error = "";

// ✅ Handle file upload (Issue Certificate)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_certificate'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);

    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $uploadDir = "uploads/certificates/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileInfo = pathinfo($_FILES['certificate']['name']);
        $ext = strtolower($fileInfo['extension']);
        $fileName = time() . "_" . bin2hex(random_bytes(8)) . "." . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['certificate']['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO certificates (student_id, course_id, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $student_id, $course_id, $targetPath);
            if ($stmt->execute()) {
                $message = "Certificate successfully uploaded.";
            } else {
                $error = "Database error: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error = "Upload failure: Check folder permissions.";
        }
    } else {
        $error = "Please select a valid file.";
    }
}

// ✅ Handle Delete Certificate
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $res = $conn->query("SELECT file_path FROM certificates WHERE id=$del_id");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['file_path'])) unlink($row['file_path']);
    }
    $conn->query("DELETE FROM certificates WHERE id=$del_id");
    header("Location: certification.php?msg=deleted");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $message = "Certificate deleted successfully.";
}

// Data Fetching
$students = $conn->query("SELECT id, name, email FROM users WHERE role='student' ORDER BY name ASC");
$courses = $conn->query("SELECT id, title FROM courses ORDER BY title ASC");
$history = $conn->query("SELECT cert.*, u.name as std_name, u.email as std_email, c.title as course_title 
                         FROM certificates cert 
                         JOIN users u ON cert.student_id = u.id 
                         JOIN courses c ON cert.course_id = c.id 
                         ORDER BY cert.uploaded_at DESC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10">
            <h1 class="text-3xl font-black tracking-tight">Course Certificates</h1>
            <p class="text-slate-500 font-medium">Upload and manage course completion certificates for students.</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Issuance Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 mb-12">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Issue New Certificate</h2>
            </div>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-end">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Select Student</label>
                    <select name="student_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                        <option value="">Choose Student...</option>
                        <?php while($s = $students->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Select Course</label>
                    <select name="course_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                        <option value="">Choose Course...</option>
                        <?php while($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Certificate File (PDF/IMG)</label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="certificate" required class="flex-1 text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-slate-900 file:text-white hover:file:bg-blue-600 transition">
                        <button type="submit" name="issue_certificate" class="bg-blue-600 text-white px-6 h-10 rounded-2xl hover:bg-black transition flex items-center justify-center shadow-lg shadow-blue-100 font-black text-[10px] uppercase tracking-widest">
                            Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Issuance History -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden text-sm">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">Issued Certificates</h3>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Certificate History</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Recipient</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Course Title</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Issued On</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = $history->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-5">
                                <p class="font-bold text-slate-800"><?= htmlspecialchars($row['std_name']) ?></p>
                                <p class="text-[9px] font-bold text-slate-400 lowercase"><?= htmlspecialchars($row['std_email']) ?></p>
                            </td>
                            <td class="px-6 py-5">
                                <span class="font-black text-blue-600 text-[10px] uppercase tracking-tighter"><?= htmlspecialchars($row['course_title']) ?></span>
                            </td>
                            <td class="px-6 py-5 font-bold text-slate-500 text-[10px]">
                                <?= date("d M, Y", strtotime($row['uploaded_at'])) ?>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="<?= $row['file_path'] ?>" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 hover:bg-emerald-600 hover:text-white transition">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Purge this credential from history?')" class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-400 hover:bg-rose-600 hover:text-white transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
