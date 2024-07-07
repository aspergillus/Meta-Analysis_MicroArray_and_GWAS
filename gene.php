<?php
session_start();

   

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


<form action="gentest.php" method="post" enctype="application/x-www-form-urlencoded" target="_self" onsubmit="showLoading();">
                          
              <h2 align="center">Gene-based
</h2>
                          <table width="95%" border="0" cellspacing="0" cellpadding="6" align="center">
                           <tr><td><label for="fname">
                             </label>
                             <div align="center"><strong>Enter list of HGNC gene symbols:</strong> <a href="#" id="example" onClick="return IEg();" style="font-size:10pt"> (Example: comma separated HGNC gene symbols ) </a><a href="#" id="example" onClick="return IENg();" style="font-size:10pt"> (Example: new line separated HGNC gene symbols ) </a>
                               </p>
                             </div>
                             <div align="center">
                             <input type="file" id="flin" onchange="IEFg()"></div></td></tr>
                            <tr>
                              <td><h3>&nbsp;</h3>                                <div align="center">
                                <textarea name="geneid" cols="60" rows="10" id = "txtbox" required oninvalid="this.setCustomValidity('Please provide HGNC gene symbols')" 
onchange="this.setCustomValidity('')" type="text"><?php echo $snpid; ?></textarea>
                              </div></td>
                            </tr>
                            <tr>
                              <td>
                              <div id='loadingmsg' style='display: none;'>
      <div align="center">Processing, please wait......
        </div>
    </div>
  <div id='loadingover' style='display: none;'></div> 
                              <div align="center">
                                <input type="submit" name="button" id="button" value="Submit" />
                                <input type="reset" name="button2" value="Reset" id="reset" onClick="return refre();"/>
                              </div></td>
                            </tr>
                          </table>
                          </form>
                          <p align="center">&nbsp;</p>
                          <p>&nbsp;</p>
                          <?php
    include('footer.php');
?>