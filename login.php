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
    <script src="./jquery-3.7.1.min.js"></script>
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

<script>
    $(document).ready(function(){
        $("#login").submit(function(event){
            event.preventDefault();
            var data = $(this).serializeArray();
                $.post("./methods/accountlogin.php", data, function(response) {
                    // Once the data for logging in has been submitted, accountlogin.php will return a response depending on what happened (saved here)
                    var res = JSON.parse(response);
                    // If the details entered were valid, it will have set the session data, so the user should be redirected to the main page
                    if (res.status == "ok") {
                        window.location.href = "/main.php"
                    // If the details weren't valid, it returns a message explaining what went wrong to the user
                    } else {
                        $("#result").html(res.msg)
                    }
            })
        })
    })
</script>