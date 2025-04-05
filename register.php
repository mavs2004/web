<?php
require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone']),
        'username_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => '',
        'full_name_err' => ''
    ];

    // Validate username
    if(empty($data['username'])) {
        $data['username_err'] = 'Please enter username';
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $data['username']]);
        if($stmt->rowCount() > 0) {
            $data['username_err'] = 'Username is already taken';
        }
    }

    // Validate email
    if(empty($data['email'])) {
        $data['email_err'] = 'Please enter email';
    } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $data['email_err'] = 'Please enter a valid email';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $data['email']]);
        if($stmt->rowCount() > 0) {
            $data['email_err'] = 'Email is already registered';
        }
    }

    // Validate password
    if(empty($data['password'])) {
        $data['password_err'] = 'Please enter password';
    } elseif(strlen($data['password']) < 6) {
        $data['password_err'] = 'Password must be at least 6 characters';
    }

    // Validate confirm password
    if(empty($data['confirm_password'])) {
        $data['confirm_password_err'] = 'Please confirm password';
    } else {
        if($data['password'] != $data['confirm_password']) {
            $data['confirm_password_err'] = 'Passwords do not match';
        }
    }

    // Validate full name
    if(empty($data['full_name'])) {
        $data['full_name_err'] = 'Please enter your full name';
    }

    // Make sure errors are empty
    if(empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && 
       empty($data['confirm_password_err']) && empty($data['full_name_err'])) {
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (:username, :email, :password, :full_name, :phone)");
        if($stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone']
        ])) {
            flash('register_success', 'Registration successful. You can now login');
            header('location: login.php');
            exit();
        } else {
            die('Something went wrong');
        }
    }
}

$page_title = 'Register';
require_once 'header.php';
?>

<!-- Registration Form -->
 <style>
    body {
    overflow-x: hidden;
    position: relative;
}

.page-enter {
    position: absolute;
    top: 0;
    left: 100%;
    width: 100%;
    height: 100%;
    transition: transform 0.5s ease;
}

.page-enter-active {
    transform: translateX(-100%);
}

.page-exit {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: transform 0.5s ease;
}

.page-exit-active {
    transform: translateX(-100%);
}
.loading-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    background-color: #e43c5c;
    width: 0%;
    z-index: 9999;
    transition: width 0.3s ease;
}
 </style>
 <div class="loading-bar"></div>
<section class="register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="register-form">
                    <h2 class="text-center mb-4">Create Your Account</h2>
                    <?php flash('register_success'); ?>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>">
                            <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>">
                            <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($data['password']) ? $data['password'] : ''; ?>">
                            <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($data['confirm_password']) ? $data['confirm_password'] : ''; ?>">
                            <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" class="form-control <?php echo (!empty($data['full_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo isset($data['full_name']) ? $data['full_name'] : ''; ?>">
                            <span class="invalid-feedback"><?php echo $data['full_name_err']; ?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Phone (Optional)</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo isset($data['phone']) ? $data['phone'] : ''; ?>">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const loadingBar = document.querySelector('.loading-bar');
    const links = document.querySelectorAll('a');
    
    links.forEach(link => {
        link.addEventListener('click', function() {
            loadingBar.style.width = '50%';
        });
    });
    
    window.addEventListener('load', function() {
        loadingBar.style.width = '100%';
        setTimeout(() => {
            loadingBar.style.width = '0%';
        }, 300);
    });
});
</script>

<?php ?>