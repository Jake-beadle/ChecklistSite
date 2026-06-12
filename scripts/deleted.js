// Most of the comments have been removed as they are the same as in the original files (main.js and users.js)
$(document).ready(function(){
    let string = window.location.search
    let parameter = new URLSearchParams(string)
    let sortby = parameter.get("sort")
    let currentpage = parameter.get("page")
    let currentsize = parameter.get("pagesize")
    let selectsort = $(document).find(`#datesort option[value=${sortby}]`)
    let selectpage = $(document).find(`#pagechange option[value=${currentpage}]`)
    let selectsize = $(document).find(`#pagesize option[value=${currentsize}]`)
    if (!($(selectsort).length)||!($(selectpage).length)||!($(selectsize).length)) {
    }
    $(selectsort).prop('selected',true)
    $(selectpage).prop('selected',true)
    $(selectsize).prop('selected',true)
    $("#checklist").submit(function(event){
        event.preventDefault();
        var data = $(this).serializeArray();
            $.post("../methods/checklistadd.php", data, function(response) {
                $("#result").html(response); 
                setTimeout(function() { location.reload(true); }, 2000);
        })
    })
    $("#deviceselect").on("input",function(){
        let devicesearch = $(document).find('#deviceselect').val().toLowerCase()
        let plantfilter = $(document).find('#plantfilter').val()
        let statusfilter = $(document).find('#statusfilter').val()
        let subsearch = $(document).find('#subfilter').val().toLowerCase()
        let rows = $(document).find('tbody tr')
        for (let i = 0; i < rows.length; i++) {
            var row = rows[i]
            name = $(row).find('#PCname').html()
            name = name.replace('Name of PC: ','').toLowerCase()
            plant = $(row).find('#Plant').html().toLowerCase()
            sub = $(row).find('#Sub').html().toLowerCase()
            status = $(row).find('#Status').html()
            status = status.replace('Status: ','').toLowerCase()
            if (name.includes(devicesearch) && sub.includes(subsearch) && ((plantfilter == "disabled") || (plantfilter == plant)) && ((statusfilter == "disabled") || (statusfilter == status))) {
                $(row).attr("hidden",false)
            } else {
                $(row).attr("hidden",true)
            }
        }
    })
    $("#plantfilter").on("change",function(){
        let devicesearch = $(document).find('#deviceselect').val().toLowerCase()
        let plantfilter = $(document).find('#plantfilter').val()
        let statusfilter = $(document).find('#statusfilter').val()
        let subsearch = $(document).find('#subfilter').val().toLowerCase()
        let rows = $(document).find('tbody tr')
        for (let i = 0; i < rows.length; i++) {
            var row = rows[i]
            name = $(row).find('#PCname').html()
            name = name.replace('Name of PC: ','').toLowerCase()
            plant = $(row).find('#Plant').html().toLowerCase()
            sub = $(row).find('#Sub').html().toLowerCase()
            status = $(row).find('#Status').html()
            status = status.replace('Status: ','').toLowerCase()
            if (name.includes(devicesearch) && sub.includes(subsearch) && ((plantfilter == "disabled") || (plantfilter == plant)) && ((statusfilter == "disabled") || (statusfilter == status))) {
                $(row).attr("hidden",false)
            } else {
                $(row).attr("hidden",true)
            }
        }
    })
    $("#statusfilter").on("input",function(){
        let devicesearch = $(document).find('#deviceselect').val().toLowerCase()
        let plantfilter = $(document).find('#plantfilter').val()
        let statusfilter = $(document).find('#statusfilter').val()
        let subsearch = $(document).find('#subfilter').val().toLowerCase()
        let rows = $(document).find('tbody tr')
        for (let i = 0; i < rows.length; i++) {
            var row = rows[i]
            name = $(row).find('#PCname').html()
            name = name.replace('Name of PC: ','').toLowerCase()
            plant = $(row).find('#Plant').html().toLowerCase()
            sub = $(row).find('#Sub').html().toLowerCase()
            status = $(row).find('#Status').html()
            status = status.replace('Status: ','').toLowerCase()
            if (name.includes(devicesearch) && sub.includes(subsearch) && ((plantfilter == "disabled") || (plantfilter == plant)) && ((statusfilter == "disabled") || (statusfilter == status))) {
                $(row).attr("hidden",false)
            } else {
                $(row).attr("hidden",true)
            }
        }
    })
    $("#subfilter").on("input",function(){
        let devicesearch = $(document).find('#deviceselect').val().toLowerCase()
        let plantfilter = $(document).find('#plantfilter').val()
        let statusfilter = $(document).find('#statusfilter').val()
        let subsearch = $(document).find('#subfilter').val().toLowerCase()
        let rows = $(document).find('tbody tr')
        for (let i = 0; i < rows.length; i++) {
            var row = rows[i]
            name = $(row).find('#PCname').html()
            name = name.replace('Name of PC: ','').toLowerCase()
            plant = $(row).find('#Plant').html().toLowerCase()
            sub = $(row).find('#Sub').html().toLowerCase()
            status = $(row).find('#Status').html()
            status = status.replace('Status: ','').toLowerCase()
            if (name.includes(devicesearch) && sub.includes(subsearch) && ((plantfilter == "disabled") || (plantfilter == plant)) && ((statusfilter == "disabled") || (statusfilter == status))) {
                $(row).attr("hidden",false)
            } else {
                $(row).attr("hidden",true)
            }
        }
    })
})

// Allows the user to restore an entry
$(document).on("click", "#restoreEntry", function(){
    let rowToEdit = $(this).closest("tr");
    let idOfRow = $(rowToEdit).find('#ComputerID').html()
    if (confirm("Are you sure you want to restore this entry? (page will refresh afterwards)") == true) {
        $.post("../methods/checklistrestore.php", {"idToRestore": idOfRow}, function(response) {
            $("#result").html(response);
            setTimeout(function() { location.reload(true); }, 2000);
        })
    }
})

// Allows the user to restore a user
$(document).on("click", "#restoreUser", function(){
    let rowToEdit = $(this).closest("tr");
    let idOfRow = $(rowToEdit).find('#UserID').html()
    if (confirm("Are you sure you want to restore this user? (page will refresh afterwards)") == true) {
        $.post("../methods/accountrestore.php", {"idToRestore": idOfRow}, function(response) {
            $("#result").html(response);
            setTimeout(function() { location.reload(true); }, 2000);
        })
    }
})

// Changes which table is shown to the user
$(document).on("click", '#entrieschoice', function(){
    $('#entries').attr("hidden",false)
    $('#users').attr("hidden",true)
})
$(document).on("click", '#userschoice', function(){
    $('#entries').attr("hidden",true)
    $('#users').attr("hidden",false)
})

$(document).on("change", ".urlchange", function(){
    sort = $(document).find('#datesort').val()
    page = $(document).find('#pagechange').val()
    size = $(document).find('#pagesize').val()
    url.searchParams.set('sort',sort)
    url.searchParams.set('page',page)
    url.searchParams.set('pagesize',size)
    window.location.replace(url)
})

$(document).on("click", "#prevpage", function(){
    page = $(document).find('#pagechange').val()
    if (!(page == 1)) {
        sort = $(document).find('#datesort').val()
        size = $(document).find('#pagesize').val()
        page = parseInt(page) - 1
        url.searchParams.set('sort',sort)
        url.searchParams.set('page',page)
        url.searchParams.set('pagesize',size)
        window.location.replace(url)
    }
})

$(document).on("click", "#nextpage", function(){
    select = $(document).find('#pagechange')
    page = $(document).find('#pagechange').val()
    pages = $(document).find('.lastpage').val()
    if (!(page == pages)) {
        sort = $(document).find('#datesort').val()
        size = $(document).find('#pagesize').val()
        page = parseInt(page) + 1
        url.searchParams.set('sort',sort)
        url.searchParams.set('page',page)
        url.searchParams.set('pagesize',size)
        window.location.replace(url)
    }
})