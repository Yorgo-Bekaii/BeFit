<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="shop-page-content" id="shop-section">
    <div class="shop-container">
        <h1 class="page-header1">BeFit Store</h1>

        <div class="compact-shop-grid">
            <?php 
            // Use absolute paths for includes
            $supplementsPath = __DIR__ . '/shop-supplements.php';
            $equipmentPath = __DIR__ . '/shop-equipment.php';
            
            if (file_exists($supplementsPath)) {
                include $supplementsPath;
            } else {
                echo '<p class="error-message">Supplements section unavailable</p>';
            }
            
            if (file_exists($equipmentPath)) {
                include $equipmentPath;
            } else {
                echo '<p class="error-message">Equipment section unavailable</p>';
            }
            ?>
        </div>
    </div>
</div>