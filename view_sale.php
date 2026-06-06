<?php
// Include database connection
include('db.php');

// Check if sale_id is set and is a valid integer
if (isset($_GET['sale_id']) && filter_var($_GET['sale_id'], FILTER_VALIDATE_INT)) {
    $sale_id = $_GET['sale_id'];
} else {
    die("Invalid Sale ID.");
}

// Fetch sale details along with customer information
$saleQuery = $pdo->prepare("SELECT sales.*, users.full_name AS sold_by, customers.full_name AS customer_name, customers.phone AS customer_contact, customers.customer_id 
                             FROM sales 
                             JOIN users ON sales.user_id = users.user_id 
                             JOIN customers ON sales.customer_id = customers.customer_id 
                             WHERE sales.sale_id = :sale_id");
$saleQuery->execute(['sale_id' => $sale_id]);
$sale = $saleQuery->fetch(PDO::FETCH_ASSOC);

// Debugging output to check the fetched sale
if (!$sale) {
    die("Sale not found with ID: " . htmlspecialchars($sale_id));
}

// Fetch sale items associated with this sale ID
$itemQuery = $pdo->prepare("SELECT sale_items.*, drugs.drug_name FROM sale_items 
                             JOIN drugs ON sale_items.drug_id = drugs.drug_id 
                             WHERE sale_items.sale_id = :sale_id");
$itemQuery->execute(['sale_id' => $sale_id]);

// Fetch the items
$items = $itemQuery->fetchAll(PDO::FETCH_ASSOC);

// Calculate final total (total_amount - discount) and balance (if any)
$final_total = $sale['total_amount'] - $sale['discount'];
$balance = $final_total - $sale['total_amount']; // Assuming balance logic comes from user payments
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sale #<?php echo htmlspecialchars($sale['sale_id']); ?></title>
    <link rel="stylesheet" href="view_sale.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <h1>Sale Details (ID: <?php echo htmlspecialchars($sale['sale_id']); ?>)</h1>
        
        <p><strong>Sold By:</strong> <?php echo htmlspecialchars($sale['sold_by']); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($sale['customer_name']); ?></p>
        <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($sale['customer_id']); ?></p>
        <p><strong>Customer Phone:</strong> <?php echo htmlspecialchars($sale['customer_contact']); ?></p>
        
        <p><strong>Total Amount:</strong> Ksh <?php echo number_format($sale['total_amount'], 2); ?></p>
        <p><strong>Discount:</strong> Ksh <?php echo number_format($sale['discount'], 2); ?></p>
        <p><strong>Final Total:</strong> Ksh <?php echo number_format($final_total, 2); ?></p> <!-- Final total -->
        <p><strong>Balance:</strong> Ksh <?php echo number_format($balance, 2); ?></p> <!-- Balance -->
        <p><strong>Sale Date:</strong> <?php echo htmlspecialchars($sale['sale_date']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($sale['payment_method']); ?></p> <!-- Payment method included -->

        <h2>Sale Items</h2>
        <?php if (!empty($items)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Drug Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['drug_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>Ksh <?php echo number_format($item['unit_price'], 2); ?></td>
                            <td>Ksh <?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No items found for this sale.</p>
        <?php endif; ?>

        <div class="buttons">
            <a href="manage_sales.php" class="btn">Go Back</a>
            <button onclick="window.print()" class="btn print-btn">Print</button>
        </div>
    </div>
</body>
</html>
