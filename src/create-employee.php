<!-- Designed by Francisco Javier Cabello Rueda on 19/05/2025  -->
 
<?php
require_once "config/dbconnect.php";
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
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/rewe.png"/>
    <title><?php echo $words['create_employee']['title']; ?></title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_content.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="content">
        <h2><?php echo $words['create_employee']['heading']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <form method="post">
                <div class="section">
                    <!-- Employee creation instructions -->
                    <p class="label"><?php echo $words['create_employee']['instructions']; ?></p>
                    <br>
                </div>
                <div class="section">
                    <input type="text" placeholder="<?php echo $words['create_employee']['firstname_placeholder']; ?>" 
                           name="firstname" class="textfield" required>
                    <input type="text" placeholder="<?php echo $words['create_employee']['lastname_placeholder']; ?>" 
                           name="lastname" class="textfield" required>
                </div>
                <div class="section">
                    <input type="submit" name="submit_btn" class="button-primary" 
                           value="<?php echo $words['create_employee']['submit_button']; ?>">
                </div>
            </form>
            
            <?php
            // Check if the form was submitted
            if (isset($_POST['submit_btn'])) {
                $firstname = $_POST['firstname'];
                $lastname = $_POST['lastname'];
                createEmployee($firstname, $lastname);
            }

            // Function to create a new employee
            function createEmployee($firstname, $lastname) {
                global $conn;
                
                // Get the maximum employee ID value from the database table
                $maxQuery = "SELECT MAX(ma_inc) FROM t_mitarbeiter";
                $maxResult = pg_query($conn, $maxQuery);

                if (!$maxResult) {
                    echo "<div class='section'><p class='error-color'>" 
                         . $GLOBALS['words']['create_employee']['max_error'] 
                         . pg_last_error($conn) . "</p></div>";
                    return;
                }

                // Increment the maximum employee ID to generate a new ID
                $maxRow = pg_fetch_row($maxResult);
                $newMaInc = $maxRow[0] + 1;
                
                // Insert new employee into the table
                $query = "INSERT INTO t_mitarbeiter (vorname, nachname, ma_inc) VALUES ($1, $2, $3)";
                $result = pg_query_params($conn, $query, array($firstname, $lastname, $newMaInc));

                if (!$result) {
                    echo "<div class='section'><p class='error-color'>" 
                         . $GLOBALS['words']['create_employee']['insert_error'] 
                         . pg_last_error($conn) . "</p></div>";
                } else {
                    // Display success message using formatted string from language file
                    echo "<div class='section'><p class='success-color'>" 
                         . sprintf($GLOBALS['words']['create_employee']['success_message'], $firstname, $lastname)
                         . "</p></div>";
                }
            }
            ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>