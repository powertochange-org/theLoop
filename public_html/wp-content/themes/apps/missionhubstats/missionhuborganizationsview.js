(function($) {
    $(document).ready(function() {
        $("#daterange").slideUp();
        $("#organizations").slideUp();
        $("#report").change(function() {
            $("#report").each(function() {
                if($(this).val() =="pat") {
                    $("#daterange").slideDown();
                    $("#organizations").slideUp();
                } else if ($(this).val() =="engagement" || $(this).val() =="discipleship") {
                    $("#daterange").slideUp();
                    $("#organizations").slideDown();
                }
            })
        })
        
        
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