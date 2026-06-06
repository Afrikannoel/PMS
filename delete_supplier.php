// delete_supplier.php
include 'db.php'; // Include your database connection

if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    // Check if there are any drugs associated with this supplier
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM drugs WHERE supplier_id = :supplier_id");
    $stmt->execute(['supplier_id' => $supplier_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // You might want to handle this case by notifying the user
        echo "Cannot delete supplier with ID $supplier_id because there are drugs associated with it.";
    } else {
        // Proceed to delete the supplier
        $stmt = $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = :supplier_id");
        $stmt->execute(['supplier_id' => $supplier_id]);

        // Redirect or notify the user
        header('Location: manage_suppliers.php');
        exit();
    }
} else {
    echo "No supplier ID provided.";
}
