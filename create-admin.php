<?php
require_once 'config/db.php';

$email = 'admin@trainbooking.com';
$password = 'admin123';

// 1. Hash the password securely
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    // 2. Insert into the database
    $stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashed_password]);
    
    echo "<h1 style='color: green;'>✅ Admin Account Created!</h1>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<br><a href='admin_login.php'>Go to Admin Login</a>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ Error</h1>";
    echo "Could not create admin (Email might already exist).<br>";
    echo "Error details: " . $e->getMessage();
}
?>