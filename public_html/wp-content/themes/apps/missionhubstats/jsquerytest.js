$("div.main").click(function() {
    $.ajax("missionhubpeople.php").done(fucntion(data) {
        $("div.pageContent").html=data;
    })
})