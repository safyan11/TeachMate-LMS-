<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;
$success_msg = '';
$error_msg = '';

// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject'], $_POST['message'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO complaints (student_id, subject, message, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iss", $student_id, $subject, $message);
    if ($stmt->execute()) {
        $success_msg = "Support ticket synchronized. Transmission archived successfully.";
    } else {
        $error_msg = "Diagnostic failure: Submission failed to synchronize with global registry.";
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
                <h1 class="text-3xl font-black tracking-tight italic">Support Terminal</h1>
                <p class="text-slate-500 font-medium italic mt-2 uppercase tracking-widest text-[11px]">Advocacy & Conflict Resolution</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-2 bg-white rounded-2xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest text-slate-400 italic">
                    Uplink Priority: Standard
                </div>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="bg-emerald-50 text-emerald-700 p-6 rounded-[2rem] mb-10 border border-emerald-100 flex items-center gap-4 italic font-black shadow-lg shadow-emerald-50">
                <i class="fa-solid fa-circle-check text-xl"></i> <?= $success_msg ?>
            </div>
        <?php elseif ($error_msg): ?>
            <div class="bg-rose-50 text-rose-700 p-6 rounded-[2rem] mb-10 border border-rose-100 flex items-center gap-4 italic font-black shadow-lg shadow-rose-50">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            <!-- Ticket Submission Orbit -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[3rem] p-10 lg:p-12 border border-slate-100 shadow-sm hover:shadow-2xl transition duration-700 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-12 opacity-5">
                        <i class="fa-solid fa-paper-plane text-9xl group-hover:-translate-y-2 group-hover:translate-x-2 transition duration-700"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black mb-8 italic flex items-center gap-4">
                        <span class="w-1.5 h-10 bg-blue-600 rounded-full"></span>
                        Initialize Support Ticket
                    </h3>

                    <form method="POST" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Incident Identifier (Subject)</label>
                            <input type="text" name="subject" placeholder="Define the operational issue..." 
                                   class="w-full bg-slate-50 border-none rounded-2xl px-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Diagnostic Narrative (Message)</label>
                            <textarea name="message" placeholder="Provide a detailed log of the anomaly..." rows="6" 
                                      class="w-full bg-slate-50 border-none rounded-2xl px-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm resize-none" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[11px] py-6 rounded-3xl hover:bg-blue-600 transition-all duration-500 shadow-xl shadow-slate-200">
                            Execute Transmission
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contextual Info -->
            <aside class="space-y-8">
                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:rotate-12 transition duration-500">
                        <i class="fa-solid fa-headset text-8xl"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 italic">Advocacy Protocol</h4>
                    <p class="text-xs font-bold text-blue-100 italic leading-relaxed mb-6">Your transmission will be audited by the administrative board. Resolution latency typically does not exceed 48 operational hours.</p>
                    <div class="flex items-center gap-4 py-4 border-t border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-blue-400"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Priority Synchronization active</span>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm italic">
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-3">
                        Incident Archetypes
                        <span class="flex-1 h-[1px] bg-slate-50"></span>
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full mt-1.5 shrink-0"></div>
                            <p class="text-xs font-bold text-slate-500">Technical Synchronization Anomalies</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 shrink-0"></div>
                            <p class="text-xs font-bold text-slate-500">Curriculum Asset Access Violations</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 shrink-0"></div>
                            <p class="text-xs font-bold text-slate-500">Evaluation Logic Discrepancies</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
