<?php
session_start();
require_once '../auth/config.php';

// Get all products
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Handle cart actions
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = 0;
        }
        $_SESSION['cart'][$product_id]++;
        header("Location: shop.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shop - BeFit</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .shop-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .shop-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .shop-title {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .shop-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-name {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .product-price {
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .product-category {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #f0f7ff;
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        .add-to-cart {
            display: block;
            width: 100%;
            padding: 0.8rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }
        
        .add-to-cart:hover {
            background: var(--primary-dark);
        }
        
        .cart-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--primary);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 100;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="shop-container">
        <div class="shop-header">
            <h1 class="shop-title">Our Premium Products</h1>
            <p class="shop-subtitle">Quality fitness equipment and supplements for your journey</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="../public/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <span class="product-category"><?= ucfirst($product['category']) ?></span>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                        <a href="shop.php?action=add&id=<?= $product['id'] ?>" class="add-to-cart">
                            Add to Cart <i class="fas fa-cart-plus"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <a href="cart.php" class="cart-link">
        <i class="fas fa-shopping-cart"></i>
        <?php if (!empty($_SESSION['cart'])): ?>
            <span class="cart-count"><?= array_sum($_SESSION['cart']) ?></span>
        <?php endif; ?>
    </a>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>