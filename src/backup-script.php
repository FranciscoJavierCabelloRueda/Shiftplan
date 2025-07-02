<!-- Designed by Francisco Javier Cabello Rueda on 20/05/2025  -->

<?php
require_once "config/langconfig.php";

// Fetch the selected databases
$dbSource = $_POST['dbSource'] ?? 'test';
$dbTarget = $_POST['dbTarget'] ?? 'test';

// Define connection details based on selections
if ($dbSource === 'test') {
    $dbHostSource = 'hcvm101953.linux.risnet.de';
    $dbPortSource = '5432';
    $dbNameSource = 'sbuch_tst';
    $dbUserSource = 'dbpo_sbuchuser';
    $dbPasswordSource = 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN';
} elseif ($dbSource === 'prod') {
    $dbHostSource = 'prod_host';
    $dbPortSource = '5432';
    $dbNameSource = 'prod_name';
    $dbUserSource = 'prod_user';
    $dbPasswordSource = 'prod_pass';
}

if ($dbTarget === 'test') {
    $dbHostTarget = 'hcvm101953.linux.risnet.de';
    $dbPortTarget = '5432';
    $dbNameTarget = 'sbuch_tst';
    $dbUserTarget = 'dbpo_sbuchuser';
    $dbPasswordTarget = 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN';
} elseif ($dbTarget === 'dev') {
    $dbHostTarget = 'hcvm102705.linux.risnet.de';
    $dbPortTarget = '5432';
    $dbNameTarget = 'sbuch_dev';
    $dbUserTarget = 'dbpo_sbuchuser';
    $dbPasswordTarget = 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN';
}

// Function to send updates to the client
function sendUpdate($message, $color) {
    echo "<span style='color:$color;'>$message</span><br>";
    echo str_repeat(' ', 4096); // Ensure immediate sending
    flush();
    usleep(500000); // Wait for 0.5 seconds between sends
}

// Set headers for content-type and cache-control
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

if (!isset($_POST['dbSource']) || !isset($_POST['dbTarget'])) {
    sendUpdate($words['backup']['select_databases'], "#B6424C");
    exit();
}

if ($dbSource == $dbTarget) {
    sendUpdate($words['backup']['cannot_export_self'], "#B6424C");
    exit();
}

// Connect to the source database
$connSourceString = "host=$dbHostSource port=$dbPortSource dbname=$dbNameSource user=$dbUserSource password=$dbPasswordSource";
$connSource = pg_connect($connSourceString);

if (!$connSource) {
    sendUpdate($words['backup']['connect_error_source'], "#B6424C");
    error_log($words['backup']['connect_error_source']);
    exit();
}

sendUpdate($words['backup']['connect_success_source'], "#000000");

// Connect to the target database
$connTargetString = "host=$dbHostTarget port=$dbPortTarget dbname=$dbNameTarget user=$dbUserTarget password=$dbPasswordTarget";
$connTarget = pg_connect($connTargetString);

if (!$connTarget) {
    sendUpdate($words['backup']['connect_error_target'], "#B6424C");
    error_log($words['backup']['connect_error_target']);
    exit();
}

sendUpdate($words['backup']['connect_success_target'], "#000000");

// Create the schema sbuchp if it does not exist
$createSchemaQuery = "CREATE SCHEMA IF NOT EXISTS sbuchp";
$result = pg_query($connTarget, $createSchemaQuery);
if (!$result) {
    sendUpdate($words['backup']['schema_error'] . pg_last_error($connTarget), "#B6424C");
    exit();
}

sendUpdate($words['backup']['schema_success'], "#006400");

// Set the search path to the schema sbuchp
pg_query($connTarget, "SET search_path TO sbuchp");

// Remove all tables from the sbuchp schema in the target database
$dropTablesQuery = "
    DO $$ DECLARE r RECORD;
    BEGIN
        FOR r IN (SELECT tablename FROM pg_tables WHERE schemaname = 'sbuchp') LOOP
            EXECUTE 'DROP TABLE IF EXISTS sbuchp.' || quote_ident(r.tablename) || ' CASCADE';
        END LOOP;
    END $$;";
$result = pg_query($connTarget, $dropTablesQuery);
if (!$result) {
    sendUpdate($words['backup']['drop_tables_error'] . pg_last_error($connTarget), "#B6424C");
    pg_close($connTarget);
    exit();
}

sendUpdate($words['backup']['drop_tables_success'], "#006400");

// Begin a transaction
pg_query($connTarget, "BEGIN");

// Query all tables from the sbuchp schema in the source database
$tablesResult = pg_query($connSource, "SELECT table_name FROM information_schema.tables WHERE table_schema = 'sbuchp'");
if (!$tablesResult) {
    sendUpdate($words['backup']['retrieve_tables_error'], "#B6424C");
    pg_close($connSource);
    pg_close($connTarget);
    exit();
}

sendUpdate($words['backup']['retrieve_tables_success'], "#000000");

// Process each table from the source database
while ($tableRow = pg_fetch_assoc($tablesResult)) {
    $tableName = $tableRow['table_name'];

    sendUpdate($words['backup']['processing_table'] . $tableName, "#000000");

    // Query primary keys for the current table
    $pkQuery = "
        SELECT a.attname AS column_name
        FROM pg_constraint AS c
        JOIN pg_attribute AS a ON a.attnum = ANY(c.conkey) AND a.attrelid = c.conrelid
        WHERE c.conrelid = 'sbuchp.$tableName'::regclass AND c.contype = 'p'";
    $pkResult = pg_query($connSource, $pkQuery);
    if (!$pkResult) {
        sendUpdate($words['backup']['retrieve_pk_error'] . $tableName, "#B6424C");
        continue;
    }

    $primaryKeys = [];
    while ($pkRow = pg_fetch_assoc($pkResult)) {
        $primaryKeys[] = $pkRow['column_name'];
    }

    // Query foreign keys for the current table
    $foreignKeysMap = [];
    $fkQuery = "
        SELECT c.conname AS constraint_name, a.attname AS column_name,
               cf.relname AS foreign_table, af.attname AS foreign_column
        FROM pg_constraint AS c
        JOIN pg_attribute AS a ON a.attnum = ANY(c.conkey) AND a.attrelid = c.conrelid
        JOIN pg_class AS cf ON c.confrelid = cf.oid
        JOIN pg_attribute AS af ON af.attnum = ANY(c.confkey) AND af.attrelid = c.confrelid
        WHERE c.conrelid = 'sbuchp.$tableName'::regclass AND c.contype = 'f'";
    $fkResult = pg_query($connSource, $fkQuery);
    if (!$fkResult) {
        sendUpdate($words['backup']['retrieve_fk_error'] . $tableName, "#B6424C");
        continue;
    }

    while ($fkRow = pg_fetch_assoc($fkResult)) {
        $foreignKeysMap[$fkRow['column_name']][] = "\"{$fkRow['column_name']}\" REFERENCES sbuchp.\"{$fkRow['foreign_table']}\"(\"{$fkRow['foreign_column']}\")";
    }

    // Query column details including type and constraints
    $columnsQuery = "
        SELECT column_name, data_type, character_maximum_length, numeric_precision, numeric_scale, is_nullable
        FROM information_schema.columns
        WHERE table_schema = 'sbuchp' AND table_name = '$tableName'";
    $columnsResult = pg_query($connSource, $columnsQuery);
    if (!$columnsResult) {
        sendUpdate($words['backup']['retrieve_columns_error'] . $tableName, "#B6424C");
        continue;
    }

    $columns = [];
    while ($columnRow = pg_fetch_assoc($columnsResult)) {
        $columnName = $columnRow['column_name'];
        $columnType = strtoupper($columnRow['data_type']);
        $maxLength = $columnRow['character_maximum_length'];
        $numericPrecision = $columnRow['numeric_precision'];
        $numericScale = $columnRow['numeric_scale'];
        $isNullable = $columnRow['is_nullable'] === 'YES' ? '' : ' NOT NULL';
        $isPrimaryKey = in_array($columnName, $primaryKeys) ? ' PRIMARY KEY' : '';

        $typeDetails = $columnType;
        if (strpos($columnType, 'CHAR') !== false && $maxLength) {
            $typeDetails .= "($maxLength)";
        } elseif (strpos($columnType, 'NUMERIC') !== false && $numericPrecision) {
            $typeDetails .= $numericScale ? "($numericPrecision, $numericScale)" : "($numericPrecision)";
        }

        $foreignKeyStr = isset($foreignKeysMap[$columnName]) ? implode(' ', $foreignKeysMap[$columnName]) : '';

        $columns[] = "\"$columnName\" $typeDetails$isPrimaryKey$isNullable $foreignKeyStr";
    }

    // Create table in target database
    if (!empty($columns)) {
        $createTableQuery = "CREATE TABLE sbuchp.\"$tableName\" (" . implode(', ', $columns) . ")";
        $result = pg_query($connTarget, $createTableQuery);
        if (!$result) {
            sendUpdate($words['backup']['create_table_error'] . $tableName . ": " . pg_last_error($connTarget), "#B6424C");
            pg_query($connTarget, "ROLLBACK");
            exit();
        }

        sendUpdate($words['backup']['table_created'] . $tableName, "#006400");
    } else {
        sendUpdate($words['backup']['no_columns'] . $tableName, "#B6424C");
        continue;
    }

    // Source data from source database to target database
    $query = "SELECT * FROM sbuchp.\"$tableName\"";
    $result = pg_query($connSource, $query);
    if (!$result) {
        sendUpdate($words['backup']['source_data_error'] . $tableName, "#B6424C");
        continue;
    }

    while ($row = pg_fetch_assoc($result)) {
        $columnNames = implode(', ', array_keys($row));
        $values = implode(', ', array_map(fn($value) => is_null($value) ? 'NULL' : ("'" . pg_escape_string($connTarget, $value) . "'"), array_values($row)));

        $insertQuery = "INSERT INTO sbuchp.\"$tableName\" ($columnNames) VALUES ($values)";
        $insertResult = pg_query($connTarget, $insertQuery);
        if (!$insertResult) {
            sendUpdate($words['backup']['insert_error'] . $tableName . ": " . pg_last_error($connTarget), "#B6424C");
            exit();
        }
    }

    sendUpdate($words['backup']['data_inserted'] . $tableName, "#006400");
}

// Commit the transaction
pg_query($connTarget, "COMMIT");

sendUpdate($words['backup']['transfer_complete'], "#008000");

// Close database connections
pg_close($connSource);
pg_close($connTarget);

?>