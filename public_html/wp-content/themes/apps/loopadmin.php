<?php
/*
*Template Name: zApp Loop_Admin
*
* A main page that allows user access levels to be changed. New access apps can
* also be added here.
*
* author: gerald.becker
*
*/
//Admin functions
include('functions/functions.php');
?>
<?php get_header(); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/chosen.jquery.js" type="text/javascript"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/docsupport/prism.css">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/chosen.css">

    <link href="<?php echo get_stylesheet_directory_uri(); ?>/workflow-style.css" rel="stylesheet" type="text/css" />
    <div id="content" class='staff-d'>
        <?php if (have_posts()) : while (have_posts()) : the_post();  ?>
            <div class="entry">
                <div style="clear:both"></div>
                <div id="main-content">
    
                    <div id="content-workflow" style="margin-bottom:200px;">
                    
                    <?php
                    
                    $admin = isAppAdmin('loopadmin', 0);
                        
                    if($admin && isset($_POST['mode'])) {
                        $mode = $_POST['mode'];
                        if($mode == 1) {
                            if(!isset($_POST['rolename']) || $_POST['rolename'] == '') {
                              $_SESSION['ERRMSG'] = 'rolename field missing.';
                            } else {
                              $rolename = $_POST['rolename'];
                            
                              if(!storeApp($rolename)) {
                                  $_SESSION['ERRMSG'] = 'Failed to add role. It may already exist.';
                              }
                            }  
                        } else if($mode == 2) {
                            if(!isset($_POST['addmemberrole']) || $_POST['addmemberrole'] == '') {
                                $_SESSION['ERRMSG'] = 'addmemberrole field missing.';
                            } else if(!isset($_POST['addmembername']) || $_POST['addmembername'] == '') {
                                $_SESSION['ERRMSG'] = 'addmembername field missing.';
                            } else {
                                $name = $_POST['addmembername'];
                                $role = $_POST['addmemberrole'];
                                
                                if(!storeMember($role, $name)){
                                    $_SESSION['ERRMSG'] = 'Failed to add user. User may already have access.';
                                }
                            }
                        } else if($mode == 3) {
                            if(!isset($_POST['removemember']) || $_POST['removemember'] == '') {
                                $_SESSION['ERRMSG'] = 'removemember field missing.';
                            } else {
                                $roletoremove = $_POST['removemember'];

                                $length = strlen($roletoremove);
                                $endVal = stripos($roletoremove, 'USER');
                                $role = substr($roletoremove, 4, $endVal - 4);
                                $user = substr($roletoremove, $endVal + 4, $length - $endVal + 4);

                                if(!removeMember($role, $user)){
                                    $_SESSION['ERRMSG'] = 'Failed to remove user.';
                                }
                            }
                        } 
                      
                    }  
                        
                    echo '<h1>Admin Portal</h1>';
                    
                    if(isset($_SESSION['ERRMSG'])) {
                        echo '<span class="errormsg">'.$_SESSION['ERRMSG'].'</span><br>';
                        unset($_SESSION['ERRMSG']);
                    }
                    
                    if($admin) {
                    ?>
                        
                        <h2>Add a New App</h2>
                        <form id="addnewrole" action="" method="POST" autocomplete="off">
                            <div class="workflow workflowleft">
                                App Name:
                            </div>
                            <div class="workflow workflowright style-1">
                                <input type="text" name="rolename" id="rolename">
                            </div>
                            <div class="clear"></div>
                            <input type="hidden" id="mode" name="mode" value="1">
                            <input type="submit" value="Add App">
                        </form>
                        
                        <h2>Give Member Access to an App</h2>
                        <form id="addnewrole" action="" method="POST" autocomplete="off">
                            <div class="workflow workflowleft">
                                App Name:
                            </div>
                            <div class="workflow workflowright style-1">
                                <select name="addmemberrole">
                                    <?php
                                    
                                    $values = getAppList();
                                    for($i = 0; $i < count($values); $i++) {
                                        echo '<option value="'.$values[$i][0].'">'.$values[$i][0].'</option>';
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
                                <select id="addmembername" name="addmembername" class="chosen-select" data-placeholder=" "><option></option>
                                <?php $values = getAllUsers();
                                for($i = 0; $i < count($values); $i++) {
                                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].'</option>';
                                }?>
                                </select>
                            </div>
                            <div class="clear"></div>
                            
                            <input type="hidden" id="mode" name="mode" value="2">
                            <input type="submit" value="Add Member">
                        </form>
                        
                        
                        <h2>Remove Member Access from an App</h2>
                        <form id="addnewrole" action="" method="POST" autocomplete="off">
                            <select name="removemember" class="chosen-select" data-placeholder=" ">
                                <option></option>
                                <?php
                                $values = getMemberAppAccess();
                                for($i = 0; $i < count($values); $i++) {
                                    echo '<option value="'.$values[$i][0].'">'.$values[$i][1].' - '.$values[$i][2].'</option>';
                                }
                                ?>
                            </select>
                            <input type="hidden" id="mode" name="mode" value="3">
                            <input type="submit" value="Remove Member">
                        </form> 
                        
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

                    <?php
                    } else {
                        echo '<p>You do not have access to view this admin portal. Please contact helpdesk at helpdesk@p2c.com</p>';
                    }
                ?>
                </div></div>
            </div>
        <?php endwhile; else: ?>
        <h2>404 - Not Found</h2>
        <p>The page you are looking for is not here.</p>                     
        <?php endif; ?>
    </div>
    <!--content end-->
    <!--Popup window-->
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>
<?php get_footer(); ?>