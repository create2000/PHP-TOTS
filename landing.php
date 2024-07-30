<?php
session_start();
require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessary

$isLoggedIn = isset($_SESSION['user_id']);

// Fetch rooms from database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);

// Fetch reviews from database
// SQL query to fetch reviews and replies
$reviewsSql = "
    SELECT r.id AS review_id, r.rating, r.comment AS review_comment, 
           CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name, r.created_at,
           rr.id AS reply_id, rr.reply AS reply_comment, CONCAT(a.`First name`, ' ', a.`Last name`) AS replier_name, rr.created_at AS reply_created_at
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN review_replies rr ON r.id = rr.review_id
    LEFT JOIN admin a ON rr.admin_id = a.id
    ORDER BY r.created_at DESC, rr.created_at ASC
";

$reviewsResult = $conn->query($reviewsSql);

if (!$reviewsResult) {
    echo "Error: " . $conn->error;
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: red;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
        }
        .header {
            background-color: rgba(74, 85, 104, 0.8); /* semi-transparent header */
            color: #ffffff;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            height: 200px;
            object-fit: cover;
        }
        .card-content {
            padding: 1.5rem;
        }
        .book-btn {
            background-color: #6c63ff;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }
        .book-btn:hover {
            background-color: #4f46e5;
        }
        .booked-btn {
            background-color: #cbd5e0;
            color: #4a5568;
        }
        .container {
            padding: 2rem 1rem; /* Adjust padding to reduce white space */
        }
        .text-center {
            margin-bottom: 2rem; /* Reduce space between elements */
        }
        .review-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            max-width: 500px;
            min-width: 350px;
            
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include("templates/Header.php"); ?>

    <main class="container mx-auto px-4 py-8">
        <div class="text-center my-10">
            <h2 class="text-4xl font-bold mb-6">Welcome to Elegant Hotel</h2>
            <p class="text-lg mb-6">Explore our luxurious rooms and book your stay today!</p>
            <div class="space-x-4">
                <?php if (!$isLoggedIn): ?>
                <a href="index.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Login</a>
                <a href="register.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Register</a>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $roomId = $row['id'];
                    $roomType = $row['type'];
                    $roomPrice = $row['price'];
                    $roomAmenities = $row['amenities'];
                    $roomImagePath =   $row['image_path'];
                    $roomStatus = $row['status'];

                    echo '<div class="card custom-shadow">';
                    echo '<img class="w-full h-64 object-cover object-center rounded-t-lg" src="' . htmlspecialchars($roomImagePath) . '" alt="' . htmlspecialchars($roomType) . '">';
                    echo '<div class="card-content">';
                    echo '<h2 class="text-xl font-bold">' . htmlspecialchars($roomType) . '</h2>';
                    echo '<p class="text-gray-600">&#x20A6; ' . htmlspecialchars($roomPrice) . ' / night</p>';
                    echo '<p class="mt-2 text-sm">' . htmlspecialchars($roomAmenities) . '</p>';
                    if ($roomStatus === 'available') {
                        if ($isLoggedIn) {
                            echo '<button class="book-btn mt-4 py-2 px-4 rounded-full text-sm font-semibold hover:bg-blue-600" onclick="openBookingModal(' . $roomId . ')">Book Now</button>';
                        }
                    } else {
                        echo '<button class="booked-btn mt-4 py-2 px-4 rounded-full text-sm font-semibold cursor-default">Booked</button>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p class='text-center mt-8'>No rooms found.</p>";
            }

            $conn->close();
            ?>
        </div>

       <!-- Reviews Section -->
<section class="my-10">
    <h2 class="text-2xl font-bold mb-4">Customer Reviews</h2>
    <div class="flex flex-wrap gap-4">
        <?php if ($reviewsResult->num_rows > 0): ?>
            <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                <div class="review-card bg-white p-4 rounded-lg shadow-md flex-1">
                    <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($review['reviewer_name']); ?></h3>
                    <p class="text-yellow-500 mt-1">
                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                            &#9733;
                        <?php endfor; ?>
                        <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                            &#9734;
                        <?php endfor; ?>
                    </p>
                    <p class="mt-2"><?php echo htmlspecialchars($review['review_comment']); ?></p>
                    <?php if (!empty($review['reply_comment'])): ?>
                        <div class="mt-4 border-t pt-4">
                            <h4 class="text-lg font-semibold">Reply:</h4>
                            <p><?php echo htmlspecialchars($review['reply_comment']); ?></p>
                            <p class="text-gray-500 text-sm">By <?php echo htmlspecialchars($review['replier_name']); ?> on <?php echo htmlspecialchars($review['reply_created_at']); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="actions mt-4">
                        <!-- <button class="delete-btn" onclick="deleteReview(<?php echo htmlspecialchars($review['review_id']); ?>)">Delete Review</button> -->
                        <button class="reply-btn" onclick="toggleReplyForm(<?php echo htmlspecialchars($review['review_id']); ?>)"><i>Reply</i></button>
                    </div>
                    <div class="reply-form " id="reply-form-<?php echo htmlspecialchars($review['review_id']); ?>">
                        <textarea  class="border-black" rows="3" placeholder="Write your reply..." id="reply-text-<?php echo htmlspecialchars($review['review_id']); ?>"></textarea>
                        <button onclick="submitReply(<?php echo htmlspecialchars($review['review_id']); ?>)" class="bg-blue-500 text-white mt-2 px-4 py-2 rounded hover:bg-blue-600">Submit Reply</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>
</section>

    </main>

    <?php include("templates/Footer.php"); ?>

    <?php if ($isLoggedIn): ?>
    <div id="bookingModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Book Room</h2>
            <form id="bookingForm" action="templates\booking.php" method="POST">
                <input type="hidden" id="room_id" name="room_id">
                <div class="mb-4">
                    <label for="check_in_date" class="block text-gray-700 font-bold mb-2">Check-In Date</label>
                    <input type="date" id="check_in_date" name="check_in_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
                </div>
                <div class="mb-4">
                    <label for="check_out_date" class="block text-gray-700 font-bold mb-2">Check-Out Date</label>
                    <input type="date" id="check_out_date" name="check_out_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Book Now</button>
                    <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 ml-2" onclick="closeBookingModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function openBookingModal(roomId) {
            document.getElementById('room_id').value = roomId;
            document.getElementById('bookingModal').classList.remove('hidden');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }
    </script>
</body>
</html>
