<footer style="
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2rem 0;
    text-align: center;
    margin-top: auto;
">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Quick Links -->
        <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 1rem;">
            <a href="/BeFit-Folder/index.php" style="color: #4A90E2; text-decoration: none;">Home</a>
            <a href="/BeFit-Folder/about.php" style="color: #4A90E2; text-decoration: none;">About</a>
            <a href="/BeFit-Folder/privacy.php" style="color: #4A90E2; text-decoration: none;">Privacy Policy</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/BeFit-Folder/auth/logout.php" style="color: #4A90E2; text-decoration: none;">Logout</a>
            <?php else: ?>
                <a href="/BeFit-Folder/auth/signin.php" style="color: #4A90E2; text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
        
        <!-- Copyright -->
        <p style="opacity: 0.8; font-size: 0.9rem;">
            &copy; <?= date('Y') ?> BeFit. All rights reserved.
        </p>
    </div>

    <!-- Cookie Consent Banner -->
    <div id="cookie-consent" style="
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #1A1A1A;
        color: white;
        padding: 15px;
        text-align: center;
        z-index: 9999;
        align-items: center;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    ">
        <p style="margin: 0;">We use cookies to enhance your experience. By continuing, you agree to our <a href="/BeFit-Folder/privacy.php" style="color: #4A90E2;">Cookie Policy</a>.</p>
        <div style="display: flex; gap: 10px;">
            <button id="accept-cookies" style="
                background: #4A90E2;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 4px;
                cursor: pointer;
            ">Accept</button>
            <button id="decline-cookies" style="
                background: #ff4444;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 4px;
                cursor: pointer;
            ">Decline</button>
        </div>
    </div>
    
    <script src="/BeFit-Folder/auth/cookie-consent.js"></script>
</footer>