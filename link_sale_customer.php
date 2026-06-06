<?php
session_start();
include 'db.php';

if (!isset($_GET['sale_id']) || empty($_GET['sale_id'])) {
    header('Location: add_sale.php');
    exit();
}

$saleId = $_GET['sale_id'];
$message = '';
$customers = [];
$searchTerm = '';

// Check if sale exists
$stmt = $pdo->prepare("SELECT * FROM sales WHERE sale_id = :sale_id");
$stmt->execute([
    ':sale_id' => $saleId
]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    header('Location: add_sale.php');
    exit();
}

// Search customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_customer'])) {
    $searchTerm = trim($_POST['customer_search']);

    if (!empty($searchTerm)) {
        $stmt = $pdo->prepare("
            SELECT * FROM customers
            WHERE full_name LIKE :search
            OR phone LIKE :search
            ORDER BY full_name ASC
        ");

        $stmt->execute([
            ':search' => '%' . $searchTerm . '%'
        ]);

        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $message = 'Please enter a customer name or phone number.';
    }
}

// Link existing customer to sale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['link_customer'])) {
    $customerId = $_POST['customer_id'];

    if (!empty($customerId)) {
        $stmt = $pdo->prepare("
            UPDATE sales
            SET customer_id = :customer_id
            WHERE sale_id = :sale_id
        ");

        $stmt->execute([
            ':customer_id' => $customerId,
            ':sale_id' => $saleId
        ]);

        header('Location: receipt_v2.php?sale_id=' . urlencode($saleId));
        exit();
    } else {
        $message = 'Please select a customer to link.';
    }
}

// Add new customer and link to sale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_and_link_customer'])) {
    $fullName = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['customer_description']);

    if (!empty($fullName) && !empty($phone)) {
        $stmt = $pdo->prepare("
            INSERT INTO customers (full_name, phone, customer_description)
            VALUES (:full_name, :phone, :customer_description)
        ");

        $stmt->execute([
            ':full_name' => $fullName,
            ':phone' => $phone,
            ':customer_description' => $description
        ]);

        $customerId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            UPDATE sales
            SET customer_id = :customer_id
            WHERE sale_id = :sale_id
        ");

        $stmt->execute([
            ':customer_id' => $customerId,
            ':sale_id' => $saleId
        ]);

        header('Location: receipt_v2.php?sale_id=' . urlencode($saleId));
        exit();
    } else {
        $message = 'Please enter both customer name and phone number.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Sale to Customer</title>
    <link rel="stylesheet" href="add_sale.css">

    <style>
        .link-container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
        }

        .section {
            margin-bottom: 30px;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 4px;
        }

        .customer-result {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .customer-result form {
            margin: 0;
        }

        input,
        textarea,
        button {
            padding: 10px;
            margin: 5px 0;
        }

        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
        }

        button,
        .btn-link {
            background: #007bff;
            color: #fff;
            border: none;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            padding: 10px 14px;
        }

        button:hover,
        .btn-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="link-container">
    <h1>Link Sale to Customer</h1>

    <p>
        <strong>Sale ID:</strong>
        <?php echo htmlspecialchars($saleId); ?>
    </p>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Search Existing Customer</h2>

        <form method="POST">
            <input
                type="text"
                name="customer_search"
                placeholder="Search by customer name or phone number"
                value="<?php echo htmlspecialchars($searchTerm); ?>"
            >

            <button type="submit" name="search_customer">Search Customer</button>
        </form>

        <?php if (!empty($searchTerm)): ?>
            <h3>Search Results</h3>

            <?php if ($customers): ?>
                <?php foreach ($customers as $customer): ?>
                    <div class="customer-result">
                        <div>
                            <strong><?php echo htmlspecialchars($customer['full_name']); ?></strong><br>
                            Phone: <?php echo htmlspecialchars($customer['phone']); ?>
                        </div>

                        <form method="POST">
                            <input
                                type="hidden"
                                name="customer_id"
                                value="<?php echo htmlspecialchars($customer['customer_id']); ?>"
                            >
                            <button type="submit" name="link_customer">Link This Customer</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No customer found.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Add New Customer</h2>

        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="full_name" placeholder="Enter customer full name" required>

            <label>Phone Number:</label>
            <input type="tel" name="phone" placeholder="Enter customer phone number" required>

            <label>Description:</label>
            <textarea name="customer_description" placeholder="Optional customer notes"></textarea>

            <button type="submit" name="add_and_link_customer">Add Customer and Link Sale</button>
        </form>
    </div>

    <a class="btn-link" href="receipt.php">Back to Original Receipt</a>
</div>

</body>
</html>