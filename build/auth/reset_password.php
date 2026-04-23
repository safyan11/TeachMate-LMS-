<?php
require_once dirname(__DIR__) . '/inc/db.php';
session_start();
// Check if OTP was verified
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}
$email = $_SESSION['reset_email'];
$error = $success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Please fill all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed_password, $email);
        
        if ($update->execute()) {
            // Clear session
            unset($_SESSION['otp_verified']);
            unset($_SESSION['reset_email']);
            
            // Delete OTP record
            $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete->bind_param("s", $email);
            $delete->execute();
            
            $success = "Password has been reset successfully. <a href='../login.php'>Login</a>";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
    <!-- Alpine JS for password toggle -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
</head>
<body class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 font-sans">
    <!-- Immersive Background with Soft Gradients -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/30 rounded-full mix-blend-screen filter blur-[120px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[120px]" style="animation-duration: 10s; animation-direction: reverse;"></div>
        <img src="../assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-20" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
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

    <section class="relative z-10 w-full min-h-screen px-4 py-10 flex items-center justify-center">
        <!-- Glassmorphism Card -->
        <div class="w-full max-w-md mx-auto rounded-3xl shadow-2xl border border-white/10 bg-slate-900/60 backdrop-blur-2xl animate-card p-8 sm:p-10 opacity-0 relative z-10 overflow-hidden">
            
            <div class="text-center mb-8 flex flex-col items-center">
                <div class="h-16 w-16 mb-4 rounded-2xl shadow-lg border border-white/10 bg-slate-800/80 p-3 flex items-center justify-center">
                    <i class="fa-solid fa-lock-open text-2xl text-blue-400"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Create New Password</h2>
            </div>
            
            <?php if($error): ?>
                <div class='bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6 animate-pulse'>
                    <i class='fa-solid fa-triangle-exclamation'></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class='bg-emerald-500/10 border border-emerald-500/40 text-emerald-400 p-6 rounded-xl text-md font-bold flex flex-col items-center justify-center gap-3 mb-4 text-center'>
                    <i class='fa-solid fa-circle-check text-4xl mb-2 text-emerald-400'></i>
                    <p><?php echo $success; // Expecting 'Password has been reset successfully. <a href='../login.php'>Login</a>' ?></p>
                </div>
            <?php else: ?>
                
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="space-y-5">
                    <!-- New Password Field -->
                    <div x-data="{ show: false }" class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">New Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input name="password" :type="show ? 'text' : 'password'" required class="w-full pl-11 pr-10 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3 text-slate-400 hover:text-white transition-colors focus:outline-none">
                                <i :class="show ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Confirm Password Field -->
                    <div x-data="{ show: false }" class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">Confirm Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-shield-check"></i>
                            </div>
                            <input name="confirm_password" :type="show ? 'text' : 'password'" required class="w-full pl-11 pr-10 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3 text-slate-400 hover:text-white transition-colors focus:outline-none">
                                <i :class="show ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-[2px] active:translate-y-0 transition-all duration-300 overflow-hidden">
                            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                            <div class="flex items-center gap-3 relative z-10">
                                <span>Reset Password</span>
                                <i class="fa-solid fa-check group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
            
            <?php endif; ?>
            
            <div class="text-center mt-6">
                <a href="../login.php" class="text-slate-400 hover:text-white text-sm font-medium transition-colors">Back to Login</a>
            </div>
        </div>
    </section>
</body>
</html>