/*
* Performs various actions for the workflow forms creation.
*
* If you are making changes to the way a field behaves, follow these steps:
* -In the addField() function, make sure to create a div that can be targeted by javascript
*  Go to a div and add id="myuniquename' + totalCount +'" 
*
* -Also add an if statement that checks whether it should be hidden when it gets copied to the history
*  area. This is done in the hideSettings() function. EX: skipfieldsettings
*
* -Do the above step in the createworkflow.php file as well since this is the initial field config spot
* 
* -Create a javascript function like toggleExtraSettings() that toggles the fields. Or you can modify this
*  function and change the behaviour of what gets hidden or shown when a field type is selected. **Make
*  sure you call this function based on when something else is clicked or selected
* 
* -Make sure to update the swapConversion() function. For example if you add the ability for
*  fields to hide when a setting is selected, add the div into this function.
*
*
*  ==========================================================================
*  ===Hiding sections of the field editing section based on the field type===
*  ===  This is used for the Hint, field size and question fields         ===
*  ==========================================================================
* Go to the workflowDetailsFixAll() function and add code that will add the hide class to the div if you
* want it to be hidden. This function is called in the history section and the area where new fields are
* added as well. 
*
*
*
*
* ==============================================================================================================
* = More Simple Instructions on how to add an input field type (ex: Input box, text area, fileupload box, etc) =
* ==============================================================================================================
* 1) In createworkflow.php, scroll to the select input name="fieldtype". Add an option and give it a new value number.
* 2) Go to the hideSettings(setting, fieldType) function. Add your new field type into the different sections depending
*    whether or not you want fields hidden. 
* 3) Add the field type to the function fieldTypeContent(selectedValue)
* 4) Add how the field displays on a form in the function preview().
* 5) Add the field to the server php file
*
* author: gerald.becker
*
*/


var totalCount = 0;
var timeout;
var newRadioFields = 2;
var DEFAULTNEWRADIOFIELDS = 2;



function find(elem) {
    return document.getElementById(elem);
}

function findClass(elem) {
    return document.getElementsByClassName(elem);
}

function changeDefaultVal(elem) {
    find(elem).defaultValue = find(elem).value;
}

function fixQuotes(text) {
    text = text.split('"').join('&quot;');
    text = text.split('<').join('&lt;');
    text = text.split('>').join('&gt;');
    return text;
}

/*
This function will add a field to the history. 
See main documentation at the top to see how to modify field behaviour.
*/
function addField() {
    //Clear the timeout callback that might cause the confirmation checkmark to stop being displayed
    clearTimeout(timeout);
    find("fieldaddconfirm").style.opacity = "0";
    
    var fieldType = find("fieldtype").value;
    
    //#hidesettingsMATCH 
    var skipfieldsettings = 0;
    if(hideSettings('fieldsettings', fieldType)) { 
        skipfieldsettings = 1;
    }
    var skipapprovalrights = 0;
    if(hideSettings('approvalrights', fieldType)) { 
        skipapprovalrights = 1;
    }
    var skipapprovallevels = 0;
    if(hideSettings('approvallevels', fieldType)) { 
        skipapprovallevels = 1;
    }
    
    //Add the field
    var text = '';
    
    text += '<div id="fieldwrap' + totalCount + '"><div id="field' + totalCount + '">' +
        '<div class="workflow workflowleft">Field type:<span class="red">*</span></div>' +
        '<div class="workflow workflowright style-1">' +
            fieldTypeLoad(fieldType) +
        '</div>' +
        '<div class="clear"></div>' +
        
        '<div id="workflowdetails' + totalCount + '">';
        
    if(fieldType != '8' && fieldType != '13' && fieldType != '2') {
        text += workflowDetailsFix(totalCount, fieldType, fixQuotes(find("workflowlabel").value), find("workflowsize").value, '', '', 1);
    } else if(fieldType == '8') {
        text += workflowDetailsFix(totalCount, fieldType, find("workflowlabela").value, find("workflowsizea").value, 
            find("workflowlabelb").value, find("workflowsizeb").value, 1);
    } else if(fieldType == '13' || fieldType == '2') { //radio or option list
        var radioFields = new Array();
        
        for(var i = 0; i < newRadioFields; i++) {
            radioFields[i] = find("workflowradio-" + i).value;
        }
        var fSize = '';
        if(find("workflowsize") != null) {
            fSize = find("workflowsize").value;
        }
        text += workflowDetailsRadioFix(totalCount, fieldType, fSize, 1, radioFields);
        
        //Reset the new radio / option fields to default
        newRadioFields = DEFAULTNEWRADIOFIELDS;
    } 
    
    text += '</div>';
    var approvalLvl = find("approvallevel").value;
    text += 
        '<div class="workflow workflowleft" id="fieldsettings1' + totalCount +
        '" ';
    if(skipfieldsettings) {
        text += 'hidden="true"';
    }
    text += '>Field Settings:</div>' +
        '<div class="workflow workflowright style-1" id="fieldsettings2' + totalCount +
        '"';
    if(skipfieldsettings) {
        text += 'hidden="true"';
    }
    text += '>' +
            requiredCheck(find("requiredfield").checked, fieldType) + '<br>' +
        '</div><div class="clear"></div>' +
        
        '<div class="workflow workflowleft" id="approvalrights1' + totalCount + '"';
    if(skipapprovalrights) {
        text += 'hidden="true"';
    }
    text += '>Approval Rights:</div>' +
        '<div class="workflow workflowright style-1" id="approvalrights2' + totalCount + '"';
    if(skipapprovalrights) {
        text += 'hidden="true"';
    }
    text += '>' +
            editableCheck(find("editable").checked, fieldType) +
            //approvalonlyCheck(find("approvalonly").checked) +
        '</div>' +
        '<div class="clear"></div>' + //<br>
        '<div class="workflow workflowleft" id="approvallevels1' + totalCount + '" ';
        
        if(skipapprovallevels) {
            text += 'hidden="true"';
        }
        
        text += '>Approval Level:</div>' + 
        '<div class="workflow workflowright style-1" id="approvallevels2' + totalCount + '" ';
        
        if(skipapprovallevels) {
            text += 'hidden="true"';
        }
        
        text += '>' +
            approvalLevel(find("approvallevel").value, fieldType) +
        '</div><div class="clear"></div>' +
        '<div class="workflow workflowleft" id="displayfinishmain1' + totalCount +'" ';
        
        if(approvalLvl == 0) {
            text += 'hidden="true"';
        }
        
        text += '>Display Settings:</div>' +
        '<div class="workflow workflowright style-1" id="displayfinishmain2' + totalCount + '" ';
        
        if(approvalLvl == 0) {
            text += 'hidden="true"';
        }
        
        text += '>' +
            approvalshowCheck(find("approvalshow").checked, fieldType) +
            '<br></div><div class="clear"></div></div>' + 
            '<br><div class="workflow workflowleft">Position:</div>' + 
            '<div class="workflow workflowright style-1">' + 
            '<button type="button" style="width: 100px;" onclick="swap('+totalCount+', 1);">Move Up</button>&nbsp;' + 
            '<button type="button" style="width: 100px;" onclick="swap('+totalCount+', 0);">Move Down</button>&nbsp;' +
            '<button type="button" style="width: 100px;" onclick="removeField('+totalCount+');">Remove</button></div>' + 
            '<div class="clear"></div>' + 
        '<div class="workflow workflowboth" style="margin-top: 0px;"><hr style="border-width:4px;"></div><div class="clear"></div></div>';
    
    find("workflowfields").innerHTML += text;
    
    totalCount++;
    find("count").value = totalCount;
    
    
    setTimeout(function() { find("fieldaddconfirm").style.opacity = "1"; }, 500);
    
    //window.scrollTo(0,document.body.scrollHeight);
    document.getElementById("fieldaddconfirmscroll").scrollIntoView();
    
    timeout = setTimeout(function() { find("fieldaddconfirm").style.opacity = "0"; }, 4000);
    
    
}

/*
 *Helper class to load the options for field types in the AddField() function. 
 *Be sure to update the fieldTypeContent() function. This is where you change the field types available.
 */
function fieldTypeLoad(selectedValue) {
    var response = '<select id="fieldtype' + totalCount + '" name="fieldtype' + totalCount + '" form="addnewworkflow" '+
        'onchange="fieldTypeEdit(this.id);">';
    response += fieldTypeContent(selectedValue);
    response += '</select>';
    
    return response;
}

/*
 *Fixes the HTML so that when a history field is changed, the default value is changed preventing it from switching back. 
 */
function fieldTypeEdit(elem) {
    var selectedValue = find(elem).value;
    
    var response = fieldTypeContent(selectedValue);
    
    updateWorkflowCreationHistory(elem);
    
    response += '</select>';
    
    find(elem).innerHTML = response;
}

function fieldTypeContent(selectedValue) {
    var response = '';
    if(selectedValue == 11) {
        response += '<option value="11" selected>Heading 1</option>';
    } else {
        response += '<option value="11">Heading 1</option>';
    }
    if(selectedValue == 10) {
        response += '<option value="10" selected>Heading 2</option>';
    } else {
        response += '<option value="10">Heading 2</option>';
    }
    if(selectedValue == 12) {
        response += '<option value="12" selected>Heading 3</option>';
    } else {
        response += '<option value="12">Heading 3</option>';
    }
    if(selectedValue == 1) {
        response += '<option value="1" selected>Instruction Text</option>';
    } else {
        response += '<option value="1">Instruction Text</option>';
    }
    if(selectedValue == 0) {
        response += '<option value="0" selected>Entry Box Input</option>';
    } else {
        response += '<option value="0">Entry Box Input</option>';
    }
    if(selectedValue == 15) {
        response += '<option value="15" selected>Text Area</option>';
    } else {
        response += '<option value="15">Text Area</option>';
    }
    if(selectedValue == 4) {
        response += '<option value="4" selected>Checkbox</option>';
    } else {
        response += '<option value="4">Checkbox</option>';
    }
    if(selectedValue == 2) {
        response += '<option value="2" selected>Drop-down List</option>';
    } else {
        response += '<option value="2">Drop-down List</option>';
    }
    if(selectedValue == 13) {
        response += '<option value="13" selected>Radio Button</option>';
    } else {
        response += '<option value="13">Radio Button</option>';
    }
    if(selectedValue == 7) {
        response += '<option value="7" selected>Date Picker</option>';
    } else {
        response += '<option value="7">Date Picker</option>';
    }
    if(selectedValue == 8) {
        response += '<option value="8" selected>Ask a Question</option>';
    } else {
        response += '<option value="8">Ask a Question</option>';
    }
    if(selectedValue == 3) {
        response += '<option value="3" selected>Start a New Line</option>';
    } else {
        response += '<option value="3">Start a New Line</option>';
    }
    if(selectedValue == 9) {
        response += '<option value="9" selected>Horizontal Line</option>';
    } else {
        response += '<option value="9">Horizontal Line</option>';
    }
    if(selectedValue == 5) {
        response += '<option value="5" selected>Autofill Name</option>';
    } else {
        response += '<option value="5">Autofill Name</option>';
    }
    if(selectedValue == 6) {
        response += '<option value="6" selected>Autofill Date</option>';
    } else {
        response += '<option value="6">Autofill Date</option>';
    }
    if(selectedValue == 14) {
        response += '<option value="14" selected>File Upload</option>';
    } else {
        response += '<option value="14">File Upload</option>';
    }
    return response;
}

function editableCheck(selectedValue, type) {
    var response = '<input type="checkbox" id="editable' + totalCount + '" name="editable' + totalCount + '"';
    if(!hideSettings('approvalrights', type) && selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);" ';
    
    if(hideSettings('approvalrights', type))
        response += 'hidden';
    
    response += '>Can this field be modified during approval steps?<br>';
    return response;
}

function approvalonlyCheck(selectedValue) {
    var response = '<input type="checkbox" id="approvalonly' + totalCount + '" name="approvalonly' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);">Approval screen field?';
    return response;
}

function approvalshowCheck(selectedValue, type) {
    var response = '<input type="checkbox" id="approvalshow' + totalCount + '" name="approvalshow' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);"';
    
    if(type == 3 || //create new line
        type == 9) { //horizontal line 
         response += ' hidden';
    }
    
    response += ' title="Can the user who submitted the form see this field once completed?">Displayed on Finished Forms?';
    return response;
}

function requiredCheck(selectedValue, type) {
    var response = '<input type="checkbox" id="requiredfield' + totalCount + '" name="requiredfield' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);" ';
    
    if(hideSettings('fieldsettings', type))
        response += 'hidden';
    
    response += ' title="Does the field have to be filled out by the user?">Required Field';
    return response;
}

function approvalLevel(selectedValue, fieldType) {
    var response = '<select id="approvallevel' + totalCount + '" name="approvallevel' + totalCount + '" form="addnewworkflow" '+
        'onchange="approvalLevelEdit(this.id);toggleExtraApprovalFields(this.id);"';
    if(hideSettings('approvallevels', fieldType)) { 
         response += ' hidden';
    }
    
    response += '>';
    
    if(selectedValue == 0) {
        response += '<option value="0" selected></option>';
    } else {
        response += '<option value="0"></option>';
    }
    if(selectedValue == 1) {
        response += '<option value="1" selected>Level 1</option>';
    } else {
        response += '<option value="1">Level 1</option>';
    }
    if(selectedValue == 2) {
        response += '<option value="2" selected>Level 2</option>';
    } else {
        response += '<option value="2">Level 2</option>';
    }
    if(selectedValue == 3) {
        response += '<option value="3" selected>Level 3</option>';
    } else {
        response += '<option value="3">Level 3</option>';
    }
    if(selectedValue == 4) {
        response += '<option value="4" selected>Level 4</option>';
    } else {
        response += '<option value="4">Level 4</option>';
    }
    response += '</select>';
    
    return response;
}

function approvalLevelEdit(elem) {
    var selectedValue = find(elem).value;
    
    var response = '';
    if(selectedValue == 0) {
        response += '<option selected></option>';
    } else {
        response += '<option></option>';
    }
    if(selectedValue == 1) {
        response += '<option value="1" selected>Level 1</option>';
    } else {
        response += '<option value="1">Level 1</option>';
    }
    if(selectedValue == 2) {
        response += '<option value="2" selected>Level 2</option>';
    } else {
        response += '<option value="2">Level 2</option>';
    }
    if(selectedValue == 3) {
        response += '<option value="3" selected>Level 3</option>';
    } else {
        response += '<option value="3">Level 3</option>';
    }
    if(selectedValue == 4) {
        response += '<option value="4" selected>Level 4</option>';
    } else {
        response += '<option value="4">Level 4</option>';
    }
    response += '</select>';
    
    find(elem).innerHTML = response;
    
}

function toggleCheckbox(elem, remove = 0) {
    var selectedElement = find(elem);
    
    if(!remove && selectedElement.checked) {
        selectedElement.setAttribute("checked", "checked");
    }
    else {
        selectedElement.removeAttribute("checked");
    }
}

function preview() {
    find("count").value = totalCount;
    var updateText = '';
    
    updateText += '<h2 class="center" style="color:black;">Preview of Current Form</h2>';
    
    if(find("behalfof").checked) {
        updateText += '<div class="workflow workflowlabel">Submit on behalf of Employee:</div>' +
            '<div class="workflow workflowright style-1" style="width:150px;"><select></select></div>' +
            '<div class="clear" style="height: 50px;"></div>';
    }
    
    for(var i = 0; i < totalCount; i++) {
        var fieldType = find("fieldtype"+i).value;
        if(find("fieldtype"+i).value == 1) { //Label
            updateText += '<div class="workflow workflowlabel outside-text-center ';
            if(find("approvallevel"+i).value != 0) {
                updateText += 'approval';
            } 
            updateText += '"';
            if(find("workflowsize"+i).value != "") {
                updateText += 'style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '><div class="inside-text-center">' + find("workflowlabel"+i).value + '</div></div>';
        } else if(find("fieldtype"+i).value == 0) { //Textbox
            updateText += '<div class="workflow workflowright style-1 outside-text-center ';
            if(find("approvallevel"+i).value != 0) 
                updateText += 'approval';
            updateText += '"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            updateText += '><div class="inside-text-center">';
            updateText += '<input type="text" placeholder="' + find("workflowlabel"+i).value + '">';
            updateText += '</div></div>';
            
        } else if(find("fieldtype"+i).value == 15) { //Text Area
            updateText += '<div class="workflow workflowright style-1 outside-text-center ';
            if(find("approvallevel"+i).value != 0) 
                updateText += 'approval';
            updateText += '"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            updateText += '><div class="inside-text-center">';
            updateText += '<textarea style="width:100%;height:100px;"></textarea>';
            updateText += '</div></div>';
            
        } else if(find("fieldtype"+i).value == 3) { //Newline
            updateText += '<div class="clear" ';
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="height:' + find("workflowsize"+i).value + 'px;"';
            }
            updateText += '></div>';
        } else if(find("fieldtype"+i).value == 4) { //Checkbox
            updateText += '<div class="workflow workflowlabel outside-text-center ';
            if(find("approvallevel"+i).value != 0) {
                updateText += 'approval';
            } 
            updateText += '"';
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '><div class="inside-text-center">';
            
            updateText += '<input type="checkbox" value="1" ';
            
            //updateText += 'checked';
            updateText +='>' + find("workflowlabel"+i).value + '</div></div>';
        } else if(find("fieldtype"+i).value ==  5) { //Autofill Name
            updateText += '<div class="workflow workflowright style-1 outside-text-center ';
            if(find("approvallevel"+i).value != 0) 
                updateText += 'approval';
            updateText += '"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '><div class="inside-text-center">';
            
            updateText += '<input type="text" placeholder="' + find("workflowlabel"+i).value + '" value="Current User Name" disabled>';
            
            updateText += '</div></div>';
        } else if(find("fieldtype"+i).value ==  6) { //Autofill Date
            if(find("approvallevel"+i).value != 0)
                updateText += '<div class="workflow workflowright style-1 approval"';
            else
                updateText += '<div class="workflow workflowright style-1"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>';
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();
            if(dd < 10){
                dd = '0' + dd
            } 
            if(mm < 10) {
                mm = '0'+ mm
            }
            today = yyyy + '-' + mm + '-' + dd;
            updateText += '<input type="date"' + ' value="' + today + '" disabled>';
            
            updateText += '</div>';
        } else if(find("fieldtype"+i).value ==  7) { //Date
            if(find("approvallevel"+i).value != 0)
                updateText += '<div class="workflow workflowright style-1 approval"';
            else
                updateText += '<div class="workflow workflowright style-1"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>';
            
            updateText += '<input type="date" placeholder="mm/dd/yyyy">';
            
            updateText += '</div>';
        } else if(find("fieldtype"+i).value == 8) { //Simple Question
            //Label portion
            updateText += '<div class="workflow workflowlabel outside-text-center ';
            if(find("approvallevel"+i).value != 0) {
                updateText += 'approval ';
            } 
            updateText += '"';
            if(find("workflowsizea"+i).value != "") {
                updateText += 'style="width:' + find("workflowsizea"+i).value + 'px;"';
            }
            
            updateText += '><div class="inside-text-center">' + find("workflowlabela"+i).value + '</div></div>';
            
            
            //Textbox portion
            if(find("approvallevel"+i).value != 0)
                updateText += '<div class="workflow workflowright style-1 approval"';
            else
                updateText += '<div class="workflow workflowright style-1"';
            
            if(find("workflowsizeb"+i).value != "") {
                updateText += ' style="width:' + find("workflowsizeb"+i).value + 'px;"';
            }
            
            updateText += '>';
            

            updateText += '<input type="text" placeholder="' + find("workflowlabelb"+i).value + '">';
           
            updateText += '</div>';
            
            
        } else if(find("fieldtype"+i).value ==  9) { //Horizontal Line
            updateText += '<div style="clear:both;"></div><hr>';
        } else if(fieldType == 10 || fieldType == 11 || fieldType == 12) { //Heading
            if(find("approvallevel"+i).value != 0) {
                updateText += '<div class="workflow workflowlabel approval" ';
            } else {
                updateText += '<div class="workflow workflowlabel" ';
            }
            updateText += '><h';
            if(fieldType == 11) {
                updateText += '1';
            } else if(fieldType == 12) {
                updateText += '3';
            } else {
                updateText += '2';
            }
            updateText += '>' + find("workflowlabel"+i).value + '</h';
            if(fieldType == 11) {
                updateText += '1';
            } else if(fieldType == 12) {
                updateText += '3';
            } else {
                updateText += '2';
            }
            updateText += '></div>';
        } else if(find("fieldtype"+i).value == 13) { //Radio
            
            for(x = 0; x < find("workflowradiocount"+i).value; x++) {
            
                updateText += '<div class="workflow workflowlabel outside-text-center ';
                if(find("approvallevel"+i).value != 0) {
                    updateText += 'approval';
                } 
                updateText += '"';
                /*if(find("workflowsize"+i).value != "") {
                    updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
                }*/
                
                updateText += '><div class="inside-text-center">';
            
            
                updateText += '<input type="radio" name="workflowradio' + i + '" value="' + find("workflowradio"+i+"-"+x).value +'">' +
                    find("workflowradio"+i+"-"+x).value; 
                
                updateText += '</div></div>';
            }
            
            
        } else if(find("fieldtype"+i).value == 2) { //Option List
            if(find("approvallevel"+i).value != 0) {
                updateText += '<div class="workflow workflowlabel approval"';
            } else {
                updateText += '<div class="workflow workflowlabel"';
            }
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>';
            
            updateText += '<select name="workflowradio' + i + '">';
            
            for(x = 0; x < find("workflowradiocount"+i).value; x++) {
                updateText += '<option value="' + find("workflowradio"+i+"-"+x).value +'">' +
                    find("workflowradio"+i+"-"+x).value + '</option>'; 
            }
            updateText += '</select></div>';
        } else if(find("fieldtype"+i).value == 14) { //File Upload
            updateText += '<div class="workflow workflowright style-1 outside-text-center ';
            if(find("approvallevel"+i).value != 0) 
                updateText += 'approval';
            updateText += '"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            updateText += '><div class="inside-text-center" style="text-align:left;">';
            updateText += '<input type="file" disabled>';
            updateText += '</div></div>';
        }
    }
    
    find("previewform").innerHTML = updateText;
    //window.scrollTo(0, 0);
    find("screen-blackout").style.display = 'inherit';
}

function closePreview() {
    find("screen-blackout").style.display = 'none';
}

/*
 * 
 *
 *You will need to also update the function that updates the history display. -> workflowDetailsFix()
 */
function updateWorkflowCreation(elem) {
    var selectedValue = find(elem).value;
    
    //Reset the number of displayed radio fields
    if(selectedValue == 13 || selectedValue == 2) {
        newRadioFields = DEFAULTNEWRADIOFIELDS;
    }
    
    var text = workflowDetailsFix('', selectedValue, '', '', '', '', 0);
    
    document.getElementById('workflowdetails').innerHTML = text;
    fixExtraFields("", selectedValue);
    toggleExtraSettings(elem);
}

/*
 *When changing field types in the history section of the create workflow page, this will keep what the user 
 *entered into the Ask a Question field type and will display it if the label is selected. 
 */
function updateWorkflowCreationHistory(elem) {
    var id = elem.substr(9, 2);
    
    var label = '', size = '', labelb = '', sizeb = '';
    
    var value = find(elem).value;
    
    if(value == 8) {
        label = find('workflowlabel' + id).value;
        size = find('workflowsize' + id).value;
    } else if(value == 0 || value == 1 || value == 4 || value == 10 || value == 11 || value == 12) {
        if(find('workflowlabela' + id) != null) {
            label = find('workflowlabela' + id).value;
            size = find('workflowsizea' + id).value;
        } else {
            if(find('workflowlabel' + id) != null) {
               label = find('workflowlabel' + id).value;
                size = find('workflowsize' + id).value; 
            }
            
        }
    }
    var text = workflowDetailsFix(id, find(elem).value, label, size, labelb, sizeb, 1);
    document.getElementById('workflowdetails' + id).innerHTML = text;
    
    //Remove fields that aren't needed for certain input types
    fixExtraFields(id, value);
    toggleExtraSettings(elem);
}

function workflowDetailsRadioFix(id, type, size, history, radioFields) {
    return workflowDetailsFixAll(id, type, "", size, "", "", history, radioFields);
}

function workflowDetailsFix(id, type, label, size, labelb, sizeb, history) {
    return workflowDetailsFixAll(id, type, label, size, labelb, sizeb, history, "");
}

/*Changes the fields that are displayed on the create workflow page when you are trying to add a new field.
 *For example when the Ask a Question field is selected, more fields appear. When an entry box input field
 *is selected, some of the fields are removed that are not neccessary.  
 *
 * Hint: Change this code if you want to change the wording for the details of a field type. Ex: If you want
 *       the value field for an entry box to say the word hint instead or adding red asterisks. 
 *
 *
 *
 *Value 8 is the more complicated Ask a Question field type. It consists of a label and an entry box but
 *the end user does not need to know this.
 *
 *@param history If history is enabled, this will configure and change an item in the history only.
 *               If history is disabled, it will configure the section where you add a new field. 
 */
function workflowDetailsFixAll(id, type, label, size, labelb, sizeb, history, radioFields) {
    //alert("your id is: " + id + " type: " + type);
    
    var valueFieldHide = 0;
    var sizeFieldHide = 0;
    
    //Hide the "Text:*" field 
    if(hideSettings('textfield', type))
        valueFieldHide = 1;
    
    //Hide the "Field Size:" field 
    if(hideSettings('fieldsize', type))
        sizeFieldHide = 1;
    
    var text = '';
    if(type != '8' && type != '13' && type != '2') {
        text += 
            '<div class="workflow workflowleft ';
        //Hides the value field
        if(valueFieldHide) { 
            text += 'hide';
        }
        
        text += '">';
        
        if(type == 0) {
            text += 'Hint:';
        } else {
            text += 'Text:' + ((type == 7) ? '' : '<span class="red">*</span>'); //Value
        }
        
        
        text += '</div><div class="workflow workflowright style-1 ';
        //Hides the value field
        if(valueFieldHide) { 
            text += 'hide';
        }
        text += '">' +
                '<input type="text" id="workflowlabel' + id + '" name="workflowlabel' + id + 
                '" maxlength="500" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + label + '"';
        }
        text += '></div><div class="clear"></div>' +
            
            '<div class="workflow workflowleft ';
        //Hides the field size
        if(sizeFieldHide) { 
            text += 'hide';
        }
        text += '">Field Size:</div>' +
            '<div class="workflow workflowright style-1 ';
        //Hides the field size
        if(sizeFieldHide) { 
            text += 'hide';
        }
        text += '">' +
                '<input type="text" id="workflowsize' + id + '" name="workflowsize' + id + '" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + size + '"';
        }
        text += ' title="Enter the width of the field in pixels. (Ex: very small = 50, small = 100, medium = 200, large = 275, x-large = 350)"></div><div class="clear"></div>';
    } else if(type == '8') { //Ask a Question 
        text += 
            '<div class="workflow workflowleft">Question:<span class="red">*</span></div>' +
            '<div class="workflow workflowright style-1"><input type="hidden" name="workflowtypecheck'+ id +'" value="8">' +
                '<input type="text" id="workflowlabela' + id + '" name="workflowlabela' + id + '" maxlength="500" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + label + '"';
        }
        text += '></div><div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Question Size:</div>' +
        '<div class="workflow workflowright style-1">' +
            '<input type="text" id="workflowsizea' + id + '" name="workflowsizea' + id + '" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + size + '"';
        }
        text += '></div><div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Entry box Hint:</div>' +
        '<div class="workflow workflowright style-1">' +
            '<input type="text" id="workflowlabelb' + id + '" name="workflowlabelb' + id + '" maxlength="500" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + labelb + '"';
        }
        text += '></div><div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Entry Box Size:</div>' +
        '<div class="workflow workflowright style-1">' +
            '<input type="text" id="workflowsizeb' + id + '" name="workflowsizeb' + id + '" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + sizeb + '"';
        }
        text += '></div><div class="clear"></div>';
            
    } else if(type == '13') { //Radio Button
        text += 
            '<div class="workflow workflowleft">Radio Options:<span class="red">*</span><input type="hidden" name="workflowtypecheck'+ id +'" value="13">' +
            '</div>';
        
        //Add a "Add radio" button only to the new field section. History does not have this option yet
        if(!history) {
            text += '<div class="workflow workflowright style-1"><button type="button" onclick="addExtraRadioField(0,0);">Add Another Radio Box</button></div><div class="clear"></div>';
        }
        
        
        //Repeat extras
        var length = (radioFields.length == 0) ? newRadioFields : radioFields.length;
        for(var x = 0; x < length; x++) {
            //Fix to add the Add Radio button in the new portion only.
            if(x != 0 || !history) { 
                text += '<div class="workflow workflowleft"></div>';
            }
            text += '<div class="workflow workflowright style-1">' +
                '<input type="text" id="workflowradio' + id + '-' + x + '" name="workflowradio' + id + '-' + x + '" maxlength="500" ';
            if(history == 1 || true) {
                text += 'onchange="changeDefaultVal(this.id);" value="' + (typeof radioFields[x] == 'undefined' ? '' : radioFields[x]) + '"';
            }
            text += '></div><div class="clear"></div>';
        }
        
        text += '<input type="hidden" id="workflowradiocount'+ id +'" name="workflowradiocount'+ id +'" value="' + length + '">';
            
    } else if(type == '2') { //Option List
        text += '<div class="workflow workflowleft ';
        //Hides the field size
        if(sizeFieldHide) { 
            text += 'hide';
        }
        text += '">Field Size:</div>' +
            '<div class="workflow workflowright style-1 ';
        //Hides the field size
        if(sizeFieldHide) { 
            text += 'hide';
        }
        text += '"">' +
                '<input type="text" id="workflowsize' + id + '" name="workflowsize' + id + '" ';
        if(history == 1 || true) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + size + '"';
        }
        text += ' title="Enter the width of the field in pixels. (Ex: very small = 50, small = 100, medium = 200, large = 275, x-large = 350)"></div><div class="clear"></div>';
        
        
        text += 
            '<div class="workflow workflowleft">List Options:<span class="red">*</span><input type="hidden" name="workflowtypecheck'+ id +'" value="2">' +
            '</div>';
        
        //Add a "Add radio" button only to the new field section. History does not have this option yet
        if(!history) {
            text += '<div class="workflow workflowright style-1"><button type="button" onclick="addExtraRadioField(0,0);">Add Another Option Box</button></div><div class="clear"></div>';
        }
        
        
        //Repeat extras
        var length = (radioFields.length == 0) ? newRadioFields : radioFields.length;
        for(var x = 0; x < length; x++) {
            //Fix to add the Add Radio button in the new portion only.
            if(x != 0 || !history) { 
                text += '<div class="workflow workflowleft"></div>';
            }
            text += '<div class="workflow workflowright style-1">' +
                '<input type="text" id="workflowradio' + id + '-' + x + '" name="workflowradio' + id + '-' + x + '" maxlength="500" ';
            if(history == 1 || true) {
                text += 'onchange="changeDefaultVal(this.id);" value="' + (typeof radioFields[x] == 'undefined' ? '' : radioFields[x]) + '"';
            }
            text += '></div><div class="clear"></div>';
        }
        
        text += '<input type="hidden" id="workflowradiocount'+ id +'" name="workflowradiocount'+ id +'" value="' + length + '">';
        
        
    }
    
    
    
    return text;
}

/*
 * Removes the extra fields that are not needed for certain field types.
 * Ex: Create New Line does not need to have to be a required field or appear on approval view only.
 */
function fixExtraFields(id, type) {
    //Set Required Field
    if(hideSettings('fieldsettings', type))
        find("requiredfield" + id).setAttribute("hidden", "true");
    else
        find("requiredfield" + id).removeAttribute("hidden");
    
    //Set if field is editable
    if(hideSettings('approvalrights', type))
        find("editable" + id).setAttribute("hidden", "true");
    else
        find("editable" + id).removeAttribute("hidden");
    
    //Approval level and Displayed on finished forms page for everyone to see
    if(hideSettings('approvallevels', type)) {
        find("approvallevel" + id).setAttribute("hidden", "true");
        find("approvalshow" + id).setAttribute("hidden", "true");
    } else {
        find("approvallevel" + id).removeAttribute("hidden");
        find("approvalshow" + id).removeAttribute("hidden");
    }
}

function toggleExtraApprovalFields(id) {
    var level = find(id).value;
    
    var field = id.split("approvallevel");
    field = field[1];
    
    if(level == '') {
        level = 0;
    }
    //console.log(level);
    if(level > 0) {
        find("displayfinishmain1" + field).removeAttribute("hidden");
        find("displayfinishmain2" + field).removeAttribute("hidden");
    } else {
        //console.log('hidding |displayfinishmain1' + field + '| and |displayfinishmain2' + field+"|");
        find("displayfinishmain1" + field).setAttribute("hidden", "true");
        find("displayfinishmain2" + field).setAttribute("hidden", "true");
    }
    
    var msg = 'A level ' + level +' approver has not yet been selected. ' +
            'Either add an approver group for level ' + level + 
            ' at the top or select a lower approal level on this field.';
    if(level == 2 && find('destination2').selectedIndex == '0' ||
        level == 3 && find('destination3').selectedIndex == '0' ||
        level == 4 && find('destination4').selectedIndex == '0'
        ) {
        alert(msg);
    }
}

function toggleExtraSettings(id) {
    var fieldType = find(id).value;
    
    var field = id.split("fieldtype");
    field = field[1];
    
    //#hidesettingsMATCH
    if(hideSettings('fieldsettings', fieldType)) { 
        find("fieldsettings1" + field).setAttribute("hidden", "true");
        find("fieldsettings2" + field).setAttribute("hidden", "true");
        toggleCheckbox("requiredfield" + field, 1);
    } else {
        find("fieldsettings1" + field).removeAttribute("hidden");
        find("fieldsettings2" + field).removeAttribute("hidden");
        toggleCheckbox("requiredfield" + field);
    }
    
    if(hideSettings('approvalrights', fieldType)) { 
        find("approvalrights1" + field).setAttribute("hidden", "true");
        find("approvalrights2" + field).setAttribute("hidden", "true");
        toggleCheckbox("editable" + field, 1);
    } else {
        find("approvalrights1" + field).removeAttribute("hidden");
        find("approvalrights2" + field).removeAttribute("hidden");
        toggleCheckbox("editable" + field);
    }
    
    if(hideSettings('approvallevels', fieldType)) { 
        find("approvallevels1" + field).setAttribute("hidden", "true");
        find("approvallevels2" + field).setAttribute("hidden", "true");
        /*find("displayfinishmain1" + field).setAttribute("hidden", "true");
        find("displayfinishmain2" + field).setAttribute("hidden", "true");*/
        var obj = find('approvallevel' + field);
        obj.value = 0;
        toggleExtraApprovalFields(obj.id);
    } else {
        find("approvallevels1" + field).removeAttribute("hidden");
        find("approvallevels2" + field).removeAttribute("hidden");
    }
}

function toggleApproverLevelFields(id) {
    var value = find(id).value;
    
    var field = id.split("destination");
    field = field[1];
    /*alert('destination is : ' + field + ' and the value is : ' + value);*/
    if(field == 2) {
        if(value == '') {
            find('destination3-1').setAttribute("hidden", "true");
            find('destination3-2').setAttribute("hidden", "true");
            find('destination4-1').setAttribute("hidden", "true");
            find('destination4-2').setAttribute("hidden", "true");
            find('destination3').selectedIndex = '0';
            find('destination4').selectedIndex = '0';
        } else {
            
            find('destination3-1').removeAttribute("hidden");
            find('destination3-2').removeAttribute("hidden");
        }
    } else if(field == 3) {
        if(value == '') {
            find('destination4-1').setAttribute("hidden", "true");
            find('destination4-2').setAttribute("hidden", "true");
            find('destination4').selectedIndex = '0';
        } else {
            
            find('destination4-1').removeAttribute("hidden");
            find('destination4-2').removeAttribute("hidden");
        }
    }
    
    
    
}

function addExtraRadioField(id, x) {
    id = '';
    x = newRadioFields++;
    text = '<div class="workflow workflowleft"></div>';

    text += '<div class="workflow workflowright style-1">' +
        '<input type="text" id="workflowradio' + id + '-' + x + '" name="workflowradio' + id + '-' + x + '" maxlength="500" ';
    text += 'onchange="changeDefaultVal(this.id);" ';
    text += '></div><div class="clear"></div>';
    find('workflowdetails').innerHTML += text;
}

function scrollDown() {
    window.scrollTo(0,document.body.scrollHeight);
}


function swap(id, moveup) {
    var exists = 0;
    var fieldToSwap = -1;
    if(moveup) {
        if(find("field" + (id - 1)) != null) {
            exists = 1;
            fieldToSwap = id - 1;
        }
    } else {
        if(find("field" + (id + 1)) != null) {
            exists = 1;
            fieldToSwap = id + 1;
        }
    }
    
    if(fieldToSwap == -1) {
        //alert("Can't swap.");
        return;
    } 
    //alert("Swapping field: " + id + " with field: " + fieldToSwap);
    
    
    var field1 = find("field" + id).innerHTML;
    var field2 = find("field" + fieldToSwap).innerHTML;
    
    find("field" + fieldToSwap).innerHTML = swapConversion(field1, id, fieldToSwap);
    find("field" + id).innerHTML = swapConversion(field2, fieldToSwap, id);
    
    //alert('done');
    
    
    
}

function swapConversion(text, oldID, newID) {
    text = text.split("workflowdetails" + oldID).join("workflowdetails" + newID);
    text = text.split("fieldtype" + oldID).join("fieldtype" + newID);
    text = text.split("workflowlabel" + oldID).join("workflowlabel" + newID);
    text = text.split("workflowsize" + oldID).join("workflowsize" + newID);
    text = text.split("requiredfield" + oldID).join("requiredfield" + newID);
    text = text.split("editable" + oldID).join("editable" + newID);
    text = text.split("approvallevel" + oldID).join("approvallevel" + newID);
    text = text.split("displayfinishmain1" + oldID).join("displayfinishmain1" + newID);
    text = text.split("displayfinishmain2" + oldID).join("displayfinishmain2" + newID);
    text = text.split("approvalshow" + oldID).join("approvalshow" + newID);
    
    text = text.split("fieldsettings1" + oldID).join("fieldsettings1" + newID);
    text = text.split("fieldsettings2" + oldID).join("fieldsettings2" + newID);
    
    text = text.split("approvalrights1" + oldID).join("approvalrights1" + newID);
    text = text.split("approvalrights2" + oldID).join("approvalrights2" + newID);
    
    text = text.split("approvallevels1" + oldID).join("approvallevels1" + newID);
    text = text.split("approvallevels2" + oldID).join("approvallevels2" + newID);
    
    //Fix radio inputs
    text = text.split("workflowradio" + oldID).join("workflowradio" + newID);
    text = text.split("workflowradiocount" + oldID).join("workflowradiocount" + newID);
    
    
    text = text.split("workflowlabela" + oldID).join("workflowlabela" + newID);
    text = text.split("workflowsizea" + oldID).join("workflowsizea" + newID);
    text = text.split("workflowlabelb" + oldID).join("workflowlabelb" + newID);
    text = text.split("workflowsizeb" + oldID).join("workflowsizeb" + newID);
    text = text.split("workflowtypecheck" + oldID).join("workflowtypecheck" + newID);
    
    return text;
}

function removeField(id) {
    
    for(var i = id; i < totalCount - 1; i++) {
        swap(i, 0);
    }
    
    document.getElementById("fieldwrap"+(totalCount - 1)).remove();
    totalCount--;
}

function processWorkflow(status) {
    find("submitmode").value = status;
    find("savedData").defaultValue = find("workflowfields").innerHTML;
    document.getElementById('formsubmitbutton').click();
}

function hideSettings(setting, fieldType) {
    if(setting == 'fieldsettings') { //Hides the "Required Field" checkbox when creating a form
        if(fieldType == 11 || //Heading 1
            fieldType == 10 || //Heading 
            fieldType == 12 || //Heading 3
            fieldType == 1 || //Instruction 
            fieldType == 3 || //New Line 
            fieldType == 4 || //Checkbox 
            fieldType == 5 || //Autofill Name 
            fieldType == 6 || //Autofill Date 
            fieldType == 9 //Horizontal line 
            ) {
            return 1;
        } else {
            return 0;
        }
    } else if(setting == 'approvalrights') { //Hides the "Can this field be modified during approval steps?" checkbox
        if(fieldType == 11 || //Heading 1
            fieldType == 10 || //Heading 
            fieldType == 12 || //Heading 
            fieldType == 1 || //Instruction  
            fieldType == 3 || //New Line  
            fieldType == 5 || //Autofill Name 
            fieldType == 6 || //Autofill Date 
            fieldType == 9 || //Horizontal line 
            fieldType == 14 //File Upload 
            ) {
            return 1;
        } else {
            return 0;
        }
    } else if(setting == 'approvallevels') { //Hides all the approval type options
        if(fieldType == 3 || //new line
            fieldType == 9 //Horizontal line 
            ) { 
            return 1;
        } else {
            return 0;
        }
    } else if(setting == 'textfield') { //Hides the "Text:*" field 
        if( fieldType == 3 || //create new line
            fieldType == 5 || //autofill name
            fieldType == 6 || //autofill date
            fieldType == 9 || //horizontal line
            fieldType == 15 || //Text Area
            fieldType == 14 //File Upload
            ) {
            return 1;
        } else {
            return 0;
        }
    } else if(setting == 'fieldsize') { //Hides the "Field Size:" field 
        if( fieldType == 10 || //heading
            fieldType == 11 || //heading 1
            fieldType == 12 || //heading 3
            fieldType == 9 || //horizontal line
            fieldType == 14 //File Upload
            ) {
            return 1;
        } else {
            return 0;
        }
    }
}
