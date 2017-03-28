<?php
/*
*The main workflow class.
*
*
*THINGS THAT SHOULD BE UPDATED BEFORE RELEASE:
*
*
* author: gerald.becker
*
*/


class Workflow {
    private $name;
    private $startAccess;
    private $approvers;
    private $processor;
    private $behalfof;
    private $fields; 
    private $draft;
    private $savedData;
    private $numFields;
    private $mode;
    private $previousID;
    private static $currentUserEmployeeNum;  // Store the current user's employee number for easy access later
    private $uniqueToken;
    private $linkAddress;
    private $allowanceCalculatorID = 133; //This needs to match the form ID in the database for the allowance calculator
    
    public function __construct() {
        $this->linkAddress = get_home_url( '/');
    }
    
    public function __destruct() {
        
    }
    
    public function createWorkflow($name, $startAccess, $approver, $approver2, $approver3, $approver4, $processor, 
                                    $behalfof, $draft, $savedData, $numFields, $mode, $previousID) {
        $this->name = Workflow::escapeScriptTags($name);
        $this->startAccess = $startAccess;
        $this->approvers = array($approver, $approver2, $approver3, $approver4);
        $this->processor = $processor;
        $this->behalfof = $behalfof;
        $this->fields = array();
        
        $this->draft = $draft;
        $this->savedData = str_replace("'", "\'", str_replace("\\", "\\\\", $savedData));;
        $this->numFields = $numFields;
        $this->mode = $mode;
        $this->previousID = $previousID;
        
    }
    
    public function addField($type, $label, $editable, $approvalonly, $approvalshow, $size, $level, $requiredfield, $newgroup) {
        $label = Workflow::escapeScriptTags($label);
        if($size == '')
            $size = 0;
        $this->fields[] = array($type, $label, $editable, $approvalonly, $approvalshow, $size, $level, $requiredfield, $newgroup);
    }
    
    /*
        Stores the new workflow layout.
        $this->mode is used to determine whether the form is in the draft state or a brand new form.
        Also it determines whether it will be published or just saved as a draft again. 
        1 - Brand new draft
        2 - Draft saving as a draft again
        3 - Brand new publishing 
        4 - Draft now being published
    */
    public function storeToDatabase() {
        global $wpdb;
        if($this->draft)
            echo '<br>DRAFT MODE ON<br>';
        if($this->mode == 1 || $this->mode == 3) {
            $sql = "INSERT INTO workflowform (NAME, APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4,
                                            PROCESSOR, BEHALFOF_SHOW,
                                                DRAFT, SAVED_FIELDS, NUM_FIELDS, ENABLED)
                    VALUES ('$this->name', '".$this->approvers['0']."'";
            
            for($i = 1; $i < count($this->approvers); $i++) {
                if($this->approvers[$i] != -1) {
                    $sql .= ", '".$this->approvers[$i]."'";
                } else {
                    $sql .= ", NULL";
                }
            }
            
            if($this->processor != -1) {
                $sql .= ", '".$this->processor."'";
            } else {
                $sql .= ", NULL";
            }
            
            $sql .= ", '".$this->behalfof."', ";
            
            $sql .= "'".$this->draft."', ";
            if($this->draft)
                $sql .= "'".$this->savedData."', ";
            else
                $sql .= "NULL, ";
            if($this->draft)
                $sql .= "'".$this->numFields."', ";
            else
                $sql .= "NULL, ";
            
            $sql .= "'".(!$this->draft)."')";
        } else if($this->mode == 2 || $this->mode == 4) {
            $sql = "UPDATE workflowform 
                    SET NAME = '".$this->name."', 
                        APPROVER_ROLE = '".$this->approvers['0']."', ";
                        
            /*if($this->approvers[$i] != -1) {
                $sql .= ", '".$this->approvers[$i]."'";
            } else {
                $sql .= ", NULL";
            } */
            
            for($i = 1; $i < count($this->approvers); $i++) {
                $sql .= "APPROVER_ROLE".($i + 1)." = ";
                if($this->approvers[$i] != -1) {
                    $sql .= "'".$this->approvers[$i]."', ";
                } else {
                    $sql .= "NULL, ";
                }
            }
            if($this->processor != -1) {
                $sql .= " PROCESSOR = '".$this->processor."', ";
            } else {
                $sql .= " PROCESSOR = NULL, ";
            }
            $sql .= "   BEHALFOF_SHOW = '".$this->behalfof."',
                        DRAFT = '".$this->draft."', 
                        SAVED_FIELDS = ";
                        
            if($this->draft)
                $sql .= "'".$this->savedData."', ";
            else
                $sql .= "NULL, ";
            
            $sql .= "NUM_FIELDS = ";
            
            if($this->draft)
                $sql .= "'".$this->numFields."', ";
            else
                $sql .= "NULL, ";
                       
            $sql .= "ENABLED = '".(!$this->draft)."' ";
            
            $sql .= "WHERE FORMID = '".$this->previousID."'";
        }
        
        
        $result = $wpdb->query($sql, ARRAY_A);
        
        if($this->mode == 1 || $this->mode == 3) {
            $inserted_id = $wpdb->insert_id;
        } else {
            $inserted_id = $this->previousID;
        }
        
        if(!$result) {
            if($wpdb->result == true && $this->mode == 2) //When nothing was updated
                return;
            else 
                die('Failed to update the form. Please contact helpdesk.');
        }
        
        //If the workflow is being saved, there is no more work needed to be done. 
        if($this->mode == 1 || $this->mode == 2) {
            return;
        } 
        
        //Store fields into WorkflowDetails
        $formCount = -1;
        for($i = 0; $i < count($this->fields); $i++) {
            //Update the FieldID if it is not part of a radio group
            //Skips this line if it is another radio button as part of that group, otherwise this is run
            if(!($this->fields[$i][8] == 0 && ($this->fields[$i][0] == 13 || $this->fields[$i][0] == 2)))
                $formCount++;
            
            
            $sql = "INSERT INTO workflowformdetails (FORMID, FIELDID, POSITION, TYPE, LABEL, EDITABLE, APPROVAL_ONLY, 
                                                    APPROVAL_SHOW, REQUIRED, APPROVAL_LEVEL, FIELD_WIDTH)
                    VALUES ('$inserted_id', '$formCount', '$i', '".$this->fields[$i][0]."', '".$this->fields[$i][1]."', '".$this->fields[$i][2]."', 
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
        
        //Save the form creation data so that it can easily be copied later
        $sql = "INSERT INTO workflowformsave (FORMID, CONTENT, VERSION, DATE_SAVED, NUM_FIELDS)
                VALUES ('$inserted_id', '".$this->savedData."', 1, '".date('Y-m-d H:i:s')."', '".$this->numFields."')";
        
        $result = $wpdb->query($sql);
        if(!$result) {
            die('Failed to insert save history.<br>'.$sql);
        }
    }
    
    /*
    Updates the database with the user submissions.
    */
    public function updateWorkflowSubmissions($fields, $newstatus, $submissionID, $formID, $user, $misc_content, $commenttext, $behalfof, $sup, $uniqueToken, $hrnotes = '') {
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
        $sql = "SELECT STATUS, STATUS_APPROVAL, COMMENT, USER, UNIQUE_TOKEN, APPROVER_DIRECT, HR_NOTES
                FROM workflowformstatus
                WHERE SUBMISSIONID = '$submissionID'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        
        if(count($result) != 0) {
            $row = $result[0];
            $oldstatus = $row['STATUS'];
            $oldApprovalStatus = $row['STATUS_APPROVAL'];
            $oldcomment = $row['COMMENT'];
            $oldhrnotes = $row['HR_NOTES'];
            $oldDirectApprover = $row['APPROVER_DIRECT'];
            if($row['USER'] != $user || ($row['USER'] == $user && $newstatus != 3)) { //Grabs the correct approval level for the history
                $historyApprovalStage = $oldApprovalStatus;
            }
            if($uniqueToken != $row['UNIQUE_TOKEN']) {
                $_SESSION['ERRMSG'] = 'This submission has recently changed. Reload the submission to make any required changes.';
                header('location: ?page=viewsubmissions');
                die();
            }
            //echo 'DEBUG: Old Status:'.$oldstatus.'<br>';
        }
        
        //HR Notes
        if($hrnotes != '' && Workflow::hasRoleAccess(Workflow::loggedInUser(), 26)) {
            $hrnotes = str_replace("\\", "\\\\", $hrnotes);
            $hrnotes = str_replace("'", "\'", $hrnotes);
        } else if(Workflow::hasRoleAccess(Workflow::loggedInUser(), 26)) {
            //If HR removed notes
            if($oldhrnotes != '')
                $hrnotes = ' ';
        } else {
            $hrnotes = '';
        }
        
        if($oldstatus == 4 || ($newstatus == 50 && $hrnotes != '')) {
            //continue updating 
        } else if($oldstatus >= 7 && !($oldstatus == 7 && $newstatus == 20)) {
            $_SESSION['ERRMSG'] = 'This form can no longer be edited.';
            header('location: ?page=viewsubmissions');
            die();
        }
        
        date_default_timezone_set('America/Los_Angeles');
        
        if($newstatus == 20) {
            //Mark as processed
            $sql = "UPDATE workflowformstatus 
                    SET ";
                    
            if($commenttext != '') {
                $oldcomment = str_replace("\\", "\\\\", $oldcomment);
                $commenttext = str_replace("\\", "\\\\", $commenttext);
                $newtext = str_replace("'", "\'", $oldcomment).'<b>['.Workflow::loggedInUserName().' on: '.date('Y-m-d H:i').']</b><br>'.
                    str_replace("'", "\'", $commenttext).'<br>';
                
                $sql .= " COMMENT = '$newtext', ";
            }
            if($hrnotes != '') {
                $sql .= " HR_NOTES = '$hrnotes', ";
            }
            $sql .= "PROCESSED = '1'";
            
            $sql .= "WHERE SUBMISSIONID = '$submissionID'";
            $result = $wpdb->query($sql, ARRAY_A);
            
            //Update history
            $sql = "INSERT INTO workflowformhistory (USER, SUBMISSION_ID, APPROVAL_LEVEL, ACTION, DATE_SUBMITTED)
                    VALUES ('$user', '$submissionID', '20', '20', '".date('Y-m-d H:i:s')."')";
            
            $result = $wpdb->query($sql, ARRAY_A);
            
            //Prevents sending of an email.
            return 0;
        } else if($newstatus == 50) {
            //Update only the HR notes
            $sql = "UPDATE workflowformstatus 
                    SET HR_NOTES = '$hrnotes'
                    WHERE SUBMISSIONID = '$submissionID'";
            $result = $wpdb->query($sql, ARRAY_A);
            return 0;
        }
        
        $directApprover = Workflow::getDirectApprover($user);
        
        if($sup) {
            $direct = Workflow::getDirectApprover($user);
            $direct2 = Workflow::getDirectApprover($direct);
            if($sup == 1) {
                $directApprover = $direct;
            } else if($sup == 2) {
                $directApprover = $direct2;
            } else {
                $directSupervisors = Workflow::getMultipleDirectApprovers($user);
                $foundSupervisor = 0;
                //TODO: Check if we want to add the supervisors of supervisors
                foreach($directSupervisors as $directSup) {
                    if($sup == $directSup['supervisor']) {
                        $directApprover = $sup;
                        $foundSupervisor = 1;
                        break;
                    }
                }
                if(!$foundSupervisor) {
                    if($sup == $direct2) {
                        $directApprover = $sup;
                        $foundSupervisor = 1;
                    }
                }
                if(!$foundSupervisor) {
                    $_SESSION['ERRMSG'] = 'The supervisor you have selected does not appear to be your supervisor. 
                    Please contact help desk at <a href="mailto:helpdesk@p2c.com">helpdesk@p2c.com</a> if this is
                    an error.';
                    header('location: ?page=viewsubmissions');
                    die();
                }
            }
            $foundSupervisor = 1;
        }
        
        $newApprovalStatus = 1;
        
        if($submissionID == 0) {
            $misc_content = str_replace("\\", "\\\\", $misc_content);//$misc_content;//str_replace("'", "\'", $misc_content);
            $new_misc_content = str_replace("'", "\'", $misc_content);//$misc_content;//str_replace("'", "\'", $misc_content);
        
            //echo 'SUBMISSION ID : '.$submissionID.' not found. <br>';
            
            $sql = "INSERT INTO workflowformstatus (USER, STATUS, FORMID, MISC_CONTENT, DATE_SUBMITTED, COMMENT, 
                                                    HR_NOTES, BEHALFOF, APPROVER_DIRECT)
                    VALUES ('$user', '$newstatus', '$formID', '$new_misc_content', '".date('Y-m-d')."', ";
            
            if($commenttext != '') {
                $commenttext = str_replace("\\", "\\\\", $commenttext);
                $sql .= "'<b>[".Workflow::loggedInUserName()." on: ".date("Y-m-d H:i")."]</b><br>".str_replace("'", "\'", $commenttext)."<br>', ";
            } else {
                $sql .= "NULL, ";
            }
            
            if($hrnotes != '') {
                $sql .= "'$hrnotes', ";
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
            } else if($newstatus == 2 || $newstatus == 3) {
                $newApprovalStatus = 0;
            }
            
            $sql = "UPDATE workflowformstatus 
                    SET STATUS = '$newstatus',
                        STATUS_APPROVAL = '$newApprovalStatus',
                        DATE_SUBMITTED = '".date('Y-m-d')."' ";
            if($foundSupervisor) {
                $sql .= ", APPROVER_DIRECT = '$directApprover'";
                
                //Quickly add the direct supervisor to the history so they can see the form once completed
                $sqlOldDirect = "INSERT INTO workflowsuphistory (USER_ID, SUBMISSIONID, DIRECT_LEVEL) 
                                VALUES ('$oldDirectApprover', '$submissionID', '$oldApprovalStatus')";
                $resultOldDirect = $wpdb->query($sqlOldDirect);
            }
                    
            if($commenttext != '') {
                $oldcomment = str_replace("\\", "\\\\", $oldcomment);
                $commenttext = str_replace("\\", "\\\\", $commenttext);
                $newtext = str_replace("'", "\'", $oldcomment).'<b>['.Workflow::loggedInUserName().' on: '.date('Y-m-d H:i').']</b><br>'.
                    str_replace("'", "\'", $commenttext).'<br>';
                
                $sql .= ", COMMENT = '$newtext' ";
            }
            if($hrnotes != '') {
                $sql .= ", HR_NOTES = '$hrnotes' ";
            }
            //Process the extra misc content including for historic forms
            //43 = Allowance calculator up until 3/20/2017
            if($formID == $this->allowanceCalculatorID || $formID = 43) {
                $misc_content = str_replace("\\", "\\\\", $misc_content);
                $misc_content = str_replace("'", "\'", $misc_content);
                $sql .= ", MISC_CONTENT = '$misc_content' ";
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
    
    /*
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
    
    /*
    Decides which layout should be displayed.
    */
    public function configureWorkflow() {
        global $wpdb;
        $configSuccess = $approver = $supNext = $processor = 0;
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
                            APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, APPROVER_DIRECT, 
                            BEHALFOF, UNIQUE_TOKEN, PROCESSOR, PROCESSED, HR_NOTES
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
                $hrnotes = $row['HR_NOTES'];
                $submittedby = $row['USER'];
                $behalfof = $row['BEHALFOF'];
                $this->uniqueToken = $row['UNIQUE_TOKEN'];
            } else {
                echo 'That submission does not exist.';
                return;
            }
            
            
            //Check if the person is authorized to look at this filled out form
            //TODO: Complete authentication
            
            $currentApprovalRole = -1;
            $hasAnotherApproval = 0;
            if($approvalStatus == 0) {
                if($row['APPROVER_ROLE'] != '') {
                    $hasAnotherApproval = 1;
                    if($row['APPROVER_ROLE'] == 8)
                        $supNext = 1;
                }
            } else if($approvalStatus == 1) {
                $currentApprovalRole = $row['APPROVER_ROLE'];
                if($row['APPROVER_ROLE2'] != '') {
                    $hasAnotherApproval = 1;
                    if($row['APPROVER_ROLE2'] == 8)
                        $supNext = 1;
                }
            } else if($approvalStatus == 2) {
                $currentApprovalRole = $row['APPROVER_ROLE2'];
                if($row['APPROVER_ROLE3'] != '') {
                    $hasAnotherApproval = 1;
                    if($row['APPROVER_ROLE3'] == 8)
                        $supNext = 1;
                }
            } else if($approvalStatus == 3) {
                $currentApprovalRole = $row['APPROVER_ROLE3'];
                if($row['APPROVER_ROLE4'] != '') {
                    $hasAnotherApproval = 1;
                    if($row['APPROVER_ROLE4'] == 8)
                        $supNext = 1;
                }
            } else if($approvalStatus == 4) {
                $currentApprovalRole = $row['APPROVER_ROLE4'];
            } else if($approvalStatus == 100) { //When the submission is complete
                if(Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE']) 
                    || Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE2'])
                    || Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE3'])
                    || Workflow::hasRoleAccess($loggedInUser, $row['APPROVER_ROLE4'])) {
                    $approver = 1;
                }
                //If they don't have normal access check if they are a supervisor
                if(!$approver && ($row['APPROVER_ROLE'] == 8 || $row['APPROVER_ROLE2'] == 8 
                    || $row['APPROVER_ROLE3'] == 8 || $row['APPROVER_ROLE4'] == 8)) {
                    $currentApprovalRole = 8;
                } 
                
                //Check if they just need to process the form
                if(Workflow::hasRoleAccess($loggedInUser, $row['PROCESSOR'])) {
                    $processor = 1;
                }
                
                //Give access if they were a direct approver at any time during the form process
                if(!$approver)
                    $approver = (Workflow::getDirectApprover($submittedby) == $loggedInUser); //Level 1 only
                if(!$approver) {
                    $sqlDirect = "SELECT USER_ID
                            FROM workflowsuphistory
                            WHERE SUBMISSIONID = '$sbid'";
                    
                    $resultDirect = $wpdb->get_results($sqlDirect, ARRAY_A);
                    foreach($resultDirect as $rowDirect) {
                        if($rowDirect['USER_ID'] == $loggedInUser) {
                            $approver = 1;
                            break;
                        }
                    }
                }
            }
            
            if($currentApprovalRole == 8 && !$approver) {
                //Decided to still allow the direct supervisor to be able to edit the submission even though
                //it wasn't directed to them. See ** below.
                $approver = ($row['APPROVER_DIRECT'] == $loggedInUser 
                    || (Workflow::getDirectApprover($submittedby) == $loggedInUser 
                        && $approvalStatus == 1)//**If this gets changed, remove this line
                    || Workflow::hasRoleAccess($loggedInUser, $currentApprovalRole));
                
            } else if(!$approver) {
                $approver = (Workflow::hasRoleAccess($loggedInUser, $currentApprovalRole));
            }
            
            
            if($processor) {
                //Give viewing access to the processor. If form was denied, you can prevent the processor from seeing it here
                $configvalue = 9;
                
                if($row['PROCESSED'] == 0)
                        $hasAnotherApproval = 1;
            } else if($configvalue == 4 && $approver) {
                //Do nothing keep going.
            } else if(($configvalue == 7 || $configvalue == 8) && $approver) {
                $configvalue = 9;
            } else if($submittedby != $loggedInUser) {
                //Check to see if an approver will eventually have access to this submission again
                if(Workflow::submissionApprover($sbid, $loggedInUser, $row['APPROVER_ROLE'], $row['APPROVER_ROLE2'], 
                    $row['APPROVER_ROLE3'], $row['APPROVER_ROLE4'], $row['APPROVER_DIRECT'])) {
                    $hasAnotherApproval = 0;
                    $configvalue = 9; 
                    echo '<span style="color:red;">This submission has already been reviewed. You can view the submission and approval
                        history below.<br></span>';
                } else if(Workflow::isAdmin(Workflow::loggedInUser())) {
                    echo '<span style="color:red;"><b>ADMIN VIEW</b><br></span>';
                    if($status == '2' || $status == '3') {
                        echo '<span style="color:red;">This submission is currently being edited by the submitter.<br></span>';
                        return;
                    } else if($status == '10') {
                        echo '<span style="color:red;">This submission has been cancelled by the submitter.<br></span>';
                        return;
                    }
                    
                    echo '<span style="color:red;">This submission is currently being processed by: <b>'.Workflow::getNextRoleName($approvalStatus, $hasAnotherApproval, $wfid, 1, $sbid, 1).'</b>.<br>It is being submitted to: <b>'.Workflow::getNextRoleName($approvalStatus, $hasAnotherApproval, $wfid, 1, $sbid).'</b></span><br><br>';
                    
                    //Set the flags to only view the submission
                    $hasAnotherApproval = 0;
                    $configvalue = 9; 
                } else {
                    echo 'You do not have access to view this form at this time. If this is an error, contact helpdesk at <a href="mailto:helpdesk@p2c.com">helpdesk@p2c.com</a>.<br>';
                    return;
                }
            } else if($configvalue == 4) {
                $configvalue = 0;
            }
            
            if($status == 7 && $row['PROCESSOR'] != NULL && $row['PROCESSOR'] != '') {
                if($row['PROCESSED'] == 0)
                    $status = 9;
                else
                    $status = 10;
            }
                    
            if(isset($_POST['export']) && $_POST['export'] == 1 && ($status == 2 || $status == 3)) {
                $misc_content = stripslashes($_POST['misc_content']);
            } else if($row['MISC_CONTENT'] != '') {
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
            
            
            $sql = "SELECT APPROVER_ROLE, ENABLED
                    FROM workflowform
                    WHERE FORMID = '$wfid'";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            if(count($result) != 1 || !$result[0]['ENABLED']) {
                header('location: ?page=viewsubmissions');
                $_SESSION['ERRMSG'] = 'This form does not exist or is no longer available.';
                die();
            }
            
            if($result[0]['APPROVER_ROLE'] == 8)
                $supNext = 1;
            else 
                $supNext = 0;
        }
        
        echo Workflow::loadWorkflowEntry($wfid, $configvalue, $sbid, $misc_content, $comments, $submittedby, 
            $status, $approvalStatus, $hasAnotherApproval, $behalfof, 0, $supNext, $hrnotes);
    }
    
    
    /**
    *Loads the entire form based on the configuration. When adding field types, copy one of the previous fields code
    *and just expand on the logic. 
    *
    *
    *
    *
    *
    *
    *
    *@param id The FORMID that is being loaded.
    *
    *@param configuration The status of the form and how it will be displayed.
    *                       1) Brand new form submission
    *                       2) Continue to edit a saved form
    *                       4) Approver - (opens the form to an approver to fill out approver fields)
    *                       7) Approved Final - The end user can see the final form and be notified that it was approved.
    *                       8) Declined Final - The end user can see the final form and be notified that it was denied.
    *                       9) Approver Final View - show all fields for the approver (Contains fields hidden to the submitter)
    *                       10) Cancelled - The end user can see the form that was cancelled but can't change anything. 
    *
    *@param submissionID The SUBMISSIONID that is being loaded. For new submissions don't pass anything in here.  
    *
    *@param misc_content If there is extra html content from another form that needs to be passed in to be part of the form pass
    *                       it through this parameter. Be sure to create an export file that will do this. 
    *                       Use allowance-calculator-export.php as an example. 
    *
    *@param comments The current chain of comments that are to be displayed.
    *
    *@param submittedby The employee number of the person that submitted the form. (This is not just the person who is logged in)
    *
    *@param submittedby The actual status of the submission. This is used for approver config value of 9 so that the correct status
    *                       is displayed.
    *
    *@param approvalStatus The level for approving. There are currently 4 levels in total. This turns to 100 once approval is done. 
    *
    *@param hasAnotherApproval Boolean to indicate if there is another approval step. This helps load the buttons correctly. 
    *
    *@param behalfof The employee number of the person who submitted a form on behalf of someone else. 
    *
    *@param emailMode Boolean to configure email mode or regular form view. (NOTE: email mode requires inline styling = NOT FUN :[ )
    *
    *@param supNext Boolean to identify whether the form needs to display radio buttons to select which direct supervisor to select. 
    *
    */
    public function loadWorkflowEntry($id, $configuration, $submissionID, $misc_content, $comments, $submittedby, 
        $status, $approvalStatus, $hasAnotherApproval, $behalfof, $emailMode, $supNext, $hrnotes = '') {
        global $wpdb;
        $formActive = 0;
        if(0 <= $configuration && $configuration < 7 && !$emailMode || $configuration == 9 && $hasAnotherApproval)
            $formActive = 1;
        
        $response = '%EMAILCLICK%';
        
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
        
        if(!$emailMode) {
            $response .= '<h2>'.$workflowName.'</h2><hr>';
            $response .= '<h2><span style="font-size:16px;">Submitted by: ';
        
            if($behalfof != '') {
                $response .= Workflow::getUserName($behalfof).' on behalf of ';
            }
            
            $response .= Workflow::getUserName($submittedby).
                    '&nbsp;&nbsp;&nbsp;Last Change by: '.$lastAction.'</span></h2>';
        }
        
        
        if(!$emailMode)
            $response .= '<p class="status">';
        else
            $response .= '<p style="font-size: 24px;margin: 0;">';
        
        if($configuration == 0 || $configuration == 4) 
            $response .= 'Status: Pending Approval.';
        else if(($configuration == 7 || $configuration == 9) && $status == 9)
            $response .= 'Status: Approved - Not Processed Yet';
        else if(($configuration == 7 || $configuration == 9) && $status == 10)
            $response .= 'Status: Approved - Processed';
        else if($configuration == 7 || ($configuration == 9 && $status == 7)) {
            $response .= 'Status: Approved.';
        } else if($configuration == 8 || ($configuration == 9 && $status == 8)) {
            $response .= 'Status: Not Approved.';
        } else if($configuration == 10) {
            $response .= 'Status: Not Approved.';
        } else if($emailMode && $configuration == 3)
            $response .= 'Status: Input Required.';
        else if($configuration == 9 && $status == 3)
            $response .= 'Status: Pending Resubmission by Submitter';
        else if($configuration == 9 && $status == 4)
            $response .= 'Status: Under Review by another approval group';
        
        $response .= '</p>';
        
        if(0 <= $configuration && $configuration < 7 && $emailMode) {
            $uniqueId = WorkFlow::workflowEmailToken($submissionID);
        }
        if($emailMode) {
            if($configuration == 4) {
                $response .= '<p style="margin-bottom:30px;">Quick Reply: ';
                $response .= '<a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=approve&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;">Approve</a>';
                
                $response .= ' | <a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=change&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;">Request Change</a>';
                
                $response .= ' | <a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=deny&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;">Not Approved</a>';
                $response .= '</p>';
            } else {
                $response .= '<p style="margin-bottom:30px;"></p>';
            }
            $response .= '<a style="padding: 15px 8px 15px 8px; background-color: #F7941E;color: white; 
            text-decoration: none;" 
                    href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'">
                    Click to view the form online</a>';
        }
        else
            $response .='<hr>';
        
        if($emailMode)
            $response .= '<div style="width: 96%; background-color: #0B7086; color: white; font-size: 1.2em; margin: 30px 0 20px 0; padding: 8px 2% 10px 2%">'.$workflowName.'</div>';
        
        
        if($formActive)
            $response .= '<form id="workflowsubmission" action="?page=process_workflow_submit" method="POST" autocomplete="off" onsubmit="return submissioncheck();"><input type="hidden" name="uniquetoken" value="'.$this->uniqueToken.'">';
        
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
            $response .= '<div class="workflow workflowlabel">Submit on behalf of Employee:</div>';
            $response .= '<div class="workflow workflowright style-1" style="width:250px;">';
            //<input type="text" id="onbehalf" name="onbehalf" placeholder="Emp Num"></div>';
            
            
            $response .= '<select id="onbehalf" name="onbehalf" class="chosen-select" data-placeholder=" " onchange="updateSupervisorButton();"><option value="Myself">Myself</option>';
            $values = Workflow::getAllUsers();
            
            for($i = 0; $i < count($values); $i++) {
                $response .= '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
            }
            $response .= '</select></div>';
            $response .= '<div class="clear" style="height: 50px;"></div>';
        }
        
        //For each field that is part of the form
        $count = 0;
        $anotherOption = $skipOptions = $foundOptionMatch = 0;
        $prevId = -1;
        foreach($result as $row) {
            //Set flags if the current field is part of an option list
            if($row['TYPE'] == 2 && $prevId == $row['FIELDID']) {
                $anotherOption = 1;
                if($skipOptions && !$foundOptionMatch) {
                    $response .='</div></div>';
                    $foundOptionMatch = 1;
                    continue;
                } else if($foundOptionMatch) {
                    continue;
                }
            } else {
                //Close the option list
                if($anotherOption && !$skipOptions)
                    $response .='</select></div></div>';
                else if($anotherOption && $skipOptions && !$foundOptionMatch)
                    $response .='</div></div>';
                $anotherOption = $skipOptions = $foundOptionMatch = 0;
                $prevId = $row['FIELDID']; //Set prevId for next time
            }
            
            
            $count++;
            $fieldvalue = '';
            if($configuration != 1) {
                //echo 'USING '.Workflow::findValue($prevSubmissions, $row['FIELDID']).'<br>';
                $fieldvalue = Workflow::findValue($prevSubmissions, $row['FIELDID']);
            }
            
            //Determines whether the field will be an approval field and if it is an editable field.
            $editableField = ($configuration != 4  
                || ((($row['APPROVAL_ONLY'] == 1 && ($row['APPROVAL_LEVEL'] == $approvalStatus))
                || $row['EDITABLE'] == 1) && $configuration == 4));
            
            $approval_show = ($configuration >= 7 && $row['APPROVAL_SHOW']) || $configuration == 9 || 
                ($configuration == 0 && $row['APPROVAL_SHOW'] && $row['APPROVAL_LEVEL'] < $approvalStatus); //for submitter while under review
            
            $appLvlAccess = $row['APPROVAL_LEVEL'] <= $approvalStatus;
            
            if($configuration >= 7 || $configuration == 0)
                $editableField = 0;
            
            /* Creates the field based on the type of field it should be
            * 0 - Textbox
            * 1 - Label
            * 2 - Option List
            * 3 - Newline
            * 4 - Checkbox
            * 5 - Autofill Name
            * 6 - Autofill Date
            * 7 - Date
            * 8 - 
            * 9 - Horizontal Line
            * 10 - Heading 2
            * 11 - Heading 1
            * 12 - Heading 3
            * 13 - Radio Boxes
            * 14 - File Upload
            * 15 - Text Area
            */
            if($row['TYPE'] == 1) { //Label
                if($row['APPROVAL_ONLY'] == 1) {
                    if($configuration == 4 && $appLvlAccess || $approval_show) {
                        $response .= '<div class="workflow workflowlabel approval mobile ';
                    } else {
                        continue;
                    }
                } else {
                    $response .= '<div class="workflow workflowlabel mobile ';
                }
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;font-weight:bold;';
                }
                
                $response .= '"><div class="inside-text-center">'.$row['LABEL'].'</div></div>';
                
            } else if($row['TYPE'] == 0) { //Textbox
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval mobile ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 mobile ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                    
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                if($editableField) {
                    $response .= '<input type="text" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" placeholder="'.$row['LABEL'].'" value="'.$fieldvalue.'" ';
                    if($row['REQUIRED'])
                        $response .= 'required';
                    if($emailMode)
                        $response .= ' disabled';
                    $response .= '>';
                } else {
                    $response .= '<span class="disabled-response">'.$fieldvalue.'</span>';
                }
                $response .= '</div></div>';
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
                        $response .= '<div class="workflow workflowlabel approval mobile ';
                            
                    } else 
                        continue;
                } else {
                    $response .= '<div class="workflow workflowlabel mobile ';
                }
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
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
                $response .='>'.$row['LABEL'].'</div></div>';
                
            } else if($row['TYPE'] == 5) { //Autofill Name
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                if($editableField) { //TODO: check approval level
                    $response .= '<input type="text" placeholder="'.$row['LABEL'].'" value="';
                    if($emailMode)
                        $response .= '%EMAILNAME%';
                    else 
                        $response .= Workflow::loggedInUserName();
                    $response .= '" disabled class="autonamefill">';
                    $response .= '<input type="hidden" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" value="';
                    if($emailMode)
                        $response .= '%EMAILNAME%';
                    else 
                        $response .= Workflow::loggedInUserName();
                    $response .= '" class="autonamefill">';
                } else {
                    $response .= '<input type="text" disabled value="'.$fieldvalue.'">';
                }
                $response .= '</div></div>';
            } else if($row['TYPE'] == 6) { //Autofill Date
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                if($editableField) { //TODO: check approval level
                    $response .= '<input type="date" value="'.date('Y-m-d').'" disabled>';
                    $response .= '<input type="hidden" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" value="'.date('Y-m-d').'">';
                } else {
                    $response .= '<input type="date" disabled value="'.date("Y-m-d", strtotime($fieldvalue)).'">';
                }
                $response .= '</div></div>';
            } else if($row['TYPE'] == 7) { //Date
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                if($editableField) {
                    $response .= '<input type="date" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" placeholder="mm/dd/yyyy" value="';
                    if($fieldvalue != '')
                        $response .= date("Y-m-d", strtotime($fieldvalue));
                    $response .= '"';
                    if($emailMode) {
                        $response .= ' disabled';
                    }
                    $response .= '>';
                } else {
                    $response .= '<input type="date" disabled value="';
                    if($fieldvalue != '')
                        $response .= date("Y-m-d", strtotime($fieldvalue));
                    $response .= '">';
                }
                $response .= '</div></div>';
            } else if($row['TYPE'] == 9) { //Horizontal Line
                $response .= '<div class="clear" ';
                if($emailMode)
                    $response .= 'style="clear:both;"';
                $response .= '></div>';
                $response .= '<hr>';
            } else if($row['TYPE'] == 10 || $row['TYPE'] == 11 || $row['TYPE'] == 12) { //Heading 1,2,3
                if($row['APPROVAL_ONLY'] == 1) {
                    if($configuration == 4 && $appLvlAccess || $approval_show) {
                        $response .= '<div class="workflow workflowlabel approval mobile ';
                    } else {
                        continue;
                    }
                } else {
                    $response .= '<div class="workflow workflowlabel mobile ';
                }
                
                $response .= '" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><h';
                if($row['TYPE'] == 11)
                    $response .= '1';
                else if($row['TYPE'] == 12)
                    $response .= '3';
                else
                    $response .= '2';
                
                $response .= '>'.$row['LABEL'].'</h';
                if($row['TYPE'] == 11)
                    $response .= '1';
                else if($row['TYPE'] == 12)
                    $response .= '3';
                else
                    $response .= '2';
                
                $response .= '></div>';
                
            } else if($row['TYPE'] == 13) { //Radio boxes
                if($row['APPROVAL_ONLY'] == 1) {
                    if($configuration == 4 && $appLvlAccess || $approval_show) {
                        $response .= '<div class="workflow workflowlabel approval mobile ';
                            
                    } else 
                        continue;
                } else {
                    $response .= '<div class="workflow workflowlabel mobile ';
                }
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                $response .= '<input type="radio" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                    $row['FIELDID'].'" value="'.$row['LABEL'].'" ';
                
                if($editableField && $row['REQUIRED'])
                    $response .= 'required ';
                
                
                if(!$editableField || $emailMode) {
                    $response .= 'disabled ';
                }
                if($fieldvalue == $row['LABEL'])
                    $response .= 'checked';
                $response .='>'.$row['LABEL'].'</div></div>';
                
            } else if($row['TYPE'] == 2) { //Option List
                if($row['APPROVAL_ONLY'] == 1) 
                    if(!($configuration == 4 && $appLvlAccess || $approval_show)) 
                        continue;
                
                //Display just the value if it is not editable
                if(!$editableField || $emailMode) {
                    if($fieldvalue == $row['LABEL']) 
                        $skipOptions = 1;
                    else
                        continue;
                }
                
                if(!$anotherOption || $skipOptions) {
                    if($row['APPROVAL_ONLY'] == 1) {
                        if($configuration == 4 && $appLvlAccess || $approval_show) {
                            $response .= '<div class="workflow approval mobile ';
                        } 
                    } else {
                        $response .= '<div class="workflow mobile ';
                    }
                    
                    if(!$skipOptions)  
                        $response .= 'workflowlabel ';
                    else 
                        $response .= 'workflowright ';
                    
                    if($row['FIELD_WIDTH'] != NULL) {
                        $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                    }
                    
                    if($skipOptions)
                        $response .= ' style-1';
                    
                    $response .= ' outside-text-center" style="';
                    
                    if($emailMode) {
                        $response .= 'float: left; margin-right:10px;';
                    }
                    
                    $response .= '"><div class="inside-text-center">';
                    
                    if(!$skipOptions) {
                        $response .= '<select id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                            $row['FIELDID'].'" ';
                        
                        if(!$editableField || $emailMode) {
                            $response .= 'disabled ';
                        }
                        
                        $response .= '>';
                    }
                    
                }
                
                if(!$skipOptions) {
                    $response .= '<option value="'.$row['LABEL'].'" ';
                    if($fieldvalue == $row['LABEL'])
                        $response .= 'selected';
                    $response .= '>'.$row['LABEL'].'</option>';
                } else {
                    $response .= '<select disabled style="background-color:#EBEBE4;color:#545454;"><option>'.$fieldvalue.'</option></select>';
                }
            } else if($row['TYPE'] == 14) { //File Upload
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval mobile ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 mobile ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                    
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                if($editableField) {
                    $response .= '<input type="file" id="file'.$row['FIELDID'].'" name="documents[]" size="70"  
                        onchange="submitFileAJAX('.$row['FIELDID'].');" accept="image/gif, image/jpeg, image/png,.xls,.xlsx,.doc,.docx, application/pdf,.txt" 
                        value="" ';
                    if($row['REQUIRED'] && $fieldvalue == '')
                        $response .= ' required';
                    $response .= '>(Max: '.ini_get('upload_max_filesize').')';
                    
                    if($fieldvalue != '')
                        $response .= '<span style="font-size:10px;color:red;">**Overwrites current file.</span>';
                    $response .= '<div id="file'.$row['FIELDID'].'msg" class="upload-msg">';
                    if($fieldvalue != '')
                        $response .= '<a href="'.$this->linkAddress.'/wp-content/uploads/p2cforms/'.$fieldvalue.'" target="blank">'.$fieldvalue.'</a>';
                    $response .= '</div>';
                    $response .= '<input type="hidden" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].'"
                        value="'.$fieldvalue.'"/>';
                } else if(!$emailMode) {
                    $response .= '<a href="'.$this->linkAddress.'/wp-content/uploads/p2cforms/'.$fieldvalue.'" target="blank">'.$fieldvalue.'</a>';
                }
                $response .= '</div></div>';
            } else if($row['TYPE'] == 15) { //Text Area
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval mobile ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 mobile ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= ' outside-text-center" style="';
                    
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '"><div class="inside-text-center">';
                
                if($editableField) {
                    $response .= '<textarea class="commenttext" style="width:100%;height:100px;" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.$row['FIELDID'].
                        '" ';
                    if($row['REQUIRED'])
                        $response .= ' required';
                    if($emailMode)
                        $response .= ' disabled';
                    $response .= '>'.$fieldvalue.'</textarea>';
                } else {
                    $response .= '<textarea class="commenttext" style="width:100%;height:100px;" disabled>'.$fieldvalue.'</textarea>';
                }
                $response .= '</div></div>';
            }
        }
        //In case the loop ended and the last id was an option list, close the field and div
        if($anotherOption && !$skipOptions)
            $response .='</select></div></div>';
        else if($anotherOption && $skipOptions)
            $response .='</div></div>';
         
        $response .= '<div class="clear"></div>';
        if($emailMode) {
            $response .= '<div style="clear:both;"></div>';
        }
        
        //HR Notes - Special notes that only the HR Notes group can edit and change
        if(!$emailMode && Workflow::hasRoleAccess(Workflow::loggedInUser(), 26)) {
            //If the form is no longer active but the HR notes need to be updated, create the form tags
            if(!$formActive)
                $response .= '<form id="workflowsubmission" action="?page=process_workflow_submit" method="POST" autocomplete="off" onsubmit="return submissioncheck();"><input type="hidden" name="uniquetoken" value="'.$this->uniqueToken.'">';
            $response .= '<div><h3>HR / Payroll Notes</h3></div>';
            $response .= '<textarea id="hrnotes" class="hrnotes" name="hrnotes" rows="5" cols="40" style="width: 100%;">'.$hrnotes.'</textarea>';
            $response .= '<br><br>';
            if(!$formActive) {
                $response .= '<input type="hidden" id="ns" name="ns" value="50">';
                $response .= '<input type="hidden" id="wfid" name="wfid" value="0">';
                $response .= '<input type="hidden" id="sbid" name="sbid" value="'.$submissionID.'">';
                $response .= '<button class="processbutton" type="submit">Update HR Notes</button><button type="button" class="processbutton" onclick="printForm();">Print</button><div style="clear:both;"></div></form>';
            }
        }
        
        //Display the comments history
        if(!$emailMode) {
            $response .= '<h3 id="add-comments">Comments</h3>';
        } else {
            $response .= '<div style="width: 96%; background-color: #0B7086; color: white; font-size: 1.2em; margin: 30px 0 20px 0; padding: 8px 2% 10px 2%">Comments</div>';
        }
        $response .= '<p class="comments-section">'.$comments.'</p>';
        
        if(0 <= $configuration && $configuration < 7 && !$emailMode) {
            if($configuration != 0) {
                $response .= '<textarea class="commenttext" name="commenttext" rows="5" cols="40" style="width: 100%;';
                if(isset($_GET['response']) && $_GET['response'] == 'change')
                    $response .= 'border:2px solid red;';
                $response .= '"></textarea>';
            }
            
            $response .= '<div class="clear"></div>';
            if($configuration == 0 || $configuration == 2 || $configuration == 3) {
                if($approvalStatus == 0) //Handle a denied form
                    $submittingStatus = $approvalStatus;
                else
                    $submittingStatus = $approvalStatus - 1;
                $submittingApproval = 1;
            } else {
                $submittingStatus = $approvalStatus;
                $submittingApproval = $hasAnotherApproval;
            }
            
            $response .= '<h3>Submitting to: '.Workflow::getNextRoleName($submittingStatus, $submittingApproval, $id, ($configuration == 0), 
                $submissionID).'</h3>';
            //If the next submission step is to a direct approver
            if($supNext && 0 < $configuration && $configuration <= 4) {
                $direct = Workflow::getDirectApprover(Workflow::loggedInUser());
                $direct2 = Workflow::getDirectApprover($direct);
                /*$response .= '<div id="supervisor-radio">';
                $response .= '<input type="radio" name="nextsupervisor" value="0" checked>'.Workflow::getUserName($direct);
                if($direct2 != 0)
                    $response .= '<input type="radio" name="nextsupervisor" value="1">'.Workflow::getUserName($direct2);
                $response .= '</div>';*/
                
                $response .= '<div id="supervisor-radio"><select name="directsupervisor">';
                $directSupervisors = Workflow::getMultipleDirectApprovers(Workflow::loggedInUser());
                //TODO: Check if we want to add the supervisors of supervisors
                $omitDirect2 = 0;
                foreach($directSupervisors as $directSup) {
                    $response .= '<option value="'.$directSup['supervisor'].'">'.Workflow::getUserName($directSup['supervisor']).'</option>';
                    if($directSup['supervisor'] == $direct2)
                        $omitDirect2 = 1;
                }
                if($direct2 != 0 && !$omitDirect2)
                    $response .= '<option value="'.$direct2.'">'.Workflow::getUserName($direct2).'</option>';
                $response .= '</select></div>';
            }
            $response .= '<div class="clear"></div>';
            $response .= '<input type="hidden" id="count" name="count" value="'.$count.'">';
            $response .= '<input type="hidden" name="wfid" value="'.$id.'">';
            $response .= '<input type="hidden" name="sbid" value="'.$submissionID.'">';
            $response .= '<input type="hidden" id="ns" name="ns" value="0">';
            if( 0 < $configuration && $configuration < 4) {
                $response .= '<button type="button" class="processbutton" onclick="saveSubmission(4, 0);">Submit Form</button>';
                $response .= '<button type="button" class="processbutton" onclick="saveSubmission(';
                if($configuration == 3)
                    $response .= '3, 0';
                else
                    $response .= '2, 0';
                $response .= ');">Save Draft</button>';
                $response .= '<button type="button" class="processbutton" onclick="saveSubmission(';
                if($configuration == 3) 
                    $response .= '8, 0';
                else
                    $response .= '10, 0';
                $response .= ');">Delete Form</button>';
                
                if(($id == $this->allowanceCalculatorID || $id = 43) && $submissionID != 0) {
                    $response .= '<button type="button" onclick="location.href=\'/mpd/allowance-goal-calculations/allowance-calculator/?sbid='.$submissionID.'\'" 
                        class="processbutton">Re-calculate Allowance</button>';
                }
            } else if($configuration == 4) {
                $response .= '<input type="hidden" name="approverredirect" value="1"/>';
                if($hasAnotherApproval) {
                    $response .= '<button type="button" id="approvelink" class="processbutton" onclick="saveSubmission(4, 1);">Approve</button>';
                } else {
                    $response .= '<button type="button" id="approvelink" class="processbutton" onclick="saveSubmission(7, 1);">Approve</button>';
                }
                $response .= '<button type="button" id="changelink" class="processbutton" onclick="saveSubmission(3, 1);">Request Change</button>';
                $response .= '<button type="button" id="denylink" class="processbutton" onclick="saveSubmission(8, 1);">Not Approved</button>';
            } else if($configuration == 0) {
                $response .= '<button type="button" id="retractlink" class="processbutton" onclick="saveSubmission(3, 0);">Retract Submission</button>';
            }
            
            //$response .= '<input type="submit" value="Submit" onclick="saveSubmission(3); onsubmit="">';
            $response .= '<input type="submit" value="Submit" id="formsubmitbutton" style="display: none;"></form>';
        } else if(0 <= $configuration && $configuration < 7 && $emailMode) {
            // ==== The following section is for the email that is sent out to the user ====
            //$uniqueId = WorkFlow::workflowEmailToken($submissionID); //This is now being set at the top of this function
            $response .= '<div class="clear"></div>';
            if($configuration == 4) {
                $response .= '<div style="width: 96%; background-color: #0B7086; color: white; font-size: 1.2em; margin: 30px 0 20px 0; padding: 8px 2% 10px 2%">Instructions</div>
        
            <p>If you require clarification on any items, please contact the submitter directly or use the request changes button.   
                Some forms may result in a fundamental change in the employment relationship of a staff member, 
                please review carefully before approving. </p>';
            }
            if($configuration == 0 || $configuration == 2 || $configuration == 3) {
                if($approvalStatus == 0) //Handle a denied form
                    $submittingStatus = $approvalStatus;
                else
                    $submittingStatus = $approvalStatus - 1;
                $submittingApproval = 1;
            } else {
                $submittingStatus = $approvalStatus;
                $submittingApproval = $hasAnotherApproval;
            }
            $response .= '<h3>Submitting to: '.Workflow::getNextRoleName($submittingStatus, $submittingApproval, $id, ($configuration == 0), 
                $submissionID).'</h3>';
            if($configuration != 3)
                $response .= '<p>By clicking approve, I acknowledge that I have read and approve the change being requested. </p>';
            
            if( 0 < $configuration && $configuration < 4) {
                $response .= '<a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'" style="padding: 15px 8px 15px 8px; background-color: #F7941E;color: white; 
            text-decoration: none; float:left;">View Form</a>';
                //Uncomment if email for revisions should include quick buttons. But they really shouldn't
                /*$response .= '<a href="'.$this->linkAddress.'/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=submit&lvl='.$approvalStatus.'&tk='.$uniqueId.'"><button type="button" style="background-color: #51abff;box-shadow: 0 0 5px 1px #969696; 
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Submit Form</button></a>';
                    
                $response .= '<a href="'.$this->linkAddress.'/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=save&lvl='.$approvalStatus.'&tk='.$uniqueId.'"><button type="button">Save Draft</button></a>';//http://local.theloop.com  https://devstaff.powertochange.org
                
                $response .= '<a href="'.$this->linkAddress.'/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=cancel&lvl='.$approvalStatus.'&tk='.$uniqueId.'"><button type="button" style="background-color: #ff8989;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Delete Form</button></a>';*/
            } else if($configuration == 4) {
                $response .= '<a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=approve&lvl='.$approvalStatus.'&tk='.$uniqueId.'" 
                    style="padding: 15px 8px 15px 8px; background-color: #F7941E;color: white; 
                    text-decoration: none;float:left;margin-right:30px;">Approve</a>';
                
                $response .= '<a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=change&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="padding: 15px 8px 15px 8px; background-color: #F7941E;color: white; 
                    text-decoration: none;float:left;margin-right:30px;">Request Change</a>';
                
                $response .= '<a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=deny&lvl='.$approvalStatus.'&tk='.$uniqueId.'"
                    style="padding: 15px 8px 15px 8px; background-color: #F7941E;color: white; 
                    text-decoration: none;float:left;margin-right:30px;">Not Approved</a>';
            } else if($configuration == 0) {
                $response .= '<a href="'.$this->linkAddress.'/forms-information/p2c-forms/?page=workflowentry&sbid='.$submissionID.'&response=retract&lvl='.$approvalStatus.'&tk='.$uniqueId.'" 
                    style="padding: 15px 8px 15px 8px; background-color: #F7941E;color: white; 
                    text-decoration: none;float:left;margin-right:30px;">Retract Submission</a>';
            }
        } else if($configuration == 9 && $hasAnotherApproval) {
            $response .= '<input type="hidden" name="approverredirect" value="1"/>';
            $response .= '<textarea class="commenttext" name="commenttext" rows="5" cols="40" style="width: 100%;"></textarea>';
            $response .= '<input type="hidden" id="count" name="count" value="'.$count.'">';
            $response .= '<input type="hidden" name="wfid" value="'.$id.'">';
            $response .= '<input type="hidden" name="sbid" value="'.$submissionID.'">';
            $response .= '<input type="hidden" id="ns" name="ns" value="20">';
            $response .= '<button type="button" id="approvelink" class="processbutton" onclick="saveSubmission(20, 1);">Processed</button>';
            $response .= '<input type="submit" value="Submit" id="formsubmitbutton" style="display: none;"></form>';
        }   
          
        
        //Show the history of the submission
        $sql = "SELECT *
                FROM workflowformhistory
                WHERE SUBMISSION_ID = '$submissionID'
                ORDER BY DATE_SUBMITTED ASC";
    
        $result = $wpdb->get_results($sql, ARRAY_A);
        if(!$emailMode)
            $response .= '<div class="clear"></div>';
        else
            $response .= '<div style="clear:both;"></div>';
        
        if($emailMode)
            $response .= '<div style="width: 96%; background-color: #0B7086; color: white; font-size: 1.2em; margin: 30px 0 20px 0; padding: 8px 2% 10px 2%">Approval history</div>';
        
        $response .= '<table id="workflowhistory"><tr><td colspan=3>';
        if(!$emailMode) 
            $response .= '<h3>Approval History</h3></td></tr>';
        
        $response .= '</td></tr>';
        $response .= '<tr><th>USER</th><th>ACTION</th><th>DATE</th></tr>';
        foreach($result as $row) {
            
            
            if($row['ACTION'] == 2) {
                //$temp .= 'Saved';
                continue;
            } else if($row['ACTION'] == 3 && $row['USER'] == $submittedby) {
                //$temp = 'Retracted - Saved';
                continue;
            } else if($row['ACTION'] == 3) {
                $temp = 'Review Required';// Lvl: '.$row['APPROVAL_LEVEL'];
            } else if($row['ACTION'] == 4 && $row['USER'] == $submittedby) {
                $temp = 'Submitted';
            } else if($row['ACTION'] == 4) {
                $temp = 'Approved';// Lvl: '.$row['APPROVAL_LEVEL'];
            } else if($row['ACTION'] == 7) {
                $temp = 'Approved';// Lvl: '.$row['APPROVAL_LEVEL'];
            } else if($row['ACTION'] == 8 && $row['USER'] == $submittedby) {
                $temp = 'Cancelled Submission';
            } else if($row['ACTION'] == 8) {
                $temp = 'Denied';// Lvl: '.$row['APPROVAL_LEVEL'];
            } else if($row['ACTION'] == 20) {
                $temp = 'Processed';
            } else {
                continue;
            }
            $response .= '<tr><td>'.Workflow::getUserName($row['USER']).'</td><td';
            if($emailMode) 
                $response .= ' style="text-align:center;padding:0 10px;"';
            $response .= '>'.$temp;
            $response .= '</td>';
            $response .= '<td>'.$row['DATE_SUBMITTED'].'</td></tr>';
            
        }
        
        $response .= '</table>';
        
        
        
        
        $response .= '<div class="clear page-break"></div>';
        
        //For processing the email click automatically
        if(isset($_GET['response']) && isset($_GET['lvl']) && $configuration == 4) {
            $tokenSuccess = (isset($_GET['tk']) && Workflow::workflowEmailTokenDecode($_GET['tk'], $submissionID));
            if(!$tokenSuccess)
                $emailclick = '<br><span style="color:red;font-weight:bold;">The email you tried using to review this form is out of date. Please review the below submission in detail.</span><br><br>';
            else if($_GET['response'] == 'approve' && $_GET['lvl'] == $approvalStatus) {
                $_SESSION['ERRMSG'] = 'approve';
                echo '<script>window.onload = function() {document.getElementById("approvelink").click();};</script>';
            } else if($_GET['response'] == 'change' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {
                    /*document.getElementById("changelink").click();*/
                    document.getElementById("add-comments").scrollIntoView();};</script>';
            else if($_GET['response'] == 'deny' && $_GET['lvl'] == $approvalStatus) {
                $_SESSION['ERRMSG'] = 'deny';
                echo '<script>window.onload = function() {document.getElementById("denylink").click();};</script>';
            }
            
            //Draw a background to prevent someone from clicking while it auto clicks.
            if($_GET['response'] == 'approve' || $_GET['response'] == 'deny') {
                $response .= '<div id="screen-blackout" onclick="closePreview();" style="display:initial;">
                    <div style="width: 500px;margin-top: 200px;margin-left: auto; margin-right: auto;
                    border: 3px solid black;background-color: rgba(220, 220, 220, 1);text-align: center;
                    font-size:25px;">';
                if($_GET['response'] == 'approve')
                    $response .= 'Approving submission # '.$submissionID;
                else if($_GET['response'] == 'deny')
                    $response .= 'Denying submission # '.$submissionID;
                /*else if($_GET['response'] == 'change')
                    $response .= 'Changing submission # '.$submissionID;*/
                $response .= '</div></div>';
            }
        }
        $response = str_replace('%EMAILCLICK%', $emailclick, $response);
        return $response;
    }
    
    
    private function findValue($resultset, $valuetofind) {
        for($i = 0; $i < count($resultset); $i++) {
            if($valuetofind == $resultset[$i][0]) {
                $value = str_replace('"', htmlentities('"'), $resultset[$i][1]);
                $value = Workflow::escapeScriptTags($value);
                return $value;
            }
        }
        
        return '';
    }
    
    
    
    /*
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
    
    /*
    View all workflows.
    */
    public function viewAllWorkflows() {
        global $wpdb;
        
        if(Workflow::loggedInUser() == 0) { 
            $_SESSION['ERRMSG'] = 'You need to log in first.';
            header('location: ?page=viewsubmissions');
            die();
        }
        
        if(Workflow::isAdmin(Workflow::loggedInUser())) {
            if(Workflow::debugMode())
                echo '<b>**DEBUG MODE - ADMIN ACCESS ENABLED**</b>';
        } else if(Workflow::debugMode()) {
            echo '<b>**DEBUG MODE - NOT VISIBLE ON THE LIVE PRODUCTION SITE**</b>';
        }
        
        $response = '';
        
        $enabled = 1;
        $draft = 0;
        if(isset($_GET['draft'])) {
            $enabled = 0;
            $draft = 1;
        } else if(isset($_GET['disabled'])) {
            $enabled = 0;
            $draft = 0;
        }
        
        $sql = "SELECT workflowform.FORMID, NAME, DRAFT, ENABLED, DATE_SAVED
                FROM workflowform
                LEFT OUTER JOIN workflowformsave ON workflowform.FORMID = workflowformsave.FORMID
                WHERE ENABLED = '$enabled' AND DRAFT = '$draft'
                ORDER BY DRAFT, NAME";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        $response .= '<form id="toggleactiveform" action="?page=edit_forms" method="POST" autocomplete="off">';
        $response .= '<table id="view-submissions"><tr><th style="width: 250px;">Form Name</th><th style="width: 450px;">Link</th><th>Date</th><th>Enabled</th><th>Copy</th></tr>';
        
        foreach($result as $row) {
            $response .= '<tr class="form-links"><td><b>'.$row['NAME'].'</b></td><td><a href="?page=';
            
            if($row['DRAFT'])
                $response .= 'createworkflow';
            else
                $response .= 'workflowentry';
            
            $response .= '&wfid='.$row['FORMID'].'">';
            
            if(!$row['DRAFT'])
                $response .= '/forms-information/p2c-forms/?page=workflowentry&wfid='.$row['FORMID'];
            else
                $response .= 'EDIT FORM '.$row['NAME'];
            $response .= '</a></td>';
            
            $response .= '<td class="center">'.($row['DATE_SAVED'] == NULL ?'' : date("Y-m-d", strtotime($row['DATE_SAVED']))).'</td>';
            
            $response .= '<td class="center">';
            
            if(!$row['DRAFT']) {
                $response .= '<input type="hidden" id="FORM'.$row['FORMID'].'" name="FORM'.$row['FORMID'].'" value="0">
                    <input type="checkbox" id="FORM'.$row['FORMID'].'" name="FORM'.$row['FORMID'].'" ';
                if($row['ENABLED'])
                    $response .= 'checked';
                $response .= '>';
            } else 
                $response .= 'DRAFT';
            $response .= '</td><td>';
            if(!$row['DRAFT'] && $row['DATE_SAVED'] != NULL)
                $response .= '<a href="?page=createworkflow&wfid='.$row['FORMID'].'&copy=1">Copy</a>';
            $response .= '</td></tr>';
        }
        $response .= '<tr><td colspan=4><input type="submit" value="Save"></td></tr>';
        $response .= '</table></form>';
        return $response;
    }
    
    public function viewSubmissionSummary($userid, $formName, $submittedby, $date, $id, $formType) {
        global $wpdb;
        $response = '';
        
        if($formType == 'both' || $formType == 'my') {
            //User submissions
            $sql = "SELECT STATUS, COUNT(STATUS) AS COUNT
                    FROM workflowformstatus
                    INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID
                    WHERE workflowformstatus.USER = '$userid'";
            
            if($formName != '') {
                $sql .= " AND (workflowform.NAME LIKE '%$formName%' OR workflowform.NAME LIKE '%$formName%') ";
            }
            if($date != '') {
                $sql .= " AND workflowformstatus.DATE_SUBMITTED = '$date' ";
            }
            if($id != '') {
                $sql .= " AND workflowformstatus.SUBMISSIONID = '$id' ";
            }
            
            $sql .= " GROUP BY workflowformstatus.STATUS";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            $saved = $approved = $input = $denied = $pending = $cancelled = 0;
            foreach($result as $row) {
                if($row['STATUS'] == 2) { //saved
                    $saved = $row['COUNT'];
                } else if($row['STATUS'] == 3) { //input required
                    $input = $row['COUNT'];
                } else if($row['STATUS'] == 4) { //approval pending
                    $pending = $row['COUNT'];
                } else if($row['STATUS'] == 7) { //approved
                    $approved = $row['COUNT'];
                } else if($row['STATUS'] == 8) { //denied
                    $denied = $row['COUNT'];
                }
            }
        }
        
        if($formType == 'both' || $formType == 'staff') {
            //Approver
            $sql = "(SELECT  workflowformstatus.STATUS,
                            COUNT(workflowformstatus.STATUS) AS COUNT,
                            '0' AS 'PROCESS',
                            '' AS 'PROCESSED'
                    FROM workflowformstatus
                    INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID 
                    LEFT JOIN employee ON workflowformstatus.USER = employee.employee_number
                    WHERE ( 
                        ( (workflowform.APPROVER_ROLE = '8' AND (STATUS_APPROVAL = '1' OR STATUS_APPROVAL = '100')
                        OR workflowform.APPROVER_ROLE2 = '8' AND (STATUS_APPROVAL = '2' OR STATUS_APPROVAL = '100')
                        OR workflowform.APPROVER_ROLE3 = '8' AND (STATUS_APPROVAL = '3' OR STATUS_APPROVAL = '100')
                        OR workflowform.APPROVER_ROLE4 = '8' AND (STATUS_APPROVAL = '4' OR STATUS_APPROVAL = '100'))
                        AND (workflowformstatus.APPROVER_DIRECT = '$userid' /*OR employee.supervisor = '$userid'*/) ) ";
                    
                    
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
            
            //Check if they were a past direct supervisor approver
            $sqlDirect = "SELECT SUBMISSIONID
                    FROM workflowsuphistory
                    WHERE USER_ID = '$userid'";
            
            $resultDirect = $wpdb->get_results($sqlDirect, ARRAY_A);
            foreach($resultDirect as $rowDirect) {
                $sql .= " OR (STATUS_APPROVAL = '100' 
                            AND workflowformstatus.SUBMISSIONID = '".$rowDirect['SUBMISSIONID']."') ";
            }
            
            $sql .= ") AND (workflowformstatus.STATUS != '2' 
                        AND workflowformstatus.STATUS != '3' 
                        AND workflowformstatus.STATUS != '10') ";
            if($formName != '') {
                $sql .= " AND (workflowform.NAME LIKE '%$formName%' OR workflowform.NAME LIKE '%$formName%') ";
            }
            if($submittedby != '') {
                $sql .= " AND (employee.first_name LIKE '%$submittedby%' OR employee.last_name LIKE '%$submittedby%') ";
            }
            if($date != '') {
                $sql .= " AND (workflowformstatus.DATE_SUBMITTED = '$date' OR workflowformstatus.DATE_SUBMITTED = '$date') ";
            }
            if($id != '') {
                $sql .= " AND workflowformstatus.SUBMISSIONID = '$id' ";
            }
            //        WHERE workflowformstatus.USER = '$userid'
            
            $sql .= "GROUP BY workflowformstatus.STATUS) UNION ALL (SELECT  workflowformstatus.STATUS,
                            COUNT(workflowformstatus.STATUS) AS COUNT,
                            '1' AS 'PROCESS',
                            workflowformstatus.PROCESSED AS 'PROCESSED'
                    FROM workflowformstatus
                    INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID 
                    LEFT JOIN employee ON workflowformstatus.USER = employee.employee_number
                    WHERE (";
            
            
            for($i = 0; $i < count($roles); $i++) {
                if($i != 0)
                    $sql .= "OR ";
                $sql .= "workflowform.PROCESSOR";
                $sql .= " = '$roles[$i]' ";
            }
            $sql .= ") ";
            
            
                    
            $sql .= "AND workflowformstatus.STATUS = '7' GROUP BY PROCESSED) ";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            $approverPending = $approverApproved = $approverDenied = $notprocessed = $processed = 0;
            foreach($result as $row) {
                if($row['STATUS'] == 4) { //approval pending
                    $approverPending = $row['COUNT'];
                } else if($row['STATUS'] == 7 && $row['PROCESS'] == 0) { //approved
                    $approverApproved = $row['COUNT'];
                } else if($row['STATUS'] == 8) { //denied
                    $approverDenied = $row['COUNT'];
                } else if($row['STATUS'] == 7 && $row['PROCESS'] == 1 && $row['PROCESSED'] == 0) { //not processed
                    $notprocessed = $row['COUNT'];
                } else if($row['STATUS'] == 7 && $row['PROCESS'] == 1 && $row['PROCESSED'] == 1) { //processed
                    $processed = $row['COUNT'];
                }
            }
        }
        
        //Display summary
        $response .= '<div id="workflow-summary">';
        $response .= '<table style="margin-left: auto; margin-right:auto;">';
        $response .= '<tr><td style="';
        if($formType == 'both')
            $response .= 'border-right: 1px solid black;';
        $response .= 'padding:10px;">';
        
        if($formType == 'both' || $formType == 'my') {
            $response .= '<a href="#workflow-summary" class="nav-option-link selected-submissions" id="user-submissions-summary" ';
            if($formType == 'both')
                $response .= 'onclick="switchRole(0);"';
            $response .= '><div class="nav-option-outside">
            <div class="nav-option-inside"><h2 style="color:inherit;">My Forms</h2>';
            
            $response .= '<table id="view-submissions" style="margin-left:auto;margin-right:auto;">';
            $response .= '<tr><th>Status</th><th>Count</th></tr>';
            $response .= '<tr><td class="left">Saved</td><td class="center">'.$saved.'</td></tr>';
            $response .= '<tr><td class="left">Input Required</td><td class="center">'.$input.'</td></tr>';
            $response .= '<tr><td class="left">Approval Pending</td><td class="center">'.$pending.'</td></tr>';
            $response .= '<tr><td class="left">Approved</td><td class="center">'.$approved.'</td></tr>';
            $response .= '<tr><td class="left">Not Approved</td><td class="center">'.$denied.'</td></tr>';
            $response .= '</table>';
            $response .= '<br><p style="color:inherit;">Click to view forms.</p></div></div></a>';
        
            $response .= '</td><td style="padding:10px;vertical-align: top;">';
        }
        
        if($formType == 'both' || $formType == 'staff') {
            $response .= '<a href="#workflow-summary" class="nav-option-link';
            if($formType == 'staff')
                $response .= ' selected-submissions';
            $response .= '" id="approver-submissions-summary" ';
            if($formType == 'both')
                $response .= 'onclick="switchRole(1);"';
            $response .= '><div class="nav-option-outside">
            <div class="nav-option-inside"><h2 style="color:inherit;">My Staff\'s Forms</h2>';
            
            $response .= '<table id="view-submissions" style="margin-left:auto;margin-right:auto;">';
            $response .= '<tr><th>Status</th><th>Count</th></tr>';
            $response .= '<tr><td class="left">Approval Pending</td><td class="center">'.$approverPending.'</td></tr>';
            $response .= '<tr><td class="left">Approved</td><td class="center">'.$approverApproved.'</td></tr>';
            $response .= '<tr><td class="left">Not Approved</td><td class="center">'.$approverDenied.'</td></tr>';
            $response .= '<tr><td class="left">Not Processed</td><td class="center">'.$notprocessed.'</td></tr>';
            $response .= '<tr><td class="left">Processed</td><td class="center">'.$processed.'</td></tr>';
            $response .= '</table>';
            $response .= '<br><p style="color:inherit;">Click to view forms.</p></div></div></a>';
        }
        $response .= '</td></tr></table>';
        
        $response .= '</div>';
        
        return $response;
    }
    
    public function viewAllSubmissions($userid, $formName, $date, $id) {
        global $wpdb;
        $LOADMOREAMT = 7; //Changes the number of submissions displayed under each section
        $response = '';
        
        if($userid == '' || $userid == '0') {
            return 'You need to be logged in to view submissions.';
        }
        
        $sql = "SELECT SUBMISSIONID, STATUS, DATE_SUBMITTED, NAME
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID
                WHERE workflowformstatus.USER = '$userid'";
        
        if($formName != '') {
            $sql .= " AND (workflowform.NAME LIKE '%$formName%' OR workflowform.NAME LIKE '%$formName%') ";
        }
        if($date != '') {
            $sql .= " AND workflowformstatus.DATE_SUBMITTED = '$date' ";
        }
        if($id != '') {
            $sql .= " AND workflowformstatus.SUBMISSIONID = '$id' ";
        }
        
        $sql .= " ORDER BY workflowformstatus.STATUS, workflowformstatus.DATE_SUBMITTED DESC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $response .= '<div id="user-submissions">';
        $response .= '<table>';
        
        $response .= '<tr><td colspan="4"><h2>My Forms</h2><br></td></tr>';
        
        $response .= '<tr><td colspan="4">
                        <div id="user-status-link2" class="workflow-status-link" onclick="switchTab(0, 2);">Saved (%SAVED%)</div>
                        <div id="user-status-link3" class="workflow-status-link workflow-status-header" onclick="switchTab(0, 3);">Input Required (%INPUTREQUIRED%)</div>
                        <div id="user-status-link4" class="workflow-status-link" onclick="switchTab(0, 4);">Approval Pending (%PENDING%)</div>
                        <div id="user-status-link7" class="workflow-status-link" onclick="switchTab(0, 7);">Approved (%APPROVED%)</div>
                        <div id="user-status-link8" class="workflow-status-link" onclick="switchTab(0, 8);">Not Approved (%DENIED%)</div>
                    </td></tr>';
        
        
        $tableHeader = '<tr class="%CLASS% submissions-header"><th style="width:50px;">ID</th><th style="width:550px;">Form Name</th><th style="width:175px;">Last Modified By</th><th style="width:125px;">Date Modified</th></tr>';
        
        
        $prevState = 1;
        $saved = $approved = $input = $denied = $pending = $cancelled = 0;
        foreach($result as $row) {
            if($row['STATUS'] != $prevState) {
                //Add status headers
                if($row['STATUS'] == 2) {
                    $response .= '<tr class="user-2 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Saved Forms</div></td></tr>'.str_replace('%CLASS%', "user-2  hide", $tableHeader);
                    $prevState = 2;
                } else if($row['STATUS'] == 3) {
                    $response .= '<tr class="user-3"><td colspan=4><div class="view-submissions-headers workflow-status-header">Forms Needing Input</div></td></tr>'.str_replace('%CLASS%', "user-3", $tableHeader);
                    $prevState = 3;
                } else if($row['STATUS'] == 4) {
                    $response .= '<tr class="user-4 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Currently Under Review</div></td></tr>'.str_replace('%CLASS%', "user-4  hide", $tableHeader);
                    $prevState = 4;
                } else if($row['STATUS'] == 7) {
                    $response .= '<tr class="user-7 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Approved Forms</div></td></tr>'.str_replace('%CLASS%', "user-7  hide", $tableHeader);
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr class="user-8 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Forms Not Approved</div></td></tr>'.str_replace('%CLASS%', "user-8  hide", $tableHeader);
                    $prevState = 8;
                } else if($row['STATUS'] == 10) {
                    $response .= '<tr class="user-10 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Cancelled</div></td></tr>'.str_replace('%CLASS%', "user-10  hide", $tableHeader);
                    $prevState = 10;
                }
                
            }
            
            $response .= '<tr class="selectedblackout ';
            
            if($row['STATUS'] == 2) {
                $response .= 'user-2 hide';
                $saved++;
            } else if($row['STATUS'] == 3) {
                $response .= 'user-3';
                $input++;
            } else if($row['STATUS'] == 4) {
                $response .= 'user-4 hide';
                $pending++;
            } else if($row['STATUS'] == 7) {
                $response .= 'user-7 hide';
                $approved++;
            } else if($row['STATUS'] == 8) {
                $response .= 'user-8 hide';
                $denied++;
            } else if($row['STATUS'] == 10) {
                $response .= 'user-10 hide';
                $cancelled++;
            }
            
            
            $response .= '" data-href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">';
            
            //Display the results
            $response .= '<td>'.$row['SUBMISSIONID'].'</td>
                <td>'.$row['NAME'].'</td>
                <td>'.WorkFlow::getLastEditedUserName($row['SUBMISSIONID']).'</td>
                <td>'.$row['DATE_SUBMITTED'].'</td>';
            $response .= '</tr>';
        }
        //Add section headers just in case they weren't created so the user knows which page they are on
        if($saved == 0) {
            $response .= '<tr class="user-2 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Saved Forms</div></td></tr>'.str_replace('%CLASS%', "user-2  hide", $tableHeader);
        }
        if($input == 0) {
            $response .= '<tr class="user-3"><td colspan=4><div class="view-submissions-headers workflow-status-header">Forms Needing Input</div></td></tr>'.str_replace('%CLASS%', "user-3", $tableHeader);
        }
        if($pending == 0) {
            $response .= '<tr class="user-4 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Currently Under Review</div></td></tr>'.str_replace('%CLASS%', "user-4  hide", $tableHeader);
        }
        if($approved == 0) {
            $response .= '<tr class="user-7 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Approved Forms</div></td></tr>'.str_replace('%CLASS%', "user-7  hide", $tableHeader);
        }
        if($denied == 0) {
            $response .= '<tr class="user-8 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Forms Not Approved</div></td></tr>'.str_replace('%CLASS%', "user-8  hide", $tableHeader);
        } 
        if($cancelled == 0) {
            $response .= '<tr class="user-10 hide"><td colspan=4><div class="view-submissions-headers workflow-status-header">Cancelled</div></td></tr>'.str_replace('%CLASS%', "user-10  hide", $tableHeader);
        }
        
        $response .= '</table>';
        
        $response .= '</div>';
        
        
        $response = str_replace('%SAVED%', $saved, $response);
        $response = str_replace('%INPUTREQUIRED%', (($input == 0) ? $input : '<b>'.$input.'</b>'), $response);
        $response = str_replace('%PENDING%', $pending, $response);
        $response = str_replace('%APPROVED%', $approved, $response);
        $response = str_replace('%DENIED%', $denied, $response);
        
        return $response;
    }
    
    public function viewAllSubmissionsAsApprover($userid, $formName, $submittedby, $date, $id, $viewAll) {
        global $wpdb;
        $LOADMOREAMT = 7; //Changes the number of submissions displayed under each section
        $response = '';
        
        if($userid == '' || $userid == '0') {
            return 'You need to be logged in to view submissions.';
        }
        
        $searchFilter = "";
        if($formName != '') {
            $searchFilter .= " AND (workflowform.NAME LIKE '%$formName%') ";
        }
        if($submittedby != '') {
            $searchFilter .= " AND (employee.first_name LIKE '%$submittedby%' OR employee.last_name LIKE '%$submittedby%') ";
        }
        if($date != '') {
            $searchFilter .= " AND (workflowformstatus.DATE_SUBMITTED = '$date') ";
        }
        if($id != '') {
            $searchFilter .= " AND workflowformstatus.SUBMISSIONID = '$id' ";
        }
        
        $sql = "("."SELECT  workflowform.NAME, 
                        workflowform.APPROVER_ROLE, 
                        workflowform.APPROVER_ROLE2, 
                        workflowform.APPROVER_ROLE3, 
                        workflowform.APPROVER_ROLE4, 
                        workflowform.PROCESSOR, 
                        '' AS PROCESSED, 
                        workflowformstatus.SUBMISSIONID, 
                        workflowformstatus.STATUS,
                        workflowformstatus.STATUS_APPROVAL,
                        workflowformstatus.DATE_SUBMITTED,
                        CONCAT(employee.last_name, ', ', employee.first_name) AS USERNAME,
                        '0' AS 'PROCESS'
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID 
                LEFT JOIN employee ON workflowformstatus.USER = employee.employee_number
                WHERE ( 
                    ( (workflowform.APPROVER_ROLE = '8' AND (STATUS_APPROVAL = '1' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE2 = '8' AND (STATUS_APPROVAL = '2' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE3 = '8' AND (STATUS_APPROVAL = '3' OR STATUS_APPROVAL = '100')
                    OR workflowform.APPROVER_ROLE4 = '8' AND (STATUS_APPROVAL = '4' OR STATUS_APPROVAL = '100'))
                    AND (workflowformstatus.APPROVER_DIRECT = '$userid' /*OR employee.supervisor = '$userid'*/) ) ";
                
                
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
        
        //Check if they were a past direct supervisor approver
        $sqlDirect = "SELECT SUBMISSIONID
                FROM workflowsuphistory
                WHERE USER_ID = '$userid'";
        
        $resultDirect = $wpdb->get_results($sqlDirect, ARRAY_A);
        foreach($resultDirect as $rowDirect) {
            $sql .= " OR (STATUS_APPROVAL = '100' 
                        AND workflowformstatus.SUBMISSIONID = '".$rowDirect['SUBMISSIONID']."') ";
        }
        
        //Check if they are an admin and should be able to see all forms
        if($viewAll) {
            $sql .= " OR 1 = 1 ";
        }
        
        //$sql .= "OR STATUS_APPROVAL = '100' AND (";
        
        $sql .= ") AND (workflowformstatus.STATUS != '2' 
                    AND workflowformstatus.STATUS != '3' 
                    AND workflowformstatus.STATUS != '10') ";
        $sql .= $searchFilter;
        //        WHERE workflowformstatus.USER = '$userid'
        $sql .= ")";
        
        $sql .= " UNION ALL (SELECT  workflowform.NAME, 
                        '' AS APPROVER_ROLE, 
                        '' AS APPROVER_ROLE2, 
                        '' AS APPROVER_ROLE3, 
                        '' AS APPROVER_ROLE4, 
                        workflowform.PROCESSOR, 
                        workflowformstatus.PROCESSED, 
                        workflowformstatus.SUBMISSIONID, 
                        workflowformstatus.STATUS,
                        workflowformstatus.STATUS_APPROVAL,
                        workflowformstatus.DATE_SUBMITTED,
                        CONCAT(employee.first_name, ' ', employee.last_name) AS USERNAME,
                        '1' AS 'PROCESS'
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID 
                LEFT JOIN employee ON workflowformstatus.USER = employee.employee_number
                WHERE ((";
        
        
        for($i = 0; $i < count($roles); $i++) {
            if($i != 0)
                $sql .= "OR ";
            $sql .= "workflowform.PROCESSOR";
            $sql .= " = '$roles[$i]' ";
        }
        $sql .= ") ";
        
        //Check if they are an admin and should be able to see all forms
        if($viewAll) {
            $sql .= " OR 1 = 1 ";
        }
        
        $sql .= ") ";
        
        $sql .= $searchFilter;
                
        $sql .= "AND workflowformstatus.STATUS = '7') 
                ORDER BY PROCESS, PROCESSED, STATUS, DATE_SUBMITTED DESC, NAME ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $response .= '<div id="approver-submissions">';
        $response .= '<table>';
        
        $response .= '<tr><td colspan="5"><h2>'.($viewAll == 1 ? 'All' : 'My').' Staff\'s Forms</h2><br></td></tr>';
        
        $response .= '<tr><td colspan="5">
                        <div id="approver-status-link4" class="workflow-status-link workflow-status-header" onclick="switchTab(1, 4);">Approval Pending (%PENDING%)</div>
                        <div id="approver-status-link7" class="workflow-status-link" onclick="switchTab(1, 7);">Approved (%APPROVED%)</div>
                        <div id="approver-status-link8" class="workflow-status-link" onclick="switchTab(1, 8);">Not Approved (%DENIED%)</div>
                        <div id="approver-status-link9" class="workflow-status-link" onclick="switchTab(1, 9);">To Be Processed (%NOTPROCESSED%)</div>
                        <div id="approver-status-link10" class="workflow-status-link" onclick="switchTab(1, 10);">Processed (%PROCESSED%)</div>
                    </td></tr>';

       
        $tableHeader = '<tr class="%CLASS% submissions-header"><th style="width:50px;">ID</th><th style="width:150px;">Staff Name</th><th style="width:450px;">Form Name</th><th style="width:150px;">Last Modified By</th><th style="width:125px;">Date Modified</th></tr>';
        
        $prevState = 1;
        $processorState = -1;
        $approved = $denied = $pending = $notprocessed = $processed = 0;
        foreach($result as $row) {
            if($row['STATUS'] != $prevState && !$row['PROCESS']) {
                if($row['STATUS'] == 4) {
                    $response .= '<tr class="approver-4"><td colspan=5><div class="view-submissions-headers workflow-status-header">Submissions Requiring Approval</div></td></tr>'.str_replace('%CLASS%', "approver-4", $tableHeader);
                    $prevState = 4;
                } else if($row['STATUS'] == 7 && !$row['PROCESS']) {
                    $response .= '<tr class="approver-7 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Approved Forms</div></td></tr>'.str_replace('%CLASS%', "approver-7  hide", $tableHeader);
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr class="approver-8 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Forms Not Approved</div></td></tr>'.str_replace('%CLASS%', "approver-8  hide", $tableHeader);
                    $prevState = 8;
                }
            } else if($processorState < 1 && $row['PROCESS'] && $row['STATUS'] == 7) {
                if($processorState == -1 && !$row['PROCESSED']) {
                    $response .= '<tr class="approver-9 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Forms To Be Processed</div></td></tr>'.str_replace('%CLASS%', "approver-9  hide", $tableHeader);
                    $processorState = 0;
                } else if(($processorState == 0 || $processorState == -1) && $row['PROCESSED']) {
                    $response .= '<tr class="approver-10 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Processed</div></td></tr>'.str_replace('%CLASS%', "approver-10  hide", $tableHeader);
                    $processorState = 1;
                }
            }
            
            $response .= '<tr class="selectedblackout ';
            if($row['STATUS'] == 4) {
                $response .= 'approver-4';
                $pending++;
            } else if($row['STATUS'] == 7 && !$row['PROCESS']) {
                $response .= 'approver-7 hide';
                $approved++;
            } else if($row['STATUS'] == 8) {
                $response .= 'approver-8 hide';
                $denied++;
            } else if($row['STATUS'] == 7 && $row['PROCESS'] && !$row['PROCESSED']) {
                $response .= 'approver-9 hide';
                $notprocessed++;
            } else if($row['STATUS'] == 7 && $row['PROCESS'] && $row['PROCESSED']) {
                $response .= 'approver-10 hide';
                $processed++;
            }
            
            $response .= '" data-href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">';
            $response .=  '<td style="width:50px;">'.$row['SUBMISSIONID'].'</td>
                            <td style="width:150px;">'.$row['USERNAME'].'</td>
                            <td style="width:450px;">'.$row['NAME'].'</td>
                            <td style="width:150px;">'.WorkFlow::getLastEditedUserName($row['SUBMISSIONID']).'</td>
                            <td style="width:125px;">'.$row['DATE_SUBMITTED'].'</td>';
            $response .= '</tr>';
        }
        //Add section headers just in case they weren't created so the user knows which page they are on
        if($pending == 0) {
            $response .= '<tr class="approver-4"><td colspan=5><div class="view-submissions-headers workflow-status-header">Submissions Requiring Approval</div></td></tr>'.str_replace('%CLASS%', "approver-4", $tableHeader);
        }
        if($approved == 0) {
            $response .= '<tr class="approver-7 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Approved Forms</div></td></tr>'.str_replace('%CLASS%', "approver-7  hide", $tableHeader);
        }
        if($denied == 0) {
            $response .= '<tr class="approver-8 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Forms Not Approved</div></td></tr>'.str_replace('%CLASS%', "approver-8  hide", $tableHeader);
        }
        if($notprocessed == 0) {
            $response .= '<tr class="approver-9 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Forms To Be Processed</div></td></tr>'.str_replace('%CLASS%', "approver-9  hide", $tableHeader);
        }
        if($processed == 0) {
            $response .= '<tr class="approver-10 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Processed</div></td></tr>'.str_replace('%CLASS%', "approver-10  hide", $tableHeader);
        }
        
        
        $response .= '</table></div>';
        
        $response = str_replace('%PENDING%', (($pending == 0) ? $pending : '<b>'.$pending.'</b>'), $response);
        $response = str_replace('%APPROVED%', $approved, $response);
        $response = str_replace('%DENIED%', $denied, $response);
        $response = str_replace('%NOTPROCESSED%', $notprocessed, $response);
        $response = str_replace('%PROCESSED%', $processed, $response);
        
        return $response;
    }
    
    public function sendEmail($submissionID) {
        require_once("PHPMailer-master/PHPMailerAutoload.php");
        //$uniqueId = WorkFlow::workflowEmailToken($submissionID);//DEBUG
        
        $workflow = new Workflow();
        
        global $wpdb;
        $response = '';
        
        $sql = "SELECT STATUS, APPROVER_DIRECT, USER, workflowformstatus.FORMID, COMMENT, MISC_CONTENT, workflowform.NAME,
                APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, STATUS_APPROVAL, BEHALFOF, PROCESSOR
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
        $processor = $row['PROCESSOR'];
        
        $behalfOf = ($row['BEHALFOF'] =! NULL ? $row['BEHALFOF'] : '');
        
        if(!($status == 4 || $status == 7 || $status == 8 || $status == 3))
            return;
        //Check to make sure the user didn't just retract their own submission
        if($status == 3) {
            $sql = "SELECT USER
                    FROM workflowformhistory
                    WHERE SUBMISSION_ID = '$submissionID'
                    ORDER BY DATE_SUBMITTED DESC
                    LIMIT 1";
            $retractCheck = $wpdb->get_results($sql, ARRAY_A);
            if($retractCheck) {
                //Don't send an email if the user retracted their submission
                if($retractCheck[0]['USER'] == $userid) 
                    return;
            } 
        }
        
        //Find out if it is a direct supervisor submission or not
        $role = $approvers[$approvalStatus - 1];
        $supNext = $approvers[$approvalStatus];
        
        //check if this submission was rejected and needs further input
        if($approvalStatus == 0 && $status == 3) { 
            $sql = "SELECT employee.employee_number AS MEMBER, employee.user_login, user_email, '1' AS EMAIL_ON
                    FROM employee  
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login 
                    WHERE employee.employee_number = '$userid'";
        } else if($role != 8 && $role != '') {
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
        //var_dump($emailRecepients);
        
        $recepients = array();
        $tempRec = '';
        foreach($emailRecepients as $row) {
            if($row['user_email'] != '') {
                $tempRec .= $row['user_email'].' SEND EMAIL: '.$row['EMAIL_ON'].'<br>';
                $recepients[] = array($row['MEMBER'], $row['user_email'], $row['EMAIL_ON'], 0);
            }
        }
        
        if($status == 7 && $processor != NULL) {
            $sql = "SELECT MEMBER, employee.user_login, user_email, EMAIL_ON
                    FROM workflowrolesmembers
                    INNER JOIN employee ON employee.employee_number = workflowrolesmembers.MEMBER
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login
                    WHERE ROLEID = '$processor'
                    ORDER BY MEMBER";
            $emailRecepients = $wpdb->get_results($sql, ARRAY_A);
            foreach($emailRecepients as $row) {
                if($row['user_email'] != '') {
                    $tempRec .= $row['user_email'].' SEND EMAIL: '.$row['EMAIL_ON'].' PROCESSOR: ON<br>';
                    $recepients[] = array($row['MEMBER'], $row['user_email'], $row['EMAIL_ON'], 1);
                }
            }
        }
        
        $mainTemplate = 
        '<table style="width:100%;font-family: Verdana,Geneva,sans-serif;" border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td>
        <!-- Header -->
        <img src="http://staff.powertochange.org/wp-content/images/WorkflowFormHeader.png" width="100%"  alt="Workflow Submission" border="0" />
        <!--<img src="http://staff.powertochange.org/wp-content/images/health-report-dots.png" width="100%"  alt="Workflow Form dots" border="0" />-->
        </td>
        </tr>
        <tr style="margin-top:40px;"><td>
        %EMAILDESIGN%
        </td></tr>
        <tr>
        <td>
        <!-- Footer -->
        <img src="http://staff.powertochange.org/wp-content/images/WorkflowFormFooter.png" width="100%" style="margin-top: 20px" alt="Report Footer"/>
        </td>
        </tr>
        </table>';
        
        
        
        
        
        if($status == 4) {
            $template = '
                <p style="margin-top:30px;">'.Workflow::getUserName($userid).' has submitted a form for your approval. The submission ID # is '.$submissionID.'.</p>
                
                
                
            '.$workflow->loadWorkflowEntry($formID, 4, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, ($supNext != ''), $behalfOf, 1, ($supNext == 8)).
            '<br>
            
            <img src="http://staff.powertochange.org/wp-content/images/health-report-dots.png" width="100%"  alt="Workflow Form dots" border="0" /><br>';
        } else if($status == 3) {
            $template = '
                <h2 style="margin-top:30px;">You have a submission requiring further input!</h2>
                <p>'.Workflow::getUserName($userid).', you previously submitted the form: '.$formName.' and it has been reviewed and requires further action. The submission ID # is '.$submissionID.'.</p>
                
            '.$workflow->loadWorkflowEntry($formID, 3, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, ($supNext != ''), $behalfOf, 1, ($supNext == 8)).
            '<br>
            <img src="http://staff.powertochange.org/wp-content/images/health-report-dots.png" width="100%"  alt="Workflow Form dots" border="0" />';
        } else {
            $templateFinished = '
                <h2 style="margin-top:30px;">You have a form that has been reviewed!</h2>
                <p><b>Form: </b>'.$formName.'  <b>Submission ID #</b> '.$submissionID.'.</p>'.
                $workflow->loadWorkflowEntry($formID, $status, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, 0, $behalfOf, 1, 0).'<br>
                <img src="http://staff.powertochange.org/wp-content/images/health-report-dots.png" width="100%"  alt="Workflow Form dots" border="0" />';
            $processedTemplate = '
                <h2 style="margin-top:30px;">You have a submission that requires processing.</h2>
                
                <p><b>Form: </b>'.$formName.'  <b>Submission ID #</b> '.$submissionID.'  <b>Submitted By:</b> '.
                Workflow::getUserName($userid).'.</p>'.
                $workflow->loadWorkflowEntry($formID, 9, $submissionID, $misc_content, $commenttext, $userid, 
                    9, $approvalStatus, 0, $behalfOf, 1, 0).'<br>
                <img src="http://staff.powertochange.org/wp-content/images/health-report-dots.png" width="100%"  alt="Workflow Form dots" border="0" />';
        }
        if(Workflow::debugMode()) {
            $template .= '<h3>DEBUG: Email List (Members in this role)</h3> '.$tempRec.'<br>';
            $templateFinished .= '<h3>Email List</h3> '.$tempRec.'<br>';
            $processedTemplate .= '<h3>Email List</h3> '.$tempRec.'<br>';
        }
        
        
        for($i = 0; $i < count($recepients); $i++) {
            if($recepients[$i][2] == 1) { //if sending of emails is checked in the email settings
                if($status == 4) {
                    $modifiedTemplate = $template;
                    $modifiedTemplate = str_replace('%EMAILNAME%', Workflow::getUserName($recepients[$i][0]), $modifiedTemplate);
                    $body = str_replace('%EMAILDESIGN%', $modifiedTemplate, $mainTemplate);
                } else if($status == 3) {
                    $modifiedTemplate = $template;
                    $modifiedTemplate = str_replace('%EMAILNAME%', Workflow::getUserName($recepients[$i][0]), $modifiedTemplate);
                    $body = str_replace('%EMAILDESIGN%', $modifiedTemplate, $mainTemplate);
                } else if($recepients[$i][3] == 1) {
                    $body = str_replace('%EMAILDESIGN%', $processedTemplate, $mainTemplate);
                } else {
                    $body = str_replace('%EMAILDESIGN%', $templateFinished, $mainTemplate);
                }
                
                
                $mail = new PHPMailer;
                $mail->isSMTP();          // Set mailer to use SMTP
                $mail->Host = 'smtp.powertochange.org'; // Specify main and backup SMTP servers
                //$mail->SMTPAuth = true;                            // Enable SMTP authentication
                //$mail->SMTPDebug = 2;
                //$mail->SMTPSecure = 'ssl';       // Enable TLS encryption, ssl also accepted
                $mail->Port = 25;

                $mail->From = 'p2cforms-no-reply@p2c.com';
                $mail->FromName = 'P2C Forms';
                
                if(Workflow::debugMode()) {
                    if(Workflow::debugMode() == 2)
                        $mail->AddAddress('matthew.campbell@p2c.com'); 
                    $mail->AddBCC('gerald.becker@p2c.com');
                } else {
                    $mail->AddAddress($recepients[$i][1]); //Sends email to the actual person
                }
                

                $mail->IsHTML(true);
                
                if($status == 3)
                    $mail->Subject = $formName.' submission requiring further input - Submission ID # '.$submissionID;
                else if($status == 4)
                    $mail->Subject = $formName.' email from '.Workflow::getUserName($userid).' - Submission ID # '.$submissionID;
                else if($status == 7 && $recepients[$i][3] == 1)
                    $mail->Subject = $formName.' email from '.Workflow::getUserName($userid).' - Submission ID # '.$submissionID;
                else
                    $mail->Subject = 'Your '.$formName.' submission has been reviewed - Submission ID # '.$submissionID;
                 
                                                            
                $mail->Body = $body;
                //echo 'DEBUG: Trying to send an email to : '.$recepients[$i][1].'<br>';
                $mail->Send();
            }
        }
    }
    
    /*Returns submissions for a given form that a user is an approver for.
     *Only submissions in progress or that have been completed will appear.
     */
    public function viewAllSubmissionsGrid($userid, $form, $viewAll) {
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
                        workflowform.PROCESSOR, 
                        workflowformstatus.PROCESSED, 
                        workflowformstatus.SUBMISSIONID, 
                        workflowformstatus.STATUS,
                        workflowformstatus.STATUS_APPROVAL,
                        workflowformstatus.DATE_SUBMITTED,
                        workflowformstatus.COMMENT,
                        CONCAT(employee.first_name, ' ', employee.last_name) AS USERNAME
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID 
                LEFT JOIN employee ON workflowformstatus.USER = employee.employee_number
                WHERE workflowform.FORMID = '$form' AND ( ";
        
        $roles = Workflow::getRole($userid);
        
        for($x = 1; $x <= 4; $x++) {
            if($x != 1)
                $sql .= " OR ";
            $sql .= " (STATUS_APPROVAL = '".$x."' OR STATUS_APPROVAL = '100') AND (";
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
        
        //Check if they are an admin and should be able to see all forms
        if($viewAll) {
            $sql .= " OR 1 = 1 ";
        }
        
        $sql .= ") AND (workflowformstatus.STATUS != '2' 
                    AND workflowformstatus.STATUS != '3' 
                    AND workflowformstatus.STATUS != '10') ";
        $sql .= "ORDER BY STATUS, PROCESSED, DATE_SUBMITTED DESC, NAME ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        //If there are no results the user has access to
        if(!$result) {
            return 'There are no submissions to view.';
        }
        
        $response .= '<div id="approver-submissions">';
        $response .= '<table id="grid-submissions">';
        
        $completeResults;
        //Figure out which fields contain user input data. Create the array to hold that data
        $formFields;
        $sql = "SELECT  wdet.TYPE,
                        wdet.POSITION,
                        wdet.FIELDID
                FROM workflowformdetails wdet
                WHERE wdet.FORMID = '$form'
                    AND wdet.TYPE IN ('0', '4', '5', '6', '7', '13', '2', '14', '15')
                GROUP BY wdet.FIELDID
                ORDER BY wdet.POSITION";
        $resultFields = $wpdb->get_results($sql, ARRAY_A);
        
        $tableHeader = '<tr class="submissions-header"><th>ID</th><th>Date</th><th>Employee</th><th>Status</th>';
        foreach($resultFields as $row) {
            $formFields[$row['FIELDID']] = '';
            $tableHeader .= '<th>'.$row['FIELDID'].'</th>';
        }
        $tableHeader .= '<th style="min-width:150px;">Comments</th>';
        $tableHeader .= '</tr>';
        
        //Assemble all the data in an array so we can display it how we want later
        foreach($result as $k=>$row) {
            $completeResults[] = $row;
            $completeResults[$k]['data'] = $formFields;
            
            $sql = "SELECT  wsub.VALUE,
                            wstat.SUBMISSIONID, 
                            wstat.STATUS,
                            wstat.STATUS_APPROVAL,
                            wstat.DATE_SUBMITTED,
                            wdet.TYPE,
                            wdet.POSITION,
                            wdet.FIELDID
                    FROM workflowformsubmissions wsub
                    INNER JOIN workflowformstatus wstat ON wsub.SUBMISSIONID = wstat.SUBMISSIONID
                    INNER JOIN workflowformdetails wdet ON wsub.FIELDID = wdet.FIELDID AND wstat.FORMID = wdet.FORMID
                    WHERE wstat.FORMID = '$form' AND wsub.SUBMISSIONID = '$row[SUBMISSIONID]'";
              
            $subresult = $wpdb->get_results($sql, ARRAY_A);
            
            foreach($subresult as $subrow) {
                $completeResults[$k]['data'][$subrow['FIELDID']] = $subrow['VALUE'];
            }
        }
        
        //Display the results
        $response .= $tableHeader;
        foreach($completeResults as $row) {
            $response .= '<tr>';
            $response .= '<td>'.$row['SUBMISSIONID'].'</td>';
            $response .= '<td>'.$row['DATE_SUBMITTED'].'</td>';
            $response .= '<td>'.$row['USERNAME'].'</td>';
            $response .= '<td>';
            if($row['STATUS'] == '4')
                $response .= 'In Progress - Level '.$row['STATUS_APPROVAL'];
            else if($row['STATUS'] == '7')
                $response .= 'Approved';
            $response .= '</td>';
            
            foreach($resultFields as $cell) {
                $response .= '<td';
                if($cell['TYPE'] == '15')
                    $response .= ' style="font-size:10px;min-width:200px;"';
                $response .= '>';
                if($cell['TYPE'] == '14')
                    $response .= '<a href="'.$this->linkAddress.'/wp-content/uploads/p2cforms/';
                $response .= $row['data'][$cell['FIELDID']];
                if($cell['TYPE'] == '14')
                    $response .= '" target="blank">'.$row['data'][$cell['FIELDID']].'</a>';
                $response .= '</td>';
            }
            $response .= '<td style="font-size:10px;">'.$row['COMMENT'].'</td>';
            $response .= '</tr>';
        }
        $response .= '</table></div>';
        
        return $response;
    }
    
    /*Returns a list of forms that the user is an approver for.*/
    public function viewAllFormsWithAccess($userid, $viewAll) {
        global $wpdb;
        $sql = "SELECT  workflowform.FORMID,
                        workflowform.NAME, 
                        workflowform.APPROVER_ROLE, 
                        workflowform.APPROVER_ROLE2, 
                        workflowform.APPROVER_ROLE3, 
                        workflowform.APPROVER_ROLE4, 
                        workflowform.PROCESSOR,
                        workflowform.ENABLED
                FROM workflowform
                WHERE ( ";
                
        $roles = Workflow::getRole($userid);
        
        for($x = 1; $x <= 4; $x++) {
            if($x != 1)
                $sql .= "OR (";
            else
                $sql .= " (";
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
        //Check if they are an admin and should be able to see all forms
        if($viewAll) {
            $sql .= " OR 1 = 1 ";
        }
        $sql .= ") ORDER BY ENABLED DESC, NAME";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $response .= '<div id="approver-submissions">';
        $response .= '<table id="grid-submissions">';
        $response .= '<tr class="submissions-header"><th>Form Name</th><th>Form ID</th><th>Approval 1</th>
            <th>Approval 2</th><th>Approval 3</th><th>Approval 4</th><th>Processor</th><th>Enabled</th></tr>';
        
        foreach($result as $row) {
            $response .= '<tr class="selectedblackout ';
            $response .= 'approver-4';
            $response .= '" data-href="?page=viewsubmissionsbyform&form='.$row['FORMID'].'">';
            $response .=  '<td>'.$row['NAME'].'</td>
                            <td>'.$row['FORMID'].'</td>
                            <td>'.$row['APPROVER_ROLE'].'</td>
                            <td>'.$row['APPROVER_ROLE2'].'</td>
                            <td>'.$row['APPROVER_ROLE3'].'</td>
                            <td>'.$row['APPROVER_ROLE4'].'</td>
                            <td>'.$row['PROCESSOR'].'</td>
                            <td>'.$row['ENABLED'].'</td>';
            $response .= '</tr>';
        }
        $response .= '</table></div>';
        return $response;
    }
    
    public function __toString() {
        return $this->$name;
    }
    
    public function getForm() {
        //$content = '<div><h2>This is a test.</h2></div>';
        
        //return $content;
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
    
    public static function impersonateEmployee($num) {
        if(isset($_SESSION['impersonate']) && $_SESSION['impersonate'] == 1 && $num == $_SESSION['impersonateuser']) {
            echo '<script>alert("You are actually the person you are trying to impersonate.");</script>';
        } else {
            $_SESSION['impersonate'] = 1;
            $_SESSION['impersonateuser'] = $num;
            
            global $wpdb;
            
            $sql = "SELECT user_login 
                    FROM employee 
                    WHERE employee_number = '".$_SESSION['impersonateuser']."'";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            if(count($result) == 1) {
                $row = $result[0];
                $_SESSION['impersonateusername'] = Workflow::getUserName($num);//$row['user_login'];
            } else {
                $_SESSION['impersonateusername'] = 'UserName not found';
            }
            
            echo '<script>alert("You are now trying to impersonate.'.$_SESSION['impersonateuser'].' - '.$_SESSION['impersonateusername'].'");</script>';
        }
        
    }
    
    public static function stopImpersonateEmployee() {
        unset($_SESSION['impersonate']);
        unset($_SESSION['impersonateuser']);
        unset($_SESSION['impersonateusername']);
        echo '<script>alert("You are now logged in as yourself.");</script>';
    }
    
    public static function loggedInUser() {
        //Need to reference the global variable
        global $currentUserEmployeeNum;
        
        // Check if we are impersonating someone else
        if(Workflow::debugMode() && isset($_SESSION['impersonate']) && $_SESSION['impersonate'] == 1) {
            return $_SESSION['impersonateuser'];
        }
        // Check if the current employee number has already been looked up and cached - this saves from having to look up in the db again
        else if ($currentUserEmployeeNum) {
            //echo '<script> alert("current emp successful");</script>'; //DEBUG
            return $currentUserEmployeeNum;
        }
        // If none of those are true, we need to look up the employee number of the current person and for this page load
        else {
            //echo '<script> alert("resorting to fallback");</script>'; //DEBUG
            //Give them no access unless they are found
            $currentUserEmployeeNum = 0;
            
            global $wpdb;
            $currentUserId = wp_get_current_user()->id;
            
            $sql = "SELECT employee.employee_number FROM employee 
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login
                    WHERE wp_users.ID = '$currentUserId'";
            
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            if(count($result) == 1) {
                $row = $result[0];
                $currentUserEmployeeNum = $row['employee_number'];
            } 
            return $currentUserEmployeeNum;
        }
    }
    
    public static function loggedInUserName() {
        // If we are impersonating someone, return the name stored in the session
        if(Workflow::debugMode() && isset($_SESSION['impersonate']) && $_SESSION['impersonate'] == 1) {
            //return $_SESSION['impersonateusername'];
            return Workflow::getUserName(Workflow::loggedInUser());
        } else {
            // Otherwise, just return the current user login
            //return wp_get_current_user()->user_login;
            return Workflow::getUserName(Workflow::loggedInUser());
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
    
    public function updateForms($formID, $enabled) {
        global $wpdb;
        $sql = "UPDATE workflowform 
                SET ENABLED = '$enabled'
                WHERE (FORMID) = ('$formID')";
        
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
    
    public static function getLastEditedUserName($submissionID) {
        global $wpdb;
        $response = '';
        
        $sql = "SELECT USER, ACTION
                FROM workflowformhistory
                WHERE SUBMISSION_ID = '$submissionID'
                ORDER BY DATE_SUBMITTED DESC
                LIMIT 1";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $lastAction = '';
        if(count($result) == 1) {
            $lastAction = Workflow::getUserName($result[0]['USER']);
        }
        
        return $lastAction;
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
    
    public static function getMultipleDirectApprovers($userid) {
        global $wpdb;
        $approver = 0;
        
        $sql = "SELECT supervisor
                FROM employee
                WHERE employee_number = '$userid'
                
                UNION 
                
                SELECT manager_employee_number as supervisor
                FROM employee_manager
                WHERE employee_number = '$userid'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        return $result;
    }
    
    
    public function getNextRoleName($currentLevel, $hasAnotherApproval, $formID, $resolveName, $sbid, $current = 0) {
        if(!$hasAnotherApproval && $current == 0 || $currentLevel == 100)
            return 'Form Complete';
        global $wpdb;
        $response = '';
        
        $sql = "SELECT workflowroles.NAME, ROLEID
                FROM workflowform ";
        
        if($currentLevel == 0 || $current == 1 && $currentLevel == 1) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE = workflowroles.ROLEID ";
        } else if($currentLevel == 1 || $current == 1 && $currentLevel == 2) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE2 = workflowroles.ROLEID ";
        } else if($currentLevel == 2 || $current == 1 && $currentLevel == 3) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE3 = workflowroles.ROLEID ";
        } else if($currentLevel == 3 || $current == 1 && $currentLevel == 4) {
            $sql .= "INNER JOIN workflowroles ON workflowform.APPROVER_ROLE4 = workflowroles.ROLEID ";
        } 
        $sql .= "WHERE FORMID = '$formID'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $response = $result[0]['NAME'];
            
            
            if($resolveName && $result[0]['ROLEID'] == 8) {
                $sql = "SELECT APPROVER_DIRECT FROM workflowformstatus WHERE SUBMISSIONID = '$sbid'";
                $result = $wpdb->get_results($sql, ARRAY_A);
                if(count($result) == 1) 
                    $response = Workflow::getUserName($result[0]['APPROVER_DIRECT']);
            }
        }
        
        
        
        
        return $response;
    }
    
    public function getAllUsers() {
        global $wpdb;
        $values = array();
        
        $sql = "SELECT employee_number, CONCAT(first_name, ' ', last_name) AS FULLNAME
                FROM employee
                ORDER BY FULLNAME ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        foreach($result as $row) {
            $values[] = array($row['employee_number'], $row['FULLNAME']);
        }
        
        return $values;
    }
    
    private function fieldWidth($size) {
        if($size <= 25)
            return 'field1to25';
        else if($size <= 50)
            return 'field26to50';
        else if($size <= 75)
            return 'field51to75';
        else if($size <= 100)
            return 'field76to100';
        else if($size <= 125)
            return 'field101to125';
        else if($size <= 150)
            return 'field126to150';
        else if($size <= 175)
            return 'field151to175';
        else if($size <= 200)
            return 'field176to200';
        else if($size <= 225)
            return 'field201to225';
        else if($size <= 250)
            return 'field226to250';
        else if($size <= 275)
            return 'field251to275';
        else if($size <= 300)
            return 'field276to300';
        else if($size <= 325)
            return 'field301to325';
        else if($size <= 350)
            return 'field326to350';
        else if($size <= 375)
            return 'field351to375';
        else if($size <= 400)
            return 'field376to400';
        else if($size <= 425)
            return 'field401to425';
        else if($size <= 450)
            return 'field426to450';
        else if($size <= 475)
            return 'field451to475';
        else if($size <= 500)
            return 'field476to500';
        else if($size <= 525)
            return 'field501to525';
        else if($size <= 550)
            return 'field526to550';
        else if($size <= 575)
            return 'field551to575';
        else if($size <= 600)
            return 'field576to600';
        else if($size <= 650)
            return 'field601to650';
        else if($size <= 700)
            return 'field651to700';
        else if($size <= 750)
            return 'field701to750';
        else if($size <= 800)
            return 'field751to800';
        else if($size <= 850)
            return 'field801to850';
        else if($size <= 900)
            return 'field851to900';
        else if($size <= 950)
            return 'field901to950';
        else if($size >= 951)
            return 'field951to1000';
        
        return '';
    }
    
    
    private function base64_url_encode($input) {
        return strtr(base64_encode($input), '+/=', '-_~');
    }

    private function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_~', '+/='));
    }
    
    private function workflowEmailToken($submissionID) {
        //Encode the current time with the id to make sure the email will only work with this submission state.
        $now = new DateTime();
        $uniqueId = Workflow::base64_url_encode($submissionID.':'.$now->getTimestamp());
        
        
        global $wpdb;
        
        $sql = "UPDATE workflowformstatus 
                SET UNIQUE_TOKEN = '$uniqueId' 
                WHERE SUBMISSIONID = '$submissionID'";
        
        $result = $wpdb->query($sql);
        
        if($result)
            return $uniqueId;
        else 
            return 0;
    }
    
    private function workflowEmailTokenDecode($uniqueId, $submissionID) {
        global $wpdb;
        
        $sql = "SELECT UNIQUE_TOKEN
                FROM workflowformstatus 
                WHERE SUBMISSIONID = '$submissionID'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        if($result)
            return ($result[0]['UNIQUE_TOKEN'] == $uniqueId);
        else
            return 0;
    }

    /*Checks if a developer is debugging the workflow app. Should return 0 in the production server.*/
    public static function debugMode() {
        return get_option( 'workflowdebug' , 0 );
    }
    
    public function escapeScriptTags($string) {
        $string = str_replace("<script", htmlentities("<script"), $string);
        $string = str_replace("</script", htmlentities("</script"), $string);
        return $string;
    }
    
    private function submissionApprover($sbid, $loggedInUser, $role1, $role2, $role3, $role4, $aprDirect) {
        global $wpdb;
        if(Workflow::hasRoleAccess($loggedInUser, $role1) 
            || Workflow::hasRoleAccess($loggedInUser, $role2)
            || Workflow::hasRoleAccess($loggedInUser, $role3)
            || Workflow::hasRoleAccess($loggedInUser, $role4)) 
                return 1;
        
        //If they don't have normal access check if they are a supervisor
        if($role1 == 8 || $role2 == 8 || $role3 == 8 || $role4 == 8) 
            if($aprDirect == $loggedInUser)
                return 1;
        
        //Give access if they were a direct approver at any time during the form process
        $sqlDirect = "SELECT USER_ID
                FROM workflowsuphistory
                WHERE SUBMISSIONID = '$sbid'";
        
        $resultDirect = $wpdb->get_results($sqlDirect, ARRAY_A);
        foreach($resultDirect as $rowDirect) {
            if($rowDirect['USER_ID'] == $loggedInUser) {
                return 1;
            }
        }
        
        return 0;
    }
    
    /*Checks if the user has access to a document that was uploaded through p2c forms. */
    public function hasDocumentAccess($filename) {
        global $wpdb;
        if(Workflow::loggedInUser() == '0') {
            return false;
        }
        $loggedInUser = Workflow::loggedInUser();
        $filename = addslashes($filename); 
        
        $sql = "SELECT workflowformstatus.FORMID, USER, 
                        APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, APPROVER_DIRECT, 
                        BEHALFOF, PROCESSOR, workflowformstatus.SUBMISSIONID,
                        workflowformsubmissions.FIELDID
                FROM workflowformstatus
                INNER JOIN workflowform ON workflowformstatus.FORMID = workflowform.FORMID
                INNER JOIN workflowformsubmissions ON workflowformsubmissions.SUBMISSIONID = workflowformstatus.SUBMISSIONID
                WHERE workflowformsubmissions.VALUE = '$filename'";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        if(count($result) == 1) {
            $row = $result[0];
            $wfid = $row['FORMID'];
            $submittedby = $row['USER'];
            $behalfof = $row['BEHALFOF'];
            $sbid = $row['SUBMISSIONID'];
            $fieldid = $row['FIELDID'];
        } else {
            return false;
        }
        
        //Allow users to view their own uploaded documents
        if($submittedby == $loggedInUser) {
            //Check if the submitter can view an approval upload
            $sql = "SELECT APPROVAL_LEVEL, APPROVAL_ONLY, APPROVAL_SHOW
                    FROM workflowformdetails
                    WHERE FORMID = '$wfid' AND FIELDID = '$fieldid'";
            $approvresult = $wpdb->get_results($sql, ARRAY_A);
            
            if(count($approvresult) == 1) {
                //The file can only be viewed by approvers
                if($approvresult[0]['APPROVAL_ONLY'] == '1' && $approvresult[0]['APPROVAL_SHOW'] == '0')
                    return false;
            }
            return true;
        }
        
        //Check if the person is authorized to look at this filled out form
        if(Workflow::hasRoleAccess($loggedInUser, $row['PROCESSOR'])) {
            return true;
        }
        
        if($submittedby != $loggedInUser) {
            if(Workflow::submissionApprover($sbid, $loggedInUser, $row['APPROVER_ROLE'], $row['APPROVER_ROLE2'], 
                $row['APPROVER_ROLE3'], $row['APPROVER_ROLE4'], $row['APPROVER_DIRECT'])) {
                //The approver will eventually have access to this submission again
                return true;
            } else if(Workflow::isAdmin(Workflow::loggedInUser())) {
                //An admin is trying to view the document
                if($status == '2' || $status == '3' || $status == '10') {
                    //submission is currently being edited by the submitter or it has been cancelled
                    return false;
                }
                return true; //They are an admin and the submission was not cancelled
            }
        }
        return false;
    }
}
    
    
    
?>