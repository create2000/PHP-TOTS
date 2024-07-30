<?php
require 'C:\xampp\htdocs\PHP-Tots\config\db.php';
require 'C:\xampp\htdocs\PHP-Tots\config\auth.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

redirect_if_not_admin();

$reviewId = $_POST['review_id'];
$reply = $_POST['reply'];

$sql = "INSERT INTO review_replies (review_id, reply, admin_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $reviewId, $reply, $_SESSION['admin_id']);

if ($stmt->execute()) {
    echo "Reply submitted successfully";
} else {
    echo "Failed to submit reply";
}
$stmt->close();
$conn->close();
?>
