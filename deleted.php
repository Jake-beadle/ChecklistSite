<!-- Most of the code used on this page can be found on other pages (main.php and users.php) so a lot of the comments have been removed -->
<?php
session_start();
if (!isset($_SESSION['user']) && !isset($_SESSION['perms'])){ 
    header("Location: /login.php");
}
if ($_SESSION['perms'] != 'Admin'){ 
    header("Location: /main.php");
}
$conn = mysqli_connect("scconco2.pc.scunthorpe.corusgroup.com", "fieldserviceseditor", "fieldservices", "field_services");
if(isset($_GET['pagesize'])) {
    $pagesize = $_GET['pagesize'];
}
else {
    $pagesize = 5;
}
$devicequery = "SELECT * FROM checklistsinfo WHERE Deleted = 1";
$infoquery = $devicequery;
$checkquery = "SELECT * FROM checklists WHERE Deleted = 1";
$amountquery = $devicequery;
$userquery = "SELECT * FROM logininfo WHERE Deleted = 1";
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
$devicequery .= " LIMIT ".$pagesize;
$infoquery .= " LIMIT ".$pagesize;
$checkquery .= " LIMIT ".$pagesize;
$amountresult = mysqli_query($conn, $amountquery);
$amount = mysqli_num_rows($amountresult);
if(isset($_GET['page'])) {
    $currpage = $_GET['page'] - 1;
    $devicequery .= " OFFSET ".($currpage*$pagesize);
    $infoquery .= " OFFSET ".($currpage*$pagesize);
    $checkquery .= " OFFSET ".($currpage*$pagesize); 
};
$deviceresult = mysqli_query($conn, $devicequery);
$inforesult = mysqli_query($conn, $infoquery);
$checkresult = mysqli_query($conn, $checkquery);
$userresult = mysqli_query($conn, $userquery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./scripts/jquery-3.7.1.min.js"></script>
    <script src="./scripts/deleted.js"></script>
    <title>PC Return Checklist</title>
</head>
<body>
    <img src="https://internal.britishsteel.uk.com/img/logo.svg">
    <h1>PC Return Checklist (Deleted Entries)</h1>
    <legend>This page lets you see deleted entries/users, as well as restore them if they were accidentally deleted.</legend>
    <p id="result"></p>
    <?php
    echo "<p><em>Return to the checklists table <a href='/main.php'>here</a></em></p>";
    echo "Username: ".$_SESSION['user'].", permission level: ".$_SESSION['perms']."<br><br>";
    ?>
    <span>Currently viewing:</span>
    <input type="radio" id="entrieschoice" name="changetable" checked>Checklist entries</input>
    <input type="radio" id="userschoice" name="changetable">Users</input>
    <!-- This div holds the entries table (same as main.php) -->
    <div id="entries">
    <h2>Select a device using the search/filters below to get its details:</h2>
    <div id="filters" name="filters">
    <label for="plantfilter">Filter by plant:</label>
    <select id="plantfilter" name="plantfilter">
        <option value="disabled">Disabled (all plants)</option>
        <option value="scunthorpe">Scunthorpe</option>
        <option value="teesside">Teesside</option>
        <option value="skinningrove">Skinningrove</option>
        <option value="immingham">Immingham</option>
    </select>
    <label for="statusfilter">Filter by status:</label>
    <select id="statusfilter" name="statusfilter">
        <option value="disabled">Disabled</option>
        <option value='received'>Received</option>
        <option value='inprogress'>In progress</option>
        <option value='passed'>Passed</option>
        <option value='failed'>Failed</option>
    </select><br><br>
    <label for="subfilter">Filter by sub-location:</label>
    <input list="sublocations" id="subfilter" name="subfilter"><br>
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
    <label for="datesort">Sort by date created/edited:</label>
    <select class="urlchange" id="datesort" name="datesort">
        <option value="disabled">Disabled</option>
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
    </select>
    <label for="pagesize">, entries per page: </label>
    <select class="urlchange" id="pagesize" name="pagesize">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
    </select><br><br>
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
    <button id="nextpage">Next page</button><br>
    </div><br><br>
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
                            <p id='Status'>Status: ".$inforow['Status']."</p>
                            <p id='PlantSub'>Plant: <span id='Plant'>".$inforow['Plant']."</span>, sub-location: <span id='Sub'>".$inforow['Sublocation']."</span></p>
                            <p id='Dateofcheck'>Date of last check/change: ".$inforow['Dateofcheck']."</p>
                            <p id='Createdby'>Created by: ".$inforow['Createdby'];
                            if ($inforow["Updatedby"] != NULL) {
                                echo "<span id='Updatedby'>, last updated by: ".$inforow['Updatedby']."</span>";
                            }
                            echo "</p><p id='Deletedby'>Deleted by: ".$inforow['Deletedby']."</p>";
                        echo "</td>
                        <td id='Checklist'>";
                        $allchecks = 0;
                        foreach($checkrow as $check => $value) {
                            if ($check != "ChecklistID" && $check != "Deleted" && $check != "Datechecked") {
                                $index = array_search($check,$checknames);
                                if ($value == 0) {
                                    echo "<span class='Default'><input type='checkbox' id='".$check."' onclick='return false'><label for='".$check."'>".$checkdescriptions[$index]."</label></span><br>";
                                }
                                else if ($value == 1) {
                                    echo "<span class='Default'><input type='checkbox' id='".$check."' onclick='return false' checked><label for='".$check."'>".$checkdescriptions[$index]."</label></span><br>";
                                    $allchecks = $allchecks + 1;
                                }
                                else {
                                    echo "Error (check database)";
                                }
                            }
                        }
                        if ($allchecks == count($checknames)) {
                            echo "<p class='completed'>All checks completed</p>";
                        }
                        echo "</td>
                        <td id='EditButtons'>                       
                            <button id='restoreEntry'>Restore entry</button>
                        </td>
                        </tr>";
                    }
            ?>
        </tbody>
    </table>
    </div>
    <!-- This div holds the users table (same as users.php) -->
    <div id="users" hidden>
        <h2>List of users:</h2>
    <table id="usertable">
        <thead>
            <th hidden>ID</th>
            <th>User details</th>
            <th>Edit user</th>
        </thead>
        <tbody>
            <?php
                while($row = mysqli_fetch_assoc($userresult))
                    {
                        echo "<tr>
                        <td id='UserID' hidden>".$row['UserID']."</td>
                        <td id='UserInfo'>
                            <p id='Username'>Username: ".$row['Username']."</p>
                            <p id='Email'>Email address: ".$row['Email']."</p>
                            <p id='Permissions'>Permissions level: ".$row['Permissions']."</p>
                        </td>
                        <td id='EditButtons'>
                            <button id='restoreUser'>Restore user</button>
                        </td>
                        </tr>";
                    }
            ?> 
        </tbody>
    </table>
    </div>
</body>
</html>

