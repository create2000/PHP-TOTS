<?php ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .container {
            padding: 2rem 1rem; /* Adjust padding to reduce white space */
        }
    </style>
</head>
<body class="bg-gray-100 ">
<?php include("../../templates/Header.php"); ?>
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Add Room</h2>
        <form action="add_room.php" method="POST" enctype="multipart/form-data"> <!-- Ensure the action points to your PHP file -->
            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-bold mb-2">Room Type</label>
                <input type="text" id="type" name="type" value="<?php echo isset($_POST['type']) ? htmlspecialchars($_POST['type']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 font-bold mb-2">Price</label>
                <input type="number" id="price" name="price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
            </div>
            <div class="mb-4">
                <label for="amenities" class="block text-gray-700 font-bold mb-2">Amenities</label>
                <textarea id="amenities" name="amenities" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" rows="4" required><?php echo isset($_POST['amenities']) ? htmlspecialchars($_POST['amenities']) : ''; ?></textarea>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 font-bold mb-2">Room Image</label>
                <input type="file" id="image" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add Room</button>
            </div>
        </form>
    </div>
    <?php include("../../templates/Footer.php"); ?>
</body>
</html>





