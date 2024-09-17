<?php
session_start();

// Check if the user is not logged in, redirect to login.php if true
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add centering styles */
        .whole_home {
            text-align: center;
            background-repeat: no-repeat;
            background-size: cover;
            height: 600px;
            height:600px;
        }

        /* Style the button */
        .upload-button {
            display: inline-block;
            padding: 5px 50px;
            background-color: darkgreen;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .menu a{
            text-decoration: none;
        }
    </style>
</head>
<body style="background-image: url('images/b4.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="whole_home">
        <br> <br> <br> <br> <br> <br> <br> <br>
        <h2>Welcome to the Home Page</h2>
        <p>This is the main page that requires login.</p>
        <h1>WELCOME TO BEESBIZ.COM</h1>
        <a href="upload_media.php" class="upload-button">Upload</a> 
        <span style="margin: 0 10px"></span>
        <a href="gallery.php" class="upload-button">Gallery</a>
         <span style="margin: 0 10px"></span>
        <a href="admin_login.php" class="upload-button">Admin Login</a>
        <span style="margin: 0 10px"></span>
        <a href="logout.php" class="upload-button">Logout</a> 
        <div class="menu">
 </div>

    </div>
</body>
</html>

