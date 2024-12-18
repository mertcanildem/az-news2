<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve environment variables
$smtp_user = $_ENV['SMTP_USER'];
$smtp_pass = $_ENV['SMTP_PASS'];

// Check if the script is being accessed via a web server or CLI
if (isset($_SERVER['HTTP_HOST'])) {
    // HTTP_HOST exists: likely running in a web server environment
    $host = $_SERVER['HTTP_HOST'];

    // Example redirection logic (you can modify this as needed)
    header("Location: http://$host/some-page");
    exit;
} else {
    // HTTP_HOST is not set: likely running from CLI
    echo "SMTP User: $smtp_user\n";
    echo "SMTP Pass: $smtp_pass\n";
}




