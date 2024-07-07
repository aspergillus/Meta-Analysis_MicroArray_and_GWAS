<?php
    session_start();
    $randNum = $_SESSION["randNum"][$_POST['scoreNum']];
    $forwardStr = $_POST['forX'].",".$_POST['forY'];
    $abc = exec('"C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/R_file/pc_CEL.R" '."$randNum $forwardStr");
    // echo shell_exec('"C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/R_file/pc_CEL.R" '."$randNum $forwardStr 2>&1")
    
    $result = array();
    $file = "fileUpload/".$randNum ."/scoreplot_data_new.txt";
    $fh = fopen($file, 'r');
    while (($line = fgetcsv($fh, 100000000, "\t")) !== false) {
        $result[] = $line;
    }
    array_shift($result);

    $scorpltArr = array();
    $contPC = array();
    $casePC = array();
    for($i=0; $i<sizeof($result); $i++){
        if(preg_match("/1/i", $result[$i][3])){
            $contPC[] = array('x'=>(float)bcdiv($result[$i][1], 1, 3), 'y'=>(float)bcdiv($result[$i][2], 1, 3), 'z'=>2, 'nes'=> $_POST['forX']);
        }else{
            $casePC[] = array('x'=>(float)bcdiv($result[$i][1], 1, 3), 'y'=>(float)bcdiv($result[$i][2], 1, 3), 'z'=>2, 'nes'=> $_POST['forY']);
        }
    }

    // array_push($scorpltArr, "kajsgdk");
    if (!empty($contPC)) {
        $contPCColr = array('color'=>'#bb99ff','data' => $contPC, 'name' => $_SESSION["conGrpName"][$_POST['scoreNum']]);
        array_push($scorpltArr, $contPCColr);
    } 

    if (!empty($casePC)) {
        $casePCColr = array('color'=>'#ffb399', 'data' => $casePC, 'name' => $_SESSION["caseGrpName"][$_POST['scoreNum']]);
        array_push($scorpltArr, $casePCColr);
    }

    $IDscoreNum = $_POST['scoreNum'];

    // echo sprintf("<input id='scoreNumID' type='hidden' value='%s'/>", json_encode($IDscoreNum));
    // echo sprintf("<input id='scorpltArr' type='hidden' value='%s'/>", json_encode($scorpltArr));
    // echo sprintf("<input id='forwardStr' type='hidden' value='%s'/>", json_encode($forwardStr));

    // include('header.php');
?>

<!-- <script src="js/jquery_3.2.1.js"></script>
<script src="js/highcharts.js"></script>
<script src="js/highcharts-more.js"></script>
<script src="js/exporting.js"></script> -->

<style>
    /* .headBubb {
        background-color: rgb(241, 232, 209);
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
        font-weight: bold;
    }

    .highcharts-tooltip span {
        height: auto;
        width: 160px;
    }

    .HoldContent {
        width: 900px;
        height: 600px;
        margin: 0px auto;
    } */
</style>

<div align="center">
    <!-- <h5 class="headBubb">Score Plot</h5> -->
    <div id="containerFoRScore"></div>
</div>



<script>
    forwardStr = <?= json_encode($forwardStr); ?> ;
    forwardStr = forwardStr.split(",");
    scorpltArr = <?= json_encode($scorpltArr); ?> ;

    scoreNumID = <?= json_encode($IDscoreNum); ?> ;
    var contScorePlt = document.getElementById("containerFoRScore");
    var holdContainer = document.createElement("div");
    holdContainer.className = "HoldContent";
    holdContainer.id = "HoldContent" + scoreNumID;
    contScorePlt.appendChild(holdContainer);
    // holdContainer.style.width = "900";
    // holdContainer.style.height = "600";
    // holdContainer.style.margin = "0 auto";
    var IDStrore = $('#containerFoRScore').children().attr('id');

    function whiteSpacer(howMany) {
        var spaceString = '';

        while (howMany) {
            spaceString += '&nbsp';
            howMany--;
        }

        return spaceString;
    }
    Highcharts.chart(holdContainer, {
        chart: {
            type: 'bubble',
            plotBorderWidth: 1,
            zoomType: 'xy'
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
                text: forwardStr[0],
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
            }
        },

        yAxis: {
            title: {
                text: forwardStr[1],
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
            }
        },

        plotOptions: {
            bubble: {
                zMin: 0,
                zMax: 50
            }
        },

        series: scorpltArr,

        tooltip: {
            useHTML: true,
            headerFormat: '<table>',
            pointFormat: '<tr><th>' + forwardStr[0] + ': </th><td>{point.x}</td></tr>' +
                '<tr><th>' + forwardStr[1] + ': </th><td>{point.y}</td></tr>' +
                '<tr><th>Sample type: </th><td>{series.name}</td></tr>',
            footerFormat: '</table>',
            followPointer: true
        },

        exporting: {
            filename: 'Score Plot'
        }
    });
</script>


<?php
    // include('footer.php');
?>