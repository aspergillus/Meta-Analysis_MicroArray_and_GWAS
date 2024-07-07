<?php
    header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure");
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset= UTF-8" />
    <link rel="shortcut icon" href="#">
    <title>BioMitra:Friend of Biologists</title>
    <script src="SpryAssets/SpryMenuBar.js" type="text/javascript">
        function goToNewPage() {
            var url = document.getElementById('list').value;
            if (url != 'none') {
                window.location = url;
            }
        }
    </script>
    <link href="SpryAssets/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="css/fontGoogleapis.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #D6D6D6;
        }

        pa {
            text-align: Center;
            font-family: Verdana;
        }

        #wrapper {
            display: flex;
            background: #F5DEB3;
            height: 135px;
        }

        #left {
            flex: 0 0 30%;

        }

        #right {
            flex: 1;
        }
    </style>

</head>

<body>
    <table width="1150" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td>
                <div id="wrapper">
                    <div id="left"><img src="head13.png"/></div>
                    <div id="right"></div>
                </div>

                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:12px;">
                    <tr bgcolor="#B99C6B">
                        <td colspan="3">
                            <ul id="MenuBar1" class="MenuBarHorizontal">
                                <li><a href="index.php">Home</a></li>
                                <li><a class="MenuBarItemSubmenu" href="#">Analysis</a>
                                    <ul>
                                        <li><a href="gwas.php">GWAS</a></li>
                                        <li><a href="miArrayindex.php">Microarray</a></li>
                                        <li><a href="sessionOut.php">Session Out</a></li>
                                    </ul>
                                </li>
                                <li><a href="stats.php">Statistics</a></li>
                                <li><a href="help.php">Help</a></li>
                                <li><a href="link.php">Links</a></li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr bgcolor="#ffffff">
            <td align="left">
                <table width="98%" align="center" id="mdTable" style="font-size:12px;">
                    <tr>
                        <td>