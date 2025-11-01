<?php require_once __DIR__ . '/auth/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About BeFit</title>
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --section-spacing: 6rem;
        }
        
        .about-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .about-section {
            text-align: center;
            padding: var(--section-spacing) 0;
        }
        
        .about-section h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(2.5rem, 6vw, 3.5rem);
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 2rem;
            line-height: 1.2;
        }
        
        .about-section p {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.2rem;
            color: var(--gray);
            line-height: 1.8;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem auto;
            max-width: 800px;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .feature-card h3 {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .feature-card p {
            font-size: 1rem;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <main class="main-content">
        <section class="about-section">
            <div class="about-content">
                <h2>About BeFit</h2>
                <p>BeFit is your AI-powered fitness companion, offering personalized workout plans tailored to your goals, equipment, and fitness level.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-robot feature-icon"></i>
                        <h3>AI-Powered</h3>
                        <p>Smart algorithms adapt to your progress and available equipment</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-chart-line feature-icon"></i>
                        <h3>Progress Tracking</h3>
                        <p>Real-time analytics and adaptive recommendations</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-dumbbell feature-icon"></i>
                        <h3>Equipment-Based</h3>
                        <p>Workouts designed for what you have available</p>
                    </div>
                </div>
                
                <h2>Our Mission</h2>
                <p>To make premium fitness accessible through intelligent technology that adapts to your unique needs and goals.</p>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <script src="/BeFit-Folder/public/js/transitions.js"></script>
</body>
</html>