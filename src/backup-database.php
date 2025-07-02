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
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/rewe.png"/>
    <title><?php echo $words['backup_database']['title']; ?></title>
    <link rel="stylesheet" href="assets/css/_header.css">
    <link rel="stylesheet" href="assets/css/_content.css">
    <link rel="stylesheet" href="assets/css/_footer.css">
    <link rel="stylesheet" href="assets/css/_font.css">
    <style>
        .progress-container {
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
            height: 200px;
            overflow-y: scroll;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="content">
        <h2><?php echo $words['backup_database']['heading']; ?></h2>
        <div class="container drop-shadow-m">
            <div class="line"></div>
            <form method="post" id="backupForm">
                <div class="section">
                    <p class="label"><?php echo $words['backup_database']['instructions']; ?></p>
                    <br>
                </div>
                <div class="section">
                    <span>
                        <?php echo $words['backup_database']['source_label']; ?>
                        <input type="radio" class="employee-checkbox" name="dbSource" value="prod" required>
                        <label for="prod"><?php echo $words['backup_database']['label_prod']; ?></label>
                        <input type="radio" class="employee-checkbox" name="dbSource" value="test" required>
                        <label for="test"><?php echo $words['backup_database']['label_test']; ?></label>
                    </span>
                </div>
                <div class="section">
                    <span>
                        <?php echo $words['backup_database']['target_label']; ?>
                        <input type="radio" class="employee-checkbox" name="dbTarget" value="dev" required>
                        <label for="dev"><?php echo $words['backup_database']['label_dev']; ?></label>
                        <input type="radio" class="employee-checkbox" name="dbTarget" value="test" required>
                        <label for="test"><?php echo $words['backup_database']['label_test']; ?></label>
                    </span>
                </div>
                <div class="section">
                    <button type="button" class="button-primary" onclick="startBackup()">
                        <?php echo $words['backup_database']['button_run']; ?>
                    </button>
                </div>
            </form>
            <div class="progress-container" id="progress-container"></div>
        </div>
    </div>
    <?php include "footer.php"; ?>
    <script>
        // Get the HTML element where progress will be displayed
        const progressContainer = document.getElementById('progress-container');

        // Function to start the backup process
        function startBackup() {
            // Create a FormData object with the data from the form with id 'backupForm'
            const formData = new FormData(document.getElementById('backupForm'));
            // Create a new XMLHttpRequest object for making HTTP requests
            const xhr = new XMLHttpRequest();
            // Initialize a POST request to the backup script, and keep it asynchronous
            xhr.open('POST', 'backup-script.php', true);
            // Define a function to be called whenever the readyState changes
            xhr.onreadystatechange = function () {
                // If the request is in loading or completed state
                if (xhr.readyState === XMLHttpRequest.LOADING || xhr.readyState === XMLHttpRequest.DONE) {
                    // Append the response text to the progress container, replacing newline characters with <br/>
                    progressContainer.innerHTML += xhr.responseText.replace(/\n/g, '<br/>');
                    // Scroll to the bottom of the progress container to show the latest updates
                    progressContainer.scrollTop = progressContainer.scrollHeight;
                }
            };
            // Send the form data using the XMLHttpRequest
            xhr.send(formData);
        }
    </script>
</body>
</html>