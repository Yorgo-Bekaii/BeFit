<?php
require_once __DIR__ . '/auth/config.php';
$title = "Privacy Policy";
include __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <h1>Privacy Policy</h1>
    
    <section>
        <h2>Cookie Usage</h2>
        <p>We use cookies to:</p>
        <ul>
            <li>Remember your preferences</li>
            <li>Provide secure login</li>
            <li>Analyze site traffic</li>
        </ul>
        
        <h3>Managing Cookies</h3>
        <p>You can manage cookies through your browser settings. To delete our cookies:</p>
        <ol>
            <li>Open your browser settings</li>
            <li>Find the privacy or cookies section</li>
            <li>Search for "befit_cookie_consent" and remove it</li>
        </ol>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>