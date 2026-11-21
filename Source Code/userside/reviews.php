<?php
$pageTitle = "Our Reviews - Elegance Salon Karachi";
include 'header.php';
require_once __DIR__ . '/../config.php';

$reviewMsg = "";
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['submit_public_review'])) {
    $cName = mysqli_real_escape_string($conn, trim($_POST['client_name']));
    $cEmail = mysqli_real_escape_string($conn, trim($_POST['client_email'] ?? ''));
    $cService = mysqli_real_escape_string($conn, trim($_POST['service_name'] ?? 'General'));
    $cStylist = mysqli_real_escape_string($conn, trim($_POST['stylist_name'] ?? 'General Staff'));
    $cRating = intval($_POST['rating'] ?? 5);
    $cComment = mysqli_real_escape_string($conn, trim($_POST['comment']));

    if (!empty($cName) && !empty($cComment)) {
        $insR = "INSERT INTO `reviews` (`client_name`, `client_email`, `service_name`, `stylist_name`, `rating`, `comment`, `status`)
                 VALUES ('$cName', '$cEmail', '$cService', '$cStylist', '$cRating', '$cComment', 'Approved')";
        if (mysqli_query($conn, $insR)) {
            $reviewMsg = "Thank you! Your verified review has been submitted successfully.";
        }
    }
}

// Fetch approved database reviews
$db_reviews = [];
$db_rev_q = mysqli_query($conn, "SELECT * FROM `reviews` WHERE `status`='Approved' ORDER BY `id` DESC");
if ($db_rev_q) {
    while ($dr = mysqli_fetch_assoc($db_rev_q)) {
        $db_reviews[] = $dr;
    }
}
?>

    <!-- REVIEWS HERO SECTION -->
    <section class="reviews-hero" id="hero-section">
        <div class="container reviews-hero-content" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-uppercase tracking-widest text-warning small fw-medium d-block mb-2" data-aos="fade-down" data-aos-delay="100">CLIENT EXPERIENCES</span>
            <h1 class="reviews-hero-title" data-aos="fade-up" data-aos-delay="200">Hear it from our clients</h1>
            <p class="text-light fw-light small mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="300">
                Verified reviews from gentlemen across Clifton, DHA, PECHS, and all across Karachi.
            </p>
        </div>
    </section>

    <!-- REVIEWS CONTENT SECTION WITH PAKISTANI REVIEWS + SMART FILTER -->
    <section class="reviews-content-section">
        <div class="container">
            <?php if (!empty($reviewMsg)): ?>
                <div class="alert alert-success text-center mb-4" style="background:rgba(5,205,153,0.15);border:1px solid #05CD99;color:#05CD99;border-radius:12px;">
                    <?php echo $reviewMsg; ?>
                </div>
            <?php endif; ?>

            <div class="text-center mb-4" data-aos="fade-up" data-aos-duration="900">
                <div class="review-badge" data-aos="zoom-in">TESTIMONIALS</div>
                <h2 class="reviews-main-heading" data-aos="fade-up" data-aos-delay="100">Real experiences from gentlemen across Karachi</h2>

                <div class="filter-buttons" data-aos="fade-up" data-aos-delay="200">
                    <button class="filter-btn active" onclick="filterReviews('all', this)">All Reviews</button>
                    <button class="filter-btn" onclick="filterReviews('haircut', this)">Haircut & Fade</button>
                    <button class="filter-btn" onclick="filterReviews('beard', this)">Beard Styling</button>
                    <button class="filter-btn" onclick="filterReviews('coloring', this)">Hair Coloring</button>
                </div>
            </div>

            <!-- Reviews Grid Container -->
            <div class="row g-4" id="reviewsGrid">
                <?php if (!empty($db_reviews)): ?>
                    <?php foreach ($db_reviews as $dRev): ?>
                        <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                            <div class="screenshot-review-card" style="border:1px solid var(--gold, #d4af37);">
                                <div>
                                    <div class="review-stars"><?php echo str_repeat('★', intval($dRev['rating'] ?: 5)); ?></div>
                                    <p class="review-body-text">"<?php echo htmlspecialchars($dRev['comment']); ?>"</p>
                                </div>
                                <div class="review-footer-info">
                                    <div class="author-info">
                                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#d4af37,#9a781b);display:flex;align-items:center;justify-content:center;font-weight:700;color:#000;font-size:14px;">
                                            <?php echo strtoupper(substr($dRev['client_name'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <h5 class="author-name"><?php echo htmlspecialchars($dRev['client_name']); ?></h5>
                                            <p class="author-location"><?php echo htmlspecialchars($dRev['service_name'] ?: 'Verified Customer'); ?> &middot; Karachi</p>
                                        </div>
                                    </div>
                                    <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- 1 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Saad is absolutely the best stylist I've ever had the pleasure of sitting in the chair for! Not only is he super talented with scissors, but he’s also an amazing guy who always makes me feel welcome. 10/10 would recommend!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop" class="author-avatar" alt="Daniyal Khan">
                                <div>
                                    <h5 class="author-name">Daniyal Khan</h5>
                                    <p class="author-location">Clifton, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 2 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">I had a great experience getting my haircut at Elegance Salon on Tariq Road. They took the time to understand exactly what I wanted and gave helpful suggestions to suit my face shape. The fade and overall finish were precise and clean!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=100&h=100&fit=crop" class="author-avatar" alt="Usman Siddiqui">
                                <div>
                                    <h5 class="author-name">Usman Siddiqui</h5>
                                    <p class="author-location">Tariq Road, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 3 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="beard">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">The barber is an absolute wizard with a beard trim. He shaped it symmetrically and gave great advice on maintenance oils. The atmosphere in the shop is extremely upscale and relaxing.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop" class="author-avatar" alt="Bilal Ahmed">
                                <div>
                                    <h5 class="author-name">Bilal Ahmed</h5>
                                    <p class="author-location">DHA Phase 5, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 4 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="coloring">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Got my hair colored here for the first time and the stylist did an incredible job with the tone blend. It looks completely natural and stylish. Will definitely be returning!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&h=100&fit=crop" class="author-avatar" alt="Farhan Akhtar">
                                <div>
                                    <h5 class="author-name">Farhan Akhtar</h5>
                                    <p class="author-location">Gulshan-e-Iqbal, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 5 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Cleanest skin fade I’ve gotten in Karachi so far. The attention to symmetry and finishing touches with the straight razor are top-tier. Worth every rupee.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" class="author-avatar" alt="Shehryar Munawar">
                                <div>
                                    <h5 class="author-name">Shehryar Munawar</h5>
                                    <p class="author-location">PECHS, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 6 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="beard">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Tariq is an expert at beard sculpting. He ensured my neckline and cheeks were sharp without taking off too much length. Great customer service too!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1628157582853-a796fa650a6a?w=100&h=100&fit=crop" class="author-avatar" alt="Zain Malik">
                                <div>
                                    <h5 class="author-name">Zain Malik</h5>
                                    <p class="author-location">North Nazimabad, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 7 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">The appointment system was super easy, and when I arrived, I didn't have to wait at all. Fantastic haircut and complimentary green tea!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=100&h=100&fit=crop" class="author-avatar" alt="Adnan Siddiqui">
                                <div>
                                    <h5 class="author-name">Adnan Siddiqui</h5>
                                    <p class="author-location">Bahria Town, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 8 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Elegance Salon maintains international standards. The shop vibe is stylish and sleek, and the haircut was executed to absolute perfection.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=100&h=100&fit=crop" class="author-avatar" alt="Hamza Qureshi">
                                <div>
                                    <h5 class="author-name">Hamza Qureshi</h5>
                                    <p class="author-location">Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 9 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="beard">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Hot towel shave combined with beard trimming here is heavenly. Extremely relaxing and professional service right in the heart of Karachi.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop" class="author-avatar" alt="Fahad Mustafa">
                                <div>
                                    <h5 class="author-name">Fahad Mustafa</h5>
                                    <p class="author-location">Defence V, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 10 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="coloring">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Grey blending treatment was done discreetly and effectively. Nobody even realized I got it done, it looks that natural!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop" class="author-avatar" alt="Junaid Khan">
                                <div>
                                    <h5 class="author-name">Junaid Khan</h5>
                                    <p class="author-location">KDA Scheme 1, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 11 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Found my permanent barbershop in Karachi. Consistent quality every single time I visit. Highly professional staff.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&h=100&fit=crop" class="author-avatar" alt="Siddharth Roy">
                                <div>
                                    <h5 class="author-name">Siddharth Roy</h5>
                                    <p class="author-location">Gulshan, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 12 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Great styling advice given before the cut even started. They really study your hair texture before applying any product.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop" class="author-avatar" alt="Ahsan Khan">
                                <div>
                                    <h5 class="author-name">Ahsan Khan</h5>
                                    <p class="author-location">Clifton, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 13 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="beard">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Clean lines, perfect fade, and wonderful beard balm recommendations. 5 stars easily deserved.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=100&h=100&fit=crop" class="author-avatar" alt="Imran Abbas">
                                <div>
                                    <h5 class="author-name">Imran Abbas</h5>
                                    <p class="author-location">Shahrah-e-Faisal, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 14 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Super slick interior design and friendly staff. Getting a haircut here feels like an elite ritual rather than a chore.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop" class="author-avatar" alt="Mikaal Zulfiqar">
                                <div>
                                    <h5 class="author-name">Mikaal Zulfiqar</h5>
                                    <p class="author-location">DHA Phase 8, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 15 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Immaculate hygiene standards, sterilized equipment used right in front of you. Extremely professional environment.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" class="author-avatar" alt="Noman Ijaz">
                                <div>
                                    <h5 class="author-name">Noman Ijaz</h5>
                                    <p class="author-location">CP Berar Society, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 16 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="coloring">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Top-grade hair coloring products used here. No scalp irritation or weird smells afterwards. Outstanding experience.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=100&h=100&fit=crop" class="author-avatar" alt="Danish Taimoor">
                                <div>
                                    <h5 class="author-name">Danish Taimoor</h5>
                                    <p class="author-location">Zamzama, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 17 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Quick, efficient, yet meticulous. Exactly what a busy professional in Karachi needs. Will be back next month!</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop" class="author-avatar" alt="Faysal Quraishi">
                                <div>
                                    <h5 class="author-name">Faysal Quraishi</h5>
                                    <p class="author-location">Korangi Road, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 18 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="beard">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Beard shaping was done with absolute precision. The barber checked every angle in the mirror before finishing up.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop" class="author-avatar" alt="Humayun Saeed">
                                <div>
                                    <h5 class="author-name">Humayun Saeed</h5>
                                    <p class="author-location">Clifton Block 4, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 19 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Best fading technique in Pakistan hands down. The blend from skin to top is completely seamless.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=100&h=100&fit=crop" class="author-avatar" alt="Shahid Afridi">
                                <div>
                                    <h5 class="author-name">Shahid Afridi</h5>
                                    <p class="author-location">DHA, Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

                <!-- 20 -->
                <div class="col-lg-6 col-md-6 review-item" data-category="haircut">
                    <div class="screenshot-review-card">
                        <div>
                            <div class="review-stars">★★★★★</div>
                            <p class="review-body-text">Amazing hospitality from the reception right down to the master stylist. Truly a 5-star luxury experience in Karachi.</p>
                        </div>
                        <div class="review-footer-info">
                            <div class="author-info">
                                <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&h=100&fit=crop" class="author-avatar" alt="Babar Azam">
                                <div>
                                    <h5 class="author-name">Babar Azam</h5>
                                    <p class="author-location">Karachi</p>
                                </div>
                            </div>
                            <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Load More Button -->
            <div class="load-more-container">
                <button class="load-more-btn" id="loadMoreBtn" onclick="loadMoreReviews()">Load More Reviews</button>
            </div>

        </div>
    </section>

<?php include 'footer.php'; ?>
