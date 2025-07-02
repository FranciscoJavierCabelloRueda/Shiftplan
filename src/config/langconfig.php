<?php
// Start session for language handling
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Load language from the .ini file
$language = $_SESSION['lang'] ?? 'en'; // Default to English if not set
$file     = file_exists("languages/$language.ini") ? "languages/$language.ini" : "languages/en.ini";
$words    = parse_ini_file($file, true);
?>