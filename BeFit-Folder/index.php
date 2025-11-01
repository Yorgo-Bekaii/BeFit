<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle cart actions
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 0;
    }
    $_SESSION['cart'][$product_id]++;
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Load configuration
require_once __DIR__ . '/auth/config.php';

// Check login status
$loggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - AI-Powered Fitness</title>
    <!-- CSS -->
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --dark: #1A1A1A;
            --light: #F8F9FA;
            --gray: #6C757D;
            --border-radius: 12px;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--dark);
            line-height: 1.6;
            background-color: var(--light);
        }
        
        h1, h2, h3 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        
        /* Welcome Message */
        .welcome-message {
            text-align: center;
            margin: 4rem 0 3rem;
        }
        .welcome-message h1 {
            font-size: 2.8rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .username {
            color: var(--dark);
            font-weight: 800;
        }
        .welcome-message p {
            font-size: 1.2rem;
            color: var(--gray);
        }

        /* Dashboard Options */
        .dashboard-options {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin: 3rem 0;
            flex-wrap: wrap;
        }
        .dashboard-btn {
            background: var(--primary);
            color: white;
            padding: 1.2rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid transparent;
            min-width: 220px;
            justify-content: center;
        }
        .dashboard-btn:hover {
            background: white;
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        .dashboard-btn i {
            font-size: 1.3rem;
        }

        /* Hero Section */
        
        .hero-section {
        text-align: center;
        padding: 6rem 2rem;
        position: relative;
        background: transparent;
        }
        .hero-title {
            font-size: clamp(2.5rem, 7vw, 4rem);
            line-height: 1.1;
            color: var(--dark);
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            /* New styles you're adding: */
            font-weight: 900;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }
        .hero-subtitle {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto 3rem;
            line-height: 1.6;
            /* New styles you're adding: */
            font-weight: 300;
            letter-spacing: 0.02em;
        }
        .cta-button {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            border: 2px solid var(--primary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .cta-button:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-2px);
        }
        /* Benefits Section */
        .benefits-section {
            padding: 5rem 2rem;
            background: white;
        }
        .benefits-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .benefits-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 4rem;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        .benefit-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }
        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .benefit-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        .benefit-heading {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 700;
        }
        .benefit-card p {
            color: var(--gray);
        }

        /* Shop Section */
        .shop-section-container {
            max-width: 1200px;
            margin: 5rem auto;
            padding: 0 2rem;
        }
        .section-separator {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #E5E5E5, transparent);
            margin: 4rem 0;
        }
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 3rem;
            position: relative;
        }
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--primary);
            margin: 1.5rem auto 0;
            border-radius: 2px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.3rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .benefits-title, .section-title {
                font-size: 2rem;
            }
            .dashboard-options {
                flex-direction: column;
                align-items: center;
            }
            .dashboard-btn, .cta-button {
                width: 100%;
                max-width: 280px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <?php if($loggedIn): ?>
            <!-- Dashboard for logged in users -->
            <section class="dashboard-section">
                <div class="welcome-message">
                    <h1>Welcome back, <span class="username"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span></h1>
                    <p>Ready to crush your fitness goals today?</p>
                </div>
        <div class="dashboard-options">
            <a href="/BeFit-Folder/workout_builder/index.php" class="dashboard-btn">
                <i class="fas fa-dumbbell"></i> Build Workout
            </a>
            <a href="/BeFit-Folder/workout_builder/history.php" class="dashboard-btn">
                <i class="fas fa-chart-line"></i> Track Progress
            </a>
        </div>
            </section>
        <?php else: ?>
            <!-- Hero Section -->
            <section class="hero-section">
            <h1 class="hero-title">Transform Your Fitness with BeFit AI Precision</h1>
            <p class="hero-subtitle">
                Workouts Tailored to You—Powered by Goals, Level & Equipment. Strength Simplified, Supplements Curated.
            </p>
            <a href="/BeFit-Folder/auth/signup.php" class="cta-button">Get Started Now</a>
            </section>
            
            <!-- Benefits Section -->
            <section class="benefits-section">
                <div class="benefits-container">
                    <h2 class="benefits-title">Why Choose BeFit?</h2>
                    <div class="benefits-grid">
                        <div class="benefit-card">
                            <i class="fas fa-robot benefit-icon"></i>
                            <h3 class="benefit-heading">AI-Powered Workouts</h3>
                            <p>Smart algorithms create the perfect workout plan based on your goals, fitness level, and available equipment.</p>
                        </div>
                        <div class="benefit-card">
                            <i class="fas fa-chart-line benefit-icon"></i>
                            <h3 class="benefit-heading">Real-Time Tracking</h3>
                            <p>Monitor your progress with detailed analytics and get adaptive recommendations to maximize results.</p>
                        </div>
                        <div class="benefit-card">
                            <i class="fas fa-tags benefit-icon"></i>
                            <h3 class="benefit-heading">Exclusive Discounts</h3>
                            <p>Members get special pricing on premium supplements and fitness gear in our curated store.</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        
        <!-- Shop Section -->
        <div class="shop-section-container">
            <hr class="section-separator">
            <h2 class="section-title">Premium Products</h2>
            <?php 
            // Include shop section with absolute path
            $shopSectionPath = __DIR__ . '/includes/shop/shop-section.php';
            if (file_exists($shopSectionPath)) {
                include $shopSectionPath;
            } else {
                echo '<p class="error-message">Shop section is currently unavailable. Please check back later.</p>';
            }
            ?>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
    
        <!-- JavaScript -->
        <script src="/BeFit-Folder/public/js/transitions.js"></script>
    <?php if (isset($_GET['action']) && $_GET['action'] == 'add'): ?>
    <div class="cart-notification" id="cartNotification">
        <i class="fas fa-check-circle"></i>
        <span>Item added to cart!</span>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('cartNotification');
            if (notification) {
                setTimeout(() => {
                    notification.classList.add('show');
                    setTimeout(() => {
                        notification.classList.remove('show');
                    }, 3000);
                }, 100);
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>