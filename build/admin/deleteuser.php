<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 


if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    try {
       
        $conn->begin_transaction();

        
        $stmt = $conn->prepare("DELETE FROM feedback WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

     
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        
        $conn->commit();

        echo "<script>alert('✅ User deleted successfully'); window.location.href='deleteuser.php';</script>";

    } catch (Exception $e) {
       
        $conn->rollback();
        echo "<script>alert('❌ Error deleting user: " . $e->getMessage() . "');</script>";
    }
}


$result = $conn->query("SELECT * FROM users");

?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
   <?php require_once "inc/sidebar.php"; ?>

    <!-- Overlay for mobile when sidebar open -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <!-- top bar -->
   <?php require_once "inc/topbar.php"; ?>
  <div class="p-6">
    <h2 class="text-2xl font-semibold mb-4">Delete User</h2>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg text-sm">
    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
        <tr>
            <th class="p-4 text-left">ID</th>
            <th class="p-4 text-left">Username</th>
            <th class="p-4 text-left">Email</th>
            <th class="p-4 text-left">Role</th>
            <th class="p-4 text-left">Status</th>
            <th class="p-4 text-left">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td class="p-4"><?= $row['id'] ?></td>
                <td class="p-4"><?= htmlspecialchars($row['name']) ?></td>
                <td class="p-4"><?= htmlspecialchars($row['email']) ?></td>
                <td class="p-4"><?= htmlspecialchars($row['role']) ?></td>
                <td class="p-4"><?= htmlspecialchars($row['verify_status']) ?></td>
                <td class="p-4">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="delete_user" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this user?');">
                            🗑️
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

      <!-- Pagination (dummy for now) -->
      <div class="flex items-center justify-between mt-4 px-2 text-sm text-gray-600">
        <button class="hover:text-black">&larr;</button>
        <span>Page 1 of 10</span>
        <button class="hover:text-black">&rarr;</button>
      </div>
    </div>
  </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
