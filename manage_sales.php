<?php
// Include database connection
include('db.php');

// Fetch all sales from the database including amount_paid and calculating balance
$query = $pdo->query("
    SELECT sales.sale_id, users.full_name, sales.total_amount, sales.discount, sales.amount_paid, 
           (sales.amount_paid - (sales.total_amount - sales.discount)) AS balance, sales.sale_date
    FROM sales
    JOIN users ON sales.user_id = users.user_id
    ORDER BY sales.sale_date DESC");
$sales = $query->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>Manage Sales</h1>

        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Discount</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Sale Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) : ?>
                    <tr>
                        <td><?php echo $sale['sale_id']; ?></td>
                        <td><?php echo $sale['full_name']; ?></td>
                        <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                        <td><?php echo number_format($sale['discount'], 2); ?></td>
                        <td><?php echo number_format($sale['amount_paid'], 2); ?></td>
                        <td><?php echo number_format($sale['balance'], 2); ?></td>
                        <td><?php echo $sale['sale_date']; ?></td>
                        <td>
                            <a href="view_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn">View</a>
                            <a href="edit_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn">Edit</a>
                            <a href="void_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn delete-btn">Void</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="buttons">
            <a href="pharmacist_dashboard.php" class="btn">Go Back</a>
            <button onclick="window.print()" class="btn print-btn">Print</button>
        </div>
    </div>
</body>
</html>
