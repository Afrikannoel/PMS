<?php
// Include database connection
include('db.php'); // Ensure this points to your db.php file

// Initialize an empty error message
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $drug_name = $_POST['drug_name'];
        $description = $_POST['description'];
        $buying_price = $_POST['buying_price'];
        $selling_price = $_POST['selling_price'];
        $quantity_in_stock = $_POST['quantity'];
        $expiry_date = $_POST['expiry_date'];
        $batch_number = $_POST['batch_number'];
        $supplier_id = $_POST['supplier_id'];

        // Insert the new drug into the database
        $query = "INSERT INTO drugs (drug_name, description, buying_price, selling_price, quantity_in_stock, expiry_date, batch_number, supplier_id, created_at) 
                  VALUES (:drug_name, :description, :buying_price, :selling_price, :quantity_in_stock, :expiry_date, :batch_number, :supplier_id, NOW())";
        $stmt = $pdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':drug_name', $drug_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':buying_price', $buying_price);
        $stmt->bindParam(':selling_price', $selling_price);
        $stmt->bindParam(':quantity_in_stock', $quantity_in_stock);
        $stmt->bindParam(':expiry_date', $expiry_date);
        $stmt->bindParam(':batch_number', $batch_number);
        $stmt->bindParam(':supplier_id', $supplier_id);
        
        // Execute the statement
        $stmt->execute();

        // Redirect after successful addition
        header('Location: manage_drugs.php');
        exit();
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch suppliers for the dropdown
$suppliers_query = "SELECT supplier_id, supplier_name FROM suppliers";
$suppliers_stmt = $pdo->prepare($suppliers_query);
$suppliers_stmt->execute();
$suppliers = $suppliers_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Drug</title>
    <link rel="stylesheet" type="text/css" href="add_drug.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <h1>Add New Drug</h1>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div> <!-- Display error message -->
        <?php endif; ?>
        <form action="add_drug.php" method="post">
            <label>Drug Name:</label>
            <input type="text" name="drug_name" required>
            
            <label>Description:</label>
            <textarea name="description" required></textarea>
            
            <label>Buying Price:</label>
            <input type="number" step="0.01" name="buying_price" required>
            
            <label>Selling Price:</label>
            <input type="number" step="0.01" name="selling_price" required>
            
            <label>Quantity in Stock:</label>
            <input type="number" name="quantity" required>
            
            <label>Expiry Date:</label>
            <input type="date" name="expiry_date">
            
            <label>Batch Number:</label>
            <input type="text" name="batch_number">
            
            <label>Supplier:</label>
            <select name="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['supplier_id']; ?>"><?php echo $supplier['supplier_name']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Add Drug</button>
        </form>
        <div class="buttons">
            <a href="manage_drugs.php" class="btn">Go Back</a>
        </div>
    </div>
</body>
</html>
