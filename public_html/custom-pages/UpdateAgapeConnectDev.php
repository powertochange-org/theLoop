<?php

echo file_get_contents('http://hebron/DeployFromGit/');

include('../wp-config.php');
			
// Creates a connection because wp will not be active
$con=mysqli_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_NAME"));

$sql = "INSERT INTO  `var_dump` (`id` ,`dump` ,`time`) VALUES (NULL ,'".mysql_real_escape_string(var_export($_POST, true))."', NULL)";
mysqli_query($con, $sql);
mysqli_close($con);

?>