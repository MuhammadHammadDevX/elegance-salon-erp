<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_msg = "";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_msg = "Please provide both Receptionist Email and Password.";
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
                if ($user['role'] === 'receptionist' || $user['role'] === 'admin') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_phone'] = $user['phone'];
                    $_SESSION['user_role'] = $user['role'];

                    // Update authentication cookies
                    setcookie('elegance_user_id', $user['id'], time() + (86400 * 30), "/");
                    setcookie('elegance_user_name', $user['name'], time() + (86400 * 30), "/");
                    setcookie('elegance_user_email', $user['email'], time() + (86400 * 30), "/");
                    setcookie('elegance_user_role', $user['role'], time() + (86400 * 30), "/");

                    header('Location: ../dashboard/reception.php');
                    exit();
                } else {
                    $error_msg = "Unauthorized: Only Front Desk staff and Admins can log in here.";
                }
            } else {
                $error_msg = "Incorrect terminal password.";
            }
        } else {
            $error_msg = "No receptionist account found with this email.";
        }
    }
}

$pageTitle = "Front Desk Reception Login | Elegance Salon Karachi";
include 'header.php';
?>

    <div class="auth-wrapper-body" data-aos="fade-up" data-aos-duration="1000">
        <div class="salon-card" style="max-width: 480px;" data-aos="zoom-in" data-aos-duration="800">
            <div class="text-center mb-3">
                <a href="index.php" class="logo text-center d-inline-block text-decoration-none">
                    ELEGANCE
                    <span style="letter-spacing: 4px; color: #05CD99;">FRONT DESK CONSOLE</span>
                </a>
            </div>

            <p class="text-center text-secondary small mb-4 fw-light">Operational login for Front Desk Receptionists & Cashiers</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-warning py-2 small mb-3 text-center" style="border-radius: 8px; background: rgba(212,175,55,0.15); border: 1px solid #d4af37; color: #d4af37;">
                    Please sign in with Receptionist or Admin credentials to access the Front Desk Console.
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger py-2 small mb-3 text-center" style="border-radius: 8px;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="reception-login.php" method="POST" autocomplete="off">
                <div class="field-group">
                    <label class="form-label-salon">Receptionist ID / Email *</label>
                    <input type="email" class="auth-form-control" name="email" placeholder="e.g. reception@elegance.pk" autocomplete="off" required>
                </div>

                <div class="field-group">
                    <label class="form-label-salon">Desk Terminal Password *</label>
                    <input type="password" class="auth-form-control" name="password" placeholder="••••••••" minlength="6" autocomplete="new-password" required>
                </div>

                <div class="field-group">
                    <label class="form-label-salon">Branch Terminal</label>
                    <select class="form-select auth-form-control">
                        <option value="tariq_road" selected>Tariq Road Flagship Branch (Terminal 01)</option>
                        <option value="clifton">Clifton Block 4 Branch (Terminal 02)</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #05CD99, #2B3674); color: #fff;">Open Reception Console →</button>
            </form>

            <button class="btn-back mt-3" onclick="window.location.href='index.php'">← Back to Public Website</button>
        </div>
    </div>

<?php include 'footer.php'; ?>
