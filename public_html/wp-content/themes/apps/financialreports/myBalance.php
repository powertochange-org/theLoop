<?php 

require('../../../../wp-blog-header.php');
const KEY = 'staffAccountBalance';

if (is_user_logged_in()) {
    include('rs_functions.php');
    global $current_user;
    get_currentUserInfo();
    $result = "No Staff Account";
    $user_id = $current_user->user_login;
    if (sizeof($user_id)>0 ) {
        if (isset($_SESSION[KEY])) {
            $result = $_SESSION[KEY];
        } else {
            $query = $wpdb->get_results($wpdb->prepare( 
                    "SELECT staff_account
                    FROM employee
                    WHERE user_login = %s", $user_id));
            $staff_account = $query[0]->staff_account;
            syslog(LOG_DEBUG, "Lookup up balance for ".$current_user->user_login.", account: ".$staff_account);
            if (sizeof($staff_account)>0) {
                $response = str_replace("<br />","",accountBalance($staff_account, $current_user->user_login));
                syslog(LOG_INFO, "Account balance response: ".$response);
                $account = explode("-",explode(":", $response)[1])[0];
                $balance = explode(":", $response)[2];
                $result = $account.": ".$balance;
                $_SESSION[KEY] = $result;
            }
        }
    }
    header('Content-type: application/json');
    http_response_code(200);
    echo(json_encode($result));
}
