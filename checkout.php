<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: add_sale.php'); // Redirect if cart is empty
    exit();
}

// Initialize variables
$cart = $_SESSION['cart'];
$totalAmount = 0;

// Calculate total amount
foreach ($cart as $item) {
    $totalAmount += $item['quantity'] * $item['unit_price'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paymentMethod = $_POST['payment_method'];
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0.0; // Ensure discount is a float
    $totalAmountAfterDiscount = max($totalAmount - $discount, 0); // Prevent negative total

    // Insert sale into the database
    $userId = $_SESSION['user_id']; // Assuming user ID is stored in session
    $stmt = $pdo->prepare("INSERT INTO sales (user_id, total_amount, payment_method, discount) VALUES (:user_id, :total_amount, :payment_method, :discount)");
    $stmt->execute([
        ':user_id' => $userId,
        ':total_amount' => $totalAmountAfterDiscount,
        ':payment_method' => $paymentMethod,
        ':discount' => $discount
    ]);

    // Get the last inserted sale ID
    $saleId = $pdo->lastInsertId();

    // Insert each item into the sale_items table and update inventory
    foreach ($cart as $item) {
        $salesItemStmt = $pdo->prepare("INSERT INTO sale_items (sale_id, drug_id, quantity, unit_price) VALUES (:sale_id, :drug_id, :quantity, :unit_price)");
        $salesItemStmt->execute([
            ':sale_id' => $saleId,
            ':drug_id' => $item['drug_id'],
            ':quantity' => $item['quantity'],
            ':unit_price' => $item['unit_price']
        ]);

        // Update inventory after sale
        $updateInventoryStmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - :quantity WHERE drug_id = :drug_id");
        $updateInventoryStmt->execute([
            ':quantity' => $item['quantity'],
            ':drug_id' => $item['drug_id']
        ]);
    }

    // Clear the cart
    unset($_SESSION['cart']);
    
    // Store transaction details in session
    $_SESSION['receipt'] = [
        'cart' => $cart,
        'total_amount' => $totalAmountAfterDiscount,
        'payment_method' => $paymentMethod,
        'discount' => $discount
    ];

    header('Location: receipt.php'); // Redirect to the receipt page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="add_sale.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <form action="" method="POST">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="cash">Cash</option>
                <option value="credit_card">Credit Card</option>
            </select>
            <label for="discount">Discount:</label>
            <input type="number" name="discount" id="discount" min="0" value="0" step="0.01">
            <button type="submit">Confirm Transaction</button>
        </form>

        <h2>Cart Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($cart as $item): 
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['drug_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($item['unit_price'], 2); ?></td>
                    <td><?php echo number_format($totalPrice, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align:right;"><strong>Total Amount:</strong></td>
                    <td><strong><?php echo number_format($totalAmount, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
