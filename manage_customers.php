<?php
// Include database connection
include('db.php');

// Fetch all customers from the database
$query = $pdo->prepare("SELECT * FROM customers ORDER BY full_name ASC");
$query->execute();
$customers = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="manage_customers.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <div class="buttons">
            <a href="pharmacist_dashboard.php" class="btn">Go Back</a>
            <a href="add_customer.php" class="btn">Add New Customer</a>
        </div>
        
        <h1>Manage Customers</h1>

        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)): ?>
                    <?php foreach ($customers as $customer) : ?>
                        <tr>
                            <td><?php echo $customer['customer_id']; ?></td>
                            <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['address']); ?></td>
                            <td><?php echo htmlspecialchars($customer['customer_description']); ?></td>
                            <td>
                                <a href="view_customer.php?customer_id=<?php echo $customer['customer_id']; ?>" class="btn">View</a>
                                <a href="edit_customer.php?customer_id=<?php echo $customer['customer_id']; ?>" class="btn">Edit</a>
                                <a href="delete_customer.php?customer_id=<?php echo $customer['customer_id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
