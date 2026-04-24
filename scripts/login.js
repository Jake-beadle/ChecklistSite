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