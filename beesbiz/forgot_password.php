<?php
session_start();

// Database connection details
$conn = new mysqli("localhost:3306", "root", "", "beesbiz");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Check if the email exists in the database
    $sql = "SELECT id, username FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Check if the new password matches the confirmation
        if ($newPassword === $confirmPassword) {
            // Hash the new password before updating the database
            $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

            // Store the hashed password in the database
            $update_sql = "UPDATE users SET password='$hashed_password' WHERE id=" . $row["id"];
            $conn->query($update_sql);

            $success = "Password updated successfully.";
        } else {
            $error = "New password and confirmation do not match.";
        }
    } else {
        $error = "No user found with that email address.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Reset Password</title>
</head>
<style>
    #np{
    height:30px;
    width:300px;
    border-radius:4px;
    border:1px solid grey;  
    }
    #btnnp{
    height:30px;
    width:300px;
    border-radius:4px;
    border:1px solid grey;
    background-color: lightblue;
    color:white;
    }
    #btnnp:hover {
            background-color: rgba(1, 100, 62, 0.651);
            color:black;
        }
</style>
<body style="background-image: url('images/b1.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
<div class="whole_reset">
<?php
// Display success or error messages
if (isset($success)) {
    echo "<p style='color: green;'>$success</p>";
} elseif (isset($error)) {
    echo "<p style='color: red;'>$error</p>";
}
?>
<div class="reset">
<img src="images/icon1.png" height="50" width="50" alt=""> <br>
<form action="" method="post">
<h4>Reset Password</h4> <br>
    <input type="email" id="np" name="email" placeholder="Email" required><br> <br>

    <input type="password" id="np" name="new_password" placeholder="New Password" required><br> <br>

    <input type="password" id="np" name="confirm_password" placeholder="Confirm new Password" required><br> <br>

    <input type="submit" value="Reset Password" id="btnnp">
</form>

<p>Remember your password? <a href="login.php">Login here</a></p>
</div>
</div>
</body>
</html>
