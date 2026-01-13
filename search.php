<?php
session_start();
$pageTitle = 'Search Trains';
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'includes/functions.php';
?>
    <main class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Search Results</h1>

        <?php
        $from = isset($_GET['from']) ? sanitize($_GET['from']) : '';
        $to = isset($_GET['to']) ? sanitize($_GET['to']) : '';
        $date = isset($_GET['date']) ? sanitize($_GET['date']) : '';

        if ($from && $to && $date) {
            echo "<p class='mb-6 bg-blue-100 p-3 text-blue-800'>Results for: <strong>$from</strong> to <strong>$to</strong> on <strong>" . formatDate($date) . "</strong></p>";
            
            try {
                $stmt = $pdo->prepare("SELECT * FROM trains WHERE LOWER(from_city) LIKE LOWER(:from) AND LOWER(to_city) LIKE LOWER(:to) AND seats_available > 0");
                $stmt->execute([':from' => "%$from%", ':to' => "%$to%"]);
                $trains = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($trains) {
                    foreach ($trains as $train) {
                        $duration = calculateDuration($train['departure_time'], $train['arrival_time']);
                        echo "
                        <div class='bg-white border border-gray-300 p-4 mb-4'>
                            <div class='flex justify-between items-center mb-2'>
                                <h3 class='text-xl font-bold'>{$train['name']}</h3>
                                <span class='font-bold text-green-600'>" . formatPrice($train['price']) . "</span>
                            </div>
                            <div class='grid grid-cols-2 gap-2 text-sm text-gray-700 mb-4'>
                                <p><strong>Route:</strong> {$train['from_city']} -> {$train['to_city']}</p>
                                <p><strong>Duration:</strong> $duration</p>
                                <p><strong>Departure:</strong> {$train['departure_time']}</p>
                                <p><strong>Arrival:</strong> {$train['arrival_time']}</p>
                                <p><strong>Seats:</strong> {$train['seats_available']}</p>
                            </div>
                            <form action='book.php' method='GET'>
                                <input type='hidden' name='train_id' value='{$train['id']}'>
                                <input type='hidden' name='train_name' value='{$train['name']}'>
                                <input type='hidden' name='from' value='{$train['from_city']}'>
                                <input type='hidden' name='to' value='{$train['to_city']}'>
                                <input type='hidden' name='date' value='$date'>
                                <input type='hidden' name='departure' value='{$train['departure_time']}'>
                                <input type='hidden' name='arrival' value='{$train['arrival_time']}'>
                                <input type='hidden' name='price' value='{$train['price']}'>
                                <button type='submit' class='bg-blue-600 text-white px-4 py-2 hover:bg-blue-700'>Book Now</button>
                            </form>
                        </div>";
                    }
                } else {
                    echo "<div class='bg-yellow-100 p-4 border border-yellow-300 text-yellow-800'>
                            <strong>No trains found.</strong> Please try a different search.
                            <br><br>
                            <button onclick='window.history.back()' class='underline'>Go Back</button>
                          </div>";
                }
            } catch (Exception $e) {
                echo "<p class='text-red-600'>Database Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='text-red-500'>Please provide all search details.</p>";
        }
        ?>
    </main>
<?php require_once 'includes/footer.php'; ?>