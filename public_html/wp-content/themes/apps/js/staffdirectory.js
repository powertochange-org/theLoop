var jcrop_api;

$(document).ready(function() {
    // Set up the change function for the file element 
    jQuery("#file").change(function () {
        // If this browser supports the FileReader API
        if (window.FileReader) {
            // Toggle the buttons
            $(".changepic").toggle();
            // Set up the jcrop:
            jQuery(function($) {
                $('#photo').Jcrop({
                    bgColor: 'white',
                    boxWidth: $("#photo").width() // Limit the width to the same as the current image being displayed
                },function(){
                    jcrop_api = this;
                    jcrop_api.disable();

                    // Create a new filereader
                    var fRead = new FileReader();

                    // Get the first file
                    fRead.readAsDataURL($("#file")[0].files[0]);
                    
                    // Once we're done loading...
                    fRead.onload = function () {
                        // Set the source of the preview image to this new image
                        jcrop_api.setImage(fRead.result, function() {
                            jcrop_api.enable();
                            jcrop_api.setOptions({
                                trueSize: [
                                    $('.jcrop-holder img')[0].naturalWidth,
                                    $('.jcrop-holder img')[0].naturalHeight
                                ]
                            });
                        });
                    }
                });
            });
        } else { // browser doesn't support filereader
            // Immediately upload; don't support any cropping
            document.getElementById("theForm").submit();
        }
    });
});

// This function updates the coordinates of some form values, 
// based off of a passed-in object that has the values of the
// crop values
function updateCoords(c) {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#width').val(c.w);
    $('#height').val(c.h);
}

// This function prompts for confirmation before 'deleting' an item (phone
// number, email address, etc). 
// In this case, deleting means that it hides the element from the page, and
// clears out all the fields of the item. 
// The backend php will perfrom the actual deletion
function deleteItem(type, id) {
    console.log("Working on a " + type + " with an id of " + id);
    if (confirm("Are you sure you want to delete this " + type + "?")) {
        var element;
        switch(type) {
            case "phone number":
                // Get the string representing the item
                element = '#editPhone' + id;
                break;
            case "email address":
                // Get the string representing the item
                element = '#editEmail' + id;
                break;
            default:
                alert("Sorry! Something went wrong.\nIt seems we don't yet support deleting those types of items");
                return;
        }
        // Hide the element from the page
        $(element).hide();
        // Clear out all of the text fields associated with this element
        $(element + ' input[type="text"]').val('');
    }
}
