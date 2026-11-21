<?php
$pageTitle = "Contact Us - Elegance Salon Karachi";
include 'header.php';
include_once __DIR__ . '/../config.php';

if (isset($_POST['submit'])) {
    $name    = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email   = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    $query = "INSERT INTO `contact_messages` (`name`, `email`, `phone`, `subject`, `message`)
              VALUES ('$name', '$email', '$phone', '$subject', '$message')";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Thank you! Your message has been sent successfully.'); window.location.href='contact.php';</script>";
    } else {
        echo "<script>alert('Database Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!-- CONTACT HERO -->
<section class="contact-hero" id="hero-section">
    <div class="container" data-aos="fade-up" data-aos-duration="1000">
        <span class="text-uppercase tracking-widest text-warning small fw-medium d-block mb-2" data-aos="fade-down"
            data-aos-delay="100">KARACHI LOCATION & INQUIRIES</span>
        <h1 class="contact-hero-title" data-aos="fade-up" data-aos-delay="200">Get In Touch</h1>
        <p class="text-light fw-light small mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="300">
            We are here to answer your grooming questions and book bespoke styling sessions.
        </p>
    </div>
</section>

<!-- CONTACT MAIN SECTION -->
<section class="contact-main-section">
    <div class="container">
        <div class="text-center mx-auto mb-5" data-aos="fade-up" data-aos-duration="900">
            <div class="section-badge" data-aos="zoom-in">CONTACT US</div>
            <h2 class="contact-sub-title" data-aos="fade-up" data-aos-delay="100">Get the look you love – schedule your
                visit in Karachi.</h2>
            <p class="contact-desc mx-auto" data-aos="fade-up" data-aos-delay="200">
                Elegance Salon Karachi is your ultimate destination for luxury male grooming in Pakistan. Founded by
                master stylists, we create looks that reflect your personality and boost your daily confidence.
            </p>
        </div>

        <!-- Info Cards Grid -->
        <div class="row g-4 mb-5 pb-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="info-title">Phone</h4>
                        <p class="info-text">+92 300 1234567</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h4 class="info-title">Address</h4>
                        <p class="info-text">Main Tariq Road, Block 2 PECHS, Karachi, Pakistan</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h4 class="info-title">Opening Hours</h4>
                        <p class="info-text">Monday – Sunday: 10:00 AM – 11:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map and Form Section -->
        <div class="row g-5 align-items-stretch">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                <div class="map-card-wrapper shadow-sm">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3619.0303867623916!2d67.065!3d24.8607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33ea06b123457%3A0x123456789abcdef!2sTariq%20Rd%2C%20Karachi%2C%20Pakistan!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                <div class="contact-form-card">
                    <h3 class="form-heading">Send Us a Message</h3>
                    <p class="form-subtext">Fill out the form below and our team will contact you soon.</p>

                    <form id="contactForm" action="contact.php" method="POST">
                        <div class="custom-input-group">
                            <label class="custom-label">FULL NAME *</label>
                            <input type="text" id="fullName" name="name" placeholder="e.g. Ali Ahmed" required class="custom-input">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="custom-input-group">
                                    <label class="custom-label">EMAIL ADDRESS *</label>
                                    <input type="email" id="email" name="email" placeholder="e.g. name@example.com" required class="custom-input">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-input-group">
                                    <label class="custom-label">PHONE NUMBER *</label>
                                    <input type="tel" id="phone" name="phone" placeholder="e.g. 03001234567" required class="custom-input" maxlength="11">
                                </div>
                            </div>
                        </div>

                        <div class="custom-input-group">
                            <label class="custom-label">SELECT SERVICE / SUBJECT *</label>
                            <select id="service" name="subject" required class="custom-select">
                                <option value="" disabled selected>Choose a service...</option>
                                <option value="Precision Haircut & Fade">Precision Haircut & Fade</option>
                                <option value="Beard Design & Shaping">Beard Design & Shaping</option>
                                <option value="Hair Colouring & Grey Blending">Hair Colouring & Grey Blending</option>
                                <option value="Traditional Hot Towel Shave">Traditional Hot Towel Shave</option>
                                <option value="Deep Clean Facial Treatment">Deep Clean Facial Treatment</option>
                                <option value="General Inquiry">General Inquiry</option>
                            </select>
                        </div>

                        <div class="custom-input-group">
                            <label class="custom-label">YOUR MESSAGE *</label>
                            <textarea id="message" class="custom-textarea" name="message" placeholder="Write your inquiry or question here..." required rows="3"></textarea>
                        </div>

                        <button type="submit" class="submit-btn" name="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>