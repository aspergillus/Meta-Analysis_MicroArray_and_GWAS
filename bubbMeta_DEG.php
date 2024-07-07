<?php
    error_reporting(0);
    session_start();
    $rndNum = $_SESSION['randNum'];
    $metaAnalTitle = $_SESSION["analTitle"];

    /////////////// Bubble Plot for Genes /////////////////
    $gFreqArr=array();
    $file="fileUpload/".end($rndNum)."/GenFreq.txt";
    $fh=fopen($file, 'r');
    while (($line=fgetcsv($fh, 100000000, "\t")) !==false) {
        $gFreqArr[]=$line;
    }
    array_shift($gFreqArr);

    $gFreq = array();
    foreach($gFreqArr as $Gen){
        array_push($gFreq, $Gen[1]);
    }
    
    if($_POST['GenFreq'] == 'all' || empty($_POST['GenFreq'])){
        $rnNum = end($rndNum);

        $b_Gen=array();
        $file="fileUpload/".$rnNum."/deg_List.txt";
        $fh=fopen($file, 'r');
        while (($line=fgetcsv($fh, 100000000, "\t")) !==false) {
            $b_Gen[]=$line;
        }
        array_shift($b_Gen);

        for($i=0; $i<sizeof($b_Gen); $i++) {
            if($b_Gen[$i][1] > 0){
                array_push($b_Gen[$i], 'Upregulated');
            }else{
                array_push($b_Gen[$i], 'Downregulated');
            }
        }

        $numStudy_Gen = array();
    
        for($i=1; $i<=sizeof($rndNum); $i++) {
            $studyCount="Study_".$i;
            for($j=0; $j<sizeof($b_Gen); $j++) {
                if(preg_match("/$studyCount/i", $b_Gen[$j][3])) {
                    $numStudy_Gen[$i-1][]=$b_Gen[$j];
                }
            }
        }

        $meta_bubb_Up_gen = array();
        $meta_bubb_Down_gen = array();
        $meta_GeneList = array();
        $meta_bubb_GeneArr = array();
     
        if(!empty($numStudy_Gen)) {
            for($i=0; $i<sizeof($rndNum); $i++){
                for($j=0; $j<sizeof($numStudy_Gen[$i]); $j++){

                    // this need to be removed from here
                    $meta_GeneList[] = $numStudy_Gen[$i][$j][0];
                    // till here 

                    if(preg_match("/Upregulated/i", $numStudy_Gen[$i][$j][4])){
                        $meta_bubb_Up_gen[] =  array('x'=>$i, 'y'=>$j, 'z'=>2,'gene'=> $numStudy_Gen[$i][$j][0], 'logfc'=> (float)bcdiv($numStudy_Gen[$i][$j][1], 1, 3), 'pval' => (float)bcdiv($numStudy_Gen[$i][$j][2], 1, 4), 'regulation' => $numStudy_Gen[$i][$j][4]);
                    }else{
                        $meta_bubb_Down_gen[] =  array('x'=>$i, 'y'=>$j, 'z'=>2, 'gene'=> $numStudy_Gen[$i][$j][0], 'logfc'=> (float)bcdiv($numStudy_Gen[$i][$j][1], 1, 3), 'pval' => (float)bcdiv($numStudy_Gen[$i][$j][2], 1, 4), 'regulation' => $numStudy_Gen[$i][$j][4]);
                    }
                }
            }
    
            if ( !empty($meta_bubb_Up_gen)) {
                $meta_bubb_Up_genColr=array('genList'=>$meta_GeneList, 'xLbel_Gen'=>$metaAnalTitle, 'color'=>'red', 'data'=> $meta_bubb_Up_gen, 'name'=>'Upregulated');
                array_push($meta_bubb_GeneArr, $meta_bubb_Up_genColr);
            }
     
            if ( !empty($meta_bubb_Down_gen)) {
                $meta_bubb_Down_genColr=array('color'=>'blue', 'data'=> $meta_bubb_Down_gen, 'name'=>'Downregulated');
                array_push($meta_bubb_GeneArr, $meta_bubb_Down_genColr);
            }
        }
    }else{
        $gFreqArr = (int)$_POST['GenFreq'];
        $rnNum = end($rndNum);
        $bExe = exec('Rscript ./R_file/metaBubble_DEG.R '."$rnNum $gFreqArr");
    
        $b_Gen=array();
        $file="fileUpload/".$rnNum."/bubble_meta_gene.txt";
        $fh=fopen($file, 'r');
        while (($line=fgetcsv($fh, 100000000, "\t")) !==false) {
            $b_Gen[]=$line;
        }
        array_shift($b_Gen);
    
        $numStudy_Gen = array();

        for($i=1; $i<=sizeof($rndNum); $i++) {
            $studyCount="Study_".$i;
            for($j=0; $j<sizeof($b_Gen); $j++) {
                if(preg_match("/$studyCount/i", $b_Gen[$j][4])) {
                    $numStudy_Gen[$i-1][]=$b_Gen[$j];
                }
            }
        }
    
        $meta_bubb_Up_gen = array();
        $meta_bubb_Down_gen = array();
        $meta_GeneList = array();
        $meta_bubb_GeneArr = array();
     
        if(!empty($numStudy_Gen)) {
            for($i=0; $i<sizeof($rndNum); $i++){
                for($j=0; $j<sizeof($numStudy_Gen[$i]); $j++){

                    // this need to be removed from here
                    $meta_GeneList[] = $numStudy_Gen[$i][$j][0];
                    // till here 

                    if(preg_match("/Upregulated/i", $numStudy_Gen[$i][$j][5])){
                        $meta_bubb_Up_gen[] =  array('x'=>$i, 'y'=>$j, 'z'=>2,'gene'=> $numStudy_Gen[$i][$j][0], 'logfc'=> (float)bcdiv($numStudy_Gen[$i][$j][2], 1, 3), 'pval' => (float)bcdiv($numStudy_Gen[$i][$j][3], 1, 4), 'regulation' => $numStudy_Gen[$i][$j][5]);
                    }else{
                        $meta_bubb_Down_gen[] =  array('x'=>$i, 'y'=>$j, 'z'=>2, 'gene'=> $numStudy_Gen[$i][$j][0], 'logfc'=> (float)bcdiv($numStudy_Gen[$i][$j][2], 1, 3), 'pval' => (float)bcdiv($numStudy_Gen[$i][$j][3], 1, 4), 'regulation' => $numStudy_Gen[$i][$j][5]);
                    }
                }
            }
    
            if ( !empty($meta_bubb_Up_gen)) {
                $meta_bubb_Up_genColr=array('color'=>'red', 'data'=> $meta_bubb_Up_gen, 'name'=>'Upregulated');
                array_push($meta_bubb_GeneArr, $meta_bubb_Up_genColr);
            }
     
            if ( !empty($meta_bubb_Down_gen)) {
                $meta_bubb_Down_genColr=array('color'=>'blue', 'data'=> $meta_bubb_Down_gen, 'name'=>'Downregulated');
                array_push($meta_bubb_GeneArr, $meta_bubb_Down_genColr);
            }

            if(!empty($meta_bubb_Up_gen)){
                $meta_bubb_GeneArr[0]['genList'] = $meta_GeneList;
                $meta_bubb_GeneArr[0]['xLbel_Gen'] = $metaAnalTitle;
            }else{
                $meta_bubb_GeneArr[0]['genList'] = $meta_GeneList;
                $meta_bubb_GeneArr[0]['xLbel_Gen'] = $metaAnalTitle;
            }
        }
    }
    
    // Header
    // include('header.php');
?>
<!-- <script src="js/jquery_3.2.1.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

<style>
    .headBubb {
        background-color: rgb(241, 232, 209);
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
        font-weight: bold;
        margin: 20px 0px;
    } -->
</style>

<div id='bubbMetaGen' > 
    <div align="center">
        <!-- <h5 class="headBubb">Bubble Plot</h5> -->
        <form class="freqGen" style="text-align-last: right;">
            <label><b>Frequency: </b></label>
            <select class="freqSel" id="freqSel">
                <option value="all">All</option>
                <?php
                foreach(array_unique($gFreq) as $frKey){
                    ?>
                <option value="<?= $frKey;?>"
                    <?= (isset($_POST['GenFreq']) && $_POST['GenFreq'] == $frKey) ? 'selected' : '' ?>><?= $frKey; ?>
                </option>
                <?php
                }
            ?>
            </select>
            <input type="hidden" id="selectedFreq" name="GenFreq">
            <input type="submit" class="submit_BubbMeGen" value="Submit" onclick="return clckbtnGen();">
            </div>
        </form>
        <div id="bubbContGen" style="width: 900; height:800px; margin: 0 auto;"></div>
    </div>
</div>


<script>
    function clckbtnGen() {
        $("#bubbContGen").html("");
        var sGen = document.getElementById('freqSel');
        var sel = sGen.options[sGen.selectedIndex].value;
        $.ajax({
            type: "post",
            url: "bubbMeta_DEG.php",
            data: {
                'GenFreq': sel
            },
            cache: false,
            success: function (html) {
                // $("#bubbContGen").html(html)
                jQuery(`#bubbContGen`).fadeOut(100, function () {
                    jQuery(this).html(html);
                }).fadeIn(1000);
            }
        });
        return false;
    }

    function metaG_bubblePlt() {
        var bubbData = <?= json_encode($meta_bubb_GeneArr) ?>;
        studyNum = bubbData[0]['xLbel_Gen'];
        genList = bubbData[0]['genList'];
        
        function whiteSpacer(howMany) {
            var spaceString = '';

            while (howMany) {
                spaceString += '&nbsp';
                howMany--;
            }

            return spaceString;
        }
        Highcharts.chart('bubbContGen', {
            chart: {
                type: 'bubble',
                plotBorderWidth: 1,
                zoomType: 'xy'
            },

            title: {
                useHTML: true,
                text: whiteSpacer(50)
            },

            // legend: {
            //     enabled: false
            // },

            credits: {
                enabled: false
            },

            xAxis: {
                title: {
                    text: '<b>Studies</b>',
                    style: {
                        fontSize: '14px',
                        color: 'black',
                        fontFamily: 'Verdana, Geneva, sans-serif'
                    }
                },
                min: -0.1,
                max: 0.1 + (studyNum.length - 1),
                gridLineWidth: 1,
                startOnTick: false,
                endOnTick: false,
                labels: {
                    formatter: function () {
                        return studyNum[this.value]
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Verdana, Geneva, sans-serif',
                        fill: 'black',
                        color: 'black'
                    }
                }
            },

            yAxis: {
                startOnTick: false,
                endOnTick: false,
                labels: {
                    formatter: function () {
                        return genList[this.value]
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Verdana, Geneva, sans-serif',
                        fill: 'black',
                        color: 'black'
                    }
                },
                title: {
                    text: '<b>Common genes</b>',
                    style: {
                        fontSize: '14px',
                        color: 'black',
                        fontFamily: 'Verdana, Geneva, sans-serif'
                    }
                }
            },

            plotOptions: {
                bubble: {
                    zMin: 2,
                    zMax: 500
                }
            },

            series: bubbData,

            tooltip: {
                useHTML: true,
                headerFormat: '<table>',
                pointFormat: '<tr><th>Gene: </th><td>{point.gene}</td></tr>' +
                    '<tr><th>Log<sub>2</sub> fold change: </th><td>{point.logfc}</td></tr>' +
                    '<tr><th>P-value: </th><td>{point.pval}</td></tr>' +
                    '<tr><th>Regulation status: </th><td>{point.regulation}</td></tr>',
                footerFormat: '</table>',
                followPointer: true
            }
        });
    }
    metaG_bubblePlt();
</script>


<?php
    // include('footer.php');
?>