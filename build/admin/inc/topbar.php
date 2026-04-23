<?php
// Fetch user info for topbar
$topbar_user_name = 'Guest';
$topbar_profile_img = 'https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff';

if (isset($_SESSION['user_id'])) {
    $tb_uid = intval($_SESSION['user_id']);
    $tb_stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $tb_stmt->bind_param("i", $tb_uid);
    $tb_stmt->execute();
    $tb_stmt->bind_result($tb_name, $tb_img);
    if ($tb_stmt->fetch()) {
        $topbar_user_name = htmlspecialchars($tb_name);
        if (!empty($tb_img)) $topbar_profile_img = "../uploads/profile/" . $tb_img;
    }
    $tb_stmt->close();
}
?>
<header class="px-6 py-4 bg-white border-b border-gray-100 sticky top-0 z-10">
    <div class="flex items-center justify-between gap-4">
        <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-500 hover:text-blue-600 focus:outline-none transition">
            <i class="fas fa-bars text-xl"></i>
        </button>


        <!-- Search -->
        <div class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
            <input type="text" placeholder="Search..."
                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
        </div>

        <!-- Profile Dropdown -->
        <div class="relative group flex-shrink-0">
            <a href="./profile.php" class="flex items-center gap-3 p-1.5 pr-4 bg-gray-50 hover:bg-gray-100 rounded-2xl border border-gray-200 transition cursor-pointer">
                <div class="w-9 h-9 rounded-xl overflow-hidden border-2 border-white shadow-sm">
                    <img src="<?= $topbar_profile_img ?>" class="w-full h-full object-cover" alt="avatar">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-xs font-bold text-gray-800 leading-none mb-0.5"><?= $topbar_user_name ?></p>
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">LMS Admin</p>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-400 ml-1"></i>
            </a>
        </div>

    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    if(btn && sidebar) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('-translate-x-full');
        });
        document.addEventListener('click', (e) => {
            if(!sidebar.contains(e.target) && !btn.contains(e.target) && !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    }
});
</script>
