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
    private $behalfof;
    private $fields; 
    
    
    public function __construct() {
    }
    
    public function __destruct() {
        
    }
    
    public function createWorkflow($name, $startAccess, $approver, $approver2, $approver3, $approver4, $behalfof) {
        $this->name = $name;
        $this->startAccess = $startAccess;
        $this->approvers = array($approver, $approver2, $approver3, $approver4);
        $this->behalfof = $behalfof;
        $this->fields = array();
    }
    
    public function addField($type, $label, $editable, $approvalonly, $approvalshow, $size, $level, $requiredfield) {
        if($size == '')
            $size = 0;
        $this->fields[] = array($type, $label, $editable, $approvalonly, $approvalshow, $size, $level, $requiredfield);
    }
    
    /**
        Stores the new workflow layout.
    */
    public function storeToDatabase() {
        global $wpdb;
        
        $sql = "INSERT INTO workflowform (NAME, APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, BEHALFOF_SHOW)
                VALUES ('$this->name', '".$this->approvers['0']."'";
        
        for($i = 1; $i < count($this->approvers); $i++) {
            if($this->approvers[$i] != -1) {
                $sql .= ", '".$this->approvers[$i]."'";
            } else {
                $sql .= ", NULL";
            }
        }
        
        $sql .= ", '".$this->behalfof."')";
        
        
        $result = $wpdb->query($sql, ARRAY_A);
        $inserted_id = $wpdb->insert_id;
        
        if(!$result) {
            header("location: ?page=index");
        } 
        
        //echo '<br>Inserted the ID: '.$inserted_id.'<br>';
        
        //Store fields into WorkflowDetails
        for($i = 0; $i < count($this->fields); $i++) {
            $sql = "INSERT INTO workflowformdetails (FORMID, FIELDID, POSITION, TYPE, LABEL, EDITABLE, APPROVAL_ONLY, 
                                                    APPROVAL_SHOW, REQUIRED, APPROVAL_LEVEL, FIELD_WIDTH)
                    VALUES ('$inserted_id', '$i', '$i', '".$this->fields[$i][0]."', '".$this->fields[$i][1]."', '".$this->fields[$i][2]."', 
                    '".$this->fields[$i][3]."', '".$this->fields[$i][4]."', '".$this->fields[$i][7]."', '".$this->fields[$i][6]."', ";
            
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
    public function updateWorkflowSubmissions($fields, $newstatus, $submissionID, $formID, $user, $misc_content, $commenttext, $behalfof) {
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
        $historyApprovalStage = 0;
        /*echo 'DISPLAYING THE RESULTS for WFID:'.$formID.'<br>';

        for($i = 0; $i < count($fields); $i++) {
            echo $fields[$i][0].'='.$fields[$i][1].'<br>';
            
        }
        echo 'done display<br>';*/
        
        
        //Check to see if an update or insert is allowed
        $sql = "SELECT STATUS, STATUS_APPROVAL, COMMENT, USER
                FROM workflowformstatus
                WHERE SUBMISSIONID = '$submissionID'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        
        if(count($result) != 0) {
            $row = $result[0];
            $oldstatus = $row['STATUS'];
            $oldApprovalStatus = $row['STATUS_APPROVAL'];
            $oldcomment = $row['COMMENT'];
            if($row['USER'] != $user) {
                $historyApprovalStage = $oldApprovalStatus;
            }
            //echo 'DEBUG: Old Status:'.$oldstatus.'<br>';
        }
        if($oldstatus == 4) {
            
        } else if($oldstatus >= 7) {
            die('This form can no longer be edited.');
        }
        
        $directApprover = Workflow::getDirectApprover($user);
        
        date_default_timezone_set('America/Los_Angeles');
        $newApprovalStatus = 1;
        
        if($submissionID == 0) {
            $misc_content = str_replace("\\", "\\\\", $misc_content);//$misc_content;//str_replace("'", "\'", $misc_content);
            $new_misc_content = str_replace("'", "\'", $misc_content);//$misc_content;//str_replace("'", "\'", $misc_content);
        
            //echo 'SUBMISSION ID : '.$submissionID.' not found. <br>';
            
            $sql = "INSERT INTO workflowformstatus (USER, STATUS, FORMID, MISC_CONTENT, DATE_SUBMITTED, COMMENT, BEHALFOF, APPROVER_DIRECT)
                    VALUES ('$user', '$newstatus', '$formID', '$new_misc_content', '".date('Y-m-d')."', ";
            
            if($commenttext != '') {
                $commenttext = str_replace("\\", "\\\\", $commenttext);
                $sql .= "'<b>[".Workflow::loggedInUserName()." on: ".date("Y-m-d H:i")."]</b><br>".str_replace("'", "\'", $commenttext)."', ";
            } else {
                $sql .= "NULL, ";
            }
            
            if($behalfof != 0) {
                $sql .= "'$behalfof', ";
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
        
        /*if(!$result) {
            die('Failed to update status.');
        }*/
        
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
        
        if($behalfof != '')
            $user = $behalfof;
        //Update history
        $sql = "INSERT INTO workflowformhistory (USER, SUBMISSION_ID, APPROVAL_LEVEL, ACTION, DATE_SUBMITTED)
                VALUES ('$user', '$submissionID', '$historyApprovalStage', '$newstatus', '".date('Y-m-d H:i:s')."')";
        
        $result = $wpdb->query($sql, ARRAY_A);
        
        
        
        
        
        return $submissionID;
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
                            APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, APPROVER_DIRECT, BEHALFOF
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
                $behalfof = $row['BEHALFOF'];
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
            $hasAnotherApproval = 1;
            $approvalStatus = 0;
        }
        
        
        //TODO Authorize the person that is trying to access this function or page.
        //echo 'DEBUG: Workflow status: '.$configvalue.'<br>';
        echo Workflow::loadWorkflowEntry($wfid, $configvalue, $sbid, $misc_content, $comments, $submittedby, 
            $status, $approvalStatus, $hasAnotherApproval, $behalfof, 0);
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
        $status, $approvalStatus, $hasAnotherApproval, $behalfof, $emailMode) {
        global $wpdb;
        $response = '';
        
        $sql = "SELECT NAME, BEHALFOF_SHOW
                FROM workflowform
                WHERE FORMID = '$id'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $workflowName = $result[0]['NAME'];
            $behalfofShow = $result[0]['BEHALFOF_SHOW'];
        } else {
            header('location: ?page=viewsubmissions');
            die();
        }
        
        $sql = "SELECT USER, ACTION
                FROM workflowformhistory
                WHERE SUBMISSION_ID = '$submissionID' AND !(USER = '$submittedby' AND (ACTION = '2' OR ACTION = '3'))
                ORDER BY DATE_SUBMITTED DESC
                LIMIT 1";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $lastAction = '';
        if(count($result) == 1) {
            $lastAction = Workflow::getUserName($result[0]['USER']);
        }
        
        $response .= '<h2>'.$workflowName.' <span style="font-size:16px;">Submitted by: ';
        
        if($behalfof != '') {
            $response .= Workflow::getUserName($behalfof).' on behalf of ';
        }
        
        $response .= Workflow::getUserName($submittedby).
                    '&nbsp;&nbsp;&nbsp;Last Change by: '.$lastAction.'</span></h2>';
        
        
        if($configuration == 0 || $configuration == 4) {
            if(!$emailMode)
                $response .= '<p class="reviewing">This form is currently under review.</p>';
            else
                $response .= '<p style="font-size: 24px;background-color: #51abff;text-align: center;margin: 0;">
                    This form is currently under review.</p>';
        } else if($configuration == 7 || ($configuration == 9 && $status == 7)) {
            if(!$emailMode)
                $response .= '<p class="approved">This form was approved.</p>';
            else
                $response .= '<p style="font-size: 24px;background-color: #8fff89;text-align: center;margin: 0;">
                    This form was approved.</p>';
        } else if($configuration == 8 || ($configuration == 9 && $status == 8)) {
            if(!$emailMode)
                $response .= '<p class="denied">This form was denied.</p>';
            else
                $response .= '<p style="font-size: 24px;background-color: #ff8989;text-align: center;margin: 0;">
                    This form was denied.</p>';
        } else if($configuration == 10) {
            $response .= '<p class="denied">This form was cancelled.</p>';
        }
        $response .='<hr>';
        
        if(0 <= $configuration && $configuration < 7 && !$emailMode)
            $response .= '<form id="workflowsubmission" action="?page=process_workflow_submit" method="POST" autocomplete="off" >';
        
        //Display the misc content
        if($misc_content != '') {
            $response .= $misc_content.'<br>';
            if(!$emailMode)
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
        
        if($behalfofShow && $configuration == 1) {//on behalf of someone else
            $response .= '<div class="workflow workflowlabel">Submit on behalf of Employee Number:</div>';
            $response .= '<div class="workflow workflowright style-1" style="width:150px;"><input type="text" id="onbehalf" name="onbehalf"
                         placeholder="Emp Num"></div>';
            $response .= '<div class="clear" style="height: 50px;"></div>';
        }
        
        //For each field that is part of the form
        $count = 0;
        foreach($result as $row) {
            $count++;
            $fieldvalue = '';
            if($configuration != 1) {
                //echo 'USING '.Workflow::findValue($prevSubmissions, $row['FIELDID']).'<br>';
                $fieldvalue = Workflow::findValue($prevSubmissions, $row['FIELDID']);
            }
            
            //Determines whether the field will be an approval field and if it is an editable field.
            $editableField = ($configuration != 4  
                || ((($row['APPROVAL_ONLY'] == 1 && ($row['APPROVAL_LEVEL'] == $approvalStatus) || $row['APPROVAL_LEVEL'] == 0)
                || $row['EDITABLE'] == 1) && $configuration == 4));
            
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
                    $response .= 'style="width:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= 'style="';
                
                if($emailMode) {
                    $response .= ' float: left; margin-right:10px;font-weight:bold;';
                }
                
                $response .= '">'.$row['LABEL'].'</div>';
                
            } else if($row['TYPE'] == 0) { //Textbox
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval"';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1"';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= ' style="';
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
                if($editableField) {
                    $response .= '<input type="text" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" placeholder="'.$row['LABEL'].'" value="'.$fieldvalue.'" ';
                    if($row['REQUIRED'])
                        $response .= 'required';
                    if($emailMode)
                        $response .= ' disabled';
                    $response .= '>';
                } else {
                    $response .= $fieldvalue;
                }
                $response .= '</div>';
                
                    
                    
                    
                    
                    
            } else if($row['TYPE'] == 2) { //Option
                $response .= ' ';
            } else if($row['TYPE'] == 3) { //Newline
                $response .= '<div class="clear" ';
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="height:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= ' style="';
                    
                if($emailMode) {
                    $response .= 'clear:both;';
                } 
                $response .= '"></div>';
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
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= ' style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
                if($editableField) {
                    $response .= '<input type="hidden" name="workflowfieldid'.$row['FIELDID'].'" value="0">';
                }
                
                $response .= '<input type="checkbox" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                    $row['FIELDID'].'" value="1" ';
                
                if(!$editableField || $emailMode) {
                    $response .= 'disabled ';
                }
                if($fieldvalue)
                    $response .= 'checked';
                $response .='>'.$row['LABEL'].'</div>';
                
            } else if($row['TYPE'] == 5) { //Autofill Name
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval"';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1"';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= ' style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
                if($editableField) { //TODO: check approval level
                    $response .= '<input type="text" placeholder="'.$row['LABEL'].'" value="';
                    if($emailMode)
                        $response .= '%EMAILNAME%';
                    else 
                        $response .= Workflow::loggedInUserName();
                    $response .= '" disabled>';
                    $response .= '<input type="hidden" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" value="';
                    if($emailMode)
                        $response .= '%EMAILNAME%';
                    else 
                        $response .= Workflow::loggedInUserName();
                    $response .= '">';
                } else {
                    $response .= $fieldvalue;
                }
                $response .= '</div>';
            } else if($row['TYPE'] == 6) { //Autofill Date
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval"';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1"';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= ' style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
                if($editableField) { //TODO: check approval level
                    $response .= '<input type="date" value="'.date('Y-m-d').'" disabled>';
                    $response .= '<input type="hidden" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" value="'.date('Y-m-d').'">';
                } else {
                    $response .= date("m/d/Y", strtotime($fieldvalue));
                }
                $response .= '</div>';
            } else if($row['TYPE'] == 7) { //Date
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval"';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1"';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= ' style="width:'.$row['FIELD_WIDTH'].'px;';
                } else 
                    $response .= ' style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
                if($editableField) {
                    $response .= '<input type="date" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" placeholder="mm/dd/yyyy" value="'.date("Y-m-d", strtotime($fieldvalue)).'"';
                    if($emailMode) {
                        $response .= ' disabled';
                    }
                    $response .= '>';
                } else {
                    $response .= date("m/d/Y", strtotime($fieldvalue));
                }
                $response .= '</div>';
            }
        }
        $response .= '<div class="clear"></div>';
        if($emailMode) {
            $response .= '<div style="clear:both;"></div>';
        }
        
        //Display the comments history
        $response .= '<h3>Comments</h3>';
        $response .= '<p class="comments-section">'.$comments.'</p>';
        
        
        if(0 <= $configuration && $configuration < 7 && !$emailMode) {
            if($configuration != 0) {
                $response .= '<textarea name="commenttext" rows="5" cols="40" style="width: 100%;"></textarea>';
            }
            
            $response .= '<div class="clear"></div>';
            if($configuration == 0 || $configuration == 2 || $configuration == 3) {
                $submittingStatus = $approvalStatus - 1;
                $submittingApproval = 1;
            } else {
                $submittingStatus = $approvalStatus;
                $submittingApproval = $hasAnotherApproval;
            }
            $response .= '<h3>Submitting to: '.Workflow::getNextRoleName($submittingStatus, $submittingApproval, $id).'</h3>';
            
            $response .= '<input type="hidden" id="count" name="count" value="'.$count.'">';
            $response .= '<input type="hidden" name="wfid" value="'.$id.'">';
            $response .= '<input type="hidden" name="sbid" value="'.$submissionID.'">';
            $response .= '<input type="hidden" id="ns" name="ns" value="0">';
            if( 0 < $configuration && $configuration < 4) {
                $response .= '<button type="button" class="savebutton" onclick="saveSubmission(';
                if($configuration == 3)
                    $response .= '3, 0';
                else
                    $response .= '2, 0';
                $response .= ');">Save Form</button>';
                $response .= '<button type="button" class="deletebutton" onclick="saveSubmission(';
                if($configuration == 3) 
                    $response .= '8, 0';
                else
                    $response .= '10, 0';
                $response .= ');">Delete Form</button>';
                $response .= '<button type="button" class="submitbutton" onclick="saveSubmission(4, 0);">Submit Form</button>';
            } else if($configuration == 4) {
                if($hasAnotherApproval) {
                    $response .= '<button type="button" id="approvelink" class="submitbutton" onclick="saveSubmission(4, 1);">Approve</button>';
                } else {
                    $response .= '<button type="button" id="approvelink" class="submitbutton" onclick="saveSubmission(7, 1);">Approve</button>';
                }
                $response .= '<button type="button" id="changelink" class="deletebutton" onclick="saveSubmission(3, 1);">Request Change</button>';
                $response .= '<button type="button" id="denylink" class="deletebutton" onclick="saveSubmission(8, 1);">Not Approved</button>';
            } else if($configuration == 0) {
                $response .= '<button type="button" id="retractlink" class="deletebutton" onclick="saveSubmission(3, 0);">Retract Submission</button>';
            }
            
            //$response .= '<input type="submit" value="Submit" onclick="saveSubmission(3); onsubmit="">';
            $response .= '<input type="submit" value="Submit" id="formsubmitbutton" style="display: none;"></form>';
        } else if(0 <= $configuration && $configuration < 7 && $emailMode) {
            $response .= '<div class="clear"></div>';
            if($configuration == 0 || $configuration == 2 || $configuration == 3) {
                $submittingStatus = $approvalStatus - 1;
                $submittingApproval = 1;
            } else {
                $submittingStatus = $approvalStatus;
                $submittingApproval = $hasAnotherApproval;
            }
            $response .= '<h3>Submitting to: '.Workflow::getNextRoleName($submittingStatus, $submittingApproval, $id).'</h3>';
            
            if( 0 < $configuration && $configuration < 4) {
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=save&lvl='.$approvalStatus.'"><button type="button">Save Form</button></a>';//http://local.theloop.com
                
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=cancel&lvl='.$approvalStatus.'"><button type="button" style="background-color: #ff8989;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Delete Form</button></a>';
                
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=submit&lvl='.$approvalStatus.'"><button type="button" style="background-color: #51abff;box-shadow: 0 0 5px 1px #969696; 
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Submit Form</button></a>';
            } else if($configuration == 4) {
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=approve&lvl='.$approvalStatus.'">
                    <button type="button" style="background-color: #51abff;box-shadow: 0 0 5px 1px #969696; 
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Approve</button></a>';
                
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=change&lvl='.$approvalStatus.'"><button type="button" style="background-color: #ff8989;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Request Change</button></a>';
                
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=deny&lvl='.$approvalStatus.'"><button type="button" style="background-color: #ff8989;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Not Approved</button></a>';
            } else if($configuration == 0) {
                $response .= '<a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=retract&lvl='.$approvalStatus.'"><button type="button" style="background-color: #ff8989;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Retract Submission</button></a>';
            }
        } else if(!$emailMode) {
            //Display approval history
            
            $sql = "SELECT *
                    FROM workflowformhistory
                    WHERE SUBMISSION_ID = '$submissionID'
                    ORDER BY DATE_SUBMITTED ASC";
        
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            $response .= '<table id="workflowhistory"><tr><td colspan=3><h3>Approval History</h3></td></tr>';
            $response .= '<tr><th>USER</th><th>ACTION</th><th>DATE</th></tr>';
            foreach($result as $row) {
                $response .= '<tr><td>'.Workflow::getUserName($row['USER']).'</td><td>';
                
                if($row['ACTION'] == 2) {
                    $response .= 'Saved';
                } else if($row['ACTION'] == 3 && $row['USER'] == $submittedby) {
                    $response .= 'Saved';
                } else if($row['ACTION'] == 3) {
                    $response .= 'Review Required Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 4 && $row['USER'] == $submittedby) {
                    $response .= 'Submitted';
                } else if($row['ACTION'] == 4) {
                    $response .= 'Approved Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 7) {
                    $response .= 'Approved Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 8 && $row['USER'] == $submittedby) {
                    $response .= 'Cancelled by Submitter';
                } else if($row['ACTION'] == 8) {
                    $response .= 'Denied Lvl: '.$row['APPROVAL_LEVEL'];
                }
                
                $response .= '</td>';
                $response .= '<td>'.$row['DATE_SUBMITTED'].'</td></tr>';
                
            }
            
            $response .= '</table>';
        }
        $response .= '<div class="clear"></div>';
        
        
        if(isset($_GET['response']) && isset($_GET['lvl']) && $configuration == 4) {
            echo '<br>DEBUG: You have chosen the command: '.$_GET['response'];
            if($_GET['response'] == 'approve' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {document.getElementById("approvelink").click();};</script>';
            else if($_GET['response'] == 'change' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {document.getElementById("changelink").click();};</script>';
            else if($_GET['response'] == 'deny' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {document.getElementById("denylink").click();};</script>';
        }
        
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
    public function viewAllWorkflows() {
        global $wpdb;
        
        if($_SESSION['activeuser'] == 0) {
            $_SESSION['ERRMSG'] = 'You need to log in first.';
            header('location: ?page=viewsubmissions');
            die();
        }
        
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
    
    public function viewAllSubmissions($userid) {
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
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers denied">Forms Needing Input</div></td></tr>'.$tableHeader;
                    $prevState = 3;
                } else if($row['STATUS'] == 4) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers reviewing">Currently Under Review</div></td></tr>'.$tableHeader;
                    $prevState = 4;
                } else if($row['STATUS'] == 7) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers approved">Approved Forms</div></td></tr>'.$tableHeader;
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr><td colspan=4><div class="view-submissions-headers denied">Denied Forms</div></td></tr>'.$tableHeader;
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
    
    public function viewAllSubmissionsAsApprover($userid) {
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
                    ( (workflowform.APPROVER_ROLE = '8' AND (STATUS_APPROVAL = '1' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE2 = '8' AND (STATUS_APPROVAL = '2' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE3 = '8' AND (STATUS_APPROVAL = '3' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE4 = '8' AND (STATUS_APPROVAL = '4' OR STATUS_APPROVAL = '100'))
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
        $sql .= "ORDER BY workflowformstatus.STATUS, workflowformstatus.DATE_SUBMITTED, workflowform.NAME";
        
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
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers reviewing">Submissions Requiring Approval</div></td></tr>'.$tableHeader;
                    $prevState = 4;
                } else if($row['STATUS'] == 7) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers approved">Approved Forms</div></td></tr>'.$tableHeader;
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr><td colspan=5><div class="view-submissions-headers denied">Denied Forms</div></td></tr>'.$tableHeader;
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
    
    public function sendEmail($submissionID) {
        require_once("phpmailer/vendor/autoload.php");
        /*$headers = "From: hr@powertochange.org";
        $subject = "Test";
        $emailMessage = 
        "This is a message that will be sent to your email when you have submitted something.

        I just want to see the formatting right now.

        It seems to auto break the text.

        Thank you MR. ".Workflow::getUserName($loggedInUser);

        //mail('gerald.becker@p2c.com', $subject, $emailMessage, $headers);*/

        
        
        $workflow = new Workflow();
        
        
        
        
        global $wpdb;
        $response = '';
        
        $sql = "SELECT STATUS, APPROVER_DIRECT, USER, workflowformstatus.FORMID, COMMENT, MISC_CONTENT, workflowform.NAME,
                APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, STATUS_APPROVAL
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID
                WHERE SUBMISSIONID = '$submissionID'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        $sql1 = $sql;
        if(count($result) == 1) {
            $row = $result[0];
        } else
            return;
        
        $status = $row['STATUS'];
        $approvalStatus = $row['STATUS_APPROVAL'];
        $directApprover = $row['APPROVER_DIRECT'];
        $userid = $row['USER'];
        $formID = $row['FORMID'];
        $commenttext = $row['COMMENT'];
        $misc_content = $row['MISC_CONTENT'];
        $formName = $row['NAME'];
        $approvers = array($row['APPROVER_ROLE'], $row['APPROVER_ROLE2'], $row['APPROVER_ROLE3'], $row['APPROVER_ROLE4']);
        
        if(!($status == 4 || $status == 7 || $status == 8))
            return;
        
        //Find out if it is a direct supervisor submission or not
        $role = $approvers[$approvalStatus - 1];
        
        if($role != 8 && $role != '') {
            $sql = "SELECT MEMBER, employee.user_login, user_email, EMAIL_ON
                    FROM workflowrolesmembers
                    INNER JOIN employee ON employee.employee_number = workflowrolesmembers.MEMBER
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login
                    WHERE ROLEID = '$role'
                    ORDER BY MEMBER";
            
        } else if($role != '') {
            $sql = "SELECT employee.employee_number AS MEMBER, employee.user_login, user_email, '1' AS EMAIL_ON
                    FROM employee  
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login 
                    WHERE employee.employee_number = '$directApprover' 
                    ORDER BY MEMBER";
        } else if($approvalStatus == 100) {
            $sql = "SELECT employee.employee_number AS MEMBER, employee.user_login, user_email, '1' AS EMAIL_ON
                    FROM employee  
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login 
                    WHERE employee.employee_number = '$userid'";
            
        } else 
            return;
        
        $emailRecepients = $wpdb->get_results($sql, ARRAY_A);
        
        $recepients = array();
        $tempRec = '';
        foreach($emailRecepients as $row) {
            if($row['user_email'] != '') {
                $tempRec .= $row['user_email'].' SEND EMAIL: '.$row['EMAIL_ON'].'<br>';
                $recepients[] = array($row['MEMBER'], $row['user_email'], $row['EMAIL_ON']);
            }
        }
        
        if($status == 4) {
        $template = '
            <body style="font-family: sans-serif; color:black;">
                <h2>You have an approval waiting for a response!</h2>
                <p><b>'.Workflow::getUserName($userid).'</b> has submitted the form <b>'.$formName.'</b></p>
                <p>To approve or deny this form, visit this link: 
                <a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'">Submission '.$submissionID.'</a></p>
            <h3>Email List (Members in this role)</h3> '.$tempRec.'<br>'.$workflow->loadWorkflowEntry($formID, 4, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, 1, 0, 1).
            '<br></body>';
        } else {
            $templateFinished = '
            <body style="font-family: sans-serif; color:black;">
                <h2>You have a form that has been reviewed!</h2>
                <p><b>Form: <b>'.$formName.'</b></p>
                <p>To view this form, visit this link: 
                <a href="https://devstaff.powertochange.org/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'">Submission '.$submissionID.'</a></p>
            <h3>Email List</h3> '.$tempRec.'<br>'.$workflow->loadWorkflowEntry($formID, $status, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, 0, 0, 1).
            '<br></body>';
        }
        
        
        
        for($i = 0; $i < count($recepients); $i++) {
            if($recepients[$i][2] == 1) { //if sending of emails is checked in the email settings
                if($status == 4)
                    $body = str_replace('%EMAILNAME%', Workflow::getUserName($recepients[$i][0]), $template);
                else 
                    $body = $templateFinished;
                $body .= '<div style="clear:both;"></div><br>Your email address is: '.$recepients[$i][1];
            
                //$body .= '<br>'.htmlspecialchars($sql1).'<br>'.htmlspecialchars($sql).'<br>';
                
                
                $mail = new PHPMailer;
                $mail->isSMTP();          // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                            // Enable SMTP authentication
                $mail->SMTPDebug = 2;
                $mail->Username = 'gerald.becker@p2c.com';                 // SMTP username
                // SMTP username
                $mail->Password = 'Ever42green$';                           // SMTP password
                $mail->SMTPSecure = 'ssl';       // Enable TLS encryption, ssl also accepted
                $mail->Port = 465;//587

                $mail->From = 'gerald.becker@p2c.com';
                $mail->FromName = 'Workflow Email';
                $mail->AddAddress('gerald.becker@p2c.com'); //TODO: multiple emails gerald.becker@p2c.com

                $mail->IsHTML(true);

                $mail->Subject = 'Workflow email from '.Workflow::getUserName($userid);
                            
                
                                        
                $mail->Body = $body;
                echo 'DEBUG: Trying to send an email to : '.$recepients[$i][1].'<br>';
                $mail->Send();
            }
        }
    }
    
    
    
    public function __toString() {
        return $this->$name;
    }
    
    public function getForm() {
        //$content = '<div><h2>This is a test.</h2></div>';
        
        //return $content;
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
    
    public function removeMember($roleid, $member) {
        global $wpdb;
        $sql = "DELETE FROM workflowrolesmembers 
                WHERE (ROLEID, MEMBER) = ('$roleid', '$member')";
        $result = $wpdb->query($sql, ARRAY_A);
        return $result;
    }
    
    public function updateMemberEmail($roleid, $member, $sendEmail) {
        global $wpdb;
        $sql = "UPDATE workflowrolesmembers 
                SET EMAIL_ON = '$sendEmail'
                WHERE (ROLEID, MEMBER) = ('$roleid', '$member')";
        
        $wpdb->query($sql, ARRAY_A);
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
        
        $sql = "SELECT MEMBER, workflowrolesmembers.ROLEID, NAME, CONCAT(first_name, ' ', last_name) AS FULLNAME, EMAIL_ON
                FROM workflowrolesmembers
                INNER JOIN workflowroles ON workflowrolesmembers.ROLEID = workflowroles.ROLEID
                LEFT OUTER JOIN employee ON employee.employee_number = workflowrolesmembers.MEMBER
                ORDER BY workflowroles.NAME ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $values[] = array('ROLE'.$row['ROLEID'].'USER'.$row['MEMBER'], $row['MEMBER'], $row['FULLNAME'], $row['NAME'], 
                $row['EMAIL_ON'], $row['ROLEID']);
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
    
    public function getNextRoleName($currentLevel, $hasAnotherApproval, $formID) {
        //echo 'DEBUG: has approval: '.$hasAnotherApproval.' level:'.$currentLevel. ' form:'.$formID;
        if(!$hasAnotherApproval)
            return 'Form Complete.';
        global $wpdb;
        $response = '';
        
        $sql = "SELECT workflowroles.NAME
                FROM workflowform ";
        
        if($currentLevel == 0) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE = workflowroles.ROLEID ";
        } else if($currentLevel == 1) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE2 = workflowroles.ROLEID ";
        } else if($currentLevel == 2) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE3 = workflowroles.ROLEID ";
        } else if($currentLevel == 3) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE4 = workflowroles.ROLEID ";
        } 
        $sql .= "WHERE FORMID = '$formID'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $response = $result[0]['NAME'];
        }
        //die(htmlspecialchars($sql));
        return $response;
    }
    
}
    
    
    
?>