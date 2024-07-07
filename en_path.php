<!-- CSS include -->
<link rel="stylesheet" href="css/bootstrap_4.0.css">
<link rel="stylesheet" href="css/dataTables.min.css">
<link rel="stylesheet" href="css/buttons.dataTables.min.css">

<!-- JS include -->
<script src="js/jquery_3.2.1.js"></script>
<script src="js/dataTables.min.js"></script>
<script src="js/dataTables.buttons.min.js"></script>
<script src="js/buttons.html5.min.js"></script>
<script src="js/highcharts.js"></script>
<script src="js/exporting.js"></script>

<?php
    header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure");
    // error_reporting(0);
    include "connect.php";

    function combin($N, $R) {
        $C=1;

        for ($i=0; $i < $N-$R; $i++) {
            $C=bcdiv(bcmul($C, $N-$i), $i+1);
        }

        return $C;
    }

    function combi($n, $r) {
        if ($r > $n) {
            return null;
        }

        if ($n - $r < $r) {
            $c=combi($n, $n - $r);
            return $c;
        }

        $solu=1;

        for ($i=0; $i < $r; $i++) {
            $solu *=($n - $i) / ($i + 1);
        }

        return $solu;
    }

    if(isset($_POST['type_Select'])){
        $q1 = mysqli_query($conn, "SELECT DISTINCT Genes from new_reactome");                                                   // Gene count from database
        while($rowGeLst = mysqli_fetch_array($q1)) {
            $geArr = explode(',', $rowGeLst['Genes']);
            foreach($geArr as $geStr){
                $q1_R[] = trim($geStr);
            }
        }
        $geneLiC = sizeof($q1_R);
        //echo $_POST['selDis'];
        $selDis = explode("; ", $_POST['selDis']);
        if($selDis[0] == 'on'){
            array_shift($selDis);
        }
        if(!empty($selDis)){
            $l = 0;
            // $gen = [];
            foreach($selDis as $key){
                $DisGen = explode("***", $key);
                if(!empty($DisGen[2])){
                    $genArr = explode(",", $DisGen[2]);
                    foreach($genArr as $genStr){
                        $gen[] = $genStr;
                    }
                }
            }
            $gen = array_unique($gen);
			//echo implode(', ', $gen);
            $NCnr = combin($geneLiC, count($gen));                             // database_gene from reactome / selected_gene from disease
            $pathL = array();
            foreach($gen as $kGe){
                $q2 = mysqli_query($conn, "SELECT Original_pathway, Genes FROM `new_reactome` WHERE FIND_IN_SET('$kGe',Genes)");
                while($row = mysqli_fetch_array($q2)){
                    array_push($pathL, $row['Original_pathway']);
                }
            }

            if(!empty($pathL)){
                $n=0;
                foreach(array_unique($pathL) as $kPath){
                    $escap_kPath = str_replace("'", "\'", $kPath);
                    $q3 = mysqli_query($conn, "SELECT DISTINCT Genes FROM new_reactome WHERE Original_pathway = '$escap_kPath'");
                    $c=0;
                    while($rowG = mysqli_fetch_array($q3)) {
                        $gpr = $rowG["Genes"];
                        $gprArr = explode(',', $gpr);
                        $pathwgcr = sizeof($gprArr);
                        foreach($gprArr as $gprStr){
                            $pathgr[$c] = $gprStr;
                            $c++;
                        }
                    }

                    $xaawr = array_intersect($pathgr, $gen);

                    if (count($xaawr) > 0) {
                        $pigr[$n] = $xaawr;                                                  // pigr is the Genes
                        $poverr[$n] = (float)bcdiv(count($xaawr) / count($pathgr), 1, 4);    // poverr is the Overlap ratio
                        $nampatr[$n] = $kPath;                                               // nampatr is the pathway 
                        $pathgr = array();
                        $gs_pcr = count($xaawr);
                        $KCkr = combin($pathwgcr, $gs_pcr);
                        $NKCnkr = combin($geneLiC - $pathwgcr, count($gen) - $gs_pcr);
                        $hy_porr = bcdiv(bcmul($KCkr, $NKCnkr), $NCnr, 20);
                        $hy_probr[$n] = (float)bcdiv($hy_porr, 1, 4);                         // hy_probr is the Hypergeometric probability (p-value)
                    }
                    $n++;
                }

                for($y=0, $r=0; $y<sizeof($hy_probr); $y++){
                    if($hy_probr[$y] < 0.05){
                        $hy_prob[$r] =  $hy_probr[$y];
                        $nampat[$r] = $nampatr[$y];
                        $pig[$r] = $pigr[$y];
                        $pover[$r] = $poverr[$y];
                        $r++;
                    }
                }

                if(!empty($hy_prob)){
                    array_multisort($hy_prob, $nampat, $pig, $pover);
                    $barP[] = array('xlbl'=> $nampat, 'data'=> $hy_prob);
                }
            }
        }
    }else{
        echo "<script> alert('Something went wrong'); return false; </script>";
    }

    // echo "<pre>";
    // print_r($hy_prob);
    // echo "</pre>";

    include('header.php');
?>

<style>
    .disDivH{
        background: rgb(241, 232, 209);
        border: 1px solid rgb(185, 156, 107);
        border: 5px;
    }

    .disTi{
        align-self: center;
        font-weight: bold;
        margin-left: 6px;
    }

    .barP{
        width: 900px;
        height: 650px; 
        margin: 0 auto;
    }

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

<div>
    <h2 class='mHead' align="center">Enrichment analysis: Reactome pathway</h2>
    <?php
        if(!empty($nampat)){
            ?>
                <div class='barP' id="barP"></div>
                <table width="80%" border="1" cellspacing="0" cellpadding="4" bordercolor="#B99C6B" style="margin: 0 auto;" class="pathEnrichTble">
                    <thead>
                        <tr>
                            <td><strong>Pathway name</strong></td>
                            <td><strong>Genes</strong></td>
                            <td><strong>Overlap ratio</strong></td>
                            <td><strong>Hypergeometric probability (p-value)</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($j=0; $j<sizeof($nampat); $j++){
                                ?>
                                    <tr>
                                        <td><?= $nampat[$j]; ?></td>
                                        <td>
                                            <?php 
                                                $hypGen = array();
                                                foreach($pig[$j] as $keyG){
                                                    $q4 = mysqli_query($conn, "SELECT GeneID FROM entrez_id WHERE Symbol = '$keyG'");
                                                    while($rowG = mysqli_fetch_array($q4)){
                                                        $GenID = $rowG["GeneID"];
                                                        if($GenID != ''){
                                                            $hypGen[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$keyG</a>";
                                                        }else{
                                                            $hypGen[] = $keyG;
                                                        }
                                                    }
                                                }
                                                echo implode(', ', $hypGen);
                                            ?>
                                        </td>
                                        <td><?= $pover[$j]; ?></td>
                                        <td><?= $hy_prob[$j]; ?></td>
                                    </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                <p>&nbsp;</p>
            <?php
        } else {
            echo "<h6 style='text-align: center;'>No enriched pathway has been found for the selected disease</h6><p></p>";
        }
    ?>
    <div align="center">
        <input type="submit" id="button2" onclick="location.href='gwas.php';" value="Back to main page" />
    </div>
    <p>&nbsp;</p>
</div>

<script>
    $('.pathEnrichTble').DataTable({
        dom: 'lBfrtip',
        "pageLength": 10,
        buttons: [{
            extend: 'csv',
            title: 'Reactome pathways',
            text: 'Downlaod'
        }]
    });

    // var xAxTitle = $('#hiddenAxisLebl').val();
    var barJSON = <?= json_encode($barP); ?>;
    var bJSON = barJSON[0];
    var bDiv = document.getElementById(`barP`);

    function whiteSpacer(howMany) {
        var spaceString = '';

        while (howMany) {
            spaceString += '&nbsp';
            howMany--;
        }

        return spaceString;
    }
    Highcharts.chart(bDiv, {
        chart: {
            type: 'bar'
        },

        title: {
            useHTML: true,
            text: whiteSpacer(50)
        },

        credits: {
            enabled: false
        },

        legend: {
            enabled: false
        },

        xAxis: {
            categories: bJSON['xlbl'],
            title: {
                text: 'Enriched pathways',
                style: {
                    color: 'black',
                    fontSize: '14px',
                    fontFamily: 'Verdana, Geneva, sans-serif',
                    fontWeight: 'bold'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    fontFamily: 'Verdana, Geneva, sans-serif',
                    fill: 'black',
                    color: 'black'
                }
            },
            margin: 30
        },

        yAxis: {
            title: {
                text: '<b>P-value</b>',
                style: {
                    color: 'black',
                    fontSize: '14px',
                    fontFamily: 'Verdana, Geneva, sans-serif',
                    fontWeight: 'bold'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    fontFamily: 'Verdana, Geneva, sans-serif',
                    fill: 'black',
                    color: 'black'
                }
            },
            margin: 30
        },

        plotOptions: {
            series: {
                stacking: 'normal'
            },
            bar: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    formatter: function () {
                        var dlabel = 'P-value: ';
                        dlabel += this.y;
                        return dlabel
                    },
                    style: {
                        color: 'blank',
                    },
                },

            },
        },

        series: [bJSON],

        tooltip: {
            formatter: function () {
                return '<b>Enriched pathways:</b>' + this.key + '<br><b>P-value:</b> ' + this.y;
            }
        },

        exporting: {
            filename: 'Bar Plot'
        }
    });
</script>

<?php
    include('footer.php');
?>