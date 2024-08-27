<?php
session_start();
require 'C:\xampp\htdocs\PHP-Tots\config\db.php'; // Adjust path as necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all necessary form fields and file are set
    if (isset($_POST['type'], $_POST['price'], $_POST['amenities'], $_FILES['image'])) {
        $type = $_POST['type'];
        $price = $_POST['price'];
        $amenities = $_POST['amenities'];

        // File upload handling
        $uploadOk = 1;
        $image_path = null;

        // Check if file is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_file = $_FILES['image'];
            $image_name = basename($image_file["name"]);
            $target_dir = __DIR__ . '/uploads/'; // Adjust path as necessary
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a valid image
            $check = getimagesize($image_file["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                header('Location: admin_dashboard.php');
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check file size
            if ($image_file["size"] > 10000000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Move uploaded file to desired location
            if ($uploadOk) {
                if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
                    echo "The file " . htmlspecialchars($image_name) . " has been uploaded.";
                    $image_path = $target_file;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    $uploadOk = 0; // Update upload status
                }
            }
        } else {
            echo "No file uploaded or upload error.";
        }

        // Insert room details into database if upload was successful
        if ($uploadOk) {
            $sql = "INSERT INTO rooms (type, price, amenities, image_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siss", $type, $price, $amenities, $image_path);

            if ($stmt->execute()) {
                echo "Room added successfully!";
                // Clear form after successful submission (optional)
                $_POST = array(); // Clear $_POST data
            } else {
                echo "Error: {$stmt->error}";
            }

            $stmt->close();
        }
    } else {
        echo "Form fields are not set.";
    }
} else {
    echo "Form submission method is not POST.";
}

$conn->close(); // Close database connection
?>
