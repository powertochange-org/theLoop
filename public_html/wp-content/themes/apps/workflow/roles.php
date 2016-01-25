<?php
/*
*Used for adding role groups, adding members to roles and removing members from roles.
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/docsupport/prism.css">
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/chosen.css">
<h1>Roles</h1>
<?php
if(isset($_SESSION['ERRMSG'])) {
    echo '<span class="errormsg">'.$_SESSION['ERRMSG'].'</span><br>';
    unset($_SESSION['ERRMSG']);
}


if(Workflow::isAdmin(Workflow::loggedInUser())) {
    $workflow = new Workflow();
    ?>
    
    <h2>Add New Role</h2>
    <form id="addnewrole" action="?page=edit_roles" method="POST" autocomplete="off">
        <div class="workflow workflowleft">
            Role Name:
        </div>
        <div class="workflow workflowright style-1">
            <input type="text" name="rolename" id="rolename">
        </div>
        <div class="clear"></div>
        <input type="hidden" id="mode" name="mode" value="1">
        <input type="submit" value="Add Role">
    </form>
    
    
    <h2>Add New Member to Role</h2>
    <form id="addnewrole" action="?page=edit_roles" method="POST" autocomplete="off">
        <div class="workflow workflowleft">
            Role Name:
        </div>
        <div class="workflow workflowright style-1">
            <select name="addmemberrole">
                <?php
                $values = $workflow->getRoles();
                for($i = 0; $i < count($values); $i++) {
                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
                }
                ?>
            </select>
            
        </div>
        <div class="clear"></div>
        
        <div class="workflow workflowleft">
            Member:
        </div>
        <div class="workflow workflowright style-1">
            <!--<input type="text" name="addmembername" id="addmembername">-->
            <select id="addmembername" name="addmembername" class="chosen-select" data-placeholder=" ">
            <option></option>
            <?php $values = Workflow::getAllUsers();
            for($i = 0; $i < count($values); $i++) {
                echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
            }?>
            </select>
        </div>
        <div class="clear"></div>
        
        <input type="hidden" id="mode" name="mode" value="2">
        <input type="submit" value="Add Member">
    </form>
    
    
    <h2>Remove Member from Role</h2>
    <form id="addnewrole" action="?page=edit_roles" method="POST" autocomplete="off">
        <select name="removemember" class="chosen-select" data-placeholder=" ">
            <option></option>
            <?php
            $values = $workflow->getMemberRoles();
            for($i = 0; $i < count($values); $i++) {
                echo '<option value="'.$values[$i][0].'">'.$values[$i][3].' - '.$values[$i][1].' - '.$values[$i][2].'</option>';
            }
            ?>
        </select>
        <input type="hidden" id="mode" name="mode" value="3">
        <input type="submit" value="Remove Member">
    </form>
    
    
    <h2>Change Email Sending Preferences</h2>
    <?php
    $values = $workflow->getRoles();
    for($i = 0; $i < count($values); $i++) {
        echo '<a href="?page=emailpolicy&group='.$values[$i][0].'">'.$values[$i][1].'</a><br>';
    }
    ?>
    
    <?php
} else { 
    echo 'You do not have access.';
}
?>

<script type="text/javascript">
var config = {
  '.chosen-select'           : {},
  '.chosen-select-deselect'  : {allow_single_deselect:true},
  '.chosen-select-no-single' : {disable_search_threshold:10},
  '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
  '.chosen-select-width'     : {width:"95%"}
}
for (var selector in config) {
  $(selector).chosen(config[selector]);
}
</script>
