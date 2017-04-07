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

function test() {
    alert("HAHA");
}

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
    
    if(status == 2 || status == 3 || status == 8 || status == 10 || status == 20) {
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

var savedSupervisor;
var savedSup = 0;
var userName = '';
function updateSupervisorButton() {
    if(savedSup == 0) {
        savedSupervisor = find('supervisor-radio').innerHTML;
        savedSup = 1;
        userName = document.getElementsByClassName('autonamefill')[0].value;
    }
    
    if(find('onbehalf').value == 'Myself') {
        find('supervisor-radio').innerHTML = savedSupervisor;
        behalfUser = userName;
    } else {
        find('supervisor-radio').innerHTML = '<input type="radio" name="nextsupervisor" value="1" checked>Supervisor'+
        '<input type="radio" name="nextsupervisor" value="2">2nd Supervisor';
        
        var behalfUser = document.getElementById("onbehalf").options[document.getElementById("onbehalf").selectedIndex ].text;
    }
    /*Update all the auto fill name fields to the selected behalf of user.*/
    var elements = document.getElementsByClassName('autonamefill');
    for(var i=0; i<elements.length; i++) {
        elements[i].value = behalfUser;
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

function toggleSearch() {
    if(find("submissionsearchbar1").classList.contains("hide")) {
        find("submissionsearchbar1").classList.remove("hide");
        find("submissionsearchbar2").classList.remove("hide");
        find("submissionsearchbar3").classList.remove("hide");
    } else {
        find("submissionsearchbar1").classList.add("hide");
        find("submissionsearchbar2").classList.add("hide");
        find("submissionsearchbar3").classList.add("hide");
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
    savedMode = mode;
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
    }
}

function formSearch() {
    document.searchform.action += "&mode=" + savedMode + "&tag=" + (savedMode == 0 ? savedTag0 : savedTag1);
}

function closePreview() {
    find("screen-blackout").style.display = 'none';
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
