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

function $find(elem) {
    return document.getElementById(elem);
}

function changeDefaultVal(elem) {
    //alert(elem);
    //alert($find(elem).value);
    $find(elem).defaultValue = $find(elem).value;
}

function addField() {
    //alert($find("fieldtype").value);
    
    //$find("debugworkflowfields").innerHTML += $find("fieldtype").value + " | " + $find("workflowlabel").value + " | "+
    //    $find("editable").checked + " | "+ $find("approvalonly").checked + " : Field ID = " + totalCount + "<br>";
    
    
    
    $find("workflowfields").innerHTML += '' +
        '<div class="workflow workflowleft">Field type:</div>' +
        '<div class="workflow workflowright style-1">' +
            fieldTypeCheck($find("fieldtype").value) +
        '</div>' +
        '<div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Value:</div>' +
        '<div class="workflow workflowright style-1">' +
            '<input type="text" id="workflowlabel' + totalCount + '" name="workflowlabel' + totalCount + 
            '" onchange="changeDefaultVal(this.id);" value="' + 
            $find("workflowlabel").value + '">' +
        '</div>' +
        '<div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Field Size:</div>' +
        '<div class="workflow workflowright style-1">' +
            '<input type="text" id="workflowsize' + totalCount + '" name="workflowsize' + totalCount + 
            '" onchange="changeDefaultVal(this.id);" value="' + 
            $find("workflowsize").value + '">' +
        '</div>' +
        '<div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Field Settings:</div>' +
        '<div class="workflow workflowright style-1">' +
            requiredCheck($find("requiredfield").checked) + '<br>' +
        '</div><div class="clear"></div>' +
        
        '<div class="workflow workflowleft">Approval Rights:</div>' +
        '<div class="workflow workflowright style-1">' +
            editableCheck($find("editable").checked) +
            approvalonlyCheck($find("approvalonly").checked) +
        '</div>' +
        '<div class="clear"></div>' +
        '<br><div class="workflow workflowleft">Approval Level:</div>' + 
        '<div class="workflow workflowright style-1">' +
            approvalLevel($find("approvallevel").value) +
        '</div><div class="clear"></div>' +
        '<div class="workflow workflowleft">Display Settings:</div>' +
        '<div class="workflow workflowright style-1">' +
            approvalshowCheck($find("approvalshow").checked) +
            '<br></div><div class="clear"></div>' + 
        '<div class="workflow workflowboth" style="margin-top: 20px;"><hr></div><div class="clear"></div>';
    
    totalCount++;
    $find("count").value = totalCount;
    
    window.scrollTo(0,document.body.scrollHeight);
}


function fieldTypeCheck(selectedValue) {
    var response = '<select id="fieldtype' + totalCount + '" name="fieldtype' + totalCount + '" form="addnewworkflow" '+
        'onchange="fieldTypeEdit(this.id)">';
    if(selectedValue == 0) {
        response += '<option value="0" selected>Textbox</option>';
    } else {
        response += '<option value="0">Textbox</option>';
    }
    if(selectedValue == 1) {
        response += '<option value="1" selected>Label</option>';
    } else {
        response += '<option value="1">Label</option>';
    }
    if(selectedValue == 2) {
        response += '<option value="2" selected>Option</option>';
    } else {
        response += '<option value="2">Option</option>';
    }
    if(selectedValue == 3) {
        response += '<option value="3" selected>Newline</option>';
    } else {
        response += '<option value="3">Newline</option>';
    }
    if(selectedValue == 4) {
        response += '<option value="4" selected>Checkbox</option>';
    } else {
        response += '<option value="4">Checkbox</option>';
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
    if(selectedValue == 7) {
        response += '<option value="7" selected>Date</option>';
    } else {
        response += '<option value="7">Date</option>';
    }
    response += '</select>';
    
    return response;
}

function fieldTypeEdit(elem) {
    //alert($find(elem).value);
    var selectedValue = $find(elem).value;
    
    //$find(elem).defaultValue = $find(elem).value;
    var response = '';
    if(selectedValue == 0) {
        response += '<option value="0" selected>Textbox</option>';
    } else {
        response += '<option value="0">Textbox</option>';
    }
    if(selectedValue == 1) {
        response += '<option value="1" selected>Label</option>';
    } else {
        response += '<option value="1">Label</option>';
    }
    if(selectedValue == 2) {
        response += '<option value="2" selected>Option</option>';
    } else {
        response += '<option value="2">Option</option>';
    }
    if(selectedValue == 3) {
        response += '<option value="3" selected>Newline</option>';
    } else {
        response += '<option value="3">Newline</option>';
    }
    if(selectedValue == 4) {
        response += '<option value="4" selected>Checkbox</option>';
    } else {
        response += '<option value="4">Checkbox</option>';
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
    if(selectedValue == 7) {
        response += '<option value="7" selected>Date</option>';
    } else {
        response += '<option value="7">Date</option>';
    }
    response += '</select>';
    
    $find(elem).innerHTML = response;
    
}

function editableCheck(selectedValue) {
    var response = '<input type="checkbox" id="editable' + totalCount + '" name="editable' + totalCount + '"';
    if(selectedValue == true) {
        response += ' checked'; 
    }
    response += ' onchange="toggleCheckbox(this.id);">Editable on approval screen?<br>';
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
    var selectedValue = $find(elem).value;
    
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
    
    $find(elem).innerHTML = response;
    
}

function toggleCheckbox(elem) {
    var selectedValue = $find(elem).value;
    //alert(elem + ' ' + $find(elem).checked);
    
    if($find(elem).checked) {
        //alert('adding');
        $find(elem).setAttribute("checked", "checked");
    }
    else {
        $find(elem).removeAttribute("checked");
        //alert('removing');
    }
    //$find(elem).checked = false;
    //$find(elem).innerHTML = response;
}

function formValidate() {
    if($find("workflowname").value == "") {
        $find("workflowname").className = "error";
        return false;
        //alert("You did not put in a name!");
    } else {
        $find("workflowname").className = "";
        //$find("workflowname").className = "workflow workflowright style-1";
        //alert("fixed");
    }
    
    return true;
}

function saveSubmission(status, approver) {
    $find("ns").value = status;
    
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
    }
    
    if(status == 2 || status == 3 || status == 8 || status == 10) {
        document.getElementById('workflowsubmission').submit();
    } else {
        document.getElementById('formsubmitbutton').click();
    }
        
    //document.getElementById('workflowsubmission').submit();
}


function preview() {
    $find("count").value = totalCount;
    var updateText = '';
    
    if($find("behalfof").checked) {
        updateText += '<div class="workflow workflowlabel">Submit on behalf of Employee Number:</div>' +
            '<div class="workflow workflowright style-1" style="width:150px;"><input type="text" id="onbehalf" name="onbehalf"'+
            'placeholder="Emp Num"></div>' +
            '<div class="clear" style="height: 50px;"></div>';
    }
    
    
    for(var i = 0; i < totalCount; i++) {
        //$find("previewform").innerHTML += i + " ";
        
        
        if($find("fieldtype"+i).value == 1) { //Label
            //$find("previewform").innerHTML += i + " found " + $find("workflowlabel"+i).value + " | ";
            if($find("approvalonly"+i).checked) {
                updateText += '<div class="workflow workflowlabel approval" ';
                //alert("found approvalonly"+i);
            } else {
                updateText += '<div class="workflow workflowlabel" ';
                //alert("didnt find approvalonly"+i);
            }
            
            if($find("workflowsize"+i).value != "") {
                updateText += 'style="width:' + $find("workflowsize"+i).value + 'px;"';
            }
            
            updateText += '>' + $find("workflowlabel"+i).value + '</div>';
        } else if($find("fieldtype"+i).value == 0) { //Textbox
                if($find("approvalonly"+i).checked)
                    updateText += '<div class="workflow workflowright style-1 approval"';
                else
                    updateText += '<div class="workflow workflowright style-1"';
                
                if($find("workflowsize"+i).value != "") {
                    updateText += ' style="width:' + $find("workflowsize"+i).value + 'px;"';
                }
                
                updateText += '>';
                

                updateText += '<input type="text" placeholder="' + $find("workflowlabel"+i).value + '">';
               
                updateText += '</div>';
                
                    
                    
                    
                    
                    
            } else if($find("fieldtype"+i).value == 2) { //Option
                updateText += ' ';
            } else if($find("fieldtype"+i).value == 3) { //Newline
                updateText += '<div class="clear" ';
                if($find("workflowsize"+i).value != "") {
                    updateText += ' style="height:' + $find("workflowsize"+i).value + 'px;"';
                }
                updateText += '></div>';
            } else if($find("fieldtype"+i).value == 4) { //Checkbox
                if($find("approvalonly"+i).checked) {
                    updateText += '<div class="workflow workflowlabel approval"';
                } else {
                    updateText += '<div class="workflow workflowlabel"';
                }
                if($find("workflowsize"+i).value != "") {
                    updateText += ' style="width:' + $find("workflowsize"+i).value + 'px;"';
                }
                
                updateText += '>';
                
                updateText += '<input type="checkbox" value="1" ';
                
                updateText += 'checked';
                updateText +='>' + $find("workflowlabel"+i).value + '</div>';
            } else if($find("fieldtype"+i).value ==  5) { //Autofill Name
                if($find("approvalonly"+i).checked)
                    updateText += '<div class="workflow workflowright style-1 approval"';
                else
                    updateText += '<div class="workflow workflowright style-1"';
                
                if($find("workflowsize"+i).value != "") {
                    updateText += ' style="width:' + $find("workflowsize"+i).value + 'px;"';
                }
                
                updateText += '>';
                
                updateText += '<input type="text" placeholder="' + $find("workflowlabel"+i).value + '" value="Current User Name" disabled>';
                
                updateText += '</div>';
            } else if($find("fieldtype"+i).value ==  6) { //Autofill Date
                if($find("approvalonly"+i).checked)
                    updateText += '<div class="workflow workflowright style-1 approval"';
                else
                    updateText += '<div class="workflow workflowright style-1"';
                
                if($find("workflowsize"+i).value != "") {
                    updateText += ' style="width:' + $find("workflowsize"+i).value + 'px;"';
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
            } else if($find("fieldtype"+i).value ==  7) { //Date
                if($find("approvalonly"+i).checked)
                    updateText += '<div class="workflow workflowright style-1 approval"';
                else
                    updateText += '<div class="workflow workflowright style-1"';
                
                if($find("workflowsize"+i).value != "") {
                    updateText += ' style="width:' + $find("workflowsize"+i).value + 'px;"';
                }
                
                updateText += '>';
                
                updateText += '<input type="date" placeholder="mm/dd/yyyy">';
                
                updateText += '</div>';
            }
        
    }
    
    $find("previewform").innerHTML = updateText;
    window.scrollTo(0, 0);
}