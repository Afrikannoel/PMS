<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admindb.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px; /* Space between buttons */
        }
        .button {
            padding: 15px 30px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF; /* Bootstrap primary color */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* Remove underline from link */
            text-align: center;
            transition: background-color 0.3s;
            width: 200px; /* Fixed button width */
        }
        .button:hover {
            background-color: #0056b3; /* Darker shade for hover effect */
        }
        .button:active {
            background-color: #004085; /* Even darker shade for active state */
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>You are logged in as <?php echo $_SESSION['role']; ?>.</p>

    <div class="button-container">
        <a href="create_user.php" class="button">Create New User</a>
        <a href="manage_users.php" class="button">Manage Users</a>
        <a href="add_drug.php" class="button">Add New Drug</a>
        <a href="manage_drugs.php" class="button">Manage Drugs</a>
        <a href="view_inventory.php" class="button">View Inventory Levels</a>
        <a href="view_sales.php" class="button">View Sales Reports</a>
        <a href="manage_suppliers.php" class="button">Manage Suppliers</a>
        <a href="system_settings.php" class="button">System Settings</a>
        <a href="logout.php" class="button" style="background-color: #dc3545;">Logout</a>
    </div>

</body>
</html>
