<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - AI-Powered Fitness</title>
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (ALLOW_ANALYTICS): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= GA_TRACKING_ID ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= GA_TRACKING_ID ?>', { 
            anonymize_ip: true,
            cookie_domain: 'auto',
            cookie_flags: 'SameSite=None;Secure'
        });
    </script>
    <?php endif; ?>
</head>
<body class="shared-bg">
    <nav class="page-header">
        <div class="nav-container">
            <div class="logo-nav">
                <a href="/BeFit-Folder/index.php">
                    <img src="/BeFit-Folder/public/photos/logo1.png" alt="BeFit Logo" class="logo">
                </a>
            </div>
            
            <ul class="nav-links">
                <li><a href="/BeFit-Folder/index.php#shop-section">Shop</a></li>
                <li><a href="/BeFit-Folder/about.php">About</a></li>
                <li><a href="/BeFit-Folder/support.php">Support</a></li>
                <li><a href="/BeFit-Folder/workout_builder/index.php" <?= basename($_SERVER['PHP_SELF']) == 'workout_builder' ? 'class="active"' : '' ?>>Workout Builder</a></li>
                <li class="nav-icons">
                    <a href="/BeFit-Folder/ecommerce/cart.php" class="icon-link" title="Cart">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?= array_sum($_SESSION['cart']) ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/BeFit-Folder/ecommerce/orders.php" class="icon-link" title="Orders">
                        <i class="fas fa-history"></i>
                    </a>
                </li>
            </ul>
    
            <div class="nav-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-profile">
                    <div class="welcome-message">
                        <span class="welcome-text">Welcome back</span>
                        <span class="username"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    </div>
                    <a href="/BeFit-Folder/auth/logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            <?php else: ?>
                <a href="/BeFit-Folder/auth/signin.php" class="auth-btn login-btn">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </a>
                <a href="/BeFit-Folder/auth/signup.php" class="auth-btn signup-btn">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            <?php endif; ?>
        </div>
            </div>
        </div>
    </nav>
    
    <main>