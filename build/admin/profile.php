<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    
    // Handle Image Upload
    $image_update_sql = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . "_" . $user_id . "." . $ext;
            $upload_path = "../uploads/profile/" . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                $old_img_res = $conn->query("SELECT profile_image FROM users WHERE id=$user_id");
                $old_img = $old_img_res->fetch_assoc()['profile_image'];
                if ($old_img && file_exists("../uploads/profile/" . $old_img)) {
                    unlink("../uploads/profile/" . $old_img);
                }
                $image_update_sql = ", profile_image='$new_filename'";
            }
        }
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name='$name', email='$email', password='$hashed' $image_update_sql WHERE id=$user_id";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email' $image_update_sql WHERE id=$user_id";
    }

    if ($conn->query($sql)) {
        $message = '<div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 font-bold flex items-center gap-2"><i class="fa-solid fa-circle-check"></i> Admin Profile updated successfully!</div>';
    } else {
        $message = '<div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 font-bold flex items-center gap-2"><i class="fa-solid fa-circle-xmark"></i> Error: ' . $conn->error . '</div>';
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$current_img = !empty($user['profile_image']) ? "../uploads/profile/" . $user['profile_image'] : "https://i.pravatar.cc/150";
?>

<body class="bg-gray-50" style="font-family:'Outfit',sans-serif;">
<div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>

  <div class="flex-1 flex flex-col md:ml-64">
    <?php require_once "inc/topbar.php"; ?>

    <div class="p-6 max-w-4xl mx-auto mt-6 w-full mb-12">
      <div class="flex items-center gap-4 mb-8">
          <div class="bg-red-600 p-3 rounded-2xl shadow-lg shadow-red-200">
              <i class="fa-solid fa-user-shield text-3xl text-white"></i>
          </div>
          <div>
              <h1 class="text-3xl font-bold text-gray-800">Admin Profile</h1>
              <p class="text-gray-500">Manage your profile information and security settings.</p>
          </div>
      </div>

      <?= $message ?>

      <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Profile Picture Sidebar -->
        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <div class="relative inline-block mb-4">
                    <img src="<?= $current_img ?>" id="preview" class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-sm mx-auto">
                    <label for="imageUpload" class="absolute bottom-0 right-0 bg-red-600 text-white p-2 rounded-full cursor-pointer hover:bg-red-700 transition shadow-lg">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </label>
                    <input type="file" name="profile_image" id="imageUpload" class="hidden" accept="image/*" onchange="previewImage(this)">
                </div>
                <h3 class="font-bold text-gray-800 tracking-tight"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-xs text-gray-400 mb-4"><?= htmlspecialchars($user['email']) ?></p>
                <div class="px-4 py-1.5 rounded-full bg-red-50 text-red-600 text-[10px] uppercase font-black tracking-widest inline-block">Administrator</div>
            </div>
        </div>

        <!-- Form Details -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block mb-2 font-bold text-gray-700 text-sm">Full Name</label>
                        <div class="relative">
                            <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 transition bg-gray-50">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 font-bold text-gray-700 text-sm">Email Address</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 transition bg-gray-50">
                        </div>
                    </div>

                    <div class="pt-4 border-t">
                        <label class="block mb-2 font-bold text-gray-700 text-sm">Change Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="password" name="password" placeholder="Verify login to change"
                                class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 transition bg-gray-50">
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" name="update_profile" 
                        class="w-full bg-red-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-600/20">
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
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
