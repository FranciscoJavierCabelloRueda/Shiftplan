<!-- Designed by Francisco Javier Cabello Rueda on 20/05/2025  -->

<?php
// Start a new session or resume the existing session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Determine the database environment from the session
// Defaulting to 'test' if no environment has been set
$environment = $_SESSION['dbEnvironment'] ?? 'test';

require_once 'dbconfig.php';


// Set up the connection details for the current environment
$host = $dbDetails[$environment]['host'];
$port = $dbDetails[$environment]['port'];
$user = $dbDetails[$environment]['user'];
$password = $dbDetails[$environment]['password'];
$dbname = $dbDetails[$environment]['dbname'];

// Create a connection string using the environment-specific details
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Attempt to establish a connection to the PostgreSQL database
$conn = pg_connect($conn_string);

// Check if the connection was successful; if not, terminate the script with an error message
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Set client encoding to UTF-8 for the current connection
pg_set_client_encoding($conn, "UTF8");

// Setup the schema search path
pg_query($conn, "SET search_path TO sbuchp");
?>