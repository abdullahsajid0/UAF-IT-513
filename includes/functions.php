<?php
/**
 * HELPER FUNCTIONS FOR TRAIN TICKET SYSTEM
 * Easy-to-understand utility functions
 */

/**
 * Format price in Pakistani Rupees
 */
function formatPrice($price) {
    return 'RS ' . number_format($price, 2);
}

/**
 * Format date in readable format
 */
function formatDate($date) {
    return date('d M, Y', strtotime($date));
}

/**
 * Calculate journey duration
 */
function calculateDuration($departure, $arrival) {
    try {
        $depart = DateTime::createFromFormat('H:i:s', $departure);
        if (!$depart) $depart = DateTime::createFromFormat('H:i', $departure);
        
        $arrive = DateTime::createFromFormat('H:i:s', $arrival);
        if (!$arrive) $arrive = DateTime::createFromFormat('H:i', $arrival);
        
        if (!$depart || !$arrive) {
            return 'N/A';
        }
        
        if ($arrive < $depart) {
            $arrive->modify('+1 day');
        }
        
        $interval = $depart->diff($arrive);
        return $interval->format('%h hrs %i mins');
    } catch (Exception $e) {
        return 'N/A';
    }
}

/**
 * Generate a random booking reference
 */
function generateBookingReference() {
    return 'TKT' . strtoupper(uniqid());
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
}

/**
 * Redirect to another page
 */
function redirect($page) {
    header("Location: " . $page);
    exit();
}

/**
 * Check if user is logged in (simple session check)
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) ? true : false;
}

?>
