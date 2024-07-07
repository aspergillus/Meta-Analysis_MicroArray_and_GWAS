<?php
session_start();
$_SESSION = array();
$dataset=$_POST["dataset"];
if($dataset=="")
{
	$dataset="disease";
}

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// Finally, destroy the session.
session_destroy();
error_reporting(0);
include("connect.php");
foreach($_POST["diss_grpp"] as $dis_groo)
	 		 {
				 $dadw[]=$dis_groo;
			 }
			 if($_POST["rese"] == "Reset")
			 {
				 $dadw=array();
			 }
	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BIOMITRA:Friend of Biologists</title>
<script src="SpryAssets/SpryMenuBar.js" type="text/javascript">
function goToNewPage()
    {
        var url = document.getElementById('list').value;
        if(url != 'none') {
            window.location = url;
        }
    }
</script>
<link href="SpryAssets/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">

<link rel="stylesheet" href="css/style.css">
<style>
body {
	background-color: #D6D6D6;
}

pa{
  text-align: Center;
  font-family: Verdana;
  }
#wrapper {
  display: flex;
  background:#F5DEB3;
   height: 135px;
}

#left {
  flex: 0 0 30%;
  
}

#right {
  flex: 1;
}
</style>

<script>
function showLoading() {
    document.getElementById('loadingmsg').style.display = 'block';
    document.getElementById('loadingover').style.display = 'block';
} </script>
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
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
}
</style>
<script language="javascript" src="js/chainedselects.js">
</script>
<script language="javascript" src="js/sele.js"></script>

<script language="javascript" src="js/jsval.js">
    </script>
    <script language="javascript" src="js/gen_validat1.js"></script>
<script type="text/javascript" src="js/dropdown.js"></script>

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
<!-- Select all checkbox -->

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

</head>

<body topmargin="0" leftmargin="0" >


<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
   <div id="wrapper">
  <div id="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="head13.png" /></div>
  <div id="right"></div>
</div> 
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#B99C6B">
    <td colspan="3"><ul id="MenuBar1" class="MenuBarHorizontal">
	<li><a href="index.php">Home</a></li>
	
     <li><a class="MenuBarItemSubmenu" href="#">Analysis</a>
        <ul>
          <li><a  href="gwas.php">GWAS</a></li>
      <li><a href="affyM_index.php">Microarray</a></li>
      
        </ul>
      </li>
      <li><a href="stats.php">Statistics</a></li>
	  <li><a href="help.php">Help</a></li>
	  <li><a href="link.php">Links</a></li>
    </ul></td>
  </tr> 
</table>
    </td>
  </tr>
  <tr bgcolor="#ffffff"><td align="left">
  <table width="90%" border="0" cellspacing="0" cellpadding="6" align="center">
  <tr>
    <td><h2 align="center">GWAS analysis</h2>
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
  
  <form action="result.php" method="post" enctype="application/x-www-form-urlencoded" name="myForm" onSubmit="selectAllOptions('sec');return validateForm()">
  
  
              <table width="100%" border="0" align="center" cellpadding="6" cellspacing="0">
  <tr>
    <td width="48%" valign="top">
      <p><strong><?php 
		if ($dataset=="disease")
	  {
		  ?>Disease/Trait
          <?php }
		  if ($dataset=="efo")
	  {?>EFO Term
		  <?php } ?>
	  </strong> </p><strong>
       <select id="sec_rem" style="min-width: 200px;float:left;" name="gene_list" multiple="multiple" size="20">
        <?php 
		if ($dataset=="disease")
	  {
		$res=mysqli_query($conn, "select DISTINCT DISEASE_TRAIT from study ORDER BY DISEASE_TRAIT");
	while($row=mysqli_fetch_array($res))
{
	 $gene_name=$row["DISEASE_TRAIT"]; 
	 if($gene_name=="")
	 {}
	 else
	 {
	?>
        <option value="<?php echo $gene_name; ?>"><?php echo $gene_name; ?></option>
        <?php } }} 
		if ($dataset=="efo")
	  {
		$res=mysqli_query($conn, "select DISTINCT EFO_term from efo_trait ORDER BY EFO_term");
	while($row=mysqli_fetch_array($res))
{
	 $gene_name=$row["EFO_term"]; 
	?>
        <option value="<?php echo $gene_name; ?>"><?php echo $gene_name; ?></option>
        <?php }} 
		
		 
		?>
        </select>
        
      </strong></td>
    <td width="10%"><table width="85%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <th width="38%" height="75" scope="row"><a href="#" onclick="listbox_moveacross('sec', 'sec_rem')"><img src="img/add.jpg" alt="Add" width="25" height="25" /></a></th>
        <td width="20%">&nbsp;</td>
        <td width="42%"><a href="#" onclick="listbox_moveacross('sec_rem', 'sec')"><img src="img/re.jpg" alt="Remove" width="25" height="25" /></a></td>
        </tr>
    </table></td>
    <td width="42%" valign="top"><p><strong><?php 
		if ($dataset=="disease")
	  {
		  ?>Selected Disease/Trait
          <?php }
		  if ($dataset=="efo")
	  {?>Selected EFO Term
		  <?php } ?></strong></p>
      <select id="sec" multiple="multiple"  size="20" style="min-width: 200px;float:left;" name="dis_grr[]">
      

    </select></td>
  </tr>
 
  
  <tr>
    <td colspan="3"><div align="center">
      <div id='loadingmsg' style='display: none;'>
        <div align="center">Processing, please wait......</div>
        </div>
        <?php //echo $dataset; ?>
      <div id='loadingover' style='display: none;'></div> 
      <input name="dataset" type="hidden" value="<?php echo $dataset; ?>" />   
      <div align="center">&nbsp;
        <input type="submit" name="button" id="button" value="Submit" onsubmit='return validate();' />&nbsp;&nbsp;</div></td>
    </tr>
  
              </table>
</form>
  
  </td>
  </tr>
</table>

  </td></tr>
  <tr bgcolor="#F5DEB3" height="100">
    <td><p align="center" class="style13"><small>
     | &copy; 2022,  Biomedical Informatics Centre, NIRRH |<br/>
    ICMR-National Institute for Research in Reproductive Health, Jehangir Merwanji Street, Parel, Mumbai-400012<br>
    Tel: +91-22-24192104, Fax No: +91-22-24139412</small></p></td>
  </tr>
</table>
<script type="text/javascript">
var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgDown:"SpryAssets/SpryMenuBarDownHover.gif", imgRight:"SpryAssets/SpryMenuBarRightHover.gif"});
</script>
</body>
</html>
