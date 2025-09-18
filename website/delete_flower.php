<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: main.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    
    // First, check if the flower exists
    $check_sql = "SELECT * FROM products_dash WHERE id = $id";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $sql = "DELETE FROM products_dash WHERE id = $id";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Flower deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting flower: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Flower not found!";
    }
    
    header("Location: admindash.php");
    exit();
} else {
    header("Location: admindash.php");
    exit();
}
?>