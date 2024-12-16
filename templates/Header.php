<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .slide-in {
            transform: translateX(-100%);
            opacity: 0;
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .slide-in.show {
            transform: translateX(0);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <header class="bg-blue-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Elegant Hotel</h1>

            <!-- Hamburger Icon -->
            <button id="menu-toggle" class="block md:hidden focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>

            <!-- Navigation Links -->
            <nav id="nav-menu" class="hidden md:flex md:items-center md:gap-4">
                <a href="landing.php" class="mx-2 hover:underline">Home</a>
                <a href="#about" class="mx-2 hover:underline">About</a>
                <a href="#contact" class="mx-2 hover:underline">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-white bg-gray-800 hover:bg-gray-700 rounded-full px-4 py-2">Logout</a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-blue-800">
            <a href="landing.php" class="block py-2 px-4 hover:bg-blue-700 slide-in">Home</a>
            <a href="#about" class="block py-2 px-4 hover:bg-blue-700 slide-in">About</a>
            <a href="#contact" class="block py-2 px-4 hover:bg-blue-700 slide-in">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="block py-2 px-4 bg-gray-800 hover:bg-gray-700 rounded-full text-center slide-in">Logout</a>
            <?php endif; ?>
        </div>
    </header>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuLinks = document.querySelectorAll('#mobile-menu a');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');

            // Trigger animation for each link with a delay
            if (!mobileMenu.classList.contains('hidden')) {
                menuLinks.forEach((link, index) => {
                    setTimeout(() => {
                        link.classList.add('show');
                    }, index * 100); // Stagger effect
                });
            } else {
                menuLinks.forEach((link) => {
                    link.classList.remove('show');
                });
            }
        });
    </script>
</body>
