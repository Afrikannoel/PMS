<?php
include('db.php'); // Connect to the database

// Fetch supplier data
$supplier_id = $_GET['id'];
$query = "SELECT * FROM suppliers WHERE supplier_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update supplier data
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $query = "UPDATE suppliers SET supplier_name = ?, contact_person = ?, phone = ?, email = ?, address = ? WHERE supplier_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$supplier_name, $contact_person, $phone, $email, $address, $supplier_id]);

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
    <title>Edit Supplier</title>
    <link rel="stylesheet" href="edit_supplier.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Edit Supplier</h1>
        <form action="" method="POST">
            <input type="text" name="supplier_name" value="<?php echo $supplier['supplier_name']; ?>" required>
            <input type="text" name="contact_person" value="<?php echo $supplier['contact_person']; ?>">
            <input type="text" name="phone" value="<?php echo $supplier['phone']; ?>">
            <input type="email" name="email" value="<?php echo $supplier['email']; ?>">
            <textarea name="address"><?php echo $supplier['address']; ?></textarea>
            <button type="submit">Update Supplier</button>
        </form>
    </div>
</body>
</html>
