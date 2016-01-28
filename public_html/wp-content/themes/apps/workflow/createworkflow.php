<?php
/*
*Creates a new workflow form.
*
*
* //TODO: create better documentation
*
*When adding a new field type, you must follow the following steps:
*1) Add the option under id=fieldtype on this page
*2) Go to the javascript file and add it to : function fieldTypeContent(selectedValue) 
        [hint: just copy the same format as the other field types. Don't worry it will make sense.]
*3) Add logic in Workflow::loadWorkflowEntry() function - this is how it will be displayed when the form is opened
*4) Go to the javascript file and update: function preview()  -  this is used for building the form
*
*
*
*When adding a new property:
*
*
*
*
* author: gerald.becker
*
*/

if(Workflow::isAdmin(Workflow::loggedInUser())) {
?>

<h1>Create New Form</h1><br>


<div id="screen-blackout">
    <div id="popuppreview">
        <div id="previewform">
        <h2 class="center">Preview of Current Form</h2>
        </div>
        <div style="clear:both;"></div>
        <button type="button" onclick="closePreview();" class="btn1 close-btn">Close the Preview</button>
    </div>
</div>
<div style="clear:both;"></div>

<hr>

<button type="button" class="submitbutton" style="width: 200px;height:35px;" onclick="scrollDown();">Scroll Down</button>
<div style="clear:both;"></div>

<?php
    $workflow = new Workflow();
    //echo $workflow->getForm();
    
    $copy = 0;
    if(isset($_GET['wfid']) && $_GET['wfid'] != '') {
        $wfid = $_GET['wfid'];
        
        global $wpdb;
        
        $sql = "SELECT *
                FROM workflowform
                WHERE FORMID = '$wfid'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $numfields = 0;
        if(count($result) != 0) {
            $formname = $result[0]['NAME'];
            $approver1 = $result[0]['APPROVER_ROLE'];
            $approver2 = $result[0]['APPROVER_ROLE2'];
            $approver3 = $result[0]['APPROVER_ROLE3'];
            $approver4 = $result[0]['APPROVER_ROLE4'];
            $behalfof = $result[0]['BEHALFOF_SHOW'];
            $savedfields = $result[0]['SAVED_FIELDS'];
            if($result[0]['NUM_FIELDS'] != null)
                $numfields = $result[0]['NUM_FIELDS'];
            $draft = $result[0]['DRAFT'];
        }
        
        if(isset($_GET['copy'])) {
            $draft = 0; //This configures the page to actually create a new form instead.
            $copy = 1;
            $sql = "SELECT CONTENT, NUM_FIELDS
                    FROM workflowformsave
                    WHERE FORMID = '$wfid'
                    ORDER BY DATE_SAVED DESC
                    LIMIT 1";
            $result = $wpdb->get_results($sql, ARRAY_A);
            if(count($result) != 0) {
                $numfields = $result[0]['NUM_FIELDS'];
                $savedfields = $result[0]['CONTENT'];
            }
        } else if(!$draft) {
            $_SESSION['ERRMSG'] = 'This form can no longer be edited. Please copy the form in order to make modifications.';
            header("location: ?page=viewsubmissions");
            die();
        }
    }
      
?>
    <script>
    /*
    Prevents the page from exiting without first giving a warning to the user. 
    */
    var pageExitOK = false;
    
    function clearPageExit() {
        pageExitOK = true;
    }
    window.onbeforeunload = function() {
        if(!pageExitOK) {
            return "Are you sure you want to exit without submitting?";
        }
        
    }
    </script>
    
    <form id="addnewworkflow" action="?page=add_workflow" method="POST" autocomplete="off" onsubmit="return formValidate();">
        <div class="workflow workflowleft">
            Form Name:<span class="red">*</span>
        </div>
        <div class="workflow workflowright style-1">
            <input type="text" name="workflowname" id="workflowname" value="<?php echo $formname;?>">
        </div>
        <div class="clear"></div>
        
        <!--<div class="workflow workflowleft">
            Access to Start:
        </div>
        <div class="workflow workflowright style-1">
            <input type="text" name="startaccess">
        </div>
        <div class="clear"></div>-->
        
        <div class="workflow workflowleft">
            Approver Level 1:<span class="red">*</span>
        </div>
        <div class="workflow workflowright style-1">
            <select name="destination1">
                <?php
                $values = $workflow->getRoles();
                for($i = 0; $i < count($values); $i++) {
                    echo '<option value="'.$values[$i][0].'" ';
                    if($values[$i][0] == $approver1)
                        echo 'selected';
                    echo '>'.$values[$i][1].'</option>';
                }
                ?>
            </select>
            <!--<input type="text" name="destination">-->
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Approver Level 2:
        </div>
        <div class="workflow workflowright style-1">
            <select name="destination2">
                <option></option>
                <?php
                $values = $workflow->getRoles();
                for($i = 0; $i < count($values); $i++) {
                    echo '<option value="'.$values[$i][0].'"';
                    if($values[$i][0] == $approver2)
                        echo 'selected';
                    echo '>'.$values[$i][1].'</option>';
                }
                ?>
            </select>
            <!--<input type="text" name="destination">-->
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Approver Level 3:
        </div>
        <div class="workflow workflowright style-1">
            <select name="destination3">
                <option></option>
                <?php
                $values = $workflow->getRoles();
                for($i = 0; $i < count($values); $i++) {
                    echo '<option value="'.$values[$i][0].'"';
                    if($values[$i][0] == $approver3)
                        echo 'selected';
                    echo '>'.$values[$i][1].'</option>';
                }
                ?>
            </select>
            <!--<input type="text" name="destination">-->
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Approver Level 4:
        </div>
        <div class="workflow workflowright style-1">
            <select name="destination4">
                <option></option>
                <?php
                $values = $workflow->getRoles();
                for($i = 0; $i < count($values); $i++) {
                    echo '<option value="'.$values[$i][0].'"';
                    if($values[$i][0] == $approver4)
                        echo 'selected';
                    echo '>'.$values[$i][1].'</option>';
                }
                ?>
            </select>
            <!--<input type="text" name="destination">-->
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Behalf Of Submissions:
        </div>
        <div class="workflow workflowright style-1">
            <input type="checkbox" id="behalfof" name="behalfof" <?php if($behalfof)echo 'checked';?>>Allow submissions on behalf of someone else.
        </div>
        <div class="clear"></div>
        
        <!--The added fields will populate here-->
        <div id="workflowfields">
            <?php if($draft || $copy) { echo $savedfields;}?>
        </div>
        <!--<div id="debugworkflowfields">
            <h3>Debug History</h3>
        </div>-->
        <div id ="fieldaddconfirmscroll" style="height: 40px;"></div>
        <img id ="fieldaddconfirm" src="/wp-content/images/workflowcheck.png" width="30px" height="30px"/>
        <div class="workflow workflowboth" style="margin-top: 20px;">
            <hr>
        </div>
        <div class="clear"></div>
        
        <!--Field Addition Template
            Add various types of fields here.-->
        <div class="workflow workflowleft">
            Field type:<span class="red">*</span>
        </div>
        <div class="workflow workflowright style-1">
            <select id="fieldtype" name="fieldtype" form="addnewworkflow" onchange="updateWorkflowCreation(this.id);">
                <option value="10">Heading</option>
                <option value="11">Heading 1</option>
                <option value="12">Heading 3</option>
                <option value="1">Instruction Text</option>
                <option value="0">Entry Box Input</option>
                <option value="4">Checkbox Input</option>
                <option value="7">Date Input</option>
                <option value="8">Ask a Question</option>
                <option value="3">Create a Newline</option>
                <option value="9">Horizontal Line</option>
                <option value="5">Autofill Name</option>
                <option value="6">Autofill Date</option>
                <option value="2">Option</option>
                <option value="13">Radio</option>
            </select>
        </div>
        <div class="clear"></div>
        
        <div id="workflowdetails">
            <div class="workflow workflowleft">
                Text:<span class="red">*</span>
            </div>
            <div class="workflow workflowright style-1">
                <input type="text" id="workflowlabel" name="workflowlabel" maxlength="500">
            </div>
            <div class="clear"></div>
            
            <div class="workflow workflowleft hide">
                Field Size:
            </div>
            <div class="workflow workflowright style-1 hide">
                <input type="text" id="workflowsize" name="workflowsize">
            </div>
            <div class="clear"></div>
        </div>
        
        <div class="workflow workflowleft">
            Field Settings:
        </div>
        <div class="workflow workflowright style-1">
            <input type="checkbox" id="requiredfield" name="requiredfield" title="Does the field have to be filled out by the user?">Required Field<br>
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Approval Rights:
        </div>
        <div class="workflow workflowright style-1">
            <input type="checkbox" id="editable" name="editable">Can this field be modified during approval steps?<br>
            <!--<input type="checkbox" id="approvalonly" name="approvalonly">Approval screen field?-->
            
        </div>
        <div class="clear"></div>
        <!--<br>-->
        <div class="workflow workflowleft">
            Approval Level:
        </div>
        <div class="workflow workflowright style-1">
            <select id="approvallevel" name="approvallevel" form="addnewworkflow">
                <option value="0"></option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Level 4</option>
            </select>
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Display Settings:
        </div>
        <div class="workflow workflowright style-1">
            <input type="checkbox" id="approvalshow" name="approvalshow" title="Can the user who submitted the form see this field once completed?">Displayed on Finished Forms (Approval fields only)?<br>
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowboth" style="margin: 20px 0 45px 0;">
            <button type="button" class="buttoncustom" onclick="addField();">Add Field</button>
            <button type="button" class="submitbutton" style="width: 99%;height:35px;" onclick="preview();">Preview Form</button>
        </div>
        <div class="clear"></div>
        
        
        <input type="hidden" id="count" name="count" value="<?php if($draft || $copy)echo $numfields;else echo '0';?>">
        <input type="hidden" id="submitmode" name="submitmode" value="3">
        <input type="hidden" id="savedData" name="savedData" value="">
        <input type="hidden" id="previousID" name="previousID" value="<?php echo $wfid?>">
        <div class="clear" style="height:40px;"></div>
        <input type="submit" id="formsubmitbutton" value="Create Form" onclick="clearPageExit();" style="display: none;">
        <button type="button" style="width: 200px;height:35px;" onclick="processWorkflow(<?php if($draft)echo '4'; else echo '3';?>);">Create Form</button>
        <button type="button" style="width: 200px;height:35px;" onclick="processWorkflow(<?php if($draft)echo '2'; else echo '1';?>);">Save Form</button>
        <br><br>
    </form>
<?php
} else {
    echo('<br>You do you not have access to create new workflows.');
}
?>

<script>
//alert(find("count").value);
//alert('TOTALCOUNT IS: ' + totalCount);

//When loading a draft or a previous form, the count field needs to be set correctly to allow adding of other fields
totalCount = find("count").value;
//Set the second value to the field type that is first displayed when this page loads.
fixExtraFields("", 10);
</script>