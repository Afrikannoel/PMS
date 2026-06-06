<?php
include('db.php'); // Connect to the database

// Fetch suppliers from the database
$query = "SELECT * FROM suppliers";
$stmt = $pdo->prepare($query);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="manage_suppliers.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Manage Suppliers</h1>
        <a href="add_supplier.php" class="btn">Add New Supplier</a>
        <table>
            <thead>
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?php echo $supplier['supplier_id']; ?></td>
                        <td><?php echo $supplier['supplier_name']; ?></td>
                        <td><?php echo $supplier['contact_person']; ?></td>
                        <td><?php echo $supplier['phone']; ?></td>
                        <td><?php echo $supplier['email']; ?></td>
                        <td><?php echo $supplier['address']; ?></td>
                        <td>
                            <a href="edit_supplier.php?id=<?php echo $supplier['supplier_id']; ?>" class="btn">Edit</a>
                            <a href="delete_supplier.php?id=<?php echo $supplier['supplier_id']; ?>" class="btn">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
