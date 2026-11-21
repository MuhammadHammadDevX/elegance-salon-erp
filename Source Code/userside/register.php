<?php
$pageTitle = "Client Registration | Elegance Salon Karachi";
include 'header.php';
include_once __DIR__ . '/../config.php';

if (isset($_POST['register'])) {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Validation check
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error_msg = "All fields marked with * are required.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        $safeName = mysqli_real_escape_string($conn, $name);
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $safePhone = mysqli_real_escape_string($conn, $phone);

        // 2. Strict Check if email or phone already exists in user table
        $checkEmail = mysqli_query($conn, "SELECT id FROM `user` WHERE `email`='$safeEmail' LIMIT 1");
        $checkPhone = mysqli_query($conn, "SELECT id FROM `user` WHERE `phone`='$safePhone' LIMIT 1");

        if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
            $error_msg = "This Email is already registered. Please sign in to your account.";
        } elseif ($checkPhone && mysqli_num_rows($checkPhone) > 0) {
            $error_msg = "This Phone Number (03XX...) is already registered. Please use another number or sign in.";
        } else {
            // 3. Hash password
            $hashedPassword = md5($password);

            // 4. Insert new client record
            $insert_query = "INSERT INTO `user` (`name`, `email`, `phone`, `password`, `role`)
                             VALUES ('$safeName', '$safeEmail', '$safePhone', '$hashedPassword', 'client')";

            $run = mysqli_query($conn, $insert_query);

            if ($run) {
                $success_msg = "Account created successfully! Redirecting you to Sign In...";
                $reg_success = true;
            } else {
                $error_msg = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="auth-wrapper-body" data-aos="fade-up" data-aos-duration="1000">
    <div class="salon-card" style="max-width: 500px;" data-aos="zoom-in" data-aos-duration="800">
        <h2 class="salon-title">Client Registration</h2>
        <p class="salon-subtitle">Elegance Salon Karachi</p>

        <!-- Error Notification -->
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger py-2 small mb-3 text-center" style="border-radius: 8px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Success Notification -->
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success py-3 small mb-3 text-center shadow" style="border-radius: 12px; background: rgba(5, 205, 153, 0.15); border: 1px solid #05CD99; color: #05CD99;">
                <div class="spinner-border spinner-border-sm me-2" role="status" style="vertical-align: middle;"></div>
                <strong><?php echo $success_msg; ?></strong>
                <div class="mt-2 text-white opacity-75" style="font-size: 11px;">Taking you to member sign in...</div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'login.php?registered=1';
                }, 1600);
            </script>
        <?php endif; ?>
        <!-- HTML FORM -->
        <form action="register.php" method="POST" autocomplete="off">

            <!-- Full Name -->
            <div class="field-group mb-3">
                <label class="form-label-salon">Full Name *</label>
                <input type="text" name="name" class="auth-form-control" placeholder="e.g. Ali Ahmed" autocomplete="off" required>
            </div>
            <!-- Email Address -->
            <div class="field-group mb-3">
                <label class="form-label-salon">Email Address *</label>
                <input type="email" name="email" class="auth-form-control" placeholder="e.g. name@example.com" autocomplete="off" required>
            </div>
            <!-- Phone Number -->
            <div class="field-group mb-3">
                <label class="form-label-salon">Phone Number (03XXXXXXXXX) *</label>
                <input type="tel" name="phone" class="auth-form-control" placeholder="e.g. 03001234567" maxlength="11" pattern="03[0-9]{9}" autocomplete="off" required>
            </div>
            <!-- Password -->
            <div class="field-group mb-4">
                <label class="form-label-salon">Password *</label>
                <input type="password" name="password" class="auth-form-control" placeholder="Create a strong password (min 6 chars)" minlength="6" autocomplete="new-password" required>
            </div>
            <button type="submit" name="register" class="btn-submit w-100">Create Account →</button>
        </form>

        <button class="btn-back mt-3" onclick="window.location.href='index.php'">← Back to Home</button>
    </div>
</div>

<?php
include 'footer.php';

?>