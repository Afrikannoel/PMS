<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    // Fetch user details
    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update user details
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header('Location: manage_users.php'); // Redirect after update
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" type="text/css" href="edit_user.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container"> <!-- Added a container div -->
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo $user['user_id']; ?>" method="post">
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
            <br>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            <br>
            <label>Role:</label>
            <select name="role" required>
                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="pharmacist" <?php if ($user['role'] == 'pharmacist') echo 'selected'; ?>>Pharmacist</option>
                <option value="staff" <?php if ($user['role'] == 'staff') echo 'selected'; ?>>Staff</option>
            </select>
            <br>
            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
