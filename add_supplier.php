<?php
include('db.php'); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Insert supplier into the database
    $query = "INSERT INTO suppliers (supplier_name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$supplier_name, $contact_person, $phone, $email, $address]);

    // Redirect to manage suppliers page
    header('Location: manage_suppliers.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="add_supplier.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Add Supplier</h1>
        <form action="" method="POST">
            <input type="text" name="supplier_name" placeholder="Supplier Name" required>
            <input type="text" name="contact_person" placeholder="Contact Person">
            <input type="text" name="phone" placeholder="Phone">
            <input type="email" name="email" placeholder="Email">
            <textarea name="address" placeholder="Address"></textarea>
            <button type="submit">Add Supplier</button>
        </form>
    </div>
</body>
</html>
