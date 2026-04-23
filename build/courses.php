<?php 
require_once "inc/header.php";
require_once "inc/db.php";

// Fetch active courses from database
$courses_sql = "SELECT c.*, cat.name as category_name 
                FROM courses c 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                ORDER BY c.created_at DESC";
$courses_result = $conn->query($courses_sql);
?>
<script src="https://cdn.tailwindcss.com"></script>

<body class="relative min-h-screen bg-slate-950 font-sans text-slate-300 selection:bg-blue-500/30">

    <!-- Immersive Animated Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/20 rounded-full mix-blend-screen filter blur-[120px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-emerald-600/20 rounded-full mix-blend-screen filter blur-[120px]" style="animation-duration: 10s; animation-direction: reverse;"></div>
        <img src="./assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-10" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
    </div>
    <div class="fixed inset-0 z-0 backdrop-blur-[2px] bg-slate-950/70"></div>

    <div class="relative z-10 flex flex-col min-h-screen">
        <?php require_once "inc/nav.php"; ?>

        <!-- Hero Section -->
        <section class="h-96 relative flex items-center justify-center overflow-hidden border-b border-white/10 shadow-2xl">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="./assets/img/about.png" class="w-full h-full object-cover opacity-30 mix-blend-overlay" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=1600&q=80';">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
            </div>
            
            <div class="relative z-10 text-center px-4">
                <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400 drop-shadow-lg mb-4">
                    Explore Our Courses
                </h1>
                <p class="text-lg text-slate-300 max-w-2xl mx-auto font-medium">Discover top-tier educational tracks curated by expert faculty specifically designed to skyrocket your career.</p>
            </div>
        </section>

        <!-- Dynamic Courses Section -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 flex-grow relative z-10">
            <?php if ($courses_result && $courses_result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php while ($course = $courses_result->fetch_assoc()): 
                        $thumb = $course['thumbnail'];
                        $thumbnail = (strpos($thumb, 'http') === 0) ? $thumb : "admin/".$thumb;
                        if (empty($thumb)) $thumbnail = "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=500&q=60";
                        
                        $cat_name = htmlspecialchars($course['category_name'] ?? 'Uncategorized');
                        $t_name = htmlspecialchars($course['instructor_name'] ?? 'Faculty');
                        $c_title = htmlspecialchars($course['title']);
                        $c_desc = htmlspecialchars(substr($course['short_description'] ?? '', 0, 100)) . '...';
                        $price = "Free"; // Price column missing in current schema
                    ?>
                    
                    <div class="group bg-slate-800/40 backdrop-blur-md border border-white/10 rounded-2xl overflow-hidden hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.5)] hover:border-white/20 transition-all duration-300 flex flex-col">
                        <!-- Card Image Header -->
                        <div class="relative h-48 overflow-hidden bg-slate-900 border-b border-white/5">
                            <img src="<?= $thumbnail ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100" alt="<?= $c_title ?>">
                            <div class="absolute top-4 left-4">
                                <span class="bg-blue-600/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg border border-blue-400/30">
                                    <?= $cat_name ?>
                                </span>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <span class="bg-emerald-500/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-lg border border-emerald-400/30 shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                                    <?= $price ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2 leading-tight group-hover:text-blue-400 transition-colors"><?= $c_title ?></h3>
                                <p class="text-sm text-slate-400 font-medium mb-4 line-clamp-2"><?= $c_desc ?></p>
                            </div>
                            
                            <div class="pt-4 mt-auto border-t border-white/10 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white uppercase shadow-inner border border-white/20">
                                        <?= substr($t_name, 0, 1) ?>
                                    </div>
                                    <span class="text-xs font-medium text-slate-300"><?= $t_name ?></span>
                                </div>
                                <div>
                                    <a href="enroll.php?id=<?= $course['id'] ?>" class="text-sm font-bold text-blue-400 hover:text-white transition-colors flex items-center gap-1 group">
                                        Enroll Now 
                                        <i class="fas fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-20 bg-slate-800/20 backdrop-blur-md border border-white/5 rounded-3xl">
                    <div class="w-20 h-20 mx-auto bg-slate-800 rounded-full border border-white/10 flex items-center justify-center mb-6 shadow-xl">
                        <i class="fas fa-folder-open text-3xl text-slate-500"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">No Courses Found</h2>
                    <p class="text-slate-400 font-medium max-w-md mx-auto">We are currently updating our syllabus. Please check back later for awesome new content!</p>
                </div>
            <?php endif; ?>
        </section>

        <?php require_once "inc/footer.php"; ?>
    </div>
</body>
</html>
