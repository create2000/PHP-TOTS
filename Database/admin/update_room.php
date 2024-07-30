<?php
// Include your database connection and necessary functions
include("config/db.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roomId = intval($_POST['id']);
    $roomType = $_POST['type'];
    $roomPrice = $_POST['price'];
    $roomAmenities = $_POST['amenities'];
    $roomStatus = $_POST['status'];

    // Check if a new image is uploaded
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == UPLOAD_ERR_OK) {
        $imagePath = 'uploads/' . basename($_FILES['image_path']['name']);
        move_uploaded_file($_FILES['image_path']['tmp_name'], $imagePath);
    } else {
        // Use the existing image path if no new image is uploaded
        $imagePath = $_POST['existing_image_path'];
    }

    // Update the room details in the database
    $query = "UPDATE rooms SET type = ?, price = ?, amenities = ?, status = ?, image_path = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $roomType, $roomPrice, $roomAmenities, $roomStatus, $imagePath, $roomId);

    if ($stmt->execute()) {
        // Redirect to a success page or display a success message
        header("succesfully updated");
        exit();
    } else {
        echo "Error updating room: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
