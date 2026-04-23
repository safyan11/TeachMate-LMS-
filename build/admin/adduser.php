<?php
require_once "../inc/db.php"; 
require_once "inc/header.php";

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Handle Status Toggle
    if (isset($_POST['toggle_status'])) {
        $user_id = intval($_POST['user_id']);
        $newStatus = $_POST['current_status'] === 'approved' ? 'ban' : 'approved';
        $stmt = $conn->prepare("UPDATE users SET verify_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $user_id);
        if ($stmt->execute()) $message = "User status updated to $newStatus.";
        else $error = "Failed to update status.";
        $stmt->close();
    }

    // 2. Handle Role Assignment
    if (isset($_POST['assign_role'])) {
        $user_id = intval($_POST['user_id']);
        $role = $_POST['assign_role'];
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);
        if ($stmt->execute()) $message = "User role updated to $role.";
        else $error = "Failed to update role.";
        $stmt->close();
    }

    // 3. Handle User Deletion
    if (isset($_POST['delete_user'])) {
        $user_id = intval($_POST['user_id']);
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) $message = "User deleted successfully.";
        else $error = "Failed to delete user.";
        $stmt->close();
    }

    // 4. Create New User
    if (isset($_POST['create_user'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        
        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $sql = "INSERT INTO users (name, email, password, role, verify_status) VALUES ('$name', '$email', '$password', '$role', 'approved')";
            if ($conn->query($sql)) $message = "User added successfully!";
            else $error = "Failed to add user: " . $conn->error;
        }
    }
}

// Stats Extraction
$total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_teachers = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='teacher'")->fetch_assoc()['c'];
$pending_approvals = $conn->query("SELECT COUNT(*) as c FROM users WHERE verify_status='pending'")->fetch_assoc()['c'];

// Fetch users
$users_result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<body class="bg-slate-100" style="font-family:'Outfit',sans-serif;">
    <div class="flex min-h-screen">
        <?php require_once "inc/sidebar.php"; ?>
        
        <!-- Main Content (offset by sidebar w-64 = 16rem) -->
        <div class="flex-1 flex flex-col md:ml-64">
            <?php require_once "inc/topbar.php"; ?>
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>
                    <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition">Add User</button>
                </div>

                <!-- Stats Tiles -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold"><?= $total_users ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Teachers</p>
                        <h3 class="text-3xl font-bold"><?= $total_teachers ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Pending Approval</p>
                        <h3 class="text-3xl font-bold"><?= $pending_approvals ?></h3>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span><?= $message ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <!-- User Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-x-auto w-full border border-gray-100">
                    <table class="min-w-max w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold text-gray-700">Name</th>
                                <th class="px-6 py-4 font-bold text-gray-700">Email</th>
                                <th class="px-6 py-4 font-bold text-gray-700">Role</th>
                                <th class="px-6 py-4 font-bold text-gray-700">Status</th>
                                <th class="px-6 py-4 font-bold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users_result->num_rows > 0): ?>
                                <?php while($row = $users_result->fetch_assoc()): ?>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($row['email']) ?></td>
                                        <td class="px-6 py-4">
                                            <form method="POST" class="flex items-center gap-2">
                                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                <select name="assign_role" class="bg-gray-50 border border-gray-200 rounded px-2 py-1 text-sm outline-none">
                                                    <option value="student" <?= $row['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                                                    <option value="teacher" <?= $row['role'] == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                                    <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                                <button type="submit" class="text-blue-600 font-bold hover:underline text-xs">Update</button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $row['verify_status'] == 'approved' ? 'bg-green-100 text-green-700' : ($row['verify_status'] == 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') ?>">
                                                <?= ucfirst($row['verify_status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 space-x-3">
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="current_status" value="<?= $row['verify_status'] ?>">
                                                <button type="submit" name="toggle_status" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">
                                                    <?= $row['verify_status'] == 'approved' ? 'Ban' : 'Approve' ?>
                                                </button>
                                            </form>
                                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                <button type="submit" name="delete_user" class="text-sm font-bold text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 font-medium italic">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Add New User</h2>
                <button onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-gray-500 hover:text-black">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Ex: John Doe">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Email</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Ex: email@TeachMate.com">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Minimum 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Role</label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="create_user" class="w-full bg-black text-white font-bold py-3 rounded-lg hover:bg-gray-800 transition">Add User</button>
            </form>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>


