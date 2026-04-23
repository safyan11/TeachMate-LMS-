<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Schedule Extraction Orchestration
$today_sync = date('Y-m-d H:i:s');
$sql = "SELECT oc.*, u.name AS teacher_name 
        FROM online_classes oc
        JOIN users u ON oc.teacher_id = u.id
        WHERE CONCAT(oc.class_date, ' ', oc.class_time) >= ?
        ORDER BY oc.class_date ASC, oc.class_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $today_sync);
$stmt->execute();
$classes_result = $stmt->get_result();
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-72 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-12 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h1 class="text-4xl font-black tracking-tight italic">Online Classes</h1>
                <p class="text-slate-500 font-medium italic mt-2 uppercase tracking-widest text-xs">Join your live sessions here</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-2 bg-white rounded-2xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest text-emerald-600 italic">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full inline-block animate-pulse mr-2"></span>
                    System Online
                </div>
            </div>
        </div>

        <!-- Live Hero Module -->
        <section class="mb-16">
            <div class="bg-slate-900 rounded-[3rem] p-10 lg:p-16 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-[40rem] h-[40rem] bg-blue-600/10 blur-[120px] -mr-80 -mt-80"></div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-widest mb-8 border border-white/10 backdrop-blur-md">
                            <i class="fa-solid fa-tower-broadcast text-blue-400"></i>
                            Session Monitoring Active
                        </div>
                        <h2 class="text-4xl lg:text-5xl font-black italic leading-tight mb-6">Synchronized <br><span class="text-blue-500 text-5xl lg:text-6xl">Knowledge Transfer.</span></h2>
                        <p class="text-slate-400 font-medium text-lg leading-relaxed italic">Join live classes and interact with your teachers in real-time.</p>
                    </div>
                    <div class="hidden lg:flex justify-end">
                        <div class="w-64 h-64 border border-white/10 rounded-[4rem] flex items-center justify-center p-8 bg-white/5 backdrop-blur-sm relative group-hover:border-blue-500/50 transition duration-700">
                            <i class="fa-solid fa-video text-8xl text-slate-700 group-hover:text-blue-500 transition duration-700"></i>
                            <div class="absolute -top-4 -right-4 w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center animate-bounce shadow-xl">
                                <i class="fa-solid fa-bolt text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Upcoming Sessions -->
        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-8 flex items-center gap-3 italic">
            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
            Class Schedule
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
            <?php if ($classes_result->num_rows > 0): ?>
                <?php while ($row = $classes_result->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2.5rem] p-3 border border-slate-100 shadow-sm hover:shadow-2xl transition duration-500 group">
                        <div class="bg-slate-50/50 rounded-[2.2rem] p-8">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition duration-500 shadow-sm border border-slate-50">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <span class="px-3 py-1 bg-white border border-slate-100 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-400 group-hover:text-blue-600 transition italic">
                                    Upcoming
                                </span>
                            </div>

                            <h4 class="text-lg font-black text-slate-800 mb-2 leading-tight italic"><?= htmlspecialchars($row['class_title']) ?></h4>
                            <div class="flex flex-col gap-1 mb-8">
                                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-widest italic">
                                    <i class="fa-solid fa-clock"></i>
                                    <?= date('M d, Y | H:i', strtotime($row['class_date'] . ' ' . $row['class_time'])) ?>
                                </div>
                                <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <?= htmlspecialchars($row['teacher_name']) ?>
                                </div>
                            </div>

                            <a href="<?= htmlspecialchars($row['meet_link']) ?>" target="_blank" 
                               class="block w-full text-center bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] py-4 rounded-[1.5rem] hover:bg-blue-600 transition-all duration-500 shadow-xl shadow-slate-100 group">
                                 Join Class <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition duration-300"></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-100 shadow-inner">
                    <i class="fa-solid fa-satellite-dish text-slate-200 text-6xl mb-6"></i>
                    <h3 class="text-xl font-black text-slate-400 italic">No online classes scheduled at the moment.</h3>
                    <p class="text-[10px] font-bold text-slate-300 uppercase mt-2 tracking-widest">Keep checking for updates</p>
                </div>
            <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
