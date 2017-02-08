<?php
require('../../../wp-blog-header.php');
require '../../themes/apps/workflow/inc/class.Workflow.inc.php';

if (is_user_logged_in()) {
    $filename = rawurldecode($_SERVER['REQUEST_URI']);
    $parts = explode("/", $filename);
    $filename = end($parts);
    $filename = str_replace('\\', '', $filename);
    $obj = new Workflow();
    if($obj->hasDocumentAccess($filename) == true) {
        //Display the document
        if(is_file("./$filename")) {
            $temp = explode(".", $filename);
            $ext = strtolower(end($temp));
            
            header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
            header("Content-Length:".filesize ("./$filename"));
            if(in_array($ext, array('jpg' , 'png', 'jpeg', 'gif', 'tiff', 'bmp'))){
                //for pictures
                header("Content-Type: image/$ext");
            } else {
                header("Pragma: private");
                header("Content-Type: application/$ext");
            }
            
            ob_end_flush();
            readfile("./$filename");
            exit;
        } else {
            //Not a file
            header($_SERVER["SERVER_PROTOCOL"]." 404 NOT FOUND");
            die();
        }
    }
}
echo 'Please contact <a href="mailto:helpdesk@p2c.com">helpdesk@p2c.com</a> if you require access to this document.<br>
    <br>If you have just uploaded this document, it will become available once you submit your form.';
?>
