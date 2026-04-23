<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = intval($_SESSION['user_id'] ?? 1);

// Award Inventory extraction
$sql = "SELECT cert.id, cert.file_path, cert.uploaded_at, c.title as course_title 
        FROM certificates cert 
        JOIN courses c ON cert.course_id = c.id 
        WHERE cert.student_id = $student_id
        ORDER BY cert.uploaded_at DESC";
$result = $conn->query($sql);
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-72 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-12 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight italic">Trophy Room</h1>
                <p class="text-slate-500 font-medium italic mt-2 uppercase tracking-widest text-[11px]">Academic Credential Repository</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-2 bg-white rounded-2xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest text-emerald-600 italic">
                    Verification Protocol: Active
                </div>
            </div>
        </div>

        <!-- Trophy Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition duration-700 group flex flex-col relative overflow-hidden">
                        <!-- Premium Accent -->
                        <div class="h-2 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                        
                        <div class="p-8 flex-1">
                            <div class="flex justify-between items-start mb-8">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition duration-500 shadow-inner">
                                    <i class="fa-solid fa-award text-3xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Issued On</p>
                                    <p class="text-[10px] font-black text-slate-900 italic"><?= date("M d, Y", strtotime($row['uploaded_at'])) ?></p>
                                </div>
                            </div>

                            <h3 class="text-xl font-black text-slate-800 mb-2 leading-tight italic group-hover:text-blue-600 transition tracking-tighter">
                                <?= htmlspecialchars($row['course_title']) ?>
                            </h3>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-2 italic shadow-sm">TeachMate Academic Council</p>
                            
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 group-hover:bg-blue-50/50 transition duration-500">
                                    <i class="fa-solid fa-square-person-confined text-blue-400 text-xs"></i>
                                    <span class="text-[10px] font-black text-slate-500 tracking-tight italic">Authenticated Curriculum Identity</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 pt-0 flex gap-4 mt-auto">
                            <a href="../admin/<?= $row['file_path'] ?>" target="_blank" class="flex-1 bg-slate-50 text-slate-900 text-center py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition duration-500 shadow-sm">
                                <i class="fa-solid fa-expand mr-2"></i> Audit
                            </a>
                            <a href="../admin/<?= $row['file_path'] ?>" download class="flex-1 bg-blue-600 text-white text-center py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition duration-500 shadow-xl shadow-blue-50">
                                <i class="fa-solid fa-cloud-arrow-down mr-2"></i> Archivist
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-24 text-center bg-white rounded-[4rem] border-2 border-dashed border-slate-100 shadow-inner">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                        <i class="fa-solid fa-medal text-4xl text-slate-200"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-400 italic">Inventory Vacant</h3>
                    <p class="text-[10px] font-bold text-slate-300 uppercase mt-2 tracking-[0.2em]">Synchronize curriculum completion to earn awards.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Motivational Summary -->
        <div class="mt-20 p-10 bg-slate-900 rounded-[3rem] text-white relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 blur-[100px] -mr-48 -mt-48"></div>
            <div class="relative z-10 max-w-2xl">
                <h4 class="text-3xl font-black mb-6 italic italic">Global Accreditation.</h4>
                <p class="text-sm font-bold text-slate-400 italic leading-relaxed">Each award archived here remains permanently verified within the TeachMate global ledger. Professional credibility is established through verified instructional execution.</p>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>

