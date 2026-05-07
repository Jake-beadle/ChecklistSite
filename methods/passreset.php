<?php
session_start();
if(isset($_POST["newpass"]))
{
    $newPass = $_POST["newpass"];
}
if(isset($_POST["confpass"]))
{
    $confPass = $_POST["confpass"];
}
// Gets the user's id from the other page
if(isset($_SESSION['userid'])) {
    $userID = $_SESSION['userid'];
}
if(isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
}
// Makes sure that the 'password' and the 'confirm password' fields are the same
if (!($newPass == $confPass)) { 
    echo "The passwords entered are not the same, please try again";
    exit;
} else {
    $passHas7characters = (mb_strlen($newPass) >= 7);
    $passNotAbove20characters = (mb_strlen($newPass) <= 20);
    $passIsAlphanumeric = ctype_alnum($newPass);
    if(!($passHas7characters && $passNotAbove20characters && $passIsAlphanumeric)) {
        echo "Invalid password entered (must be 7-20 characters long, and can only use alphanumeric characters)";
    } else {
        $conn = mysqli_connect("", "", "", "");
        // Then hashes the password and adds it to the database for that user
        $hashPass = password_hash($newPass, PASSWORD_DEFAULT); 
        $query = "UPDATE logininfo SET HashedPassword = '$hashPass' WHERE UserID = '$userID'";
        if(!$result = mysqli_query($conn, $query))
        {
            echo "Error when attempting update: ".mysqli_error($conn);
        }
        else
        {
            echo "Successfully updated database";
            // If it worked properly, it removes the row with that token so that it can't be reused
            $tokenQuery = "DELETE FROM resettokens WHERE Token = '$token'";
            $remove = mysqli_query($conn, $tokenQuery);
        }
    }
}
?> 