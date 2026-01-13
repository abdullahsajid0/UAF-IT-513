<?php
$pageTitle = 'Admin Panel';
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}



$success = '';
$error = '';

// ADD TRAIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_train'])) {
    $name = sanitize($_POST['name']);
    $from = sanitize($_POST['from']);
    $to = sanitize($_POST['to']);
    $departure = sanitize($_POST['departure']);
    $arrival = sanitize($_POST['arrival']);
    $price = (float) $_POST['price'];
    $seats = (int) $_POST['seats'];

    if ($name && $from && $to && $departure && $arrival && $price > 0 && $seats > 0) {
        $stmt = $pdo->prepare("INSERT INTO trains (name, from_city, to_city, departure_time, arrival_time, price, seats_available) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $from, $to, $departure, $arrival, $price, $seats])) {
            $success = "Train added successfully!";
        } else {
            $error = "Error adding train";
        }
    } else {
        $error = "Please fill all fields correctly";
    }
}

// DELETE TRAIN
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM trains WHERE id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "Train deleted!";
    }
}

// Get all trains
$trains = $pdo->query("SELECT * FROM trains")->fetchAll(PDO::FETCH_ASSOC);

// Get all bookings
try {
    $bookings = $pdo->query("SELECT b.*, t.name as train_name FROM bookings b JOIN trains t ON b.train_id = t.id ORDER BY b.booking_date DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $bookings = [];
}

// Get stats
$train_count = count($trains);
$booking_count = count($bookings);
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<main class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Admin Dashboard</h1>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <article class="bg-blue-600 text-white p-6 text-center rounded shadow-sm">
            <h3 class="text-4xl font-bold"><?= $train_count ?></h3>
            <p class="font-bold mt-2">Total Trains</p>
        </article>
        <article class="bg-purple-600 text-white p-6 text-center rounded shadow-sm">
            <h3 class="text-4xl font-bold"><?= $booking_count ?></h3>
            <p class="font-bold mt-2">Total Bookings</p>
        </article>
        <article class="bg-teal-500 text-white p-6 text-center rounded shadow-sm">
            <h3 class="text-4xl font-bold"><?= $users_count ?></h3>
            <p class="font-bold mt-2">Total Users</p>
        </article>
    </section>

    <section class="bg-white p-6 border border-gray-300 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Add New Train</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" name="name" placeholder="Train Name" required
                class="border border-gray-400 p-2 rounded w-full">

            <input type="text" name="from" placeholder="From City" required
                class="border border-gray-400 p-2 rounded w-full">

            <input type="text" name="to" placeholder="To City" required
                class="border border-gray-400 p-2 rounded w-full">

            <input type="number" name="price" placeholder="Price (RS)" min="1" required
                class="border border-gray-400 p-2 rounded w-full">

            <div class="md:col-span-2 lg:col-span-1">
                <label class="block text-xs text-gray-500">Departure</label>
                <input type="time" name="departure" required class="border border-gray-400 p-2 rounded w-full">
            </div>

            <div class="md:col-span-2 lg:col-span-1">
                <label class="block text-xs text-gray-500">Arrival</label>
                <input type="time" name="arrival" required class="border border-gray-400 p-2 rounded w-full">
            </div>

            <div class="md:col-span-2 lg:col-span-1">
                <label class="block text-xs text-gray-500">Seats</label>
                <input type="number" name="seats" placeholder="Seats" min="1" required
                    class="border border-gray-400 p-2 rounded w-full">
            </div>

            <div class="md:col-span-2 lg:col-span-1 flex items-end">
                <button type="submit" name="add_train"
                    class="bg-blue-600 text-white font-bold py-2 px-4 rounded w-full hover:bg-blue-700">
                    Add Train
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white p-6 border border-gray-300 mb-8 overflow-x-auto">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">All Trains</h2>
        <?php if ($trains): ?>
            <table class="w-full border-collapse border border-gray-300 min-w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="border border-gray-300 p-2">ID</th>
                        <th class="border border-gray-300 p-2">Name</th>
                        <th class="border border-gray-300 p-2">Route</th>
                        <th class="border border-gray-300 p-2">Time</th>
                        <th class="border border-gray-300 p-2">Price</th>
                        <th class="border border-gray-300 p-2">Seats</th>
                        <th class="border border-gray-300 p-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trains as $train): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 p-2"><?= $train['id'] ?></td>
                            <td class="border border-gray-300 p-2 font-bold"><?= $train['name'] ?></td>
                            <td class="border border-gray-300 p-2"><?= $train['from_city'] ?> → <?= $train['to_city'] ?></td>
                            <td class="border border-gray-300 p-2"><?= $train['departure_time'] ?> -
                                <?= $train['arrival_time'] ?></td>
                            <td class="border border-gray-300 p-2">RS <?= number_format($train['price'], 0) ?></td>
                            <td class="border border-gray-300 p-2"><?= $train['seats_available'] ?></td>
                            <td class="border border-gray-300 p-2 text-center">
                                <a href="?delete_id=<?= $train['id'] ?>" class="text-red-600 hover:text-red-800 underline"
                                    onclick="return confirm('Delete this train?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No trains yet. Add one above!</p>
        <?php endif; ?>
    </section>

    <section class="bg-white p-6 border border-gray-300 mb-8 overflow-x-auto">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">All Bookings</h2>
        <?php if ($bookings): ?>
            <table class="w-full border-collapse border border-gray-300 min-w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="border border-gray-300 p-2">ID</th>
                        <th class="border border-gray-300 p-2">Passenger Name</th>
                        <th class="border border-gray-300 p-2">Email</th>
                        <th class="border border-gray-300 p-2">Phone</th>
                        <th class="border border-gray-300 p-2">Train</th>
                        <th class="border border-gray-300 p-2">Seats</th>
                        <th class="border border-gray-300 p-2">Total Price</th>
                        <th class="border border-gray-300 p-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 p-2"><?= $booking['id'] ?></td>
                            <td class="border border-gray-300 p-2"><?= $booking['passenger_name'] ?></td>
                            <td class="border border-gray-300 p-2 text-sm"><?= $booking['passenger_email'] ?></td>
                            <td class="border border-gray-300 p-2"><?= $booking['passenger_phone'] ?></td>
                            <td class="border border-gray-300 p-2"><?= $booking['train_name'] ?></td>
                            <td class="border border-gray-300 p-2"><?= $booking['seats_booked'] ?></td>
                            <td class="border border-gray-300 p-2">RS <?= number_format($booking['total_price'], 0) ?></td>
                            <td class="border border-gray-300 p-2 text-sm">
                                <?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No bookings yet.</p>
        <?php endif; ?>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>