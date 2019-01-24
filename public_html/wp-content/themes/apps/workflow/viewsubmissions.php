<?php
/*
*Views a list of submissions that was submitted by the user. Also shows the list of submissions
*from other users that you are an approver of. 
*
*
* author: gerald.becker
*
*/
?>
<h1>View Submissions</h1>

<?php
//If reminder emails need to be sent, send them out
$dateCheck = new DateTime(date('Y-m-d H:i:s'));
$dateCheck->setTimezone(new DateTimeZone('America/Los_Angeles'));
if('08:00' <= $dateCheck->format('H:i') && $dateCheck->format('H:i') <= '17:00' && $dateCheck->format('N') != '7') {
    if($dateCheck->format('Y-m-d H:i:s') >= get_option('workflow_email_alert')) {
        include 'emailreminder.php';
        $date = new DateTime(date("Y-m-d H:i:s"));
        $date->add(new DateInterval('PT2H'));
        $date->setTimezone(new DateTimeZone('America/Los_Angeles'));
        update_option('workflow_email_alert', $date->format('Y-m-d H:i:s'));
    }
}

if(isset($_SESSION['ERRMSG'])) {
    echo '<span style="color:red;font-size:30px;">'.$_SESSION['ERRMSG'].'</span><br>';
    unset($_SESSION['ERRMSG']);
}

/*Impersonating an employee in debug mode.*/
global $wpdb;
$debugText = '';
$impersonateMode = Workflow::impersonateMode();
if(Workflow::debugMode() || $impersonateMode) {
    $debugText = '<hr><b>'.($impersonateMode ? 'IMPERSONATE' : 'DEBUG').' MODE ENABLED.</b><br>The below form is only available when debug mode is turned on or you are part of the IMPERSONATE USER role group.<br>';
    if(isset($_POST['newuser']) && $_POST['newuser'] != '') {
        //Assign the impersonate variable to allow for impersonation. 
        if(Workflow::actualloggedInUser() == $_POST['newuser'])
            Workflow::stopImpersonateEmployee();
        else 
            Workflow::impersonateEmployee($_POST['newuser']);
    } 

    $debugText .= 'Currently logged in as: ';
    $debugText .= Workflow::loggedInUser().' - '.Workflow::loggedInUserName();
    $debugText .= '  |  ACTUAL: '.Workflow::actualloggedInUser().'<BR>';
}
/*End of impersonating an employee.*/

if(Workflow::loggedInUser() != '0') {
    $idsearch = $formsearch = $submittedsearch = $datesearch = '';
    $showfiled = $showCompleted = 2;
    $showvoid = 0;
    //By default, hide filed forms for Hr Notes group
    if(Workflow::hasRoleAccess(Workflow::loggedInUser(), 26)) {
        $showfiled = 0;
    }
    if(isset($_POST['idsearch']) && $_POST['idsearch'] != '') {
        $idsearch = $_POST['idsearch'];
    }
    if(isset($_POST['formsearch']) && $_POST['formsearch'] != '') {
        $formsearch = $_POST['formsearch'];
    }
    if(isset($_POST['submittedsearch']) && $_POST['submittedsearch'] != '') {
        $submittedsearch = $_POST['submittedsearch'];
    }
    if(isset($_POST['datesearch']) && $_POST['datesearch'] != '') {
        $datesearch = $_POST['datesearch'];
    }
    if(isset($_POST['filed']) && 0 <= $_POST['filed'] && $_POST['filed'] <= 2) {
        $showfiled = $_POST['filed'];
    }
    if(isset($_GET['void']) && 0 <= $_GET['void'] && $_GET['void'] <= 2) {
        $showvoid = $_GET['void'];
    }
    if(isset($_POST['showcompleted']) && 0 <= $_POST['showcompleted'] && $_POST['showcompleted'] <= 2) {
        $showCompleted = $_POST['showcompleted'];
    }
    
    $obj = new Workflow();
    
    if(isset($_GET['forms']))
        $formType = $_GET['forms'];
    else
        $formType = 'my';
    
    echo $obj->viewSubmissionSummary(Workflow::loggedInUser(), $formsearch, "", $datesearch, $idsearch, $formType, $showvoid, $showfiled);
    
    ?>
    <hr>
    <form name="searchform" action="?page=viewsubmissions&forms=<?php echo $formType;?>" method="POST" autocomplete="off">
        <table id="submissionsearchbar">
            <tr><td colspan=4 style="text-align: center;"><h2 style="display:inline;margin-left:65px;color:inherit;">Filter</h2><button class="search-expand" type="button" onclick="toggleSearch();">Expand</button></td></tr>
            <tr id="submissionsearchbar1" class="hide"><th>ID</th><th>Form Name</th><th>Submitted By</th><th>Date</th></tr>
            <tr id="submissionsearchbar2" class="hide">
                <td><div class="style-1 inputfix workflowright" style="width: 100px;"><input type="text" name="idsearch" id="idsearch" value="<?php echo $idsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="text" name="formsearch" id="formsearch" value="<?php echo $formsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="text" name="submittedsearch" id="submittedsearch" value="<?php echo $submittedsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="date" name="datesearch" id="datesearch" value="<?php echo $datesearch;?>"></div></td>
            </tr>
            <tr id="submissionsearchbar4" class="hide">
                <td colspan=4>
                <?php if(Workflow::hasRoleAccess(Workflow::loggedInUser(), 26)) { ?>
                    <br><b>Filter Filed Forms</b><br>
                    <input type="radio" name="filed" value="0" <?php echo ($showfiled == 0 ? 'checked' : ''); ?>>Unfiled
                    <input type="radio" name="filed" value="1" <?php echo ($showfiled == 1 ? 'checked' : ''); ?>>Filed
                    <input type="radio" name="filed" value="2" <?php echo ($showfiled == 2 ? 'checked' : ''); ?>>All
                    <br>
                    <b>Filter Approved Forms</b><br>
                    <input type="radio" name="showcompleted" value="0" <?php echo ($showCompleted == 0 ? 'checked' : ''); ?>>To Be Processed
                    <input type="radio" name="showcompleted" value="1" <?php echo ($showCompleted == 1 ? 'checked' : ''); ?>>Processed / Approved
                    <input type="radio" name="showcompleted" value="2" <?php echo ($showCompleted == 2 ? 'checked' : ''); ?>>All
                <?php } ?>
                </td>
            </tr>
            <tr id="submissionsearchbar3" class="hide">
                <td colspan=4 class="center">
                    <input type="submit" value="Apply Filter" style="display:inherit; margin-right:20px;" onclick="formSearch();">
                    <input type="reset" value="Clear" style="display:inherit;" onclick="clearSearch();" >
                </td>
            </tr>
        </table>
    </form>

    
    <?php
    
    
    ?>
    
    <div style="text-align:center;">
    
    <?php
    if($formType == 'my' || $formType == 'both')
        echo $obj->viewAllSubmissions(Workflow::loggedInUser(), $formsearch, $datesearch, $idsearch, 0);
    if($formType == 'staff' || $formType == 'both')
        echo $obj->viewAllSubmissionsAsApprover(Workflow::loggedInUser(), $formsearch, $submittedsearch, $datesearch, $idsearch, 0,
             $showvoid, $showfiled, $showCompleted);
    if($formType == 'all' && Workflow::isAdmin(Workflow::loggedInUser()))
        echo $obj->viewAllSubmissionsAsApprover(Workflow::loggedInUser(), $formsearch, $submittedsearch, $datesearch, $idsearch, 1,
             $showvoid, $showfiled, $showCompleted);
    
    //Display the forms that the user was in before hitting the search button
    if(isset($_GET['mode']) && isset($_GET['tag'])) {
        echo '<script>switchRole('.$_GET['mode'].');switchTab('.$_GET['mode'].', '.$_GET['tag'].');</script>';
    }
    
    ?>
    </div>
    <?php
} else {
    echo('<br>Your account may not have been set up to use this feature yet. Please contact help desk at <a href="mailto:helpdesk@p2c.com">helpdesk@p2c.com</a>.<br>');
}

if(Workflow::debugMode() || $impersonateMode) {
    echo $debugText;
    echo '<form id="edituser" action="?page=viewsubmissions" method="POST" autocomplete="off">
        <div class="style-1 workflowright" style="float:left;"><input type="text" id="newuser" name="newuser"></div>
        '.(Workflow::actualloggedInUser() != Workflow::loggedInUser() ? '<input type="button" value="Stop Impersonating" onclick="document.getElementById(\'newuser\').value = \''.Workflow::actualloggedInUser().'\';document.getElementById(\'edituser\').submit();" style="float:left;"/>' : '').
        '<div style="clear:both;"></div>
        <input type="submit" value="Submit">
        
    </form><hr>';
}

?>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".selectedblackout").click(function () {
            if(submissionLink) 
                window.document.location = $(this).data("href");
            if(!submissionLink)
                submissionLink = true;
        });
    });
</script>
