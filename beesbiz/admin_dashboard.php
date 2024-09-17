<?php
session_start();

// Check if the user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Establish database connection
$conn = new mysqli("localhost:3306", "root", "", "beesbiz");

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $update_query = "UPDATE orders SET status = '$status' WHERE order_id = '$order_id'";
    $conn->query($update_query);
}

// Handle search
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Query to fetch orders by user names and calculate totals
$order_query = "SELECT users.username, users.email, orders.order_id, orders.item_name, orders.quantity, orders.price, 
                orders.phone_number, orders.order_date, orders.status, orders.location, (orders.price * orders.quantity) AS total_cost
                FROM orders 
                JOIN users ON orders.user_id = users.id";

if (!empty($search)) {
    $order_query .= " WHERE users.username LIKE '%$search%' OR orders.item_name LIKE '%$search%' OR users.email LIKE '%$search%'";
}

$order_query .= " ORDER BY users.username ASC, orders.order_id ASC";  // Assuming 'order_id' exists in the 'orders' table

$order_result = $conn->query($order_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3, h4, h5 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
        }
        h5 {
            margin-bottom: 10px;
        }
        .message {
            text-align: center;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .btn-container {
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
            font-size: 12px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .status-form {
            display: inline-block;
        }
        .print-btn {
            margin-bottom: 100px;
            background-color: #4caf50;
        }
        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-input {
            padding: 10px;
            font-size: 14px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
       
  .menuicons2{
  background-color:rgb(67, 143, 161);
  text-align: center;
  }
  .menuicons2 ul{
      list-style: none;
      display: inline-flex;
  }
  .menuicons2 ul li{
      background-color: black;
      color: white;
      margin: 10px;
      padding-top: 2px;
      padding-bottom: 2px;
      padding-right: 30px;
      padding-left: 30px;
      border-radius: 10px;
  }
  a:hover {
      background-color: rgba(1, 100, 62, 0.651);
      padding-top: 2px;
      padding-bottom: 2px;
      padding-right: 30px;
      padding-left: 30px;
      border-radius: 10px;
  }
  .menuicons2 ul li a{
      text-decoration: none;
      color: white;
  }
    </style>
    <script>
        function printOrders(username) {
            const printContents = document.getElementById('orders-' + username).innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</head>
<body>

<div class="menuicons2">
    <ul>
        <li><a href="gallery.php" >🛍️Gallery</a></li>
        <li><a href="admin_action.php" >🔐Admin Action</a></li>
    </ul>
</div>
<br>
<div class="container">
    <h2>Welcome to Admin Orders Panel</h2>
    <?php
    // Display any session messages
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
    ?>
    <div class="btn-container">
        <a href="logout.php" class="btn">Logout</a>
    </div>
    <h3>Orders List</h3>
    <div class="search-form">
        <form method="POST">
            <input type="text" name="search" class="search-input" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>
    <?php if ($order_result->num_rows > 0): ?>
        <?php
        $current_user = '';
        $user_total = 0;
        $item_number = 1;
        ?>
        <?php while ($row = $order_result->fetch_assoc()): ?>
            <?php
            if ($current_user !== $row['username']) {
                if ($current_user !== '') {
                    // Display total for the previous user
                    echo '<tr style="background-color: #f2f2f2;"><td colspan="9" style="text-align: right;"><strong>Total for ' . htmlspecialchars($current_user) . ':</strong></td>';
                    echo '<td colspan="3"><strong>$' . number_format($user_total, 2) . '</strong></td></tr>';
                    echo '</tbody></table></div>';
                    echo '<button class="btn print-btn" onclick="printOrders(\'' . htmlspecialchars($current_user) . '\')">Print</button>';
                }
                // Reset for the new user
                $current_user = $row['username'];
                $user_total = 0;
                $item_number = 1;
                echo '<div id="orders-' . htmlspecialchars($current_user) . '">';
                ?>
                <!-- Display user information -->
                <table>
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Order Date</th>
                        <th colspan="7"></th>
                    </tr>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Update Status</th>
                    </tr>
                    </thead>
                    <tbody>
                <?php
            }
            $user_total += $row['total_cost'];
            ?>
            <tr>
                <td><?php echo $item_number++; ?></td>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td>$<?php echo number_format($row['total_cost'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td>
                    <form method="post" class="status-form">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <select name="status">
                            <option value="On Board" <?php echo $row['status'] == 'On Board' ? 'selected' : ''; ?>>On Board</option>
                            <option value="Delivered" <?php echo $row['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Received" <?php echo $row['status'] == 'Received' ? 'selected' : ''; ?>>Received</option>
                        </select>
                        <button type="submit" name="update_status" class="btn">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php
        // Display total for the last user
        if ($current_user !== '') {
            echo '<tr style="background-color: #f2f2f2;"><td colspan="9" style="text-align: right;"><strong>Total for ' . htmlspecialchars($current_user) . ':</strong></td>';
            echo '<td colspan="3"><strong>$' . number_format($user_total, 2) . '</strong></td></tr>';
            echo '</tbody></table></div>';
            echo '<button class="btn print-btn" onclick="printOrders(\'' . htmlspecialchars($current_user) . '\')">Print</button>';
        }
        ?>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</div>
</body>
</html>
