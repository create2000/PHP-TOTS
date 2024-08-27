<?php
require_once __DIR__ . '/../vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Fetch API credentials from environment variables
$paystackSecretKey = getenv('PAYSTACK_SECRET_KEY');

// Retrieve transaction reference from query parameters
$reference = $_GET['reference'] ?? '';

// Initialize Guzzle client
$client = new Client([
    'base_uri' => 'https://api.paystack.co',
    'headers' => [
        'Authorization' => 'Bearer sk_test_8b321d39a440908f64adcc8b8718b81c4cba5318',
        'Content-Type' => 'application/json',
    ],
]);

// Verify the transaction
try {
    $response = $client->get("/transaction/verify/$reference");
    $body = $response->getBody()->getContents();
    $data = json_decode($body, true);

    if ($data['status'] && $data['data']['status'] === 'success') {
        echo 'Payment successful! Thank you for your purchase.';
    } else {
        echo 'Payment verification failed. Please try again.';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
