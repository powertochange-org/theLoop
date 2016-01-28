<?php
/*
*Views a list of submissions that was submitted by the user. Also shows the list of submissions
*from other users that you are an approver of. 
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/
?>
<h1>View Submissions</h1>

<?php

if(isset($_SESSION['ERRMSG'])) {
    echo '<span style="color:red;font-size:30px;">'.$_SESSION['ERRMSG'].'</span><br>';
    unset($_SESSION['ERRMSG']);
}


/*Impersonating an employee in debug mode.*/
global $wpdb;

$debugText = 'DEBUG MODE ENABLED. The below form will not be visible on the production version.<br>';
if(Workflow::debugMode() && isset($_POST['newuser']) && $_POST['newuser'] != '') {
    //Assign the impersonate variable to allow for impersonation. 
    if(Workflow::actualloggedInUser() == $_POST['newuser'])
        Workflow::stopImpersonateEmployee();
    else 
        Workflow::impersonateEmployee($_POST['newuser']);
    
    
} else if(isset($_POST['newuser']) && $_POST['newuser'] != '') {
    echo '<script> alert("Debug mode is turned off");</script>';
}

$debugText .= 'Currently logged in as: ';
$debugText .= Workflow::loggedInUser().' - '.Workflow::loggedInUserName();
$debugText .= '  |  ACTUAL: '.Workflow::actualloggedInUser().'<BR>';

/*End of impersonating an employee.*/
?>



<?php
if(Workflow::loggedInUser() != '0') {
    
?>
    
    
    <?php

    $idsearch = $formsearch = $submittedsearch = $datesearch = '';
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
    
    $obj = new Workflow();
    
    echo $obj->viewSubmissionSummary(Workflow::loggedInUser(), $formsearch, "", $datesearch, $idsearch);
    
    ?>
    <hr>
    <form name="searchform" action="?page=viewsubmissions" method="POST" autocomplete="off">
        <table id="submissionsearchbar">
            <tr><td colspan=4 style="text-align: center;"><h2 style="display:inline;margin-left:65px;color:inherit;">Filter</h2><button class="search-expand" type="button" onclick="toggleSearch();">Expand</button></td></tr>
            <tr id="submissionsearchbar1" class="hide"><th>ID</th><th>Form Name</th><th>Submitted By</th><th>Date</th></tr>
            <tr id="submissionsearchbar2" class="hide">
                <td><div class="style-1 inputfix workflowright" style="width: 100px;"><input type="text" name="idsearch" id="idsearch" value="<?php echo $idsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="text" name="formsearch" id="formsearch" value="<?php echo $formsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="text" name="submittedsearch" id="submittedsearch" value="<?php echo $submittedsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="date" name="datesearch" id="datesearch" value="<?php echo $datesearch;?>"></div></td>
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
    
    echo $obj->viewAllSubmissions(Workflow::loggedInUser(), $formsearch, $datesearch, $idsearch);
    echo $obj->viewAllSubmissionsAsApprover(Workflow::loggedInUser(), $formsearch, $submittedsearch, $datesearch, $idsearch);
    
    //Display the forms that the user was in before hitting the search button
    if(isset($_GET['mode']) && isset($_GET['tag'])) {
        echo '<script>switchRole('.$_GET['mode'].');switchTab('.$_GET['mode'].', '.$_GET['tag'].');</script>';
    }
    
    ?>
    </div>
    <?php
} else {
    echo('<br>You need to log in!');
}

if(Workflow::debugMode()) {
    echo $debugText;
    echo '<form id="edituser" action="?page=viewsubmissions" method="POST" autocomplete="off">
        <div class="style-1 workflowright"><input type="text" name="newuser"></div>
        <input type="submit" value="Submit">
    </form>';
}

?>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".selectedblackout").click(function () {
            window.document.location = $(this).data("href");
        });
    });
</script>