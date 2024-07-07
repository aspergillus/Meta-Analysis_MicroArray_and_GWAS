$(document).ready(function() {
    if(document.getElementById("rnkFile_Anal") != null){
        // When page loads...
        $(".tab_content").hide(); // Hide all content
        $('#degToggle').removeClass('active');  // remove class of DEG tab
        $('#degTab').removeClass('in active');  //remove class of DEG tab
        $("ul.tabs li:first").addClass("active").show(); // Activate first tab
        $('#gseaTab').addClass("in active");
        $(".tab_content:first").show(); // Show first tab content
    }else{
        // When page loads...
        $(".tab_content").hide(); // Hide all content
        $("ul.tabs li:first").addClass("active").show(); // Activate first tab
        $(".tab_content:first").show(); // Show first tab content
    }

    // // When page loads...
    // $(".tab_content").hide(); // Hide all content
    // $("ul.tabs li:first").addClass("active").show(); // Activate first tab
    // $(".tab_content:first").show(); // Show first tab content

    // On Click Event
    $("ul.tabs li").click(function() {
        $("ul.tabs li").removeClass("active"); // Remove any "active" class
        $(this).addClass("active"); // Add "active" class to selected tab
        $(".tab_content").hide(); // Hide all tab content
        var activeTab = $(this).find("a").attr("href"); // Find the href attribute value to identify the active tab + content
        $(activeTab).fadeIn(); // Fade in the active ID content
        return false;
    });
});

function tdModel(clickedID) {
    var btnGen = document.getElementById('btnModal');
    $(btnGen).attr('data-target', '#'+clickedID).click();
}

function pModel(clickedID) {
    var btnGen = document.getElementById('btnModal');
    $(btnGen).attr('data-target', '#'+clickedID).click();
}