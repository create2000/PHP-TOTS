<?php

require 'C:\xampp\htdocs\PHP-Tots\config\db.php';
require 'C:\xampp\htdocs\PHP-Tots\config\auth.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

redirect_if_not_admin();

// Fetch rooms from database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);

// Fetch reviews from database
$reviewsSql = "SELECT r.id, r.rating, r.comment, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, r.created_at
               FROM reviews r
               JOIN users u ON r.user_id = u.id
               ORDER BY r.created_at DESC";
$reviewsResult = $conn->query($reviewsSql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Roboto', sans-serif;
        }
        .header {
            background-color: rgba(74, 85, 104, 0.8);
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
        .container {
            padding: 2rem 1rem;
        }
        .review-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            min-width: 350px;
            max-width: 500px ;
         
        }
        .review-card h3 {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .review-card p {
            margin-top: 0.5rem;
            color: #555;
        }
        .review-card .actions {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
        }
        .delete-btn {
            background-color: #f87171;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-btn:hover {
            background-color: #ef4444;
        }
        .reply-form {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
        }
        .reply-form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
        }
        .reply-form button {
            display: block;
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
        }
        .reply-form button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include("../../templates/Header.php"); ?>
    <main class="container mx-auto px-4 py-8">
        <div class="text-center my-10">
            <h2 class="text-4xl font-bold mb-6">Admin Dashboard</h2>
            <p class="text-lg mb-6">Manage rooms and customer reviews.</p>
            <a href="add.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Add Room</a>
            <a href="manage_bookings.php" class="bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600">Manage bookings</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $roomId = $row['id'];
                    $roomType = $row['type'];
                    $roomPrice = $row['price'];
                    $roomAmenities = $row['amenities'];
                    $roomImagePath = str_replace('\\', '/', $row['image_path']);
                    $roomStatus = $row['status'];

                    echo '<div class="card custom-shadow">';
                    echo '<img class="w-full h-64 object-cover object-center rounded-t-lg" src="/PHP-Tots/' . htmlspecialchars($roomImagePath) . '" alt="' . htmlspecialchars($roomType) . '">';
                    echo '<div class="card-content">';
                    echo '<h2 class="text-xl font-bold">' . htmlspecialchars($roomType) . '</h2>';
                    echo '<p class="text-gray-600">&#x20A6; ' . htmlspecialchars($roomPrice) . ' / night</p>';
                    echo '<p class="mt-2 text-sm">' . htmlspecialchars($roomAmenities) . '</p>';
                    echo "<a href=\"edit_room.php?id=$roomId\" class=\"mt-4 py-2 px-4 rounded-full text-sm font-semibold bg-blue-500 text-white hover:bg-blue-600\">Edit Room</a>";
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
                        <div class="review-card bg-white p-4 rounded-lg shadow-md flex-1 ">
                            <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($review['customer_name']); ?></h3>
                            <p class="text-yellow-500 mt-1">
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    &#9733;
                                <?php endfor; ?>
                                <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                    &#9734;
                                <?php endfor; ?>
                            </p>
                            <p class="mt-2"><?php echo htmlspecialchars($review['comment']); ?></p>
                            <div class="actions mt-4">
                                <button class="delete-btn" onclick="deleteReview(<?php echo $review['id']; ?>)">Delete</button>
                                <button class="reply-btn" onclick="toggleReplyForm(<?php echo $review['id']; ?>)">Reply</button>
                            </div>
                            <div class="reply-form" id="reply-form-<?php echo $review['id']; ?>">
                                <textarea rows="3" placeholder="Write your reply..." id="reply-text-<?php echo $review['id']; ?>"></textarea>
                                <button onclick="submitReply(<?php echo $review['id']; ?>)" class="bg-blue-500 text-white mt-2 px-4 py-2 rounded hover:bg-blue-600">Submit Reply</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include("../../templates/Footer.php"); ?>

    <script>
        function deleteReview(reviewId) {
            if (confirm("Are you sure you want to delete this review?")) {
                window.location.href = 'delete_review.php?id=' + reviewId;
            }
        }

        function toggleReplyForm(reviewId) {
            var form = document.getElementById('reply-form-' + reviewId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function submitReply(reviewId) {
            var replyText = document.getElementById('reply-text-' + reviewId).value;
            if (replyText.trim() === '') {
                alert('Reply cannot be empty');
                return;
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'submit_reply.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert('Reply submitted successfully');
                    location.reload();
                }
            };
            xhr.send('review_id=' + reviewId + '&reply=' + encodeURIComponent(replyText));
        }
    </script>
</body>
</html>

