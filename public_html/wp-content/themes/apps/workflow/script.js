/*
* Performs various actions for the workflow forms.
* //TODO: create better documentation
*
* If you are making changes to the way a field behaves, follow these steps:
* -In the addField() function, make sure to create a div that can be targeted by javascript
*  Go to a div and add id="myuniquename' + totalCount +'" 
*
* -Also add an if statement that checks whether it should be hidden when it gets coped to the history
*  area. EX: skipfieldsettings
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
* author: gerald.becker
*
*/

var totalCount = 0;
var timeout;
var newRadioFields = 2;
var DEFAULTNEWRADIOFIELDS = 2;
var submissionLink = true;

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

function formValidate() {
    if(find("workflowname").value == "") {
        find("workflowname").className = "error";
        pageExitOK = false;
        window.scrollTo(0, 0);
        return false;
    } else {
        find("workflowname").className = "";
    }
    if(!pageExitOK || preventEnterSubmission) {
        return false;
    }
    return true;
}

function preventSubmission(event) {
    //Avoid submitting form on the enter key
    if(event.keyCode == 13) {
        preventEnterSubmission = true;
        pageExitOK = false;
        setTimeout(function(){preventEnterSubmission = false;pageExitOK = false;}, 3000);
    }
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
    } else if(status == 62) {
        if (confirm("Are you sure you want to void this form submission?") == false) {
            return;
        }
    }
    //Prevent submissions on client side
    if(status == 4) {
        var sup = find("directsupervisor");
        if(sup != null) {
            if(sup.length == 0 || sup[sup.options.selectedIndex].text == "" || sup[sup.options.selectedIndex].text == null) {
                find("warningmsg").innerHTML = 'Please select a supervisor from the dropdown. If there is no supervisor available, click Save Draft and contact <a href="mailto:helpdesk@p2c.com" target="blank">helpdesk@p2c.com</a>.';
                return;
            }
        }
    }
    if(status == 2 || status == 3 || status == 8 || status == 10 || status == 20 || 
        status == 50 || (60 <= status && status <= 63)) {
        processBtnToggle(0);
        document.getElementById('workflowsubmission').submit();
        setTimeout(function(){ processBtnToggle(1); }, 5000);
    } else {
        processBtnToggle(0);
        document.getElementById('formsubmitbutton').click();
        setTimeout(function(){ processBtnToggle(1); }, 5000);
    }
        
    //document.getElementById('workflowsubmission').submit();
}

function submissioncheck() {
    if(find("ns").value == 0) {
        return false;
    } 
    
    return true;
}

var savedSupervisor;
var savedSup = 0;
var userName = '';
var failedSupervisorLookup = '<input type="radio" name="nextsupervisor" value="1" checked>Supervisor <input type="radio" name="nextsupervisor" value="2">2nd Supervisor';
function updateSupervisorButton() {
    if(savedSup == 0) {
        savedSupervisor = find('supervisor-radio').innerHTML;
        savedSup = 1;
        userNameField = document.getElementsByClassName('autonamefill')[0];
        if(userNameField != null)
            userName = userNameField.value;
    }
    
    if(find('onbehalf').value == 'Myself') {
        find('supervisor-radio').innerHTML = savedSupervisor;
        behalfUser = userName;
    } else {
        //Update the dropdown for the selected employee
        var employeeSelected = document.getElementById("onbehalf").options[document.getElementById("onbehalf").selectedIndex];
        updateSupervisorsDropdown(employeeSelected.value);
        var behalfUser = employeeSelected.text;
    }
    /*Update all the auto fill name fields to the selected behalf of user.*/
    var elements = document.getElementsByClassName('autonamefill');
    for(var i=0; i<elements.length; i++) {
        elements[i].value = behalfUser;
    }
}

/*
 * Pulls the current supervisors for a given employee and changes the dropdown 
 * select option with all the supervisors. 
 */
function updateSupervisorsDropdown(employeeNum) {
    var formData = new FormData();
    formData.append('action', 'workflow_get_supervisor');
    formData.append('employeeNum', employeeNum);
    
    $.ajax({
        url: "/wp-admin/admin-ajax.php",
        type: "POST",
        data: formData,
        processData: false,  // tell jQuery not to process the data
        contentType: false   // tell jQuery not to set contentType
    }).done(function( data ) {
        var obj = JSON.parse(data);
        if(obj.ReturnCode != '200') {
            find('warningmsg').innerHTML = 'Failed to update the supervisor list. Please use one of ' +
                'the options above or contact helpdesk at <a href="mailto:helpdesk@p2c.com" target="blank">helpdesk@p2c.com</a>';
            find('supervisor-radio').innerHTML = failedSupervisorLookup;
            return;
        } else if(find('directsupervisor') == null) {
            find('supervisor-radio').innerHTML = '<select name="directsupervisor" id="directsupervisor"></select>';
        }
        find('warningmsg').innerHTML = '';
        //Update the supervisors dropdown list
        var dirSelect = find('directsupervisor');
        //Remove the previous options
        for(var x = dirSelect.length - 1; x >= 0; x--) {
            dirSelect.remove(x);
        }
        //Add the new supervisor options
        if(obj.Data.length == 0) {
            find('warningmsg').innerHTML = 'Please select a supervisor from the dropdown. If there is no supervisor available, click Save Draft and contact <a href="mailto:helpdesk@p2c.com" target="blank">helpdesk@p2c.com</a>.';
        }
        for(var x = 0; x < obj.Data.length; x++) {
            var opt = document.createElement('option');
            opt.text = obj.Data[x]['supname'];
            opt.value = obj.Data[x]['supervisor'];
            if(opt.text != 'null' && opt.text != '' && opt.value != 'null' && opt.value != '')
                dirSelect.add(opt);
        }
    });
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

function toggleSearch() {
    if(find("submissionsearchbar1").classList.contains("hide")) {
        find("submissionsearchbar1").classList.remove("hide");
        find("submissionsearchbar2").classList.remove("hide");
        find("submissionsearchbar3").classList.remove("hide");
        find("submissionsearchbar4").classList.remove("hide");
    } else {
        find("submissionsearchbar1").classList.add("hide");
        find("submissionsearchbar2").classList.add("hide");
        find("submissionsearchbar3").classList.add("hide");
        find("submissionsearchbar4").classList.add("hide");
    }
}

var savedMode = 0;
var savedTag0 = 3;
var savedTag1 = 4;
/*
 * mode 0 = user submissions
 * mode 1 = approval submissions
 */
function switchTab(mode, tab) {
    if(mode == 'staff' || mode == 'all') {
        mode = 1;
    } else if(mode == 'my') {
        mode = 0;
    }
    var d = new Date();
    d.setTime(d.getTime() + (1*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    if(performance.navigation.type != 2) {
        document.cookie = 'workflowtab='+tab+';' + expires + ";path=/";
        document.cookie = 'workflowmode='+mode+';' + expires + ";path=/";
    }
    savedMode = mode;
    if(mode == 0) {
        find('user-status-link' + savedTag0).classList.remove("workflow-status-header");
        find('user-status-link' + tab).classList.add("workflow-status-header");
        savedTag0 = tab;
        if(tab != 2) {
            var tmp = findClass('user-2');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        }
        else {
            var tmp = findClass('user-2');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 3) {
            var tmp = findClass('user-3');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('user-3');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 4) {
            var tmp = findClass('user-4');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('user-4');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 7) {
            var tmp = findClass('user-7');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('user-7');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 8) {
            var tmp = findClass('user-8');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('user-8');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 10) {
            var tmp = findClass('user-10');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('user-10');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
    } else if(mode == 1) {
        find('approver-status-link' + savedTag1).classList.remove("workflow-status-header");
        find('approver-status-link' + tab).classList.add("workflow-status-header");
        savedTag1 = tab;
        if(tab != 4) {
            var tmp = findClass('approver-4');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('approver-4');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 7) {
            var tmp = findClass('approver-7');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('approver-7');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 8) {
            var tmp = findClass('approver-8');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('approver-8');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 9) {
            var tmp = findClass('approver-9');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('approver-9');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
        if(tab != 10) {
            var tmp = findClass('approver-10');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.add("hide");
            }
        } else {
            var tmp = findClass('approver-10');
            for(i = 0; i < tmp.length; i++) {
                tmp[i].classList.remove("hide");
            }
        }
    }
}

/*
 * Toggles between user submissions and submissions requiring approval
 */
function switchRole(mode) {
    /*savedMode = mode;
    if(mode == 0) {
        find('user-submissions').classList.remove('hide');
        find('approver-submissions').classList.add('hide');
        find('user-submissions-summary').classList.add('selected-submissions');
        find('approver-submissions-summary').classList.remove('selected-submissions');
    } else if(mode == 1) {
        find('user-submissions').classList.add('hide');
        find('approver-submissions').classList.remove('hide');
        find('user-submissions-summary').classList.remove('selected-submissions');
        find('approver-submissions-summary').classList.add('selected-submissions');
    }*/
}

function formSearch() {
    document.searchform.action += "&mode=" + savedMode + "&tag=" + (savedMode == 0 ? savedTag0 : savedTag1);
}

function closePreview(num) {
    if(num == undefined) {
        num = '';
    }
    find("screen-blackout" + num).style.display = 'none';
}

function showPreview(num) {
    if(num == undefined) {
        num = '';
    }
    find("screen-blackout" + num).style.display = 'block';
}

function printForm() {
    document.getElementById('hrnotes').style.height = document.getElementById('hrnotes').scrollHeight + 'px';
    window.print();
}

function loadComments(commentid) {
    submissionLink = false;
    var text = '';
    var elem = document.getElementById('comment' + commentid);
    if(elem != null)
        text = elem.innerHTML;
    var text = '<h2 class="center" style="color:black;">Comments</h2><br>' + text;
    document.getElementById('previewform').innerHTML = text;
    document.getElementById('screen-blackout').style.display = 'inherit';
}

/*
 *Toggles submission buttons to prevent clicking on them repeatedly. 
 */
function processBtnToggle($enable) {
    var x = findClass('processbutton');
    for(var y = 0; y < x.length; y++) {
        x[y].disabled = ($enable == 1 ? false : true);
        x[y].style.cursor = ($enable == 1 ? 'pointer' : 'not-allowed');
    }
}
