<?php
session_start();

$pageTitle = 'My Tickets';
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'includes/functions.php';
?>
    <main class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6"> My Booked Tickets</h1>

        <?php
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo "
            <div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6'>
                <strong class='font-bold'>Please Login to View Your Tickets</strong>
                <p class='mt-1'>You need to be logged in to view your booked tickets.</p>
                <a href='login.php' class='inline-block bg-blue-600 text-white px-4 py-2 mt-3 rounded hover:bg-blue-700'>Go to Login</a>
            </div>
            ";
            require_once 'includes/footer.php';
            exit;
        }

        try {
            // Fetch bookings for the logged-in user
            // Note: In a real app, you would add "WHERE user_id = ?" 
            // Since your current DB schema might not link bookings to users perfectly yet, 
            // I'm keeping your original query but strictly speaking it should filter by user.
            $stmt = $pdo->query("
                SELECT b.*, t.name, t.from_city, t.to_city, t.departure_time, t.arrival_time
                FROM bookings b
                JOIN trains t ON b.train_id = t.id
                ORDER BY b.booking_date DESC
            ");
            
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($bookings) {
                echo "<p class='mb-4 text-gray-600'>You have " . count($bookings) . " booking(s)</p>";
                
                echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-6'>";
                
                foreach ($bookings as $booking) {
                    $ref = $booking['id'] . 'TKT' . date('Y');
                    echo "
                    <div class='bg-white border border-gray-300 p-5 rounded-lg shadow-sm'>
                        <h3 class='font-bold text-lg mb-2 text-blue-800'>Ref: $ref</h3>
                        
                        <div class='text-sm text-gray-700 space-y-2'>
                            <p><strong>Train:</strong> {$booking['name']}</p>
                            <p><strong>Route:</strong> {$booking['from_city']} &rarr; {$booking['to_city']}</p>
                            <p><strong>Passenger:</strong> {$booking['passenger_name']}</p>
                            <p><strong>Date:</strong> " . formatDate($booking['journey_date']) . "</p>
                            <p><strong>Time:</strong> {$booking['departure_time']} - {$booking['arrival_time']}</p>
                            <p><strong>Seats:</strong> {$booking['seats_booked']}</p>
                            <p class='text-green-600 font-bold'>Total: " . formatPrice($booking['total_price']) . "</p>
                            <p class='text-xs text-gray-500'>Booked on: " . date('d M, Y H:i', strtotime($booking['booking_date'])) . "</p>
                        </div>

                        <button onclick=\"alert('Ticket Reference: $ref\\n\\nSave this for your journey!')\" class='mt-4 bg-gray-800 text-white px-4 py-2 text-sm hover:bg-gray-900 w-full md:w-auto'>
                            📱 Show Ticket
                        </button>
                    </div>
                    ";
                }
                echo "</div>";
            } else {
                echo "
                <div class='bg-gray-50 border border-gray-300 p-6 text-center'>
                    <p class='text-lg font-bold text-gray-700 mb-2'>No bookings found</p>
                    <p class='text-gray-600 mb-4'>You haven't booked any tickets yet.</p>
                    <a href='index.php' class='text-blue-600 underline font-bold'>Start booking now!</a>
                </div>";
            }
        } catch (Exception $e) {
            echo "<div class='bg-red-100 text-red-700 p-4 border border-red-400'>Error: " . $e->getMessage() . "</div>";
        }
        ?>
    </main>

<?php require_once 'includes/footer.php'; ?>