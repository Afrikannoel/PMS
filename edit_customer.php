<?php
// Include database connection
include('db.php');

// Check if customer_id is provided in the URL
if (!isset($_GET['customer_id'])) {
    die("Customer ID not provided.");
}

// Get the customer ID from the URL
$customer_id = $_GET['customer_id'];

// Fetch customer details from the database
$customer_query = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :customer_id LIMIT 1");
$customer_query->execute(['customer_id' => $customer_id]);
$customer = $customer_query->fetch(PDO::FETCH_ASSOC);

// Check if the customer exists
if (!$customer) {
    die("Customer not found.");
}

// Handle form submission for updating customer details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $customer_description = $_POST['customer_description'];

    // Prepare and execute the update query
    $update_query = $pdo->prepare("UPDATE customers SET full_name = :full_name, phone = :phone, email = :email, address = :address, customer_description = :customer_description WHERE customer_id = :customer_id");
    
    $update_query->execute([
        'full_name' => $full_name,
        'phone' => $phone,
        'email' => $email,
        'address' => $address,
        'customer_description' => $customer_description,
        'customer_id' => $customer_id
    ]);

    // Redirect after successful update
    header("Location: view_customer.php?customer_id=" . $customer_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="edit_customer.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Edit Customer</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($customer['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" id="address" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="customer_description">Description:</label>
                <textarea name="customer_description" id="customer_description"><?php echo htmlspecialchars($customer['customer_description']); ?></textarea>
            </div>
            <button type="submit" class="btn">Update Customer</button>
            <a href="manage_customers.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>
