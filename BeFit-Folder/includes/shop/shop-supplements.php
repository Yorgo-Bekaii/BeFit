<?php
require_once __DIR__ . '/../../auth/config.php';

// Get supplement products
$stmt = $pdo->prepare("SELECT * FROM products WHERE category = 'supplement'");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="shop-section">
    <h2>Supplements</h2>
    <div class="shop-items">
        <?php foreach ($products as $product): ?>
            <div class="shop-item">
                <img src="/BeFit-Folder/public/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="item-image">
                <div class="item-name"><?= htmlspecialchars($product['name']) ?></div>
                <div class="price-container">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php
                        $discountedPrice = $product['price'] * 0.85;
                        ?>
                        <div class="original-price">$<?= number_format($product['price'], 2) ?></div>
                        <div class="discounted-price">$<?= number_format($discountedPrice, 2) ?> (15% OFF)</div>
                    <?php else: ?>
                        <div class="item-price">$<?= number_format($product['price'], 2) ?></div>
                        <div class="signin-notice">Sign in to unlock 15% discount</div>
                    <?php endif; ?>
                </div>
                <a href="/BeFit-Folder/ecommerce/cart.php?action=add&id=<?= $product['id'] ?>" class="buy-button">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>