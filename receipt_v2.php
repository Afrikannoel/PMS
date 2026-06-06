<?php
session_start();
include 'db.php';

if (!isset($_GET['sale_id']) || empty($_GET['sale_id'])) {
    header('Location: add_sale.php');
    exit();
}

$saleId = $_GET['sale_id'];

// Fetch sale and customer details
$stmt = $pdo->prepare("
    SELECT 
        sales.*,
        customers.full_name,
        customers.phone,
        customers.customer_description
    FROM sales
    LEFT JOIN customers ON sales.customer_id = customers.customer_id
    WHERE sales.sale_id = :sale_id
");

$stmt->execute([
    ':sale_id' => $saleId
]);

$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    header('Location: add_sale.php');
    exit();
}

// Fetch sold items
$stmt = $pdo->prepare("
    SELECT 
        sale_items.quantity,
        sale_items.unit_price,
        drugs.drug_name
    FROM sale_items
    INNER JOIN drugs ON sale_items.drug_id = drugs.drug_id
    WHERE sale_items.sale_id = :sale_id
");

$stmt->execute([
    ':sale_id' => $saleId
]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$discount = isset($sale['discount']) ? (float)$sale['discount'] : 0;
$finalTotal = isset($sale['total_amount']) ? (float)$sale['total_amount'] : 0;
$amountPaid = isset($sale['amount_paid']) ? (float)$sale['amount_paid'] : 0;
$balance = isset($sale['balance']) ? (float)$sale['balance'] : ($amountPaid - $finalTotal);
$paymentMethod = isset($sale['payment_method']) ? $sale['payment_method'] : '';

$totalBeforeDiscount = $finalTotal + $discount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Receipt</title>
    <link rel="stylesheet" href="receipt.css">
</head>
<body>

    <div class="receipt-container">
        <h1>Customer Receipt</h1>

        <p>
            <strong>Sale ID:</strong>
            <?php echo htmlspecialchars($saleId); ?>
        </p>

        <h2>Customer Details</h2>

        <table class="receipt-table">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($sale['full_name'] ?? 'Not linked'); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($sale['phone'] ?? 'Not linked'); ?></td>
            </tr>
            <?php if (!empty($sale['customer_description'])): ?>
                <tr>
                    <th>Description</th>
                    <td><?php echo htmlspecialchars($sale['customer_description']); ?></td>
                </tr>
            <?php endif; ?>
        </table>

        <h2>Items Purchased</h2>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php $itemTotal = $item['quantity'] * $item['unit_price']; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['drug_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>KSh <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td>KSh <?php echo number_format($itemTotal, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Payment Summary</h2>

        <table class="receipt-table">
            <tr>
                <th>Total Amount</th>
                <td>KSh <?php echo number_format($totalBeforeDiscount, 2); ?></td>
            </tr>
            <tr>
                <th>Discount</th>
                <td>KSh <?php echo number_format($discount, 2); ?></td>
            </tr>
            <tr>
                <th>Final Total</th>
                <td>KSh <?php echo number_format($finalTotal, 2); ?></td>
            </tr>
            <tr>
                <th>Amount Paid</th>
                <td>KSh <?php echo number_format($amountPaid, 2); ?></td>
            </tr>
            <tr>
                <th>Balance</th>
                <td>KSh <?php echo number_format($balance, 2); ?></td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td><?php echo htmlspecialchars($paymentMethod); ?></td>
            </tr>
        </table>
    </div>
<br>
<br>

    <div class="button-group no-print">
        <button onclick="window.print()">Print Customer Receipt</button>

        <button onclick="window.location.href='add_sale.php'">
            New Sale
        </button>

       <button onclick="window.location.href='manage_daily_sales.php'">
            View Today's Sales
        </button>
    </div>

</body>
</html>