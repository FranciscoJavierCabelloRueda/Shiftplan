<!-- Designed by Francisco Javier Cabello Rueda on 20/05/2025  -->

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
    <title><?php echo $words['create_shift']['title']; ?></title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_content.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="content">
        <h2><?php echo $words['create_shift']['heading']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <form method="post">
                <div class="section">
                    <p class="label"><?php echo $words['create_shift']['instructions']; ?></p>
                    <?php
                    // Query to fetch all employees ordered by last name
                    $sql = "SELECT * FROM t_mitarbeiter ORDER BY nachname;";
                    $result = pg_query($conn, $sql);

                    if ($result && pg_num_rows($result) > 0) {
                        echo '<table class="employee-table">'; // Start the HTML table for employees
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
                        echo '</table>'; // End table
                    } else {
                        // No employees found in the database
                        echo "<div class='section'><p class='error-color'>{$words['create_shift']['no_employees']}</p></div>";
                    }
                    ?>
                </div>
                <div class="section">
                    <span>
                        <?php echo $words['create_shift']['start_label']; ?>
                        <input type="date" class="textfield_shift" name="date" value="<?php echo date("Y-m-d"); ?>" required>
                    </span>
                    <span>
                        <?php echo $words['create_shift']['end_label']; ?>
                        <input type="date" class="textfield_shift" name="dateEnd" value="<?php echo date("Y-m-d"); ?>" required>
                    </span>
                </div>
                <div class="section">
					<table class="employee-table" style="width: 100%; border-collapse: collapse; text-align: center;">
                        <tr>
                            <td style="width: 25%; padding: 10px; text-align: center; vertical-align: middle;">
                                <input type="radio" class="employee-checkbox ff" name="schichtart" value="ff" required>
                                <label for="ff"><?php echo $words['create_shift']['label_ff']; ?></label>
                            </td>
                            <td style="width: 25%; padding: 10px; text-align: center; vertical-align: middle;">
                                <input type="radio" class="employee-checkbox ss" name="schichtart" value="ss" required>
                                <label for="ss"><?php echo $words['create_shift']['label_ss']; ?></label>
                            </td>
                            <td style="width: 25%; padding: 10px; text-align: center; vertical-align: middle;">
                                <input type="radio" class="employee-checkbox nn" name="schichtart" value="nn" required>
                                <label for="nn"><?php echo $words['create_shift']['label_nn']; ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 25%; padding: 10px; text-align: center; vertical-align: middle;">
                                <input type="radio" class="employee-checkbox fs" name="schichtart" value="fs" required>
                                <label for="fs"><?php echo $words['create_shift']['label_fs']; ?></label>
                            </td>
                            <td style="width: 25%; padding: 10px; text-align: center; vertical-align: middle;">
                                <input type="radio" class="employee-checkbox sn" name="schichtart" value="sn" required>
                                <label for="sn"><?php echo $words['create_shift']['label_sn']; ?></label>
                            </td>
                            <td style="width: 25%; padding: 10px; text-align: center; vertical-align: middle;">
                                <input type="radio" class="employee-checkbox nx" name="schichtart" value="nx" required>
                                <label for="nx"><?php echo $words['create_shift']['label_nx']; ?></label>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="section">
                    <input type="submit" name="submit_btn" class="button-primary" value="<?php echo $words['create_shift']['submit_button']; ?>">
                </div>
            </form>
            <?php
            // Process the form if submit button is pressed
            if (isset($_POST['submit_btn'])) {
                // Gather inputs from the form
                $schicht = $_POST['schichtart'];
                $schichtBuchstabe = str_split($schicht); // Split the shift type into individual characters
                $schichtvar = ""; // Initialize variable for shift pattern
                $date = strtotime($_POST['date']); // Convert start date to timestamp
                $dateEnd = strtotime($_POST['dateEnd']); // Convert end date to timestamp
                $datediff = round(($dateEnd - $date) / (60 * 60 * 24)) + 1; // Calculate number of days (inclusive)

                // Verify start date is earlier than or equal to end date
                if ($date <= $dateEnd) {
                    // Check if any employees are selected
                    if (isset($_POST['mitarbeiter'])) {
                        $mitarbeiter = $_REQUEST['mitarbeiter']; // Retrieve selected employees

                        foreach ($mitarbeiter as $name) {
                            // If only one day difference then insert a single shift record
                            if ($datediff == 1) {
                                schichtplanInsert(date('Y-m-d', $date), $schichtBuchstabe[0], $name);
                            } else {
                                // Build the shift pattern for multiple days
                                if ($schichtvar == "") {
                                    $fill = array(
                                        "ff" => "ffssnnxxxx",
                                        "ss" => "ssnnxxxx",
                                        "nn" => "nnxxxx",
                                        "fs" => "fssnnxxxx",
                                        "sn" => "snnxxxx",
                                        "nx" => "nxxxx"
                                    );
                                    // Define a repeating sequence for remaining days
                                    $reihenfolge = array("f", "f", "s", "s", "n", "n", "x", "x", "x", "x");

                                    $fillBuchstabe = str_split($fill[$schicht]); // Split selected pattern into characters
                                    if ($datediff <= sizeof($fillBuchstabe)) {
                                        // Build the shift pattern for a short range
                                        for ($i = 0; $i < $datediff; $i++) {
                                            $schichtvar .= $fillBuchstabe[$i];
                                        }
                                    } else {
                                        // Build the shift pattern up to the length of the fill pattern
                                        for ($i = 0; $i < sizeof($fillBuchstabe); $i++) {
                                            $schichtvar .= $fillBuchstabe[$i];
                                        }
                                        $remaining = $datediff - sizeof($fillBuchstabe); // Calculate remaining days after the fill pattern
                                        for ($i = 0; $i < $remaining; $i++) {
                                            $schichtvar .= $reihenfolge[$i % sizeof($reihenfolge)]; // Cycle through the repeating sequence
                                        }
                                    }
                                }
                                $schichtvarBuchstabe = str_split($schichtvar); // Split the final shift sequence into characters
                                // Insert a shift record for each day in the date range
                                for ($i = 0; $i < $datediff; $i++) {
                                    $newdate = strtotime("+" . $i . " day", $date);
                                    // Insert only if the shift character is not 'x'
                                    if ($schichtvarBuchstabe[$i] != "x") {
                                        schichtplanInsert(date('Y-m-d', $newdate), $schichtvarBuchstabe[$i], $name);
                                    }
                                }
                            }
                        }
                    } else {
                        // Display error if no employee is selected
                        echo "<div class='section'><p class='error-color'>{$words['create_shift']['no_employee_selected']}</p></div>";
                        return;
                    }
                } else {
                    // Display error if start date is after end date
                    echo "<div class='section'><p class='error-color'>{$words['create_shift']['invalid_date_range']}</p></div>";
                    return;
                }

                // Create a string of selected employee names for display
                $namestr = implode(", ", array_map('htmlspecialchars', $mitarbeiter));
                echo "<div class='section'><p class='success-color'>{$words['create_shift']['success']} $namestr {$words['create_shift']['success_end']}</p></div>";
            }

            // Function to insert shift data into the database
            function schichtplanInsert($date, $schicht, $name) {
                global $conn;
                $maxIdQuery = "SELECT MAX(_id) FROM t_schichtplan";
                $maxIdResult = pg_query($conn, $maxIdQuery);
                $maxIdRow = pg_fetch_row($maxIdResult);
                $nextId = $maxIdRow[0] + 1; // Increment the maximum id found

                // SQL query for inserting a new shift record
                $query = "INSERT INTO t_schichtplan (_id, _datum, _schicht, _name) VALUES ($1, $2, $3, $4)";
                $result = pg_query_params($conn, $query, array($nextId, $date, $schicht, $name));

                if (!$result) {
                    echo "An error occurred: " . pg_last_error($conn);
                }
            }
            ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>