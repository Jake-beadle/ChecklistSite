<?php
session_start();
if(isset($_POST["idToRestore"]))
{
    $restoreID = $_POST["idToRestore"];
}
$username = $_SESSION['user'];
$conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
// Removes the data showing who deleted the entry then restores the entry to the website
$infoquery = "UPDATE checklistsinfo SET Deleted = 0, Deletedby = NULL, Dateofcheck = CURRENT_TIMESTAMP WHERE ComputerID = $restoreID"; 
$checkquery = "UPDATE checklists SET Deleted = 0, Datechecked = CURRENT_TIMESTAMP WHERE ChecklistID = $restoreID"; 
if(!$inforesult = mysqli_query($conn, $infoquery))
{
    echo "Error when attempting update: ".mysqli_error($conn);
}
else
{
    if(!$checkresult = mysqli_query($conn, $checkquery))
    {
        echo "Error when attempting update: ".mysqli_error($conn);
    }
    else
    {
        echo "Successfully updated database";
    }
}
?>