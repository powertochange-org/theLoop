(function($) {
    $(document).ready(function() {
        $("#report-generate").click(function() {	
			/* Show a loading spinner */
			$("#ajax-loading").css("display", "block");
			
			/* Loop through the form elements, and add them to the data to send
			   with the Ajax request. */
			var ajaxPostData = new Object();
			var formElements = document.getElementById('report-parameters').elements;
			for(var i = 0; i < formElements.length; i++)
			{
				ajaxPostData[formElements[i].name] = formElements[i].value;
			}
			
			/* Add a few important configuration settings to the post data as well */
			var reportName = document.getElementById("reportName").value;
			ajaxPostData["reportName"] = reportName;		
			ajaxPostData["action"] = "p2cs-stats-report-generate";
			ajaxPostData["nonce"] = P2CSStatsReportsAjax.nonce;
			
			console.log(ajaxPostData);
			
			/* Log what we are doing */
			console.log('Initiating Ajax call for report ' + reportName);
			
			/* Send an Ajax request to the server to generate the report */
			$.post(
				/* URL to send the Ajax call to. This has been defined for us already
				 * by PHP to work with WordPress */
				P2CSStatsReportsAjax.ajaxurl,
				
				/* Data to include with the Ajax call (assembled above) */
				ajaxPostData,
				
				/* Define what happens when the Ajax call returns a response */
				(function (response) {
					/* We got an Ajax response! Hide the loading spinner to show we are done */
					$("#ajax-loading").css("display", "none");
					
					/* Show the result in the appropriate area */
					$("#report-table").html(response);
                                        tsorter.create('report');
				})
			)
			
			/* Return false so the form doesn't actually submit */
            return false;
        });
    });
})(jQuery);