<?php 

// filename: upload.processor.php 

// first let's set some variables 

// make a note of the current working directory, relative to root. 
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 

// make a note of the directory that will recieve the uploaded file 
$uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . 'wp-content/uploads/staff_photos/'; 

// make a note of the location of the upload form in case we need it 
$uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'staff-directory/?page=profile'; 

// make a note of the location of the success page 
$uploadSuccess = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'upload.success.php'; 
$uploadSuccess = 'http://' . $_SERVER['HTTP_HOST'] . '/staffdirectory/?page=upload_success';

// fieldname used within the file <input> of the HTML form 
$fieldname = 'file'; 

// Now let's deal with the upload 

// possible PHP upload errors 
$errors = array(1 => 'php.ini max file size exceeded', 
                2 => 'html form max file size exceeded', 
                3 => 'file upload was only partial', 
                4 => 'no file was attached'); 

// check for PHP's built-in uploading errors 
($_FILES[$fieldname]['error'] == 0) 
    or error($errors[$_FILES[$fieldname]['error']], $uploadForm); 
     
// check that the file we are working on really was the subject of an HTTP upload 
@is_uploaded_file($_FILES[$fieldname]['tmp_name']) 
    or error('not an HTTP upload', $uploadForm); 
     
// validation... since this is an image upload script we should run a check   
// to make sure the uploaded file is in fact an image. Here is a simple check: 
// getimagesize() returns false if the file tested is not an image. 
@getimagesize($_FILES[$fieldname]['tmp_name']) 
    or error('only image uploads are allowed', $uploadForm); 
     
// make a unique filename for the uploaded file and check it is not already 
// taken... if it is already taken keep trying until we find a vacant one 
// sample filename: 1140732936-filename.jpg 
$now = time(); 
while(file_exists($uploadFilename = $uploadsDirectory.$now.'-'.$_FILES[$fieldname]['name'])) 
{ 
    $now++; 
} 
$wpdb->update( 'employee', array('photo' => $now.'-'.$_FILES[$fieldname]['name']), array('user_login' => $current_user->user_login), 
	array('%s'), array('%s') );

// Save the image thumbnail
@saveImage($_FILES[$fieldname]['tmp_name'], $uploadFilename)
    or error('Saving file failed; please ensure you are using a supported file type (.gif, .jpeg, .jpg, .png)', $uploadForm);

     
// If you got this far, everything has worked and the file has been successfully saved. 
// We are now going to redirect the client to a success page. 

			$wpdb->update( 'employee', 
					array( 'share_photo' => 1	),
					array( 'user_login' => $current_user->user_login  ),
					array('%d')
			);	
			$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 
			header( 'Location: ' . site_url() . $directory_self . 'staff-directory/?page=profile' ) ;
	?>
<div id="Upload"> 
            <h1>File upload</h1> 
            <p />Congratulations! Your file upload was successful
			<p /><a href=
					<?php echo $directory_self . 'staff-directory/?page=profile'; ?>
				>Return to Profile</a>
        </div> 
<?php
// The following function is an error handler which is used 
// to output an HTML error page if the file upload fails 
function error($error, $location, $seconds = 5) 
{ 
    header("Refresh: $seconds; URL=\"$location\""); 
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n". 
    '"http://www.w3.org/TR/html4/strict.dtd">'."\n\n". 
    '<html lang="en">'."\n". 
    '    <head>'."\n". 
    '        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">'."\n\n". 
    '        <link rel="stylesheet" type="text/css" href="stylesheet.css">'."\n\n". 
    '    <title>Upload error</title>'."\n\n". 
    '    </head>'."\n\n". 
    '    <body>'."\n\n". 
    '    <div id="Upload">'."\n\n". 
    '        <h1>Upload failure</h1>'."\n\n". 
    '        <p>An error has occured: '."\n\n". 
    '        <span class="red">' . $error . '...</span>'."\n\n". 
    '         The upload form is reloading</p>'."\n\n". 
    '     </div>'."\n\n". 
    '</html>'; 
    exit; 
} // end error handler 

// This function, given a source image and destination location, saves a crop of
// the image set to 440 px wide, in the destination location. 
function saveImage($src, $dest)
{
    // Maximum width for the image; we'll calculate the height appropriately
    $target_width = 440;
    // Make sure we have width and height set in the post variables
    if ((int)$_POST['width'] > 0 && (int)$_POST['height'] > 0) {
        // Use post variables
        $source_width = $_POST['width'];
        $source_height = $_POST['height'];
    } else { // No post variables set
        // Just use the entire images width / height
        list($source_width, $source_height) = getimagesize($src);
    }
    $target_height = ($target_width / $source_width * $source_height);
    switch (exif_imagetype($src)) { 
        // Determine what type of image we're working with, and create the image
        case IMAGETYPE_GIF:
            $img_src = imagecreatefromgif($src);
            break;
        case IMAGETYPE_JPEG:
            $img_src = imagecreatefromjpeg($src);
            break;
        case IMAGETYPE_PNG:
            $img_src = imagecreatefrompng($src);
            break;
        default: // Handle bad files
            error_log("Error: Attempted to upload an image of type " . exif_imagetype($src) . ", which is unsupported");
            return false;
    }
    // Create the image that will be used as the destination
    $img_dest = ImageCreateTrueColor($target_width,$target_height);

    // Copy the image over
	imagecopyresampled($img_dest,$img_src,
        0,0, // Destination x and y values
        (empty($_POST['x']) ? 0 : $_POST['x']), // Source x coord
        (empty($_POST['y']) ? 0 : $_POST['y']), // Source y coord
	    $target_width,$target_height, // Destination width and height
        $source_width,$source_height); // Source width and height
    // Return the result of imagejpeg; if it's successful, this will be true. 
    // Otherwise, false
    return imagejpeg($img_dest, $dest, 100);
}

?>
