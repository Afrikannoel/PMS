<?php
session_start();
include 'db.php'; // Include the database connection

// Check if there is a receipt stored in the session
if (!isset($_SESSION['receipt'])) {
    header('Location: add_sale.php'); // Redirect to add_sale if no receipt exists
    exit();
}

// Check if customer ID and sale ID are provided
if (isset($_POST['customer_id']) && isset($_POST['sale_id'])) {
    $customerId = $_POST['customer_id'];
    $saleId = $_POST['sale_id']; // Get the sale ID from the form submission

    // Update the existing sale record with the customer ID
    $sql = "UPDATE sales SET customer_id = :customer_id WHERE sale_id = :sale_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':customer_id' => $customerId,
        ':sale_id' => $saleId
    ]);

    // Redirect to a confirmation page or show a success message after linking
    header('Location: receipt_page.php'); // Redirect to a receipt or confirmation page
    exit();
} else {
    echo 'Error: Customer ID or Sale ID not provided.';
    exit();
}
?>
