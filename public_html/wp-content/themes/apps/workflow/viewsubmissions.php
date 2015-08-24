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



global $wpdb;

if(isset($_POST['newuser']) && $_POST['newuser'] != '') {
    $_SESSION['activeuser'] = $_POST['newuser'];
    echo 'Now logged in as:';
    
    $sql = "SELECT CONCAT(first_name, ' ', last_name) AS name
            FROM employee
            WHERE employee_number = '$_SESSION[activeuser]'";
    
    $result = $wpdb->get_results($sql, ARRAY_A);
    
    if(count($result) == 1) {
        $_SESSION['activeusername'] = $result[0]['name'];
    } else {
        $_SESSION['activeusername'] = 'Unknown User';
    }
    
} else if(isset($_SESSION['activeuser']) && $_SESSION['activeuser'] != '') {
    echo 'Currently logged in as:';
} else {
    $_SESSION['activeuser'] = $_SESSION['activeusername'] = '0';
    echo 'Defaulting to log in as: ';
}

echo $_SESSION['activeuser'].' | '.$_SESSION['activeusername'];
echo '   ACTUAL: '.Workflow::actualloggedInUser().'<BR>';

?>

<form id="edituser" action="?page=viewsubmissions" method="POST" autocomplete="off">
    <input type="text" name="newuser">
    <input type="submit" value="Submit">
</form>

<?php
if($_SESSION['activeuser'] != '0') {
    
?>
    <h2>Forms for User</h2>
    <?php
    
    $obj = new Workflow();
    echo $obj->viewAllSubmissions($_SESSION['activeuser']);
    ?>
    <h2>Forms Requiring Approval</h2>
    <?php
    echo $obj->viewAllSubmissionsAsApprover($_SESSION['activeuser']);
} else {
    echo('<br>You need to log in!');
}
?>
        