<?php
/*
*Template Name: zApp Staff_Review
*
* A page that shows users their staff reviews
*
* author: gerald.becker
*
*/
?>
<?php get_header(); ?>

<style>
    td, th {
        text-align: center;
        padding: 10px;
        font-size: 20px;
        border: 0;
        border-bottom: 1px solid black;
    }
    #main-content h1 {
        font-size: 45px;
        text-align: center;
        margin-bottom: 25px;
    }
    a.staffreviewlink {
        border: 1px solid #0079C1;
        display: block;
    }
</style>
    
    <div id="content" class='staff-df'>
        <?php if (have_posts()) : while (have_posts()) : the_post();  ?>
            <div class="entry">
                <div style="clear:both"></div>
                <div id="main-content">
                    <?php
                    $wpID = wp_get_current_user()->id;
                    ?>
                    <h1>Staff Review Dashboard</h1>
                    <h2>My Review</h2>
                    <?php
                    $sql = "SELECT staffreview.*, employee.first_name, employee.last_name 
                            FROM staffreview 
                            LEFT JOIN employee on staffreview.empid = employee.employee_number 
                            LEFT JOIN wp_users ON employee.user_login = wp_users.user_login 
                            WHERE wp_users.ID = '$wpID'";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    $e = '<table><tr><th></th>
                        <th>Step 1: Staff Member Prepwork</th>
                        <th>Step 2: Supervisor Prepwork</th>
                        <th>Step 3: Discussion with Supervisor</th>
                        <th>Document Links</th></tr>';
                    foreach($result as $row) {
                        $e .= '<tr>';
                        $e .= '<td>'.$row['first_name'].' '.$row['last_name'].'</td>';
                        $e .= '<td>'.($row['empsubmitdate'] == null ? '&#10006;' : '&#10004;').'</td>';
                        $e .= '<td>'.($row['supsubmitdate'] == null ? '&#10006;' : '&#10004;').'</td>';
                        $e .= '<td>'.($row['reviewsubmitdate'] == null ? '&#10006;' : '&#10004;').'</td>';
                        $e .= '<td><a class="staffreviewlink" href="'.$row['empdraftlink'].'" target="_blank">Complete Prepwork</a> <br> 
                            <a class="staffreviewlink" href="'.$row['reviewlink'].'" target="_blank">Discussion with Supervisor</a></td>';
                        $e .= '</tr>';
                    }
                    $e .= '</table>';
                    echo $e;
                    ?>
                    
                    <br><br>
                    
                    <!-- SUPERVISOR SECTION -->
                    <?php
                    $displaySup = 0;
                    $sql = "SELECT staffreview.*, employee.first_name, employee.last_name, sup.first_name AS supfirst_name, sup.last_name AS suplast_name 
                            FROM staffreview 
                            LEFT JOIN employee on staffreview.empid = employee.employee_number 
                            LEFT JOIN employee sup on staffreview.supid = sup.employee_number 
                            LEFT JOIN wp_users ON sup.user_login = wp_users.user_login 
                            WHERE wp_users.ID = '$wpID'";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    
                    $e = '<h2>My Staff</h2>
                        <table><tr><th></th>
                            <th>Step 1: Staff Member Prepwork</th>
                            <th>Step 2: Supervisor Prepwork</th>
                            <th>Step 3: Discussion with Staff Member</th>
                            <th>Document Link</th>
                        </tr>';
                    foreach($result as $row) {
                        $displaySup = 1;
                        $e .= '<tr>';
                        $e .= '<td>'.$row['first_name'].' '.$row['last_name'].'</td>';
                        $e .= '<td>'.($row['empsubmitdate'] == null ? '&#10006;' : '&#10004;').'</td>';
                        $e .= '<td>'.($row['supsubmitdate'] == null ? '&#10006;' : '&#10004;').'</td>';
                        $e .= '<td>'.($row['reviewsubmitdate'] == null ? '&#10006;' : '&#10004;').'</td>';
                        $e .= '<td><a class="staffreviewlink" href="'.$row['supdraftlink'].'" target="_blank">Complete Prepwork</a> <br> 
                            <a class="staffreviewlink" href="'.$row['reviewlink'].'" target="_blank">Discussion with Staff Member</a></td>';
                        $e .= '</tr>';
                    }
                    $e .= '</table>';
                    if($displaySup)
                        echo $e;
                    ?>
                        
                </div>
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