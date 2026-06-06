<?php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    include 'db.php';

    // Get the user data from the form
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $password, $full_name, $role, $email, $phone]);

    echo "User created successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="create_user.css">
</head>

<body>

    <form action="create_user.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <label>Full Name:</label>
        <input type="text" name="full_name" required>
        <br>
        <label>Role:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="pharmacist">Pharmacist</option>
            <option value="staff">Staff</option>
        </select>
        <br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Phone:</label>
        <input type="tel" name="phone" required>
        <br>
        <button type="submit">Create User</button>

        <div class="navigation">
                <a href="admin_dashboard.php" class="button">Back To Home</a>
    </div>


    </form>
   
    <style>
    .navigation {
        margin: 10px;
        
    .button {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px;
        color: white;
        background-color:green; /* Bootstrap primary color */
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    .button:hover {
        background-color: #0056b3; /* Darker shade for hover effect */
    }
</style>
</body>
</html>
