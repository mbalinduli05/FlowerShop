<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';

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
// Initialize variables
$flower = null;
$success = '';
$error = '';

// Check if we're editing an existing flower
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM products_dash WHERE id = $id");
    
    if ($result && $result->num_rows > 0) {
        $flower = $result->fetch_assoc();
    } else {
        $error = "Flower not found!";
        header("Location: admin_dashboard.php?error=" . urlencode($error));
        exit();
    }
} else {
    $error = "No flower ID specified!";
    header("Location: admindash.php?error=" . urlencode($error));
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
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
        // Refresh flower data
        $result = $conn->query("SELECT * FROM products_dash WHERE id = $id");
        if ($result && $result->num_rows > 0) {
            $flower = $result->fetch_assoc();
        }
    } else {
        $error = "Error updating flower: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flower - Lowie Flowers Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #f8f1ff;
            --secondary-color: #8D769A;
            --accent-color: #46315C;
            --text-color: #3b346c;
            --special-tag: #BA96C1;
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
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--dashboard-light);
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent-color);
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
            text-decoration: none;
            display: inline-block;
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

        .flower-form {
            background-color: var(--primary-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .current-image {
            margin-top: 10px;
        }

        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .navigation {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-flower-tulip"></i> LOWIE ADMIN
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
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h1>Edit Flower</h1>
        
        <!-- Flower Form -->
        <div class="flower-form">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $flower['id']; ?>">
                
                <div class="form-group">
                    <label for="name">Flower Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($flower['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($flower['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (R)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" 
                           value="<?php echo $flower['price']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category" 
                           value="<?php echo htmlspecialchars($flower['category']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="featured">
                        <input type="checkbox" id="featured" name="featured" value="1" 
                            <?php echo ($flower['featured'] == 1) ? 'checked' : ''; ?>>
                        Featured Product (shown on homepage)
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="image">Flower Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <?php if (!empty($flower['image_url'])): ?>
                        <div class="current-image">
                            <p>Current image:</p>
                            <img src="<?php echo $flower['image_url']; ?>" alt="<?php echo htmlspecialchars($flower['name']); ?>">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Flower</button>
                    <a href="admindash.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>

        <div class="navigation">
            <a href="admindash.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <a href="delete_flower.php?id=<?php echo $flower['id']; ?>" 
               class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to delete this flower?')">
               <i class="fas fa-trash"></i> Delete Flower
            </a>
        </div>
    </div>

    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const description = document.getElementById('description').value.trim();
            const price = document.getElementById('price').value;
            const category = document.getElementById('category').value.trim();
            
            if (!name || !description || !price || !category) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }
            
            if (price <= 0) {
                e.preventDefault();
                alert('Price must be greater than 0');
                return false;
            }
        });
    </script>
</body>
</html>