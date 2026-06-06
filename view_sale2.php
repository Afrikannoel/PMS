<?php
include('db.php');

if (!isset($_GET['sale_id']) || !filter_var($_GET['sale_id'], FILTER_VALIDATE_INT)) {
    die("Invalid Sale ID.");
}

$sale_id = (int) $_GET['sale_id'];

$saleQuery = $pdo->prepare("
    SELECT 
        sales.*,
        users.full_name AS sold_by,
        customers.full_name AS customer_name,
        customers.phone AS customer_contact,
        customers.customer_id AS linked_customer_id
    FROM sales
    JOIN users ON sales.user_id = users.user_id
    LEFT JOIN customers ON sales.customer_id = customers.customer_id
    WHERE sales.sale_id = :sale_id
");

$saleQuery->execute([
    ':sale_id' => $sale_id
]);

$sale = $saleQuery->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    die("Sale not found with ID: " . htmlspecialchars($sale_id));
}

$itemQuery = $pdo->prepare("
    SELECT 
        sale_items.*,
        drugs.drug_name
    FROM sale_items
    JOIN drugs ON sale_items.drug_id = drugs.drug_id
    WHERE sale_items.sale_id = :sale_id
");

$itemQuery->execute([
    ':sale_id' => $sale_id
]);

$items = $itemQuery->fetchAll(PDO::FETCH_ASSOC);

$discount = isset($sale['discount']) ? (float) $sale['discount'] : 0;
$final_total = isset($sale['total_amount']) ? (float) $sale['total_amount'] : 0;
$amount_paid = isset($sale['amount_paid']) ? (float) $sale['amount_paid'] : 0;
$balance = isset($sale['balance']) ? (float) $sale['balance'] : ($amount_paid - $final_total);
$total_before_discount = $final_total + $discount;

$customer_name = !empty($sale['customer_name']) ? $sale['customer_name'] : 'Not linked';
$customer_id = !empty($sale['linked_customer_id']) ? $sale['linked_customer_id'] : 'Not linked';
$customer_contact = !empty($sale['customer_contact']) ? $sale['customer_contact'] : 'Not linked';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sale #<?php echo htmlspecialchars($sale['sale_id']); ?></title>
    <link rel="stylesheet" href="view_sale.css">
</head>
<body>
    <div class="container">
        <h1>Sale Details (ID: <?php echo htmlspecialchars($sale['sale_id']); ?>)</h1>

        <p><strong>Sold By:</strong> <?php echo htmlspecialchars($sale['sold_by']); ?></p>

        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
        <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customer_id); ?></p>
        <p><strong>Customer Phone:</strong> <?php echo htmlspecialchars($customer_contact); ?></p>

        <p><strong>Total Amount:</strong> Ksh <?php echo number_format($total_before_discount, 2); ?></p>
        <p><strong>Discount:</strong> Ksh <?php echo number_format($discount, 2); ?></p>
        <p><strong>Final Total:</strong> Ksh <?php echo number_format($final_total, 2); ?></p>
        <p><strong>Amount Paid:</strong> Ksh <?php echo number_format($amount_paid, 2); ?></p>
        <p><strong>Balance:</strong> Ksh <?php echo number_format($balance, 2); ?></p>
        <p><strong>Sale Date:</strong> <?php echo htmlspecialchars($sale['sale_date']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($sale['payment_method']); ?></p>

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
            <a href="manage_customers.php" class="btn">Go Back</a>
            <button onclick="window.print()" class="btn print-btn">Print</button>
        </div>
    </div>|
</body>
</html>