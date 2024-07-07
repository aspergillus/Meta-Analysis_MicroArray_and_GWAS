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
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // error_reporting(0);
    ini_set('max_execution_time', '3600'); //3600 seconds = 1 hour
    header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure");
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

    $GO_file_name = array('biological_process', 'cellular_component', 'molecular_function');
    $GO_pathway = array('Biological_proc_pathways', 'Cellular_component_pathways', 'Molecular_func_pathways');
    $GO_genes = array('Biological_proc_genes', 'Cellular_comp_genes', 'Molecular_func_genes');
    $colorCombi = array('#9BBFE0', '#E8A09A', '#FBE29F');

    if(isset($_POST['type_Select'])){
        for($f=0; $f<sizeof($GO_file_name); $f++){
            $fStr = $GO_file_name[$f];
            $gStr = $GO_genes[$f];
            $q4 = mysqli_query($conn, "SELECT COUNT(DISTINCT $gStr) from $fStr");                                        // Gene count from database
            $q4_R = mysqli_fetch_array($q4);
            $geneLiC[] = $q4_R[0];
        }

        // $q4 = mysqli_query($conn, "SELECT COUNT(DISTINCT Cellular_comp_genes) from cellular_component");                                        // Gene count from database
        // $q4_R = mysqli_fetch_array($q4);
        // $geneLiC = $q4_R[0];

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
            $gen = array_unique($gen);
            for($f=0; $f<sizeof($GO_file_name); $f++){
                $fStr = $GO_file_name[$f];                                             // File name
                $gStr = $GO_genes[$f];                                                 // Genes
                $pStr = $GO_pathway[$f];                                               // Pathways 
            
                $NCnr = combin($geneLiC[$f], count($gen));                             // database_gene from biological_process / selected_gene from disease
                $bioloL = array();
                $bGenR = $bioProR = $pValueR = [];
                foreach($gen as $kGe){
                    $q5 = mysqli_query($conn, "SELECT $pStr from $fStr WHERE $gStr = '$kGe'");
                    while($row2 = mysqli_fetch_array($q5)){
                        array_push($bioloL, $row2[$pStr]);
                    }
                }

                if(!empty($bioloL)){
                    $n=0;
                    foreach(array_unique($bioloL) as $bioK){
                        $new_bioK = str_replace("'", "\'", $bioK);
                        $q6 = mysqli_query($conn, "SELECT $gStr from $fStr WHERE $pStr = '$new_bioK'");
                        $q6rr = mysqli_num_rows($q6);
                        $genL = $q6rr;

                        $c=0;
                        while($rowG2 = mysqli_fetch_array($q6)) {
                            $bioG = $rowG2[$gStr];
                            $bioG = trim($bioG);
                            $bioProsGen[$c] = $bioG;
                            $c++;
                        }

                        $fndGen = array_intersect($bioProsGen, $gen);

                        if (count($fndGen) > 0) {
                            $bGenR[$n] = $fndGen;                                               // bGen is the Genes responsible for the bioogical process
                            
                            // bioPro is the biological process 
                            if(preg_match("/.*\(([^)]*)\)/", $bioK)){
                                $brkStr = explode('(GO:', $bioK);
                                $brkStr2 = explode(')', $brkStr[1])[0];
                                $hypLnkStr = "<a href=https://www.ebi.ac.uk/QuickGO/term/GO:$brkStr2 target=_blank>$brkStr2</a>";
                                $bioProR[$n] = $brkStr[0]." (GO: ".$hypLnkStr.")<br>";
                            } else {
                                $bioProR[$n] = $bioK;
                            }
                            
                            $bioProsGen = array();
                            $fndGenC = count($fndGen);
                            $kCkr = combin($genL, $fndGenC);
                            $NKCnkr = combin($geneLiC[$f] - $genL, count($gen) - $fndGenC);
                            $pVal = bcdiv(bcmul($kCkr, $NKCnkr), $NCnr, 20);
                            $pValueR[$n] = (float)bcdiv($pVal, 1, 4);                           // hy_probr is the Hypergeometric probability (p-value)
                        }
                        $n++;
                    }

                    for($y=0; $y<sizeof($pValueR); $y++){
                        if($pValueR[$y] < 0.05){
                            $pValue[$f][] =  $pValueR[$y];
                            $bioPro[$f][] = $bioProR[$y];
                            $bGen[$f][] = $bGenR[$y];
                        }
                    }

                    array_multisort($pValue[$f], $bioPro[$f], $bGen[$f]);
                    $barP[$f][] = array('xlbl'=> $bioPro[$f], 'data'=> $pValue[$f], 'color' => $colorCombi[$f]);
                }
            }
        }
    }else{
        echo "<script> alert('Something went wrong'); return false; </script>";
    }

    // echo "<pre>";
    // print_r($pValue);
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

    .pieCht{
        width: 700px;
        height: 450px; 
        margin: 0 auto;
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
    <h2 class='mHead' align="center">Gene Ontology enrichment</h2>
    <div class='pieCht' id="pieCh"></div>
    <?php
        if(isset($bioPro)){
            $tbl_ttl = array('Biological process', 'Cellular component', 'Molecular function');
            $barID = array('bioP', 'cellC', 'molFun');
            $geEnrC = []; 
            for($f=0; $f<sizeof($GO_file_name); $f++){
                $geEnrC[$f] = sizeof($bioPro[$f]);
                ?>
                    <h3 class='mHead' align="center"><?= $tbl_ttl[$f]; ?></h3>
                    <div class='barP' id="<?= $barID[$f]; ?>"></div>
                    <table width="80%" border="1" cellspacing="0" cellpadding="4" bordercolor="#B99C6B" style="margin: 0 auto;" class="geOnto_tbl">
                        <thead> 
                            <tr>
                                <td><strong><?= $tbl_ttl[$f]; ?></strong></td>
                                <td><strong>Genes</strong></td>
                                <td><strong>Hypergeometric probability (p-value)</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                for($j=0; $j<sizeof($bioPro[$f]); $j++){
                                    ?>
                                        <tr>
                                            <td><?= $bioPro[$f][$j]; ?></td>
                                            <td>
                                                <?php 
                                                    $hypGen = array();
                                                    foreach($bGen[$f][$j] as $keyG){
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
                                            <td><?= $pValue[$f][$j]; ?></td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                    <p>&nbsp;</p>
                <?php
            }
        } else {
            echo "<h6 style='text-align: center;'>No biological process has been found for the selected disease</h6><p></p>";
        }
    ?>
    <div align="center">
        <input type="submit" id="button2" onclick="location.href='gwas.php';" value="Back to main page" />
    </div>
    <p>&nbsp;</p>
</div>

<script>
    $('.geOnto_tbl').DataTable({
        dom: 'lBfrtip',
        "pageLength": 10,
        buttons: [{
            extend: 'csv',
            title: 'Gene ontology',
            text: 'Downlaod'
        }]
    });

    var pieData = <?= json_encode($geEnrC); ?>;
    var barJSON = <?= json_encode($barP); ?>;
    var barID = <?= json_encode($barID); ?>;
    var bar_title = <?= json_encode($tbl_ttl); ?>;

    function whiteSpacer(howMany) {
        var spaceString = '';

        while (howMany) {
            spaceString += '&nbsp';
            howMany--;
        }

        return spaceString;
    }

    function pieChart() {
        Highcharts.chart('pieCh', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            credits: {
                enabled: false
            },

            legend: {
                enabled: false
            },
            title: {
                useHTML: true,
                text: whiteSpacer(50)
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.key + '</b>' + '<br>Pathways count:' + this.y;
                }
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            exporting: {
                filename: 'Pie chart'
            },
            plotOptions: {
                pie: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                data: [{
                    name: 'Biological process',
                    y: pieData[0],
                    color: '#9BBFE0',
                }, {
                    name: 'Cellular component',
                    y: pieData[1],
                    color: '#E8A09A',
                }, {
                    name: 'Molecular function',
                    y: pieData[2],
                    color: '#FBE29F',
                }]
            }]
        });
    }
    pieChart();

    for (var i = 0; i < barJSON.length; i++) {
        bJSON = barJSON[i][0];
        var bDiv = document.getElementById(barID[i]);
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
                    text: bar_title[i],
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
                    // text: '<b>P-value</b>',
                    text: 'P-value',
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
                            textOutline: 'none'
                        },
                    },

                },
            },

            series: [bJSON],

            tooltip: {
                formatter: function () {
                    return `<b>${bar_title[i]}: <b>${this.key}<br><b>P-value: </b> ${this.y}`;
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