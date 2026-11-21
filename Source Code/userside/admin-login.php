<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_msg = "";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_msg = "Please provide both Staff Email and Password.";
    } else {
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $query = mysqli_query($conn, "SELECT * FROM `user` WHERE `email`='$safeEmail' LIMIT 1");

        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            $db_pass = $user['password'];

            $is_valid = false;
            if (md5($password) === $db_pass) {
                $is_valid = true;
            } elseif (password_verify($password, $db_pass)) {
                $is_valid = true;
            } elseif ($password === $db_pass) {
                $is_valid = true;
            }

            if ($is_valid) {
                // Check if account has staff privileges
                if ($user['role'] === 'admin') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_phone'] = $user['phone'];
                    $_SESSION['user_role'] = 'admin';

                    header('Location: ../dashboard/admin.php');
                    exit();
                } elseif ($user['role'] === 'receptionist') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_phone'] = $user['phone'];
                    $_SESSION['user_role'] = 'receptionist';

                    header('Location: ../dashboard/reception.php');
                    exit();
                } else {
                    $error_msg = "Access Denied: This portal is reserved for Salon Staff only. Please use Member Sign In.";
                }
            } else {
                $error_msg = "Incorrect security password.";
            }
        } else {
            $error_msg = "No staff account found with this email.";
        }
    }
}

$pageTitle = "Staff & Admin Portal | Elegance Salon Karachi";
include 'header.php';
?>

    <div class="auth-wrapper-body" data-aos="fade-up" data-aos-duration="1000">
        <div class="salon-card" style="max-width: 480px;" data-aos="zoom-in" data-aos-duration="800">
            <div class="text-center mb-3">
                <a href="index.php" class="logo text-center d-inline-block text-decoration-none">
                    ELEGANCE
                    <span style="letter-spacing: 4px;">STAFF PORTAL</span>
                </a>
            </div>

            <p class="text-center text-secondary small mb-4 fw-light">Authorized access for Salon Executives & Front Desk Staff</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-warning py-2 small mb-3 text-center" style="border-radius: 8px; background: rgba(212,175,55,0.15); border: 1px solid #d4af37; color: #d4af37;">
                    Please sign in with authorized credentials to access the Dashboard.
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger py-2 small mb-3 text-center" style="border-radius: 8px;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="admin-login.php" method="POST" autocomplete="off">
                <div class="field-group mb-3">
                    <label class="form-label-salon">Staff Email *</label>
                    <input type="email" class="auth-form-control" name="email" placeholder="e.g. admin@elegance.pk or reception@elegance.pk" autocomplete="off" required>
                </div>

                <div class="field-group mb-4">
                    <label class="form-label-salon">Security Password *</label>
                    <input type="password" class="auth-form-control" name="password" id="staffPassInput" placeholder="••••••••" minlength="6" autocomplete="new-password" required>
                </div>

                <div class="mb-3 p-3 rounded" style="background:rgba(212,175,55,0.06);border:1px solid rgba(212,175,55,0.25);">
                    <div class="small fw-semibold mb-2" style="color:var(--gold,#d4af37);">✦ Quick Demo Credentials (1-Click Fill):</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm" style="background:#1e1a12;border:1px solid #d4af37;color:#d4af37;font-size:11.5px;" onclick="fillStaffCreds('admin@elegance.pk', 'admin123')">
                            👑 Admin (admin@elegance.pk)
                        </button>
                        <button type="button" class="btn btn-sm" style="background:#111b15;border:1px solid #05cd99;color:#05cd99;font-size:11.5px;" onclick="fillStaffCreds('reception@elegance.pk', 'reception123')">
                            🛎️ Reception (reception@elegance.pk)
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit w-100">Sign In to Staff Console →</button>
            </form>

            <script>
                function fillStaffCreds(email, pass) {
                    var eInput = document.querySelector('input[name="email"]');
                    var pInput = document.getElementById('staffPassInput');
                    if (eInput) eInput.value = email;
                    if (pInput) pInput.value = pass;
                }
            </script>

            <button class="btn-back mt-3" onclick="window.location.href='index.php'">← Back to Public Website</button>
        </div>
    </div>

<?php include 'footer.php'; ?>
