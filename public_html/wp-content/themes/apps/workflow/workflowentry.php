<!--<h1>Workflow</h1>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
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