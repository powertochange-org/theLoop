(function($) {
    $(document).ready(function() {
        $("#home").click(function() {
            $.ajax({
                url: "missionhubstats.php"
            }).done(function(data) {
                $("#reportcontent").html(data);
            });
            handleButtonClick("home");
            return false;
        });
        $("#missionhub").click(function() {
            handleButtonClick("missionhub");
            return false;
        });
        $("#pat").click(function() {
            $.ajax({
                url: "missionhubpat.php"
            }).done(function(data) {
                $("reportcontent").html(data);
            });
            $("filter").html("yay!");
            return false;
        });
        $("#eventbrite").click(function() {
            handleButtonClick("eventbrite");
            return false;
        });
        $("#theloop").click(function() {
            handleButtonClick("theloop");
            return false;
        });
        $("#admin").click(function() {
            handleButtonClick("admin");
            return false;
        });
    }); 
})(jQuery);

function handleButtonClick(buttondata) {
    var buttonaction;
    switch(buttondata) {
        case "home":
            break;
        case "missionhub":
            buttonaction = "nav-click";
            break;
        case "pat":
            break;
        case "eventbrite":
            break;
        case "theloop":
            break;
        case "admin":
            break;
        default:
            break;
    }
    
    $.post(
        WordPressAjax.ajaxurl,
        //Data to pass
        {
            action: buttonaction,
            button: buttondata,
            nonce: WordPressAjax.nonce
        },
        (function(response) {
            $("filter").html(response);
        })
    );
}