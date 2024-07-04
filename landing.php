<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .custom-shadow {
            box-shadow: 4px 4px 6px 4px rgba(0, 0, 0, 0.1), 
                        0 2px 4px 4px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include("templates/Header.php"); ?>

    <main class="flex-grow container mx-auto p-4">
        <div class="text-center my-10">
            <h2 class="text-4xl font-bold mb-6">Welcome to MyWebsite</h2>
            <p class="text-lg mb-6">Join us and explore the amazing world of opportunities.</p>
            <div class="space-x-4">


                <?php if (!$isLoggedIn): ?>
                <a href="index.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Login</a>
                <a href="register.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Register</a>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include("templates/Footer.php"); ?>
</body>
</html>
