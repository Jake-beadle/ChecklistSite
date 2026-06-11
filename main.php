<?php
session_start();
// If a user attempts to access the page without session data being set (which is 
// needed for the page to work properly), redirects the user back to the login page
if (!isset($_SESSION['user']) && !isset($_SESSION['perms'])){ 
    header("Location: /login.php");
}
$conn = mysqli_connect("", "", "", "");
// Only shows a small amount of entries in the table at once to prevent the site running slowly (can be changed by the user if needed)
if(isset($_GET['pagesize'])) {
    $pagesize = $_GET['pagesize'];
}
else {
    $pagesize = 5;
}
// Queries that are used for filling out the table (as well as the drop-down list using the first query)
// Three of the queries below are the same, but different variables are needed so that they can be used more than once (usages explained later)
$devicequery = "SELECT * FROM checklistsinfo WHERE Deleted != 1";
$infoquery = $devicequery;
$checkquery = "SELECT * FROM checklists WHERE Deleted != 1";
$amountquery = $devicequery;
// If a sort has been specified by the user, it returns a different query to normal, sorting by the date that an entry was last edited
if(isset($_GET['sort'])) {
    if ($_GET['sort'] == 'newest')
    {
        $infoquery .= " ORDER BY Dateofcheck DESC";
        $checkquery .= " ORDER BY Datechecked DESC";
    }
    elseif ($_GET['sort'] == 'oldest')
    {
        $infoquery .= " ORDER BY Dateofcheck ASC";
        $checkquery .= " ORDER BY Datechecked ASC";
    }
}
// Then limits the amount of devices shown on one page as specified by the user (uses 5 entries if not)
$devicequery .= " LIMIT ".$pagesize;
$infoquery .= " LIMIT ".$pagesize;
$checkquery .= " LIMIT ".$pagesize;
// Finds out how many entries there are that haven't been deleted
$amountresult = mysqli_query($conn, $amountquery);
$amount = mysqli_num_rows($amountresult);
// If a page has been selected, it gets the current page and offsets the query by the amount needed
if(isset($_GET['page'])) {
    $currpage = $_GET['page'] - 1;
    $devicequery .= " OFFSET ".($currpage*$pagesize);
    $infoquery .= " OFFSET ".($currpage*$pagesize);
    $checkquery .= " OFFSET ".($currpage*$pagesize); 
};
$deviceresult = mysqli_query($conn, $devicequery);
$inforesult = mysqli_query($conn, $infoquery);
$checkresult = mysqli_query($conn, $checkquery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./scripts/jquery-3.7.1.min.js"></script>
    <script src="./scripts/main.js"></script>
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
            <option value="immingham">Immingham</option>
        </select><br><br>
        <label for="sub">Sub-location:</label>
        <input list="sublocations" id="sub" name="sub">
        <datalist id='sublocations' name='sublocations'>
            <option>Blast Furnaces</option>
            <option>BOS Plant</option>
            <option>Concast (Caster 4)</option>
            <option>Concast (Caster 5)</option>
            <option>SRSM 1</option>
            <option>SRSM 2</option>
            <option>CPS</option>
            <option>Turbos</option>
            <option>Outstations</option>
            <option>Rod Mill</option>
            <option>Central Labs</option>
            <option>RSC/Area 3</option>
            <option>OPP</option>
            <option>OBP</option>
            <option>Sinter Plant</option>
            <option>Coal Handling Plant</option>
            <option>Billet Caster</option>
            <option>Admin</option>
        </datalist><br><br>
        <input type="submit">
    </form>
    <p id="result"></p>
    <?php
    if ($_SESSION['perms'] == 'Admin') { // If someone is an admin, they are given access to the user table
        echo "<p><em>This account is an admin - you can access user details (and edit them) <a href='/users.php'>here</a></em></p>";
    }
    // Shows the user's name and permissions while using the site
    echo "Username: <span id='username'>".$_SESSION['user']."</span>, permission level: ".$_SESSION['perms']."<br><br>";
    ?>
    <!-- Logs the user out, sending them back to the login page -->
    <a href="./methods/accountlogout.php"><button>Log out of your account</button></a>
    <h2>Select a device from the drop-down list below (or search its name) to get its details:</h2>
    <!-- Lets the user sort the returned rows by date based on their selection -->
    <label for="datesort">Sort by date created/edited:</label>
    <select class="urlchange" id="datesort" name="datesort">
        <option value="disabled">Disabled</option>
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
    </select>
    <!-- Can be used to limit the amount of entries that can be shown at once to prevent lag for the user (which could happen if there were large amounts of entries shown at once) -->
    <label for="pagesize">, entries per page: </label>
    <select class="urlchange" id="pagesize" name="pagesize">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
    </select><br><br>
    <!-- Changes the page that the user is on to show different entries from the table -->
    <button id="prevpage">Previous page</button>
    <label for="pagechange">Currently on page</label>
    <select class="urlchange" id="pagechange" name="pagechange">
        <?php 
            $pagecount = ceil($amount/$pagesize);
            for ($page = 1; $page <= $pagecount; $page++) {
                if ($page == $pagecount) {
                    echo '<option class="lastpage" value='.$page.'>'.$page.'</option>'; 
                } else {
                    echo '<option value='.$page.'>'.$page.'</option>';
                }
            }
        ?>   
    </select>
    <button id="nextpage">Next page</button><br><br>
    <!-- Gives the user two methods of selecting devices: a drop-down list or a search bar
    Choosing a PC from the list makes it appear in the table (allows multiple to be shown at once)
    Searching a PC's name (or part of it) will make it appear in the table -->
    <label for="deviceselect">Select/search for device:</label>
    <input list="devices" id="deviceselect" name="deviceselect">
    <datalist id='devices' name='devices'>  
        <?php 
        while ($deviceoption = mysqli_fetch_assoc($deviceresult)) {
            echo "<option value=".$deviceoption["PCname"].">".$deviceoption["PCname"]."</option>";
        }
        ?>
    </datalist><br>
    <!-- Lets the user filter by the plant that the device is located at -->
    <label for="plantfilter">Filter by plant:</label>
    <select id="plantfilter" name="plantfilter">
        <option value="disabled">Disabled (all plants)</option>
        <option value="scunthorpe">Scunthorpe</option>
        <option value="teesside">Teesside</option>
        <option value="skinningrove">Skinningrove</option>
        <option value="immingham">Immingham</option>
    </select><br><br>
    <!-- Lets the user filter by the sublocation that the device is located at -->
    <label for="subfilter">Filter by sub-location:</label>
    <input list="sublocations" id="subfilter" name="subfilter"><br><br>
    <!-- Table that contains checklists and information for each PC.
    Initially shows all information for that page, but can change what
    entries are shown by using the search/dropdown above -->
    <table id="checklisttable">
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
                        $checkdescriptions = ["Sentinel One installed and running","Windows Updates installed",
                            "Device enrolled in Intune / MDM","Asset Tag checked",
                            "Local admin password reset","Antivirus definitions updated",
                            "Disk health checked","User data removed or backed up",
                            "Network test completed","Standard software pack installed"    
                        ];
                        $checkrow = mysqli_fetch_assoc($checkresult);
                        echo "<tr id='Entry".$inforow['ComputerID']."'>
                        <td id='ComputerID' hidden>".$inforow['ComputerID']."</td>
                        <td id='PCUserInfo'>
                            <p id='PCname'>Name of PC: ".$inforow['PCname']."</p>
                            <p id='PCnameEditP' hidden>Name of PC: <input type=text id='PCnameEdit' value=".$inforow['PCname']."></p>
                            <p id='Status'>Status: ".$inforow['Status']."</p>
                            <p id='StatusEditP' hidden>Status: 
                            <select id='StatusEdit' name='StatusEdit'>
                                <option value='received'>Received</option>
                                <option value='inprogress'>In progress</option>
                                <option value='passed'>Passed</option>
                                <option value='failed'>Failed</option>
                            </select></p>
                            <p id='PlantSub'>Plant: <span id='Plant'>".$inforow['Plant']."</span>, sub-location: <span id='Sub'>".$inforow['Sublocation']."</span></p>
                            <p id='PlantSubEditP' hidden>Plant: 
                            <select id='PlantEdit' name='PlantEdit'>
                                <option value='scunthorpe'>Scunthorpe</option>
                                <option value='teesside'>Teesside</option>
                                <option value='skinningrove'>Skinningrove</option>
                                <option value='immingham'>Immingham</option>
                            </select>
                            , sub-location: <input list='sublocations' id='SublocationEdit' name='SublocationEdit'></input></p>
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
                            // $checkrow would return all columns, but some (ChecklistID, Deleted, Datechecked) are unneeded for the checklist
                            if ($check != "ChecklistID" && $check != "Deleted" && $check != "Datechecked") {
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
