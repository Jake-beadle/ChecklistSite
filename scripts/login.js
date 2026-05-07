$(document).ready(function(){
    $("#login").submit(function(event){
        event.preventDefault();
        var data = $(this).serializeArray();
            $.post("../methods/accountlogin.php", data, function(response) {
                // Once the data for logging in has been submitted, accountlogin.php will return a response depending on what happened (saved here)
                var res = JSON.parse(response);
                // If the details entered were valid, it will have set the session data, so the user should be redirected to the main page
                if (res.status == "ok") {
                    window.location.href = "/main.php"
                // If the details weren't valid, it returns a message explaining what went wrong to the user
                } else {
                    $("#result").html(res.msg)
                }
        })
    })
})

// Shows a form to the user that can be used to get their password from the database if they forget it
$(document).on("click", "#forgotpass", function(){
    $(document).find("#forgotpassdiv").attr("hidden", false)
    $("#forgotpassform").submit(function(event){
        event.preventDefault();
        var data = $(this).serializeArray();
            $.post("../methods/forgotpass.php", data, function(response) {
                // Shows the user whether an email was sent or not
                $("#result").html(response);
                // Refreshes the page after two seconds have passed
                setTimeout(function() { location.reload(true); }, 2000);
        })
    })
})