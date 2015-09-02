
<h2>Preview of Current Form</h2>
<div id="previewform">
</div>
<div style="clear:both;"></div>

<hr>

<button type="button" class="submitbutton" style="width: 200px;height:35px;" onclick="scrollDown();">Scroll Down</button>
<div style="clear:both;"></div>
<h1>Create New Form</h1><br>
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
    

    $workflow = new Workflow();
    echo $workflow->getForm();
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
            Form Name:
        </div>
        <div class="workflow workflowright style-1">
            <input type="text" name="workflowname" id="workflowname">
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
            Approver Level 1:
        </div>
        <div class="workflow workflowright style-1">
            <select name="destination1">
                <?php
                $values = $workflow->getRoles();
                for($i = 0; $i < count($values); $i++) {
                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
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
                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
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
                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
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
                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
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
            <input type="checkbox" id="behalfof" name="behalfof">Allow submissions on behalf of someone else.
        </div>
        <div class="clear"></div>
        
        <!--The added fields will populate here-->
        <div id="workflowfields">
            <h3 style="text-align: center;">History</h3>
        </div>
        <!--<div id="debugworkflowfields">
            <h3>Debug History</h3>
        </div>-->
        
        <div class="workflow workflowboth" style="margin-top: 20px;">
            <hr>
        </div>
        <div class="clear"></div>
        
        <!--Field Addition Template
            Add various types of fields here.-->
        <div class="workflow workflowleft">
            Field type:
        </div>
        <div class="workflow workflowright style-1">
            <select id="fieldtype" name="fieldtype" form="addnewworkflow" onchange="updateWorkflowCreation(this.id);">
                <option value="10">Heading</option>
                <option value="1">Instruction Text</option>
                <option value="0">Entry Box Input</option>
                <option value="4">Checkbox Input</option>
                <option value="7">Date Input</option>
                <option value="8">Ask a Question</option>
                <option value="3">Create a Newline</option>
                <option value="9">Header Row</option>
                <option value="5">Autofill Name</option>
                <option value="6">Autofill Date</option>
                <option value="2">Option</option>
            </select>
        </div>
        <div class="clear"></div>
        
        <div id="workflowdetails">
            <div class="workflow workflowleft">
                Value:
            </div>
            <div class="workflow workflowright style-1">
                <input type="text" id="workflowlabel" name="workflowlabel">
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
            <input type="checkbox" id="requiredfield" name="requiredfield">Required Field<br>
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
            <input type="checkbox" id="approvalshow" name="approvalshow">Displayed on Finished Forms (Approval fields only)?<br>
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowboth" style="margin: 20px 0 45px 0;">
            <button type="button" class="buttoncustom" onclick="addField();">Add Field</button>
            <button type="button" class="submitbutton" style="width: 99%;height:35px;" onclick="preview();">Update Preview</button>
        </div>
        <div class="clear"></div>
        
        
        <input type="hidden" id="count" name="count" value="0">
        <div class="clear"></div>
        <input type="submit" value="Create Form" onclick="clearPageExit();">
    </form>
<?php
} else {
    echo('<br>You do you not have access to create new workflows.');
}
?>