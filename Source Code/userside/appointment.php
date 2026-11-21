<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check user login session (Login Required)
if (!isset($_SESSION['user_id'])) {
    $redirectTarget = 'appointment.php';
    if (!empty($_SERVER['QUERY_STRING'])) {
        $redirectTarget .= '?' . $_SERVER['QUERY_STRING'];
    }
    header("Location: login.php?redirect=" . urlencode($redirectTarget) . "&msg=login_required");
    exit();
}

$pageTitle = "Book Luxury Appointment | Elegance Salon Karachi";
$userId = intval($_SESSION['user_id']);
$userClientName = $_SESSION['user_name'] ?? '';
$userClientEmail = $_SESSION['user_email'] ?? '';
$userClientPhone = $_SESSION['user_phone'] ?? '';
$bookingSuccess = false;
$bookingData = null;

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_name'])) {
    $clientName = trim($_POST['client_name']);
    $clientPhone = trim($_POST['client_phone']);
    $clientEmail = trim($_POST['client_email']);
    $serviceId = isset($_POST['service_id']) ? $_POST['service_id'] : '';
    $stylistId = isset($_POST['stylist_id']) ? $_POST['stylist_id'] : '';
    $apptDate = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
    $apptTime = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
    $paymentMode = isset($_POST['paymentMode']) ? $_POST['paymentMode'] : 'Cash at Salon';

    // Fetch service from database
    $serviceName = 'Total Grooming';
    $servicePrice = 5000;
    if (!empty($serviceId)) {
        $srvQuery = mysqli_query($conn, "SELECT name, price FROM `services` WHERE id='$serviceId' LIMIT 1");
        if ($srvQuery && $srvRow = mysqli_fetch_assoc($srvQuery)) {
            $serviceName = $srvRow['name'];
            $servicePrice = $srvRow['price'];
        }
    }
    // Fetch stylist from database
    $stylistName = 'Zachary Nickell';
    if (!empty($stylistId)) {
        $stQuery = mysqli_query($conn, "SELECT name FROM `staff` WHERE id='$stylistId' LIMIT 1");
        if ($stQuery && $stRow = mysqli_fetch_assoc($stQuery)) {
            $stylistName = $stRow['name'];
        }
    }

    $tokenNum = sprintf('%02d', rand(10, 99));
    $bookingRef = 'ELG-' . rand(1000, 9999);
    $commissionAmt = $servicePrice * 0.18;
    $chairNo = rand(1, 4);
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $sIdVal = !empty($serviceId) ? intval($serviceId) : 0;
    $stIdVal = !empty($stylistId) ? intval($stylistId) : 0;

    $sNameEsc = mysqli_real_escape_string($conn, $serviceName);
    $stNameEsc = mysqli_real_escape_string($conn, $stylistName);
    $cNameEsc = mysqli_real_escape_string($conn, $clientName);
    $cPhoneEsc = mysqli_real_escape_string($conn, $clientPhone);
    $cEmailEsc = mysqli_real_escape_string($conn, $clientEmail);
    $aDateEsc = mysqli_real_escape_string($conn, $apptDate);
    $aTimeEsc = mysqli_real_escape_string($conn, $apptTime);
    $pModeEsc = mysqli_real_escape_string($conn, $paymentMode);

    $uIdSql = ($userId > 0) ? "'$userId'" : "NULL";
    $sIdSql = ($sIdVal > 0) ? "'$sIdVal'" : "NULL";
    $stIdSql = ($stIdVal > 0) ? "'$stIdVal'" : "NULL";

    $ins_sql = "INSERT INTO `appointments`
                (`user_id`, `service_id`, `stylist_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_name`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`)
                VALUES
                ($uIdSql, $sIdSql, $stIdSql, '$bookingRef', '$tokenNum', '$cNameEsc', '$cPhoneEsc', '$cEmailEsc', '$sNameEsc', '$stNameEsc', '$aDateEsc', '$aTimeEsc', '$chairNo', '$servicePrice', '$commissionAmt', '$pModeEsc', 'Unpaid', 'Confirmed')";

    $ins_run = mysqli_query($conn, $ins_sql);

    $bookingSuccess = true;
    $bookingData = [
        'id' => $bookingRef,
        'token' => $tokenNum,
        'client' => $clientName,
        'phone' => $clientPhone,
        'email' => $clientEmail,
        'service' => $serviceName,
        'stylist' => $stylistName,
        'date' => $apptDate,
        'time' => $apptTime,
        'price' => $servicePrice,
        'payment' => $paymentMode,
        'status' => 'Confirmed'
    ];
}

include 'header.php';
?>

    <!-- APPOINTMENT HERO -->
    <section class="appointment-hero" id="hero-section">
        <div class="container text-center appointment-hero-content" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-uppercase tracking-widest text-warning small fw-medium d-block mb-2" data-aos="fade-down" data-aos-delay="100">BESPOKE RESERVATIONS</span>
            <h1 class="appointment-hero-title" data-aos="fade-up" data-aos-delay="200">Reserve Your Slot</h1>
            <p class="text-light fw-light small mx-auto mb-0" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="300">
                Flagship Lounge &bull; Main Tariq Road, Karachi. Select your signature grooming treatment and dedicated master barber.
            </p>
        </div>
    </section>

    <!-- APPOINTMENT MAIN SECTION -->
    <section class="appointment-main-section">
        <div class="container">
            <div class="text-center mx-auto mb-5" data-aos="fade-up" data-aos-duration="900">
                <div class="section-badge" data-aos="zoom-in">SALON RESERVATIONS</div>
                <h2 class="appointment-sub-title" data-aos="fade-up" data-aos-delay="100">Select your luxury treatment & barber in Karachi.</h2>
                <p class="appointment-desc mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Elegance Salon Karachi offers an integrated booking portal with real-time floor chair allocation and instant queue tokens.
                </p>
            </div>

            <form id="appointmentForm" action="appointment.php" method="POST" onsubmit="return handleAppointmentSubmit(event)">
                <div class="row g-5 align-items-start">

                    <!-- LEFT COLUMN: BOOKING PORTAL FORM -->
                    <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1000">
                        <div class="booking-form-card">
                            <h3 class="form-heading mb-1">Appointment Details</h3>
                            <p class="form-subtext mb-4">Choose your treatment, stylist, and preferred schedule.</p>

                            <!-- 1. Select Service Package -->
                            <div class="mb-4">
                                <label class="booking-step-title">
                                    <span>1. Select Service Package *</span>
                                    <span class="badge-hint">Choose Treatment</span>
                                </label>
                                <div class="row g-3" id="serviceCardsContainer">
                                    <?php
                                    $srvFetch = mysqli_query($conn, "SELECT * FROM `services` WHERE `is_active`=1 ORDER BY id ASC");
                                    $getSrv = isset($_GET['service']) ? trim(html_entity_decode($_GET['service'])) : '';
                                    $getSrvId = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
                                    $firstSrv = true;
                                    $defaultSrvPrice = 5000;
                                    $defaultSrvDuration = '120 min';
                                    $defaultSrvName = 'Total Grooming';
                                    $defaultSrvId = 1;

                                    if ($srvFetch && mysqli_num_rows($srvFetch) > 0):
                                        while ($sRow = mysqli_fetch_assoc($srvFetch)):
                                            $isSel = false;
                                            if ($getSrvId > 0 && $getSrvId == $sRow['id']) {
                                                $isSel = true;
                                            } elseif (!empty($getSrv) && (strcasecmp($sRow['name'], $getSrv) === 0 || stripos($sRow['name'], $getSrv) !== false)) {
                                                $isSel = true;
                                            } elseif ($firstSrv && $getSrvId === 0 && empty($getSrv)) {
                                                $isSel = true;
                                            }
                                            if ($isSel) {
                                                $defaultSrvPrice = $sRow['price'];
                                                $defaultSrvDuration = $sRow['duration'];
                                                $defaultSrvName = $sRow['name'];
                                                $defaultSrvId = $sRow['id'];
                                                $firstSrv = false;
                                            }
                                            $imgThumb = !empty($sRow['hero_image']) ? $sRow['hero_image'] : 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=200&auto=format&fit=crop';
                                    ?>
                                        <div class="col-md-6">
                                            <div class="service-pick-card <?php echo $isSel ? 'active' : ''; ?>"
                                                 onclick="pickService(this, <?php echo $sRow['id']; ?>, '<?php echo addslashes($sRow['name']); ?>', <?php echo $sRow['price']; ?>, '<?php echo addslashes($sRow['duration']); ?>', '<?php echo addslashes($imgThumb); ?>')">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo htmlspecialchars($imgThumb); ?>" class="service-pick-thumb" alt="<?php echo htmlspecialchars($sRow['name']); ?>">
                                                    <div style="flex:1;min-width:0;">
                                                        <div class="service-pick-title text-truncate"><?php echo htmlspecialchars($sRow['name']); ?></div>
                                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                                            <span class="service-pick-price">Rs. <?php echo number_format($sRow['price']); ?></span>
                                                            <span class="service-pick-duration"><?php echo htmlspecialchars($sRow['duration']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </div>
                                <input type="hidden" name="service_id" id="hiddenServiceId" value="<?php echo $defaultSrvId; ?>">
                                <div class="error-msg" id="serviceError">Please select a service package.</div>
                            </div>

                            <!-- 2. Choose Master Barber -->
                            <div class="mb-4">
                                <label class="booking-step-title">
                                    <span>2. Choose Master Barber *</span>
                                    <span class="badge-hint">Assigned Craftsman</span>
                                </label>
                                <div class="row g-3" id="stylistCardsContainer">
                                    <?php
                                    $staffFetch = mysqli_query($conn, "SELECT * FROM `staff` WHERE `status` = 'Active' ORDER BY id ASC");
                                    $getSty = isset($_GET['stylist']) ? trim(html_entity_decode($_GET['stylist'])) : '';
                                    $getStyId = isset($_GET['stylist_id']) ? intval($_GET['stylist_id']) : 0;
                                    $firstSt = true;
                                    $defaultStylistName = 'Zachary Nickell';
                                    $defaultStylistId = 1;

                                    if ($staffFetch && mysqli_num_rows($staffFetch) > 0):
                                        while ($stRow = mysqli_fetch_assoc($staffFetch)):
                                            $isStSel = false;
                                            if ($getStyId > 0 && $getStyId == $stRow['id']) {
                                                $isStSel = true;
                                            } elseif (!empty($getSty) && (strcasecmp($stRow['name'], $getSty) === 0 || stripos($stRow['name'], $getSty) !== false)) {
                                                $isStSel = true;
                                            } elseif ($firstSt && $getStyId === 0 && empty($getSty)) {
                                                $isStSel = true;
                                            }
                                            if ($isStSel) {
                                                $defaultStylistName = $stRow['name'];
                                                $defaultStylistId = $stRow['id'];
                                                $firstSt = false;
                                            }
                                            $stPhoto = !empty($stRow['image']) ? $stRow['image'] : 'assets/images/homepage/stylist/1.jpg';
                                    ?>
                                        <div class="col-md-6">
                                            <div class="stylist-pick-card <?php echo $isStSel ? 'active' : ''; ?>"
                                                 onclick="pickStylist(this, <?php echo $stRow['id']; ?>, '<?php echo addslashes($stRow['name']); ?>')">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo htmlspecialchars($stPhoto); ?>" class="stylist-pick-photo" alt="<?php echo htmlspecialchars($stRow['name']); ?>">
                                                    <div style="flex:1;min-width:0;">
                                                        <div class="stylist-pick-name text-truncate"><?php echo htmlspecialchars($stRow['name']); ?></div>
                                                        <div class="stylist-pick-spec"><?php echo htmlspecialchars($stRow['specialization'] ?? 'Master Stylist'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </div>
                                <input type="hidden" name="stylist_id" id="hiddenStylistId" value="<?php echo $defaultStylistId; ?>">
                                <div class="error-msg" id="stylistError">Please select a master barber.</div>
                            </div>

                            <!-- 3. Date & Available Time Slots -->
                            <div class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <div class="custom-input-group mb-0">
                                            <label class="custom-label">3. APPOINTMENT DATE *</label>
                                            <input type="date" class="custom-input" name="appointment_date" id="apptDateInput"
                                                   value="<?php echo date('Y-m-d'); ?>"
                                                   min="<?php echo date('Y-m-d'); ?>"
                                                   onchange="updateSummary();" required>
                                            <div class="error-msg" id="dateError">Please select a date.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="custom-label mb-2">4. PREFERRED TIME SLOT *</label>
                                        <div class="time-slots-grid" id="timeSlotsWrap">
                                            <button type="button" class="time-chip active" onclick="pickTimeSlot(this, '10:00 AM')">10:00 AM</button>
                                            <button type="button" class="time-chip" onclick="pickTimeSlot(this, '11:30 AM')">11:30 AM</button>
                                            <button type="button" class="time-chip" onclick="pickTimeSlot(this, '02:00 PM')">02:00 PM</button>
                                            <button type="button" class="time-chip" onclick="pickTimeSlot(this, '04:30 PM')">04:30 PM</button>
                                            <button type="button" class="time-chip" onclick="pickTimeSlot(this, '07:00 PM')">07:00 PM</button>
                                            <button type="button" class="time-chip" onclick="pickTimeSlot(this, '08:30 PM')">08:30 PM</button>
                                        </div>
                                        <input type="hidden" name="appointment_time" id="hiddenApptTime" value="10:00 AM">
                                        <div class="error-msg" id="timeError">Please choose a time slot.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Client Information -->
                            <div class="border-top pt-4 mt-4">
                                <label class="booking-step-title mb-3">5. Client Details</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="custom-input-group mb-2">
                                            <label class="custom-label">FULL NAME *</label>
                                            <input type="text" class="custom-input" id="clientName" name="client_name" placeholder="e.g. Ali Ahmed" value="<?php echo htmlspecialchars($userClientName); ?>" autocomplete="off" oninput="updateSummary();" required>
                                            <div class="error-msg" id="nameError">Please enter your full name.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-input-group mb-2">
                                            <label class="custom-label">PHONE NUMBER (03XXXXXXXXX) *</label>
                                            <input type="tel" class="custom-input" id="clientPhone" name="client_phone" placeholder="e.g. 03001234567" value="<?php echo htmlspecialchars($userClientPhone); ?>" maxlength="11" autocomplete="off" oninput="updateSummary();" required>
                                            <div class="error-msg" id="phoneError">Enter a valid 11-digit mobile number.</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="custom-input-group mb-0">
                                            <label class="custom-label">EMAIL ADDRESS (FOR BOOKING PASS)</label>
                                            <input type="email" class="custom-input" id="clientEmail" name="client_email" placeholder="e.g. client@example.com" value="<?php echo htmlspecialchars($userClientEmail); ?>" autocomplete="off" oninput="updateSummary();">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. Payment Preference -->
                            <div class="border-top pt-4 mt-4">
                                <label class="custom-label mb-2">6. PAYMENT SETTLEMENT *</label>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="pay-chip active" onclick="selectPayChip(this, 'Cash at Salon')">
                                            <input type="radio" name="paymentMode" value="Cash at Salon" checked style="display:none;">
                                            <span class="pay-chip-icon">💵</span>
                                            <div>
                                                <div class="pay-chip-title">Pay at Front Desk</div>
                                                <div class="pay-chip-sub">Cash or Card upon arrival</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="pay-chip" onclick="selectPayChip(this, 'Digital Wallet / Card')">
                                            <input type="radio" name="paymentMode" value="Digital Wallet / Card" style="display:none;">
                                            <span class="pay-chip-icon">💳</span>
                                            <div>
                                                <div class="pay-chip-title">JazzCash / EasyPaisa / Card</div>
                                                <div class="pay-chip-sub">Digital contactless payment</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- RIGHT COLUMN: REAL-TIME SUMMARY SLIP -->
                    <div class="col-lg-5" data-aos="fade-left" data-aos-duration="1000">
                        <div class="summary-card-luxury">
                            <div class="summary-header">
                                <span class="text-uppercase tracking-widest text-secondary small fw-medium d-block mb-1" style="letter-spacing: 2px; font-size: 11px;">FLAGSHIP LOUNGE RECEIPT</span>
                                <h4 class="fw-normal mb-1" id="sumServiceTitle"><?php echo htmlspecialchars($defaultSrvName); ?></h4>
                                <span class="small text-secondary" id="sumDurationText">Duration: <?php echo htmlspecialchars($defaultSrvDuration); ?></span>
                            </div>

                            <div class="summary-row">
                                <span>Master Stylist:</span>
                                <span class="summary-val" id="sumStylist"><?php echo htmlspecialchars($defaultStylistName); ?></span>
                            </div>

                            <div class="summary-row">
                                <span>Date:</span>
                                <span class="summary-val" id="sumDate">Today</span>
                            </div>

                            <div class="summary-row">
                                <span>Time Slot:</span>
                                <span class="summary-val" id="sumTime">10:00 AM</span>
                            </div>

                            <div class="summary-row">
                                <span>Payment Mode:</span>
                                <span class="summary-val" id="sumPayment">Cash at Salon</span>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-row">
                                <span>Service Fee:</span>
                                <span class="summary-val" id="sumPrice">Rs. <?php echo number_format($defaultSrvPrice); ?></span>
                            </div>

                            <div class="summary-row">
                                <span>Salon GST / Taxes:</span>
                                <span class="summary-val text-success">Included</span>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-total">
                                <span>Total Payable:</span>
                                <span class="total-amount-val" id="sumTotal">Rs. <?php echo number_format($defaultSrvPrice); ?></span>
                            </div>

                            <button type="submit" name="book_appointment" class="submit-btn" id="confirmBookingBtn">
                                Confirm & Reserve Appointment →
                            </button>

                            <div class="text-center mt-3">
                                <small class="text-muted" style="font-size: 12px;">
                                    ✓ Instant queue token & live reception desk synchronization
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>

    <!-- BOOKING SUCCESS CONFIRMATION MODAL -->
    <div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content text-dark" style="background: #ffffff; border: 1px solid #e0e0e0; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
                <div class="modal-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 64px; height: 64px; background: #000; color: #fff;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>

                    <h3 class="fw-normal mb-1 text-dark" style="font-size: 24px;">Appointment Reserved!</h3>
                    <p class="text-secondary small mb-3">Your session has been queued at Elegance Salon Tariq Road Flagship.</p>

                    <div class="p-3 rounded-4 mb-4 text-start" style="background: #faf9f6; border: 1px solid #eeeeee;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                            <span class="text-secondary small fw-medium">TOKEN NUMBER:</span>
                            <span class="badge bg-dark text-white fw-bold px-3 py-1 fs-6" id="modalTokenBadge">TK-09</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Booking Ref:</span>
                            <span class="fw-medium text-dark" id="modalBookingId">ELG-9110</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Client Name:</span>
                            <span class="text-dark" id="modalClientName">--</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Service:</span>
                            <span class="text-dark fw-medium" id="modalService">--</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Master Barber:</span>
                            <span class="text-dark fw-medium" id="modalStylist">--</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Schedule:</span>
                            <span class="text-dark" id="modalSchedule">--</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Total Payable:</span>
                            <span class="fw-bold text-dark fs-6" id="modalTotal">Rs. 5,000</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="submit-btn text-decoration-none d-inline-block text-center py-3">
                            View in My Appointments →
                        </a>
                        <button type="button" class="btn btn-outline-dark py-2 rounded-pill small" data-bs-dismiss="modal" onclick="window.location.href='index.php'">
                            Return to Website
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT FOR DYNAMIC INTERACTIVE APPOINTMENT RESERVATIONS -->
    <script>
        var currentService = {
            id: <?php echo $defaultSrvId; ?>,
            name: "<?php echo addslashes($defaultSrvName); ?>",
            price: <?php echo $defaultSrvPrice; ?>,
            duration: "<?php echo addslashes($defaultSrvDuration); ?>"
        };
        var currentStylist = {
            id: <?php echo $defaultStylistId; ?>,
            name: "<?php echo addslashes($defaultStylistName); ?>"
        };
        var currentSlot = "10:00 AM";
        var currentPayment = "Cash at Salon";

        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('apptDateInput');
            if (dateInput && !dateInput.value) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.min = today;
                dateInput.value = today;
            }
            updateSummary();

            <?php if ($bookingSuccess && $bookingData): ?>
                showConfirmationModal(<?php echo json_encode($bookingData); ?>);
            <?php endif; ?>
        });

        function pickService(el, id, name, price, duration, img) {
            document.querySelectorAll('.service-pick-card').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            currentService = { id: id, name: name, price: price, duration: duration };
            document.getElementById('hiddenServiceId').value = id;
            updateSummary();
        }

        function pickStylist(el, id, name, spec) {
            document.querySelectorAll('.stylist-pick-card').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            currentStylist = { id: id, name: name };
            document.getElementById('hiddenStylistId').value = id;
            updateSummary();
        }

        function pickTimeSlot(el, slot) {
            document.querySelectorAll('.time-chip').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            currentSlot = slot;
            document.getElementById('hiddenApptTime').value = slot;
            updateSummary();
        }

        function selectPayChip(el, mode) {
            document.querySelectorAll('.pay-chip').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');
            const radio = el ? el.querySelector('input[type="radio"]') : null;
            if (radio) radio.checked = true;
            currentPayment = mode;
            updateSummary();
        }

        function updateSummary() {
            // Service
            if (document.getElementById('sumServiceTitle')) {
                document.getElementById('sumServiceTitle').innerText = currentService.name;
                document.getElementById('sumDurationText').innerText = 'Duration: ' + currentService.duration;
                document.getElementById('sumPrice').innerText = 'Rs. ' + Number(currentService.price).toLocaleString();
                document.getElementById('sumTotal').innerText = 'Rs. ' + Number(currentService.price).toLocaleString();
            }

            // Stylist
            if (document.getElementById('sumStylist')) {
                document.getElementById('sumStylist').innerText = currentStylist.name;
            }

            // Date
            const dInput = document.getElementById('apptDateInput');
            if (dInput && dInput.value && document.getElementById('sumDate')) {
                document.getElementById('sumDate').innerText = dInput.value;
            }

            // Time
            if (document.getElementById('sumTime')) {
                document.getElementById('sumTime').innerText = currentSlot;
            }

            // Payment
            if (document.getElementById('sumPayment')) {
                document.getElementById('sumPayment').innerText = currentPayment;
            }
        }

        function handleAppointmentSubmit(event) {
            const name = document.getElementById('clientName').value.trim();
            const phone = document.getElementById('clientPhone').value.trim();

            if (!name || name.length < 3) {
                event.preventDefault();
                alert('Please enter your valid full name.');
                return false;
            }

            if (!phone || phone.length < 11 || !phone.startsWith('03')) {
                event.preventDefault();
                alert('Please enter a valid 11-digit mobile number starting with 03 (e.g. 03001234567).');
                return false;
            }

            return true;
        }

        function showConfirmationModal(item) {
            document.getElementById('modalTokenBadge').innerText = item.token;
            document.getElementById('modalBookingId').innerText = item.id;
            document.getElementById('modalClientName').innerText = item.client;
            document.getElementById('modalService').innerText = item.service;
            document.getElementById('modalStylist').innerText = item.stylist;
            document.getElementById('modalSchedule').innerText = item.date + ' at ' + item.time;
            document.getElementById('modalTotal').innerText = 'Rs. ' + parseFloat(item.price).toLocaleString();

            const modalEl = document.getElementById('bookingSuccessModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modalInstance = new bootstrap.Modal(modalEl);
                modalInstance.show();
            }
        }
    </script>

<?php include 'footer.php'; ?>
