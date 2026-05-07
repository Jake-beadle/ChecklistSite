<?php
session_start();
// If the user is logged in, they shouldn't need a password reset, so they would be sent back to the main page
if (isset($_SESSION['user']) && isset($_SESSION['perms'])){ 
    header("Location: /main.php");
}
// Creates a connection with the database
$conn = mysqli_connect("", "", "", "");
// Gets the token from the url, then runs tests on it to make sure that it can be used (if not, redirects the user to the login page to try again)
$urltoken = str_replace("/resetpass.php?token=", "", $_SERVER['REQUEST_URI']);
// It first checks that the token is valid (64 characters long)
if (strlen($urltoken) != 64) {
    $_SESSION['reseterr'] = 'Token was invalid (not 64 characters), redirected to login page.';
    header("Location: /login.php");
} else {
    $tokenquery = "SELECT * FROM resettokens WHERE Token = '$urltoken'";
    $tokenresult = mysqli_query($conn,$tokenquery);
    $tokentest = mysqli_num_rows($tokenresult);
    // It then checks that the token is in the database that stores the tokens
    if ($tokentest == 0) {
        $_SESSION['reseterr'] = '<script>alert("Token was invalid (already used/not found), redirected to login page.")</script>';
        header("Location: /login.php");
    } else {
        $values = mysqli_fetch_assoc($tokenresult);
        $_SESSION['token'] = $urltoken;
        $_SESSION['userid'] = $values["UserID"];
        $expiry = $values["ExpiryTime"];
        $currtime = time();
        // Finally, it checks its expiry time to check if the token has expired or not
        if ($currtime > $expiry) {
            $_SESSION['reseterr'] = '<script>alert("Token was invalid (expired), redirected to login page.")</script>';
            header("Location: /login.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="./scripts/jquery-3.7.1.min.js"></script>
    <script src="./scripts/resetpass.js"></script>
    <title>PCRC Password Reset</title>
</head>
<body>
    <img src="https://internal.britishsteel.uk.com/img/logo.svg">
    <h1>Reset your password</h1>
    <legend>Enter the new password for your account:</legend><br>
    <form id="reset" action="" method="post">
        <label for="newpass">Password:</label>
        <input type="password" id="newpass" name="newpass" placeholder="Enter a new password" required><br>
        <label for="confpass">Confirm password:</label>
        <input type="password" id="confpass" name="confpass" placeholder="Enter the same password" required><br><br>
        <input type="submit">
    </form>
    <p id="result"><br>
</body>
</html>