<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
</head>
<body>
	<h1>Deploy from Git to Hebron</h1>
<?php

$repos = array(
	'theLoop' => array (
		'/home/powertochange/domains/staff.powertochange.org',
		'/home/development/domains/devstaff.powertochange.org'));
$project = $_GET["project"];
		
if (bool in_array($project ,  $repos )){
	foreach ($repos[$project] as $folder){
		$gitCommand = "pull";
		echo "<p>Running command <b>git " + $gitCommand + "</b> in folder <b>" + $folder + "</b></p>";
		$output = runGitCommand($folder, $gitCommand);		
		echo "<span style='font-family: courier new; font-size: small'>";
		echo output.Replace(" ", "&nbsp;").Replace("\n", "<br />");
		echo "</span>";
	}
}
else {
	echo "<p>Unknown project <b>git " + project + "</b></p>";
}

function syscall ($folder, $gitCommand) {
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