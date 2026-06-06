<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manage_user.css"> <!-- Link to the CSS file -->
    <title>Manage Users</title>
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>
        
        <!-- Print Button -->
        <button onclick="window.print();">Print Users</button>
        <div class="navigation">
                <a href="admin_dashboard.php" class="button">Back To Home</a>
    </div>

        
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
    include('db.php'); // Ensure this is correct
    
    // Fetching users from the database
    $query = "SELECT * FROM users"; // Example query
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all users

    if (count($users) === 0) {
        echo "<tr><td colspan='5'>No users found.</td></tr>";
    } else {
        foreach ($users as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $row['user_id']; ?>">Edit</a>
                    <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <a href="deactivate_user.php?id=<?php echo $row['user_id']; ?>">Deactivate</a>

                </td>
            </tr>
        <?php endforeach;
    }
    ?>
</tbody>

        </table>
    </div>

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
