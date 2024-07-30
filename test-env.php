<?php 
// test-env.php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();

// Fetch API credentials from environment variables
$apiKey = getenv('MONNIFY_API_KEY');
$secretKey = getenv('MONNIFY_SECRET_KEY');

// Print the API Key and Secret Key for debugging purposes
echo "API Key: $apiKey<br>";
echo "Secret Key: $secretKey<br>";
