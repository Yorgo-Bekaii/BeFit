    </main>
    
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-column">
                    <a href="/BeFit-Folder/index.php" class="footer-logo">
                        <img src="/BeFit-Folder/public/photos/logo1.png" alt="BeFit Logo" class="footer-logo-img">
                    </a>
                    <p class="footer-text">Transform your fitness journey with AI-powered training and nutrition plans.</p>
                </div>

                <div class="footer-column">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/BeFit-Folder/index.php">Home</a></li>
                        <li><a href="/BeFit-Folder/index.php#shop-section">Shop</a></li>
                        <li><a href="/BeFit-Folder/about.php">About</a></li>
                        <li><a href="/BeFit-Folder/auth/signin.php">Sign In</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-heading">Legal</h4>
                    <ul class="footer-links">
                        <li><a href="/BeFit-Folder/terms.php">Terms of Service</a></li>
                        <li><a href="/BeFit-Folder/privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-heading">Connect</h4>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/befitmiddleeast?igsh=eGNodThhdHB0ZmVt"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/share/16V3szLFh2/"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> BeFit AI. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Simple page transition effects
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('loaded');
            
            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script> <script>
  console.log('Cookie Consent:', document.cookie);
</script>
</body>
</html>
