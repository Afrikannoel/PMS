<?php 
// Initialize balance variable to avoid undefined variable error
$balance = 0.00; // Default balance to 0
session_start();
include 'db.php'; // Include the database connection


// Initialize session variables
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Prepare receipt data
$receipt = [];

// Handle adding drugs to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add drug to cart
    if (isset($_POST['add_to_cart'])) {
        $drugId = $_POST['drug_id'];
        $quantity = $_POST['quantity'];

        // Check drug availability
        $stmt = $pdo->prepare("SELECT * FROM drugs WHERE drug_id = :drug_id");
        $stmt->execute([':drug_id' => $drugId]);
        $drug = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($drug && $drug['quantity_in_stock'] >= $quantity) {
            // Drug is available, add to cart
            $totalPrice = $quantity * $drug['selling_price'];

            // Check if the drug already exists in the cart
            if (isset($cart[$drugId])) {
                $cart[$drugId]['quantity'] += $quantity;
                $cart[$drugId]['total_price'] += $totalPrice;
            } else {
                $cart[$drugId] = [
                    'drug_id' => $drug['drug_id'],
                    'drug_name' => $drug['drug_name'],
                    'quantity' => $quantity,
                    'unit_price' => $drug['selling_price'],
                    'total_price' => $totalPrice
                ];
            }

            $_SESSION['cart'] = $cart;

            // Update inventory for the added quantity
            $updateStmt = $pdo->prepare("UPDATE drugs SET quantity_in_stock = quantity_in_stock - :quantity WHERE drug_id = :drug_id");
            $updateStmt->execute([
                ':quantity' => $quantity,
                ':drug_id' => $drugId
            ]);
        } else {
            echo "<script>alert('Not enough stock available for this drug.');</script>";
        }
    }

    // Handle removing items from the cart
    if (isset($_POST['remove_cart_item'])) {
        $drugId = $_POST['drug_id'];

        // Restore stock to the inventory
        if (isset($cart[$drugId])) {
            $removedItem = $cart[$drugId];
            $updateStmt = $pdo->prepare("UPDATE drugs SET quantity_in_stock = quantity_in_stock + :quantity WHERE drug_id = :drug_id");
            $updateStmt->execute([
                ':quantity' => $removedItem['quantity'],
                ':drug_id' => $drugId
            ]);
            unset($cart[$drugId]);
            $_SESSION['cart'] = $cart;
        }
    }

    // Handle updating item quantities in the cart
    if (isset($_POST['update_cart'])) {
        $drugId = $_POST['drug_id'];
        $newQuantity = $_POST['quantity'];

        if (isset($cart[$drugId])) {
            $oldQuantity = $cart[$drugId]['quantity'];

            // Update stock based on quantity change
            if ($newQuantity > $oldQuantity) {
                $quantityToDeduct = $newQuantity - $oldQuantity;

                // Check availability before deducting stock
                $stmt = $pdo->prepare("SELECT quantity_in_stock FROM drugs WHERE drug_id = :drug_id");
                $stmt->execute([':drug_id' => $drugId]);
                $stock = $stmt->fetchColumn();

                if ($stock >= $quantityToDeduct) {
                    $updateStmt = $pdo->prepare("UPDATE drugs SET quantity_in_stock = quantity_in_stock - :quantity WHERE drug_id = :drug_id");
                    $updateStmt->execute([
                        ':quantity' => $quantityToDeduct,
                        ':drug_id' => $drugId
                    ]);
                } else {
                    echo "<script>alert('Not enough stock available for this drug.');</script>";
                    $newQuantity = $oldQuantity; // Revert to old quantity
                }
            } elseif ($newQuantity < $oldQuantity) {
                $quantityToAdd = $oldQuantity - $newQuantity;
                $updateStmt = $pdo->prepare("UPDATE drugs SET quantity_in_stock = quantity_in_stock + :quantity WHERE drug_id = :drug_id");
                $updateStmt->execute([
                    ':quantity' => $quantityToAdd,
                    ':drug_id' => $drugId
                ]);
            }

            // Update the cart
            $cart[$drugId]['quantity'] = $newQuantity;
            $cart[$drugId]['total_price'] = $newQuantity * $cart[$drugId]['unit_price'];
            $_SESSION['cart'] = $cart;
        }
    }

    // Handle clearing the cart
    if (isset($_POST['clear_cart'])) {
        // Restore stock for all items in the cart
        foreach ($cart as $item) {
            $updateStmt = $pdo->prepare("UPDATE drugs SET quantity_in_stock = quantity_in_stock + :quantity WHERE drug_id = :drug_id");
            $updateStmt->execute([
                ':quantity' => $item['quantity'],
                ':drug_id' => $item['drug_id']
            ]);
        }
        unset($_SESSION['cart']);
        $cart = [];
    }

    // Handle checkout process
if (isset($_POST['checkout'])) {
    // Retrieve the payment method from the form
    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

    // Capture the amount paid
    $amountPaid = isset($_POST['amount_paid']) ? (float)$_POST['amount_paid'] : 0;

    // Calculate total amount of items in the cart
    $totalSaleAmount = 0;
    foreach ($cart as $item) {
        $totalSaleAmount += $item['total_price'];
    }

    // Capture discount
    $discount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0;

    // Calculate balance
    $balance = $amountPaid - ($totalSaleAmount - $discount);

    // Ensure the cart isn't empty and a payment method is provided
    if (!empty($cart) && $paymentMethod) {
        // Prepare receipt data
        $receipt['cart'] = $cart;
        $receipt['total_amount'] = $totalSaleAmount; // Use totalSaleAmount calculated above
        $receipt['discount'] = $discount;
        $receipt['payment_method'] = $paymentMethod;
        $receipt['amount_paid'] = $amountPaid; // Add amount paid to receipt
        $receipt['balance'] = $balance; // Add balance to receipt

        // Adjust total after discount
        $finalAmount = $totalSaleAmount - $discount;

        // Insert sale record into the sales table
        $stmt = $pdo->prepare("INSERT INTO sales (user_id, total_amount, discount, sale_date, amount_paid, balance, payment_method) 
                               VALUES (:user_id, :total_amount, :discount, NOW(), :amount_paid, :balance, :payment_method)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'], // Assuming user_id is stored in session
            ':total_amount' => $finalAmount,
            ':discount' => $discount,
            ':amount_paid' => $amountPaid, // Insert amount paid
            ':balance' => $balance, // Insert balance
            ':payment_method' => $paymentMethod // Insert selected payment method
        ]);

        // Get the sale ID for inserting sale items
        $saleId = $pdo->lastInsertId();

        // Insert each cart item into the sale_items table
        foreach ($cart as $item) {
            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, drug_id, quantity, unit_price)
                                   VALUES (:sale_id, :drug_id, :quantity, :unit_price)");
            $stmt->execute([
                ':sale_id' => $saleId,
                ':drug_id' => $item['drug_id'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['unit_price']
            ]);
        }

        // Add sale ID and final amount to the receipt data
            $receipt['sale_id'] = $saleId;
            $receipt['final_total'] = $finalAmount;

            // Store receipt in session for the receipt page
            $_SESSION['receipt'] = $receipt;

            // Clear the cart after successful checkout
            unset($_SESSION['cart']);

        // Redirect to receipt page
        header('Location: receipt.php');
        exit();
    } else {
        // Handle case where cart is empty or payment method is missing
        echo "<script>alert('Cart is empty or payment method is missing. Please add items and select a payment method.');</script>";
    }
}

}

// Handle search functionality
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';
$drugs = []; // Initialize the drugs variable

// Clear search results if no search term is provided
if ($searchTerm) {
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE drug_name LIKE :search_term");
    $stmt->execute([':search_term' => '%' . $searchTerm . '%']);
    $drugs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Reset drugs to an empty array when the search term is empty
    $drugs = [];
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sale</title>
    <link rel="stylesheet" href="add_sale.css"> <!-- Link to your CSS -->
</head>
<body>

    <div class="navigation">
        <a href="manage_daily_sales.php" class="button">View Today's Sales</a>
        <a href="pharmacist_dashboard.php" class="button">Back To Home</a>
    </div>

    <div class="container">
        <h1>Add Sale</h1>

        <!-- Search Form -->
        <form method="POST">
            <input type="text" name="search_term" placeholder="Search for drugs..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Display Search Results -->
        <h2>Search Results</h2>
        <table>
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Description</th>
                    <th>Unit Price</th>
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Quantity in Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($drugs): ?>
                    <?php foreach ($drugs as $drug): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drug['drug_name']); ?></td>
                            <td><?php echo htmlspecialchars($drug['description']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($drug['selling_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($drug['batch_number']); ?></td>
                            <td><?php echo htmlspecialchars($drug['expiry_date']); ?></td>
                            <td><?php echo htmlspecialchars($drug['quantity_in_stock']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="drug_id" value="<?php echo htmlspecialchars($drug['drug_id']); ?>">
                                    <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($drug['quantity_in_stock']); ?>" required>
                                    <button type="submit" name="add_to_cart">Add to Cart</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No drugs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Cart Display -->
        <h2>Cart</h2>
        <table>
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cartTotal = 0; // Initialize cart total variable
                if ($cart): ?>
                    <?php foreach ($cart as $item): 
                        $cartTotal += $item['total_price']; // Accumulate the total price
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['drug_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['unit_price']); ?></td>
                            <td><?php echo htmlspecialchars($item['total_price']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="drug_id" value="<?php echo htmlspecialchars($item['drug_id']); ?>">
                                    <button type="submit" name="remove_cart_item">Remove</button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="drug_id" value="<?php echo htmlspecialchars($item['drug_id']); ?>">
                                    <input type="number" name="quantity" min="1" required>
                                    <button type="submit" name="update_cart">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Display Cart Total -->
        <h3>Total: <?php echo isset($cartTotal) ? htmlspecialchars(number_format($cartTotal, 2)) : '0.00'; ?></h3>

        <form method="POST">
    <input type="number" name="discount" placeholder="Discount Amount" min="0">

    <label for="amount_paid">Amount Paid:</label>
    <input type="number" name="amount_paid" placeholder="Enter amount paid" min="0" required>

    <!-- Display the balance -->
    <label for="balance">Balance:</label>
    <input type="text" name="balance" value="<?php echo htmlspecialchars($balance); ?>" readonly>
<br>
<br>

    <div>
        <label>
            <input type="radio" name="payment_method" value="Cash" required> Cash
        </label>
        <label>
            <input type="radio" name="payment_method" value="M-Pesa" required> M-Pesa
        </label>
        <label>
            <input type="radio" name="payment_method" value="Card" required> Card
        </label>
    </div>
<br>
    <button type="submit" name="checkout">Confirm and Complete Transaction</button>
    <button type="submit" name="clear_cart">Clear Cart</button>
</form>




    </div>
</body>
</html>
