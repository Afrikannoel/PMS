<?php
// Include database connection
include('db.php');

// Fetch inventory levels including price_per_unit
try {
    $stmt = $pdo->query("SELECT drug_name, quantity_in_stock, expiry_date, supplier_id, selling_price FROM drugs");
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch drugs out of stock
    $stmt_out_of_stock = $pdo->query("SELECT drug_name FROM drugs WHERE quantity_in_stock = 0");
    $out_of_stock = $stmt_out_of_stock->fetchAll(PDO::FETCH_ASSOC);

    // Fetch drugs expiring in less than 6 months
    $stmt_expiring_soon = $pdo->query("
        SELECT drug_name, expiry_date 
        FROM drugs 
        WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
    ");
    $expiring_soon = $stmt_expiring_soon->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error fetching inventory: " . $e->getMessage();
    exit;
}

// Initialize total inventory value
$total_value = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inventory Levels</title>
    <link rel="stylesheet" href="view_inventory.css"> <!-- Link to your CSS file -->
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Inventory Levels</h1>

        <!-- Inventory Table -->
        <table>
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Quantity in Stock</th>
                    <th>Expiry Date</th>
                    <th>Supplier ID</th>
                    <th>selling_price</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($inventory) > 0): ?>
                    <?php foreach ($inventory as $drug): ?>
                        <?php 
                            $drug_total_value = $drug['quantity_in_stock'] * $drug['selling_price']; 
                            $total_value += $drug_total_value;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drug['drug_name']); ?></td>
                            <td><?php echo htmlspecialchars($drug['quantity_in_stock']); ?></td>
                            <td><?php echo htmlspecialchars($drug['expiry_date']); ?></td>
                            <td><?php echo htmlspecialchars($drug['supplier_id']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($drug['selling_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars(number_format($drug_total_value, 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No inventory records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <h3>Total Inventory Value: <?php echo number_format($total_value, 2); ?></h3>

        <!-- Drugs Out of Stock -->
        <h2>Drugs Out of Stock</h2>
        <?php if (count($out_of_stock) > 0): ?>
            <ul>
                <?php foreach ($out_of_stock as $drug): ?>
                    <li><?php echo htmlspecialchars($drug['drug_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>All drugs are in stock.</p>
        <?php endif; ?>

        <!-- Drugs Expiring in Less Than 6 Months -->
        <h2>Drugs Expiring in Less Than 6 Months</h2>
        <?php if (count($expiring_soon) > 0): ?>
            <ul>
                <?php foreach ($expiring_soon as $drug): ?>
                    <li><?php echo htmlspecialchars($drug['drug_name']) . " (Expiry Date: " . htmlspecialchars($drug['expiry_date']) . ")"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No drugs are expiring within the next 6 months.</p>
        <?php endif; ?>

        <div class="buttons">
            <button class="btn" onclick="printPage()">Print</button>
            <a href="admin_dashboard.php" class="btn">Go Back</a>
        </div>
    </div>
</body>
</html>
