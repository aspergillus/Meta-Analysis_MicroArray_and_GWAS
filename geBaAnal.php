<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script src="js/dom-to-image.js"></script>
<script src="js/FileSaver.js"></script>
<script src="js/saveSvgAsPng.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<!-- <script src="https://d3js.org/d3.v3.min.js"></script> -->
<script src='https://cdn.plot.ly/plotly-2.0.0.min.js'></script>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/bootstrap_4.0.css">
<script src="js/bootstrap_3.4.1.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // include("connect.php");
    session_start();
    $num = $_GET['Num'];
    $randNum = $_SESSION["randNum"][$num];
    $geneList = explode(",", $_GET['geneVal']);
    // echo sprintf("<input id='geneNum' type='hidden' value='%s'/>", json_encode($geneList));
    $ctlArray = $_SESSION["controlList".$num];
    $controlArray = $ctlArray[0];
    $ttmtArray = $_SESSION["treatmentList".$num];
    $treatmentArray = $ttmtArray[0];

    $result = array();
    $file = "fileUpload/".$randNum."/Expression_All_Normalized.txt";
    $fh = fopen($file, 'r');
    while (($line = fgetcsv($fh, 100000000, "\t")) !== false) {
        $result[] = $line;
    }
    ///////////////////////////////////////// BarPlot Variable //////////////////////////////////////////////
    
    $l=0;
    foreach($geneList as $val) {
        $xLbel = array();
        $forY = array();

        $contArr =  array();
        $caseArr =  array();
        $barArray = array();

        for ($i = 0; $i < sizeof($result); $i++) {
            $result2 = $result[$i];
            if (preg_match("/$val/i", $result2[1])) {
                for ($j = 2; $j < sizeof($result2); $j++) {
                    $xLbel[] = $result[0][$j];
                    $forY[] = $result2[$j];
                }
                break;
            }
        }
        
        $p = 0;
        for($i=0; $i<sizeof($controlArray); $i++){
            $contArr[] = array('x'=>$p, 'y'=>(float)bcdiv($forY[$p], 1, 3), 'xLbel'=> $controlArray[$i]);
            $p++;
        }

        for($i=0; $i<sizeof($treatmentArray); $i++){
            $caseArr[] = array('x'=>$p, 'y'=>(float)bcdiv($forY[$p], 1, 3), 'xLbel'=> $treatmentArray[$i]);
            $p++;
        }

        if (!empty($contArr) && !empty($caseArr)) {
            $contPCColr = array('genNm' => $val, 'color'=>'#f6dca7','data' => $contArr, 'name' => $_SESSION["conGrpName"][$_GET['Num']]);
            array_push($barArray, $contPCColr);

            $casePCColr = array('color'=>'#c38360', 'data' => $caseArr, 'name' => $_SESSION["caseGrpName"][$_GET['Num']]);
            array_push($barArray, $casePCColr);
        }

        $idVAl = "mainArray".$l;
        echo sprintf("<input id='$idVAl' type='hidden' value='%s'/>", json_encode($barArray));

        $l++;

        // $fp = fopen('results.json', 'w');
        // fwrite($fp, json_encode($barArray, JSON_PRETTY_PRINT));
        // fclose($fp);
    }

    ///////////////////////////////////////// HeatMap Variable //////////////////////////////////////////////
    $heatforX = array();
    $heatforY = array();
    $heatArray = array();
    for ($i = 0; $i < sizeof($geneList); $i++) {
        $selectedGene = $geneList[$i];
        for($j = 0; $j<sizeof($result); $j++){
            $result2 = $result[$j];
            if (preg_match("/^$selectedGene\b/i", $result2[1])) {
                for ($k = 2; $k<sizeof($result2); $k++) {
                    $heatforY[$i][] = $result2[$k];
                }
            }
        }
    }
    $resultX = $result[0];
    for($i = 2; $i < sizeof($resultX); $i++){
        $heatforX[] = $resultX[$i];
    }

    $heatArray[] = array("hoverongaps"=>"false", "type"=>"heatmap", "z"=>$heatforY, "x" => $heatforX, "y" => $geneList, 'hovertemplate'=> "Gene: %{y}<br>"."Sample: %{x}<br>"."Expression value: %{z}<br>"."<extra></extra>");
    // echo sprintf("<input id='heatArray' type='hidden' value='%s'/>", json_encode($heatArray));

    // ///////////////////////////////////// HyperLink to the Genes ///////////////////////////////////////////
    // $hyperlinkGene = array();
    // for($i=0; $i<sizeof($geneList); $i++){
    //     $GenID="";
    //     $vari =  $geneList[$i];
    //     $res = mysqli_query($conn, "SELECT GeneID FROM entrez_id WHERE Symbol = '$vari'"); 
    //     while($row = mysqli_fetch_array($res))
    //     {
    //         $GenID= $row["GeneID"];
    //     }
    //     if(!empty($GenID)){
    //         $hyperlinkGene[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$vari</a>";
    //     }else{
    //         $hyperlinkGene[] = $vari;
    //     }
    // }
    
    include('header.php');
?>

<style>
    .downldBtn {
        float: right;
        border: groove;
        background: rgb(239, 239, 239);
        font-size: 16px;
        cursor: pointer;
    }

    /* Bar Plot */
    .axis {
        font: 12px sans-serif;
    }

    .axis path,
    .axis line {
        fill: none;
        stroke: #000;
        shape-rendering: crispEdges;
    }

    .tooltipBar {
        border: 1px solid black;
        color: 'black';
        position: absolute;
        background-color: white;
        display: none;
        /* margin-top: -41.5%; */
        margin-top: -49em;
        margin-left: 11em;
    }
</style>

<p align="justify">&nbsp;</p>
<table width="98%" border="0" cellspacing="0" cellpadding="6" align="center" id="mdTable"></table>

<script>
    var mnTble = document.getElementById("mdTable");

    // Selected Gene Table Row
    var geneSelTr = document.createElement('tr');
    mnTble.appendChild(geneSelTr);

    // Table Data
    var geneSelTD = document.createElement('td');
    geneSelTr.appendChild(geneSelTD);

    var GenBAnalDiv = document.createElement("div");
    geneSelTD.appendChild(GenBAnalDiv);

    var GenBAnalHead = document.createElement("h5");
    GenBAnalHead.innerHTML = "Gene based analysis"
    GenBAnalHead.style.alignSelf = "center";
    GenBAnalHead.style.fontWeight = "bold";
    GenBAnalHead.style.textAlign = "center";
    GenBAnalHead.style.fontSize = "14px";
    GenBAnalDiv.appendChild(GenBAnalHead);

    var leftgeneSel = document.createElement('div');
    leftgeneSel.innerHTML = "Genes selected for analysis:";
    leftgeneSel.style.width = "25%";
    // leftgeneSel.style.fontSize = "15px";
    leftgeneSel.style.fontWeight = "600";
    leftgeneSel.style.display = "inline-block";
    leftgeneSel.style.marginBottom = "15px";
    GenBAnalDiv.appendChild(leftgeneSel);

    var rightgeneSel = document.createElement('div');
    rightgeneSel.style.width = "75%";
    // rightgeneSel.style.fontSize = "13px";
    rightgeneSel.style.display = "inline-block";
    rightgeneSel.style.marginBottom = "15px";
    hyperlinkGene = <?= json_encode($hyperlinkGene); ?>;
    rightgeneSel.innerHTML = hyperlinkGene;
    GenBAnalDiv.appendChild(rightgeneSel);

    // Bar Plot Table row
    var barTr = document.createElement('tr');
    mnTble.appendChild(barTr);

    var barTd = document.createElement('td');
    barTd.style.textAlign = "center";
    barTr.appendChild(barTd);

    var barDiv = document.createElement("div");
    barDiv.className = "d-flex mb-4";
    barDiv.style.background = "rgb(241, 232, 209)";
    barDiv.style.border = "1px solid rgb(185, 156, 107)";
    barDiv.style.borderRadius = "5px";
    barTd.appendChild(barDiv);

    var barHead = document.createElement("div");
    barHead.innerHTML = "Bar Plot";
    barHead.className = "mr-auto";
    barHead.id = "barPltID";
    barHead.style.fontWeight = "bold";
    barHead.style.alignSelf = "center";
    barHead.style.marginLeft = "6px";
    barDiv.appendChild(barHead);

    //Go to Study directly 
    var seltTD = document.createElement('div');
    $(seltTD).css({
        "alignSelf": "center",
        "padding": "6px 10px 6px 10px",
        "background": "#f1E8d1",
        "border": "1px solid #b99c6b",
        "border-radius": "5px"
    });
    var drpdwnSelDiv = document.createElement('div');
    drpdwnSelDiv.className = "dropdown";
    var fstSel = document.createElement('a');
    fstSel.id = "my-dropdown";
    fstSel.className = "dropdown-toggle";
    $(fstSel).attr("data-toggle", "dropdown").css({
        "color": "black",
        "text-decoration-line": "none",
        "cursor": "pointer"
    });
    fstSel.innerText = "Go to: ";
    var SecSelUL = document.createElement('ul');
    SecSelUL.className = "dropdown-menu gseaGoToStuLst";

    barDiv.appendChild(seltTD);
    seltTD.appendChild(drpdwnSelDiv);
    drpdwnSelDiv.appendChild(fstSel);
    drpdwnSelDiv.appendChild(SecSelUL);

    var addBlnkSpce = document.createElement("p");
    barDiv.appendChild(addBlnkSpce);

    var genNum = <?= json_encode($geneList); ?>;
    var l = 0;
    genNum.forEach(function (key) {
        var graphDiv = document.createElement("div");
        graphDiv.id = "graph" + l;
        graphDiv.style.border = "1px solid black";
        graphDiv.style.textAlign = "center";
        graphDiv.style.display = "inline-block";
        graphDiv.style.width = "1000px";
        graphDiv.style.height = "600px";
        barTd.appendChild(graphDiv);

        var avgHv = document.createElement("div");
        avgHv.className = "tooltipBar";
        avgHv.id = "barHover_" + l;
        avgHv.innerText = "Average expression";
        barTd.appendChild(avgHv);

        var spcDiv = document.createElement("p")
        spcDiv.innerHTML = "&nbsp;";
        barTd.appendChild(spcDiv);

        var mainArrayNum = "mainArray" + l;
        var barJSON = JSON.parse(document.getElementById(mainArrayNum).value);
        var xLbl = [];
        var yVal = [];
        barJSON.forEach(function (key) {
            keyD = key["data"];
            keyD.forEach(function (val) {
                xLbl.push(val["xLbel"]);
                yVal.push(val["y"]);
            });
        });

        var avgE = yVal.reduce(function (a, b) {
            return a + b;
        }, 0) / yVal.length;

        Highcharts.chart(graphDiv, {
            chart: {
                type: 'column'
            },

            title: {
                text: barJSON[0]['genNm'],
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Verdana, Geneva, sans-serif'
                }
            },

            credits: {
                enabled: false
            },

            xAxis: {
                categories: xLbl,
                title: {
                    text: "Samples",
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
                        color: 'black',
                    },
                    rotation: -70
                }
            },

            yAxis: {
                title: {
                    text: "Expression values",
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
                plotLines: [{
                    dashStyle: 'shortdash',
                    color: '#000000', // Red
                    width: 2,
                    value: avgE, // Position, you'll have to translate this to the values on your x axis (Elbow Method),
                    events: {
                        mouseover() {
                            let lineBBox = this.svgElem.getBBox();
                            avgHv.style.display = 'block';
                            avgHv.style.left = 'px';
                        },
                        mouseout() {
                            avgHv.style.display = 'none'
                        }
                    }
                }]
            },

            plotOptions: {
                column: {
                    grouping: false,
                },
            },
            series: barJSON,

            tooltip: {
                useHTML: true,
                headerFormat: '<table>',
                pointFormat: '<tr><th>Sample: </th><td>{point.xLbel}</td></tr>' +
                    '<tr><th>Value: </th><td>{point.y}</td></tr>',
                footerFormat: '</table>',
                followPointer: true
            },

            exporting: {
                filename: barJSON[0]['genNm'] + ' Bar Plot'
            }
        });

        l++;
    });

    // Heat Map Table row
    var HeatTr = document.createElement('tr');
    mnTble.appendChild(HeatTr);

    var heatTd = document.createElement("td");
    HeatTr.appendChild(heatTd);

    var heatDiv = document.createElement("div");
    heatDiv.className = "d-flex mb-2";
    heatDiv.style.background = "rgb(241, 232, 209)";
    heatDiv.style.border = "1px solid rgb(185, 156, 107)";
    heatDiv.style.borderRadius = "5px";
    heatTd.appendChild(heatDiv);

    var heatHead = document.createElement("div");
    heatHead.innerHTML = "Heat Map";
    heatHead.className = "mr-auto";
    heatHead.id = "heatMapID";
    heatHead.style.fontWeight = "bold";
    heatHead.style.alignSelf = "center";
    heatHead.style.marginLeft = "6px";
    heatDiv.appendChild(heatHead);

    //Go to Study directly 
    var seltTD = document.createElement('div');
    $(seltTD).css({
        "alignSelf": "center",
        "padding": "6px 10px 6px 10px",
        "background": "#f1E8d1",
        "border": "1px solid #b99c6b",
        "border-radius": "5px"
    });
    var drpdwnSelDiv = document.createElement('div');
    drpdwnSelDiv.className = "dropdown";
    var fstSel = document.createElement('a');
    fstSel.id = "my-dropdown";
    fstSel.className = "dropdown-toggle";
    $(fstSel).attr("data-toggle", "dropdown").css({
        "color": "black",
        "text-decoration-line": "none",
        "cursor": "pointer"
    });
    fstSel.innerText = "Go to: ";
    var SecSelUL = document.createElement('ul');
    SecSelUL.className = "dropdown-menu gseaGoToStuLst";

    heatDiv.appendChild(seltTD);
    seltTD.appendChild(drpdwnSelDiv);
    drpdwnSelDiv.appendChild(fstSel);
    drpdwnSelDiv.appendChild(SecSelUL);

    var heatDiv = document.createElement("div");
    heatDiv.id = "heatmapID";
    heatDiv.style.textAlign = "-webkit-center";
    heatTd.appendChild(heatDiv);

    function heatMap() {
        var heatData = <?= json_encode($heatArray); ?>;
        var layout = {
            width: 1000,
            title: {
                text: '<b>Differentially Expressed Genes in All Samples</b>',
            },
            xaxis: {
                title: '<b>Samples</b>',
                titlefont: {
                    family: 'Verdana, Geneva, sans-serif',
                    size: 14,
                    color: 'black'
                },
                showticklabels: true,
                tickangle: '-30',
                tickfont: {
                    family: 'Verdana, Geneva, sans-serif',
                    size: 12,
                    color: 'black'
                },
                exponentformat: 'e',
                showexponent: 'all'
            },
            yaxis: {
                automargin: true,
                title: {
                    text: '<b>Genes</b>',
                    standoff: 10
                },
                titlefont: {
                    family: 'Verdana, Geneva, sans-serif',
                    size: 14,
                    color: 'black'
                },
                showticklabels: true,
                tickangle: 0,
                tickfont: {
                    family: 'Verdana, Geneva, sans-serif',
                    size: 12,
                    color: 'black'
                },
                exponentformat: 'e',
                showexponent: 'all'
            },
            margin: {
                b: '150'
            },
        };

        if(hyperlinkGene.length > 5){
            layout['height'] = 700;
        }

        Plotly.newPlot('heatmapID', heatData, layout);
    }
    heatMap();

    var GoTochrtLi = document.querySelectorAll(".gseaGoToStuLst");
    $(GoTochrtLi).append('<li><a href="#barPltID">Bar Plot</a></li>');
    $(GoTochrtLi).append('<li><a href="#heatMapID">Heat Map</a></li>');

    var pElemt = document.createElement("p");
    pElemt.innerHTML = "&nbsp;";
    heatDiv.appendChild(pElemt);
</script>

<?php
    include('footer.php');
?>