<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Message Transmission Protocol
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_msg'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $message     = $conn->real_escape_string(trim($_POST['message']));
    if (!empty($message) && $receiver_id > 0) {
        $conn->query("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES ($student_id, $receiver_id, '$message')");
    }
    header("Location: livechat.php?with=" . $receiver_id);
    exit;
}

$chat_with = isset($_GET['with']) ? intval($_GET['with']) : 0;
if ($chat_with > 0) {
    $conn->query("UPDATE chat_messages SET is_read=1 WHERE sender_id=$chat_with AND receiver_id=$student_id AND is_read=0");
}

// Instructor Inventory Extraction
$teachers_res = $conn->query("SELECT u.id, u.name, u.email,
    (SELECT COUNT(*) FROM chat_messages WHERE sender_id=u.id AND receiver_id=$student_id AND is_read=0) as unread_count,
    (SELECT message FROM chat_messages WHERE (sender_id=u.id AND receiver_id=$student_id) OR (sender_id=$student_id AND receiver_id=u.id) ORDER BY sent_at DESC LIMIT 1) as last_message
    FROM users u WHERE u.role='teacher' ORDER BY u.name ASC");

// Conversation Stream Extraction
$messages = [];
if ($chat_with > 0) {
    $msg_res = $conn->query("SELECT m.*, u.name as sender_name FROM chat_messages m LEFT JOIN users u ON m.sender_id=u.id WHERE (m.sender_id=$student_id AND m.receiver_id=$chat_with) OR (m.sender_id=$chat_with AND m.receiver_id=$student_id) ORDER BY m.sent_at ASC");
    while ($m = $msg_res->fetch_assoc()) $messages[] = $m;
}

$chat_user = null;
if ($chat_with > 0) {
    $chat_user = $conn->query("SELECT name, email FROM users WHERE id=$chat_with")->fetch_assoc();
}
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-72 transition-all duration-300 h-screen overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <main class="flex-1 flex overflow-hidden">
        <!-- Instructor Uplink Queue -->
        <aside class="w-80 lg:w-96 bg-white border-r border-slate-100 flex flex-col shrink-0">
            <div class="p-8 border-b border-slate-50">
                <h2 class="text-xl font-black italic tracking-tight">Instructional Uplink</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Faculty Synchronization Queue</p>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                <?php while ($t = $teachers_res->fetch_assoc()): ?>
                    <a href="livechat.php?with=<?= $t['id'] ?>"
                       class="group flex items-center gap-4 p-5 rounded-[2rem] transition-all duration-500 <?= $chat_with == $t['id'] ? 'bg-slate-900 text-white shadow-2xl shadow-slate-200' : 'hover:bg-slate-50 text-slate-600' ?>">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black italic shadow-lg">
                                <?= strtoupper(substr($t['name'], 0, 1)) ?>
                            </div>
                            <?php if ($t['unread_count'] > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[8px] font-black rounded-full w-5 h-5 flex items-center justify-center border-2 border-white animate-bounce"><?= $t['unread_count'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <h4 class="text-xs font-black truncate italic transition group-hover:text-blue-600 <?= $chat_with == $t['id'] ? 'text-white' : '' ?>">
                                    <?= htmlspecialchars($t['name']) ?>
                                </h4>
                            </div>
                            <p class="text-[10px] font-bold opacity-60 truncate italic capitalize">
                                <?= htmlspecialchars(substr($t['last_message'] ?? 'No active synchronized logs.', 0, 35)) ?>
                            </p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </aside>

        <!-- Communication Stream -->
        <section class="flex-1 flex flex-col bg-slate-50/50 relative overflow-hidden">
            <?php if ($chat_with > 0 && $chat_user): ?>
                <!-- Stream Header -->
                <header class="bg-white/80 backdrop-blur-xl border-b border-slate-100 px-8 py-5 flex items-center justify-between sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black italic shadow-lg shadow-blue-200">
                            <?= strtoupper(substr($chat_user['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="text-sm font-black italic text-slate-800 tracking-tight"><?= htmlspecialchars($chat_user['name']) ?></h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Instructional Uplink Active</p>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Message Protocol Feed -->
                <div class="flex-1 overflow-y-auto p-10 space-y-8 custom-scrollbar" id="chatStream">
                    <?php if (empty($messages)): ?>
                        <div class="h-full flex items-center justify-center flex-col opacity-20">
                            <i class="fa-solid fa-satellite-dish text-8xl mb-6"></i>
                            <p class="text-xl font-black italic">Initialize Synchronization Protocol</p>
                            <p class="text-xs font-bold uppercase tracking-widest mt-2">Transmissions requested for archival.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($messages as $msg): ?>
                        <?php $is_mine = $msg['sender_id'] == $student_id; ?>
                        <div class="flex <?= $is_mine ? 'justify-end' : 'justify-start' ?> group">
                            <div class="max-w-md">
                                <div class="px-7 py-5 rounded-[2rem] text-sm font-bold shadow-sm transition-all duration-300 <?= $is_mine ? 'bg-slate-900 text-white rounded-br-sm shadow-slate-200' : 'bg-white text-slate-800 rounded-bl-sm border border-slate-100' ?>">
                                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                </div>
                                <div class="flex items-center gap-3 mt-3 px-2 <?= $is_mine ? 'justify-end' : 'justify-start' ?>">
                                    <p class="text-[9px] font-black text-slate-300 uppercase italic">
                                        <?= date('h:i A', strtotime($msg['sent_at'])) ?>
                                    </p>
                                    <?php if($is_mine): ?>
                                        <i class="fa-solid fa-check-double text-[10px] text-blue-500"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Input Terminal -->
                <footer class="p-8 bg-white border-t border-slate-100">
                    <form method="POST" class="max-w-5xl mx-auto flex gap-4">
                        <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">
                        <div class="flex-1 relative group">
                            <input type="text" name="message" placeholder="Initialize transmission to instructor..." autofocus autocomplete="off"
                                   class="w-full bg-slate-50 border-none rounded-[2rem] px-8 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm">
                        </div>
                        <button type="submit" name="send_msg" class="bg-blue-600 text-white px-10 rounded-[2rem] hover:bg-slate-900 transition-all duration-500 font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-blue-100 shrink-0">
                            Execute <i class="fa-solid fa-paper-plane ml-3"></i>
                        </button>
                    </form>
                </footer>
            <?php else: ?>
                <div class="flex-1 flex items-center justify-center flex-col text-center p-12">
                    <div class="w-32 h-32 bg-white rounded-[4rem] border border-slate-100 shadow-inner flex items-center justify-center text-slate-100 mb-8">
                        <i class="fa-solid fa-shield-cat text-7xl"></i>
                    </div>
                    <h2 class="text-3xl font-black italic text-slate-300">Synchronize with Instructors</h2>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-3">Select a faculty node to begin instructional uplink.</p>
                </div>
            <?php endif; ?>
        </section>
      </main>
    </div>
  </div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
  const chatStream = document.getElementById('chatStream');
  if (chatStream) chatStream.scrollTop = chatStream.scrollHeight;
</script>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
