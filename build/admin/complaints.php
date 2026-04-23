<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

$message = "";

// 1. Handle Status Toggle
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $newStatus = $_GET['current_status'] === 'resolved' ? 'Pending' : 'Resolved';
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $id);
    if ($stmt->execute()) $message = "Complain #$id status updated to $newStatus.";
    $stmt->close();
}

// 2. DELETE complaint
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) $message = "Ticket #$delete_id purged from registry.";
    $stmt->close();
}

// 3. FETCH complaints
$result = $conn->query("SELECT c.*, u.name AS std_name, u.email as std_email 
                        FROM complaints c
                        LEFT JOIN users u ON c.student_id = u.id
                        ORDER BY c.created_at DESC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10">
            <h1 class="text-3xl font-black tracking-tight">Support Tickets</h1>
            <p class="text-slate-500 font-medium">Coordinate resolution and manage platform grievances.</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-blue-50 text-blue-700 p-4 rounded-2xl mb-8 font-bold border border-blue-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-info"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Complaints Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden text-sm">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">Active Grievances</h3>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Platform Sync Active</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reporter</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking_widest">Issue Subject</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Filed Date</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): 
                                $statusColor = strtolower($row['status']) === 'resolved' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600';
                            ?>
                                <tr class="hover:bg-slate-50/50 transition duration-200">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center font-black text-slate-400 uppercase">
                                                <?= substr($row['std_name'] ?? 'U', 0, 1) ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800"><?= htmlspecialchars($row['std_name'] ?? 'Anonymous') ?></p>
                                                <p class="text-[9px] font-bold text-slate-400 lowercase"><?= htmlspecialchars($row['std_email'] ?? 'n/a') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 max-w-xs">
                                        <p class="font-black text-blue-600 text-[10px] uppercase tracking-tighter mb-1"><?= htmlspecialchars($row['subject']) ?></p>
                                        <p class="text-slate-500 font-medium leading-relaxed truncate"><?= htmlspecialchars($row['message']) ?></p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest <?= $statusColor ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-[10px] font-bold text-slate-400">
                                        <?= date("d M, Y", strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="?toggle_status=<?= $row['id'] ?>&current_status=<?= strtolower($row['status']) ?>" 
                                               class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition"
                                               title="Toggle ResolutionStatus">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                            <a href="?delete_id=<?= $row['id'] ?>" 
                                               onclick="return confirm('Archive and delete this ticket permanently?')" 
                                               class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-400 hover:bg-rose-600 hover:text-white transition">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-bold italic">Clear skies! No pending tickets detected.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
