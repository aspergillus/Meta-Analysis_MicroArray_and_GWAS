<?php
     include("connect.php");
    session_start();
	$st_acc=array();
	$st_acc=array();
	$dis_a=array();
	$pvalue=(float) $_POST["p_value"];
	$dataset= $_POST["dataset"];
   foreach($_POST["stu_acc"] as $stu_acct)
   {
	   $stun[]=$stu_acct;
	   $stu_acce=array();
	   $stu_acce=explode("#", $stu_acct);
	   $st_acc[]=$stu_acce[0];
	   $dis_a[]=$stu_acce[1];
   }
   $st_accn=$dis_an=array();
   $st_accn=array_unique($st_acc);
   $dis_an=array_unique($dis_a);
   $ai=0;
   $scn="";
   $cdng = array('frameshift_variant', 'missense_variant', 'protein_altering_variant', 'start_lost', 'stop_gained', 'stop_lost', 'synonymous_variant');
            $non_cdng = array('intron_variant', '3_prime_UTR_variant', '5_prime_UTR_variant', 'intergenic_variant', 'non_coding_transcript_exon_variant', 'TF_binding_site_variant', 'splice_acceptor_variant', 'splice_donor_variant', 'splice_region_variant', 'on_variant', 'mature_miRNA_variant', 'regulatory_region_variant', 'non_coding_transcript_exon_variant');
   foreach($st_accn as $str_ac)
   {
	   if($ai==0)
	   {
	   $scn="study_accession = '$str_ac'";
	   }
	   else
	   {
		   $scn="$scn OR study_accession = '$str_ac'";
	   }
	   $ai++;
   }
   $ain=0;
   $dcn="";
  
   foreach($dis_an as $dis_n)
   {
	   if($ain==0)
	   {
	   $dcn="disease_merge = '$dis_n'";
	   }
	   else
	   {
		   $dcn="$dcn OR disease_merge = '$dis_n'";
	   }
	   $ain++;
   }
   
    
    if(!is_array($_POST['tissue'])){
        $allTissue = explode(",", $_POST['tissue']);
    }else{
        $allTissue = $_POST['tissue'];
    }
	//echo "SELECT DISTINCT snps FROM snp_table WHERE $scn AND $dcn";
   $resn = mysqli_query($conn, "SELECT DISTINCT snps, chr, pos FROM snp_table WHERE ($scn) AND ($dcn)");
    while($rown = mysqli_fetch_array($resn)){
    $snpid = $rown["snps"];
	$resn1=mysqli_query($conn, "SELECT DISTINCT context FROM snp_table WHERE (snps='$snpid') AND ($scn) AND ($dcn)");
				while($rown1 = mysqli_fetch_array($resn1)){
					if(in_array($rown1["context"], $cdng)){
                        $qcntxt = "Coding";
                    }elseif(in_array($rown1["context"], $non_cdng)){
                        $qcntxt = "Non-coding";
                    }else{
                        $qcntxt = "-";
                    }
				$natrs[$snpid]=$qcntxt;
				}
				$qsnp_id[]=$snpid;
				$chro[]=$rown["chr"];
				$posn[]=$rown["pos"];
                $l = 0;
                foreach($allTissue as $tKey){
					//echo "SELECT * FROM `$tKey` WHERE snpid = '$snpid' AND Pvalue < '$pvalue' ORDER BY Mapped_gene<br>";
					$countt =0;
                    $res = mysqli_query($conn, "SELECT * FROM `$tKey` WHERE snpid = '$snpid' AND Pvalue < '$pvalue' ORDER BY Mapped_gene");
                    $countt = mysqli_num_rows($res);
					if($countt > 0)
					{
                    $c = 0;
                    while($row = mysqli_fetch_array($res)){
                        if($row['Mapped_gene'] != "NULL"){
                            $mGen = $row['Mapped_gene'];
                            $pval = sprintf("%.2e", $row['Pvalue']);
                            if($c > 0){
                                if($tissResult[$f][$l]['mapGen'][$c - 1] == $mGen){
                                    if($tissResult[$f][$l]['pVal'][$c - 1] > $pval){
                                        $srcID = $row['Sourceid'];
                            $res2 = mysqli_query($conn, "SELECT * FROM `qtlbase_sourceid` WHERE Sourceid = '$srcID'");
                                        while($row2 = mysqli_fetch_array($res2)){
                                            $qtlList[] = $row2['xQTL'];
                                            if(isset($_POST['qtlSNP'])){ $qtlopt=$_POST['qtlSNP'];
                                                if($_POST['qtlSNP'] == 'all_QTL'){
                                                    $tissResult[$f][$l]['tissName'][0] = $tKey;
                                                    $tissResult[$f][$l]['snpChr'][$c - 1] = preg_replace('~\D~', '', $row['SNP_chr']);
                                                    $tissResult[$f][$l]['snpPos'][$c - 1] = $row['SNP_pos_hg38'];
                                                    $tissResult[$f][$l]['mapGen'][$c - 1] = $mGen;
                                                    $tissResult[$f][$l]['pVal'][$c - 1] = $pval;
                                                    $tissResult[$f][$l]['xQTL'][$c - 1] = $row2['xQTL'];
                                                }elseif($row2['xQTL'] == $_POST['qtlSNP']){
                                                    $tissResult[$f][$l]['tissName'][0] = $tKey;
                                                    $tissResult[$f][$l]['snpChr'][$c - 1] = preg_replace('~\D~', '', $row['SNP_chr']);
                                                    $tissResult[$f][$l]['snpPos'][$c - 1] = $row['SNP_pos_hg38'];
                                                    $tissResult[$f][$l]['mapGen'][$c - 1] = $mGen;
                                                    $tissResult[$f][$l]['pVal'][$c - 1] = $pval;
                                                    $tissResult[$f][$l]['xQTL'][$c - 1] = $row2['xQTL'];
                                                }

                                            }else{
                                                $tissResult[$f][$l]['tissName'][0] = $tKey;
                                                $tissResult[$f][$l]['snpChr'][$c - 1] = preg_replace('~\D~', '', $row['SNP_chr']);
                                                $tissResult[$f][$l]['snpPos'][$c - 1] = $row['SNP_pos_hg38'];
                                                $tissResult[$f][$l]['mapGen'][$c - 1] = $mGen;
                                                $tissResult[$f][$l]['pVal'][$c - 1] = $pval;
                                                $tissResult[$f][$l]['xQTL'][$c - 1] = $row2['xQTL'];
                                            }
                                        }
                                    }
                                }
                                else{
                                    $srcID = $row['Sourceid'];
                                    $res2 = mysqli_query($conn, "SELECT * FROM `qtlbase_sourceid` WHERE Sourceid = '$srcID'");
                                    while($row2 = mysqli_fetch_array($res2)){
                                        $qtlList[] = $row2['xQTL'];
                                        if(isset($_POST['qtlSNP'])){
                                            if($_POST['qtlSNP'] == 'all_QTL'){
                                                $tissResult[$f][$l]['tissName'][0] = $tKey;
                                                $tissResult[$f][$l]['snpChr'][$c] = preg_replace('~\D~', '', $row['SNP_chr']);
                                                $tissResult[$f][$l]['snpPos'][$c] = $row['SNP_pos_hg38'];
                                                $tissResult[$f][$l]['mapGen'][$c] = $mGen;
                                                $tissResult[$f][$l]['pVal'][$c] = $pval;
                                                $tissResult[$f][$l]['xQTL'][$c] = $row2['xQTL'];
                                                $c++;
                                            }elseif($row2['xQTL'] == $_POST['qtlSNP']){
                                                $tissResult[$f][$l]['tissName'][0] = $tKey;
                                                $tissResult[$f][$l]['snpChr'][$c] = preg_replace('~\D~', '', $row['SNP_chr']);
                                                $tissResult[$f][$l]['snpPos'][$c] = $row['SNP_pos_hg38'];
                                                $tissResult[$f][$l]['mapGen'][$c] = $mGen;
                                                $tissResult[$f][$l]['pVal'][$c] = $pval;
                                                $tissResult[$f][$l]['xQTL'][$c] = $row2['xQTL'];
                                                $c++;
                                            }
                                        }else{
                                            $tissResult[$f][$l]['tissName'][0] = $tKey;
                                            $tissResult[$f][$l]['snpChr'][$c] = preg_replace('~\D~', '', $row['SNP_chr']);
                                            $tissResult[$f][$l]['snpPos'][$c] = $row['SNP_pos_hg38'];
                                            $tissResult[$f][$l]['mapGen'][$c] = $mGen;
                                            $tissResult[$f][$l]['pVal'][$c] = $pval;
                                            $tissResult[$f][$l]['xQTL'][$c] = $row2['xQTL'];
                                            $c++;
                                        }
                                    }
                                }
                            }else{
                                $srcID = $row['Sourceid'];
                                $res2 = mysqli_query($conn, "SELECT * FROM `qtlbase_sourceid` WHERE Sourceid = '$srcID'");
                                while($row2 = mysqli_fetch_array($res2)){
                                    $qtlList[] = $row2['xQTL'];
                                    if(isset($_POST['qtlSNP'])){
                                        if($_POST['qtlSNP'] == 'all_QTL'){
                                            $tissResult[$f][$l]['tissName'][0] = $tKey;
                                  $tissResult[$f][$l]['snpChr'][$c] = preg_replace('~\D~', '', $row['SNP_chr']);
                                            $tissResult[$f][$l]['snpPos'][$c] = $row['SNP_pos_hg38'];
                                            $tissResult[$f][$l]['mapGen'][$c] = $mGen;
                                            $tissResult[$f][$l]['pVal'][$c] = $pval;
                                            $tissResult[$f][$l]['xQTL'][$c] = $row2['xQTL'];
                                            $c++;
                                        }elseif($row2['xQTL'] == $_POST['qtlSNP']){
                                            $tissResult[$f][$l]['tissName'][0] = $tKey;
                                            $tissResult[$f][$l]['snpChr'][$c] = preg_replace('~\D~', '', $row['SNP_chr']);
                                            $tissResult[$f][$l]['snpPos'][$c] = $row['SNP_pos_hg38'];
                                            $tissResult[$f][$l]['mapGen'][$c] = $mGen;
                                            $tissResult[$f][$l]['pVal'][$c] = $pval;
                                            $tissResult[$f][$l]['xQTL'][$c] = $row2['xQTL'];
                                            $c++;
                                        }
                                    }else{
                                        $tissResult[$f][$l]['tissName'][0] = $tKey;
                                        $tissResult[$f][$l]['snpChr'][$c] = preg_replace('~\D~', '', $row['SNP_chr']);
                                        $tissResult[$f][$l]['snpPos'][$c] = $row['SNP_pos_hg38'];
                                        $tissResult[$f][$l]['mapGen'][$c] = $mGen;
                                        $tissResult[$f][$l]['pVal'][$c] = $pval;
                                        $tissResult[$f][$l]['xQTL'][$c] = $row2['xQTL'];
                                        $c++;
                                    }
                                }
                            }
                        }
                    }}
                    $l++;
                }    
                $f++;        
            }
        
    

    include('header.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="js/jquery_3.2.1.js"></script>

<style>
    .leftDiv{
        float: left;
        margin: 10px 0;
    }

    .rightDiv{
        float: right;
        margin: 7px 0;
    } 

    .qtlTbl{
        margin-bottom: 1px;
        border: #b99c6b;
        border-collapse: collapse;
        display: block;
        /* min-height: 200px; */
        max-height: 900px;
        overflow: auto;
        width: 1120px;
    }
</style>

<div><br />
    <h3 align="center">Disease analyzer: QTL analysis</h3>
    <?php
        if(!empty($tissResult)){
            ?>
                
  <div align="right">                                                  <!-- Change to various QTL -->
                         <form action="" method="POST">
                            <label>Change QTL to: </label>
                            <select name="qtlSNP" id="rsSelect" onchange="this.form.submit()">
                                <option value="all_QTL">All QTLs</option>
                                <?php
                                    $qtlList = array_unique($qtlList);
                                    foreach($qtlList as $qKey){
                                        ?>
                                            <option value="<?= $qKey; ?>"<?php if($qtlopt==$qKey){ ?> selected="selected" <?php } ?>><?= $qKey; ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                            <input type="hidden" name="sel_rs" value="<?= $_POST["sel_rs"]; ?>">
                            <?php 
                                if(!is_array($_POST["tissue"])){
                                    ?>
                                        <input type="hidden" name="tissue" value="<?= $_POST["tissue"]; ?>">
                                    <?php
                                }else{
                                    ?>
                                        <input type="hidden" name="tissue" value="<?= implode(",", $_POST["tissue"]); ?>">
                                    <?php
                                }

                                if(isset($_POST['cdngChk'])){
                                    ?>
                                        <input type="hidden" name="cdngChk" value="<?= $_POST["cdngChk"]; ?>">
                                    <?php
                                }

                                if(isset($_POST['ncdngChk'])){
                                    ?>
                                        <input type="hidden" name="ncdngChk" value="<?= $_POST["ncdngChk"]; ?>">
                                    <?php
                                }
								foreach($stun as $stn_d)
								{
                            ?><input name="stu_acc[]" type="hidden" value="<?php echo $stn_d; ?>" />
                            <input type="hidden" name="pvalue" value="<?= $_POST["pvalue"]; ?>">
                            <?php } ?>
                             <input type="hidden" name="dataset" value="<?php echo $dataset; ?>" />
                           <i style="font-size:20px; cursor: pointer; margin-right: 14px; margin-left: 2px; vertical-align: text-top;" class="fa dnbtn"
                                onclick="tblDnld()">&#xf019;</i>
                        </form>
                        </div>
                    
                

                <table width="99%" border="1" cellspacing="0" cellpadding="4" bordercolor="#B99C6B" id="qtlTbl" align="center">
                    <thead bgcolor="#F1E8D1">
                        <tr>
                            <td width="5%"><Strong>SNP ID</Strong></td>
                            <td><Strong>Chromosome number: position</Strong></td>
                            <td width="13%"><strong>Study accession</strong></td>
                            <td width="13%"><strong><strong>Diseases/Traits</strong></strong></td>
                            <td width="15%"><strong>SNP type</strong></td>
                            <td width="11%"><Strong>Mapped gene/s</Strong></td>
                            <td width="9%"><Strong>Tissue</Strong></td>
                            <td width="5%"><Strong>P-value</Strong></td>
                            <td width="5%"><Strong>QTL Type</Strong></td>
                            <td><div align="center">
                              <input type="checkbox" id="select_all" />
                            </div></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 0;
                            foreach($tissResult as $tVal){
                                $q = 0;
                                $tissContCount = 0;
                                $r = 0;
                                foreach($tVal as $tKey){
                                    $tissContCount = $tissContCount + sizeof($tKey['snpChr']);                                       //will count the all rows of the snp
                                    $allGen = array();
                                    for($k=0; $k<sizeof($tKey['mapGen']); $k++){
                                        $allGen[] = $tKey['mapGen'][$k];
                                        ?>
                                            <tr>
                                                <?php
                                                    if($q == 0){
                                                        ?>
                                                            <td valign="top" id="td_<?= $i?>" class="rsID">
                                                   <?php $snppid=$qsnp_id[$i];
												    echo "<a href=https://www.ncbi.nlm.nih.gov/snp/$qsnp_id[$i] target=_blank>$qsnp_id[$i]</a>"; ?>
                                                            </td>
                                                        <?php
                                                    }

                                                    if($k == 0){
                                                        ?>
                                                            <script>
                                                                var rsCnt = <?= json_encode($i); ?>;
                                                                var allTiss = <?= json_encode($tissContCount); ?>;
                                                                $(`#td_${rsCnt}`).attr('rowspan', allTiss);
                                                            </script>

                                                            <?php
													}
                                                    if($q == 0){
                                                        ?>

                                                            <td valign="top" class="ChrID" id="tda_<?= $i?>"> 
                                                                <?php echo $chro[$i]; ?>: <?php echo $posn[$i]; ?> 
                                                            </td>

															<?php
                                                    }

                                                    if($k == 0){
                                                        ?>
                                                            <script>
                                                                var rsCnt = <?= json_encode($i); ?>;
                                                                var allTiss = <?= json_encode($tissContCount); ?>;
                                                                $(`#tda_${rsCnt}`).attr('rowspan', allTiss);
                                                            </script>

                                                            <?php
													}
                                                    
                                                    if($q == 0){
                                                        ?>
                                                            
                                                           <td valign="top" rowspan="<?= count($tKey['mapGen']); ?>" class="study_acc"><?php 
														 // echo "SELECT DISTINCT study_accession FROM gwas_cc_input WHERE snp_id='$snppid'";
			$resn2 = mysqli_query($conn, "SELECT DISTINCT study_accession FROM snp_table WHERE snps='$snppid'");
														   $stid = array();
														  while($rown2 = mysqli_fetch_array($resn2)){  
														  $stid[]=$rown2["study_accession"];
														 
														  }
														  $stlst=array_intersect($stid, $st_accn);
														  //print_r($stun);
														  echo implode(", ", $stlst);
														   
														   ?></td>
                                                            <?php
                                                    }

                                                    if($k == 0){
                                                        ?>
                                                            <script>
                                                                var rsCnt = <?= json_encode($i); ?>;
                                                                var allTiss = <?= json_encode($tissContCount); ?>;
                                                                $(`#tdb_${rsCnt}`).attr('rowspan', allTiss);
                                                            </script>

                                                            <?php
													}
                                                    if($q == 0){
                                                        ?>
                                                            <td rowspan="<?= count($tKey['mapGen']); ?>" valign="top">
                                                           <?php 
														 // echo "SELECT DISTINCT study_accession FROM gwas_cc_input WHERE snp_id='$snppid'";
							$dsnd=array();							 
                   
                        $resn3 = mysqli_query($conn, "SELECT DISTINCT disease_merge FROM snp_table WHERE snps='$snppid'");
						while($rown3 = mysqli_fetch_array($resn3)){  
														  $dsnd[]=$rown3["disease_merge"];
														 
														  }
                   
              											  $dsnid = array();
														  
														  $dsnid=array_intersect($dsnd, $dis_an);
														  //print_r($stun);
														  echo implode(", ", $dsnid);
														   
														   ?> 
                                                            
                                                            
                                                            </td>
                                                            <?php
                                                    }

                                                    if($k == 0){
                                                        ?>
                                                            <script>
                                                                var rsCnt = <?= json_encode($i); ?>;
                                                                var allTiss = <?= json_encode($tissContCount); ?>;
                                                                $(`#tdc_${rsCnt}`).attr('rowspan', allTiss);
                                                            </script>

                                                        
                        <td rowspan="<?= count($tKey['mapGen']); ?>" valign="top"><?php echo $natrs[$snppid]; ?></td>
                        <td rowspan="<?= count($tKey['mapGen']); ?>" valign="top"><?= $tKey['mapGen'][$k]; ?></td>
                                                            
                                                            <td valign="top" rowspan="<?= count($tKey['mapGen']); ?>" class="tissue_Name"> 
                                                                <?= ucfirst(str_replace("_", " ", $tKey['tissName'][0])); ?> 
                                                            </td>
                                                        <?php
                                                    }
                                                ?>
                                                
                                                <td><?= $tKey['pVal'][$k]; ?></td>
                                                <td><?= $tKey['xQTL'][$k]; ?></td>
                                                <?php
                                                    if($k == 0){
                                                        ?>
                                                            <td width="6%" valign="top" id="td_<?= $i."_".$r; ?>">
                                                                <div align="center">
                                                                  <input type="checkbox" class="checkbox" />
                                                            </div></td>
                                                        <?php
                                                    }
                                                ?>
                                            </tr>
                                        <?php
                                        $q++;
                                    }
                                    $hiData = $qsnp_id[$i].'***'.ucfirst(str_replace("_", " ", $tKey['tissName'][0]))."***".implode(",", $allGen);
                                    ?>
                                        <script>
                                            var rCGen = <?= json_encode($i."^".$r."^".count($tKey['mapGen'])."^".$hiData); ?>;
                                            var rCGenArr = rCGen.split("^");
                                            $(`#td_${rCGenArr[0]}_${rCGenArr[1]}`).attr('rowspan', rCGenArr[2]).children().attr('value', rCGenArr[3]);
                                        </script>
                                    <?php
                                    $r++;
                                }
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>

                <!-- Button Table -->
  <p></p>
                <table width="65%" border="0" align="center" cellpadding="4" cellspacing="0">
                    <tr>
                        <td>
                            <form action="en_path.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" class="selDis_Gen">
                                <input type="hidden" name="type_Select" value='path_Enrich'>
                                <input type="submit" value="Pathway enrichment" onclick='return transQuery(this.value);'
                                    onsubmit='return validate();' />
                            </form>
                        </td>
                        <td>
                            <form action="ge_onto.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" class="selDis_Gen">
                                <input type="hidden" name="type_Select" value='gene_Onto'>
                                <input type="submit" value="Gene ontology enrichment" onclick='return transQuery(this.value);'
                                    onsubmit='return validate();' />
                            </form>
                        </td>
                        <td>
                            <form action="disEnrich.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" class="selDis_Gen">
                                <input type="hidden" name="type_Select" value='dis_Enrich'>
                                <input type="submit" value="Disease enrichment" onclick='return transQuery(this.value);'
                                    onsubmit='return validate();' />
                            </form>
                        </td>
                        <td>
                            <form action="gene_prioritization.php" method="post" target="_blank">
                                <input type="hidden" name="selDis" class="selDis_Gen">
                                <input type="hidden" name="type_Select" value='gene_prio'>
                                <input type="submit" value="Gene prioritization" onclick='return transQuery(this.value);'
                                    onsubmit='return validate();' />
                            </form>
                        </td>
                        <td>
                            <div align="center" style="padding-bottom: 12px;">
                                <input type="submit" onclick="location.href='gwas.php';" value="Back" />
                            </div>
                        </td>
                    </tr>
                </table>
                <p></p>
            <?php
        }else{
            echo '<label align="center">No data has been found for QTL Analysis</label>';
        }
    ?>
</div>

<!--<script>
    var selVal = <? //= json_encode($_POST['qtlSNP']); ?>;
    if(selVal != null){
        $(document).ready(function () {
            $('#rsSelect').val(selVal).change();
        });
    }else{
        $("#rsSelect").val($("#rsSelect option:first").val());
    }

    // console.log(selVal);
</script>
-->
<script>
    $(document).ready(function () {
        $('#select_all').on('click', function () {
            if (this.checked) {
                $('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function () {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });

    function transQuery(val){
        if(val == 'Pathway enrichment' || val == 'Gene ontology enrichment' || val == 'Disease enrichment' || val == 'Gene prioritization'){
            if ($(".checkbox").is(":checked")) {
                var selctDis = $('input:checked').map(function () {
                    return $(this).val();
                }).get().join([separator = ', ']);

                $('.selDis_Gen').val(selctDis);

                $('#select_all, .checkbox').each(function () {
                    this.checked = false;
                });
            }else{
                alert("Select the option before submit")
                return false
            }
        }else{
            alert("Select the option before submit")
            return false
        }
    }

    function generateExcel(el) {
        var clon = el.clone();
        var html = clon.wrap('<div>').parent().html();

        //add more symbols if needed...
        while (html.indexOf('á') != -1) html = html.replace(/á/g, '&aacute;');
        while (html.indexOf('é') != -1) html = html.replace(/é/g, '&eacute;');
        while (html.indexOf('í') != -1) html = html.replace(/í/g, '&iacute;');
        while (html.indexOf('ó') != -1) html = html.replace(/ó/g, '&oacute;');
        while (html.indexOf('ú') != -1) html = html.replace(/ú/g, '&uacute;');
        while (html.indexOf('º') != -1) html = html.replace(/º/g, '&ordm;');
        html = html.replace(/<td>/g, "<td>&nbsp;");
html = html.replace(/"checkbox"/g, "");
		
		//html = html.replace(/"<input type="checkbox" class="checkbox" />"/g, "&nbsp;");

        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
    }

    function tblDnld(){
        generateExcel($("#qtlTbl"));
    }
</script>

<?php
unlink($file);
    include('footer.php');
?>