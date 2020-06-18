<?php
/*
*Template Name: zApp Staff_Review_SODC
*
* A page that shows users their SODC staff reviews (Type 3)
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
                    <h1>Staff Objectives and Development Cycle Dashboard</h1>
                    <hr><h2 style="text-align: center;">My Objective and Development Cycle</h2><hr>
                    <?php
                    $sql = "SELECT staffreview.*, employee.first_name, employee.last_name 
                            FROM staffreview 
                            LEFT JOIN employee on staffreview.empid = employee.employee_number 
                            LEFT JOIN wp_users ON employee.user_login = wp_users.user_login 
                            WHERE wp_users.ID = '$wpID' AND reviewtype = '3'
                            ORDER BY year DESC";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    $e = '<table><tr><th></th>
                        <th>Set Objectives</th>
                        <th>Review Objectives <br>Growth Check-in</th>
                        <th>Review Objectives</th>
                        <th>Debrief <br>Engagement Check-in</th>
                        </tr>';
                    $prevYearsHeader = true;
                    foreach($result as $row) {
                        $hideDraft = false;
                        if($row['year'] < date('Y') && $prevYearsHeader) {
                            $e .= '<tr style="background-color: #0079c1;"><td colspan="5" style="color:white;font-weight:bold;">Previous Years</td></tr>';
                            $prevYearsHeader = false;
                        }
                        if($row['year'] < date('Y')) {
                            $hideDraft = true;
                        }
                        $e .= '<tr>';
                        $e .= '<td>'.$row['first_name'].' '.$row['last_name'].'<br>('.$row['ministry'].')<br><b>'.($row['year'] != '' ? $row['year'] : '').'</b></td>'; //$row['year']-1).'/'.$row['year']
                        $e .= '<td>'.($row['sodc1date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        
                        if($row['sodc1date'] == null)
                            $e .= '<a class="staffreviewlink" href="'.$row['objectiveslink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '<td>'.($row['sodc2date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc2date'] == null && $row['sodc1date'] != null)
                            $e .= '<a class="staffreviewlink" href="'.$row['objectiveslink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '<td>'.($row['sodc3date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc2date'] != null)
                            $e .= '<a class="staffreviewlink" href="'.$row['objectiveslink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '<td>'.($row['sodc4date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc3date'] != null)
                            $e .= '<a class="staffreviewlink" href="'.$row['reviewlink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
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
                            LEFT JOIN wp_users wp1 ON sup.user_login = wp1.user_login 
                            LEFT JOIN employee sup2 on staffreview.supid2 = sup2.employee_number
                            LEFT JOIN wp_users wp2 ON sup2.user_login = wp2.user_login
                            LEFT JOIN employee sup3 on staffreview.supid3 = sup3.employee_number
                            LEFT JOIN wp_users wp3 ON sup3.user_login = wp3.user_login
                            LEFT JOIN employee sup4 on staffreview.supid4 = sup4.employee_number
                            LEFT JOIN wp_users wp4 ON sup4.user_login = wp4.user_login
                            WHERE (wp1.ID = '$wpID' OR wp2.ID = '$wpID' 
                                OR wp3.ID = '$wpID' OR wp4.ID = '$wpID')
                                AND reviewtype = '3'
                            ORDER BY year DESC";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    
                    $e = '<hr><h2 style="text-align: center;">My Staff\'s Objective and Development Cycles</h2><hr>
                        <table><tr><th></th>
                            <th>Set Objectives</th>
                            <th>Review Objectives Growth Check-in</th>
                            <th>Review Objectives</th>
                            <th>Debrief Engagement Check-in</th>
                        </tr>';
                    $prevYearsHeader = true;
                    foreach($result as $row) {
                        $hideDraft = false;
                        if($row['year'] < date('Y') && $prevYearsHeader) {
                            $e .= '<tr style="background-color: #0079c1;"><td colspan="5" style="color:white;font-weight:bold;">Previous Years</td></tr>';
                            $prevYearsHeader = false;
                        }
                        if($row['year'] < date('Y')) {
                            $hideDraft = true;
                        }
                        $displaySup = 1;
                        $e .= '<tr>';
                        $e .= '<td>'.$row['first_name'].' '.$row['last_name'].'<br>('.$row['ministry'].')<br><b>'.($row['year'] != '' ? $row['year'] : '').'</b></td>';
                        
                        $e .= '<td>'.($row['sodc1date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc1date'] == null)
                            $e .= '<a class="staffreviewlink" href="'.$row['objectiveslink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '<td>'.($row['sodc2date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc2date'] == null && $row['sodc1date'] != null)
                            $e .= '<a class="staffreviewlink" href="'.$row['objectiveslink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '<td>'.($row['sodc3date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc2date'] != null)
                            $e .= '<a class="staffreviewlink" href="'.$row['objectiveslink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '<td>'.($row['sodc4date'] == null ? '&#10006;' : '&#10004;').'<br><br>';
                        if($row['sodc3date'] != null)
                            $e .= '<a class="staffreviewlink" href="'.$row['reviewlink'].'" target="_blank" style="max-width:100px;margin:auto;">View</a>';
                        $e .= '</td>';
                        
                        $e .= '</tr>';
                    }
                    $e .= '</table>';
                    if($displaySup)
                        echo $e;
                    ?>
                        
                </div>
            </div>
            <div style="margin-top: 30px;margin-bottom: 30px;">
                <?php echo get_the_content(); ?>
            </div>
            <?php require_once 'staff-reviews-cron.php'; ?>
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