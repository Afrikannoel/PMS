<?php
// Include database connection
include('db.php');

// Check if customer_id is provided in the URL
if (!isset($_GET['customer_id'])) {
    die("Customer ID not provided.");
}

// Get the customer ID from the URL
$customer_id = $_GET['customer_id'];

// Fetch customer details to display for confirmation
$customer_query = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :customer_id LIMIT 1");
$customer_query->execute(['customer_id' => $customer_id]);
$customer = $customer_query->fetch(PDO::FETCH_ASSOC);

// Check if the customer exists
if (!$customer) {
    die("Customer not found.");
}

// Handle deletion if the confirmation is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Prepare and execute the delete query
    $delete_query = $pdo->prepare("DELETE FROM customers WHERE customer_id = :customer_id");
    $delete_query->execute(['customer_id' => $customer_id]);

    // Redirect to the manage customers page after deletion
    header("Location: manage_customers.php?message=Customer deleted successfully.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Customer</title>
    <link rel="stylesheet" href="delete_customer.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Delete Customer</h1>
        <p>Are you sure you want to delete the customer <strong><?php echo htmlspecialchars($customer['full_name']); ?></strong>?</p>
        <form method="POST" action="">
            <button type="submit" class="btn delete-btn">Delete Customer</button>
            <a href="manage_customers.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>
