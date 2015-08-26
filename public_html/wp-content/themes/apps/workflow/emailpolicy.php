<?php
/*
*.
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

<h1>Roles</h1>
<?php
if(isset($_SESSION['ERRMSG'])) {
    echo '<span class="errormsg">'.$_SESSION['ERRMSG'].'</span><br>';
    unset($_SESSION['ERRMSG']);
}

if(!isset($_GET['group'])) {
    $_SESSION['ERRMSG'] = 'No Group Selected.';
    header('location: ?page=roles');
    die();
}

$group = $_GET['group'];

if(Workflow::isAdmin(Workflow::loggedInUser())) {
    $workflow = new Workflow();
    ?>
    
    <h2>Edit Email Sending</h2>
    <form id="changeemail" action="?page=edit_roles" method="POST" autocomplete="off">
        <table id="emailsettings">
            <tr><th>ID</th><th>Emp Num</th><th>Name</th><th>Role</th><th>Email</th></tr>
        <?php
        $values = $workflow->getMemberRoles();
        
        for($i = 0; $i < count($values); $i++) {
            if($values[$i][5] == $group) {?>
            <tr>
                <td><?php echo $values[$i][0];?></td>
                <td><?php echo $values[$i][1];?></td>
                <td><?php echo $values[$i][2];?></td>
                <td><?php echo $values[$i][3];?></td>
                <td><input type="hidden" id="<?php echo $values[$i][0];?>" name="<?php echo $values[$i][0];?>" value="0">
                    <input type="checkbox" id="<?php echo $values[$i][0];?>" name="<?php echo $values[$i][0];?>" 
                        <?php if($values[$i][4])echo 'checked';?>></td>
            </tr>
            
            <?php
            } //echo '<option value="'.$values[$i][0].'">'.$values[$i][3].' - '.$values[$i][1].' - '.$values[$i][2].'</option>';
        }
        ?>
        </table>
        
        <input type="hidden" id="mode" name="mode" value="4">
        <input type="submit" value="Save Settings">
    </form>
    
    <h2>Remove Member from Role</h2>
    <form id="addnewrole" action="?page=edit_roles" method="POST" autocomplete="off">
        <select name="removemember">
            <option></option>
            <?php
            $values = $workflow->getMemberRoles();
            for($i = 0; $i < count($values); $i++) {
                if($values[$i][5] == $group) 
                    echo '<option value="'.$values[$i][0].'">'.$values[$i][3].' - '.$values[$i][1].' - '.$values[$i][2].'</option>';
            }
            ?>
        </select>
        <input type="hidden" id="mode" name="mode" value="3">
        <input type="submit" value="Remove Member">
    </form>
    
    <?php
} else { 
    $_SESSION['ERRMSG'] = 'You do not have access.';
    header('location: ?page=roles');
    die();
}
?>

