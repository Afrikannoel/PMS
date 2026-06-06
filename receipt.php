<?php
session_start();
include 'db.php'; // Include the database connection

// Check if there is a receipt stored in the session
if (!isset($_SESSION['receipt'])) {
    header('Location: add_sale.php'); // Redirect to add_sale if no receipt exists
    exit();
}

// Retrieve receipt data from session
$receipt = $_SESSION['receipt'];
$totalAmount = $receipt['total_amount'];
$discount = $receipt['discount'];
$paymentMethod = $receipt['payment_method'];
$amountPaid = $receipt['amount_paid']; // New field for amount paid
$cartItems = $receipt['cart'];

// Calculate final total after discount
$finalTotal = $totalAmount - $discount;

// Calculate the balance
$balance = $amountPaid - $finalTotal;

// Clear the receipt from session after use
unset($_SESSION['receipt']);

// Check if there is a posted form request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the customer ID from the form
    $customerId = isset($_POST['customer_id']) ? $_POST['customer_id'] : null;

    // If no customer ID is provided, default to 1
    if (empty($customerId)) {
        $customerId = 1; // Default customer ID
    }

    // Assuming you have a sales table and you're inserting a new sale
    $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
    
    // Insert sale into the sales table
    $sql = "INSERT INTO sales (user_id, customer_id, total_amount, discount, sale_date, payment_method) 
            VALUES (:user_id, :customer_id, :total_amount, :discount, NOW(), :payment_method)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':customer_id' => $customerId,
        ':total_amount' => $finalTotal,
        ':discount' => $discount,
        ':payment_method' => $paymentMethod
    ]);

    // Redirect or show a success message after linking
    header('Location: receipt_page.php'); // Redirect to a receipt or confirmation page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="receipt.css"> <!-- Link to your CSS -->
    <style>
        /* Add styles for customer results display */
        #customerResults {
            margin-top: 10px;
        }
        .customer-item {
            padding: 5px;
            border: 1px solid #ccc;
            margin-bottom: 5px;
        }
        /* Style for new customer form */
        .new-customer-container {
            background: #f9f9f9;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .new-customer-container h2 {
            margin-bottom: 15px;
            color: #007BFF;
        }
        .new-customer-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .new-customer-container input,
        .new-customer-container textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .new-customer-container button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .new-customer-container button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleCustomerSearch() {
            var customerSearch = document.getElementById('customerSearch');
            customerSearch.style.display = customerSearch.style.display === 'none' ? 'block' : 'none';
        }

        function searchCustomer() {
            var query = document.getElementById('customerSearchInput').value;

            // Check if the input is empty
            if (!query) {
                alert('Please enter a customer name or phone number to search.');
                return;
            }

            // Make AJAX request to search for the customer
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'search_customer.php', true); // Your PHP file that handles the search
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var resultsDiv = document.getElementById('customerResults');
                    resultsDiv.innerHTML = ''; // Clear previous results

                    if (response.length > 0) {
                        // Display customers found
                        response.forEach(function (customer) {
                            var customerDiv = document.createElement('div');
                            customerDiv.className = 'customer-item';
                            customerDiv.innerHTML = 'Name: ' + customer.full_name + ', Phone: ' + customer.phone + 
                                ' <button onclick="linkSaleToCustomer(' + customer.customer_id + ')">Link Sale</button>';
                            resultsDiv.appendChild(customerDiv);
                        });
                    } else {
                        // No customers found
                        alert('No customer found with that name or phone number.');
                    }
                }
            };
            xhr.send('query=' + encodeURIComponent(query));
        }

        function linkSaleToCustomer(customerId) {
            // Update the hidden input with the selected customer ID
            document.getElementById('selectedCustomerId').value = customerId;
            alert('Selected customer ID: ' + customerId);
        }

        function submitNewCustomerForm() {
            // Get form data
            var fullName = document.getElementById('customerName').value;
            var phone = document.getElementById('customerPhone').value;
            var description = document.getElementById('customerDescription').value;

            // Validate form fields
            if (!fullName || !phone) {
                alert('Please enter both the full name and phone number.');
                return;
            }

            // Create an AJAX request to submit the customer data to the server
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_customer.php', true); // add_customer.php will handle customer addition
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === 'success') {
                        alert('Customer added successfully!');
                        // Optionally, clear the form after success
                        document.getElementById('newCustomerForm').reset();
                    } else {
                        alert('Failed to add customer. Please try again.');
                    }
                }
            };

            // Send the form data
            xhr.send('full_name=' + encodeURIComponent(fullName) + '&phone=' + encodeURIComponent(phone) + '&customer_description=' + encodeURIComponent(description));
        }
    </script>
</head>
<body>
    <!-- Hidden input to store the selected customer ID -->
    <input type="hidden" id="selectedCustomerId" name="customer_id" value="">

    <!-- Print and back buttons -->
    <div class="button-group">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.location.href='add_sale.php'">Go Back to Add Sale</button>
        <button id="linkCustomerBtn" onclick="toggleCustomerSearch()">Link Sale to Customer</button>
    </div>
    
    <div class="receipt-container">
        <h1>Receipt</h1>
        
        <h2>Items Purchased</h2>
        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['drug_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($item['unit_price'], 2)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($item['total_price'], 2)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Summary</h2>
        <table class="receipt-table">
            <tr>
                <th>Total Amount</th>
                <td>KSh <?php echo number_format($totalAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Discount</th>
                <td>KSh <?php echo number_format($discount, 2); ?></td>
            </tr>
            <tr>
                <th>Amount Paid</th>
                <td>KSh <?php echo number_format($amountPaid, 2); ?></td>
            </tr>
            <tr>
                <th>Balance</th>
                <td>KSh <?php echo number_format($balance, 2); ?></td>
            </tr>
        </table>
    </div>

    <!-- Customer search and linking section -->
    <div id="customerSearch" style="display:none;">
        <h2>Link Sale to Customer</h2>
        <input type="text" id="customerSearchInput" placeholder="Search by name or phone" />
        <button onclick="searchCustomer()">Search Customer</button>
        
        <div id="customerResults"></div>
    </div>

    <!-- New customer form -->
    <div class="new-customer-container">
        <h2>Add New Customer</h2>
        <form id="newCustomerForm" onsubmit="event.preventDefault(); submitNewCustomerForm();">
            <label for="customerName">Full Name:</label>
            <input type="text" id="customerName" name="full_name" placeholder="Enter customer full name" />

            <label for="customerPhone">Phone Number:</label>
            <input type="tel" id="customerPhone" name="phone" placeholder="Enter customer phone number" />

            <label for="customerDescription">Description (Optional):</label>
            <textarea id="customerDescription" name="customer_description" placeholder="Enter additional information"></textarea>

            <button type="submit">Add Customer</button>
        </form>
    </div>

</body>
</html>
