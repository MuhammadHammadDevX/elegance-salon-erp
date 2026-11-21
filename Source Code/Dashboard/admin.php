<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strict Admin Access Control
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../userside/admin-login.php?error=unauthorized");
    exit();
}

$pageTitle = "Admin Dashboard | Elegance Salon Karachi";
$currentUserName = !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Saad Admin';
$currentUserRole = 'Executive Administrator';

// ==========================================
// 1-CLICK CSV SALES EXPORT (ACCOUNTING)
// ==========================================
if (isset($_GET['export_sales_csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Elegance_Salon_Sales_' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Booking Ref', 'Token', 'Customer Name', 'Phone', 'Email', 'Service', 'Stylist', 'Date', 'Time', 'Chair', 'Price (PKR)', 'Commission (18%)', 'Payment Mode', 'Payment Status', 'Status']);
    
    $exp_q = mysqli_query($conn, "SELECT * FROM `appointments` ORDER BY `appointment_date` DESC, `id` DESC");
    if ($exp_q) {
        while ($r = mysqli_fetch_assoc($exp_q)) {
            fputcsv($out, [
                $r['booking_ref'],
                $r['token'],
                $r['client_name'],
                $r['client_phone'],
                $r['client_email'],
                $r['service_name'],
                $r['stylist_name'],
                $r['appointment_date'],
                $r['appointment_time'],
                $r['chair_no'],
                $r['price'],
                $r['commission_amount'],
                $r['payment_mode'],
                $r['payment_status'],
                $r['status']
            ]);
        }
    }
    fclose($out);
    exit();
}

///USER_PAGE

//SERVICES FETCH (SELECT)


$query = "SELECT * FROM `user` ORDER BY `id` DESC";
$result = mysqli_query($conn, $query);

//Add User Form Submission


if (isset($_POST['add_user_submit'])){
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $role = htmlspecialchars(trim($_POST['role']));
    $password = md5($_POST['password']);

    $insert_query =" INSERT INTO `user` (`name`, `email`, `phone`, `password`, `role`)
                      VALUES ('$name', '$email', '$phone', '$password', '$role')";


    mysqli_query($conn, $insert_query);
    header("Location: admin.php?tab=users");
    exit();


}


// UPDATE USER FORM SUBMISSION

if(isset($_POST['update_user_submit'])){
    $id =$_POST['user_id'];
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $role = htmlspecialchars(trim($_POST['role']));


    $update_query = "UPDATE `user` SET `name`='$name', `email`='$email', `phone`='$phone', `role`='$role' WHERE `id`='$id'";

    mysqli_query($conn, $update_query);
    header("Location: admin.php?tab=users");
    exit();
}


// DELETE USER


if(isset($_GET['delete_user'])){
    $id =$_GET['delete_user'];

    $delete_query = "DELETE FROM `user` WHERE `id`='$id'";

    mysqli_query($conn, $delete_query);

    header("Location: admin.php?tab=users");
    exit();
}




///SERVICE_PAGE



//SERVICES FETCH (SELECT)


$service_query = "SELECT * FROM `services` ORDER BY `id` DESC";

$services_result = mysqli_query($conn, $service_query);


//SERVICE ADD FORM SUBMISSION
if (isset($_POST['add_service_submit'])) {
    $name        = htmlspecialchars(trim($_POST['name']));
    $category    = htmlspecialchars(trim($_POST['category']));
    $price       = floatval($_POST['price']);
    $duration    = htmlspecialchars(trim($_POST['duration']));
    $tag         = htmlspecialchars(trim($_POST['tag'] ?? 'SIGNATURE SERVICE'));
    $subtitle    = htmlspecialchars(trim($_POST['subtitle'] ?? ''));
    $hero_image  = htmlspecialchars(trim($_POST['hero_image'] ?? 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop'));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $card1_title = htmlspecialchars(trim($_POST['card1_title'] ?? ''));
    $card1_desc  = htmlspecialchars(trim($_POST['card1_desc'] ?? ''));
    $card1_img   = htmlspecialchars(trim($_POST['card1_img'] ?? ''));
    $card2_title = htmlspecialchars(trim($_POST['card2_title'] ?? ''));
    $card2_desc  = htmlspecialchars(trim($_POST['card2_desc'] ?? ''));
    $card2_img   = htmlspecialchars(trim($_POST['card2_img'] ?? ''));
    $card3_title = htmlspecialchars(trim($_POST['card3_title'] ?? ''));
    $card3_desc  = htmlspecialchars(trim($_POST['card3_desc'] ?? ''));
    $card3_img   = htmlspecialchars(trim($_POST['card3_img'] ?? ''));
    $package_det = htmlspecialchars(trim($_POST['package_details'] ?? ''));

    $insert_service_query = "INSERT INTO `services`
        (`name`, `category`, `price`, `duration`, `tag`, `subtitle`, `hero_image`, `description`, `card1_title`, `card1_desc`, `card1_img`, `card2_title`, `card2_desc`, `card2_img`, `card3_title`, `card3_desc`, `card3_img`, `package_details`, `bookings`, `is_active`)
        VALUES
        ('$name', '$category', '$price', '$duration', '$tag', '$subtitle', '$hero_image', '$description', '$card1_title', '$card1_desc', '$card1_img', '$card2_title', '$card2_desc', '$card2_img', '$card3_title', '$card3_desc', '$card3_img', '$package_det', 0, 1)";

    mysqli_query($conn, $insert_service_query);
    header("Location: admin.php?tab=services");
    exit();
}

//SERVICE UPDATE FORM SUBMISSION
if (isset($_POST['update_service_submit'])) {
    $id          = intval($_POST['service_id']);
    $name        = htmlspecialchars(trim($_POST['name']));
    $category    = htmlspecialchars(trim($_POST['category']));
    $price       = floatval($_POST['price']);
    $duration    = htmlspecialchars(trim($_POST['duration']));
    $tag         = htmlspecialchars(trim($_POST['tag'] ?? 'SIGNATURE SERVICE'));
    $subtitle    = htmlspecialchars(trim($_POST['subtitle'] ?? ''));
    $hero_image  = htmlspecialchars(trim($_POST['hero_image'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $card1_title = htmlspecialchars(trim($_POST['card1_title'] ?? ''));
    $card1_desc  = htmlspecialchars(trim($_POST['card1_desc'] ?? ''));
    $card1_img   = htmlspecialchars(trim($_POST['card1_img'] ?? ''));
    $card2_title = htmlspecialchars(trim($_POST['card2_title'] ?? ''));
    $card2_desc  = htmlspecialchars(trim($_POST['card2_desc'] ?? ''));
    $card2_img   = htmlspecialchars(trim($_POST['card2_img'] ?? ''));
    $card3_title = htmlspecialchars(trim($_POST['card3_title'] ?? ''));
    $card3_desc  = htmlspecialchars(trim($_POST['card3_desc'] ?? ''));
    $card3_img   = htmlspecialchars(trim($_POST['card3_img'] ?? ''));
    $package_det = htmlspecialchars(trim($_POST['package_details'] ?? ''));

    $update_service_query = "UPDATE `services`
        SET `name`='$name', `category`='$category', `price`='$price', `duration`='$duration', `tag`='$tag', `subtitle`='$subtitle', `hero_image`='$hero_image', `description`='$description', `card1_title`='$card1_title', `card1_desc`='$card1_desc', `card1_img`='$card1_img', `card2_title`='$card2_title', `card2_desc`='$card2_desc', `card2_img`='$card2_img', `card3_title`='$card3_title', `card3_desc`='$card3_desc', `card3_img`='$card3_img', `package_details`='$package_det'
        WHERE `id`='$id'";

    mysqli_query($conn, $update_service_query);
    header("Location: admin.php?tab=services");
    exit();
}


// DELETE SERVICE
if (isset($_GET['delete_service'])) {
    $id = intval($_GET['delete_service']);

    // Clean up local uploaded images if present on disk
    $svcQuery = mysqli_query($conn, "SELECT hero_image, card1_img, card2_img, card3_img FROM `services` WHERE `id`='$id' LIMIT 1");
    if ($svcQuery && $sRow = mysqli_fetch_assoc($svcQuery)) {
        $imagesToCheck = [$sRow['hero_image'], $sRow['card1_img'], $sRow['card2_img'], $sRow['card3_img']];
        foreach ($imagesToCheck as $imgPath) {
            if (!empty($imgPath) && !filter_var($imgPath, FILTER_VALIDATE_URL)) {
                $docRoot = __DIR__ . '/..';
                $candidates = [
                    $docRoot . '/' . ltrim($imgPath, '/'),
                    $docRoot . '/userside/' . ltrim($imgPath, '/'),
                    $docRoot . '/assets/' . ltrim($imgPath, '/')
                ];
                foreach ($candidates as $cand) {
                    if (file_exists($cand) && is_file($cand) && (strpos($cand, 'uploads') !== false || strpos($cand, 'custom_') !== false)) {
                        @unlink($cand);
                    }
                }
            }
        }
    }

    $delete_service_query = "DELETE FROM `services` WHERE `id`='$id'";
    mysqli_query($conn, $delete_service_query);

    header("Location: admin.php?tab=services");
    exit();
}



/// ==================== STYLISTS & STAFF PAGE ====================

// 1. STYLISTS FETCH (SELECT)
$staff_query = "SELECT * FROM `staff` ORDER BY `id` DESC";
$staff_result = mysqli_query($conn, $staff_query);

// 2. ADD STYLIST FORM SUBMISSION (INSERT)
if (isset($_POST['add_stylist_submit'])) {
    $name            = htmlspecialchars(trim($_POST['name']));
    $email           = htmlspecialchars(trim($_POST['email']));
    $phone           = htmlspecialchars(trim($_POST['phone']));
    $specialization  = htmlspecialchars(trim($_POST['specialization']));
    $commission_rate = htmlspecialchars(trim($_POST['commission_rate']));
    $work_schedule   = htmlspecialchars(trim($_POST['work_schedule']));
    $image           = htmlspecialchars(trim($_POST['image'] ?? 'assets/images/homepage/stylist/1.jpg'));
    $bio             = htmlspecialchars(trim($_POST['bio'] ?? ''));
    $status          = htmlspecialchars(trim($_POST['status']));

    $insert_staff_query = "INSERT INTO `staff` (`name`, `email`, `phone`, `specialization`, `commission_rate`, `work_schedule`, `image`, `bio`, `status`)
                           VALUES ('$name', '$email', '$phone', '$specialization', '$commission_rate', '$work_schedule', '$image', '$bio', '$status')";

    mysqli_query($conn, $insert_staff_query);
    header("Location: admin.php?tab=stylists");
    exit();
}

// 3. UPDATE STYLIST FORM SUBMISSION (UPDATE)
if (isset($_POST['update_stylist_submit'])) {
    $id              = $_POST['stylist_id'];
    $name            = htmlspecialchars(trim($_POST['name']));
    $email           = htmlspecialchars(trim($_POST['email']));
    $phone           = htmlspecialchars(trim($_POST['phone']));
    $specialization  = htmlspecialchars(trim($_POST['specialization']));
    $commission_rate = htmlspecialchars(trim($_POST['commission_rate']));
    $work_schedule   = htmlspecialchars(trim($_POST['work_schedule']));
    $image           = htmlspecialchars(trim($_POST['image'] ?? ''));
    $bio             = htmlspecialchars(trim($_POST['bio'] ?? ''));
    $status          = htmlspecialchars(trim($_POST['status']));

    $update_staff_query = "UPDATE `staff`
                           SET `name`='$name', `email`='$email', `phone`='$phone', `specialization`='$specialization', `commission_rate`='$commission_rate', `work_schedule`='$work_schedule', `image`='$image', `bio`='$bio', `status`='$status'
                           WHERE `id`='$id'";

    mysqli_query($conn, $update_staff_query);
    header("Location: admin.php?tab=stylists");
    exit();
}

// 4. DELETE STYLIST (DELETE)
if (isset($_GET['delete_stylist'])) {
    $id = intval($_GET['delete_stylist']);

    // Clean up local uploaded stylist photo if exists
    $stQuery = mysqli_query($conn, "SELECT `image` FROM `staff` WHERE `id`='$id' LIMIT 1");
    if ($stQuery && $stRow = mysqli_fetch_assoc($stQuery)) {
        $imgPath = $stRow['image'];
        if (!empty($imgPath) && !filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $docRoot = __DIR__ . '/..';
            $candidates = [
                $docRoot . '/' . ltrim($imgPath, '/'),
                $docRoot . '/userside/' . ltrim($imgPath, '/')
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_file($cand) && (strpos($cand, 'uploads') !== false || strpos($cand, 'custom_') !== false)) {
                    @unlink($cand);
                }
            }
        }
    }

    $delete_staff_query = "DELETE FROM `staff` WHERE `id`='$id'";
    mysqli_query($conn, $delete_staff_query);
    header("Location: admin.php?tab=stylists");
    exit();
}



/// ==================== STOCK & INVENTORY PAGE ====================

// 1. INVENTORY FETCH (SELECT) & SUMMARY STATS
$inventory_query = "SELECT * FROM `inventory` ORDER BY `id` DESC";
$inventory_result = mysqli_query($conn, $inventory_query);

$total_stock_items = 0;
$total_cost_valuation = 0;
$total_retail_valuation = 0;
$critical_stock_count = 0;
$out_of_stock_count = 0;

$inv_items_cache = [];
if ($inventory_result) {
    while ($r = mysqli_fetch_assoc($inventory_result)) {
        $inv_items_cache[] = $r;
        $total_stock_items++;
        $total_cost_valuation += ($r['quantity'] * $r['cost_price']);
        $total_retail_valuation += ($r['quantity'] * ($r['retail_price'] ?? 0));
        if ($r['quantity'] <= 0) {
            $out_of_stock_count++;
            $critical_stock_count++;
        } elseif ($r['quantity'] <= $r['min_threshold']) {
            $critical_stock_count++;
        }
    }
}

// 2. ADD STOCK ITEM FORM SUBMISSION (INSERT)
if (isset($_POST['add_stock_submit'])) {
    $code          = htmlspecialchars(trim($_POST['code']));
    $name          = htmlspecialchars(trim($_POST['name']));
    $brand         = htmlspecialchars(trim($_POST['brand'] ?? 'Elegance Reserve'));
    $image         = htmlspecialchars(trim($_POST['image'] ?? 'https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop'));
    $category      = htmlspecialchars(trim($_POST['category']));
    $quantity      = intval($_POST['quantity']);
    $min_threshold = intval($_POST['min_threshold']);
    $cost_price    = floatval($_POST['cost_price']);
    $retail_price  = floatval($_POST['retail_price'] ?? ($cost_price * 1.6));
    $unit_type     = htmlspecialchars(trim($_POST['unit_type'] ?? 'Pieces'));
    $supplier_name = htmlspecialchars(trim($_POST['supplier_name']));
    $last_restocked= date('Y-m-d');

    // Auto status determination
    $status = ($quantity <= 0) ? 'Out of Stock' : (($quantity <= $min_threshold) ? 'Low Stock' : 'In Stock');

    $insert_stock_query = "INSERT INTO `inventory` (`code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`)
                           VALUES ('$code', '$name', '$brand', '$image', '$category', '$quantity', '$min_threshold', '$cost_price', '$retail_price', '$unit_type', '$supplier_name', '$status', '$last_restocked')";

    mysqli_query($conn, $insert_stock_query);
    header("Location: admin.php?tab=stock");
    exit();
}

// 3. UPDATE STOCK ITEM FORM SUBMISSION (UPDATE)
if (isset($_POST['update_stock_submit'])) {
    $id            = intval($_POST['stock_id']);
    $code          = htmlspecialchars(trim($_POST['code']));
    $name          = htmlspecialchars(trim($_POST['name']));
    $brand         = htmlspecialchars(trim($_POST['brand'] ?? 'Elegance Reserve'));
    $image         = htmlspecialchars(trim($_POST['image'] ?? ''));
    $category      = htmlspecialchars(trim($_POST['category']));
    $quantity      = intval($_POST['quantity']);
    $min_threshold = intval($_POST['min_threshold']);
    $cost_price    = floatval($_POST['cost_price']);
    $retail_price  = floatval($_POST['retail_price'] ?? ($cost_price * 1.6));
    $unit_type     = htmlspecialchars(trim($_POST['unit_type'] ?? 'Pieces'));
    $supplier_name = htmlspecialchars(trim($_POST['supplier_name']));

    $status = ($quantity <= 0) ? 'Out of Stock' : (($quantity <= $min_threshold) ? 'Low Stock' : 'In Stock');

    $update_stock_query = "UPDATE `inventory`
                           SET `code`='$code', `name`='$name', `brand`='$brand', `image`='$image', `category`='$category', `quantity`='$quantity', `min_threshold`='$min_threshold', `cost_price`='$cost_price', `retail_price`='$retail_price', `unit_type`='$unit_type', `supplier_name`='$supplier_name', `status`='$status'
                           WHERE `id`='$id'";

    mysqli_query($conn, $update_stock_query);
    header("Location: admin.php?tab=stock");
    exit();
}

// 4. QUICK RESTOCK ACTION (ADD QUANTITY)
if (isset($_POST['quick_restock_submit'])) {
    $id       = intval($_POST['restock_item_id']);
    $add_qty  = intval($_POST['add_quantity']);
    $today    = date('Y-m-d');

    $check_item = mysqli_query($conn, "SELECT `quantity`, `min_threshold` FROM `inventory` WHERE `id`='$id'");
    if ($check_item && $item_data = mysqli_fetch_assoc($check_item)) {
        $new_qty = $item_data['quantity'] + $add_qty;
        $min = $item_data['min_threshold'];
        $new_status = ($new_qty <= 0) ? 'Out of Stock' : (($new_qty <= $min) ? 'Low Stock' : 'In Stock');

        mysqli_query($conn, "UPDATE `inventory` SET `quantity`='$new_qty', `last_restocked`='$today', `status`='$new_status' WHERE `id`='$id'");
    }

    header("Location: admin.php?tab=stock");
    exit();
}

// 5. DELETE STOCK ITEM (DELETE)
if (isset($_GET['delete_stock'])) {
    $id = intval($_GET['delete_stock']);

    // Clean up local uploaded product image if exists
    $invQuery = mysqli_query($conn, "SELECT `image` FROM `inventory` WHERE `id`='$id' LIMIT 1");
    if ($invQuery && $invRow = mysqli_fetch_assoc($invQuery)) {
        $imgPath = $invRow['image'];
        if (!empty($imgPath) && !filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $docRoot = __DIR__ . '/..';
            $candidates = [
                $docRoot . '/' . ltrim($imgPath, '/'),
                $docRoot . '/userside/' . ltrim($imgPath, '/')
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_file($cand) && (strpos($cand, 'uploads') !== false || strpos($cand, 'custom_') !== false)) {
                    @unlink($cand);
                }
            }
        }
    }

    $delete_stock_query = "DELETE FROM `inventory` WHERE `id`='$id'";
    mysqli_query($conn, $delete_stock_query);
    header("Location: admin.php?tab=stock");
    exit();
}



/// ==================== APPOINTMENTS PAGE ====================

// 1. APPOINTMENTS FETCH (SELECT)
$appointments_query = "SELECT * FROM `appointments` ORDER BY `appointment_date` DESC, `id` DESC";
$appointments_result = mysqli_query($conn, $appointments_query);

// 2. BOOK APPOINTMENT FORM SUBMISSION (INSERT)
if (isset($_POST['book_appointment_submit'])) {
    $ref              = 'ELG-' . rand(1000, 9999);
    $tok              = 'TK-' . rand(100, 999);
    $client_name      = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_name'])));
    $client_phone     = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_phone'])));
    $client_email     = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_email'] ?? '')));
    $service_name     = mysqli_real_escape_string($conn, clean_text(trim($_POST['service_name'])));
    $stylist_name     = mysqli_real_escape_string($conn, clean_text(trim($_POST['stylist_name'])));
    $appointment_date = mysqli_real_escape_string($conn, trim($_POST['appointment_date']));
    $appointment_time = mysqli_real_escape_string($conn, trim($_POST['appointment_time']));
    $chair_no         = intval($_POST['chair_no'] ?? 1);
    $price            = floatval($_POST['price']);
    $payment_mode     = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_mode'] ?? 'Cash')));
    $payment_status   = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_status'] ?? 'Unpaid')));
    $status           = mysqli_real_escape_string($conn, clean_text(trim($_POST['status'] ?? 'Confirmed')));
    $commission       = $price * 0.18;

    $insert_appt_query = "INSERT INTO `appointments` (`booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_name`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`)
                          VALUES ('$ref', '$tok', '$client_name', '$client_phone', '$client_email', '$service_name', '$stylist_name', '$appointment_date', '$appointment_time', '$chair_no', '$price', '$commission', '$payment_mode', '$payment_status', '$status')";
    mysqli_query($conn, $insert_appt_query);
    header("Location: admin.php?tab=appointments");
    exit();
}

// 3. UPDATE APPOINTMENT DETAILS (UPDATE)
if (isset($_POST['update_appointment_submit'])) {
    $id               = intval($_POST['appointment_id']);
    $client_name      = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_name'])));
    $client_phone     = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_phone'])));
    $client_email     = mysqli_real_escape_string($conn, clean_text(trim($_POST['client_email'] ?? '')));
    $service_name     = mysqli_real_escape_string($conn, clean_text(trim($_POST['service_name'])));
    $stylist_name     = mysqli_real_escape_string($conn, clean_text(trim($_POST['stylist_name'])));
    $appointment_date = mysqli_real_escape_string($conn, trim($_POST['appointment_date']));
    $appointment_time = mysqli_real_escape_string($conn, trim($_POST['appointment_time']));
    $chair_no         = intval($_POST['chair_no'] ?? 1);
    $price            = floatval($_POST['price']);
    $payment_mode     = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_mode'] ?? 'Cash')));
    $payment_status   = mysqli_real_escape_string($conn, clean_text(trim($_POST['payment_status'] ?? 'Unpaid')));
    $status           = mysqli_real_escape_string($conn, clean_text(trim($_POST['status'] ?? 'Confirmed')));
    $commission       = $price * 0.18;

    $update_appt_query = "UPDATE `appointments` SET
                          `client_name`='$client_name', `client_phone`='$client_phone', `client_email`='$client_email',
                          `service_name`='$service_name', `stylist_name`='$stylist_name', `appointment_date`='$appointment_date',
                          `appointment_time`='$appointment_time', `chair_no`='$chair_no', `price`='$price',
                          `commission_amount`='$commission', `payment_mode`='$payment_mode', `payment_status`='$payment_status',
                          `status`='$status' WHERE `id`='$id'";
    mysqli_query($conn, $update_appt_query);
    header("Location: admin.php?tab=appointments");
    exit();
}

// 4. QUICK APPOINTMENT STATUS TOGGLE (SEAT / DONE / CANCEL)
if (isset($_GET['set_appt_status']) && isset($_GET['appt_id'])) {
    $st = htmlspecialchars($_GET['set_appt_status']);
    $id = intval($_GET['appt_id']);
    $pay_clause = ($st === 'Completed') ? ", `payment_status`='Paid'" : "";
    mysqli_query($conn, "UPDATE `appointments` SET `status`='$st' $pay_clause WHERE `id`='$id'");
    header("Location: admin.php?tab=appointments");
    exit();
}

// 5. DELETE APPOINTMENT (DELETE)
if (isset($_GET['delete_appointment'])) {
    $id = intval($_GET['delete_appointment']);
    mysqli_query($conn, "DELETE FROM `appointments` WHERE `id`='$id'");
    header("Location: admin.php?tab=appointments");
    exit();
}



/// ==================== FEEDBACKS & REVIEWS PAGE ====================

// 1. FETCH REVIEWS & CONTACT MESSAGES
$reviews_query = "SELECT * FROM `reviews` ORDER BY `id` DESC";
$reviews_result = mysqli_query($conn, $reviews_query);

$messages_query = "SELECT * FROM `contact_messages` ORDER BY `id` DESC";
$messages_result = mysqli_query($conn, $messages_query);

// 2. APPROVE REVIEW
if (isset($_GET['approve_review'])) {
    $id = intval($_GET['approve_review']);
    mysqli_query($conn, "UPDATE `reviews` SET `status`='Approved' WHERE `id`='$id'");
    header("Location: admin.php?tab=feedbacks");
    exit();
}

// 3. DELETE REVIEW
if (isset($_GET['delete_review'])) {
    $id = intval($_GET['delete_review']);
    mysqli_query($conn, "DELETE FROM `reviews` WHERE `id`='$id'");
    header("Location: admin.php?tab=feedbacks");
    exit();
}

// 4. DELETE CONTACT MESSAGE
if (isset($_GET['delete_contact_msg'])) {
    $id = intval($_GET['delete_contact_msg']);
    mysqli_query($conn, "DELETE FROM `contact_messages` WHERE `id`='$id'");
    header("Location: admin.php?tab=feedbacks");
    exit();
}



/// ==================== EXECUTIVE METRICS & REPORTS ====================

// Lifetime & Today's Revenue
$rev_query = mysqli_query($conn, "SELECT SUM(price) as total_rev FROM `appointments` WHERE `status`='Completed' OR `payment_status`='Paid'");
$rev_data = mysqli_fetch_assoc($rev_query);
$total_lifetime_revenue = floatval($rev_data['total_rev'] ?? 0);

$today_rev_query = mysqli_query($conn, "SELECT SUM(price) as today_rev FROM `appointments` WHERE `appointment_date`=CURDATE() AND (`status`='Completed' OR `payment_status`='Paid')");
$today_rev_data = mysqli_fetch_assoc($today_rev_query);
$today_revenue = floatval($today_rev_data['today_rev'] ?? 0);

// Total counts
$total_users_count = mysqli_num_rows(mysqli_query($conn, "SELECT `id` FROM `user`"));
$total_services_count = mysqli_num_rows(mysqli_query($conn, "SELECT `id` FROM `services`"));
$total_stylists_count = mysqli_num_rows(mysqli_query($conn, "SELECT `id` FROM `staff`"));

$appts_summary_q = mysqli_query($conn, "SELECT
    COUNT(*) as total_count,
    SUM(CASE WHEN `status`='Completed' THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN `status`='In Chair' THEN 1 ELSE 0 END) as in_chair_count,
    SUM(CASE WHEN `status`='Confirmed' OR `status`='Booked' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN `status`='Cancelled' THEN 1 ELSE 0 END) as cancelled_count
    FROM `appointments`");
$appts_summary = mysqli_fetch_assoc($appts_summary_q);
$total_appointments_count = intval($appts_summary['total_count'] ?? 0);
$completed_appointments_count = intval($appts_summary['completed_count'] ?? 0);
$avg_ticket_value = ($completed_appointments_count > 0) ? round($total_lifetime_revenue / $completed_appointments_count) : 0;

// Average Feedback Rating
$rating_avg_q = mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM `reviews`");
$rating_avg_data = mysqli_fetch_assoc($rating_avg_q);
$salon_avg_rating = round(floatval($rating_avg_data['avg_rating'] ?? 5.0), 1);
$salon_total_reviews = intval($rating_avg_data['total_reviews'] ?? 0);

// Cache Services & Staff for dropdown selects across modals
$services_list_cache = [];
$svc_dropdown_q = mysqli_query($conn, "SELECT `id`, `name`, `price`, `duration` FROM `services` ORDER BY `name` ASC");
if ($svc_dropdown_q) {
    while ($s_row = mysqli_fetch_assoc($svc_dropdown_q)) {
        $services_list_cache[] = $s_row;
    }
}

$staff_list_cache = [];
$staff_dropdown_q = mysqli_query($conn, "SELECT `id`, `name`, `specialization` FROM `staff` ORDER BY `name` ASC");
if ($staff_dropdown_q) {
    while ($st_row = mysqli_fetch_assoc($staff_dropdown_q)) {
        $staff_list_cache[] = $st_row;
    }
}

// Real Chart Data Aggregations from MySQL
$rev_chart_labels = [];
$rev_chart_data = [];
$rev_c_q = mysqli_query($conn, "SELECT DATE_FORMAT(appointment_date, '%d %b') as appt_day, SUM(price) as day_rev FROM `appointments` WHERE `status`='Completed' OR `payment_status`='Paid' GROUP BY `appointment_date` ORDER BY `appointment_date` ASC LIMIT 7");
if ($rev_c_q && mysqli_num_rows($rev_c_q) > 0) {
    while ($rc = mysqli_fetch_assoc($rev_c_q)) {
        $rev_chart_labels[] = $rc['appt_day'];
        $rev_chart_data[] = floatval($rc['day_rev']);
    }
}
if (empty($rev_chart_labels)) {
    $rev_chart_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $rev_chart_data = [15000, 22000, 18500, 31000, 42000, 58000, 49000];
}

$pie_services_labels = [];
$pie_services_counts = [];
$pie_q = mysqli_query($conn, "SELECT `service_name`, COUNT(*) as cnt FROM `appointments` GROUP BY `service_name` ORDER BY cnt DESC LIMIT 5");
if ($pie_q && mysqli_num_rows($pie_q) > 0) {
    while ($p_row = mysqli_fetch_assoc($pie_q)) {
        $pie_services_labels[] = $p_row['service_name'];
        $pie_services_counts[] = intval($p_row['cnt']);
    }
}
if (empty($pie_services_labels)) {
    $pie_services_labels = ['Total Grooming', 'Signature Cut & Beard', 'Beard Design', 'Traditional Shave', 'Deep Clean Facial'];
    $pie_services_counts = [12, 18, 9, 6, 8];
}

$peak_hours_labels = [];
$peak_hours_counts = [];
$peak_q = mysqli_query($conn, "SELECT `appointment_time`, COUNT(*) as cnt FROM `appointments` GROUP BY `appointment_time` ORDER BY cnt DESC LIMIT 6");
if ($peak_q && mysqli_num_rows($peak_q) > 0) {
    while ($ph = mysqli_fetch_assoc($peak_q)) {
        $peak_hours_labels[] = $ph['appointment_time'];
        $peak_hours_counts[] = intval($ph['cnt']);
    }
}
if (empty($peak_hours_labels)) {
    $peak_hours_labels = ['10:00 AM', '11:30 AM', '02:00 PM', '04:30 PM', '07:00 PM', '08:30 PM'];
    $peak_hours_counts = [8, 14, 11, 16, 12, 7];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Jost:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
</head>

<body class="dash-body">

    <!-- Luxury Executive Welcome Splash Screen (Shows ONLY on initial load, NOT on form submissions/refreshes) -->
    <div id="adminWelcomeSplash" style="display:none;position:fixed;inset:0;background:radial-gradient(circle at center, #181c28 0%, #090a0f 100%);z-index:99999;flex-direction:column;align-items:center;justify-content:center;transition:opacity 0.5s ease, visibility 0.5s;">
        <div style="text-align:center;max-width:480px;padding:30px;">
            <div style="width:76px;height:76px;margin:0 auto 20px;border-radius:22px;background:linear-gradient(135deg, #d4af37, #85581A);display:flex;align-items:center;justify-content:center;font-size:34px;box-shadow:0 0 35px rgba(212,175,55,0.45);border:1px solid rgba(255,255,255,0.2);">
                👑
            </div>
            <span style="letter-spacing:4px;font-size:11px;color:#d4af37;text-transform:uppercase;font-weight:700;display:block;margin-bottom:8px;">EXECUTIVE MANAGEMENT CONSOLE</span>
            <h2 style="color:#ffffff;font-size:26px;font-weight:300;margin-bottom:8px;">Welcome, <span style="color:#d4af37;font-weight:600;"><?php echo htmlspecialchars($currentUserName); ?></span></h2>
            <p style="color:#8b93a7;font-size:13px;margin-bottom:24px;">Initializing secure salon management systems & real-time analytics...</p>

            <div style="width:220px;height:4px;background:rgba(255,255,255,0.1);border-radius:10px;margin:0 auto;overflow:hidden;position:relative;">
                <div style="width:100%;height:100%;background:linear-gradient(90deg, #d4af37, #fef08a, #d4af37);position:absolute;left:-100%;animation:splashLoader 1.2s infinite ease-in-out;"></div>
            </div>
        </div>
    </div>
    <style>
        @keyframes splashLoader {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
    <script>
        (function() {
            var splashKey = 'elegance_admin_splash_seen';
            var splash = document.getElementById('adminWelcomeSplash');
            if (splash && !sessionStorage.getItem(splashKey)) {
                splash.style.display = 'flex';
                sessionStorage.setItem(splashKey, '1');
                setTimeout(function() {
                    splash.style.opacity = '0';
                    setTimeout(function() { splash.style.display = 'none'; }, 500);
                }, 900);
            }
        })();
    </script>

    <div class="dash-layout">

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="dash-sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

        <!-- ===== SIDEBAR ===== -->
        <aside class="dash-sidebar" id="sidebar">
            <a href="../index.php" class="dash-brand">
                <div class="dash-brand-icon">E</div>
                <div class="dash-brand-text">
                    <h2>ELEGANCE</h2>
                    <span>SALON KARACHI</span>
                </div>
            </a>

            <nav class="dash-nav">
                    <div class="dash-nav-label">Main</div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link active" data-tab="overview" onclick="switchTab('overview',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    <div class="dash-nav-label">Management</div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="users" onclick="switchTab('users',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Users</span>
                        </a>
                    </div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="services" onclick="switchTab('services',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                            </svg>
                            <span>Services</span>
                        </a>
                    </div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="stylists" onclick="switchTab('stylists',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Stylists & Staff</span>
                        </a>
                    </div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="stock" onclick="switchTab('stock',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span>Stock & Inventory</span>
                        </a>
                    </div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="appointments" onclick="switchTab('appointments',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Appointments</span>
                        </a>
                    </div>

                    <div class="dash-nav-label">Insights</div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="reports" onclick="switchTab('reports',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            <span>Reports</span>
                        </a>
                    </div>
                    <div class="dash-nav-item">
                        <a class="dash-nav-link" data-tab="feedbacks" onclick="switchTab('feedbacks',this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span>Feedbacks</span>
                        </a>
                    </div>
                </nav>

            <div class="dash-sidebar-footer">
                <a href="reception.php" class="dash-logout"
                    style="background:var(--gold-soft);color:var(--gold);border-color:var(--border-gold);margin-bottom:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <rect x="2" y="3" width="20" height="14" rx="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    Reception Desk
                </a>
                <a href="../userside/logout.php" class="dash-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Log Out
                </a>
            </div>
        </aside>

        <!-- ===== MAIN ===== -->
        <main class="dash-main">

            <!-- Topbar -->
            <header class="dash-topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="dash-topbar-btn d-lg-none"
                        onclick="toggleSidebar()" style="padding:8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="dash-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="globalSearch" placeholder="Search anything...">
                    </div>
                </div>
                <div class="dash-topbar-right">
                    <a href="reception.php" class="dash-topbar-btn" style="color:var(--gold);border-color:rgba(212,175,55,0.35);">
                        <span style="margin-right:4px;">🛎️</span> Reception Desk
                    </a>
                    <a href="../index.php" class="dash-topbar-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                            </path>
                        </svg>
                        Public Site
                    </a>
                    <div class="dash-bell" onclick="toast('3 alerts: low stock, 4 walk-ins pending','info')">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="dash-bell-dot"></span>
                    </div>
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=100&auto=format&fit=crop"
                        class="dash-avatar" alt="Admin">
                </div>
            </header>

            <!-- Content -->
            <div class="dash-content">

                <!-- ============ OVERVIEW ============ -->
                <section class="dash-pane active" id="pane-overview">
                    <div class="dash-header">
                        <div>
                            <h3 id="greetingHeader">Good Evening, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Executive'); ?></h3>
                            <p>Here's what's happening at Elegance Salon today.</p>
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <a href="admin.php?export_sales_csv=1" class="dash-btn outline" style="color:var(--gold);border-color:rgba(212,175,55,0.4);text-decoration:none;" title="Download complete sales reports into Excel CSV">
                                📥 Export Sales CSV
                            </a>
                            <button class="dash-btn gold" onclick="openModal('bookModal')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                New Booking
                            </button>
                            <a href="reception.php" class="dash-btn outline" style="color:var(--green);border-color:rgba(5,205,153,0.3);">
                                Front Desk Console →
                            </a>
                        </div>
                    </div>

                    <!-- Quick Action Hub -->
                    <div class="dash-card mb-4 dash-animate-in" style="background:linear-gradient(135deg, rgba(212,175,55,0.08), rgba(22,25,34,0.95));border:1px solid var(--border-gold);padding:18px 24px;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 style="color:#fff;margin-bottom:2px;font-size:15px;font-weight:600;">⚡ Salon Operations Quick Hub</h5>
                                <p style="color:var(--text-muted);font-size:12px;margin:0;">Instant shortcuts to create appointments, services, products, and launch terminals</p>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <button class="dash-btn gold sm" onclick="openModal('bookModal')">+ Book Appointment</button>
                                <button class="dash-btn outline sm" onclick="openModal('addServiceModal')">✨ Add Service</button>
                                <button class="dash-btn outline sm" onclick="openModal('addStockModal')">📦 Add Product</button>
                                <button class="dash-btn outline sm" onclick="openModal('addStylistModal')">✂️ Add Stylist</button>
                                <a href="reception.php" class="dash-btn sm" style="background:rgba(5,205,153,0.15);color:#05CD99;border:1px solid rgba(5,205,153,0.3);">💼 Front Desk</a>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-sm-6 dash-animate-in" style="animation-delay:0.05s;">
                            <div class="dash-stat-card gold-accent">
                                <div>
                                    <div class="dash-stat-label">Today's Revenue</div>
                                    <div class="dash-stat-val" style="color:var(--gold);">Rs. <?php echo number_format($today_revenue); ?></div>
                                    <div class="dash-stat-change up">Live from Appointments</div>
                                </div>
                                <div class="dash-stat-icon gold">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 dash-animate-in" style="animation-delay:0.1s;">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Total Appointments</div>
                                    <div class="dash-stat-val"><?php echo $total_appointments_count; ?></div>
                                    <div class="dash-stat-change up"><?php echo $completed_appointments_count; ?> Completed</div>
                                </div>
                                <div class="dash-stat-icon blue">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 dash-animate-in" style="animation-delay:0.15s;">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Registered Users</div>
                                    <div class="dash-stat-val"><?php echo $total_users_count; ?></div>
                                    <div class="dash-stat-change up">Active Accounts</div>
                                </div>
                                <div class="dash-stat-icon green">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 dash-animate-in" style="animation-delay:0.2s;">
                            <div class="dash-stat-card" style="<?php echo ($critical_stock_count > 0) ? 'border-color:rgba(239,68,68,0.4);' : ''; ?>">
                                <div>
                                    <div class="dash-stat-label">Low Stock Alerts</div>
                                    <div class="dash-stat-val" style="color:<?php echo ($critical_stock_count > 0) ? 'var(--red)' : 'var(--green)'; ?>;"><?php echo $critical_stock_count; ?></div>
                                    <div class="dash-stat-change <?php echo ($critical_stock_count > 0) ? 'down' : 'up'; ?>">
                                        <?php echo ($critical_stock_count > 0) ? 'Needs purchase order' : 'All stocks optimal'; ?>
                                    </div>
                                </div>
                                <div class="dash-stat-icon <?php echo ($critical_stock_count > 0) ? 'red' : 'green'; ?>">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart + Chair Occupancy -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-8 dash-animate-in">
                            <div class="dash-card h-100">
                                <div class="dash-card-header">
                                    <h5>Revenue Overview (PKR)</h5>
                                    <span class="dash-badge gold">Total: Rs. <?php echo number_format($total_lifetime_revenue); ?></span>
                                </div>
                                <div style="height:260px;"><canvas id="revenueChart"></canvas></div>
                            </div>
                        </div>
                        <div class="col-lg-4 dash-animate-in">
                            <div class="dash-card h-100">
                                <div class="dash-card-header">
                                    <h5>Salon Floor Status</h5>
                                    <span class="dash-badge green"><span class="dash-live-dot"></span> Live</span>
                                </div>
                                <div id="chairGrid" class="row g-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent + Schedule -->
                    <div class="row g-3">
                        <div class="col-lg-6 dash-animate-in">
                            <div class="dash-card h-100">
                                <div class="dash-card-header">
                                    <h5>Recent Bookings</h5>
                                    <a class="dash-btn outline sm" onclick="switchTab('appointments',null)">View All</a>
                                </div>
                                <div id="recentBookings">
                                    <?php
                                    $recent_appts_q = mysqli_query($conn, "SELECT * FROM `appointments` ORDER BY `id` DESC LIMIT 5");
                                    if ($recent_appts_q && mysqli_num_rows($recent_appts_q) > 0): ?>
                                        <?php while ($ra = mysqli_fetch_assoc($recent_appts_q)):
                                            $badgeC = ($ra['status'] === 'Completed') ? 'gold' : (($ra['status'] === 'In Chair') ? 'green' : (($ra['status'] === 'Cancelled') ? 'red' : 'blue'));
                                        ?>
                                            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
                                                <div style="display:flex;align-items:center;gap:12px;">
                                                    <div style="width:38px;height:38px;border-radius:10px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--gold);">
                                                        <?php echo strtoupper(substr($ra['client_name'], 0, 2)); ?>
                                                    </div>
                                                    <div>
                                                        <div style="font-weight:600;font-size:13px;color:var(--text-primary);"><?php echo htmlspecialchars($ra['client_name']); ?></div>
                                                        <div style="font-size:12px;color:var(--text-secondary);"><?php echo htmlspecialchars($ra['service_name']); ?> &middot; <?php echo htmlspecialchars($ra['appointment_time']); ?></div>
                                                    </div>
                                                </div>
                                                <div style="text-align:right;">
                                                    <div style="font-weight:700;font-size:13px;color:var(--gold);">Rs. <?php echo number_format($ra['price']); ?></div>
                                                    <span class="dash-badge <?php echo $badgeC; ?>" style="font-size:10px;"><?php echo htmlspecialchars($ra['status']); ?></span>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p style="text-align:center;padding:20px;color:var(--text-muted);">No bookings found in database</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 dash-animate-in">
                            <div class="dash-card h-100">
                                <div class="dash-card-header">
                                    <h5>Top Services</h5>
                                    <a class="dash-btn outline sm" onclick="switchTab('services',null)">Manage Services</a>
                                </div>
                                <div id="topServices">
                                    <?php
                                    $top_svc_q = mysqli_query($conn, "SELECT * FROM `services` ORDER BY `price` DESC LIMIT 5");
                                    if ($top_svc_q && mysqli_num_rows($top_svc_q) > 0): ?>
                                        <?php while ($ts = mysqli_fetch_assoc($top_svc_q)): ?>
                                            <div style="margin-bottom:14px;">
                                                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                                                    <span style="font-size:13px;font-weight:600;color:var(--text-primary);"><?php echo htmlspecialchars($ts['name']); ?></span>
                                                    <span style="font-size:12px;font-weight:700;color:var(--gold);">Rs. <?php echo number_format($ts['price']); ?></span>
                                                </div>
                                                <div style="height:6px;border-radius:4px;background:var(--bg-tertiary);overflow:hidden;">
                                                    <div style="height:100%;width:80%;border-radius:4px;background:linear-gradient(90deg,var(--gold),var(--gold-hover));"></div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ============ USERS ============ -->
                <section class="dash-pane" id="pane-users">
                    <div class="dash-header">
                        <div>
                            <h3>User Management</h3>
                            <p>All registered users, admins, and reception staff.</p>
                        </div>
                        <button class="dash-btn gold" onclick="openModal('addUserModal')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add User
                        </button>
                    </div>
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable">
                                <?php
                                $count = 1;
                                if (isset($result) && $result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><span
                                                    style="color:var(--text-muted);font-size:12px;font-weight:600;">#<?php echo $count++; ?></span>
                                            </td>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <div
                                                        style="width:34px;height:34px;border-radius:10px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);flex-shrink:0;">
                                                        <?php echo strtoupper(substr($row['name'], 0, 2)); ?>
                                                    </div>
                                                    <span
                                                        style="font-weight:600;color:var(--text-primary);"><?php echo htmlspecialchars($row['name']); ?></span>
                                                </div>
                                            </td>
                                            <td style="color:var(--text-secondary);font-size:13px;">
                                                <?php echo htmlspecialchars($row['email']); ?>
                                            </td>
                                            <td style="color:var(--text-secondary);font-size:13px;">
                                                <?php echo htmlspecialchars($row['phone']); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $r = strtolower($row['role']);
                                                $badgeClass = ($r === 'admin') ? 'gold' : (($r === 'receptionist') ? 'blue' : 'green');
                                                ?>
                                                <span class="dash-badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($row['role']); ?>
                                                </span>
                                            </td>
                                            <td style="color:var(--text-muted);font-size:12px;">
                                                <?php echo !empty($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : '—'; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="dash-btn outline sm me-1"
                                                    style="color:var(--gold); border-color:var(--gold);"
                                                    onclick="openEditUserModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['email']); ?>', '<?php echo addslashes($row['phone']); ?>', '<?php echo $row['role']; ?>')">
                                                    Edit
                                                </button>
                                                <a href="admin.php?delete_user=<?php echo $row['id']; ?>"
                                                    onclick="return confirm('Kya aap waqai is user ko delete karna chahte hain?')"
                                                    class="dash-btn outline sm"
                                                    style="color:var(--red); border-color:rgba(239,68,68,0.3); text-decoration:none;">
                                                    Remove
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">No
                                            users found in database</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ============ SERVICES ============ -->
                <section class="dash-pane" id="pane-services">
                    <div class="dash-header">
                        <div>
                            <h3>Salon Services</h3>
                            <p>Manage all grooming packages and pricing.</p>
                        </div>
                        <button class="dash-btn gold" onclick="openModal('addServiceModal')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Service
                        </button>
                    </div>
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service Name</th>
                                    <th>Category</th>
                                    <th>Price (Rs.)</th>
                                    <th>Duration</th>
                                    <th>Bookings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTable">
    <?php
    $s_count = 1;
    if (isset($services_result) && $services_result && mysqli_num_rows($services_result) > 0): ?>
        <?php while ($s = mysqli_fetch_assoc($services_result)): ?>
            <tr>
                <td><span style="color:var(--text-muted);font-size:12px;font-weight:600;">#<?php echo $s_count++; ?></span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="<?php echo !empty($s['hero_image']) ? htmlspecialchars($s['hero_image']) : 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=200&auto=format&fit=crop'; ?>"
                             style="width:44px;height:44px;border-radius:10px;object-fit:cover;border:1px solid var(--border-gold);"
                             alt="<?php echo htmlspecialchars($s['name']); ?>">
                        <div>
                            <strong style="color:var(--text-primary);display:block;"><?php echo htmlspecialchars($s['name']); ?></strong>
                            <small style="color:var(--gold);font-size:11px;"><?php echo htmlspecialchars($s['tag'] ?? 'SIGNATURE'); ?></small>
                        </div>
                    </div>
                </td>
                <td><span class="dash-badge blue"><?php echo htmlspecialchars($s['category']); ?></span></td>
                <td><span style="font-weight:700;color:var(--gold);">Rs. <?php echo number_format($s['price']); ?></span></td>
                <td style="color:var(--text-secondary);font-size:13px;"><?php echo htmlspecialchars($s['duration']); ?></td>
                <td><span class="dash-badge green"><?php echo $s['bookings'] ?? 0; ?> Bookings</span></td>
                <td>
                    <button type="button"
                            class="dash-btn outline sm me-1"
                            style="color:var(--cyan, #06b6d4); border-color:rgba(6,182,212,0.4);"
                            data-service="<?php echo htmlspecialchars(json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                            onclick="openViewServiceFromBtn(this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:2px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        View
                    </button>
                    <button type="button"
                            class="dash-btn outline sm me-1"
                            style="color:var(--gold); border-color:var(--gold);"
                            data-service="<?php echo htmlspecialchars(json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                            onclick="openEditServiceFromBtn(this)">
                        Edit
                    </button>
                    <a href="admin.php?delete_service=<?php echo $s['id']; ?>"
                       onclick="return confirm('Kya aap waqai is service ko delete karna chahte hain?')"
                       class="dash-btn outline sm"
                       style="color:var(--red); border-color:rgba(239,68,68,0.3); text-decoration:none;">
                        Remove
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">No services found in database</td>
        </tr>
    <?php endif; ?>
</tbody>
                        </table>
                    </div>
                </section>

                <!-- ============ STYLISTS & STAFF ============ -->
                <section class="dash-pane" id="pane-stylists">
                    <div class="dash-header">
                        <div>
                            <h3>Stylists & Staff Management</h3>
                            <p>Manage salon master barbers, specializations, shift schedules, and commission rates.</p>
                        </div>
                        <button class="dash-btn gold" onclick="openModal('addStylistModal')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Stylist
                        </button>
                    </div>

                    <div class="dash-card mb-3" style="padding:16px 20px;">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <div class="dash-search" style="width:100%;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="stylistSearch" placeholder="Search stylist name, phone, specialization..." oninput="filterStylistTable()">
                                </div>
                            </div>
                            <div class="col-md-7 text-md-end">
                                <span style="font-size:12px;color:var(--text-muted);background:rgba(255,255,255,0.04);padding:6px 14px;border-radius:20px;border:1px solid rgba(255,255,255,0.08);">
                                    Active Barbers Roster: <strong style="color:var(--gold);"><?php echo (isset($staff_result) && $staff_result) ? mysqli_num_rows($staff_result) : 0; ?></strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Stylists Table Navigation & Scroller Bar -->
                    <div class="dash-table-bar">
                        <div class="table-bar-left">
                            <span class="scroll-pill-hint">⇄ Horizontal Scroll & Drag</span>
                            <span style="color:var(--text-muted);font-size:11px;">(Use buttons or drag horizontally)</span>
                        </div>
                        <div class="table-bar-right">
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('stylistTableWrap', -320)">◀ Scroll Left</button>
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('stylistTableWrap', 320)">Scroll Right ▶</button>
                        </div>
                    </div>
                    <div class="dash-top-scroll">
                        <div class="dash-top-scroll-inner"></div>
                    </div>
                    <div class="dash-table-wrap" id="stylistTableWrap">
                        <table class="dash-table" style="min-width:1120px;">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th style="min-width:240px;">Master Barber Profile</th>
                                    <th style="min-width:170px;">Specialization</th>
                                    <th style="min-width:180px;">Phone / Email</th>
                                    <th style="min-width:140px;">Commission Rate</th>
                                    <th style="min-width:160px;">Work Schedule</th>
                                    <th style="min-width:110px;">Status</th>
                                    <th class="col-actions" style="min-width:200px;text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="stylistsTable">
                                <?php
                                $st_count = 1;
                                if (isset($staff_result) && $staff_result && mysqli_num_rows($staff_result) > 0): ?>
                                    <?php while ($st = mysqli_fetch_assoc($staff_result)):
                                        $stImg = !empty($st['image']) ? $st['image'] : '';
                                        $initials = strtoupper(substr($st['name'], 0, 2));
                                    ?>
                                        <tr class="stylist-row" data-search="<?php echo strtolower(htmlspecialchars($st['name'] . ' ' . $st['specialization'] . ' ' . $st['phone'] . ' ' . ($st['email'] ?? '') . ' ' . $st['work_schedule'])); ?>">
                                            <td><span style="color:var(--text-muted);font-size:12px;font-weight:600;">#<?php echo $st_count++; ?></span></td>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:12px;">
                                                    <div style="width:42px;height:42px;border-radius:12px;overflow:hidden;border:1px solid rgba(212,175,55,0.3);background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--gold);flex-shrink:0;box-shadow:0 3px 10px rgba(0,0,0,0.35);">
                                                        <?php if (!empty($stImg)): ?>
                                                            <img src="<?php echo htmlspecialchars($stImg); ?>" alt="<?php echo htmlspecialchars($st['name']); ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.parentElement.innerText='<?php echo $initials; ?>';">
                                                        <?php else: ?>
                                                            <?php echo $initials; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <strong style="color:var(--text-primary);font-size:13.5px;display:block;"><?php echo htmlspecialchars($st['name']); ?></strong>
                                                        <span style="font-size:11px;color:var(--gold);opacity:0.9;">Barber Station</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="dash-badge blue" style="font-size:11px;"><?php echo htmlspecialchars($st['specialization'] ?: 'Stylist'); ?></span>
                                            </td>
                                            <td>
                                                <div style="color:var(--text-primary);font-size:12.5px;font-weight:500;">
                                                    📞 <?php echo htmlspecialchars($st['phone']); ?>
                                                </div>
                                                <?php if (!empty($st['email'])): ?>
                                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                                        ✉️ <?php echo htmlspecialchars($st['email']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="dash-badge gold" style="font-weight:700;font-size:11.5px;">
                                                    ✂️ <?php echo $st['commission_rate']; ?>% Cut
                                                </span>
                                            </td>
                                            <td>
                                                <div style="color:var(--text-secondary);font-size:12.5px;">
                                                    🕒 <?php echo htmlspecialchars($st['work_schedule'] ?: 'Tue-Sun 11AM-9PM'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="dash-badge <?php echo ($st['status'] === 'Active') ? 'green' : (($st['status'] === 'On Leave') ? 'orange' : 'red'); ?>">
                                                    <?php echo htmlspecialchars($st['status']); ?>
                                                </span>
                                            </td>
                                            <!-- Actions (Sticky Right Column) -->
                                            <td class="col-actions" style="text-align:right;">
                                                <div class="dash-action-group" style="justify-content:flex-end;">
                                                    <button type="button" class="dash-btn outline sm"
                                                        style="color:var(--cyan, #06b6d4); border-color:rgba(6,182,212,0.4);"
                                                        data-stylist="<?php echo htmlspecialchars(json_encode($st, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                                                        onclick="openViewStylistFromBtn(this)">
                                                        View
                                                    </button>
                                                    <button type="button" class="dash-btn outline sm"
                                                        style="color:var(--gold); border-color:rgba(212,175,55,0.4);"
                                                        data-stylist="<?php echo htmlspecialchars(json_encode($st, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                                                        onclick="openEditStylistFromBtn(this)">
                                                        Edit
                                                    </button>
                                                    <a href="admin.php?delete_stylist=<?php echo $st['id']; ?>"
                                                        onclick="return confirm('Kya aap waqai is stylist ko delete karna chahte hain?')"
                                                        class="dash-btn outline sm" style="color:var(--red); border-color:rgba(239,68,68,0.3); text-decoration:none;">
                                                        Remove
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No stylists found in database. Click "Add Stylist" to register a barber.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ============ STOCK & INVENTORY ============ -->
                <section class="dash-pane" id="pane-stock">
                    <div class="dash-header">
                        <div>
                            <h3>Stock & Inventory Management</h3>
                            <p>Real-time retail & backbar inventory tracking, valuation, supplier reorders, and stock health.</p>
                        </div>
                        <div style="display:flex;gap:10px;">
                            <button class="dash-btn gold" onclick="openModal('addStockModal')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Add New Product
                            </button>
                        </div>
                    </div>

                    <!-- Top Inventory KPI Stat Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="dash-stat-card gold-accent">
                                <div>
                                    <div class="dash-stat-label">Total Asset Value (Cost)</div>
                                    <div class="dash-stat-val" style="color:var(--gold);">Rs. <?php echo number_format($total_cost_valuation); ?></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">Capital invested in inventory</div>
                                </div>
                                <div class="dash-stat-icon gold">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Potential Retail Value</div>
                                    <div class="dash-stat-val" style="color:var(--green);">Rs. <?php echo number_format($total_retail_valuation); ?></div>
                                    <div style="font-size:11px;color:var(--green);margin-top:6px;font-weight:600;">
                                        +Rs. <?php echo number_format(max(0, $total_retail_valuation - $total_cost_valuation)); ?> Profit Margin
                                    </div>
                                </div>
                                <div class="dash-stat-icon green">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Active SKUs / Products</div>
                                    <div class="dash-stat-val"><?php echo $total_stock_items; ?> <span style="font-size:14px;color:var(--text-secondary);font-weight:500;">Items</span></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">Across 6 Salon Categories</div>
                                </div>
                                <div class="dash-stat-icon blue">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="dash-stat-card" style="<?php echo ($critical_stock_count > 0) ? 'border-color:rgba(239,68,68,0.4);' : ''; ?>">
                                <div>
                                    <div class="dash-stat-label">Stock Reorder Alerts</div>
                                    <div class="dash-stat-val" style="color:<?php echo ($critical_stock_count > 0) ? 'var(--red)' : 'var(--green)'; ?>;">
                                        <?php echo $critical_stock_count; ?> <span style="font-size:14px;font-weight:500;">Critical</span>
                                    </div>
                                    <div style="font-size:11px;color:<?php echo ($out_of_stock_count > 0) ? 'var(--red)' : 'var(--text-muted)'; ?>;margin-top:6px;">
                                        <?php echo $out_of_stock_count; ?> completely out of stock
                                    </div>
                                </div>
                                <div class="dash-stat-icon <?php echo ($critical_stock_count > 0) ? 'red' : 'green'; ?>">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="dash-card mb-3" style="padding:16px 20px;">
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-4">
                                <div class="dash-search" style="width:100%;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="stockFilterSearch" placeholder="Search product name, brand, SKU code, supplier..." oninput="filterStockTable()">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:lg-end;">
                                    <button class="dash-btn outline sm stock-cat-btn active" data-cat="all" onclick="filterStockCategory('all', this)">All (<?php echo $total_stock_items; ?>)</button>
                                    <?php if ($critical_stock_count > 0): ?>
                                        <button class="dash-btn outline sm stock-cat-btn" data-cat="critical" onclick="filterStockCategory('critical', this)" style="color:var(--red);border-color:rgba(239,68,68,0.3);">⚠️ Low / Out (<?php echo $critical_stock_count; ?>)</button>
                                    <?php endif; ?>
                                    <button class="dash-btn outline sm stock-cat-btn" data-cat="Hair Care" onclick="filterStockCategory('Hair Care', this)">Hair Care</button>
                                    <button class="dash-btn outline sm stock-cat-btn" data-cat="Beard Care" onclick="filterStockCategory('Beard Care', this)">Beard Care</button>
                                    <button class="dash-btn outline sm stock-cat-btn" data-cat="Skin Care" onclick="filterStockCategory('Skin Care', this)">Skin Care</button>
                                    <button class="dash-btn outline sm stock-cat-btn" data-cat="Tools & Blades" onclick="filterStockCategory('Tools & Blades', this)">Tools & Blades</button>
                                    <button class="dash-btn outline sm stock-cat-btn" data-cat="Fragrance" onclick="filterStockCategory('Fragrance', this)">Fragrance</button>
                                    <button class="dash-btn outline sm stock-cat-btn" data-cat="Treatments" onclick="filterStockCategory('Treatments', this)">Treatments</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commercial Stock Table Navigation & Scroller Bar -->
                    <div class="dash-table-bar">
                        <div class="table-bar-left">
                            <span class="scroll-pill-hint">⇄ Horizontal Scroll & Drag</span>
                            <span style="color:var(--text-muted);font-size:11px;">(<?php echo count($inv_items_cache); ?> products &bull; Drag to pan horizontally)</span>
                        </div>
                        <div class="table-bar-right">
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('stockTableWrap', -320)">◀ Scroll Left</button>
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('stockTableWrap', 320)">Scroll Right ▶</button>
                        </div>
                    </div>
                    <div class="dash-top-scroll">
                        <div class="dash-top-scroll-inner"></div>
                    </div>
                    <div class="dash-table-wrap" id="stockTableWrap">
                        <table class="dash-table" id="mainStockTable" style="min-width:1220px;">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th style="min-width:240px;">Product Profile & SKU</th>
                                    <th style="min-width:130px;">Category & Unit</th>
                                    <th style="min-width:180px;">Inventory Health</th>
                                    <th style="min-width:140px;">Pricing & Margin</th>
                                    <th style="min-width:130px;">Asset Valuation</th>
                                    <th style="min-width:170px;">Supplier / Restocked</th>
                                    <th class="col-actions" style="min-width:240px;text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="stockTableBody">
                                <?php
                                $inv_count = 1;
                                if (!empty($inv_items_cache)): ?>
                                    <?php foreach ($inv_items_cache as $inv):
                                        $qty = intval($inv['quantity']);
                                        $min = intval($inv['min_threshold']);
                                        $isLow = ($qty <= $min && $qty > 0);
                                        $isOut = ($qty <= 0);
                                        $cost = floatval($inv['cost_price']);
                                        $retail = floatval($inv['retail_price'] ?? ($cost * 1.6));
                                        $profitPerUnit = max(0, $retail - $cost);
                                        $markupPct = ($cost > 0) ? round(($profitPerUnit / $cost) * 100) : 0;
                                        $totalVal = $qty * $cost;

                                        // Progress bar calculation
                                        $maxBar = max(30, $min * 3);
                                        $barPct = min(100, round(($qty / $maxBar) * 100));
                                        $barColor = $isOut ? 'var(--red)' : ($isLow ? 'var(--orange)' : 'var(--green)');

                                        $imgUrl = !empty($inv['image']) ? $inv['image'] : 'https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop';
                                        $brandName = !empty($inv['brand']) ? $inv['brand'] : 'Elegance Reserve';
                                        $unit = !empty($inv['unit_type']) ? $inv['unit_type'] : 'Pieces';
                                    ?>
                                        <tr class="stock-row"
                                            data-category="<?php echo htmlspecialchars($inv['category']); ?>"
                                            data-status="<?php echo $isOut ? 'out' : ($isLow ? 'low' : 'ok'); ?>"
                                            data-search="<?php echo strtolower(htmlspecialchars($inv['name'] . ' ' . $brandName . ' ' . $inv['code'] . ' ' . $inv['category'] . ' ' . $inv['supplier_name'])); ?>"
                                            style="<?php echo $isOut ? 'background:rgba(239,68,68,0.06);' : ($isLow ? 'background:rgba(245,158,11,0.04);' : ''); ?>">

                                            <!-- Index -->
                                            <td><span style="color:var(--text-muted);font-size:12px;font-weight:600;">#<?php echo $inv_count++; ?></span></td>

                                            <!-- Product Photo + Name + Brand + SKU -->
                                            <td>
                                                <div style="display:flex;align-items:center;gap:12px;">
                                                    <div style="width:48px;height:48px;border-radius:10px;overflow:hidden;border:1px solid rgba(212,175,55,0.25);background:#111;flex-shrink:0;box-shadow:0 3px 10px rgba(0,0,0,0.3);">
                                                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($inv['name']); ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop';">
                                                    </div>
                                                    <div>
                                                        <div style="font-size:11px;color:var(--gold);text-transform:uppercase;font-weight:700;letter-spacing:0.5px;">
                                                             <?php echo htmlspecialchars($brandName); ?>
                                                        </div>
                                                        <strong style="color:var(--text-primary);font-size:13.5px;line-height:1.2;display:block;margin:2px 0;">
                                                            <?php echo htmlspecialchars($inv['name']); ?>
                                                        </strong>
                                                        <span style="font-family:monospace;font-size:11.5px;color:var(--text-muted);background:rgba(255,255,255,0.05);padding:1px 6px;border-radius:4px;">
                                                            <?php echo htmlspecialchars($inv['code']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Category & Unit Type -->
                                            <td>
                                                <span class="dash-badge blue" style="margin-bottom:4px;"><?php echo htmlspecialchars($inv['category']); ?></span>
                                                <div style="font-size:11.5px;color:var(--text-secondary);"><?php echo htmlspecialchars($unit); ?></div>
                                            </td>

                                            <!-- Stock Health & Progress Bar -->
                                            <td style="min-width:170px;">
                                                <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:4px;">
                                                    <strong style="font-size:13.5px;color:<?php echo $isOut ? 'var(--red)' : ($isLow ? 'var(--orange)' : 'var(--text-primary)'); ?>;">
                                                        <?php echo $qty; ?> <?php echo htmlspecialchars($unit); ?>
                                                    </strong>
                                                    <span style="font-size:11px;color:var(--text-muted);">Min: <?php echo $min; ?></span>
                                                </div>
                                                <!-- Visual Depletion Bar -->
                                                <div style="height:6px;background:rgba(255,255,255,0.06);border-radius:3px;overflow:hidden;margin-bottom:6px;">
                                                    <div style="height:100%;width:<?php echo $barPct; ?>%;background:<?php echo $barColor; ?>;border-radius:3px;transition:width 0.6s ease;"></div>
                                                </div>
                                                <?php if ($isOut): ?>
                                                    <span class="dash-badge red" style="font-size:9.5px;padding:2px 8px;">OUT OF STOCK</span>
                                                <?php elseif ($isLow): ?>
                                                    <span class="dash-badge orange" style="font-size:9.5px;padding:2px 8px;">LOW STOCK ALERT</span>
                                                <?php else: ?>
                                                    <span class="dash-badge green" style="font-size:9.5px;padding:2px 8px;">IN STOCK</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Pricing & Margin -->
                                            <td>
                                                <div style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                                    Rs. <?php echo number_format($cost); ?> <small style="font-size:10.5px;color:var(--text-muted);font-weight:500;">(Cost)</small>
                                                </div>
                                                <div style="font-size:12px;color:var(--green);font-weight:600;">
                                                    Rs. <?php echo number_format($retail); ?> <small style="font-size:10.5px;color:var(--text-muted);font-weight:500;">(Retail)</small>
                                                </div>
                                                <span class="dash-badge green" style="font-size:9.5px;padding:1px 6px;margin-top:3px;">
                                                    +<?php echo $markupPct; ?>% Margin
                                                </span>
                                            </td>

                                            <!-- Asset Valuation -->
                                            <td>
                                                <strong style="color:var(--gold);font-size:13.5px;">Rs. <?php echo number_format($totalVal); ?></strong>
                                                <div style="font-size:11px;color:var(--text-muted);"><?php echo $qty; ?> &times; Rs. <?php echo number_format($cost); ?></div>
                                            </td>

                                            <!-- Supplier & Restocked -->
                                            <td>
                                                <div style="font-size:12.5px;color:var(--text-primary);font-weight:600;"><?php echo htmlspecialchars($inv['supplier_name'] ?: 'Local Distributor'); ?></div>
                                                <div style="font-size:11px;color:var(--text-muted);">Restocked: <?php echo date('M d, Y', strtotime($inv['last_restocked'] ?: 'now')); ?></div>
                                            </td>

                                            <!-- Actions (Sticky Right Column) -->
                                            <td class="col-actions" style="text-align:right;">
                                                <div class="dash-action-group" style="justify-content:flex-end;">
                                                    <?php if ($isLow || $isOut):
                                                        $reorderMsg = urlencode("Salam " . ($inv['supplier_name'] ?: 'Supplier') . ", urgent restock needed for Elegance Salon Karachi (Tariq Road). Please dispatch 10 units of " . $inv['name'] . " (" . $inv['code'] . ").");
                                                    ?>
                                                        <a href="https://wa.me/923001234567?text=<?php echo $reorderMsg; ?>" target="_blank" class="dash-btn outline sm" style="color:var(--green);border-color:rgba(5,205,153,0.4);" title="WhatsApp Supplier Reorder">
                                                            💬 Order
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="dash-btn outline sm"
                                                        style="color:var(--green); border-color:rgba(34,197,94,0.4);"
                                                        onclick="openQuickRestockModal(<?php echo $inv['id']; ?>, '<?php echo addslashes($inv['name']); ?>')">
                                                        + Restock
                                                    </button>
                                                    <button type="button" class="dash-btn outline sm"
                                                        style="color:var(--gold); border-color:rgba(212,175,55,0.4);"
                                                        data-stock="<?php echo htmlspecialchars(json_encode($inv, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                                                        onclick="openEditStockFromBtn(this)">
                                                        Edit
                                                    </button>
                                                    <a href="admin.php?delete_stock=<?php echo $inv['id']; ?>"
                                                        onclick="return confirm('Kya aap waqai is item ko inventory se delete karna chahte hain?')"
                                                        class="dash-btn outline sm"
                                                        style="color:var(--red); border-color:rgba(239,68,68,0.3); text-decoration:none;">
                                                        Remove
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                            No products found in salon inventory. Click "Add New Product" to get started.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ============ APPOINTMENTS ============ -->
                <section class="dash-pane" id="pane-appointments">
                    <div class="dash-header">
                        <div>
                            <h3>Appointments Management</h3>
                            <p>Real-time booking schedule, client visits, stylist chairs, and payment tracking.</p>
                        </div>
                        <button class="dash-btn gold" onclick="openModal('bookModal')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Book Appointment
                        </button>
                    </div>
                    <div class="dash-card mb-3" style="padding:16px 20px;">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <div class="dash-search" style="width:100%;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="apptSearch" placeholder="Search phone (0300...), email, client name, ref, token..." oninput="filterApptTable()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select id="apptFilter" class="dash-select" onchange="filterApptTable()" style="width:100%;">
                                    <option value="all">All Statuses</option>
                                    <option value="In Chair">In Chair</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span style="font-size:12px;color:var(--text-muted);background:rgba(255,255,255,0.04);padding:6px 14px;border-radius:20px;border:1px solid rgba(255,255,255,0.08);">
                                    Total Bookings: <strong style="color:var(--gold);"><?php echo $total_appointments_count; ?></strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Table Navigation & Scroller Bar -->
                    <div class="dash-table-bar">
                        <div class="table-bar-left">
                            <span class="scroll-pill-hint">⇄ Horizontal Scroll & Drag</span>
                            <span style="color:var(--text-muted);font-size:11px;">(Use buttons or drag horizontally)</span>
                        </div>
                        <div class="table-bar-right">
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('adminApptTableWrap', -320)">◀ Scroll Left</button>
                            <button type="button" class="dash-table-scroll-btn" onclick="scrollTableWrap('adminApptTableWrap', 320)">Scroll Right ▶</button>
                        </div>
                    </div>
                    <div class="dash-top-scroll">
                        <div class="dash-top-scroll-inner"></div>
                    </div>
                    <div class="dash-table-wrap" id="adminApptTableWrap">
                        <table class="dash-table" style="min-width:1120px;">
                            <thead>
                                <tr>
                                    <th style="width:120px;">Ref / Token</th>
                                    <th style="min-width:190px;">Client Profile</th>
                                    <th style="min-width:150px;">Treatment</th>
                                    <th style="min-width:130px;">Barber</th>
                                    <th style="min-width:130px;">Date & Time</th>
                                    <th style="min-width:130px;">Amount & Status</th>
                                    <th style="min-width:110px;">Visit Status</th>
                                    <th class="col-actions" style="min-width:240px;text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="apptTable">
                                <?php
                                $ap_count = 1;
                                if ($appointments_result && mysqli_num_rows($appointments_result) > 0): ?>
                                    <?php while ($ap = mysqli_fetch_assoc($appointments_result)):
                                        $st = $ap['status'];
                                        $stColor = ($st === 'Completed') ? 'gold' : (($st === 'In Chair') ? 'green' : (($st === 'Cancelled') ? 'red' : 'blue'));
                                        $clientName = $ap['client_name'];
                                        $clientEmail = $ap['client_email'] ?? '';
                                        $clientPhone = $ap['client_phone'] ?? '';
                                        $cleanSrvName = clean_text($ap['service_name']);
                                        $initials = strtoupper(substr($clientName, 0, 2));
                                    ?>
                                        <tr class="appt-row"
                                            data-status="<?php echo htmlspecialchars($st); ?>"
                                            data-search="<?php echo strtolower(htmlspecialchars($clientName . ' ' . $clientPhone . ' ' . $clientEmail . ' ' . $cleanSrvName . ' ' . $ap['stylist_name'] . ' ' . $ap['booking_ref'] . ' ' . $ap['token'])); ?>">

                                            <!-- Ref / Token -->
                                            <td>
                                                <span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--gold);background:rgba(212,175,55,0.08);padding:3px 8px;border-radius:5px;border:1px solid rgba(212,175,55,0.2);display:inline-block;">
                                                    <?php echo htmlspecialchars($ap['booking_ref'] ?: 'ELG-'.$ap['id']); ?>
                                                </span>
                                                <div style="font-size:10.5px;color:var(--text-muted);margin-top:3px;">Token: <strong><?php echo htmlspecialchars($ap['token'] ?: 'TK-'.$ap['id']); ?></strong></div>
                                            </td>

                                            <!-- Client Details (Name, Phone, Email) -->
                                            <td>
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <div style="width:36px;height:36px;border-radius:10px;background:var(--gold-soft);border:1px solid rgba(212,175,55,0.25);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);flex-shrink:0;">
                                                        <?php echo $initials; ?>
                                                    </div>
                                                    <div>
                                                        <strong style="color:var(--text-primary);font-size:13.5px;line-height:1.2;display:block;">
                                                            <?php echo safe_html($clientName); ?>
                                                        </strong>
                                                        <span style="font-size:11.5px;color:var(--text-secondary);display:block;"><?php echo safe_html($clientPhone); ?></span>
                                                        <?php if (!empty($clientEmail)): ?>
                                                            <span style="font-size:11px;color:var(--gold);opacity:0.85;display:block;"><?php echo safe_html($clientEmail); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Service & Chair -->
                                            <td>
                                                <strong style="color:var(--text-primary);font-size:13px;display:block;"><?php echo safe_html($cleanSrvName); ?></strong>
                                                <span style="font-size:11px;color:var(--gold);">Chair #<?php echo $ap['chair_no'] ?: '1'; ?></span>
                                            </td>

                                            <!-- Stylist -->
                                            <td>
                                                <span class="dash-badge blue" style="font-size:11px;"><?php echo htmlspecialchars($ap['stylist_name']); ?></span>
                                            </td>

                                            <!-- Date & Time -->
                                            <td>
                                                <div style="color:var(--text-primary);font-weight:600;font-size:13px;"><?php echo date('M d, Y', strtotime($ap['appointment_date'])); ?></div>
                                                <div style="color:var(--text-muted);font-size:11.5px;"><?php echo htmlspecialchars($ap['appointment_time']); ?></div>
                                            </td>

                                            <!-- Amount & Payment -->
                                            <td>
                                                <div style="font-size:13.5px;font-weight:700;color:var(--gold);">Rs. <?php echo number_format($ap['price']); ?></div>
                                                <span class="dash-badge <?php echo ($ap['payment_status'] === 'Paid') ? 'green' : 'orange'; ?>" style="font-size:9.5px;padding:1px 6px;margin-top:2px;">
                                                    <?php echo htmlspecialchars($ap['payment_status'] ?? 'Unpaid'); ?> &bull; <?php echo htmlspecialchars($ap['payment_mode'] ?? 'Cash'); ?>
                                                </span>
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                <span class="dash-badge <?php echo $stColor; ?>">
                                                    <?php echo htmlspecialchars($st); ?>
                                                </span>
                                            </td>

                                            <!-- Actions (Sticky Right Column) -->
                                            <td class="col-actions" style="text-align:right;">
                                                <div class="dash-action-group" style="justify-content:flex-end;">
                                                    <?php if ($st === 'Confirmed' || $st === 'Booked'): ?>
                                                        <a href="admin.php?tab=appointments&set_appt_status=In+Chair&appt_id=<?php echo $ap['id']; ?>" class="dash-btn outline sm" style="color:var(--green);border-color:rgba(5,205,153,0.4);" title="Assign to Chair">Seat</a>
                                                    <?php endif; ?>
                                                    <?php if ($st === 'In Chair'): ?>
                                                        <a href="admin.php?tab=appointments&set_appt_status=Completed&appt_id=<?php echo $ap['id']; ?>" class="dash-btn sm" style="background:var(--gold);color:#050505;font-weight:700;" title="Mark Service as Completed">Done</a>
                                                    <?php endif; ?>
                                                    <?php if ($st !== 'Cancelled' && $st !== 'Completed'): ?>
                                                        <a href="admin.php?tab=appointments&set_appt_status=Cancelled&appt_id=<?php echo $ap['id']; ?>" class="dash-btn outline sm" style="color:var(--red);border-color:rgba(239,68,68,0.3);" title="Cancel Booking">Cancel</a>
                                                    <?php endif; ?>
                                                    <button type="button" class="dash-btn outline sm" style="color:var(--cyan, #06b6d4);border-color:rgba(6,182,212,0.4);" onclick="printReceiptSlip('<?php echo htmlspecialchars($ap['booking_ref']); ?>', '<?php echo addslashes($ap['client_name']); ?>', '<?php echo addslashes($clientPhone); ?>', '<?php echo addslashes($cleanSrvName); ?>', '<?php echo addslashes($ap['stylist_name']); ?>', '<?php echo addslashes($ap['token'] ?? ''); ?>', '<?php echo $ap['price']; ?>', '<?php echo addslashes($ap['payment_mode'] ?? 'Cash'); ?>', '<?php echo $ap['appointment_date']; ?>', '<?php echo addslashes($ap['appointment_time']); ?>')">Slip</button>
                                                    <button type="button" class="dash-btn outline sm" style="color:var(--gold);border-color:rgba(212,175,55,0.4);" onclick="openEditApptModal(<?php echo $ap['id']; ?>, '<?php echo addslashes($ap['client_name']); ?>', '<?php echo addslashes($ap['client_phone']); ?>', '<?php echo addslashes($ap['client_email'] ?? ''); ?>', '<?php echo addslashes($cleanSrvName); ?>', '<?php echo addslashes($ap['stylist_name']); ?>', '<?php echo $ap['appointment_date']; ?>', '<?php echo addslashes($ap['appointment_time']); ?>', <?php echo $ap['chair_no'] ?? 1; ?>, <?php echo $ap['price']; ?>, '<?php echo addslashes($ap['payment_mode'] ?? 'Cash'); ?>', '<?php echo addslashes($ap['payment_status'] ?? 'Unpaid'); ?>', '<?php echo addslashes($ap['status']); ?>')">Edit</button>
                                                    <a href="admin.php?tab=appointments&delete_appointment=<?php echo $ap['id']; ?>" onclick="return confirm('Kya aap waqai is appointment record ko delete karna chahte hain?')" class="dash-btn outline sm" style="color:var(--red);border-color:rgba(239,68,68,0.3);text-decoration:none;">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                            No appointments found in database. Click "Book Appointment" to add your first appointment.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ============ REPORTS ============ -->
                <section class="dash-pane" id="pane-reports">
                    <div class="dash-header">
                        <div>
                            <h3>Business Reports & Analytics</h3>
                            <p>Real-time financial performance, stylist commissions, and booking trends.</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-sm-6 dash-animate-in">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Total Completed Revenue</div>
                                    <div class="dash-stat-val" style="color:var(--gold);">Rs. <?php echo number_format($total_lifetime_revenue); ?></div>
                                </div>
                                <div class="dash-stat-icon gold">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 dash-animate-in">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Total Appointments</div>
                                    <div class="dash-stat-val"><?php echo $total_appointments_count; ?></div>
                                </div>
                                <div class="dash-stat-icon blue">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 dash-animate-in">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Stock Asset Valuation</div>
                                    <div class="dash-stat-val" style="color:var(--green);">Rs. <?php echo number_format($total_cost_valuation); ?></div>
                                </div>
                                <div class="dash-stat-icon green">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 dash-animate-in">
                            <div class="dash-stat-card">
                                <div>
                                    <div class="dash-stat-label">Client Satisfaction</div>
                                    <div class="dash-stat-val" style="color:var(--orange);"><?php echo $salon_avg_rating; ?>/5.0</div>
                                </div>
                                <div class="dash-stat-icon orange">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-lg-6 dash-animate-in">
                            <div class="dash-card h-100">
                                <div class="dash-card-header">
                                    <h5>Service Revenue Share</h5>
                                </div>
                                <div style="height:240px;"><canvas id="servicePieChart"></canvas></div>
                            </div>
                        </div>
                        <div class="col-lg-6 dash-animate-in">
                            <div class="dash-card h-100">
                                <div class="dash-card-header">
                                    <h5>Peak Booking Hours</h5>
                                </div>
                                <div style="height:240px;"><canvas id="peakHoursChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <div class="dash-card dash-animate-in">
                        <div class="dash-card-header">
                            <h5>Staff Performance & Commission Payouts</h5>
                        </div>
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Stylist / Barber</th>
                                        <th>Total Appointments</th>
                                        <th>Revenue Generated</th>
                                        <th>Est. Commission (18%)</th>
                                        <th>Client Rating</th>
                                    </tr>
                                </thead>
                                <tbody id="staffPerfTable">
                                    <?php
                                    $staff_perf_q = mysqli_query($conn, "SELECT
                                        stylist_name,
                                        COUNT(*) as total_appts,
                                        SUM(price) as total_rev,
                                        SUM(commission_amount) as total_comm
                                        FROM `appointments`
                                        GROUP BY `stylist_name`
                                        ORDER BY `total_rev` DESC");
                                    if ($staff_perf_q && mysqli_num_rows($staff_perf_q) > 0): ?>
                                        <?php while ($sp = mysqli_fetch_assoc($staff_perf_q)): ?>
                                            <tr>
                                                <td><strong style="color:var(--text-primary);font-size:13.5px;"><?php echo htmlspecialchars($sp['stylist_name'] ?: 'General Barber'); ?></strong></td>
                                                <td><span class="dash-badge blue"><?php echo $sp['total_appts']; ?> Bookings</span></td>
                                                <td><strong style="color:var(--gold);font-size:14px;">Rs. <?php echo number_format($sp['total_rev']); ?></strong></td>
                                                <td><strong style="color:var(--green);font-size:13.5px;">Rs. <?php echo number_format($sp['total_comm']); ?></strong></td>
                                                <td><span style="color:var(--gold);font-weight:700;">★ 4.9</span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" style="text-align:center;padding:25px;color:var(--text-muted);">No staff performance data recorded yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ============ FEEDBACKS ============ -->
                <section class="dash-pane" id="pane-feedbacks">
                    <div class="dash-header">
                        <div>
                            <h3>Client Feedbacks & Inquiries</h3>
                            <p>Customer reviews, ratings, and website contact inquiries from MySQL database.</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-lg-4 dash-animate-in">
                            <div class="dash-card text-center" style="padding:28px;">
                                <div style="font-size:48px;font-weight:800;color:var(--gold);line-height:1;margin-bottom:8px;">
                                    <?php echo $salon_avg_rating; ?>
                                </div>
                                <div style="color:var(--gold);font-size:18px;margin-bottom:6px;">★★★★★</div>
                                <div style="font-size:12px;color:var(--text-muted);">Based on <?php echo $salon_total_reviews; ?> reviews in database</div>
                            </div>
                        </div>
                        <div class="col-lg-8 dash-animate-in">
                            <div class="dash-card" style="padding:24px;">
                                <h6 style="color:var(--text-primary);font-size:14px;margin-bottom:14px;font-weight:700;">Rating Distribution</h6>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;">
                                        <span style="width:50px;color:var(--gold);">5 Stars</span>
                                        <div style="flex:1;height:6px;background:rgba(255,255,255,0.06);border-radius:3px;overflow:hidden;">
                                            <div style="width:90%;height:100%;background:var(--gold);"></div>
                                        </div>
                                        <span style="width:30px;color:var(--text-muted);text-align:right;">90%</span>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;">
                                        <span style="width:50px;color:var(--gold);">4 Stars</span>
                                        <div style="flex:1;height:6px;background:rgba(255,255,255,0.06);border-radius:3px;overflow:hidden;">
                                            <div style="width:10%;height:100%;background:var(--gold);"></div>
                                        </div>
                                        <span style="width:30px;color:var(--text-muted);text-align:right;">10%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="dash-card mb-4">
                        <div class="dash-card-header">
                            <h5>Client Reviews</h5>
                            <span class="dash-badge gold"><?php echo $salon_total_reviews; ?> Reviews</span>
                        </div>
                        <div id="feedbacksList" class="d-flex flex-column gap-3">
                            <?php if ($reviews_result && mysqli_num_rows($reviews_result) > 0): ?>
                                <?php while ($rev = mysqli_fetch_assoc($reviews_result)): ?>
                                    <div class="dash-feedback-item">
                                        <div class="dash-feedback-main">
                                            <div style="width:42px;height:42px;border-radius:12px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:var(--gold);flex-shrink:0;">
                                                <?php echo strtoupper(substr($rev['client_name'], 0, 2)); ?>
                                            </div>
                                            <div class="dash-feedback-content">
                                                <div class="dash-feedback-header">
                                                    <strong style="color:var(--text-primary);font-size:14px;"><?php echo htmlspecialchars($rev['client_name']); ?></strong>
                                                    <span style="color:var(--gold);font-size:13px;">
                                                        <?php echo str_repeat('★', intval($rev['rating'])); ?>
                                                    </span>
                                                    <span class="dash-badge <?php echo ($rev['status'] === 'Approved') ? 'green' : 'orange'; ?>" style="font-size:9.5px;padding:2px 7px;">
                                                        <?php echo htmlspecialchars($rev['status']); ?>
                                                    </span>
                                                </div>
                                                <div style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;">
                                                    Service: <strong style="color:var(--text-primary);"><?php echo htmlspecialchars($rev['service_name'] ?: 'General'); ?></strong> &middot; Stylist: <strong><?php echo htmlspecialchars($rev['stylist_name'] ?: 'Staff'); ?></strong>
                                                </div>
                                                <p style="font-size:13px;color:var(--text-secondary);margin:0;line-height:1.4;">
                                                    "<?php echo htmlspecialchars($rev['comment']); ?>"
                                                </p>
                                            </div>
                                        </div>
                                        <div class="dash-feedback-actions">
                                            <?php if ($rev['status'] !== 'Approved'): ?>
                                                <a href="admin.php?tab=feedbacks&approve_review=<?php echo $rev['id']; ?>" class="dash-btn outline sm" style="color:var(--green);border-color:rgba(5,205,153,0.3);text-decoration:none;">Approve</a>
                                            <?php endif; ?>
                                            <a href="admin.php?tab=feedbacks&delete_review=<?php echo $rev['id']; ?>" onclick="return confirm('Kya aap is review ko delete karna chahte hain?')" class="dash-btn outline sm" style="color:var(--red);border-color:rgba(239,68,68,0.3);text-decoration:none;">Delete</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p style="text-align:center;padding:30px;color:var(--text-muted);">No client reviews recorded in database.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contact Inquiry Messages -->
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h5>Website Contact Messages</h5>
                            <span class="dash-badge blue">Inquiries</span>
                        </div>
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email / Phone</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Received</th>
                                        <th style="text-align:right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($messages_result && mysqli_num_rows($messages_result) > 0): ?>
                                        <?php while ($msg = mysqli_fetch_assoc($messages_result)): ?>
                                            <tr>
                                                <td><strong style="color:var(--text-primary);font-size:13px;"><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                                                <td>
                                                    <div style="font-size:12.5px;color:var(--text-secondary);"><?php echo htmlspecialchars($msg['email']); ?></div>
                                                    <div style="font-size:11.5px;color:var(--text-muted);"><?php echo htmlspecialchars($msg['phone']); ?></div>
                                                </td>
                                                <td><span class="dash-badge gold"><?php echo htmlspecialchars($msg['subject']); ?></span></td>
                                                <td style="max-width:300px;font-size:12.5px;color:var(--text-secondary);"><?php echo htmlspecialchars($msg['message']); ?></td>
                                                <td style="font-size:11.5px;color:var(--text-muted);"><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                                <td style="text-align:right;">
                                                    <a href="admin.php?tab=feedbacks&delete_contact_msg=<?php echo $msg['id']; ?>" onclick="return confirm('Kya aap is contact message ko delete karna chahte hain?')" class="dash-btn outline sm" style="color:var(--red);border-color:rgba(239,68,68,0.3);text-decoration:none;">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" style="text-align:center;padding:25px;color:var(--text-muted);">No contact inquiries found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- ============ MODALS ============ -->

    <!-- Add User Modal -->
    <div class="dash-modal-overlay" id="addUserModal">
        <div class="dash-modal">
            <div class="dash-modal-header">
                <h5>Add New User</h5>
                <button class="dash-modal-close" onclick="closeModal('addUserModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST" autocomplete="off">
                <div class="dash-modal-body">
                    <div class="mb-3">
                        <label class="dash-form-label">Full Name *</label>
                        <input type="text" name="name" class="dash-input" placeholder="e.g. Ali Ahmed" autocomplete="off" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Email *</label>
                            <input type="email" name="email" class="dash-input" placeholder="e.g. client@example.com" autocomplete="off" required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Phone *</label>
                            <input type="tel" name="phone" class="dash-input" placeholder="e.g. 03001234567" maxlength="11" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Password *</label>
                            <input type="password" name="password" class="dash-input" placeholder="••••••••" autocomplete="new-password" required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Role *</label>
                            <select class="dash-select" name="role" style="width:100%;">
                                <option value="client">Client</option>
                                <option value="receptionist">Receptionist</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('addUserModal')">Cancel</button>
                    <button type="submit" name="add_user_submit" class="dash-btn gold">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="dash-modal-overlay" id="editUserModal">
        <div class="dash-modal">
            <div class="dash-modal-header">
                <h5>Edit User Details</h5>
                <button class="dash-modal-close" onclick="closeModal('editUserModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="dash-modal-body">
                    <div class="mb-3">
                        <label class="dash-form-label">Full Name *</label>
                        <input type="text" name="name" class="dash-input" id="editUserName" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Email *</label>
                            <input type="email" name="email" class="dash-input" id="editUserEmail" required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Phone *</label>
                            <input type="tel" name="phone" class="dash-input" id="editUserPhone" maxlength="11"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Role *</label>
                        <select class="dash-select" name="role" id="editUserRole" style="width:100%;">
                            <option value="client">Client</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('editUserModal')">Cancel</button>
                    <button type="submit" name="update_user_submit" class="dash-btn gold">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Service Modal (Spacious Big Modal with 4 Live Image Previews) -->
    <div class="dash-modal-overlay" id="addServiceModal">
        <div class="dash-modal" style="max-width:880px;">
            <div class="dash-modal-header">
                <div>
                    <h5>✨ Add New Signature Service</h5>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Configure luxury service details, hero head banner, and 3 feature step cards</p>
                </div>
                <button class="dash-modal-close" onclick="closeModal('addServiceModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <div class="dash-modal-body" style="max-height:75vh;overflow-y:auto;padding-right:12px;">
                    <!-- General Details -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Service Title *</label>
                            <input type="text" name="name" class="dash-input" placeholder="e.g. Keratin Hair Treatment" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Tag / Badge Tagline</label>
                            <input type="text" name="tag" class="dash-input" placeholder="e.g. PREMIUM THERAPY" value="SIGNATURE SERVICE">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">Category *</label>
                            <select class="dash-select" name="category" style="width:100%;">
                                <option value="Grooming">Grooming</option>
                                <option value="Hair">Hair</option>
                                <option value="Beard">Beard</option>
                                <option value="Facial">Facial</option>
                                <option value="Shave">Shave</option>
                                <option value="Spa">Spa & Treatment</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Price (PKR) *</label>
                            <input type="number" name="price" class="dash-input" placeholder="4500" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Duration *</label>
                            <select class="dash-select" name="duration" style="width:100%;">
                                <option value="30 min">30 min</option>
                                <option value="45 min">45 min</option>
                                <option value="60 min" selected>60 min</option>
                                <option value="90 min">90 min</option>
                                <option value="120 min">120 min</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Subtitle / Hook Line *</label>
                        <input type="text" name="subtitle" class="dash-input" placeholder="e.g. Restorative keratin infusion for silky, frizz-free texture.">
                    </div>

                    <!-- Hero Banner Live Preview Section -->
                    <div style="background:rgba(212,175,55,0.05);border:1px solid var(--border-gold);border-radius:12px;padding:14px;margin-bottom:16px;">
                        <label class="dash-form-label" style="color:var(--gold);">🌟 Top Head Hero Banner Image</label>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <div style="height:90px;width:100%;border-radius:10px;overflow:hidden;border:1px solid rgba(255,255,255,0.15);background:#111;">
                                    <img id="addSrvHeroPreview" src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="hero_image" id="addSrvHeroInput" class="dash-input mb-2"
                                    value="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop"
                                    oninput="document.getElementById('addSrvHeroPreview').src = this.value || 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop'">
                                <small style="color:var(--text-muted);font-size:11px;">Live preview updates automatically when you paste an image URL or local path.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Main Overview Description</label>
                        <textarea name="description" class="dash-input" rows="2" placeholder="Comprehensive description of the service experience in Karachi..."></textarea>
                    </div>

                    <!-- 3 Feature Cards Section with Live Images -->
                    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:16px;margin-bottom:16px;">
                        <h6 style="color:var(--gold);margin-bottom:14px;font-size:14px;">📸 3 Step Feature Cards (What happens in this service)</h6>

                        <!-- Card 1 -->
                        <div class="p-3 mb-3 rounded-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div style="width:75px;height:75px;border-radius:10px;overflow:hidden;border:1px solid var(--border-gold);background:#111;">
                                        <img id="addSrvC1Preview" src="assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="card1_title" class="dash-input" placeholder="Card 1 Step Title (e.g. Precision Cut)">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card1_img" class="dash-input" placeholder="Image URL / Path" value="assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg" oninput="document.getElementById('addSrvC1Preview').src = this.value">
                                        </div>
                                    </div>
                                    <input type="text" name="card1_desc" class="dash-input" placeholder="Card 1 explanation...">
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="p-3 mb-3 rounded-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div style="width:75px;height:75px;border-radius:10px;overflow:hidden;border:1px solid var(--border-gold);background:#111;">
                                        <img id="addSrvC2Preview" src="assets/images/homepage/total-grooming/2.jpg" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="card2_title" class="dash-input" placeholder="Card 2 Step Title (e.g. Beard Sculpt)">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card2_img" class="dash-input" placeholder="Image URL / Path" value="assets/images/homepage/total-grooming/2.jpg" oninput="document.getElementById('addSrvC2Preview').src = this.value">
                                        </div>
                                    </div>
                                    <input type="text" name="card2_desc" class="dash-input" placeholder="Card 2 explanation...">
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="p-3 rounded-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div style="width:75px;height:75px;border-radius:10px;overflow:hidden;border:1px solid var(--border-gold);background:#111;">
                                        <img id="addSrvC3Preview" src="assets/images/homepage/total-grooming/3.jpg" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="card3_title" class="dash-input" placeholder="Card 3 Step Title (e.g. Hot Towel Therapy)">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card3_img" class="dash-input" placeholder="Image URL / Path" value="assets/images/homepage/total-grooming/3.jpg" oninput="document.getElementById('addSrvC3Preview').src = this.value">
                                        </div>
                                    </div>
                                    <input type="text" name="card3_desc" class="dash-input" placeholder="Card 3 explanation...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="dash-form-label">Package Inclusions Summary (Bottom Box)</label>
                        <input type="text" name="package_details" class="dash-input" placeholder="e.g. Includes consultation, shampoo wash, precision cut, and hot towel finish.">
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('addServiceModal')">Cancel</button>
                    <button type="submit" name="add_service_submit" class="dash-btn gold">Save Service Package</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal (Spacious Big Modal with 4 Live Image Previews) -->
    <div class="dash-modal-overlay" id="editServiceModal">
        <div class="dash-modal" style="max-width:880px;">
            <div class="dash-modal-header">
                <div>
                    <h5>✨ Edit Service Details & Live Cards</h5>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Update header, description, pricing, and all 3 feature cards</p>
                </div>
                <button class="dash-modal-close" onclick="closeModal('editServiceModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="service_id" id="editServiceId">
                <div class="dash-modal-body" style="max-height:75vh;overflow-y:auto;padding-right:12px;">
                    <!-- General Details -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Service Title *</label>
                            <input type="text" name="name" class="dash-input" id="editServiceName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Tag / Badge Tagline</label>
                            <input type="text" name="tag" class="dash-input" id="editServiceTag">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">Category *</label>
                            <select class="dash-select" name="category" id="editServiceCategory" style="width:100%;">
                                <option value="Grooming">Grooming</option>
                                <option value="Hair">Hair</option>
                                <option value="Beard">Beard</option>
                                <option value="Facial">Facial</option>
                                <option value="Shave">Shave</option>
                                <option value="Spa">Spa & Treatment</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Price (PKR) *</label>
                            <input type="number" name="price" class="dash-input" id="editServicePrice" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Duration *</label>
                            <select class="dash-select" name="duration" id="editServiceDuration" style="width:100%;">
                                <option value="30 min">30 min</option>
                                <option value="45 min">45 min</option>
                                <option value="60 min">60 min</option>
                                <option value="90 min">90 min</option>
                                <option value="120 min">120 min</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Subtitle / Hook Line</label>
                        <input type="text" name="subtitle" class="dash-input" id="editServiceSubtitle">
                    </div>

                    <!-- Hero Banner Live Preview Section -->
                    <div style="background:rgba(212,175,55,0.05);border:1px solid var(--border-gold);border-radius:12px;padding:14px;margin-bottom:16px;">
                        <label class="dash-form-label" style="color:var(--gold);">🌟 Top Head Hero Banner Image</label>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <div style="height:90px;width:100%;border-radius:10px;overflow:hidden;border:1px solid rgba(255,255,255,0.15);background:#111;">
                                    <img id="editSrvHeroPreview" src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="hero_image" id="editServiceHero" class="dash-input mb-2"
                                    oninput="document.getElementById('editSrvHeroPreview').src = this.value">
                                <small style="color:var(--text-muted);font-size:11px;">Live preview updates automatically when you paste an image URL or local path.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Main Overview Description</label>
                        <textarea name="description" id="editServiceDesc" class="dash-input" rows="2"></textarea>
                    </div>

                    <!-- 3 Feature Cards Section with Live Images -->
                    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:16px;margin-bottom:16px;">
                        <h6 style="color:var(--gold);margin-bottom:14px;font-size:14px;">📸 3 Step Feature Cards (What happens in this service)</h6>

                        <!-- Card 1 -->
                        <div class="p-3 mb-3 rounded-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div style="width:75px;height:75px;border-radius:10px;overflow:hidden;border:1px solid var(--border-gold);background:#111;">
                                        <img id="editSrvC1Preview" src="assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="card1_title" class="dash-input" id="editServiceC1Title" placeholder="Card 1 Title">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card1_img" class="dash-input" id="editServiceC1Img" placeholder="Image URL" oninput="document.getElementById('editSrvC1Preview').src = this.value">
                                        </div>
                                    </div>
                                    <input type="text" name="card1_desc" class="dash-input" id="editServiceC1Desc" placeholder="Card 1 description...">
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="p-3 mb-3 rounded-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div style="width:75px;height:75px;border-radius:10px;overflow:hidden;border:1px solid var(--border-gold);background:#111;">
                                        <img id="editSrvC2Preview" src="assets/images/homepage/total-grooming/2.jpg" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="card2_title" class="dash-input" id="editServiceC2Title" placeholder="Card 2 Title">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card2_img" class="dash-input" id="editServiceC2Img" placeholder="Image URL" oninput="document.getElementById('editSrvC2Preview').src = this.value">
                                        </div>
                                    </div>
                                    <input type="text" name="card2_desc" class="dash-input" id="editServiceC2Desc" placeholder="Card 2 description...">
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="p-3 rounded-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div style="width:75px;height:75px;border-radius:10px;overflow:hidden;border:1px solid var(--border-gold);background:#111;">
                                        <img id="editSrvC3Preview" src="assets/images/homepage/total-grooming/3.jpg" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="card3_title" class="dash-input" id="editServiceC3Title" placeholder="Card 3 Title">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card3_img" class="dash-input" id="editServiceC3Img" placeholder="Image URL" oninput="document.getElementById('editSrvC3Preview').src = this.value">
                                        </div>
                                    </div>
                                    <input type="text" name="card3_desc" class="dash-input" id="editServiceC3Desc" placeholder="Card 3 description...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="dash-form-label">Package Inclusions Summary (Bottom Box)</label>
                        <input type="text" name="package_details" class="dash-input" id="editServicePkg">
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('editServiceModal')">Cancel</button>
                    <button type="submit" name="update_service_submit" class="dash-btn gold">Update Service Package</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Service Details Modal -->
    <div class="dash-modal-overlay" id="viewServiceModal">
        <div class="dash-modal" style="max-width:820px;">
            <div class="dash-modal-header" style="border-bottom:none;padding-bottom:0;">
                <div>
                    <span class="dash-badge gold" id="viewServiceTag" style="font-size:10.5px;letter-spacing:1px;text-transform:uppercase;">SIGNATURE SERVICE</span>
                    <h4 id="viewServiceName" style="color:var(--text-primary);margin:6px 0 0 0;font-size:22px;">Service Details</h4>
                </div>
                <button class="dash-modal-close" onclick="closeModal('viewServiceModal')">&times;</button>
            </div>
            <div class="dash-modal-body" style="max-height:75vh;overflow-y:auto;padding-top:14px;">
                <!-- Hero Preview Banner -->
                <div style="position:relative;height:200px;border-radius:14px;overflow:hidden;border:1px solid var(--border-gold);margin-bottom:18px;">
                    <img id="viewServiceHeroImg" src="" style="width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;inset:0;background:linear-gradient(0deg, rgba(9,10,15,0.9) 0%, rgba(9,10,15,0.3) 60%, transparent 100%);"></div>
                    <div style="position:absolute;bottom:14px;left:18px;right:18px;display:flex;justify-content:space-between;align-items:flex-end;">
                        <div>
                            <span class="dash-badge blue" id="viewServiceCategory" style="font-size:11px;margin-bottom:4px;">Grooming</span>
                            <div id="viewServiceSubtitle" style="color:var(--text-secondary);font-size:13px;font-style:italic;">Sub-headline text</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Price & Duration</div>
                            <div style="font-size:20px;font-weight:800;color:var(--gold);"><span id="viewServicePrice">Rs. 0</span> <span id="viewServiceDuration" style="font-size:12px;color:var(--text-secondary);font-weight:500;">/ 60 min</span></div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="dash-form-label" style="color:var(--gold);">📖 Overview Description</label>
                    <p id="viewServiceDesc" style="color:var(--text-secondary);font-size:13.5px;line-height:1.6;background:rgba(255,255,255,0.03);padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.08);margin:0;"></p>
                </div>

                <!-- 3 Step Feature Cards -->
                <div class="mb-4">
                    <label class="dash-form-label" style="color:var(--gold);">📸 Service Experience Steps</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:hidden;height:100%;">
                                <div style="height:100px;overflow:hidden;">
                                    <img id="viewServiceC1Img" src="" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div style="padding:12px;">
                                    <strong id="viewServiceC1Title" style="color:var(--text-primary);font-size:13px;display:block;margin-bottom:4px;">Card 1</strong>
                                    <p id="viewServiceC1Desc" style="color:var(--text-muted);font-size:11.5px;margin:0;line-height:1.4;"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:hidden;height:100%;">
                                <div style="height:100px;overflow:hidden;">
                                    <img id="viewServiceC2Img" src="" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div style="padding:12px;">
                                    <strong id="viewServiceC2Title" style="color:var(--text-primary);font-size:13px;display:block;margin-bottom:4px;">Card 2</strong>
                                    <p id="viewServiceC2Desc" style="color:var(--text-muted);font-size:11.5px;margin:0;line-height:1.4;"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:hidden;height:100%;">
                                <div style="height:100px;overflow:hidden;">
                                    <img id="viewServiceC3Img" src="" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div style="padding:12px;">
                                    <strong id="viewServiceC3Title" style="color:var(--text-primary);font-size:13px;display:block;margin-bottom:4px;">Card 3</strong>
                                    <p id="viewServiceC3Desc" style="color:var(--text-muted);font-size:11.5px;margin:0;line-height:1.4;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Details Summary -->
                <div class="mb-3">
                    <label class="dash-form-label" style="color:var(--gold);">✨ Inclusions & Notes</label>
                    <div id="viewServicePkg" style="color:var(--text-secondary);font-size:13px;background:rgba(212,175,55,0.04);border:1px solid var(--border-gold);padding:12px;border-radius:10px;"></div>
                </div>
            </div>
            <div class="dash-modal-footer" style="justify-content:space-between;">
                <a id="viewServicePublicLink" href="#" target="_blank" class="dash-btn outline" style="color:var(--cyan, #06b6d4);border-color:rgba(6,182,212,0.4);text-decoration:none;">
                    🔗 Open Live Website Page
                </a>
                <div>
                    <button type="button" class="dash-btn outline me-2" onclick="closeModal('viewServiceModal')">Close</button>
                    <button type="button" class="dash-btn gold" id="viewServiceEditBtn">✏️ Edit Service</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Stylist Profile Modal -->
    <div class="dash-modal-overlay" id="viewStylistModal">
        <div class="dash-modal" style="max-width:680px;">
            <div class="dash-modal-header">
                <h5>Stylist Profile & Performance</h5>
                <button class="dash-modal-close" onclick="closeModal('viewStylistModal')">&times;</button>
            </div>
            <div class="dash-modal-body" style="max-height:75vh;overflow-y:auto;">
                <!-- Profile Top Header -->
                <div style="display:flex;align-items:center;gap:20px;margin-bottom:22px;background:rgba(255,255,255,0.02);padding:18px;border-radius:14px;border:1px solid rgba(255,255,255,0.08);">
                    <div style="width:90px;height:90px;border-radius:18px;overflow:hidden;border:2px solid var(--border-gold);flex-shrink:0;background:#111;">
                        <img id="viewStylistPhoto" src="" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                            <h4 id="viewStylistName" style="color:var(--text-primary);margin:0;font-size:20px;font-weight:700;">Stylist Name</h4>
                            <span id="viewStylistStatus" class="dash-badge green">Active</span>
                        </div>
                        <div id="viewStylistSpec" style="color:var(--gold);font-size:13px;font-weight:600;margin-bottom:6px;">Fade Specialist</div>
                        <div style="display:flex;gap:14px;font-size:12px;color:var(--text-secondary);">
                            <span id="viewStylistPhone">📞 0300...</span>
                            <span id="viewStylistEmail">✉️ email@...</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);padding:14px;border-radius:12px;text-align:center;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Commission Rate</div>
                            <div id="viewStylistComm" style="font-size:20px;font-weight:800;color:var(--gold);margin-top:2px;">18%</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);padding:14px;border-radius:12px;text-align:center;">
                            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Work Schedule</div>
                            <div id="viewStylistSchedule" style="font-size:13px;font-weight:600;color:var(--text-primary);margin-top:4px;">Tue - Sun (11am - 9pm)</div>
                        </div>
                    </div>
                </div>

                <!-- Bio -->
                <div class="mb-3">
                    <label class="dash-form-label" style="color:var(--gold);">📜 Biography & Background</label>
                    <p id="viewStylistBio" style="color:var(--text-secondary);font-size:13px;line-height:1.6;background:rgba(255,255,255,0.03);padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.08);margin:0;">No biography added yet.</p>
                </div>
            </div>
            <div class="dash-modal-footer" style="justify-content:space-between;">
                <a id="viewStylistPublicLink" href="#" target="_blank" class="dash-btn outline" style="color:var(--cyan, #06b6d4);border-color:rgba(6,182,212,0.4);text-decoration:none;">
                    🔗 Open Public Profile
                </a>
                <div>
                    <button type="button" class="dash-btn outline me-2" onclick="closeModal('viewStylistModal')">Close</button>
                    <button type="button" class="dash-btn gold" id="viewStylistEditBtn">✏️ Edit Stylist</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Stylist Modal -->
    <div class="dash-modal-overlay" id="addStylistModal">
        <div class="dash-modal">
            <div class="dash-modal-header">
                <h5>Add New Stylist</h5>
                <button class="dash-modal-close" onclick="closeModal('addStylistModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <div class="dash-modal-body">
                    <div class="mb-3">
                        <label class="dash-form-label">Full Name *</label>
                        <input type="text" name="name" class="dash-input" placeholder="e.g. Zachary Nickell" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Email</label>
                            <input type="email" name="email" class="dash-input" placeholder="stylist@elegance.pk">
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Phone *</label>
                            <input type="tel" name="phone" class="dash-input" placeholder="03221234567" maxlength="11"
                                required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Specialization *</label>
                            <input type="text" name="specialization" class="dash-input"
                                placeholder="e.g. Fade & Beard Specialist" required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Commission (%) *</label>
                            <input type="number" name="commission_rate" class="dash-input" value="18" step="0.5"
                                required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Work Schedule *</label>
                            <input type="text" name="work_schedule" class="dash-input"
                                placeholder="e.g. Tue-Sun 11AM-9PM" required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Status *</label>
                            <select class="dash-select" name="status" style="width:100%;">
                                <option value="Active">Active</option>
                                <option value="On Leave">On Leave</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Image Path / Photo</label>
                        <input type="text" name="image" class="dash-input" placeholder="assets/images/homepage/stylist/1.jpg" value="assets/images/homepage/stylist/1.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Short Bio / Philosophy</label>
                        <textarea name="bio" class="dash-input" rows="2" placeholder="e.g. Master barber specializing in textured styling & fades with 8+ years experience..."></textarea>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline"
                        onclick="closeModal('addStylistModal')">Cancel</button>
                    <button type="submit" name="add_stylist_submit" class="dash-btn gold">Save Stylist</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Stylist Modal -->
    <div class="dash-modal-overlay" id="editStylistModal">
        <div class="dash-modal">
            <div class="dash-modal-header">
                <h5>Edit Stylist Details</h5>
                <button class="dash-modal-close" onclick="closeModal('editStylistModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="stylist_id" id="editStylistId">
                <div class="dash-modal-body">
                    <div class="mb-3">
                        <label class="dash-form-label">Full Name *</label>
                        <input type="text" name="name" class="dash-input" id="editStylistName" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Email</label>
                            <input type="email" name="email" class="dash-input" id="editStylistEmail">
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Phone *</label>
                            <input type="tel" name="phone" class="dash-input" id="editStylistPhone" maxlength="11"
                                required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Specialization *</label>
                            <input type="text" name="specialization" class="dash-input" id="editStylistSpec" required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Commission (%) *</label>
                            <input type="number" name="commission_rate" class="dash-input" id="editStylistComm"
                                step="0.5" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="dash-form-label">Work Schedule *</label>
                            <input type="text" name="work_schedule" class="dash-input" id="editStylistSchedule"
                                required>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Status *</label>
                            <select class="dash-select" name="status" id="editStylistStatus" style="width:100%;">
                                <option value="Active">Active</option>
                                <option value="On Leave">On Leave</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Image Path / Photo</label>
                        <input type="text" name="image" class="dash-input" id="editStylistImage" placeholder="assets/images/homepage/stylist/1.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Short Bio / Philosophy</label>
                        <textarea name="bio" class="dash-input" id="editStylistBio" rows="2" placeholder="Stylist background & bio details..."></textarea>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline"
                        onclick="closeModal('editStylistModal')">Cancel</button>
                    <button type="submit" name="update_stylist_submit" class="dash-btn gold">Update Stylist</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Stock Item Modal -->
    <!-- Add Stock Item Modal -->
    <!-- Add Stock Item Modal -->
    <div class="dash-modal-overlay" id="addStockModal">
        <div class="dash-modal" style="max-width:650px;">
            <div class="dash-modal-header">
                <div>
                    <h5>Add New Inventory Product</h5>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Register retail merchandise or salon consumable supply</p>
                </div>
                <button class="dash-modal-close" onclick="closeModal('addStockModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <div class="dash-modal-body">
                    <!-- Image Preview & Path -->
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-auto">
                            <div style="width:70px;height:70px;border-radius:12px;border:2px dashed var(--gold-border);background:#111;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                                <img id="addStockImgPreview" src="https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Product Image URL / Path *</label>
                            <input type="text" name="image" id="addStockImgInput" class="dash-input"
                                value="https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop"
                                placeholder="https://images.unsplash.com/... or assets/images/products/..."
                                oninput="document.getElementById('addStockImgPreview').src = this.value || 'https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop'" required>
                            <small style="color:var(--text-muted);font-size:11px;">Paste any product image link or local asset path.</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">SKU / Barcode *</label>
                            <input type="text" name="code" class="dash-input" placeholder="e.g. PRD-109" style="font-family:monospace;font-weight:700;color:var(--gold);" required>
                        </div>
                        <div class="col-md-8">
                            <label class="dash-form-label">Brand / Manufacturer *</label>
                            <input type="text" name="brand" class="dash-input" placeholder="e.g. Baxter of California / L'Oreal" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Product Name *</label>
                        <input type="text" name="name" class="dash-input" placeholder="e.g. Matte Styling Clay Pomade (100ml)" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Category *</label>
                            <select class="dash-select" name="category" style="width:100%;">
                                <option value="Hair Care">Hair Care</option>
                                <option value="Beard Care">Beard Care</option>
                                <option value="Skin Care">Skin Care</option>
                                <option value="Tools & Blades">Tools & Blades</option>
                                <option value="Fragrance">Fragrance</option>
                                <option value="Treatments">Treatments</option>
                                <option value="Shave Supplies">Shave Supplies</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Unit Type *</label>
                            <select class="dash-select" name="unit_type" style="width:100%;">
                                <option value="Jars">Jars (e.g. Wax, Clay, Pomade)</option>
                                <option value="Bottles">Bottles (e.g. Oil, Spray, Cologne)</option>
                                <option value="Tubes">Tubes (e.g. Cream, Gel)</option>
                                <option value="Packs">Packs (e.g. Blades, Foils)</option>
                                <option value="Kits">Kits (e.g. Facial Kit)</option>
                                <option value="Pieces">Pieces</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pricing & Margin Live Calculation -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Cost Price / Unit (PKR) *</label>
                            <input type="number" step="0.01" name="cost_price" id="addCostInput" class="dash-input" placeholder="1200" value="1200" oninput="calcAddMargin()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Retail Selling Price (PKR) *</label>
                            <input type="number" step="0.01" name="retail_price" id="addRetailInput" class="dash-input" placeholder="2200" value="2200" oninput="calcAddMargin()" required>
                        </div>
                    </div>

                    <div id="addMarginInfo" style="background:rgba(5,205,153,0.08);border:1px solid rgba(5,205,153,0.2);padding:8px 12px;border-radius:10px;margin-bottom:14px;font-size:12px;color:var(--green);">
                        💰 Estimated Profit: <strong>Rs. 1,000 / unit</strong> (+83.3% Markup Margin)
                    </div>

                    <!-- Stock & Alerts -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Initial Quantity in Stock *</label>
                            <input type="number" name="quantity" class="dash-input" placeholder="20" value="20" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Low Stock Alert Threshold *</label>
                            <input type="number" name="min_threshold" class="dash-input" placeholder="5" value="5" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Supplier / Distributor Name</label>
                        <input type="text" name="supplier_name" class="dash-input" placeholder="e.g. Karachi Salon Traders (Tariq Rd)">
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('addStockModal')">Cancel</button>
                    <button type="submit" name="add_stock_submit" class="dash-btn gold">Save Product to Inventory</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Stock Item Modal -->
    <div class="dash-modal-overlay" id="editStockModal">
        <div class="dash-modal" style="max-width:650px;">
            <div class="dash-modal-header">
                <div>
                    <h5>Edit Inventory Product</h5>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Update specifications, pricing margins, stock, or supplier</p>
                </div>
                <button class="dash-modal-close" onclick="closeModal('editStockModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="stock_id" id="editStockId">
                <div class="dash-modal-body">
                    <!-- Image Preview & Path -->
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-auto">
                            <div style="width:70px;height:70px;border-radius:12px;border:2px dashed var(--gold-border);background:#111;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                                <img id="editStockImgPreview" src="" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </div>
                        <div class="col">
                            <label class="dash-form-label">Product Image URL / Path *</label>
                            <input type="text" name="image" id="editStockImage" class="dash-input"
                                placeholder="Image URL or local asset path"
                                oninput="document.getElementById('editStockImgPreview').src = this.value;" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">SKU / Barcode *</label>
                            <input type="text" name="code" id="editStockCode" class="dash-input" style="font-family:monospace;font-weight:700;color:var(--gold);" required>
                        </div>
                        <div class="col-md-8">
                            <label class="dash-form-label">Brand / Manufacturer *</label>
                            <input type="text" name="brand" id="editStockBrand" class="dash-input" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Product Name *</label>
                        <input type="text" name="name" id="editStockName" class="dash-input" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Category *</label>
                            <select class="dash-select" name="category" id="editStockCategory" style="width:100%;">
                                <option value="Hair Care">Hair Care</option>
                                <option value="Beard Care">Beard Care</option>
                                <option value="Skin Care">Skin Care</option>
                                <option value="Tools & Blades">Tools & Blades</option>
                                <option value="Fragrance">Fragrance</option>
                                <option value="Treatments">Treatments</option>
                                <option value="Shave Supplies">Shave Supplies</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Unit Type *</label>
                            <select class="dash-select" name="unit_type" id="editStockUnit" style="width:100%;">
                                <option value="Jars">Jars (e.g. Wax, Clay, Pomade)</option>
                                <option value="Bottles">Bottles (e.g. Oil, Spray, Cologne)</option>
                                <option value="Tubes">Tubes (e.g. Cream, Gel)</option>
                                <option value="Packs">Packs (e.g. Blades, Foils)</option>
                                <option value="Kits">Kits (e.g. Facial Kit)</option>
                                <option value="Pieces">Pieces</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pricing & Margin Live Calculation -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Cost Price / Unit (PKR) *</label>
                            <input type="number" step="0.01" name="cost_price" id="editStockCost" class="dash-input" oninput="calcEditMargin()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Retail Selling Price (PKR) *</label>
                            <input type="number" step="0.01" name="retail_price" id="editStockRetail" class="dash-input" oninput="calcEditMargin()" required>
                        </div>
                    </div>

                    <div id="editMarginInfo" style="background:rgba(5,205,153,0.08);border:1px solid rgba(5,205,153,0.2);padding:8px 12px;border-radius:10px;margin-bottom:14px;font-size:12px;color:var(--green);">
                        💰 Margin Details
                    </div>

                    <!-- Stock & Alerts -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Current Quantity in Stock *</label>
                            <input type="number" name="quantity" id="editStockQty" class="dash-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Low Stock Alert Threshold *</label>
                            <input type="number" name="min_threshold" id="editStockMin" class="dash-input" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">Supplier / Distributor Name</label>
                        <input type="text" name="supplier_name" id="editStockSupplier" class="dash-input">
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('editStockModal')">Cancel</button>
                    <button type="submit" name="update_stock_submit" class="dash-btn gold">Update Product Details</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Restock Modal -->
    <div class="dash-modal-overlay" id="quickRestockModal">
        <div class="dash-modal" style="max-width:440px;">
            <div class="dash-modal-header">
                <h5>+ Quick Restock Product</h5>
                <button class="dash-modal-close" onclick="closeModal('quickRestockModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="restock_item_id" id="restockItemId">
                <div class="dash-modal-body">
                    <p style="color:var(--text-secondary);font-size:14px;margin-bottom:15px;">
                        Restocking Product: <strong id="restockItemName" style="color:var(--gold);"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="dash-form-label">Units to Add *</label>
                        <input type="number" name="add_quantity" class="dash-input" value="10" min="1" required>
                        <small style="color:var(--text-muted);font-size:11px;">Ye quantity current stock me add ho jayegi aur status update ho jayega.</small>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('quickRestockModal')">Cancel</button>
                    <button type="submit" name="quick_restock_submit" class="dash-btn gold">+ Add to Stock</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Book Appointment Modal -->
    <div class="dash-modal-overlay" id="bookModal">
        <div class="dash-modal" style="max-width:620px;">
            <div class="dash-modal-header">
                <div>
                    <h5>Book New Salon Appointment</h5>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Schedule client visit, assign chair, and set service pricing</p>
                </div>
                <button class="dash-modal-close" onclick="closeModal('bookModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <div class="dash-modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Client Full Name *</label>
                            <input type="text" name="client_name" class="dash-input" placeholder="e.g. Bilal Ahmed" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Client Phone *</label>
                            <input type="tel" name="client_phone" class="dash-input" placeholder="e.g. 03001234567" maxlength="11" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Client Email (Optional)</label>
                        <input type="email" name="client_email" class="dash-input" placeholder="e.g. client@example.com">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Selected Service *</label>
                            <select class="dash-select" name="service_name" id="bServiceSelect" style="width:100%;" onchange="updateBookPrice(this)" required>
                                <option value="" disabled selected>-- Select Service --</option>
                                <?php foreach ($services_list_cache as $svc): ?>
                                    <option value="<?php echo htmlspecialchars($svc['name']); ?>" data-price="<?php echo $svc['price']; ?>">
                                        <?php echo htmlspecialchars($svc['name']); ?> (Rs. <?php echo number_format($svc['price']); ?> &middot; <?php echo htmlspecialchars($svc['duration']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Assigned Stylist *</label>
                            <select class="dash-select" name="stylist_name" style="width:100%;" required>
                                <?php foreach ($staff_list_cache as $stf): ?>
                                    <option value="<?php echo htmlspecialchars($stf['name']); ?>">
                                        <?php echo htmlspecialchars($stf['name']); ?> (<?php echo htmlspecialchars($stf['specialization']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">Date *</label>
                            <input type="date" name="appointment_date" class="dash-input" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Time Slot *</label>
                            <select class="dash-select" name="appointment_time" style="width:100%;" required>
                                <option>10:00 AM</option>
                                <option>11:00 AM</option>
                                <option>12:00 PM</option>
                                <option>01:30 PM</option>
                                <option>02:30 PM</option>
                                <option>03:30 PM</option>
                                <option>04:30 PM</option>
                                <option>05:30 PM</option>
                                <option>06:30 PM</option>
                                <option>07:30 PM</option>
                                <option>08:30 PM</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Chair / Station *</label>
                            <select class="dash-select" name="chair_no" style="width:100%;">
                                <option value="1">Chair #1 (VIP Master)</option>
                                <option value="2">Chair #2 (Styling)</option>
                                <option value="3">Chair #3 (Grooming)</option>
                                <option value="4">Chair #4 (Executive)</option>
                                <option value="5">Chair #5 (Color Bar)</option>
                                <option value="6">Chair #6 (Shampoo / Wash)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">Amount (PKR) *</label>
                            <input type="number" name="price" id="bPriceInput" class="dash-input" placeholder="2500" value="2500" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Payment Method *</label>
                            <select class="dash-select" name="payment_mode" style="width:100%;">
                                <option value="Cash">Cash</option>
                                <option value="JazzCash">JazzCash</option>
                                <option value="EasyPaisa">EasyPaisa</option>
                                <option value="Card">Credit/Debit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Payment Status *</label>
                            <select class="dash-select" name="payment_status" style="width:100%;">
                                <option value="Unpaid">Unpaid (Pay after service)</option>
                                <option value="Paid">Paid (Advance / Counter)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Initial Booking Status</label>
                        <select class="dash-select" name="status" style="width:100%;">
                            <option value="Confirmed">Confirmed</option>
                            <option value="In Chair">In Chair (Customer Arrived)</option>
                            <option value="Booked">Booked</option>
                        </select>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('bookModal')">Cancel</button>
                    <button type="submit" name="book_appointment_submit" class="dash-btn gold">Confirm & Save Booking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Appointment Modal -->
    <div class="dash-modal-overlay" id="editApptModal">
        <div class="dash-modal" style="max-width:620px;">
            <div class="dash-modal-header">
                <div>
                    <h5>Edit Appointment Details</h5>
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">Update appointment schedule, stylist, chair, price or status</p>
                </div>
                <button class="dash-modal-close" onclick="closeModal('editApptModal')">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="appointment_id" id="editApptId">
                <div class="dash-modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Client Name *</label>
                            <input type="text" name="client_name" id="editApptName" class="dash-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Client Phone *</label>
                            <input type="tel" name="client_phone" id="editApptPhone" class="dash-input" maxlength="11" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Client Email</label>
                        <input type="email" name="client_email" id="editApptEmail" class="dash-input">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">Service *</label>
                            <select class="dash-select" name="service_name" id="editApptService" style="width:100%;" required>
                                <?php foreach ($services_list_cache as $svc): ?>
                                    <option value="<?php echo htmlspecialchars($svc['name']); ?>">
                                        <?php echo htmlspecialchars($svc['name']); ?> (Rs. <?php echo number_format($svc['price']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">Stylist *</label>
                            <select class="dash-select" name="stylist_name" id="editApptStylist" style="width:100%;" required>
                                <?php foreach ($staff_list_cache as $stf): ?>
                                    <option value="<?php echo htmlspecialchars($stf['name']); ?>">
                                        <?php echo htmlspecialchars($stf['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">Date *</label>
                            <input type="date" name="appointment_date" id="editApptDate" class="dash-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Time Slot *</label>
                            <input type="text" name="appointment_time" id="editApptTime" class="dash-input" placeholder="e.g. 02:00 PM" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Chair No *</label>
                            <input type="number" name="chair_no" id="editApptChair" class="dash-input" min="1" max="10" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="dash-form-label">Amount (PKR) *</label>
                            <input type="number" name="price" id="editApptPrice" class="dash-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Payment Mode *</label>
                            <select class="dash-select" name="payment_mode" id="editApptPayMode" style="width:100%;">
                                <option value="Cash">Cash</option>
                                <option value="JazzCash">JazzCash</option>
                                <option value="EasyPaisa">EasyPaisa</option>
                                <option value="Card">Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="dash-form-label">Payment Status *</label>
                            <select class="dash-select" name="payment_status" id="editApptPayStatus" style="width:100%;">
                                <option value="Unpaid">Unpaid</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dash-form-label">Appointment Status *</label>
                        <select class="dash-select" name="status" id="editApptStatus" style="width:100%;">
                            <option value="Confirmed">Confirmed</option>
                            <option value="In Chair">In Chair</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Booked">Booked</option>
                        </select>
                    </div>
                </div>
                <div class="dash-modal-footer">
                    <button type="button" class="dash-btn outline" onclick="closeModal('editApptModal')">Cancel</button>
                    <button type="submit" name="update_appointment_submit" class="dash-btn gold">Update Appointment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipt Modal (Thermal POS Slip) -->
    <div class="dash-modal-overlay" id="receiptModal">
        <div class="dash-modal" style="max-width:440px;">
            <div class="dash-modal-header">
                <h5 style="display:flex;align-items:center;gap:8px;">
                    <span style="color:var(--gold);">🧾</span> Salon POS Receipt Slip
                </h5>
                <button class="dash-modal-close" onclick="closeModal('receiptModal')">&times;</button>
            </div>
            <div class="dash-modal-body" style="background:#0c0a07;padding:16px 20px;">
                <div id="receiptContent" class="thermal-receipt-paper"></div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn outline" onclick="closeModal('receiptModal')">Close</button>
                <button type="button" class="dash-btn outline" style="color:var(--cyan, #06b6d4);border-color:rgba(6,182,212,0.4);" onclick="downloadReceiptPDF()">📥 Download PDF</button>
                <button type="button" class="dash-btn gold" onclick="printCleanReceipt()">🖨️ Print Slip (80mm)</button>
            </div>
        </div>
    </div>

    <script src="html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dashboard.js?v=<?php echo time(); ?>"></script>
    <script>
        function openEditUserModal(id, name, email, phone, role) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = name;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserPhone').value = phone;
            document.getElementById('editUserRole').value = role;
            openModal('editUserModal');
        }

        function setAddStockPreset(imgUrl, category) {
            document.getElementById('addStockImgInput').value = imgUrl;
            document.getElementById('addStockImgPreview').src = imgUrl;
        }

        function setEditStockPreset(imgUrl) {
            document.getElementById('editStockImage').value = imgUrl;
            document.getElementById('editStockImgPreview').src = imgUrl;
        }

        function openEditServiceModal(data) {
            if (typeof data === 'string') {
                try { data = JSON.parse(data); } catch(e) {}
            }
            document.getElementById('editServiceId').value = data.id || '';
            document.getElementById('editServiceName').value = data.name || '';
            document.getElementById('editServiceTag').value = data.tag || 'SIGNATURE SERVICE';
            document.getElementById('editServiceCategory').value = data.category || 'Grooming';
            document.getElementById('editServicePrice').value = data.price || '';
            document.getElementById('editServiceDuration').value = data.duration || '60 min';
            document.getElementById('editServiceSubtitle').value = data.subtitle || '';
            document.getElementById('editServiceHero').value = data.hero_image || '';
            document.getElementById('editSrvHeroPreview').src = data.hero_image || 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop';
            document.getElementById('editServiceDesc').value = data.description || '';

            document.getElementById('editServiceC1Title').value = data.card1_title || '';
            document.getElementById('editServiceC1Img').value = data.card1_img || '';
            document.getElementById('editSrvC1Preview').src = data.card1_img || 'assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg';
            document.getElementById('editServiceC1Desc').value = data.card1_desc || '';

            document.getElementById('editServiceC2Title').value = data.card2_title || '';
            document.getElementById('editServiceC2Img').value = data.card2_img || '';
            document.getElementById('editSrvC2Preview').src = data.card2_img || 'assets/images/homepage/total-grooming/2.jpg';
            document.getElementById('editServiceC2Desc').value = data.card2_desc || '';

            document.getElementById('editServiceC3Title').value = data.card3_title || '';
            document.getElementById('editServiceC3Img').value = data.card3_img || '';
            document.getElementById('editSrvC3Preview').src = data.card3_img || 'assets/images/homepage/total-grooming/3.jpg';
            document.getElementById('editServiceC3Desc').value = data.card3_desc || '';

            document.getElementById('editServicePkg').value = data.package_details || '';
            openModal('editServiceModal');
        }

        function openEditStylistModal(id, name, email, phone, spec, comm, schedule, status, image, bio) {
            document.getElementById('editStylistId').value = id;
            document.getElementById('editStylistName').value = name;
            document.getElementById('editStylistEmail').value = email;
            document.getElementById('editStylistPhone').value = phone;
            document.getElementById('editStylistSpec').value = spec;
            document.getElementById('editStylistComm').value = comm;
            document.getElementById('editStylistSchedule').value = schedule;
            document.getElementById('editStylistStatus').value = status;
            document.getElementById('editStylistImage').value = image || '';
            document.getElementById('editStylistBio').value = bio || '';
            openModal('editStylistModal');
        }

        function openEditApptModal(id, name, phone, email, service, stylist, date, time, chair, price, payMode, payStatus, status) {
            document.getElementById('editApptId').value = id;
            document.getElementById('editApptName').value = name;
            document.getElementById('editApptPhone').value = phone;
            document.getElementById('editApptEmail').value = email || '';
            document.getElementById('editApptService').value = service;
            document.getElementById('editApptStylist').value = stylist;
            document.getElementById('editApptDate').value = date;
            document.getElementById('editApptTime').value = time;
            document.getElementById('editApptChair').value = chair;
            document.getElementById('editApptPrice').value = price;
            document.getElementById('editApptPayMode').value = payMode || 'Cash';
            document.getElementById('editApptPayStatus').value = payStatus || 'Unpaid';
            document.getElementById('editApptStatus').value = status;
            openModal('editApptModal');
        }

        function updateBookPrice(selectEl) {
            var selectedOpt = selectEl.options[selectEl.selectedIndex];
            var price = selectedOpt.getAttribute('data-price');
            if (price && document.getElementById('bPriceInput')) {
                document.getElementById('bPriceInput').value = price;
            }
        }

        function calcAddMargin() {
            var c = parseFloat(document.getElementById('addCostInput').value) || 0;
            var r = parseFloat(document.getElementById('addRetailInput').value) || 0;
            var profit = Math.max(0, r - c);
            var pct = c > 0 ? Math.round((profit / c) * 100) : 0;
            var el = document.getElementById('addMarginInfo');
            if (el) {
                el.innerHTML = '💰 Estimated Profit: <strong>Rs. ' + profit.toLocaleString() + ' / unit</strong> (+' + pct + '% Markup Margin)';
            }
        }

        function calcEditMargin() {
            var c = parseFloat(document.getElementById('editStockCost').value) || 0;
            var r = parseFloat(document.getElementById('editStockRetail').value) || 0;
            var profit = Math.max(0, r - c);
            var pct = c > 0 ? Math.round((profit / c) * 100) : 0;
            var el = document.getElementById('editMarginInfo');
            if (el) {
                el.innerHTML = '💰 Estimated Profit: <strong>Rs. ' + profit.toLocaleString() + ' / unit</strong> (+' + pct + '% Markup Margin)';
            }
        }

        function openEditStockModal(id, code, name, brand, image, category, quantity, min, cost, retail, unit, supplier) {
            document.getElementById('editStockId').value = id;
            document.getElementById('editStockCode').value = code;
            document.getElementById('editStockName').value = name;
            document.getElementById('editStockBrand').value = brand || 'Elegance Reserve';
            document.getElementById('editStockImage').value = image || '';
            document.getElementById('editStockImgPreview').src = image || 'https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=400&auto=format&fit=crop';
            document.getElementById('editStockCategory').value = category;
            document.getElementById('editStockUnit').value = unit || 'Pieces';
            document.getElementById('editStockQty').value = quantity;
            document.getElementById('editStockMin').value = min;
            document.getElementById('editStockCost').value = cost;
            document.getElementById('editStockRetail').value = retail || (cost * 1.6);
            document.getElementById('editStockSupplier').value = supplier || '';
            calcEditMargin();
            openModal('editStockModal');
        }

        function openQuickRestockModal(id, name) {
            document.getElementById('restockItemId').value = id;
            document.getElementById('restockItemName').textContent = name;
            openModal('quickRestockModal');
        }

        /* Client-side Live Stock Search & Category Filtering */
        var currentStockCat = 'all';
        function filterStockCategory(cat, btn) {
            currentStockCat = cat;
            document.querySelectorAll('.stock-cat-btn').forEach(function(b) { b.classList.remove('active'); });
            if (btn) btn.classList.add('active');
            filterStockTable();
        }

        function filterStockTable() {
            var search = (document.getElementById('stockFilterSearch') ? document.getElementById('stockFilterSearch').value : '').toLowerCase().trim();
            var rows = document.querySelectorAll('.stock-row');
            rows.forEach(function(row) {
                var rowCat = row.getAttribute('data-category');
                var rowStatus = row.getAttribute('data-status');
                var searchData = row.getAttribute('data-search') || '';

                var matchCat = (currentStockCat === 'all') ||
                               (currentStockCat === 'critical' && (rowStatus === 'low' || rowStatus === 'out')) ||
                               (rowCat === currentStockCat);

                var matchSearch = !search || (searchData.indexOf(search) !== -1);

                if (matchCat && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        /* Client-side Live Appointments Filtering */
        function filterApptTable() {
            var search = (document.getElementById('apptSearch') ? document.getElementById('apptSearch').value : '').toLowerCase().trim();
            var filter = (document.getElementById('apptFilter') ? document.getElementById('apptFilter').value : 'all');
            var rows = document.querySelectorAll('.appt-row');
            rows.forEach(function(row) {
                var rowStatus = row.getAttribute('data-status');
                var searchData = row.getAttribute('data-search') || '';

                var matchStatus = (filter === 'all') || (rowStatus === filter);
                var matchSearch = !search || (searchData.indexOf(search) !== -1);

                if (matchStatus && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        /* Client-side Live Stylist Filtering */
        function filterStylistTable() {
            var search = (document.getElementById('stylistSearch') ? document.getElementById('stylistSearch').value : '').toLowerCase().trim();
            var rows = document.querySelectorAll('.stylist-row');
            rows.forEach(function(row) {
                var searchData = row.getAttribute('data-search') || '';
                if (!search || searchData.indexOf(search) !== -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        var activeViewServiceData = null;
        function openViewServiceFromBtn(btn) {
            try {
                var raw = btn.getAttribute('data-service');
                var s = JSON.parse(raw);
                activeViewServiceData = s;
                document.getElementById('viewServiceName').textContent = s.name || 'Service Details';
                document.getElementById('viewServiceTag').textContent = s.tag || 'SIGNATURE SERVICE';
                document.getElementById('viewServiceCategory').textContent = s.category || 'Grooming';
                document.getElementById('viewServiceSubtitle').textContent = s.subtitle || '';
                document.getElementById('viewServicePrice').textContent = 'Rs. ' + Number(s.price || 0).toLocaleString();
                document.getElementById('viewServiceDuration').textContent = '/ ' + (s.duration || '60 min');
                document.getElementById('viewServiceDesc').textContent = s.description || 'No description provided.';
                document.getElementById('viewServiceHeroImg').src = s.hero_image || 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop';

                document.getElementById('viewServiceC1Title').textContent = s.card1_title || 'Step 1';
                document.getElementById('viewServiceC1Desc').textContent = s.card1_desc || '';
                document.getElementById('viewServiceC1Img').src = s.card1_img || 'assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg';

                document.getElementById('viewServiceC2Title').textContent = s.card2_title || 'Step 2';
                document.getElementById('viewServiceC2Desc').textContent = s.card2_desc || '';
                document.getElementById('viewServiceC2Img').src = s.card2_img || 'assets/images/homepage/total-grooming/2.jpg';

                document.getElementById('viewServiceC3Title').textContent = s.card3_title || 'Step 3';
                document.getElementById('viewServiceC3Desc').textContent = s.card3_desc || '';
                document.getElementById('viewServiceC3Img').src = s.card3_img || 'assets/images/homepage/total-grooming/3.jpg';

                document.getElementById('viewServicePkg').textContent = s.package_details || 'Standard Luxury Elegance Salon Grooming Package.';

                var publicLink = '../userside/service.php';
                if (s.name && s.name.toLowerCase().indexOf('beard') !== -1) publicLink = '../userside/beard-design.php';
                else if (s.name && s.name.toLowerCase().indexOf('facial') !== -1) publicLink = '../userside/deep-clean-facial.php';
                else if (s.name && s.name.toLowerCase().indexOf('total') !== -1) publicLink = '../userside/total-grooming.php';
                else if (s.name && s.name.toLowerCase().indexOf('traditional') !== -1) publicLink = '../userside/traditional-design.php';
                else if (s.name && s.name.toLowerCase().indexOf('signature') !== -1) publicLink = '../userside/signature-cut-beard.php';
                document.getElementById('viewServicePublicLink').href = publicLink;

                document.getElementById('viewServiceEditBtn').onclick = function() {
                    closeModal('viewServiceModal');
                    openEditServiceModal(activeViewServiceData);
                };

                openModal('viewServiceModal');
            } catch(e) {
                console.error("Error opening view service modal:", e);
            }
        }

        var activeViewStylistData = null;
        function openViewStylistFromBtn(btn) {
            try {
                var raw = btn.getAttribute('data-stylist');
                var st = JSON.parse(raw);
                activeViewStylistData = st;
                document.getElementById('viewStylistName').textContent = st.name || 'Stylist Profile';
                document.getElementById('viewStylistSpec').textContent = st.specialization || 'Professional Barber';
                document.getElementById('viewStylistStatus').textContent = st.status || 'Active';
                document.getElementById('viewStylistStatus').className = 'dash-badge ' + (st.status === 'Active' ? 'green' : (st.status === 'On Leave' ? 'orange' : 'red'));
                document.getElementById('viewStylistPhone').textContent = '📞 ' + (st.phone || '—');
                document.getElementById('viewStylistEmail').textContent = '✉️ ' + (st.email || '—');
                document.getElementById('viewStylistComm').textContent = (st.commission_rate || 18) + '%';
                document.getElementById('viewStylistSchedule').textContent = st.work_schedule || 'Tue - Sun (11:00 AM - 09:00 PM)';
                document.getElementById('viewStylistBio').textContent = st.bio || 'Master craftsman with rich experience in precision cuts, beard sculpts, and executive grooming.';
                document.getElementById('viewStylistPhoto').src = st.image || 'assets/images/homepage/stylist/1.jpg';

                document.getElementById('viewStylistPublicLink').href = '../userside/stylist-detail.php?id=' + (st.id || 1);

                document.getElementById('viewStylistEditBtn').onclick = function() {
                    closeModal('viewStylistModal');
                    openEditStylistModal(st.id, st.name, st.email || '', st.phone || '', st.specialization || '', st.commission_rate || 18, st.work_schedule || '', st.status || 'Active', st.image || '', st.bio || '');
                };

                openModal('viewStylistModal');
            } catch(e) {
                console.error("Error opening view stylist modal:", e);
            }
        }

        function openEditServiceFromBtn(btn) {
            try {
                var raw = btn.getAttribute('data-service');
                var data = JSON.parse(raw);
                openEditServiceModal(data);
            } catch(e) {
                console.error("Error opening edit service modal:", e);
            }
        }

        function openEditStylistFromBtn(btn) {
            try {
                var raw = btn.getAttribute('data-stylist');
                var st = JSON.parse(raw);
                openEditStylistModal(st.id, st.name, st.email || '', st.phone || '', st.specialization || '', st.commission_rate || 18, st.work_schedule || '', st.status || 'Active', st.image || '', st.bio || '');
            } catch(e) {
                console.error("Error parsing stylist:", e);
            }
        }

        function openEditStockFromBtn(btn) {
            try {
                var raw = btn.getAttribute('data-stock');
                var inv = JSON.parse(raw);
                openEditStockModal(inv.id, inv.code, inv.name, inv.brand || 'Elegance Reserve', inv.image || '', inv.category, inv.quantity, inv.min_threshold, inv.cost_price, inv.retail_price, inv.unit || 'Pieces', inv.supplier_name || '');
            } catch(e) {
                console.error("Error parsing stock item:", e);
            }
        }

        // Universal Form Submission Loading Feedback
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '⏳ Saving...';
                }
            });
        });

        // Render Real Dynamic Charts from Database
        window.addEventListener('DOMContentLoaded', function() {
            // 1. Revenue Bar Chart
            var revCanvas = document.getElementById('revenueChart');
            if (revCanvas) {
                var revCtx = revCanvas.getContext('2d');
                new Chart(revCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($rev_chart_labels); ?>,
                        datasets: [{
                            label: 'Revenue (Rs.)',
                            data: <?php echo json_encode($rev_chart_data); ?>,
                            backgroundColor: 'rgba(212,175,55,0.75)',
                            borderColor: 'rgba(212,175,55,1)',
                            borderWidth: 1.5,
                            borderRadius: 8,
                            barPercentage: 0.55
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#9ca3af', font: { family: 'DM Sans', size: 11.5 } } },
                            y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#9ca3af', callback: function(v) { return 'Rs. ' + Number(v).toLocaleString(); } } }
                        }
                    }
                });
            }

            // 2. Service Doughnut Chart
            var pieCanvas = document.getElementById('servicePieChart');
            if (pieCanvas) {
                var pieCtx = pieCanvas.getContext('2d');
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($pie_services_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($pie_services_counts); ?>,
                            backgroundColor: ['#d4af37', '#05CD99', '#3b82f6', '#f59e0b', '#ec4899'],
                            borderColor: '#111318',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#9ca3af', font: { family: 'DM Sans', size: 11 }, padding: 12, usePointStyle: true }
                            }
                        }
                    }
                });
            }

            // 3. Peak Hours Chart
            var peakCanvas = document.getElementById('peakHoursChart');
            if (peakCanvas) {
                var peakCtx = peakCanvas.getContext('2d');
                new Chart(peakCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($peak_hours_labels); ?>,
                        datasets: [{
                            label: 'Appointments',
                            data: <?php echo json_encode($peak_hours_counts); ?>,
                            backgroundColor: 'rgba(5,205,153,0.75)',
                            borderColor: '#05CD99',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#9ca3af', font: { family: 'DM Sans', size: 11.5 } } },
                            y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#9ca3af', stepSize: 1 } }
                        }
                    }
                });
            }
        });

        function printReceiptSlip(ref, client, phone, service, stylist, token, price, payment, date, time) {
            var el = document.getElementById('receiptContent');
            if (!el) return;

            var numPrice = Number(price) || 0;
            var formattedPrice = numPrice.toLocaleString('en-PK');
            var displayToken = token ? token : 'TK-' + (ref ? ref.replace('ELG-', '') : '01');
            var displayDate = date || '<?php echo date("Y-m-d"); ?>';
            var displayTime = time || '<?php echo date("h:i A"); ?>';

            el.innerHTML = `
                <div style="text-align:center;">
                    <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;color:#555 !important;">✦ SALON SALES SLIP ✦</div>
                    <h3 style="margin:2px 0;font-size:19px;font-weight:900;letter-spacing:2px;color:#000 !important;">ELEGANCE SALON</h3>
                    <div style="font-size:10.5px;font-weight:700;color:#222 !important;letter-spacing:0.5px;">LUXURY MEN'S LOUNGE KARACHI</div>
                    <div style="font-size:9.5px;color:#555 !important;margin-top:1px;">Main Tariq Road, Block 2 PECHS &bull; Tel: 0300-1234567</div>
                </div>

                <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

                <!-- Token Highlight Box -->
                <div class="receipt-token-badge" style="margin:6px auto;padding:5px 14px;font-size:15px;">
                    TOKEN: ${displayToken}
                </div>

                <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

                <!-- Client & Appointment Meta Table -->
                <table style="width:100%;border-collapse:collapse;font-size:11.5px;line-height:1.5;">
                    <tr><td style="color:#555;padding:1px 0;">Invoice No:</td><td style="text-align:right;font-weight:700;color:#000;padding:1px 0;">${ref || 'ELG-0000'}</td></tr>
                    <tr><td style="color:#555;padding:1px 0;">Date / Time:</td><td style="text-align:right;color:#000;padding:1px 0;">${displayDate} &bull; ${displayTime}</td></tr>
                    <tr><td style="color:#555;padding:1px 0;">Customer:</td><td style="text-align:right;font-weight:700;color:#000;padding:1px 0;">${client || 'Walk-in Client'}</td></tr>
                    <tr><td style="color:#555;padding:1px 0;">Phone:</td><td style="text-align:right;color:#000;padding:1px 0;">${phone || 'N/A'}</td></tr>
                    <tr><td style="color:#555;padding:1px 0;">Master Barber:</td><td style="text-align:right;font-weight:700;color:#000;padding:1px 0;">${stylist || 'Assigned Stylist'}</td></tr>
                </table>

                <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

                <!-- Treatment & Charges Breakdown -->
                <table style="width:100%;border-collapse:collapse;font-size:12px;margin:4px 0;">
                    <tr style="font-weight:700;">
                        <td style="text-align:left;color:#000;padding:2px 0;">${service || 'Hair & Beard Grooming'}</td>
                        <td style="text-align:right;color:#000;white-space:nowrap;padding:2px 0;">Rs. ${formattedPrice}</td>
                    </tr>
                    <tr style="font-size:10.5px;color:#666;">
                        <td style="text-align:left;padding:2px 0;">Salon GST / Tax (Included)</td>
                        <td style="text-align:right;white-space:nowrap;padding:2px 0;">Rs. 0.00</td>
                    </tr>
                </table>

                <div class="receipt-divider-double" style="margin:8px 0;"></div>

                <!-- Net Total -->
                <table style="width:100%;border-collapse:collapse;margin:4px 0;">
                    <tr style="font-size:15.5px;font-weight:900;">
                        <td style="color:#000;padding:3px 0;">NET TOTAL:</td>
                        <td style="text-align:right;color:#000;white-space:nowrap;padding:3px 0;">Rs. ${formattedPrice}</td>
                    </tr>
                    <tr style="font-size:11px;">
                        <td style="color:#555;padding:2px 0;">Payment Method:</td>
                        <td style="text-align:right;padding:2px 0;">
                            <strong style="color:#000;text-transform:uppercase;background:#e5e5e5;padding:1px 6px;border-radius:3px;">${payment || 'Cash'} (PAID)</strong>
                        </td>
                    </tr>
                </table>

                <div class="receipt-divider-dashed" style="margin:8px 0;"></div>

                <!-- Realistic POS Barcode Simulation -->
                <div class="receipt-barcode" style="height:28px;margin:8px auto 3px;">
                    <span style="width:2px;"></span><span style="width:1px;background:transparent;"></span>
                    <span style="width:3px;"></span><span style="width:2px;background:transparent;"></span>
                    <span style="width:1px;"></span><span style="width:1px;background:transparent;"></span>
                    <span style="width:4px;"></span><span style="width:2px;background:transparent;"></span>
                    <span style="width:2px;"></span><span style="width:1px;background:transparent;"></span>
                    <span style="width:3px;"></span><span style="width:3px;background:transparent;"></span>
                    <span style="width:1px;"></span><span style="width:2px;background:transparent;"></span>
                    <span style="width:4px;"></span><span style="width:1px;background:transparent;"></span>
                    <span style="width:2px;"></span><span style="width:2px;background:transparent;"></span>
                    <span style="width:3px;"></span><span style="width:1px;background:transparent;"></span>
                    <span style="width:1px;"></span><span style="width:2px;background:transparent;"></span>
                    <span style="width:4px;"></span><span style="width:1px;background:transparent;"></span>
                    <span style="width:2px;"></span>
                </div>
                <div style="text-align:center;font-size:9px;letter-spacing:2px;color:#555 !important;margin-bottom:6px;">
                    *${ref || 'ELG-0000'}*
                </div>

                <!-- Footer Details -->
                <div style="text-align:center;font-size:9.5px;line-height:1.4;color:#444 !important;margin-top:6px;">
                    <div style="font-weight:700;color:#111 !important;">THANK YOU FOR YOUR PATRONAGE!</div>
                    <div>Follow us on Instagram: @elegance.salon.pk</div>
                    <div style="font-style:italic;margin-top:2px;font-size:8.5px;color:#777 !important;">*** Computer Generated Sales Slip &bull; 80mm POS ***</div>
                </div>
            `;
            window.currentSlipRef = ref || 'SLIP';
            openModal('receiptModal');
        }

        function downloadReceiptPDF() {
            var source = document.getElementById('receiptContent');
            if (!source) return;
            var filename = 'Elegance_Receipt_' + (window.currentSlipRef || 'SLIP') + '.pdf';

            // Create temporary rendering container at 0,0 with high z-index (captured cleanly at 0,0, then removed)
            var tempDiv = document.createElement('div');
            tempDiv.id = 'tempPdfRenderBox';
            tempDiv.style.cssText = 'position:fixed;top:0;left:0;width:330px;background:#ffffff;color:#000000;margin:0;padding:14px 16px;z-index:999999;box-sizing:border-box;font-family:"Courier New",Courier,monospace;font-size:12px;line-height:1.45;box-shadow:none;border:none;';
            tempDiv.innerHTML = source.innerHTML;

            document.body.appendChild(tempDiv);

            setTimeout(function() {
                var hPx = tempDiv.scrollHeight || 480;
                var hMm = Math.ceil(hPx * 0.264583) + 6;

                var opt = {
                    margin: [0, 0, 0, 0],
                    filename: filename,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2.5,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        scrollY: 0,
                        scrollX: 0,
                        x: 0,
                        y: 0,
                        width: 330,
                        height: hPx
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: [80, Math.max(90, hMm)],
                        orientation: 'portrait'
                    },
                    pagebreak: { mode: ['avoid-all'] }
                };

                if (typeof html2pdf !== 'undefined') {
                    html2pdf().set(opt).from(tempDiv).save().then(function() {
                        if (tempDiv.parentNode) tempDiv.parentNode.removeChild(tempDiv);
                    }).catch(function(err) {
                        console.error('PDF error:', err);
                        if (tempDiv.parentNode) tempDiv.parentNode.removeChild(tempDiv);
                        printCleanReceipt();
                    });
                } else {
                    if (tempDiv.parentNode) tempDiv.parentNode.removeChild(tempDiv);
                    printCleanReceipt();
                }
            }, 80);
        }

        function printCleanReceipt() {
            var content = document.getElementById('receiptContent');
            if (!content) return;

            var printWin = window.open('', '_blank', 'width=460,height=680');
            if (!printWin) {
                window.print();
                return;
            }

            printWin.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Elegance_Receipt_${window.currentSlipRef || 'SLIP'}</title>
                    <style>
                        @page { size: 80mm auto; margin: 4mm; }
                        html, body {
                            margin: 0;
                            padding: 8px 12px;
                            background: #ffffff !important;
                            color: #000000 !important;
                            font-family: 'Courier New', Courier, monospace;
                            font-size: 12.5px;
                            line-height: 1.5;
                        }
                        * { color: #000000 !important; box-sizing: border-box; }
                        .thermal-receipt-paper {
                            width: 100%;
                            max-width: 80mm;
                            margin: 0 auto;
                            background: #fff;
                        }
                        .receipt-token-badge {
                            display: block;
                            border: 2px solid #000;
                            background: #000 !important;
                            color: #fff !important;
                            padding: 6px 14px;
                            font-size: 16px;
                            font-weight: 900;
                            text-align: center;
                            margin: 8px auto;
                            width: fit-content;
                        }
                        .receipt-token-badge * { color: #fff !important; }
                        .receipt-divider-dashed { border-top: 1.5px dashed #000; margin: 10px 0; }
                        .receipt-divider-double { border-top: 3px double #000; margin: 10px 0; }
                        .receipt-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
                        .receipt-row.total-row { font-size: 15px; font-weight: 900; }
                        .receipt-barcode { height: 32px; display: flex; justify-content: center; gap: 2px; margin: 10px 0 4px; }
                        .receipt-barcode span { display: inline-block; height: 100%; background: #000 !important; }
                    </style>
                </head>
                <body>
                    <div class="thermal-receipt-paper">
                        ${content.innerHTML}
                    </div>
                    <script>
                        window.onload = function() {
                            window.focus();
                            window.print();
                            setTimeout(function() { window.close(); }, 800);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWin.document.close();
        }
    </script>
</body>

</html>