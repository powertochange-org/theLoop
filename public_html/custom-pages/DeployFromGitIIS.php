<?php 
echo file_get_contents("http://hebron/DeployFromGit/default.aspx?$_SERVER[QUERY_STRING]"); 
echo file_get_contents("http://webiis/DeployFromGit/default.aspx?$_SERVER[QUERY_STRING]"); 
?>