<?php
function repairAutoloader() {
    $vendorDir = __DIR__.'/vendor';
    
    // 1. Delete problematic autoload files
    $filesToRemove = [
        $vendorDir.'/autoload.php',
        $vendorDir.'/composer/autoload_classmap.php',
        $vendorDir.'/composer/autoload_files.php',
        $vendorDir.'/composer/autoload_namespaces.php',
        $vendorDir.'/composer/autoload_psr4.php',
        $vendorDir.'/composer/autoload_real.php',
        $vendorDir.'/composer/autoload_static.php'
    ];
    
    foreach ($filesToRemove as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    // 2. Regenerate autoloader
    $composerPhar = __DIR__.'/composer.phar';
    if (!file_exists($composerPhar)) {
        file_put_contents($composerPhar, file_get_contents('https://getcomposer.org/composer-stable.phar'));
    }
    
    exec('php '.escapeshellarg($composerPhar).' dump-autoload');
    
    // 3. Verify
    if (!@include($vendorDir.'/autoload.php')) {
        die("Failed to repair autoloader");
    }
    
    return true;
}
?>