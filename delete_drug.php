<?php
include('db.php'); // Include your database connection file

if (isset($_GET['id'])) {
    $drug_id = $_GET['id'];

    // Delete the drug from the database
    $stmt = $pdo->prepare("DELETE FROM drugs WHERE drug_id = :id");
    $stmt->execute(['id' => $drug_id]);

    // Redirect back to manage drugs page after deleting
    header('Location: manage_drugs.php');
    exit();
} else {
    die("No drug ID specified.");
}
?>
