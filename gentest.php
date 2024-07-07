<script src="js/jquery_3.2.1.js"></script>
<script src="js/select2_min.js"></script>
<link rel="stylesheet" href="css/select2_min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php


    if(isset($_POST["geneid"])){
		
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
        $gene = $_POST["geneid"];
		$gene = preg_replace("/\s\s+/", " ", $gene);
		$gene = rtrim($gene);
		$lst_gene=preg_split("/[\s,]+/", $gene);
		$ccat=0;
		$msnp=$nmsnp=array();
    foreach($lst_gene as $gene_n)
	{   
		$res=mysqli_query($conn, "SELECT DISTINCT mapped_gene FROM gwas_cc_input where mapped_gene='$gene_n'");
		$cnt=mysqli_num_rows($res);
		if($cnt > 0)
		{ $mgen[]=$gene_n;
			while($row=mysqli_fetch_array($res))
			{ 
			if($ccat==0)
			{
			$selDis = "on; dd***dm***$gene_n";
			}
			else
			{
			$selDis = $selDis."; dd***dm***$gene_n";
			}
				
			}
			$ccat++;
		}
		else
		{
			$nmgen[]=$gene_n;
		}
		
	}
	

?>
<?php
    include('header.php');
?><br />
                          <h2 align="center">Gene-based</h2>
                          <table width="95%" border="0" cellspacing="0" cellpadding="6" align="center">
                            <tr>
                              <td>
                                
                               
                               
                               
                <?php //echo $sel_rs; ?>
                <table width="95%" border="0" cellspacing="0" cellpadding="6">
  <?php if(count($mgen) > 0)
  { ?>
  <tr>
    <td width="19%">Mapped genes</td>
    <td width="81%"><?php echo implode(", ", $mgen); ?></td>
  </tr>
  <?php } 
  if(count($nmgen) > 0)
  { ?>
  <tr>
    <td>Unmapped genes</td>
    <td><?php echo implode(", ", $nmgen); ?></td>
  </tr>
  <?php } ?>
</table>

                <p align="center">&nbsp;</p>                         
                 <table width="65%" border="0" align="center" cellpadding="4" cellspacing="0">
                    <tr>
                        <td>
                            <form action="en_path.php" method="post" target="_blank">
                            <input type="hidden" name="selDis" id="sel_rsa" value="<?php echo $selDis; ?>">
                             <input type="hidden" name="type_Select" value='path_Enrich'>
                                <input type="submit" value="Pathway enrichment"/>
                            </form>
                        </td>
                        <td>
                            <form action="ge_onto.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" id="sel_rsa" value="<?php echo $selDis; ?>">
                                <input type="hidden" name="type_Select" value='gene_Onto'>
                                <input type="submit" value="Gene ontology enrichment" />
                            </form>
                        </td>
                        <td>
                            <form action="disEnrich.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" id="sel_rsa" value="<?php echo $selDis; ?>">
                                <input type="hidden" name="type_Select" value='dis_Enrich'>
                                <input type="submit" value="Disease enrichment" />
                            </form>
                        </td>
                        <td>
                            <form action="gene_prioritization.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" class="selDis_Gen">
                                <input type="hidden" name="type_Select" value='gene_prio'>
                                <input type="submit" value="Gene prioritization" />
                            </form>
                              </td>
                            </tr>
                          </table>
                          <p align="center">&nbsp;</p>
                          <table width="48%" border="0" align="center" cellpadding="4" cellspacing="0">
                        <tr>
                            <td>
                              <div align="center">
                                <input type="submit" id="button2" onclick="location.href='gene.php';"
                                        value="Back" />
                                </div>                            </td>
                            </tr>
                    </table>
                          
                          <?php
    include('footer.php');
	}else{
		
		header('Location: gene.php');
        
    }
?>