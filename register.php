<?php
/**
 * USER REGISTRATION PAGE
 * Users can create new account with email and password
 */

session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        $error = ' Name, email, and password are required!';
    } elseif (strlen($password) < 6) {
        $error = ' Password must be at least 6 characters!';
    } elseif ($password !== $confirm_password) {
        $error = ' Passwords do not match!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = ' Invalid email format!';
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$email]);
        
        if ($check_stmt->rowCount() > 0) {
            $error = ' Email already registered! Try login instead.';
        } else {
            // Register new user
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
           try {
                if ($stmt->execute([$name, $email, $hashed_password, $phone])) {
                    $success = 'Registration successful! Redirecting to login...';
                    header('Refresh: 2; url=login.php');
                } else {
                    // This will print the exact database error on the screen
                    $errorInfo = $stmt->errorInfo();
                    $error = 'SQL Error: ' . $errorInfo[2]; 
                }
            } catch (PDOException $e) {
                $error = 'Database Error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration - Train Booking System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans">

    <div class="bg-white p-8 border border-gray-300 w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 text-sm">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-gray-700 font-bold mb-1">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name" 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <div>
                <label for="email" class="block text-gray-700 font-bold mb-1">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email" 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <div>
                <label for="phone" class="block text-gray-700 font-bold mb-1">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone (optional)" 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 font-bold mb-1">Password</label>
                <input type="password" id="password" name="password" required placeholder="Min 6 characters" 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1"> Password must be at least 6 characters long</p>
            </div>
            
            <div>
                <label for="confirm_password" class="block text-gray-700 font-bold mb-1">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter password" 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 mt-4">
                 REGISTER
            </button>
        </form>
        
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
            <a href="index.php" class="text-white rounded-lg bg-blue-600 p-2 hover:underline mx-2"> Home</a>
            <a href="login.php" class="text-white rounded-lg bg-blue-600 p-2 hover:underline mx-2"> Login</a>
        </div>
    </div>

</body>
</html>