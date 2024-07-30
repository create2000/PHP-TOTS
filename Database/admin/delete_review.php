<?php
require 'C:\xampp\htdocs\PHP-Tots\config\db.php';
require 'C:\xampp\htdocs\PHP-Tots\config\auth.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

redirect_if_not_admin();

$reviewId = $_GET['id'];

$sql = "DELETE FROM reviews WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reviewId);

if ($stmt->execute()) {
    header('Location: admin_dashboard.php?message=Review deleted successfully');
} else {
    header('Location: admin_dashboard.php?error=Failed to delete review');
}
$stmt->close();
$conn->close();
?>
