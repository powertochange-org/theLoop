<?php
/*
* Updates the directors for a given ministry in the P2C Forms system.
*
* author: gerald.becker
*/
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/docsupport/prism.css">
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/chosen.css">
<h1>Directors</h1>
<?php
if(isset($_SESSION['ERRMSG'])) {
    echo '<span class="errormsg">'.$_SESSION['ERRMSG'].'</span><br>';
    unset($_SESSION['ERRMSG']);
}

if(Workflow::isAdmin(Workflow::loggedInUser())) {
    $workflow = new Workflow();
    
    //Update the directors in the database
    if(isset($_POST)) {
        for($i = 0; $i < count($_POST['settingkey']); $i++) {
            Workflow::saveWorkflowSetting('directors', $_POST['settingkey'][$i], $_POST['settingvalue'][$i]);
        }
    }
    ?>
    
    <h2>Edit Directors</h2>
    <form id="addnewrole" action="?page=directors" method="POST" autocomplete="off">
        <?php
        $settings = Workflow::getWorkflowSetting('directors');
        $users = Workflow::getAllUsers();
        $ministries = Workflow::getAllMinistries();
        
        //Find out if a new ministry exists and add it to the settings list
        for($i = 0; $i < count($ministries); $i++) {
            $found = 0;
            foreach($settings as $val) {
                if(in_array($ministries[$i]['ministry'], $val)) {
                    $found = 1;
                    break;
                }
            }
            if(!$found) {
                $settings[] = array('SETTINGS_KEY'=>$ministries[$i]['ministry'],
                                    'VALUE'=>'');
            }
        }
        
        //Populate settings page
        $e = '';
        for($i = 0; $i < count($settings); $i++) {
            $e .= '<div class="workflow workflowleft field276to300">'.$settings[$i]['SETTINGS_KEY'].'</div>';
            $e .= '<div class="workflow workflowright style-1">
                <input type="hidden" name="settingkey[]" value="'.$settings[$i]['SETTINGS_KEY'].'"/>
                <select name="settingvalue[]" class="chosen-select" data-placeholder=" "><option></option>';
            
            for($u = 0; $u < count($users); $u++) {
                $e .= '<option value="'.$users[$u][0].'" '.($settings[$i]['VALUE'] == $users[$u][0] ? 'selected' : '').
                    '>'.$users[$u][1].'</option>';
            }
            
            $e .= '</select>
                </div>';
            $e .= '<div class="clear"></div>';

        }
        echo $e;
        ?>
        
        <input type="submit" value="Save Changes">
    </form>
    
    
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
