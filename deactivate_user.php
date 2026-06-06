<?php
include('db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Check if the user exists before attempting to deactivate
    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Update user status to deactivated
        $query = "UPDATE users SET role = 'deactivated' WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        header('Location: manage_users.php'); // Redirect after deactivation
        exit();
    } else {
        echo "User not found.";
    }
} else {
    echo "No user ID provided.";
}
?>
