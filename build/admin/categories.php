<?php
// Include DB connection
require '../inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            echo "<script>alert('Category added successfully!'); window.location.href='categories.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['delete_category'])) {
        $id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>alert('Category deleted successfully!'); window.location.href='categories.php';</script>";
        }
        $stmt->close();
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
?>

<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50">

  <?php require_once "inc/sidebar.php"; ?>

  <main class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden bg-white rounded-lg shadow p-6 min-h-screen">
    <?php require_once "inc/topbar.php"; ?>
    <h2 class="text-2xl font-bold mb-6 mt-10">Manage Categories</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Add Category Form -->
        <div>
            <form action="categories.php" method="POST" class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Category Name</label>
                    <input type="text" name="name" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
                </div>
                <div>
                    <button type="submit" name="add_category" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                        Add Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Categories List -->
        <div>
            <h3 class="text-xl font-bold mb-4">Existing Categories</h3>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2 text-left">Name</th>
                            <th class="border border-gray-300 p-2 text-left">Description</th>
                            <th class="border border-gray-300 p-2 text-center w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($categories->num_rows > 0): ?>
                            <?php while ($row = $categories->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['description']) ?></td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <form action="categories.php" method="POST" onsubmit="return confirm('Delete this category?');" class="inline">
                                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="delete_category" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="border border-gray-300 p-2 text-center text-gray-500">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </main>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
