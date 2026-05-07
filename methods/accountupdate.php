<?php
if(isset($_POST["newUser"]))
{
    $user = $_POST["newUser"];
}
if(isset($_POST["newEmail"]))
{
    $email = $_POST["newEmail"];
}
if(isset($_POST["newPerms"]))
{
    $perms = $_POST["newPerms"];
}
if(isset($_POST["idToUpdate"]))
{
    $updateID = $_POST["idToUpdate"];
}
// Using inspect element would let you add edit buttons to the admin row and change the account, which shouldn't be possible. This prevents that from happening
if($updateID == 1) {
    echo "The admin account cannot be updated outside of the database.";
    exit;
}
// If the email is in the incorrect format, there'd be no way of finding the password without getting it from the database
$emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!($emailIsValid)) {
    echo "Email is in the incorrect format, please try again";
    exit;
}
// Makes sure that the entered username and email haven't already been added to the database (includes deleted users)
$userDupe = "SELECT Username FROM logininfo WHERE Username = '$user'";
$emailDupe = "SELECT Email FROM logininfo WHERE Email = '$email'";
$userQuery = mysqli_query($conn,$userDupe); 
$emailQuery = mysqli_query($conn,$emailDupe); 
$userTest = mysqli_num_rows($userQuery);
$emailTest = mysqli_num_rows($emailQuery);
if ($userTest > 0) { 
    echo "Error: username entered has already been used, try another";
    exit;
}  
else if ($emailTest > 0) { 
    echo "Error: email entered has already been used, try another";
    exit;
}  
$conn = mysqli_connect("", "", "", "");
// Updates the information for the account selected (ID taken from the row being changed)
$query = "UPDATE logininfo SET Username = '$user', Email = '$email', Permissions = '$perms' WHERE UserID = '$updateID'";
if(!$result = mysqli_query($conn, $query))
{
    echo "Error when attempting update: ".mysqli_error($conn);
}
else
{
    echo "Successfully updated database";
}
?> 