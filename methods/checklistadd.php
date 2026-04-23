<?php
    session_start();
    $conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
        if(isset($_POST["pcname"]))
        {
            $pcName = $_POST["pcname"];
        }
        
        $plant = $_POST["plant"];

        if(isset($_POST["sub"]))
        {
            $subLocation = $_POST["sub"];
        }

        if(isset($_SESSION["user"])) 
        {
            $createdBy = $_SESSION['user'];
        }
    // Checks to see if a PC has already been added with the same name (to prevent confusion)
    $dupequerytouse = "SELECT PCname FROM checklistsinfo WHERE PCname = '$pcName' AND Deleted = 0";
    $dupequery = mysqli_query($conn,$dupequerytouse);
    $dupetest = mysqli_num_rows($dupequery);
    if ($dupetest > 0) { 
        echo "Error: this PC name has already been entered, try again";
        exit;
    }
    // Adds the information from the form into the database that stores it
    $infoquery = "INSERT INTO checklistsinfo (PCname, Plant, Sublocation, Createdby) VALUES('$pcName', '$plant','$subLocation', '$createdBy')";
    if(!$result = mysqli_query($conn, $infoquery))
    {
        echo "Error: ".mysqli_error($conn);
    }
    else
    {
        echo "Successfully added to database";
    }   
    // After the other information has been added, it gets the ID from the newly created row
    $idquery = "SELECT ComputerID from checklistsinfo WHERE PCname = '$pcName'";
    $idresult = mysqli_query($conn, $idquery);
    $idfetch = mysqli_fetch_assoc($idresult);
    $id = $idfetch["ComputerID"];
    // This is used to create a neew checklist for that computer, adding the ID of that computer as a foreign key
    $checklistquery = "INSERT INTO checklists (ChecklistID) VALUES ('$id')";
    $checklistresult = mysqli_query($conn,$checklistquery);
?>