<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Check if products_dash table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'products_dash'");
    
    if ($table_check && $table_check->num_rows > 0) {
        // Fetch all products from products_dash table
        $products_query = $conn->query("SELECT * FROM products_dash ORDER BY id DESC");
        
        if ($products_query && $products_query->num_rows > 0) {
            $products = [];
            while($row = $products_query->fetch_assoc()) {
                $products[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'products' => $products
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No products found in database'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Products table does not exist'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>