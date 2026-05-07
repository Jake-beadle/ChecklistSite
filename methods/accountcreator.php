<?php
        // Creates a connection to the database to be used later
        $conn = mysqli_connect("", "", "", "");
        // Gets the variables from the posted form
        if(isset($_POST["user"]))
        {
            $user = $_POST["user"];
        }  
        if(isset($_POST["email"]))
        {
            $email = $_POST["email"];
        }   
        if(isset($_POST["pass"]))
        {
            $pass = $_POST["pass"];            
        }
        $perms = $_POST["perms"];
        // Then creates test variables to be used on the form variables 
        // to make sure that they're valid (nothing that shouldn't be there)
        // Each of these checks are explained by the names of the variables below           
        $userHas7characters = (mb_strlen($user) >= 7);
        $userNotAbove20characters = (mb_strlen($user) <= 20);
        $userIsAlphanumeric = ctype_alnum($user);
        $passHas7characters = (mb_strlen($pass) >= 7);
        $passNotAbove20characters = (mb_strlen($pass) <= 20);
        $passIsAlphanumeric = ctype_alnum($pass);
        $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        // Checks are run here, returning an error message if they fail
        if(!($userHas7characters && $userNotAbove20characters && $userIsAlphanumeric)) {
            echo "Invalid username entered (must be 7-20 characters in length, and can only use alphanumeric characters)";
        } else if(!($passHas7characters && $passNotAbove20characters && $passIsAlphanumeric)) {
            echo "Invalid password entered (must be 7-20 characters long, and can only use alphanumeric characters)";
        } else if (!($emailIsValid)) {
            echo "Invalid email entered (incorrect format)";
        } else {
            // Makes sure that the entered username hasn't already been added to the database (includes deleted users)
            $dupeQueryToUse = "SELECT Username FROM logininfo WHERE Username = '$user'";
            $dupeQuery = mysqli_query($conn,$dupeQueryToUse); 
            $dupeTest = mysqli_num_rows($dupeQuery);
            if ($dupeTest > 0) { 
            echo "Error: username entered has already been used, try another";
            }   
            else {
            // Hashes the entered password (to make it more secure)
            $hashPass = password_hash($pass, PASSWORD_DEFAULT); 
            // Adds the account info into the database, returning an error if something doesn't work as expected
            $insertQuery = "INSERT INTO logininfo (Username, Email, Password, HashedPassword, Permissions) VALUES('$user', '$email', '$pass', '$hashPass', '$perms')";
            if(!$result = mysqli_query($conn, $insertQuery)){
                echo "Error: ".mysqli_error($conn);
            }
            else{
                echo "Successfully created account"; 
                }
            } 
        }
?>