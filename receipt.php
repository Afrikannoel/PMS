<?php
session_start();
include 'db.php';

if (!isset($_SESSION['receipt'])) {
    header('Location: add_sale.php');
    exit();
}

$receipt = $_SESSION['receipt'];

$saleId = isset($receipt['sale_id']) ? $receipt['sale_id'] : '';
$cartItems = isset($receipt['cart']) ? $receipt['cart'] : [];

$totalAmount = isset($receipt['total_amount']) ? $receipt['total_amount'] : 0;
$discount = isset($receipt['discount']) ? $receipt['discount'] : 0;
$finalTotal = isset($receipt['final_total']) ? $receipt['final_total'] : ($totalAmount - $discount);

$paymentMethod = isset($receipt['payment_method']) ? $receipt['payment_method'] : '';
$amountPaid = isset($receipt['amount_paid']) ? $receipt['amount_paid'] : 0;
$balance = isset($receipt['balance']) ? $receipt['balance'] : ($amountPaid - $finalTotal);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="receipt.css">
</head>
<body>

    <div class="receipt-container">
        <h1>Receipt</h1>

        <p>
            <strong>Sale ID:</strong>
            <?php echo htmlspecialchars($saleId); ?>
        </p>

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
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['drug_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>KSh <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td>KSh <?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Payment Summary</h2>

            <table class="receipt-table">
                <tr>
                    <th>Total Amount</th>
                    <td>KSh <?php echo number_format($totalAmount, 2); ?></td>
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
        <button onclick="window.print()">Print Receipt</button>
         <?php if (!empty($saleId)): ?>
            <button onclick="window.location.href='link_sale_customer.php?sale_id=<?php echo htmlspecialchars($saleId); ?>'">
                Link Sale to Customer
            </button>
        <button onclick="window.location.href='add_sale.php'">
            New Sale
        </button>
        <button onclick="window.location.href='manage_daily_sales.php'">
            View Today's Sales
        </button>
        <?php endif; ?>
    </div>

</body>
</html>