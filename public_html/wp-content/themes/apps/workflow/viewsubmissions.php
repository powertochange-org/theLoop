<?php
/*
*Views a list of submissions that was submitted by the user. Also shows the list of submissions
*from other users that you are an approver of. 
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/
?>
<div id="main-content">
    <div id="content-workflow">
        <h1>View Submissions</h1>
        
        <?php
        
        
        if(isset($_POST['newuser']) && $_POST['newuser'] != '') {
            $_SESSION['activeuser'] = $_POST['newuser'];
            echo 'Now logged in as:';
            
            //Connect to server and select database
            $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

            //Check if connection succeeded
            if(!$con) {
                die("Connection failed ");
            }
                
            $sql = "SELECT CONCAT(first_name, ' ', last_name) AS name
                    FROM employee
                    WHERE employee_number = '$_SESSION[activeuser]'";
            
            $result = mysqli_query($con, $sql);
            
            if($row = mysqli_fetch_assoc($result)) {
                $_SESSION['activeusername'] = $row['name'];
            } else {
                $_SESSION['activeusername'] = 'Unknown User';
            }
            
            
            mysqli_close($con);
            
            
        } else if(isset($_SESSION['activeuser']) && $_SESSION['activeuser'] != '') {
            echo 'Currently logged in as:';
        } else {
            $_SESSION['activeuser'] = $_SESSION['activeusername'] = '0';
            echo 'Defaulting to log in as: ';
        }
        
        echo $_SESSION['activeuser'].' | '.$_SESSION['activeusername'];
        echo '   ACTUAL: '.Workflow::actualloggedInUser().'<BR>';
        
        ?>
        
        <form id="edituser" action="?page=viewsubmissions" method="POST" autocomplete="off">
            <input type="text" name="newuser">
            <input type="submit" value="Submit">
        </form>
        
        <?php
        if($_SESSION['activeuser'] != '0') {
            
        ?>
            <h2>Forms for User</h2>
            <?php
            
            $obj = new Workflow();
            echo $obj->viewAllSubmissions($_SESSION['activeuser']);
            ?>
            <h2>Forms Requiring Approval</h2>
            <?php
            echo $obj->viewAllSubmissionsAsApprover($_SESSION['activeuser']);
        } else {
            echo('<br>You need to log in!');
        }
        ?>
        
        
    </div>
</div>