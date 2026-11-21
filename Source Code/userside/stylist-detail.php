<?php 
require_once __DIR__ . '/../config.php';

// 1. URL se Stylist ki ID ya Name fetch karo
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nameParam = isset($_GET['name']) ? trim($_GET['name']) : '';

$stylist = null;

if ($id > 0) {
    $stmt = mysqli_query($conn, "SELECT * FROM `staff` WHERE `id` = '$id' LIMIT 1");
    if ($stmt && mysqli_num_rows($stmt) > 0) {
        $stylist = mysqli_fetch_assoc($stmt);
    }
} elseif (!empty($nameParam)) {
    $safeName = mysqli_real_escape_string($conn, $nameParam);
    $stmt = mysqli_query($conn, "SELECT * FROM `staff` WHERE `name` LIKE '%$safeName%' LIMIT 1");
    if ($stmt && mysqli_num_rows($stmt) > 0) {
        $stylist = mysqli_fetch_assoc($stmt);
    }
}

// Fallback: Default to first active stylist if not found
if (!$stylist) {
    $fallbackStmt = mysqli_query($conn, "SELECT * FROM `staff` WHERE `status` = 'Active' ORDER BY `id` ASC LIMIT 1");
    if ($fallbackStmt && mysqli_num_rows($fallbackStmt) > 0) {
        $stylist = mysqli_fetch_assoc($fallbackStmt);
    }
}

$stylistName     = !empty($stylist['name']) ? $stylist['name'] : 'Master Stylist';
$stylistSpec     = !empty($stylist['specialization']) ? $stylist['specialization'] : 'Master Barber';
$stylistImg      = !empty($stylist['image']) ? $stylist['image'] : 'assets/images/homepage/stylist/1.jpg';
$stylistBio      = !empty($stylist['bio']) ? $stylist['bio'] : 'A pivotal member of the Elegance Salon Karachi team, representing the high-fashion transition between precision barbering and contemporary men\'s hairdressing.';
$stylistSchedule = !empty($stylist['work_schedule']) ? $stylist['work_schedule'] : 'Tue - Sun: 11:00 AM - 9:00 PM';
$stylistPhone    = !empty($stylist['phone']) ? $stylist['phone'] : '+92 300 1234567';
$stylistId       = !empty($stylist['id']) ? $stylist['id'] : 1;

$pageTitle = $stylistName . " - Stylist Details | Elegance Salon Karachi";
include 'header.php'; 
?>

<!-- HERO HEADER BANNER (Consistent with index, about, and service pages) -->
<section class="service-hero" id="hero-section" style="background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.85)), url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=1920&auto=format&fit=crop') no-repeat center center / cover;">
    <div class="container text-center" data-aos="fade-up" data-aos-duration="1000">
        <span class="text-uppercase tracking-widest text-warning small fw-medium d-block mb-2" data-aos="fade-down" data-aos-delay="100">MASTER STYLIST PROFILE</span>
        <h1 class="service-hero-title" data-aos="fade-up" data-aos-delay="200"><?php echo htmlspecialchars($stylistName); ?></h1>
        <p class="text-light fw-light small mx-auto mb-0" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="300">
            <?php echo htmlspecialchars($stylistSpec); ?> — Elegance Salon Flagship, Main Tariq Road Karachi
        </p>
    </div>
</section>

<!-- CLEAN LIGHT LUXURY STYLIST PROFILE SECTION -->
<section class="service-main-section py-5">
    <div class="container">
        <!-- Back Navigation & Section Tag -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5" data-aos="fade-down" data-aos-duration="800">
            <a href="index.php" class="clean-back-link">
                ← Back to Home
            </a>
            <span class="section-badge mb-0">TEAM ARTIST</span>
        </div>

        <div class="row g-5 align-items-center">
            <!-- Left Column: Stylist Portrait -->
            <div class="col-lg-5" data-aos="fade-right" data-aos-duration="1000">
                <div class="clean-stylist-img-box shadow-sm">
                    <img src="<?php echo htmlspecialchars($stylistImg); ?>" alt="<?php echo htmlspecialchars($stylistName); ?>">
                </div>
            </div>

            <!-- Right Column: Stylist Details -->
            <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000">
                <h1 class="clean-stylist-name mb-2"><?php echo htmlspecialchars($stylistName); ?></h1>
                <p class="clean-stylist-role text-secondary mb-4"><?php echo htmlspecialchars($stylistSpec); ?></p>
                
                <p class="clean-stylist-bio mb-4">
                    <?php echo nl2br(htmlspecialchars($stylistBio)); ?>
                </p>

                <div class="clean-detail-row">
                    <span class="clean-detail-label">Role</span>
                    <span class="clean-detail-value"><?php echo htmlspecialchars($stylistSpec); ?></span>
                </div>
                <div class="clean-detail-row">
                    <span class="clean-detail-label">Experience</span>
                    <span class="clean-detail-value">Master Barber Professional</span>
                </div>
                <div class="clean-detail-row">
                    <span class="clean-detail-label">Schedule</span>
                    <span class="clean-detail-value"><?php echo htmlspecialchars($stylistSchedule); ?></span>
                </div>
                <div class="clean-detail-row">
                    <span class="clean-detail-label">Branch</span>
                    <span class="clean-detail-value">Main Tariq Road, PECHS Karachi</span>
                </div>
                <div class="clean-detail-row">
                    <span class="clean-detail-label">Phone</span>
                    <span class="clean-detail-value"><?php echo htmlspecialchars($stylistPhone); ?></span>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-2">
                    <a href="appointment.php?stylist_id=<?php echo $stylistId; ?>" class="btn btn-dark rounded-pill px-5 py-3 fw-medium text-uppercase text-decoration-none shadow-sm" style="letter-spacing: 1px; font-size: 14px;">
                        Book With <?php echo htmlspecialchars(explode(' ', $stylistName)[0]); ?>
                    </a>

                    <div>
                        <span class="text-secondary small me-2 text-uppercase" style="letter-spacing: 1px;">Connect:</span>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="clean-social-btn" aria-label="Instagram">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>