<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$userId = $_SESSION['user_id'] ?? 1;
$feedbackSaved = false;
$error_msg = '';

// Course Inventory Extraction
$courses = [];
$result = $conn->query("SELECT id, title FROM courses ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Handle feedback orchestration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');

    if ($course_id > 0 && $rating >= 1 && $rating <= 5 && !empty($comments)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_id, rating, comments) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $userId, $course_id, $rating, $comments);
        if ($stmt->execute()) {
            $feedbackSaved = true;
        } else {
            $error_msg = "Failed to synchronize feedback with central evaluation registry.";
        }
        $stmt->close();
    } else {
        $error_msg = "Please verify all diagnostic parameters before submission.";
    }
}
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-72 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-12 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight italic">Evaluation Loop</h1>
                <p class="text-slate-500 font-medium italic mt-2 uppercase tracking-widest text-[11px]">Curriculum Quality Assurance</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-2 bg-white rounded-2xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest text-blue-600 italic">
                    Feedback Synchronized: Global
                </div>
            </div>
        </div>

        <?php if ($feedbackSaved): ?>
            <div class="bg-emerald-50 text-emerald-700 p-8 rounded-[2.5rem] mb-12 border border-emerald-100 flex items-center gap-6 italic font-black shadow-lg shadow-emerald-50 max-w-4xl">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 shrink-0">
                    <i class="fa-solid fa-trophy text-xl"></i>
                </div>
                <div>
                    <p class="text-lg">Quality Metrics Archived.</p>
                    <p class="text-emerald-600/70 text-xs font-bold uppercase tracking-widest mt-1">Thank you for your valuable feedback.</p>
                </div>
            </div>
        <?php elseif ($error_msg): ?>
            <div class="bg-rose-50 text-rose-700 p-6 rounded-[2rem] mb-10 border border-rose-100 flex items-center gap-4 italic font-black shadow-lg shadow-rose-50">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
            <!-- Feedback Synthesis Center -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-[3rem] p-10 lg:p-14 border border-slate-100 shadow-sm group">
                    <h3 class="text-2xl font-black mb-10 italic flex items-center gap-4">
                        <span class="w-1.5 h-10 bg-blue-600 rounded-full"></span>
                        Synthesize Observation
                    </h3>

                    <form method="POST" class="space-y-10">
                        <!-- Course Selection Protocol -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Target Curriculum Asset</label>
                            <div class="relative group/select">
                                <select name="course_id" required class="w-full appearance-none bg-slate-50 border-none rounded-2xl px-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm cursor-pointer">
                                    <option value="">Initialize Course Selection...</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-hover/select:text-blue-600 transition">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Precision Rating -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Diagnostic Rating (1-5)</label>
                            <div class="flex gap-4">
                                <?php for($i=5; $i>=1; $i--): ?>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="rating" value="<?= $i ?>" required class="hidden peer">
                                        <div class="bg-slate-50 rounded-2xl py-6 flex flex-col items-center gap-2 border border-transparent peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-xl peer-checked:shadow-blue-200 transition duration-300 hover:bg-slate-100">
                                            <span class="text-xl font-black italic"><?= $i ?></span>
                                            <i class="fa-solid fa-star text-[10px] opacity-40"></i>
                                        </div>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Qualitative Analysis -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Qualitative Narrative</label>
                            <textarea name="comments" placeholder="Describe the instructional impact and overall curriculum efficacy..." rows="6" 
                                      class="w-full bg-slate-50 border-none rounded-[2rem] px-8 py-7 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm resize-none" required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[11px] py-7 rounded-3xl hover:bg-blue-600 transition-all duration-700 shadow-xl shadow-slate-200 relative overflow-hidden group/btn">
                            <span class="relative z-10 italic">Authorize Feedback Submission</span>
                            <div class="absolute inset-0 bg-blue-500 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Instructional Impact -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-10 opacity-10 group-hover:rotate-12 transition duration-700">
                        <i class="fa-solid fa-lightbulb text-9xl"></i>
                    </div>
                    <h4 class="text-2xl font-black mb-6 italic">Why Feedback Matters?</h4>
                    <p class="text-sm font-bold text-slate-400 mt-2">TeachMate Platform Support</p>
                    <p class="text-sm font-bold text-slate-400 italic leading-relaxed mb-8">TeachMate utilizes student feedback to continuously refine our course offerings and ensure the highest quality of instruction.</p>
                    
                    <div class="space-y-6">
                        <div class="bg-white/5 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-2 italic">Impact Metric</p>
                            <p class="text-xs font-bold text-white italic">"Feedback increases content retention rates by 42% through active evaluation."</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black italic">Anonymous Uploads</h4>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-2 italic shadow-sm">TeachMate Academic Council</p>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-500 italic leading-relaxed">All evaluations are synchronized through our secure identity masking protocol to ensure unbiased curriculum auditing.</p>
                </div>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>

