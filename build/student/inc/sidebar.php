<?php
// ─── State for Student ───
$student_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE is_read=0");
$unread_notifications_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM chat_messages WHERE receiver_id=$student_id AND is_read=0");
$unread_chat_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE student_id=$student_id AND status='Pending'");
$pending_complaints_count = $r ? $r->fetch_assoc()['c'] : 0;

function student_badge($count) {
    if ($count > 0) {
        return "<span class=\"ml-auto bg-blue-600 text-white text-[9px] font-black rounded-lg px-2 py-0.5 shadow-sm shadow-blue-200\">$count</span>";
    }
    return "";
}

$current_page = basename($_SERVER['PHP_SELF']);
function isActiveStudent($page, $current) {
    return ($page == $current) ? 'bg-blue-50 text-blue-600 font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100';
}
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-72 bg-white border-r border-slate-100 transform -translate-x-full transition-all duration-300 ease-in-out md:translate-x-0 flex flex-col">

    <!-- Logo -->
    <div class="px-8 py-6 flex items-center gap-3 flex-shrink-0">
        <img src="../assets/img/teachmate_logo.png" alt="TeachMate Logo" class="w-12 h-12 rounded-[10px] shadow-md shadow-blue-200">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">TeachMate</h2>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">Student Hub</p>
        </div>
    </div>

    <!-- Nav Links — scrollable -->
    <nav class="flex-1 overflow-y-auto px-4 space-y-1 pb-2">
        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mb-2">Academic Hub</p>

        <a href="./studentdashboard.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('studentdashboard.php', $current_page) ?> transition">
            <i class="fas fa-th-large w-4 text-center"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a href="./playlist.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('playlist.php', $current_page) ?> transition">
            <i class="fas fa-play w-4 text-center"></i>
            <span class="text-sm font-medium">Course Playlist</span>
        </a>
        <a href="./material.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('material.php', $current_page) ?> transition">
            <i class="fas fa-file-alt w-4 text-center"></i>
            <span class="text-sm font-medium">Materials</span>
        </a>
        <a href="./assignment.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('assignment.php', $current_page) ?> transition">
            <i class="fas fa-tasks w-4 text-center"></i>
            <span class="text-sm font-medium">Assignments</span>
        </a>
        <a href="./onlineclass.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('onlineclass.php', $current_page) ?> transition">
            <i class="fas fa-video w-4 text-center"></i>
            <span class="text-sm font-medium">Online Classes</span>
        </a>

        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mt-5 mb-2">Account</p>

        <a href="./certificate.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('certificate.php', $current_page) ?> transition">
            <i class="fas fa-certificate w-4 text-center"></i>
            <span class="text-sm font-medium">My Certificates</span>
        </a>
        <a href="./complains.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('complains.php', $current_page) ?> transition">
            <i class="fas fa-exclamation-circle w-4 text-center"></i>
            <span class="text-sm font-medium">Complaints</span>
            <?= student_badge($pending_complaints_count) ?>
        </a>
        <a href="./feedback.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('feedback.php', $current_page) ?> transition">
            <i class="fas fa-comment w-4 text-center"></i>
            <span class="text-sm font-medium">Feedback</span>
        </a>
        <a href="./profile.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveStudent('profile.php', $current_page) ?> transition">
            <i class="fas fa-user-circle w-4 text-center"></i>
            <span class="text-sm font-medium">My Profile</span>
        </a>
    </nav>

    <!-- Logout — Pinned to Bottom -->
    <div class="flex-shrink-0 px-4 py-4 border-t border-slate-100">
        <a href="../logout.php"
           class="flex items-center justify-center gap-2 w-full py-3 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-red-200 hover:shadow-lg hover:shadow-red-300">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>
