<?php
// ─── Badge Logic for Teacher ───
$teacher_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM chat_messages WHERE receiver_id=$teacher_id AND is_read=0");
$unread_chat_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM submissions s JOIN assignments a ON s.assignment_id = a.id WHERE a.uploaded_by = $teacher_id AND s.status = 'pending'");
$pending_submissions_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM online_classes oc WHERE oc.teacher_id = $teacher_id AND oc.class_date = CURDATE() AND NOT EXISTS (SELECT 1 FROM attendance att WHERE att.class_id = oc.id LIMIT 1)");
$unmarked_attendance_count = $r ? $r->fetch_assoc()['c'] : 0;

function teacher_badge($count) {
    if ($count > 0) {
        return "<span class=\"ml-auto bg-blue-600 text-white text-[10px] font-bold rounded-full px-2 py-0.5\">$count</span>";
    }
    return "";
}

$current_page = basename($_SERVER['PHP_SELF']);
function isActiveTeacher($page, $current) {
    return ($page == $current) ? 'bg-blue-50 text-blue-600 font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100';
}
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-64 bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col">

    <!-- Logo -->
    <div class="px-8 py-6 flex items-center gap-3 flex-shrink-0">
        <img src="../assets/img/teachmate_logo.png" alt="TeachMate Logo" class="w-12 h-12 rounded-[10px] shadow-md shadow-blue-200">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">TeachMate</h2>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">Faculty Portal</p>
        </div>
    </div>

    <!-- Nav Links — scrollable -->
    <nav class="flex-1 overflow-y-auto px-4 space-y-1 pb-2">
        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mb-2">Main Menu</p>

        <a href="./teacherdashboard.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('teacherdashboard.php', $current_page) ?> transition">
            <i class="fas fa-th-large w-4 text-center"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a href="./studentstatus.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('studentstatus.php', $current_page) ?> transition">
            <i class="fas fa-users w-4 text-center"></i>
            <span class="text-sm font-medium">Student Status</span>
        </a>

        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mt-5 mb-2">Resources</p>

        <a href="./sharematerial.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('sharematerial.php', $current_page) ?> transition">
            <i class="fas fa-share-alt w-4 text-center"></i>
            <span class="text-sm font-medium">Share Material</span>
        </a>
        <a href="./assignment.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('assignment.php', $current_page) ?> transition">
            <i class="fas fa-file-upload w-4 text-center"></i>
            <span class="text-sm font-medium">Upload Assignment</span>
        </a>
        <a href="./grading.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('grading.php', $current_page) ?> transition">
            <i class="fas fa-graduation-cap w-4 text-center"></i>
            <span class="text-sm font-medium">Grading Assignment</span>
            <?= teacher_badge($pending_submissions_count) ?>
        </a>
        <a href="./onlineclass.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('onlineclass.php', $current_page) ?> transition">
            <i class="fas fa-video w-4 text-center"></i>
            <span class="text-sm font-medium">Online Class</span>
        </a>
        <a href="./profile.php" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= isActiveTeacher('profile.php', $current_page) ?> transition">
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
