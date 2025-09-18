<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: main.php");
    exit();
}

// Database connection
require_once 'config.php';

// Fetch admin data
$email = $_SESSION['email'];
$user_query = $conn->query("SELECT * FROM users WHERE email = '$email'");
$user = $user_query->fetch_assoc();

// Initialize messages
$success = '';
$error = '';

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new flower
    if (isset($_POST['add_flower'])) {
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
                $error = "Sorry, there was an error uploading your file.";
            }
        }
        
        $sql = "INSERT INTO products_dash (name, description, price, category, image_url, featured) 
                VALUES ('$name', '$description', '$price', '$category', '$image_url', '$featured')";
        
        if ($conn->query($sql)) {
            $success = "Flower added successfully!";
        } else {
            $error = "Error adding flower: " . $conn->error;
        }
    }
    
    // Update flower
    if (isset($_POST['update_flower'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = $conn->real_escape_string($_POST['price']);
        $category = $conn->real_escape_string($_POST['category']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        // Handle image update if new image is uploaded
        $image_sql = "";
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "uploads/";
            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $filename = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_sql = ", image_url = '$target_file'";
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
        
        $sql = "UPDATE products_dash SET 
                name = '$name', 
                description = '$description', 
                price = '$price', 
                category = '$category',
                featured = '$featured'
                $image_sql
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            $success = "Flower updated successfully!";
            // Clear edit_flower after successful update
            unset($edit_flower);
        } else {
            $error = "Error updating flower: " . $conn->error;
        }
    }
}

// Delete flower
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    
    // First, check if the flower exists
    $check_sql = "SELECT * FROM products_dash WHERE id = $id";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $sql = "DELETE FROM products_dash WHERE id = $id";
        
        if ($conn->query($sql)) {
            $success = "Flower deleted successfully!";
        } else {
            $error = "Error deleting flower: " . $conn->error;
        }
    } else {
        $error = "Flower not found!";
    }
}

// Fetch all flowers for management
$flowers_query = $conn->query("SELECT * FROM products_dash ORDER BY id DESC");
$flowers = [];
if ($flowers_query) {
    if ($flowers_query->num_rows > 0) {
        while($row = $flowers_query->fetch_assoc()) {
            $flowers[] = $row;
        }
    }
} else {
    $error = "Error fetching flowers: " . $conn->error;
}

// Fetch flower to edit
$edit_flower = null;
if (isset($_GET['edit'])) {
    $id = $conn->real_escape_string($_GET['edit']);
    $edit_query = $conn->query("SELECT * FROM products_dash WHERE id = $id");
    if ($edit_query && $edit_query->num_rows > 0) {
        $edit_flower = $edit_query->fetch_assoc();
    } else {
        $error = "Flower not found for editing!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lowie Flowers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #f8f1ff;
            --secondary-color: #8D769A;
            --accent-color: #46315C;
            --text-color: #3b346c;
            --special-tag: #BA96C1;
            --announcement-color: #fff0f5;
            --dashboard-primary: #63286f;
            --dashboard-secondary: #ffb6c1;
            --dashboard-accent: #9c27b0;
            --dashboard-light: #f8f9fa;
            --dashboard-dark: #343a40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Announcement Bar */
        .announcement-bar {
            background-color: var(--announcement-color);
            color: var(--text-color);
            padding: 0.1rem;
            text-align: center;
            font-weight: bold;
        }

        .announcement-bar marquee {
            font-size: 0.9rem;
        }

        /* Header Styles */
        header {
            padding: 1rem 2rem;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent-color);
        }

        /* Navigation Styles */
        .navbar {
            display: flex;
            align-items: center;
        }

        .nav-menu {
            display: flex;
            list-style: none;
        }

        .nav-item {
            margin-left: 1.5rem;
        }

        .nav-item a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-item a:hover {
            color:#cab1eb;
            transform:translate(-2px);
            transition: all 0.3s ease;
        }

        .hamburger {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--accent-color);
            cursor: pointer;
            display: none;
        }

        /* Dashboard Container */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 120px);
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, var(--dashboard-primary), #4d1e4dff);
            color: white;
            padding: 20px 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-right: 20px;
        }

        .brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .brand h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            font-size: 28px;
            color: var(--dashboard-secondary);
        }

        .menu {
            list-style: none;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--dashboard-secondary);
        }

        .menu-icon {
            font-size: 20px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--dashboard-light);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--dashboard-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }

        .welcome h2 {
            font-size: 24px;
            color: var(--dashboard-dark);
        }

        .welcome p {
            color: var(--dashboard-dark);
        }

        /* Dashboard Widgets */
        .dashboard-title {
            margin-bottom: 20px;
            color: var(--dashboard-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-icon {
            font-size: 28px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--dashboard-primary);
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--dashboard-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--dashboard-accent);
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #bd2130;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .action-btn {
            padding: 5px 10px;
            font-size: 14px;
        }
        
        .flower-form {
            background-color: var(--primary-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .flower-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .flower-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .flower-card:hover {
            transform: translateY(-5px);
        }
        
        .flower-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .flower-info {
            padding: 15px;
        }
        
        .flower-info h4 {
            margin-bottom: 5px;
            color: var(--dashboard-dark);
        }
        
        .flower-info .price {
            color: var(--dashboard-primary);
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .flower-info .description {
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .flower-info .category {
            display: inline-block;
            background-color: var(--special-tag);
            color: white;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* Table Styles for List View */
        .flowers-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .flowers-table th, 
        .flowers-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .flowers-table th {
            background-color: var(--primary-color);
            color: var(--dashboard-primary);
            font-weight: bold;
        }
        
        .flowers-table tr:hover {
            background-color: rgba(157, 39, 176, 0.05);
        }
        
        .view-toggle {
            display: flex;
            margin-bottom: 20px;
            background: var(--primary-color);
            border-radius: 5px;
            overflow: hidden;
            width: fit-content;
        }
        
        .view-toggle button {
            padding: 8px 15px;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: bold;
        }
        
        .view-toggle button.active {
            background-color: var(--dashboard-primary);
            color: white;
        }

        /* Footer Styles */
        footer {
            background-color: var(--text-color);
            color: white;
            padding: 3rem 2rem 1rem;
            margin-top: 40px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-about h3, .footer-links h3, .footer-contact h3 {
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .footer-about p {
            margin-bottom: 1rem;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
        }

        .footer-contact p {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-icons a {
            color: white;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: var(--secondary-color);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px;
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .brand {
                padding: 10px;
            }
            
            .menu {
                display: flex;
                overflow-x: auto;
                gap: 5px;
            }
            
            .menu-item {
                padding: 10px 15px;
                border-left: none;
                border-bottom: 4px solid transparent;
            }
            
            .menu-item:hover, .menu-item.active {
                border-left: none;
                border-bottom: 4px solid var(--dashboard-secondary);
            }
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 80px;
                right: 2rem;
                background-color: white;
                width: 200px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                flex-direction: column;
                padding: 1rem 0;
            }

            .nav-menu.active {
                display: flex;
            }

            .nav-item {
                margin: 0;
                padding: 0.8rem 1.5rem;
                border-bottom: 1px solid var(--secondary-color);
            }

            .nav-item:last-child {
                border-bottom: none;
            }

            .hamburger {
                display: block;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                width: 100%;
                justify-content: space-between;
            }
            
            .flower-grid {
                grid-template-columns: 1fr;
            }
            
            .flowers-table {
                display: block;
                overflow-x: auto;
            }
        }
         /* Floating Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 90%;
            max-width: 400px;
            background: white;
            border-radius: 30px;
            padding: 12px 20px;
            box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--text-color);
            font-size: 0.7rem;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 15px;
        }

        .bottom-nav-item i {
            font-size: 1.2rem;
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .bottom-nav-item.active {
            color: white;
            background: linear-gradient(45deg, var(--gradient-start), var(--gradient-end));
        }

        .bottom-nav-item:hover {
            color: var(--new-accent);
            background-color: rgba(78, 205, 196, 0.1);
        }

        .bottom-nav-item:hover i {
            transform: translateY(-3px);
        }

        /* For mobile devices, hide text and show only icons */
        @media (max-width: 768px) {
            .bottom-nav-item span {
                display: none;
            }
            
            .bottom-nav-item i {
                margin-bottom: 0;
                font-size: 1.4rem;
            }
            
            .bottom-nav {
                padding: 15px 20px;
            }
        }

        /* For very small screens, adjust the navigation */
        @media (max-width: 480px) {
            .bottom-nav {
                width: 95%;
                bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <marquee behavior="scroll" direction="left">🌷 ADMIN DASHBOARD - Manage Your Flower Inventory 🌷</marquee>
    </div>

    <!-- Header with Navigation -->
    <header>
        <div class="header-container">
            <div class="logo">LOWIE ADMIN</div>
            <nav class="navbar">
                <button class="hamburger" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <ul class="nav-menu">
                    <li class="nav-item"><a href="home.php">Home</a></li>
                    <li class="nav-item"><a href="shop.php">Shop</a></li>
            
                    <li class="nav-item"><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
   
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <h1><span class="brand-icon">🌸</span> Admin Panel</h1>
            </div>
            <ul class="menu">
                <li class="menu-item active" data-tab="manage-flowers">
                    <span class="menu-icon">🌺</span>
                    <span>Manage Flowers</span>
                </li>
                <li class="menu-item" data-tab="orders">
                    <span class="menu-icon">📦</span>
                    <span>Orders</span>
                </li>
                <li class="menu-item" data-tab="users">
                    <span class="menu-icon">👥</span>
                    <span>Users</span>
                </li>
                <li class="menu-item" onclick="window.location.href='logout.php'">
                    <span class="menu-icon">🚪</span>
                    <span>Logout</span>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome">
                    <h2>Welcome, Admin <?php echo htmlspecialchars($user['name']); ?>!</h2>
                    <p>Manage your flower inventory and store settings.</p>
                </div>
                <div class="user-info">
                    <div class="avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div>
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p>Administrator</p>
                    </div>
                </div>
            </div>
            
            <!-- Display success/error messages -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Manage Flowers Tab -->
            <div class="tab-content" id="manage-flowers-tab">
                <h1 class="dashboard-title"><span class="dashboard-icon">🌺</span> 
                    <?php echo $edit_flower ? 'Edit Flower' : 'Add New Flower'; ?>
                </h1>
                
                <!-- Flower Form -->
                <div class="flower-form">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($edit_flower): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_flower['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="name">Flower Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $edit_flower ? $edit_flower['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $edit_flower ? $edit_flower['description'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Price (R)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                   value="<?php echo $edit_flower ? $edit_flower['price'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category" 
                                   value="<?php echo $edit_flower ? $edit_flower['category'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Flower Image</label>
                            <input type="file" class="form-control" id="image" name="image" 
                                <?php echo $edit_flower ? '' : 'required'; ?>>
                            <?php if ($edit_flower && $edit_flower['image_url']): ?>
                                <p>Current image: <a href="<?php echo $edit_flower['image_url']; ?>" target="_blank">View</a></p>
                            <?php endif; ?>
                        </div>
                        <!-- In the flower form section of admindash.php -->
<div class="form-group">
    <label for="featured">
        <input type="checkbox" id="featured" name="featured" value="1" 
            <?php echo ($edit_flower && $edit_flower['featured']) ? 'checked' : ''; ?>>
        Featured Product (shown on homepage)
    </label>
</div>
                        
                        <div class="form-group">
                            <?php if ($edit_flower): ?>
                                <button type="submit" name="update_flower" class="btn btn-primary">Update Flower</button>
                                <a href="admin_dashboard.php" class="btn btn-danger">Cancel</a>
                            <?php else: ?>
                                <button type="submit" name="add_flower" class="btn btn-primary">Add Flower</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <h2 class="dashboard-title">All Flowers</h2>
                
                <!-- View Toggle -->
                <div class="view-toggle">
                    <button class="active" onclick="toggleView('grid')"><i class="fas fa-th"></i> Grid View</button>
                    <button onclick="toggleView('list')"><i class="fas fa-list"></i> List View</button>
                </div>
                
                <!-- Flowers Grid View -->
                <div id="grid-view">
                    <div class="flower-grid">
                        <?php if (count($flowers) > 0): ?>
                            <?php foreach ($flowers as $flower): ?>
                            <div class="flower-card">
                                <img src="<?php echo $flower['image_url']; ?>" alt="<?php echo $flower['name']; ?>">
                                <div class="flower-info">
                                    <h4><?php echo $flower['name']; ?></h4>
                                    <span class="price">R<?php echo number_format($flower['price'], 2); ?></span>
                                    <div class="category"><?php echo $flower['category']; ?></div>
                                    <p class="description"><?php echo substr($flower['description'], 0, 100); ?>...</p>
                                    <div class="action-buttons">
                                        <a href="edit_flower.php?id=<?php echo $flower['id']; ?>" class="btn btn-primary action-btn">Edit</a>
                                        <a href="delete_flower.php?id=<?php echo $flower['id']; ?>" 
                                           class="btn btn-danger action-btn" 
                                           onclick="return confirm('Are you sure you want to delete this flower?')">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No flowers found. Add some flowers to get started!</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Flowers List View -->
                <div id="list-view" style="display: none;">
                    <table class="flowers-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($flowers) > 0): ?>
                                <?php foreach ($flowers as $flower): ?>
                                <tr>
                                    <td><img src="<?php echo $flower['image_url']; ?>" alt="<?php echo $flower['name']; ?>" style="width: 60px; height: 60px; object-fit: cover;"></td>
                                    <td><?php echo $flower['name']; ?></td>
                                    <td>R<?php echo number_format($flower['price'], 2); ?></td>
                                    <td><?php echo $flower['category']; ?></td>
                                    <td><?php echo substr($flower['description'], 0, 50); ?>...</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_flower.php?id=<?php echo $flower['id']; ?>" class="btn btn-primary action-btn">Edit</a>
                                            <a href="delete_flower.php?id=<?php echo $flower['id']; ?>" 
                                               class="btn btn-danger action-btn" 
                                               onclick="return confirm('Are you sure you want to delete this flower?')">Delete</a>
                                </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No flowers found. Add some flowers to get started!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>Our Story</h3>
                <p>Lowie was founded by four best friends who shared a passion for growing beautiful flowers. What began as a hobby in our backyards blossomed into a business built on love and care for nature's beauty.</p>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="shop.php">Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p><i class="fas fa-envelope"></i> info@lowieflowers.com</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 Lowie Flower Shop. All rights reserved.</p>
        </div>
    </footer>
    <nav class="bottom-nav">
    <a href="home.php" class="bottom-nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
<!-- In your bottom navigation -->
<a href="profile_redirect.php" class="bottom-nav-item">
    <i class="fas fa-user"></i>
    <span>Profile</span>

        </a>
    </nav>

    <script>
        // Mobile menu toggle
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });
        
        // View toggle functionality
        function toggleView(viewType) {
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            const buttons = document.querySelectorAll('.view-toggle button');
            
            if (viewType === 'grid') {
                gridView.style.display = 'block';
                listView.style.display = 'none';
                buttons[0].classList.add('active');
                buttons[1].classList.remove('active');
            } else {
                gridView.style.display = 'none';
                listView.style.display = 'block';
                buttons[0].classList.remove('active');
                buttons[1].classList.add('active');
            }
        }
        
        // Scroll to form when editing
        <?php if ($edit_flower): ?>
        window.addEventListener('load', function() {
            document.querySelector('.flower-form').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>