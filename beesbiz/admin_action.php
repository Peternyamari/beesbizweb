<?php
session_start();

// Check if the user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Establish database connection
$conn = new mysqli("localhost", "root", "", "beesbiz");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $table = $_POST['table'];
    $id = $_POST['id'];
    $idColumn = $_POST['id_column'];

    // Get foreign key constraints
    $sql = "SELECT table_name, column_name 
            FROM information_schema.key_column_usage 
            WHERE referenced_table_name = '$table' AND referenced_column_name = '$idColumn'";
    $result = $conn->query($sql);

    // Delete related records first
    while ($row = $result->fetch_assoc()) {
        $relatedTable = $row['table_name'];
        $relatedColumn = $row['column_name'];
        $deleteRelatedSql = "DELETE FROM $relatedTable WHERE $relatedColumn = ?";
        $relatedStmt = $conn->prepare($deleteRelatedSql);
        $relatedStmt->bind_param("i", $id);
        $relatedStmt->execute();
        $relatedStmt->close();
    }

    // Delete the main record
    $sql = "DELETE FROM $table WHERE $idColumn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo "Record deleted successfully.";
}

// Retrieve all table names
$tables = [];
$sql = "SHOW TABLES";
$result = $conn->query($sql);
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Handle search
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Actions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            font-size: 14px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-input {
            padding: 10px;
            font-size: 14px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
            font-size: 14px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #ff4d4d;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
        .message {
            text-align: center;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }
  .menuicons3{
  background-color:rgb(67, 143, 161);
  text-align: center;
  }
  .menuicons3 ul{
      list-style: none;
      display: inline-flex;
  }
  .menuicons3 ul li{
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
  .menuicons3 ul li a{
      text-decoration: none;
      color: white;
  }
    </style>
</head>
<body>
<div class="menuicons3">
<ul>
    <li><a href="admin_dashboard.php" >🔐Admin Dashboard</a></li>
</ul>
</div>

<div class="container">
    <h1>Admin Actions</h1>
    <?php
    // Display any session messages
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
    ?>
    <div class="search-form">
        <form method="POST">
            <input type="text" name="search" class="search-input" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>
    <?php
    foreach ($tables as $table) {
        echo "<h2>Table: $table</h2>";
        
        // Get columns
        $columns = [];
        $sql = "SHOW COLUMNS FROM $table";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        
        // Get data with search filter
        $sql = "SELECT * FROM $table";
        if (!empty($search)) {
            $searchConditions = [];
            foreach ($columns as $column) {
                $searchConditions[] = "$column LIKE '%$search%'";
            }
            $sql .= " WHERE " . implode(' OR ', $searchConditions);
        }
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            foreach ($columns as $column) {
                echo "<th>$column</th>";
            }
            echo "<th>Action</th>";
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
                }
                echo "<td>";
                echo "<form method='POST' style='display:inline-block;'>
                        <input type='hidden' name='table' value='$table'>
                        <input type='hidden' name='id' value='" . $row[$columns[0]] . "'>
                        <input type='hidden' name='id_column' value='" . $columns[0] . "'>
                        <button type='submit' name='delete' class='btn delete-btn'>Delete</button>
                      </form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }
    }
    $conn->close();
    ?>
    <div class="btn-container" style="text-align:center;">
        <a href="logout.php" class="btn">Logout</a>
    </div>
</div>
</body>
</html>
