<nav class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-white/10 transition-all duration-300">
  <div class="w-full xl:px-20 lg:px-10 px-5 mx-auto">
    <div class="flex justify-between items-center h-20">
      
      <!-- Brand / Logo -->
      <a href="./index.php" class="flex items-center space-x-3 group">
        <div class="relative p-0.5 rounded-xl bg-gradient-to-tr from-blue-600 to-emerald-500 rounded-[10px] shadow-[0_0_15px_rgba(59,130,246,0.5)] group-hover:shadow-[0_0_25px_rgba(59,130,246,0.7)] transition-all">
          <img src="./assets/img/teachmate_logo.png" alt="TeachMate Logo" class="h-10 w-auto rounded-[8px] bg-slate-900">
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400 drop-shadow-sm">TeachMate</h1>
      </a>

      <!-- Desktop Menu -->
      <div class="hidden lg:flex items-center space-x-1">
        <a href="./index.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Home</a>
        <a href="./courses.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Courses</a>
        <a href="./about.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors">About Us</a>
        <a href="./contact.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Contact Us</a>
        
        <div class="ml-4 pl-4 border-l border-white/10 flex items-center space-x-3">
            <a href="./login.php" class="inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-0.5 transition-all">
                Sign In
            </a>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <div class="lg:hidden flex items-center">
        <button id="mobile-nav-btn" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors focus:outline-none">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Menu Dropdown -->
  <div id="mobile-nav-menu" class="lg:hidden hidden border-t border-white/10 bg-slate-900/95 backdrop-blur-xl transition-all">
    <div class="px-4 pt-2 pb-6 space-y-1">
      <a href="./index.php" class="block px-4 py-3 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-white/5">Home</a>
      <a href="./courses.php" class="block px-4 py-3 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-white/5">Courses</a>
      <a href="./about.php" class="block px-4 py-3 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-white/5">About Us</a>
      <a href="./contact.php" class="block px-4 py-3 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-white/5">Contact Us</a>
      <div class="pt-4 mt-2 border-t border-white/10">
        <a href="./login.php" class="block w-full text-center px-4 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-[0_0_15px_rgba(37,99,235,0.4)]">
            Sign In
        </a>
      </div>
    </div>
  </div>
</nav>

<script>
  document.getElementById('mobile-nav-btn')?.addEventListener('click', () => {
    const mobileMenu = document.getElementById('mobile-nav-menu');
    const icon = document.querySelector('#mobile-nav-btn i');
    if (mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.remove('hidden');
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    } else {
        mobileMenu.classList.add('hidden');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }
  });
</script>
