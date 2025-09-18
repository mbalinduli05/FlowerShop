<?php

session_start();
if(!isset($_SESSION['email'])){
    header("location: main.php");
    exit();
}

// Database connection
require_once 'config.php';

// Fetch user data
$email = $_SESSION['email'];
$user_query = $conn->query("SELECT * FROM users WHERE email = '$email'");
$user = $user_query->fetch_assoc();

// Fetch orders
$orders_query = $conn->query("SELECT * FROM orders WHERE user_id = ".$user['id']." ORDER BY order_date DESC LIMIT 5");
$orders = [];
if ($orders_query->num_rows > 0) {
    while($row = $orders_query->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Fetch wishlist items
$wishlist_query = $conn->query("SELECT p.* FROM products p 
                               JOIN wishlist w ON p.id = w.product_id 
                               WHERE w.user_id = ".$user['id']." LIMIT 12");
$wishlist = [];
if ($wishlist_query->num_rows > 0) {
    while($row = $wishlist_query->fetch_assoc()) {
        $wishlist[] = $row;
    }
}

// Calculate total spent
$total_spent_query = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE user_id = ".$user['id']);
$total_spent = $total_spent_query->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Lowie Flowers</title>
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
        .hamburger:hover{
            color: var(--accent-color);
            transform: scale(1.1);
            transition: all 0.3s ease;
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

        .widgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .widget {
            background: var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s;
        }

        .widget:hover {
            transform: translateY(-5px);
        }

        .widget-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .widget-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .widget-info p {
            color: var(--dashboard-dark);
        }

        /* Orders Table */
        .card {
            background: var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 20px;
            color: var(--dashboard-primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--dashboard-light);
        }

        th {
            background-color: var(--dashboard-light);
            color: var(--dashboard-primary);
        }

        tr:hover {
            background-color: rgba(74, 111, 40, 0.05);
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-delivered {
            background-color: rgba(40, 167, 69, 0.15);
            color: green;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.15);
            color: orange;
        }

        .status-canceled {
            background-color: rgba(220, 53, 69, 0.15);
            color: red;
        }

        /* Wishlist Items */
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .wishlist-item {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .wishlist-item:hover {
            transform: translateY(-5px);
        }

        .wishlist-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .wishlist-info {
            padding: 15px;
        }

        .wishlist-info h4 {
            margin-bottom: 5px;
            color: var(--dashboard-dark);
        }

        .wishlist-info .price {
            color: var(--dashboard-primary);
            font-weight: bold;
        }

        .wishlist-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .wishlist-btn {
            background: var(--dashboard-primary);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .wishlist-btn.remove {
            background: #dc3545;
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

            .widgets {
                grid-template-columns: 1fr;
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
            background-color: rgba(4, 5, 5, 0.1);
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

        @media (max-width: 480px) {
            .header-container {
                padding: 0.5rem;
            }

            .logo {
                font-size: 1.5rem;
            }
            
            .wishlist-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <marquee behavior="scroll" direction="left">🌷 20% OFF SALE COMING SOON - STAY TUNED! 🌷</marquee>
    </div>

    <!-- Header with Navigation -->
    <header>
        <div class="header-container">
            <div class="logo">LOWIE</div>
            <nav class="navbar">
                <button class="hamburger" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                    <ul class="nav-menu">
                    <li class="nav-item"><a href="home.php">Home</a></li>
                    <li class="nav-item"><a href="shop.php">Shop</a></li>
                    <li class="nav-item"><a href="#"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li class="nav-item"><a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="logout.php">Logout</a></li>
                </ul>
                </button>
                <ul class="nav-menu">
                <!--<li class="nav-item"><a href="signin.html"><i class="fas fa-user"></i>Sign in</a></li>
                    <li class="nav-item"><a href="login.html">Login</a></li>-->
                    <li class="nav-item"><a href="#">Contact</a></li>
                    <!--<li class="nav-item"><a href="#"><i class="fas fa-shopping-cart"></i> Cart</a></li>-->
                    <li class="nav-item"><a href="#">About</a></li>
                    <li class="nav-item"><a href="#">T&T</a></li>
                </ul>
            </nav>
               
            </nav>
        </div>
    </header>
   
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <h1><span class="brand-icon">🌸</span> My Dashboard</h1>
            </div>
            <ul class="menu">
                <li class="menu-item active" data-tab="overview">
                    <span class="menu-icon">📊</span>
                    <span>Overview</span>
                </li>
                <li class="menu-item" data-tab="orders">
                    <span class="menu-icon">📦</span>
                    <span>Orders</span>
                </li>
                <li class="menu-item" data-tab="wishlist">
                    <span class="menu-icon">❤️</span>
                    <span>Wishlist</span>
                </li>
                <li class="menu-item" data-tab="profile">
                    <span class="menu-icon">👤</span>
                    <span>Profile</span>
                </li>
                <li class="menu-item" data-tab="security">
                    <span class="menu-icon">🔒</span>
                    <span>Security</span>
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
                    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                    <p>Here's what's happening with your flower shop account today.</p>
                </div>
                <div class="user-info">
                    <div class="avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div>
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Overview Tab -->
            <div class="tab-content" id="overview-tab">
                <h1 class="dashboard-title"><span class="dashboard-icon">📊</span> Dashboard Overview</h1>
                
                <!-- Widgets -->
                <div class="widgets">
                    <div class="widget">
                        <div class="widget-icon" style="background-color: rgba(74, 111, 40, 0.15); color: var(--dashboard-primary);">
                            📦
                        </div>
                        <div class="widget-info">
                            <h3><?php echo count($orders); ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    
                    <div class="widget">
                        <div class="widget-icon" style="background-color: rgba(255, 182, 193, 0.2); color: #e91e63;">
                            ❤️
                        </div>
                        <div class="widget-info">
                            <h3><?php echo count($wishlist); ?></h3>
                            <p>Wishlisted Items</p>
                        </div>
                    </div>
                    
                    <div class="widget">
                        <div class="widget-icon" style="background-color: rgba(156, 39, 176, 0.15); color: var(--dashboard-accent);">
                            💰
                        </div>
                        <div class="widget-info">
                            <h3>R<?php echo number_format($total_spent, 2); ?></h3>
                            <p>Total Spent</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Orders</h2>
                        <button class="btn btn-primary" onclick="switchTab('orders')">View All</button>
                    </div>
                    <?php if (count($orders) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                <td><?php echo $order['items_count']; ?> items</td>
                                <td>R<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No orders found. <a href="shop.php">Start shopping!</a></p>
                    <?php endif; ?>
                </div>
                
                <!-- Wishlist Preview -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Wishlist</h2>
                        <button class="btn btn-primary" onclick="switchTab('wishlist')">View All</button>
                    </div>
                    <?php if (count($wishlist) > 0): ?>
                    <div class="wishlist-grid">
                        <?php foreach (array_slice($wishlist, 0, 4) as $item): ?>
                        <div class="wishlist-item">
                            <img src="<?php echo $item['image_url'] ?? 'https://via.placeholder.com/200x150?text=Flower'; ?>" alt="<?php echo $item['name']; ?>">
                            <div class="wishlist-info">
                                <h4><?php echo $item['name']; ?></h4>
                                <p class="price">R<?php echo number_format($item['price'], 2); ?></p>
                                <div class="wishlist-actions">
                                    <button class="wishlist-btn">Add to Cart</button>
                                    <button class="wishlist-btn remove">Remove</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p>Your wishlist is empty. <a href="shop.php">Add some items!</a></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Other tabs would go here (orders, wishlist, profile, security) -->
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>Our Story</h3>
                <p>Lowie was founded by four best friends who shared a passion for growing beautiful flowers. What began as a hobby in our backyards blossomed into a business built on love and care for nature's beauty. Each of our products reflects the dedication we put into cultivating the finest flowers.</p>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
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
        
        <a href="#" class="bottom-nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Products</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <i class="fas fa-heart"></i>
            <span>Wishlist</span>
        </a>
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
        
        // Tab switching functionality
        function switchTab(tabName) {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked menu item
            document.querySelector(`.menu-item[data-tab="${tabName}"]`).classList.add('active');
            
            // Here you would typically load the tab content via AJAX or show/hide content
            alert(`Switching to ${tabName} tab. This would load ${tabName} content.`);
        }
        
        // Add event listeners to menu items
        document.querySelectorAll('.menu-item[data-tab]').forEach(item => {
            item.addEventListener('click', function() {
                switchTab(this.getAttribute('data-tab'));
            });
        });
        
        // Wishlist item removal
        document.querySelectorAll('.wishlist-btn.remove').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const item = this.closest('.wishlist-item');
                if (confirm('Remove this item from your wishlist?')) {
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.remove();
                        // Here you would make an AJAX call to update the database
                    }, 300);
                }
            });
        });
    </script>
</body>
</html>