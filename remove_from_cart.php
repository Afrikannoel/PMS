<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $drug_id = $_POST['drug_id'];

    if (isset($_SESSION['cart'][$drug_id])) {
        // Remove the drug from the cart
        unset($_SESSION['cart'][$drug_id]);
    }

    // Redirect back to add_sale.php
    header('Location: add_sale.php');
    exit();
}
?>
