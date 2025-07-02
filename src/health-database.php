<!-- Designed by Francisco Javier Cabello Rueda on 20/05/2025  -->

<?php
require_once "config/langconfig.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/rewe.png">
    <title><?php echo $words['health_database']['title']; ?></title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
    <link rel="stylesheet" href="assets/css/_content.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="content">
        <h2 class="section-title"><?php echo $words['health_database']['section_title']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <?php
            // Start session to handle database environment settings
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            // Retrieve current database environment; default to 'test'
            $environment = $_SESSION['dbEnvironment'] ?? 'test';

            require_once 'config/dbconnect.php';

            /*
            // Define configurations for 'test', 'dev', and 'prod' environments
            $dbConfigs = [
                'test' => [
                    'host'     => 'hcvm101953.linux.risnet.de',
                    'port'     => '5432',
                    'user'     => 'dbpo_sbuchuser',
                    'dbname'   => 'sbuch_tst',
                    'password' => 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN',
                ],
                'dev' => [
                    'host'     => 'hcvm102705.linux.risnet.de',
                    'port'     => '5432',
                    'user'     => 'dbpo_sbuchuser',
                    'dbname'   => 'sbuch_dev',
                    'password' => 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN',
                ],
                'prod' => [
                    'host'     => 'vvm10693.dc.rewe.local',
                    'port'     => '5432',
                    'user'     => 'SBuchuser_dbo',
                    'dbname'   => 'SBuchP',
                    'password' => 'VhhDETgW9gXU',
                ],
            ];
            */

            // Loop through each environment and test the database connection
            foreach (['test', 'dev', 'prod'] as $env) {
                #$config = $dbConfigs[$env];
                $config = $dbDetails[$env];

                // Create connection string for the current environment
                $connString = "host=".$config['host']." port=".$config['port']." dbname=".$config['dbname']." user=".$config['user']." password=".$config['password']." connect_timeout=2";

                // Attempt to connect to the PostgreSQL database
                $conn = pg_connect($connString);

                // Display connection status
                echo '<div class="section">';
                if ($conn) {
                    echo "<div class='status-box alert-success'>{$words['health_database']['success']} <b>{$env}</b> {$words['health_database']['db_connection']}</div>";
                    pg_close($conn);
                } else {
                    echo "<div class='status-box alert-danger'>{$words['health_database']['error']} <b>{$env}</b> {$words['health_database']['db_connection']}!</div>";
                    error_log("Error connecting to the {$env} database.");
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>