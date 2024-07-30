<?php

require 'C:\xampp\htdocs\PHP-Tots\config\db.php';
require 'C:\xampp\htdocs\PHP-Tots\config\auth.php';

// Check if the room ID is set in the URL
if (!isset($_GET['id'])) {
    echo "Room ID is missing.";
    exit();
}

$roomId = intval($_GET['id']);

// Fetch room details from the database
$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    echo "Room not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update room details
    $roomType = $_POST['type'];
    $roomPrice = $_POST['price'];
    $roomAmenities = $_POST['amenities'];
    $roomStatus = $_POST['status'];

    // Handle image upload
    $imagePath = $room['image_path']; // Use existing image if no new image is uploaded
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['image_path']['name']);
        $imagePath = $targetDir . $imageName;
        move_uploaded_file($_FILES['image_path']['tmp_name'], $imagePath);
    }

    $updateSql = "UPDATE rooms SET type = ?, price = ?, amenities = ?, status = ?, image_path = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssi", $roomType, $roomPrice, $roomAmenities, $roomStatus, $imagePath, $roomId);

    if ($updateStmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating room: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .container {
            padding: 2rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include("../../templates/Header.php"); ?>
    <main class="container mx-auto">
        <h2 class="text-4xl font-bold mb-6">Edit Room</h2>
        <form action="edit_room.php?id=<?php echo $roomId; ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Room Type</label>
                <input type="text" name="type" id="type" value="<?php echo htmlspecialchars($room['type']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Room Price</label>
                <input type="text" name="price" id="price" value="<?php echo htmlspecialchars($room['price']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="amenities" class="block text-sm font-medium text-gray-700">Room Amenities</label>
                <textarea name="amenities" id="amenities" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($room['amenities']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Room Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="available" <?php echo $room['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="booked" <?php echo $room['status'] == 'booked' ? 'selected' : ''; ?>>Booked</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="image_path" class="block text-sm font-medium text-gray-700">Room Image</label>
                <input type="file" name="image_path" id="image_path" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <img src="/PHP-Tots/<?php echo htmlspecialchars($room['image_path']); ?>" alt="Room Image" class="mt-4 w-full h-64 object-cover object-center rounded-md">
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Update Room</button>
        </form>
    </main>
    <?php include("../../templates/Footer.php"); ?>
</body>
</html>
