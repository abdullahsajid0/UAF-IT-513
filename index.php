<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'includes/functions.php';
?>
    <main class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-4">Welcome to Train Ticket Booking System!</h1>
        <p class="text-center text-gray-600 mb-8">Book your train tickets easily and quickly.</p>

        <section class="mb-10">
            <div class="bg-white p-6 border border-gray-300 max-w-2xl mx-auto">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Search for Trains</h2>
                
                <form action="search.php" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-1">From City:</label>
                            <input type="text" name="from" placeholder="e.g., Karachi" required 
                                class="w-full border border-gray-400 p-2">
                        </div>
                        <div>
                            <label class="block mb-1">To City:</label>
                            <input type="text" name="to" placeholder="e.g., Lahore" required 
                                class="w-full border border-gray-400 p-2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Date of Journey:</label>
                        <input type="date" name="date" required 
                            class="w-full border border-gray-400 p-2">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white p-2 font-bold hover:bg-blue-700">
                        Search Trains
                    </button>
                </form>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold mb-4">Available Trains</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM trains LIMIT 3");
                    $trains = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($trains) {
                        foreach ($trains as $train) {
                            echo "
                            <div class='bg-white p-4 border border-gray-300'>
                                <h3 class='text-lg font-bold text-blue-800 mb-2'>{$train['name']}</h3>
                                <p><strong>From:</strong> {$train['from_city']}</p>
                                <p><strong>To:</strong> {$train['to_city']}</p>
                                <p class='text-sm my-2'>Dep: {$train['departure_time']} | Arr: {$train['arrival_time']}</p>
                                <p class='font-bold text-green-700'>" . formatPrice($train['price']) . "</p>
                                <p class='text-sm text-gray-500'>Seats: {$train['seats_available']}</p>
                            </div>
                            ";
                        }
                    }
                } catch (Exception $e) {
                    echo "<p class='text-red-500'>Error loading trains.</p>";
                }
                ?>
            </div>
        </section>
    </main>
<?php require_once 'includes/footer.php'; ?>