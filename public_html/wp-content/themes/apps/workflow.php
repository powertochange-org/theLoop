<?php
/*
*Template Name: zApp Workflow
*
*The main landing page for the Workflow forms. 
*Workflow can be created with different approvers along each approval level. When a form is approved, the form gets moved
*to the next approver or to the finished state.
*
*The form templates are created here and can then be accessed by using the link (ex: /forms-information/workflow/?page=workflowentry&wfid=36)
*This link can be placed anywhere in the loop and they will be directed to the form. Alternatively, a page can be created
*and the form can be loaded there. A parameter is required though: ?wfid=###
*Then have the following code in the php code near the top:
        include_once('workflow/config.php');
        function __autoload($class_name) {
            include_once('workflow/inc/class.' . $class_name . '.inc.php');
        }
        session_start();
*
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/

// Include the main Workflow class
require_once('workflow/inc/class.Workflow.inc.php');

?>
<?php get_header(); ?>
    <link href="<?php echo get_stylesheet_directory_uri(); ?>/workflow-style.css" rel="stylesheet" type="text/css" />
    <script language="javascript" type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/workflow/script.js"></script>
    <div id="content" class='staff-d'>
        <?php if (have_posts()) : while (have_posts()) : the_post();  ?>
            <!--<h1 style="float:left;"><a style="font-size:35pt;font-family:Roboto Slab;font-weight:100;" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>-->
            <div class="entry">
                <div style="clear:both"></div>
                <div id="main-content">
    
                    <div id="content-workflow">
                <?php 
                    include('workflow/menu.php');
                    
                    if(isset($_GET['page'])){ //check if the page has been specified
                        $site = $_GET['page']; //grab from the URL which page we're looking for
                    } else { 
                        $site = "";
                    }
                    switch ($site) //load the specified site, or the dashboard by default
                    {
                        case "email":
                            include 'workflow/email.php';
                            break;
                        case "emailpolicy":
                            include 'workflow/emailpolicy.php';
                            break;
                        case "edit_roles":
                            include 'workflow/edit_roles.php';
                            break;
                        case "edit_forms":
                            include 'workflow/edit_forms.php';
                            break;
                        case "add_workflow":
                            include 'workflow/add_workflow.php';
                            break;
                        case "process_workflow_submit":
                            include 'workflow/process_workflow_submit.php';
                            break;
                        case "view":
                            include 'workflow/view.php';
                            break;
                        case "debugstartworkflow":
                            include 'workflow/debugstartworkflow.php';
                            break;
                        case "startworkflow":
                            include 'workflow/startworkflow.php';
                            break;
                        case "workflowentry":
                            include 'workflow/workflowentry.php';
                            break;
                        case "viewsubmissions":
                            include 'workflow/viewsubmissions.php';
                            break;
                        case "roles":
                            include 'workflow/roles.php';
                            break;
                        case "createworkflow":
                            include "workflow/createworkflow.php";
                            break;
                        case "allowance-calculator-export":
                            include 'workflow/allowance-calculator-export.php';
                            break;
                        case "search":
                            include 'workflow/search.php';
                            break;
                        default:
                            include "workflow/viewsubmissions.php";
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