    $(document).ready(function(){ 
		$(document).pngFix(); 
		
		$(".tab_content").hide(); //Hide all content
		$("ul.tabs li:first").addClass("active").show(); //Activate first tab
		$(".tab_content:first").show(); //Show first tab content
		//On Click Event
		$("ul.tabs li").click(function() {
			$("ul.tabs li").removeClass("active"); //Remove any "active" class
			$(this).addClass("active"); //Add "active" class to selected tab
			$(".tab_content").hide(); //Hide all tab content
			var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
			$(activeTab).fadeIn(); //Fade in the active ID content
			return false;
		});		
    });
	
	
// initialise plugins
	jQuery(function(){
		jQuery('ul.sf-menu').superfish();
	});
		
// <![CDATA[
$(document).ready(function(){	
	$("#slider").easySlider({
		/*controlsBefore:	'<p id="controls">',
		controlsAfter:	'</p>',*/
		auto: true, 
		continuous: true
	});	
});
// ]]>
		
	Cufon.replace('.replace');
