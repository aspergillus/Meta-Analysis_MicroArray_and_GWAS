<?php 
session_start();

?>
<script src="js/jquery_3.2.1.js"></script>
<script src="js/select2_min.js"></script>
<link rel="stylesheet" href="css/select2_min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<?php
       
  if(isset($_SESSION["dataset"]))
		{
			$dataset=$_SESSION["dataset"];
		}
		else
		{
  $dataset= $_POST["dataset"];
  $_SESSION["dataset"]=$dataset;
		}
    
    include("connect.php");
   
    $i=0;
    
    $disha=array();
    // If Disease Selected
        if(isset($_SESSION["diseas"]))
		{
        foreach($_POST["dis_grr"] as $disa) {
            $disb=str_replace("'", "\'", $disa);
            $disha[]=$disa;
			//echo "Session working";
        
    }
		}
		else
		{ 
		//$_SESSION["diseas"]=array();
		$ds=0;
			foreach($_POST["dis_grr"] as $disa) {
            $disb=str_replace("'", "\'", $disa);
            $disha[]=$disa;
			$_SESSION["diseas"][$ds]=$disa;
			$ds++;
        
    }
		}

    ///////// include header  //////
    include('header.php');                      /// Don't move from here   
?>
<style>
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


    .blnkTD{
        width: 175px;
        padding-left: 5px;
    }

    .select2-container .select2-search--inline .select2-search__field{
        margin-top: 7px;
    }

    /* .sel_tiss{
        display: inline-flex;
    } */

    /* .select2-container--default .select2-selection--multiple{
        width: 80%;
    } */

    /* .allBtn{
        border-radius: 4px;
        padding: 6px 10px;
    } */

    /* .select2-container .select2-search--inline .select2-search__field{
        margin-top: 7px;
        margin-left: 8px;
    } */
</style>
<script src="SpryAssets/SpryMenuBar.js" type="text/javascript">
    function goToNewPage() {
        var url = document.getElementById('list').value;
        if (url != 'none') {
            window.location = url;
        }
    }

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
</script>

<script type="text/javascript">
    // function validate() {
    //     var fields = $("input[name='dis_rsID[]']").serializeArray();
    //     if (fields.length == 0) {
    //         alert('Please select option/s');
    //         return false;
    //     } else {
    //         showLoading();
    //     }

    // }
</script>
<script type="text/javascript">
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

    function showLoading() {
        document.getElementById('loadingmsg').style.display = 'block';
        document.getElementById('loadingover').style.display = 'block';
    } 
</script>
<div>
    <h2 align="center">GWAS analysis</h2><form action="result.php" method="post" enctype="application/x-www-form-urlencoded" target="_self">
    <table width="99%" border="0" cellspacing="0" cellpadding="6">
  <tr>
    
    <td width="100%">

    
    <div style="text-align-last: right;">
        <i style="font-size:20px; cursor: pointer; margin-right: 2px;" class="fa dnbtn" onclick="tblDnld()">&#xf019;</i>
    </div>
    <?php if(count($disha) > 0)
	{ ?>
   <form action="qtAnal.php" method="post" target="_blank">
   <div class="container" style="width:40%";>

<table class="table table-striped table-bordered" id="sortTable">
<thead>
    
        <tr bgcolor="#F1E8D1">
            <th><strong>Sr. No.</strong></th>
            <th width="3%">
                <?php 
                    if($dataset=="disease") {
                        ?><strong>Disease/Trait</strong><?php
                    }

                    if($dataset=="efo") {
                        ?><strong>EFO term </strong><?php
                    }
                ?>
            </th>
            <th><strong>Parent term</strong></th>
            <th><strong>Database</strong></th>
            <th><strong>Case</strong></th>
            <th><strong>Control</strong>            </th>
            
            <th><strong>Study accession</strong></th>
            <th><strong>SNP type</strong></th>
            <th><strong>SNP ID</strong></th>
            <th ><strong>P-value</strong></th>
            <td><strong>Chromosome number</strong></th>
            <td><strong>Chromosome position</strong></th>
            <th><strong>Mapped gene/s</strong></th>
            <th width="2%" class="lastTD"><input type="checkbox" id="select_all" /></th>
        </tr></thead>
		<tbody>
        <?php
            $cdng = array('frameshift_variant', 'missense_variant', 'protein_altering_variant', 'start_lost', 'stop_gained', 'stop_lost', 'synonymous_variant');
            $non_cdng = array('intron_variant', '3_prime_UTR_variant', '5_prime_UTR_variant', 'intergenic_variant', 'non_coding_transcript_exon_variant', 'TF_binding_site_variant', 'splice_acceptor_variant', 'splice_donor_variant', 'splice_region_variant', 'on_variant', 'mature_miRNA_variant', 'regulatory_region_variant', 'non_coding_transcript_exon_variant');
            for($i=0; $i<sizeof($disha); $i++){
                $aGen = array();
                $qpval = $qcntxt = $qsnp_id = $qchr_id = $qchr_pos = $qupd_cs = $qupd_con = $qupd_con = $qmad_gen = $prnt_Trm = $src_db = array();
$disn = str_replace("'", "\'", $disha[$i]);
                
                if ($dataset == "disease") {
					
                    $res4 = mysqli_query($conn, "select DISTINCT snp_id, study_accession, context, pvalue, chr_id, chr_pos, updated_case, updated_control, mapped_gene, parent_term, source_db FROM gwas_cc_input where diseases = '$disn' and snp_id LIKE 'rs%' ORDER BY parent_term, updated_case, updated_control, study_accession");
                } else {
					
                    $res4 = mysqli_query($conn, "select DISTINCT snp_id, study_accession, context, pvalue, chr_id, chr_pos, updated_case, updated_control, mapped_gene, parent_term, source_db FROM gwas_cc_input where efo_term = '$disn' and snp_id LIKE 'rs%' ORDER BY parent_term, updated_case, updated_control, study_accession");
                }

                while ($row4 = mysqli_fetch_array($res4)) {
                    $qpval[] = $row4['pvalue'];
                    $qsnp_id[] = $row4['snp_id'];
                    $stu_Acc[] = $row4['study_accession'];
                    if(in_array($row4['context'], $cdng)){
                        $qcntxt[] = "Coding";
                    }elseif(in_array($row4['context'], $non_cdng)){
                        $qcntxt[] = "Non-coding";
                    }else{
                        $qcntxt[] = "-";
                    }
                    $qchr_id[] = $row4['chr_id'];
                    $qchr_pos[] = $row4['chr_pos'];
                    $qupd_cs[] = $row4['updated_case'];
                    $qupd_con[] = $row4['updated_control'];
                    $qmad_gen[] = $row4['mapped_gene'];
                    $prnt_Trm[] = $row4['parent_term'];
                    $src_db[] = $row4['source_db'];
                }

                // will do multi sorting to all the array
                // sort($stu_Acc);
                // array_multisort($prnt_Trm, $qupd_cs, $qupd_con, $qsnp_id, $qpval, $qchr_id, $qmad_gen, $qchr_pos);
                // array_multisort($prnt_Trm, $qupd_cs, $qupd_con, $stu_Acc);

                $snpID = $chrID = $chrPos = array();
                if(!empty($qsnp_id)){
                    
                    // initializing blank variable
                    $preVal_Stu = $preVal_CS = $preVal_Con = $preVal_Paren = $preVal_Chkbx = $preVal_srcdb = '';             // previous study_access, case, control and parent value respectively
                    $preValCnt_Stu = $preValCnt_CS_Con = $preValCnt_Paren = $preValCnt_srcdb = 0;               // same study_access, case, control and parent value respectively counter 
                    $tdCnt_Stu = $tdCnt_CS_Con = $tdCnt_Paren = $tdCnt_Chkbx = $tdCnt_srcdb = 0;            // unique study_access, case, control and parent value respectively row counter

                    for($j=0; $j<count($qsnp_id); $j++) {
                        $snglID=$qsnp_id[$j];
                        ?>
                            <tr>
                                <?php
                                    if($j == 0){
                                        ?>
                                            <td width="1%" valign="top" class="serial_number" rowspan="<?= count($qsnp_id); ?>">
                                                <div align="justify" id="srNum"><?php echo ($i+1); ?></div>
                                            </td>
                                            <td width="20%" valign="top" class="diseas_name" rowspan="<?= count($qsnp_id); ?>">
                                                <div align="justify" id="disTr_efo"><?php echo $disha[$i]; ?></div>
                                            </td>
                                        <?php
                                    }

                                    // Parent term
                                    if($preVal_Paren == $prnt_Trm[$j]){
                                        $preValCnt_Paren++;
                                        if($preValCnt_Paren != 0){
                                            ?>
                                                <script>
                                                    var td_dis_ID = <?= $i; ?>;
                                                    var td_paren_ID = <?= $tdCnt_Paren; ?>;
                                                    var smtd_paren_Cnt = <?= $preValCnt_Paren; ?>;
                                                    $(`#td_paren_${td_dis_ID}_${td_paren_ID - 1}`).attr('rowspan', smtd_paren_Cnt + 1);
                                                </script>
                                            <?php
                                        }
                                    }else{
                                        $preVal_Paren = $prnt_Trm[$j];
                                        ?>
                                            <td valign="top" class="parent_term" id="td_paren_<?= $i.'_'.$tdCnt_Paren; ?>"><?= $prnt_Trm[$j]; ?></td>
                                        <?php
                                        $preValCnt_Paren = 0;
                                        $tdCnt_Paren++;
                                    }
									

                                    // Database
                                    if($preVal_srcdb == $src_db[$j]){
                                        $preValCnt_srcdb++;
                                        if($preValCnt_srcdb != 0){
                                            ?>
                                                <script>
                                                    var td_dis_ID = <?= $i; ?>;
                                                    var td_db_ID = <?= $tdCnt_srcdb; ?>;
                                                    var smtd_db_Cnt = <?= $preValCnt_srcdb; ?>;
                                                    $(`#td_db_${td_dis_ID}_${td_db_ID - 1}`).attr('rowspan', smtd_db_Cnt + 1);
                                                </script>
                                            <?php
                                        }
                                    }else{
                                        $preVal_srcdb = $src_db[$j];
                                        ?>
                                            <td valign="top" class="parent_term" id="td_db_<?= $i.'_'.$tdCnt_srcdb; ?>"><?= $src_db[$j]; ?></td>
                                            <td valign="top" class="updated_case_and_control" id="td_Updated_CS_Con_<?= $i.'_'.$tdCnt_CS_Con?>"><?php 
                                                                    if( !empty($qupd_cs)) {
                                                                        echo $qupd_cs[$j]; 
                                                                    }
                                                                    else {
                                                                        echo "-";
                                                                    }
                                                                ?></td>
                                        <?php
                                        $preValCnt_srcdb = 0;
                                        $tdCnt_srcdb++;
                                    }
									?>
                                    
                                    
                                    <?php

                                    // Case and Control
                                    if($preVal_CS == $qupd_cs[$j] && $preVal_Con == $qupd_con[$j]){
                                        $preValCnt_CS_Con++;
                                        if($preValCnt_CS_Con != 0){
                                            ?>
                                                <script>
                                                    var td_dis_ID = <?= $i; ?>;
                                                    var td_CS_Con_ID = <?= $tdCnt_CS_Con; ?>;
                                                    var smtd_CS_Con_Cnt = <?= $preValCnt_CS_Con; ?>;
                                                    $(`#td_Updated_CS_Con_${td_dis_ID}_${td_CS_Con_ID - 1}`).attr('rowspan', smtd_CS_Con_Cnt + 1);
                                                </script>
                                            <?php
                                        }
                                    }else{
                                        $preVal_CS = $qupd_cs[$j];
                                        $preVal_Con = $qupd_con[$j];
                                        ?>
                                            <td valign="top" class="updated_case_and_control" id="td_Updated_CS_Con_<?= $i.'_'.$tdCnt_CS_Con?>"><?php 
                                                                    if( !empty($qupd_con)) {
                                                                        echo $qupd_con[$j]; 
                                                                    }
                                                                    else {
                                                                        echo "-";
                                                                    }
                                                                ?>
                                            </td>
                                        <?php
                                        $preValCnt_CS_Con = 0;
                                        $tdCnt_CS_Con++;
                                    }
									?>
                                    
                                    <?php

                                    // study_accession
                                    if($preVal_Stu == $stu_Acc[$j]){
                                        $preValCnt_Stu++;
                                        if($preValCnt_Stu != 0){
                                            ?>
                                                <script>
                                                    var td_dis_ID = <?= $i; ?>;
                                                    var td_Stu_ID = <?= $tdCnt_Stu; ?>;
                                                    var smtd_Stu_Cnt = <?= $preValCnt_Stu; ?>;
                                                    $(`#td_${td_dis_ID}_${td_Stu_ID - 1}`).attr('rowspan', smtd_Stu_Cnt + 1);
                                                </script>
                                            <?php
                                        }
                                    }else{
                                        $preVal_Stu = $stu_Acc[$j];
                                        $hypStu_Acc = $stu_Acc[$j];
                                        ?>
                                            <td valign="top" class="study_accession" id="td_<?= $i.'_'.$tdCnt_Stu?>"><?= "<a href='https://www.ebi.ac.uk/gwas/studies/$hypStu_Acc' target=_blank>$hypStu_Acc</a>"; ?></td>
                                        <?php
                                        $tdCnt_Stu++;
                                    }
                                ?>
                                <!-- SNP Type -->
                                <td valign="top" class="contxt">
                                    <?php
                                        if( !empty($qcntxt)) {
                                            echo $qcntxt[$j]; 
                                        }
                                        else {
                                            echo "-";
                                        }
                                    ?>
                                </td>
                                <td valign="top" class="snp_ID">
                                    <?php 
                                        if(preg_match("#^rs(.*)$#i",$qsnp_id[$j])) {
                                            echo "<a href='https://www.ncbi.nlm.nih.gov/snp/$snglID' target=_blank>$snglID</a>"; 
                                        }
                                        else {
                                            echo $qsnp_id[$j];
                                        }
                                    ?>
                                </td>
                                <td valign="top" class="pVal">
                                    <?php
                                        if( !empty($qpval)) {
                                            echo $qpval[$j]; 
                                        }
                                        else {
                                            echo "-";
                                        }
                                    ?>
                                </td>
                                <td valign="top" class="chr_ID">
                                    <?php 
                                        if( !empty($qchr_id)) {
                                            if($qchr_id[$j] != ""){
                                               // echo 'chr'.$qchr_id[$j];
												echo $qchr_id[$j];
                                            }else{
                                                echo "-";
                                            }
                                        }
                                        else {
                                            echo "-";
                                        }
                                    ?>
                                </td>
                                <td valign="top" class="chr_pos">
                                    <?php
                                        if( !empty($qchr_pos)) {
                                            if($qchr_pos[$j] != ""){
                                                echo $qchr_pos[$j];
                                            }else{
                                                echo "-";
                                            }
                                        }
                                        else {
                                            echo "-";
                                        }
                                        
                                    ?>
                                </td>
                                <td valign="top" class="mappedGen">
                                    <?php
                                        if($qmad_gen[$j] != ""){
                                            if(preg_match("/ - /i", $qmad_gen[$j])){
                                                $spltStrng = explode(' - ', $qmad_gen[$j]);
                                                $hypstrg = array();
                                                foreach($spltStrng as $key){
                                                    $sglestng = str_replace(' ', '', $key);
                                                    $aGen[] = $sglestng;
                                                    $qure = "SELECT GeneID FROM entrez_id WHERE Symbol = '$sglestng'";
                                                    $res6 = mysqli_query($conn, $qure);
                                                    $GenID="";

                                                    while($row6 = mysqli_fetch_array($res6)){
                                                        $GenID = $row6["GeneID"];
                                                    }

                                                    if($GenID !="") {
                                                        $hypstrg[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$sglestng</a>";
                                                    } else {
                                                        $hypstrg[]=$sglestng;
                                                    }
                                                }
                                                echo implode(' - ', $hypstrg);
                                            }elseif(preg_match("/,/i", $qmad_gen[$j])){
                                                $spltStrng = array_unique(explode(', ', $qmad_gen[$j]));
                                                $hypstrg = array();
                                                foreach($spltStrng as $key){
                                                    $sglestng = str_replace(' ', '', $key);
                                                    $aGen[] = $sglestng;
                                                    $qure2 = "SELECT GeneID FROM entrez_id WHERE Symbol = '$sglestng'";
                                                    $res7 = mysqli_query($conn, $qure2);
                                                    $GenID="";

                                                    while($row7 = mysqli_fetch_array($res7)){
                                                        $GenID = $row7["GeneID"];
                                                    }
                                                    
                                                    if($GenID !="") {
                                                        $hypstrg[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$sglestng</a>";
                                                    } else {
                                                        $hypstrg[] = $sglestng;
                                                    }
                                                }
                                                echo implode(', ', $hypstrg);
                                            }elseif(preg_match("/;/i", $qmad_gen[$j])){
                                                $spltStrng = array_unique(explode('; ', $qmad_gen[$j]));
                                                $hypstrg = array();
                                                foreach($spltStrng as $key){
                                                    $sglestng = str_replace(' ', '', $key);
                                                    $aGen[] = $sglestng;
                                                    $qure4 = "SELECT GeneID FROM entrez_id WHERE Symbol = '$sglestng'";
                                                    $res8 = mysqli_query($conn, $qure4);
                                                    $GenID="";

                                                    while($row8 = mysqli_fetch_array($res8)){
                                                        $GenID = $row8["GeneID"];
                                                    }

                                                    if($GenID !="") {
                                                        $hypstrg[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$sglestng</a>";
                                                    } else {
                                                        $hypstrg[] = $sglestng;
                                                    }
                                                }
                                                echo implode(', ', $hypstrg);
                                            }else{
                                                $sglestng = $qmad_gen[$j];
                                                $aGen[] = $sglestng;
                                                $qure3 = "SELECT GeneID FROM entrez_id WHERE Symbol = '$sglestng'";
                                                $res8 = mysqli_query($conn, $qure3);
                                                $GenID="";
                                                
                                                while($row8 = mysqli_fetch_array($res8)){
                                                    $GenID = $row8["GeneID"];
                                                }

                                                if($GenID !="") {
                                                    echo "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$sglestng</a>";
                                                } else {
                                                    echo $sglestng;
                                                }
                                            }
                                        } else {
                                            echo " - ";
                                        }
                                    ?>
                                </td>
                                <?php
                                    if($preVal_Chkbx == $stu_Acc[$j]){
                                        if($preValCnt_Stu != 0){
                                            $snpID[] = $qsnp_id[$j];
                                            $chrID[] = $qchr_id[$j];
                                            $chrPos[] = $qchr_pos[$j];
                                            $pval[] = $qpval[$j];
                                            $cntxt[] = $qcntxt[$j];
                                            $mad_gen[] = $qmad_gen[$j];
                                            ?>
                                                <script>
                                                    var td_Stu_ID = <?= $tdCnt_Stu; ?>;
                                                    var smtd_Stu_Cnt = <?= $preValCnt_Stu; ?>;
                                                    $(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).attr('rowspan', smtd_Stu_Cnt + 1);
                                                </script>
                                            <?php

                                            if($j == count($qsnp_id) - 1){
                                                $chkVal  = array();
                                                for($r=0; $r < sizeof($snpID); $r++){
                                                    $chkVal[] = $snpID[$r].'**'.$chrID[$r].'**'.$chrPos[$r].'**'.$pval[$r].'**'.$cntxt[$r].'**'.$mad_gen[$r];
                                                }
                                                $chkVal_Join = json_encode(implode(";", $chkVal));
                                                if(sizeof($snpID) != 1){
                                                    ?>
                                                        <script>
                                                            var chkVal_ap = <?= $chkVal_Join; ?>;
                                                            $(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val($(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val() + chkVal_ap);
                                                        </script>
                                                    <?php
                                                }else{
                                                    ?>
                                                        <script>
                                                            var chkVal_ap = <?= json_encode($chkVal[0]); ?>;
                                                            $(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val($(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val() + chkVal_ap);
                                                        </script>
                                                    <?php
                                                }
                                            }
                                        }
                                    }else{
                                        if(!empty($snpID)){
                                            $chkVal  = array();
                                            for($r=0; $r < sizeof($snpID); $r++){
                                                $chkVal[] = $snpID[$r].'**'.$chrID[$r].'**'.$chrPos[$r].'**'.$pval[$r].'**'.$cntxt[$r].'**'.$mad_gen[$r];
                                            }
                                            $chkVal_Join = json_encode(implode(";", $chkVal));
                                            if(sizeof($snpID) != 1){
                                                ?>
                                                    <script>
                                                        var chkVal_ap = <?= $chkVal_Join; ?>;
                                                        $(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val($(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val() + chkVal_ap);
                                                    </script>
                                                <?php
                                            }else{
                                                ?>
                                                    <script>
                                                        var chkVal_ap = <?= json_encode($chkVal[0]); ?>;
                                                        $(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val($(`#td_Chkbx_${td_dis_ID}_${td_Stu_ID - 1}`).children('input').val() + chkVal_ap);
                                                    </script>
                                                <?php
                                            }
                                        }

                                        $preVal_Chkbx = $stu_Acc[$j];
                                        ?>
                                            <td width="1%" valign="top" id="td_Chkbx_<?= $i.'_'.$tdCnt_Chkbx; ?>" class="lastTD">
                                                <input type="checkbox" class="checkbox" value="<?= $stu_Acc[$j].'**'.$qsnp_id[$j].'**'.$qchr_id[$j].'**'.$qchr_pos[$j].'**'.$qpval[$j].'**'.$qcntxt[$j].'**'.$qmad_gen[$j].';'; ?>" />
                                            </td>
                                        <?php
                                        $preValCnt_Stu = 0;
                                        $snpID = $chrID = $chrPos = $pval = $cntxt = $mad_gen  = array();
                                        $tdCnt_Chkbx++;
                                    }
                                ?>
                            </tr>
                        <?php
                    }
                } 
                   
            } 
        ?>
        </tbody>
    </table>
    </div>

    <script>
$('#sortTable').DataTable();
</script>
        
        <table width="100%" border="0" align="center" cellpadding="6" cellspacing="0" style="margin-top: 10px;">
            <tr>
                <td width="30%">
                    <label><strong>QTL Analysis:</strong></label>
                    <div style="padding-top: 12px; padding-left: 10px;">
                        <div>
                            <input type="checkbox" name="cdngChk" id="c_snp" value="Coding">
                            <label>Coding SNPs (Mapped Genes + QTL Analysis)</label>
                        </div>
                        &nbsp;
                        <div>
                            <input type="checkbox" name="ncdngChk" id="nc_snp" value="Non-coding">
                            <label>Non-coding SNPs (QTL Analysis)</label>
                        </div>
                    </div>
                </td>
                <td>
                    <div id="qtlSelect" class="sel_tiss">
                        <select class="select_TissCls" name="tissue[]" id="select_TissID" multiple="multiple"
                            style="width:640px;" data-placeholder="Select Tissue">
                            <option></option>
                          <option value="adipose">Adipose</option>
<option value="adipose-subcutaneous">Adipose-subcutaneous</option>
<option value="adipose-visceral">Adipose-visceral</option>
<option value="adrenal_gland">Adrenal gland</option>
<option value="artery">Artery</option>
<option value="artery-aorta">Artery-aorta</option>
<option value="artery-coronary">Artery-coronary</option>
<option value="artery-tibial">Artery-tibial</option>
<option value="bladder">Bladder</option>
<option value="blood">Blood</option>
<option value="blood-b_cell">Blood-b_cell</option>
<option value="blood-b_cell_cd19+">Blood-b cell cd19+</option>
<option value="blood-erythroid">Blood-erythroid</option>
<option value="blood-macrophage">Blood-macrophage</option>
<option value="blood-monocyte">Blood-monocyte</option>
<option value="blood-monocytes_cd14+">Blood-monocytes cd14+</option>
<option value="blood-natural_killer_cell">Blood-natural killer cell</option>
<option value="blood-neutrophils_cd16+">Blood-neutrophils cd16+</option>
<option value="blood-t_cell">Blood-t cell</option>
<option value="blood-t_cell_cd4+">Blood-t cell cd4+</option>
<option value="blood-t_cell_cd4+_activated">Blood-t cell cd4+ activated</option>
<option value="blood-t_cell_cd4+_naive">Blood-t cell cd4+ naive</option>
<option value="blood-t_cell_cd8+">Blood-t cell cd8+</option>
<option value="blood-t_cell_cd8+_activated">Blood-t cell cd8+ activated</option>
<option value="blood-t_cell_cd8+_naive">Blood-t cell cd8+ naive</option>
<option value="bone">Bone</option>
<option value="brain">Brain</option>
<option value="brain-amygdala">Brain-amygdala</option>
<option value="brain-anterior_cingulate_cortex">Brain-anterior cingulate cortex</option>
<option value="brain-caudate">Brain-caudate</option>
<option value="brain-cerebellar_hemisphere">Brain-cerebellar hemisphere</option>
<option value="brain-cerebellum">Brain-cerebellum</option>
<option value="brain-cortex">Brain-cortex</option>
<option value="brain-frontal_cortex">Brain-frontal cortex</option>
<option value="brain-hippocampus">Brain-hippocampus</option>
<option value="brain-hypothalamus">Brain-hypothalamus</option>
<option value="brain-nucleus_accumbens">Brain-nucleus accumbens</option>
<option value="brain-pons">Brain-pons</option>
<option value="brain-prefrontal_cortex">Brain-prefrontal cortex</option>
<option value="brain-putamen">Brain-putamen</option>
<option value="brain-spinal_cord">Brain-spinal cord</option>
<option value="brain-substantia_nigra">Brain-substantia nigra</option>
<option value="brain-temporal_cortex">Brain-temporal cortex</option>
<option value="breast">Breast</option>
<option value="cartilage">Cartilage</option>
<option value="central_nervous_system">Central nervous system</option>
<option value="cervix">Cervix</option>
<option value="dendritic_cells">Dendritic cells</option>
<option value="epithelium">Epithelium</option>
<option value="esophagus">Esophagus</option>
<option value="eye">Eye</option>
<option value="fibroblast">Fibroblast</option>
<option value="gallbladder">Gallbladder</option>
<option value="heart">Heart</option>
<option value="heart-atrial_appendage">Heart-atrial appendage</option>
<option value="heart-left_ventricle">Heart-left ventricle</option>
<option value="kidney">Kidney</option>
<option value="large_intestine">Large intestine</option>
<option value="large_intestine-colon">Large intestine-colon</option>
<option value="large_intestine-rectum">Large intestine-rectum</option>
<option value="liver">Liver</option>
<option value="lung">Lung</option>
<option value="lymphocyte">Lymphocyte</option>
<option value="minor_salivary_gland">Minor salivary gland</option>
<option value="mouth-saliva">Mouth-saliva</option>
<option value="mouth-sputum">Mouth-sputum</option>
<option value="muscle">Muscle</option>
<option value="muscle-skeletal">Muscle-skeletal</option>
<option value="muscle-smooth">Muscle-smooth</option>
<option value="ovary">Ovary</option>
<option value="pancreas">Pancreas</option>
<option value="peripheral_nervous_system">Peripheral nervous system</option>
<option value="placenta">Placenta</option>
<option value="prostate">Prostate</option>
<option value="skin">Skin</option>
<option value="small_intestine">Small intestine</option>
<option value="small_intestine-duodenum">Small intestine-duodenum</option>
<option value="small_intestine-ileum">Small intestine-ileum</option>
<option value="spleen">Spleen</option>
<option value="stem_cell-ipsc">Stem cell-ipsc</option>
<option value="stomach">Stomach</option>
<option value="testis">Testis</option>
<option value="thymus">Thymus</option>
<option value="thyroid_gland">Thyroid gland</option>
<option value="uterus">Uterus</option>
<option value="vagina">Vagina</option>
                        </select>
                        <input type="button" id="addAll" value="Add all" style="margin: 0px 5px;" class="allBtn">
                        <input type="button" id="resetAll" value="Clear all" class="allBtn">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 12px;">
                    <table width="48%" border="0" align="center" cellpadding="4" cellspacing="0">
                        <tr>
                            <td>
                                <input type="hidden" name="sel_rs" id="sel_rs">
                                <input type="submit" value="Submit" onclick="return getData();" />
                            </td>
                            <td>
                                <div align="center">
                                    <input type="submit" id="button2" onclick="location.href='gwas.php';"
                                        value="Back" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php }
	else
	{
		header('Location:gwas.php');
	}?>
    </td>
  </tr>
</table>
    <div align="center">
        <div id='loadingmsg' style='display: none;'>
            <div align="center">Processing, please wait......</div>
        </div>
        <div id='loadingover' style='display: none;'></div>
        <p></p>
    </div>
</div>

<script>
    $('#select_TissID').select2();
    $('#addAll').click(function () {
        $("#select_TissID > option:not(:first-child)").prop("selected", true);
        $("#select_TissID").trigger("change");
    });

    $('#resetAll').click(function () {
        $("#select_TissID").val(0).trigger('change.select2')
    });
    function getData() {
        if ($(".checkbox").is(":checked")) {
            var selctDis = $('.dslyTble input:checked').map(function () {
                return $(this).val();
            }).get().join([separator = '***']);
            $('#sel_rs').val(selctDis);

            if($('#c_snp').is(':checked') || $('#nc_snp').is(':checked')){
                if($('#select_TissID').val() != ''){
                    
					return true;
					
                }else{
                    alert('Select tissue');
                    return false;
                }
            }else{
                alert('Select the Type of SNP');
                return false;
            }
        } else {
            alert("Select the disease before submit")
            return false;
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

        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
    }

    function tblDnld(){
        generateExcel($("#dslyTble"));
    }
</script>

<!-- Footer (Don't include anywhere else) -->
<?php
    include('footer.php');
?>