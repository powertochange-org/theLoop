<?php
/*
 * Template Name: zApp URL Shortener
 * Description: Provide an interface to create shortened URLs with the p2c.sh prefix,
 *				using the Yourls install at that domain.
 * Author: Jason Brink
 */
?>
<?php get_header(); ?>
	<div id="content">
		<div id="main-content">	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="post">
				<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<div class="entry">
					<?php 
					/*** Include page content for instructions, help text, etc ***/
					the_content(); 
					?>
					
					<?php
					/*** URL Shortener App Code begins here ***/
					
					// Initialize some variables
					$message = "";		// For displaying a message to the user about what was done
					$errors = array();	// For storing an array of errors
					$title = "";		// For storing the title of the page to shorten, if found
					$errorLoadingPage = 0; // Indicate if there was an error loading the URL to be shortened
					
					// Create a custom error handler function for the call to loadHTMLFile. Otherwise,
					// if the web page cannot be found, or there are HTML errors on the page, it will 
					// print warnings to the screen!
					function loadHTMLFileWarningHandler($errno, $errstr) {
						global $errorLoadingPage;
						
						// Debugging print message:
						//print "<p>Error: $errno, $errstr</p>";
						
						// Check specifically for an error indicating it couldn't load the page
						if (strpos($errstr, "failed to load external entity") !== false) {
							$errorLoadingPage = 1;
						}
					}					

					// Check if we are processing a form submission
					if (isset($_POST['submit'])) {
						
						// Ensure the long URL required field was filled out
						if (!isset($_POST['longurl']) || !$_POST['longurl']) {
							$errors[] = 'Long URL is required';
						} else {
							$longurl = $_POST['longurl'];
							
							// Check if the user put the http or https prefix on the URL. If not, add it
							if (preg_match('#^https?://#i', $longurl) === 0) {
								$longurl = "http://" . $longurl;
							}
							
							// Attempt to grab the web page to see if it is valid, and to get the title
							$doc = new DOMDocument();
							
							// (set custom error handler for warnings generated inside loadHTMLFile)
							set_error_handler("loadHTMLFileWarningHandler", E_WARNING);
							$doc->loadHTMLFile($longurl);
							restore_error_handler();
							
							// Check if there were errors loading the HTML file
							if ($errorLoadingPage) {
								$errors[] = 'That URL does not appear to be valid.';
							} else {
								// Attempt to grab the "title" node
								$xpath = new DOMXPath($doc);
								$titleNode = $xpath->query('//title')->item(0);

								// If there is a title node, store it's value
								if ($titleNode) {
									$title = $titleNode->nodeValue;
								}
							}
						}
						
						// If we got this far without errors, then we can proceed with creating
						// the short link
						if (count($errors) == 0) {
							// Create a time-limited signature token used to call the Yourls API
							$timestamp = time();
							$signature = md5( $timestamp . constant('YOURLS_SECRET') );

							// Set up CURL to call the API
							$post_fields = array(     // Data to POST
									'action'    => 'shorturl',
									'url'       => $longurl,
									'format'    => 'json',
									'timestamp' => $timestamp,
									'signature' => $signature
								);
							
							// Add optional elements to the POST variables if available
							if ($_POST['keyword']) {
								$post_fields['keyword'] = $_POST['keyword'];
							}
							if ($title) {
								$post_fields['title'] = $title;
							} 
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, constant('YOURLS_API_URL'));
							curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
							curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

							// Fetch and return content
							$curl_data = curl_exec($ch);
							curl_close($ch);

							// Decode the json result
							$json_data = json_decode( $curl_data );
							
							// Debugging print statements:
							//print "Curl data: <br />";
							//var_dump($curl_data);
							//print "<br /><br />JSON data:<br />";
							//var_dump($json_data);
							
							// Check if the Yourls call was successful or not, and show a message to the user
							if ($json_data->status == "fail") {
								$errors[] = "Error: " . $json_data->message;
							} else {
								$message = "Success! Short link <b>" . $json_data->shorturl . "</b> created.";
							}
						}
					}
					
					
					// Print out any messages or errors for the user
					if ($message) {
						print "<p style='color: green'>$message</p>";
					}
					if (count($errors) > 0) {
						print "<p style='color: red'>";
						foreach ($errors as $error) {
							print "$error<br />";
						}
						print "</p>";
					}
					?>					
					
					<div class="form">
					<form name="shortener" method="POST">
					  <label for="longurl" style="font-weight: bold">Long URL:</label>
					  <input type="text" name="longurl" size="50" value="<?php if (isset($longurl)) { print $longurl; } ?>" /> <br />
					  
					  <label for="keyword" style="font-weight: bold">Short URL:</label>
					  p2c.sh/<input type="text" name="keyword" style="text-transform:lowercase;" value="<?php if (isset($_POST['keyword'])) { print $_POST['keyword']; } ?>"/> (leave blank to have a short link auto-generated) <br /><br />
					  <input type="submit" name="submit" value="Create Short Link" />
					</form>
					</div>
					
					<?php
					
					/*** URL Shortener App Code ends here ***/
					
					?>						
				</div>
				<div class="clear"></div>				
			</div>
			<?php endwhile; else : ?>
			<div class="post">
				<h2>404 - Not Found</h2>
				<p>The page you are looking for is not here.</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<!--content end-->
	<!--Popup window-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>	
<?php get_footer(); ?>