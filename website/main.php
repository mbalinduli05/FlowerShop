<?php 
session_start();

$error=[
    'login' =>$_SESSION['login_error']??'',
    'register' => $_SESSION['register_error']??'',
];
$activeForm = $_SESSION['active_form']??'login';

session_unset();

function showError($error){
    return !empty($error) ? "<p class = 'error-message'>$error</p>" : '';

}

function isActiveForm($forName,$activeForm){
    return $forName === $activeForm? 'active' : '';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> lowie - Login & Registration</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <main class="auth-container">
        <div class="welcome-panel">
            <h2>Welcome to Lowie</h2>
            <p>Sign in to access your account or register to create a new one and enjoy our Premium Flower Shop.</p>
        </div>
        
        <div class="form-panel">
            <div class="form-tabs">
                <div class="form-tab active" onclick="showForm('login-form')">Login</div>
                <div class="form-tab" onclick="showForm('register-form')">Register</div>
            </div>
            
            <div id="login-form" class="form-box <?= isActiveForm('login',$activeForm);?>">
                <h1 class="auth-title">Login to Account</h1>
                <form action="login_register.php" method="post">
                    <div class="form-group">
                        <?=showError($error['login']);?>
                        <label for="login-email">Email</label>
                        <input type="email" name="email" id="login-email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" name="password" id="login-password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="login" class="auth-btn">Login</button>
                    <p class="auth-footer">
                        Don't have an account? <a href="#" onclick="showForm('register-form'); return false;" class="auth-link">Register here</a>
                    </p>
                </form>
            </div>
            
            <div id="register-form" class="form-box <?= isActiveForm('register',$activeForm);?>">
                <h1 class="auth-title">Create Account</h1>
                <form action="login_register.php" method="post">
                    <div class="form-group">
                        <?=showError($error['register']);?>
                        <label for="user-type">Account Type</label>
                        <select id="user-type" name="role" class="form-control" required>
                            <option value="">Select account type</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" name="conpassword" id="confirm-password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="register" class="auth-btn">Register</button>
                    <p class="auth-footer">
                        Already have an account? <a href="#" onclick="showForm('login-form'); return false;" class="auth-link">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <script src="main.js"></script>
</body>
</html>