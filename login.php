<?php
session_start();
require 'db.php'; // Include database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Prepare and execute the query
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            exit;
        } elseif ($user['role'] === 'pharmacist') {
            header("Location: pharmacist_dashboard.php"); // Redirect to pharmacist dashboard
            exit;
        } else {
            header("Location: login.php?error=invalid_role"); // Redirect for invalid roles
            exit;
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> <!-- Link to your CSS file -->
</head>
<body>
    
    <?php if (isset($error)) : ?>
        <p style="color:red;"><?php echo $error; ?></p> <!-- Show error message -->
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
