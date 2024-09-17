<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Check if media_id and quantity are set in the POST request
if (isset($_POST['media_id'], $_POST['quantity'])) {
    // Sanitize input
    $media_id = (int)$_POST['media_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = (int)$_SESSION['user_id'];

    // Fetch media details from the database
    $conn = new mysqli("localhost:3306", "root", "", "beesbiz");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM media WHERE media_id = $media_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Check if the item already exists in the cart
        $cart_check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND media_id = $media_id";
        $cart_check_result = $conn->query($cart_check_sql);

        if ($cart_check_result->num_rows > 0) {
            // Update the quantity if item exists
            $update_sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND media_id = $media_id";
            $conn->query($update_sql);
        } else {
            // Add the item to the cart
            $insert_sql = "INSERT INTO cart (user_id, media_id, quantity) VALUES ($user_id, $media_id, $quantity)";
            $conn->query($insert_sql);
        }

        // Respond with success message
        echo "Item added to cart";
    } else {
        echo "Media item not found.";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
