$(document).ready(function(){
        // Gets the parameters/settings for the table from the URL and sets them as the chosen value for the select elements
        let string = window.location.search
        let parameter = new URLSearchParams(string)
        let sortby = parameter.get("sort")
        let currentpage = parameter.get("page")
        let currentsize = parameter.get("pagesize")
        let selectsort = $(document).find(`#datesort option[value=${sortby}]`)
        let selectpage = $(document).find(`#pagechange option[value=${currentpage}]`)
        let selectsize = $(document).find(`#pagesize option[value=${currentsize}]`)
        // If the parameters can't be set for any reason (e.g. if the URL is changed manually), it gets set back to the default settings
        if (!($(selectsort).length)||!($(selectpage).length)||!($(selectsize).length)) {
            window.location.replace("http://localhost:81/main.php?sort=disabled&page=1&pagesize=5")
        }
        $(selectsort).prop('selected',true)
        $(selectpage).prop('selected',true)
        $(selectsize).prop('selected',true)
        // After the 'add PC' form gets selected, it gets added to the database here
        $("#checklist").submit(function(event){
            event.preventDefault();
            var data = $(this).serializeArray();
                $.post("../methods/checklistadd.php", data, function(response) {
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
            $.post("../methods/checklistupdate.php", {"idToUpdate": idOfRow, "newName": newName, "newPlant": newPlant, "newSub": newSub, "checklistArray": checkArray}, function(response) {
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
            $.post("../methods/checklistdelete.php", {"idToDelete": idOfRow}, function(response) {
                $("#result").html(response);
                setTimeout(function() { location.reload(true); }, 2000);
            })
        }
    })

    // When an element that would edit the URL gets changed, it gets the values of all elements and updates the page
    $(document).on("change", ".urlchange", function(){
        sort = $(document).find('#datesort').val()
        page = $(document).find('#pagechange').val()
        size = $(document).find('#pagesize').val()
        let url = new URL('http://localhost:81/main.php')
        url.searchParams.set('sort',sort)
        url.searchParams.set('page',page)
        url.searchParams.set('pagesize',size)
        window.location.replace(url)
    })

    $("#deviceselect").on("input",function(){
        // Sets the input to lowercase to make it ignore capitals, making iit easier to search (name of pc is also set to lowercase later for this reason)
        let search = $(document).find('#deviceselect').val().toLowerCase()
        if (search.length >= 3) {
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
        }
    })