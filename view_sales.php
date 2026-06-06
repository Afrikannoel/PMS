<?php
// view_sales.php

include('db.php');

// Fetch daily sales data with details
$daily_sales_query = "
    SELECT 
        DATE(s.sale_date) AS sale_day,
        di.drug_id,
        di.drug_name,
        si.quantity,
        si.unit_price,
        (si.quantity * si.unit_price) AS total_amount
    FROM sales s
    JOIN sale_items si ON s.sale_id = si.sale_id
    JOIN drugs di ON si.drug_id = di.drug_id
    ORDER BY sale_day;
";
$daily_sales_result = $pdo->query($daily_sales_query);

// Fetch total sales data
$total_sales_query = "
    SELECT 
        SUM(total_amount) AS total_sales
    FROM sales;
";
$total_sales_result = $pdo->query($total_sales_query);
$total_sales = $total_sales_result->fetch(PDO::FETCH_ASSOC)['total_sales'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="view_sales.css"> <!-- Update this to your CSS file path -->
</head>
<body>
    <div class="container">
        <h1>Sales Report</h1>
        <h2>Daily Sales Report</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Drug ID</th>
                <th>Drug Name</th>
                <th>Quantity Sold</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
            </tr>
            <?php while ($row = $daily_sales_result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['sale_day']; ?></td>
                    <td><?php echo $row['drug_id']; ?></td>
                    <td><?php echo $row['drug_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo number_format($row['unit_price'], 2); ?></td>
                    <td><?php echo number_format($row['total_amount'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>Total Sales Amount: <?php echo number_format($total_sales, 2); ?></h2>

        <div class="buttons">
            <a href="javascript:window.print()" class="btn">Print Report</a>
            <a href="admin_dashboard.php" class="btn">Go Back</a>
        </div>
    </div>
</body>
</html>
