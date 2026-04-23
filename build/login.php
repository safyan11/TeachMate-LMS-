<?php
error_reporting(0);
session_start();

require_once "inc/db.php";

require_once "inc/header.php";
// nav.php removed from here to prevent overlapping UI issues

$loginError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);
    if (empty($email) || empty($password) || empty($role)) {
        $loginError = "Please fill all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Check if account is approved
            if ($user['verify_status'] !== 'approved') {
                $loginError = "Your account is not approved yet.";
            }
            // Check password
            elseif (!password_verify($password, $user['password'])) {
                $loginError = "Invalid password.";
            }
            // Success
            else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                // Redirect based on role
                if ($role === 'admin') {
                    header("Location: admin/admindashboard.php");
                } elseif ($role === 'teacher') {
                    header("Location: teacher/teacherdashboard.php");
                } else {
                    header("Location: student/studentdashboard.php");
                }
                exit();
            }
        } else {
            $loginError = "No user found with provided credentials.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!-- Inject Tailwind CSS manually here because inc/header.php doesn't have it -->
<script src="https://cdn.tailwindcss.com"></script>

<body class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 font-sans">
    <!-- Immersive Background with Soft Gradients -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <!-- Animated glowing orbs -->
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/30 rounded-full mix-blend-screen filter blur-[120px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[120px]" style="animation-duration: 10s; animation-direction: reverse;"></div>
        
        <!-- Subtle image overlay -->
        <img src="./assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-20" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
    </div>

    <!-- Blur Overlay -->
    <div class="absolute inset-0 z-0 backdrop-blur-[2px] bg-slate-950/60"></div>

    <style>
        /* Smooth Entrance Animation */
        @keyframes slideInFade {
            0% {
                opacity: 0;
                transform: scale(0.95) translateY(20px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-card {
            animation: slideInFade 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        /* Focus Glow for inputs */
        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.5);
        }
    </style>

    <section class="relative z-10 w-full px-4 sm:px-6 lg:px-8 py-12 flex justify-center items-center">
        <!-- Glassmorphism Login Card -->
        <div class="w-full max-w-lg rounded-3xl shadow-2xl border border-white/10 bg-slate-900/60 backdrop-blur-2xl animate-card p-8 sm:p-12 opacity-0">
            
            <form action="" method="POST">
                <!-- Header -->
                <div class="text-center mb-10 flex flex-col items-center">
                    <img src="./assets/img/teachmate_logo.png" alt="TeachMate" class="h-16 w-16 mb-5 rounded-2xl shadow-lg border border-white/10 bg-slate-800/80 p-1">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Welcome Back</h2>
                    <p class="text-sm font-medium text-slate-400 mt-2">Don't have an account? <a href="signup.php" class="text-blue-400 font-bold hover:text-blue-300 transition-colors underline-offset-4 hover:underline">Create one</a></p>
                </div>
                
                <?php if (!empty($loginError)): ?>
                    <div class="bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center gap-3 mb-6 animate-pulse">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                        <?= $loginError ?>
                    </div>
                <?php endif; ?>
                
                <div class="space-y-6">
                    <!-- Email Field -->
                    <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-sm font-bold text-slate-300 px-4 pt-3">Email Address</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <input name="email" type="email" class="w-full pl-11 pr-5 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="user@example.com" required>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div x-data="{ show: false }" class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-sm font-bold text-slate-300 px-4 pt-3">Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input name="password" :type="show ? 'text' : 'password'" class="w-full pl-11 pr-12 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="••••••••" required>
                            <button type="button" @click="show = !show" class="absolute right-4 text-slate-400 hover:text-white transition-colors focus:outline-none">
                                <i :class="show ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Role Dropdown -->
                    <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-sm font-bold text-slate-300 px-4 pt-3">User Role</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <select name="role" class="w-full pl-11 pr-10 pb-3 pt-1 bg-transparent text-white focus:outline-none cursor-pointer appearance-none font-medium [&>option]:bg-slate-900" required>
                                <option value="" disabled selected>Select your role</option>
                                <option value="admin">Admin</option>
                                <option value="teacher">Teacher</option>
                                <option value="student">Student</option>
                            </select>
                            <div class="absolute right-4 text-slate-400 pointer-events-none group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Forgot Password -->
                    <div class="flex justify-end pt-1">
                        <a href="auth/forgot_password.php" class="text-sm font-semibold text-blue-400 hover:text-blue-300 transition-colors underline-offset-4 hover:underline">Forgot Password?</a>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-[2px] active:translate-y-0 transition-all duration-300 overflow-hidden">
                            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                            <div class="flex items-center gap-3 relative z-10">
                                <span>Sign In</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1.5 transition-transform duration-300"></i>
                            </div>
                        </button>
                    </div>
                    <style>
                        @keyframes shimmer {
                            100% { transform: translateX(100%); }
                        }
                    </style>
                </div>
            </form>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
