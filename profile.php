<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Get user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    // Get user enrollments with payment status
    $enrollment_stmt = $conn->prepare("SELECT 
                                        e.*, 
                                        c.class_name, 
                                        c.duration_months,
                                        c.price,
                                        p.status AS payment_status
                                     FROM enrollments e
                                     JOIN classes c ON e.class_id = c.class_id
                                     JOIN payments p ON e.payment_id = p.payment_id
                                     WHERE e.user_id = :user_id
                                     ORDER BY e.enrollment_date DESC");
    $enrollment_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $enrollment_stmt->execute();
    $enrollments = $enrollment_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - GymLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="stylee.css" type="text/css">
    <style>
        h5{
            
        }
        h1 {
            color: white;
            font-family:"times-new-roman";
            text-align: center;
        }
       
        body {
            background-color:rgb(3, 3, 3);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px 0;
        }
        
        .profile-section, .enrollments-section {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }
        
        .membership-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .membership-active {
            border-left: 5px solid #28a745;
        }
        
        .membership-pending {
            border-left: 5px solid #ffc107;
        }
        
        .membership-expired {
            border-left: 5px solid #dc3545;
        }
        
        .badge-active {
            background-color: #28a745;
        }
        
        .badge-pending {
            background-color: #ffc107;
        }
        
        .badge-expired {
            background-color: #6c757d;
        }
        
        .payment-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .days-remaining {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="./index.html">
                            <img src="img/ARMANDO12.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="nav-menu">
                        <ul>
                            <li class="active"><a href="./index.php">Home</a></li>
                            <li><a href="./about-us.php">About Us</a></li>
                            <li><a href="./class-details.php">Classes</a></li>
                            <li><a href="./services.php">Services</a></li>
                            <li><a href="./team.php">Our team</a></li>
                            <li><a href="#">Pages</a>
                                <ul class="dropdown">
                                    <li><a href="./about-us.php">About us</a></li>
                                    <li><a href="./class-timetable.php">Classes timetable</a></li>
                                    <li><a href="./bmi-calculator.php">Bmi calculate</a></li>
                                    <li><a href="./team.php">Our team</a></li>
                                    <li><a href="./gallery.php">Gallery</a></li>
                                </ul>
                            </li>
                            <li><a href="./contact.php">Contact</a></li>
                            <?php if(!isset($_SESSION['user_id'])): ?>
                                <li><a href="register.php" class="secondary-btn">Register</a></li>
                            <?php else: ?>
                                <li><a href="logout.php" class="secondary-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="top-option">
                        <div class="to-search search-switch">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="to-social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-youtube-play"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="canvas-open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                   <h2>
                    .
                   </h2>
                   <h2>
                    .
                   </h2>
                   <h2>
                    .
                </h2>
                <h2>.</h2>
                    <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
                    
                    <div class="profile-section">
                        <h2 class="mb-4"><i class="fas fa-user-circle me-2"></i>My Information</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
                                <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="enrollments-section">
                        <h2 class="mb-4"><i class="fas fa-dumbbell me-2"></i>My Memberships</h2>
                        <?php if (!empty($enrollments)): ?>
                            <div class="row">
                                <?php foreach ($enrollments as $enrollment): 
                                    $is_active = $enrollment['status'] == 1 && strtotime($enrollment['expiry_date']) > time();
                                    $is_pending = $enrollment['payment_status'] === 'pending';
                                    $days_remaining = floor((strtotime($enrollment['expiry_date']) - time()) / (60 * 60 * 24));
                                ?>
                                <div class="col-md-6">
                                    <div class="membership-card <?php 
                                        echo $is_pending ? 'membership-pending' : 
                                            ($is_active ? 'membership-active' : 'membership-expired'); 
                                    ?>">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h3><?php echo htmlspecialchars($enrollment['class_name']); ?></h3>
                                            <span class="badge <?php 
                                                echo $is_pending ? 'badge-pending' : 
                                                    ($is_active ? 'badge-active' : 'badge-expired'); 
                                            ?>">
                                                <?php echo $is_pending ? 'Pending' : 
                                                    ($is_active ? 'Active' : 'Expired'); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div><strong>Duration:</strong> <?php echo $enrollment['duration_months']; ?> months</div>
                                            <div><strong>Price:</strong> ₱<?php echo number_format($enrollment['price'], 2); ?></div>
                                            <div><strong>Enrolled:</strong> <?php echo date('F j, Y', strtotime($enrollment['enrollment_date'])); ?></div>
                                            <div><strong>Expires:</strong> <?php echo date('F j, Y', strtotime($enrollment['expiry_date'])); ?></div>
                                            <?php if ($is_active): ?>
                                                <div><strong>Days remaining:</strong> <span class="days-remaining"><?php echo $days_remaining > 0 ? $days_remaining : 'Last day'; ?></span></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="payment-details">
                                            <h5><i class="fas fa-receipt me-2"></i>Payment Status</h5>
                                            <span class="badge bg-<?php 
                                                echo $enrollment['payment_status'] === 'completed' ? 'success' : 
                                                    ($enrollment['payment_status'] === 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($enrollment['payment_status']); ?>
                                            </span>
                                            <?php if ($enrollment['payment_status'] === 'pending'): ?>
                                                <p class="mt-2 mb-0 text-muted">Your membership will activate once payment is verified</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                You are not enrolled in any memberships yet.
                            </div>
                            <a href="index.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Purchase Membership</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <section class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="fs-about">
                        <div class="fa-logo">
                            <a href="#"><img src="img/ARMANDO12.png" alt=""></a>
                        </div>
                        <p>“I hated every minute of training, but I said, 'Don't quit. Suffer now and live the rest of your life as a champion.” – Muhammad Ali</p>
                        <div class="fa-social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-youtube-play"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                            <a href="#"><i class="fa  fa-envelope-o"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="fs-widget">
                        <h4>Useful links</h4>
                        <ul>
                            <li><a href="#">About</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Classes</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="fs-widget">
                        <h4>Support</h4>
                        <ul>
                            <li><a href="#">Login</a></li>
                            <li><a href="#">My account</a></li>
                            <li><a href="#">Subscribe</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="fs-widget">
                        <h4>Tips & Guides</h4>
                        <div class="fw-recent">
                            <h6><a href="#">Physical fitness may help prevent depression, anxiety</a></h6>
                            <ul>
                                <li>3 min read</li>
                                <li>20 Comment</li>
                            </ul>
                        </div>
                        <div class="fw-recent">
                            <h6><a href="#">Fitness: The best exercise to lose belly fat and tone up...</a></h6>
                            <ul>
                                <li>3 min read</li>
                                <li>20 Comment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="copyright-text">
                        <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This Website is made with <i class="fa fa-heart" aria-hidden="true"></i></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>