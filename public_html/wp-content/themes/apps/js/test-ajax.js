(function($) {
	$(document).ready(function(){
		// Wire up button click events
		$('#button1').click(function() {
			handleAjaxButtonClick('Button1');
			// Return false so that the button click doesn't actually go anywhere!
			return false;
		});
		
		$('#button2').click(function() {
			handleAjaxButtonClick('Button2');
			// Return false so that the button click doesn't actually go anywhere!
			return false;
		});
	});
})(jQuery);

function handleAjaxButtonClick(buttonData){
	// Send a POST Ajax request to the server
	$.post(
		// What URL to send the request to - this is defined in PHP on the server
		WordPressAjax.ajaxurl,
		// Data to pass along
		{
			// The action to call (which we've registered with WordPress in PHP
			action : 'test-ajax',			
			// The variables to pass
			button: buttonData,		 
			// Send a "nonce" along which proves that the request is from a legit, logged-in user
			nonce : WordPressAjax.nonce
		},
		// What function to call when the Ajax request returns data
		(function(response) {
			$('#content-left').html(response);
		})
	);
};
