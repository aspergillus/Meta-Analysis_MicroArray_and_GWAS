<!-- CSS include -->
<link rel="stylesheet" href="css/bootstrap_4.0.css">
<link rel="stylesheet" href="css/dataTables.min.css">

<!-- JS include -->
<script src="js/jquery_3.2.1.js"></script>
<script src="js/dataTables.min.js"></script>
<script src="js/highcharts.js"></script>
<script src="js/exporting.js"></script>

<?php
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
        if($_POST['type_Select'] == 'path_Enrich'){

            // echo $_POST['selDis'];
            
            $q1 = mysqli_query($conn, "select count(DISTINCT geneSymbol) from reactome");                                        // Gene count from database
            $q1_R = mysqli_fetch_array($q1);
            $geneLiC = $q1_R[0];

            $selDis = explode("; ", $_POST['selDis']);

            if(!empty($selDis)){
                $l = 0;
                foreach($selDis as $key){
                    $DisGen = explode("***", $key);
                    if(!empty($DisGen[1])){
                        $sDis[$l] = $DisGen[0];
                        $gen = explode(", ", $DisGen[1]);
                        $NCnr = combin($geneLiC, count($gen));                             // database_gene from reactome / selected_gene from disease

                        $pathL = array();
                        foreach($gen as $kGe){
                            $q2 = mysqli_query($conn, "SELECT pathwayname FROM reactome WHERE geneSymbol = '$kGe'");
                            while($row = mysqli_fetch_array($q2)){
                                array_push($pathL, $row['pathwayname']);
                            }
                        }

                        // echo "<pre>";
                        //     print_r($hy_prob);
                        //     echo "</pre>";

                        if(!empty($pathL)){
                            $n=0;
                            foreach(array_unique($pathL) as $kPath){
                                $escap_kPath = str_replace("'", "\'", $kPath);
                                $q3 = mysqli_query($conn, "select DISTINCT geneSymbol from reactome where pathwayname = '$escap_kPath'");
                                $q3rr = mysqli_num_rows($q3);
                                $pathwgcr = $q3rr;

                                $c=0;
                                while($rowG=mysqli_fetch_array($q3)) {
                                    $gpr = $rowG["geneSymbol"];
                                    $gpr = trim($gpr);
                                    $pathgr[$c] = $gpr;
                                    $c++;
                                }

                                $xaawr = array_intersect($pathgr, $gen);

                                if (count($xaawr) > 0) {
                                    $pigr[$l][$n] = $xaawr;                                                  // pigr is the Genes
                                    $poverr[$l][$n] = (float)bcdiv(count($xaawr) / count($pathgr), 1, 4);    // poverr is the Overlap ratio
                                    $nampatr[$l][$n] = $kPath;                                               // nampatr is the pathway 
                                    $pathgr = array();
                                    $gs_pcr = count($xaawr);
                                    $KCkr = combin($pathwgcr, $gs_pcr);
                                    $NKCnkr = combin($geneLiC - $pathwgcr, count($gen) - $gs_pcr);
                                    $hy_porr = bcdiv(bcmul($KCkr, $NKCnkr), $NCnr, 20);
                                    $hy_probr[$l][$n] = (float)bcdiv($hy_porr, 1, 4);                         // hy_probr is the Hypergeometric probability (p-value)
                                }
                                $n++;
                            }

                            for($y=0, $r=0; $y<sizeof($hy_probr[$l]); $y++){
                                if($hy_probr[$l][$y] < 0.05){
                                    $hy_prob[$l][$r] =  $hy_probr[$l][$y];
                                    $nampat[$l][$r] = $nampatr[$l][$y];
                                    $pig[$l][$r] = $pigr[$l][$y];
                                    $pover[$l][$r] = $poverr[$l][$y];
                                    $r++;
                                }
                            }

                            if(!empty($hy_prob)){
                                array_multisort($hy_prob[$l], $nampat[$l], $pig[$l], $pover[$l]);
                                $barP[$l][] = array('xlbl'=> $nampat[$l], 'data'=> $hy_prob[$l]);
                            }
                            $l++;
                        }

                    }
                }
            }
        }elseif($_POST['type_Select'] == 'gene_Onto'){
            $q4 = mysqli_query($conn, "SELECT COUNT(DISTINCT biolo_proc_gene) from enrichr");                                        // Gene count from database
            $q4_R = mysqli_fetch_array($q4);
            $geneLiC = $q4_R[0];

            $selDis = explode("; ", $_POST['selDis']);
            if(!empty($selDis)){
                $l = 0;
                foreach($selDis as $key){
                    $DisGen = explode("***", $key);

                    if(!empty($DisGen[1])){
                        $sDis[$l] = $DisGen[0];
                        $gen = explode(", ", $DisGen[1]);
                        $NCnr = combin($geneLiC, count($gen));                             // database_gene from enrichr / selected_gene from disease

                        $bioloL = array();
                        foreach($gen as $kGe){
                            $q5 = mysqli_query($conn, "SELECT biological_process from enrichr WHERE biolo_proc_gene = '$kGe'");
                            while($row2 = mysqli_fetch_array($q5)){
                                array_push($bioloL, $row2['biological_process']);
                            }
                        }

                        if(!empty($bioloL)){
                            $n=0;
                            foreach(array_unique($bioloL) as $bioK){
                                $new_bioK = str_replace("'", "\'", $bioK);
                                $q6 = mysqli_query($conn, "SELECT biolo_proc_gene from enrichr WHERE biological_process = '$new_bioK'");
                                $q6rr = mysqli_num_rows($q6);
                                $genL = $q6rr;

                                $c=0;
                                while($rowG2 = mysqli_fetch_array($q6)) {
                                    $bioG = $rowG2["biolo_proc_gene"];
                                    $bioG = trim($bioG);
                                    $bioProsGen[$c] = $bioG;
                                    $c++;
                                }

                                $fndGen = array_intersect($bioProsGen, $gen);

                                if (count($fndGen) > 0) {
                                    $bGenR[$l][$n] = $fndGen;                                               // bGen is the Genes responsible for the bioogical process
                                    
                                    // bioPro is the biological process 
                                    if(preg_match("/.*\(([^)]*)\)/", $bioK)){
                                        $brkStr = explode('(GO:', $bioK);
                                        $brkStr2 = explode(')', $brkStr[1])[0];
                                        $hypLnkStr = "<a href=https://www.ebi.ac.uk/QuickGO/term/GO:$brkStr2 target=_blank>$brkStr2</a>";
                                        $bioProR[$l][$n] = $brkStr[0]." (GO: ".$hypLnkStr.")<br>";
                                    } else {
                                        $bioProR[$l][$n] = $bioK;
                                    }
                                    
                                    $bioProsGen = array();
                                    $fndGenC = count($fndGen);
                                    $kCkr = combin($genL, $fndGenC);
                                    $NKCnkr = combin($geneLiC - $genL, count($gen) - $fndGenC);
                                    $pVal = bcdiv(bcmul($kCkr, $NKCnkr), $NCnr, 20);
                                    $pValueR[$l][$n] = (float)bcdiv($pVal, 1, 4);                           // hy_probr is the Hypergeometric probability (p-value)
                                }
                                $n++;
                            }

                            for($y=0, $r=0; $y<sizeof($pValueR[$l]); $y++){
                                if($pValueR[$l][$y] < 0.05){
                                    $pValue[$l][$r] =  $pValueR[$l][$y];
                                    $bioPro[$l][$r] = $bioProR[$l][$y];
                                    $bGen[$l][$r] = $bGenR[$l][$y];

                                    $r++;
                                }
                            }

                            array_multisort($pValue[$l], $bioPro[$l], $bGen[$l]);
                            $barP[$l][] = array('xlbl'=> $bioPro[$l], 'data'=> $pValue[$l], 'pval'=>$pValue[$l]);
                            $l++;
                        }
                    }
                }
            }
        }
    }else{
        echo "<script> alert('Something went wrong'); return false; </script>";
    }

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
    <?php
        if($_POST['type_Select'] == 'path_Enrich'){
            ?>
                <h2 class='mHead' align="center">Enrichment analysis: Reactome pathway</h2>
                <?php
                    if(isset($nampat)){
                        for($i=0; $i<sizeof($sDis); $i++){
                            $DisGen = explode("***", $sDis[$i]);
                            if(!empty($nampat[$i])){
                                ?>
                                    <div class="d-flex mb-2 disDivH">
                                        <div class="mr-auto disTi"><?= $DisGen[0]; ?></div>
                                    </div>
                                    <div class='barP' id="barP_<?= $i; ?>"></div>
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
                                                for($j=0; $j<sizeof($nampat[$i]); $j++){
                                                    ?>
                                                        <tr>
                                                            <td><?= $nampat[$i][$j]; ?></td>
                                                            <td>
                                                                <?php 
                                                                    $hypGen = array();
                                                                    foreach($pig[$i][$j] as $keyG){
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
                                                            <td><?= $pover[$i][$j]; ?></td>
                                                            <td><?= $hy_prob[$i][$j]; ?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <p>&nbsp;</p>
                                <?php
                            }
                        }
                    } else {
                        echo "<h6 style='text-align: center;'>No enriched pathway has been found for the selected disease</h6><p></p>";
                    }
                ?>
            <?php
        }else{
            ?>
               <h2 class='mHead' align="center">Gene Ontology enrichment</h2>
               <?php
                    if(isset($bioPro)){
                        for($i=0; $i<sizeof($sDis); $i++){
                            $DisGen = explode("***", $sDis[$i]);
                            if(!empty($bioPro[$i])){
                                ?>
                                    <div class="d-flex mb-2 disDivH">
                                        <div class="mr-auto disTi"><?= $DisGen[0]; ?></div>
                                    </div>
                                    <div class='barP' id="barP_<?= $i; ?>"></div>
                                    <table width="80%" border="1" cellspacing="0" cellpadding="4" bordercolor="#B99C6B" style="margin: 0 auto;" class="enrichTble">
                                        <thead> 
                                            <tr>
                                                <td><strong>Biological process</strong></td>
                                                <td><strong>Genes</strong></td>
                                                <td><strong>Hypergeometric probability (p-value)</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                for($j=0; $j<sizeof($bioPro[$i]); $j++){
                                                    ?>
                                                        <tr>
                                                            <td><?= $bioPro[$i][$j]; ?></td>
                                                            <td>
                                                                <?php 
                                                                    $hypGen = array();
                                                                    foreach($bGen[$i][$j] as $keyG){
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
                                                            <td><?= $pValue[$i][$j]; ?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <p>&nbsp;</p>
                                <?php
                            }
                        }
                    } else {
                        echo "<h6 style='text-align: center;'>No biological process has been found for the selected disease</h6><p></p>";
                    }
                ?>
            <?php
        }
    ?>
    <div align="center">
        <input type="submit" id="button2" onclick="location.href='gwas.php';" value="Back to main page" />
        <input type="hidden" id='hiddenAxisLebl' value='<?php
            if($_POST['type_Select']=='path_Enrich') {
                echo "<b>Enriched pathways</b>";
            } else {
                echo "<b>Biological process</b>";
            }
        ?>' >
    </div>
    <p>&nbsp;</p>
</div>

<script>
    $('.pathEnrichTble, .enrichTble').DataTable({
        "pageLength": 25
    });

    var xAxTitle = $('#hiddenAxisLebl').val();
    var barJSON = <?= json_encode($barP); ?>;
    for (var i = 0; i < <?= sizeof($barP) ?>; i++) {
        bJSON = barJSON[i][0];
        var bDiv = document.getElementById(`barP_${i}`);

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
                    text: xAxTitle,
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
                            color: 'white',
                        },
                    },

                },
            },

            series: [bJSON],

            tooltip: {
                formatter: function () {
                    return '<b>'+xAxTitle+':</b>' + this.key + '<br><b>P-value:</b> ' + this.y;
                }
            },

            exporting: {
                filename: 'Bar Plot'
            }
        });
    }
</script>


<?php
    include('footer.php');
?>