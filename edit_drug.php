<?php
include('db.php'); // Ensure you're including your database connection file

// Check if the drug ID is set
if (isset($_GET['id'])) {
    $drug_id = $_GET['id'];

    // Fetch the drug details from the database
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE drug_id = :id");
    $stmt->execute(['id' => $drug_id]);
    $drug = $stmt->fetch();

    if (!$drug) {
        die("Drug not found.");
    }

    // Handle form submission for editing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $drug_name = $_POST['drug_name'];
        $description = $_POST['description'];
        $buying_price = $_POST['buying_price'];
        $selling_price = $_POST['selling_price'];
        $quantity_in_stock = $_POST['quantity_in_stock'];
        $expiry_date = $_POST['expiry_date'];
        $batch_number = $_POST['batch_number'];

        // Update the drug in the database
        $updateStmt = $pdo->prepare("UPDATE drugs SET drug_name = ?, description = ?, buying_price = ?, selling_price = ?, quantity_in_stock = ?, expiry_date = ?, batch_number = ? WHERE drug_id = ?");
        $updateStmt->execute([$drug_name, $description, $buying_price, $selling_price, $quantity_in_stock, $expiry_date, $batch_number, $drug_id]);

        // Redirect back to manage drugs page after updating
        header('Location: manage_drugs.php');
        exit();
    }
} else {
    die("No drug ID specified.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Drug</title>
    <link rel="stylesheet" href="admin_dashboard.css">
<link rel="stylesheet" href="edit_drug.css">

</head>
<body>
    <div class="container">
        <h1>Edit Drug</h1>
        <form method="POST">
            <label for="drug_name">Drug Name:</label>
            <input type="text" name="drug_name" value="<?php echo htmlspecialchars($drug['drug_name']); ?>" required>

            <label for="description">Description:</label>
            <textarea name="description"><?php echo htmlspecialchars($drug['description']); ?></textarea>

            <label for="buying_price">Buying Price:</label>
            <input type="number" step="0.01" name="buying_price" value="<?php echo htmlspecialchars($drug['buying_price']); ?>" required>

            <label for="selling_price">Selling Price:</label>
            <input type="number" step="0.01" name="selling_price" value="<?php echo htmlspecialchars($drug['selling_price']); ?>" required>

            <label for="quantity_in_stock">Quantity in Stock:</label>
            <input type="number" name="quantity_in_stock" value="<?php echo htmlspecialchars($drug['quantity_in_stock']); ?>" required>

            <label for="expiry_date">Expiry Date:</label>
            <input type="date" name="expiry_date" value="<?php echo htmlspecialchars($drug['expiry_date']); ?>">

            <label for="batch_number">Batch Number:</label>
            <input type="text" name="batch_number" value="<?php echo htmlspecialchars($drug['batch_number']); ?>">

            <button type="submit">Update Drug</button>
        </form>
        <a href="manage_drugs.php" class="btn">Go Back</a>
    </div>
</body>
</html>
