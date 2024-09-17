<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

$conn = new mysqli("localhost:3306", "root", "", "beesbiz");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM media");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            grid-gap: 20px;
        }
        .item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .item h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .item p {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .item form {
            margin-top: 10px;
        }
        .item form input[type="number"] {
            width: 50px;
        }
        .item form input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .item form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="item">
                <?php 
                // Output image if file_path is set and file exists
                if (!empty($row['file_path']) && file_exists('uploads/' . $row['file_path'])): ?>
                    <img src="uploads/<?php echo $row['file_path']; ?>" alt="Media Image">
                <?php endif; ?>
                <h3><?php echo $row['media_type']; ?></h3>
                <p>Contact Info: <?php echo $row['contact_info']; ?></p>
                <p>Price: $<?php echo $row['price']; ?></p>
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="media_id" value="<?php echo $row['media_id']; ?>">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity_<?php echo $row['media_id']; ?>" name="quantity" value="1" min="1">
                    <p>Total: $<span id="total_<?php echo $row['media_id']; ?>"><?php echo $row['price']; ?></span></p>
                    <input type="submit" value="Add to Cart">
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No media items found.</p>
    <?php endif; ?>
</div>

<script>
    // Update total price dynamically based on quantity
    document.querySelectorAll('input[type="number"]').forEach(function(input) {
        input.addEventListener('input', function() {
            const quantity = parseInt(input.value);
            const price = parseFloat(input.closest('.item').querySelector('p>span').textContent);
            const total = quantity * price;
            input.closest('.item').querySelector('p>span').textContent = total.toFixed(2);
        });
    });
</script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
 <a href="logout.php">Logout</a> <br><a href="gallery.php">Gallery</a>