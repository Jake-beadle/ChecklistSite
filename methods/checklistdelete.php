<?php
    session_start();
    if(isset($_POST["idToDelete"]))
        {
            $deleteID = $_POST["idToDelete"];
        }
    $username = $_SESSION['user'];
    $conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
    // Updates the info table to say who it was deleted by (only visible in the database)
    // Then updates both the info and checklist table to set the computer as deleted
    // Note: If trying to restore one of the PCs/checklists, make sure that you set Deleted = 0 in both checklists and checklistsinfo or else the site will not open
    $infoquery = "UPDATE checklistsinfo SET Deleted = 1, Deletedby = '$username' WHERE ComputerID = $deleteID"; 
    $checkquery = "UPDATE checklists SET Deleted = 1 WHERE ChecklistID = $deleteID"; 
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