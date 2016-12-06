<!--<h1>Workflow</h1>-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/docsupport/prism.css">
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/workflow/chosen/chosen.css">

<?php
/*
*Loads a previous submission or creates a new entry for a workflow form.
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/

$obj = new Workflow();
echo $obj->configureWorkflow();


?>

<script type="text/javascript">
function submitFileAJAX(num) {
    var form = document.getElementById('workflowsubmission');
    var fileSelect = document.getElementById('file' + num);
    var files = fileSelect.files;
    var formData = new FormData();
    if (files[0].type.match('image.*') || files[0].type==='application/pdf' || 
        files[0].type==='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
        files[0].type==='application/vnd.ms-excel' ||
        files[0].type==='application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        files[0].type==='application/msword' || files[0].type==='text/plain') {
        formData.append('documents', files[0], files[0].name);
    } else {
        document.getElementById('file' + num + 'msg').innerHTML = 'Please upload one of the following file types: .jpg | .jpeg | .png | .gif | .doc | .docx | .xls | .xlsx | .pdf | .txt |' + files[0].type;
        return;
    }
    formData.append('action', 'workflow_upload_document');
    $.ajax({
        url: "<?php echo admin_url( 'admin-ajax.php' );?>",
        type: "POST",
        data: formData,
        processData: false,  // tell jQuery not to process the data
        contentType: false   // tell jQuery not to set contentType
    }).done(function( data ) {
        var obj = JSON.parse(data);
        if(obj.ReturnCode == '0' || obj.ReturnCode == '304') {
            document.getElementById('file' + num + 'msg').innerHTML = obj.Msg;
            document.getElementById('workflowfieldid' + num).value = obj.Upload;
        } else {
            document.getElementById('file' + num + 'msg').innerHTML = obj.Msg;
        }
    });
}
var config = {
  '.chosen-select'           : {},
  '.chosen-select-deselect'  : {allow_single_deselect:true},
  '.chosen-select-no-single' : {disable_search_threshold:10},
  '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
  '.chosen-select-width'     : {width:"95%"}
}
for (var selector in config) {
  $(selector).chosen(config[selector]);
}
</script>