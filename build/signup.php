<?php include("inc/header.php"); ?>
<!-- Inject Tailwind CSS manually here because inc/header.php doesn't have it -->
<script src="https://cdn.tailwindcss.com"></script>

<body class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 font-sans">
    <!-- Immersive Background with Soft Gradients -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/30 rounded-full mix-blend-screen filter blur-[120px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[120px]" style="animation-duration: 10s; animation-direction: reverse;"></div>
        <img src="./assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-20" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
    </div>

    <!-- Blur Overlay -->
    <div class="absolute inset-0 z-0 backdrop-blur-[2px] bg-slate-950/60"></div>

    <style>
        @keyframes slideInFade {
            0% { opacity: 0; transform: scale(0.95) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-card { animation: slideInFade 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.5);
        }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
    </style>

<section class="relative z-10 w-full px-4 sm:px-6 lg:px-8 py-10 flex justify-center items-center">
    <!-- Glassmorphism Signup Card -->
    <div class="w-full max-w-lg rounded-3xl shadow-2xl border border-white/10 bg-slate-900/60 backdrop-blur-2xl animate-card p-8 sm:p-10 opacity-0 relative z-10 overflow-hidden">
        
        <form action="" method="POST">
            <!-- Logo Header -->
            <div class="text-center mb-8 flex flex-col items-center">
                <img src="./assets/img/teachmate_logo.png" alt="TeachMate" class="h-16 w-16 mb-4 rounded-2xl shadow-lg border border-white/10 bg-slate-800/80 p-1">
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Create Account</h2>
                <p class="text-sm font-medium text-slate-400 mt-1">Already have an account? <a href="./login.php" class="text-blue-400 font-bold hover:text-blue-300 transition-colors underline-offset-4 hover:underline drop-shadow-md">Login here</a></p>
            </div>
                    
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = trim($_POST["name"]);
                $email = trim($_POST["email"]);
                $password = trim($_POST["password"]);
                $confirm_password = trim($_POST["confirm_password"]);
                
                if ($password !== $confirm_password) {
                    echo "<div class='bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6 animate-pulse'><i class='fa-solid fa-triangle-exclamation'></i> Passwords do not match!</div>";
                } else {
                    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $check_email->bind_param("s", $email);
                    $check_email->execute();
                    $result = $check_email->get_result();
                    
                    if ($result->num_rows > 0) {
                        echo "<div class='bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6 animate-pulse'><i class='fa-solid fa-circle-xmark'></i> Email already registered.</div>";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (name, email, password, role, verify_status) VALUES (?, ?, ?, 'student', 'approved')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $name, $email, $hashed_password);
                        
                        if ($stmt->execute()) {
                            echo "<div class='bg-emerald-500/10 border border-emerald-500/40 text-emerald-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6'><i class='fa-solid fa-circle-check'></i> Account created! <a href='./login.php' class='underline hover:text-emerald-300'>Log in here</a></div>";
                        } else {
                            echo "<div class='bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6 animate-pulse'><i class='fa-solid fa-circle-xmark'></i> Error creating account. Please try again.</div>";
                        }
                        $stmt->close();
                    }
                    $check_email->close();
                }
            }
            ?>
                    
            <div class="space-y-5">
                <!-- Full Name Field -->
                <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                    <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">Full Name</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input name="name" required class="w-full pl-11 pr-5 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="John Doe" type="text">
                    </div>
                </div>

                <!-- Email Field -->
                <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                    <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">Email Address</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input name="email" required type="email" class="w-full pl-11 pr-5 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="john@example.com">
                    </div>
                </div>

                <!-- Password Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div x-data="{ show: false }" class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input name="password" required :type="show ? 'text' : 'password'" class="w-full pl-11 pr-10 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3 text-slate-400 hover:text-white transition-colors focus:outline-none">
                                <i :class="show ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div x-data="{ show: false }" class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">Confirm</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-shield-check"></i>
                            </div>
                            <input name="confirm_password" required :type="show ? 'text' : 'password'" class="w-full pl-11 pr-10 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3 text-slate-400 hover:text-white transition-colors focus:outline-none">
                                <i :class="show ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-[2px] active:translate-y-0 transition-all duration-300 overflow-hidden">
                        <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                        <div class="flex items-center gap-3 relative z-10">
                            <span>Create Account</span>
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1.5 transition-transform duration-300"></i>
                        </div>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>