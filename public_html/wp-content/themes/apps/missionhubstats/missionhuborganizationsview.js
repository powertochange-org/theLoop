(function($) {
    $(document).ready(function() {
        $("#daterange").hide();
        $("#organizations").hide();
        $("#report").change(function() {
            $("#report").each(function() {
                if($(this).val() =="pat") {
                    $("#daterange").slideDown();
                    $("#organizations").slideUp();
                } else if ($(this).val() =="engagement" || $(this).val() =="discipleship") {
                    $("#daterange").slideUp();
                    $("#organizations").slideDown();
                }
            })
        })
        
        
        $("#submit").click(function() {
            var org = document.getElementById("orgname");
            var orgname = org.options[org.selectedIndex].value;
            var report = document.getElementById("report");
            var reportname = report.options[report.selectedIndex].value;
            var yearselect = document.getElementById("year");
            var year = yearselect.options[yearselect.selectedIndex].value;
            handleSubmit(orgname, reportname, year);
            return false;
        }); 
    });
})(jQuery);

function handleSubmit(orgname, report, year) {
    /* Show a loading spinner */
	$("#ajax-loading").css("display", "block");
	
	/* Make the Ajax call to get the report data */
	$.post(
        MissionHubStatsAjax.ajaxurl,
        {
            action: "handle-submit",
            report: report,
            orgname: orgname,
            year: year,
            nonce: MissionHubStatsAjax.nonce
        },
        (function (response) {
            //if(!('error' in response) ) {
				/* We got an Ajax response! Hide the loading spinner to show we are done */
				$("#ajax-loading").css("display", "none");
				
				/* Show the result in the appropriate area */
                console.log(response);
                $("#report-table").html(response);
            //}
            //else {
            //    console.log(response.error);
            //}
        })
    )
}