(function($) {
    $(document).ready(function() {
        var e = document.getElementById("orgname");
        var orgname = e.options[e.selectedIndex].value;
        console.log(orgname);
    });
})(jQuery);