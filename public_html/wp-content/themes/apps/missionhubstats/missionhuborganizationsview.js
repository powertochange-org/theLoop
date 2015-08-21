(function($) {
    $(document).ready(function() {
        showDropdowns($("#report").val());
        $("#report").change(function() {
            showDropdowns($(this).val())
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

function showDropdowns(selection) {
    $("#report").each(function() {
        if(selection == "pat") {
            $("#daterange").slideDown();
            $("#organizations").slideUp();
        } else if (selection == "engagement" || selection == "discipleship") {
            $("#daterange").slideUp();
            $("#organizations").slideDown();
        } else if (selection == "decision") {
            $("#daterange").slideUp();
            $("#organizations").slideUp();
        }
    });
}

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
                $(".threshold").click(function() {
                    $.post(
                        MissionHubStatsAjax.ajaxurl,
                        {
                            action: "handle-submit",
                            report: "threshold",
                            orgname: orgname,
                            year: year,
                            label: $(this).context.id,
                            nonce: MissionHubStatsAjax.nonce
                        },
                        (function (response) {
                            $("#ajax-loading").css("display", "none");
                            $('#report-table').html(response);
                        })
                    )
                })
            //}
            //else {
            //    console.log(response.error);
            //}
        })
    )
}