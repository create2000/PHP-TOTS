<?php

require 'C:\xampp\htdocs\PHP-Tots\config\db.php';
require 'C:\xampp\htdocs\PHP-Tots\config\auth.php';

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE `first name` = ? AND `last name` = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("ss", $first_name, $last_name);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['Password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $errorMessage = "Invalid password";
        }
    } else {
        $errorMessage = "Invalid credentials";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
         .container {
            padding: 2rem 1rem; /* Adjust padding to reduce white space */
        }
    </style>
</head>
<body class="bg-gray-100 ">
<?php include("../../templates/Header.php"); ?>
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6 mt-4">
        <h2 class="text-2xl font-bold mb-4">Admin Login</h2>
        <form action="admin_login.php" method="POST">
            <div class="mb-4 ">
                <label for="first_name" class="block text-gray-700 font-bold mb-2">First Name</label>
                <input type="text" id="first_name" name="first_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700 font-bold mb-2">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" required>
            </div>
            <?php if (!empty($errorMessage)): ?>
                <p class="text-red-500"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Login</button>
            </div>
        </form>
    </div>
    <?php include("../../templates/Footer.php"); ?>
</body>
</html>
