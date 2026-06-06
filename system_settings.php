<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Database connection
require 'db.php';

// You can add logic to fetch and update settings here

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>System Settings</h1>
    <form method="post">
        <!-- Add your settings fields here -->
        <input type="text" name="setting_name" placeholder="Setting Name" required>
        <input type="text" name="setting_value" placeholder="Setting Value" required>
        <button type="submit">Update Settings</button>
    </form>
</body>
</html>
