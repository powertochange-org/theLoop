<?php

echo file_get_contents("http://hebron/DeployFromGit/default.aspx?$_SERVER[QUERY_STRING]");
echo $_SERVER["QUERY_STRING"];

?>