<?php
session_start();
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $drugId = $_POST['drug_id'];
    $quantity = $_POST['quantity'][$drugId]; // Get quantity based on the drug_id

    // Check drug availability
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE drug_id = :drug_id");
    $stmt->execute([':drug_id' => $drugId]);
    $drug = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($drug) {
        if ($drug['quantity_in_stock'] >= $quantity) {
            // Drug is available, add to cart
            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $totalPrice = $quantity * $drug['selling_price'];

            // Check if the drug already exists in the cart
            if (isset($cart[$drugId])) {
                // If the drug is already in the cart, update its quantity and total price
                $cart[$drugId]['quantity'] += $quantity;
                $cart[$drugId]['total_price'] += $totalPrice;
            } else {
                // Add new drug to the cart
                $cart[$drugId] = [
                    'drug_id' => $drug['drug_id'],
                    'drug_name' => $drug['drug_name'],
                    'quantity' => $quantity,
                    'unit_price' => $drug['selling_price'],
                    'total_price' => $totalPrice
                ];
            }

            // Update the session cart
            $_SESSION['cart'] = $cart;

            // Update inventory
            $updateStmt = $pdo->prepare("UPDATE drugs SET quantity_in_stock = quantity_in_stock - :quantity WHERE drug_id = :drug_id");
            $updateStmt->execute([
                ':quantity' => $quantity,
                ':drug_id' => $drugId
            ]);

            // Redirect back to add_sale page
            header('Location: add_sale.php'); 
            exit(); // Always call exit after header redirection
        } else {
            // Drug is not available
            echo "<script>alert('Not enough stock available for this drug.');</script>";
            header('Location: add_sale.php');
            exit(); // Ensure exit after header to stop script execution
        }
    } else {
        // Drug not found in the database
        echo "<script>alert('Drug not found.');</script>";
        header('Location: add_sale.php');
        exit(); // Ensure exit after header to stop script execution
    }
}
?>
