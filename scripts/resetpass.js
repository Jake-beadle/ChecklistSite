$(document).ready(function(){
    $("#reset").submit(function(event){
        event.preventDefault();
        var data = $(this).serializeArray();
            $.post("../methods/passreset.php", data, function(response) {
                // Shows the user if the password has been updated or not
                $("#result").html(response); 
                // Waits 2 seconds before reloading the page (enough time for the user to read the response)
                setTimeout(function() { location.reload(true); }, 2000);
        })
    })
})