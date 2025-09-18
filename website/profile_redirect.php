<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Ensure no output has been sent before header redirect
    if (!headers_sent()) {
        header("Location: main.php");
        exit();
    } else {
        echo "Headers already sent. Cannot redirect.";
        // You may want to use JavaScript redirect as fallback
        echo "&lt;script>window.location.href = 'main.php';&lt;/script>";
    }
}

// Redirect based on user role
if ($_SESSION['role'] == 'admin') {
    if (!headers_sent()) {
        header("Location: admindash.php");
        exit();
    } else {
        echo "&lt;script>window.location.href = 'admindash.php';&lt;/script>";
    }
} else {
    if (!headers_sent()) {
        header("Location: userdash.php");
        exit();
    } else {
        echo "&lt;script>window.location.href = 'userdash.php';&lt;/script>";
    }
}
?>

       