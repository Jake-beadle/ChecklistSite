<?php 
session_start();
if (!empty($_POST)) {
    if (isset($_POST['user']) && isset($_POST['pass'])) {
        $conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
        // Sets up a query and returns an object from the database which will have the desired user's details
        $stmt = $conn->prepare("SELECT * FROM logininfo WHERE Username = ?");
        $stmt->bind_param('s',$_POST['user']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();
        // Checks if the user's account has been deleted before continuing
        if ($user->Deleted != 1) {
            // Then checks if the password entered for the user is the correct one (checked against the hashed password in the database)
            if (password_verify($_POST['pass'], $user->HashedPassword)) {
                // If the login was successful, it adds the user's name and permissions to the session 
                // and returns the 'ok' status (allowing the user to be redirected to the main page)
                $_SESSION['user'] = $user->Username;
                $permissionsRequest = mysqli_query($conn,"SELECT Permissions FROM logininfo WHERE Username = '$user->Username'");
                $permissionsFetch =  mysqli_fetch_assoc($permissionsRequest);
                $permissions = $permissionsFetch['Permissions'];
                $_SESSION['perms'] = $permissions;
                echo json_encode(["status" => "ok"]);
            } else {
                echo json_encode(["status" => "error", "msg" => "Invalid username or password, please try again"]);
        }} else {
                echo json_encode(["status" => "error", "msg" => "This account has been deleted, please use a different login"]);
        }
    }
} 
?>
