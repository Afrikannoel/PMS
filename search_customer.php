<?php
session_start();
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = isset($_POST['query']) ? $_POST['query'] : '';

    // Check for empty search query
    if (empty($query)) {
        echo json_encode([]); // Return an empty array if no query is provided
        exit;
    }

    try {
        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE full_name LIKE ? OR phone LIKE ?");
        $likeQuery = '%' . $query . '%';
        $stmt->execute([$likeQuery, $likeQuery]);

        // Fetch matching customers
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return the results as JSON
        echo json_encode($customers);
    } catch (PDOException $e) {
        // Handle database errors (optional)
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
