<?php
/*
 * Displays a list of all submissions for a given form. 
 *
 *
 *
 * author: gerald.becker
 *
 */
?>
<h1>View Submissions By Form</h1>

<?php
global $wpdb;

if(Workflow::loggedInUser() != '0') {
    $form = '';
    if(isset($_GET['form']) && $_GET['form'] != '') {
        $form = $_GET['form'];
    }
    
    $obj = new Workflow();
    ?>
    <hr>
    <div style="text-align:center;">
    
    <?php
    if($form == '')
        echo $obj->viewAllFormsWithAccess(Workflow::loggedInUser(), Workflow::isAdmin(Workflow::loggedInUser()));
    else
        echo $obj->viewAllSubmissionsGrid(Workflow::loggedInUser(), $form, Workflow::isAdmin(Workflow::loggedInUser()));
    ?>
    </div>
    <?php
} else {
    echo('<br>Your account may not have been set up to use this feature yet. Please contact help desk at <a href="mailto:helpdesk@p2c.com">helpdesk@p2c.com</a>.<br>');
}

?>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".selectedblackout").click(function () {
            window.document.location = $(this).data("href");
        });
    });
</script>
