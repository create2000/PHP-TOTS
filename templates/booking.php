<?php
session_start();
require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessary

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $roomId = $_POST['room_id'];
    $checkInDate = $_POST['check_in_date'];
    $checkOutDate = $_POST['check_out_date'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert booking
        $sql = "INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $userId, $roomId, $checkInDate, $checkOutDate);
        $stmt->execute();
        $stmt->close();

        // Update room status
        $sql = "UPDATE rooms SET status = 'booked' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect to landing page
        header("Location:../Dashboard.php?booking=success");
        exit();
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    $conn->close();
} else {
    echo "Form submission method is not POST or user is not logged in.";
}
?>
