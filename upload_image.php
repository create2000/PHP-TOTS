<?php
session_start();
include 'config/db.php'; // Adjust the path based on your file structure

$room_id = $_POST['room_id'];
$upload_dir = 'uploads/';

// Create uploads directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create directory with full permissions
}

$target_file = $upload_dir . basename($_FILES["image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Check file size
if ($_FILES["image"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$image_path = $target_file;

// Update room record with image path
$sql = "UPDATE rooms SET image_path = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $image_path, $room_id);
$stmt->execute();

$stmt->close();
$conn->close();
?>




<form action="upload_image.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <input type="hidden" name="room_id" value="1"> <!-- Example: Room ID -->
    <button type="submit">Upload Image</button>
</form>
