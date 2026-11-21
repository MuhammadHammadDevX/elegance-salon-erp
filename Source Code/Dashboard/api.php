<?php
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}
require_once __DIR__ . '/../config.php';

$response = ['success' => false, 'message' => 'Invalid action'];

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection offline', 'offline' => true]);
    exit;
}

switch ($action) {
    case 'get_stats':
        // Today's revenue
        $revRes = mysqli_query($conn, "SELECT SUM(price) as today_rev FROM `appointments` WHERE `appointment_date` = CURDATE() AND (`status` = 'Completed' OR `payment_status` = 'Paid')");
        $todayRev = ($revRes && $row = mysqli_fetch_assoc($revRes)) ? floatval($row['today_rev'] ?? 0) : 0;

        // Today's total appointments
        $apptRes = mysqli_query($conn, "SELECT COUNT(*) as total_today FROM `appointments` WHERE `appointment_date` = CURDATE()");
        $todayAppts = ($apptRes && $row = mysqli_fetch_assoc($apptRes)) ? intval($row['total_today'] ?? 0) : 0;

        // Active in chair
        $chairRes = mysqli_query($conn, "SELECT COUNT(*) as in_chair FROM `appointments` WHERE `appointment_date` = CURDATE() AND `status` = 'In Chair'");
        $inChair = ($chairRes && $row = mysqli_fetch_assoc($chairRes)) ? intval($row['in_chair'] ?? 0) : 0;

        // Low stock count
        $stockRes = mysqli_query($conn, "SELECT COUNT(*) as low_stock FROM `inventory` WHERE `quantity` <= `min_threshold`");
        $lowStock = ($stockRes && $row = mysqli_fetch_assoc($stockRes)) ? intval($row['low_stock'] ?? 0) : 0;

        // Total clients
        $clientRes = mysqli_query($conn, "SELECT COUNT(*) as total_clients FROM `user` WHERE `role` = 'client'");
        $totalClients = ($clientRes && $row = mysqli_fetch_assoc($clientRes)) ? intval($row['total_clients'] ?? 0) : 0;

        echo json_encode([
            'success' => true,
            'stats' => [
                'todayRevenue' => $todayRev,
                'todayAppointments' => $todayAppts,
                'inChair' => $inChair,
                'lowStockCount' => $lowStock,
                'totalClients' => $totalClients
            ]
        ]);
        exit;

    case 'get_appointments':
        $date = $_GET['date'] ?? date('Y-m-d');
        $q = "SELECT * FROM `appointments` ORDER BY `appointment_date` DESC, `id` DESC LIMIT 100";
        $res = mysqli_query($conn, $q);
        $appointments = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $appointments[] = $row;
            }
        }
        echo json_encode(['success' => true, 'appointments' => $appointments]);
        exit;

    case 'update_appointment_status':
        $id = intval($_POST['id'] ?? 0);
        $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Confirmed');
        $chair = intval($_POST['chair_no'] ?? 0);
        $paymentStatus = mysqli_real_escape_string($conn, $_POST['payment_status'] ?? 'Unpaid');

        if ($id > 0) {
            $q = "UPDATE `appointments` SET `status` = '$status', `chair_no` = $chair, `payment_status` = '$paymentStatus' WHERE `id` = $id";
            if (mysqli_query($conn, $q)) {
                echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment']);
        exit;

    case 'add_walkin':
    case 'add_appointment':
        $clientName = trim(mysqli_real_escape_string($conn, $_POST['client_name'] ?? 'Walk-In Guest'));
        $clientPhone = trim(mysqli_real_escape_string($conn, $_POST['client_phone'] ?? '03000000000'));
        $clientEmail = trim(mysqli_real_escape_string($conn, $_POST['client_email'] ?? 'walkin@elegance.pk'));
        $serviceName = mysqli_real_escape_string($conn, $_POST['service_name'] ?? 'Total Grooming');
        $stylistName = mysqli_real_escape_string($conn, $_POST['stylist_name'] ?? 'Zachary Nickell');
        $apptDate = mysqli_real_escape_string($conn, $_POST['appointment_date'] ?? date('Y-m-d'));
        $apptTime = mysqli_real_escape_string($conn, $_POST['appointment_time'] ?? date('h:i A'));
        $price = floatval($_POST['price'] ?? 5000);
        $paymentMode = mysqli_real_escape_string($conn, $_POST['payment_mode'] ?? 'Cash');
        $paymentStatus = mysqli_real_escape_string($conn, $_POST['payment_status'] ?? 'Paid');
        $chairNo = intval($_POST['chair_no'] ?? 1);
        $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'In Chair');

        $bookingRef = 'ELG-' . rand(1000, 9999);
        $token = sprintf('%02d', rand(10, 99));

        // Calculate commission (18% default)
        $commRate = 18.00;
        $commRes = mysqli_query($conn, "SELECT `commission_rate` FROM `staff` WHERE `name` LIKE '%$stylistName%' LIMIT 1");
        if ($commRes && $crow = mysqli_fetch_assoc($commRes)) {
            $commRate = floatval($crow['commission_rate']);
        }
        $commAmount = ($price * $commRate) / 100;

        $q = "INSERT INTO `appointments` (`booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_name`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`) 
              VALUES ('$bookingRef', '$token', '$clientName', '$clientPhone', '$clientEmail', '$serviceName', '$stylistName', '$apptDate', '$apptTime', $chairNo, $price, $commAmount, '$paymentMode', '$paymentStatus', '$status')";
        
        if (mysqli_query($conn, $q)) {
            $insertId = mysqli_insert_id($conn);
            
            // Create invoice if paid
            if ($paymentStatus === 'Paid') {
                $invNo = 'INV-' . rand(1000, 9999);
                @mysqli_query($conn, "INSERT INTO `invoices` (`invoice_no`, `appointment_id`, `client_name`, `client_phone`, `service_name`, `subtotal`, `discount`, `tax`, `final_amount`, `payment_mode`, `payment_status`, `cashier_name`, `invoice_date`) 
                                      VALUES ('$invNo', $insertId, '$clientName', '$clientPhone', '$serviceName', $price, 0, 0, $price, '$paymentMode', 'Paid', 'Front Desk', CURDATE())");
                
                // Record commission
                @mysqli_query($conn, "INSERT INTO `commissions` (`stylist_name`, `appointment_id`, `service_amount`, `commission_rate`, `commission_earned`, `payout_status`, `date`) 
                                      VALUES ('$stylistName', $insertId, $price, $commRate, $commAmount, 'Pending', CURDATE())");
            }

            echo json_encode([
                'success' => true,
                'message' => 'Booking created successfully',
                'booking' => [
                    'id' => $insertId,
                    'booking_ref' => $bookingRef,
                    'token' => $token,
                    'client_name' => $clientName,
                    'service_name' => $serviceName,
                    'stylist_name' => $stylistName,
                    'price' => $price,
                    'chair_no' => $chairNo,
                    'status' => $status
                ]
            ]);
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
        exit;

    case 'get_inventory':
        $res = mysqli_query($conn, "SELECT * FROM `inventory` ORDER BY `quantity` ASC");
        $items = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $items[] = $row;
            }
        }
        echo json_encode(['success' => true, 'inventory' => $items]);
        exit;

    case 'restock_inventory':
        $id = intval($_POST['id'] ?? 0);
        $qty = intval($_POST['quantity'] ?? 10);
        if ($id > 0 && $qty > 0) {
            $q = "UPDATE `inventory` SET `quantity` = `quantity` + $qty, `status` = 'In Stock', `last_restocked` = CURDATE() WHERE `id` = $id";
            if (mysqli_query($conn, $q)) {
                // Record purchase order
                $orderRef = 'PO-' . rand(1000, 9999);
                $pRes = mysqli_query($conn, "SELECT `name`, `supplier_name`, `cost_price` FROM `inventory` WHERE `id` = $id LIMIT 1");
                if ($pRes && $pRow = mysqli_fetch_assoc($pRes)) {
                    $pName = $pRow['name'];
                    $sName = $pRow['supplier_name'] ?? 'Supplier';
                    $unitCost = floatval($pRow['cost_price']);
                    $totalCost = $unitCost * $qty;
                    @mysqli_query($conn, "INSERT INTO `purchase_orders` (`order_ref`, `product_id`, `product_name`, `supplier_name`, `quantity`, `unit_cost`, `total_cost`, `order_date`, `status`) 
                                          VALUES ('$orderRef', $id, '$pName', '$sName', $qty, $unitCost, $totalCost, CURDATE(), 'Delivered')");
                }
                echo json_encode(['success' => true, 'message' => 'Stock restocked successfully']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Failed to restock']);
        exit;

    case 'get_users':
        $res = mysqli_query($conn, "SELECT `id`, `name`, `email`, `phone`, `role`, `created_at` FROM `user` ORDER BY `id` ASC");
        $users = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $users[] = $row;
            }
        }
        echo json_encode(['success' => true, 'users' => $users]);
        exit;

    case 'save_user':
        $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
        $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
        $role = mysqli_real_escape_string($conn, $_POST['role'] ?? 'client');
        $pass = $_POST['password'] ?? '123456';
        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        if (!empty($name) && !empty($email)) {
            $q = "INSERT INTO `user` (`name`, `email`, `phone`, `password`, `role`) VALUES ('$name', '$email', '$phone', '$hashed', '$role') 
                  ON DUPLICATE KEY UPDATE `name`='$name', `phone`='$phone', `role`='$role'";
            if (mysqli_query($conn, $q)) {
                echo json_encode(['success' => true, 'message' => 'User saved successfully']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Failed to save user']);
        exit;

    case 'get_clients':
        $res = mysqli_query($conn, "SELECT u.id, u.name, u.email, u.phone, u.created_at, COUNT(a.id) as total_visits, SUM(CASE WHEN a.status='Completed' THEN a.price ELSE 0 END) as total_spent FROM `user` u LEFT JOIN `appointments` a ON (u.email = a.client_email OR u.phone = a.client_phone) WHERE u.role = 'client' GROUP BY u.id ORDER BY total_spent DESC");
        $clients = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $clients[] = $row;
            }
        }
        echo json_encode(['success' => true, 'clients' => $clients]);
        exit;

    case 'get_staff':
        $res = mysqli_query($conn, "SELECT * FROM `staff` ORDER BY `id` ASC");
        $staff = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $staff[] = $row;
            }
        }
        echo json_encode(['success' => true, 'staff' => $staff]);
        exit;

    case 'get_services':
        $res = mysqli_query($conn, "SELECT * FROM `services` ORDER BY `id` ASC");
        $services = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $services[] = $row;
            }
        }
        echo json_encode(['success' => true, 'services' => $services]);
        exit;

    case 'get_suppliers':
        $res = mysqli_query($conn, "SELECT DISTINCT supplier_name as name, COUNT(*) as product_count FROM `inventory` WHERE supplier_name != '' GROUP BY supplier_name ORDER BY name ASC");
        $suppliers = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $suppliers[] = $row;
            }
        }
        echo json_encode(['success' => true, 'suppliers' => $suppliers]);
        exit;

    case 'get_commissions':
        $res = mysqli_query($conn, "SELECT id, booking_ref, stylist_name, client_name, service_name, price, commission_amount, appointment_date, status FROM `appointments` ORDER BY `appointment_date` DESC, `id` DESC");
        $commissions = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $commissions[] = $row;
            }
        }
        echo json_encode(['success' => true, 'commissions' => $commissions]);
        exit;

    case 'get_invoices':
        $res = mysqli_query($conn, "SELECT id, booking_ref, client_name, client_phone, service_name, price, payment_mode, payment_status, appointment_date FROM `appointments` WHERE `payment_status`='Paid' OR `status`='Completed' ORDER BY `appointment_date` DESC, `id` DESC");
        $invoices = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $invoices[] = $row;
            }
        }
        echo json_encode(['success' => true, 'invoices' => $invoices]);
        exit;

    case 'get_reviews':
        $res = mysqli_query($conn, "SELECT * FROM `reviews` ORDER BY `id` DESC");
        $reviews = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $reviews[] = $row;
            }
        }
        echo json_encode(['success' => true, 'reviews' => $reviews]);
        exit;

    case 'get_contact_messages':
        $res = mysqli_query($conn, "SELECT * FROM `contact_messages` ORDER BY `id` DESC");
        $messages = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $messages[] = $row;
            }
        }
        echo json_encode(['success' => true, 'messages' => $messages]);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
        exit;
}
?>
