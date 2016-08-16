<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
</head>
<body>
	<h1>Deploy from Git to web-host-1</h1>
<?php

$repos = array(
	'theLoop' => array (
		'/home/powertochange/domains/staff.powertochange.org',
		'/home/powertochange/domains/stafftemp.powertochange.org',
		'/home/development/domains/devstaff.powertochange.org'),
	'agencyreporting' => array (
		'/home/development/domains/dev.agencyreporting.powertochange.org/public_html',
		'/home/powertochange/domains/agencyreporting.powertochange.org/public_html'),
        'staffappsbutton' => array (
                '/home/powertochange/domains/staffappsbutton.powertochange.org/public_html')
    );

if (array_key_exists("project", $_GET)){
	$project = $_GET["project"];
	if (array_key_exists($project ,  $repos )){
		foreach ($repos[$project] as $folder){
			$gitCommand = "pull";
			echo "<p>Running command <b>git $gitCommand</b> in folder <b>$folder</b></p>";
			$output = runGitCommand($folder, $gitCommand);
			echo "<span style='font-family: courier new; font-size: small'>";
			echo str_replace("\n", "<br />", str_replace(" ", "&nbsp", $output));
			echo "</span>";
		}
	}
	else {
		echo "<p>Unknown project: <b>$project</b></p>";
	}
}
else {
	echo "<p>No project given <BR><BR>Example:</p>";
	echo "<span style='font-family: courier new; font-size: small'>";
	echo "?project=AgapeConnectCentral";
	echo "</span>";
}


function runGitCommand ($folder, $gitCommand) {
    $descriptorspec = array( 1 => array('pipe', 'w') ); // stdout is a pipe that the child will write to
    $resource = proc_open("git $gitCommand", $descriptorspec, $pipes, $folder);
    if (is_resource($resource)) {
           $output = stream_get_contents($pipes[1]);
           fclose($pipes[1]);
           proc_close($resource);
           return $output;
    }
}

?>
</body>
</html>
