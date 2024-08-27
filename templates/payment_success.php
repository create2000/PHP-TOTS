<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Fetch API credentials from environment variables
$paystackSecretKey = getenv('paystack_secret_key');

// Initialize Guzzle client with debugging
$client = new Client([
    'base_uri' => 'https://api.paystack.co',
    'headers' => [
        'Authorization' => "Bearer sk_test_8b321d39a440908f64adcc8b8718b81c4cba5318",
        'Content-Type' => 'application/json',
    ],
    'timeout' => 10.0, // Set a timeout to prevent hanging
    'verify' => false, // Disable SSL verification for local testing
]);

// Retrieve the transaction reference from the query parameters
$reference = isset($_GET['reference']) ? $_GET['reference'] : '';

if ($reference) {
    try {
        $response = $client->get("/transaction/verify/$reference");

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if ($data['status'] && $data['data']['status'] === 'success') {
            $amount = $data['data']['amount'] / 100; // Convert from kobo to naira
            $email = $data['data']['customer']['email'];
            $_SESSION['payment_status'] = 'success';
            $_SESSION['payment_amount'] = $amount;
            $_SESSION['payment_email'] = $email;
        } else {
            die('Payment verification failed.');
        }
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
} else {
    die('No reference provided.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Success</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .success-message {
        text-align: center;
        padding: 50px;
        border-radius: 10px;
        background-color: #f0f9ff;
        border: 1px solid #b3e5fc;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="success-message">
    <h1 class="text-3xl font-bold mb-4 text-green-500">🎉 Payment Successful! 🎉</h1>
    <p class="text-lg mb-4">Thank you for your payment, <strong><?php echo htmlspecialchars($_SESSION['payment_email']); ?></strong>.</p>
    <p class="text-lg mb-4">You have successfully paid <strong>₦<?php echo number_format($_SESSION['payment_amount'], 2); ?></strong>.</p>
    <a href="../Dashboard.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Go to Dashboard</a>
  </div>
</body>
</html>
