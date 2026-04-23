<?php
    session_start();
    // If a user attempts to access the page without session data being set (which is 
    // needed for the page to work properly), redirects the user back to the login page
    if (!isset($_SESSION['user']) && !isset($_SESSION['perms'])){ 
        header("Location: /login.php");
    }
    $conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
    // Queries that are used for filling out the table (as well as the drop-down list using the first query)
    // The two queries below are the same, but two variables are needed so that it can be used more than once (usages explained later)
    $devicequery = "SELECT * FROM checklistsinfo WHERE Deleted != 1";
    $deviceresult = mysqli_query($conn, $devicequery);
    $infoquery = "SELECT * FROM checklistsinfo WHERE Deleted != 1";
    $inforesult = mysqli_query($conn, $infoquery);
    $checkquery = "SELECT * FROM checklists WHERE Deleted != 1";
    $checkresult = mysqli_query($conn, $checkquery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./jquery-3.7.1.min.js"></script>
    <title>PC Return Checklist</title>
</head>
<body>
    <img src="https://internal.britishsteel.uk.com/img/logo.svg">
    <h1>PC Return Checklist</h1>
    <legend>Add a PC to the checklist by entering the PC's details below and submitting the form.</legend><br>
    <form id="checklist"> <!-- Form that lets a user add a PC to the table -->
        <label for="pcname">Name of the PC:</label>
        <input type="text" id="pcname" name="pcname" placeholder="Enter PC Name" required><br><br>
        <label for="plant">Location of Plant:</label>
        <select id="plant" name="plant">
            <option value="scunthorpe">Scunthorpe</option>
            <option value="teesside">Teesside</option>
            <option value="skinningrove">Skinningrove</option>
        </select><br><br>
        <label for="sub">Sub-location:</label>
        <textarea type="textbox" id="sub" name="sub" placeholder="Enter sub-location" required></textarea><br><br>
        <input type="submit">
    </form>
    <p id="result"></p>
    <?php
    if ($_SESSION['perms'] == 'Admin') {    // If someone is an admin, they are given access to the user table
        echo "<p><em>This account is an admin - you can access user details (and edit them) <a href='/users.php'>here</a></em></p>";
    }
    // Shows the user's name and permissions while using the site
    echo "Username: <span id='username'>".$_SESSION['user']."</span>, permission level: ".$_SESSION['perms']."<br><br>";
    ?>
    <!-- Logs the user out, sending them back to the login page -->
    <a href="./methods/accountlogout.php"><button>Log out of your account</button></a>
    <h2>Select a device from the drop-down list below to get its details:</h2>
    <h4>(alternatively, you can use it to search if you know the device's name)</h4>
    <!-- Gives the user two methods of selecting devices: a drop-down list or a search bar -->
    <!-- Choosing a PC from the list makes it appear in the table (allows multiple to be shown at once) --><!-- Searching a PC's name (or part of it) will make it appear in the table -->
    <input list="devices" id="deviceselect" name="deviceselect"><br><br> 
    <datalist id='devices' name='devices'>  
        <?php 
        while ($deviceoption = mysqli_fetch_assoc($deviceresult)) {
            echo "<option value=".$deviceoption["PCname"].">".$deviceoption["PCname"]."</option>";
        }
        ?>
    </datalist>
    <!-- Table that contains checklists and information for each PC.
    Contains all of the data from the database, but only shows the 
    entries that the user has selected using the above methods -->
    <table id="checklisttable" hidden>
        <thead>
            <th hidden>ID</th>
            <th>PC/user information</th>
            <th>Checklist</th>
            <th>Edit entry</th>
        </thead>
        <tbody>
            <?php
                while($inforow = mysqli_fetch_assoc($inforesult))
                    { 
                        // Names of all the checks in the checklist (as well as the extended description for each)
                        // Used to fill out the checklist later on (as the descriptions aren't saved to the database)
                        $checknames = ['Sentinelinstall','Windowsupdate','Deviceenrolled','Assettag',
                        'Localreset','Diskcheck','Antivirusupdated','Userdata','Networktest','Softwareinstall'];
                        $checkdescriptions = ["Sentinel One installed and running",
                            "Windows Updates installed",
                            "Device enrolled in Intune / MDM",
                            "Asset Tag checked",
                            "Local admin password reset",
                            "Antivirus definitions updated",
                            "Disk health checked",
                            "User data removed or backed up",
                            "Network test completed",
                            "Standard software pack installed"    
                        ];
                        $checkrow = mysqli_fetch_assoc($checkresult);
                        echo "<tr id='Entry".$inforow['ComputerID']."' hidden>
                        <td id='ComputerID' hidden>".$inforow['ComputerID']."</td>
                        <td id='PCUserInfo'>
                            <p id='PCname'>Name of PC: ".$inforow['PCname']."</p>
                            <p id='PCnameEditP' hidden>Name of PC: <input type=text id='PCnameEdit' value=".$inforow['PCname']."></p>
                            <p id='PlantSub'>Plant: ".$inforow['Plant'].", sub-location: ".$inforow['Sublocation']."</p>
                            <p id='PlantSubEditP' hidden>Plant: 
                            <select id='PlantEdit' name='PlantEdit'>
                                <option value='scunthorpe'>Scunthorpe</option>
                                <option value='teesside'>Teesside</option>
                                <option value='skinningrove'>Skinningrove</option>
                            </select>
                            , sub-location: <textarea id='SublocationEdit'>".$inforow['Sublocation']."</textarea></p>
                            <p id='Dateofcheck'>Date of last check/change: ".$inforow['Dateofcheck']."</p>
                            <p id='Createdby'>Created by: ".$inforow['Createdby']."</p>";
                            // If an entry hasn't been changed yet, this element will not appear
                            if ($inforow["Updatedby"] != NULL) {
                                echo "<p id='Updatedby'>Last updated by: ".$inforow['Updatedby']."</p>";
                            }
                            // Deletedby is saved to the database but it'd never be shown to the user so there isn't a check for it
                        echo "</td>
                        <td id='Checklist'>";
                        // Variable used for the reminder (see the p element below)
                        $allchecks = 0;
                        foreach($checkrow as $check => $value) {
                            // $checkrow returns all columns, but the first (ChecklistID) and last (Deleted) are unneeded for the checklist
                            if ($check != "ChecklistID" && $check != "Deleted") {
                                // Variable used to get the description for the check (rather than its name in the database)
                                $index = array_search($check,$checknames);
                                // Adds a checkbox for each of the remaining columns (which is checked or unchecked depending on its value, being 0 or 1)
                                if ($value == 0) {
                                    echo "<span class='Default'><input type='checkbox' id='".$check."' onclick='return false'><label for='".$check."'>".$checkdescriptions[$index]."</label></span><br>
                                    <span class='Edit' hidden><input type='checkbox' id='".$check."' ><label for='".$check."'>".$checkdescriptions[$index]."</label></span>";
                                }
                                else if ($value == 1) {
                                    echo "<span class='Default'><input type='checkbox' id='".$check."' onclick='return false' checked><label for='".$check."'>".$checkdescriptions[$index]."</label></span><br>
                                    <span class='Edit' hidden><input type='checkbox' id='".$check."' checked><label for='".$check."'>".$checkdescriptions[$index]."</label></span>";
                                    $allchecks = $allchecks + 1;
                                }
                                // If the value is somehow not 0 or 1, outputs an error instead 
                                else {
                                    echo "Error (check database)";
                                }
                            }
                        }
                        // Adds a reminder below the checklist if all checks have been completed
                        if ($allchecks == count($checknames)) {
                            echo "<p class='completed'>All checks completed (ready to delete)</p>";
                        }
                        echo "</td>
                        <td id='EditButtons'>                       
                            <button id='editEntry'>Edit entry</button>
                            <button id='finishEditEntry' hidden>Finish editing entry</button>
                            <br><br><button id='deleteEntry'>Delete entry</button>
                            <button id='cancelEditEntry' hidden>Cancel editing entry</button>
                        </td>
                        </tr>";
                    }
            ?>
        </tbody>
    </table>
</body>
</html>
<script>
    $(document).ready(function(){
        $("#checklist").submit(function(event){
            event.preventDefault();
            var data = $(this).serializeArray();
                $.post("./methods/checklistadd.php", data, function(response) {
                    // Shows the user if the new data has been added (and if not, what went wrong)
                    $("#result").html(response); 
                    // Waits 2 seconds before reloading the page (enough time for the user to read the response)
                    setTimeout(function() { location.reload(true); }, 2000); 
            })
        })
    })
    
    // rowToEdit is used in multiple of these functions, but cannot be declared globablly as $(this) wouldn't work
    // (which is necessary so that it can select the row that is being selected to edit)
    // This function unhides the necessary areas that are used to edit an entry
    $(document).on("click", "#editEntry", function(){
        let rowToEdit = $(this).closest("tr");
        $(rowToEdit).find("#PCname").attr("hidden", true)
        $(rowToEdit).find("#PlantSub").attr("hidden", true)
        $(rowToEdit).find("#editEntry").attr("hidden", true)
        $(rowToEdit).find("#deleteEntry").attr("hidden", true)
        $(rowToEdit).find(".Default").attr("hidden", true)
        $(rowToEdit).find("#PCnameEditP").attr("hidden", false)
        $(rowToEdit).find("#PlantSubEditP").attr("hidden", false)
        $(rowToEdit).find("#finishEditEntry").attr("hidden", false)
        $(rowToEdit).find("#cancelEditEntry").attr("hidden", false)
        $(rowToEdit).find(".Edit").attr("hidden", false)
    })
 
    // This does the opposite of the above function, hiding the parts that would let the user edit the entry
    $(document).on("click", "#cancelEditEntry", function(){
        let rowToEdit = $(this).closest("tr");
        $(rowToEdit).find("#PCname").attr("hidden", false)
        $(rowToEdit).find("#PlantSub").attr("hidden", false)
        $(rowToEdit).find("#editEntry").attr("hidden", false)
        $(rowToEdit).find("#deleteEntry").attr("hidden", false)
        $(rowToEdit).find(".Default").attr("hidden", false)
        $(rowToEdit).find("#PCnameEditP").attr("hidden", true)
        $(rowToEdit).find("#PlantSubEditP").attr("hidden", true)
        $(rowToEdit).find("#finishEditEntry").attr("hidden", true)
        $(rowToEdit).find("#cancelEditEntry").attr("hidden", true)
        $(rowToEdit).find(".Edit").attr("hidden", true)
    })

    // After a user has finished editing an entry, it gets the values that have been inputted by the user
    // (including their changes to the checklist, as they can be checked/unchecked while editing the entry)
    // These are then posted over to checklistupdate.php, where the database gets updated using these values
    $(document).on("click", "#finishEditEntry", function(){
        let rowToEdit = $(this).closest("tr");
        let idOfRow = $(rowToEdit).find('#ComputerID').html()
        let newName = $(rowToEdit).find('#PCnameEdit').val()
        let newPlant = $(rowToEdit).find('#PlantEdit option:selected').text()
        let newSub = $(rowToEdit).find('#SublocationEdit').val()
        let checks = $(rowToEdit).find(".Edit input")
        let checkArray = []
        for (let i = 0; i < checks['length']; i++) {
            // For each of the checks, it finds whether it has been checked or not, adding a 1 if it has and a 0 if it hasn't
            // This lets it get iterated through when updating the database, making it quicker than hard-coding each variable
            var checkedtest = $(checks[i]).is(":checked")
            if (checkedtest) {
                checkArray.push(1)
            } else {
                checkArray.push(0)
            }
        }
        if (confirm("Are you sure you want to edit this entry? (page will refresh afterwards)") == true) {
            $.post("./methods/checklistupdate.php", {"idToUpdate": idOfRow, "newName": newName, "newPlant": newPlant, "newSub": newSub, "checklistArray": checkArray}, function(response) {
                $("#result").html(response)
                setTimeout(function() { location.reload(true); }, 2000);
            })
        }
    })

    // This is similar to the above function, changing the row so that it isn't visible on the site anymore (effectively deleting the entry)
    $(document).on("click", "#deleteEntry", function(){
        let rowToEdit = $(this).closest("tr");
        let idOfRow = $(rowToEdit).find('#ComputerID').html()
        if (confirm("Are you sure you want to delete this entry? (page will refresh afterwards)") == true) {
            $.post("./methods/checklistdelete.php", {"idToDelete": idOfRow}, function(response) {
                $("#result").html(response);
                setTimeout(function() { location.reload(true); }, 2000);
            })
        }
    })

    $("#deviceselect").on("input",function(){
        // Unhides the table after it has been used for the first time, which improves appearance 
        // (otherwise it would only have had the titles of each column with no values under them)
        if ($(document).find("#checklisttable").attr("hidden")) {
            $(document).find("#checklisttable").attr("hidden", false)
        }
        // Sets the input to lowercase to make it ignore capitals, making iit easier to search (name of pc is also set to lowercase later for this reason)
        let search = $(document).find('#deviceselect').val().toLowerCase()
        // Gets the rows inside the table body and saves it as an array (so they can be iterated through)
        let rows = $(document).find('tbody tr')
        for (let i = 0; i < rows.length; i++) {
            // Finds the current row and gets the name of the PC on that row
            var row = rows[i]
            name = $(row).find('#PCname').html()
            name = name.replace('Name of PC: ','').toLowerCase()
            // If the searched text is part of the name, the row for that PC will be shown to the user
            if (name.includes(search)) {
                $(row).attr("hidden",false)
            // If not, it gets hidden (so that it only shows the PCs that the user wants to see)
            } else {
                $(row).attr("hidden",true)
            }
        }
        })
</script>
