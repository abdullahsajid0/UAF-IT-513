<?php
/**
 * USER LOGIN PAGE
 * Users can login with email and password
 */

session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required!';
    } else {
        // Check database
        $sql = "SELECT id, name, email, password  FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            $success = 'Login successful! Redirecting...';
            header("Location: login.php");
        } else {
            $error = 'Invalid email or password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login - Train Booking System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans">

    <div class="bg-white p-8 border border-gray-300 w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6">User Login</h2>
        
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
                <label for="email" class="block text-gray-700 font-bold mb-1">Email Address</label>
                <input type="email" id="email" name="email" required 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 font-bold mb-1">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 mt-2">
                 LOGIN
            </button>
        </form>
        
        <div class="text-center mt-6 pt-4 border-t border-gray-200 text-sm">
            <a href="index.php" class="text-blue-600 hover:underline mx-2"> Home</a> |
            <a href="register.php" class="text-blue-600 hover:underline mx-2"> Register Now</a>
        </div>
        
        <div class="mt-6 text-center text-xs text-gray-500 bg-gray-50 p-2 border border-gray-200">
            <p class="font-bold">Demo Account:</p>
            <p>Email: demo@example.com</p>
            <p>Password: demo123</p>
        </div>
    </div>

</body>
</html>