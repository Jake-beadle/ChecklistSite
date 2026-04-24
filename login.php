<?php
session_start();
if (isset($_SESSION['user']) && isset($_SESSION['perms'])){
    header("Location: /main.php");  // If session data has already been set, redirects to /main.php
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./scripts/jquery-3.7.1.min.js"></script>
    <script src="./scripts/login.js"></script>
    <title>Login page</title>
</head>
<body>
    <img src="https://internal.britishsteel.uk.com/img/logo.svg"><br> 
    <!--Below is the form to login with -->
    <h1>Login Page</h1>
    <legend>Please enter your username and password below to access the checklist.</legend><br>
    <form id="login" action="" method="post">
        <label for="username">Username:</label>
        <input type="text" id="user" name="user" placeholder="Enter a username here" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="pass" name="pass" placeholder="Enter a password here" required><br><br>
        <input type="submit">
    </form>
    <p id="result">
</body>
</html>