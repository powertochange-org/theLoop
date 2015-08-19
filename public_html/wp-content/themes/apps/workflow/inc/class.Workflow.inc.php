<?php
/*
*The main workflow class.
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/


//include_once('workflow/config.php');
//include_once('./config-staff.php');

class Workflow {
    private $name;
    private $startAccess;
    private $approvers;
    /*private $approver2;
    private $approver3;
    private $approver4;*/
    private $fields; 
    
    
    public function __construct() {
    }
    
    public function __destruct() {
        
    }
    
    public function createWorkflow($name, $startAccess, $approver, $approver2, $approver3, $approver4) {
        $this->name = $name;
        $this->startAccess = $startAccess;
        $this->approvers = array($approver, $approver2, $approver3, $approver4);
        $this->fields = array();
    }
    
    public function addField($type, $label, $editable, $approvalonly, $approvalshow, $size, $level) {
        if($size == '')
            $size = 0;
        $this->fields[] = array($type, $label, $editable, $approvalonly, $approvalshow, $size, $level);
    }
    
    /**
        Stores the new workflow layout.
    */
    public function storeToDatabase() {
        global $wpdb;
        
        $sql = "INSERT INTO workflowform (NAME, APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4)
                VALUES ('$this->name', '".$this->approvers['0']."'";
        
        for($i = 1; $i < count($this->approvers); $i++) {
            if($this->approvers[$i] != -1) {
                $sql .= ", '".$this->approvers[$i]."'";
            } else {
                $sql .= ", NULL";
            }
        }
        $sql .= ")";
        
        $result = $wpdb->query($sql, ARRAY_A);
        $inserted_id = $wpdb->insert_id;
        
        if(!$result) {
            header("location: ?page=index");
        } 
        
        //echo '<br>Inserted the ID: '.$inserted_id.'<br>';
        
        //Store fields into WorkflowDetails
        for($i = 0; $i < count($this->fields); $i++) {
            $sql = "INSERT INTO workflowformdetails (FORMID, FIELDID, POSITION, TYPE, LABEL, EDITABLE, APPROVAL_ONLY, 
                                                    APPROVAL_SHOW, APPROVAL_LEVEL, FIELD_WIDTH)
                    VALUES ('$inserted_id', '$i', '$i', '".$this->fields[$i][0]."', '".$this->fields[$i][1]."', '".$this->fields[$i][2]."', 
                    '".$this->fields[$i][3]."', '".$this->fields[$i][4]."', '".$this->fields[$i][6]."', ";
            
            if($this->fields[$i][5] == 0)
                $sql .= "NULL";
            else
                $sql .= "'".$this->fields[$i][5]."'";
            
            $sql .= ")";
            $result = $wpdb->query($sql, ARRAY_A);
            //echo 'RESULT: '.$i.' : '.$result.'<br>';
            //echo $sql.'<br>';
            if(!$result) {
                die('Failed to insert form fields.');
            }
        }
    }
    
    /**
    Updates the database with the user submissions.
    */
    public function updateWorkflowSubmissions($fields, $newstatus, $submissionID, $formID, $user, $misc_content, $commenttext) {
        /*
        1) Brand new field
        2) Continue to edit
        3) Submitted
        4) Approval new
        5) Approval edit
        */
        global $wpdb;
        $oldstatus = 1;
        $oldcomment = '';
        /*echo 'DISPLAYING THE RESULTS for WFID:'.$formID.'<br>';

        for($i = 0; $i < count($fields); $i++) {
            echo $fields[$i][0].'='.$fields[$i][1].'<br>';
            
        }
        echo 'done display<br>';*/
        
        
        //Check to see if an update or insert is allowed
        $sql = "SELECT STATUS, STATUS_APPROVAL, COMMENT
                FROM workflowformstatus
                WHERE SUBMISSIONID = '$submissionID'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        
        if(count($result) != 0) {
            $row = $result[0];
            $oldstatus = $row['STATUS'];
            $oldApprovalStatus = $row['STATUS_APPROVAL'];
            $oldcomment = $row['COMMENT'];
            //echo 'DEBUG: Old Status:'.$oldstatus.'<br>';
        }
        if($oldstatus == 4) {
            
        } else if($oldstatus >= 7) {
            die('This form can no longer be edited.');
        }
        
        $directApprover = Workflow::getDirectApprover($user);
        
        date_default_timezone_set('America/Los_Angeles');
        
        if($submissionID == 0) {
            $misc_content = str_replace("\\", "\\\\", $misc_content);//$misc_content;//str_replace("'", "\'", $misc_content);
            $new_misc_content = str_replace("'", "\'", $misc_content);//$misc_content;//str_replace("'", "\'", $misc_content);
        
            //echo 'SUBMISSION ID : '.$submissionID.' not found. <br>';
            
            $sql = "INSERT INTO workflowformstatus (USER, STATUS, FORMID, MISC_CONTENT, DATE_SUBMITTED, COMMENT, APPROVER_DIRECT)
                    VALUES ('$user', '$newstatus', '$formID', '$new_misc_content', '".date('Y-m-d')."', ";
            
            if($commenttext != '') {
                $commenttext = str_replace("\\", "\\\\", $commenttext);
                $sql .= "'<b>[".Workflow::loggedInUserName()." on: ".date("Y-m-d H:i")."]</b><br>".str_replace("'", "\'", $commenttext)."', ";
            } else {
                $sql .= "NULL, ";
            }
            
            if($directApprover != 0) {
                $sql .= "'$directApprover')";
            } else {
                $sql .= "NULL)";
            }
        } else {
            $newApprovalStatus = 1;//TODO: Check if approval goes one level down or back to the beginning
            if($oldstatus == 4 && $newstatus == 4) {
                $newApprovalStatus = $oldApprovalStatus + 1;
            } else if($newstatus == 7 || $newstatus == 8) {
                $newApprovalStatus = 100;
            }
            
            $sql = "UPDATE workflowformstatus 
                    SET STATUS = '$newstatus',
                        STATUS_APPROVAL = '$newApprovalStatus',
                        DATE_SUBMITTED = '".date('Y-m-d')."' ";
                    
            if($commenttext != '') {
                $oldcomment = str_replace("\\", "\\\\", $oldcomment);
                $commenttext = str_replace("\\", "\\\\", $commenttext);
                $newtext = str_replace("'", "\'", $oldcomment).'<br><b>['.Workflow::loggedInUserName().' on: '.date('Y-m-d H:i').']</b><br>'.
                    str_replace("'", "\'", $commenttext);
                
                $sql .= ", COMMENT = '$newtext' ";
                
            }
            $sql .= "WHERE SUBMISSIONID = '$submissionID'";
        }
        
        $result = $wpdb->query($sql, ARRAY_A);
        
        if(!$result) {
            die('Failed to update status.');
        }
        
        if($submissionID == 0) {
            $submissionID = $wpdb->insert_id;
        }
        
        
        
        //Update the fields
        for($i = 0; $i < count($fields); $i++) {
            $sql = "SELECT SUBMISSIONID 
                    FROM workflowformsubmissions
                    WHERE SUBMISSIONID = '$submissionID'
                    AND FIELDID = '".$fields[$i][0]."'";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            if(count($result) == 0) {
                $sql = "INSERT INTO workflowformsubmissions (SUBMISSIONID, FIELDID, VALUE)
                        VALUES ('$submissionID', '".$fields[$i][0]."', '".$fields[$i][1]."')";
            } else {
                $sql = "UPDATE workflowformsubmissions 
                        SET VALUE = '".$fields[$i][1]."'
                        WHERE SUBMISSIONID = '$submissionID'
                        AND FIELDID = '".$fields[$i][0]."'";
            }
            
            
            $result = $wpdb->query($sql);
            /*var_dump($result);
            if(!$result) {
                echo htmlspecialchars($sql);
                echo('<br>Query failed to update the FIELDID value.<br>');
            }*/
        }
    }
    
    /**
    Loads a workflow.
    */
    public function loadWorkflowID() {
        global $wpdb;
        if(!isset($_GET['wfid']) || $_GET['wfid'] == '') {
            die("NO ID GIVEN");
        }
        $id = $_GET['wfid'];
        
        $response = '';
        
        $sql = "SELECT *
                FROM workflowform
                WHERE FORMID = '$id'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $response .= $row['NAME'].'<br>';
        }
        
        
        $sql = "SELECT *
                FROM workflowformdetails
                WHERE FORMID = '$id'
                ORDER BY POSITION ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            //$response .= $row['FIELDID'].' | '.Workflow::translateFieldType($row['TYPE']).' | '.$row['LABEL'].' | '.
            //    $row['EDITABLE'].' | '.$row['APPROVAL_ONLY'].'<br>';
            
            if($row['APPROVAL_ONLY'] == 1) {
                //continue;
                
            }
                
            
            if($row['TYPE'] == 1) {
                if($row['APPROVAL_ONLY'] == 1)
                    $response .= '<div class="workflow workflowlabel approval">'.$row['LABEL'].'</div>';
                else 
                    $response .= '<div class="workflow workflowlabel">'.$row['LABEL'].'</div>';
            } else if($row['TYPE'] == 0) {
                if($row['APPROVAL_ONLY'] == 1)
                    $response .= '<div class="workflow workflowright style-1 approval"';
                else
                    $response .= '<div class="workflow workflowright style-1"';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;"';
                }
                
                
                $response .='><input type="text" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                    '" placeholder="'.$row['LABEL'].'"></div>';
            } else if($row['TYPE'] == 2) {
                $response .= ' ';
            } else if($row['TYPE'] == 3) {
                $response .= '<div class="clear"></div>';
            } else if($row['TYPE'] == 4) {
                if($row['APPROVAL_ONLY'] == 1) {
                    $response .= '<div class="workflow workflowlabel approval">
                        <input type="checkbox" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                        $row['FIELDID'].'">'.$row['LABEL'].'</div>';
                } else {
                    $response .= '<div class="workflow workflowlabel">
                        <input type="checkbox" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                        $row['FIELDID'].'">'.$row['LABEL'].'</div>';
                }
            }
            
            //file_put_contents('HAHAHA.txt', $response);
            
        }
        
        
        return 'DEBUG: You attempted to load ID: '.$id.' : '.$response;
    }
    
    /**
    Decides which layout should be displayed.
    */
    public function configureWorkflow() {
        global $wpdb;
        //$_POST      = array_map('stripslashes_deep', $_POST);
        /*$_GET       = array_map('stripslashes_deep', $_GET);
        $_COOKIE    = array_map('stripslashes_deep', $_COOKIE);
        $_REQUEST   = array_map('stripslashes_deep', $_REQUEST);*/
        $configSuccess = $approver = 0;
        $configMsg = '';
        
        if(Workflow::loggedInUser() == '0') {
            echo('<br>You need to log in.');
            return;
        }
            
        $configvalue = $status = 1;
        $sbid = 0;
        $misc_content = '';
        $comments = '';
        $submittedby = '';
        //TODO: use this class to configure the loadWorkflowEntry function
        
        $loggedInUser = Workflow::loggedInUser();
        //echo 'DEBUG: USER LOGGED IN:'.$loggedInUser.'<br>';
        
        if(isset($_GET['sbid']) && $_GET['sbid'] != '') {
            $sbid = $_GET['sbid'];
            
            $sql = "SELECT STATUS, STATUS_APPROVAL, workflowformstatus.FORMID, COMMENT, MISC_CONTENT, USER, 
                            APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, APPROVER_DIRECT
                    FROM workflowformstatus
                    INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID
                    WHERE SUBMISSIONID = '$sbid'";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            if(count($result) == 1) {
                $row = $result[0];
                $wfid = $row['FORMID'];
                $configvalue = $status = $row['STATUS'];
                $approvalStatus = $row['STATUS_APPROVAL'];
                $comments = $row['COMMENT'];
                $submittedby = $row['USER'];
            } else {
                echo 'That submission does not exist.';
                return;
            }
            
            
            //Check if the person is authorized to look at this filled out form
            //TODO: Complete authentication
            
            $currentApprovalRole = -1;
            $hasAnotherApproval = 0;
            if($approvalStatus == 1) {
                $currentApprovalRole = $row['APPROVER_ROLE'];
                if($row['APPROVER_ROLE2'] != '')
                    $hasAnotherApproval = 1;
            } else if($approvalStatus == 2) {
                $currentApprovalRole = $row['APPROVER_ROLE2'];
                if($row['APPROVER_ROLE3'] != '')
                    $hasAnotherApproval = 1;
            } else if($approvalStatus == 3) {
                $currentApprovalRole = $row['APPROVER_ROLE3'];
                if($row['APPROVER_ROLE4'] != '')
                    $hasAnotherApproval = 1;
            } else if($approvalStatus == 4) {
                $currentApprovalRole = $row['APPROVER_ROLE4'];
            } else if($approvalStatus == 100) {
                //The approval status is incorrect for some reason
                if($row['APPROVER_ROLE'] == 8 || $row['APPROVER_ROLE2'] == 8 || $row['APPROVER_ROLE3'] == 8 
                    || $row['APPROVER_ROLE4'] == 8) {
                    $currentApprovalRole = 8;
                } else if(Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE']) 
                    || Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE2'])
                    || Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE3'])
                    || Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE4'])) {
                    $approver = 1;
                }
            }
            
            
            if($currentApprovalRole == 8) {
                $approver = ($row['APPROVER_DIRECT'] == $loggedInUser || Workflow::getDirectApprover($submittedby) == $loggedInUser 
                    || Workflow::hasRoleAccess($loggedInUser, $currentApprovalRole));
            } else if(!$approver) {
                $approver = (Workflow::hasRoleAccess($loggedInUser, $currentApprovalRole));
            }
            
            //$approver = 1;
            if($configvalue == 4 && $approver) {
                echo 'DEBUG: You are an approver '.$loggedInUser.'<br>';
            } else if(($configvalue == 7 || $configvalue == 8) && $approver) {
                echo 'DEBUG: You are an approver '.$loggedInUser.'<br>';
                $configvalue = 9;
            } else if($submittedby != $loggedInUser) {
                echo 'You do not have access to view this form.<br>';
                return;
            } else if($configvalue == 4) {
                $configvalue = 0;
            }
            
            if($row['MISC_CONTENT'] != '') {
                //echo $row['MISC_CONTENT'];
                $misc_content = $row['MISC_CONTENT'];
            }
            
        } else { //CREATE A NEW FORM TO FILL OUT
            if(!isset($_GET['wfid']) || $_GET['wfid'] == '') {
                echo 'NO ID GIVEN.<br>';
                return;
            }
            $wfid = $_GET['wfid'];
            
            if(isset($_POST['misc_content'])) {
                $misc_content = stripslashes($_POST['misc_content']);
            }
            $submittedby = $loggedInUser;
        }
        
        
        //TODO Authorize the person that is trying to access this function or page.
        //echo 'DEBUG: Workflow status: '.$configvalue.'<br>';
        echo Workflow::loadWorkflowEntry($wfid, $configvalue, $sbid, $misc_content, $comments, $submittedby, 
            $status, $approvalStatus, $hasAnotherApproval);
    }
    /**
    
    *1) Brand new field
    *2) Continue to edit
    *3) No access
    *4) Approval New
    *5) 
    *6) 
    *7) Approved Final
    *8) Declined Final
    *9) Approver - show all
    *10) Cancelled 
    */
    public function loadWorkflowEntry($id, $configuration, $submissionID, $misc_content, $comments, $submittedby, 
        $status, $approvalStatus, $hasAnotherApproval) {
        global $wpdb;
        $response = '';
        
        $sql = "SELECT *
                FROM workflowform
                WHERE FORMID = '$id'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $row = $result[0];
            $response .= '<h2>'.$row['NAME'].' <span style="font-size:16px;">Submitted by: '.Workflow::getUserName($submittedby).'</span></h2>';
        } else {
            header('location: ?page=viewsubmissions');
            die();
        }
        
        if($configuration == 0 || $configuration == 4) {
            $response .= '<p class="reviewing">This form is currently under review.</p>';
        } else if($configuration == 7 || ($configuration == 9 && $status == 7)) {
            $response .= '<p class="approved">This form was approved.</p>';
        } else if($configuration == 8 || ($configuration == 9 && $status == 8)) {
            $response .= '<p class="denied">This form was denied.</p>';
        } else if($configuration == 10) {
            $response .= '<p class="denied">This form was cancelled.</p>';
        }
        
        $response .='<hr>';
        if(0 < $configuration && $configuration < 7)
            $response .= '<form id="workflowsubmission" action="?page=process_workflow_submit" method="POST" autocomplete="off" >';
        
        //Display the misc content
        if($misc_content != '') {
            $response .= $misc_content.'<br>';
            $response .= '<textarea hidden name="misc_content" rows="1" cols="1">'.$misc_content.'</textarea>';
        } else {
            //echo 'No extra content<br>';
        }
        
        $sql = "SELECT *
                FROM workflowformdetails
                WHERE FORMID = '$id'
                ORDER BY POSITION ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if($configuration != 1) {
            $sql = "SELECT FIELDID, VALUE
                    FROM workflowformsubmissions
                    WHERE SUBMISSIONID = '$submissionID'
                    ORDER BY FIELDID ASC";
        
            $savedResult = $wpdb->get_results($sql, ARRAY_A);
            $prevSubmissions = array();
            foreach($savedResult as $row) {
                //echo $row['FIELDID'].' and '.$row['VALUE'].'<br>';
                $prevSubmissions[] = array($row['FIELDID'], $row['VALUE']);
            }
        }
        
        
        
        $count = 0;
        foreach($result as $row) {
            $count++;
            $fieldvalue = '';
            if($configuration != 1) {
                //echo 'USING '.Workflow::findValue($prevSubmissions, $row['FIELDID']).'<br>';
                $fieldvalue = Workflow::findValue($prevSubmissions, $row['FIELDID']);
            }
            
            //Determines whether the field will be an approval field and if it is an editable field.
            $editableField = ($configuration != 4 || (($row['APPROVAL_ONLY'] == 1 || $row['EDITABLE'] == 1) && $configuration == 4));
            $approval_show = ($configuration >= 7 && $row['APPROVAL_SHOW']) || $configuration == 9 || 
                ($configuration == 0 && $row['APPROVAL_SHOW'] && $row['APPROVAL_LEVEL'] < $approvalStatus); //for submitter while under review
            $appLvlAccess = $row['APPROVAL_LEVEL'] <= $approvalStatus;
            //echo 'DEBUG: ID:'.$row['FIELDID'].' | configuration:'.$configuration.' | APPROVAL_SHOW:'.$row['APPROVAL_SHOW'].' | APPROVAL_LEVEL:'.$row['APPROVAL_LEVEL']. ' | approvalStatus:'.$approvalStatus.' | Value:'.$fieldvalue.'<br>';
            
            if($configuration >= 7 || $configuration == 0)
                $editableField = 0;
            
            if($row['TYPE'] == 1) { //Label
                if($row['APPROVAL_ONLY'] == 1) {
                    if($configuration == 4 && $appLvlAccess || $approval_show) {
                        $response .= '<div class="workflow workflowlabel approval" ';
                    } else {
                        continue;
                    }
                } else {
                    $response .= '<div class="workflow workflowlabel" ';
                }
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= 'style="width:'.$row['FIELD_WIDTH'].'px;"';
                }
                
                $response .= '>'.$row['LABEL'].'</div>';
                
            } else if($row['TYPE'] == 0) { //Textbox
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval"';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1"';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;"';
                }
                
                $response .= '>';
                
                if($editableField) {
                    $response .= '<input type="text" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" placeholder="'.$row['LABEL'].'" value="'.$fieldvalue.'">';
                } else {
                    $response .= $fieldvalue;
                }
                $response .= '</div>';
                
                    
                    
                    
                    
                    
            } else if($row['TYPE'] == 2) { //Option
                $response .= ' ';
            } else if($row['TYPE'] == 3) { //Newline
                $response .= '<div class="clear"></div>';
            } else if($row['TYPE'] == 4) { //Checkbox
                if($row['APPROVAL_ONLY'] == 1) {
                    if($configuration == 4 && $appLvlAccess || $approval_show) {
                        $response .= '<div class="workflow workflowlabel approval"';
                            
                    } else 
                        continue;
                } else {
                    $response .= '<div class="workflow workflowlabel"';
                }
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;"';
                }
                
                $response .= '>';
                
                if($editableField) {
                    $response .= '<input type="hidden" name="workflowfieldid'.$row['FIELDID'].'" value="0">';
                }
                
                $response .= '<input type="checkbox" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                    $row['FIELDID'].'" value="1" ';
                
                if(!$editableField) {
                    $response .= 'disabled ';
                }
                if($fieldvalue)
                    $response .= 'checked';
                $response .='>'.$row['LABEL'].'</div>';
                
                
                
                
            }
            
            //file_put_contents('HAHAHA.txt', $response);
        }
        $response .= '<div class="clear"></div>';
        
        //Display the comments history
        $response .= '<h3>Comments</h3>';
        $response .= '<p class="comments-section">'.$comments.'</p>';
        
        
        if(0 < $configuration && $configuration < 7) {
            $response .= '<textarea name="commenttext" rows="5" cols="40" style="width: 100%;"></textarea>';
            $response .= '<div class="clear"></div>';
            $response .= '<input type="hidden" id="count" name="count" value="'.$count.'">';
            $response .= '<input type="hidden" name="wfid" value="'.$id.'">';
            $response .= '<input type="hidden" name="sbid" value="'.$submissionID.'">';
            $response .= '<input type="hidden" id="ns" name="ns" value="0">';
            if($configuration < 4) {
                $response .= '<button type="button" class="savebutton" onclick="saveSubmission(';
                if($configuration == 3)
                    $response .= '3';
                else
                    $response .= '2';
                $response .= ');">Save Form</button>';
                $response .= '<button type="button" class="deletebutton" onclick="saveSubmission(';
                if($configuration == 3) 
                    $response .= '8';
                else
                    $response .= '10';
                $response .= ');">Delete Form</button>';
                $response .= '<button type="button" class="submitbutton" onclick="saveSubmission(4);">Submit Form</button>';
            } else if($configuration == 4) {
                if($hasAnotherApproval) {
                    $response .= '<button type="button" class="submitbutton" onclick="saveSubmission(4);">Approve Submission</button>';
                } else {
                    $response .= '<button type="button" class="submitbutton" onclick="saveSubmission(7);">Approve Submission</button>';
                }
                $response .= '<button type="button" class="deletebutton" onclick="saveSubmission(3);">Deny Submission</button>';
                $response .= '<button type="button" class="deletebutton" onclick="saveSubmission(8);">Deny Submission Permanently</button>';
            }
            
            //$response .= '<input type="submit" value="Submit" onclick="saveSubmission(3); onsubmit="">';
            $response .= '</form>';
        }
        $response .= '<div class="clear"></div>';
        
        //return 'DEBUG: You attempted to load ID: '.$id.' : <br>'.$response;
        return $response;
    }
    
    
    private function findValue($resultset, $valuetofind) {
        for($i = 0; $i < count($resultset); $i++) {
            if($valuetofind == $resultset[$i][0]) {
                return $resultset[$i][1];
            }
        }
        
        return '';
    }
    
    
    
    /**
    Debugs a workflow. 
    */
    public function debugloadWorkflowID($id) {
        global $wpdb;
        $response = '';
        
        $sql = "SELECT *
                FROM workflowform
                WHERE FORMID = '$id'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $response .= $row['NAME'].'<br>';
        }
        
        
        $sql = "SELECT *
                FROM workflowformdetails
                WHERE FORMID = '$id'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $response .= $row['FIELDID'].' | '.Workflow::translateFieldType($row['TYPE']).' | '.$row['LABEL'].' | '.
                $row['EDITABLE'].' | '.$row['APPROVAL_ONLY'].'<br>';
        }
        
        return 'You attempted to load ID: '.$id.' : '.$response;
    }
    
    /**
    View all workflows.
    */
    function viewAllWorkflows() {
        global $wpdb;
        
        if($_SESSION['activeuser'] == 0)
            die('<br>You need to log in.');
        
        $response = '';
        
        $sql = "SELECT *
                FROM workflowform";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            /*$response .= '<b>'.$row['NAME'].'</b> Link:<a href="?page=startworkflow&wfid='.$row['FORMID'].
                '">localhost/testing/workflow/startworkflow.php?wfid='.$row['FORMID'].'</a> ::: <a href="?page=debugstartworkflow&wfid='.$row['FORMID'].
                '">DEBUG</a> ::: <a href="?page=workflowentry&wfid='.$row['FORMID'].'">Create New Entry</a><br>';*/
            $response .= '<b>'.$row['NAME'].'</b> Link: <a href="?page=workflowentry&wfid='.$row['FORMID'].'">/forms-information/workflow/?page=workflowentry&wfid='.$row['FORMID'].'</a><br>';
        }
        
        return $response;
    }
    
    function viewAllSubmissions($userid) {
        global $wpdb;
        $response = '';
        
        if($userid == '' || $userid == '0') {
            return 'You need to be logged in to view submissions.';
        }
        
        $sql = "SELECT *
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID
                WHERE workflowformstatus.USER = '$userid'
                ORDER BY workflowformstatus.STATUS, workflowformstatus.DATE_SUBMITTED";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $tableHeader = '<tr><th>ID</th><th>Form Name</th><th>Status</th><th>Last Date Modified</th></tr>';
        
        $response .= '<table id="view-submissions">';//.$tableHeader;
        
        $prevState = 1;
        foreach($result as $row) {
            if($row['STATUS'] != $prevState) {
                if($row['STATUS'] == 2) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers approved">Saved Forms</div></td></tr>'.$tableHeader;
                    $prevState = 2;
                } else if($row['STATUS'] == 3) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers denied">Denied Forms</div></td></tr>'.$tableHeader;
                    $prevState = 3;
                } else if($row['STATUS'] == 4) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers reviewing">In Review</div></td></tr>'.$tableHeader;
                    $prevState = 4;
                } else if($row['STATUS'] == 7) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers approved">Approved Forms</div></td></tr>'.$tableHeader;
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers denied">Denied Forms Final</div></td></tr>'.$tableHeader;
                    $prevState = 8;
                } else if($row['STATUS'] == 10) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers denied">Cancelled</div></td></tr>'.$tableHeader;
                    $prevState = 10;
                }
                
            }
            
            $response .= '<tr>';
            $response .= '<td>'.$row['SUBMISSIONID'].'</td><td><a style="display:block;" href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">'.$row['NAME'].'</a></td><td style="text-align:center;">'.$row['STATUS'].'</td><td>'.$row['DATE_SUBMITTED'].'</td>';
            $response .= '</tr>';
        }
        $response .= '</table><div style="margin-bottom:50px;"></div>';
        
        return $response;
    }
    
    function viewAllSubmissionsAsApprover($userid) {
        global $wpdb;
        $response = '';
        
        if($userid == '' || $userid == '0') {
            return 'You need to be logged in to view submissions.';
        }
        
        $sql = "SELECT  workflowform.NAME, 
                        workflowform.APPROVER_ROLE, 
                        workflowform.APPROVER_ROLE2, 
                        workflowform.APPROVER_ROLE3, 
                        workflowform.APPROVER_ROLE4, 
                        workflowformstatus.SUBMISSIONID, 
                        workflowformstatus.STATUS,
                        workflowformstatus.STATUS_APPROVAL,
                        workflowformstatus.DATE_SUBMITTED,
                        CONCAT(employee.first_name, ' ', employee.last_name) AS USERNAME
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID 
                LEFT JOIN employee ON workflowformstatus.USER = employee.employee_number
                WHERE ( 
                    ( (workflowform.APPROVER_ROLE = '8' && (STATUS_APPROVAL = '1' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE2 = '8' && (STATUS_APPROVAL = '2' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE3 = '8' && (STATUS_APPROVAL = '3' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE4 = '8' && (STATUS_APPROVAL = '4' OR STATUS_APPROVAL = '100'))
                    AND (workflowformstatus.APPROVER_DIRECT = '$userid' OR employee.supervisor = '$userid') ) ";
                
                
                //WHERE ('0' = '1' ";//((workflowform.APPROVER_ROLE = '8' AND (workflowformstatus.APPROVER_DIRECT = '$userid' 
                //    //OR employee.supervisor = '$userid')) ";
        $roles = Workflow::getRole($userid);
        
        
        for($x = 1; $x <= 4; $x++) {
            $sql .= "OR (STATUS_APPROVAL = '".$x."' OR STATUS_APPROVAL = '100') AND (";
            for($i = 0; $i < count($roles); $i++) {
                if($i != 0)
                    $sql .= "OR ";
                $sql .= "workflowform.APPROVER_ROLE";
                if($x != 1)
                    $sql .= $x;
                $sql .= " = '$roles[$i]' ";
            }
            $sql .= ") ";
        
        }
        
        //$sql .= "OR STATUS_APPROVAL = '100' AND (";
        
        
        $sql .= ") AND (workflowformstatus.STATUS != '2' 
                    AND workflowformstatus.STATUS != '3' 
                    AND workflowformstatus.STATUS != '10') ";
        
        //        WHERE workflowformstatus.USER = '$userid'
        $sql .= "ORDER BY workflowformstatus.STATUS, workflowformstatus.DATE_SUBMITTED";
        //die(htmlspecialchars($sql));
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $tableHeader = '<tr><th>ID</th><th>Form Name</th><th>Status</th><th>Submitted By</th><th>Last Date Modified</th></tr>';
        $response .= '<table id="view-submissions">';
        
        $prevState = 1;
        foreach($result as $row) {
            if($row['STATUS'] != $prevState) {
                if($row['STATUS'] == 2) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers approved">Saved Forms</div></td></tr>'.$tableHeader;
                    $prevState = 2;
                } else if($row['STATUS'] == 3) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers denied">Denied Forms</div></td></tr>'.$tableHeader;
                    $prevState = 3;
                } else if($row['STATUS'] == 4) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers reviewing">In Review</div></td></tr>'.$tableHeader;
                    $prevState = 4;
                } else if($row['STATUS'] == 7) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers approved">Approved Forms</div></td></tr>'.$tableHeader;
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers denied">Denied Forms Final</div></td></tr>'.$tableHeader;
                    $prevState = 8;
                } else if($row['STATUS'] == 10) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers denied">Cancelled</div></td></tr>'.$tableHeader;
                    $prevState = 10;
                }
                
            }
            
            $response .= '<tr>';
            $response .= '<td>'.$row['SUBMISSIONID'].'</td><td><a style="display:block;" href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">'.$row['NAME'].'</a></td><td style="text-align:center;">'.$row['STATUS'].'</td><td>'.$row['USERNAME'].'</td><td>'.$row['DATE_SUBMITTED'].'</td>';
            $response .= '</tr>';
        }
        $response .= '</table><div style="margin-bottom:50px;"></div>';
        
        return $response;
    }
    
    public function __toString() {
          return $this->$name;
    }
    
    public function getForm() {
        $content = '<div><h2>This is a test.</h2></div>';
        
        return $content;
    }
    
    
    public static function logInUser() {
        global $wpdb;
        $empid = 0;
        $current = wp_get_current_user()->id;
        
        $sql = "SELECT employee.employee_number,
                        CONCAT(first_name, ' ', last_name) AS name
                FROM employee 
                INNER JOIN wp_users ON employee.user_login = wp_users.user_login
                WHERE wp_users.ID = '$current'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $row = $result[0];
            $empid = $_SESSION['activeuser'] = $row['employee_number'];
            $_SESSION['activeusername'] = $row['name'];
        }
        
        return $empid;
        
    }
    
    /*DEBUG FUNCTION*/
    public static function actualloggedInUser() {
        global $wpdb;
        $empid = 0;
        $current = wp_get_current_user()->id;
        
        $sql = "SELECT employee.employee_number FROM employee 
                INNER JOIN wp_users ON employee.user_login = wp_users.user_login
                WHERE wp_users.ID = '$current'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $row = $result[0];
            $empid = $row['employee_number'];
        } 
        return $empid;
    }
    
    public static function loggedInUser() {
        if(isset($_SESSION['activeuser']) && $_SESSION['activeuser'] != '') {
            return $_SESSION['activeuser'];
        } else {
            return '0';
        }
    }
    
    public static function loggedInUserName() {
        if(isset($_SESSION['activeusername']) && $_SESSION['activeusername'] != '') {
            return $_SESSION['activeusername'];
        } else {
            return '0';
        }
    }
    
    public static function hasRoleAccess($user, $roleSearch) {
        $roles = Workflow::getRole($user);
        for($i = 0; $i < count($roles); $i++) {
            if($roles[$i] == $roleSearch)
                return 1;
        }
        
        return 0;
    }
    
    public static function isAdmin($user) {
        return Workflow::hasRoleAccess($user, 5);
    }
    
    public static function getRole($user) {
        global $wpdb;
        $roleid[] = '0';
        
        $sql = "SELECT ROLEID
                FROM workflowrolesmembers
                WHERE MEMBER = '$user'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $roleid[] = $row['ROLEID'];
        }
        
        return $roleid;
    }
    
    public function debugDisplayWorkflow() {
        $response = '';
        for($i = 0; $i < count($this->fields); $i++) {
            //$response .= $this->fields[$i][0].' | '.$this->fields[$i][1].' | '.$this->fields[$i][2].' | '.$this->fields[$i][3].'<br>';
            for($x = 0; $x < count($this->fields[$i]); $x++) {
                $response .= $this->fields[$i][$x].' | ';
            }
            $response .= '<br>';
        }
        return $response;
    }
    
    
    public static function translateFieldType($type) {
        if($type == 0) {
            return 'Textbox';
        } else if($type == 1) {
            return 'Label';
        } else if($type == 2) {
            return 'Option';
        } else if($type == 3) {
            return 'Newline';
        } else {
            return '---';
        }
    }
    
    public function storeRole($name) {
        global $wpdb;
        $sql = "INSERT INTO workflowroles (NAME)
                VALUES ('$name')";
        $result = $wpdb->query($sql, ARRAY_A);
        return $result;
    }
    
    public function storeMember($roleid, $member) {
        global $wpdb;
        $sql = "INSERT INTO workflowrolesmembers (ROLEID, MEMBER)
                VALUES ('$roleid', '$member')";
        $result = $wpdb->query($sql, ARRAY_A);
        return $result;
    }
    
    public function getRoles() {
        global $wpdb;
        $values = array();
        
        $sql = "SELECT *
                FROM workflowroles
                ORDER BY NAME ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $values[] = array($row['ROLEID'], $row['NAME']);
        }
        
        return $values;
    }
    
    public function getMemberRoles() {
        global $wpdb;
        $values = array();
        
        $sql = "SELECT *
                FROM workflowrolesmembers
                INNER JOIN workflowroles ON workflowrolesmembers.ROLEID = workflowroles.ROLEID
                ORDER BY workflowrolesmembers.ROLEID ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $values[] = array($row['ID'], $row['MEMBER'], $row['ROLEID'], $row['NAME']);
        }
        
        return $values;
    }
    
    public static function getUserName($userid) {
        global $wpdb;
        $name = '';
            
        $sql = "SELECT CONCAT(first_name, ' ', last_name) AS name
                FROM employee
                WHERE employee_number = '$userid'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $name = $result[0]['name'];
        }
        
        return $name;
    }
    
    public static function getDirectApprover($userid) {
        global $wpdb;
        $approver = 0;
        
        $sql = "SELECT supervisor
                FROM employee
                WHERE employee_number = '$userid'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $approver = $result[0]['supervisor'];
        }
        
        return $approver;
    }
    
}
    
    
    
?>