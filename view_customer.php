<?php
// Include database connection
include('db.php');

// Check if customer_id is provided in the URL
if (!isset($_GET['customer_id'])) {
    die("Customer ID not provided.");
}

// Get the customer ID from the URL
$customer_id = $_GET['customer_id'];

// Fetch customer details
$customer_query = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :customer_id LIMIT 1");
$customer_query->execute(['customer_id' => $customer_id]);
$customer = $customer_query->fetch(PDO::FETCH_ASSOC);

// Check if the customer exists
if (!$customer) {
    die("Customer not found.");
}

// Pagination setup
$limit = 10; // Number of sales per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Calculate the offset for SQL query

// Date filtering setup
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Prepare the base query for sales
$sales_query_base = "SELECT sales.sale_id, sales.total_amount, sales.discount, sales.amount_paid, sales.balance, sales.sale_date, sales.payment_method
                     FROM sales 
                     WHERE sales.customer_id = :customer_id";

// Append date filtering to the query if dates are provided
if ($date_from) {
    $sales_query_base .= " AND sales.sale_date >= :date_from";
}
if ($date_to) {
    $sales_query_base .= " AND sales.sale_date <= :date_to";
}

$sales_query_base .= " ORDER BY sales.sale_date DESC LIMIT :limit OFFSET :offset";

// Prepare the sales query
$sales_query = $pdo->prepare($sales_query_base);
$sales_query->bindParam(':customer_id', $customer_id);
$sales_query->bindParam(':limit', $limit, PDO::PARAM_INT);
$sales_query->bindParam(':offset', $offset, PDO::PARAM_INT);

// Bind date parameters if provided
if ($date_from) {
    $sales_query->bindParam(':date_from', $date_from);
}
if ($date_to) {
    $sales_query->bindParam(':date_to', $date_to);
}

// Execute the sales query
$sales_query->execute();
$sales = $sales_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch total sales for pagination
$total_sales_query = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE customer_id = :customer_id");
$total_sales_query->execute(['customer_id' => $customer_id]);
$total_sales_count = $total_sales_query->fetchColumn();
$total_pages = ceil($total_sales_count / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customer</title>
    <link rel="stylesheet" href="view_customer.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <div class="buttons">
            <a href="manage_customers.php" class="btn">Go Back</a>
            <a href="edit_customer.php?customer_id=<?php echo $customer['customer_id']; ?>" class="btn">Edit Customer</a>
        </div>
        
        <h1>Customer Details</h1>
        <table>
            <tr>
                <th>Customer ID:</th>
                <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
            </tr>
            <tr>
                <th>Full Name:</th>
                <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo htmlspecialchars($customer['email']); ?></td>
            </tr>
            <tr>
                <th>Address:</th>
                <td><?php echo htmlspecialchars($customer['address']); ?></td>
            </tr>
            <tr>
                <th>Description:</th>
                <td><?php echo htmlspecialchars($customer['customer_description']); ?></td>
            </tr>
        </table>

        <h2>Sales Linked to This Customer</h2>
        
        <!-- Date Filtering Form -->
        <form method="GET" action="">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <label for="date_from">From:</label>
            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            <label for="date_to">To:</label>
            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            <button type="submit" class="btn">Filter</button>
        </form>

        <?php if (!empty($sales)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Sale Date</th>
                        <th>Payment Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo $sale['sale_id']; ?></td>
                            <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td><?php echo number_format($sale['discount'], 2); ?></td>
                            <td><?php echo number_format($sale['amount_paid'], 2); ?></td>
                            <td><?php echo number_format($sale['balance'], 2); ?></td>
                            <td><?php echo $sale['sale_date']; ?></td>
                            <td><?php echo $sale['payment_method']; ?></td>
                            <td>
                                <a href="view_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn">View Sale</a>
                                <a href="edit_sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn">Edit Sale</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?customer_id=<?php echo $customer_id; ?>&page=<?php echo $i; ?>&date_from=<?php echo htmlspecialchars($date_from); ?>&date_to=<?php echo htmlspecialchars($date_to); ?>" class="page-btn"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p>No sales found for this customer.</p>
        <?php endif; ?>
    </div>
</body>
</html>
