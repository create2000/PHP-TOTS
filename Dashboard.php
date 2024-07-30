<?php
session_start();
$popupMessage = isset($_SESSION['popupMessage']) ? $_SESSION['popupMessage'] : '';
unset($_SESSION['popupMessage']);

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessary

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

<div id="popup" class="popup"><?php echo $popupMessage; ?></div>
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
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
              <h2 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($room['type']); ?></h2>
              <p class="text-gray-600">Price: $<?php echo htmlspecialchars($room['price']); ?> / night</p>
              <p class="text-gray-600">Check-In Date: <?php echo htmlspecialchars($room['check_in_date']); ?></p>
              <p class="text-gray-600">Check-Out Date: <?php echo htmlspecialchars($room['check_out_date']); ?></p>
              <form action="templates\cancel_booking.php" method="POST" class="mt-4">
                <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Cancel Booking</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-gray-600">You have no booked rooms.</p>
        <?php endif; ?>
      </div>
      
      <!-- Payment section -->
      <div class="mt-6">
        <h2 class="text-xl font-semibold">Total Price: $<?php echo htmlspecialchars($totalPrice); ?></h2>
        <form action="templates\payment.php" method="post" class="mt-4">
          <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($totalPrice); ?>">
          <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Proceed to Payment</button>
      </form>
      </div>

      <!-- Leave a Review Button -->
      <div class="-mt-8 text-center">
        <button id="leaveReviewBtn" class="bg-green-500 text-white py-2 px-4  rounded hover:bg-green-600">Leave a Review</button>
      </div>
    </main>
  </div>
</div>

<!-- Modal -->
<!-- Inside the Modal in dashboard.php -->
<div id="reviewModal" class="modal">
  <div class="modal-content">
    <span id="closeModal" class="cursor-pointer text-gray-500">&times;</span>
    <h2 class="text-lg font-bold mb-4">Leave a Review</h2>
    <form action="templates/reviews.php" method="POST">
      <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
      <div class="mb-4">
        <label for="rating" class="block text-gray-700">Rating:</label>
        <select id="rating" name="rating" class="block w-full mt-2 p-2 border border-gray-300 rounded">
          <option value="1">1 - Very Poor</option>
          <option value="2">2 - Poor</option>
          <option value="3">3 - Average</option>
          <option value="4">4 - Good</option>
          <option value="5">5 - Excellent</option>
        </select>
      </div>
      <div class="mb-4">
        <label for="comment" class="block text-gray-700">Comment:</label>
        <textarea id="comment" name="comment" rows="4" class="block w-full mt-2 p-2 border border-gray-300 rounded" placeholder="Write your review here..."></textarea>
      </div>
      <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Submit Review</button>
    </form>
  </div>
  
</div>

<?php if ($popupMessage): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var popup = document.getElementById('popup');
                popup.classList.add('show');
                setTimeout(function() {
                    popup.classList.add('hide');
                    setTimeout(function() {
                        popup.style.display = 'none';
                    }, 1000); // Wait for the transition to complete
                }, 3000); // Show for 3 seconds
            });
        </script>
    <?php endif; ?>

<script>
  // JavaScript to handle modal display and dimming
  const reviewModal = document.getElementById('reviewModal');
  const leaveReviewBtn = document.getElementById('leaveReviewBtn');
  const closeModal = document.getElementById('closeModal');
  const body = document.body;

  leaveReviewBtn.addEventListener('click', function() {
    reviewModal.style.display = 'flex';
    body.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
  });

  closeModal.addEventListener('click', function() {
    reviewModal.style.display = 'none';
    body.style.backgroundColor = '';
  });

  window.addEventListener('click', function(event) {
    if (event.target === reviewModal) {
      reviewModal.style.display = 'none';
      body.style.backgroundColor = '';
    }
  });
</script>

</body>
</html>

