<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Notification Status Orchestration
if (isset($_GET['mark_read'])) {
    $nid = intval($_GET['mark_read']);
    $conn->query("UPDATE notifications SET is_read=1 WHERE id=$nid AND user_id=$student_id");
    header("Location: notifications.php");
    exit;
}

// Bulk Sync Orchestration
if (isset($_POST['mark_all_read'])) {
    $conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$student_id AND is_read=0");
}

$notifications_result = $conn->query("SELECT * FROM notifications WHERE user_id=$student_id ORDER BY created_at DESC LIMIT 50");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-72 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-12 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight italic">Communication Terminal</h1>
                <p class="text-slate-500 font-medium italic mt-2 uppercase tracking-widest text-[11px]">Synchronized System Alerts</p>
            </div>
            <div class="flex items-center gap-4">
                <form method="POST">
                    <button type="submit" name="mark_all_read" class="px-5 py-2.5 bg-white text-slate-900 rounded-xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition duration-500">
                        Synchronize All Read
                    </button>
                </form>
            </div>
        </div>

        <!-- Notification Feed -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
            <?php if ($notifications_result && $notifications_result->num_rows > 0): ?>
                <div class="divide-y divide-slate-50">
                    <?php while ($notif = $notifications_result->fetch_assoc()): ?>
                        <div class="group relative flex items-center justify-between p-8 hover:bg-slate-50/50 transition duration-500 <?= $notif['is_read'] ? '' : 'bg-blue-50/30' ?>">
                            <!-- Unread Marker -->
                            <?php if (!$notif['is_read']): ?>
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-blue-600 rounded-r-full shadow-lg shadow-blue-500/20"></div>
                            <?php endif; ?>

                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition duration-500 shadow-sm">
                                    <i class="fa-solid <?= $notif['is_read'] ? 'fa-envelope-open' : 'fa-envelope-dot animate-pulse' ?> text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 leading-tight italic group-hover:text-blue-600 transition"><?= htmlspecialchars($notif['message']) ?></h4>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2 italic flex items-center gap-2">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        Archived <?= date('M d, Y | h:i A', strtotime($notif['created_at'])) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <?php if (!$notif['is_read']): ?>
                                    <a href="notifications.php?mark_read=<?= $notif['id'] ?>" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all duration-500 shadow-lg shadow-blue-200">
                                        Acknowledge
                                    </a>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[8px] font-black uppercase tracking-widest italic border border-emerald-100">
                                        Audited
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="py-32 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner text-slate-200 text-3xl">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-400 italic">Feed Synchronized.</h3>
                    <p class="text-[10px] font-bold text-slate-300 uppercase mt-2 tracking-widest">No active system alerts at this coordinate.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- System Status Footer -->
        <div class="mt-12 p-8 bg-slate-900 rounded-[2.5rem] flex items-center justify-between text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-[80px] -mr-32 -mt-32"></div>
            <div class="flex items-center gap-6 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black italic">Network Integrity</h4>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">All communication channels are encrypted and monitored.</p>
                </div>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
