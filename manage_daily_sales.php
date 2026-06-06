<?php 
// Include database connection
include('db.php');

// Get the current date in 'YYYY-MM-DD' format
$current_date = date('Y-m-d');

// Fetch sales for the current date, including payment methods
$query = $pdo->prepare("SELECT sales.sale_id, users.full_name, sales.total_amount, sales.discount, sales.amount_paid, sales.balance, sales.sale_date, sales.payment_method 
                        FROM sales 
                        JOIN users ON sales.user_id = users.user_id 
                        WHERE DATE(sales.sale_date) = :sale_date
                        ORDER BY sales.sale_date DESC");
$query->execute(['sale_date' => $current_date]);

// Fetch the results
$sales = $query->fetchAll(PDO::FETCH_ASSOC);

// Initialize totals for each payment method
$total_collected = 0;
$total_mpesa = 0;
$total_cash = 0;
$total_card = 0;
$total_discount = 0;
$final_total = 0;

// Calculate the total amounts collected by payment method
foreach ($sales as $sale) {
    $total_collected += $sale['total_amount'];
    $total_discount += $sale['discount'];
    $final_total += $sale['total_amount'] - $sale['discount']; // Final total after discount

    // Update totals based on the payment method
    switch ($sale['payment_method']) {
        case 'M-Pesa':
            $total_mpesa += $sale['total_amount'];
            break;
        case 'Cash':
            $total_cash += $sale['total_amount'];
            break;
        case 'Card':
            $total_card += $sale['total_amount'];
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sales</title>
    <link rel="stylesheet" href="manage_sales.css">
</head>
<body>

    <div class="container">
    <div class="buttons">
            <a href="pharmacist_dashboard.php" class="btn">Go Back</a>
            <button onclick="window.print()" class="btn print-btn">Print</button>
        </div>
        <h1>Sales for <?php echo $current_date; ?></h1>

        <h4>Total Amount Collected Today: Ksh <?php echo number_format($total_collected, 2); ?></h4>
        <h4>Total Discounts Given: Ksh <?php echo number_format($total_discount, 2); ?></h4>
        <h4>Final Total (After Discounts): Ksh <?php echo number_format($final_total, 2); ?></h4>
        <h4>Total Collected through M-Pesa: Ksh <?php echo number_format($total_mpesa, 2); ?></h4>
        <h4>Total Collected through Cash: Ksh <?php echo number_format($total_cash, 2); ?></h4>
        <h4>Total Collected through Card: Ksh <?php echo number_format($total_card, 2); ?></h4>

        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Sold By</th>
                    <th>Total Amount</th>
                    <th>Discount</th>
                    <th>Final Total</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Sale Date</th>
                    <th>Payment Method</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sales)): ?>
                    <?php foreach ($sales as $sale) : ?>
                        <tr>
                            <td><?php echo $sale['sale_id']; ?></td>
                            <td><?php echo $sale['full_name']; ?></td>
                            <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td><?php echo number_format($sale['discount'], 2); ?></td>
                            <td><?php echo number_format($sale['total_amount'] - $sale['discount'], 2); ?></td> <!-- Final total -->
                            <td><?php echo number_format($sale['amount_paid'], 2); ?></td>
                            <td><?php echo number_format($sale['balance'], 2); ?></td>
                            <td><?php echo $sale['sale_date']; ?></td>
                            <td><?php echo $sale['payment_method']; ?></td>
                            <td>
                                <a href="view_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn">View</a>
                                <a href="edit_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn">Edit</a>
                                <a href="void_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn delete-btn">Void</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No sales for today.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
