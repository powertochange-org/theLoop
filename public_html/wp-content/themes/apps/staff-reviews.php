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
                            WHERE wp_users.ID = '$wpID'
                            ORDER BY year DESC";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    $e = '<table><tr><th></th>
                        <th>Step 1: Staff Member Prepwork</th>
                        <th>Step 2: Supervisor Prepwork</th>
                        <th>Step 3: Discussion with Supervisor</th>
                        <th>Document Links</th></tr>';
                    foreach($result as $row) {
                        $e .= '<tr>';
                        $e .= '<td>'.$row['first_name'].' '.$row['last_name'].'<br>('.$row['ministry'].')<br><b>'.($row['year'] != '' ? ($row['year']-1).'/'.$row['year'] : '').'</b></td>';
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
                            LEFT JOIN wp_users wp1 ON sup.user_login = wp1.user_login 
                            LEFT JOIN employee sup2 on staffreview.supid2 = sup2.employee_number
                            LEFT JOIN wp_users wp2 ON sup2.user_login = wp2.user_login
                            LEFT JOIN employee sup3 on staffreview.supid3 = sup3.employee_number
                            LEFT JOIN wp_users wp3 ON sup3.user_login = wp3.user_login
                            LEFT JOIN employee sup4 on staffreview.supid4 = sup4.employee_number
                            LEFT JOIN wp_users wp4 ON sup4.user_login = wp4.user_login
                            WHERE wp1.ID = '$wpID' OR wp2.ID = '$wpID' 
                                OR wp3.ID = '$wpID' OR wp4.ID = '$wpID'
                            ORDER BY year DESC";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    
                    $e = '<h2>My Staff</h2>
                        <table><tr><th></th>
                            <th>Step 1: Staff Member Prepwork</th>
                            <th>Step 2: Supervisor Prepwork</th>
                            <th>Step 3: Discussion with Staff Member</th>
                            <th>Document Links</th>
                        </tr>';
                    foreach($result as $row) {
                        $displaySup = 1;
                        $e .= '<tr>';
                        $e .= '<td>'.$row['first_name'].' '.$row['last_name'].'<br><b>'.($row['year'] != '' ? ($row['year']-1).'/'.$row['year'] : '').'</b></td>';
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
            <div style="margin-top: 30px;margin-bottom: 30px;">
                <?php echo get_the_content(); ?>
            </div>
            
            <?php
            //Use this script execution to send out emails that require supervisor reminders
            
            $date = new DateTime(date("Y-m-d"));
            $newdate = new DateTime(date("Y-m-d"));
            $newdate->add(new DateInterval('P7D'));
            
            $sql = "SELECT staffreview.*, employee.first_name, employee.last_name, sup.first_name AS supfirst_name, sup.last_name AS suplast_name, wp_users.user_email 
                    FROM staffreview 
                    LEFT JOIN employee on staffreview.empid = employee.employee_number 
                    LEFT JOIN employee sup on staffreview.supid = sup.employee_number 
                    LEFT JOIN wp_users ON sup.user_login = wp_users.user_login 
                    WHERE empsubmitdate IS NOT NULL 
                        AND supsubmitdate IS NULL 
                        AND (supreminder <= '".$date->format('Y-m-d')."' 
                                OR supreminder IS NULL)
                        AND skipreminder = '0'";
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            $template = '{SUPERVISOR_NAME}, <br><br>
<p>{STAFF_NAME} has completed their Prepwork for the staff review!</p>

<p>You can now review their answers and complete your prepwork.  To do this, click the link below and in the "staff reviews" menu select "Get Staff Review".  After authorizing the script, you should see the staff member\'s answers.  In rare cases, the data may not load on the first try.  If this happens, simply select "get staff review" again and it should load.  </p>

<p><a href="{SUP_LINK}" target="_blank">Complete your Prepwork.</a></p>

<p>You can check the progress of all your staff on the <a href="https://staff.powertochange.org/forms-information/my-position/staff-reviews-2018-2019/" target="_blank">Staff Review Dashboard</a>. </p>';
            
            foreach($result as $row) {
                $subId = $row['id'];
                
                $mail = array('to' => '');
                $mail['headers'][] =  'From: Staff Review <staffreview-no-reply@p2c.com>';
                $mail['headers'][] = 'Content-Type: text/html; charset=UTF-8';
                
                $mail['to'] = $row['user_email'];
                
                $mail['subject'] = 'Staff Review Prepwork Completed by '.$row['first_name'].' '.$row['last_name'];
                
                $tmpBody = str_replace('{SUPERVISOR_NAME}', $row['supfirst_name'].' '.$row['suplast_name'], $template);
                $tmpBody = str_replace('{STAFF_NAME}', $row['first_name'].' '.$row['last_name'], $tmpBody);
                $body = str_replace('{SUP_LINK}', $row['supdraftlink'], $tmpBody);
                
                $mail['message'] = $body;
                wp_mail($mail['to'], $mail['subject'], $mail['message'], $mail['headers']);
                
                //Update the submission reminder date
                $sql = "UPDATE staffreview 
                        SET supreminder = '".$newdate->format('Y-m-d')."'
                        WHERE id = '$subId'";
                $wpdb->query($sql, ARRAY_A);
            }
            
            ?>
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