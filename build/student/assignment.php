<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Fetch active assignments
$sql = "SELECT a.*, u.name AS teacher_name 
        FROM assignments a 
        JOIN users u ON a.uploaded_by = u.id
        ORDER BY a.uploaded_at DESC";
$assignments_result = $conn->query($sql);

// Map student submissions
$sql_subs = "SELECT * FROM submissions WHERE student_id = $student_id";
$student_submissions = $conn->query($sql_subs);
$subs_map = [];
if ($student_submissions && $student_submissions->num_rows > 0) {
    while ($row = $student_submissions->fetch_assoc()) {
        $subs_map[$row['assignment_id']] = $row;
    }
}

// Fetch Grades
$sql_grades = "SELECT g.*, a.title AS assignment_title 
               FROM grading g
               JOIN assignments a ON g.assignment_id = a.id
               WHERE g.student_id = $student_id
               ORDER BY g.graded_at DESC";
$grades_result = $conn->query($sql_grades);

$status_msg = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $status_msg = "Assignment submitted successfully!";
    } elseif ($_GET['status'] === 'error') {
        $status_msg = "Error submitting assignment. Please try again.";
    }
}
?>

<body class="bg-slate-50 relative before:fixed before:inset-0 before:-z-10 before:w-full before:h-full before:bg-\[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))\] before:from-blue-100 before:via-white before:to-emerald-50">
    <div class="flex">
        <?php require_once "inc/sidebar.php"; ?>
        <div class="flex-1 md:ml-72 transition-all duration-300">
            <?php require_once "inc/topbar.php"; ?>
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold">Assignments</h1>
                    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 text-sm font-semibold">
                        Available: <?= $assignments_result->num_rows ?>
                    </div>
                </div>

                <?php if ($status_msg): ?>
                    <div class="p-4 rounded-lg mb-8 font-bold border <?= strpos($status_msg, 'successfully') !== false ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' ?>">
                        <?= $status_msg ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <?php if ($assignments_result && $assignments_result->num_rows > 0): ?>
                        <?php while ($assignment = $assignments_result->fetch_assoc()): ?>
                            <?php
                                $sub = $subs_map[$assignment['id']] ?? null;
                                $submitted = $sub !== null;
                            ?>
                            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-file-alt text-xl"></i>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $submitted ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= $submitted ? 'Submitted' : 'Pending' ?>
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold mb-1"><?= htmlspecialchars($assignment['title']) ?></h3>
                                <p class="text-gray-500 text-xs mb-6"><?= htmlspecialchars($assignment['teacher_name']) ?> | <?= date('M d, Y', strtotime($assignment['uploaded_at'])) ?></p>
                                
                                <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                                    <a href="../uploads/assignments/<?= $assignment['filename'] ?>" download class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-download"></i> Download File
                                    </a>
                                    <?php if ($submitted): ?>
                                        <span class="text-xs font-bold text-green-600 flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Submission Uploaded
                                        </span>
                                    <?php else: ?>
                                        <form action="submit_assignment.php" method="POST" enctype="multipart/form-data" class="flex-1 flex gap-2">
                                            <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                            <input type="file" name="submission_file" required class="text-[10px] flex-1">
                                            <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-800 transition">Upload</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

                <h2 class="text-2xl font-bold mb-6">Grades & Feedback</h2>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Assignment</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Marks</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Grade</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if ($grades_result && $grades_result->num_rows > 0): ?>
                                <?php while ($grade = $grades_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-6 py-4 font-bold"><?= htmlspecialchars($grade['assignment_title']) ?></td>
                                        <td class="px-6 py-4"><?= $grade['obtained_marks'] ?> / <?= $grade['total_marks'] ?></td>
                                        <td class="px-6 py-4 font-bold text-blue-600"><?= $grade['grade'] ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?= strtolower($grade['status']) == 'passed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= $grade['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500"><?= date('M d, Y', strtotime($grade['graded_at'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">No grades found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>

