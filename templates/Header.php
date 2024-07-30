<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .custom-shadow {
            box-shadow: 4px 4px 6px 4px rgba(0, 0, 0, 0.1), 
                        0 2px 4px 4px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <header class="bg-blue-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Elegant Hotel</h1>
            <nav>
                <a href="landing.php" class="mx-2 hover:underline">Home</a>
                <a href="#about" class="mx-2 hover:underline">About</a>
                <a href="#contact" class="mx-2 hover:underline">Contact</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-white bg-gray-800 hover:bg-gray-700 rounded-full px-4 py-2 inline-block mt-4" style="border-radius: 9999px;">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
