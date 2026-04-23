<?php
require_once "../inc/db.php";
require_once "inc/header.php";

$message = '';
$error = '';

if (!isset($_GET['id'])) {
    header("Location: addcourse.php");
    exit();
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM courses WHERE id=$id");
if ($res->num_rows === 0) {
    header("Location: addcourse.php");
    exit();
}
$course = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $short_description = $conn->real_escape_string($_POST['short_description']);
    $video_hours = $conn->real_escape_string($_POST['video_hours']);
    $articles = intval($_POST['articles']);
    $resources = intval($_POST['resources']);
    $assignments = intval($_POST['assignments']);
    $certificate = $_POST['certificate'];
    $full_description = $conn->real_escape_string($_POST['full_description']);
    $instructor_name = $conn->real_escape_string($_POST['instructor_name']);
    $instructor_designation = $conn->real_escape_string($_POST['instructor_designation']);
    $overview = $conn->real_escape_string($_POST['overview']);
    $what_you_will_learn = $conn->real_escape_string($_POST['what_you_will_learn']);

    $thumb_url = $course['thumbnail'];
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $dir = 'uploads/courses/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        // Delete old thumbnail if exists
        if (!empty($thumb_url) && file_exists(__DIR__ . '/' . $thumb_url)) {
            unlink(__DIR__ . '/' . $thumb_url);
        }

        $fname = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['thumbnail']['name']);
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir . $fname)) {
            $thumb_url = $dir . $fname;
        }
    }

    $sql = "UPDATE courses SET 
            title=?, short_description=?, video_hours=?, articles=?, 
            resources=?, assignments=?, certificate=?, full_description=?, 
            instructor_name=?, instructor_designation=?, overview=?, 
            what_you_will_learn=?, thumbnail=?, category_id=? 
            WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiisssssssii", $title, $short_description, $video_hours, $articles, $resources, $assignments, $certificate, $full_description, $instructor_name, $instructor_designation, $overview, $what_you_will_learn, $thumb_url, $category_id, $id);
    
    if ($stmt->execute()) {
        $message = "Course updated successfully.";
        // Refresh local data
        $res = $conn->query("SELECT * FROM courses WHERE id=$id");
        $course = $res->fetch_assoc();
    } else {
        $error = "Error: Could not update course. " . $stmt->error;
    }
    $stmt->close();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Edit Course</h1>
                <p class="text-slate-500 font-medium text-sm">Update course information and image.</p>
            </div>
            <a href="addcourse.php" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to List
            </a>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12">
            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Col -->
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Category</label>
                            <select name="category_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 appearance-none">
                                <option value="">Select Category...</option>
                                <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $course['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Course Title</label>
                            <input type="text" name="title" required value="<?= htmlspecialchars($course['title']) ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Short Description</label>
                            <textarea name="short_description" rows="3" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600"><?= htmlspecialchars($course['short_description']) ?></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Total Hours</label>
                                <input type="text" name="video_hours" required value="<?= htmlspecialchars($course['video_hours']) ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Certificate</label>
                                <select name="certificate" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                                    <option value="Yes" <?= $course['certificate'] === 'Yes' ? 'selected' : '' ?>>Enabled</option>
                                    <option value="No" <?= $course['certificate'] === 'No' ? 'selected' : '' ?>>Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Col -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Articles</label>
                                <input type="number" name="articles" value="<?= $course['articles'] ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Materials</label>
                                <input type="number" name="resources" value="<?= $course['resources'] ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Assignments</label>
                                <input type="number" name="assignments" value="<?= $course['assignments'] ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Instructor Information</label>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="instructor_name" placeholder="Full Name" required value="<?= htmlspecialchars($course['instructor_name']) ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                                <input type="text" name="instructor_designation" placeholder="Designation" required value="<?= htmlspecialchars($course['instructor_designation']) ?>" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Course Image</label>
                            <div class="flex items-center gap-4 border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                                <img src="<?= htmlspecialchars($course['thumbnail']) ?>" class="w-16 h-12 object-cover rounded-lg shadow-sm border border-white">
                                <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Full Description</label>
                            <textarea name="full_description" rows="3" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600"><?= htmlspecialchars($course['full_description']) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Course Overview</label>
                        <textarea name="overview" rows="4" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600"><?= htmlspecialchars($course['overview']) ?></textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Learning Objectives (Line separated)</label>
                        <textarea name="what_you_will_learn" rows="4" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600"><?= htmlspecialchars($course['what_you_will_learn']) ?></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex justify-end">
                    <button type="submit" name="update_course" class="bg-blue-600 text-white font-black uppercase tracking-[0.2em] text-[10px] px-10 py-4 rounded-2xl hover:bg-slate-900 transition shadow-xl shadow-blue-100">
                        Upload
                    </button>
                </div>
            </form>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
