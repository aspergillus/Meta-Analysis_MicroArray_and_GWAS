<script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
<script src="js/jquery_3.2.1.js"></script>

<?php
    error_reporting(0);
    session_start();
    if($_GET['Num'] != NULL){
        $sessionNum =  $_GET['Num'];
        $upRange = "1";
        $downRange = "1";
    }else{
        $sessionNum = $_POST['rdNum'];
        $upRange = $_POST['upReg'];
        $downRange = $_POST['DwnReg'];
    }

    $rangeboth = array();
    array_push($rangeboth, $upRange);
    array_push($rangeboth, $downRange);
    // echo sprintf("<input id='rangeboth' type='hidden' value='%s'/>", json_encode($rangeboth));

    $resultArray = $_SESSION["gseaResultSession"][$sessionNum];
    array_shift($resultArray);

    $degList = $_SESSION["degArray"][$sessionNum];
    array_shift($degList);

    // $pathUp = array();
    // $genUp = array();
    // $pathdown = array();
    // $genedown = array();
    for($i=0; $i<sizeof($resultArray); $i++){
        if (preg_match("/Upregulated/i", $resultArray[$i][8])) {
            if(!empty($resultArray[$i][0])){
                $pathUp[] = $resultArray[$i][0];
                $genUp[] = $resultArray[$i][7];
            }
        }

        if (preg_match("/Downregulated/i", $resultArray[$i][8])){
            if(!empty($resultArray[$i][0])){
                $pathdown[] = $resultArray[$i][0];
                $genedown[] = $resultArray[$i][7];
            }
        }
    }

    $pathAll = array();
    $geneAll = array();

    if(!empty($pathUp)){
        $pathau=array();
        $geneUp=array();

        for($i=0; $i < (int)$upRange; $i++){
            $pathau[] = $pathUp[$i];
            $geneUp[] = $genUp[$i];
        }

        for($i=0; $i<sizeof($pathau); $i++){
            $pathAll[] = $pathau[$i];
            $geneAll[] = $geneUp[$i];
        }
    }

    if(!empty($pathdown)){
        $pathad=array();
        $geneDn=array();
        for($i=0; $i < (int)$downRange; $i++){
            $pathad[] = $pathdown[$i];
            $geneDn[] = $genedown[$i];
        }
    
        for($i=0; $i<sizeof($pathad); $i++){
            $pathAll[] = $pathad[$i];
            $geneAll[] = $geneDn[$i];
        }
    }

    $linka=array();
    $genea=array();
    for($i=0; $i<sizeof($geneAll); $i++){
        $g = explode(", ",$geneAll[$i]);
        foreach ($g as $singleGene){
            $linka[] = $pathAll[$i]."***".$singleGene;
            $genea[] = $singleGene;
        }
    }

    $genea = array_unique($genea);
    array_values($genea);
    $pathau = array_unique($pathau);
    array_values($pathau);
    $pathad = array_unique($pathad);
    array_values($pathad);
    $linka = array_unique($linka);
    array_values($linka);

    $pathGen = array();
    if(!empty($pathau)){
        foreach ($pathau as $l){
            $pathGen["nodes"][] = array("id" => $l,"group" => "pathwayup", "label"=> $l);
        }    
    }

    if(!empty($pathad)){
        foreach ($pathad as $l){
            $pathGen["nodes"][] = array("id" => $l,"group" => "pathwaydown", "label"=> $l);
        }
    }
    
    foreach ($genea as $g){
        foreach($degList as $genMatch){
            if(preg_match("/$g/i", $genMatch[0])){
                if(preg_match("/Upregulated/i", $genMatch[7])){
                    $pathGen["nodes"][] = array("id" => $g,"group" => "genesUpReg", "label"=> $g);
                }else{
                    $pathGen["nodes"][] = array("id" => $g,"group" => "genesDownReg", "label"=> $g);
                }
                break;
            }
        }
    }
    
    $d=1;
    foreach ($linka as $li){
        $ll = explode("***", $li);
        $pathGen["links"][] = array("from" => $ll[1],"to" => $ll[0]);
        $d++;
    }

    // echo "<pre>";
    // print_r($rangeboth);
    // echo "<pre>";

    // $fp = fopen('results.json', 'w');
    // fwrite($fp, json_encode($abc, JSON_PRETTY_PRINT));
    // fclose($fp);

    include('header.php');
?>

<style>
    .selLimit {
        display: -webkit-box;
    }

    .submitLimt {
        margin-left: 15px;
    }

    .selLmtUp,
    .selLmtDown {
        padding: 4px 0px;
    }

    .submitLimt {
        font-size: 12px;
        border: 1px solid black;
        padding: 5px;
        color: black;
        border-radius: 4px;
        cursor: pointer;
    }

    .links line {
        stroke: #999;
        stroke-opacity: 0.5;
        fill-opacity: 0.5;
    }

    .nodes circle {
        stroke: #fff;
        stroke-width: 0.5px;
        stroke-opacity: 1;
        fill-opacity: 1;
    }

    text {
        font-family: sans-serif;
        font-size: 8px;
        fill-opacity: 0.7;
        stroke-opacity: 0.5;
    }

    #loadingBar {
        width: 100px;
        height: 100px;
        /* background-color: red; */
        position: absolute;
        top: 0;
        bottom: 0;
        left: -20%;
        right: 0;
        margin: auto;
    }

    #mynetwork {
        /* width: 100%; */
        height: 1000px;
        border: 1px solid #444444;
        border-radius: 8px;
        /* background-color: #222222; */
    }

    #text {
        position: absolute;
        top: 8px;
        left: 530px;
        width: 30px;
        height: 50px;
        margin: auto auto auto auto;
        font-size: 22px;
        color: #000000;
    }

    div.outerBorder {
        position: relative;
        /* top: 400px; */
        width: 600px;
        height: 44px;
        margin: auto auto auto auto;
        /* border: 8px solid rgba(0, 0, 0, 0.1); */
        background: rgb(252, 252, 252);
        /* Old browsers */
        background: -moz-linear-gradient(top,
                rgba(252, 252, 252, 1) 0%,
                rgba(237, 237, 237, 1) 100%);
        /* FF3.6+ */
        background: -webkit-gradient(linear,
                left top,
                left bottom,
                color-stop(0%, rgba(252, 252, 252, 1)),
                color-stop(100%, rgba(237, 237, 237, 1)));
        /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top,
                rgba(252, 252, 252, 1) 0%,
                rgba(237, 237, 237, 1) 100%);
        /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top,
                rgba(252, 252, 252, 1) 0%,
                rgba(237, 237, 237, 1) 100%);
        /* Opera 11.10+ */
        background: -ms-linear-gradient(top,
                rgba(252, 252, 252, 1) 0%,
                rgba(237, 237, 237, 1) 100%);
        /* IE10+ */
        background: linear-gradient(to bottom,
                rgba(252, 252, 252, 1) 0%,
                rgba(237, 237, 237, 1) 100%);
        /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fcfcfc', endColorstr='#ededed', GradientType=0);
        /* IE6-9 */
        border-radius: 72px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

    #border {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 500px;
        height: 23px;
        margin: auto auto auto auto;
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    #bar {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 20px;
        height: 20px;
        margin: auto auto auto auto;
        border-radius: 11px;
        border: 2px solid rgba(30, 30, 30, 0.05);
        background: rgb(0, 173, 246);
        /* Old browsers */
        box-shadow: 2px 0px 4px rgba(0, 0, 0, 0.4);
    }

    .headGPNEt {
        margin: 15px 0px 15px 0px;
        padding: 5px 0px 6px 10px;
        background: rgb(241, 232, 209);
        border: 1px solid rgb(185, 156, 107);
        border-radius: 5px;
    }

    .triangle-up {
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 16px solid #fb6060;
    }

    .triangle-down {
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 16px solid #5252ff;
    }

    .circular-up {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: red;
        border: 2px solid black;
        margin-top: -3px;
    }

    .circular-down {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: blue;
        border: 2px solid black;
        margin-top: -3px;
    }

    .legendGPNet {
        display: -webkit-box;
        /* float: right; */
        margin-bottom: 12px;
        float: right;
    }

    label {
        margin-left: 5px;
    }

    .triangleUpR,
    .triangleDownR,
    .circularGene {
        display: -webkit-box;
    }

    .triangleDownR,
    .circularGene {
        margin-left: 12px;
    }
</style>

<table width="98%" border="0" cellspacing="0" cellpadding="6" align="center">
    <tr>
        <td align="center">
            <p align="justify">&nbsp;</p>
            <h3 class="headGPNEt">Gene - Pathway Network</h3>
            <!-- <div class="selLimit"> -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="RegulimtForm" class="selLimit"
                method="post">
                <?php
                    if(!empty($pathUp)){
                        ?>
                            <div class="selLmtUp">
                                <label for="UpRegulation">Top upregulated: </label>
                                <select id="upRegSel">
                                    <option value="0">Select option</option>
                                    <?php
                                        for ($i=1; $i<= sizeof($pathUp); $i++){
                                    ?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        <?php
                    }
                ?>

                <?php
                    if(!empty($pathdown)){
                        ?>
                            <div class="selLmtDown">
                                <label for="downRegulation">Top downregulated: </label>
                                <select id="downRegSel">
                                    <option value="0">Select option</option>
                                    <?php
                                        for ($i=1; $i<= sizeof($pathdown); $i++){
                                    ?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        <?php
                    }
                ?>

                <button class="btn submitLimt" type="submit">show</button>
                <!-- <input class="btn submitLimt" name="submit" type="submit"> -->
                <input type="hidden" id="rndNum" name="rdNum" value="<?php echo $sessionNum;?>">
                <input type="hidden" id="upReg" name="upReg">
                <input type="hidden" id="DwnReg" name="DwnReg">
            </form>
            <!-- </div> -->
            <div class="legendGPNet">
                <div class="triangleUpR">
                    <div class="triangle-up"></div>
                    <label>Upregulated gene</label>
                </div>

                <div class="triangleDownR">
                    <div class="triangle-down"></div>
                    <label>Downregulated gene</label>
                </div>

                <div class="circularGene">
                    <div class="circular-up"></div>
                    <label>Upregulated pathway</label>
                </div>

                <div class="circularGene">
                    <div class="circular-down"></div>
                    <label>Downregulated pathway</label>
                </div>
            </div>


            <div style="float:right; width: 100%;">
                <div id="mynetwork"></div>
                <div id="loadingBar">
                    <div class="outerBorder">
                        <div id="text">0%</div>
                        <div id="border">
                            <div id="bar"></div>
                        </div>
                    </div>
                </div>
            </div>
            <p align="justify">&nbsp;</p>
        </td>
    </tr>
</table>


<script>
    // var bothNum = JSON.parse(document.getElementById("rangeboth").value);
    var bothNum = <?= json_encode($rangeboth); ?>;
    if ($('#upRegSel').find(":selected").val() == 0 || $('#downRegSel').find(":selected").val() == 0) {
        <?php
            if(!empty($pathUp)){
                ?>
                    var upRegSel = document.getElementById('upRegSel');
                    for (var i, j = 0; i = upRegSel.options[j]; j++) {
                        if (i.value == bothNum[0]) {
                            upRegSel.selectedIndex = j;
                            break;
                        }
                    }
                <?php
            }
        ?>

        <?php
            if(!empty($pathdown)){
                ?>
                    var downRegSel = document.getElementById('downRegSel');
                    for (var i, j = 0; i = downRegSel.options[j]; j++) {
                        if (i.value == bothNum[1]) {
                            downRegSel.selectedIndex = j;
                            break;
                        }
                    }
                <?php
            }
        ?>
    }
    

    $('.submitLimt').click(function (event) {
        if ($('#upRegSel').find(":selected").val() == 0) {
            alert("Select another option");
            event.preventDefault();
            return false;
        }else{
            var upRegSelDiv = document.getElementById('upRegSel');
            var upSeltd = upRegSelDiv.options[upRegSelDiv.selectedIndex].value;
            $('#upReg').val(upSeltd);
        }

        if ($('#downRegSel').find(":selected").val() == 0) {
            alert("Select another option");
            event.preventDefault();
            return false;
        }else{
            var dwnRegSelDiv = document.getElementById('downRegSel');
            var dwnSeltd = dwnRegSelDiv.options[dwnRegSelDiv.selectedIndex].value;
            $('#DwnReg').val(dwnSeltd);
        }
        var rdNum = document.getElementById('rndNum').value;
    })
</script>


<script>
    function draw() {
        // var result = JSON.parse(document.getElementById("abc").value);
        var result = <?= json_encode($pathGen); ?>;
        var nodes = result["nodes"];
        var edges = result["links"];

        // create a network
        var container = document.getElementById("mynetwork");
        var data = {
            nodes: nodes,
            edges: edges,
        };
        var options = {
            nodes: {
                shape: "dot",
                size: 18,
                font: {
                    size: 15,
                    color: "#222222",
                },
                borderWidth: 2,
            },
            edges: {
                width: 2,
            },
            groups: {
                pathwayup: {
                    color: {
                        background: "red",
                        border: "black"
                    },
                    font: {
                        size: 20,
                        fontWeight: "bold"
                    },
                },
                genesUpReg: {
                    shape: "triangle",
                    color: {
                        background: "#fb6060",
                        border: "#b3b3b3"
                    },
                },
                genesDownReg: {
                    shape: "triangle",
                    color: {
                        background: "#5252ff",
                        border: "#b3b3b3"
                    },
                },
                pathwaydown: {
                    color: {
                        background: "blue",
                        border: "black"
                    },
                    font: {
                        size: 20,
                        fontWeight: "bold"
                    },
                },
            },
        };
        var network = new vis.Network(container, data, options);

        network.on("stabilizationProgress", function (params) {
            var maxWidth = 496;
            var minWidth = 20;
            var widthFactor = params.iterations / params.total;
            var width = Math.max(minWidth, maxWidth * widthFactor);

            document.getElementById("bar").style.width = width + "px";
            document.getElementById("text").innerText =
                Math.round(widthFactor * 100) + "%";
        });
        network.once("stabilizationIterationsDone", function () {
            document.getElementById("text").innerText = "100%";
            document.getElementById("bar").style.width = "496px";
            document.getElementById("loadingBar").style.opacity = 0;

            // really clean the dom element
            setTimeout(function () {
                document.getElementById("loadingBar").style.display = "none";
            }, 500);
        });
    }
    window.addEventListener("load", () => {
        draw();
    });
</script>

<?php
    include('footer.php');
?>