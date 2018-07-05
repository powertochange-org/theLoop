<?php
/*
*Template Name: zApp Email_Open_Report
*
* Creates a page that allows for reporting on the email open rate of loop posts.
*
* author: gerald.becker
*
*/

function isAppAdmin($app, $accessLevel) {
    $current_user_id = wp_get_current_user()->id;
    return (pow(2, $accessLevel) & intval(get_user_meta($current_user_id, ('loopadmin_'.$app), true))) > 0;
}
?>
<style>
th {
    padding: 15px;
    text-align: center;
}
#content.staff-d td {
    padding: 5px;
    text-align: center;
}

#content.staff-d td.left-align {
    text-align: left;
}

</style>
<?php get_header(); ?>
    <div id="content" class='staff-d'>
        <?php if(isAppAdmin('emailopenreport', 0)) {
            $showAll = 0;
            if(isset($_GET['archive'])) {
                if($_GET['archive'] == 1)
                    $showAll = 1;
                else if($_GET['archive'] == 25)
                    $showAll = 25;
                else if($_GET['archive'] == 50)
                    $showAll = 50;
                else if($_GET['archive'] == 100)
                    $showAll = 100;
            }
            if (have_posts()) : while (have_posts()) : the_post();  ?>
            <div class="entry">
                <div style="clear:both"></div>
                <div id="main-content">
                    <h1>Email Open Rate Report</h1>
                    <div>
                        <?php
                        $sql = "SELECT email_subject, 
                                sender,
                                COUNT(*) AS 'EMAILSSENT', 
                                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS 'OPENED',
                                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS 'NOTOPENED',
                                AVG(CASE WHEN status = 1 THEN TIMESTAMPDIFF(SECOND, date_sent, date_opened) END) AS 'OPENAVERAGETIME',
                                MIN(date_sent) AS 'SENDDATE'
                                FROM `email_open_tracking` eot
                                WHERE email_subject <> '' AND email_subject IS NOT NULL AND email_subject <> 'UNKNOWN'
                                GROUP BY email_subject, sender
                                ORDER BY date_sent DESC
                                ".($showAll == 0 ? 'LIMIT 10' : ($showAll == 1 ? '' : 'LIMIT '.$showAll));
                        
                        $result = $wpdb->get_results($sql, ARRAY_A);
                        
                        echo '<table>
                                <tr>
                                    <th>Loop Post</th>
                                    <th>Sender</th>
                                    <th>Date Sent</th>
                                    <th>Emails Sent</th>
                                    <th>Opened</th>
                                    <th>Not Opened</th>
                                    <th>Open Rate</th>
                                    <th>Open Average Time (Min)</th>
                                </tr>';
                        foreach($result as $key=>$row) {
                            echo '<tr>
                                    <td class="left-align">'.$row['email_subject'].'</td>
                                    <td class="left-align">'.$row['sender'].'</td>
                                    <td>'.$row['SENDDATE'].'</td>
                                    <td>'.$row['EMAILSSENT'].'</td>
                                    <td>'.$row['OPENED'].'</td>
                                    <td>'.$row['NOTOPENED'].'</td>
                                    <td>'.(round($row['OPENED'] / $row['EMAILSSENT'], 2) * 100).'%</td>
                                    <td>'.round($row['OPENAVERAGETIME'] / 60, 2).'</td>
                                </tr>';
                        }
                        echo '</table>';
                        
                        
                        echo '<br><br><a href="/email-open-rate/?archive=1">Show All Loop Posts</a>&nbsp;&nbsp;&nbsp;
                            <a href="/email-open-rate/?archive=25">Last 25</a>&nbsp;&nbsp;&nbsp;
                            <a href="/email-open-rate/?archive=50">Last 50</a>&nbsp;&nbsp;&nbsp;
                            <a href="/email-open-rate/?archive=100">Last 100</a>';
                        ?>
                    </div>
                    
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

<?php } else {
    echo 'You do not have access to this page. Please contact helpdesk@p2c.com<br><br>';
} ?>
</div>
<!--wrapper end-->
<div style='clear:both;'></div>

<?php get_footer(); ?>

