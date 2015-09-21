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
                                        $(".download").on('click', function (event) {
                                            exportToCSV.apply(this, [$('#report'), 'export.csv']);
                                        });
                                        $('.parent').click(function() {
                                            var lvl = $(this).attr('class').split("level")[1].split(' ')[0];
                                            if ($(this).hasClass('collapsed')) {
                                                $(this).nextUntil('tr.parent.level'+lvl, 'tr.level'+(parseInt(lvl)+1)).show();
                                                $(this).removeClass('collapsed');
                                            } else {
                                                $(this).addClass('collapsed');
                                                $(this).nextUntil('tr.parent.level'+lvl,'tr').addClass('collapsed').hide();
                                            }
                                        });
                                        // Start with only the first level expanded
                                        $('.level1').addClass('collapsed');
                                        $('.level1').nextUntil('tr.parent.level1','tr').addClass('collapsed').hide();
				})
			)
			
			/* Return false so the form doesn't actually submit */
            return false;
        });
    });
})(jQuery);