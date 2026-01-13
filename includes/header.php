<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="styles/output.css" rel="stylesheet">
    <title>
        Train Ticket System - <?php echo isset($pageTitle) ? $pageTitle : 'Home'; ?>
    </title>
</head>

<body class="bg-gray-100 text-gray-900 font-sans flex flex-col min-h-screen">

    <header class="bg-gray-800 text-white p-6">
        <div class="container mx-auto flex flex-col items-center">

            <h1 class="text-2xl font-bold mb-6 text-center">Train Ticket Booking</h1>

            <div class="w-full flex flex-col  md:flex-row justify-center items-center gap-4">

                <nav>
                    <ul class="flex flex-col md:flex-row gap-4 text-sm items-center text-center">
                        <li><a href="index.php" class="hover:underline hover:text-gray-300">Home</a></li>
                        <li><a href="search.php" class="hover:underline hover:text-gray-300">Search Trains</a></li>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="my_tickets.php" class="hover:underline hover:text-gray-300">My Tickets</a></li>
                        <?php endif; ?>

                        <li><a href="contact.php" class="hover:underline hover:text-gray-300">Contact</a></li>

                        <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
                            <li><a href="admin_login.php" class="text-red-300 hover:text-red-100">Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="mt-2 md:mt-0 md:ml-4">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <a href="admin_logout.php"
                            class="bg-red-600 px-4 py-2 rounded text-white text-sm hover:bg-red-700 block text-center">Logout</a>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php"
                            class="bg-red-600 px-4 py-2 rounded text-white text-sm hover:bg-red-700 block text-center">Logout</a>
                    <?php else: ?>
                        <a href="login.php"
                            class="bg-blue-600 px-4 py-2 rounded text-white text-sm hover:bg-blue-500 block text-center">Login</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </header>