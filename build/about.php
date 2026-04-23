<?php require_once "inc/header.php"; ?>

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
        <section class="h-[400px] relative flex items-center justify-center overflow-hidden border-b border-white/10 shadow-2xl">
            <div class="absolute inset-0 z-0">
                <img src="./assets/img/about.png" class="w-full h-full object-cover opacity-30 mix-blend-overlay" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=80';">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
            </div>
            
            <div class="relative z-10 text-center px-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-6">
                    Who We Are
                </div>
                <h1 class="text-5xl md:text-6xl font-black text-white drop-shadow-lg mb-4">
                    About <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">TeachMate</span>
                </h1>
            </div>
        </section>

        <!-- About Us Content -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-16"> 
                <div class="lg:w-1/2 w-full">
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Learn, Create, and Grow with Us</h2>
                    <div class="w-16 h-1 bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full mb-8"></div>
                    
                    <p class="text-lg leading-relaxed text-slate-400 mb-6 font-medium">
                        At TeachMate, we believe in empowering individuals with the skills they need to succeed in the rapidly evolving digital world. Whether you want to master Web Development, explore Creative Designing, or conquer Digital Marketing, our expert-led courses are built specifically for you.
                    </p>
                    <p class="text-lg leading-relaxed text-slate-400 font-medium">
                        Our centralized Learning Management System ensures that students get 1-on-1 mentorship, state-of-the-art grading tools, and real-time class interactions regardless of their background or location.
                    </p>
                </div>
                
                <div class="lg:w-1/2 w-full relative">
                    <!-- Glass image frame -->
                    <div class="absolute inset-0 bg-gradient-to-bl from-blue-600 to-emerald-500 rounded-3xl transform -rotate-3 scale-105 opacity-20 blur-xl"></div>
                    <div class="relative rounded-3xl overflow-hidden border border-white/10 bg-slate-800 p-2 shadow-2xl">
                        <img src="./assets/img/aboutimg.avif" alt="About Us" class="w-full h-auto rounded-2xl opacity-90 transition-opacity duration-500 hover:opacity-100" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80';">
                    </div>
                </div>
            </div>
        </section>

        <!-- Achievements (Glass Cards) -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10 border-t border-white/5 bg-slate-900/30">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Our Milestones</h2>
            </div>

            <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
                
                <!-- Card 1 -->
                <div class="group text-center bg-slate-800/40 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-[0_10px_30px_rgba(0,0,0,0.3)] hover:-translate-y-2 hover:bg-slate-800/60 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-blue-500/20 to-blue-500/5 rounded-2xl border border-blue-500/20 flex items-center justify-center mb-6 text-blue-400 shadow-inner">
                        <i class="fa fa-pencil text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Global Reach</h3>
                    <p class="text-sm font-medium text-slate-400 leading-relaxed">
                        We are proud of our achievements in online learning. With over 5000+ satisfied students trained worldwide, our platform has become a trusted name for digital learners.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="group text-center bg-slate-800/40 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-[0_10px_30px_rgba(0,0,0,0.3)] hover:-translate-y-2 hover:bg-slate-800/60 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-emerald-500/20 to-emerald-500/5 rounded-2xl border border-emerald-500/20 flex items-center justify-center mb-6 text-emerald-400 shadow-inner">
                        <i class="fa fa-book text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Our Vision</h3>
                    <p class="text-sm font-medium text-slate-400 leading-relaxed">
                        Our vision is to empower individuals by providing accessible, high-quality digital education. We aim to break barriers of location, making advanced tech skills universally available.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="group text-center bg-slate-800/40 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-[0_10px_30px_rgba(0,0,0,0.3)] hover:-translate-y-2 hover:bg-slate-800/60 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-purple-500/20 to-purple-500/5 rounded-2xl border border-purple-500/20 flex items-center justify-center mb-6 text-purple-400 shadow-inner">
                        <i class="fa fa-globe text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Our Mission</h3>
                    <p class="text-sm font-medium text-slate-400 leading-relaxed">
                        Our mission is to deliver practical, skill-based training. We strive to create a supportive learning ecosystem where students not only consume knowledge but also rapidly build careers.
                    </p>
                </div>

            </div>
        </section>

        <!-- Testimonials Mini Section -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10">
            <div class="bg-gradient-to-br from-blue-900/40 to-emerald-900/40 border border-white/10 rounded-3xl p-10 backdrop-blur-xl text-center shadow-2xl relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/30 rounded-full blur-[80px]"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-blue-500/30 rounded-full blur-[80px]"></div>
                
                <h2 class="text-3xl font-extrabold text-white mb-6 relative z-10">Ready to transform your future?</h2>
                <p class="text-slate-300 font-medium mb-10 max-w-xl mx-auto relative z-10">Join thousands of students who have already accelerated their careers using TeachMate.</p>
                <a href="./signup.php" class="relative z-10 inline-flex items-center justify-center px-8 py-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-500 hover:to-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:-translate-y-1 transition-all duration-300">
                    Get Started Now <i class="fas fa-rocket ml-3"></i>
                </a>
            </div>
        </section>

        <?php require_once "inc/footer.php"; ?>
    </div>
</body>
</html>