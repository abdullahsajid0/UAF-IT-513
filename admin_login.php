<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        header("Location: admin.php"); // Updated to match your previous file name
        exit;
    } else {
        $error = "❌ Invalid email or password";
    }
}

// Include header AFTER login logic
$pageTitle = 'Admin Login';
require_once 'includes/header.php';
?>

<main class="container mx-auto p-6 flex justify-center items-center mt-10">
    <div class="w-full max-w-sm bg-white p-8 border border-gray-300">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Admin Login</h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-bold mb-1">Email</label>
                <input type="email" name="email" placeholder="admin@example.com" required 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-1">Password</label>
                <input type="password" name="password" placeholder="Password" required 
                    class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 mt-2">
                Login
            </button>
        </form>
        
        <div class="text-center mt-6 text-sm text-gray-500 bg-gray-50 p-2 border border-gray-200">
            <p>Demo Credentials:</p>
            <p><strong>admin@trainbooking.com</strong></p>
            <p><strong>admin123</strong></p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>