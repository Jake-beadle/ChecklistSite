<?php
    session_start();
    // Declares all of the variables to be added to the info table
    if(isset($_POST["newName"]))
        {
            $name = $_POST["newName"];
        }
    if(isset($_POST["newPlant"]))
        {
            $plant = $_POST["newPlant"];
        }
    if(isset($_POST["newSub"]))
        {
            $sub = $_POST["newSub"];
        }
    if(isset($_POST["checklistArray"]))
        {
            $checkarray = $_POST["checklistArray"];
        }
    if(isset($_POST["idToUpdate"]))
        {
            $updateID = $_POST["idToUpdate"];
        }
    $username = $_SESSION['user']; 
    // The names of each of the checks in the checklist table (used to set up the query)
    $checknames = ['Sentinelinstall','Windowsupdate','Deviceenrolled','Assettag','Localreset','Diskcheck','Antivirusupdated','Userdata','Networktest','Softwareinstall','Deleted'];
    $checkstoset = "";
    $setcount = 0;
    // Goes through each of the checks from the posted form, separating them into their names and values
    // Then adds each of them to a string, with its value determining whether it uses 0 or 1
    foreach ($checkarray as $check => $checked) {
        $setcount = $setcount + 1; 
        if ($checked == 1) {
            if ($setcount != count($checkarray))
                $checkstoset = $checkstoset.$checknames[$check]." = 1, ";
            else {
                $checkstoset = $checkstoset.$checknames[$check]." = 1";
            }
        }
        else {
            if ($setcount != count($checkarray))
                $checkstoset = $checkstoset.$checknames[$check]." = 0, ";
            else {
                $checkstoset = $checkstoset.$checknames[$check]." = 0";
            }
        }
    }
    $conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
    // First updates the information about the computer using the text variables, then the checklists which uses the sentence from before in the query
    $infoquery = "UPDATE checklistsinfo SET PCname = '$name', Plant = '$plant', Sublocation = '$sub', Updatedby = '$username' WHERE ComputerID = '$updateID'";
    $checkquery = "UPDATE checklists SET ".$checkstoset." WHERE ChecklistID = $updateID";
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