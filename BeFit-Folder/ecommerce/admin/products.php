<?php
require_once 'db.php';

// Create products table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255)
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image_url]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
    <style>
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 8px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 30px; }
        .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Management</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="image_url" required>
            </div>
            
            <button type="submit">Add Product</button>
        </form>
        
        <h2>Product List</h2>
        <div class="product-grid">
            <?php
            $stmt = $pdo->query("SELECT * FROM products");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='product-card'>";
                echo "<h3>{$row['name']}</h3>";
                echo "<p>{$row['description']}</p>";
                echo "<p>Price: $".number_format($row['price'], 2)."</p>";
                if ($row['image_url']) {
                    echo "<img src='{$row['image_url']}' alt='{$row['name']}' style='max-width: 100%;'>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>