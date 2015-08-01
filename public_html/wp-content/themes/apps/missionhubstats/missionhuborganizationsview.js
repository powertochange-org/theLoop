(function($) {
    $(document).ready(function() {
        $("#submit").click(function() {
            var org = document.getElementById("orgname");
            var orgname = org.options[org.selectedIndex].value;
            var report = document.getElementById("report");
            var reportname = report.options[report.selectedIndex].value;
            handleSubmit(orgname, reportname);
            return false;
        }); 
    });
})(jQuery);

function handleSubmit(orgname, report) {
    $.post(
        WordPressAjaxOrgs.ajaxurl,
        {
            action: "handle-submit",
            report: report,
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