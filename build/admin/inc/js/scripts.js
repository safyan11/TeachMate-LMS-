// <!-- drop down profile log out  -->

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



//   <!-- sidebar menu  -->

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


// admin dashboard 
     
    // Simulated live data: "Active Users" line + "Submissions" bar
    function generateInitial(points = 12) {
      const now = new Date();
      const labels = [];
      const submissions = [];
      const activeUsers = [];
      for (let i = points - 1; i >= 0; i--) {
        const d = new Date(now.getTime() - i * 60000);
        labels.push(d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
        submissions.push(Math.round(10 + Math.random() * 60));
        activeUsers.push(Math.round(80 + Math.random() * 80));
      }
      return { labels, submissions, activeUsers };
    }

    const ctx = document.getElementById('combinedChart').getContext('2d');
    let { labels, submissions, activeUsers } = generateInitial();

    const combinedChart = new Chart(ctx, {
      data: {
        labels,
        datasets: [
          {
            type: 'bar',
            label: 'Submissions',
            data: submissions,
            borderRadius: 6,
            barThickness: 14,
            yAxisID: 'y',
          },
          {
            type: 'line',
            label: 'Active Users',
            data: activeUsers,
            tension: 0.35,
            borderWidth: 2,
            pointRadius: 3,
            fill: false,
            yAxisID: 'y',
          },
        ],
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'top' },
          tooltip: { enabled: true },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { maxTicksLimit: 5 }
          },
          x: {
            ticks: { autoSkip: true, maxRotation: 0 }
          }
        },
      },
    });

    // Live update every 5 seconds (simulated)
    setInterval(() => {
      const label = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      const newSubmissions = Math.round(10 + Math.random() * 60);
      const newActiveUsers = Math.round(80 + Math.random() * 80);

      combinedChart.data.labels.push(label);
      combinedChart.data.datasets[0].data.push(newSubmissions);
      combinedChart.data.datasets[1].data.push(newActiveUsers);

      if (combinedChart.data.labels.length > 12) {
        combinedChart.data.labels.shift();
        combinedChart.data.datasets.forEach(ds => ds.data.shift());
      }
      combinedChart.update();
    }, 5000);
