<?php
if(isset($_POST["newUser"]))
{
    $user = $_POST["newUser"];
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
$conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
// Updates the information for the account selected (ID taken from the row being changed)
$query = "UPDATE logininfo SET Username = '$user', Permissions = '$perms' WHERE UserID = '$updateID'";
if(!$result = mysqli_query($conn, $query))
{
    echo "Error when attempting update: ".mysqli_error($conn);
}
else
{
    echo "Successfully updated database";
}
?> 