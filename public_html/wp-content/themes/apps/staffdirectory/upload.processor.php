<?php 

// filename: upload.processor.php 

// first let's set some variables 

// make a note of the current working directory, relative to root. 
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 

// make a note of the directory that will recieve the uploaded file 
$uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . 'wp-content/uploads/staff_photos/'; 

// fieldname used within the file <input> of the HTML form 
$fieldname = 'file'; 

// Now let's deal with the upload 

// possible PHP upload errors 
$errors = array(1 => 'Php.ini max file size exceeded', 
                2 => 'Html form max file size exceeded', 
                3 => 'File upload was only partial', 
                4 => 'No file was attached'); 

// check for PHP's built-in uploading errors 
if ($_FILES[$fieldname]['error'] == 0) {
    // check that the file we are working on really was the subject of an HTTP upload 
    if (is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
        // validation... since this is an image upload script we should run a check   
        // to make sure the uploaded file is in fact an image. Here is a simple check: 
        // getimagesize() returns false if the file tested is not an image. 
        if (getimagesize($_FILES[$fieldname]['tmp_name'])) {
            // At this point, we have passed almost all of the necessary validation

            // Replace all whitespace with underscore in filename
            $filename = preg_replace('/\s+/', '_', $_FILES[$fieldname]['name']);

            // Remove single quotes
            $filename = preg_replace('/\'+/', '', $filename);

            // make a unique filename for the uploaded file and check it is not already 
            // taken... if it is already taken keep trying until we find a vacant one 
            // sample filename: 1140732936-filename.jpg 
            $now = time(); 
            while(file_exists($uploadFilename = $uploadsDirectory.$now.'-'.$filename)) 
            { 
                $now++; 
            } 
            // Attempt to save the image thumbnail
            if (saveImage($_FILES[$fieldname]['tmp_name'], $uploadFilename)) {
                // If you got this far, everything has worked and the file has been successfully saved. 
                // Update database fields
                $wpdb->update( 'employee', array('photo' => $now.'-'.$filename), array('user_login' => $current_user->user_login), 
                	array('%s'), array('%s') );
            

			    $wpdb->update( 'employee', 
			    		array( 'share_photo' => 1	),
			    		array( 'user_login' => $current_user->user_login  ),
			    		array('%d')
			    );	
            } else {
                error('Saving file failed; please ensure you are using a supported file type (.gif, .jpeg, .jpg, .png).<br/>This error could also be caused by insufficient permissions on the image directory');
            }
        } else { // Not an image
            error('Only image uploads are allowed');
        }
    } else { // not an http upload
        error('Not an HTTP upload');
    }
} else { // upload error
    error($errors[$_FILES[$fieldname]['error']]); 
}
     
// The following function is an error handler which is used 
// to output an error to the page if the file upload fails 
function error($error) 
{ 
    echo '<br/>
          <br/>
          <div id="Upload">
            <h1>Image upload failure</h1>
            <p><span class="red">' . $error . '</span></p>
         </div>';
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
