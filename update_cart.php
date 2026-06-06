<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $drug_id = $_POST['drug_id'];
    $quantity = $_POST['quantity'];

    if (isset($_SESSION['cart'][$drug_id])) {
        // Update the quantity in the cart
        $_SESSION['cart'][$drug_id]['quantity'] = $quantity;
        $_SESSION['cart'][$drug_id]['total_price'] = $_SESSION['cart'][$drug_id]['unit_price'] * $quantity;
    }

    // Redirect back to add_sale.php
    header('Location: add_sale.php');
    exit();
}
?>
