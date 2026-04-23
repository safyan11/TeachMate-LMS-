<?php
require_once "inc/header.php";
$teacher_id = $_SESSION['user_id'] ?? 1; // Example teacher ID
require_once "../inc/db.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $class_id = intval($_POST['class_id']);
    
    // Process attendance
    if (isset($_POST['status']) && is_array($_POST['status'])) {
        foreach ($_POST['status'] as $student_id => $status) {
            $student_id = intval($student_id);
            $status = $conn->real_escape_string($status);
            
            // Check if already exists for today
            $check = $conn->query("SELECT id FROM attendance WHERE class_id=$class_id AND student_id=$student_id AND DATE(marked_at) = CURDATE()");
            if ($check->num_rows > 0) {
                // Update
                $conn->query("UPDATE attendance SET status='$status' WHERE class_id=$class_id AND student_id=$student_id AND DATE(marked_at) = CURDATE()");
            } else {
                // Insert
                $conn->query("INSERT INTO attendance (student_id, class_id, status) VALUES ($student_id, $class_id, '$status')");
            }
        }
        $message = '<p class="text-green-600 font-bold mb-4">Attendance saved successfully!</p>';
    }
}

// Fetch classes created by this teacher
$classes_result = $conn->query("SELECT oc.*, c.title as course_title FROM online_classes oc LEFT JOIN courses c ON oc.course_id = c.id WHERE oc.teacher_id = $teacher_id ORDER BY oc.class_date DESC LIMIT 20");

// If a class is selected, fetch students (for now, simply fetch all enrolled students, but we don't have enrollments yet, so fetch all users with role 'user' / 'student')
$selected_class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$students = [];
if ($selected_class_id > 0) {
    // In a real scenario, fetch users enrolled in the course linked to this class
    // Doing a left join with current attendance for today
    $sql_students = "
        SELECT u.id, u.name, u.email, a.status 
        FROM users u 
        LEFT JOIN attendance a ON a.student_id = u.id AND a.class_id = $selected_class_id AND DATE(a.marked_at) = CURDATE()
        WHERE u.role = 'user' OR u.role = 'student'
        ORDER BY u.name ASC
    ";
    $students_res = $conn->query($sql_students);
    if ($students_res) {
        while($r = $students_res->fetch_assoc()) {
            $students[] = $r;
        }
    }
}
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="md:p-10 p-6 max-w-7xl">
        <h1 class="text-3xl font-bold mb-8">Mark Attendance</h1>
        <?= $message ?>

        <!-- Select Class -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-bold mb-4">Select Online Class</h2>
            <form method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block mb-2 font-semibold">Class</label>
                    <select name="class_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Select Class --</option>
                        <?php while($c = $classes_result->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= $selected_class_id == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['course_title'] ?? 'General') ?> - <?= htmlspecialchars($c['class_title']) ?> (<?= $c['class_date'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Load Students</button>
            </form>
        </div>

        <?php if ($selected_class_id > 0): ?>
            <!-- Students List -->
            <form method="POST">
                <input type="hidden" name="class_id" value="<?= $selected_class_id ?>">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Students</h2>
                    <?php if (count($students) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y text-left text-sm divide-gray-200 mb-6">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 font-semibold">Student Name</th>
                                        <th class="px-6 py-3 font-semibold">Email</th>
                                        <th class="px-6 py-3 font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($students as $stu): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4"><?= htmlspecialchars($stu['name']) ?></td>
                                        <td class="px-6 py-4"><?= htmlspecialchars($stu['email']) ?></td>
                                        <td class="px-6 py-4">
                                            <label class="inline-flex items-center text-green-600 font-semibold cursor-pointer mr-4">
                                                <input type="radio" name="status[<?= $stu['id'] ?>]" value="Present" <?= ($stu['status'] == 'Present' || empty($stu['status'])) ? 'checked' : '' ?> class="mr-2"> Present
                                            </label>
                                            <label class="inline-flex items-center text-red-600 font-semibold cursor-pointer">
                                                <input type="radio" name="status[<?= $stu['id'] ?>]" value="Absent" <?= $stu['status'] == 'Absent' ? 'checked' : '' ?> class="mr-2"> Absent
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" name="mark_attendance" class="bg-black text-white px-6 py-2 rounded">Save Attendance</button>
                    <?php else: ?>
                        <p class="text-gray-500">No students found.</p>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>

      </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
