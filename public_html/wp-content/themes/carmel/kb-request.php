<?php
/*
*Template Name: zApp KB_Request
*
* Creates a page that allows users to submit KB article requests. Users can upvote other requests as well.
*
* author: gerald.becker
*
*/

function isAppAdmin($app, $accessLevel) {
    $current_user_id = wp_get_current_user()->id;
    return (pow(2, $accessLevel) & intval(get_user_meta($current_user_id, ('loopadmin_'.$app), true))) > 0;
}


//Handle an AJAX call for upvoting or marking the request as complete
if(isset($_POST['requestid']) && isset($_POST['complete'])) {
    //Admin marking the article as complete
    $returndata = array('ReturnCode'=>'', 'Msg'=>'', 'NewCount'=>'');
    if(!isAppAdmin('kb', 0)) {
        $returndata['ReturnCode'] = '400';
        die();
    }
    $requestid = $_POST['requestid'];
    
    $sql = "UPDATE kb_requests 
            SET added = '1',
                date_added = '".date('Y-m-d')."'
            WHERE kb_id ='$requestid'";
    
    $result = $wpdb->query($sql, ARRAY_A);
    
    if($wpdb->result == false) {
        $returndata['ReturnCode'] = '400';
        $returndata['Msg'] = 'Error';
    } else {
        $returndata['ReturnCode'] = '200';
        $returndata['Msg'] = 'Removed';
    }
    echo json_encode($returndata);
    die();
} else if(isset($_POST['requestid'])) {
    //Process an upvote
    $returndata = array('ReturnCode'=>'', 'Msg'=>'', 'NewCount'=>'');
    
    $user = wp_get_current_user()->id;
    $requestid = $_POST['requestid'];
    
    $sql = "INSERT INTO kb_upvotes (kb_id, user_id)
            VALUES ('$requestid', '$user')";
    
    $result = $wpdb->query($sql, ARRAY_A);
    
    if($wpdb->result == false) {
        $returndata['ReturnCode'] = '400';
        $returndata['Msg'] = 'Already upvoted.';
    } else {
        $returndata['ReturnCode'] = '200';
        
        $sql = "SELECT SUM(upvoted) AS votes
                FROM kb_upvotes
                WHERE kb_id = '$requestid'";
        $result = $wpdb->get_results($sql, ARRAY_A);
        $votes = 1;
        if($result) {
            if($result[0]['votes'] != null)
                $votes = $result[0]['votes'];
        }
        $returndata['NewCount'] = $votes;
    }
    echo json_encode($returndata);
    die();
}

//Add a new article suggestion
if(isset($_POST['newkbrequest']) && $_POST['newkbrequest'] != '') {
    $request = $_POST['newkbrequest'];
    $user = wp_get_current_user()->id;
    
    $sql = "INSERT INTO kb_requests (request, requested_by, date_requested)
            VALUES ('$request', '$user', '".date('Y-m-d')."')";
    $result = $wpdb->query($sql, ARRAY_A);
    $insert_id = $wpdb->insert_id;
        
    if($wpdb->result == false)
        $_SESSION['kbmsg'] = 'Failed to insert request.';
    else {
        //Add an entry into the upvotes
        $sql = "INSERT INTO kb_upvotes (kb_id, user_id)
                VALUES ('$insert_id', '$user')";
        $result2 = $wpdb->query($sql, ARRAY_A);
        
        $msg = 'A new KB article has been requested at: '.$_SERVER['SERVER_NAME'].'/kb-request '."\r\n ".'Request : '.$request;
        ini_set('SMTP','smtp.powertochange.org');
        mail("helpdesk@p2c.com","New KB Article Request", $msg);
        $_SESSION['kbmsg'] = 'Your Knowledge Base Article request has been received successfully!';
        header("Refresh:0");
        die();
    }
}
?>

<?php get_header(); ?>
    <div id="content" class='staff-d'>
        <?php if (have_posts()) : while (have_posts()) : the_post();  ?>
            <div class="entry">
                <div style="clear:both"></div>
                <div id="main-content">
                    <?php include('wikimenu.php'); ?>
                    
                    <h1>Request a Knowledge Base Article</h1>
                    <form id="kbrequest" method="post" action="./">
                        <textarea name="newkbrequest"></textarea>
                        
                        <input type="submit" value="Submit"/>
                        <?php
                        if(isset($_SESSION['kbmsg'])) {
                            echo '<p class="kbmsg">'.$_SESSION['kbmsg'].'</p>';
                            unset($_SESSION['kbmsg']);
                        }
                        ?>
                    </form>
                    
                    <div id="kb-prev-requests">
                        <h1>Current Requests</h1>
                        <ul>
                        <?php
                        $user = wp_get_current_user()->id;
                        $sql = "SELECT kbr.*, display_name, upvoted
                                FROM kb_requests kbr
                                LEFT OUTER JOIN wp_users ON kbr.requested_by = wp_users.ID
                                LEFT OUTER JOIN kb_upvotes ON kb_upvotes.kb_id = kbr.kb_id AND kb_upvotes.user_id = '$user'
                                WHERE kbr.added = '0'
                                ORDER BY  kbr.kb_id ASC";
                        
                        $result = $wpdb->get_results($sql, ARRAY_A);
                        $i = 0;
                        foreach($result as $k=>$row) {
                            $sql = "SELECT SUM(upvoted) AS votes
                                    FROM kb_upvotes
                                    WHERE kb_id = '$row[kb_id]'";
                            $result2 = $wpdb->get_results($sql, ARRAY_A);
                            $votes = 0;
                            if($result2) {
                                if($result2[0]['votes'] != null)
                                    $votes = $result2[0]['votes'];
                            }
                            $result[$k]['votes'] = $votes;
                            
                            $numvotes[$k] = $votes;
                            //$kbid[$k] = $row['kb_id']; //In case we want to put newer requests at the top
                        }
                        //A very cool sort that sorts the results by the number of votes then by age
                        if(!empty($numvotes))
                            array_multisort($numvotes, SORT_DESC, $result); //$kbid, SORT_DESC, - for newer requests at the top
                        
                        foreach($result as $k=>$row) {
                            echo '<li id="kb-row-'.$row['kb_id'].'"><div class="kb-left"><div id="kb-id-'.$row['kb_id'].'" class="kb-left-icon'.($row['upvoted'] == '1' ? ' selected' : '').'"
                                onclick="processRequest('.$row['kb_id'].',0);"></div><span id="kbvotes'.$row['kb_id'].'">'.$row['votes'].'</span></div>
                                <div class="kb-right">'.
                                $row['request'].'<br><b>Submitted By: '.$row['display_name'].'</b>';
                                
                            if(isAppAdmin('kb', 0))
                                echo '<button onclick="processRequest('.$row['kb_id'].',1);">Mark as Complete</button>';
                            echo '</div></li>';
                        }
                        
                        ?>
                        </ul>
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
</div>
<!--wrapper end-->
<div style='clear:both;'></div>

<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript">
function processRequest(num, complete) {
    var formData = new FormData();
    formData.append('requestid', num);
    if(complete == '1') {
        formData.append('complete', complete);
    }
    $.ajax({
        url: "/kb-request",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false
    }).done(function( data ) {
        console.log( data );
        var obj = JSON.parse(data);
        if(obj.ReturnCode == '200' && obj.Msg == 'Removed') {
            document.getElementById('kb-row-' + num).style.display = 'none';
        } else if(obj.ReturnCode == '200') {
            document.getElementById('kbvotes' + num).innerHTML = obj.NewCount;
            document.getElementById('kb-id-' + num).className += ' selected';
        }
    });
}
</script>

<?php get_footer(); ?>

