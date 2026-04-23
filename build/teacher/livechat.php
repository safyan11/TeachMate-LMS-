<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'];

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_msg'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $message     = $conn->real_escape_string(trim($_POST['message']));
    if (!empty($message) && $receiver_id > 0) {
        $conn->query("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES ($teacher_id, $receiver_id, '$message')");
    }
    header("Location: livechat.php?with=" . $receiver_id);
    exit;
}

// Mark messages as read
$chat_with = isset($_GET['with']) ? intval($_GET['with']) : 0;
if ($chat_with > 0) {
    $conn->query("UPDATE chat_messages SET is_read=1 WHERE sender_id=$chat_with AND receiver_id=$teacher_id AND is_read=0");
}

// Fetch all students for contact list
$students_res = $conn->query("SELECT u.id, u.name, u.email,
    (SELECT COUNT(*) FROM chat_messages WHERE sender_id=u.id AND receiver_id=$teacher_id AND is_read=0) as unread_count,
    (SELECT message FROM chat_messages WHERE (sender_id=u.id AND receiver_id=$teacher_id) OR (sender_id=$teacher_id AND receiver_id=u.id) ORDER BY sent_at DESC LIMIT 1) as last_message
    FROM users u WHERE u.role='student' ORDER BY u.name ASC");

// Fetch conversation
$messages = [];
if ($chat_with > 0) {
    $msg_res = $conn->query("SELECT m.*, u.name as sender_name FROM chat_messages m LEFT JOIN users u ON m.sender_id=u.id WHERE (m.sender_id=$teacher_id AND m.receiver_id=$chat_with) OR (m.sender_id=$chat_with AND m.receiver_id=$teacher_id) ORDER BY m.sent_at ASC");
    while ($m = $msg_res->fetch_assoc()) $messages[] = $m;
}

$chat_user = null;
if ($chat_with > 0) {
    $chat_user = $conn->query("SELECT name, email FROM users WHERE id=$chat_with")->fetch_assoc();
}
?>
<body class="bg-gray-50 font-sans antialiased">
<div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>
  <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
  <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
    <?php require_once "inc/topbar.php"; ?>

    <div class="flex h-[calc(100vh-64px)]">

      <!-- Contact List -->
      <div class="w-72 bg-white border-r flex flex-col">
        <div class="p-4 border-b">
          <h2 class="text-lg font-bold text-gray-800">💬 Live Chat</h2>
          <p class="text-xs text-gray-500 mt-1">Chat with your students</p>
        </div>
        <div class="overflow-y-auto flex-1">
          <?php while ($s = $students_res->fetch_assoc()): ?>
            <a href="livechat.php?with=<?= $s['id'] ?>"
               class="flex items-center gap-3 p-3 border-b hover:bg-gray-50 transition <?= $chat_with == $s['id'] ? 'bg-blue-50 border-l-4 border-blue-600' : '' ?>">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                <?= strtoupper(substr($s['name'], 0, 1)) ?>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center">
                  <p class="font-semibold text-sm text-gray-800 truncate"><?= htmlspecialchars($s['name']) ?></p>
                  <?php if ($s['unread_count'] > 0): ?>
                    <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0"><?= $s['unread_count'] ?></span>
                  <?php endif; ?>
                </div>
                <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars(substr($s['last_message'] ?? 'No messages yet', 0, 35)) ?></p>
              </div>
            </a>
          <?php endwhile; ?>
        </div>
      </div>

      <!-- Chat Window -->
      <div class="flex-1 flex flex-col bg-gray-50">
        <?php if ($chat_with > 0 && $chat_user): ?>
          <!-- Chat Header -->
          <div class="bg-white border-b px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
              <?= strtoupper(substr($chat_user['name'], 0, 1)) ?>
            </div>
            <div>
              <p class="font-bold text-gray-800"><?= htmlspecialchars($chat_user['name']) ?></p>
              <p class="text-xs text-green-500">● Online</p>
            </div>
          </div>

          <!-- Messages -->
          <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chatBox">
            <?php if (empty($messages)): ?>
              <div class="text-center text-gray-400 mt-20">
                <i class="fa-solid fa-comments text-5xl mb-4 block"></i>
                <p>No messages yet. Say hello! 👋</p>
              </div>
            <?php endif; ?>
            <?php foreach ($messages as $msg): ?>
              <?php $is_mine = $msg['sender_id'] == $teacher_id; ?>
              <div class="flex <?= $is_mine ? 'justify-end' : 'justify-start' ?>">
                <div class="max-w-xs lg:max-w-md">
                  <div class="px-4 py-2 rounded-2xl text-sm <?= $is_mine ? 'bg-blue-600 text-white rounded-br-sm' : 'bg-white text-gray-800 shadow rounded-bl-sm' ?>">
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                  </div>
                  <p class="text-xs text-gray-400 mt-1 <?= $is_mine ? 'text-right' : 'text-left' ?>">
                    <?= date('h:i A', strtotime($msg['sent_at'])) ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Message Input -->
          <div class="bg-white border-t p-4">
            <form method="POST" class="flex gap-3">
              <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">
              <input type="text" name="message" placeholder="Type a message..." autofocus autocomplete="off"
                class="flex-1 border border-gray-300 rounded-full px-5 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              <button type="submit" name="send_msg" class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition font-semibold text-sm">
                Send <i class="fa-solid fa-paper-plane ml-1"></i>
              </button>
            </form>
          </div>

        <?php else: ?>
          <div class="flex-1 flex items-center justify-center text-gray-400 flex-col">
            <i class="fa-solid fa-comments text-7xl mb-6 opacity-30"></i>
            <p class="text-xl font-semibold">Select a student to start chatting</p>
            <p class="text-sm mt-2">Choose from the list on the left</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<script>
  // Auto-scroll to bottom of chat
  const chatBox = document.getElementById('chatBox');
  if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>