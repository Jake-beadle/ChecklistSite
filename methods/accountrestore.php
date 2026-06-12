<?php
session_start();
if(isset($_POST["idToRestore"]))
{
    $restoreID = $_POST["idToRestore"];
}
$username = $_SESSION['user'];
$conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
$query = "UPDATE logininfo SET Deleted = 0 WHERE UserID = $restoreID"; // Sets deleted to 0 which adds it back to the website
if(!$result = mysqli_query($conn, $query))
{
    echo "Error when attempting update: ".mysqli_error($conn);
}
else
{
    echo "Successfully updated database";
}
?>