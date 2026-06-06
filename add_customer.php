<?php
// Include database connection
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $customer_description = trim($_POST['customer_description']);

    // Check if mandatory fields are filled
    if ($full_name && $phone) {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO customers (full_name, phone, email, address, customer_description) 
                                VALUES (:full_name, :phone, :email, :address, :customer_description)");

        // Execute the statement
        if ($stmt->execute([
            ':full_name' => $full_name,
            ':phone' => $phone,
            ':email' => $email,
            ':address' => $address,
            ':customer_description' => $customer_description
        ])) {
            $success_message = "Customer added successfully!";
        } else {
            $error_message = "Error adding customer.";
        }
    } else {
        $error_message = "Full name and phone are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <link rel="stylesheet" href="add_customer.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <h1>Add Customer</h1>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="add_customer.php" method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">

            <label for="address">Address:</label>
            <textarea id="address" name="address"></textarea>

            <label for="customer_description">Customer Description:</label>
            <textarea id="customer_description" name="customer_description"></textarea>

            <button type="submit">Add Customer</button>
        </form>

        <div class="buttons">
            <a href="manage_customers.php" class="btn">View Customers</a>
            <a href="pharmacist_dashboard.php" class="btn">Go Back</a>
        </div>
    </div>
</body>
</html>
