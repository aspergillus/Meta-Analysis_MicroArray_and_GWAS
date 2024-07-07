<?php
    // error_reporting(0);
    session_start();

    if(isset($_POST["dataset"])){
        $dataset = $_POST["dataset"];
    }else{
        $dataset="disease";
    }

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params=session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]);
    }

    // Finally, destroy the session.
    session_destroy();
    include("connect.php");
?>

<?php
    include('header.php');
?>

<script src="js/jquery_3.2.1.js"></script>
<script src="js/select2_min.js"></script>
<link rel="stylesheet" href="css/select2_min.css">

<script>
    function showLoading() {
        document.getElementById('loadingmsg').style.display = 'block';
        document.getElementById('loadingover').style.display = 'block';
    }
</script>
<style type="text/css">
    #loadingmsg {
        color: black;
        background: #fff;
        padding: 10px;
        position: fixed;
        top: 50%;
        left: 46%;
        z-index: 100;
        margin-right: -25%;
        margin-bottom: -25%;
    }

    #loadingover {
        background: black;
        z-index: 99;
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
        filter: alpha(opacity=80);
        -moz-opacity: 0.8;
        -khtml-opacity: 0.8;
        opacity: 0.8;
    }

    #sec_rem, #sec{
        width: 500px;
        overflow: auto;
    }

    .showBorder{
        border: 1px solid black;
    }

    .analType{
        padding: 7px;
        border-radius: 4px;
    }

    .allBtn{
        border-radius: 4px;
        padding: 6px 10px;
    }

    .select2-container .select2-search--inline .select2-search__field{
        margin-top: 7px;
        margin-left: 8px;
    }

    /* .select2-container--default .select2-results>.select2-results__options {
        max-height: 258px;
    } */
    
</style>
<SCRIPT>
		function listbox_move(listID, direction) {
 
			var listbox = document.getElementById(listID);
			var selIndex = listbox.selectedIndex;
 
			if(-1 == selIndex) {
				alert("Please select an option to move.");
				return;
			}
 
			var increment = -1;
			if(direction == 'up')
				increment = -1;
			else
				increment = 1;
 
			if((selIndex + increment) < 0 ||
				(selIndex + increment) > (listbox.options.length-1)) {
				return;
			}
 
			var selValue = listbox.options[selIndex].value;
			var selText = listbox.options[selIndex].text;
			listbox.options[selIndex].value = listbox.options[selIndex + increment].value
			listbox.options[selIndex].text = listbox.options[selIndex + increment].text
 
			listbox.options[selIndex + increment].value = selValue;
			listbox.options[selIndex + increment].text = selText;
 
			listbox.selectedIndex = selIndex + increment;
		}
 
		function listbox_moveacross(sourceID, destID) {
			var src = document.getElementById(sourceID);
			var dest = document.getElementById(destID);
 
			for(var count=0; count < src.options.length; count++) {
 
				if(src.options[count].selected == true) {
						var option = src.options[count];
 
						var newOption = document.createElement("option");
						newOption.value = option.value;
						newOption.text = option.text;
						newOption.selected = true;
						try {
								 dest.add(newOption, null); //Standard
								 src.remove(count, null);
						 }catch(error) {
								 dest.add(newOption); // IE only
								 src.remove(count);
						 }
						count--;
 
				}
 
			}
 
		}
		function listbox_selectall(listID, isSelect) {
 
			var listbox = document.getElementById(listID);
			for(var count=0; count < listbox.options.length; count++) {
 
				listbox.options[count].selected = isSelect;
 
			}
		}

Selectbox.selectAllOptions = function(obj) {
  if (!this.hasOptions(obj)) { return false; }
  for (var i=0; i<obj.options.length; i++) {
    obj.options[i].selected = true;
  }
  return true;
};

function selectAllOptions(selStr)
{
  var selObj = document.getElementById(selStr);
  for (var i=0; i<selObj.options.length; i++) {
    selObj.options[i].selected = true;
  }
}

function listbox_move(listID, direction) 
{   
var listbox = document.getElementById(listID);    
var selIndex = listbox.selectedIndex;    
if(-1 == selIndex) {       
alert("Please select an option to move.");   
return; 
} 
if(selIndex > 19) {       
alert("Select only 20 diseases");   
return; 
} 
var increment = -1; 
if(direction == 'up') 
increment = -1; 
else  
increment = 1; 
if((selIndex + increment) < 0 || 
(selIndex + increment) > (listbox.options.length-1)) { 
return; 
} 
var selValue = listbox.options[selIndex].value; 
var selText = listbox.options[selIndex].text; 
listbox.options[selIndex].value = listbox.options[selIndex + increment].value 
listbox.options[selIndex].text = listbox.options[selIndex + increment].text 
listbox.options[selIndex + increment].value = selValue; 
listbox.options[selIndex + increment].text = selText; 
listbox.selectedIndex = selIndex + increment; 
}
// <!-- Select all checkbox -->

function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }

</SCRIPT>

<script>


function echeck(str) {

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    
		    return false
		 }

 		 return true					
	}

function validateEmail(email)
{
    var splitted = email.match("^(.+)@(.+)$");
    if (splitted == null) return false;
    if (splitted[1] != null)
    {
        var regexp_user = /^\"?[\w-_\.]*\"?$/;
        if (splitted[1].match(regexp_user) == null) return false;
    }
    if (splitted[2] != null)
    {
        var regexp_domain = /^[\w-\.]*\.[A-Za-z]{2,4}$/;
        if (splitted[2].match(regexp_domain) == null)
        {
            var regexp_ip = /^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
            if (splitted[2].match(regexp_ip) == null) return false;
        } // if
        return true;
    }
    return false;
}
function showLoading() {
    document.getElementById('loadingmsg').style.display = 'block';
    document.getElementById('loadingover').style.display = 'block';
}

function validate(form) {

var e = form.elements, m = '';

var alphaExp = /^[0-9a-zA-Z\s]+$/;
var sps = /^[\s]+$/;

  if(!e['sec'].value) {m += '- Please add Disease/s.\n';}
  if(!e['merge_select'].value) {m += '- Please provide merge selected disease/trait.\n';}
  if(m) {
    alert('The following error(s) occurred:\n\n' + m);
    return false;
  }
  else
	  {
		showLoading();
	  }
  
}


/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */



</script>

<style type="text/css">
      #loadingmsg {
      color: black;
      background: #fff; 
      padding: 10px;
      position: fixed;
      top: 50%;
      left: 46%;
      z-index: 100;
      margin-right: -25%;
      margin-bottom: -25%;
      }
      #loadingover {
      background: black;
      z-index: 99;
      width: 100%;
      height: 100%;
      position: fixed;
      top: 0;
      left: 0;
      -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
      filter: alpha(opacity=80);
      -moz-opacity: 0.8;
      -khtml-opacity: 0.8;
      opacity: 0.8;
    }
</style>
<script>
    function listbox_moveacross(sourceID, destID) {
        var src = document.getElementById(sourceID);
        var dest = document.getElementById(destID);

        for (var count = 0; count < src.options.length; count++) {

            if (src.options[count].selected == true) {
                var option = src.options[count];

                var newOption = document.createElement("option");
                newOption.value = option.value;
                newOption.text = option.text;
                newOption.selected = true;
                try {
                    dest.add(newOption, null); //Standard
                    src.remove(count, null);
                } catch (error) {
                    dest.add(newOption); // IE only
                    src.remove(count);
                }
                count--;

            }

        }

    }

    function listbox_selectall(listID, isSelect) {

        var listbox = document.getElementById(listID);
        for (var count = 0; count < listbox.options.length; count++) {

            listbox.options[count].selected = isSelect;

        }
    }

    function selectAllOptions(selStr) {
        var selObj = document.getElementById(selStr);
        for (var i = 0; i < selObj.options.length; i++) {
            selObj.options[i].selected = true;
        }
    }

    function listbox_move(listID, direction) {
        var listbox = document.getElementById(listID);
        var selIndex = listbox.selectedIndex;
        if (-1 == selIndex) {
            alert("Please select an option to move.");
            return;
        }
        if (selIndex > 19) {
            alert("Select only 20 diseases");
            return;
        }
        var increment = -1;
        if (direction == 'up')
            increment = -1;
        else
            increment = 1;
        if ((selIndex + increment) < 0 ||
            (selIndex + increment) > (listbox.options.length - 1)) {
            return;
        }
        var selValue = listbox.options[selIndex].value;
        var selText = listbox.options[selIndex].text;
        listbox.options[selIndex].value = listbox.options[selIndex + increment].value
        listbox.options[selIndex].text = listbox.options[selIndex + increment].text
        listbox.options[selIndex + increment].value = selValue;
        listbox.options[selIndex + increment].text = selText;
        listbox.selectedIndex = selIndex + increment;
    }

    // Select all checkbox
    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }
</script>
<script LANGUAGE="JavaScript">
function ValidateForma(form){
ErrorText= "";
if ( ( form.com[0].checked == false ) && ( form.com[1].checked == false )  && ( form.com[2].checked == false ) && ( form.com[3].checked == false ))
{
alert ( "Please select method");
return false;
}
if (ErrorText= "") { form.submit() }
}
</script>



<body onLoad="initListGroup('disease', document.forms[0].parent, document.forms[0].disease_term, 'cs'); initValidation()" topmargin="0" leftmargin="0" >
<h2 align="center">GWAS analysis</h2>

<!-- Radio based selection -->
<form action="gwas.php" method="post" enctype="application/x-www-form-urlencoded" onSubmit="selectAllOptions('sec');return validate(this);">
    <p>&nbsp;</p>
    <p>
      <input type="radio" name="dataset" onclick="javascript: submit()" value="disease" <?php if ($dataset=="disease")
	  {
		  ?>checked="checked" <?php } ?>>
      <strong>Disease/Trait
<input type="radio" name="dataset" onclick="javascript: submit()" value="efo" <?php if ($dataset=="efo")
	  {
		  ?>checked="checked" <?php } ?>> 
      EFO Term
      
      </strong></p>
  </form>
<script>
    $('.sel_dat').on('change', function () {
        $(this).closest("form").submit();
    });
</script>

<!-- Result form after selection -->
<form action="result.php" method="post" name="myForm" id="gwasQuery"onSubmit="selectAllOptions('sec');return validate(this);">
    <table width="100%" border="0" align="center" cellpadding="6" cellspacing="0">
        <tr>
            <td width="40%" valign="top">
                <p>
                    <strong>
                        <?php if ($dataset=="disease") {
                            ?>Disease/Trait <?php
                        }

                        if ($dataset=="efo") {
                            ?>EFO Term <?php
                        }
                        ?>
                    </strong>
                </p>
                <strong>
                    <select id="sec_rem" name="disList" size="20" multiple />
                    <!-- <select id="sec_rem" name="gene_list" multiple="multiple" size="20"> -->
                        <?php 
                            if ($dataset=="disease") {
                                $res=mysqli_query($conn, "select DISTINCT diseases FROM gwas_cc_input ORDER BY diseases");
                                while($row=mysqli_fetch_array($res)) {
                                    $gene_name = $row["diseases"];

                                    if($gene_name=="") {}

                                    else {
                                        ?><option value="<?php echo $gene_name; ?>"><?php echo $gene_name;
                                        ?></option><?php
                                    }
                                }
                            }

                            if ($dataset=="efo") {
                                $res=mysqli_query($conn, "select DISTINCT efo_term FROM gwas_cc_input ORDER BY efo_term");

                                while($row=mysqli_fetch_array($res)) {
                                    $gene_name = $row["efo_term"];
                                    ?><option value="<?php echo $gene_name; ?>"><?php echo $gene_name;
                                    ?></option><?php
                                }
                            }
                        ?>
                    </select>
                </strong>
            </td>
            <td width="20%">
                <table>
                    <tr>
                    <!-- width="38%" height="75" scope="row" -->
                        <th width="45%">
                            <a href="#" onClick="listbox_moveacross('sec', 'sec_rem')">
                                <img src="img/add.jpg" alt="Add" width="25" height="25" />
                            </a>
                        </th>
                        <td width="20%">&nbsp;</td>
                        <td width="40%">
                            <a href="#" onClick="listbox_moveacross('sec_rem', 'sec')">
                                <img src="img/re.jpg" alt="Remove" width="25" height="25" />
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%" valign="top">
                <p>
                    <strong>
                        <?php 
                        if ($dataset=="disease") {
                            ?>Selected Disease/Trait<?php
                        }

                        if ($dataset=="efo") {
                            ?>Selected EFO Term <?php
                        }
                        ?>
                    </strong>
                </p>
                <select id="sec" multiple="multiple" size="20" style="min-width: 200px;float:left;" name="dis_grr[]"></select>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div align="center">
                    <div id='loadingmsg' style='display: none;'>
                        <div align="center">Processing, please wait......</div>
                    </div>

                    <?php //echo $dataset; ?>
                    <div id='loadingover' style='display: none;'></div>
                    <input name="dataset" type="hidden" value="<?php echo $dataset; ?>" />

                    <div align="center">
                        <p><strong>Merge selected disease/trait 
                          <input type="text" name="merge_select" value="">
                        </strong></p>
                    </div>

                    <div align="center">&nbsp;<input class="allBtn" type="submit" name="button" id="btn_result" value="Submit" onsubmit='return validate();'  onClick=" return ValidateForma(this.form)" />&nbsp;&nbsp;
                        <input class="allBtn" type="button" value="Reset" onClick="resetListGroup('disease')" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
</form>

<script>
    $('#sec_rem').select2();
    // $('#sec').select2();

    // $('#select_TissID').select2();
    // $('#addAll').click(function () {
    //     $("#select_TissID > option:not(:first-child)").prop("selected", true);
    //     $("#select_TissID").trigger("change");
    // });

    // $('#resetAll').click(function () {
    //     $("#select_TissID").val(0).trigger('change.select2')
    // });
</script>

<script>
    // document.getElementById('anaTy').addEventListener('change', function () {
    //     if(this.value == "qtl_an"){
    //         $('#qtlSelect').show();
    //     }else{
    //         $('#qtlSelect').hide();
    //     }
    // });

    // $('#btn_result').on('click', function(){
    //     if (document.getElementById('sec').length > 0) {
    //         if ($('#c_snp').is(':checked') || $('#nc_snp').is(':checked')){
    //             if($('#anaTy').val() == null){
    //                 alert('Select the type of analysis');
    //                 return false;
    //             }else{
    //                 var selID = document.getElementById("anaTy");
    //                 var selText = selID.options[selID.selectedIndex].value;
    //                 if(selText == "qtl_an"){
    //                     if($('#select_TissID').val().length == 0){
    //                         alert('Select the Tissue');
    //                         return false;
    //                     }else{
    //                         $('#gwasQuery').attr('action', 'qtAnal.php');
    //                     }
    //                 }
    //                 return true; 
    //             }
    //         }else{
    //             alert("Select SNPs");
    //             return false;
    //         }
    //     } else {
    //         alert("Select the disease");
    //         return false;
    //     }
    // });
</script>

<?php
    include('footer.php');
?>