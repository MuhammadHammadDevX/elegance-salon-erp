<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$servicePages = [
    'total-grooming.php',
    'signature-cut-beard.php',
    'beard-design.php',
    'traditional-design.php',
    'deep-clean-facial.php'
];
$isServices = in_array($currentPage, $servicePages);

$accountPages = [
    'login.php',
    'register.php',
    'admin-login.php'
];
$isAccount = in_array($currentPage, $accountPages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Elegance Salon Karachi'; ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Jost:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,300&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Global Stylesheet -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- FLOATING BACK TO TOP BUTTON (GLOBAL BODY LEVEL) -->
    <button id="backToTopBtn" class="back-to-top-btn" aria-label="Scroll to top" title="Back to top">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
    </button>

    <!-- MOBILE NAVIGATION BACKDROP OVERLAY -->
    <div class="mobile-nav-backdrop" id="mobileNavBackdrop"></div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg" id="main-navbar">
        <a href="index.php" class="logo">
            ELEGANCE
            <span>KARACHI</span>
        </a>

        <button class="navbar-toggler d-lg-none collapsed" type="button" id="mobileMenuToggler" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="bar bar-1"></span>
            <span class="bar bar-2"></span>
            <span class="bar bar-3"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-lg-center" id="navbarNav">
            <!-- Mobile Drawer Header -->
            <div class="mobile-drawer-header d-flex d-lg-none justify-content-between align-items-center mb-3 pb-3 border-bottom border-secondary border-opacity-25 w-100">
                <div class="drawer-logo">
                    <span class="fw-medium text-white d-block" style="font-size: 15px; letter-spacing: 2px;">ELEGANCE</span>
                    <span class="text-warning d-block" style="font-size: 9px; letter-spacing: 3px;">KARACHI</span>
                </div>
                <button type="button" class="drawer-close-btn" id="drawerCloseBtn" aria-label="Close menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <ul class="nav-links">
                <li><a href="index.php" class="<?php echo ($currentPage === 'index.php' || $currentPage === '') ? 'active' : ''; ?>">Home</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0);" role="button" class="<?php echo $isServices ? 'active' : ''; ?>">
                        Services
                        <span class="nav-chevron">
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                    <div class="dropdown-content">
                        <div class="dropdown-list">
                            <div class="dropdown-header-tag">SIGNATURE TREATMENTS</div>
                            <?php
                            $navSrvQuery = mysqli_query($conn, "SELECT `id`, `name`, `description` FROM `services` WHERE `is_active`=1 ORDER BY `id` ASC");
                            if ($navSrvQuery && mysqli_num_rows($navSrvQuery) > 0):
                                while ($navS = mysqli_fetch_assoc($navSrvQuery)):
                                    $sName = $navS['name'];
                                    $sUrl = "service.php?id=" . $navS['id'];
                                    if (stripos($sName, 'Total Grooming') !== false) $sUrl = 'total-grooming.php';
                                    elseif (stripos($sName, 'Signature Cut') !== false) $sUrl = 'signature-cut-beard.php';
                                    elseif (stripos($sName, 'Beard Design') !== false) $sUrl = 'beard-design.php';
                                    elseif (stripos($sName, 'Traditional') !== false) $sUrl = 'traditional-design.php';
                                    elseif (stripos($sName, 'Facial') !== false) $sUrl = 'deep-clean-facial.php';

                                    $sDescShort = !empty($navS['description']) ? (strlen($navS['description']) > 48 ? substr($navS['description'], 0, 48) . '...' : $navS['description']) : 'Bespoke grooming treatment';
                            ?>
                                    <a href="<?php echo $sUrl; ?>" class="dropdown-service-item">
                                        <span class="dropdown-item-title"><?php echo htmlspecialchars($sName); ?></span>
                                        <span class="dropdown-item-desc"><?php echo htmlspecialchars($sDescShort); ?></span>
                                    </a>
                            <?php
                                endwhile;
                            endif;
                            ?>
                        </div>
                        <div class="dropdown-image-box d-none d-lg-block">
                            <div class="dropdown-image">
                                <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?q=80&w=600&auto=format&fit=crop" alt="Services">
                                <div class="dropdown-image-overlay">
                                    <span class="image-overlay-badge">FLAGSHIP EXPERIENCE</span>
                                    <h5>Elegance Karachi</h5>
                                    <p>Main Tariq Road Lounge</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="about.php" class="<?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="reviews.php" class="<?php echo $currentPage === 'reviews.php' ? 'active' : ''; ?>">Our Reviews</a></li>
                <li><a href="contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0);" role="button" class="<?php echo $isAccount ? 'active' : ''; ?>">
                        <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
                            <span class="text-warning">Hi, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                        <?php else: ?>
                            Account
                        <?php endif; ?>
                        <span class="nav-chevron">
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                    <div class="dropdown-content account-dropdown">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown-header-tag">
                                <?php
                                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') echo 'EXECUTIVE PORTAL';
                                elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'receptionist') echo 'FRONT DESK CONSOLE';
                                else echo 'MEMBER ACCOUNT';
                                ?>
                            </div>
                            <a href="appointment.php" class="account-link book-link">
                                <span class="d-flex align-items-center gap-2">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    Book Appointment
                                </span>
                            </a>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <a href="../dashboard/admin.php" class="account-link staff-link">
                                    <span class="d-flex align-items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                        Admin Dashboard
                                    </span>
                                </a>
                                <a href="../dashboard/reception.php" class="account-link staff-link">
                                    <span class="d-flex align-items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                        Reception Desk
                                    </span>
                                </a>
                            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'receptionist'): ?>
                                <a href="../dashboard/reception.php" class="account-link staff-link">
                                    <span class="d-flex align-items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                        Reception Desk
                                    </span>
                                </a>
                            <?php else: ?>
                                <a href="dashboard.php" class="account-link">
                                    <span class="d-flex align-items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        My Appointments
                                    </span>
                                </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="account-link logout-link">
                                <span class="d-flex align-items-center gap-2">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    Log Out
                                </span>
                            </a>
                        <?php else: ?>
                            <div class="dropdown-header-tag">MEMBERSHIP</div>
                            <a href="login.php" class="account-link">
                                <span class="d-flex align-items-center gap-2">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                    Sign In
                                </span>
                            </a>
                            <a href="register.php" class="account-link">
                                <span class="d-flex align-items-center gap-2">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                    Register
                                </span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="admin-login.php" class="account-link staff-link">
                                <span class="d-flex align-items-center gap-2">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    Staff Portal
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>

            <!-- Mobile Book Appointment Button -->
            <div class="d-lg-none w-100 pt-3 mt-3 border-top border-secondary border-opacity-25">
                <a href="appointment.php" class="mobile-nav-book-btn d-flex align-items-center justify-content-center gap-2 w-100 text-decoration-none">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="6" cy="6" r="3"></circle>
                        <circle cx="6" cy="18" r="3"></circle>
                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                    </svg>
                    Book Appointment
                </a>
            </div>
        </div>

        <div class="nav-right d-none d-lg-flex">
            <div class="nav-divider d-none d-md-block"></div>
            <a href="appointment.php" class="nav-book-btn">Book Appointment</a>
        </div>
    </nav>