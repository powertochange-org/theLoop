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

$debugText = '';
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

    ?>
    <form action="?page=viewsubmissions" method="POST" autocomplete="off">
        <table id="submissionsearchbar">
            <tr><td colspan=4 style="text-align: center;"><h2>Search</h2></td></tr>
            <tr><th>ID</th><th>Form Name</th><th>Submitted By</th><th>Date</th></tr>
            <tr>
                <td><div class="style-1 inputfix workflowright" style="width: 100px;"><input type="text" name="idsearch" id="idsearch" value="<?php echo $idsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="text" name="formsearch" id="formsearch" value="<?php echo $formsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="text" name="submittedsearch" id="submittedsearch" value="<?php echo $submittedsearch;?>"></div></td>
                <td><div class="style-1 inputfix"><input type="date" name="datesearch" id="datesearch" value="<?php echo $datesearch;?>"></div></td>
            </tr>
            <tr>
                <td colspan=4 class="center">
                    <input type="submit" value="Search" style="float:left; margin-right:20px;">
                    <input type="reset" value="Clear" onclick="clearSearch();" style="float:left;">
                </td>
            </tr>
        </table>
    </form>

    
    <?php
    
    $obj = new Workflow();
    echo $obj->viewAllSubmissions(Workflow::loggedInUser(), $formsearch, $datesearch, $idsearch);
    ?>
    
    <?php
    echo $obj->viewAllSubmissionsAsApprover(Workflow::loggedInUser(), $formsearch, $submittedsearch, $datesearch, $idsearch);
} else {
    echo('<br>You need to log in!');
}

echo $debugText;
?>
<form id="edituser" action="?page=viewsubmissions" method="POST" autocomplete="off">
    <div class="style-1 workflowright"><input type="text" name="newuser"></div>
    <input type="submit" value="Submit">
</form>
