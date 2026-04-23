<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

$message = "";

// Handle Delete
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    if ($conn->query("DELETE FROM feedback WHERE id=$del_id")) {
        $message = "Feedback review purged from registry.";
    }
}

// Fetch feedback with associated user and course details
$sql = "SELECT f.*, u.name AS std_name, u.email AS std_email, c.title AS course_title 
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.id
        LEFT JOIN courses c ON f.course_id = c.id
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);

function renderStars($rating) {
    $html = '<div class="flex gap-1 text-[10px]">';
    for ($i = 1; $i <= 5; $i++) {
        $color = $i <= $rating ? 'text-amber-400' : 'text-slate-200';
        $html .= '<i class="fa-solid fa-star ' . $color . '"></i>';
    }
    $html .= '</div>';
    return $html;
}
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Public Reviews</h1>
                <p class="text-slate-500 font-medium">Monitor student sentiment and course performance metrics.</p>
            </div>
            <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-6">
                <div class="text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aggregate</p>
                    <p class="font-black text-slate-900">4.8</p>
                </div>
                <div class="w-px h-8 bg-slate-100"></div>
                <div class="text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</p>
                    <p class="font-black text-slate-900"><?= $result->num_rows ?></p>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-trash-can"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Feedback Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:shadow-slate-200/50 transition duration-300 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center font-black text-slate-400 uppercase text-lg border border-slate-100">
                                    <?= substr($row['std_name'] ?? 'U', 0, 1) ?>
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-800 text-sm"><?= htmlspecialchars($row['std_name'] ?? 'Anonymous Participant') ?></h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mb-2"><?= date('d M Y \• H:i', strtotime($row['created_at'])) ?></p>
                                    <?= renderStars($row['rating']) ?>
                                </div>
                            </div>
                            <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Erase this review?')" class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300 hover:bg-rose-500 hover:text-white transition-all opacity-0 group-hover:opacity-100">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">Impacted Curriculum</p>
                            <p class="font-bold text-slate-700 text-xs"><?= htmlspecialchars($row['course_title'] ?? 'General Experience') ?></p>
                        </div>

                        <div class="relative">
                            <i class="fa-solid fa-quote-left absolute -top-2 -left-2 text-slate-100 text-3xl -z-0"></i>
                            <p class="text-slate-600 text-sm italic font-medium leading-relaxed relative z-10 pl-2">
                                "<?= nl2br(htmlspecialchars($row['comments'])) ?>"
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-comment-slash text-slate-200 text-3xl"></i>
                    </div>
                    <h2 class="text-xl font-black text-slate-300 uppercase tracking-widest">No Feedback Recorded</h2>
                    <p class="text-slate-400 font-medium">Student perspectives will appear here once submitted.</p>
                </div>
            <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
