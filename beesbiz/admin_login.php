<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password are correct (you'll need to modify this part)
    if ($username === "admin" && $password === "admin321") {
        $_SESSION['user_id'] = 1; // Assuming admin user ID is 1
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error_message = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            padding-bottom: 90px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #ff0000;
            margin-bottom: 10px;
            text-align: center;
        }
  .menuicons6{
  background-color:rgb(67, 143, 161);
  text-align: center;
  }
  .menuicons6 ul{
      list-style: none;
      display: inline-flex;
  }
  .menuicons6 ul li{
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
  .menuicons6 ul li a{
      text-decoration: none;
      color: white;
  }
    </style>
</head>
<body style="background-image: url('images/b3.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
<div class="menuicons6">
    <ul>
        <li><a href="Logout.php">📤Logout</a></li>
        <li><a href="home.php">🏠Home</a></li>
    </ul>
</div>

<div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="admin_login.php" method="post">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>      
    </div>
    
</body>
</html>
