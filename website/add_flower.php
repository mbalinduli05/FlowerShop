<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: main.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_flower'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle image upload
    $image_url = 'https://via.placeholder.com/300x200?text=Flower';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    
    $sql = "INSERT INTO products_dash (name, description, price, category, image_url, featured) 
            VALUES ('$name', '$description', '$price', '$category', '$image_url', '$featured')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Flower added successfully!";
    } else {
        $_SESSION['error'] = "Error adding flower: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>v<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: main.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_flower'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle image upload
    $image_url = 'https://via.placeholder.com/300x200?text=Flower';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    
    $sql = "INSERT INTO products_dash (name, description, price, category, image_url, featured) 
            VALUES ('$name', '$description', '$price', '$category', '$image_url', '$featured')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Flower added successfully!";
    } else {
        $_SESSION['error'] = "Error adding flower: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>