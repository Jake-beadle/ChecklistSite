<?php
    session_start();
    if(isset($_POST["idToDelete"]))
        {
            $deleteID = $_POST["idToDelete"];
        }
    $username = $_SESSION['user'];
    $conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
    $query = "UPDATE logininfo SET Deleted = 1 WHERE UserID = $deleteID"; // Sets deleted to 1 which removes it from website (but keeps it in the database)
    if(!$result = mysqli_query($conn, $query))
    {
        echo "Error when attempting update: ".mysqli_error($conn);
    }
    else
    {
        echo "Successfully updated database";
    }
?>