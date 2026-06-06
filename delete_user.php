<?php
include('db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete user
    $query = "DELETE FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header('Location: manage_users.php'); // Redirect after deletion
    exit();
}
?>
