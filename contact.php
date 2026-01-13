<?php
$pageTitle = 'Contact Us';
require_once 'includes/header.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $message = sanitize($_POST['message']);
    
    if ($name && $email && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = "Thank you! Your message has been sent successfully. We'll respond within 24 hours.";
        } catch (Exception $e) {
            $error = "Error sending message. Please try again.";
        }
    } else {
        $error = "Please fill all fields";
    }
}
?>
    <main class="container mx-auto p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-2">Contact Us</h1>
        <p class="text-gray-600 mb-6">Have questions or feedback? Send us a message!</p>

        <section class="bg-white border border-gray-300 p-6">
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 text-sm rounded">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 text-sm rounded">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-gray-700 font-bold mb-1">Your Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-bold mb-1">Your Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label for="message" class="block text-gray-700 font-bold mb-1">Message:</label>
                    <textarea name="message" id="message" rows="5" placeholder="Write your message here..." required 
                        class="w-full border border-gray-400 p-2 rounded focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                    Send Message
                </button>
            </form>
        </section>
    </main>

<?php require_once 'includes/footer.php'; ?>