<?php
// register.php
session_start();
require 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $full_name = trim($_POST['full_name']);
    $role = trim($_POST['role']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Insert user into the database
    $sql = "INSERT INTO users (username, password, full_name, role, email, phone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$username, $password, $full_name, $role, $email, $phone])) {
        $_SESSION['message'] = "Registration successful!";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Registration failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="full_name" placeholder="Full Name" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="pharmacist">Pharmacist</option>
        </select>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <button type="submit">Register</button>
    </form>
</body>
</html>
