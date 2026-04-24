<?php
    session_start();
    if (!isset($_SESSION['user']) && !isset($_SESSION['perms'])){ 
        header("Location: /login.php");
    }
    if ($_SESSION['perms'] != 'Admin'){ 
        header("Location: /main.php");
    }
    $conn = mysqli_connect("", "", "", "");
    $query = "SELECT * FROM logininfo WHERE Deleted != 1"; // Returns all users that haven't been deleted (shown in the table)
    $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./scripts/jquery-3.7.1.min.js"></script>
    <script src="./scripts/users.js"></script>
    <title>PCRC Users</title>
</head>
<body>
    <img src="https://internal.britishsteel.uk.com/img/logo.svg">
    <h1>PC Control Checklist Users Table</h1>
    <legend>Add a user by entering details below:</legend><br>  <!-- Form that lets an admin add a user -->
    <form id="checklistuser" action="" method="post">
        <label for="user">Username:</label>
        <input type="text" id="user" name="user" placeholder="Enter username"><br><br>
        <label for="pass">Password:</label>
        <input type="password" id="pass" name="pass" placeholder="Enter password"><br><br>
        <label for="perms">Permissions type:</label>
        <select id="perms" name="perms">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <input type="submit">
    </form>
    <p id="result"></p>
    <?php
    echo "<p><em>Return to the checklists table <a href='/main.php'>here</a></em></p>";
    echo "Username: ".$_SESSION['user'].", permission level: ".$_SESSION['perms']."<br>";
    ?>
    <h2>List of users:</h2>
    <table id="usertable">
        <thead>
            <th hidden>ID</th>
            <th>User details</th>
            <th>Edit user</th>
        </thead>
        <tbody>
            <?php
                while($row = mysqli_fetch_assoc($result))
                    {
                        echo "<tr>
                        <td id='UserID' hidden>".$row['UserID']."</td>
                        <td id='UserInfo'>
                            <p id='Username'>Username: ".$row['Username']."</p>
                            <p id='UsernameEditP' hidden>Username: <input type=text id='UsernameEdit' value=".$row['Username']."></p>
                            <p id='Permissions'>Permissions level: ".$row['Permissions']."</p>
                            <p id='PermissionsEditP' hidden>Permissions level: <select id='PermsEdit'>
                                <option value='user'>User</option>
                                <option value='admin'>Admin</option>
                            </select></p>
                        </td>
                        <td id='EditButtons'>";
                        // The 'testadmin' account is intentionally not changeable as if if it was deleted, it could lead to no accounts existing in the database
                        // This would make the main parts of the site inaccessible without manually adding an account to the database, which is undesirable
                        // Therefore the easiest solution is to add an admin account which cannot be deleted (so that it can create more accounts if needed)
                        if($row["Username"] != "Testadmin") {
                                echo "<button id='editUser'>Edit user details</button>
                                <button id='finishEditUser' hidden>Finish changing details</button><br><br>
                                <button id='deleteUser'>Delete user</button>
                                <button id='cancelEditUser' hidden>Cancel editing</button>";
                        }
                        else {
                            echo "<p style='color:orange'>To avoid issues, the default admin account cannot be altered.</p>";
                        }
                        echo "</td>
                        </tr>";
                    }
            ?> 
        </tbody>
    </table>
</body>
</html>