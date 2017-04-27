var jcrop_api;

// This variable is to keep track of whether or not we have a variable at all.
// Unfortunately, the jcrop coordinates do NOT get reset if we 'release' the
// selection, so we have to manually keep track of whether or not we have a
// selection
var selection = true;

var repeat = false;

// Do we have support for the placeholder feature?
var placeholderSupport = 'placeholder' in document.createElement("input");

$(document).ready(function() {
  // If we don't have access to the placeholder feature
  if (!placeholderSupport) {
    console.log("Manually handling placeholders...");
    insertPlaceholders();
  }
  // Set up the change function for the file element
  jQuery("#file").change(function () {
    // If this browser supports the FileReader API

    if (window.FileReader) {

      //make sure we have a file
      if ($("#file")[0].files.length > 0){

        // Toggle the buttons (changed to showing here)
        $(".changepic").show();

        // Set up the jcrop:
        jQuery(function($) {

          if(repeat == true) {
            jcrop_api.destroy();
          }
          repeat = true;

          $('#photo').Jcrop({
            bgColor: 'white',
            onRelease: releaseSelection,
            onSelect: setSelection,
            boxWidth: $("#photo").width() // Limit the width to the same as the current image being displayed
          },function(){

            jcrop_api = this;
            jcrop_api.disable();

            // Create a new filereader
            var fRead = new FileReader();

            // Get the first file
            fRead.readAsDataURL($("#file")[0].files[0]);

            //function to help us add a new image correctly
            addImageHelper();

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
      } else {//meaning else if files length isn't > 0
    }
  } else { // browser doesn't support filereader
  // Perform some processing before submitting

  preSubmit();
  // Submit without giving the user the opportunity to crop,
  // since it's not supported
  document.getElementById("theForm").submit();
}


});
});

// This function updates the coordinates of some form values,
// based off of a passed-in object that has the values of the
// crop values
function updateCoords(c) {
  // Ensure that we had a selection
  if (selection) {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#width').val(c.w);
    $('#height').val(c.h);
  }
}

// This function sets selection to false
function releaseSelection() {
  selection = false;
}

// This function sets selection to true
function setSelection() {
  selection = true;
}

// This function prompts for confirmation before 'deleting' an item (phone
// number, email address, etc).
// In this case, deleting means that it hides the element from the page, and
// clears out all the fields of the item.
// The backend php will perfrom the actual deletion
function deleteItem(type, id) {
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

// This function prompts for confirmation before removing an image (and replacing it with the public one from the giving site)
function deleteImage () {
  if (confirm("Are you sure you want to delete your profile image?\nNOTE: This will automatically be replaced by the photo on your public staff giving page, if you have one")) {

    //delete the api to destroy any images we didn't start with
    if (jcrop_api) {
      jcrop_api.destroy();
    }

    // First, set the hidden field's value, so that the backend-php will know
    $('#deleteImage').val(1);

    // Hide the related elements, without affecting the layout of the page
    $('#photo, .changepic').css("visibility", "hidden");

    $('.addOrSwitchpic').val("ADD IMAGE");
  }
}

//This function helps us add images correctly by setting the right things visible and adjusting the buttons
//to say change image rather than add image
function addImageHelper() {
  $('#deleteImage').val(0);
  $('#photo, .changepic').css("visibility", "visible");
  $('.addOrSwitchpic').val("CHANGE IMAGE");
}


//  This function is designed for IE 9 or lower, and provides a placeholder-like
//  experience for those browsers that don't actually support it
function insertPlaceholders() {
  // Foreach input element with a placeholder
  $("input[placeholder]").each(function() {
    // If we don't have a value in it already...
    if (!$(this).attr('value')) {
      // Set the css styling to be placeholder
      $(this).addClass("placeholder");
      // Set the value to the placeholder
      $(this).val($(this).attr("placeholder"));
    }
    // When it gets focus...
    $(this).focus(function() {
      // If this is a placeholder
      if ($(this).hasClass("placeholder")) {
        // Reset the value
        $(this).val($(this).attr('value'));
        // Remove the placeholder class
        $(this).removeClass("placeholder");
      }
    });
    // When it loses focus...
    $(this).blur(function() {
      // Set the value attribute to the current value
      $(this).attr('value', $(this).val());
      // If we don't have any value in it currently
      if (!$(this).val()) {
        // Reset it to the placeholder
        $(this).val($(this).attr("placeholder"));
        // Add css class
        $(this).addClass("placeholder");
      }
    });
  });
}

// A function to remove the placeholder values when submitting
function removePlaceholderValues() {
  // Iterate through each input that we modified earlier
  $("input[placeholder]").each(function() {
    // If it's currently supposed to be a placeholder
    if ($(this).hasClass("placeholder")) {
      // Set the value to nothing. This prevents the form from
      // submitting values that were supposed to just be placeholders
      $(this).val('');
    }
  });
}

// A function for processing before submission
function preSubmit() {

  //Check to make sure the photo cropped will be a usable aspect ratio.
  var jcrops = document.getElementsByClassName('jcrop-tracker');
  if((jcrops[0].clientHeight / jcrops[0].clientWidth) >= 2) {
    alert("The photo you have cropped is too narrow. Try cropping a slightly wider region.");
    return false;
  }

  // If we have the jcrop api
  if (jcrop_api) {

    // Update coordinates
    updateCoords(jcrop_api.tellSelect());
  }
  // If we don't have native placeholder support
  if (!placeholderSupport) {
    // Need to remove values
    removePlaceholderValues();
  }
  return true;
}
