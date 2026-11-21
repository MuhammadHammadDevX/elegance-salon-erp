<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=dashboard.php&msg=login_required");
    exit();
}

$userId = intval($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Member';
$userEmail = $_SESSION['user_email'] ?? '';
$userPhone = $_SESSION['user_phone'] ?? '';
$userRole = $_SESSION['user_role'] ?? 'client';

// Handle Review Submission
$reviewSuccess = "";
$reviewError = "";
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['submit_review'])) {
    $rName = mysqli_real_escape_string($conn, $userName);
    $rEmail = mysqli_real_escape_string($conn, $userEmail);
    $rService = mysqli_real_escape_string($conn, $_POST['service_name'] ?? 'Total Grooming');
    $rStylist = mysqli_real_escape_string($conn, $_POST['stylist_name'] ?? 'Zachary Nickell');
    $rRating = intval($_POST['rating'] ?? 5);
    $rComment = mysqli_real_escape_string($conn, trim($_POST['comment'] ?? ''));

    if (!empty($rComment) && $conn) {
        $q = "INSERT INTO `reviews` (`client_name`, `client_email`, `service_name`, `stylist_name`, `rating`, `comment`, `status`)
              VALUES ('$rName', '$rEmail', '$rService', '$rStylist', $rRating, '$rComment', 'Approved')";
        if (mysqli_query($conn, $q)) {
            $reviewSuccess = "Thank you! Your review has been submitted and published.";
        } else {
            $reviewError = "Failed to submit review. Please try again.";
        }
    }
}

// Fetch user's appointments
$userAppts = [];
if ($conn) {
    $cleanEmail = mysqli_real_escape_string($conn, $userEmail);
    $cleanPhone = mysqli_real_escape_string($conn, $userPhone);
    $q = "SELECT * FROM `appointments` WHERE `client_email` = '$cleanEmail' OR `client_phone` = '$cleanPhone' OR `user_id` = '$userId' ORDER BY `appointment_date` DESC, `id` DESC";
    $res = mysqli_query($conn, $q);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $userAppts[] = $row;
        }
    }
}

$pageTitle = "My Appointments & Profile | Elegance Salon Karachi";
include 'header.php';
?>

<!-- CLIENT DASHBOARD SECTION IN WEBSITE FLOW -->
<section class="py-5" style="background: #0d0f14; min-height: 85vh; padding-top: 130px !important;">
    <div class="container">

        <!-- Welcome Hero Banner -->
        <div class="p-4 p-md-5 mb-4 rounded-4 position-relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(26, 29, 41, 0.95), rgba(15, 17, 23, 0.98)); border: 1px solid rgba(212, 175, 55, 0.25); box-shadow: 0 10px 30px rgba(0,0,0,0.5);"
             data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 64px; height: 64px; font-size: 22px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #000; font-weight: 800; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);">
                        <?php echo strtoupper(substr($userName, 0, 2)); ?>
                    </div>
                    <div>
                        <span class="badge text-uppercase tracking-wider px-3 py-1 mb-2" style="background: rgba(212, 175, 55, 0.15); color: #d4af37; border: 1px solid rgba(212, 175, 55, 0.3); font-size: 11px;">VIP Client Portal</span>
                        <h2 class="text-white fw-light mb-1">Welcome back, <span style="color:#d4af37; font-family:'Playfair Display', serif;"><?php echo htmlspecialchars($userName); ?></span></h2>
                        <p class="text-secondary small mb-0">
                            <span><?php echo htmlspecialchars($userEmail); ?></span>
                            <?php if (!empty($userPhone)): ?>
                                &bull; <span><?php echo htmlspecialchars($userPhone); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="appointment.php" class="btn px-4 py-2 text-dark fw-bold rounded-pill" style="background: linear-gradient(135deg, #d4af37, #aa7c11); border: none; font-size: 14px;">
                        + Book New Slot
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($reviewSuccess)): ?>
            <div class="alert alert-success py-3 px-4 rounded-3 small mb-4 shadow" style="background: rgba(5, 205, 153, 0.15); border: 1px solid #05CD99; color: #05CD99;">
                <?php echo $reviewSuccess; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($reviewError)): ?>
            <div class="alert alert-danger py-3 px-4 rounded-3 small mb-4 shadow" style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444;">
                <?php echo $reviewError; ?>
            </div>
        <?php endif; ?>

        <!-- Active Appointment Digital Pass -->
        <?php if (!empty($userAppts)):
            $latest = $userAppts[0];
            $statusBadgeClass = ($latest['status'] === 'Completed') ? 'bg-success' : (($latest['status'] === 'In Chair') ? 'bg-warning text-dark' : 'bg-primary');
        ?>
            <div class="p-4 p-md-4 rounded-4 mb-4 position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.12), rgba(22, 25, 34, 0.95)); border: 1px solid rgba(212, 175, 55, 0.35); box-shadow: 0 10px 25px rgba(0,0,0,0.4);"
                 data-aos="fade-up">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, #d4af37, #996515); color: #000; font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35);">
                                #<?php echo htmlspecialchars($latest['token'] ?? '01'); ?>
                            </div>
                            <div>
                                <span class="badge <?php echo $statusBadgeClass; ?> px-2 py-1 mb-1" style="font-size: 11px;">
                                    <?php echo htmlspecialchars($latest['status']); ?>
                                </span>
                                <h3 class="text-white mb-0 fw-light"><?php echo htmlspecialchars($latest['service_name']); ?></h3>
                            </div>
                        </div>
                        <div class="row g-3 small text-light opacity-75 mt-1">
                            <div class="col-sm-6">
                                <span class="text-secondary d-block">Master Stylist:</span>
                                <strong class="text-white"><?php echo htmlspecialchars($latest['stylist_name']); ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-secondary d-block">Appointment Date:</span>
                                <strong class="text-white"><?php echo htmlspecialchars($latest['appointment_date']); ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-secondary d-block">Time Slot:</span>
                                <strong class="text-white"><?php echo htmlspecialchars($latest['appointment_time']); ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-secondary d-block">Booking Reference:</span>
                                <code class="text-warning"><?php echo htmlspecialchars($latest['booking_ref']); ?></code>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 text-lg-end border-start-lg border-secondary border-opacity-25 ps-lg-4">
                        <div class="small text-secondary mb-1">Total Service Amount</div>
                        <div class="h2 mb-2" style="color: #d4af37; font-weight: 700;">Rs. <?php echo number_format($latest['price'], 0); ?></div>
                        <div class="small mb-3 text-secondary">
                            Payment: <strong class="text-white"><?php echo htmlspecialchars($latest['payment_mode'] ?? 'Cash'); ?></strong>
                            &bull; Status: <span class="badge bg-secondary"><?php echo htmlspecialchars($latest['payment_status'] ?? 'Unpaid'); ?></span>
                        </div>
                        <button class="btn btn-outline-warning btn-sm px-3 py-2 rounded-pill" onclick="window.print()">
                            🖨️ Print Digital Pass / Receipt
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Appointment History & Feedback Section -->
        <div class="row g-4" data-aos="fade-up">

            <!-- Left: Appointment History Table -->
            <div class="col-lg-8">
                <div class="p-4 rounded-4 h-100" style="background: rgba(22, 25, 34, 0.85); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-white fw-light mb-0">My Appointment History</h4>
                        <span class="badge bg-dark border border-secondary text-secondary px-3 py-2"><?php echo count($userAppts); ?> Records</span>
                    </div>

                    <?php if (empty($userAppts)): ?>
                        <div class="text-center py-5 text-secondary">
                            <p class="mb-3">You haven't booked any appointments yet.</p>
                            <a href="appointment.php" class="btn btn-warning btn-sm rounded-pill px-4">Book Your First Appointment</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(212,175,55,0.2); font-size: 12px; color: #c5a880; text-transform: uppercase; letter-spacing: 1px;">
                                        <th class="py-3">Ref / Token</th>
                                        <th class="py-3">Service</th>
                                        <th class="py-3">Stylist</th>
                                        <th class="py-3">Date & Time</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userAppts as $ap): ?>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 13px;">
                                            <td class="py-3">
                                                <span class="badge bg-warning text-dark me-1">#<?php echo htmlspecialchars($ap['token'] ?? '01'); ?></span>
                                                <code class="text-secondary small"><?php echo htmlspecialchars($ap['booking_ref'] ?? ''); ?></code>
                                            </td>
                                            <td class="py-3 text-white fw-medium"><?php echo htmlspecialchars($ap['service_name']); ?></td>
                                            <td class="py-3 text-secondary"><?php echo htmlspecialchars($ap['stylist_name']); ?></td>
                                            <td class="py-3 text-secondary">
                                                <div><?php echo htmlspecialchars($ap['appointment_date']); ?></div>
                                                <small class="opacity-75"><?php echo htmlspecialchars($ap['appointment_time']); ?></small>
                                            </td>
                                            <td class="py-3 text-warning fw-bold">Rs. <?php echo number_format($ap['price'], 0); ?></td>
                                            <td class="py-3">
                                                <span class="badge <?php echo ($ap['status'] === 'Completed') ? 'bg-success' : (($ap['status'] === 'In Chair') ? 'bg-warning text-dark' : 'bg-primary'); ?> px-2 py-1">
                                                    <?php echo htmlspecialchars($ap['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Submit Review Form -->
            <div class="col-lg-4">
                <div class="p-4 rounded-4 h-100" style="background: rgba(22, 25, 34, 0.85); border: 1px solid rgba(212, 175, 55, 0.25);">
                    <h4 class="text-white fw-light mb-2">Rate Your Experience</h4>
                    <p class="text-secondary small mb-4">Your feedback helps us maintain the highest standard of luxury barbering in Karachi.</p>

                    <form action="dashboard.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase">Service Availed *</label>
                            <select name="service_name" class="form-select bg-dark text-white border-secondary small">
                                <?php
                                $srvQ = mysqli_query($conn, "SELECT name FROM services WHERE is_active=1");
                                if ($srvQ && mysqli_num_rows($srvQ) > 0) {
                                    while ($s = mysqli_fetch_assoc($srvQ)) {
                                        echo "<option value=\"" . htmlspecialchars($s['name']) . "\">" . htmlspecialchars($s['name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=\"Total Grooming\">Total Grooming</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase">Stylist / Master Barber *</label>
                            <select name="stylist_name" class="form-select bg-dark text-white border-secondary small">
                                <?php
                                $stQ = mysqli_query($conn, "SELECT name FROM staff WHERE status='Active'");
                                if ($stQ && mysqli_num_rows($stQ) > 0) {
                                    while ($st = mysqli_fetch_assoc($stQ)) {
                                        echo "<option value=\"" . htmlspecialchars($st['name']) . "\">" . htmlspecialchars($st['name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=\"Zachary Nickell\">Zachary Nickell</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase">Rating *</label>
                            <select name="rating" class="form-select bg-dark text-white border-secondary small">
                                <option value="5" selected>⭐⭐⭐⭐⭐ (5/5) Exceptional</option>
                                <option value="4">⭐⭐⭐⭐ (4/5) Very Good</option>
                                <option value="3">⭐⭐⭐ (3/5) Average</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary small text-uppercase">Your Review Message *</label>
                            <textarea name="comment" rows="3" class="form-control bg-dark text-white border-secondary small" placeholder="Tell us about your haircut, hot towel therapy, or service..." required></textarea>
                        </div>

                        <button type="submit" name="submit_review" class="btn w-100 text-dark fw-bold rounded-pill" style="background: linear-gradient(135deg, #d4af37, #aa7c11); border: none;">
                            Submit VIP Review →
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>