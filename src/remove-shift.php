<!-- Designed by Francisco Javier Cabello Rueda on 19/05/2025  -->
 
<?php
require_once "config/dbconnect.php";
require_once "config/langconfig.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/rewe.png"/>
    <title><?php echo $words['remove_shift']['title']; ?></title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_content.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="content">
        <h2><?php echo $words['remove_shift']['heading']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <form method="post">
                <div class="section">
                    <p class="label"><?php echo $words['remove_shift']['instructions']; ?></p>
                    <?php
                    // Query to fetch all employees ordered by last name
                    $sql = "SELECT * FROM t_mitarbeiter ORDER BY nachname;";
                    $result = pg_query($conn, $sql);

                    if ($result && pg_num_rows($result) > 0) {
                        echo '<table class="employee-table">'; // Start employee table
                        $counter = 0;

                        while ($row = pg_fetch_assoc($result)) {
                            if ($counter % 4 == 0) { // Start a new row every four employees
                                if ($counter > 0) {
                                    echo '</tr>'; // Close previous row if applicable
                                }
                                echo '<tr>'; // Start new table row
                            }

                            echo '<td>'; // Start table cell
                            echo "<input type='checkbox' name='mitarbeiter[]' value=\"" . htmlspecialchars($row["nachname"]) . "\" class='employee-checkbox'>";
                            echo "<label class='employee-label'>" . htmlspecialchars($row["nachname"]) . ", " . htmlspecialchars($row["vorname"]) . "</label>";
                            echo '</td>'; // End table cell

                            $counter++;
                        }
                        echo '</tr>'; // Close last row
                        echo '</table>'; // End employee table
                    } else {
                        // No employees found in the database
                        echo "<div class='section'><p class='error-color'>{$words['remove_shift']['no_employees']}</p></div>";
                    }
                    ?>
                </div>
                <div class="section">
                    <span>
                        <?php echo $words['remove_shift']['start_label']; ?> 
                        <input type="date" class="textfield_shift" name="date" value="<?php echo date("Y-m-d"); ?>" required>
                    </span>
                    <span>
                        <?php echo $words['remove_shift']['end_label']; ?> 
                        <input type="date" class="textfield_shift" name="dateEnd" value="<?php echo date("Y-m-d"); ?>" required>
                    </span>
                </div>
                <div class="section">
                    <input type="submit" name="submit_btn" class="button-primary" value="<?php echo $words['remove_shift']['submit_button']; ?>">
                </div>
            </form>
            <?php
            // Check if the submit button was clicked
            if (isset($_POST['submit_btn'])) {
                // Get the dates from form input
                $date = strtotime($_POST['date']);
                $dateEnd = strtotime($_POST['dateEnd']);

                // Check if the end date is the same or later than the start date
                if ($date <= $dateEnd) {
                    // Check if at least one employee checkbox was selected
                    if (isset($_POST['mitarbeiter'])) {
                        $mitarbeiter = $_REQUEST['mitarbeiter'];
                        // Loop through each selected employee and delete shift records within the date range
                        foreach ($mitarbeiter as $name) {
                            schichtplanDelete($name, date('Y-m-d', $date), date('Y-m-d', $dateEnd));
                        }
                    } else {
                        // Error message if no employee was selected
                        echo "<div class='section'><p class='error-color'>{$words['remove_shift']['no_employee_selected']}</p></div>";
                        return;
                    }
                } else {
                    // Error message if the start date is after the end date
                    echo "<div class='section'><p class='error-color'>{$words['remove_shift']['invalid_date_range']}</p></div>";
                    return;
                }

                // Display success message with a list of deleted employees
                $namestr = implode(", ", array_map('htmlspecialchars', $mitarbeiter));
                if ($date == $dateEnd) {
                    echo "<div class='section'><p class='success-color'>{$words['remove_shift']['success']} $namestr {$words['remove_shift']['on']} " . date('d.m.y', $date) . " {$words['remove_shift']['removed_once']}</p></div>";
                } else {
                    echo "<div class='section'><p class='success-color'>{$words['remove_shift']['success']} $namestr {$words['remove_shift']['removed_range']} " . date('d.m.y', $date) . " {$words['remove_shift']['until']} " . date('d.m.y', $dateEnd) . ".</p></div>";
                }
            }

            // Function to delete shift records from the database
            function schichtplanDelete($name, $date, $dateEnd) {
                global $conn;
                $query = "DELETE FROM t_schichtplan WHERE _name = $1 AND _datum BETWEEN $2 AND $3;";
                
                // Execute deletion query with parameters
                $result = pg_query_params($conn, $query, array($name, $date, $dateEnd));
                if (!$result) {
                    // Display an error message if deletion fails
                    echo "An error occurred: " . pg_last_error($conn);
                }
            }
            ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>