<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grade'])) {
    $student_id   = intval($_POST['student_id']);
    $assignment_id = intval($_POST['assignment_id']);
    $obtained     = intval($_POST['obtained_marks']);
    $total        = intval($_POST['total_marks']);
    $percentage   = $total > 0 ? round(($obtained / $total) * 100, 2) : 0;

    if ($percentage >= 90) { $grade = 'A+'; $status = 'Passed'; }
    elseif ($percentage >= 80) { $grade = 'A'; $status = 'Passed'; }
    elseif ($percentage >= 70) { $grade = 'B'; $status = 'Passed'; }
    elseif ($percentage >= 60) { $grade = 'C'; $status = 'Passed'; }
    elseif ($percentage >= 50) { $grade = 'D'; $status = 'Passed'; }
    else { $grade = 'F'; $status = 'Failed'; }

    // Upsert grading record
    $exists = $conn->query("SELECT id FROM grading WHERE assignment_id=$assignment_id AND student_id=$student_id");
    if ($exists->num_rows > 0) {
        $conn->query("UPDATE grading SET total_marks=$total, obtained_marks=$obtained, percentage=$percentage, grade='$grade', status='$status' WHERE assignment_id=$assignment_id AND student_id=$student_id");
    } else {
        $conn->query("INSERT INTO grading (assignment_id, student_id, total_marks, obtained_marks, percentage, grade, status) VALUES ($assignment_id, $student_id, $total, $obtained, $percentage, '$grade', '$status')");
    }

    // Auto-notify student
    $assign_title = $conn->query("SELECT title FROM assignments WHERE id=$assignment_id")->fetch_assoc()['title'] ?? 'Diagnostic Assessment';
    $notif_msg = "📊 Your performance audit for '{$assign_title}' is available. Result: {$grade} ({$status}) - {$percentage}%.";
    
    $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt_notif->bind_param("is", $student_id, $notif_msg);
    $stmt_notif->execute();
    $stmt_notif->close();

    $message = "Grade for '{$assign_title}' successfully saved and student notified.";
}

// Get assignments by this teacher
$assignments_res = $conn->query("SELECT a.id, a.title, c.title as course_title FROM assignments a LEFT JOIN courses c ON a.course_id = c.id WHERE a.uploaded_by = $teacher_id ORDER BY a.uploaded_at DESC");
$assignments_arr = [];
while ($r = $assignments_res->fetch_assoc()) $assignments_arr[] = $r;

// Fetch all students
$students_res = $conn->query("SELECT id, name, email FROM users WHERE role='student' ORDER BY name ASC");
$students_arr = [];
while ($r = $students_res->fetch_assoc()) $students_arr[] = $r;

// Grading records for this teacher
$sql = "SELECT g.*, a.title AS assignment_title, c.title as course_title, u.name AS student_name, u.email as std_email
        FROM grading g
        LEFT JOIN assignments a ON g.assignment_id = a.id
        LEFT JOIN courses c ON a.course_id = c.id
        LEFT JOIN users u ON g.student_id = u.id
        WHERE (a.uploaded_by = $teacher_id OR g.assignment_id = 0 OR g.assignment_id IS NULL)
        ORDER BY g.graded_at DESC";
$result = $conn->query($sql);
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Grading & Results</h1>
                <p class="text-slate-500 font-medium">Review student submissions and assign grades.</p>
            </div>
            <div class="bg-blue-600 text-white px-6 py-2 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-200">
                Live Ledger View
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3 italic">
                <i class="fa-solid fa-stamp"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Grading Interface -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 mb-12">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-pen-nib"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Assign Grade</h2>
            </div>

            <form method="POST" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Assignment</label>
                        <select name="assignment_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold text-slate-700">
                            <option value="">Choose Assignment...</option>
                            <?php foreach ($assignments_arr as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Student</label>
                        <select name="student_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold text-slate-700">
                            <option value="">Choose Student...</option>
                            <?php foreach ($students_arr as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Marks Obtained</label>
                        <input type="number" name="obtained_marks" required min="0" placeholder="e.g. 85" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Total Marks</label>
                        <input type="number" name="total_marks" required min="1" value="100" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold text-slate-700">
                    </div>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" name="save_grade" class="bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] px-10 py-4 rounded-2xl hover:bg-blue-600 transition shadow-xl shadow-slate-100 flex items-center gap-4 group">
                        Save Grade
                        <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Grading Records -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center text-sm">
                <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">Grade List</h3>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Platform Sync Active</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Student</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Assignment</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Score</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Grade</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): 
                                $statusColor = strtolower($row['status']) === 'passed' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600';
                                $gradeColor = $row['grade'] === 'F' ? 'text-rose-600' : 'text-slate-900';
                            ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center font-black text-slate-400 uppercase">
                                                <?= substr($row['student_name'] ?? 'U', 0, 1) ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800"><?= htmlspecialchars($row['student_name'] ?? 'Anonymous') ?></p>
                                                <p class="text-[9px] font-bold text-slate-400 lowercase"><?= htmlspecialchars($row['std_email'] ?? 'n/a') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($row['assignment_title'] ?? 'Core Assessment') ?></p>
                                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest"><?= htmlspecialchars($row['course_title'] ?? 'Platform Core') ?></p>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="w-24 bg-slate-100 h-1.5 rounded-full mx-auto mb-2 relative overflow-hidden">
                                            <div class="absolute inset-y-0 left-0 bg-blue-600 rounded-full" style="width: <?= $row['percentage'] ?>%"></div>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-900"><?= number_format($row['percentage'], 1) ?>% <span class="text-slate-400 tracking-tighter">(<?= $row['obtained_marks'] ?>/<?= $row['total_marks'] ?>)</span></p>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="w-12 h-12 rounded-[1.25rem] bg-slate-50 flex items-center justify-center mx-auto border border-slate-100">
                                            <span class="text-xl font-black tracking-tight <?= $gradeColor ?>"><?= $row['grade'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest <?= $statusColor ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="py-20 text-center font-bold italic text-slate-300">No grades have been recorded yet.</td></tr>
                        <?php endif; ?>
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
