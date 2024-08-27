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

// Clear the payment status after displaying it once
if ($payment_status === 'success') {
    $payment_amount = $_SESSION['payment_amount'];
    $payment_email = $_SESSION['payment_email'];
    unset($_SESSION['payment_status']);
    unset($_SESSION['payment_amount']);
    unset($_SESSION['payment_email']);
    $totalPrice = 0; // Reset total price after successful payment
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

// Fetch booked rooms for the user
$sql = "SELECT r.id, r.type, r.price, b.check_in_date, b.check_out_date 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$bookedRooms = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate total price
$totalPrice = 0;
foreach ($bookedRooms as $room) {
    $totalPrice += $room['price'];
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
      <span class="text-lg font-bold">Hello <?php echo htmlspecialchars($name) ?></span>
      <button id="toggleSidebar" class="text-white">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 5h12a1 1 0 010 2H4a1 1 0 110-2zm0 4h12a1 1 0 010 2H4a1 1 0 110-2zm0 4h12a1 1 0 010 2H4a1 1 0 110-2z" clip-rule="evenodd"/></svg>
      </button>
    </div>
    <nav class="flex flex-col space-y-2 p-4">
      <a href="landing.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/></svg>
        <span>Book Another Room</span>
      </a>
      <a href="profile.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM10 7a3 3 0 110 6 3 3 0 010-6z"/></svg>
        <span>Profile</span>
      </a>
      <a href="settings.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2h-1v2h1a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 012-2h1V9H4a2 2 0 01-2-2V5zm3 4h8V5H5v4zm10 4h1v2h-1v-2zm-8 0h6v2H7v-2z"/></svg>
        <span>Settings</span>
      </a>
      <a href="notifications.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/></svg>
        <span>Notifications</span>
      </a>
    </nav>
  </div>

 <!-- Main content -->
 <div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow p-4">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold"><?php echo htmlspecialchars($name) . "'s" ?> Dashboard</h1>
        <div class="flex items-center space-x-4">
          <a href="logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
        </div>
      </div>
    </header>

    <!-- Main section -->
    <main class="flex-1 p-4 overflow-y-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (!empty($bookedRooms)): ?>
          <?php foreach ($bookedRooms as $room): ?>
            <div class="bg-white shadow rounded-lg p-4">
              <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($room['type']); ?></h2>
              <p>Check-in: <?php echo htmlspecialchars($room['check_in_date']); ?></p>
              <p>Check-out: <?php echo htmlspecialchars($room['check_out_date']); ?></p>
              <p>Price: $<?php echo htmlspecialchars($room['price']); ?></p>
              <div class="mt-4 flex space-x-2">
                <!-- Cancel Booking Button -->
                <form action="./templates/cancel_booking.php" method="POST" class="inline-block">
                  <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
                  <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Cancel Booking</button>
                </form>
                
               
              </div>
            </div>
           <!-- Hidden Review Form -->
<div id="reviewForm" class="modal">
    <div class="modal-content">
        <span onclick="closeReviewModal()" class="close">&times;</span>
        <form action="./templates/reviews.php" method="POST">
            <h2 class="text-lg font-semibold">Leave a Review</h2>

            <!-- Room ID Input -->
            <div class="mt-4">
                <label for="room_id" class="block text-sm font-medium text-gray-700">Room ID</label>
                <input type="text" id="room_id" name="room_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <!-- Rating Input -->
            <div class="mt-4">
                <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <!-- Comment Input -->
            <div class="mt-4">
                <label for="comment" class="block text-sm font-medium text-gray-700">Comment</label>
                <textarea id="comment" name="comment" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Submit Review</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600" onclick="toggleReviewForm()">Cancel</button>
            </div>
        </form>
    </div>
</div>


          <?php endforeach; ?>
        <?php else: ?>
          <p>No booked rooms found.</p>
        <?php endif; ?>
      </div>

      <!-- Initialize Payment Button -->
      <?php if ($totalPrice > 0 && $payment_status !== 'success'): ?>
        <div class="bg-gray-100 p-4 rounded-lg mt-4">
          <form action="./templates/payment.php" method="POST">
            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($totalPrice); ?>">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Initialize Payment</button>
          </form>

          <?php foreach($bookedRooms as $room) ;
             echo '<button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" onclick="openReviewModal(' . htmlspecialchars($room['id']) . ')">Leave a Review</button>';
             echo '</div></div>';
          ?>
         
      <?php endif; ?>

      <!-- Display total price -->
      <?php if ($totalPrice > 0 && $payment_status !== 'success'): ?>
        <div class="bg-gray-100 p-4 rounded-lg mt-4">
          <h2 class="text-lg font-semibold">Total Price</h2>
          <p>$<?php echo htmlspecialchars($totalPrice); ?></p>
        </div>
      <?php endif; ?>

      <?php if ($payment_status === 'success'): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mt-4">
          <h2 class="text-lg font-semibold">Payment Successful!</h2>
          <p>Thank you, <?php echo htmlspecialchars($name); ?>. Your payment of $<?php echo htmlspecialchars($payment_amount); ?> was successful.</p>
          <p>A receipt has been sent to <?php echo htmlspecialchars($payment_email); ?>.</p>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<script>
document.getElementById('toggleSidebar').addEventListener('click', function () {
  const sidebar = document.getElementById('sidebar');
  if (sidebar.classList.contains('sidebar-expanded')) {
    sidebar.classList.remove('sidebar-expanded');
    sidebar.classList.add('sidebar-collapsed');
  } else {
    sidebar.classList.remove('sidebar-collapsed');
    sidebar.classList.add('sidebar-expanded');
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const popup = document.getElementById('popup');
  if (popup.textContent.trim()) {
    popup.classList.add('show');
    setTimeout(function() {
      popup.classList.remove('show');
      popup.classList.add('hide');
    }, 3000);
  }
});

function toggleReviewForm() {
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm.style.display === 'none' || !reviewForm.style.display) {
        reviewForm.style.display = 'flex';
    } else {
        reviewForm.style.display = 'none';
    }
}
function openReviewModal(roomId) {
    document.getElementById('room_id').value = roomId; // Set the room ID in the hidden field
    document.getElementById('reviewForm').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewForm').style.display = 'none';
}


</script>

</body>
</html>