<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    p.submit { padding-bottom: 12px!important; }
</style>

<div class="wrap">

    <?php screen_icon(Easy_Pie_MM_Constants::PLUGIN_SLUG); ?>
    <h2>Easy Pie Maintenance Mode</h2>
    <?php
    if (isset($_GET['settings-updated'])) {
        echo "<div class='updated'><p>" . Easy_Pie_MM_Utility::__('If you have a caching plugin, be sure to clear the cache!') . "</p></div>";
    }

    $option_array = get_option(Easy_Pie_MM_Constants::OPTION_NAME);
    ?>
    <div class="inside">        
        <form method="post" action="options.php"> 
            <?php
            settings_fields(Easy_Pie_MM_Constants::MAIN_PAGE_KEY);
            do_settings_sections(Easy_Pie_MM_Constants::MAIN_PAGE_KEY);
            ?>
            <div  style="margin-top: 25px; width:784px" class="postbox easy-pie-mm-toggle">
                <div class="handlediv" title="Click to toggle" onclick="easyPie.MM.toggleAdvancedBox();"><br></div>
                <h3 style="cursor:pointer; height:25px; margin-bottom:0px; padding-left: 10px; padding-top:0px;" class="hndl" onclick="easyPie.MM.toggleAdvancedBox();"><span style="font-weight:bold"><?php Easy_Pie_MM_Utility::_e('Advanced Settings'); ?><span></h3>
                <table id="easy-pie-mm-advanced" style="display:none" class="form-table">
                    <tr valign="top">
                        <th scope="row" style="padding-left:9px;"><?php Easy_Pie_MM_Utility::_e("Custom CSS") ?></th><td>
                            <div>
                                <textarea cols="67" rows="9" id="easy-pie-mm-field-junk" name="easy-pie-mm-options[css]"><?php echo $option_array["css"]; ?></textarea>
                                <p><small><strong><?php Easy_Pie_MM_Utility::_e("Page styling varies greatly. ")?></strong><?php Easy_Pie_MM_Utility::_e("Update custom CSS when switching mini-themes."); ?></small></p>
                            </div>             
                        </td>
                    </tr>
                    <tr>
                        
                        <td colspan="2">
                            <?php
                            $format = "<p><a style='font-size:11px' target='_blank' href='%s'>%s</a></p>";

                            $resolved = sprintf($format, "http://easypiewp.com/how-to-create-maintenance-mode-theme/", Easy_Pie_MM_Utility::__('How to create a mini-theme for yourself or the community') );        

                            Easy_Pie_MM_Utility::_e($resolved);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div style="margin-top:30px; margin-bottom:25px; border-radius:4px; box-shadow: 1px 6px 36px -5px rgba(34,34,34,1);width: 514px; color: rgb(200, 22, 22); background-color: white;font-weight: bold;border: red solid 1px;padding: 5px;">Custom backgrounds, email collection, social media and more <a href="https://easypiewp.com/coming-soon-page-elite-mm?footerfixed" target="_blank">in our Pro product!</a>                       
            </div>
            
            <?php
            submit_button();
            ?>
                        
            <a href="http://easypiewp.com/easy-pie-maintenance-mode-faq/" target="_blank"><?php $this->_e('Plugin FAQ'); ?></a>
            |
            <a href="mailto:bob@easypiewp.com" target="_blank"><?php $this->_e('Contact Bob'); ?></a>
        </form>
    </div>
</div>