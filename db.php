<?php
// db.php

$host = 'localhost'; // Database host
$db = 'pms_db';      // Database name
$user = 'root';      // Database username (change if necessary)
$pass = '';          // Database password (change if necessary)

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: Set the character set to UTF-8
    $pdo->exec("SET NAMES 'utf8'");

    // Uncomment the line below to check connection success
    // echo "Connected successfully to the database $db";

} catch (PDOException $e) {
    // Handle connection error
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
