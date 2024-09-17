<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Establish database connection
$conn = new mysqli("localhost:3306", "root", "", "beesbiz");

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user's ID from session
$user_id = $_SESSION['user_id'];

// Fetch the user's name
$user_result = $conn->query("SELECT username FROM users WHERE id = '$user_id'");
if ($user_result->num_rows == 0) {
    die("User not found.");
}
$user_name = $user_result->fetch_assoc()['username'];

// Fetch cart items for the user
$cart_result = $conn->query("SELECT cart.id AS cart_id, media.item_name, media.price, cart.quantity 
                            FROM cart 
                            JOIN media ON cart.media_id = media.media_id 
                            WHERE cart.user_id = '$user_id'");

$cart_items = [];
if ($cart_result->num_rows > 0) {
    while ($row = $cart_result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Small font size similar to h5 */
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h2, h3 {
            text-align: center;
            font-size: 16px; /* Adjust font size to h5 equivalent */
        }
        .message {
            text-align: center;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px; /* Small font size */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px; /* Small font size */
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd; /* Borders for rows and columns */
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
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px; /* Small font size */
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #e53935;
        }
        .scroll-container {
            max-height: 300px; /* Adjust as needed */
            overflow-y: auto;
        }
  .menuicons8{
  background-color:rgb(67, 143, 161);
  text-align: center;
  }
  .menuicons8 ul{
      list-style: none;
      display: inline-flex;
  }
  .menuicons8 ul li{
      background-color: black;
      color: white;
      margin: 10px;
      padding-top: 2px;
      padding-bottom: 2px;
      padding-right: 30px;
      padding-left: 30px;
      border-radius: 10px;
      font-size:smaller;
  }
  a:hover {
      background-color: rgba(1, 100, 62, 0.651);
      padding-top: 2px;
      padding-bottom: 2px;
      padding-right: 30px;
      padding-left: 30px;
      border-radius: 10px;
  }
  .menuicons8 ul li a{
      text-decoration: none;
      color: white;
  }
    </style>
</head>
<body>
<div class="menuicons8">
    <ul>
        <li><a href="cart.php">Back to Cart</a></li>
        <li><a href="gallery.php">Continue Shopping</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="upload_media.php">Upload</a></li>
    </ul>
     
</div>
<div class="container">
    <h2>Checkout</h2>
    <h3>Welcome, <?php echo htmlspecialchars($user_name); ?></h3>
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
    ?>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <div class="scroll-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($cart_items as $index => $item): ?>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" style="text-align: right;">Total:</td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="btn-container">
                <a href="place_order.php" class="btn">Place Order</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
