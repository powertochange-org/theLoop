<?php



// echo "\nfields $fields";
/*echo "<br>newstatus $newstatus";
echo "<br>submissionID $submissionID";
echo "<br>formID $formID";
echo "<br>user $user";
echo "<br>statuslevel $statuslevel";
echo "<br>historyApprovalStage $historyApprovalStage";
echo "<br>newApprovalStatus $newApprovalStatus";
echo "<br> $";
echo "<br> $";*/
//, , $, $, $, $misc_content, $commenttext, $behalfof, $sup, $uniqueToken, $miscfields, $hrnotes = '', $ = 0, $newDirectApprover = 0

//TELUS NEW CONTRACT FORM
if(($formID == 223 || $formID == 224) && $newApprovalStatus == 100 && $newstatus == 7) {
    $sql = "SELECT * 
            FROM workflowformsubmissions
            WHERE SUBMISSIONID = '$submissionID'";
    
    $result = $wpdb->get_results($sql, ARRAY_A);
        
    $validRequest = false;
    
    
    $FULLNAME = $CURRENTCELL = $CURRENTCARRIER = $ACCOUNTNUMBER = $CONTRACTTYPE = $DEVICENEEDED = $DEVICE = $APPLECARE = $PLANTYPE = $AREACODE = $SHIPPINGADDRESS = $CONTACTPHONE = '';
    
    
    if(count($result) != 0) {
        $validRequest = true;
        foreach($result as $row) {
            // var_dump($row);
            
            if(($formID == 223 || $formID == 224) && $row['FIELDID'] == 3)
                $FULLNAME = $row['VALUE'];
            else if(($formID == 223 && $row['FIELDID'] == 14) 
                || ($formID == 224 && $row['FIELDID'] == 6))
                $CURRENTCELL = $row['VALUE'];
            else if($formID == 223 && $row['FIELDID'] == 17)
                $CURRENTCARRIER = $row['VALUE'];
            else if($formID == 223 && $row['FIELDID'] == 20)
                $ACCOUNTNUMBER = $row['VALUE'];
            else if($formID == 223 && $row['FIELDID'] == 27)
                $CONTRACTTYPE = $row['VALUE'];
            else if($formID == 223 && $row['FIELDID'] == 35)
                $DEVICENEEDED = ($row['VALUE'] == 'Yes');
            else if(($formID == 223 && $row['FIELDID'] == 38) 
                || ($formID == 224 && $row['FIELDID'] == 18)) {
                $DEVICE = $row['VALUE'];
                if($formID == 224)
                    $DEVICENEEDED = true;
            } else if(($formID == 223 && $row['FIELDID'] == 41) 
                || ($formID == 224 && $row['FIELDID'] == 21))
                $APPLECARE = $row['VALUE'];
            else if(($formID == 223 && $row['FIELDID'] == 48)
                || ($formID == 224 && $row['FIELDID'] == 28))
                $PLANTYPE = $row['VALUE'];
            else if(($formID == 223 && $row['FIELDID'] == 51)
                || ($formID == 224 && $row['FIELDID'] == 31))
                $AREACODE = $row['VALUE'];
            else if(($formID == 223 && $row['FIELDID'] == 54)
                || ($formID == 224 && $row['FIELDID'] == 34))
                $SHIPPINGADDRESS = $row['VALUE'];
            else if(($formID == 223 && $row['FIELDID'] == 57)
                || ($formID == 223 && $row['FIELDID'] == 37))
                $CONTACTPHONE = $row['VALUE'];
        }
    }
    
    
    //Contract logic
    $contractTypeEm = '';
    if($CONTRACTTYPE == 'Month to Month')
        $contractTypeEm = 'Please activate a line on a month-to-month plan';
    else if($CONTRACTTYPE == '3 Year Agreement')
        $contractTypeEm = 'Please activate a line on a three-year term';
    else
        $contractTypeEm = "Please renew phone number $CURRENTCELL on a new three-year term.";
    
    //Port logic
    $portEm = '';
    if($CURRENTCARRIER != '' || $CURRENTCELL != '') {
        $portEm = "Please port in $CURRENTCELL";
        if($CURRENTCARRIER != '')
            $portEm .= " from $CURRENTCARRIER account number $ACCOUNTNUMBER";
    } else
        $portEm = "No phone number will be ported in. Please supply a new number for this line local to area code $AREACODE.";
    
    //Device logic
    $deviceEm = '';
    if($DEVICENEEDED) {
        $deviceEm = "This plan will include the following device $DEVICE";
        if($APPLECARE == 'Yes')
            $deviceEm .= " and include AppleCare";
    } else {
        $deviceEm = 'No device purchase will be included in this order';
    }
    
    //Plan logic
    $planEm = '';
    if($PLANTYPE != '') {
        
        switch($PLANTYPE) {
            case 'Corp Connect Roam Flex 3GB - $35 ($48 with new device)':
                $planEm = 'Corp Connect Roam Flex 3GB at $'.($DEVICENEEDED ? '48' : '35');
                break;
            case 'Corp Connect Roam Flex 5GB - $39 ($52 with new device)':
                $planEm = 'Corp Connect Roam Flex 5GB at $'.($DEVICENEEDED ? '52' : '39');
                break;
            case 'Corp Easy Share 3GB - $43 ($53 with new device)':
                $planEm = 'Corp Easy Share 3GB at $'.($DEVICENEEDED ? '53' : '43');
                break;
            case 'Corp Connect ER Can-US 3GB - $55 ($65 with new device)':
                $planEm = 'Corp Connect ER Can-US 3GB at $'.($DEVICENEEDED ? '65' : '55');
                break;
            case 'Corp Adv Voice 20 - $20 ($25 with new device)':
                $planEm = 'Corp Adv Voice 20 at $'.($DEVICENEEDED ? '25' : '20');
                break;
            case 'Access Data Tablet - $9.50':
                $planEm = 'Access Data Tablet at $9.50';
                break;
            case 'Corporate Watch Access Plan - $10':
                $planEm = 'Corporate Watch Access Plan at $10';
                break;
            case 'Switch My Plan To: Corp Connect Roam Flex 3GB - $48':
                $planEm = 'Corp Connect Roam Flex 3GB at $48';
                break;
            case 'Switch My Plan To: Corp Connect Roam Flex 5GB - $52':
                $planEm = 'Corp Connect Roam Flex 5GB at $52';
                break;
            case 'Switch My Plan To: Corp Easy Share 3GB - $53':
                $planEm = 'Corp Easy Share 3GB at $53';
                break;
            case 'Switch My Plan To: Corp Connect ER Can-US 3GB - $65':
                $planEm = 'Corp Connect ER Can-US 3GB at $65';
                break;
            case 'Switch My Plan To: Corp Adv Voice 20 - $25':
                $planEm = 'Corp Adv Voice 20 at $25';
                break;
            default: 
                $planEm = 'Please continue this line on the existing plan';
        }
    } else {
        $planEm = 'Please continue this line on the existing plan';
    }
    
    //Compose Email
    $telusEmail =   "<p style='margin-bottom:5px;'>ATTN: TELUS WSS WEST SUPPORT</p>
                    <p style='margin-bottom:5px;'>Please fill the following order.</p>
                    <p style='margin-bottom:5px;'><b>Staff member associated with this phone:</b> $FULLNAME</p>
                    <p style='margin-bottom:5px;'><b>Action requested:</b> $contractTypeEm </p>
                    <p style='margin-bottom:5px;'><b>Porting actions:</b> $portEm</p>
                    <p style='margin-bottom:5px;'><b>Device:</b> $deviceEm</p>
                    <p style='margin-bottom:5px;'><b>Plan choice:</b> $planEm</p>
                    <p style='margin-bottom:5px;'><b>Shipping address for this order:</b> $SHIPPINGADDRESS</p>
                    <p style='margin-bottom:5px;'><b>Please ensure that PORT BLOCKING is enabled on this line.</b></p>
                    <p style='margin-bottom:5px;'>Ref # $submissionID</p>";
    
    $userEmail = '';
    $userName = '';
    $sql = "SELECT USER, user_email, first_name, last_name
            FROM workflowformstatus
            INNER JOIN employee ON employee.employee_number = workflowformstatus.USER
            INNER JOIN wp_users ON employee.user_login = wp_users.user_login
            WHERE SUBMISSIONID = '$submissionID'";
        
    $result = $wpdb->get_results($sql, ARRAY_A);
    if(count($result) == 1) {
        $userEmail = $result[0]['user_email'];
        $userName = $result[0]['first_name'].' '.$result[0]['last_name'];
    }
    
    $mail = array('to' => '');
    $mail['headers'][] =  'From: Keith Richmond <keith.richmond@p2c.com>';
    $mail['to'] = 'mrpotatohead@p2c.com';
    $mail['headers'][] = 'Cc: '.$userName.' <'.$userEmail.'>';
    
    //Add role members
    $sql = "SELECT user_email, first_name, last_name, EMAIL_ON
            FROM workflowrolesmembers
            INNER JOIN employee ON employee.employee_number = workflowrolesmembers.MEMBER
            INNER JOIN wp_users ON employee.user_login = wp_users.user_login
            WHERE ROLEID = '32'
            ORDER BY MEMBER";
    $emailRecepients = $wpdb->get_results($sql, ARRAY_A);
    foreach($emailRecepients as $row) {
        if($row['user_email'] != '' && $row['EMAIL_ON']) {
            $mail['headers'][] = 'Cc: '.$row['first_name'].' '.$row['last_name'].' <'.$row['user_email'].'>';
        }
    }
    
    $mail['headers'][] = 'Content-Type: text/html; charset=UTF-8';
    $mail['subject'] = 'ATTN: TELUS WSS WEST SUPPORT';
    
    $mail['message'] = $telusEmail;
    
    wp_mail($mail['to'], $mail['subject'], $mail['message'], $mail['headers']);
    //echo $userName.' '.$userEmail.'<br>'.$telusEmail; die();
}







