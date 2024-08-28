<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessary

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $roomId = isset($_POST['room_id']) ? $_POST['room_id'] : null; // Ensure room_id is received
    $checkInDate = isset($_POST['check_in_date']) ? $_POST['check_in_date'] : null;
    $checkOutDate = isset($_POST['check_out_date']) ? $_POST['check_out_date'] : null;

    if ($roomId && $checkInDate && $checkOutDate) {
        echo "User ID: $userId<br>";
        echo "Room ID: $roomId<br>";
        echo "Check-In Date: $checkInDate<br>";
        echo "Check-Out Date: $checkOutDate<br>";
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
            header("Location: ../Dashboard.php?booking=success");
            exit();
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }

        $conn->close();
    } else {
        echo "Missing booking details. Please check the form submission.";
        echo "Room ID: $roomId<br>";
        echo "Check-In Date: $checkInDate<br>";
        echo "Check-Out Date: $checkOutDate<br>";
    }
} else {
    echo "Form submission method is not POST or user is not logged in.";
}
?>