<?php
// Define connection details based on the selected environment
$dbDetails = [
    'prod' => [
        'host' => 'db-prod',
        'port' => '5432',
        'user' => 'SBuchuser_dbo',
        'password' => 'VhhDETgW9gXU',
        'dbname' => 'SBuchP',
    ],
    'test' => [
        'host' => 'db-test',
        'port' => '5432',
        'user' => 'dbpo_sbuchuser',
        'password' => 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN',
        'dbname' => 'sbuch_tst',
    ],
    'dev' => [
        'host' => 'db-dev',
        'port' => '5432',
        'user' => 'dbpo_sbuchuser',
        'password' => 'TST!BbLEN7VW744gBoRMxZsHEa4nNd5uo2AweKN',
        'dbname' => 'sbuch_dev',
    ],
];
?>