<?php require_once "inc/header.php"; ?>

<script src="https://cdn.tailwindcss.com"></script>

<style>
/* Modern Animated 3D Text Effect for Dark Background */
.text-3d {
    color: #fff;
    text-shadow: 
        0px 1px 0px #0284c7, 
        0px 2px 0px #0369a1, 
        0px 3px 0px #075985, 
        0px 4px 0px #082f49,
        0px 5px 15px rgba(0,0,0,0.8),
        0px 10px 20px rgba(59,130,246,0.4);
    letter-spacing: -1px;
}
.text-3d-secondary {
    text-shadow: 
        0px 1px 0px #059669, 
        0px 2px 0px #047857, 
        0px 3px 0px #064e3b,
        0px 5px 15px rgba(0,0,0,0.8);
}
</style>

<body class="relative min-h-screen container-fluid bg-slate-950 font-sans text-slate-300 selection:bg-blue-500/30">

    <!-- Immersive Animated Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/20 rounded-full mix-blend-screen filter blur-[120px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-emerald-600/20 rounded-full mix-blend-screen filter blur-[120px]" style="animation-duration: 10s; animation-direction: reverse;"></div>
        <img src="./assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-10" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
    </div>
    <div class="fixed inset-0 z-0 backdrop-blur-[2px] bg-slate-950/70"></div>

    <!-- Ensure content sits above fixed background -->
    <div class="relative z-10 flex flex-col min-h-screen">

        <?php require_once "inc/nav.php"; ?>

        <!-- Hero Section -->
        <section class="relative xl:px-20 lg:px-10 px-5 xl:py-32 py-16 flex justify-center items-center overflow-hidden min-h-[85vh]">
            <!-- Hero Inner Glowing Sphere border effect -->
            <div class="absolute inset-x-0 w-3/4 mx-auto top-10 h-[500px] bg-gradient-to-tr from-blue-500/10 to-emerald-500/10 rounded-full filter blur-3xl -z-10 mix-blend-screen animate-pulse"></div>
            
            <div class="text-center w-full lg:w-4/5 xl:w-3/4 relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-800/80 border border-white/10 backdrop-blur-md mb-8">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping absolute"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 relative"></span>
                    <span class="text-xs font-black uppercase tracking-widest text-emerald-300">New Era of Education</span>
                </div>

                <!-- Customized 3D Hero Typography -->
                <h1 class="text-6xl sm:text-7xl lg:text-8xl font-black mb-4">
                    <span class="block text-3d mb-2">TeachMate</span>
                    <span class="block text-3xl sm:text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200 text-3d-secondary">
                        Learning Management System
                    </span>
                </h1>
                
                <p class="xl:text-xl text-lg pt-6 text-slate-400 font-medium max-w-3xl mx-auto leading-relaxed">
                    Boost your skills with a revolutionary, immersive learning platform. We provide world-class tools and materials to empower students globally. Unlock your true potential today.
                </p>

                <div class="pt-12 flex flex-col sm:flex-row justify-center items-center gap-5">
                    <a href="./signup.php" class="w-full sm:w-auto px-8 py-4 rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-[0_0_20px_rgba(37,99,235,0.4)] hover:shadow-[0_0_30px_rgba(37,99,235,0.6)] hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden">
                        <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                        Get Started For Free
                    </a>
                </div>
            </div>
        </section>

        <!-- Features (Glassmorphism Cards) -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10 border-t border-white/5 bg-slate-900/30">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Awesome Features</h2>
                <p class="text-lg pt-4 text-slate-400 font-medium max-w-2xl mx-auto">Experience a fully integrated virtual classroom designed to maximize your learning curve.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center">
                <!-- Feature 1 -->
                <div class="group relative bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-3xl p-8 hover:-translate-y-2 hover:bg-slate-800/60 transition-all duration-300 overflow-hidden text-center">
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-blue-500 to-emerald-500 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                    <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-blue-500/20 to-blue-500/5 rounded-2xl border border-blue-500/20 flex items-center justify-center mb-6 shadow-[0_0_15px_rgba(59,130,246,0.15)] group-hover:shadow-[0_0_20px_rgba(59,130,246,0.3)] transition-all">
                        <i class="fas fa-graduation-cap text-3xl text-blue-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Scholarship Facility</h3>
                    <p class="text-sm font-medium text-slate-400">We provide exclusive scholarship opportunities to outstanding students for their future endeavors.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group relative bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-3xl p-8 hover:-translate-y-2 hover:bg-slate-800/60 transition-all duration-300 overflow-hidden text-center">
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                    <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-emerald-500/20 to-emerald-500/5 rounded-2xl border border-emerald-500/20 flex items-center justify-center mb-6 shadow-[0_0_15px_rgba(16,185,129,0.15)] group-hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all">
                        <i class="fa-solid fa-laptop-code text-3xl text-emerald-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Online Courses</h3>
                    <p class="text-sm font-medium text-slate-400">Access thousands of premium online courses remotely, with dedicated instructor support.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group relative bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-3xl p-8 hover:-translate-y-2 hover:bg-slate-800/60 transition-all duration-300 overflow-hidden text-center">
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                    <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-indigo-500/20 to-indigo-500/5 rounded-2xl border border-indigo-500/20 flex items-center justify-center mb-6 shadow-[0_0_15px_rgba(99,102,241,0.15)] group-hover:shadow-[0_0_20px_rgba(99,102,241,0.3)] transition-all">
                        <i class="fas fa-certificate text-3xl text-indigo-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Verified Certificates</h3>
                    <p class="text-sm font-medium text-slate-400">Earn globally recognized certifications to highlight your new skills on your professional resume.</p>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-16">
                
                <div class="lg:w-1/2 w-full relative">
                    <!-- Glass image frame -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-emerald-500 rounded-3xl transform rotate-3 scale-105 opacity-20 blur-xl"></div>
                    <div class="relative rounded-3xl overflow-hidden border border-white/10 bg-slate-800 p-2 shadow-2xl">
                        <img src="./assets/img/choose.jpg" class="w-full h-auto rounded-2xl opacity-90 hover:opacity-100 transition-opacity duration-500" alt="Why Choose Us" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';">
                    </div>
                </div>

                <div class="lg:w-1/2 w-full">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest mb-6">
                        Our Mission
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-white mb-6">Why Choose Us?</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-400 mb-6 font-medium">
                        Education underpins all social progress. Our aim is to harness technology to make all education and skills training available to anyone, anywhere for free. We believe that modern education, more than anything, has the power to break through boundaries and transform lives.
                    </p>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-400 mb-10 font-medium">
                        By integrating AI tools, real-time grading, and responsive tracking, we streamline the bridge between expert instructors and eager students worldwide.
                    </p>
                    <a href="./about.php" class="inline-flex items-center justify-center px-6 py-3 border border-white/20 rounded-xl text-lg font-bold text-white bg-slate-800/80 hover:bg-slate-700/80 backdrop-blur-md shadow-lg hover:-translate-y-1 transition-all duration-300">
                        Learn More <i class="fas fa-arrow-right ml-2 text-sm text-emerald-400"></i>
                    </a>
                </div>

            </div>
        </section>

        <!-- Our Expert Teachers -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10 border-t border-white/5 bg-slate-900/30">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Our Expert Teachers</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-emerald-500 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Teacher 1 -->
                <div class="group bg-slate-800/40 backdrop-blur-lg border border-white/10 rounded-3xl p-6 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.4)] transition-all duration-300 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=2563eb&color=fff&size=200" alt="John Doe" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-slate-700 shadow-xl group-hover:border-blue-500 transition-colors z-10 relative">
                    <h3 class="text-xl font-bold text-white relative z-10">John Doe</h3>
                    <p class="text-emerald-400 text-sm font-semibold mb-4 relative z-10">Digital Marketing</p>
                    <p class="text-sm text-slate-400 font-medium relative z-10">5+ years in SEO & social media marketing. Helps brands grow through online strategies.</p>
                </div>

                <!-- Teacher 2 -->
                <div class="group bg-slate-800/40 backdrop-blur-lg border border-white/10 rounded-3xl p-6 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.4)] transition-all duration-300 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="https://ui-avatars.com/api/?name=Sarah+Smith&background=059669&color=fff&size=200" alt="Sarah Smith" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-slate-700 shadow-xl group-hover:border-emerald-500 transition-colors z-10 relative">
                    <h3 class="text-xl font-bold text-white relative z-10">Sarah Smith</h3>
                    <p class="text-emerald-400 text-sm font-semibold mb-4 relative z-10">Game Development</p>
                    <p class="text-sm text-slate-400 font-medium relative z-10">8+ years of experience in Unity & Unreal Engine. Expert in creating interactive game worlds.</p>
                </div>

                <!-- Teacher 3 -->
                <div class="group bg-slate-800/40 backdrop-blur-lg border border-white/10 rounded-3xl p-6 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.4)] transition-all duration-300 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="https://ui-avatars.com/api/?name=Michael+Lee&background=7c3aed&color=fff&size=200" alt="Michael Lee" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-slate-700 shadow-xl group-hover:border-purple-500 transition-colors z-10 relative">
                    <h3 class="text-xl font-bold text-white relative z-10">Michael Lee</h3>
                    <p class="text-emerald-400 text-sm font-semibold mb-4 relative z-10">Graphic Designing</p>
                    <p class="text-sm text-slate-400 font-medium relative z-10">7+ years in creative design & branding. Pro in Photoshop, Illustrator & Figma.</p>
                </div>

                <!-- Teacher 4 -->
                <div class="group bg-slate-800/40 backdrop-blur-lg border border-white/10 rounded-3xl p-6 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.4)] transition-all duration-300 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-pink-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="https://ui-avatars.com/api/?name=Emily+Brown&background=db2777&color=fff&size=200" alt="Emily Brown" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-slate-700 shadow-xl group-hover:border-pink-500 transition-colors z-10 relative">
                    <h3 class="text-xl font-bold text-white relative z-10">Emily Brown</h3>
                    <p class="text-emerald-400 text-sm font-semibold mb-4 relative z-10">Frontend Dev</p>
                    <p class="text-sm text-slate-400 font-medium relative z-10">6+ years building responsive UIs. Specialist in React, Tailwind, and Vue.</p>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="xl:px-20 lg:px-10 px-5 py-20 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Student Testimonials</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-slate-800/30 backdrop-blur-md border border-white/5 rounded-3xl p-8 hover:bg-slate-800/50 hover:border-white/10 transition-all duration-300">
                    <div class="flex text-yellow-500 mb-4 text-sm gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-300 italic text-sm leading-relaxed mb-8">
                        "TeachMate Hub made me a confident frontend developer. The instructors are amazing and the content is remarkably practical and well-structured!"
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Sara+Ahmed&background=1e293b&color=cbd5e1" alt="Sara" class="w-12 h-12 rounded-full border border-slate-600">
                        <div>
                            <h4 class="font-bold text-white text-sm">Sara Ahmed</h4>
                            <p class="text-xs text-emerald-400 font-medium">Frontend Developer</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-slate-800/30 backdrop-blur-md border border-white/5 rounded-3xl p-8 hover:bg-slate-800/50 hover:border-white/10 transition-all duration-300">
                    <div class="flex text-yellow-500 mb-4 text-sm gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-300 italic text-sm leading-relaxed mb-8">
                        "The graphic designing course helped me build a strong portfolio. Highly recommend for creative learners who want a solid foundation!"
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Ali+Khan&background=1e293b&color=cbd5e1" alt="Ali" class="w-12 h-12 rounded-full border border-slate-600">
                        <div>
                            <h4 class="font-bold text-white text-sm">Ali Khan</h4>
                            <p class="text-xs text-emerald-400 font-medium">Graphic Designer</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-slate-800/30 backdrop-blur-md border border-white/5 rounded-3xl p-8 hover:bg-slate-800/50 hover:border-white/10 transition-all duration-300">
                    <div class="flex text-yellow-500 mb-4 text-sm gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="text-slate-300 italic text-sm leading-relaxed mb-8">
                        "With TeachMate's digital marketing course, I grew my freelance career significantly. Great mentorship and extremely fast instructor support!"
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Usman+Raza&background=1e293b&color=cbd5e1" alt="Usman" class="w-12 h-12 rounded-full border border-slate-600">
                        <div>
                            <h4 class="font-bold text-white text-sm">Usman Raza</h4>
                            <p class="text-xs text-emerald-400 font-medium">Digital Marketer</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php require_once "inc/footer.php"; ?>

    </div>
    
    <style>
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
    </style>
</body>
</html>
