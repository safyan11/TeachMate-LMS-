<?php
require '../inc/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch stats
$total_users_q = $conn->query("SELECT COUNT(*) as count FROM users");
$total_users = $total_users_q->fetch_assoc()['count'];

$role_q = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$roles = [];
while($r = $role_q->fetch_assoc()) {
    $roles[$r['role']] = $r['count'];
}
$students = isset($roles['student']) ? $roles['student'] : 0;
$teachers = isset($roles['teacher']) ? $roles['teacher'] : 0;
$admins = isset($roles['admin']) ? $roles['admin'] : 0;

$courses_q = $conn->query("SELECT COUNT(*) as count FROM courses");
$total_courses = $courses_q->fetch_assoc()['count'];

$materials_q = $conn->query("SELECT COUNT(*) as count FROM materials");
$total_materials = $materials_q->fetch_assoc()['count'];


$submissions_q = $conn->query("SELECT COUNT(*) as count FROM submissions");
$total_submissions = $submissions_q->fetch_assoc()['count'];
?>

<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50">

  <?php require_once "inc/sidebar.php"; ?>

  <main class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden bg-white rounded-lg shadow p-6 min-h-screen">
    <?php require_once "inc/topbar.php"; ?>
    <h2 class="text-2xl font-bold mb-6 mt-10">System Reports</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-100 p-6 rounded-lg text-center shadow">
            <h3 class="text-xl font-bold text-blue-800">Total Users</h3>
            <p class="text-4xl font-bold mt-2 text-blue-900"><?= $total_users ?></p>
        </div>
        <div class="bg-green-100 p-6 rounded-lg text-center shadow">
            <h3 class="text-xl font-bold text-green-800">Students</h3>
            <p class="text-4xl font-bold mt-2 text-green-900"><?= $students ?></p>
        </div>
        <div class="bg-purple-100 p-6 rounded-lg text-center shadow">
            <h3 class="text-xl font-bold text-purple-800">Teachers</h3>
            <p class="text-4xl font-bold mt-2 text-purple-900"><?= $teachers ?></p>
        </div>
        <div class="bg-yellow-100 p-6 rounded-lg text-center shadow">
            <h3 class="text-xl font-bold text-yellow-800">Total Courses</h3>
            <p class="text-4xl font-bold mt-2 text-yellow-900"><?= $total_courses ?></p>
        </div>
        <div class="bg-indigo-100 p-6 rounded-lg text-center shadow">
            <h3 class="text-xl font-bold text-indigo-800">Assignments Submitted</h3>
            <p class="text-4xl font-bold mt-2 text-indigo-900"><?= $total_submissions ?></p>
        </div>
    </div>
    
  </main>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
