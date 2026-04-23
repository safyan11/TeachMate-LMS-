<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Asset Inventory Extraction
$materials = [];
$sql = "SELECT m.*, c.title as course_title 
        FROM materials m
        LEFT JOIN courses c ON m.course_id = c.id
        ORDER BY m.uploaded_at DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-72 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight italic">Course Materials</h1>
                <p class="text-slate-500 font-medium italic uppercase tracking-wider text-[11px]">Download study files and resources</p>
            </div>
            <div class="px-5 py-2 bg-white rounded-2xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest text-slate-400 italic">
                Total Resources: <?= count($materials) ?>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-max w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">File Title</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Course</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">File Size</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-center">Date Uploaded</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-800">
                        <?php if (count($materials) > 0): ?>
                            <?php foreach ($materials as $material): ?>
                                <tr class="hover:bg-slate-50/50 transition duration-300 group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition shadow-inner">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-800 italic"><?= htmlspecialchars($material['title']) ?></p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase"><?= htmlspecialchars($material['filename']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <span class="px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-slate-100 italic">
                                            <?= htmlspecialchars($material['course_title'] ?? 'Global Module') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-center text-xs font-bold text-slate-400 italic">
                                        <?= number_format($material['filesize'] / 1024, 2) ?> KB
                                    </td>
                                    <td class="px-6 py-6 text-center text-xs font-bold text-slate-400">
                                        <?= date('M d, Y', strtotime($material['uploaded_at'])) ?>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="../teacher/uploads/<?= urlencode($material['filename']) ?>" 
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-lg shadow-slate-200" 
                                           download>
                                            <i class="fa-solid fa-cloud-arrow-down shadow-sm"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-20 text-center italic text-slate-300 font-bold uppercase tracking-widest">
                                    No materials available at the moment.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Motivational Banner -->
        <div class="mt-12 bg-blue-600 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-10 opacity-10 group-hover:scale-110 transition duration-700">
                <i class="fa-solid fa-graduation-cap text-9xl"></i>
            </div>
            <div class="relative z-10 max-w-2xl">
                <h4 class="text-2xl font-black mb-4 italic leading-tight">Start Learning Today.</h4>
                <p class="text-sm font-bold text-blue-100 italic">Download your files and start studying. Consistent learning leads to success.</p>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
