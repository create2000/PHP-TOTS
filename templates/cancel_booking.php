<?php
session_start();
require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessar

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $roomId = $_POST['room_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete booking
        $sql = "DELETE FROM bookings WHERE user_id = ? AND room_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $roomId);
        $stmt->execute();
        $stmt->close();

        // Update room status
        $sql = "UPDATE rooms SET status = 'available' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect back to dashboard
        header("Location: ../landing.php?cancellation=success");
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
