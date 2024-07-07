<!-- CSS include -->
<link rel="stylesheet" href="css/bootstrap_4.0.css">
<link rel="stylesheet" href="css/dataTables.min.css">
<link rel="stylesheet" href="css/buttons.dataTables.min.css">

<!-- JS include -->
<script src="js/jquery_3.2.1.js"></script>
<script src="js/canvasjs.min.js"></script>
<script src="js/dataTables.min.js"></script>
<script src="js/dataTables.buttons.min.js"></script>
<script src="js/buttons.html5.min.js"></script>
<script src="js/highcharts.js"></script>
<script src="js/exporting.js"></script>

<?php
    session_start();
    ini_set('max_execution_time', '3600'); //3600 seconds = 1 hour
    header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure");
    // error_reporting(0);
    include "connect.php";

    function combi($n, $r) {
        if ($r > $n) {
            return null;
        }

        if ($n - $r < $r) {
            return combi($n, $n - $r);
        }

        $solu=1;

        for ($i=0; $i < $r; $i++) {
            $solu *=($n - $i) / ($i + 1);
        }

        return $solu;
    }

    function combin($N, $R) {
        $C=1;

        for ($i=0; $i < $N-$R; $i++) {
            $C=bcdiv(bcmul($C, $N-$i), $i+1);
        }

        return $C;
    }

    if(isset($_POST['type_Select'])){
        $selDis = explode("; ", $_POST['selDis']);

        $q1 = "select count(DISTINCT geneSymbol) from disease_enrich";
        $q1r = mysqli_query($conn, $q1);
        $popu = mysqli_fetch_array($q1r);

        $selDis = explode("; ", $_POST['selDis']);
        if($selDis[0] == 'on'){
            array_shift($selDis);
        }

        if(!empty($selDis)){
            foreach($selDis as $key){
                $DisGen = explode("***", $key);
                if(!empty($DisGen[2])){
                    $genArr = explode(",", $DisGen[2]);
                    foreach($genArr as $genStr){
                        $gen[] = $genStr;
                    }
                }
            }
            $dq = implode(',', $gen);
            $genes = explode(',',$dq);
            $gc = count($genes);
            $rep = "\" OR geneSymbol = \"";
            $ser = array(","," ,",", ","\n");
            $qur= str_replace($ser,$rep,$dq);
            $qur="geneSymbol = \"".$qur."\"";
            $repp = ", ";
            $dq = str_replace($ser,$repp,$dq);
            $q1 = "select count(DISTINCT geneSymbol) from disease_enrich";
            $q1r = mysqli_query($conn, $q1);
            $popu = mysqli_fetch_array($q1r);
            $pop=$popu[0];
            $NCn=combin($pop, $gc);

            if ($gc <="20") {
                $q2="select DISTINCT disease_merge from disease_enrich where ( $qur )";
            }elseif (($gc > "20") && ($gc <="120")) {
                $q2="select DISTINCT disease_merge from disease_enrich where ( $qur ) GROUP BY disease_merge, geneSymbol HAVING COUNT(geneSymbol) > 3";
            }elseif (($gc > "120") && ($gc <="300")) {
                $q2="select DISTINCT disease_merge from disease_enrich where ( $qur ) GROUP BY disease_merge, geneSymbol HAVING COUNT(geneSymbol) > 4";
            }else {
                $q2="select DISTINCT disease_merge from disease_enrich where ( $qur ) GROUP BY disease_merge, geneSymbol HAVING COUNT(geneSymbol) > 5";
            }

            $q22 = mysqli_query($conn, $q2);

            while($row=mysqli_fetch_array($q22)) {
                $path=$row["disease_merge"];
                $nampat[]=$path;
                $q3="select DISTINCT geneSymbol from disease_enrich where disease_merge = '$path'";
                $q33=mysqli_query($conn, $q3);
                $q3r=mysqli_num_rows($q33);
                $pathwgc=$q3r;
                $a=0;

                while($rowp=mysqli_fetch_array($q33)) {
                    $gp=$rowp["geneSymbol"];
                    $gp=trim($gp);
                    $pathg[$a]=strtoupper ($gp);
                    $a++;
                }

                $xaaw=array_intersect($pathg, $genes);
                $pig[]=$xaaw;
                $gs_pc=count($xaaw);
                $o=count($xaaw)/count($pathg);
                if($o=="NAN") {
                    $o="0";
                }

                $pover[]=$o;
                $pg[]=$pathg;
                $pathg=array();

                $KCk=combin($pathwgc, $gs_pc);
                $NKCnk=combin($pop-$pathwgc, $gc-$gs_pc);
                $hy_por=bcdiv(bcmul($KCk, $NKCnk), $NCn, 50);
                $hy_prob[]=$hy_por;
            }
            array_multisort($hy_prob, $nampat, $pover, $pig);
        }
    }else{
        echo "<script> alert('Something went wrong'); return false; </script>";
    }

    // echo "<pre>";
    // print_r($pig);
    // echo "</pre>";

    include('header.php')
?>
<script type="text/javascript">
    window.onload = function () {
        var chart1 = new CanvasJS.Chart("chartContainer1", {
        animationEnabled: true,
        zoomEnabled: true,
        toolTip:{
            enabled: true,       //disable here
            animationEnabled: true //disable here
        },
        axisX:{
            interval: 1,
            labelFontSize: 12,
            labelFontColor: "black",
        },
        axisY2:{
            gridColor: "rgba(1,77,101,.1)",
            title: "Gene overlap ratio",
            titleFontFamily: "verdana",
            titleFontSize: 12,
            labelFontSize: 12,
            minimum: 0,
        },
        data: [{
            type: "bar",
            name: "Diseases",
            axisYType: "secondary",
            dataPointWidth: 50,
            indexLabelFontSize: 12,
            indexLabelFontColor: "black",

            dataPoints: [ 
                <?php
                    $cole = array("#4682b4", "#4f97a3", "#73c2f3", "#b0dfe5", "#81d8d0");
                    $c = 0;
                    $maxx = 0.05;
                    $minn = min($hy_prob);
                    $diff = ($maxx - $minn) / 6;
                    $dfa1 = $minn + $diff;
                    $dfa2 = $minn + $diff + $diff;
                    $dfa3 = $minn + $diff + $diff + $diff;
                    $dfa4 = $minn + $diff + $diff + $diff + $diff;
                    $dfa5 = $minn + $diff + $diff + $diff + $diff + $diff;
                    $ai = 0;
                    $patkca = count($nampat);
                    if ($patkca > 30) {
                        $patkc = 30;
                    } else {
                        $patkc = $patkca;
                    }
                    $cc = $patkc - 1;
                    $cja = 0;
                    for ($i = 0; $i < $patkca; $i++) {
                        $pval = $hy_prob[$i];
                        $pavver = $pover[$i];
                        if ($pval > $dfa5) {
                            $ai = 4;
                        } elseif ($pval > $dfa4) {
                            $ai = 3;
                        } elseif($pval > $dfa3) {
                            $ai = 2;
                        } elseif($pval > $dfa2) {
                            $ai = 1;
                        } else {
                            $ai = 0;
                        }
                        $pavver = round($pavver, 4);
                        $pval = sprintf("%0.2E", $pval);
                        if ($cja < $patkc) {
                            if (($c == $cc) && ($hy_prob[$i] < 0.05)) {
                                echo "{y: $pavver, label: \"$nampat[$i]\", indexLabel: \"P value: $pval\", color: \"$cole[$ai]\"}";
                                $cja++;
                            }
                            if (($c < $cc) && ($hy_prob[$i] < 0.05)) {
                                echo "{y: $pavver, label: \"$nampat[$i]\", indexLabel: \"P value: $pval\", color: \"$cole[$ai]\"},";
                                $cja++;
                            }
                        }
                        $c++;
                        $ai++;
                    }
                ?>
            ]
        }]
	});
	chart1.render();
}
</script>

<style>
    .mHead {
        display: block;
        font-size: 1.5em;
        margin-block-start: 0.83em;
        margin-block-end: 0.83em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        text-align: center;
        font-weight: bold;
    }
    
    .dataTables_length{
        margin-left: 112px;
    }

    button.dt-button:first-child, div.dt-button:first-child, a.dt-button:first-child, input.dt-button:first-child{
        padding: 3px 10px;
        margin-left: 10px;
    }

    .dataTables_filter{
        margin-bottom: 5px;
        margin-right: 112px;
    }

    .dataTables_info{
        margin-top: 10px;
        margin-left: 112px;
    }

    .dataTables_paginate{
        margin-top: 7px;
        margin-right: 102px;
    }
</style>

<table width="98%" border="0" cellspacing="0" cellpadding="6" align="center">
    <tr>
        <td>
            <h2 class='mHead' align="center">Disease enrichment</h2>
            <blockquote>
                <p><strong>Input gene symbols:</strong></p>
                <p align="justify">
                    <?php 
                        $si=0;
                        foreach($genes as $gse) {
                            if($si==0) {
                                $gsaa="<a href=GgeneT.php?gens=$gse target=_blank>$gse</a>";
                            }else {
                                $gsaa="$gsaa, <a href=GgeneT.php?gens=$gse target=_blank>$gse</a>";
                            }
                            $si++;
                        }
                        echo $gsaa; 
                    ?>&nbsp;
                </p>
            </blockquote>
            <div id="chartContainer1" style="height: 900px; width: 100%;"></div>
            <p align="center"><strong>P Value</strong></p>
            <table border="0" align="center" cellpadding="3" cellspacing="0" width="80%">
                <tr height="30px">
                    <td width="25%">&nbsp;</td>
                    <td width="20px" bgcolor="<?php echo $cole[0]; ?>">&nbsp;</td>
                    <td width="120px">Less than <?php echo sprintf("%0.2E", $dfa2); ?></td>
                    <td width="20px" bgcolor="<?php echo $cole[1]; ?>">&nbsp;</td>
                    <td width="120px"><?php echo sprintf("%0.2E", $dfa2); ?> to <?php echo sprintf("%0.2E", $dfa3); ?>
                    </td>
                    <td width="20px" bgcolor="<?php echo $cole[2]; ?>">&nbsp;</td>
                    <td width="120px"><?php echo sprintf("%0.2E", $dfa3); ?> to <?php echo sprintf("%0.2E", $dfa4); ?>
                    </td>
                    <td width="20px" bgcolor="<?php echo $cole[3]; ?>">&nbsp;</td>
                    <td width="120px"><?php echo sprintf("%0.2E", $dfa4); ?> to <?php echo sprintf("%0.2E", $dfa5); ?>
                    </td>
                    <td width="20px" bgcolor="<?php echo $cole[4]; ?>">&nbsp;</td>
                    <td width="120px">greater than <?php echo sprintf("%0.2E", $dfa5); ?></td>
                </tr>
            </table>
            <form id="myForm" name="myForm" method="post" action="network/polyphar_dis_en.php" onSubmit="return validate();">
                <p>&nbsp;</p>
                <table width="80%" border="1" cellspacing="0" cellpadding="4" align="center" style="margin: 0 auto;" class="disen_Tble">
                    <thead>
                        <tr>
                            <td><strong>Disease name</strong></td>
                            <td><strong>Genes</strong></td>
                            <td><strong>Overlap ratio</strong></td>
                            <td><strong>Hypergeometric probability (p-value)</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $b=0;
                            foreach($nampat as $fp){	
                                $hyp =$hy_prob[$b];
                                $pathgg = $pg[$b]; #all pathway genes
                                $pgg=implode(",",$pathgg);
                                $pingg = $pig[$b]; #intersecting genes
                                $pigg=implode(",",$pingg);
                                $orp = $pover[$b];
                                $orp = round($orp,3);
                                if($hyp < 0.05){ 
                                    $hyp = sprintf("%0.2E",$hyp); 
                                    ?>
                                        <tr>
                                            <td><?php echo $fp;?></td>
                                            <td> <?php echo str_replace(',',', ',$pigg);?></td>
                                            <td> <?php echo $orp; ?></td>
                                            <td><?php echo $hyp; ?></td>
                                        </tr>
                                    <?php
                                }
                                $b++;
                            }
                        ?>
                    </tbody>
                </table>
                <p></p>
                <div align="center">
                    <input type="submit" id="button2" onclick="location.href='gwas.php';" value="Back to main page" />
                </div>
            </form>
            <p>&nbsp;</p>
        </td>
    </tr>
</table>

<script>
    $('.disen_Tble').DataTable({
        dom: 'lBfrtip',
        "pageLength": 10,
        buttons: [{
            extend: 'csv',
            title: 'Disease enrichment',
            text: 'Downlaod'
        }]
    });
</script>

<?php
    include('footer.php')
?>
