<?php
require_once dirname(__DIR__) . '/inc/db.php';
// Set timezone to match your location
date_default_timezone_set('Asia/Karachi'); // Change this to your timezone
// Set MySQL timezone to match PHP timezone
$conn->query("SET time_zone = '+05:00'"); // Change this to your timezone offset
$email = isset($_GET['email']) ? $_GET['email'] : '';
$error = $success = '';

// Fetch remaining time for the countdown
$remaining_seconds = 0;
if (!empty($email)) {
    $stmt = $conn->prepare("SELECT expires_at FROM password_resets WHERE email = ? ORDER BY expires_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $expiry_time = strtotime($row['expires_at']);
        $current_time = time();
        $remaining_seconds = max(0, $expiry_time - $current_time);
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);
    $email = trim($_POST['email']);
    
    if (empty($otp)) {
        $error = "Please enter the OTP";
    } else {
        // Get current time in the same format as stored in database
        $current_time = date('Y-m-d H:i:s');
        
        // Check if OTP exists and is not expired
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE email = ? AND token = ? AND expires_at > ?");
        $stmt->bind_param("sss", $email, $otp, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // OTP is valid, set session and redirect to reset password
            session_start();
            $_SESSION['otp_verified'] = true;
            $_SESSION['reset_email'] = $email;
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Invalid or expired OTP";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
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
                    <i class="fa-solid fa-shield-halved text-2xl text-blue-400"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Verify Identity</h2>
                <div id="countdown-container" class="mt-3 flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold animate-pulse">
                    <i class="fa-solid fa-clock"></i>
                    <span>Code expires in: <span id="timer">00:00</span></span>
                </div>
            </div>
            
            <?php if($error): ?>
                <div class='bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6 animate-pulse'>
                    <i class='fa-solid fa-triangle-exclamation'></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            

            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="space-y-5">
                    <!-- Email Field -->
                    <div class="input-glow rounded-xl border border-white/10 bg-slate-800/20 transition-all duration-300 relative overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-400 px-4 pt-2">Email Address</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-500">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly class="w-full pl-11 pr-5 pb-3 pt-1 bg-transparent text-slate-400 focus:outline-none font-medium cursor-not-allowed">
                        </div>
                    </div>
                    
                    <!-- OTP Field -->
                    <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">6-Digit Code</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-hashtag"></i>
                            </div>
                            <input type="text" id="otp" name="otp" required class="w-full pl-11 pr-5 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-600 font-mono text-center tracking-[0.5em] text-lg font-bold" placeholder="XXXXXX">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-[2px] active:translate-y-0 transition-all duration-300 overflow-hidden">
                            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                            <div class="flex items-center gap-3 relative z-10">
                                <span>Verify Code</span>
                                <i class="fa-solid fa-check-circle group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="text-center mt-6">
                <a href="forgot_password.php" class="text-blue-400 hover:text-blue-300 text-sm font-bold transition-all underline-offset-4 hover:underline">
                    <span class="mr-1">&larr;</span> Change Email
                </a>
            </div>
        </div>
    </section>
    <script>
        let timeLeft = <?php echo $remaining_seconds; ?>;
        const timerDisplay = document.getElementById('timer');
        const countdownContainer = document.getElementById('countdown-container');

        function updateTimer() {
            if (timeLeft <= 0) {
                timerDisplay.textContent = "00:00";
                countdownContainer.classList.remove('text-blue-400', 'bg-blue-500/10', 'border-blue-500/20');
                countdownContainer.classList.add('text-red-400', 'bg-red-500/10', 'border-red-500/20');
                countdownContainer.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> <span>Code Expired!</span>';
                return;
            }

            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }

        if (timeLeft > 0) {
            updateTimer();
        } else {
            updateTimer(); // Trigger "Expired" state immediately if time is already up
        }
    </script>
</body>
</html>