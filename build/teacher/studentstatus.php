<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>

    <!-- Overlay for mobile when sidebar open -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <!-- top bar -->
       <?php require_once "inc/topbar.php"; ?>
  <div class=" p-6">
    <div class="bg-white rounded-2xl shadow overflow-hidden">
      <div class="px-6 py-5 border-b">
        <h1 class="text-2xl font-bold">Student Status Overview</h1>
      </div>
      <div class="w-full overflow-x-auto">
     <?php
require '../inc/db.php';
// Fetch only approved users
$sql = "SELECT id, name, email, role, verify_status, created_at 
        FROM users 
        WHERE verify_status = 'approved' AND role = 'student' 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<table class="min-w-full divide-y divide-gray-200">
  <thead class="bg-gray-100">
    <tr>
      <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 whitespace-nowrap">Student Name</th>
      <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 whitespace-nowrap">Email</th>
      <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 whitespace-nowrap">Role</th>
      <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 whitespace-nowrap">Status</th>
      <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 whitespace-nowrap">Created At</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-200 text-sm">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['name']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['email']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['role']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full <?= $row['verify_status'] === 'approved' ? 'bg-green-500' : 'bg-red-500' ?>"></span>
            <span><?= htmlspecialchars(ucfirst($row['verify_status'])) ?></span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No approved students found.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<?php $conn->close(); ?>

      </div>
     
    </div>
  </div>
    </div>
  </div>

  

  <!-- sidebar menu  -->
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

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
