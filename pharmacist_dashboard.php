<?php
// Ensure user is authenticated and has pharmacist role
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pharmacist') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Dashboard</title>
    <link rel="stylesheet" href="pharmacist_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Pharmacist Dashboard</h1>
        
        <div class="dashboard-grid">
        <!-- Add Sale Button -->
        <div class="dashboard-card">
                <h2>Add New Sale</h2>
                <p>Record a new sale in the system.</p>
                <a href="add_sale.php" class="btn">Add Sale</a>
            </div>    
        <!-- Manage Sales -->
        <div class="dashboard-card">
                <h2>Manage Sales</h2>
                <p>View, edit, or delete sales records.</p>
                <a href="manage_daily_sales.php" class="btn">Manage Sales</a>
            </div>
        <!-- Manage Customers -->
        <div class="dashboard-card">
                <h2>Manage Customers</h2>
                <p>Add new customers to the system.</p>
                <a href="add_customer.php" class="btn">Add Customer</a>
            </div>
             
          <!-- Logout -->
<div class="dashboard-card">
    <h2>Log Out</h2>
    <p>Click the button below to log out of your account.</p>
    <a href="logout.php" class="btn">Log Out</a>
</div>

        </div>
    </div>
</body>
</html>
