<?php
/*
*The main workflow class.
*
*
*THINGS THAT SHOULD BE UPDATED BEFORE RELEASE:
*-Update the log in so that it is not stored as a session variable. Use the actualloggedinUser function instead.
*-Remove the form field that lets you log in as someone else for debugging on the viewsubmissions.php file. 
*-viewAllWorkflows() we need to change it so only an ADMIN can access that content
*
*
*
*
* author: gerald.becker
*
*/


class Workflow {
    private $name;
    private $startAccess;
    private $approvers;
    private $behalfof;
    private $fields; 
    private $draft;
    private $savedData;
    private $numFields;
    private $mode;
    private $previousID;
    private static $currentUserEmployeeNum;  // Store the current user's employee number for easy access later
    private $uniqueToken;
    
    public function __construct() {
    }
    
    public function __destruct() {
        
    }
    
    public function createWorkflow($name, $startAccess, $approver, $approver2, $approver3, $approver4, $behalfof, 
                                    $draft, $savedData, $numFields, $mode, $previousID) {
        
        $this->name = $name;
        $this->startAccess = $startAccess;
        $this->approvers = array($approver, $approver2, $approver3, $approver4);
        $this->behalfof = $behalfof;
        $this->fields = array();
        
        $this->draft = $draft;
        $this->savedData = str_replace("'", "\'", str_replace("\\", "\\\\", $savedData));;
        $this->numFields = $numFields;
        $this->mode = $mode;
        $this->previousID = $previousID;
        
    }
    
    public function addField($type, $label, $editable, $approvalonly, $approvalshow, $size, $level, $requiredfield, $newgroup) {
        if($size == '')
            $size = 0;
        $this->fields[] = array($type, $label, $editable, $approvalonly, $approvalshow, $size, $level, $requiredfield, $newgroup);
    }
    
    /*
        Stores the new workflow layout.
    */
    public function storeToDatabase() {
        global $wpdb;
        if($this->draft)
            echo '<br>DRAFT MODE ON<br>';
        if($this->mode == 1 || $this->mode == 3) {
            $sql = "INSERT INTO workflowform (NAME, APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, BEHALFOF_SHOW,
                                                DRAFT, SAVED_FIELDS, NUM_FIELDS, ENABLED)
                    VALUES ('$this->name', '".$this->approvers['0']."'";
            
            for($i = 1; $i < count($this->approvers); $i++) {
                if($this->approvers[$i] != -1) {
                    $sql .= ", '".$this->approvers[$i]."'";
                } else {
                    $sql .= ", NULL";
                }
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
        
        
        //die(htmlspecialchars($sql));
        
        
        $result = $wpdb->query($sql, ARRAY_A);
        
        if($this->mode == 1 || $this->mode == 3) {
            $inserted_id = $wpdb->insert_id;
        } else {
            $inserted_id = $this->previousID;
        }
        
        if(!$result) {
            //die('failed to update<br>'.htmlspecialchars($sql));
            die('failed to update');
        }
        
        //If the workflow is being saved, there is no more work needed to be done. 
        if($this->mode == 1 || $this->mode == 2) {
            header("location: ?page=view");
            die();
        } 
        //die('SUCCESS!!!<br>'.htmlspecialchars($sql));
        //echo '<br>Inserted the ID: '.$inserted_id.'<br>';
        
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
    public function updateWorkflowSubmissions($fields, $newstatus, $submissionID, $formID, $user, $misc_content, $commenttext, $behalfof, $sup, $uniqueToken) {
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
        $sql = "SELECT STATUS, STATUS_APPROVAL, COMMENT, USER, UNIQUE_TOKEN
                FROM workflowformstatus
                WHERE SUBMISSIONID = '$submissionID'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        
        if(count($result) != 0) {
            $row = $result[0];
            $oldstatus = $row['STATUS'];
            $oldApprovalStatus = $row['STATUS_APPROVAL'];
            $oldcomment = $row['COMMENT'];
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
        if($oldstatus == 4) {
            
        } else if($oldstatus >= 7) {
            $_SESSION['ERRMSG'] = 'This form can no longer be edited.';
            header('location: ?page=viewsubmissions');
            die();
        }
        
        $directApprover = Workflow::getDirectApprover($user);
        
        if($sup) {
            //echo 'DEBUG: You are trying to get a supervisors supervisor for : '.$directApprover.'<br>';
             $temp = Workflow::getDirectApprover($directApprover); //Gets the supervisors supervisor
             if($temp != 0)
                $directApprover = $temp;
            //echo 'DEBUG: You got: '.$directApprover.'<br>';
            //die();
        }
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
        $configSuccess = $approver = $supNext = 0;
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
                            APPROVER_ROLE, APPROVER_ROLE2, APPROVER_ROLE3, APPROVER_ROLE4, APPROVER_DIRECT, BEHALFOF, UNIQUE_TOKEN
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
                $this->uniqueToken = $row['UNIQUE_TOKEN'];
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
                //echo 'DEBUG: You are an approver '.$loggedInUser.'<br>';
            } else if(($configvalue == 7 || $configvalue == 8) && $approver) {
                //echo 'DEBUG: You are an approver '.$loggedInUser.'<br>';
                $configvalue = 9;
            } else if($submittedby != $loggedInUser) {
                echo 'You do not have access to view this form at this time. If this is an error, contact helpdesk at <a href="mailto:helpdesk@p2c.com">helpdesk@p2c.com</a>.<br>';
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
        
        
        //TODO Authorize the person that is trying to access this function or page.
        //echo 'DEBUG: Workflow status: '.$configvalue.'<br>';
        echo Workflow::loadWorkflowEntry($wfid, $configvalue, $sbid, $misc_content, $comments, $submittedby, 
            $status, $approvalStatus, $hasAnotherApproval, $behalfof, 0, $supNext);
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
        $status, $approvalStatus, $hasAnotherApproval, $behalfof, $emailMode, $supNext) {
        global $wpdb;
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
        
        $response .= '<h2>'.$workflowName.'</h2><hr>';
        
        
        $response .= '<h2><span style="font-size:16px;">Submitted by: ';
        
        if($behalfof != '') {
            $response .= Workflow::getUserName($behalfof).' on behalf of ';
        }
        
        $response .= Workflow::getUserName($submittedby).
                    '&nbsp;&nbsp;&nbsp;Last Change by: '.$lastAction.'</span></h2>';
        
        if(!$emailMode)
            $response .= '<p class="status">';
        else
            $response .= '<p style="font-size: 24px;text-align: center;margin: 0;">';
        
        if($configuration == 0 || $configuration == 4) {
            $response .= 'Status: Pending Approval.';
        } else if($configuration == 7 || ($configuration == 9 && $status == 7)) {
            $response .= 'Status: Approved.';
        } else if($configuration == 8 || ($configuration == 9 && $status == 8)) {
            $response .= 'Status: Not Approved.';
        } else if($configuration == 10) {
            $response .= 'Status: Not Approved.';
        } else if($emailMode && $configuration == 3)
            $response .= 'Status: Input Required.';
        $response .= '</p>';
        $response .='<hr>';
        
        if(0 <= $configuration && $configuration < 7 && !$emailMode)
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
            
            
            $response .= '<select id="onbehalf" name="onbehalf" class="chosen-select" data-placeholder=" " onchange="updateSupervisorButton();"><option value="">Myself</option>';
            $values = Workflow::getAllUsers();
            
            for($i = 0; $i < count($values); $i++) {
                $response .= '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
            }
            $response .= '</select></div>';
            $response .= '<div class="clear" style="height: 50px;"></div>';
        }
        
        //For each field that is part of the form
        $count = 0;
        $anotherOption = 0;
        $prevId = -1;
        foreach($result as $row) {
            //Set flags if the current field is part of an option list
            if($row['TYPE'] == 2 && $prevId == $row['FIELDID']) {
                $anotherOption = 1;
            } else {
                //Close the option list
                if($anotherOption)
                    $response .='</select></div>';
                $anotherOption = 0;
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
            
            // echo 'DEBUG: ID:'.$row['FIELDID'].' | configuration:'.$configuration.' | editable:'.$editableField.' | APPROVAL_SHOW:'.$row['APPROVAL_SHOW'].' | APPROVAL_LEVEL:'.$row['APPROVAL_LEVEL']. ' | approvalStatus:'.$approvalStatus.' | Value:'.$fieldvalue.'<br>';
            
            
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
                
                $response .= '" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;font-weight:bold;';
                }
                
                $response .= '">'.$row['LABEL'].'</div>';
                
            } else if($row['TYPE'] == 0) { //Textbox
                if($row['APPROVAL_ONLY'] == 1)
                    if($configuration == 4 && $appLvlAccess || $approval_show)
                        $response .= '<div class="workflow workflowright style-1 approval mobile ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 mobile ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    //$response .= ' style="width:'.$row['FIELD_WIDTH'].'px;';
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= '" style="';
                    
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
                
                $response .= '" style="';
                
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
                        $response .= '<div class="workflow workflowright style-1 approval ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= '" style="';
                
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
                        $response .= '<div class="workflow workflowright style-1 approval ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= '" style="';
                
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
                        $response .= '<div class="workflow workflowright style-1 approval ';
                    else
                        continue;
                else
                    $response .= '<div class="workflow workflowright style-1 ';
                
                if($row['FIELD_WIDTH'] != NULL) {
                    $response .= Workflow::fieldWidth($row['FIELD_WIDTH']);
                }
                
                $response .= '" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
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
                    $response .= date("m/d/Y", strtotime($fieldvalue));
                }
                $response .= '</div>';
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
                
                $response .= '" style="';
                
                if($emailMode) {
                    $response .= 'float: left; margin-right:10px;';
                }
                
                $response .= '">';
                
                $response .= '<input type="radio" id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                    $row['FIELDID'].'" value="'.$row['LABEL'].'" ';
                
                if($editableField && $row['REQUIRED'])
                    $response .= 'required ';
                
                
                if(!$editableField || $emailMode) {
                    $response .= 'disabled ';
                }
                if($fieldvalue == $row['LABEL'])
                    $response .= 'checked';
                $response .='>'.$row['LABEL'].'</div>';
                
            } else if($row['TYPE'] == 2) { //Option List
                if(!$anotherOption) {
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
                    
                    $response .= '" style="';
                    
                    if($emailMode) {
                        $response .= 'float: left; margin-right:10px;';
                    }
                    
                    $response .= '">';
                    $response .= '<select id="workflowfieldid'.$row['FIELDID'].'" name="workflowfieldid'.
                        $row['FIELDID'].'" ';
                    
                    if(!$editableField || $emailMode) {
                        $response .= 'disabled ';
                    }
                    
                    $response .= '>';
                }
                
                
                $response .= '<option value="'.$row['LABEL'].'" ';
                if($fieldvalue == $row['LABEL'])
                    $response .= 'selected';
                $response .= '>'.$row['LABEL'].'</option>';
                
                /*if($editableField && $row['REQUIRED'])
                    $response .= 'required ';*/
                
            } 
        }
        //In case the loop ended and the last id was an option list, close the field and div
        if($anotherOption)
            $response .='</select></div>';
         
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
            
            $response .= '<h3>Submitting to: '.Workflow::getNextRoleName($submittingStatus, $submittingApproval, $id, ($configuration == 0), 
                $submissionID).'</h3>';
            if($supNext && 0 < $configuration && $configuration <= 4) {
                $direct = Workflow::getDirectApprover(Workflow::loggedInUser());
                $direct2 = Workflow::getDirectApprover($direct);
                $response .= '<div id="supervisor-radio">';
                $response .= '<input type="radio" name="nextsupervisor" value="0" checked>'.Workflow::getUserName($direct);
                if($direct2 != 0)
                    $response .= '<input type="radio" name="nextsupervisor" value="1">'.Workflow::getUserName($direct2);
                $response .= '</div>';
            }
            $response .= '<div class="clear"></div>';
            $response .= '<input type="hidden" id="count" name="count" value="'.$count.'">';
            $response .= '<input type="hidden" name="wfid" value="'.$id.'">';
            $response .= '<input type="hidden" name="sbid" value="'.$submissionID.'">';
            $response .= '<input type="hidden" id="ns" name="ns" value="0">';
            if( 0 < $configuration && $configuration < 4) {
                $response .= '<button type="button" class="submitbutton" onclick="saveSubmission(4, 0);">Submit Form</button>';
                $response .= '<button type="button" class="savebutton" onclick="saveSubmission(';
                if($configuration == 3)
                    $response .= '3, 0';
                else
                    $response .= '2, 0';
                $response .= ');">Save Draft</button>';
                $response .= '<button type="button" class="deletebutton" onclick="saveSubmission(';
                if($configuration == 3) 
                    $response .= '8, 0';
                else
                    $response .= '10, 0';
                $response .= ');">Delete Form</button>';
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
            $uniqueId = WorkFlow::workflowEmailToken($submissionID);
            $response .= '<div class="clear"></div>';
            if($configuration == 0 || $configuration == 2 || $configuration == 3) {
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
                $response .= '<a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'" style="text-decoration:none;"><button type="button" style="background-color: #0079C1;box-shadow: 0 0 5px 1px #969696; 
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">View Form</button></a>';
                //Uncomment if email for revisions should include quick buttons. But they really shouldn't
                /*$response .= '<a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=submit&lvl='.$approvalStatus.'&tk='.$uniqueId.'"><button type="button" style="background-color: #51abff;box-shadow: 0 0 5px 1px #969696; 
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Submit Form</button></a>';
                    
                $response .= '<a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=save&lvl='.$approvalStatus.'&tk='.$uniqueId.'"><button type="button">Save Draft</button></a>';//http://local.theloop.com  https://devstaff.powertochange.org
                
                $response .= '<a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=cancel&lvl='.$approvalStatus.'&tk='.$uniqueId.'"><button type="button" style="background-color: #ff8989;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Delete Form</button></a>';*/
            } else if($configuration == 4) {
                $response .= '<a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=approve&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;">
                    <button type="button" style="background-color: #0079C1;box-shadow: 0 0 5px 1px #969696; 
                    display: block;font-family: sans-serif;font-size: 18px;margin: 20px 10px 0px 0;
                    min-width: 200px;">Approve</button></a>';
                
                $response .= '<br><a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=change&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;"><button type="button" style="background-color: #F58220;box-shadow: 0 0 5px 1px #969696;
                    display: block;font-family: sans-serif;font-size: 18px;margin: 10px 10px 0px 0;
                    min-width: 200px;">Request Change</button></a>';
                
                $response .= '<br><a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=deny&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;"><button type="button" style="background-color: #F58220;box-shadow: 0 0 5px 1px #969696;
                    display: block;font-family: sans-serif;font-size: 18px;margin: 10px 10px 20px 0;
                    min-width: 200px;">Not Approved</button></a>';
            } else if($configuration == 0) {
                $response .= '<a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'&response=retract&lvl='.$approvalStatus.'&tk='.$uniqueId.'" style="text-decoration:none;"><button type="button" style="background-color: #F58220;box-shadow: 0 0 5px 1px #969696;
                    display: block;float: left;font-family: sans-serif;font-size: 18px;margin: 20px 10px 20px 0;
                    min-width: 200px;">Retract Submission</button></a>';
            }
        } else if(!$emailMode) {
            //Display approval history //Uncomment the below code to have it only show up once the form is complete.
            
            /*$sql = "SELECT *
                    FROM workflowformhistory
                    WHERE SUBMISSION_ID = '$submissionID'
                    ORDER BY DATE_SUBMITTED ASC";
        
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            $response .= '<table id="workflowhistory"><tr><td colspan=3><h3>Approval History</h3></td></tr>';
            $response .= '<tr><th>USER</th><th>ACTION</th><th>DATE</th></tr>';
            foreach($result as $row) {
                
                
                if($row['ACTION'] == 2) {
                    //$temp .= 'Saved';
                    continue;
                } else if($row['ACTION'] == 3 && $row['USER'] == $submittedby) {
                    //$temp = 'Saved';
                    continue;
                } else if($row['ACTION'] == 3) {
                    $temp = 'Review Required Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 4 && $row['USER'] == $submittedby) {
                    $temp = 'Submitted';
                } else if($row['ACTION'] == 4) {
                    $temp = 'Approved Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 7) {
                    $temp = 'Approved Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 8 && $row['USER'] == $submittedby) {
                    $temp = 'Cancelled Submission';
                } else if($row['ACTION'] == 8) {
                    $temp = 'Denied Lvl: '.$row['APPROVAL_LEVEL'];
                } else {
                    continue;
                }
                $response .= '<tr><td>'.Workflow::getUserName($row['USER']).'</td><td>';
                $response .= $temp;
                $response .= '</td>';
                $response .= '<td>'.$row['DATE_SUBMITTED'].'</td></tr>';
                
            }
            
            $response .= '</table>';*/
        }
        
        //Show the history of the submission
        if(true) {
            $sql = "SELECT *
                    FROM workflowformhistory
                    WHERE SUBMISSION_ID = '$submissionID'
                    ORDER BY DATE_SUBMITTED ASC";
        
            $result = $wpdb->get_results($sql, ARRAY_A);
            if(!$emailMode)
                $response .= '<div class="clear"></div>';
            else
                $response .= '<div style="clear:both;"></div>';
            $response .= '<table id="workflowhistory"><tr><td colspan=3><h3>Approval History</h3></td></tr>';
            $response .= '<tr><th>USER</th><th>ACTION</th><th>DATE</th></tr>';
            foreach($result as $row) {
                
                
                if($row['ACTION'] == 2) {
                    //$temp .= 'Saved';
                    continue;
                } else if($row['ACTION'] == 3 && $row['USER'] == $submittedby) {
                    //$temp = 'Retracted - Saved';
                    continue;
                } else if($row['ACTION'] == 3) {
                    $temp = 'Review Required Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 4 && $row['USER'] == $submittedby) {
                    $temp = 'Submitted';
                } else if($row['ACTION'] == 4) {
                    $temp = 'Approved Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 7) {
                    $temp = 'Approved Lvl: '.$row['APPROVAL_LEVEL'];
                } else if($row['ACTION'] == 8 && $row['USER'] == $submittedby) {
                    $temp = 'Cancelled Submission';
                } else if($row['ACTION'] == 8) {
                    $temp = 'Denied Lvl: '.$row['APPROVAL_LEVEL'];
                } else {
                    continue;
                }
                $response .= '<tr><td>'.Workflow::getUserName($row['USER']).'</td><td>';
                $response .= $temp;
                $response .= '</td>';
                $response .= '<td>'.$row['DATE_SUBMITTED'].'</td></tr>';
                
            }
            
            $response .= '</table>';
        }
        
        
        
        $response .= '<div class="clear"></div>';
        
        //For processing the email click automatically
        if(isset($_GET['response']) && isset($_GET['lvl']) && $configuration == 4) {
            $tokenSuccess = (isset($_GET['tk']) && Workflow::workflowEmailTokenDecode($_GET['tk'], $submissionID));
            if(!$tokenSuccess)
                $emailclick = '<br><span style="color:red;font-weight:bold;">The email you tried using to review this form is out of date. Please review the below submission in detail.</span><br><br>';
            else if($_GET['response'] == 'approve' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {document.getElementById("approvelink").click();};</script>';
            else if($_GET['response'] == 'change' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {document.getElementById("changelink").click();};</script>';
            else if($_GET['response'] == 'deny' && $_GET['lvl'] == $approvalStatus)
                echo '<script>window.onload = function() {document.getElementById("denylink").click();};</script>';
        }
        $response = str_replace('%EMAILCLICK%', $emailclick, $response);
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
            $response .= '<tr><td><b>'.$row['NAME'].'</b></td><td><a href="?page=';
            
            if($row['DRAFT'])
                $response .= 'createworkflow';
            else
                $response .= 'workflowentry';
            
            $response .= '&wfid='.$row['FORMID'].'">';
            
            if(!$row['DRAFT'])
                $response .= '/forms-information/workflow/?page=workflowentry&wfid='.$row['FORMID'];
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
    
    public function viewSubmissionSummary($userid, $formName, $submittedby, $date, $id) {
        global $wpdb;
        $response = '';
        
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
        
        //Approver
        $sql = "SELECT  workflowformstatus.STATUS,
                        COUNT(workflowformstatus.STATUS) AS COUNT
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
        $sql .= "GROUP BY workflowformstatus.STATUS";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $approverPending = $approverApproved = $approverDenied = 0;
        foreach($result as $row) {
            if($row['STATUS'] == 4) { //approval pending
                $approverPending = $row['COUNT'];
            } else if($row['STATUS'] == 7) { //approved
                $approverApproved = $row['COUNT'];
            } else if($row['STATUS'] == 8) { //denied
                $approverDenied = $row['COUNT'];
            }
        }
        
        
        //Display summary
        $response .= '<div id="workflow-summary">';
        $response .= '<table style="margin-left: auto; margin-right:auto;">';
        $response .= '<tr><td style="border-right: 1px solid black;padding:10px;">';
        
        $response .= '<a href="#user-submissions" class="nav-option-link" onclick="switchRole(0);"><div class="nav-option-outside">
        <div class="nav-option-inside"><h2 style="color:inherit;">'.Workflow::loggedInUserName().'\'s <br>Submissions</h2>';
        
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
        
        $response .= '<a href="#approver-submissions" class="nav-option-link" onclick="switchRole(1);"><div class="nav-option-outside">
        <div class="nav-option-inside"><h2 style="color:inherit;">Forms Requiring Approval</h2>';
        
        $response .= '<table id="view-submissions" style="margin-left:auto;margin-right:auto;">';
        $response .= '<tr><th>Status</th><th>Count</th></tr>';
        $response .= '<tr><td class="left">Approval Pending</td><td class="center">'.$approverPending.'</td></tr>';
        $response .= '<tr><td class="left">Approved</td><td class="center">'.$approverApproved.'</td></tr>';
        $response .= '<tr><td class="left">Not Approved</td><td class="center">'.$approverDenied.'</td></tr>';
        $response .= '</table>';
        $response .= '<br><p style="color:inherit;">Click to view forms.</p></div></div></a>';
        
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
        
        $response .= '<tr><td colspan="4"><h2>'.Workflow::loggedInUserName().'\'s Submissions</h2><br></td></tr>';
        
        $response .= '<tr><td colspan="4">
                        <div id="user-status-link2" class="workflow-status-link" onclick="switchTab(0, 2);">Saved (%SAVED%)</div>
                        <div id="user-status-link3" class="workflow-status-link workflow-status-header" onclick="switchTab(0, 3);">Input Required (%INPUTREQUIRED%)</div>
                        <div id="user-status-link4" class="workflow-status-link" onclick="switchTab(0, 4);">Approval Pending (%PENDING%)</div>
                        <div id="user-status-link7" class="workflow-status-link" onclick="switchTab(0, 7);">Approved (%APPROVED%)</div>
                        <div id="user-status-link8" class="workflow-status-link" onclick="switchTab(0, 8);">Not Approved (%DENIED%)</div>
                    </td></tr>';
        
        
        $tableHeader = '<tr class="%CLASS% submissions-header"><th>ID</th><th>Form Name</th><th>Last Modified By</th><th>Date Modified</th></tr>';
        
        
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
                <td>'./*<a style="display:block;" href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">'.*/$row['NAME'].'</a></td>
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
    
    public function viewAllSubmissionsAsApprover($userid, $formName, $submittedby, $date, $id) {
        global $wpdb;
        $LOADMOREAMT = 7; //Changes the number of submissions displayed under each section
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
        $sql .= "ORDER BY workflowformstatus.STATUS, workflowformstatus.DATE_SUBMITTED DESC, workflowform.NAME ASC";
        
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $response .= '<div id="approver-submissions" class="hide">';
        $response .= '<table>';
        
        $response .= '<tr><td colspan="5"><h2>Forms Requiring Approval</h2><br></td></tr>';
        
        $response .= '<tr><td colspan="5">
                        <div id="approver-status-link4" class="workflow-status-link workflow-status-header" onclick="switchTab(1, 4);">Approval Pending (%PENDING%)</div>
                        <div id="approver-status-link7" class="workflow-status-link" onclick="switchTab(1, 7);">Approved (%APPROVED%)</div>
                        <div id="approver-status-link8" class="workflow-status-link" onclick="switchTab(1, 8);">Not Approved (%DENIED%)</div>
                    </td></tr>';

       
        $tableHeader = '<tr class="%CLASS% submissions-header"><th>ID</th><th>Form Name</th><th>Submitted By</th><th>Last Modified By</th><th>Date Modified</th></tr>';
        
        $prevState = 1;
        $approved = $denied = $pending = 0;
        foreach($result as $row) {
            if($row['STATUS'] != $prevState) {
                if($row['STATUS'] == 4) {
                    $response .= '<tr class="approver-4"><td colspan=5><div class="view-submissions-headers workflow-status-header">Submissions Requiring Approval</div></td></tr>'.str_replace('%CLASS%', "approver-4", $tableHeader);
                    $prevState = 4;
                } else if($row['STATUS'] == 7) {
                    $response .= '<tr class="approver-7 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Approved Forms</div></td></tr>'.str_replace('%CLASS%', "approver-7  hide", $tableHeader);
                    $prevState = 7;
                } else if($row['STATUS'] == 8) {
                    $response .= '<tr class="approver-8 hide"><td colspan=5><div class="view-submissions-headers workflow-status-header">Forms Not Approved</div></td></tr>'.str_replace('%CLASS%', "approver-8  hide", $tableHeader);
                    $prevState = 8;
                }
                
            }
            
            $response .= '<tr class="selectedblackout ';
            if($row['STATUS'] == 4) {
                $response .= 'approver-4';
                $pending++;
            } else if($row['STATUS'] == 7) {
                $response .= 'approver-7 hide';
                $approved++;
            } else if($row['STATUS'] == 8) {
                $response .= 'approver-8 hide';
                $denied++;
            }
            
            $response .= '" data-href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">';
            $response .=  '<td>'.$row['SUBMISSIONID'].'</td>
                            <td>'./*<a style="display:block;" href="?page=workflowentry&sbid='.$row['SUBMISSIONID'].'">'.*/$row['NAME'].'</a></td>
                            <td>'.$row['USERNAME'].'</td>
                            <td>'.WorkFlow::getLastEditedUserName($row['SUBMISSIONID']).'</td>
                            <td>'.$row['DATE_SUBMITTED'].'</td>';
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
        
        
        
        $response .= '</table></div><div style="margin-bottom:50px;"></div>';
        
        $response = str_replace('%PENDING%', (($pending == 0) ? $pending : '<b>'.$pending.'</b>'), $response);
        $response = str_replace('%APPROVED%', $approved, $response);
        $response = str_replace('%DENIED%', $denied, $response);
        
        return $response;
    }
    
    public function sendEmail($submissionID) {
        //require_once("phpmailer/vendor/autoload.php");
        require_once("PHPMailer-master/PHPMailerAutoload.php");
        /*$headers = "From: hr@powertochange.org";
        $subject = "Test";
        $emailMessage = 
        "put the message in here";

        //mail('gerald.becker@p2c.com', $subject, $emailMessage, $headers);*/
        
        
        //$uniqueId = WorkFlow::workflowEmailToken($submissionID);//DEBUG
        
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
        
        if(!($status == 4 || $status == 7 || $status == 8 || $status == 3))
            return;
        
        //Find out if it is a direct supervisor submission or not
        $role = $approvers[$approvalStatus - 1];
        $supNext = $approvers[$approvalStatus];
        
        //check if this submission was rejected and needs further input
        if($approvalStatus == 1 && $status == 3) { 
            $sql = "SELECT employee.employee_number AS MEMBER, employee.user_login, user_email, '1' AS EMAIL_ON
                    FROM employee  
                    INNER JOIN wp_users ON employee.user_login = wp_users.user_login 
                    WHERE employee.employee_number = '$userid'";
                    echo 'DENIED: denied approval test';
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
        var_dump($emailRecepients);
        
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
                <p>'.Workflow::getUserName($userid).' has submitted the form: '.$formName.'</p>
                <p><a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'">Submission '.$submissionID.'</a>
                (click to view form online)</p>
                <p>If you require clarification on any items, please contact the submitter directly or use the request changes button.   
                Some forms may result in a fundamental change in the employment relationship of a staff member, 
                please review carefully before approving. </p>
            <h3>DEBUG: Email List (Members in this role)</h3> '.$tempRec.'<br>'.$workflow->loadWorkflowEntry($formID, 4, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, ($supNext != ''), 0, 1, ($supNext == 8)).
            '<br></body>';
        } else if($status == 3) {
            $template = '
            <body style="font-family: sans-serif; color:black;">
                <h2>You have a submission requiring further input!</h2>
                <p>'.Workflow::getUserName($userid).', you previously submitted the form: '.$formName.' and it has been reviewed and requires further action.</p>
                <p><a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'">Submission '.$submissionID.'</a>
                (click to view form online)</p>
                <p></p>
            <h3>DEBUG: Email List (Members in this role)</h3> '.$tempRec.'<br>'.$workflow->loadWorkflowEntry($formID, 3, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, ($supNext != ''), 0, 1, ($supNext == 8)).
            '<br></body>';
        } else {
            $templateFinished = '
            <body style="font-family: sans-serif; color:black;">
                <h2>You have a form that has been reviewed!</h2>
                <p><b>Form: <b>'.$formName.'</b></p>
                <p>To view this form, visit this link: 
                <a href="http://local.theloop.com/forms-information/workflow/?page=workflowentry&sbid='.$submissionID.'">Submission '.$submissionID.'</a></p>
            <h3>Email List</h3> '.$tempRec.'<br>'.$workflow->loadWorkflowEntry($formID, $status, $submissionID, $misc_content, $commenttext, $userid, 
                    $status, $approvalStatus, 0, 0, 1, 0).
            '<br></body>';
        }
        
        
        
        for($i = 0; $i < count($recepients); $i++) {
            if($recepients[$i][2] == 1) { //if sending of emails is checked in the email settings
                if($status == 4)
                    $body = str_replace('%EMAILNAME%', Workflow::getUserName($recepients[$i][0]), $template);
                else if($status == 3)
                    $body = $body = str_replace('%EMAILNAME%', Workflow::getUserName($recepients[$i][0]), $template);
                else 
                    $body = $templateFinished;
                //$body .= '<div style="clear:both;"></div><br>DEBUG:Your email address is: '.$recepients[$i][1];
            
                //$body .= '<br>'.htmlspecialchars($sql1).'<br>'.htmlspecialchars($sql).'<br>';
                
                
                $mail = new PHPMailer;
                $mail->isSMTP();          // Set mailer to use SMTP
                $mail->Host = 'smtp.powertochange.org'; // Specify main and backup SMTP servers
                //$mail->SMTPAuth = true;                            // Enable SMTP authentication
                $mail->SMTPDebug = 2;
                //$mail->SMTPSecure = 'ssl';       // Enable TLS encryption, ssl also accepted
                $mail->Port = 25;

                $mail->From = 'workflowloop-no-reply@p2c.com';
                $mail->FromName = 'Workflow Email';
                $mail->AddAddress('matthew.campbell@p2c.com'); //TODO: multiple emails gerald.becker@p2c.com
                $mail->AddBCC('gerald.becker@p2c.com'); //TODO: multiple emails gerald.becker@p2c.com

                $mail->IsHTML(true);
                
                if($status != 3)
                    $mail->Subject = 'Workflow email from '.Workflow::getUserName($userid);
                else
                    $mail->Subject = 'Workflow email requiring further input';
                                                            
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
    
    
    public function getNextRoleName($currentLevel, $hasAnotherApproval, $formID, $resolveName, $sbid) {
        //echo 'DEBUG: has approval: '.$hasAnotherApproval.' level:'.$currentLevel. ' form:'.$formID;
        if(!$hasAnotherApproval)
            return 'Form Complete';
        global $wpdb;
        $response = '';
        
        $sql = "SELECT workflowroles.NAME, ROLEID
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
        return 1;
    }
}
    
    
    
?>