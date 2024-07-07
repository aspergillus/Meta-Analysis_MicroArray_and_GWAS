// $(function() {
//     $('#celDiv').hide(); 
//     $('#selButID').change(function(){
//         if($('#selButID').val() == 'cel') {
//             $('#celDiv').show(); 
//         } else {
//             $('#celDiv').hide(); 
//         } 
//     });
// });

// Upload Function
$(".OnlyForSpace").hide();
function leftAndRightUpload(){
    $("#leftGrp0").on("change", function () {
        var input = document.getElementById("leftGrp0");
        var output = document.getElementById("showleftGrp0");
        $(output).show().empty();
        for (var i = 0; i < input.files.length; i++) {
            output.innerHTML += '<li>' + input.files.item(i).name + '</li>';
        }
    })
    
    $("#rightGrp0").on("change", function () {
        var input = document.getElementById("rightGrp0");
        var output = document.getElementById("showRightGrp0");
        $(output).show().empty();
        for (var i = 0; i < input.files.length; i++) {
            output.innerHTML += '<li>' + input.files.item(i).name + '</li>';
        }
    })
}
leftAndRightUpload();

// Show processing time and logo
function showProcessing_Time(){
    $(".frstTR").hide();
    $(".OnlyForSpace").show();
    $("#mnTbl").append(`<tr align="center">
            <th colspan="2" class="tleJob" style="font-size:14px;">Job is Running</th>
        </tr>
        <tr>
            <td height="35" colspan="2"></td>
        </tr>
        <tr>
            <td style="width: 40%; font-size:12px;">Time since submission: </td>
            <td style="width: 48%;">
                <div id="timer" style="padding-left: 15px;"></div>
            </td>
        </tr>
        <tr style="text-align: center;">
            <td colspan="2">
                <div class="lds-ellipsis">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </td>
        </tr>`).append(`<p>&nbsp;</p>`);
    var timerVar = setInterval(countTimer, 1000);
    var totalSeconds = 0;

    function countTimer() {
        ++totalSeconds;
        var hour = Math.floor(totalSeconds / 3600);
        var minute = Math.floor((totalSeconds - hour * 3600) / 60);
        var seconds = totalSeconds - (hour * 3600 + minute * 60);
        if (hour < 10)
            hour = "0" + hour;
        if (minute < 10)
            minute = "0" + minute;
        if (seconds < 10)
            seconds = "0" + seconds;
        document.getElementById("timer").innerHTML = hour + ":" + minute + ":" + seconds;
    }
    countTimer();
    $('#myform').submit();
}

// // platForm selection
// $("#techno_sel").on('input', function () {
//     var val = this.value;
//     if($('#techno option').filter(function(){
//         return this.value.toUpperCase() === val.toUpperCase();        
//     }).length) {
//         $('#techno_Hidden').attr('value', val);
//         $.ajax({
//             type: "post",
//             url: "miArrayindex_Copy.php",
//             data: {
//                 'technology': val
//             },
//             cache: false,
//             success: function (html) {
//                 jQuery('#headDiv_platForm').fadeOut(100, function () {
//                     jQuery(this).html($(html).find('#midDiv_platForm'));
//                     jQuery(this).append('<script src="miArrayScript.js"></script>');
//                 }).fadeIn(1000);
//             }
//         });
//         return false;
//     }
// });

// if(document.getElementById('companyOwner_Hidden').value == ""){
//     $('#platForm_Title').hide();
// }

// $("#Company_sel").on('input', function () {
//     var compVal = this.value;
//     if($('#compOwner option').filter(function(){
//         return this.value.toUpperCase() === compVal.toUpperCase();        
//     }).length) {
//         $('#companyOwner_Hidden').attr('value', compVal);
//         tech = $('#techno_Hidden').val();
//         $.ajax({
//             type: "post",
//             url: "miArrayindex_Copy.php",
//             data: {
//                 'technology': tech,
//                 'companyOwner': compVal
//             },
//             cache: false,
//             success: function (html) {
//                 jQuery('#headDiv_platForm').fadeOut(100, function () {
//                     jQuery(this).html($(html).find('#midDiv_platForm'));
//                     jQuery(this).append('<script src="miArrayScript.js"></script>');
//                 }).fadeIn(1000);
//             }
//         });
//         return false;
//     }
// });

///////////   add the main div for meta-analysis  //////////////
$("#removBut").hide();
var cloneCount = 0;
$("#dupliBut").on('click', function (e) {
    if($(".mainUpldDIV").children().children().next().children(".flexDiv").children().find('ul > li').length > 0){

        // $('#duplicetor' + cloneCount).prev().hide();

        if($('#mainUpldDIV .upldANDdrag').attr("id") == ""){
            $("#removBut").hide();
        }else{
            $("#removBut").show();
        }

        var incmnt = ++cloneCount;

        // clone the div and change the ID of the div
        var icnrID = "duplicetor" + incmnt;
        $('#duplicetor0').clone().removeAttr("id").prop("id", icnrID).appendTo("#mainUpldDIV");

        // prevDiv = document.getElementById("duplicetor" + (incmnt - 1));
        // prevDiv.style.display = "none";

        // console.log("something i dont know");
        // $("#" + icnrID).prev().css("display", "none");

        // $("#" + icnrID).prev().hide();
        // alert($("#" + icnrID).prev().attr('id'));
        // console.log($('#mainUpldDIV'));
        // console.log($("#" + icnrID).prev().attr('id'));
        // console.log($('#mainUpldDIV').children().last().prev().attr('id'));

        tleAlyID = "titleAlyID" + incmnt;
        tleAlyName = "titleAlyName" + incmnt;
        $("#" + icnrID).children(".tleAnl").children().children().next().prop({
            "id": tleAlyID,
            "name": tleAlyName
        });

        // whole Left Group Div
        chgrpfrstName = "frstGrpName" + incmnt;
        chGrpfstID = "frstGrpID" + incmnt;
        chShowleftGrp = "showleftGrp" + incmnt;
        chLeftGrp = "leftGrp" + incmnt;
        chLeftInputNm = "files" + incmnt + "[]";
        $("#" + icnrID).children().next().children(".flexDiv").children().children(".leftGrp").children().next()
            .prop({
                "id": chGrpfstID,
                "name": chgrpfrstName
            }).parent().next().prop("id", chShowleftGrp).next().prop({
                "id": chLeftGrp,
                "name": chLeftInputNm
            });

        // whole Right Group Div
        chgrpSecName = "SecGrpName" + incmnt;
        chGrpSecID = "SecGrpID" + incmnt;
        chShowRightGrp = "showRightGrp" + incmnt;
        chRightGrp = "rightGrp" + incmnt;
        chRightInputNm = "files" + incmnt + "[]";
        $("#" + icnrID).children().next().children(".flexDiv").children().next().next(".rightGrpS").children().children()
            .next().prop({
                "id": chGrpSecID,
                "name": chgrpSecName
            }).parent().next().prop("id", chShowRightGrp).next().prop({
                "id": chRightGrp,
                "name": chRightInputNm
            });

        // Adding padding in the right side Div
        // $(".rightGrpS").css('padding-bottom', '50px')

        // increment the control and treatment by adding number after control and treatment in name and also in the ID
        $("#" + icnrID).children().eq(-2).attr("name", "control" + incmnt).prop("id", "controlbox" + incmnt);
        $("#" + icnrID).children().last().attr("name", "treatment" + incmnt).prop("id", "treatmentbox" + incmnt);

        // Empty the all UL and also input[type = file]
        $("#" + tleAlyID).val('');
        $("#" + chGrpfstID).val('');
        $("#" + chShowleftGrp).empty();
        $("#" + chLeftGrp).val('');
        $("#" + chGrpSecID).val('');
        $("#" + chShowRightGrp).empty();
        $("#" + chRightGrp).val('');

        if (cloneCount == 9) {
            $("#dupliBut").hide();
        }

        // show the file which are uploaded in the form of list
        $("#" + chLeftGrp).on("change", function () {
            var input = document.getElementById(chLeftGrp);
            var output = document.getElementById(chShowleftGrp);
            $(output).show().empty();
            for (var i = 0; i < input.files.length; i++) {
                output.innerHTML += '<li>' + input.files.item(i).name + '</li>';
            }
        })
        
        $("#" + chRightGrp).on("change", function () {
            var input = document.getElementById(chRightGrp);
            var output = document.getElementById(chShowRightGrp);
            $(output).show().empty();
            for (var i = 0; i < input.files.length; i++) {
                output.innerHTML += '<li>' + input.files.item(i).name + '</li>';
            }
        })

        // $("#duplicetor" + (incmnt - 1)).prev().hide();

    } else {
        alert("Please upload files");  
    }
});

// remove the last div which is clone and also clonecount
$("#removBut").click(function() {
    if(cloneCount < 10){
        // $('.mainUpldDIV').children().last().show();

        $("#dupliBut").show();
        $("#mainUpldDIV .upldANDdrag").last().remove();
        --cloneCount;
        if($('#mainUpldDIV .upldANDdrag').last().attr("id") == "duplicetor0"){
            $("#removBut").hide();
        }else{
            $("#removBut").show();
        } 
    }
    else{
        $("#mainUpldDIV .upldANDdrag").last().remove();
        $("#dupliBut").show();
        --cloneCount;
    }
});

// Submit CEL file
$('#cel_Submit').click(function () {
    var divID = $('#mainUpldDIV').children().last().attr("id");
    var divCount = divID.replace("duplicetor", "");
    $("#cel_AnalCountBox").val(divCount);

    for(var k=0; k<=divCount; k++){
        var cntlLst = [];
        var trntmtLst = [];
        
        var study_Title = $(`#titleAlyID${k}`).val();
        if(study_Title != ""){
            var lst_1 = document.querySelectorAll("#showleftGrp" + k +" li");
            var first_grpName = $(`#frstGrpID${k}`).val();
            if(lst_1.length == 0){
                alert(`Please upload file for the ${study_Title} in group 1`);
                return false;
            }else if(first_grpName == ""){
                alert(`Please Enter name for the ${study_Title} in group 1`);
            }else{
                for (var i = 0; i < lst_1.length; i++) {
                    cntlLst.push(lst_1[i].innerHTML);
                }
            }

            var lst_2 = document.querySelectorAll("#showRightGrp" + k +" li");
            var second_grpName = $(`#SecGrpID${k}`).val();
            if(lst_2.length == 0){
                alert(`Please upload file for the study ${study_Title} in group 2`);
                return false;
            }else if(second_grpName == ""){
                alert(`Please Enter name for the ${study_Title} in group 2`);
                return false;
            }else{
                for (var i = 0; i < lst_2.length; i++) {
                    trntmtLst.push(lst_2[i].innerHTML);
                }
            }

            $(`#controlbox${k}`).val(cntlLst);
            $(`#treatmentbox${k}`).val(trntmtLst);

            if(divCount == k){
                // if($(`#techno_sel`).val() == ""){
                //     alert("Select Plat form");
                //     return false;
                // }else if($(`#Company_sel`).val() == ""){
                //     alert("Select Company/Owner");
                //     return false;
                // }else{
                    showProcessing_Time();
                // }
            }
        }else{
            alert(`Please Enter the Title of the Study`);
            return false;
        }
    }
});

// //////////////////////////  DEG List //////////////////////////////////

// var degList_cloneCount = 0;
// $("#dupliDEGlist").on('click', function (e) {
//     var incmnt = ++degList_cloneCount;

//     // clone the div and change the ID of the div
//     var icnrID = "degLstDuplicetor" + incmnt;
//     $('#degLstDuplicetor0').clone().removeAttr("id").prop("id", icnrID).appendTo("#degLstMainDiv");

//     tleAlyID = "degListTitleID" + incmnt;
//     tleAlyName = "degListName" + incmnt;
//     $("#" + icnrID).children(".tleAnl").children().children().next().prop({
//         "id": tleAlyID,
//         "name": tleAlyName
//     });

//     chLeftGrp = "degLstUpldID" + incmnt;
//     chLeftInputNm = "degList" + incmnt;
//     $("#" + icnrID).children(".tleAnl").next().children().children().prop({
//         "id": chLeftGrp,
//         "name": chLeftInputNm
//     });

//     if (degList_cloneCount == 9) {
//         $("#dupliDEGlist").hide();
//     }

//     // Empty the all UL and also input[type = file]
//     $("#degListTitleID" + incmnt).val('');
//     $("#degLstUpldID" + incmnt).val('');
// });

// // remove the last div which is clone and also clonecount
// $("#removDEGlist").click(function() {
//     if(degList_cloneCount < 10){
//         // $('.mainUpldDIV').children().last().show();

//         $("#dupliDEGlist").show();
//         $("#degLstMainDiv .upldANDdrag").last().remove();
//         --degList_cloneCount;
//         if($('#degLstMainDiv .upldANDdrag').last().attr("id") == "degLstDuplicetor0"){
//             $("#removDEGlist").hide();
//         }else{
//             $("#removDEGlist").show();
//         } 
//     }
//     else{
//         $("#degLstMainDiv .upldANDdrag").last().remove();
//         $("#dupliDEGlist").show();
//         --degList_cloneCount;
//     }
// });

// $('#degListSubmit').click(function () {
//     var divID = $('#degLstMainDiv').children().last().attr("id");
//     var divCount = divID.replace("degLstDuplicetor", "");
//     $("#degListAnalCount").val(divCount);
// });

// //////////////////////////  .rnk File which will do GSEA and its components //////////////////////////////////

// var rnk_cloneCount = 0;
// $("#dupliRnk").on('click', function (e) {
//     var incmnt = ++rnk_cloneCount;

//     // clone the div and change the ID of the div
//     var icnrID = "rnkDuplicetor" + incmnt;
//     $('#rnkDuplicetor0').clone().removeAttr("id").prop("id", icnrID).appendTo("#rnkMainDiv");

//     tleAlyID = "rnkTitleID" + incmnt;
//     tleAlyName = "rnkName" + incmnt;
//     $("#" + icnrID).children(".tleAnl").children().children().next().prop({
//         "id": tleAlyID,
//         "name": tleAlyName
//     });

//     chLeftGrp = "rnkUpldID" + incmnt;
//     chLeftInputNm = "rnkFle" + incmnt;
//     $("#" + icnrID).children(".tleAnl").next().children().children().prop({
//         "id": chLeftGrp,
//         "name": chLeftInputNm
//     });

//     if (rnk_cloneCount == 9) {
//         $("#dupliRnk").hide();
//     }

//     // Empty the all UL and also input[type = file]
//     $("#rnkTitleID" + incmnt).val('');
//     $("#rnkUpldID" + incmnt).val('');
// });

// // remove the last div which is clone and also clonecount
// $("#removRnk").click(function() {
//     if (rnk_cloneCount < 10) {
//         // $('.mainUpldDIV').children().last().show();

//         $("#dupliRnk").show();
//         $("#rnkMainDiv .upldANDdrag").last().remove();
//         --rnk_cloneCount;
//         if ($('#rnkMainDiv .upldANDdrag').last().attr("id") == "rnkDuplicetor0") {
//             $("#removRnk").hide();
//         } else {
//             $("#removRnk").show();
//         }
//     } else {
//         $("#rnkMainDiv .upldANDdrag").last().remove();
//         $("#dupliRnk").show();
//         --rnk_cloneCount;
//     }
// });

// $('#rnkSubmit').click(function () {
//     var divID = $('#rnkMainDiv').children().last().attr("id");
//     var divCount = divID.replace("rnkDuplicetor", "");
//     $("#rnkAnalCount").val(divCount);
// });