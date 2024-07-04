<?php
session_start();
include 'config\db.php';

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}


$errorMessage = ""; 



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement
    $sql = "SELECT id, `first name`, `last name`, email, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql); // Prepare statement for security
    mysqli_stmt_bind_param($stmt, "s", $email); // Bind email parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result); 

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_first name'] = $user['first name'];
            $_SESSION['user_last name'] = $user['last name'];
            header("Location: landing.php");
            exit; 
        } else {
            $errorMessage = "Invalid email or password.";
        }
    } else {
        $errorMessage = "Invalid email or password.";
    }

    mysqli_stmt_close($stmt); 
    mysqli_close($conn); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body >
    <?php include("templates/Header.php"); ?>

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-gradient-to-r from-yellow-100 via-yellow-50 to-yellow-100 p-8 rounded-lg custom-shadow w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Login!</h2>
            <?php if ($errorMessage): ?>
                <div class="text-red-600 mb-4"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            <form action="index.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="mt-1 px-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="mt-1 px-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-black text-white py-2 rounded-md hover:bg-blue-600">Login</button>
            </form>
            <div class="mt-4 text-center">
                <p class="text-gray-700">Don't have an account? <a href="register.php" class="text-blue-500 hover:underline">Register</a></p>
            </div>
        </div>
    </div>

    <?php include("templates/Footer.php"); ?>
</body>
</html>
