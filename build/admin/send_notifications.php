<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$message = '';

// Handle Send Notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $target = $_POST['target']; // 'all', or specific student_id
    $msg = $conn->real_escape_string(trim($_POST['message']));

    if (!empty($msg)) {
        if ($target === 'all') {
            $students = $conn->query("SELECT id FROM users WHERE role='student'");
            $stmt_all = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            while ($s = $students->fetch_assoc()) {
                $uid = $s['id'];
                $stmt_all->bind_param("is", $uid, $_POST['message']); // use raw message, prepared stmt handles it
                $stmt_all->execute();
            }
            $stmt_all->close();
            $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">✅ Notification sent to ALL students!</div>';
        } else {
            $sid = intval($target);
            $stmt_single = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt_single->bind_param("is", $sid, $_POST['message']);
            $stmt_single->execute();
            $stmt_single->close();
            $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">✅ Notification sent to student!</div>';
        }
    } else {
        $message = '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">❌ Please write a message.</div>';
    }
}

// Handle Post Result (Grade)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_result'])) {
    $student_id  = intval($_POST['student_id']);
    $course_name = $conn->real_escape_string(trim($_POST['course_name']));
    $obtained    = intval($_POST['obtained_marks']);
    $total       = intval($_POST['total_marks']);
    $percentage  = ($total > 0) ? round(($obtained / $total) * 100, 2) : 0;
    $grade       = '';
    $status      = 'Failed';

    if ($percentage >= 90) { $grade = 'A+'; $status = 'Passed'; }
    elseif ($percentage >= 80) { $grade = 'A'; $status = 'Passed'; }
    elseif ($percentage >= 70) { $grade = 'B'; $status = 'Passed'; }
    elseif ($percentage >= 60) { $grade = 'C'; $status = 'Passed'; }
    elseif ($percentage >= 50) { $grade = 'D'; $status = 'Passed'; }
    else { $grade = 'F'; $status = 'Failed'; }

    // Use assignment_id 0 for direct course result
    $conn->query("INSERT INTO grading (assignment_id, student_id, total_marks, obtained_marks, percentage, grade, status) 
                  VALUES (0, $student_id, $total, $obtained, $percentage, '$grade', '$status')");

    // Auto-send notification to student
    $notif_msg = "📊 Your result for course '{$course_name}' has been posted by the Admin! You scored {$obtained}/{$total} ({$percentage}%) — Grade: {$grade} ({$status}).";
    
    $stmt_res_notif = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt_res_notif->bind_param("is", $student_id, $notif_msg);
    $stmt_res_notif->execute();
    $stmt_res_notif->close();

    $message = '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">✅ Result posted and student notified automatically!</div>';
}

// Fetch students
$students = $conn->query("SELECT id, name, email FROM users WHERE role='student' ORDER BY name ASC");
$students_arr = [];
while ($s = $students->fetch_assoc()) $students_arr[] = $s;

// Recent notifications
$recent_notifs = $conn->query("SELECT n.*, u.name FROM notifications n LEFT JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC LIMIT 20");
?>

<body class="bg-gray-50 font-sans antialiased">
<div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>
  <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
  <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
    <?php require_once "inc/topbar.php"; ?>
    <div class="p-6 max-w-5xl">
      <h1 class="text-3xl font-bold mb-8">📣 Notifications & Results Center</h1>
      <?= $message ?>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">

        <!-- Send Notification Card -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><span>🔔</span> Send Notification</h2>
          <form method="POST" class="space-y-4">
            <div>
              <label class="block mb-1 font-semibold text-gray-700">Send To</label>
              <select name="target" required class="w-full border rounded px-3 py-2">
                <option value="all">📢 All Students</option>
                <?php foreach ($students_arr as $s): ?>
                  <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['email']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block mb-1 font-semibold text-gray-700">Message</label>
              <textarea name="message" rows="3" required placeholder="Type your notification message here..." class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"></textarea>
            </div>
            <button type="submit" name="send_notification" class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 font-semibold transition">Send Notification</button>
          </form>
        </div>

        <!-- Post Result Card -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><span>📊</span> Post Student Result</h2>
          <p class="text-sm text-gray-500 mb-4">When you post a result, student will <strong>automatically receive a notification</strong>.</p>
          <form method="POST" class="space-y-4">
            <div>
              <label class="block mb-1 font-semibold text-gray-700">Select Student</label>
              <select name="student_id" required class="w-full border rounded px-3 py-2">
                <option value="">-- Select Student --</option>
                <?php foreach ($students_arr as $s): ?>
                  <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block mb-1 font-semibold text-gray-700">Course / Subject Name</label>
              <input type="text" name="course_name" required placeholder="e.g. Full Stack Web Development" class="w-full border rounded px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block mb-1 font-semibold text-gray-700">Obtained Marks</label>
                <input type="number" name="obtained_marks" required min="0" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block mb-1 font-semibold text-gray-700">Total Marks</label>
                <input type="number" name="total_marks" required min="1" value="100" class="w-full border rounded px-3 py-2">
              </div>
            </div>
            <button type="submit" name="post_result" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold transition">Post Result & Notify Student</button>
          </form>
        </div>
      </div>

      <!-- Recent Notifications Sent -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">📋 Recently Sent Notifications</h2>
        <?php if ($recent_notifs && $recent_notifs->num_rows > 0): ?>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-3 text-left font-semibold">Student</th>
                  <th class="px-4 py-3 text-left font-semibold">Message</th>
                  <th class="px-4 py-3 text-left font-semibold">Status</th>
                  <th class="px-4 py-3 text-left font-semibold">Time</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php while ($n = $recent_notifs->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3 font-semibold text-blue-700"><?= htmlspecialchars($n['name']) ?></td>
                  <td class="px-4 py-3 max-w-sm truncate" title="<?= htmlspecialchars($n['message']) ?>"><?= htmlspecialchars($n['message']) ?></td>
                  <td class="px-4 py-3">
                    <?php if ($n['is_read']): ?>
                      <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Read ✓</span>
                    <?php else: ?>
                      <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Unread</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-gray-500"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="text-gray-500 text-center py-4">No notifications sent yet.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
