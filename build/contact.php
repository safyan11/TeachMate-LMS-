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

    <style>
        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.5);
        }
    </style>

    <div class="relative z-10 flex flex-col min-h-screen">
        <?php require_once "inc/nav.php"; ?>

        <!-- Contact Section -->
        <section class="xl:px-20 lg:px-10 px-5 py-24 flex-grow relative z-10 flex items-center justify-center">
            
            <div class="w-full max-w-6xl mx-auto flex flex-col lg:flex-row bg-slate-900/60 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                
                <!-- Left: Contact Details -->
                <div class="lg:w-5/12 w-full p-10 sm:p-14 bg-gradient-to-br from-blue-900/40 to-slate-900/80 relative overflow-hidden">
                    <!-- Decorative blur -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 blur-[50px] rounded-full"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-emerald-500/20 blur-[60px] rounded-full"></div>
                    
                    <div class="relative z-10">
                        <h1 class="text-4xl font-extrabold text-white mb-2">Get in Touch</h1>
                        <p class="text-sm font-medium text-blue-300 mb-10">We'd love to hear from you. Drop us a message!</p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start gap-5">
                                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex flex-shrink-0 items-center justify-center text-blue-400">
                                    <i class="fa-solid fa-location-dot text-xl"></i>
                                </div>
                                <div class="pt-1">
                                    <h4 class="text-white font-bold mb-1">Our Location</h4>
                                    <p class="text-sm font-medium text-slate-400">Lahore, Pakistan<br>Educational Hub Plaza, Main Street.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-5">
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex flex-shrink-0 items-center justify-center text-emerald-400">
                                    <i class="fa-solid fa-envelope text-xl"></i>
                                </div>
                                <div class="pt-1">
                                    <h4 class="text-white font-bold mb-1">Email Address</h4>
                                    <p class="text-sm font-medium text-slate-400">TeachMate@gmail.com<br>support@teachmate-lms.com</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-5">
                                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex flex-shrink-0 items-center justify-center text-indigo-400">
                                    <i class="fa-solid fa-phone text-xl"></i>
                                </div>
                                <div class="pt-1">
                                    <h4 class="text-white font-bold mb-1">Phone Number</h4>
                                    <p class="text-sm font-medium text-slate-400">+92 56 292 9978<br>Toll-Free: 0800-TEACH</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-5">
                                <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex flex-shrink-0 items-center justify-center text-purple-400">
                                    <i class="fa-solid fa-clock text-xl"></i>
                                </div>
                                <div class="pt-1">
                                    <h4 class="text-white font-bold mb-1">Working Hours</h4>
                                    <p class="text-sm font-medium text-slate-400">Mon - Fri: 8:00 AM to 5:00 PM<br>Weekends: Closed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="lg:w-7/12 w-full p-10 sm:p-14">
                    <h2 class="text-2xl font-bold text-white mb-8 border-b border-white/10 pb-4">Send Us a Message</h2>
                    
                    <form action="form.php" method="POST" class="space-y-6">
                        <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                            <label class="block mb-1 text-xs font-bold text-slate-400 px-4 pt-3 uppercase tracking-wider">Your Name</label>
                            <input type="text" name="name" required class="w-full px-4 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-600 font-medium" placeholder="John Doe">
                        </div>

                        <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                            <label class="block mb-1 text-xs font-bold text-slate-400 px-4 pt-3 uppercase tracking-wider">Email Address</label>
                            <input type="email" name="email" required class="w-full px-4 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-600 font-medium" placeholder="john@example.com">
                        </div>

                        <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                            <label class="block mb-1 text-xs font-bold text-slate-400 px-4 pt-3 uppercase tracking-wider">Subject</label>
                            <input type="text" name="subject" required class="w-full px-4 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-600 font-medium" placeholder="How can we help?">
                        </div>

                        <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                            <label class="block mb-1 text-xs font-bold text-slate-400 px-4 pt-3 uppercase tracking-wider">Your Message</label>
                            <textarea name="message" rows="5" required class="w-full px-4 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-600 font-medium resize-none" placeholder="Write your message here..."></textarea>
                        </div>

                        <button type="submit" class="group relative w-full flex justify-center items-center gap-3 py-4 border border-transparent rounded-xl text-base font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
                            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                            <span class="relative z-10">Send Message</span>
                            <i class="fa-solid fa-paper-plane relative z-10 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                    
                    <style>
                        @keyframes shimmer {
                            100% { transform: translateX(100%); }
                        }
                    </style>
                </div>
                
            </div>
        </section>

        <?php require_once "inc/footer.php"; ?>
    </div>
</body>
</html>
