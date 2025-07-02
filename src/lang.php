<!-- Designed by Francisco Javier Cabello Rueda on 19/05/2025  -->
 
<?php
// Start or resume the current session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Set the session variable 'lang' to the value of the 'l' query parameter if available, otherwise default to 'en' (English)
$_SESSION['lang'] = $_GET['l'] ?? 'en';
// Get the previous page URL from the HTTP referrer
$previousPage = $_SERVER['HTTP_REFERER'] ?? './'; // Default to the root if referrer is not set
// Redirect the user to the previous page to maintain their navigation history
header("Location: $previousPage");
?>