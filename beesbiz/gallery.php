<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

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

// Retrieve comments and likes for each media item
$comments = [];
$likes = [];

$media_ids = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $media_ids[] = $row['media_id'];
    }
}

if (!empty($media_ids)) {
    $media_ids_str = implode(",", $media_ids);

    // Fetch comments
    $comment_result = $conn->query("
        SELECT c.*, u.username 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.media_id IN ($media_ids_str)
    ");
    if ($comment_result->num_rows > 0) {
        while ($row = $comment_result->fetch_assoc()) {
            $comments[$row['media_id']][] = $row;
        }
    }

    // Fetch likes
    $like_result = $conn->query("SELECT media_id, COUNT(*) as like_count FROM likes WHERE media_id IN ($media_ids_str) GROUP BY media_id");
    if ($like_result->num_rows > 0) {
        while ($row = $like_result->fetch_assoc()) {
            $likes[$row['media_id']] = $row['like_count'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
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
        .item img, .item video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .item a {
            display: block;
            margin-bottom: 10px;
            color: #3498db;
            text-decoration: none;
        }
        .item a:hover {
            text-decoration: underline;
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
      
      
        .scrollable-comments {
            max-height: 100px;
            overflow-y: auto;
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            text-align: left;
        }
        .scrollable-comments p {
            font-size: 10px;
            margin-bottom: 5px;
            word-break: break-all; /* Add this line to break words after 25 characters */
        }
        .scrollable-comments p strong {
            font-weight: bold;
        }
        .cart-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .cart-form label, .cart-form input[type="number"] {
            margin: 5px 0;
            font-size: 12px;
        }
        .cart-form input[type="submit"] {
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .cart-form input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .like-btn, .comment-btn {
            padding: 5px 10px;
            font-size: 12px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px 0;
        }
        .like-btn {
            background-color: #e67e22;
        }
        .like-btn:hover {
            background-color: #d35400;
        }
        .comment-btn {
            background-color: #9b59b6;
        }
        .comment-btn:hover {
            background-color: #8e44ad;
        }
        .comment-form textarea {
            width: 80%;
            font-size: 12px;
            padding: 5px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .comment-form input[type="submit"] {
            padding: 5px 10px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 5px;
        }
        .comment-form input[type="submit"]:hover {
            background-color: #27ae60;
        }
  .menuicons4{
  background-color:rgb(67, 143, 161);
  text-align: center;
  }
  .menuicons4 ul{
      list-style: none;
      display: inline-flex;
  }
  .menuicons4 ul li{
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
  .menuicons4 ul li a{
      text-decoration: none;
      color: white;
  }
    </style>
</head>
<body>

<div class="menuicons4">
<ul>
    <li><a href="cart.php" class="cart">🛒Cart</a></li>
    <li><a href="logout.php" class="logout">📤Logout</a></li>
    <li><a href="upload_media.php" class="upload">⬆️Upload</a></li>
    <li><a href="admin_login.php" class="admin">🔐Admin</a></li>
 </ul>
</div>
<br>
<div class="search-form">
    <form method="GET" action="gallery.php">
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
                $result->data_seek(0); // Reset result pointer to the beginning
                while ($row = $result->fetch_assoc()): 
                    if ($counter % 3 == 0 && $counter != 0) {
                        echo '</tr><tr>';
                    }
                    $counter++;
                ?>
                <td>
                    <div class="item">
                        <?php 
                        // Display media based on type
                        $file_path = 'uploads/' . $row['file_path'];
                        if (!empty($row['file_path']) && file_exists($file_path)) {
                            if ($row['media_type'] === 'photo') {
                                echo '<img src="' . $file_path . '" alt="Media Image">';
                            } elseif ($row['media_type'] === 'video') {
                                echo '<video controls>
                                        <source src="' . $file_path . '" type="' . mime_content_type($file_path) . '">
                                        Your browser does not support the video tag.
                                      </video>';
                            } elseif ($row['media_type'] === 'pdf') {
                                echo '<a href="' . $file_path . '" target="_blank">View PDF</a>';
                            } elseif ($row['media_type'] === 'document') {
                                echo '<a href="' . $file_path . '" target="_blank">Download Document</a>';
                            } else {
                                echo '<a href="' . $file_path . '" target="_blank">Download ' . $row['media_type'] . '</a>';
                            }
                        }
                        ?>
                        <h6><?php echo ucfirst($row['media_type']); ?></h6>
                        <h5>Item Name: <?php echo htmlspecialchars($row['item_name']); ?></h5>
                        <p>Description/Contact Info: <?php echo htmlspecialchars($row['contact_info']); ?></p>
                        <p>Price: $<span id="price_<?php echo $row['media_id']; ?>"><?php echo htmlspecialchars($row['price']); ?></span></p>
                        <form class="cart-form" data-media-id="<?php echo $row['media_id']; ?>">
                            <input type="hidden" name="media_id" value="<?php echo $row['media_id']; ?>">
                            <label for="quantity_<?php echo $row['media_id']; ?>">Qty:</label>
                            <input type="number" id="quantity_<?php echo $row['media_id']; ?>" name="quantity" value="1" min="1">
                            <p>Total: $<span id="total_<?php echo $row['media_id']; ?>"><?php echo htmlspecialchars($row['price']); ?></span></p>
                            <input type="submit" value="Add to Cart">
                        </form>
                        <div>
                            <form action="like.php" method="post">
                                <input type="hidden" name="media_id" value="<?php echo $row['media_id']; ?>">
                                <input type="submit" class="like-btn" value="&#x1F44D; Like">
                            </form>
                            <p>Likes: <?php echo isset($likes[$row['media_id']]) ? htmlspecialchars($likes[$row['media_id']]) : 0; ?></p>
                        </div>
                        <div>
                            <form action="comment.php" method="post" class="comment-form">
                                <input type="hidden" name="media_id" value="<?php echo $row['media_id']; ?>">
                                <textarea name="comment" rows="2" placeholder="Add a comment..."></textarea>
                                <input type="submit" class="comment-btn" value="&#x1F4AC; Comment">
                            </form>
                            <p>Comments: <?php echo isset($comments[$row['media_id']]) ? count($comments[$row['media_id']]) : 0; ?></p>
                        </div>
                        <div class="scrollable-comments">
                            <?php if (isset($comments[$row['media_id']])): ?>
                                <?php foreach ($comments[$row['media_id']] as $comment): ?>
                                    <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <?php endwhile; ?>
            </tr>
        </table>
    <?php else: ?>
        <p>No media items found.</p>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.cart-form').forEach(function(form) {
        const mediaId = form.dataset.mediaId;
        const priceElement = document.getElementById('price_' + mediaId);
        const totalElement = document.getElementById('total_' + mediaId);
        const quantityInput = document.getElementById('quantity_' + mediaId);

        // Update total price dynamically based on quantity
        quantityInput.addEventListener('input', function() {
            const quantity = parseInt(quantityInput.value);
            const price = parseFloat(priceElement.textContent);
            const total = quantity * price;
            totalElement.textContent = total.toFixed(2);
        });

        // Handle form submission via AJAX
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(form);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Item added to cart');
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
