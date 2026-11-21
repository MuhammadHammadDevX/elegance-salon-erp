<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $redirectUrl = 'service.php';
    if (!empty($_SERVER['QUERY_STRING'])) {
        $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
    }
    header("Location: login.php?redirect=" . urlencode($redirectUrl) . "&msg=login_required");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nameParam = isset($_GET['name']) ? trim($_GET['name']) : '';

$srv = null;
if ($id > 0) {
    $srvQuery = mysqli_query($conn, "SELECT * FROM `services` WHERE `id` = '$id' LIMIT 1");
    if ($srvQuery && mysqli_num_rows($srvQuery) > 0) {
        $srv = mysqli_fetch_assoc($srvQuery);
    }
} elseif (!empty($nameParam)) {
    $safeN = mysqli_real_escape_string($conn, $nameParam);
    $srvQuery = mysqli_query($conn, "SELECT * FROM `services` WHERE `name` LIKE '%$safeN%' LIMIT 1");
    if ($srvQuery && mysqli_num_rows($srvQuery) > 0) {
        $srv = mysqli_fetch_assoc($srvQuery);
    }
}

if (!$srv) {
    $srvQuery = mysqli_query($conn, "SELECT * FROM `services` ORDER BY `id` ASC LIMIT 1");
    $srv = mysqli_fetch_assoc($srvQuery);
}

$serviceId    = $srv['id'] ?? 1;
$serviceName  = $srv['name'] ?? 'Total Grooming';
$serviceTag   = !empty($srv['tag']) ? $srv['tag'] : 'SIGNATURE SERVICE';
$serviceSub   = !empty($srv['subtitle']) ? $srv['subtitle'] : 'Bespoke grooming experience designed for the modern gentleman.';
$serviceHero  = !empty($srv['hero_image']) ? $srv['hero_image'] : 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop';
$serviceDesc  = !empty($srv['description']) ? $srv['description'] : 'Experience premier salon treatments tailored to your distinct profile and style.';
$servicePrice = floatval($srv['price'] ?? 5000);
$serviceDur   = !empty($srv['duration']) ? $srv['duration'] : '60 min';

$card1_title = !empty($srv['card1_title']) ? $srv['card1_title'] : 'Precision Styling';
$card1_desc  = !empty($srv['card1_desc']) ? $srv['card1_desc'] : 'Expert consultation and styling tailored specifically to your personality.';
$card1_img   = !empty($srv['card1_img']) ? $srv['card1_img'] : 'assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg';

$card2_title = !empty($srv['card2_title']) ? $srv['card2_title'] : 'Facial & Beard Care';
$card2_desc  = !empty($srv['card2_desc']) ? $srv['card2_desc'] : 'Meticulous detailing and nourishing treatments to refresh your look.';
$card2_img   = !empty($srv['card2_img']) ? $srv['card2_img'] : 'assets/images/homepage/total-grooming/2.jpg';

$card3_title = !empty($srv['card3_title']) ? $srv['card3_title'] : 'Hot Towel Finish';
$card3_desc  = !empty($srv['card3_desc']) ? $srv['card3_desc'] : 'Soothing hot towel relaxation and premium styling products to lock in freshness.';
$card3_img   = !empty($srv['card3_img']) ? $srv['card3_img'] : 'assets/images/homepage/total-grooming/3.jpg';

$pkg_details = !empty($srv['package_details']) ? $srv['package_details'] : 'Includes full consultation, precision styling, scalp therapy, and luxury finish.';

$pageTitle = $serviceName . " | Elegance Salon Karachi";
include 'header.php';
?>

    <!-- HERO SECTION -->
    <section class="service-hero" id="hero-section" style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.75)), url('<?php echo htmlspecialchars($serviceHero); ?>') no-repeat center center / cover;">
        <div class="container" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-uppercase tracking-widest text-warning small fw-medium d-block mb-2" data-aos="fade-down" data-aos-delay="100"><?php echo htmlspecialchars($serviceTag); ?></span>
            <h1 class="service-hero-title" data-aos="fade-up" data-aos-delay="200"><?php echo htmlspecialchars($serviceName); ?></h1>
            <p class="text-light fw-light small mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="300">
                <?php echo htmlspecialchars($serviceSub); ?>
            </p>
        </div>
    </section>

    <!-- ULTRA-SLIM LUXURY BRAND MARQUEE -->
    <section class="luxury-marquee" data-aos="fade-in" data-aos-duration="800">
        <div class="luxury-marquee-track">
            <div class="luxury-marquee-group">
                <span><?php echo htmlspecialchars($card1_title); ?></span>
                <div class="luxury-dot"></div>
                <span><?php echo htmlspecialchars($card2_title); ?></span>
                <div class="luxury-dot"></div>
                <span><?php echo htmlspecialchars($card3_title); ?></span>
                <div class="luxury-dot"></div>
                <span>Hot Towel Finish</span>
                <div class="luxury-dot"></div>
                <span>Elegance Karachi Standard</span>
                <div class="luxury-dot"></div>
            </div>
            <div class="luxury-marquee-group">
                <span><?php echo htmlspecialchars($card1_title); ?></span>
                <div class="luxury-dot"></div>
                <span><?php echo htmlspecialchars($card2_title); ?></span>
                <div class="luxury-dot"></div>
                <span><?php echo htmlspecialchars($card3_title); ?></span>
                <div class="luxury-dot"></div>
                <span>Hot Towel Finish</span>
                <div class="luxury-dot"></div>
                <span>Elegance Karachi Standard</span>
                <div class="luxury-dot"></div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT SECTION -->
    <section class="service-main-section">
        <div class="container">
            <div class="text-center mx-auto mb-5" data-aos="fade-up" data-aos-duration="900">
                <div class="section-badge" data-aos="zoom-in"><?php echo htmlspecialchars($serviceTag); ?></div>
                <h2 class="service-sub-title" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($serviceName); ?> Experience in Karachi</h2>
                <p class="service-desc mx-auto" data-aos="fade-up" data-aos-delay="200">
                    <?php echo htmlspecialchars($serviceDesc); ?>
                </p>
            </div>

            <!-- Features Grid (Exact 3 Cards) -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
                    <div class="feature-card-clean">
                        <div class="feature-img-box">
                            <img src="<?php echo htmlspecialchars($card1_img); ?>" alt="<?php echo htmlspecialchars($card1_title); ?>">
                        </div>
                        <div class="feature-content">
                            <h4 class="feature-title"><?php echo htmlspecialchars($card1_title); ?></h4>
                            <p class="feature-text"><?php echo htmlspecialchars($card1_desc); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
                    <div class="feature-card-clean">
                        <div class="feature-img-box">
                            <img src="<?php echo htmlspecialchars($card2_img); ?>" alt="<?php echo htmlspecialchars($card2_title); ?>">
                        </div>
                        <div class="feature-content">
                            <h4 class="feature-title"><?php echo htmlspecialchars($card2_title); ?></h4>
                            <p class="feature-text"><?php echo htmlspecialchars($card2_desc); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
                    <div class="feature-card-clean">
                        <div class="feature-img-box">
                            <img src="<?php echo htmlspecialchars($card3_img); ?>" alt="<?php echo htmlspecialchars($card3_title); ?>">
                        </div>
                        <div class="feature-content">
                            <h4 class="feature-title"><?php echo htmlspecialchars($card3_title); ?></h4>
                            <p class="feature-text"><?php echo htmlspecialchars($card3_desc); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing & Booking CTA Box -->
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="zoom-in" data-aos-duration="900" data-aos-delay="200">
                    <div class="pricing-card">
                        <span class="text-uppercase tracking-widest small text-secondary d-block mb-2">Package Details</span>
                        <h3 class="fw-light mb-3 text-white"><?php echo htmlspecialchars($serviceName); ?></h3>
                        <div class="price-tag">Rs. <?php echo number_format($servicePrice); ?></div>
                        <p class="text-secondary fw-light small mb-4"><?php echo htmlspecialchars($pkg_details); ?></p>
                        <a href="appointment.php?service_id=<?php echo $serviceId; ?>&service=<?php echo urlencode($serviceName); ?>" class="book-now-btn">Book Your Slot Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>