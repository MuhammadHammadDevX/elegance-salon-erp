/* ========================================================
   ELEGANCE SALON — Unified Dashboard Engine
   admin.php + reception.php
   ======================================================== */

(function () {
  'use strict';

  /* ─── Default Salon State ─── */
  const DEFAULT_STATE = {
    users: [
      { id: 1, name: 'Saad', email: 'saad@elegance.com', phone: '03001234567', role: 'admin', joined: '2024-01-15' },
      { id: 2, name: 'Ayesha Khan', email: 'ayesha@elegance.com', phone: '03111234567', role: 'receptionist', joined: '2024-03-20' },
      { id: 3, name: 'Hamza Farooq', email: 'hamza@gmail.com', phone: '03211234567', role: 'client', joined: '2024-06-10' },
      { id: 4, name: 'Bilal Ahmed', email: 'bilal@gmail.com', phone: '03331234567', role: 'client', joined: '2024-07-05' },
      { id: 5, name: 'Omar Malik', email: 'omar@gmail.com', phone: '03451234567', role: 'client', joined: '2024-08-18' }
    ],
    services: [
      { id: 1, name: 'Total Grooming', category: 'Grooming', price: 5000, duration: '120 min', bookings: 87 },
      { id: 2, name: 'Signature Cut & Beard', category: 'Hair', price: 3500, duration: '90 min', bookings: 142 },
      { id: 3, name: 'Beard Design', category: 'Beard', price: 2000, duration: '45 min', bookings: 98 },
      { id: 4, name: 'Traditional Shave', category: 'Shave', price: 1800, duration: '30 min', bookings: 63 },
      { id: 5, name: 'Deep Clean Facial', category: 'Facial', price: 3000, duration: '60 min', bookings: 54 }
    ],
    stylists: [
      { id: 1, name: 'Zachary Nickell', specialization: 'Master Barber & Beard Stylist', email: 'zachary@elegance.pk', phone: '03221234567', commission: 20, schedule: 'Tue-Sun 11AM-9PM', status: 'Active' },
      { id: 2, name: 'Ahmed Al-Tawil', specialization: 'Fade & Razor Sculptor', email: 'ahmed@elegance.pk', phone: '03229988771', commission: 18, schedule: 'Tue-Sun 11AM-9PM', status: 'Active' },
      { id: 3, name: 'Marcus Vance', specialization: 'Skin Treatments & Grooming', email: 'marcus@elegance.pk', phone: '03334455667', commission: 18, schedule: 'Tue-Sun 12PM-10PM', status: 'Active' },
      { id: 4, name: 'Liam O\'Connor', specialization: 'Classic Shave & Haircut', email: 'liam@elegance.pk', phone: '03445566778', commission: 15, schedule: 'Wed-Mon 11AM-9PM', status: 'Active' }
    ],
    stock: [
      { code: 'HS-001', name: 'Pomade Premium', category: 'Hair Styling', stock: 12, min: 5, cost: 850, supplier: 'HairCo Pakistan' },
      { code: 'HS-002', name: 'Sea Salt Spray', category: 'Hair Styling', stock: 8, min: 5, cost: 620, supplier: 'HairCo Pakistan' },
      { code: 'BC-001', name: 'Beard Oil (Sandalwood)', category: 'Beard Care', stock: 4, min: 5, cost: 480, supplier: 'GroomBox' },
      { code: 'BC-002', name: 'Beard Balm', category: 'Beard Care', stock: 15, min: 5, cost: 550, supplier: 'GroomBox' },
      { code: 'FS-001', name: 'Charcoal Peel Mask', category: 'Facial & Skin', stock: 2, min: 5, cost: 380, supplier: 'SkinGlow Labs' },
      { code: 'FS-002', name: 'Argan Oil Serum', category: 'Facial & Skin', stock: 3, min: 5, cost: 720, supplier: 'SkinGlow Labs' },
      { code: 'BS-001', name: 'Shaving Cream (Menthol)', category: 'Barber Supplies', stock: 20, min: 8, cost: 290, supplier: 'BarberMart' },
      { code: 'SH-001', name: 'Aftershave Balm', category: 'Shave Supplies', stock: 10, min: 5, cost: 340, supplier: 'BarberMart' },
      { code: 'HC-001', name: 'Hair Conditioner Pro', category: 'Hair Care', stock: 6, min: 5, cost: 410, supplier: 'HairCo Pakistan' }
    ],
    appointments: [
      { id: 1001, client: 'Hamza Farooq', phone: '03211234567', email: 'hamza@gmail.com', service: 'Total Grooming', stylist: 'Zachary Nickell', date: '2026-08-19', time: '10:00 AM', amount: 5000, payment: 'Cash', status: 'Completed', chair: 1 },
      { id: 1002, client: 'Bilal Ahmed', phone: '03331234567', email: 'bilal@gmail.com', service: 'Signature Cut & Beard', stylist: 'Marcus Vance', date: '2026-08-19', time: '11:30 AM', amount: 3500, payment: 'JazzCash', status: 'In Chair', chair: 2 },
      { id: 1003, client: 'Omar Malik', phone: '03451234567', email: 'omar@gmail.com', service: 'Beard Design', stylist: 'Ahmed Al-Tawil', date: '2026-08-19', time: '02:00 PM', amount: 2000, payment: 'Cash', status: 'Confirmed', chair: 0 },
      { id: 1004, client: 'Faisal Naveed', phone: '03001112233', email: 'faisal@yahoo.com', service: 'Traditional Shave', stylist: 'Liam O\'Connor', date: '2026-08-19', time: '04:30 PM', amount: 1800, payment: 'Card', status: 'Confirmed', chair: 0 },
      { id: 1005, client: 'Saifullah Khan', phone: '03121112233', email: 'saif@gmail.com', service: 'Deep Clean Facial', stylist: 'Zachary Nickell', date: '2026-08-19', time: '07:00 PM', amount: 3000, payment: 'EasyPaisa', status: 'Confirmed', chair: 0 },
      { id: 1006, client: 'Waleed Akram', phone: '03331112233', email: 'waleed@gmail.com', service: 'Signature Cut & Beard', stylist: 'Marcus Vance', date: '2026-08-18', time: '02:00 PM', amount: 3500, payment: 'Cash', status: 'Completed', chair: 0 },
      { id: 1007, client: 'Taimoor Shah', phone: '03441112233', email: 'taimoor@gmail.com', service: 'Total Grooming', stylist: 'Ahmed Al-Tawil', date: '2026-08-18', time: '10:00 AM', amount: 5000, payment: 'Card', status: 'Completed', chair: 0 },
      { id: 1008, client: 'Hamza Farooq', phone: '03211234567', email: 'hamza@gmail.com', service: 'Beard Design', stylist: 'Zachary Nickell', date: '2026-08-17', time: '11:30 AM', amount: 2000, payment: 'Cash', status: 'Completed', chair: 0 }
    ],
    feedbacks: [
      { id: 1, name: 'Hamza Farooq', type: 'review', rating: 5, text: 'Best salon experience in Karachi! The Total Grooming package is worth every rupee. Zachary is a true artist.', date: '2026-08-15', avatar: 'HF' },
      { id: 2, name: 'Bilal Ahmed', type: 'review', rating: 5, text: 'Marcus gave me the best haircut I\'ve ever had. The ambiance is top-notch, feels like a luxury lounge.', date: '2026-08-14', avatar: 'BA' },
      { id: 3, name: 'Omar Malik', type: 'message', subject: 'Group Booking Inquiry', text: 'Hi, I want to book for 5 guys on August 25th for the Total Grooming package. Do you offer group discounts?', date: '2026-08-16', avatar: 'OM' },
      { id: 4, name: 'Saifullah Khan', type: 'review', rating: 4, text: 'Great facial service, very relaxing. Only suggestion: maybe add aromatherapy options. Will definitely come back.', date: '2026-08-13', avatar: 'SK' },
      { id: 5, name: 'Faisal Naveed', type: 'message', subject: 'Product Availability', text: 'Do you sell the Sandalwood Beard Oil separately? I loved it during my shave and want to buy a bottle.', date: '2026-08-17', avatar: 'FN' },
      { id: 6, name: 'Waleed Akram', type: 'review', rating: 5, text: 'The whole team is incredible. Ahmed Al-Tawil transformed my beard completely. Premium experience all around.', date: '2026-08-12', avatar: 'WA' }
    ],
    clients: [
      { id: 1, name: 'Hamza Farooq', phone: '03211234567', visits: 12, spent: 52000, preferences: 'Hot towel, espresso' },
      { id: 2, name: 'Bilal Ahmed', phone: '03331234567', visits: 8, spent: 31500, preferences: 'Fade cut, no product' },
      { id: 3, name: 'Omar Malik', phone: '03451234567', visits: 5, spent: 15800, preferences: 'Sandalwood products' },
      { id: 4, name: 'Faisal Naveed', phone: '03001112233', visits: 3, spent: 7200, preferences: 'Sensitive skin' },
      { id: 5, name: 'Saifullah Khan', phone: '03121112233', visits: 6, spent: 22000, preferences: 'Fragrance-free' },
      { id: 6, name: 'Waleed Akram', phone: '03331112233', visits: 10, spent: 41000, preferences: 'Premium oils only' },
      { id: 7, name: 'Taimoor Shah', phone: '03441112233', visits: 4, spent: 18500, preferences: 'Morning slots' }
    ],
    chairs: [
      { id: 1, name: 'Chair 1', status: 'available' },
      { id: 2, name: 'Chair 2', status: 'available' },
      { id: 3, name: 'Chair 3', status: 'available' },
      { id: 4, name: 'Chair 4', status: 'available' },
      { id: 5, name: 'Chair 5', status: 'available' },
      { id: 6, name: 'Chair 6', status: 'available' }
    ],
    transactions: [
      { ref: 'TXN-8001', client: 'Hamza Farooq', service: 'Total Grooming', amount: 5000, payment: 'Cash', date: '2026-08-19' },
      { ref: 'TXN-8002', client: 'Waleed Akram', service: 'Signature Cut & Beard', amount: 3500, payment: 'Card', date: '2026-08-18' },
      { ref: 'TXN-8003', client: 'Taimoor Shah', service: 'Total Grooming', amount: 5000, payment: 'Card', date: '2026-08-18' },
      { ref: 'TXN-8004', client: 'Hamza Farooq', service: 'Beard Design', amount: 2000, payment: 'Cash', date: '2026-08-17' }
    ],
    nextUserId: 6,
    nextApptId: 1009,
    nextTxnId: 8005
  };

  /* ─── State Persistence ─── */
  function getState() {
    try {
      const raw = localStorage.getItem('elegance_salon_state');
      if (!raw) return { ...DEFAULT_STATE, chairs: DEFAULT_STATE.chairs.map(c => ({ ...c })) };
      const saved = JSON.parse(raw);
      const merged = { ...DEFAULT_STATE, ...saved };
      if (!merged.chairs || merged.chairs.length < 6) {
        merged.chairs = DEFAULT_STATE.chairs.map(c => ({ ...c }));
      }
      return merged;
    } catch (e) {
      return { ...DEFAULT_STATE, chairs: DEFAULT_STATE.chairs.map(c => ({ ...c })) };
    }
  }

  function saveState(state) {
    localStorage.setItem('elegance_salon_state', JSON.stringify(state));
  }

  let state = getState();
  saveState(state);

  /* ─── Page Detection ─── */
  const isAdmin = !!document.getElementById('pane-overview');
  const isReception = !!document.getElementById('pane-floor');

  /* ─── Toast ─── */
  window.toast = function (msg, type) {
    type = type || 'success';
    const existing = document.querySelector('.dash-toast');
    if (existing) existing.remove();
    const el = document.createElement('div');
    el.className = 'dash-toast ' + type;
    var iconChar = type === 'success' ? '&#10003;' : type === 'danger' ? '&#10007;' : '&#9432;';
    el.innerHTML = '<span class="toast-icon">' + iconChar + '</span><span>' + msg + '</span>';
    document.body.appendChild(el);
    requestAnimationFrame(function () { el.classList.add('show'); });
    setTimeout(function () {
      el.classList.add('toast-exit');
      setTimeout(function () { el.remove(); }, 400);
    }, 3200);
  };

  /* ─── Tab Switching ─── */
  window.switchTab = function (tabId, linkEl) {
    document.querySelectorAll('.dash-pane').forEach(function (p) { p.classList.remove('active'); });
    document.querySelectorAll('.dash-nav-link').forEach(function (l) { l.classList.remove('active'); });
    var pane = document.getElementById('pane-' + tabId);
    if (pane) pane.classList.add('active');
    if (linkEl) linkEl.classList.add('active');
    else {
      var link = document.querySelector('.dash-nav-link[data-tab="' + tabId + '"]');
      if (link) link.classList.add('active');
    }
    var sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.remove('show');
    if (isAdmin) renderAdminTab(tabId);
    if (isReception) renderReceptionTab(tabId);
  };

  /* ─── Modals ─── */
  window.openModal = function (id) {
    var m = document.getElementById(id);
    if (m) m.classList.add('show');
  };
  window.closeModal = function (id) {
    var m = document.getElementById(id);
    if (m) m.classList.remove('show');
  };
  document.querySelectorAll('.dash-modal-overlay').forEach(function (ov) {
    ov.addEventListener('click', function (e) {
      if (e.target === ov) ov.classList.remove('show');
    });
  });

  /* ─── ESC Key Closes Modals ─── */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      document.querySelectorAll('.dash-modal-overlay.show').forEach(function (m) {
        m.classList.remove('show');
      });
    }
  });

  /* ─── Helpers ─── */
  function fmt(n) {
    return 'Rs. ' + Number(n).toLocaleString('en-PK');
  }
  function fmtDate(d) {
    if (!d) return '—';
    var dt = new Date(d);
    return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }
  function escapeHtml(s) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(s));
    return div.innerHTML;
  }
  function generateId(type) {
    if (type === 'user') return ++state.nextUserId;
    if (type === 'appt') return ++state.nextApptId;
    if (type === 'txn') return ++state.nextTxnId;
    return Date.now();
  }
  function statusColor(s) {
    switch (s) {
      case 'In Chair': return 'green';
      case 'Confirmed': return 'blue';
      case 'Completed': return 'gold';
      case 'Cancelled': return 'red';
      case 'available': return 'green';
      case 'occupied': return 'red';
      case 'cleaning': return 'orange';
      default: return 'blue';
    }
  }
  function starRating(n) {
    var s = '';
    for (var i = 0; i < 5; i++) s += '<span style="color:' + (i < n ? 'var(--gold)' : 'var(--text-muted)') + ';">★</span>';
    return s;
  }
  function initials(name) {
    return name.split(' ').map(function (w) { return w[0]; }).join('').substring(0, 2).toUpperCase();
  }

  /* ============================================================
     PREMIUM FEATURES — NEW ADDITIONS
     ============================================================ */

  /* ─── 1. Live Clock ─── */
  function initLiveClock() {
    var topbarRight = document.querySelector('.dash-topbar-right');
    if (!topbarRight) return;
    var clockEl = document.createElement('div');
    clockEl.className = 'dash-clock';
    topbarRight.insertBefore(clockEl, topbarRight.firstChild);
    function updateClock() {
      var now = new Date();
      var h = now.getHours();
      var m = String(now.getMinutes()).padStart(2, '0');
      var s = String(now.getSeconds()).padStart(2, '0');
      var ampm = h >= 12 ? 'PM' : 'AM';
      var h12 = h % 12 || 12;
      clockEl.textContent = h12 + ':' + m + ':' + s + ' ' + ampm;
    }
    updateClock();
    setInterval(updateClock, 1000);
  }

  /* ─── 2. Animated Number Counters ─── */
  function animateCounter(el, target, prefix, duration) {
    prefix = prefix || '';
    duration = duration || 800;
    var start = 0;
    var startTime = null;
    var isNegative = target < 0;
    target = Math.abs(target);
    function easeOut(t) { return 1 - Math.pow(1 - t, 3); }
    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = easeOut(progress);
      var current = Math.round(start + (target - start) * eased);
      el.textContent = prefix + current.toLocaleString('en-PK');
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = prefix + target.toLocaleString('en-PK');
      }
    }
    requestAnimationFrame(step);
  }

  function animateStatValues() {
    var statVals = document.querySelectorAll('.dash-stat-val');
    statVals.forEach(function (el) {
      var text = el.textContent.trim();
      var isCurrency = text.indexOf('Rs.') >= 0;
      var numStr = text.replace(/[^0-9]/g, '');
      var num = parseInt(numStr, 10);
      if (!isNaN(num)) {
        animateCounter(el, num, isCurrency ? 'Rs. ' : '', 800);
      }
    });
  }

  /* ─── 3. Dynamic Greeting ─── */
  function getGreeting() {
    var h = new Date().getHours();
    if (h >= 5 && h < 12) return 'Good Morning';
    if (h >= 12 && h < 17) return 'Good Afternoon';
    if (h >= 17 && h < 21) return 'Good Evening';
    return 'Good Night';
  }

  function updateGreeting() {
    var headerEl = document.querySelector('#pane-overview .dash-header h3');
    if (headerEl) {
      var greeting = getGreeting();
      var suffix = isAdmin ? ', Saad' : '';
      headerEl.textContent = greeting + suffix;
    }
  }

  /* ─── 6. Live Data Refresh Flash ─── */
  function flashStatCards() {
    var cards = document.querySelectorAll('.dash-stat-card');
    cards.forEach(function (card) {
      card.classList.add('stat-flash');
      setTimeout(function () {
        card.classList.remove('stat-flash');
      }, 800);
    });
  }

  /* ─── 7. Smooth Table Row Entrance ─── */
  function animateTableRows(containerEl) {
    if (!containerEl) return;
    var rows = containerEl.querySelectorAll('tbody tr');
    rows.forEach(function (row, i) {
      row.classList.add('row-animate');
      row.style.animationDelay = (i * 0.04) + 's';
    });
  }

  /* ─── 8. Button Ripple Effect ─── */
  function initRipple() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.dash-btn');
      if (!btn) return;
      var rect = btn.getBoundingClientRect();
      var x = ((e.clientX - rect.left) / rect.width * 100);
      var y = ((e.clientY - rect.top) / rect.height * 100);
      btn.style.setProperty('--ripple-x', x + '%');
      btn.style.setProperty('--ripple-y', y + '%');
      btn.classList.remove('ripple-active');
      void btn.offsetWidth;
      btn.classList.add('ripple-active');
      setTimeout(function () { btn.classList.remove('ripple-active'); }, 500);
    });
  }

  /* ─── 10. Nav Hover Micro-Interaction ─── */
  function initNavHoverFx() {
    document.querySelectorAll('.dash-nav-link').forEach(function (link) {
      link.addEventListener('mouseenter', function () {
        this.style.transition = 'all 0.2s cubic-bezier(0.4, 0, 0.2, 1)';
      });
    });
  }

  /* ═══════════════════════════════════════
     ADMIN DASHBOARD RENDERING
     ═══════════════════════════════════════ */
  function renderAdminTab(tab) {
    switch (tab) {
      case 'overview': renderOverview(); break;
      case 'users': renderUsers(); break;
      case 'services': renderServices(); break;
      case 'stylists': renderStylists(); break;
      case 'stock': renderStock(); break;
      case 'appointments': renderAppointments(); break;
      case 'reports': renderReports(); break;
      case 'feedbacks': renderFeedbacks(); break;
    }
  }

  /* ─── Overview ─── */
  function renderOverview() {
    updateGreeting();
    animateStatValues();
  }

  function todayStr() {
    var d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  function renderChairGrid() {
    var el = document.getElementById('chairGrid');
    if (!el) return;
    var html = '';
    state.chairs.forEach(function (c) {
      var occupied = state.appointments.find(function (a) { return a.chair === c.id && a.status === 'In Chair'; });
      var st = occupied ? 'occupied' : c.status;
      var label = occupied ? occupied.client : 'Vacant';
      html += '<div class="col-4"><div class="dash-chair-card ' + st + '" style="text-align:center;padding:14px 8px;border-radius:12px;background:var(--bg-card);border:1px solid var(--border-light);cursor:default;">';
      html += '<div style="font-size:28px;margin-bottom:4px;">' + (st === 'occupied' ? '💈' : st === 'cleaning' ? '🧹' : '✅') + '</div>';
      html += '<div style="font-weight:600;font-size:12px;color:var(--text-primary);">' + c.name + '</div>';
      html += '<div style="font-size:11px;margin-top:2px;color:' + (st === 'occupied' ? 'var(--red)' : 'var(--green)') + ';">' + (occupied ? occupied.client : 'Vacant') + '</div>';
      html += '</div></div>';
    });
    el.innerHTML = html;
  }

  function renderRecentBookings() {
    var el = document.getElementById('recentBookings');
    if (!el || el.children.length > 0) return;
  }

  function renderTopServices() {
    var el = document.getElementById('topServices');
    if (!el || el.children.length > 0) return;
  }

  function initRevenueChart() {
    var canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    if (canvas._chart) canvas._chart.destroy();
    var ctx = canvas.getContext('2d');
    var labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
    var completedRevenue = [85000, 92000, 78000, 0];
    var thisWeekAppts = state.appointments.filter(function (a) { return a.status === 'Completed'; });
    completedRevenue[3] = thisWeekAppts.reduce(function (s, a) { return s + a.amount; }, 0) || 65000;

    canvas._chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Revenue (Rs.)',
          data: [0, 0, 0, 0],
          backgroundColor: 'rgba(212,175,55,0.7)',
          borderColor: 'rgba(212,175,55,1)',
          borderWidth: 1,
          borderRadius: 6,
          barPercentage: 0.6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1200, easing: 'easeOutQuart' },
        plugins: { legend: { display: false } },
        scales: {
          x: {
            grid: { color: 'rgba(255,255,255,0.04)' },
            ticks: { color: '#9ca3af', font: { family: 'DM Sans', size: 12 } }
          },
          y: {
            grid: { color: 'rgba(255,255,255,0.04)' },
            ticks: {
              color: '#9ca3af',
              font: { family: 'DM Sans', size: 12 },
              callback: function (v) { return 'Rs. ' + (v / 1000) + 'k'; }
            }
          }
        }
      }
    });

    setTimeout(function () {
      canvas._chart.data.datasets[0].data = completedRevenue;
      canvas._chart.update('active');
    }, 300);
  }

  /* ─── Users Table ─── */
  function renderUsers() {
    var el = document.getElementById('usersTable');
    if (!el) return;
    animateTableRows(el.closest('.dash-table-wrap') || el.closest('table'));
  }

  window.deleteUser = function (id) {
    if (!confirm('Remove this user?')) return;
    state.users = state.users.filter(function (u) { return u.id !== id; });
    saveState(state);
    renderUsers();
    toast('User removed successfully');
  };

  window.addUser = function (e) {
    e.preventDefault();
    state.users.push({
      id: generateId('user'),
      name: document.getElementById('uName').value.trim(),
      email: document.getElementById('uEmail').value.trim(),
      phone: document.getElementById('uPhone').value.trim(),
      role: document.getElementById('uRole').value,
      joined: todayStr()
    });
    saveState(state);
    closeModal('addUserModal');
    renderUsers();
    e.target.reset();
    toast('User added successfully');
  };

  /* ─── Services Table ─── */
  function renderServices() {
    var el = document.getElementById('servicesTable');
    if (!el) return;
    animateTableRows(el.closest('.dash-table-wrap') || el.closest('table'));
  }

  window.deleteService = function (id) {
    if (!confirm('Remove this service?')) return;
    state.services = state.services.filter(function (s) { return s.id !== id; });
    saveState(state);
    renderServices();
    toast('Service removed');
  };

  window.addService = function (e) {
    e.preventDefault();
    state.services.push({
      id: state.services.length ? Math.max.apply(null, state.services.map(function (s) { return s.id; })) + 1 : 1,
      name: document.getElementById('sName').value.trim(),
      category: document.getElementById('sCat').value,
      price: parseInt(document.getElementById('sPrice').value),
      duration: document.getElementById('sDuration').value,
      bookings: 0
    });
    saveState(state);
    closeModal('addServiceModal');
    renderServices();
    e.target.reset();
    toast('Service added successfully');
  };

  /* ─── Stylists Table ─── */
  function renderStylists() {
    var el = document.getElementById('stylistsTable');
    if (!el) return;
    animateTableRows(el.closest('.dash-table-wrap') || el.closest('table'));
  }

  /* ─── Stock Table ─── */
  function renderStock() {
    var el = document.getElementById('stockTable');
    if (!el) return;
    animateTableRows(el.closest('.dash-table-wrap') || el.closest('table'));
  }

  window.restockItem = function (code) {
    var item = state.stock.find(function (s) { return s.code === code; });
    if (item) {
      item.stock += 10;
      saveState(state);
      renderStock();
      toast(item.name + ' restocked (+10 units)');
    }
  };

  window.addStockItem = function (e) {
    e.preventDefault();
    var code = 'IT-' + String(state.stock.length + 1).padStart(3, '0');
    state.stock.push({
      code: code,
      name: document.getElementById('iName').value.trim(),
      category: document.getElementById('iCat').value,
      stock: parseInt(document.getElementById('iStock').value),
      min: parseInt(document.getElementById('iMin').value) || 5,
      cost: parseInt(document.getElementById('iCost').value),
      supplier: document.getElementById('iSupplier').value.trim()
    });
    saveState(state);
    closeModal('addItemModal');
    renderStock();
    e.target.reset();
    toast('Inventory item added');
  };

  /* ─── Appointments Table ─── */
  function renderAppointments() {
    var el = document.getElementById('apptTable');
    if (!el) return;
    animateTableRows(el.closest('.dash-table-wrap') || el.closest('table'));
  }

  window.updateApptStatus = function (id, newStatus) {
    var appt = state.appointments.find(function (a) { return a.id === id; });
    if (!appt) return;
    if (newStatus === 'In Chair') {
      var chairOccupied = state.chairs.find(function (c) {
        return state.appointments.some(function (a) { return a.chair === c.id && a.status === 'In Chair'; });
      });
      var freeChair = state.chairs.find(function (c) {
        return !state.appointments.some(function (a) { return a.chair === c.id && a.status === 'In Chair'; });
      });
      if (freeChair) appt.chair = freeChair.id;
    }
    appt.status = newStatus;
    if (newStatus === 'Completed' || newStatus === 'Cancelled') {
      appt.chair = 0;
      if (newStatus === 'Completed') {
        state.transactions.push({
          ref: 'TXN-' + generateId('txn'),
          client: appt.client,
          service: appt.service,
          amount: appt.amount,
          payment: appt.payment,
          date: todayStr()
        });
      }
    }
    saveState(state);
    renderAppointments();
    toast('Appointment #' + id + ' → ' + newStatus);
  };

  window.bookAppointment = function (e) {
    e.preventDefault();
    var serviceEl = document.getElementById('bService');
    var selected = serviceEl.options[serviceEl.selectedIndex];
    var price = parseInt(selected.getAttribute('data-price')) || 0;
    state.appointments.push({
      id: generateId('appt'),
      client: document.getElementById('bName').value.trim(),
      phone: document.getElementById('bPhone').value.trim(),
      email: document.getElementById('bEmail').value.trim(),
      service: serviceEl.value,
      stylist: document.getElementById('bStylist').value,
      date: document.getElementById('bDate').value,
      time: document.getElementById('bTime').value,
      amount: price,
      payment: document.getElementById('bPayment').value,
      status: 'Confirmed',
      chair: 0
    });
    saveState(state);
    closeModal('bookModal');
    renderAppointments();
    e.target.reset();
    toast('Appointment booked successfully');
  };

  /* ─── Reports ─── */
  function renderReports() {
    initServicePieChart();
    renderPeakHours();
    renderStaffPerformance();
    renderPaymentMethods();
  }

  function initServicePieChart() {
    var canvas = document.getElementById('servicePieChart');
    if (!canvas) return;
    if (canvas._chart) canvas._chart.destroy();
    var ctx = canvas.getContext('2d');
    var names = state.services.map(function (s) { return s.name; });
    var data = state.services.map(function (s) { return s.bookings; });
    var colors = ['#d4af37', '#3b82f6', '#22c55e', '#f97316', '#ef4444'];
    canvas._chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: names,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderColor: '#111318',
          borderWidth: 3,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        animation: { animateRotate: true, duration: 1000, easing: 'easeOutQuart' },
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#9ca3af',
              font: { family: 'DM Sans', size: 11 },
              padding: 12,
              usePointStyle: true,
              pointStyleWidth: 10
            }
          }
        }
      }
    });
  }

  function renderPeakHours() {
    var el = document.getElementById('peakHours');
    if (!el) return;
    var hours = [
      { label: '10:00 AM', count: 0 },
      { label: '11:30 AM', count: 0 },
      { label: '02:00 PM', count: 0 },
      { label: '04:30 PM', count: 0 },
      { label: '07:00 PM', count: 0 }
    ];
    state.appointments.forEach(function (a) {
      var h = hours.find(function (h) { return h.label === a.time; });
      if (h) h.count++;
    });
    var max = Math.max.apply(null, hours.map(function (h) { return h.count; }).concat([1]));
    var html = '';
    hours.forEach(function (h) {
      var pct = Math.round((h.count / max) * 100);
      html += '<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">';
      html += '<span style="width:80px;font-size:12px;color:var(--text-secondary);text-align:right;">' + h.label + '</span>';
      html += '<div style="flex:1;height:22px;border-radius:6px;background:var(--bg-tertiary);overflow:hidden;">';
      html += '<div style="height:100%;width:' + (pct || 5) + '%;border-radius:6px;background:linear-gradient(90deg,var(--gold),#b8941f);transition:width 0.8s;"></div>';
      html += '</div>';
      html += '<span style="width:30px;font-size:12px;font-weight:700;color:var(--gold);">' + h.count + '</span></div>';
    });
    el.innerHTML = html;
  }

  function renderStaffPerformance() {
    var el = document.getElementById('staffPerformance');
    if (!el) return;
    var stylists = ['Zachary Nickell', 'Marcus Vance', 'Ahmed Al-Tawil', 'Liam O\'Connor'];
    var perf = stylists.map(function (name) {
      var appts = state.appointments.filter(function (a) { return a.stylist === name; });
      var revenue = appts.reduce(function (s, a) { return s + a.amount; }, 0);
      return { name: name, count: appts.length, revenue: revenue };
    }).sort(function (a, b) { return b.revenue - a.revenue; });
    var maxRev = perf[0] ? perf[0].revenue : 1;
    var html = '';
    perf.forEach(function (p, i) {
      var pct = Math.round((p.revenue / maxRev) * 100);
      html += '<div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">';
      html += '<div style="width:20px;font-size:13px;font-weight:700;color:' + (i === 0 ? 'var(--gold)' : 'var(--text-muted)') + ';">' + (i + 1) + '</div>';
      html += '<div style="width:32px;height:32px;border-radius:8px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;color:var(--gold);">' + initials(p.name) + '</div>';
      html += '<div style="flex:1;">';
      html += '<div style="font-weight:600;font-size:13px;color:var(--text-primary);">' + p.name + '</div>';
      html += '<div style="height:5px;border-radius:3px;background:var(--bg-tertiary);margin-top:4px;overflow:hidden;">';
      html += '<div style="height:100%;width:' + pct + '%;border-radius:3px;background:linear-gradient(90deg,var(--gold),#b8941f);"></div></div></div>';
      html += '<div style="text-align:right;"><div style="font-weight:700;font-size:13px;color:var(--gold);">' + fmt(p.revenue) + '</div>';
      html += '<div style="font-size:11px;color:var(--text-muted);">' + p.count + ' appts</div></div></div>';
    });
    el.innerHTML = html;
  }

  function renderPaymentMethods() {
    var el = document.getElementById('paymentMethods');
    if (!el) return;
    var methods = { Cash: 0, JazzCash: 0, EasyPaisa: 0, Card: 0 };
    state.appointments.forEach(function (a) {
      if (methods[a.payment] !== undefined) methods[a.payment] += a.amount;
    });
    var total = Object.values(methods).reduce(function (s, v) { return s + v; }, 0) || 1;
    var colors = { Cash: '#22c55e', JazzCash: '#3b82f6', EasyPaisa: '#f97316', Card: '#d4af37' };
    var html = '';
    Object.keys(methods).forEach(function (m) {
      var pct = Math.round((methods[m] / total) * 100);
      html += '<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-light);">';
      html += '<div style="display:flex;align-items:center;gap:10px;">';
      html += '<div style="width:10px;height:10px;border-radius:50%;background:' + colors[m] + ';"></div>';
      html += '<span style="font-size:13px;color:var(--text-primary);">' + m + '</span></div>';
      html += '<div style="text-align:right;"><span style="font-weight:600;font-size:13px;color:var(--text-primary);">' + fmt(methods[m]) + '</span>';
      html += '<span style="font-size:11px;color:var(--text-muted);margin-left:6px;">' + pct + '%</span></div></div>';
    });
    el.innerHTML = html;
  }

  /* ─── Feedbacks ─── */
  function renderFeedbacks() {
    var el = document.getElementById('feedbackList');
    var countEl = document.getElementById('feedbackCount');
    if (countEl) countEl.textContent = state.feedbacks.length + ' total';
    if (!el) return;
    var html = '';
    state.feedbacks.forEach(function (f) {
      html += '<div style="padding:16px 0;border-bottom:1px solid var(--border-light);display:flex;gap:14px;">';
      html += '<div style="width:42px;height:42px;border-radius:12px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:var(--gold);flex-shrink:0;">' + (f.avatar || initials(f.name)) + '</div>';
      html += '<div style="flex:1;min-width:0;">';
      html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">';
      html += '<div><span style="font-weight:700;font-size:14px;color:var(--text-primary);">' + escapeHtml(f.name) + '</span>';
      html += ' <span class="dash-badge ' + (f.type === 'review' ? 'gold' : 'blue') + '" style="font-size:10px;margin-left:6px;">' + f.type + '</span></div>';
      html += '<span style="font-size:12px;color:var(--text-muted);">' + fmtDate(f.date) + '</span></div>';
      if (f.rating) html += '<div style="margin-bottom:6px;">' + starRating(f.rating) + '</div>';
      if (f.subject) html += '<div style="font-weight:600;font-size:13px;color:var(--text-primary);margin-bottom:2px;">' + escapeHtml(f.subject) + '</div>';
      html += '<p style="font-size:13px;color:var(--text-secondary);margin:0;line-height:1.5;">' + escapeHtml(f.text) + '</p>';
      html += '</div></div>';
    });
    if (!state.feedbacks.length) html = '<p style="text-align:center;padding:30px;color:var(--text-muted);">No feedback yet</p>';
    el.innerHTML = html;
  }

  /* ═══════════════════════════════════════
     RECEPTION DASHBOARD RENDERING
     ═══════════════════════════════════════ */
  function renderReceptionTab(tab) {
    switch (tab) {
      case 'floor': renderReceptionFloor(); break;
      case 'bookings': renderReceptionAppts(); break;
      case 'clients': renderClients(); break;
      case 'pos': renderPOS(); break;
    }
  }

  /* ─── Floor & Queue ─── */
  function renderReceptionFloor() {
    renderReceptionChairs();
    renderReceptionApptTable();
    renderReceptionTransactions();
  }

  function renderReceptionChairs() {
    var el = document.getElementById('receptionChairs');
    if (!el) return;
    var html = '';
    state.chairs.forEach(function (c) {
      var occupied = state.appointments.find(function (a) { return a.chair === c.id && a.status === 'In Chair'; });
      var st = occupied ? 'occupied' : c.status;
      html += '<div class="col-md-2 col-sm-4 col-6">';
      html += '<div class="dash-chair-card ' + st + '" style="text-align:center;padding:20px 12px;border-radius:14px;background:var(--bg-card);border:1px solid ' + (st === 'occupied' ? 'var(--red)' : 'var(--border-light)') + ';position:relative;overflow:hidden;">';
      if (st === 'occupied') html += '<div style="position:absolute;top:0;left:0;right:0;height:3px;background:var(--red);"></div>';
      html += '<div style="font-size:36px;margin-bottom:6px;">' + (st === 'occupied' ? '💈' : '✅') + '</div>';
      html += '<div style="font-weight:700;font-size:13px;color:var(--text-primary);margin-bottom:2px;">' + c.name + '</div>';
      html += '<div style="font-size:12px;color:' + (st === 'occupied' ? 'var(--red)' : 'var(--green)') + ';">' + (occupied ? occupied.client : 'Vacant') + '</div>';
      if (occupied) {
        html += '<div style="font-size:11px;color:var(--text-muted);margin-top:2px;">' + escapeHtml(occupied.time) + '</div>';
        html += '<button class="dash-btn outline sm" onclick="updateApptStatus(' + occupied.id + ',\'Completed\')" style="margin-top:8px;font-size:11px;padding:4px 12px;color:var(--gold);border-color:var(--border-gold);">Complete</button>';
      }
      html += '</div></div>';
    });
    el.innerHTML = html;
  }

  function renderReceptionApptTable() {
    var el = document.getElementById('receptionApptTable');
    if (!el) return;
    var today = todayStr();
    var list = state.appointments.filter(function (a) { return a.date === today; });
    list.sort(function (a, b) { return a.time.localeCompare(b.time); });
    var html = '';
    list.forEach(function (a, i) {
      html += '<tr>';
      html += '<td style="color:var(--text-muted);font-size:13px;">' + (i + 1) + '</td>';
      html += '<td style="font-weight:600;color:var(--text-primary);font-size:13px;">' + escapeHtml(a.client) + '</td>';
      html += '<td style="color:var(--text-secondary);font-size:13px;">' + escapeHtml(a.service) + '</td>';
      html += '<td style="color:var(--text-secondary);font-size:13px;">' + escapeHtml(a.stylist) + '</td>';
      html += '<td style="color:var(--text-primary);font-size:13px;">' + escapeHtml(a.time) + '</td>';
      html += '<td style="color:var(--text-muted);font-size:13px;">' + escapeHtml(a.payment) + '</td>';
      html += '<td><span class="dash-badge ' + statusColor(a.status) + '">' + a.status + '</span></td>';
      html += '<td>';
      if (a.status === 'Confirmed') {
        html += '<button class="dash-btn outline sm" onclick="updateApptStatus(' + a.id + ',\'In Chair\')" style="margin-right:4px;color:var(--green);border-color:rgba(34,197,94,0.3);font-size:11px;">Seat</button>';
      }
      if (a.status === 'In Chair') {
        html += '<button class="dash-btn outline sm" onclick="updateApptStatus(' + a.id + ',\'Completed\')" style="color:var(--gold);border-color:var(--border-gold);font-size:11px;">Done</button>';
      }
      html += '</td></tr>';
    });
    if (!list.length) html = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted);">No appointments today</td></tr>';
    el.innerHTML = html;
    animateTableRows(el);
  }

  function renderReceptionTransactions() {
    var el = document.getElementById('receptionTransactions');
    if (!el) return;
    var recent = state.transactions.slice().reverse().slice(0, 5);
    var html = '';
    recent.forEach(function (t) {
      html += '<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-light);">';
      html += '<div><div style="font-weight:600;font-size:13px;color:var(--text-primary);">' + escapeHtml(t.client) + '</div>';
      html += '<div style="font-size:11px;color:var(--text-muted);">' + escapeHtml(t.service) + ' · ' + escapeHtml(t.payment) + '</div></div>';
      html += '<div style="font-weight:700;font-size:13px;color:var(--gold);">' + fmt(t.amount) + '</div></div>';
    });
    if (!recent.length) html = '<p style="text-align:center;padding:20px;color:var(--text-muted);">No transactions yet</p>';
    el.innerHTML = html;
  }

  /* ─── Reception Appointments (All Bookings tab) ─── */
  function renderReceptionAppts() {
    var el = document.getElementById('recFullApptTable');
    if (!el) return;
    var search = (document.getElementById('recApptSearch') ? document.getElementById('recApptSearch').value : '').toLowerCase();
    var filter = document.getElementById('recApptFilter') ? document.getElementById('recApptFilter').value : 'all';
    var list = state.appointments.filter(function (a) {
      var matchSearch = !search || a.client.toLowerCase().indexOf(search) >= 0 || a.phone.indexOf(search) >= 0;
      var matchFilter = filter === 'all' || a.status === filter;
      return matchSearch && matchFilter;
    });
    list.sort(function (a, b) { return b.date.localeCompare(a.date) || a.time.localeCompare(b.time); });
    var html = '';
    list.forEach(function (a) {
      html += '<tr>';
      html += '<td style="color:var(--text-muted);font-size:12px;">#' + a.id + '</td>';
      html += '<td style="font-weight:600;color:var(--text-primary);font-size:13px;">' + escapeHtml(a.client) + '</td>';
      html += '<td style="color:var(--text-secondary);font-size:13px;">' + escapeHtml(a.service) + '</td>';
      html += '<td style="color:var(--text-secondary);font-size:13px;">' + escapeHtml(a.stylist) + '</td>';
      html += '<td style="font-size:13px;"><div>' + fmtDate(a.date) + '</div><div style="font-size:12px;color:var(--text-muted);">' + escapeHtml(a.time) + '</div></td>';
      html += '<td style="font-weight:600;color:var(--gold);font-size:13px;">' + fmt(a.amount) + '</td>';
      html += '<td><span class="dash-badge ' + statusColor(a.status) + '">' + a.status + '</span></td>';
      html += '<td>';
      if (a.status === 'Confirmed') {
        html += '<button class="dash-btn outline sm" onclick="updateApptStatus(' + a.id + ',\'In Chair\')" style="margin-right:4px;color:var(--green);border-color:rgba(34,197,94,0.3);">Seat</button>';
      }
      if (a.status === 'In Chair') {
        html += '<button class="dash-btn outline sm" onclick="updateApptStatus(' + a.id + ',\'Completed\')" style="margin-right:4px;color:var(--gold);border-color:var(--border-gold);">Done</button>';
      }
      html += '<button class="dash-btn outline sm" onclick="updateApptStatus(' + a.id + ',\'Cancelled\')" style="color:var(--red);border-color:rgba(239,68,68,0.3);">X</button>';
      html += '</td></tr>';
    });
    if (!list.length) html = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted);">No bookings found</td></tr>';
    el.innerHTML = html;
    animateTableRows(el);
  }

  /* ─── Clients ─── */
  function renderClients() {
    var el = document.getElementById('recClientTable');
    if (!el) return;
    var html = '';
    state.clients.forEach(function (c) {
      html += '<tr>';
      html += '<td style="color:var(--text-muted);font-size:12px;">#' + c.id + '</td>';
      html += '<td><div style="display:flex;align-items:center;gap:10px;">';
      html += '<div style="width:34px;height:34px;border-radius:10px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);">' + initials(c.name) + '</div>';
      html += '<span style="font-weight:600;color:var(--text-primary);">' + escapeHtml(c.name) + '</span></div></td>';
      html += '<td style="color:var(--text-secondary);font-size:13px;">' + escapeHtml(c.phone) + '</td>';
      html += '<td style="font-weight:600;color:var(--text-primary);font-size:13px;">' + c.visits + '</td>';
      html += '<td style="font-weight:600;color:var(--gold);font-size:13px;">' + fmt(c.spent) + '</td>';
      html += '<td style="color:var(--text-muted);font-size:12px;">' + escapeHtml(c.preferences) + '</td>';
      html += '</tr>';
    });
    el.innerHTML = html;
    animateTableRows(el);
  }

  /* ─── POS & Billing ─── */
  function renderPOS() {
    var el = document.getElementById('posTable');
    if (!el) return;
    var completed = state.appointments.filter(function (a) { return a.status === 'Completed'; });
    var html = '';
    completed.forEach(function (a) {
      html += '<tr>';
      html += '<td style="font-family:monospace;font-size:12px;color:var(--text-muted);">TXN-' + a.id + '</td>';
      html += '<td style="font-weight:600;color:var(--text-primary);font-size:13px;">' + escapeHtml(a.client) + '</td>';
      html += '<td style="color:var(--text-secondary);font-size:13px;">' + escapeHtml(a.service) + '</td>';
      html += '<td style="font-weight:600;color:var(--gold);font-size:13px;">' + fmt(a.amount) + '</td>';
      html += '<td><span class="dash-badge ' + (a.payment === 'Cash' ? 'green' : a.payment === 'Card' ? 'gold' : 'blue') + '">' + a.payment + '</span></td>';
      html += '<td style="color:var(--text-muted);font-size:12px;">' + fmtDate(a.date) + '</td>';
      html += '<td><button class="dash-btn outline sm" onclick="showReceipt(' + a.id + ')" style="color:var(--gold);border-color:var(--border-gold);">Receipt</button></td>';
      html += '</tr>';
    });
    if (!completed.length) html = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">No completed services yet</td></tr>';
    el.innerHTML = html;
    animateTableRows(el);
  }

  window.showReceipt = function (id) {
    var a = state.appointments.find(function (ap) { return ap.id === id; });
    if (!a) return;
    var el = document.getElementById('receiptContent');
    if (!el) return;
    el.innerHTML = '<div style="text-align:center;">' +
      '<div style="font-size:10.5px;font-weight:700;letter-spacing:1.5px;color:#555 !important;">✦ SALON SALES SLIP ✦</div>' +
      '<h3 style="margin:4px 0 2px;font-size:20px;font-weight:900;letter-spacing:2px;color:#000 !important;">ELEGANCE SALON</h3>' +
      '<div style="font-size:11px;font-weight:700;color:#222 !important;letter-spacing:0.5px;">LUXURY MEN\'S LOUNGE KARACHI</div>' +
      '<div style="font-size:10px;color:#555 !important;margin-top:2px;">Main Tariq Road, Block 2 PECHS &bull; Tel: 0300-1234567</div>' +
      '</div>' +
      '<div class="receipt-divider-dashed"></div>' +
      '<div class="receipt-token-badge">TOKEN: ' + (a.token || ('TK-' + a.id)) + '</div>' +
      '<div class="receipt-divider-dashed"></div>' +
      '<div class="receipt-row"><span style="color:#555 !important;">Invoice No:</span><strong style="color:#000 !important;font-weight:700;">' + (a.ref || ('ELG-' + a.id)) + '</strong></div>' +
      '<div class="receipt-row"><span style="color:#555 !important;">Date / Time:</span><span style="color:#111 !important;">' + fmtDate(a.date) + '</span></div>' +
      '<div class="receipt-row"><span style="color:#555 !important;">Customer:</span><strong style="color:#000 !important;font-weight:700;">' + escapeHtml(a.client) + '</strong></div>' +
      '<div class="receipt-row"><span style="color:#555 !important;">Phone:</span><span style="color:#111 !important;">' + escapeHtml(a.phone || 'N/A') + '</span></div>' +
      '<div class="receipt-row"><span style="color:#555 !important;">Master Barber:</span><strong style="color:#000 !important;">' + escapeHtml(a.stylist || 'Assigned Staff') + '</strong></div>' +
      '<div class="receipt-divider-dashed"></div>' +
      '<div class="receipt-row" style="font-weight:700;font-size:13px;"><span style="color:#000 !important;">' + escapeHtml(a.service) + '</span><span style="color:#000 !important;">' + fmt(a.amount) + '</span></div>' +
      '<div class="receipt-divider-double"></div>' +
      '<div class="receipt-row total-row"><span style="color:#000 !important;">NET TOTAL:</span><span style="color:#000 !important;">' + fmt(a.amount) + '</span></div>' +
      '<div class="receipt-row" style="margin-top:6px;font-size:11.5px;"><span style="color:#444 !important;">Payment:</span><strong style="color:#000 !important;text-transform:uppercase;background:#e5e5e5;padding:2px 8px;border-radius:3px;">' + escapeHtml(a.payment || 'Cash') + ' (PAID)</strong></div>' +
      '<div class="receipt-divider-dashed"></div>' +
      '<div style="text-align:center;font-size:10px;line-height:1.4;color:#444 !important;margin-top:8px;">' +
      '<div style="font-weight:700;color:#111 !important;">THANK YOU FOR YOUR PATRONAGE!</div>' +
      '<div>Follow us on Instagram: @elegance.salon.pk</div>' +
      '<div style="font-style:italic;margin-top:3px;font-size:9px;color:#777 !important;">*** Computer Generated Sales Slip &bull; 80mm POS ***</div>' +
      '</div>';
    openModal('receiptModal');
  };

  window.handleWalkin = function (e) {
    e.preventDefault();
    var serviceEl = document.getElementById('wService');
    var selected = serviceEl.options[serviceEl.selectedIndex];
    var price = parseInt(selected.getAttribute('data-price')) || 0;
    var freeChair = state.chairs.find(function (c) {
      return !state.appointments.some(function (a) { return a.chair === c.id && a.status === 'In Chair'; });
    });
    state.appointments.push({
      id: generateId('appt'),
      client: document.getElementById('wName').value.trim(),
      phone: document.getElementById('wPhone').value.trim(),
      email: document.getElementById('wEmail').value.trim(),
      service: serviceEl.value,
      stylist: document.getElementById('wStylist').value,
      date: todayStr(),
      time: document.getElementById('wTime').value,
      amount: price,
      payment: document.getElementById('wPayment').value,
      status: freeChair ? 'In Chair' : 'Confirmed',
      chair: freeChair ? freeChair.id : 0
    });
    saveState(state);
    closeModal('walkinModal');
    if (isReception) renderReceptionFloor();
    e.target.reset();
    toast('Walk-in booked' + (freeChair ? ' — seated at ' + freeChair.name : ' — awaiting chair'));
  };

  window.addClient = function (e) {
    e.preventDefault();
    state.clients.push({
      id: state.clients.length ? Math.max.apply(null, state.clients.map(function (c) { return c.id; })) + 1 : 1,
      name: document.getElementById('rcName').value.trim(),
      phone: document.getElementById('rcPhone').value.trim(),
      visits: 0,
      spent: 0,
      preferences: document.getElementById('rcStylist').value ? 'Prefers: ' + document.getElementById('rcStylist').value : ''
    });
    saveState(state);
    closeModal('addClientRecModal');
    renderClients();
    e.target.reset();
    toast('Client registered successfully');
  };

  /* ─── Global Search ─── */
  var searchInput = document.getElementById('globalSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      var q = this.value.toLowerCase().trim();
      if (!q) return;
      if (isAdmin) {
        var found = false;
        ['users', 'services', 'stock', 'appointments', 'feedbacks'].forEach(function (tab) {
          if (found) return;
          var pane = document.getElementById('pane-' + tab);
          if (pane && pane.classList.contains('active')) {
            if (tab === 'appointments') {
              if (document.getElementById('apptSearch')) document.getElementById('apptSearch').value = q;
              if (typeof filterApptTable === 'function') filterApptTable();
              else renderAppointments();
              found = true;
            }
          }
        });
      }
      if (isReception) {
        var activePane = document.querySelector('.dash-pane.active');
        if (activePane && activePane.id === 'pane-bookings') {
          var recSearch = document.getElementById('recApptSearch');
          if (recSearch) { recSearch.value = q; renderReceptionAppts(); }
        }
      }
    });
  }

  /* ─── Initial Render & URL Tab Auto-select ─── */
  var urlParams = new URLSearchParams(window.location.search);
  var activeTabParam = urlParams.get('tab') || window.location.hash.replace('#', '');
  if (activeTabParam && document.getElementById('pane-' + activeTabParam)) {
    window.switchTab(activeTabParam);
  } else {
    if (isAdmin) renderOverview();
    if (isReception) renderReceptionFloor();
  }

  /* ─── Luxury Horizontal Table Scroller System ─── */
  window.scrollTableWrap = function (wrapRef, distance) {
    var wrap = (typeof wrapRef === 'string') ? document.getElementById(wrapRef) : wrapRef;
    if (!wrap && typeof wrapRef === 'string') {
      wrap = document.querySelector(wrapRef);
    }
    if (wrap) {
      wrap.scrollBy({ left: distance, behavior: 'smooth' });
    }
  };

  function initDualTableScrollers() {
    var wraps = document.querySelectorAll('.dash-table-wrap');
    wraps.forEach(function (wrap) {
      var table = wrap.querySelector('.dash-table');
      if (!table) return;

      // Link with top scroller if present
      var prev = wrap.previousElementSibling;
      if (prev && prev.classList.contains('dash-top-scroll')) {
        var inner = prev.querySelector('.dash-top-scroll-inner');
        function syncTopWidth() {
          if (inner && table) {
            inner.style.width = table.scrollWidth + 'px';
          }
        }
        syncTopWidth();
        window.addEventListener('resize', syncTopWidth);

        var isSyncingWrap = false;
        var isSyncingTop = false;

        wrap.addEventListener('scroll', function () {
          if (!isSyncingWrap) {
            isSyncingTop = true;
            prev.scrollLeft = wrap.scrollLeft;
            isSyncingTop = false;
          }
        });

        prev.addEventListener('scroll', function () {
          if (!isSyncingTop) {
            isSyncingWrap = true;
            wrap.scrollLeft = prev.scrollLeft;
            isSyncingWrap = false;
          }
        });
      }

      // Smooth mouse drag-to-pan (Excel-like horizontal scrolling)
      var isDown = false;
      var startX, scrollLeft;

      wrap.addEventListener('mousedown', function (e) {
        if (e.target.closest('a, button, input, select, textarea, .dash-btn')) return;
        isDown = true;
        wrap.style.cursor = 'grabbing';
        wrap.style.userSelect = 'none';
        startX = e.pageX - wrap.offsetLeft;
        scrollLeft = wrap.scrollLeft;
      });

      wrap.addEventListener('mouseleave', function () {
        if (isDown) {
          isDown = false;
          wrap.style.cursor = '';
          wrap.style.userSelect = '';
        }
      });

      wrap.addEventListener('mouseup', function () {
        if (isDown) {
          isDown = false;
          wrap.style.cursor = '';
          wrap.style.userSelect = '';
        }
      });

      wrap.addEventListener('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        var x = e.pageX - wrap.offsetLeft;
        var walk = (x - startX) * 1.5; // Scroll speed factor
        wrap.scrollLeft = scrollLeft - walk;
      });
    });
  }

  // Hook into tab switches so table widths re-calculate properly
  var origSwitchTab = window.switchTab;
  window.switchTab = function (paneId) {
    if (typeof origSwitchTab === 'function') origSwitchTab(paneId);
    setTimeout(initDualTableScrollers, 60);
  };

  /* ─── Initialize Premium Features ─── */
  initLiveClock();
  initRipple();
  initNavHoverFx();
  initDualTableScrollers();

  setInterval(flashStatCards, 30000);

  /* ─── Mobile Sidebar Toggle & Backdrop System ─── */
  window.toggleSidebar = function () {
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar) return;
    var isOpen = sidebar.classList.toggle('show');
    if (backdrop) {
      if (isOpen) {
        backdrop.classList.add('active');
      } else {
        backdrop.classList.remove('active');
      }
    }
    // Prevent body scroll when sidebar is open
    document.body.style.overflow = isOpen ? 'hidden' : '';
  };

  window.closeSidebar = function () {
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) sidebar.classList.remove('show');
    if (backdrop) backdrop.classList.remove('active');
    document.body.style.overflow = '';
  };

  // Close sidebar on window resize to desktop
  window.addEventListener('resize', function () {
    if (window.innerWidth > 991) {
      closeSidebar();
    }
  });

  // Close sidebar on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeSidebar();
    }
  });

  // Close sidebar when a nav link is clicked (mobile)
  document.querySelectorAll('.dash-nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 991) {
        setTimeout(closeSidebar, 150);
      }
    });
  });

})();
