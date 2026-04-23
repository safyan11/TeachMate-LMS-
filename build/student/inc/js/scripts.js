//  drop down profile log out  

  const userMenuBtn = document.getElementById('user-menu-btn');
  const userMenu = document.getElementById('user-menu');
  const chevron = document.getElementById('chevron');

  function toggleUserMenu() {
    const open = !userMenu.classList.contains('hidden');
    if (open) {
      userMenu.classList.add('hidden');
      chevron.style.transform = 'rotate(0deg)';
      userMenuBtn.setAttribute('aria-expanded', 'false');
    } else {
      userMenu.classList.remove('hidden');
      chevron.style.transform = 'rotate(180deg)';
      userMenuBtn.setAttribute('aria-expanded', 'true');
    }
  }

  // close when clicking outside
  document.addEventListener('click', (e) => {
    if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
      if (!userMenu.classList.contains('hidden')) {
        userMenu.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
        userMenuBtn.setAttribute('aria-expanded', 'false');
      }
    }
  });

  function handleLogout() {
    // replace with real logout logic
    alert('Logging out...');
  }



   // Upload button logic
    document.querySelectorAll('.upload-btn').forEach((btn, index) => {
      const fileInput = btn.parentElement.querySelector('.file-input');

      if (fileInput) {
        btn.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (event) => {
          const file = event.target.files[0];
          if (file) {
            alert(`✅ Uploaded: ${file.name}`);
            // You can send it to backend here using fetch/AJAX
            // const formData = new FormData();
            // formData.append("assignment", file);
          }
        });
      }
    });


    // sidebar js 


    
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menu-btn');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }

    menuBtn.addEventListener('click', () => {
      if (sidebar.classList.contains('-translate-x-full')) {
        openSidebar();
      } else {
        closeSidebar();
      }
    });

    overlay.addEventListener('click', closeSidebar);



//   faqs js  

    // Basic accordion logic (only one open at a time)
    document.querySelectorAll('[data-accordion-target]').forEach(btn => {
      btn.addEventListener('click', () => {
        const isOpen = btn.getAttribute('aria-expanded') === 'true';
        // close all
        document.querySelectorAll('[data-accordion-target]').forEach(b => {
          b.setAttribute('aria-expanded', 'false');
          b.querySelector('[aria-hidden]').textContent = '+';
          b.nextElementSibling.classList.add('hidden');
        });
        if (!isOpen) {
          btn.setAttribute('aria-expanded', 'true');
          btn.querySelector('[aria-hidden]').textContent = '−';
          btn.nextElementSibling.classList.remove('hidden');
        }
      });
    });
