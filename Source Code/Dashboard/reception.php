<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strict Reception Access Control (Receptionist or Admin Supervision)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'receptionist' && $_SESSION['user_role'] !== 'admin')) {
    header("Location: ../userside/admin-login.php?error=unauthorized");
    exit();
}

$pageTitle = "Reception Desk Console | Elegance Salon Karachi";

// Fetch exact Logged-in User from database if session exists, else default to Ayesha Reception
$uid = intval($_SESSION['user_id'] ?? 0);
$uQuery = ($uid > 0) ? mysqli_query($conn, "SELECT `name`, `email`, `role` FROM `user` WHERE `id`='$uid' LIMIT 1") : false;
$recData = ($uQuery && $rRow = mysqli_fetch_assoc($uQuery)) ? $rRow : null;

$currentUserName = !empty($recData['name']) ? $recData['name'] : ($_SESSION['user_name'] ?? 'Ayesha Reception');
$userRoleVal = !empty($recData['role']) ? $recData['role'] : ($_SESSION['user_role'] ?? 'receptionist');
$currentUserRole = ($userRoleVal === 'admin') ? 'Administrator (Supervision)' : 'Front Desk Receptionist';

// ==========================================
// 1. WALK-IN APPOINTMENT SUBMISSION (INSERT)
// ==========================================
if (isset($_POST['walkin_booking_submit'])) {
    $ref          = 'ELG-' . rand(1000, 9999);
    $tok          = 'TK-' . rand(10, 99);
    $client_name  = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_name'])));
    $client_phone = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_phone'])));
    $client_email = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_email'] ?? '')));
    $service_name = mysqli_real_escape_string($conn, clean_text(trim($_POST['service_name'])));
    $stylist_name = mysqli_real_escape_string($conn, clean_text(trim($_POST['stylist_name'])));
    $appt_date    = date('Y-m-d');
    $appt_time    = mysqli_real_escape_string($conn, trim($_POST['appointment_time'] ?? date('h:i A')));
    $price        = floatval($_POST['price'] ?? 3500);
    $payment_mode = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_mode'] ?? 'Cash')));
    $chair_no     = intval($_POST['chair_no'] ?? 1);
    $commission   = $price * 0.18;

    $ins_query = "INSERT INTO `appointments`
                  (`booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_name`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`)
                  VALUES
                  ('$ref', '$tok', '$client_name', '$client_phone', '$client_email', '$service_name', '$stylist_name', '$appt_date', '$appt_time', '$chair_no', '$price', '$commission', '$payment_mode', 'Unpaid', 'In Chair')";

    mysqli_query($conn, $ins_query);
    header("Location: reception.php?tab=floor");
    exit();
}

// ==========================================
// 2. QUICK 1-CLICK STATUS UPDATES
// ==========================================
if (isset($_GET['rec_action']) && isset($_GET['id'])) {
    $act = trim($_GET['rec_action']);
    $id = intval($_GET['id']);

    if ($act === 'seat') {
        $chair = isset($_GET['chair']) ? intval($_GET['chair']) : rand(1, 6);
        mysqli_query($conn, "UPDATE `appointments` SET `status`='In Chair', `chair_no`='$chair' WHERE `id`='$id'");
    } elseif ($act === 'done') {
        mysqli_query($conn, "UPDATE `appointments` SET `status`='Completed', `payment_status`='Paid' WHERE `id`='$id'");
    } elseif ($act === 'cancel') {
        mysqli_query($conn, "UPDATE `appointments` SET `status`='Cancelled' WHERE `id`='$id'");
    }
    header("Location: reception.php?tab=floor");
    exit();
}

// ==========================================
// 3. REGISTER NEW CLIENT FROM RECEPTION
// ==========================================
if (isset($_POST['add_client_rec_submit'])) {
    $c_name  = htmlspecialchars(trim($_POST['client_name']));
    $c_phone = htmlspecialchars(trim($_POST['client_phone']));
    $c_email = htmlspecialchars(trim($_POST['client_email'] ?? ''));
    $c_pass  = md5('123456');

    if (!empty($c_name) && !empty($c_phone)) {
        if (empty($c_email)) $c_email = 'client_' . time() . '@elegance.pk';
        mysqli_query($conn, "INSERT INTO `user` (`name`, `email`, `phone`, `password`, `role`) VALUES ('$c_name', '$c_email', '$c_phone', '$c_pass', 'client')");
    }
    header("Location: reception.php?tab=clients");
    exit();
}

// ==========================================
// 4. UPDATE APPOINTMENT (RECEPTION)
// ==========================================
if (isset($_POST['update_appointment_rec_submit'])) {
    $id               = intval($_POST['appointment_id']);
    $client_name      = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_name'])));
    $client_phone     = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_phone'])));
    $client_email     = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_email'] ?? '')));
    $service_name     = mysqli_real_escape_string($conn, clean_text(trim($_POST['service_name'])));
    $stylist_name     = mysqli_real_escape_string($conn, clean_text(trim($_POST['stylist_name'])));
    $appointment_date = mysqli_real_escape_string($conn, trim($_POST['appointment_date']));
    $appointment_time = mysqli_real_escape_string($conn, trim($_POST['appointment_time']));
    $chair_no         = intval($_POST['chair_no'] ?? 1);
    $price            = floatval($_POST['price']);
    $payment_mode     = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_mode'] ?? 'Cash')));
    $payment_status   = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_status'] ?? 'Unpaid')));
    $status           = mysqli_real_escape_string($conn, clean_text(trim($_POST['status'] ?? 'Confirmed')));
    $commission       = $price * 0.18;

    $update_appt_query = "UPDATE `appointments` SET
                          `client_name`='$client_name', `client_phone`='$client_phone', `client_email`='$client_email',
                          `service_name`='$service_name', `stylist_name`='$stylist_name', `appointment_date`='$appointment_date',
                          `appointment_time`='$appointment_time', `chair_no`='$chair_no', `price`='$price',
                          `commission_amount`='$commission', `payment_mode`='$payment_mode', `payment_status`='$payment_status',
                          `status`='$status'
                          WHERE `id`='$id'";
    mysqli_query($conn, $update_appt_query);
    header("Location: reception.php?tab=bookings");
    exit();
}

// ==========================================
// 5. DELETE APPOINTMENT (RECEPTION)
// ==========================================
if (isset($_GET['delete_appointment_rec'])) {
    $id = intval($_GET['delete_appointment_rec']);
    mysqli_query($conn, "DELETE FROM `appointments` WHERE `id`='$id'");
    header("Location: reception.php?tab=bookings");
    exit();
}

// ==========================================
// 6. FETCH DATA & METRICS FOR RECEPTION
// ==========================================

// Today's Queue & Metrics
$today_str = date('Y-m-d');
$today_queue_q = mysqli_query($conn, "SELECT * FROM `appointments` WHERE `appointment_date` = '$today_str' ORDER BY CASE WHEN `status`='In Chair' THEN 1 WHEN `status`='Confirmed' OR `status`='Booked' THEN 2 WHEN `status`='Completed' THEN 3 ELSE 4 END, `id` DESC");

$today_active_in_chair = 0;
$today_waiting_count = 0;
$today_done_count = 0;
$today_sales_total = 0;

$t_calc_q = mysqli_query($conn, "SELECT `status`, `price`, `payment_status` FROM `appointments` WHERE `appointment_date` = '$today_str'");
if ($t_calc_q) {
    while ($tc = mysqli_fetch_assoc($t_calc_q)) {
        if ($tc['status'] === 'In Chair') $today_active_in_chair++;
        if ($tc['status'] === 'Confirmed' || $tc['status'] === 'Booked') $today_waiting_count++;
        if ($tc['status'] === 'Completed') {
            $today_done_count++;
            $today_sales_total += $tc['price'];
        }
    }
}

// All Appointments
$all_appts_q = mysqli_query($conn, "SELECT * FROM `appointments` ORDER BY `appointment_date` DESC, `id` DESC");

// Occupied Chairs Mapping
$occupied_chairs = [];
$chair_q = mysqli_query($conn, "SELECT * FROM `appointments` WHERE `status` = 'In Chair'");
if ($chair_q) {
    while ($cRow = mysqli_fetch_assoc($chair_q)) {
        $cNo = intval($cRow['chair_no'] ?: 1);
        $occupied_chairs[$cNo] = $cRow;
    }
}

// Collections / POS
$pos_q = mysqli_query($conn, "SELECT * FROM `appointments` WHERE `status` = 'Completed' OR `payment_status` = 'Paid' ORDER BY `appointment_date` DESC, `id` DESC LIMIT 30");

// Clients Directory
$clients_dir_q = mysqli_query($conn, "SELECT u.id, u.name, u.email, u.phone, u.created_at, COUNT(a.id) as total_visits, SUM(CASE WHEN a.status='Completed' THEN a.price ELSE 0 END) as total_spent FROM `user` u LEFT JOIN `appointments` a ON (u.email = a.client_email OR u.phone = a.client_phone) WHERE u.role = 'client' GROUP BY u.id ORDER BY total_visits DESC, u.id DESC");

// Dropdown Caches
$services_list = [];
$s_res = mysqli_query($conn, "SELECT `id`, `name`, `price`, `duration` FROM `services` ORDER BY `name` ASC");
if ($s_res) { while ($s = mysqli_fetch_assoc($s_res)) { $services_list[] = $s; } }

$staff_list = [];
$st_res = mysqli_query($conn, "SELECT `id`, `name`, `specialization` FROM `staff` WHERE `status` = 'Active' ORDER BY `name` ASC");
if ($st_res) { while ($st = mysqli_fetch_assoc($st_res)) { $staff_list[] = $st; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
</head>
<body class="dash-body">

    <!-- Luxury Reception Terminal Welcome Splash Screen (Shows ONLY on initial load, NOT on refreshes) -->
    <div id="receptionWelcomeSplash" style="display:none;position:fixed;inset:0;background:radial-gradient(circle at center, #101c24 0%, #060b0e 100%);z-index:99999;flex-direction:column;align-items:center;justify-content:center;transition:opacity 0.5s ease, visibility 0.5s;">
        <div style="text-align:center;max-width:480px;padding:30px;">
            <div style="width:76px;height:76px;margin:0 auto 20px;border-radius:22px;background:linear-gradient(135deg, #05CD99, #1B2559);display:flex;align-items:center;justify-content:center;font-size:32px;box-shadow:0 0 35px rgba(5,205,153,0.4);border:1px solid rgba(255,255,255,0.2);">
                💼
            </div>
            <span style="letter-spacing:4px;font-size:11px;color:#05CD99;text-transform:uppercase;font-weight:700;display:block;margin-bottom:8px;">FLAGSHIP FRONT DESK TERMINAL</span>
            <h2 style="color:#ffffff;font-size:25px;font-weight:300;margin-bottom:6px;">Welcome, <span style="color:#05CD99;font-weight:600;"><?php echo htmlspecialchars($currentUserName); ?></span></h2>
            <p style="color:#8b93a7;font-size:12.5px;margin-bottom:20px;">Role: <span style="color:#fff;font-weight:600;"><?php echo htmlspecialchars($currentUserRole); ?></span> &middot; Terminal 01 (Tariq Rd)</p>

            <div style="width:220px;height:4px;background:rgba(255,255,255,0.1);border-radius:10px;margin:0 auto;overflow:hidden;position:relative;">
                <div style="width:100%;height:100%;background:linear-gradient(90deg, #05CD99, #70E2C8, #05CD99);position:absolute;left:-100%;animation:splashLoaderRec 1.2s infinite ease-in-out;"></div>
            </div>
        </div>
    </div>
    <style>
        @keyframes splashLoaderRec {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
    <script>
        (function() {
            var splashKey = 'elegance_reception_splash_seen';
            var splash = document.getElementById('receptionWelcomeSplash');
            if (splash && !sessionStorage.getItem(splashKey)) {
                splash.style.display = 'flex';
                sessionStorage.setItem(splashKey, '1');
                setTimeout(function() {
                    splash.style.opacity = '0';
                    setTimeout(function() { splash.style.display = 'none'; }, 500);
                }, 900);
            }
        })();
    </script>

<div class="dash-layout">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="dash-sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="dash-sidebar" id="sidebar">
        <a href="../index.php" class="dash-brand">
            <div class="dash-brand-icon" style="background:linear-gradient(135deg,var(--green),#065f46);">R</div>
            <div class="dash-brand-text">
                <h2>ELEGANCE</h2>
                <span>FRONT DESK</span>
            </div>
        </a>

        <nav class="dash-nav">
                <div class="dash-nav-label">Operations</div>
                <div class="dash-nav-item">
                    <a class="dash-nav-link active" data-tab="floor" onclick="switchTab('floor',this)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>
                        <span>Floor & Queue</span>
                    </a>
                </div>
                <div class="dash-nav-item">
                    <a class="dash-nav-link" data-tab="bookings" onclick="switchTab('bookings',this)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>All Bookings</span>
                    </a>
                </div>
                <div class="dash-nav-item">
                    <a class="dash-nav-link" data-tab="clients" onclick="switchTab('clients',this)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        <span>Clients Directory</span>
                    </a>
                </div>
                <div class="dash-nav-item">
                    <a class="dash-nav-link" data-tab="pos" onclick="switchTab('pos',this)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                        <span>POS & Collections</span>
                    </a>
                </div>
            </nav>

        <div class="dash-sidebar-footer">
            <a href="../userside/logout.php" class="dash-logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Log Out Terminal
            </a>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="dash-main">
        <header class="dash-topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="dash-topbar-btn d-lg-none" onclick="toggleSidebar()" style="padding:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <div class="dash-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="globalRecSearch" placeholder="🔍 Quick Lookup: Phone (0300...), Gmail, Name..." oninput="filterRecTables(this.value)">
                </div>
            </div>
            <div class="dash-topbar-right" style="gap:14px;">
                <?php if ($userRoleVal === 'admin'): ?>
                    <a href="admin.php" class="dash-btn outline sm" style="color:var(--gold);border-color:rgba(212,175,55,0.4);text-decoration:none;">
                        ⚡ Admin Panel
                    </a>
                <?php endif; ?>
                <a href="../index.php" class="dash-btn outline sm" style="text-decoration:none;">
                    🌐 Public Site
                </a>
                <button class="dash-btn gold sm" onclick="openModal('walkinModal')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + Walk-In Client
                </button>
                <div class="dash-bell" onclick="toast('Front Desk: All systems online','info')">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span class="dash-bell-dot"></span>
                </div>

                <!-- Dynamic Logged-in Receptionist Profile -->
                <div class="d-none d-md-flex align-items-center gap-2" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);padding:5px 12px;border-radius:12px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#05CD99,#065f46);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:13px;box-shadow:0 0 12px rgba(5,205,153,0.3);">
                        <?php echo strtoupper(substr($currentUserName, 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:12.5px;color:#fff;line-height:1.2;"><?php echo htmlspecialchars($currentUserName); ?></div>
                        <div style="font-size:10.5px;color:#05CD99;font-weight:600;"><?php echo htmlspecialchars($currentUserRole); ?></div>
                    </div>
                </div>
            </div>
        </header>

        <div class="dash-content">

            <!-- ============ FLOOR & QUEUE ============ -->
            <section class="dash-pane active" id="pane-floor">

                <!-- Top Floor Operations Header -->
                <div class="dash-header" style="animation:paneReveal 0.4s ease forwards;">
                    <div>
                        <h3>Live Salon Floor & Chair Status</h3>
                        <p>Real-time occupancy for Chairs 1 to 6 and today's active service queue.</p>
                    </div>
                    <button class="dash-btn gold" onclick="openModal('walkinModal')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        New Walk-In Client
                    </button>
                </div>

                <!-- 4 Quick KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="dash-stat-card green-accent">
                            <div>
                                <div class="dash-stat-label">Chairs In Service</div>
                                <div class="dash-stat-val" style="color:var(--green);"><?php echo $today_active_in_chair; ?> <span style="font-size:14px;color:var(--text-secondary);font-weight:500;">/ 6 Active</span></div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Clients currently seated</div>
                            </div>
                            <div class="dash-stat-icon green">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="dash-stat-label">Waiting in Lounge</div>
                                <div class="dash-stat-val" style="color:var(--blue);"><?php echo $today_waiting_count; ?> <span style="font-size:14px;color:var(--text-secondary);font-weight:500;">In Queue</span></div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Next in line for chairs</div>
                            </div>
                            <div class="dash-stat-icon blue">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="dash-stat-label">Completed Visits Today</div>
                                <div class="dash-stat-val" style="color:var(--gold);"><?php echo $today_done_count; ?> <span style="font-size:14px;color:var(--text-secondary);font-weight:500;">Services</span></div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Checked out & paid</div>
                            </div>
                            <div class="dash-stat-icon gold">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="dash-stat-card gold-accent">
                            <div>
                                <div class="dash-stat-label">Today's Counter Collections</div>
                                <div class="dash-stat-val" style="color:var(--gold);">Rs. <?php echo number_format($today_sales_total); ?></div>
                                <div style="font-size:11px;color:var(--green);margin-top:4px;font-weight:600;">Gross Walk-In & Appts</div>
                            </div>
                            <div class="dash-stat-icon gold">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Digital Floor Clock & Status Banner -->
                <div class="dash-card mb-4" style="background:linear-gradient(135deg, rgba(5,205,153,0.08), rgba(22,25,34,0.95));border:1px solid rgba(5,205,153,0.25);padding:14px 22px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:12px;background:rgba(5,205,153,0.15);border:1px solid var(--green);display:flex;align-items:center;justify-content:center;font-size:20px;">
                                ⏱️
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--green);font-weight:700;letter-spacing:1px;text-transform:uppercase;">LIVE SALON FLOOR TERMINAL &bull; PECHS KARACHI</div>
                                <div style="font-size:14px;color:#ffffff;font-weight:600;" id="liveFloorClock"><?php echo date('h:i:s A'); ?> PKT &bull; <?php echo date('l, d M Y'); ?></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="dash-badge green" style="font-size:11px;">🟢 Floor Terminal Online</span>
                            <span class="dash-badge blue" style="font-size:11px;">6 Chairs Synchronized</span>
                        </div>
                    </div>
                </div>

                <!-- 6 Chairs Live Grid -->
                <div class="row g-3 mb-4">
                    <?php for ($chair = 1; $chair <= 6; $chair++):
                        $isOccupied = isset($occupied_chairs[$chair]);
                        $appt = $isOccupied ? $occupied_chairs[$chair] : null;
                        $cBorder = $isOccupied ? 'rgba(5,205,153,0.5)' : 'rgba(255,255,255,0.08)';
                    ?>
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dash-card text-center p-3 h-100" style="border:1px solid <?php echo $cBorder; ?>;background:<?php echo $isOccupied ? 'rgba(5,205,153,0.04)' : 'rgba(22,25,34,0.7)'; ?>;position:relative;overflow:hidden;border-radius:14px;">
                                <?php if ($isOccupied): ?>
                                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:var(--green);"></div>
                                    <div style="font-size:28px;margin-bottom:4px;">💈</div>
                                    <div style="font-weight:700;font-size:13px;color:var(--green);">Chair #<?php echo $chair; ?></div>
                                    <div class="mt-1 mb-1" style="background:rgba(5,205,153,0.12);border:1px solid rgba(5,205,153,0.3);border-radius:20px;padding:2px 6px;font-size:10px;color:var(--green);font-weight:600;display:inline-block;">
                                        ⏱️ In Chair &bull; <?php echo htmlspecialchars($appt['appointment_time'] ?? 'Active'); ?>
                                    </div>
                                    <div style="font-size:12.5px;font-weight:700;color:var(--text-primary);margin-top:2px;">
                                        <?php echo htmlspecialchars($appt['client_name']); ?>
                                    </div>
                                    <div style="font-size:11px;color:var(--text-secondary);margin-top:1px;">
                                        <?php echo htmlspecialchars($appt['service_name']); ?>
                                    </div>
                                    <div style="font-size:10.5px;color:var(--gold);margin-top:1px;">
                                        Stylist: <?php echo htmlspecialchars($appt['stylist_name']); ?>
                                    </div>
                                    <div style="display:flex;gap:4px;justify-content:center;margin-top:8px;">
                                        <a href="reception.php?rec_action=done&id=<?php echo $appt['id']; ?>" class="dash-btn sm" style="font-size:10px;padding:3px 8px;background:var(--gold);color:#000;font-weight:700;">
                                            Complete
                                        </a>
                                        <button type="button" class="dash-btn outline sm" style="font-size:10px;padding:3px 6px;" onclick="printReceiptSlip('<?php echo htmlspecialchars($appt['booking_ref']); ?>', '<?php echo addslashes($appt['client_name']); ?>', '<?php echo addslashes($appt['client_phone'] ?? ''); ?>', '<?php echo addslashes($appt['service_name']); ?>', '<?php echo addslashes($appt['stylist_name'] ?? ''); ?>', '<?php echo addslashes($appt['token'] ?? ''); ?>', '<?php echo $appt['price']; ?>', '<?php echo addslashes($appt['payment_mode'] ?: 'Cash'); ?>', '<?php echo $appt['appointment_date']; ?>', '<?php echo addslashes($appt['appointment_time'] ?? ''); ?>')">
                                            Slip
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size:28px;margin-bottom:4px;">💺</div>
                                    <div style="font-weight:700;font-size:13px;color:var(--text-primary);">Chair #<?php echo $chair; ?></div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">Vacant</div>
                                    <button type="button" class="dash-btn outline sm mt-2" style="font-size:10px;padding:2px 8px;border-color:rgba(5,205,153,0.3);color:var(--green);" onclick="openWalkinForChair(<?php echo $chair; ?>)">
                                        + Seat Walk-In
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Today's Queue Table -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <div>
                            <h5>Today's Appointment Queue</h5>
                            <p style="font-size:12px;color:var(--text-muted);margin:0;">Live queue for <?php echo date('l, F d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="dash-table-bar">
                        <div class="table-bar-left">
                            <span class="scroll-pill-hint">⇄ Live Floor Queue</span>
                            <span style="color:var(--text-muted);font-size:11px;">(Drag horizontally to view full queue)</span>
                        </div>
                        <div class="table-bar-right">
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('recQueueTableWrap', -300)">◀ Left</button>
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('recQueueTableWrap', 300)">Right ▶</button>
                        </div>
                    </div>
                    <div class="dash-top-scroll">
                        <div class="dash-top-scroll-inner"></div>
                    </div>
                    <div class="dash-table-wrap" id="recQueueTableWrap">
                        <table class="dash-table" style="min-width:1080px;">
                            <thead>
                                <tr>
                                    <th>Ref / Token</th>
                                    <th>Client Details</th>
                                    <th>Service</th>
                                    <th>Stylist</th>
                                    <th>Time</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($today_queue_q && mysqli_num_rows($today_queue_q) > 0): ?>
                                    <?php while ($tq = mysqli_fetch_assoc($today_queue_q)):
                                        $st = $tq['status'];
                                        $bClass = ($st === 'Completed') ? 'gold' : (($st === 'In Chair') ? 'green' : (($st === 'Cancelled') ? 'red' : 'blue'));
                                        $tqEmail = $tq['client_email'] ?? '';
                                        $tqPhone = $tq['client_phone'] ?? '';
                                    ?>
                                        <tr class="rec-queue-row" data-search="<?php echo strtolower(htmlspecialchars($tq['client_name'] . ' ' . $tqPhone . ' ' . $tqEmail . ' ' . $tq['service_name'] . ' ' . $tq['stylist_name'] . ' ' . $tq['booking_ref'] . ' ' . $tq['token'])); ?>">
                                            <td>
                                                <strong style="color:var(--gold);font-family:monospace;font-size:12px;"><?php echo htmlspecialchars($tq['booking_ref']); ?></strong>
                                                <div style="font-size:10px;color:var(--text-muted);">Token: <?php echo htmlspecialchars($tq['token']); ?></div>
                                            </td>
                                            <td>
                                                <strong style="color:var(--text-primary);font-size:13px;display:block;"><?php echo htmlspecialchars($tq['client_name']); ?></strong>
                                                <span style="font-size:11px;color:var(--text-secondary);display:block;"><?php echo htmlspecialchars($tqPhone); ?></span>
                                                <?php if (!empty($tqEmail)): ?>
                                                    <span style="font-size:10.5px;color:var(--gold);opacity:0.85;display:block;"><?php echo htmlspecialchars($tqEmail); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="color:var(--text-primary);font-size:13px;">
                                                <?php echo htmlspecialchars($tq['service_name']); ?>
                                            </td>
                                            <td><span class="dash-badge blue" style="font-size:11px;"><?php echo htmlspecialchars($tq['stylist_name']); ?></span></td>
                                            <td style="color:var(--text-primary);font-weight:600;font-size:12.5px;"><?php echo htmlspecialchars($tq['appointment_time']); ?></td>
                                            <td style="color:var(--gold);font-weight:700;">Rs. <?php echo number_format($tq['price']); ?></td>
                                            <td><span class="dash-badge <?php echo $bClass; ?>"><?php echo htmlspecialchars($st); ?></span></td>
                                            <td style="text-align:right;">
                                                <?php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $tqPhone);
                                                $waPhone = (str_starts_with($cleanPhone, '03')) ? ('92' . substr($cleanPhone, 1)) : $cleanPhone;
                                                $waMsg = urlencode("Salam " . $tq['client_name'] . ", your reservation at Elegance Salon Karachi is confirmed! Token: " . ($tq['token'] ?: 'TK-01') . ", Stylist: " . $tq['stylist_name'] . ".");
                                                ?>
                                                <?php if (!empty($waPhone)): ?>
                                                    <a href="https://wa.me/<?php echo $waPhone; ?>?text=<?php echo $waMsg; ?>" target="_blank" class="dash-btn outline sm me-1" style="color:var(--green);border-color:rgba(5,205,153,0.35);" title="Send WhatsApp Confirmation">
                                                        💬 WA
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($st === 'Confirmed' || $st === 'Booked'): ?>
                                                    <a href="reception.php?rec_action=seat&id=<?php echo $tq['id']; ?>" class="dash-btn outline sm me-1" style="color:var(--green);border-color:rgba(5,205,153,0.4);">Seat</a>
                                                <?php endif; ?>
                                                <?php if ($st === 'In Chair'): ?>
                                                    <a href="reception.php?rec_action=done&id=<?php echo $tq['id']; ?>" class="dash-btn sm me-1" style="background:var(--gold);color:#000;font-weight:700;">Complete</a>
                                                <?php endif; ?>
                                                <button type="button" class="dash-btn outline sm me-1" style="color:var(--gold);border-color:var(--gold);" data-appt="<?php echo htmlspecialchars(json_encode($tq, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>" onclick="openEditApptRecFromBtn(this)">
                                                    Edit
                                                </button>
                                                <button type="button" class="dash-btn outline sm me-1" onclick="printReceiptSlip('<?php echo htmlspecialchars($tq['booking_ref']); ?>', '<?php echo addslashes($tq['client_name']); ?>', '<?php echo addslashes($tqPhone); ?>', '<?php echo addslashes($tq['service_name']); ?>', '<?php echo addslashes($tq['stylist_name']); ?>', '<?php echo addslashes($tq['token'] ?? ''); ?>', '<?php echo $tq['price']; ?>', '<?php echo addslashes($tq['payment_mode']); ?>', '<?php echo $tq['appointment_date']; ?>', '<?php echo addslashes($tq['appointment_time']); ?>')">
                                                    Slip
                                                </button>
                                                <?php if ($st !== 'Cancelled' && $st !== 'Completed'): ?>
                                                    <a href="reception.php?rec_action=cancel&id=<?php echo $tq['id']; ?>" onclick="return confirm('Cancel appointment?')" class="dash-btn outline sm" style="color:var(--red);border-color:rgba(239,68,68,0.3);">Cancel</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted);">No bookings scheduled for today. Click "+ Walk-In Client" to add one.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ============ BOOKINGS ============ -->
            <section class="dash-pane" id="pane-bookings">
                <div class="dash-header">
                    <div>
                        <h3>All Appointments & Bookings</h3>
                        <p>Complete historical, active, and upcoming client reservations across Elegance Karachi salon.</p>
                    </div>
                    <button class="dash-btn gold" onclick="openModal('walkinModal')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        + Walk-In Client
                    </button>
                </div>
                <div class="dash-card mb-3" style="padding:16px 20px;">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <div class="dash-search" style="width:100%;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="recApptSearch" placeholder="Search phone (0300...), email, client name, ref, token..." oninput="filterRecApptTable()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="recApptFilter" class="dash-select" onchange="filterRecApptTable()" style="width:100%;">
                                <option value="all">All Statuses</option>
                                <option value="In Chair">In Chair</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span style="font-size:12px;color:var(--text-muted);background:rgba(255,255,255,0.04);padding:6px 14px;border-radius:20px;border:1px solid rgba(255,255,255,0.08);">
                                Total Records: <strong style="color:var(--gold);"><?php echo ($all_appts_q) ? mysqli_num_rows($all_appts_q) : 0; ?></strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table Navigation & Scroller Bar -->
                <div class="dash-table-bar">
                    <div class="table-bar-left">
                        <span class="scroll-pill-hint">⇄ Horizontal Scroll & Drag</span>
                        <span style="color:var(--text-muted);font-size:11px;">(Use buttons or drag horizontally)</span>
                    </div>
                    <div class="table-bar-right">
                        <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('recBookingsTableWrap', -320)">◀ Scroll Left</button>
                        <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('recBookingsTableWrap', 320)">Scroll Right ▶</button>
                    </div>
                </div>
                <div class="dash-top-scroll">
                    <div class="dash-top-scroll-inner"></div>
                </div>
                <div class="dash-table-wrap" id="recBookingsTableWrap">
                    <table class="dash-table" style="min-width:1120px;">
                        <thead>
                            <tr>
                                <th style="width:120px;">Booking Ref</th>
                                <th style="min-width:190px;">Client Profile</th>
                                <th style="min-width:150px;">Treatment</th>
                                <th style="min-width:130px;">Barber</th>
                                <th style="min-width:130px;">Schedule</th>
                                <th style="min-width:110px;">Billing</th>
                                <th style="min-width:110px;">Status</th>
                                <th class="col-actions" style="min-width:240px;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recAllApptsBody">
                            <?php if ($all_appts_q && mysqli_num_rows($all_appts_q) > 0): ?>
                                <?php while ($ap = mysqli_fetch_assoc($all_appts_q)):
                                    $st = $ap['status'];
                                    $bClass = ($st === 'Completed') ? 'gold' : (($st === 'In Chair') ? 'green' : (($st === 'Cancelled') ? 'red' : 'blue'));
                                    $apEmail = $ap['client_email'] ?? '';
                                    $apPhone = $ap['client_phone'] ?? '';
                                    $cInitials = strtoupper(substr($ap['client_name'], 0, 2));
                                    $cleanSrvName = clean_text($ap['service_name']);
                                ?>
                                    <tr class="rec-appt-row" data-status="<?php echo htmlspecialchars($st); ?>" data-search="<?php echo strtolower(htmlspecialchars($ap['client_name'] . ' ' . $apPhone . ' ' . $apEmail . ' ' . $cleanSrvName . ' ' . $ap['stylist_name'] . ' ' . $ap['booking_ref'] . ' ' . ($ap['token'] ?? ''))); ?>">
                                        <td>
                                            <span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--gold);background:rgba(212,175,55,0.08);padding:3px 8px;border-radius:5px;border:1px solid rgba(212,175,55,0.2);display:inline-block;">
                                                <?php echo htmlspecialchars($ap['booking_ref'] ?: 'ELG-'.$ap['id']); ?>
                                            </span>
                                            <?php if (!empty($ap['token'])): ?>
                                                <div style="font-size:10.5px;color:var(--text-muted);margin-top:3px;">Token: <strong><?php echo htmlspecialchars($ap['token']); ?></strong></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <div style="width:36px;height:36px;border-radius:10px;background:var(--gold-soft);border:1px solid rgba(212,175,55,0.25);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);flex-shrink:0;">
                                                    <?php echo $cInitials; ?>
                                                </div>
                                                <div>
                                                    <strong style="color:var(--text-primary);font-size:13.5px;line-height:1.2;display:block;">
                                                        <?php echo safe_html($ap['client_name']); ?>
                                                    </strong>
                                                    <span style="font-size:11.5px;color:var(--text-secondary);display:block;"><?php echo safe_html($apPhone); ?></span>
                                                    <?php if (!empty($apEmail)): ?>
                                                        <span style="font-size:11px;color:var(--gold);opacity:0.85;display:block;"><?php echo safe_html($apEmail); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong style="color:var(--text-primary);font-size:13px;display:block;"><?php echo safe_html($cleanSrvName); ?></strong>
                                            <span style="font-size:11px;color:var(--gold);">Chair #<?php echo $ap['chair_no'] ?: '1'; ?></span>
                                        </td>
                                        <td>
                                            <span class="dash-badge blue" style="font-size:11px;"><?php echo htmlspecialchars($ap['stylist_name']); ?></span>
                                        </td>
                                        <td>
                                            <div style="color:var(--text-primary);font-weight:600;font-size:13px;"><?php echo date('M d, Y', strtotime($ap['appointment_date'])); ?></div>
                                            <div style="font-size:11.5px;color:var(--text-muted);"><?php echo htmlspecialchars($ap['appointment_time']); ?></div>
                                        </td>
                                        <td>
                                            <div style="color:var(--gold);font-weight:700;font-size:13.5px;">Rs. <?php echo number_format($ap['price']); ?></div>
                                            <span class="dash-badge <?php echo ($ap['payment_status'] === 'Paid') ? 'green' : 'orange'; ?>" style="font-size:9.5px;padding:1px 6px;margin-top:2px;">
                                                <?php echo htmlspecialchars($ap['payment_status'] ?? 'Unpaid'); ?> &bull; <?php echo htmlspecialchars($ap['payment_mode'] ?: 'Cash'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="dash-badge <?php echo $bClass; ?>">
                                                <?php echo htmlspecialchars($st); ?>
                                            </span>
                                        </td>
                                        <!-- Actions (Sticky Right Column) -->
                                        <td class="col-actions" style="text-align:right;">
                                            <div class="dash-action-group" style="justify-content:flex-end;">
                                                <?php if ($st === 'Confirmed' || $st === 'Booked'): ?>
                                                    <a href="reception.php?rec_action=seat&id=<?php echo $ap['id']; ?>" class="dash-btn outline sm" style="color:var(--green);border-color:rgba(5,205,153,0.4);" title="Seat Client on Chair">Seat</a>
                                                <?php elseif ($st === 'In Chair'): ?>
                                                    <a href="reception.php?rec_action=done&id=<?php echo $ap['id']; ?>" class="dash-btn sm" style="background:var(--gold);color:#050505;font-weight:700;" title="Mark Service as Completed">Done</a>
                                                <?php endif; ?>
                                                <button type="button" class="dash-btn outline sm" style="color:var(--gold);border-color:rgba(212,175,55,0.4);" data-appt="<?php echo htmlspecialchars(json_encode($ap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>" onclick="openEditApptRecFromBtn(this)">
                                                    Edit
                                                </button>
                                                <button type="button" class="dash-btn outline sm" style="color:var(--cyan, #06b6d4);border-color:rgba(6,182,212,0.4);" onclick="printReceiptSlip('<?php echo htmlspecialchars($ap['booking_ref']); ?>', '<?php echo addslashes($ap['client_name']); ?>', '<?php echo addslashes($apPhone); ?>', '<?php echo addslashes($ap['service_name']); ?>', '<?php echo addslashes($ap['stylist_name']); ?>', '<?php echo addslashes($ap['token'] ?? ''); ?>', '<?php echo $ap['price']; ?>', '<?php echo addslashes($ap['payment_mode']); ?>', '<?php echo $ap['appointment_date']; ?>', '<?php echo addslashes($ap['appointment_time']); ?>')">
                                                    Slip
                                                </button>
                                                <a href="reception.php?delete_appointment_rec=<?php echo $ap['id']; ?>" onclick="return confirm('Delete appointment record permanently?')" class="dash-btn outline sm" style="color:var(--red);border-color:rgba(239,68,68,0.3);text-decoration:none;">
                                                    Remove
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No appointments recorded. Click "+ Walk-In Client" to add one.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ============ CLIENTS ============ -->
            <section class="dash-pane" id="pane-clients">
                <div class="dash-header">
                    <div>
                        <h3>Registered Clients Directory</h3>
                        <p>Customer profiles, contact details, visit counts, and total spending.</p>
                    </div>
                    <button class="dash-btn gold" onclick="openModal('addClientRecModal')">+ Register New Client</button>
                </div>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>#</th><th>Client Name</th><th>Email</th><th>Phone</th><th>Total Visits</th><th>Total Spent</th><th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cCount = 1;
                            if ($clients_dir_q && mysqli_num_rows($clients_dir_q) > 0):
                                while ($c = mysqli_fetch_assoc($clients_dir_q)):
                            ?>
                                <tr>
                                    <td><span style="color:var(--text-muted);font-size:12px;">#<?php echo $cCount++; ?></span></td>
                                    <td><strong style="color:var(--text-primary);"><?php echo htmlspecialchars($c['name']); ?></strong></td>
                                    <td style="color:var(--text-secondary);"><?php echo htmlspecialchars($c['email']); ?></td>
                                    <td style="color:var(--text-secondary);"><?php echo htmlspecialchars($c['phone']); ?></td>
                                    <td><span class="dash-badge blue"><?php echo $c['total_visits'] ?? 0; ?> Visits</span></td>
                                    <td style="color:var(--gold);font-weight:700;">Rs. <?php echo number_format($c['total_spent'] ?? 0); ?></td>
                                    <td style="color:var(--text-muted);font-size:12px;"><?php echo !empty($c['created_at']) ? date('M d, Y', strtotime($c['created_at'])) : '—'; ?></td>
                                </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                                <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">No registered clients found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ============ POS & BILLING ============ -->
            <section class="dash-pane" id="pane-pos">
                <div class="dash-header">
                    <div>
                        <h3>Point of Sale (POS) & Collections</h3>
                        <p>Completed services, revenue transactions, payment methods, and invoices.</p>
                    </div>
                </div>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Booking Ref</th><th>Client</th><th>Service Rendered</th><th>Amount Paid</th><th>Payment Mode</th><th>Date</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pos_q && mysqli_num_rows($pos_q) > 0): ?>
                                <?php while ($p = mysqli_fetch_assoc($pos_q)): ?>
                                    <tr>
                                        <td><strong style="color:var(--gold);font-family:monospace;"><?php echo htmlspecialchars($p['booking_ref']); ?></strong></td>
                                        <td><strong style="color:var(--text-primary);"><?php echo htmlspecialchars($p['client_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($p['service_name']); ?></td>
                                        <td style="color:var(--gold);font-weight:700;">Rs. <?php echo number_format($p['price']); ?></td>
                                        <td><span class="dash-badge green"><?php echo htmlspecialchars($p['payment_mode'] ?: 'Cash'); ?></span></td>
                                        <td style="color:var(--text-muted);font-size:12.5px;"><?php echo date('M d, Y', strtotime($p['appointment_date'])); ?></td>
                                        <td>
                                            <button type="button" class="dash-btn outline sm" onclick="printReceiptSlip('<?php echo htmlspecialchars($p['booking_ref']); ?>', '<?php echo addslashes($p['client_name']); ?>', '<?php echo addslashes($p['client_phone'] ?? ''); ?>', '<?php echo addslashes($p['service_name']); ?>', '<?php echo addslashes($p['stylist_name'] ?? ''); ?>', '<?php echo addslashes($p['token'] ?? ''); ?>', '<?php echo $p['price']; ?>', '<?php echo addslashes($p['payment_mode'] ?: 'Cash'); ?>', '<?php echo $p['appointment_date']; ?>', '<?php echo addslashes($p['appointment_time'] ?? ''); ?>')">
                                                🖨️ Print Slip
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">No completed transactions recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </main>
</div>

<!-- ===== MODALS ===== -->

<!-- Walk-In Modal -->
<div class="dash-modal-overlay" id="walkinModal">
    <div class="dash-modal" style="max-width:620px;">
        <div class="dash-modal-header">
            <div>
                <h5>⚡ New Walk-In Appointment</h5>
                <p style="font-size:12px;color:var(--text-muted);margin:0;">Register immediate walk-in client and assign chair</p>
            </div>
            <button class="dash-modal-close" onclick="closeModal('walkinModal')">&times;</button>
        </div>
        <form action="reception.php" method="POST">
            <div class="dash-modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label">Client Name *</label>
                        <input type="text" name="client_name" class="dash-input" placeholder="e.g. Ali Ahmed" required>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Phone *</label>
                        <input type="tel" name="client_phone" class="dash-input" placeholder="e.g. 03001234567" maxlength="11" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="dash-form-label">Client Email (Optional)</label>
                    <input type="email" name="client_email" class="dash-input" placeholder="e.g. client@example.com">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label">Service Package *</label>
                        <select class="dash-select" name="service_name" id="recWalkinService" onchange="updateRecPrice(this)" style="width:100%;" required>
                            <option value="" disabled selected>-- Select Service --</option>
                            <?php foreach ($services_list as $sl): ?>
                                <option value="<?php echo htmlspecialchars($sl['name']); ?>" data-price="<?php echo $sl['price']; ?>">
                                    <?php echo htmlspecialchars($sl['name']); ?> (Rs. <?php echo number_format($sl['price']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Assigned Stylist *</label>
                        <select class="dash-select" name="stylist_name" style="width:100%;" required>
                            <?php foreach ($staff_list as $stl): ?>
                                <option value="<?php echo htmlspecialchars($stl['name']); ?>">
                                    <?php echo htmlspecialchars($stl['name']); ?> (<?php echo htmlspecialchars($stl['specialization']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="dash-form-label">Assign Chair *</label>
                        <select class="dash-select" name="chair_no" id="recWalkinChair" style="width:100%;">
                            <option value="1">Chair #1 (VIP Master)</option>
                            <option value="2">Chair #2 (Styling)</option>
                            <option value="3">Chair #3 (Grooming)</option>
                            <option value="4">Chair #4 (Executive)</option>
                            <option value="5">Chair #5 (Color Bar)</option>
                            <option value="6">Chair #6 (Shampoo Wash)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="dash-form-label">Service Price (PKR) *</label>
                        <input type="number" name="price" id="recWalkinPrice" class="dash-input" placeholder="3500" value="3500" oninput="calculateWalkinFinalPrice()" required>
                    </div>
                    <div class="col-md-4">
                        <label class="dash-form-label">Payment Mode *</label>
                        <select class="dash-select" name="payment_mode" style="width:100%;">
                            <option value="Cash">Cash</option>
                            <option value="JazzCash">JazzCash</option>
                            <option value="EasyPaisa">EasyPaisa</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label">Apply Promo / Discount</label>
                        <select id="recWalkinDiscount" class="dash-select" onchange="calculateWalkinFinalPrice()" style="width:100%;">
                            <option value="0">Standard Rate (0% Off)</option>
                            <option value="10">First Visit Promo (10% Off)</option>
                            <option value="15">VIP Loyalty (15% Off)</option>
                            <option value="20">Special Event (20% Off)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Net Payable at Desk</label>
                        <div class="p-2 rounded" style="background:rgba(212,175,55,0.08);border:1px dashed var(--border-gold);display:flex;justify-content:space-between;align-items:center;height:42px;">
                            <span style="font-size:11.5px;color:var(--text-secondary);">Total with GST:</span>
                            <strong style="color:var(--gold);font-size:14px;" id="walkinFinalPriceDisplay">Rs. 3,500</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn outline" onclick="closeModal('walkinModal')">Cancel</button>
                <button type="submit" name="walkin_booking_submit" class="dash-btn gold">Confirm & Seat Client</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Appointment Modal (Reception) -->
<div class="dash-modal-overlay" id="editApptRecModal">
    <div class="dash-modal" style="max-width:650px;">
        <div class="dash-modal-header">
            <div>
                <h5>Edit Appointment Details</h5>
                <p style="font-size:12px;color:var(--text-muted);margin:0;">Update time, assigned chair, stylist, price or status</p>
            </div>
            <button class="dash-modal-close" onclick="closeModal('editApptRecModal')">&times;</button>
        </div>
        <form action="reception.php" method="POST">
            <input type="hidden" name="appointment_id" id="editRecApptId">
            <div class="dash-modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label">Client Name *</label>
                        <input type="text" name="client_name" id="editRecApptName" class="dash-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Phone *</label>
                        <input type="tel" name="client_phone" id="editRecApptPhone" class="dash-input" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="dash-form-label">Client Email</label>
                    <input type="email" name="client_email" id="editRecApptEmail" class="dash-input">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label">Service Package *</label>
                        <select class="dash-select" name="service_name" id="editRecApptService" onchange="updateRecEditPrice(this)" style="width:100%;" required>
                            <?php foreach ($services_list as $sl): ?>
                                <option value="<?php echo htmlspecialchars($sl['name']); ?>" data-price="<?php echo $sl['price']; ?>">
                                    <?php echo htmlspecialchars($sl['name']); ?> (Rs. <?php echo number_format($sl['price']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Assigned Stylist *</label>
                        <select class="dash-select" name="stylist_name" id="editRecApptStylist" style="width:100%;" required>
                            <?php foreach ($staff_list as $stl): ?>
                                <option value="<?php echo htmlspecialchars($stl['name']); ?>">
                                    <?php echo htmlspecialchars($stl['name']); ?> (<?php echo htmlspecialchars($stl['specialization']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label">Date *</label>
                        <input type="date" name="appointment_date" id="editRecApptDate" class="dash-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Time *</label>
                        <input type="text" name="appointment_time" id="editRecApptTime" class="dash-input" placeholder="e.g. 03:30 PM" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="dash-form-label">Chair No *</label>
                        <select class="dash-select" name="chair_no" id="editRecApptChair" style="width:100%;">
                            <option value="1">Chair #1</option>
                            <option value="2">Chair #2</option>
                            <option value="3">Chair #3</option>
                            <option value="4">Chair #4</option>
                            <option value="5">Chair #5</option>
                            <option value="6">Chair #6</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="dash-form-label">Amount (PKR) *</label>
                        <input type="number" name="price" id="editRecApptPrice" class="dash-input" required>
                    </div>
                    <div class="col-md-4">
                        <label class="dash-form-label">Payment Mode *</label>
                        <select class="dash-select" name="payment_mode" id="editRecApptPayMode" style="width:100%;">
                            <option value="Cash">Cash</option>
                            <option value="JazzCash">JazzCash</option>
                            <option value="EasyPaisa">EasyPaisa</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="dash-form-label">Payment Status *</label>
                        <select class="dash-select" name="payment_status" id="editRecApptPayStatus" style="width:100%;">
                            <option value="Unpaid">Unpaid</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label">Appointment Status *</label>
                        <select class="dash-select" name="status" id="editRecApptStatus" style="width:100%;">
                            <option value="In Chair">In Chair</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn outline" onclick="closeModal('editApptRecModal')">Cancel</button>
                <button type="submit" name="update_appointment_rec_submit" class="dash-btn gold">Update Appointment</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Client Modal -->
<div class="dash-modal-overlay" id="addClientRecModal">
    <div class="dash-modal" style="max-width:500px;">
        <div class="dash-modal-header">
            <div>
                <h5>Register New Customer</h5>
                <p style="font-size:12px;color:var(--text-muted);margin:0;">Create a permanent client profile in database</p>
            </div>
            <button class="dash-modal-close" onclick="closeModal('addClientRecModal')">&times;</button>
        </div>
        <form action="reception.php" method="POST">
            <div class="dash-modal-body">
                <div class="mb-3">
                    <label class="dash-form-label">Customer Name *</label>
                    <input type="text" name="client_name" class="dash-input" placeholder="e.g. Ali Ahmed" required>
                </div>
                <div class="mb-3">
                    <label class="dash-form-label">Phone Number *</label>
                    <input type="tel" name="client_phone" class="dash-input" placeholder="e.g. 03001234567" maxlength="11" required>
                </div>
                <div class="mb-3">
                    <label class="dash-form-label">Email Address (Optional)</label>
                    <input type="email" name="client_email" class="dash-input" placeholder="e.g. client@example.com">
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn outline" onclick="closeModal('addClientRecModal')">Cancel</button>
                <button type="submit" name="add_client_rec_submit" class="dash-btn gold">Register Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Receipt Modal (Thermal POS Slip) -->
<div class="dash-modal-overlay" id="receiptModal">
    <div class="dash-modal" style="max-width:440px;">
        <div class="dash-modal-header">
            <h5 style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--gold);">🧾</span> Salon POS Receipt Slip
            </h5>
            <button class="dash-modal-close" onclick="closeModal('receiptModal')">&times;</button>
        </div>
        <div class="dash-modal-body" style="background:#0c0a07;padding:16px 20px;">
            <div id="receiptContent" class="thermal-receipt-paper"></div>
        </div>
        <div class="dash-modal-footer">
            <button type="button" class="dash-btn outline" onclick="closeModal('receiptModal')">Close</button>
            <button type="button" class="dash-btn outline" style="color:var(--cyan, #06b6d4);border-color:rgba(6,182,212,0.4);" onclick="downloadReceiptPDF()">📥 Download PDF</button>
            <button type="button" class="dash-btn gold" onclick="printCleanReceipt()">🖨️ Print Slip (80mm)</button>
        </div>
    </div>
</div>

<script src="html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="dashboard.js?v=<?php echo time(); ?>"></script>
<script>
    function updateRecPrice(sel) {
        var opt = sel.options[sel.selectedIndex];
        var p = opt.getAttribute('data-price');
        if (p && document.getElementById('recWalkinPrice')) {
            document.getElementById('recWalkinPrice').value = p;
            calculateWalkinFinalPrice();
        }
    }

    function calculateWalkinFinalPrice() {
        var basePrice = parseFloat(document.getElementById('recWalkinPrice') ? document.getElementById('recWalkinPrice').value : 0) || 0;
        var discountPct = parseFloat(document.getElementById('recWalkinDiscount') ? document.getElementById('recWalkinDiscount').value : 0) || 0;
        var finalPrice = basePrice - (basePrice * (discountPct / 100));
        var display = document.getElementById('walkinFinalPriceDisplay');
        if (display) {
            display.innerText = 'Rs. ' + Math.round(finalPrice).toLocaleString();
        }
    }

    // Live Digital Floor Clock
    setInterval(function() {
        var clockEl = document.getElementById('liveFloorClock');
        if (clockEl) {
            var now = new Date();
            var timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            var dateStr = now.toLocaleDateString('en-US', { weekday: 'long', day: '2-digit', month: 'short', year: 'numeric' });
            clockEl.innerHTML = timeStr + ' PKT &bull; ' + dateStr;
        }
    }, 1000);

    function updateRecEditPrice(sel) {
        var opt = sel.options[sel.selectedIndex];
        var p = opt.getAttribute('data-price');
        if (p && document.getElementById('editRecApptPrice')) {
            document.getElementById('editRecApptPrice').value = p;
        }
    }

    function openEditApptRecFromBtn(btn) {
        try {
            var raw = btn.getAttribute('data-appt');
            var ap = JSON.parse(raw);
            document.getElementById('editRecApptId').value = ap.id || '';
            document.getElementById('editRecApptName').value = ap.client_name || '';
            document.getElementById('editRecApptPhone').value = ap.client_phone || '';
            document.getElementById('editRecApptEmail').value = ap.client_email || '';
            document.getElementById('editRecApptService').value = ap.service_name || '';
            document.getElementById('editRecApptStylist').value = ap.stylist_name || '';
            document.getElementById('editRecApptDate').value = ap.appointment_date || '';
            document.getElementById('editRecApptTime').value = ap.appointment_time || '';
            document.getElementById('editRecApptChair').value = ap.chair_no || '1';
            document.getElementById('editRecApptPrice').value = ap.price || '';
            document.getElementById('editRecApptPayMode').value = ap.payment_mode || 'Cash';
            document.getElementById('editRecApptPayStatus').value = ap.payment_status || 'Unpaid';
            document.getElementById('editRecApptStatus').value = ap.status || 'Confirmed';
            openModal('editApptRecModal');
        } catch(e) {
            console.error("Error opening edit appointment modal:", e);
        }
    }

    function openWalkinForChair(chairNo) {
        var chairSel = document.getElementById('recWalkinChair');
        if (chairSel) {
            chairSel.value = chairNo;
        }
        openModal('walkinModal');
    }

    function filterRecTables(val) {
        var query = (val || '').toLowerCase().trim();

        // Filter Queue Rows
        var queueRows = document.querySelectorAll('.rec-queue-row');
        queueRows.forEach(function(row) {
            var sData = row.getAttribute('data-search') || '';
            row.style.display = (!query || sData.indexOf(query) !== -1) ? '' : 'none';
        });

        // Filter All Bookings Rows
        var apptRows = document.querySelectorAll('.rec-appt-row');
        apptRows.forEach(function(row) {
            var sData = row.getAttribute('data-search') || '';
            row.style.display = (!query || sData.indexOf(query) !== -1) ? '' : 'none';
        });

        // Also sync with recApptSearch input if exists
        var subSearch = document.getElementById('recApptSearch');
        if (subSearch && subSearch.value !== val) {
            subSearch.value = val;
        }
    }

    function filterRecApptTable() {
        var search = (document.getElementById('recApptSearch') ? document.getElementById('recApptSearch').value : '').toLowerCase().trim();
        var filter = (document.getElementById('recApptFilter') ? document.getElementById('recApptFilter').value : 'all');
        var rows = document.querySelectorAll('.rec-appt-row');
        rows.forEach(function(row) {
            var st = row.getAttribute('data-status');
            var sData = row.getAttribute('data-search') || '';
            var matchStatus = (filter === 'all') || (st === filter);
            var matchSearch = !search || (sData.indexOf(search) !== -1);
            if (matchStatus && matchSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function printReceiptSlip(ref, client, phone, service, stylist, token, price, payment, date, time) {
        var el = document.getElementById('receiptContent');
        if (!el) return;

        var numPrice = Number(price) || 0;
        var formattedPrice = numPrice.toLocaleString('en-PK');
        var displayToken = token ? token : 'TK-' + (ref ? ref.replace('ELG-', '') : '01');
        var displayDate = date || '<?php echo date("Y-m-d"); ?>';
        var displayTime = time || '<?php echo date("h:i A"); ?>';

        el.innerHTML = `
            <div style="text-align:center;">
                <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;color:#555 !important;">✦ SALON SALES SLIP ✦</div>
                <h3 style="margin:2px 0;font-size:19px;font-weight:900;letter-spacing:2px;color:#000 !important;">ELEGANCE SALON</h3>
                <div style="font-size:10.5px;font-weight:700;color:#222 !important;letter-spacing:0.5px;">LUXURY MEN'S LOUNGE KARACHI</div>
                <div style="font-size:9.5px;color:#555 !important;margin-top:1px;">Main Tariq Road, Block 2 PECHS &bull; Tel: 0300-1234567</div>
            </div>

            <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

            <!-- Token Highlight Box -->
            <div class="receipt-token-badge" style="margin:6px auto;padding:5px 14px;font-size:15px;">
                TOKEN: ${displayToken}
            </div>

            <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

            <!-- Client & Appointment Meta Table -->
            <table style="width:100%;border-collapse:collapse;font-size:11.5px;line-height:1.5;">
                <tr><td style="color:#555;padding:1px 0;">Invoice No:</td><td style="text-align:right;font-weight:700;color:#000;padding:1px 0;">${ref || 'ELG-0000'}</td></tr>
                <tr><td style="color:#555;padding:1px 0;">Date / Time:</td><td style="text-align:right;color:#000;padding:1px 0;">${displayDate} &bull; ${displayTime}</td></tr>
                <tr><td style="color:#555;padding:1px 0;">Customer:</td><td style="text-align:right;font-weight:700;color:#000;padding:1px 0;">${client || 'Walk-in Client'}</td></tr>
                <tr><td style="color:#555;padding:1px 0;">Phone:</td><td style="text-align:right;color:#000;padding:1px 0;">${phone || 'N/A'}</td></tr>
                <tr><td style="color:#555;padding:1px 0;">Master Barber:</td><td style="text-align:right;font-weight:700;color:#000;padding:1px 0;">${stylist || 'Assigned Stylist'}</td></tr>
            </table>

            <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

            <!-- Treatment & Charges Breakdown -->
            <table style="width:100%;border-collapse:collapse;font-size:12px;margin:4px 0;">
                <tr style="font-weight:700;">
                    <td style="text-align:left;color:#000;padding:2px 0;">${service || 'Hair & Beard Grooming'}</td>
                    <td style="text-align:right;color:#000;white-space:nowrap;padding:2px 0;">Rs. ${formattedPrice}</td>
                </tr>
                <tr style="font-size:10.5px;color:#666;">
                    <td style="text-align:left;padding:2px 0;">Salon GST / Tax (Included)</td>
                    <td style="text-align:right;white-space:nowrap;padding:2px 0;">Rs. 0.00</td>
                </tr>
            </table>

            <div class="receipt-divider-double" style="margin:8px 0;"></div>

            <!-- Net Total -->
            <table style="width:100%;border-collapse:collapse;margin:4px 0;">
                <tr style="font-size:15.5px;font-weight:900;">
                    <td style="color:#000;padding:3px 0;">NET TOTAL:</td>
                    <td style="text-align:right;color:#000;white-space:nowrap;padding:3px 0;">Rs. ${formattedPrice}</td>
                </tr>
                <tr style="font-size:11px;">
                    <td style="color:#555;padding:2px 0;">Payment Method:</td>
                    <td style="text-align:right;padding:2px 0;">
                        <strong style="color:#000;text-transform:uppercase;background:#e5e5e5;padding:1px 6px;border-radius:3px;">${payment || 'Cash'} (PAID)</strong>
                    </td>
                </tr>
            </table>

            <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

            <!-- Realistic POS Barcode Simulation -->
            <div class="receipt-barcode" style="height:28px;margin:8px auto 3px;">
                <span style="width:2px;"></span><span style="width:1px;background:transparent;"></span>
                <span style="width:3px;"></span><span style="width:2px;background:transparent;"></span>
                <span style="width:1px;"></span><span style="width:1px;background:transparent;"></span>
                <span style="width:4px;"></span><span style="width:2px;background:transparent;"></span>
                <span style="width:2px;"></span><span style="width:1px;background:transparent;"></span>
                <span style="width:3px;"></span><span style="width:3px;background:transparent;"></span>
                <span style="width:1px;"></span><span style="width:2px;background:transparent;"></span>
                <span style="width:4px;"></span><span style="width:1px;background:transparent;"></span>
                <span style="width:2px;"></span><span style="width:2px;background:transparent;"></span>
                <span style="width:3px;"></span><span style="width:1px;background:transparent;"></span>
                <span style="width:1px;"></span><span style="width:2px;background:transparent;"></span>
                <span style="width:4px;"></span><span style="width:1px;background:transparent;"></span>
                <span style="width:2px;"></span>
            </div>
            <div style="text-align:center;font-size:9px;letter-spacing:2px;color:#555 !important;margin-bottom:6px;">
                *${ref || 'ELG-0000'}*
            </div>

            <!-- Footer Details -->
            <div style="text-align:center;font-size:9.5px;line-height:1.4;color:#444 !important;margin-top:6px;">
                <div style="font-weight:700;color:#111 !important;">THANK YOU FOR YOUR PATRONAGE!</div>
                <div>Follow us on Instagram: @elegance.salon.pk</div>
                <div style="font-style:italic;margin-top:2px;font-size:8.5px;color:#777 !important;">*** Computer Generated Sales Slip &bull; 80mm POS ***</div>
            </div>
        `;
        window.currentSlipRef = ref || 'SLIP';
        openModal('receiptModal');
    }

    function downloadReceiptPDF() {
        var source = document.getElementById('receiptContent');
        if (!source) return;
        var filename = 'Elegance_Receipt_' + (window.currentSlipRef || 'SLIP') + '.pdf';

        // Create temporary rendering container at 0,0 with high z-index (captured cleanly at 0,0, then removed)
        var tempDiv = document.createElement('div');
        tempDiv.id = 'tempPdfRenderBox';
        tempDiv.style.cssText = 'position:fixed;top:0;left:0;width:330px;background:#ffffff;color:#000000;margin:0;padding:14px 16px;z-index:999999;box-sizing:border-box;font-family:"Courier New",Courier,monospace;font-size:12px;line-height:1.45;box-shadow:none;border:none;';
        tempDiv.innerHTML = source.innerHTML;

        document.body.appendChild(tempDiv);

        setTimeout(function() {
            var hPx = tempDiv.scrollHeight || 480;
            var hMm = Math.ceil(hPx * 0.264583) + 6;

            var opt = {
                margin: [0, 0, 0, 0],
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2.5,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    scrollY: 0,
                    scrollX: 0,
                    x: 0,
                    y: 0,
                    width: 330,
                    height: hPx
                },
                jsPDF: {
                    unit: 'mm',
                    format: [80, Math.max(90, hMm)],
                    orientation: 'portrait'
                },
                pagebreak: { mode: ['avoid-all'] }
            };

            if (typeof html2pdf !== 'undefined') {
                html2pdf().set(opt).from(tempDiv).save().then(function() {
                    if (tempDiv.parentNode) tempDiv.parentNode.removeChild(tempDiv);
                }).catch(function(err) {
                    console.error('PDF error:', err);
                    if (tempDiv.parentNode) tempDiv.parentNode.removeChild(tempDiv);
                    printCleanReceipt();
                });
            } else {
                if (tempDiv.parentNode) tempDiv.parentNode.removeChild(tempDiv);
                printCleanReceipt();
            }
        }, 80);
    }

    function printCleanReceipt() {
        var content = document.getElementById('receiptContent');
        if (!content) return;

        var printWin = window.open('', '_blank', 'width=460,height=680');
        if (!printWin) {
            window.print();
            return;
        }

        printWin.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Elegance_Receipt_${window.currentSlipRef || 'SLIP'}</title>
                <style>
                    @page { size: 80mm auto; margin: 4mm; }
                    html, body {
                        margin: 0;
                        padding: 8px 12px;
                        background: #ffffff !important;
                        color: #000000 !important;
                        font-family: 'Courier New', Courier, monospace;
                        font-size: 12.5px;
                        line-height: 1.5;
                    }
                    * { color: #000000 !important; box-sizing: border-box; }
                    .thermal-receipt-paper {
                        width: 100%;
                        max-width: 80mm;
                        margin: 0 auto;
                        background: #fff;
                    }
                    .receipt-token-badge {
                        display: block;
                        border: 2px solid #000;
                        background: #000 !important;
                        color: #fff !important;
                        padding: 6px 14px;
                        font-size: 16px;
                        font-weight: 900;
                        text-align: center;
                        margin: 8px auto;
                        width: fit-content;
                    }
                    .receipt-token-badge * { color: #fff !important; }
                    .receipt-divider-dashed { border-top: 1.5px dashed #000; margin: 10px 0; }
                    .receipt-divider-double { border-top: 3px double #000; margin: 10px 0; }
                    .receipt-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
                    .receipt-row.total-row { font-size: 15px; font-weight: 900; }
                    .receipt-barcode { height: 32px; display: flex; justify-content: center; gap: 2px; margin: 10px 0 4px; }
                    .receipt-barcode span { display: inline-block; height: 100%; background: #000 !important; }
                </style>
            </head>
            <body>
                <div class="thermal-receipt-paper">
                    ${content.innerHTML}
                </div>
                <script>
                    window.onload = function() {
                        window.focus();
                        window.print();
                        setTimeout(function() { window.close(); }, 800);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWin.document.close();
    }

    // Universal Form Submission Loading Feedback
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function() {
            var btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.innerHTML = '⏳ Processing...';
            }
        });
    });
</script>
</body>
</html>