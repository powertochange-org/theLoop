<?php 
include("constant.php");
if (isset($_POST['type'])){
	switch ($_POST['type']){
		case 'staff_photo':
			if (isset($_POST['api_token']) && $_POST['api_token'] == $api_token){
				if (isset($_POST['staff_username'])){
					
					global $GET_WORD_PRESS_VARIABLE;
					$GET_WORD_PRESS_VARIABLE = true;
					
					include('../wp-config.php');
					
					// Creates a connection because wp will not be active
					$con=mysqli_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_NAME"));
					
					
					$sql="SELECT `photo` FROM `employee` WHERE `user_login` = '$_POST[staff_username]'";
					$result = mysqli_query($con, $sql);
					if($staff = $result->fetch_object()){ 
						if (is_null($staff->photo)){
							$file = "$_SERVER[DOCUMENT_ROOT]/wp-content/uploads/staff_photos/anonymous.jpg";
						}
						else {
							$file = "$_SERVER[DOCUMENT_ROOT]/wp-content/uploads/staff_photos/".$staff->photo;
						}
						if(is_file($file)){
							$temp = explode(".", $file);
							$ext = strtolower(end($temp));
							header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
							header("Content-Length:".filesize ($file));
							header("Content-Type: image/$ext");
							readfile($file);
							exit;
						} 
						else{
							header($_SERVER["SERVER_PROTOCOL"]." 404 NOT FOUND");
							echo "ERROR: photo not found";
						} 
						mysqli_close($con);
					}
				}
				else{
					echo 'ERROR: no staff username given';
				}
			}
			else{
				echo 'ERROR: problem with api_token';
			}
			break;
		default:
			echo 'ERROR: unknown type';
	}
}
else {
	echo 'ERROR: no type given';
}

?>