<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$user_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name        = $conn->real_escape_string(trim($_POST['name']));
    $email       = $conn->real_escape_string(trim($_POST['email']));
    $roll_number = $conn->real_escape_string(trim($_POST['roll_number']));
    $password    = $_POST['password'];

    $image_update_sql = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = time() . "_" . $user_id . "." . $ext;
            $dir = "../uploads/profile/";
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $upload_path  = $dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $old_img_res = $conn->query("SELECT profile_image FROM users WHERE id=$user_id");
                $old_img     = $old_img_res->fetch_assoc()['profile_image'] ?? '';
                if ($old_img && file_exists("../uploads/profile/" . $old_img)) {
                    unlink("../uploads/profile/" . $old_img);
                }
                $image_update_sql = ", profile_image='$new_filename'";
            }
        }
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name='$name', email='$email', roll_number='$roll_number', password='$hashed' $image_update_sql WHERE id=$user_id";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email', roll_number='$roll_number' $image_update_sql WHERE id=$user_id";
    }

    if ($conn->query($sql)) {
        $message = "Profile updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

$user        = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$current_img = !empty($user['profile_image'])
    ? "../uploads/profile/" . $user['profile_image']
    : "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&background=3b82f6&color=fff";
?>

<body class="bg-gray-50" style="font-family:'Outfit',sans-serif;">
<div class="min-h-screen flex">

    <!-- Sidebar -->
    <?php require_once "inc/sidebar.php"; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-72">
        <?php require_once "inc/topbar.php"; ?>

        <div class="p-6 max-w-4xl mx-auto mt-6 w-full mb-12">

            <!-- Page Header -->
            <div class="flex items-center gap-4 mb-8">
                <div class="bg-blue-600 p-3 rounded-2xl shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-user-gear text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
                    <p class="text-gray-500">Update your personal details and profile picture.</p>
                </div>
            </div>

            <!-- Success / Error Messages -->
            <?php if ($message): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Profile Picture Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div class="relative inline-block mb-4">
                            <img src="<?= $current_img ?>" id="preview" class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-sm mx-auto">
                            <label for="imageUpload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition shadow-lg">
                                <i class="fa-solid fa-camera text-sm"></i>
                            </label>
                            <input type="file" name="profile_image" id="imageUpload" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <h3 class="font-bold text-gray-800 tracking-tight text-lg"><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-xs text-gray-400 mb-4"><?= htmlspecialchars($user['email']) ?></p>
                        <div class="px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[10px] uppercase font-black tracking-widest inline-block">Student</div>
                        <?php if (!empty($user['roll_number'])): ?>
                            <p class="text-xs text-gray-400 mt-3 font-semibold">Roll: <?= htmlspecialchars($user['roll_number']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="grid grid-cols-1 gap-6">

                            <!-- Full Name -->
                            <div>
                                <label class="block mb-2 font-bold text-gray-700 text-sm">Full Name</label>
                                <div class="relative">
                                    <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                            </div>

                            <!-- Roll Number -->
                            <div>
                                <label class="block mb-2 font-bold text-gray-700 text-sm">Roll Number</label>
                                <div class="relative">
                                    <i class="fa-solid fa-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="roll_number" value="<?= htmlspecialchars($user['roll_number'] ?? '') ?>" placeholder="E.g. SF-2024-001"
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block mb-2 font-bold text-gray-700 text-sm">Email Address</label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="pt-4 border-t">
                                <label class="block mb-2 font-bold text-gray-700 text-sm">New Password</label>
                                <div class="relative">
                                    <i class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="password" name="password" placeholder="Leave blank to keep current password"
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Password will be encrypted before saving.</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" name="update_profile"
                                class="w-full bg-blue-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                                Upload
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
