<?php
// Start PHP Session
session_start();

// Include the database connection
include('db.php');

// Check user role (Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect if not admin
    exit();
}

// Fetch all drugs from the database
try {
    $query = "SELECT * FROM drugs";
    $stmt = $pdo->query($query);
    $drugs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching drugs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drugs</title>
    <link rel="stylesheet" href="admin_dashboard.css"> <!-- Link to your CSS -->
    <link rel="stylesheet" href="manage_drugs.css"> <!-- Link to the new CSS -->
</head>
<body>
    <div class="container">
        <h1>Manage Drugs</h1>
        
        <div class="button-container">
            <a href="javascript:void(0);" onclick="window.print();" class="btn">Print</a>
            <a href="admin_dashboard.php" class="btn">Go Back</a>
        </div>
        
        <a href="add_drug.php" class="btn">Add New Drug</a> <!-- Button to add new drug -->
        <table>
            <tr>
                <th>Drug ID</th>
                <th>Drug Name</th>
                <th>Description</th>
                <th>Buying Price</th>
                <th>Selling Price</th>
                <th>Quantity in Stock</th>
                <th>Expiry Date</th>
                <th>Batch Number</th>
                <th>Actions</th>
            </tr>
            <?php if (count($drugs) > 0): ?>
                <?php foreach ($drugs as $drug): ?>
                <tr>
                    <td><?php echo htmlspecialchars($drug['drug_id']); ?></td>
                    <td><?php echo htmlspecialchars($drug['drug_name']); ?></td>
                    <td><?php echo htmlspecialchars($drug['description']); ?></td>
                    <td><?php echo htmlspecialchars($drug['buying_price']); ?></td>
                    <td><?php echo htmlspecialchars($drug['selling_price']); ?></td>
                    <td><?php echo htmlspecialchars($drug['quantity_in_stock']); ?></td>
                    <td><?php echo htmlspecialchars($drug['expiry_date']); ?></td>
                    <td><?php echo htmlspecialchars($drug['batch_number']); ?></td>
                    <td>
    <a href="edit_drug.php?id=<?php echo $drug['drug_id']; ?>">Edit</a>
    <a href="delete_drug.php?id=<?php echo $drug['drug_id']; ?>" onclick="return confirm('Are you sure you want to delete this drug?');">Delete</a>
</td>

                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No drugs found in the database.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
