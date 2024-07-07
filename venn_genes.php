<?php
	error_reporting(0);
	session_start();
	$degResult_1 = $_SESSION["degArray"][0];
	$degResult_2 = $_SESSION["degArray"][1];
	$degResult_3 = $_SESSION["degArray"][2];
    $degResult_4 = $_SESSION["degArray"][3];
	$degResult_5 = $_SESSION["degArray"][4];
	$degResult_6 = $_SESSION["degArray"][5];

	$degList1 = array();
	$degList2 = array();
	$degList3 = array();
	$degList4 = array();
	$degList5 = array();
	$degList6 = array();

	if($_GET['selectedValue'] == "Upregulated"){
		for($i=1; $i<=sizeof($degResult_1); $i++){
			$degR = $degResult_1[$i];
			if(preg_match("/Upregulated/i", end($degR))){
				$degList1[] = $degR[0];
			}
		}
		for($i=1; $i<sizeof($degResult_2); $i++){
			$degR = $degResult_2[$i];
			if(preg_match("/Upregulated/i", end($degR))){
				$degList2[] =  $degResult_2[$i][0];
			}
		}
		for($i=1; $i<=sizeof($degResult_3); $i++){
			$degR = $degResult_3[$i];
			if(preg_match("/Upregulated/i", end($degR))){
				$degList3[] =  $degResult_3[$i][0];
			}
		}
		for($i=1; $i<sizeof($degResult_4); $i++){
			$degR = $degResult_4[$i];
			if(preg_match("/Upregulated/i", end($degR))){
				$degList4[] =  $degResult_4[$i][0];
			}
		}
		for($i=1; $i<=sizeof($degResult_5); $i++){
			$degR = $degResult_5[$i];
			if(preg_match("/Upregulated/i", end($degR))){
				$degList5[] =  $degResult_5[$i][0];
			}
		}
		for($i=1; $i<sizeof($degResult_6); $i++){
			$degR = $degResult_6[$i];
			if(preg_match("/Upregulated/i", end($degR))){
				$degList6[] =  $degResult_6[$i][0];
			}
		}
	}elseif($_GET['selectedValue'] == "Downregulated"){
		for($i=1; $i<=sizeof($degResult_1); $i++){
			$degR = $degResult_1[$i];
			if(preg_match("/Downregulated/i", end($degR))){
				$degList1[] =  $degResult_1[$i][0];
			}
		}
		for($i=1; $i<sizeof($degResult_2); $i++){
			$degR = $degResult_2[$i];
			if(preg_match("/Downregulated/i", end($degR))){
				$degList2[] =  $degResult_2[$i][0];
			}
		}
		for($i=1; $i<=sizeof($degResult_3); $i++){
			$degR = $degResult_3[$i];
			if(preg_match("/Downregulated/i", end($degR))){
				$degList3[] =  $degResult_3[$i][0];
			}
		}
		for($i=1; $i<sizeof($degResult_4); $i++){
			$degR = $degResult_4[$i];
			if(preg_match("/Downregulated/i", end($degR))){
				$degList4[] =  $degResult_4[$i][0];
			}
		}
		for($i=1; $i<=sizeof($degResult_5); $i++){
			$degR = $degResult_5[$i];
			if(preg_match("/Downregulated/i", end($degR))){
				$degList5[] =  $degResult_5[$i][0];
			}
		}
		for($i=1; $i<sizeof($degResult_6); $i++){
			$degR = $degResult_6[$i];
			if(preg_match("/Downregulated/i", end($degR))){
				$degList6[] =  $degResult_6[$i][0];
			}
		}
	}elseif($_GET['selectedValue'] == "All"){
		for($i=1; $i<=sizeof($degResult_1); $i++){
			$degList1[] =  $degResult_1[$i][0];
		}
		for($i=1; $i<sizeof($degResult_2); $i++){
			$degList2[] =  $degResult_2[$i][0];
		}
		for($i=1; $i<=sizeof($degResult_3); $i++){
			$degList3[] =  $degResult_3[$i][0];
		}
		for($i=1; $i<sizeof($degResult_4); $i++){
			$degList4[] =  $degResult_4[$i][0];
		}
		for($i=1; $i<=sizeof($degResult_5); $i++){
			$degList5[] =  $degResult_5[$i][0];
		}
		for($i=1; $i<sizeof($degResult_6); $i++){
			$degList6[] =  $degResult_6[$i][0];
		}
	}

	$analTitle1 = $_SESSION["analTitle"][0];
	$analTitle2 = $_SESSION["analTitle"][1];
	$analTitle3 = $_SESSION["analTitle"][2];
	$analTitle4 = $_SESSION["analTitle"][3];
	$analTitle5 = $_SESSION["analTitle"][4];
	$analTitle6 = $_SESSION["analTitle"][5];
?>

<head>
	<link href="css/prettify.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css/bootstrap-colorpicker.min.css" rel="stylesheet">

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/bootstrap-colorpicker.min.js"></script>

	<script type="text/javascript" src="js/canvas2svg.js"></script>
	<script type="text/javascript" src="js/jvenn.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery.iframe-transport.js"></script>
	<script type="text/javascript" src="js/jquery.fileupload.js"></script>

	<script language="Javascript">
		$("#left").children().css('max-width', 'none');
		$(document).ready(function () {

			var colorDefault = ["#006600", "#5a9bd4", "#f15a60", "#cfcf1b", "#ff7500", "#c09853"],
				displayMode = "classic",
				displayStat = false,
				displaySwitch = false,
				shortNumber = false,
				fontSize = "12px",
				fontFamily = "Arial",
				uploadSeries = new Array();

			function updateJvenn() {
				if ($("#paste-tab").hasClass("active")) {
					var type = "pa",
						seriesTable = new Array(),
						num = 0;

					for (var i = 6; i >= 1; i--) {
						if ($("#area_pa_" + i).val() != "") {
							num = i;
							break;
						}
					}
					for (var i = 1; i <= num; i++) {
						seriesTable.push({
							name: $("#name_pa_" + i).val(),
							data: getArrayFromArea("area_pa_" + i)
						});
					}
				} else {
					var type = "up",
						seriesTable = uploadSeries;
					if (seriesTable.length > 0) {
						if ("A" in seriesTable[0].name) {
							seriesTable[0].name["A"] = $("#name_up_1").val();
						}
						if ("B" in seriesTable[0].name) {
							seriesTable[0].name["B"] = $("#name_up_2").val();
						}
						if ("C" in seriesTable[0].name) {
							seriesTable[0].name["C"] = $("#name_up_3").val();
						}
						if ("D" in seriesTable[0].name) {
							seriesTable[0].name["D"] = $("#name_up_4").val();
						}
						if ("E" in seriesTable[0].name) {
							seriesTable[0].name["E"] = $("#name_up_5").val();
						}
						if ("F" in seriesTable[0].name) {
							seriesTable[0].name["F"] = $("#name_up_6").val();
						}
					}
				}

				var colorsTable = new Array();
				colorsTable.push($('#name_' + type + '_1').css("color"));
				colorsTable.push($('#name_' + type + '_2').css("color"));
				colorsTable.push($('#name_' + type + '_3').css("color"));
				colorsTable.push($('#name_' + type + '_4').css("color"));
				colorsTable.push($('#name_' + type + '_5').css("color"));
				colorsTable.push($('#name_' + type + '_6').css("color"));

				$("#jvenn-container").jvenn({
					series: seriesTable,
					colors: colorsTable,
					fontSize: fontSize,
					fontFamily: fontFamily,
					searchInput: $("#search-field"),
					searchStatus: $("#search-status"),
					displayMode: displayMode,
					shortNumber: shortNumber,
					displayStat: displayStat,
					displaySwitch: displaySwitch,
					fnClickCallback: function () {
						var value = "";
						if (this.listnames.length == 1) {
							value += "Elements only in ";
						} else {
							value += "Common elements in ";
						}
						for (name in this.listnames) {
							value += this.listnames[name] + " ";
						}
						value += ":\n";
						for (val in this.list) {
							value += this.list[val] + "\n";
						}
						$("#names").val(value);
					}
				});
			}

			// update the view when any fields change
			$("[id^=name]").change(function () {
				updateJvenn();
			});
			$("[id^=area]").change(function () {
				updateJvenn();
			});

			$("#venn-type").change(function () {
				updateJvenn();
			});

			$("#qm_yes").click(function () {
				shortNumber = true;
				updateJvenn();
			});
			$("#qm_no").click(function () {
				shortNumber = false;
				updateJvenn();
			});

			$("#ds_yes").click(function () {
				displayStat = true;
				updateJvenn();
			});
			$("#ds_no").click(function () {
				displayStat = false;
				updateJvenn();
			});

			$("#dsw_yes").click(function () {
				displaySwitch = true;
				updateJvenn();
			});
			$("#dsw_no").click(function () {
				displaySwitch = false;
				updateJvenn();
			});

			$("#dm_classic").click(function () {
				displayMode = "classic";
				updateJvenn();
			});
			$("#dm_edwards").click(function () {
				displayMode = "edwards";
				updateJvenn();
			});
			$('[id^="ff"]').click(function () {
				fontFamily = $(this).html();
				updateJvenn();
			});
			$('[id^="fs"]').click(function () {
				fontSize = $(this).html();
				updateJvenn();
			});

			$('[id^="colorp"]').colorpicker().on('changeColor.colorpicker', function (event) {
				var type = $(this).attr("id").split("_")[1],
					index = $(this).attr("id").split("_")[2];
				$("#name_" + type + "_" + index).css("color", event.color.toHex());
				$("#name_" + type + "_" + index).css("border-color", event.color.toHex());
				if (type == "pa") {
					$("#area_" + type + "_" + index).css("color", event.color.toHex());
					$("#area_" + type + "_" + index).css("border-color", event.color.toHex());
				}
				updateJvenn();
			});

			$('[id^="colord"]').click(function () {
				var type = $(this).attr("id").split("_")[1],
					index = $(this).attr("id").split("_")[2];
				$("#name_" + type + "_" + index).css("color", colorDefault[index - 1]);
				$("#name_" + type + "_" + index).css("border-color", colorDefault[index - 1]);
				if (type == "pa") {
					$("#area_" + type + "_" + index).css("color", colorDefault[index - 1]);
					$("#area_" + type + "_" + index).css("border-color", colorDefault[index - 1]);
				}
				$("#colorp_" + type + "_" + index).colorpicker('setValue', colorDefault[index - 1]);
				updateJvenn();
			});

			$('[id^="clear"]').click(function () {
				var type = $(this).attr("id").split("_")[1],
					index = $(this).attr("id").split("_")[2];
				if (type == "pa") {
					$("#area_" + type + "_" + index).val("");
				}
				$("#name_" + type + "_" + index).val("List " + index);
				updateJvenn();
			});

			$('#clear-all').click(function () {
				location.replace("/venn_gene.php");
			});

			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				updateJvenn();
				$("#names").val("");
				$("#search-field").val("");
				$("#search-status").html("");
			});

			function switchListOn(index, switchon, value) {
				if (switchon) {
					$("#name_up_" + index).val(value);
					$("#name_up_" + index).prop("disabled", false);
					$("#colorp_up_" + index).colorpicker('enable');
					$("#colord_up_" + index).prop("disabled", false);
					$("#colorp_up_" + index).colorpicker('setValue', colorDefault[index - 1]);
				} else {
					$("#name_up_" + index).prop("disabled", true);
					$("#colorp_up_" + index).colorpicker('setValue', "lightgrey");
					$("#colorp_up_" + index).colorpicker('disable');
					$("#colord_up_" + index).prop("disabled", true);
				}
			}

			$("#upload_and_draw").click(function () {
				$('.progress-bar').removeClass("bar-danger");
				$('.bar').css('width', '0%');
				$('.sr-only').html('0%');
				$("#browse_upload").click();
			});

			$('#browse_upload').fileupload({
				dataType: 'json',
				add: function (e, data) {
					data.submit();
				},
				done: function (e, data) {
					var vennData = jQuery.extend(true, {}, data);
					if ("error" in data.result[0]) {
						$('.progress-bar').addClass("bar-danger");
						$('.sr-only').html("100% - " + data.result[0].error);
					} else {
						$('.sr-only').html("100% - Upload file: " + vennData.files[0].name);
						uploadSeries = new Array({
							name: data.result[0].name,
							data: data.result[0].data,
							values: data.result[0].values
						});
						if (vennData.result[0].name["A"]) {
							switchListOn(1, true, vennData.result[0].name["A"]);
						} else {
							switchListOn(1, false, "");
						}
						if (vennData.result[0].name["B"]) {
							switchListOn(2, true, vennData.result[0].name["B"]);
						} else {
							switchListOn(2, false, "");
						}
						if (vennData.result[0].name["C"]) {
							switchListOn(3, true, vennData.result[0].name["C"]);
						} else {
							switchListOn(3, false, "");
						}
						if (vennData.result[0].name["D"]) {
							switchListOn(4, true, vennData.result[0].name["D"]);
						} else {
							switchListOn(4, false, "");
						}
						if (vennData.result[0].name["E"]) {
							switchListOn(5, true, vennData.result[0].name["E"]);
						} else {
							switchListOn(5, false, "");
						}
						if (vennData.result[0].name["F"]) {
							switchListOn(6, true, vennData.result[0].name["F"]);
						} else {
							switchListOn(6, false, "");
						}
						updateJvenn();
					}
				},
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('.bar').css('width', progress + '%');
					$('.sr-only').html(progress + '%');
				}
			});
			$('#browse_upload').bind('fileuploadsubmit', function (e, data) {
				data.formData = {
					delimiter: $('[id^="sep_"].active').attr("name"),
					header: $('[id^="header_"].active').attr("name")
				};
			});

			function getArrayFromArea(areaID) {
				var lines = $("#" + areaID).val().split("\n");
				var table = new Array();
				for (var lindex in lines) {
					table.push(lines[lindex].trim());
				}
				return (table);
			}

			$('#colorp_pa_1').children("span").children("i").css("background-color", colorDefault[0]);
			$('#colorp_pa_2').children("span").children("i").css("background-color", colorDefault[1]);
			$('#colorp_pa_3').children("span").children("i").css("background-color", colorDefault[2]);
			$('#colorp_pa_4').children("span").children("i").css("background-color", colorDefault[3]);
			$('#colorp_pa_5').children("span").children("i").css("background-color", colorDefault[4]);
			$('#colorp_pa_6').children("span").children("i").css("background-color", colorDefault[5]);
			$('#colorp_up_1').children("span").children("i").css("background-color", colorDefault[0]);
			$('#colorp_up_2').children("span").children("i").css("background-color", colorDefault[1]);
			$('#colorp_up_3').children("span").children("i").css("background-color", colorDefault[2]);
			$('#colorp_up_4').children("span").children("i").css("background-color", colorDefault[3]);
			$('#colorp_up_5').children("span").children("i").css("background-color", colorDefault[4]);
			$('#colorp_up_6').children("span").children("i").css("background-color", colorDefault[5]);
			updateJvenn();

		});
	</script>
	<!-- <link rel="stylesheet" href="css/bootstrap_4.0.css"> -->
	<style type="text/css">
		body {
            background-color: #D6D6D6;
        }

		.lablClsVenn{
            font-size: 12px;
            font-weight: bold;
        }

		.vennTitle {
			text-align: center;
			background-color: rgb(241, 232, 209);
			padding: 8px;
			font-size: 16px;
			font-weight: bold;
		}

		/* add classes control-group.color */
		.control-group.color1 input,
		.control-group.color1 textarea {
			color: #006600;
			border-color: #006600;
		}

		.control-group.color1 input:focus,
		.control-group.color1 textarea:focus {
			border-color: #65a265;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #006600;
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #006600;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #006600;
		}

		.control-group.color2 input,
		.control-group.color2 textarea {
			color: #5a9bd4;
			border-color: #5a9bd4;
		}

		.control-group.color2 input:focus,
		.control-group.color2 textarea:focus {
			border-color: #5a9bd4;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #5a9bd4;
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #5a9bd4;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #5a9bd4;
		}

		.control-group.color3 input,
		.control-group.color3 textarea {
			color: #f15a60;
			border-color: #f15a60;
		}

		.control-group.color3 input:focus,
		.control-group.color3 textarea:focus {
			border-color: #f15a60;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #f15a60;
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #f15a60;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #f15a60;
		}

		.control-group.color4 input,
		.control-group.color4 textarea {
			color: #cfcf1b;
			border-color: #cfcf1b;
		}

		.control-group.color4 input:focus,
		.control-group.color4 textarea:focus {
			border-color: #cfcf1b;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #cfcf1b;
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #cfcf1b;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #cfcf1b;
		}

		.control-group.color5 input,
		.control-group.color5 textarea {
			color: #ff7500;
			border-color: #ff7500;
		}

		.control-group.color5 input:focus,
		.control-group.color5 textarea:focus {
			border-color: #ff7500;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ff7500;
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ff7500;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ff7500;
		}

		.control-group.color6 input,
		.control-group.color6 textarea {
			color: #c09853;
			border-color: #c09853;

		}

		.control-group.color6 input:focus,
		.control-group.color6 textarea:focus {
			border-color: #c09853;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c09853;
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c09853;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c09853;
		}

		.add-on {
			margin-left: -7px !important;
		}

		/* Venn conf pane css */
		.bs-docs-example {
			position: relative;
			margin: 0 0 25px 0;
			padding: 39px 19px 14px;
			*padding-top: 19px;
			background-color: #fff;
			border: 1px solid #ddd;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
		}

		/* Echo out a label for the example */
		.bs-docs-example:after {
			content: attr(content-value);
			/*"Venn global configuration";*/
			position: absolute;
			top: -1px;
			left: -1px;
			padding: 3px 7px;
			font-size: 12px;
			font-weight: bold;
			background-color: #f5f5f5;
			border: 1px solid #ddd;
			color: #9da0a4;
			-webkit-border-radius: 4px 0 4px 0;
			-moz-border-radius: 4px 0 4px 0;
			border-radius: 4px 0 4px 0;
		}

		.colorpicker {
			min-width: 100px;
		}
	</style>
	<link rel="stylesheet" href="css/VennBootStrap_4.0.css">

</head>

<body>
	<?php
		$name1 = $analTitle1;
		$name2 = $analTitle2;
		$name3 = $analTitle3;
		$name4 = $analTitle4;
		$name5 = $analTitle5;
		$name6 = $analTitle6;

		$p1 = implode("\n",$degList1);
		$p2 = implode("\n",$degList2);
		$p3 = implode("\n",$degList3);
		$p4 = implode("\n",$degList4);
		$p5 = implode("\n",$degList5); 
		$p6 = implode("\n",$degList6);
	?>
	<div class="container">
		<h3 align="center" class="vennTitle">Venn analysis: Genes (<?php echo $_GET['selectedValue']; ?>)<sup><a
					href="help.php#vennanalysis">
					<!-- <i style="font-size:12px;color:#1DA8BF" class="fa fa-question-circle"></i> --></a></sup>
		</h3>

		<!-- Main hero unit for a primary marketing message or call to action -->
		<p>&nbsp;</p>
		<div class="row-fluid" style="padding: 0px 10px;">
			<div class="span12">
				<div class="row-fluid">
					<div class="span6">
						<div class="row-fluid">
							<div id="jvenn-container"></div>
						</div>
						<div class="row-fluid">
							<div>
								<p> Click on a venn diagram figure to display the linked elements: </p>
								<textarea readonly id="names" style="width: 87%;" wrap="off" rows="10"></textarea>
							</div>
						</div>
					</div>

					<div class="span6">
						<div class="bs-docs-example" content-value="Search in Venn">
							Find an element in list(s):
							<input id="search-field" type="text" style="margin-top:8px"
								placeholder="enter an element name..." /><br /><br />
							<strong>NOTE:</strong>&nbsp;<label>Gene symbol to be searched must be in UPPERCASE.</label>
							<span id="search-status" class="label label-info"
								style="vertical-align: 16px; margin-left: -35px;"></span>
						</div>
					</div>

					<div class="span6">

						<ul class="nav nav-tabs" style="margin:0px">
							<li role="presentation" id="paste-tab" class="active"><a data-toggle="tab" href="#paste"></a>
							</li>
						</ul>

						<div class="tab-content clearfix"
							style="margin:0px;padding:20px 0 0 20px;border-left:1px solid #ddd;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-radius:0 0 4px 4px">
							<div class="tab-pane active" id="paste">

								<div class="row-fluid">
									<div class="span6">
										<div class="control-group color1">
											<input class="span6" id="name_pa_1" type="text" style="font-weight: bold;"
												value="<?php echo $name1; ?>" />
											<div id="colorp_pa_1"
												class="input-append colorpicker-component colorpicker-element">
												<input type="text" value="#006600" class="form-control"
													style="display:none" />
												<span class="add-on"><i></i></span>
											</div>
											<div class="btn-group" style="margin-left:15px">
												<button id="colord_pa_1" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-refresh"></i></button>
												<button id="clear_pa_1" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-trash"></i></button>
											</div>
											<div class="controls controls-row">
												<textarea class="span11" id="area_pa_1" wrap="off" rows="4"
													style="resize:none"><?php echo "$p1"; ?></textarea>
											</div>
										</div>
									</div>
									<div class="span6">
										<div class="control-group color2">
											<input class="span6" id="name_pa_2" type="text" style="font-weight: bold;"
												value="<?php echo $name2; ?>" />
											<div id="colorp_pa_2"
												class="input-append colorpicker-component colorpicker-element">
												<input type="text" value="#5a9bd4" class="form-control"
													style="display:none" />
												<span class="add-on"><i></i></span>
											</div>
											<div class="btn-group" style="margin-left:15px">
												<button id="colord_pa_2" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-refresh"></i></button>
												<button id="clear_pa_2" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-trash"></i></button>
											</div>
											<div class="controls controls-row">
												<textarea class="span11" id="area_pa_2" wrap="off" rows="4"
													style="resize:none"><?php echo "$p2"; ?></textarea>
											</div>
										</div>
									</div>
								</div>

								<div class="row-fluid">
									<div class="span6">
										<div class="control-group color3">
											<input class="span6" id="name_pa_3" type="text" style="font-weight: bold;"
												value="<?php echo $name3; ?>" />
											<div id="colorp_pa_3"
												class="input-append colorpicker-component colorpicker-element">
												<input type="text" value="#f15a60" class="form-control"
													style="display:none" />
												<span class="add-on"><i></i></span>
											</div>
											<div class="btn-group" style="margin-left:15px">
												<button id="colord_pa_3" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-refresh"></i></button>
												<button id="clear_pa_3" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-trash"></i></button>
											</div>
											<div class="controls controls-row">
												<textarea class="span11" id="area_pa_3" wrap="off" rows="4"
													style="resize:none"><?php echo "$p3"; ?></textarea>
											</div>
										</div>
									</div>
									<div class="span6">
										<div class="control-group color4">
											<input class="span6" id="name_pa_4" type="text" style="font-weight: bold;"
												value="<?php echo $name4; ?>" />
											<div id="colorp_pa_4"
												class="input-append colorpicker-component colorpicker-element">
												<input type="text" value="#cfcf1b" class="form-control"
													style="display:none" />
												<span class="add-on"><i></i></span>
											</div>
											<div class="btn-group" style="margin-left:15px">
												<button id="colord_pa_4" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-refresh"></i></button>
												<button id="clear_pa_4" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-trash"></i></button>
											</div>
											<div class="controls controls-row">
												<textarea class="span11" id="area_pa_4" wrap="off" rows="4"
													style="resize:none"><?php echo "$p4"; ?></textarea>
											</div>
										</div>
									</div>
								</div>

								<div class="row-fluid">
									<div class="span6">
										<div class="control-group color5">
											<input class="span6" id="name_pa_5" type="text" style="font-weight: bold;"
												value="<?php echo $name5; ?>" />
											<div id="colorp_pa_5"
												class="input-append colorpicker-component colorpicker-element">
												<input type="text" value="#ff7500" class="form-control"
													style="display:none" />
												<span class="add-on"><i></i></span>
											</div>
											<div class="btn-group" style="margin-left:15px">
												<button id="colord_pa_5" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-refresh"></i></button>
												<button id="clear_pa_5" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-trash"></i></button>
											</div>
											<div class="controls controls-row">
												<textarea class="span11" id="area_pa_5" wrap="off" rows="4"
													style="resize:none"><?php echo "$p5"; ?></textarea>
											</div>
										</div>
									</div>
									<div class="span6">
										<div class="control-group color6">
											<input class="span6" id="name_pa_6" type="text" style="font-weight: bold;"
												value="<?php echo $name6; ?>" />
											<div id="colorp_pa_6"
												class="input-append colorpicker-component colorpicker-element">
												<input type="text" value="#c09853" class="form-control"
													style="display:none" />
												<span class="add-on"><i></i></span>
											</div>
											<div class="btn-group" style="margin-left:15px">
												<button id="colord_pa_6" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-refresh"></i></button>
												<button id="clear_pa_6" style="margin-bottom:10px" class="btn btn-mini"><i
														class="icon-trash"></i></button>
											</div>
											<div class="controls controls-row">
												<textarea class="span11" id="area_pa_6" wrap="off" rows="4"
													style="resize:none"><?php echo "$p6"; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		! function ($) {
			$(function () {
				window.prettyPrint && prettyPrint()
			})
		}(window.jQuery)
	</script>
</body>

</html>