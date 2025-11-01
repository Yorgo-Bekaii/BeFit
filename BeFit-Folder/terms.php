<?php
$pageTitle = "Terms & Conditions - BeFit";
$activePage = "terms";
require_once __DIR__ . '/auth/config.php';
include __DIR__ . '/includes/header.php';
?>

<main class="terms-page">
    <section class="terms-hero">
        <div class="hero-content">
            <h1>BeFit Terms & Conditions</h1>
        </div>
    </section>

    <div class="container">
        <div class="terms-container">
            <article class="terms-content">
                <section class="terms-section">
                    <h2><span class="section-number">1</span> Introduction</h2>
                    <p>Welcome to BeFit. These terms govern your use of our fitness products and AI workout services. By accessing our platform, you agree to these conditions.</p>
                </section>

                <section class="terms-section">
                    <h2><span class="section-number">2</span> Account Registration</h2>
                    <ul class="styled-list">
                        <li>You must be at least 18 years old to create an account</li>
                        <li>You're responsible for maintaining your login credentials</li>
                        <li>All registration information must be accurate</li>
                    </ul>
                </section>

                <section class="terms-section">
                    <h2><span class="section-number">3</span> Product Terms</h2>
                    <ul class="styled-list">
                        <li>30-day return policy for unopened items</li>
                        <li>3-5 business day shipping</li>
                        <li>1-year warranty on equipment</li>
                    </ul>
                </section>

                <section class="terms-section">
                    <h2><span class="section-number">4</span> AI Services</h2>
                    <div class="notice-box">
                        <p>Our AI provides fitness suggestions only, not medical advice. Consult a physician before beginning any program.</p>
                    </div>
                </section>

                <div class="terms-actions">
                    <a href="support.php" class="btn-primary">Contact Support</a>
                </div>
            </article>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<style>
.terms-page {
    background: #f8f9fa;
    padding-bottom: 80px;
}

.terms-hero {
    background: #f8f9fa;
    padding: 80px 0 40px;
    text-align: center;
    position: relative;
}

.terms-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    display: inline-block;
}

.terms-hero h1:after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #4A90E2;
}

.terms-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    padding: 50px;
    position: relative;
    top: -40px;
}

.terms-section {
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid #eee;
}

.terms-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.terms-section h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.section-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #4A90E2;
    color: white;
    border-radius: 50%;
    font-size: 0.9rem;
    margin-right: 15px;
}

.styled-list {
    list-style: none;
    padding-left: 0;
    margin-top: 20px;
}

.styled-list li {
    padding-left: 30px;
    position: relative;
    margin-bottom: 12px;
}

.styled-list li:before {
    content: "•";
    color: #4A90E2;
    font-size: 1.5rem;
    position: absolute;
    left: 0;
    top: -3px;
}

.notice-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 25px 0;
    border-left: 4px solid #4A90E2;
}

.terms-actions {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.btn-primary {
    background: #4A90E2;
    color: white;
    padding: 12px 25px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}

.btn-primary:hover {
    background: #357ABD;
}

@media (max-width: 768px) {
    .terms-container {
        padding: 30px;
        top: -20px;
    }
    
    .terms-hero h1 {
        font-size: 2rem;
    }
    
    .terms-section h2 {
        font-size: 1.3rem;
    }
}
</style>