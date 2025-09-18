<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
require_once 'config.php';

// Fetch products from database
$products = [];
try {
    $products_query = $conn->query("SELECT * FROM products_dash ORDER BY id DESC LIMIT 12");
    if ($products_query && $products_query->num_rows > 0) {
        while($row = $products_query->fetch_assoc()) {
            $products[] = $row;
        }
    }
} catch (Exception $e) {
    // Handle error or fallback to hardcoded products
    error_log("Database error: " . $e->getMessage());
    
    // Fallback products if database is empty or has errors
    $products = array(
        array(
            'id' => 1,
            'name' => 'Mixed flower bouquet',
            'price' => 599.99,
            'image_url' => 'mixed_flowers.JPG',
            'featured' => 1,
            'description' => 'Beautiful mixed flower bouquet'
        ),
        array(
            'id' => 2,
            'name' => 'White Tulips 6 set stems tall',
            'price' => 149.99,
            'image_url' => 'C:\xampp\htdocs\website\IMG_5613.JPG',
            'featured' => 0,
            'description' => 'Elegant white tulips'
        ),
        array(
            'id' => 3,
            'name' => 'Pink Tulips Bouquet',
            'price' => 299.99,
            'image_url' => 'pink_tulips.JPG',
            'featured' => 0,
            'description' => 'Beautiful pink tulips bouquet'
        ),
        array(
            'id' => 4,
            'name' => 'Red Roses single stem',
            'price' => 89.99,
            'image_url' => 'IMG_5617.JPG',
            'featured' => 0,
            'description' => 'Single stem red rose'
        ),
        array(
            'id' => 5,
            'name' => 'Mixed Roses Bouquet',
            'price' => 299.99,
            'image_url' => 'mixed_roses_bouquet.JPG',
            'featured' => 0,
            'description' => 'Mixed roses bouquet'
        ),
        array(
            'id' => 6,
            'name' => 'Mixed Tulips Bouquet',
            'price' => 299.99,
            'image_url' => 'mixed_tulips_bouquet.JPG',
            'featured' => 1,
            'description' => 'Mixed tulips bouquet'
        )
    );
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowie - Premium Flower Shop</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <marquee behavior="scroll" direction="left">🌷 20% OFF SALE COMING SOON - STAY TUNED! 🌷</marquee>
    </div>

    <!-- Header with Navigation -->
    <header>
        <div class="header-container">
            <div class="logo">
                <i class=" fas fa-flower-tulip"></i>LOWIE</div>
                 <div class="search-filter-container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search flowers...">
            <button id="searchBtn"><i class="fas fa-search"></i></button>
        </div>
        <div class="filter-box">
            <select id="priceFilter">
                <option value="">Filter by Price</option>
                <option value="0-100">Under R100</option>
                <option value="100-200">R100 - R200</option>
                <option value="200-500">R200 - R500</option>
                <option value="500">R500+</option>
            </select>
            <select id="typeFilter">
                <option value="">Filter by Type</option>
                <option value="tulips">Tulips</option>
                <option value="roses">Roses</option>
                <option value="bouquet">Bouquets</option>
                <option value="stems">Stems</option>
                <option value="special">Special</option>

            </select>
        </div>
    </div>

    
            <nav class="navbar">
                <button class="hamburger" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                    <ul class="nav-menu">
                    <li class="nav-item"><a href="#">Products</a></li>
                </button>
                <ul class="nav-menu">
                    
                    <!--<li class="nav-item"><a href="signin.html"><i class="fas fa-user"></i>Sign in</a></li>
                    <li class="nav-item"><a href="login.html">Login</a></li>-->
                    <li class="nav-item"><a href="contactus.html">Contact</a></li>
                    <!--<li class="nav-item"><a href="#"><i class="fas fa-shopping-cart"></i> Cart</a></li>-->
                    <li class="nav-item"><a href="aboutus.html">About</a></li>
                    <li class="nav-item"><a href="#">T&T</a></li>
                    
                </ul>
            </nav>
        </div>
        
    </header>
   
    <!-- Main Content -->
    <main>
        <section class="hero">
            <h1>Beautiful Flowers, Grown with Love</h1>
            <p>Premium quality flowers cultivated by passionate florists</p>
            <button class="cta-button">Shop Now</button>
        </section>

        

        <section class="products-section">
            <h2>Our Featured Products</h2>
            <div class="products-container">
                <div class="product-card featured">
                     <?php
                // Database connection
                require_once 'config.php';
                
                // Try to get products from database
                $db_products = [];
                $db_has_products = false;
                
                try {
                    // Check if products_dash table exists
                    $table_check = $conn->query("SELECT 1 FROM products_dash LIMIT 1");
                    
                    if ($table_check !== false) {
                        $products_query = $conn->query("SELECT * FROM products_dash ORDER BY id DESC LIMIT 12");
                        
                        if ($products_query && $products_query->num_rows > 0) {
                            while($row = $products_query->fetch_assoc()) {
                                $db_products[] = $row;
                            }
                            $db_has_products = true;
                        }
                    }
                } catch (Exception $e) {
                    // Table doesn't exist or other error, we'll use hardcoded products
                    $db_has_products = false;
                }
                
                // If we have database products, display them
                if ($db_has_products && count($db_products) > 0):
                    foreach ($db_products as $product): ?>
                        <div class="product-card">
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                            <div class="product-info">
                                <h3><?php echo $product['name']; ?></h3>
                                <p class="price">R <?php echo number_format($product['price'], 2); ?></p>
                                <button class="shop-btn">Add to cart</button>
                            </div>
                        </div>
                    <?php endforeach;
                else:
                    // Define hardcoded products as fallback
                    $products = array(
                        array(
                            'name' => 'Mixed flower bouquet',
                            'price' => 599.99,
                            'image_url' => 'mixed_flowers.JPG',
                            'featured' => true
                        ),
                        array(
                            'name' => 'White Tulips 6 set stems tall',
                            'price' => 149.99,
                            'image_url' => 'IMG_5613.JPG',
                            'featured' => false
                        ),
                        // Add other products here...
                    );
                    
                    if (count($products) > 0):
                        foreach ($products as $product): ?>
                            <div class="product-card <?php echo $product['featured'] ? 'featured' : ''; ?>">
                                <?php if ($product['featured']): ?>
                                    <div class="product-tag">Special</div>
                                <?php endif; ?>
                                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                                <div class="product-info">
                                    <h3><?php echo $product['name']; ?></h3>
                                    <p class="price">R <?php echo number_format($product['price'], 2); ?></p>
                                    <button class="shop-btn">Add to cart</button>
                                </div>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <!-- Fallback to hardcoded products if database is empty -->
                        <div class="product-card featured">
                            <div class="product-tag">Special</div>
                            <img src="mixed_flowers.JPG" alt="Pink Tulips bouquet">
                            <div class="product-info" id="1">
                                <h3>Mixed flower bouquet</h3>
                                <p class="price">R 599.99</p>
                                <button class="shop-btn">Shop now</button>
                            </div>
                        </div>

                        <div class="product-card">
                            <img src="C:\xampp\htdocs\website\IMG_5613.JPG" alt="White Tulips">
                            <div class="product-info">
                                <h3>White Tulips 6 set stems tall</h3>
                                <p class="price">R 149.99</p>
                                <button class="shop-btn">Add to cart</button>
                            </div>
                        </div>

                        <div class="product-card">
                            <img src="pink_tulips.JPG" alt="Pink Tulips Bouquet">
                            <div class="product-info">
                                <h3>Pink Tulips Bouquet</h3>
                                <p class="price">R 299.99</p>
                                <button class="shop-btn">Add to cart</button>
                            </div>
                        </div>
            
                        <div class="product-card">
                            <img src="image\IMG_5613.JPG" alt="Red Roses single stem">
                            <div class="product-info">
                                <h3>Red Roses single stem</h3>
                                <p class="price">R 89.99</p>
                                <button class="shop-btn">Add to cart</button>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <img src="mixed_roses_bouquet.JPG" alt="Mixed Roses Bouquet">
                            <div class="product-info">
                                <h3>Mixed Roses Bouquet</h3>
                                <p class="price">R 299.99</p>
                                <button class="shop-btn">Add to cart</button>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <img src="mixed_tulips_bouquet.JPG" alt="Mixed Tulips">
                            <div class="product-info">
                                <h3>Mixed Tulips Bouquet</h3>
                                <p class="price">R 299.99</p>
                                <button class="shop-btn">Add to cart</button>
                            </div>
                        </div>
                    <?php endif;
                endif; ?>
            </div>
        </section>
    </main>

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
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Products</a></li>
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
    
    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav">
        <a href="home.php" class="bottom-nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
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
        <a href="javascript:history.back()" class="bottom-nav-item">
    <i class="fas fa-arrow-left"></i>
    <span>Back</span>
</a>
    </nav>

    <script src="home.js"></script>
</body>
</html>