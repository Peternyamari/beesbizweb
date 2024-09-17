<?php
session_start();

$conn = new mysqli("localhost", "root", "", "beesbiz");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $result = $conn->query("SELECT * FROM media WHERE media_type LIKE '%$search_query%' OR item_name LIKE '%$search_query%' OR price LIKE '%$search_query%'");
} else {
    $result = $conn->query("SELECT * FROM media");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available</title>
    <style>
        body {
            font-family: Verdana, sans-serif;      
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 10px;
            vertical-align: top;
        }
        .item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .item h6, .item p {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-form input[type="text"] {
            padding: 5px;
            font-size: 12px;
            width: 200px;
        }
        .search-form input[type="submit"] {
            padding: 5px 10px;
            font-size: 12px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .bt{
            background-color: grey;
            color: white;
            margin:10px;
            border-radius: 10px;
        }
        a{
            text-decoration: none;
            color: white;
            background-color:darkgreen;
            padding-right:20px;
            padding-left:20px;
            border-radius:5px;
        }
        a:hover{
            background-color:red;
        }
    </style>
</head>
<body>
<div class="bt">
     <a href="login.php">🗝️Login</a>   
</div>
<p style="background-color: #f0f0f0;"><small>Please note that for you to place an order, you must sign up. If you're already a member, simply log in.</small></p>

<div class="search-form">
    <form method="GET" action="available.php">
        <input type="text" name="search" placeholder="Search for media..." value="<?php echo htmlspecialchars($search_query); ?>">
        <input type="submit" value="Search">
    </form>
</div>

<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <?php
                $counter = 0;
                while ($row = $result->fetch_assoc()): 
                    if ($counter % 3 == 0 && $counter != 0) {
                        echo '</tr><tr>';
                    }
                    $counter++;
                ?>
                <td>
                    <div class="item">
                        <?php 
                        // Display image if media type is photo
                        $file_path = 'uploads/' . $row['file_path'];
                        if (!empty($row['file_path']) && file_exists($file_path) && $row['media_type'] === 'photo') {
                            echo '<img src="' . $file_path . '" alt="Media Image">';
                        }
                        ?>
                        <h6><?php echo ucfirst($row['media_type']); ?></h6>
                        <h5>Item Name: <?php echo htmlspecialchars($row['item_name']); ?></h5>
                        <p>Description/Contact Info: <?php echo htmlspecialchars($row['contact_info']); ?></p>
                        <p>Price: $<?php echo htmlspecialchars($row['price']); ?></p>
                    </div>
                </td>
                <?php endwhile; ?>
            </tr>
        </table>
    <?php else: ?>
        <p>No media items found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
