<!-- Designed by Francisco Javier Cabello Rueda on 19/05/2025  -->

<?php

require_once "config/langconfig.php";

// Manage the database environment configuration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dbEnvironment'])) {
        $_SESSION['dbEnvironment'] = $_POST['dbEnvironment'];
    }
}

if (!isset($_SESSION['dbEnvironment'])) {
    $_SESSION['dbEnvironment'] = 'test';
}

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
    <title>Schichtplan | Home </title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
    <link rel="stylesheet" href="assets/css/_content.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="content">
        <h2><?php echo $words['index']['home']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <p class="label"><?php echo $words['index']['welcome']; ?></p>
            <p class="label"><?php echo $words['index']['select_environment']; ?></p>
            <form method="post">
                <label>
                    <input type="radio" name="dbEnvironment" value="test" <?php echo $_SESSION['dbEnvironment'] === 'test' ? 'checked' : ''; ?>>
                    <?php echo $words['index']['test']; ?>
                </label>
                <label>
                    <input type="radio" name="dbEnvironment" value="dev" <?php echo $_SESSION['dbEnvironment'] === 'dev' ? 'checked' : ''; ?>>
                    <?php echo $words['index']['dev']; ?>
                </label>
                <label>
                    <input type="radio" name="dbEnvironment" value="prod" <?php echo $_SESSION['dbEnvironment'] === 'prod' ? 'checked' : ''; ?>>
                    <?php echo $words['index']['prod']; ?>
                </label>
                <button type="submit" style="margin-left: 10px">
                    <?php echo $words['index']['save_environment']; ?>
                </button>
            </form>
            <p><?php echo $words['index']['options']; ?></p>
            <div class="section">
                <button onclick="window.location.href='create-employee.php'">
                    <?php echo $words['index']['create_employee']; ?>
                </button>
                <br>
                <button onclick="window.location.href='remove-employee.php'">
                    <?php echo $words['index']['remove_employee']; ?>
                </button>
                <br>
                <button onclick="window.location.href='create-shift.php'">
                    <?php echo $words['index']['create_shift']; ?>
                </button>
                <br>
                <button onclick="window.location.href='remove-shift.php'">
                    <?php echo $words['index']['remove_shift']; ?>
                </button>
                <br>
                <button onclick="window.location.href='backup-database.php'">
                    <?php echo $words['index']['backup_database']; ?>
                </button>
                <br><br>
                <button onclick="window.location.href='health-database.php'">
                    <?php echo $words['index']['health_database']; ?>
                </button>
                <br><br>
            </div>
            <p class="label"><?php echo $words['index']['version']; ?></p>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>