<?php
require_once "../inc/db.php";
require_once "inc/header.php";

$message = '';
$error = '';

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    // Fetch thumbnail to delete file
    $res = $conn->query("SELECT thumbnail FROM courses WHERE id=$del_id");
    if($row = $res->fetch_assoc()) {
        if(!empty($row['thumbnail']) && file_exists(__DIR__ . '/' . $row['thumbnail'])) {
            unlink(__DIR__ . '/' . $row['thumbnail']);
        }
    }
    if ($conn->query("DELETE FROM courses WHERE id=$del_id")) {
        $message = "Course deleted successfully.";
    } else {
        $error = "Error: Could not delete course. " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
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

    // Handle thumbnail
    $thumb_url = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $dir = 'uploads/courses/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $fname = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['thumbnail']['name']);
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir . $fname)) {
            $thumb_url = $dir . $fname;
        }
    }

    $sql = "INSERT INTO courses (title, short_description, video_hours, articles, resources, assignments, certificate, full_description, instructor_name, instructor_designation, overview, what_you_will_learn, thumbnail, category_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiisssssssi", $title, $short_description, $video_hours, $articles, $resources, $assignments, $certificate, $full_description, $instructor_name, $instructor_designation, $overview, $what_you_will_learn, $thumb_url, $category_id);
    
    if ($stmt->execute()) {
        $message = "Course '$title' successfully created.";
    } else {
        $error = "Error: Could not create course. " . $stmt->error;
    }
    $stmt->close();
}

$courses = $conn->query("SELECT c.*, cat.name as cat_name FROM courses c LEFT JOIN categories cat ON c.category_id = cat.id ORDER BY c.created_at DESC");
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10">
            <h1 class="text-3xl font-black tracking-tight">Course Management</h1>
            <p class="text-slate-500 font-medium">Create and manage academic courses on the platform.</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Creation Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 mb-12">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Add New Course</h2>
            </div>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Col -->
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Category</label>
                            <select name="category_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                                <option value="">Select Category...</option>
                                <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Course Title</label>
                            <input type="text" name="title" required placeholder="e.g. Advanced Neural Architectures" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Short Description</label>
                            <textarea name="short_description" rows="3" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Total Hours</label>
                                <input type="text" name="video_hours" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Certificate</label>
                                <select name="certificate" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                                    <option value="Yes">Enabled</option>
                                    <option value="No">Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Col -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Articles</label>
                                <input type="number" name="articles" value="5" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Materials</label>
                                <input type="number" name="resources" value="10" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Assignments</label>
                                <input type="number" name="assignments" value="2" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Instructor Information</label>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="instructor_name" placeholder="Full Name" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                                <input type="text" name="instructor_designation" placeholder="Designation" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Course Image</label>
                            <input type="file" name="thumbnail" accept="image/*" required class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Learning Objectives (Line separated)</label>
                            <textarea name="what_you_will_learn" rows="3" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600"></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex justify-end">
                    <button type="submit" name="add_course" class="bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] px-10 py-4 rounded-2xl hover:bg-blue-600 transition shadow-xl shadow-slate-100">
                        Upload
                    </button>
                </div>
            </form>
        </div>

        <!-- Course Inventory -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden text-sm">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">All Courses</h3>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active List</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Course Title</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Instructor</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Duration</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cert</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = $courses->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img src="<?= htmlspecialchars($row['thumbnail']) ?>" class="w-14 h-10 object-cover rounded-lg shadow-sm">
                                    <span class="font-bold text-slate-800"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-5 font-black text-blue-600 text-[10px] uppercase"><?= htmlspecialchars($row['cat_name'] ?? 'Core') ?></td>
                            <td class="px-6 py-5">
                                <p class="font-bold text-slate-700"><?= htmlspecialchars($row['instructor_name']) ?></p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter"><?= htmlspecialchars($row['instructor_designation']) ?></p>
                            </td>
                            <td class="px-6 py-5 font-black text-slate-500 uppercase text-[10px]"><?= $row['video_hours'] ?> Hrs</td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase <?= $row['certificate'] === 'Yes' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400' ?>">
                                    <?= $row['certificate'] === 'Yes' ? 'Enabled' : 'Locked' ?>
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="edit_course.php?id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Erase this course and all associated lessons?')" class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-400 hover:bg-rose-600 hover:text-white transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
