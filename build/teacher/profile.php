<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$user_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    
    $image_update_sql = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . "_" . $user_id . "." . $ext;
            $dir = "../uploads/profile/";
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $upload_path = $dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $old_img_res = $conn->query("SELECT profile_image FROM users WHERE id=$user_id");
                $old_img = $old_img_res->fetch_assoc()['profile_image'] ?? '';
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
        $message = "Profile updated successfully.";
    } else {
        $error = "Error: Could not update profile. " . $conn->error;
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$current_img = !empty($user['profile_image']) ? "../uploads/profile/" . $user['profile_image'] : "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&background=0D8ABC&color=fff";
?>

<body class="bg-[#f8fafc]" style="font-family:'Outfit',sans-serif;">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col md:ml-64">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10 max-w-5xl mx-auto w-full">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Teacher Profile</h1>
                <p class="text-slate-500 font-medium">Update your personal information and profile image.</p>
            </div>
            <div class="px-5 py-2 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic">
                Verified Instructor
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3 italic">
                <i class="fa-solid fa-id-card-clip"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Perspective Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-blue-600/5 opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="w-40 h-40 rounded-[2.5rem] overflow-hidden border-4 border-slate-50 shadow-xl mx-auto mb-4 relative z-10">
                            <img src="<?= $current_img ?>" id="preview" class="w-full h-full object-cover">
                        </div>
                        <label for="imageUpload" class="absolute -bottom-2 -right-2 bg-slate-900 text-white w-10 h-10 rounded-2xl flex items-center justify-center cursor-pointer hover:bg-blue-600 transition shadow-lg z-20">
                            <i class="fa-solid fa-camera-retro"></i>
                        </label>
                        <input type="file" name="profile_image" id="imageUpload" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>

                    <h3 class="text-xl font-black text-slate-800 leading-tight"><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-xs font-bold text-slate-400 mb-6"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="flex flex-col gap-3">
                        <div class="bg-slate-50 p-4 rounded-2xl text-left border border-slate-100 hover:bg-slate-100 transition">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Authorization</p>
                            <p class="text-xs font-black text-slate-900 uppercase tracking-tighter"><?= strtoupper($user['role']) ?></p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl text-left border border-slate-100 hover:bg-slate-100 transition">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                            <p class="text-xs font-black text-slate-900 uppercase tracking-tighter">Active Member</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Core -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Personal Information</h2>
                    </div>

                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Full Name</label>
                                <div class="relative">
                                    <i class="fa-solid fa-user-tag absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Email Address</label>
                                <div class="relative">
                                    <i class="fa-solid fa-at absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600">
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-50">
                            <div class="flex items-center gap-3 mb-6">
                                <i class="fa-solid fa-shield-halved text-blue-600"></i>
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-900">Security Override</h4>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">New Password</label>
                                <div class="relative">
                                    <i class="fa-solid fa-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600">
                                </div>
                                <p class="text-[9px] text-slate-400 font-bold mt-2 italic px-2">Password will be updated securely.</p>
                            </div>
                        </div>

                        <div class="pt-10">
                            <button type="submit" name="update_profile" class="w-full bg-slate-900 text-white px-8 py-5 rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-blue-600 transition shadow-2xl shadow-slate-200 flex items-center justify-center gap-4 group">
                                Upload
                                <i class="fa-solid fa-cloud-arrow-up group-hover:translate-y-[-2px] transition duration-500"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50/50 rounded-[2.5rem] p-8 border border-blue-100/50">
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-700 mb-2">Platform Metrics</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/50 p-4 rounded-2xl border border-blue-100">
                            <p class="text-xl font-black text-blue-700 leading-none mb-1">24</p>
                            <p class="text-[9px] font-bold text-blue-400 uppercase tracking-widest leading-none">Modules Taught</p>
                        </div>
                        <div class="bg-white/50 p-4 rounded-2xl border border-blue-100">
                            <p class="text-xl font-black text-blue-700 leading-none mb-1">1.2k</p>
                            <p class="text-[9px] font-bold text-blue-400 uppercase tracking-widest leading-none">Interactions</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </main>
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
