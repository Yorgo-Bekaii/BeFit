<?php
session_start();
require_once '../auth/config.php';


if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header("Location: ../auth/signin.php");
    exit;
}

// Get user's orders
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History - BeFit</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .orders-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .orders-header {
            margin-bottom: 2rem;
        }
        
        .orders-title {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .orders-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .order-id {
            font-size: 1.2rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        .order-date {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
       .order-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
}
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .order-details {
            padding: 1.5rem;
        }
        
        .order-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .order-total {
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .order-items {
            border-top: 1px solid #e9ecef;
            padding-top: 1.5rem;
        }
        
        .items-title {
            font-size: 1rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .item-name {
            font-weight: 500;
        }
        
        .item-price {
            color: var(--gray);
        }
        
        .empty-orders {
            text-align: center;
            padding: 4rem 0;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .empty-message {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }
/* Add to the existing styles */
.status-pending {
    background: #FFEB3B;
    color: #FF6D00;
    box-shadow: 0 2px 5px rgba(255, 152, 0, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.status-completed {
    background: #4CAF50;
    color: white;
    box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);
}

.status-completed:hover {
    transform: scale(1.05);
}
.status-completed::after {
    content: "✓";
    margin-left: 5px;
    font-weight: bold;
}

/* Add a clock icon for pending status */
.status-pending::before {
    content: "⏳";
    margin-right: 5px;
}
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="orders-container">
        <div class="orders-header">
            <h1 class="orders-title">Your Order History</h1>
            <p class="orders-subtitle">View details of your past purchases</p>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="empty-message">You haven't placed any orders yet</h3>
                <a href="../ecommerce/shop.php" class="cta-button">
                    Start Shopping <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
    <div class="order-header">
        <div>
            <h3 class="order-id">Order #<?= $order['id'] ?></h3>
            <p class="order-date">Placed on <?= date('F j, Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <span class="order-status status-<?= $order['status'] ?>">
            <?php if ($order['status'] == 'pending'): ?>
                <span id="status-timer-<?= $order['id'] ?>"></span>
            <?php else: ?>
                <?= ucfirst($order['status']) ?>
            <?php endif; ?>
        </span>
    </div>
    
    <div class="order-details">
        <div class="order-summary">
            <div>
                <h4>Order Summary</h4>
            </div>
            <div class="order-total">
                Total: $<?= number_format($order['total'], 2) ?>
            </div>
        </div>
        
        <div class="order-items">
            <h5 class="items-title">Items Purchased</h5>
            <?php
            $stmt = $pdo->prepare("
                SELECT p.name, p.price, oi.quantity 
                FROM order_items oi
                JOIN products p ON p.id = oi.product_id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$order['id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item):
            ?>
                <div class="item">
                    <span class="item-name"><?= $item['name'] ?></span>
                    <span class="item-price">
                        $<?= number_format($item['price'], 2) ?> × <?= $item['quantity'] ?> = $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
            <?php endforeach; ?>
        <?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($orders as $order): ?>
        <?php if ($order['status'] == 'pending'): ?>
            // Calculate remaining time in seconds
            const orderTime<?= $order['id'] ?> = new Date('<?= $order['created_at'] ?>').getTime();
            const tenMinutes = 10 * 60 * 1000; // 10 minutes in milliseconds
            const endTime<?= $order['id'] ?> = orderTime<?= $order['id'] ?> + tenMinutes;
            
            function updateTimer<?= $order['id'] ?>() {
                const now = new Date().getTime();
                const distance = endTime<?= $order['id'] ?> - now;
                
                if (distance < 0) {
                    // Time is up, reload the page to show updated status
                    document.getElementById('status-timer-<?= $order['id'] ?>').innerHTML = 'Processing...';
                    setTimeout(() => location.reload(), 2000);
                    return;
                }
                
                // Calculate minutes and seconds
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Display the result
                document.getElementById('status-timer-<?= $order['id'] ?>').innerHTML = 
                    `Pending (${minutes}m ${seconds}s)`;
            }
            
            // Update timer immediately and then every second
            updateTimer<?= $order['id'] ?>();
            setInterval(updateTimer<?= $order['id'] ?>, 1000);
        <?php endif; ?>
    <?php endforeach; ?>
});
</script>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
