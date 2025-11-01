<?php
session_start();
require_once '../auth/config.php';

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header("Location: ../auth/signin.php");
    exit;
}
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/signin.php?redirect=cart");
    exit;
}

// Handle cart actions
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
        header("Location: cart.php");
        exit;
    }
    elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        unset($_SESSION['cart'][$product_id]);
        header("Location: cart.php");
        exit;
    }
    elseif ($_GET['action'] == 'update' && isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        header("Location: cart.php");
        exit;
    }
    elseif ($_GET['action'] == 'checkout') {
        if (!empty($_SESSION['cart'])) {
            // Calculate total
            $total = 0;
            $product_ids = array_keys($_SESSION['cart']);
            
            if (!empty($product_ids)) {
                $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
                $stmt->execute($product_ids);
                $cart_products = $stmt->fetchAll();
                
                foreach ($cart_products as $product) {
                    $price = $product['price'];
                    if (isset($_SESSION['user_id'])) {
                        $price = $price * 0.85;
                    }
                    $total += $price * $_SESSION['cart'][$product['id']];
                }
                
                // Verify user exists first
                $userCheck = $pdo->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
                $userCheck->execute([$_SESSION['user_id']]);
                $validUser = $userCheck->fetch();

                if (!$validUser) {
                    // If user doesn't exist, log them out and redirect
                    session_destroy();
                    header("Location: ../auth/signin.php?error=invalid_user");
                    exit;
                }

                // Create order (only once - removed the duplicate insertion)
                try {
                    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status, created_at, status_updated_at) VALUES (?, ?, 'pending', NOW(), NOW())");
                    $stmt->execute([$_SESSION['user_id'], $total]);
                    $order_id = $pdo->lastInsertId();
                    
                    // Add order items
                    foreach ($_SESSION['cart'] as $product_id => $quantity) {
                        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                        $stmt->execute([$order_id, $product_id, $quantity]);
                    }
                    
                    // Clear cart
                    unset($_SESSION['cart']);
                    header("Location: orders.php");
                    exit;
                } catch (PDOException $e) {
                    // If error occurs, show simple message
                    die("Error during checkout. Please try again or contact support.");
                }
            }
        }
    }
}

// Get cart products
$cart_products = [];
$grand_total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $cart_products = $stmt->fetchAll();
    
    foreach ($cart_products as $product) {
        $price = $product['price'];
        if (isset($_SESSION['user_id'])) {
            $price = $price * 0.85;
        }
        $grand_total += $price * $_SESSION['cart'][$product['id']];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            font-family: 'Open Sans', sans-serif;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .cart-title {
            font-size: 2rem;
            color: var(--dark);
            font-weight: 600;
        }
        
        .continue-shopping {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        
        .cart-table th {
            text-align: left;
            padding: 1rem;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }
        
        .cart-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .product-name {
            font-weight: 500;
            color: var(--dark);
        }
        
        .product-price {
            font-weight: 500;
        }
        
        .discounted {
            color: #28a745;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }
        
        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            text-align: center;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .remove-item {
            color: #dc3545;
            text-decoration: none;
            font-size: 1.2rem;
        }
        
        .cart-summary {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        .summary-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            width: 350px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-weight: 400;
        }
        
        .summary-total {
            font-size: 1.2rem;
            font-weight: 500;
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: var(--primary-dark);
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 0;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .empty-cart-message {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }
        
        .update-cart-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="cart-header">
            <h1 class="cart-title">Your Shopping Cart</h1>
            <a href="../ecommerce/shop.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
        
        <?php if (!empty($cart_products)): ?>
            <form action="cart.php?action=update" method="post">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_products as $product): ?>
                            <?php
                            $price = $product['price'];
                            $is_discounted = isset($_SESSION['user_id']);
                            $display_price = $is_discounted ? $price * 0.85 : $price;
                            $subtotal = $display_price * $_SESSION['cart'][$product['id']];
                            ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <img src="../public/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                                        <span class="product-name"><?= htmlspecialchars($product['name']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($is_discounted): ?>
                                        <span class="original-price">$<?= number_format($price, 2) ?></span>
                                        <span class="product-price discounted">$<?= number_format($display_price, 2) ?></span>
                                    <?php else: ?>
                                        <span class="product-price">$<?= number_format($display_price, 2) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" name="quantity[<?= $product['id'] ?>]" value="<?= $_SESSION['cart'][$product['id']] ?>" min="1" class="quantity-input">
                                </td>
                                <td class="product-price">$<?= number_format($subtotal, 2) ?></td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?= $product['id'] ?>" class="remove-item" title="Remove item">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" name="update_cart" class="update-cart-btn">
                    <i class="fas fa-sync-alt"></i> Update Cart
                </button>
            </form>
            
            <div class="cart-summary">
                <div class="summary-card">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($grand_total, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>FREE</span>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="summary-row">
                        <span>Discount (15%):</span>
                        <span class="discounted">-$<?= number_format($grand_total / 0.85 - $grand_total, 2) ?></span>
                    </div>
                    <?php else: ?>
                    <div class="summary-row">
                        <span>Member Discount:</span>
                        <span><a href="../auth/signin.php">Login to save 15%</a></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span>$<?= number_format($grand_total, 2) ?></span>
                    </div>
                    
                    <a href="cart.php?action=checkout" class="checkout-btn">
                        Proceed to Checkout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="empty-cart-message">Your cart is empty</h3>
                <a href="../ecommerce/shop.php" class="cta-button">
                    Start Shopping <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
