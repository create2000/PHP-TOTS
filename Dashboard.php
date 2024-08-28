<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
use GuzzleHttp\Client;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$paystackSecretKey = getenv('paystack_secret_key');

$payment_status = isset($_SESSION['payment_status']) ? $_SESSION['payment_status'] : '';

if ($payment_status === 'success') {
  $payment_amount = $_SESSION['payment_amount'];
  $payment_email = $_SESSION['payment_email'];
  unset($_SESSION['payment_status']);
  unset($_SESSION['payment_amount']);
  unset($_SESSION['payment_email']);
  // Reset total price after successful payment
  $totalPrice = 0;

  // Set notification for successful payment
  $_SESSION['notification'] = "Payment of $$payment_amount was successful!";
  $_SESSION['notification_seen'] = false; // Set notification as unseen
} else {
  $payment_amount = 0;
  $payment_email = '';
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; 

$userId = $_SESSION['user_id'];
$name = $_SESSION['user_last name'];

// Fetch all booked rooms for the user, including both paid and unpaid ones
$sql = "SELECT r.id, r.type, r.price, b.check_in_date, b.check_out_date, b.status 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.user_id = ?"; // Fetch all bookings regardless of status
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$bookedRooms = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate total price only for unpaid bookings if payment has not been successful
$totalPrice = 0;
if ($payment_status !== 'success') {
    foreach ($bookedRooms as $room) {
        if ($room['status'] == 'pending') {
            $totalPrice += $room['price'];
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .sidebar {
      transition: width 0.3s;
    }
    .sidebar-expanded {
      width: 240px;
    }
    .sidebar-collapsed {
      width: 60px;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 50;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 500px;
    }
    .popup {
      display: none;
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 20px;
      background-color: #4caf50;
      color: white;
      border-radius: 5px;
      text-align: center;
      z-index: 1000;
      opacity: 0;
      transition: opacity 1s ease-in-out;
    }

    .popup.show {
      display: block;
      opacity: 1;
    }

    .popup.hide {
      opacity: 0;
    }
  </style>
</head>
<body class="bg-gray-100">

<div id="popup" class="popup"></div>
<div class="flex h-screen">
  <!-- Sidebar -->
  <div id="sidebar" class="sidebar sidebar-expanded bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-between p-4">
      <span class="text-lg font-bold">Hello <?php echo htmlspecialchars($name); ?></span>
      <button id="toggleSidebar" class="text-white">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4 5h12a1 1 0 010 2H4a1 1 0 110-2zm0 4h12a1 1 0 010 2H4a1 1 0 110-2zm0 4h12a1 1 0 010 2H4a1 1 0 110-2z" clip-rule="evenodd"/>
        </svg>
      </button>
    </div>
    <nav class="flex flex-col space-y-2 p-4">
      <a href="landing.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/>
        </svg>
        <span>Book Another Room</span>
      </a>
      <a href="profile.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM10 7a3 3 0 110 6 3 3 0 010-6z"/>
        </svg>
        <span>Profile</span>
      </a>
      <a href="settings.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2h-1v2h1a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 012-2h1V9H4a2 2 0 01-2-2V5zm3 4h8V5H5v4zm10 4h1v2h-1v-2zm-8 0h6v2H7v-2z"/>
        </svg>
        <span>Settings</span>
      </a>
      <a href="notifications.php" class="relative flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/>
        </svg>
        <span>Notifications</span>
        <?php if (isset($_SESSION['notification']) && !$_SESSION['notification_seen']): ?>
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-green-500"></span>
        <?php endif; ?>
      </a>

      <!-- Notification Modal -->
      <?php if (isset($_SESSION['notification'])): ?>
      <div id="notificationModal" class="modal">
          <div class="modal-content">
              <span onclick="closeNotificationModal()" class="close">&times;</span>
              <p><?php echo htmlspecialchars($_SESSION['notification']); ?></p>
              <?php $_SESSION['notification_seen'] = true; // Mark notification as seen ?>
          </div>
      </div>
      <?php endif; ?>

    </nav>
  </div>

 <!-- Main content area -->
 <div class="flex-grow p-6">
    <h1 class="text-2xl font-bold mb-4">Booked Rooms</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php foreach ($bookedRooms as $room): ?>
        <div class="bg-white shadow-md rounded-lg p-4">
          <h2 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($room['type']); ?></h2>
          <p>Check-in Date: <?php echo htmlspecialchars($room['check_in_date']); ?></p>
          <p>Check-out Date: <?php echo htmlspecialchars($room['check_out_date']); ?></p>
          <p>Price: $<?php echo htmlspecialchars($room['price']); ?></p>
          <p>Status: <?php echo htmlspecialchars($room['status']); ?></p>
          <?php if ($room['status'] == 'pending'): ?>
            <form action="cancel_booking.php" method="post">
              <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
              <button type="submit" class="bg-red-500 text-white p-2 rounded mt-2 hover:bg-red-700">Cancel Booking</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($payment_status !== 'success' && $totalPrice > 0): ?>
      <div class="mt-4">
        <p class="text-lg font-semibold">Total Price: $<?php echo htmlspecialchars($totalPrice); ?></p>
        <form action="./templates/payment.php" method="post">
          <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($totalPrice); ?>">
          <button type="submit" class="bg-green-500 text-white p-2 rounded hover:bg-green-700">Initialize Payment</button>
        </form>
      </div>
    <?php endif; ?>

    <!-- Display Review Button -->
<?php if ($payment_status === 'success' && !empty($bookedRooms)): ?>
    <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" onclick="toggleReviewForm()">Leave a Review</button>
<?php endif; ?>
  </div>
</div>

<script>
document.getElementById('toggleSidebar').addEventListener('click', function() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('sidebar-expanded');
  sidebar.classList.toggle('sidebar-collapsed');
});

// Notification Popup
function showPopup(message) {
  const popup = document.getElementById('popup');
  popup.textContent = message;
  popup.classList.add('show');

  setTimeout(() => {
    popup.classList.remove('show');
  }, 5000);
}

// Check if there's a notification
<?php if (isset($_SESSION['notification']) && !$_SESSION['notification_seen']): ?>
    document.addEventListener('DOMContentLoaded', function() {
      showPopup("<?php echo addslashes($_SESSION['notification']); ?>");
    });
<?php endif; ?>
</script>

</body>
</html>
