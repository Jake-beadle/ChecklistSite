<?php
    session_start();
    if(isset($_POST["idToDelete"]))
        {
            $deleteID = $_POST["idToDelete"];
        }
    $username = $_SESSION['user'];
    $conn = mysqli_connect("", "", "", "");
    // Using inspect element would let you add edit buttons to the admin row and delete the account, which shouldn't be possible. This prevents that from happening
    if($updateID == 1) {
        echo "The admin account cannot be deleted from the database.";
        exit;
    }
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