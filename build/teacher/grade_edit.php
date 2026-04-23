<?php
require_once "inc/header.php";
require_once "../inc/db.php";



$teacher_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
  header('Location: grading.php');
  exit;
}

$grading_id = intval($_GET['id']);

// Fetch grading info with permission check
$sql = "SELECT g.*, a.title AS assignment_title, u.name AS student_name, a.uploaded_by
        FROM grading g
        JOIN assignments a ON g.assignment_id = a.id
        JOIN users u ON g.student_id = u.id
        WHERE g.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $grading_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || $row['uploaded_by'] != $teacher_id) {
  header('Location: grading.php');
  exit;
}
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-8">
        <h2 class="text-xl font-bold mb-4">Edit Grade for <?= htmlspecialchars($row['assignment_title']) ?> - <?= htmlspecialchars($row['student_name']) ?></h2>

        <form method="POST" action="process_grading.php">
          <input type="hidden" name="grading_id" value="<?= $grading_id ?>">

          <div class="mb-4">
            <label class="block mb-1 font-semibold">Total Marks</label>
           <input type="number" name="total_marks" min="1" value="<?= htmlspecialchars($row['total_marks'] ?? 100) ?>" class="border rounded px-3 py-2 w-full" required />

            <!-- <input type="number" name="total_marks" value="100" class="border rounded px-3 py-2 w-full bg-gray-100" /> -->
          </div>

          <div class="mb-4">
            <label class="block mb-1 font-semibold">Obtained Marks</label>
            <input type="number" name="obtained_marks" min="0" max="100" value="<?= htmlspecialchars($row['obtained_marks'] ?? 0) ?>" required class="border rounded px-3 py-2 w-full" />

            <!-- <input type="number" name="obtained_marks" min="0" max="100" value="0'' ?>" required class="border rounded px-3 py-2 w-full" /> -->
          </div>

          <button type="submit" name="grade_submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Save Grade</button>
          <a href="grading.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </form>
      </div>
    </div>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menu-btn');
    const overlay = document.getElementById('overlay');
    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }
    menuBtn.addEventListener('click', () => {
      if (sidebar.classList.contains('-translate-x-full')) {
        openSidebar();
      } else {
        closeSidebar();
      }
    });
    overlay.addEventListener('click', closeSidebar);
  </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
