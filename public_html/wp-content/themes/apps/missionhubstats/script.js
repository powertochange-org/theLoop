var defaultContent = "";

$(document).ready(function() {
    checkURL();
    $('ul li a').click(function(e) {
        checkURL(this.hash);
    });
    defaultContent = $('#pageContent').html();
    
    setInterval("checkURL()", 250);
});

var lasturl="";

function checkURL(hash)
{
    if(!hash) hash = window.location.hash;
    
    if(hash != lasturl) {
        lasturl = hash;
        loadPage(hash);
    }
}

function loadPage(url)
{
    //url = url.replace('#page', '');
    
    $.ajax({
        type: "POST",
        url: "load_page.php",
        data: 'report=' + url,
        dataType: "html",
        success: function(msg) {
            if(parseInt(msg) != 0) {
                $('#pageContent').html(msg);
            }
        }
    });
}