<!-- Designed by Francisco Javier Cabello Rueda on 20/05/2025  -->
 
<?php
require_once "config/dbconnect.php";
require_once "config/langconfig.php";

$errorMessage = ''; // Variable to store error message if no employee is selected

if (isset($_POST['submit_btn'])) { // Check if the submit button was clicked
    if (isset($_POST['mitarbeiter'])) { // Check if any employee checkbox was selected
        $employeeNames   = $_POST['mitarbeiter']; // Get list of selected employee last names
        $employeeString  = ""; // Initialize string for feedback

        foreach ($employeeNames as $employee) {
            if ($employeeString != "")
                $employeeString .= "," . $employee; // Concatenate names with a comma
            else
                $employeeString = $employee; // First name, no comma needed

            removeEmployee($employee); // Execute removal operation
        }

        // Redirect to the same page with success feedback in the URL
        header('Location:?success=' . urlencode($employeeString));
        exit();
    } else {
        $errorMessage = $words['remove_employee']['select_error']; // Set error message if no employee was selected
    }
}

// Function to remove an employee based on the provided last name
function removeEmployee($name) {
    global $conn;
    $query  = "DELETE FROM t_mitarbeiter WHERE nachname = $1"; // Prepare the SQL query to delete by last name
    $result = pg_query_params($conn, $query, array($name)); // Execute the query safely

    if (!$result) {
        echo $GLOBALS['words']['remove_employee']['execution_error'] . pg_last_error($conn); // Display error if query fails
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/rewe.png"/>
    <title><?php echo $words['remove_employee']['title']; ?></title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_content.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="content">
        <h2><?php echo $words['remove_employee']['heading']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <form method="post">
                <div class="section">
                    <p class="label"><?php echo $words['remove_employee']['instructions']; ?></p>
                </div>
                <div class="section">
                    <?php
                    // Query to get all employees ordered by last name
                    $sql    = "SELECT * FROM t_mitarbeiter ORDER BY nachname;";
                    $result = pg_query($conn, $sql);

                    if ($result && pg_num_rows($result) > 0) {
                        echo '<table class="employee-table">'; // Start employee table
                        $counter = 0; // Counter for employees per row

                        while ($row = pg_fetch_assoc($result)) {
                            if ($counter % 4 == 0) { // Start a new row every four employees
                                if ($counter > 0) {
                                    echo '</tr>'; // Close previous row if applicable
                                }
                                echo '<tr>'; // Start a new table row
                            }

                            echo '<td>'; // Start table cell
                            echo "<input type='checkbox' name='mitarbeiter[]' value=\"" . htmlspecialchars($row["nachname"]) . "\" class='employee-checkbox'>";
                            echo "<label class='employee-label'>" . htmlspecialchars($row["nachname"]) . ", " . htmlspecialchars($row["vorname"]) . "</label>";
                            echo '</td>'; // Close table cell

                            $counter++; // Increment counter
                        }
                        echo '</tr>'; // Close the last row
                        echo '</table>'; // End employee table
                    } else {
                        // No employees found in the database
                        echo "<div class='section'><p class='error-color'>{$words['remove_employee']['no_employees']}</p></div>";
                    }
                    ?>
                </div>
                <div class="section">
                    <input type="submit" name="submit_btn" class="button-primary" value="<?php echo $words['remove_employee']['submit_button']; ?>">
                </div>
            </form>
            <?php
            // Display error message if set
            if ($errorMessage) {
                echo "<div class='section'><p class='error-color'>$errorMessage</p></div>";
            }

            // Check if there is success feedback in the URL
            if (isset($_GET['success'])) {
                $array_success = explode(",", $_GET['success']); // Convert the comma-separated list to an array
                $employees     = implode(', ', array_map('htmlspecialchars', $array_success)); // Prepare names for safe display
                $counter       = count($array_success); // Count number of deleted employees

                if ($counter == 1) {
                    echo "<div class='section'><p class='success-color'>{$words['remove_employee']['single_success']}$employees{$words['remove_employee']['success_end']}</p></div>";
                } else {
                    echo "<div class='section'><p class='success-color'>{$words['remove_employee']['plural_success']}$employees{$words['remove_employee']['success_end_plural']}</p></div>";
                }
            }
            ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>