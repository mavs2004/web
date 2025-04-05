<?php
require_once 'config.php';
$page_title = 'Enroll Now';
require_once 'header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=enroll.php');
    exit();
}
?>
<style>
    /* Enrollment Page Styles */
    body{
        background-color: black;
        font-family: Arial, sans-serif;
    }
.enroll-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

.enroll-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 40px;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.payment-method {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method:hover {
    border-color: #e43c5c;
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(228, 60, 92, 0.1);
}

.payment-logo {
    width: 80px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
}

.payment-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.payment-info h4 {
    color: #333;
    margin-bottom: 5px;
}

.payment-info p {
    color: #777;
    margin: 0;
}

.note-box {
    background-color: #f8f9fa;
    border-left: 4px solid #e43c5c;
    padding: 20px;
    border-radius: 0 8px 8px 0;
}

.note-box h4 {
    color: #e43c5c;
    margin-bottom: 15px;
}

.note-box h4 i {
    margin-right: 10px;
}

.map-link {
    display: inline-flex;
    align-items: center;
    color: #e43c5c;
    text-decoration: none;
}

.map-link i {
    margin-right: 8px;
}

.map-link:hover {
    text-decoration: underline;
}

.map-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

/* Loading transition (same as index/service) */
.page-transition {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.page-transition.active {
    opacity: 1;
    pointer-events: all;
}

.loader {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #e43c5c;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<!-- Loading Transition -->
<div class="page-transition">
    <div class="loader"></div>
</div>

<!-- Enrollment Section -->
<section class="enroll-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="enroll-card">
                    <h2 class="text-center mb-4">Choose Payment Method</h2>
                    <p class="text-center mb-5">Please select your preferred payment method to complete your enrollment</p>
                    
                    <div class="payment-methods">
                        <!-- GCash Payment -->
                        <a href="pyment2.php" class="payment-method" onclick="return processPayment(event, 'gcash')">
                            <div class="payment-logo">
                                <img src="img/payment/gcash.png" alt="GCash" class="img-fluid">
                            </div>
                            <div class="payment-info">
                                <h4>GCash</h4>
                                <p>Philippines' leading mobile wallet</p>
                            </div>
                        </a>
                        
                        <!-- PayPal Payment -->
                        <a href="https://www.paypal.com" class="payment-method" onclick="return processPayment(event, 'paypal')">
                            <div class="payment-logo">
                                <img src="img/payment/paypal.png" alt="PayPal" class="img-fluid">
                            </div>
                            <div class="payment-info">
                                <h4>PayPal</h4>
                                <p>International online payments</p>
                            </div>
                        </a>
                        
                        <!-- Visa Payment -->
                        <a href="https://www.visa.com" class="payment-method" onclick="return processPayment(event, 'visa')">
                            <div class="payment-logo">
                                <img src="img/payment/visa.png" alt="Visa" class="img-fluid">
                            </div>
                            <div class="payment-info">
                                <h4>Visa</h4>
                                <p>Credit/Debit Card payments</p>
                            </div>
                        </a>
                    </div>
                    
                    <div class="note-box mt-5">
                        <h4><i class="fas fa-info-circle"></i> Important Note</h4>
                        <p>For in-person enrollment and personal training purchases, please visit our gym location:</p>
                        <div class="gym-location mt-3">
                            <a href="https://maps.app.goo.gl/z2kpHNTSQZjsRvFF8" target="_blank" class="map-link">
                                <i class="fas fa-map-marker-alt"></i> 96 Kalayaan B, Quezon City, Metro Manila
                            </a>
                            <div class="map-container mt-3">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3713.647294826133!2d121.0869707748709!3d14.691143374853912!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bb7c17c50677%3A0xbad12298cf5d2019!2sARMANDO&#39;S%20GYM!5e1!3m2!1sen!2sph!4v1743489544265!5m2!1sen!2sph" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Process payment selection
function processPayment(event, method) {
    event.preventDefault();
    
    // Show loading transition
    document.querySelector('.page-transition').classList.add('active');
    
    // Simulate processing delay
    setTimeout(() => {
        // Redirect to respective payment gateway
        switch(method) {
            case 'gcash':
                window.location.href = 'pyment2.php';
                break;
            case 'paypal':
                window.location.href = 'https://www.paypal.com';
                break;
            case 'visa':
                window.location.href = 'https://www.visa.com';
                break;
        }
    }, 500);
    
    // Prevent default anchor behavior
    return false;
}

// Hide loading transition when page loads
window.addEventListener('load', function() {
    document.querySelector('.page-transition').classList.remove('active');
});

// Also hide loading if user returns via back button
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        document.querySelector('.page-transition').classList.remove('active');
    }
});
</script>

<?php  ?>