<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to send email notification
function sendPaymentVerificationEmail($user_id, $payment_id, $reference_number, $amount) {
    // Get admin email from config or set directly
    $admin_email = "admin@gymlife.com"; // Replace with your admin email
    
    // Get user details
    global $conn;
    $stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Email subject
    $subject = "New Payment Verification Required - GymLife";

    // Email message
    $message = "
    <html>
    <head>
        <title>Payment Verification Required</title>
    </head>
    <body>
        <h2>New Payment Requires Verification</h2>
        <p>A new payment has been submitted and requires verification:</p>
        <ul>
            <li>Payment ID: {$payment_id}</li>
            <li>User: {$user['full_name']} (ID: {$user_id})</li>
            <li>Reference Number: {$reference_number}</li>
            <li>Amount: ₱" . number_format($amount, 2) . "</li>
        </ul>
        <p>Please verify this payment in the admin dashboard.</p>
    </body>
    </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: GymLife System <noreply@gymlife.com>" . "\r\n";
    
    // Send email
    return mail($admin_email, $subject, $message, $headers);
}

$user_id = $_SESSION['user_id'];
$class_id = 3; // 9-month membership

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    // Validate inputs
    if (empty($_POST['reference_number'])) {
        $_SESSION['error'] = "GCash reference number is required";
        header("Location: pyment3.php");
        exit();
    }

    $reference_number = trim($_POST['reference_number']);

    // Validate GCash reference number using the function from config.php
    if (!validateGCashReference($reference_number)) {
        $_SESSION['error'] = "Invalid GCash reference number format. It should be 13 digits.";
        header("Location: pyment3.php");
        exit();
    }

    try {
        // Get class details
        $stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
        $stmt->execute([$class_id]);
        $class = $stmt->fetch();
        
        if (!$class) {
            throw new Exception("Invalid class selected");
        }
        
        // Check if reference number already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM payments WHERE reference_number = ?");
        $stmt->execute([$reference_number]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("This GCash reference number has already been used");
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // 1. Record payment
        $stmt = $conn->prepare("INSERT INTO payments (user_id, class_id, amount, reference_number, status) 
                               VALUES (?, ?, ?, ?, 'pending')"); // Set to pending until verified
        $stmt->execute([$user_id, $class_id, $class['price'], $reference_number]);
        $payment_id = $conn->lastInsertId();
        
        // 2. Create enrollment (but set status to 0/inactive until payment is verified)
        $expiry_date = date('Y-m-d H:i:s', strtotime("+{$class['duration_months']} months"));
        
        $stmt = $conn->prepare("INSERT INTO enrollments (user_id, class_id, payment_id, expiry_date, status) 
                               VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$user_id, $class_id, $payment_id, $expiry_date]);
        
        $conn->commit();
        
        // Send email notification
        $emailSent = sendPaymentVerificationEmail($user_id, $payment_id, $reference_number, $class['price']);
        
        if (!$emailSent) {
            // Log the email failure but don't stop the process
            error_log("Failed to send payment verification email for payment ID: " . $payment_id);
        }
        
        $_SESSION['success'] = "Payment submitted for verification! We'll confirm your enrollment once we verify your GCash payment.";
        header("Location: profile.php");
        exit();
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: pyment3.php");
        exit();
    }
}

// Get class details
try {
    $stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();
    
    if (!$class) {
        throw new Exception("9-months membership class not found in database");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "System error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" sizes="32x32" href="img/ew.jpg" />
    <title>6-Month Membership Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stylee.css" type="text/css">
    <style>
      .alert {
          max-width: 300px;
          margin: 20px auto;
      }
      .gcash-info {
          background-color: #f8f9fa;
          border-radius: 5px;
          padding: 15px;
          margin-bottom: 20px;
      }
      .payment-instructions {
          margin-bottom: 20px;
      }
      .reference-number-input {
          font-size: 1.1em;
          letter-spacing: 1px;
      }
      .qr-code-container {
          text-align: center;
          margin-bottom: 20px;
      }
      .qr-code-container img {
          max-width: 200px;
          height: auto;
      }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container py-5">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">9-Months Membership Payment</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="qr-code-container">
                                    <img src="img/qr3.jpg" alt="GCash QR Code" class="img-fluid rounded shadow-sm" />
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="gcash-info">
                                    <h5>Payment Instructions:</h5>
                                    <ol>
                                        <li>Open your GCash app</li>
                                        <li>Scan the QR code or send payment to our GCash number</li>
                                        <li>Amount: ₱<?php echo isset($class) ? number_format($class['price'], 3) : '3000.00'; ?></li>
                                        <li>Save the 13-digit reference number from your transaction</li>
                                    </ol>
                                </div>
                                
                                <form method="post" class="payment-form" id="paymentForm">
                                    <input type="hidden" name="class_id" value="<?php echo isset($class) ? $class['class_id'] : 3; ?>">
                                    <div class="mb-3">
                                        <label for="reference_number" class="form-label">GCash Reference Number</label>
                                        <input type="text" 
                                               class="form-control reference-number-input" 
                                               id="reference_number" 
                                               name="reference_number" 
                                               pattern="[0-9]{13}" 
                                               title="Please enter a valid 13-digit GCash reference number"
                                               placeholder="Enter 13-digit reference number"
                                               required>
                                        <div class="form-text">Enter the 13-digit reference number from your GCash transaction</div>
                                    </div>
                                    <button type="submit" name="confirm_payment" class="btn btn-primary w-100">
                                        Submit Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const refNum = document.getElementById('reference_number').value;
            if (!/^\d{13}$/.test(refNum)) {
                alert('Please enter a valid 13-digit GCash reference number');
                e.preventDefault();
            }
        });

        // Format reference number input
        document.getElementById('reference_number').addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');
            
            // Limit to 13 digits
            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }
        });
    </script>
</body>
</html>