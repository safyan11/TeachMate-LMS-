// navbar js 


  // Mobile Menu Toggle with icon change
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const menuIcon = menuBtn.querySelector('i');

  menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    
    if (mobileMenu.classList.contains('hidden')) {
      menuIcon.classList.remove('fa-times');
      menuIcon.classList.add('fa-bars');
    } else {
      menuIcon.classList.remove('fa-bars');
      menuIcon.classList.add('fa-times');
    }
  });

  // Dropdown Toggle for Mobile only
  function toggleDropdown(menuId, iconId) {
    const dropdowns = [
      {menu: 'courses-mobile', icon: 'courses-icon-mobile'},
      {menu: 'login-mobile', icon: 'login-icon-mobile'},
    ];

    dropdowns.forEach(item => {
      const menu = document.getElementById(item.menu);
      const icon = document.getElementById(item.icon);

      if (item.menu === menuId) {
        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
      } else {
        menu.classList.add('hidden');
        icon.classList.remove('rotate-180');
      }
    });
  }
