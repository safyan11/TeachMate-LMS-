<?php
// ─── Badge Logic ───
$r = $conn->query("SELECT COUNT(*) as c FROM users WHERE verify_status='pending'");
$pending_users_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status='Pending'");
$pending_complaints_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE is_read=0");
$unread_notifications_count = $r ? $r->fetch_assoc()['c'] : 0;

function show_badge($count) {
    if ($count > 0) {
        return "<span class=\"ml-auto bg-red-600 text-white text-[10px] font-bold rounded-full px-2 py-0.5\">$count</span>";
    }
    return "";
}

$current_page = basename($_SERVER['PHP_SELF']);
function isActiveAdmin($page, $current) {
    return ($page == $current) ? 'bg-blue-50 text-blue-600 font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100';
}
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-64 bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col">

    <!-- Logo -->
    <div class="px-8 py-6 flex items-center gap-3 flex-shrink-0">
        <img src="../assets/img/teachmate_logo.png" alt="TeachMate Logo" class="w-12 h-12 rounded-[10px] shadow-md shadow-blue-200">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">TeachMate</h2>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">LMS Admin</p>
        </div>
    </div>

    <!-- Nav Links — scrollable -->
    <nav class="flex-1 overflow-y-auto px-4 space-y-1 pb-2">
        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mb-2">Main Menu</p>

        <a href="./admindashboard.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('admindashboard.php', $current_page) ?> transition">
            <i class="fas fa-th-large w-4 text-center"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a href="./adduser.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('adduser.php', $current_page) ?> transition">
            <i class="fas fa-user-plus w-4 text-center"></i>
            <span class="text-sm font-medium">Users</span>
            <?= show_badge($pending_users_count) ?>
        </a>
        <a href="./addcourse.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('addcourse.php', $current_page) ?> transition">
            <i class="fas fa-book w-4 text-center"></i>
            <span class="text-sm font-medium">Courses</span>
        </a>

        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mt-5 mb-2">Other</p>

        <a href="./complaints.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('complaints.php', $current_page) ?> transition">
            <i class="fas fa-comment-dots w-4 text-center"></i>
            <span class="text-sm font-medium">Student Complaints</span>
            <?= show_badge($pending_complaints_count) ?>
        </a>
        <a href="./certification.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('certification.php', $current_page) ?> transition">
            <i class="fas fa-certificate w-4 text-center"></i>
            <span class="text-sm font-medium">Certificates</span>
        </a>
        <a href="./feedback.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('feedback.php', $current_page) ?> transition">
            <i class="fas fa-star w-4 text-center"></i>
            <span class="text-sm font-medium">Feedback</span>
        </a>
        <a href="./profile.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveAdmin('profile.php', $current_page) ?> transition">
            <i class="fas fa-user-circle w-4 text-center"></i>
            <span class="text-sm font-medium">My Profile</span>
        </a>
    </nav>

    <!-- Logout — Pinned to Bottom -->
    <div class="flex-shrink-0 px-4 py-4 border-t border-gray-100">
        <a href="../logout.php"
           class="flex items-center justify-center gap-2 w-full py-3 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-red-200 hover:shadow-lg hover:shadow-red-300">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>
