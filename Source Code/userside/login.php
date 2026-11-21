<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_msg = "";
$success_msg = "";

if (isset($_POST['login'])) {
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $user_query = mysqli_query($conn, "SELECT * FROM `user` WHERE `email`='$safeEmail' LIMIT 1");

        if ($user_query && mysqli_num_rows($user_query) > 0) {
            $user = mysqli_fetch_assoc($user_query);
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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_role'] = $user['role'];

                $redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
                if ($user['role'] === 'admin') {
                    header("Location: ../dashboard/admin.php");
                    exit();
                } elseif ($user['role'] === 'receptionist') {
                    header("Location: ../dashboard/reception.php");
                    exit();
                } elseif (!empty($redirect)) {
                    header("Location: " . $redirect);
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                $error_msg = "Invalid password. Please try again.";
            }
        } else {
            $error_msg = "No registered account found with this email address.";
        }
    }
}

$pageTitle = "Member Sign In | Elegance Salon Karachi";
include 'header.php';
?>
    <div class="auth-wrapper-body" data-aos="fade-up" data-aos-duration="1000">
        <div class="salon-card" data-aos="zoom-in" data-aos-duration="800">
            <h2 class="salon-title">Member Sign In</h2>
            <p class="salon-subtitle">Elegance Salon Karachi</p>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'login_required'): ?>
                <div class="alert alert-warning py-2 small mb-3 text-center" style="border-radius: 8px; background: rgba(212,175,55,0.15); border: 1px solid #d4af37; color: #d4af37;">
                    Please sign in to your account to reserve an appointment or access salon services.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success py-2 small mb-3 text-center" style="border-radius: 8px;">
                    Account created successfully! Please enter your password to sign in.
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger py-2 small mb-3 text-center" style="border-radius: 8px;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success py-2 small mb-3 text-center" style="border-radius: 8px;">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . htmlspecialchars($_GET['redirect']) : ''; ?>" method="POST" autocomplete="off">
                <!-- Email -->
                <div class="field-group mb-3">
                    <label class="form-label-salon">Email Address *</label>
                    <input type="email" name="email" class="auth-form-control" placeholder="e.g. name@example.com" autocomplete="off" required>
                </div>

                <!-- Password -->
                <div class="field-group mb-4">
                    <label class="form-label-salon">Password *</label>
                    <input type="password" name="password" id="clientPassInput" class="auth-form-control" placeholder="••••••••" autocomplete="new-password" required>
                </div>

                <div class="mb-3 p-3 rounded" style="background:rgba(212,175,55,0.06);border:1px solid rgba(212,175,55,0.25);">
                    <div class="small fw-semibold mb-2" style="color:var(--gold,#d4af37);">✦ Quick Demo Credentials (1-Click Fill):</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm" style="background:#1e1a12;border:1px solid #d4af37;color:#d4af37;font-size:11.5px;" onclick="fillClientCreds('client@elegance.pk', 'client123')">
                            👤 Client (client@elegance.pk)
                        </button>
                        <button type="button" class="btn btn-sm" style="background:#1e1a12;border:1px solid rgba(255,255,255,0.2);color:#aaa;font-size:11.5px;" onclick="fillClientCreds('admin@elegance.pk', 'admin123')">
                            👑 Admin (admin@elegance.pk)
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-submit w-100">Sign In →</button>
            </form>

            <script>
                function fillClientCreds(email, pass) {
                    var eInput = document.querySelector('input[name="email"]');
                    var pInput = document.getElementById('clientPassInput');
                    if (eInput) eInput.value = email;
                    if (pInput) pInput.value = pass;
                }
            </script>

            <button class="btn-back mt-3" onclick="window.location.href='index.php'">← Back to Home</button>
            <a href="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . htmlspecialchars($_GET['redirect']) : ''; ?>" class="salon-link mt-2">Don't have an account? <span>Register here</span></a>
        </div>
    </div>

<?php include 'footer.php'; ?>

