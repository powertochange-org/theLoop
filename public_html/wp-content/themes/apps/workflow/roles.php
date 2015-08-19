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
<div id="main-content">

    <div id="content-workflow">
        <h1>Roles</h1>
        <?php
        if(Workflow::isAdmin(Workflow::loggedInUser())) {
            $workflow = new Workflow();
            ?>
            
            <h2>Add New Role</h2>
            <form id="addnewrole" action="?page=add_role" method="POST" autocomplete="off">
                <div class="workflow workflowleft">
                    Role Name:
                </div>
                <div class="workflow workflowright style-1">
                    <input type="text" name="rolename" id="rolename">
                </div>
                <div class="clear"></div>
                <input type="hidden" id="mode" name="mode" value="1">
                <input type="submit" value="Submit">
            </form>
            
            
            <h2>Add New Member to Role</h2>
            <form id="addnewrole" action="?page=add_role" method="POST" autocomplete="off">
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
                    Member Name:
                </div>
                <div class="workflow workflowright style-1">
                    <input type="text" name="addmembername" id="addmembername">
                </div>
                <div class="clear"></div>
                
                <input type="hidden" id="mode" name="mode" value="2">
                <input type="submit" value="Submit">
            </form>
            
            
            <h2>Remove Member from Role</h2>
            <select name="test">
                <option></option>
            <?php
            $values = $workflow->getMemberRoles();
            
            
            
            for($i = 0; $i < count($values); $i++) {
                //array($row['ID'], $row['MEMBER'], $row['ROLEID'], $row['NAME']);
                echo '<option value="'.$values[$i][0].'">'.$values[$i][3].' - '.$values[$i][1].' - '.$values[$i][2].'</option>';
            }
        } else { 
            echo 'You do not have access.';
        }
        ?>
    </select>
        
    </div>
</div>
