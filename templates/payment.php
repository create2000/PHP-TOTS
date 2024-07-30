<?php
require_once __DIR__ . '/../vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Fetch API credentials from environment variables
$paystackSecretKey = getenv('paystack_secret_key');

// Retrieve totalPrice from query parameters or POST data
$totalPrice = isset($_POST['total_price']) ? (float) $_POST['total_price'] : (isset($_GET['totalPrice']) ? (float) $_GET['totalPrice'] : 0);

// Make sure totalPrice is valid
if ($totalPrice <= 0) {
    die('Invalid total price.');
}

// Initialize Guzzle client
$client = new Client([
    'base_uri' => 'https://api.paystack.co',
    'headers' => [
        'Authorization' => 'Bearer ' . $paystackSecretKey,
        'Content-Type' => 'application/json',
    ],
]);

// Example request to initialize a transaction
try {
    $response = $client->post('/transaction/initialize', [
        'json' => [
            'email' => 'customer@example.com', // Replace with customer's email
            'amount' => $totalPrice * 100, // Amount in kobo
            'callback_url' => 'https://yourwebsite.com/verify.php', // Replace with your callback URL
        ],
    ]);

    $body = $response->getBody()->getContents();
    $data = json_decode($body, true);

    if ($data['status']) {
        $authorizationUrl = $data['data']['authorization_url'];
        // Redirect to Paystack payment page
        header('Location: ' . $authorizationUrl);
        exit;
    } else {
        echo 'Error: ' . $data['message'];
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Payment Page</h1>
    <p class="text-lg mb-4">Total Price: $<?php echo htmlspecialchars($totalPrice); ?></p>
    <!-- Add your payment form here -->
    <form action="payment.php" method="POST">
        <div class="mb-4">
            <label for="total_price" class="block text-gray-700 font-bold mb-2">Total Price (₦)</label>
            <input type="text" id="total_price" name="total_price" value="<?php echo htmlspecialchars($totalPrice); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
            Pay Now with Paystack
        </button>
    </form>
  </div>
</body>
</html>
