<?php
    error_reporting(0);
    session_start();
    $rndNum = $_SESSION['randNum'];
    $metaAnalTitle = $_SESSION["analTitle"];

    /////////////// Bubble Plot for Genes /////////////////
    $pFreqArr=array();
    $file="fileUpload/".end($rndNum)."/PathFreq.txt";
    $fh=fopen($file, 'r');
    while (($line=fgetcsv($fh, 100000000, "\t")) !==false) {
        $pFreqArr[]=$line;
    }
    array_shift($pFreqArr);

    $pathFre = array();
    foreach($pFreqArr as $pa){
        array_push($pathFre, $pa[1]);
    }
    
    if($_POST['pathFreq'] == 'all' || empty($_POST['pathFreq'])){
        $rnNum = end($rndNum);

        // Extract Result Data from CSV file
        $b_pathArr = array();
        $fh = "fileUpload/".$rnNum."/path_list.csv";
        $file = fopen($fh, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $b_pathArr[] = str_ireplace("'",  "&apos;", $line);
        }
        fclose($file);
        array_shift($b_pathArr);

        $numStudy_Path = array();

        for($i=1; $i<=sizeof($rndNum); $i++) {
            $studyCount = "Study_".$i;
            for($j=0; $j<sizeof($b_pathArr); $j++) {
                if(preg_match("/$studyCount/i", $b_pathArr[$j][5])) {
                    $numStudy_Path[$i-1][]=$b_pathArr[$j];
                }
            }
        }
     
        $meta_bubb_Up_path = array();
        $meta_bubb_Down_path = array();
        $meta_pathList = array();
        $meta_bubb_pathArr = array();
        
        if( !empty($numStudy_Path)) {
            for($i=0; $i<sizeof($rndNum); $i++){
                for($j=0; $j<sizeof($numStudy_Path[$i]); $j++){

                    $meta_pathList[] = $numStudy_Path[$i][$j][0];
                    
                    if(preg_match("/Upregulated/i", $numStudy_Path[$i][$j][4])){
                        $meta_bubb_Up_path[] =  array('x'=>$i, 'y'=>$j, 'z'=>(float)$numStudy_Path[$i][$j][3], 'pathNm'=>$numStudy_Path[$i][$j][0], 'nes'=> (float)bcdiv($numStudy_Path[$i][$j][2], 1, 3), 'pval' => (float)bcdiv($numStudy_Path[$i][$j][1], 1, 4));
                    }else{
                        $meta_bubb_Down_path[] =  array('x'=>$i, 'y'=>$j, 'z'=>(float)$numStudy_Path[$i][$j][3], 'pathNm'=>$numStudy_Path[$i][$j][0], 'nes'=> (float)bcdiv($numStudy_Path[$i][$j][2], 1, 3), 'pval' => (float)bcdiv($numStudy_Path[$i][$j][1], 1, 4));
                    }
                }
            }
        }

        if ( !empty($meta_bubb_Up_path)) {
            $meta_bubb_Up_pathColr=array('color'=>'red', 'data'=> $meta_bubb_Up_path, 'name'=>'Upregulated');
            array_push($meta_bubb_pathArr, $meta_bubb_Up_pathColr);
        }
        // array_push($meta_bubb_pathArr, array('pathList'=>$meta_pathList, 'xLbel_Path'=>$metaAnalTitle));
        if( !empty($meta_bubb_Down_path)) {
            $meta_bubb_Down_pathColr=array('color'=>'blue', 'data'=> $meta_bubb_Down_path, 'name'=>'Downregulated');
            array_push($meta_bubb_pathArr, $meta_bubb_Down_pathColr);
        }

        if(!empty($meta_bubb_Up_path)){
            $meta_bubb_pathArr[0]['pathList'] = $meta_pathList;
            $meta_bubb_pathArr[0]['xLbel_Path'] = $metaAnalTitle;
        }else{
            $meta_bubb_pathArr[0]['pathList'] = $meta_pathList;
            $meta_bubb_pathArr[0]['xLbel_Path'] = $metaAnalTitle;
        }
    }else{
        $pathFreqArr = (int)$_POST['pathFreq'];
        $rnNum = end($rndNum);
        $bExe = exec('Rscript ./R_file/metaBubble_GSEA.R '."$rnNum $pathFreqArr");
        
        // Extract Result Data from CSV file
        $b_pathArr = array();
        $fh = "fileUpload/".$rnNum."/bubble_meta_Path.csv";
        $file = fopen($fh, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $b_pathArr[] = str_ireplace("'",  "&apos;", $line);
        }
        fclose($file);
        array_shift($b_pathArr);

        $numStudy_Path = array();

        for($i=1; $i<=sizeof($rndNum); $i++) {
            $studyCount = "Study_".$i;
            for($j=0; $j<sizeof($b_pathArr); $j++) {
                if(preg_match("/$studyCount/i", $b_pathArr[$j][6])) {
                    $numStudy_Path[$i-1][]=$b_pathArr[$j];
                }
            }
        }
     
        $meta_bubb_Up_path = array();
        $meta_bubb_Down_path = array();
        $meta_pathList = array();
        $meta_bubb_pathArr = array();
        
        if( !empty($numStudy_Path)) {
            for($i=0; $i<sizeof($rndNum); $i++){
                for($j=0; $j<sizeof($numStudy_Path[$i]); $j++){

                    $meta_pathList[] = $numStudy_Path[$i][$j][0];

                    if(preg_match("/Upregulated/i", $numStudy_Path[$i][$j][5])){
                        $meta_bubb_Up_path[] =  array('x'=>$i, 'y'=>$j, 'z'=>(float)$numStudy_Path[$i][$j][4], 'pathNm'=>$numStudy_Path[$i][$j][0], 'nes'=> (float)bcdiv($numStudy_Path[$i][$j][3], 1, 3), 'pval' => (float)bcdiv($numStudy_Path[$i][$j][2], 1, 4));
                    }else{
                        $meta_bubb_Down_path[] =  array('x'=>$i, 'y'=>$j, 'z'=>(float)$numStudy_Path[$i][$j][4], 'pathNm'=>$numStudy_Path[$i][$j][0], 'nes'=> (float)bcdiv($numStudy_Path[$i][$j][3], 1, 3), 'pval' => (float)bcdiv($numStudy_Path[$i][$j][2], 1, 4));
                    }
                }
            }
        
        //     // $meta_pathList = array_unique($meta_pathList);
        }

        // 'pathList'=>$meta_pathList, 'xLbel_Path'=>$metaAnalTitle, 

        if ( !empty($meta_bubb_Up_path)) {
            $meta_bubb_Up_pathColr=array('color'=>'red', 'data'=> $meta_bubb_Up_path, 'name'=>'Upregulated');
            array_push($meta_bubb_pathArr, $meta_bubb_Up_pathColr);
        }

        if( !empty($meta_bubb_Down_path)) {
            $meta_bubb_Down_pathColr=array('color'=>'blue', 'data'=> $meta_bubb_Down_path, 'name'=>'Downregulated');
            array_push($meta_bubb_pathArr, $meta_bubb_Down_pathColr);
        }

        if(!empty($meta_bubb_Up_path)){
            $meta_bubb_pathArr[0]['pathList'] = $meta_pathList;
            $meta_bubb_pathArr[0]['xLbel_Path'] = $metaAnalTitle;
        }else{
            $meta_bubb_pathArr[0]['pathList'] = $meta_pathList;
            $meta_bubb_pathArr[0]['xLbel_Path'] = $metaAnalTitle;
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
    }
</style> -->

<div id='bubbMetaGen' > 
    <div align="center">
        <form class="freqPath" style="text-align-last: right;">
            <label><b>Frequency: </b>
            <select class="freqSel" id="pathfreqSel">
                <option value="all">All</option>
                <?php
                foreach(array_unique($pathFre) as $frKey){
                    ?>
                    <option value="<?= $frKey;?>"
                        <?= (isset($_POST['pathFreq']) && $_POST['pathFreq'] == $frKey) ? 'selected' : '' ?>><?= $frKey; ?>
                    </option>
                <?php
                }
            ?>
            </select>
            <input type="hidden" id="selectedFreq_path" name="pathFreq">
            <input type="submit" class="submit_BubbMePath" value="Submit" onclick="return clckbtnPath();">
            </div>
        </form>
        <div id="bubbContPath" style="width: 900; height:800px; margin: 0 auto;"></div>
    </div>
</div>


<script>
    function clckbtnPath() {
        $("#bubbContPath").html("");
        var spath = document.getElementById('pathfreqSel');
        var selPath = spath.options[spath.selectedIndex].value;
        $.ajax({
            type: "post",
            url: "bubbMeta_GSEA.php",
            data: {
                'pathFreq': selPath
            },
            cache: false,
            success: function (html) {
                $("#bubbContPath").html(html)
            }
        });
        return false;
    }

    function metaP_BubblePlt() {
        var bubbData = <?= json_encode($meta_bubb_pathArr) ?>;
        var pathNames = bubbData[0]['pathList'];
        var studyNames = bubbData[0]['xLbel_Path'];
        // console.log(bubbData);

        function whiteSpacer(howMany) {
            var spaceString = '';

            while (howMany) {
                spaceString += '&nbsp';
                howMany--;
            }

            return spaceString;
        }
        Highcharts.chart('bubbContPath', {
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
                max: 0.1 + (studyNames.length - 1),
                gridLineWidth: 1,
                startOnTick: false,
                endOnTick: false,
                labels: {
                    formatter: function () {
                        return studyNames[this.value]
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
                        return pathNames[this.value]
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Verdana, Geneva, sans-serif',
                        fill: 'black',
                        color: 'black'
                    }
                },
                title: {
                    text: '<b>Common pathways</b>',
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
                pointFormat: '<tr><th>Pathway: </th><td>{point.pathNm}</td></tr>' +
                    '<tr><th>NES: </th><td>{point.nes}</td></tr>' +
                    '<tr><th>P-value: </th><td>{point.pval}</td></tr>' +
                    '<tr><th>Gene size: </th><td>{point.z}</td></tr>',
                footerFormat: '</table>',
                followPointer: true
            }
        });
    }
    metaP_BubblePlt();
</script>


<?php
    // include('footer.php');
?>