<?php 
require 'config/db.php';

$errors = array('first_name' => '', 'last_name' => '', 'email' => '', 'password' => '' );
if(isset($_POST['submit'])) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate first name
        if (empty($_POST['first_name'])) {
            $errors['first_name'] = 'First name must be provided <br />';
        } else {
            $first_name = $_POST['first_name'];
            if (!preg_match("/^[a-zA-Z ]*$/", $first_name)) {
                $errors['first_name'] = 'First name can only contain letters and spaces <br />';
            }
        }

        // Validate last name
        if (empty($_POST['last_name'])) {
            $errors['last_name'] = 'Last name must be provided <br />';
        } else {
            $last_name = $_POST['last_name'];
            if (!preg_match("/^[a-zA-Z ]*$/", $last_name)) {
                $errors['last_name'] = 'Last name can only contain letters and spaces <br />';
            }
        }
    
        // Validate email
        if (empty($_POST['email'])) {
            $errors['email'] = 'Email must be provided <br />';
        } else {
            $email = $_POST['email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email must be a valid email address <br />';
            }
        }
    
        // Validate password
        if (empty($_POST['password'])) {
            $errors['password'] = 'Password must be provided <br />';
        } else {
            $password = $_POST['password'];
            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
                $errors['password'] = 'Password must be at least 8 characters long and include both letters and numbers <br />';
            }
        }

        if(array_filter($errors)) {
            // echo "error: " . $errors;
        } else {

            $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);

            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users(first_name, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$hashed_password')";

            if(mysqli_query($conn, $sql)) {
                
                header('Location:index.php');
            } else {
                echo 'query error: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .custom-shadow {
            box-shadow: 4px 4px 6px 4px rgba(0, 0, 0, 0.1), 
                        0 2px 4px 4px rgba(0, 0, 0, 0.06);
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .to-up {
            margin: 0px auto;
        }
        .white-filter {
            filter: invert(100%) sepia(0%) saturate(0%) hue-rotate(180deg);
        }
    </style>
</head>
<body class="">
    <?php include("templates/Header.php"); ?>

    <div class="bg-white mt-4 p-8 rounded-lg custom-shadow w-full max-w-md fade-in to-up">
        <h2 class="text-3xl font-bold mb-6 text-center">Register</h2>
        <form action="Register.php" method="POST">
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700">First Name</label>
                <input type="text" id="first_name" name="first_name" class="mt-1 px-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                <div class="text-red-600"><?php echo $errors['first_name']; ?> </div>
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="mt-1 px-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                <div class="text-red-600"><?php echo $errors['last_name']; ?> </div>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="text" id="email" name="email" class="mt-1 px-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                <div class="text-red-600"><?php echo $errors['email']; ?> </div>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 px-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                <div class="text-red-600"><?php echo $errors['password']; ?> </div>
            </div>
            <button type="submit" name="submit" value="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 hover-zoom">Register</button>
        </form>
        <div class="mt-6 text-center">
            <p class="text-gray-700 mb-4">or register with</p>
            <div class="flex justify-center space-x-4">
                <button class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover-zoom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.73 12.334c0-.762-.068-1.49-.188-2.198H12v4.157h6.107c-.264 1.408-1.062 2.595-2.253 3.397v2.835h3.622c2.125-1.957 3.354-4.839 3.354-8.191z" fill="#4285F4"/><path d="M12 24c3.24 0 5.946-1.08 7.928-2.926l-3.622-2.835c-1.003.67-2.28 1.068-4.306 1.068-3.295 0-6.093-2.22-7.096-5.199H1.17v3.107C3.142 21.38 7.225 24 12 24z" fill="#34A853"/><path d="M4.904 14.108c-.23-.67-.36-1.38-.36-2.108s.13-1.438.36-2.108V6.793H1.17C.424 8.352 0 10.12 0 12s.424 3.648 1.17 5.207l3.734-3.099z" fill="#FBBC05"/><path d="M12 4.807c1.75 0 3.316.603 4.554 1.783l3.416-3.417C17.946 1.222 15.24 0 12 0 7.225 0 3.142 2.62 1.17 6.793l3.734 3.1c1.003-2.98 3.8-5.199 7.096-5.199z" fill="#EA4335"/></svg>
                </button>
                <button class="w-10 h-10 bg-gray-900 text-white rounded-full flex items-center justify-center hover-zoom">
                    <img src="./Images/11053970_x_logo_twitter_new_brand_icon.png" alt="" class="h-6 w-6 white-filter">
                </button>
                <button class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center hover-zoom">
                    <img src="./Images/211902_social_facebook_icon.png" alt="" class="h-6 w-6">
                </button>
            </div>
        </div>
    </div>

    <?php include("templates/Footer.php"); ?>
</body>
</html>
