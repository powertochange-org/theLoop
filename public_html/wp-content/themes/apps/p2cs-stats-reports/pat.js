(function($) {
    $(document).ready(function() {
        $("#filtersummer").click(function() {
            handleButtonClick('summer');
            $("filterresult").html("You clicked summer!");
            return false; 
        });
        $("#filterspring").click(function() {
            handleButtonClick('spring');
            $("filterresult").html("You clicked spring!");
            return false; 
        });
    });
})(jQuery);

function handleButtonClick(buttonData) {
    var buttonaction;
    switch(buttonData) {
        case "summer":
            buttonaction = 'summer-filter';
            break;
        case "spring":
            buttonaction = 'spring-filter';
            break;
        default:
            buttonaction = 'this broke';
            break;            
    }
    $.post(
        //URL to send request to, defined in php on the server
        MissionHubStatsAjax.ajaxurl,
        //Data to pass
        {
            action: buttonaction,
            button: buttonData,
            nonce: MissionHubStatsAjax.nonce
        },
        //Function to execute
        (function(response) {
            $("#filterresult").html(response);
        })
    
    );
};