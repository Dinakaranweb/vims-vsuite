<?php
echo "<h2>Environment Variables Test</h2>";
echo "<pre>";
echo "DB_DATABASE: " . getenv('DB_DATABASE') . "\n";
echo "DB_CONNECTION: " . getenv('DB_CONNECTION') . "\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "APP_ENV: " . getenv('APP_ENV') . "\n";
echo "</pre>";
?>