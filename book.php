<?php
session_start();
$pageTitle = 'Book Ticket';
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

// 1. Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<main class="container mx-auto p-6 max-w-xl">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Complete Your Booking</h1>

    <?php
    // 2. Get details from URL (sent from search page)
    $train_id = isset($_GET['train_id']) ? (int)$_GET['train_id'] : 0;
    $price = isset($_GET['price']) ? (float)$_GET['price'] : 0;
    
    // Retrieve other details for display purposes
    $train_name = isset($_GET['train_name']) ? htmlspecialchars($_GET['train_name']) : '';
    $from = isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '';
    $to = isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '';
    $date = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '';

    // 3. Show Booking Form only if a valid train is selected
    if ($train_id) {
    ?>
        <div class="bg-blue-50 border border-blue-200 p-5 mb-6 rounded shadow-sm">
            <h3 class="font-bold text-lg mb-2 text-blue-800">🚄 Journey Details</h3>
            <p class="text-gray-700"><strong>Train:</strong> <?= $train_name ?></p>
            <p class="text-gray-700"><strong>Route:</strong> <?= $from ?> ➝ <?= $to ?></p>
            <p class="text-gray-700"><strong>Date:</strong> <?= $date ?></p>
            <p class="text-gray-700"><strong>Price per Seat:</strong> RS <?= formatPrice($price) ?></p>
        </div>

        <div class="bg-white border border-gray-300 p-6 rounded shadow-sm">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Passenger Details</h3>
            
            <form action="actions/book_action.php" method="POST" class="space-y-4">
                
                <input type="hidden" name="train_id" value="<?= $train_id ?>">
                <input type="hidden" name="journey_date" value="<?= $date ?>">
                <input type="hidden" name="price" value="<?= $price ?>">

                <div>
                    <label class="block mb-1 font-bold text-gray-700">Full Name:</label>
                    <input type="text" name="name" value="<?= $_SESSION['user_name'] ?>" required 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-1 font-bold text-gray-700">Email:</label>
                    <input type="email" name="email" value="<?= $_SESSION['user_email'] ?>" required 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-1 font-bold text-gray-700">Phone Number:</label>
                    <input type="text" name="phone" required placeholder="0300-1234567" 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-1 font-bold text-gray-700">Passenger Age:</label>
                    <input type="number" name="age" required min="1" max="100" placeholder="Age" 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-1 font-bold text-gray-700">Number of Seats:</label>
                    <input type="number" id="seats" name="seats" min="1" max="5" value="1" required 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="bg-gray-100 p-4 rounded text-center border border-gray-200 mt-4">
                    <strong class="text-xl text-gray-800">Total: RS <span id="total"><?= $price ?></span></strong>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white p-3 font-bold rounded hover:bg-green-700 transition duration-200 mt-4">
                    Confirm Booking
                </button>
            </form>
        </div>

        <script>
            document.getElementById('seats').addEventListener('change', function() {
                let price = <?= $price ?>; // Get price from PHP
                let seats = this.value;
                
                // Ensure seats is at least 1
                if(seats < 1) seats = 1;
                
                let total = (price * seats).toFixed(2);
                document.getElementById('total').textContent = total;
            });
        </script>

    <?php 
    } else {
        // Error state if someone tries to visit book.php directly without selecting a train
        echo "
        <div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>
            <p><strong>Error:</strong> No train selected.</p>
            <p>Please <a href='search.php' class='underline font-bold'>search for a train</a> first.</p>
        </div>";
    }
    ?>
</main>

<?php require_once 'includes/footer.php'; ?>