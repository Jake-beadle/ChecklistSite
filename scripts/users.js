$(document).ready(function(){
    $("#checklistuser").submit(function(event){
        event.preventDefault();
            var data = $(this).serializeArray();
            $.post("../methods/accountcreator.php", data, function(response) {
                // Shows the user whether the creation of the account worked or not
                $("#result").html(response);
                // Resets the values in the textbox (so that multiple of the same accounts aren't created before the page refreshes)
                document.getElementById("user").value = ""
                document.getElementById("pass").value = ""
                // Refreshes the page after two seconds have passed
                setTimeout(function() { location.reload(true); }, 2000);
        })
    })
})
// All the below functions use the rowToEdit variable, which lets them select elements from the table row that they're on
// This is mainly used for hiding and unhiding elements, like below where it allows the user to edit a user
$(document).on("click", "#editUser", function(){
    let rowToEdit = $(this).closest("tr"); 
    $(rowToEdit).find("#Username").attr("hidden", true)
    $(rowToEdit).find("#Permissions").attr("hidden", true)
    $(rowToEdit).find("#editUser").attr("hidden", true)
    $(rowToEdit).find("#deleteUser").attr("hidden", true)
    $(rowToEdit).find("#UsernameEditP").attr("hidden", false)
    $(rowToEdit).find("#PermissionsEditP").attr("hidden", false)
    $(rowToEdit).find("#finishEditUser").attr("hidden", false)
    $(rowToEdit).find("#cancelEditUser").attr("hidden", false)
})
// This simply does the opposite of editUser, reverting the page to how it was before
$(document).on("click", "#cancelEditUser", function(){
    let rowToEdit = $(this).closest("tr");
    $(rowToEdit).find("#Username").attr("hidden", false)
    $(rowToEdit).find("#Permissions").attr("hidden", false)
    $(rowToEdit).find("#editUser").attr("hidden", false)
    $(rowToEdit).find("#deleteUser").attr("hidden", false)
    $(rowToEdit).find("#UsernameEditP").attr("hidden", true)
    $(rowToEdit).find("#PermissionsEditP").attr("hidden", true)
    $(rowToEdit).find("#finishEditUser").attr("hidden", true)
    $(rowToEdit).find("#cancelEditUser").attr("hidden", true)
})
// Once a user's details has been edited, it gets the needed values from the page 
// then posts them to accountupdate.php to update the database with the new values
$(document).on("click", "#finishEditUser", function(){
    let rowToEdit = $(this).closest("tr");
    let idOfRow = $(rowToEdit).find('#UserID').html()
    let newUser = $(rowToEdit).find('#UsernameEdit').val()
    let newPerms = $(rowToEdit).find('#PermsEdit option:selected').text()
    if (confirm("Are you sure you want to edit this user's details? (page will refresh afterwards)") == true) {
        $.post("../methods/accountupdate.php", {"idToUpdate": idOfRow, "newUser": newUser, "newPerms": newPerms}, function(response) {
            $("#result").html(response);
            setTimeout(function() { location.reload(true); }, 2000);
        })
    }
})

// Similar to the above function however it only needs one variable (the ID of the row)
// It then 'deletes' the user, hiding it from other users on the site
$(document).on("click", "#deleteUser", function(){
    let rowToEdit = $(this).closest("tr");
    let idOfRow = $(rowToEdit).find('#UserID').html()
    if (confirm("Are you sure you want to delete this user? (page will refresh afterwards)") == true) {
        $.post("../methods/accountdelete.php", {"idToDelete": idOfRow}, function(response) {
            $("#result").html(response);
            setTimeout(function() { location.reload(true); }, 2000);
        })
    }
})