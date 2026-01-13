<?php
/**
 * BOOKING ACTION FILE
 * Handles the booking logic securely
 */

session_start(); // 1. Start Session

require_once '../config/db.php';
require_once '../includes/functions.php';

// 2. Security Check: Stop if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $train_id = (int)$_POST['train_id'];
        
        // 3. SECURITY FIX: Get Name/Email from Session, NOT POST
        // This ensures users can't book tickets for other people's emails
        $name = $_SESSION['user_name'];
        $email = $_SESSION['user_email'];
        
        $phone = sanitize($_POST['phone']);
        $age = (int)$_POST['age'];
        $seats = (int)$_POST['seats'];
        $journey_date = sanitize($_POST['journey_date']);
        $price = (float)$_POST['price'];
        $total_price = $price * $seats;

        // Validate data
        if ($seats < 1 || $seats > 5) {
            throw new Exception("Invalid number of seats");
        }

        if ($age < 1 || $age > 120) {
            throw new Exception("Invalid age");
        }

        // Check if train has enough seats
        $stmt = $pdo->prepare("SELECT seats_available FROM trains WHERE id = :id");
        $stmt->execute([':id' => $train_id]);
        $train = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$train) {
            throw new Exception("Train not found");
        }

        if ($train['seats_available'] < $seats) {
            throw new Exception("Not enough seats available");
        }

        // Start transaction
        $pdo->beginTransaction();

        // Insert booking
        $stmt = $pdo->prepare("
            INSERT INTO bookings (train_id, passenger_name, passenger_email, passenger_phone, age, seats_booked, total_price, journey_date)
            VALUES (:train_id, :name, :email, :phone, :age, :seats, :total_price, :journey_date)
        ");

        $stmt->execute([
            ':train_id' => $train_id,
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':age' => $age,
            ':seats' => $seats,
            ':total_price' => $total_price,
            ':journey_date' => $journey_date
        ]);

        // Update available seats
        $stmt = $pdo->prepare("
            UPDATE trains 
            SET seats_available = seats_available - :seats 
            WHERE id = :id
        ");

        $stmt->execute([
            ':seats' => $seats,
            ':id' => $train_id
        ]);

        // Commit transaction
        $pdo->commit();

        // 4. Redirect to My Tickets Page
        header("Location: ../my_tickets.php?success=booked");
        exit;

    } catch (Exception $e) {
        // Rollback on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Go back to booking page with error
        header("Location: ../book.php?train_id=$train_id&error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>