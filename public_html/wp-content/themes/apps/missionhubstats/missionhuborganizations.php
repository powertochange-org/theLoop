<?php
//including "people" seems to be broken.
$organizations = getIndexOfEndpoint("organizations", 'people', '10');
echo count($organizations['organizations']);
var_export($organizations['organizations']);

?>