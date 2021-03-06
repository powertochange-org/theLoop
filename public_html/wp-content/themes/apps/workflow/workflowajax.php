<?php
function workflow_upload_document() {
    $returndata = array('ReturnCode'=>'', 'Upload'=>'', 'Msg'=>'');
    //In case it is larger than what is allowed in the php ini
    if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
        $returndata['ReturnCode'] = '400';
        $postMax = ini_get('post_max_size');
        $returndata['Msg'] = 'The total allowable file size limit of '.$postMax.' has been exceeded.';
        echo json_encode($returndata);
        die();
    }
    $uploaddir = dirname(dirname(dirname( __DIR__))).'/uploads/p2cforms/';
    
    $filename = $_FILES["documents"]['name'];
    $temp_name = $_FILES["documents"]['tmp_name'];
    
    switch ($_FILES["documents"]['error']) {
        case 0:  // good upload
            if (is_uploaded_file($temp_name)) {
                $filename = str_replace('%', '', $filename); //Remove the % symbol from the filename
                $timefilename = time()."_".basename($filename);
                $uploadfile = $uploaddir.$timefilename;
                
                $imageFileType = strtolower(pathinfo($uploadfile, PATHINFO_EXTENSION));
                
                if($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'png' && $imageFileType != 'gif'
                    && $imageFileType != 'pdf' && $imageFileType != 'doc' && $imageFileType != 'docx' && $imageFileType != 'xls'
                    && $imageFileType != 'xlsx' && $imageFileType != 'txt') {
                    $returndata['ReturnCode'] = '6';
                    $returndata['Msg'] = 'Uploading file "'.$filename.'" has failed. Please upload one of the following file types: .jpg | .jpeg | .png | .gif | .doc | .docx | .xls | .xlsx | .pdf | .txt |';
                    break;
                }
                
                if (file_exists($uploadfile)) {
                    $returndata['ReturnCode'] = '304';
                    $returndata['Msg'] = '"'.$filename . '" already exists.';
                    $returndata['Upload'] = $timefilename;
                    break;
                }
                if (move_uploaded_file($temp_name,$uploadfile)) {
                    $returndata['ReturnCode'] = '0';
                    $returndata['Msg'] = 'File "'.$filename.'" uploaded successfully. <a href="/wp-content/uploads/p2cforms/'.$timefilename.'" target="blank">'.$timefilename.'</a>';
                    $returndata['Upload'] = $timefilename;
                } else {
                    $returndata['ReturnCode'] = '6';
                    $returndata['Msg'] = 'Uploading file "'.$filename.'" has failed.';
                }
            } else {
                $returndata['ReturnCode'] = '6';
                $returndata['Msg'] = 'The upload did not work.';
            }
            break;
        case 1:

        case 2:
            $maxsize = ini_get('upload_max_filesize');
            $returndata['ReturnCode'] = '2';
            $returndata['Msg'] = 'File "'.$filename.
            '" exceeded the maximum file size allowed. Please reduce the file size to '.$maxsize.' or less.';
            break;

        case 3:
            $returndata['ReturnCode'] = '3';
            $returndata['Msg'] = 'Upload of file "'.$filename.'" was incomplete. Please try again.';
            break;

        case 4: 
            $returndata['ReturnCode'] = '6';
            $returndata['Msg'] = 'Uploading file "'.$filename.'" has failed.';
            break;
        default:
            $returndata['ReturnCode'] = '400';
            $returndata['Msg'] = 'Unknown error';
    } 
    
    echo json_encode($returndata);
    exit;
}

add_action( 'wp_ajax_workflow_upload_document', 'workflow_upload_document' );


function workflow_get_supervisor() {
    require_once('inc/class.Workflow.inc.php');
    
    $returndata = array('ReturnCode'=>'200', 'Msg'=>'', 'Data'=>'');
    
    if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && 
        strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
        $returndata['ReturnCode'] = '400';
        $returndata['Msg'] = 'Post failed.';
        echo json_encode($returndata);
        die();
    }
    
    $returndata['Data'] = Workflow::getMultipleDirectApprovers($_POST['employeeNum']);
    
    echo json_encode($returndata);
    exit;
}

add_action( 'wp_ajax_workflow_get_supervisor', 'workflow_get_supervisor' );

?>
