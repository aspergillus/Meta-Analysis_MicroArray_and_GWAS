<script src="js/jquery_3.2.1.js"></script>
<!-- <script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
<script src="js/highcharts.js"></script>
<script src="js/highcharts-more.js"></script>
<script src="js/exporting.js"></script>
<script src="js/accessibility.js"></script>

<?php
    error_reporting(0);
    session_start();
    $num = $_GET['Num'];
    $randNum = $_SESSION["randNum"][$num];
    $pathListStr = $_GET['pathVal'];
    $pathListArr = explode ("||", $pathListStr);

    // Extract Result Data from CSV file
    $gseaResult = array();
    $gseafh = "fileUpload/".$randNum."/gseaResult.csv";
    $gseafile = fopen($gseafh, 'r');
    while (($line = fgetcsv($gseafile)) !== FALSE) {
        $gseaResult[] = str_ireplace("'",  "&apos;", $line);
    }
    fclose($gseafile);
    array_shift($gseaResult);

    $bubbPltUp = array(); 
    $bubbPltDown = array(); 
    $bubbPltArr = array(); 

    $l = 0;
    foreach($pathListArr as $path){
        for($i=0; $i<sizeof($gseaResult); $i++){
            $gseaResult2 = $gseaResult[$i];
            if (preg_match("/$path/i", $gseaResult2[0])) {
                if(preg_match("/Upregulated/i", $gseaResult2[8])){
                    $bubbPltUp[] =  array('x'=>(float)bcdiv($gseaResult2[3], 1, 4), 'y'=>$l, 'z'=> sizeof(explode(",", $gseaResult2[7])), 'nes'=> (float)bcdiv($gseaResult2[5], 1, 3));
                }

                if(preg_match("/Downregulated/i", $gseaResult2[8])){
                    $bubbPltDown[] =  array('x'=>(float)bcdiv($gseaResult2[3], 1, 4), 'y'=>$l, 'z'=> sizeof(explode(",", $gseaResult2[7])), 'nes'=> (float)bcdiv($gseaResult2[5], 1, 3));
                }
                break;
            }
        }
        $l++;
    }

    // 'ylbl'=> $pathListArr

    if (!empty($bubbPltUp)) {
        $bubbPltUpColr = array('color'=>'green', 'data' => $bubbPltUp, 'name'=>'Upregulated');
        array_push($bubbPltArr, $bubbPltUpColr);
    }

    if (!empty($bubbPltDown)) {
        $bubbPltDownColr = array('color'=>'red', 'data' => $bubbPltDown, 'name'=>'Downregulated');
        array_push($bubbPltArr, $bubbPltDownColr);
    }

    if(!empty($bubbPltUp)){
        $bubbPltArr[0]['ylbl'] = $pathListArr;
    }else{
        $bubbPltArr[0]['ylbl'] = $pathListArr;
    }

    include('header.php');
?>

<style>
    canvas {
        background-color: #eee;
    }

    .headBubb {
        background-color: rgb(241, 232, 209);
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
        font-weight: bold;
    }

    .highcharts-tooltip span {
        height: auto;
        width: 150px;
    }

    .highcharts-axis-title {
        font-weight: bold;
        font-size: 16px;
    }

    .highcharts-axis-labels {
        font-size: 12px;
    }
</style>

<table width="98%" align="center" id="mdTable" style="font-size:12px;">
    <p>&nbsp;</p>
    <tr>
        <td>
            <div align="center">
                <h5 class="headBubb">Bubble Plot</h5>
                <div id="container" style="width: 900; height:800px; margin: 0 auto;"></div>
            </div>
        </td>
    </tr>
</table>

<script>
    var bubbData = <?= json_encode($bubbPltArr); ?>;
    var names = bubbData[0]['ylbl'];

    function whiteSpacer(howMany) {
        var spaceString = '';

        while (howMany) {
            spaceString += '&nbsp';
            howMany--;
        }

        return spaceString;
    }
    $(function () {
        $('#container').highcharts({
            chart: {
                type: 'bubble'
            },
            title: {
                useHTML: true,
                text: whiteSpacer(50)
            },

            credits: {
                enabled: false
            },

            xAxis: {
                title: {
                    text: 'P-value',
                    style: {
                        color: 'black',
                        fontSize: '14px',
                        fontFamily: 'Verdana, Geneva, sans-serif',
                        marginButtom: '100px'
                    }
                },
                labels: {
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
                        return names[this.value]
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Verdana, Geneva, sans-serif',
                        fill: 'black',
                        color: 'black',
                        paddingBottom: '10px',
                    }
                },
                title: {
                    text: 'Enriched pathways',
                    style: {
                        color: 'black',
                        fontSize: '14px',
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

            tooltip: {
                useHTML: true,
                headerFormat: '<table>',
                pointFormat: '<tr><th>NES: </th><td>{point.nes}</td></tr>' +
                    '<tr><th>P-value: </th><td>{point.x}</td></tr>' +
                    '<tr><th>Gene size: </th><td>{point.z}</td></tr>',
                footerFormat: '</table>',
                followPointer: true
            },
            series: bubbData,
            exporting: {
                filename: 'Bubble Plot'
            }
        });
    });
</script>

<?php
    include('footer.php');
?>



