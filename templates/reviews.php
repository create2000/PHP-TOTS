<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessary

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Validate input
    if (!empty($roomId) && !empty($rating) && !empty($comment)) {
        $sql = "INSERT INTO reviews (user_id, room_id, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $userId, $roomId, $rating, $comment);

        if ($stmt->execute()) {
            $_SESSION['popupMessage'] = "Review submitted successfully!";
        } else {
            $_SESSION['popupMessage'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['popupMessage'] = "All fields are required.";
    }
}

$conn->close();
header('Location: ../dashboard.php');
exit;
?>
