/*
* Performs various actions for the workflow forms.
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/

var totalCount = 0;


function test() {
    alert("HAHA");
}

function find(elem) {
    return document.getElementById(elem);
}

function changeDefaultVal(elem) {
    //alert(elem);
    //alert(find(elem).value);
    find(elem).defaultValue = find(elem).value;
}

function fixQuotes(text) {
    text = text.split('"').join('&quot;');
    text = text.split('<').join('&lt;');
    text = text.split('>').join('&gt;');
    return text;
}

function addField() {
    var text = '';
    
    text += '<div id="fieldwrap' + totalCount + '"><div id="field' + totalCount + '">' +
        '<div class="workflow workflowleft">Field type:</div>' +
        '<div class="workflow workflowright style-1">' +
            fieldTypeLoad(find("fieldtype").value) +
        '</div>' +
        '<div class="clear"></div>' +
        
        '<div id="workflowdetails' + totalCount + '">';
        
    if(find("fieldtype").value != '8') {
        text += workflowDetailsFix(totalCount, find("fieldtype").value, fixQuotes(find("workflowlabel").value), find("workflowsize").value, '', '', 1);
    } else if(find("fieldtype").value == '8') {
        text += workflowDetailsFix(totalCount, find("fieldtype").value, find("workflowlabela").value, find("workflowsizea").value, 
            find("workflowlabelb").value, find("workflowsizeb").value, 1);
    }
    text += '</div>';
    text += 
        '<div class="workflow workflowleft">Field Settings:</div>' +
        '<div class="workflow workflowright style-1">' +
            requiredCheck(find("requiredfield").checked) + '<br>' +
        '</div><div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Approval Rights:</div>' +
        '<div class="workflow workflowright style-1">' +
            editableCheck(find("editable").checked) +
            //approvalonlyCheck(find("approvalonly").checked) +
        '</div>' +
        '<div class="clear"></div>' + //<br>
        '<div class="workflow workflowleft">Approval Level:</div>' + 
        '<div class="workflow workflowright style-1">' +
            approvalLevel(find("approvallevel").value) +
        '</div><div class="clear"></div>' +
        '<div class="workflow workflowleft">Display Settings:</div>' +
        '<div class="workflow workflowright style-1">' +
            approvalshowCheck(find("approvalshow").checked) +
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
    
    window.scrollTo(0,document.body.scrollHeight);
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
    if(selectedValue == 10) {
        response += '<option value="10" selected>Heading</option>';
    } else {
        response += '<option value="10">Heading</option>';
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
    if(selectedValue == 4) {
        response += '<option value="4" selected>Checkbox Input</option>';
    } else {
        response += '<option value="4">Checkbox Input</option>';
    }
    if(selectedValue == 7) {
        response += '<option value="7" selected>Date Input</option>';
    } else {
        response += '<option value="7">Date Input</option>';
    }
    if(selectedValue == 8) {
        response += '<option value="8" selected>Ask a Question</option>';
    } else {
        response += '<option value="8">Ask a Question</option>';
    }
    if(selectedValue == 3) {
        response += '<option value="3" selected>Create a Newline</option>';
    } else {
        response += '<option value="3">Create a Newline</option>';
    }
    if(selectedValue == 9) {
        response += '<option value="9" selected>Header Row</option>';
    } else {
        response += '<option value="9">Header Row</option>';
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
    if(selectedValue == 2) {
        response += '<option value="2" selected>Option</option>';
    } else {
        response += '<option value="2">Option</option>';
    }
    return response;
}

function editableCheck(selectedValue) {
    var response = '<input type="checkbox" id="editable' + totalCount + '" name="editable' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);">Can this field be modified during approval steps?<br>';
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

function approvalshowCheck(selectedValue) {
    var response = '<input type="checkbox" id="approvalshow' + totalCount + '" name="approvalshow' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);">Displayed on Finished Forms (Approval fields only)?';
    return response;
}

function requiredCheck(selectedValue) {
    var response = '<input type="checkbox" id="requiredfield' + totalCount + '" name="requiredfield' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);">Required Field';
    return response;
}

function approvalLevel(selectedValue) {
    var response = '<select id="approvallevel' + totalCount + '" name="approvallevel' + totalCount + '" form="addnewworkflow" '+
        'onchange="approvalLevelEdit(this.id)">';
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

function toggleCheckbox(elem) {
    var selectedValue = find(elem).value;
    
    if(find(elem).checked) {
        find(elem).setAttribute("checked", "checked");
    }
    else {
        find(elem).removeAttribute("checked");
    }
}

function formValidate() {
    if(find("workflowname").value == "") {
        find("workflowname").className = "error";
        pageExitOK = false;
        window.scrollTo(0, 0);
        return false;
    } else {
        find("workflowname").className = "";
    }
    
    return true;
}

function saveSubmission(status, approver) {
    find("ns").value = status;
    
    if(status == 10) {
        if (confirm("Are you sure you want to delete this form?") == false) {
            return;
        }
    } else if(status == 8) {
        if(approver == 1) {
            if (confirm("Are you sure you want to deny this form?") == false) {
                return;
            }
        } else {
            if (confirm("Are you sure you want to cancel this form submission?") == false) {
                return;
            }
        }
    } else if(status == 0) {
        return;
    }
    
    if(status == 2 || status == 3 || status == 8 || status == 10) {
        document.getElementById('workflowsubmission').submit();
    } else {
        document.getElementById('formsubmitbutton').click();
    }
        
    //document.getElementById('workflowsubmission').submit();
}

function submissioncheck() {
    if(find("ns").value == 0) {
        return false;
    } 
    
    return true;
}

function preview() {
    find("count").value = totalCount;
    var updateText = '';
    
    if(find("behalfof").checked) {
        updateText += '<div class="workflow workflowlabel">Submit on behalf of Employee:</div>' +
            '<div class="workflow workflowright style-1" style="width:150px;"><select></select></div>' +
            '<div class="clear" style="height: 50px;"></div>';
    }
    
    for(var i = 0; i < totalCount; i++) {
        
        if(find("fieldtype"+i).value == 1) { //Label
            if(find("approvallevel"+i).value != 0) {
                updateText += '<div class="workflow workflowlabel approval" ';
            } else {
                updateText += '<div class="workflow workflowlabel" ';
            }
            
            if(find("workflowsize"+i).value != "") {
                updateText += 'style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>' + find("workflowlabel"+i).value + '</div>';
        } else if(find("fieldtype"+i).value == 0) { //Textbox
            if(find("approvallevel"+i).value != 0) 
                updateText += '<div class="workflow workflowright style-1 approval"';
            else
                updateText += '<div class="workflow workflowright style-1"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>';
            

            updateText += '<input type="text" placeholder="' + find("workflowlabel"+i).value + '">';
           
            updateText += '</div>';
            
        } else if(find("fieldtype"+i).value == 2) { //Option
            updateText += ' ';
        } else if(find("fieldtype"+i).value == 3) { //Newline
            updateText += '<div class="clear" ';
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="height:' + find("workflowsize"+i).value + 'px;"';
            }
            updateText += '></div>';
        } else if(find("fieldtype"+i).value == 4) { //Checkbox
            if(find("approvallevel"+i).value != 0) {
                updateText += '<div class="workflow workflowlabel approval"';
            } else {
                updateText += '<div class="workflow workflowlabel"';
            }
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>';
            
            updateText += '<input type="checkbox" value="1" ';
            
            updateText += 'checked';
            updateText +='>' + find("workflowlabel"+i).value + '</div>';
        } else if(find("fieldtype"+i).value ==  5) { //Autofill Name
            if(find("approvallevel"+i).value != 0) 
                updateText += '<div class="workflow workflowright style-1 approval"';
            else
                updateText += '<div class="workflow workflowright style-1"';
            
            if(find("workflowsize"+i).value != "") {
                updateText += ' style="width:' + find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>';
            
            updateText += '<input type="text" placeholder="' + find("workflowlabel"+i).value + '" value="Current User Name" disabled>';
            
            updateText += '</div>';
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
            if(find("approvallevel"+i).value != 0) {
                updateText += '<div class="workflow workflowlabel approval" ';
            } else {
                updateText += '<div class="workflow workflowlabel" ';
            }
            
            if(find("workflowsizea"+i).value != "") {
                updateText += 'style="width:' + find("workflowsizea"+i).value + 'px;"';
            }
            
            updateText += '>' + find("workflowlabela"+i).value + '</div>';
            
            
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
            
            
        } else if(find("fieldtype"+i).value ==  9) { //Header Row
            updateText += '<div style="clear:both;"></div><hr>';
        } else if(find("fieldtype"+i).value == 10) { //Heading
            if(find("approvallevel"+i).value != 0) {
                updateText += '<div class="workflow workflowlabel approval" ';
            } else {
                updateText += '<div class="workflow workflowlabel" ';
            }
            
            updateText += '><h2>' + find("workflowlabel"+i).value + '</h2></div>';
        }
    }
    
    find("previewform").innerHTML = updateText;
    window.scrollTo(0, 0);
}

/*
 * 
 *
 *You will need to also update the function that updates the history display. -> workflowDetailsFix()
 */
function updateWorkflowCreation(elem) {
    var selectedValue = find(elem).value;
    var text = workflowDetailsFix('', selectedValue, '', '', '', '', 0);
    
    document.getElementById('workflowdetails').innerHTML = text;
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
    } else if(value == 0 || value == 1 || value == 4) {
        if(find('workflowlabela' + id) != null) {
            label = find('workflowlabela' + id).value;
            size = find('workflowsizea' + id).value;
        } else {
            label = find('workflowlabel' + id).value;
            size = find('workflowsize' + id).value;
        }
    }
    var text = workflowDetailsFix(id, find(elem).value, label, size, labelb, sizeb, 1);
    document.getElementById('workflowdetails' + id).innerHTML = text;
    
    
}

/*Changes the fields that are displayed on the create workflow page when you are trying to add a new field.
 *For example when the Ask a Question field is selected, more fields appear. When an entry box input field
 *is selected, some of the fields are removed that are not neccessary.  
 *
 *Value 8 is the more complicated Ask a Question field type. It consists of a label and an entry box but
 *the end user does not need to know this.
 *
 *@param history If history is enabled, this will configure and change an item in the history only.
 *               If history is disabled, it will configure the section where you add a new field. 
 */
function workflowDetailsFix(id, type, label, size, labelb, sizeb, history) {
    var text = '';
    if(type != '8') {
        text += 
            '<div class="workflow workflowleft ';
        if(type == 3 || type == 9) { //Hides the value field
            text += 'hide';
        }
        
        text += '">Value:</div>' +
            '<div class="workflow workflowright style-1 ';
        if(type == 3 || type == 9) { //Hides the value field
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
        if(type == 9) { //Hides the field size
            text += 'hide';
        }
        text += '">Field Size:</div>' +
            '<div class="workflow workflowright style-1 ';
        if(type == 9) { //Hides the field size
            text += 'hide';
        }
        text += '">' +
                '<input type="text" id="workflowsize' + id + '" name="workflowsize' + id + '" ';
        if(history == 1) {
            text += 'onchange="changeDefaultVal(this.id);" value="' + size + '"';
        }
        text += '></div><div class="clear"></div>';
    } else if(type == '8') {
        text += 
            '<div class="workflow workflowleft">Question:</div>' +
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
            
    }
    return text;
}

var savedSupervisor;
var savedSup = 0;
function updateSupervisorButton() {
    
    if(savedSup == 0) {
        savedSupervisor = find('supervisor-radio').innerHTML;
        savedSup = 1;
    }
    
    if(find('onbehalf').value == 'Myself') {
        find('supervisor-radio').innerHTML = savedSupervisor;
    } else {
        find('supervisor-radio').innerHTML = '<input type="radio" name="nextsupervisor" value="0" checked>Supervisor'+
        '<input type="radio" name="nextsupervisor" value="1">2nd Supervisor';
    }
}

var pending = 0;
var approved = 0;
var denied = 0;
var cancelled = 0;
function extendResults(category, max) {
    var current = -1;
    
    if(category == "pending") {
        pending++;
        current = pending;
    } else if(category == "approved") {
        approved++;
        current = approved;
    } else if(category == "denied") {
        denied++;
        current = denied;
    } else if(category == "cancelled") {
        cancelled++;
        current = cancelled;
    } 
    
    //alert('Called:'+category + current + ' max:' + max);
    if(current >= max) {
        return;
    }
    
    if(current != -1) {
        var elements = document.getElementsByClassName(category + current);
        for(var i=0; i<elements.length; i++) {
            elements[i].style["display"] = '';
        }
        
        if(current + 1 >= max) {
            find(category + "more").style.display = "none";
        }
    }
    
}

function clearSearch() {
    find("idsearch").defaultValue = '';
    find("formsearch").defaultValue = '';
    find("submittedsearch").defaultValue = '';
    find("datesearch").defaultValue = '';
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
    text = text.split("fieldtype" + oldID).join("fieldtype" + newID);
    text = text.split("workflowlabel" + oldID).join("workflowlabel" + newID);
    text = text.split("workflowsize" + oldID).join("workflowsize" + newID);
    text = text.split("requiredfield" + oldID).join("requiredfield" + newID);
    text = text.split("editable" + oldID).join("editable" + newID);
    text = text.split("approvallevel" + oldID).join("approvallevel" + newID);
    text = text.split("approvalshow" + oldID).join("approvalshow" + newID);
    
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
    if(status == 1 || status == 2) {
        find("savedData").defaultValue = find("workflowfields").innerHTML;
    }
    
    
    document.getElementById('formsubmitbutton').click();
}