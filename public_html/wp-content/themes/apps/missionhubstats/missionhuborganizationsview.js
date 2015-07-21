(function($) {
    $(document).ready(function() {
        $("#submit").click(function() {
            var e = document.getElementById("orgname");
            var orgname = e.options[e.selectedIndex].value;
            handleSubmit(orgname);
            return false;
        }); 
    });
})(jQuery);

function handleSubmit(orgname) {
    $.post(
        WordPressAjaxOrgs.ajaxurl,
        {
            action: "create-engagement-report",
            orgname: orgname,
            nonce: WordPressAjaxOrgs.nonce
        },
        (function (response) {
            //if(!('error' in response) ) {
                console.log(response);
                $("#table").html(response);
            //}
            //else {
            //    console.log(response.error);
            //}
        })
    )
}