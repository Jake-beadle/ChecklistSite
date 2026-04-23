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
    $conn = mysqli_connect("", "", "", "");
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