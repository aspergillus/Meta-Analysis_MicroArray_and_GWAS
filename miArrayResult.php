<!-- Start the library -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/bootstrap_4.0.css">
<link rel="stylesheet" href="css/bootstrap_3.4.1.css">

<!-- JS include -->
<script src="js/jquery_3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="js/saveSvgAsPng.js"></script> <!--  Save SVG to PNG like for Volcano Plot -->
<script src="js/bootstrap_3.4.1.js"></script>
<script src="js/plotly-2.0.0.min.js"></script>

<!-- <link rel="stylesheet" href="css/bootstrap.min_3.2.0.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min_3.2.0.css"> -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- HighCharts JS Library -->
<script src="js/highcharts.js"></script>
<script src="js/highcharts-more.js"></script>
<script src="js/dumbbell.js"></script>
<script src="js/lollipop.js"></script>
<script src="js/exporting.js"></script>

<link rel="stylesheet" href="css/jquery.dataTables.min.css">
<link rel="stylesheet" href="css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="css/searchBuilder.dataTables.min.css">

<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="js/dataTables_searchBuilder_min.js"></script>

<link rel="stylesheet" href="miArrayResultStyle.css">
<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    // ini_set('max_execution_time', -1);
    error_reporting(0);
    header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure");
    include("connect.php");
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Check if form was submited 
        if(isset($_POST['cel_Submit'])) {
            
            // will destroy all the session;
            if (isset($_SESSION["randNum"])){
                if (basename($_SERVER['PHP_SELF']) != $_SESSION["randNum"]) {
                    unset($_SESSION['randNum']);
                    unset($_SESSION['analTitle']);
                    unset($_SESSION['tPvalue']);
                    unset($_SESSION['tLogfcu']);
                    unset($_SESSION['tLogfcd']);
                    unset($_SESSION['conGrpName']);
                    unset($_SESSION['caseGrpName']);
                    unset($_SESSION['numControl']);
                    unset($_SESSION['numCase']);
                    unset($_SESSION['resultArray']);
                    unset($_SESSION['degArray']);
                    unset($_SESSION['gseaResultSession']);
                    unset($_SESSION['gseaMainResultSession']);
                }
            }
            echo "<input id='analType' type='hidden' value='celAnal'>";
            
            // study Count
            $AnalCount = $_POST['cel_AnalCount'];
            $_SESSION['tPvalue'] = $_POST['tPvalue_CEL'];
            $_SESSION['tLogfcu'] = $_POST['tLogfcu_CEL'];
            $_SESSION['tLogfcd'] = $_POST['tLogfcd_CEL'];

            // Blank Array
            $methType = array();                   // Collect horn's method result
            $hyperArr = array();                   // Genes Hyper-Linked 
            $screePltCont = array();               // Scree Plot array
            $screePltAxisCont = array();           // Scree Plot X-axis label 
            $numPCA = array();                     // Number of PC count
            $pltArr = array();                     // Score plot array   
            $frwdStr = array();                    // Score plot(PC1 and PC2) default select
            $lolliArrVari = array();               // Lollipop plot array        

            for($m=0; $m <= $AnalCount; $m++) {
                // Session for randNum
                $rand_name = rand(1, 10000);
                $_SESSION["randNum"][] = $rand_name;
                $upload_dir='fileUpload/'. $rand_name .'/';
                
                // Configure upload directory and allowed file types
                $allowed_types=array('CEL', 'gz', 'txt');

                // // Define maxsize for files i.e 10MB 
                // $maxsize=10 * 1024 * 1024;

                // Checks if user sent an empty form  
                if( !empty(array_filter($_FILES['files'.$m]['name']))) {
                    if(mkdir($upload_dir, 0777)) {

                        // Loop through each file in files[] array
                        $opened_txt="";

                        foreach ($_FILES['files'.$m]['tmp_name'] as $key=> $value) {

                            // $file_name = file_get_contents() $_FILES['files']['tmp_name'];
                            $file_tmpname=$_FILES['files'.$m]['tmp_name'][$key];
                            $file_name=$_FILES['files'.$m]['name'][$key];
                            $file_size=$_FILES['files'.$m]['size'][$key];
                            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

                            // Set upload file path 
                            $filepath=$upload_dir.$file_name;

                            // open dataset file and will check for first line of dataset file
                            if($file_ext=="gz"|| $file_ext=="CEL") {
                                $gz_open=gzopen($file_tmpname, "r");
                                $opened_cel=fgets($gz_open);
                            }

                            // $lastIndexArr = end($_FILES['files']['name']);
                            if($file_ext=="txt") {
                                $opnd_txt=file($file_tmpname);
                                $opened_txt=trim($opnd_txt[0]);
                            }

                            // if ($file_size > $maxsize) echo "Error: File size is larger than the allowed limit.";

                            if(strtolower($file_ext) && $allowed_types) {
                                if(move_uploaded_file($file_tmpname, $filepath)) {
                                    // echo "<br>{$file_name} successfully uploaded <br />";
                                }

                                $content='Sample'."\t".'SUBTYPE'."\n";
                                $array=$_POST['control'.$m];
                                $myArray=explode(',', $array);

                                foreach($myArray as $value) {
                                    $content .=$value."\t"."1 \n";
                                }

                                $array_2=$_POST['treatment'.$m];
                                $myArray_2=explode(',', $array_2);

                                foreach($myArray_2 as $value) {
                                    $content .=$value."\t"."2 \n";
                                }

                                file_put_contents("$upload_dir".'group_file.txt', $content);
                                // echo "Your File has been Created";
                            }
                        }

                        // Get variable for index.php
                        $nControl=$_POST['control'.$m];
                        $ctlArryay=explode(',', $nControl);
                        $_SESSION["controlList".$m][]=$ctlArryay;
                        $numControl=sizeof($ctlArryay);

                        $ctlCaArryay=$_POST['treatment'.$m];
                        $numCaseArr=explode(',', $ctlCaArryay);
                        $_SESSION["treatmentList".$m][]=$numCaseArr;
                        $numCase=sizeof($numCaseArr);

                        $conGrpName = $_POST['frstGrpName'.$m];
                        $caseGrpName = $_POST['SecGrpName'.$m];
                        $analTitle = $_POST['titleAlyName'.$m];
                        $tPvalue = $_SESSION['tPvalue'];
                        $tLogfcu = $_SESSION['tLogfcu'];
                        $tLogfcd = $_SESSION['tLogfcd'];
                        
                    }
                } else {
                    echo "No files selected.";
                }

                $variOutR = array();
                // echo shell_exec('Rscript ./R_files/affyMat.R '."$rand_name $tPvalue $tLogfcu $tLogfcd 2>&1");
                // echo shell_exec('Rscript ./R_files/gseaR.R '."$rand_name 2>&1");

                $abc = exec('Rscript ./R_files/affyMat.R '."$rand_name $tPvalue $tLogfcu $tLogfcd", $variOutR);
                $xyz = exec('Rscript ./R_files/gseaR.R '."$rand_name");

                $methType[$m] = preg_replace('/\s+/', '', $variOutR[1]);

                //////////////////////////////////////////////// DEG(Differentially Expressed Genes) ////////////////////////////////////////////
            
                // Extract Result Data from TSV file
                $result = array();
                $file = "fileUpload/".$rand_name."/Significant_DEGs.txt";
                $fh = fopen($file, 'r');
                // $l = 0;
                while (($line = fgetcsv($fh, 10000, "\t")) !== false) {
                    $result[] = $line;
                    // if($l == 20) { break; }
                    // $l++;
                }
        
                // Added Regulation Column in the table
                array_push($result[0],"Regulation Status");
                for($i=1; $i<sizeof($result); $i++){
                    if($result[$i][1] > 0){
                        array_push($result[$i],"Upregulated");
                    }else{
                        array_push($result[$i],"Downregulated");
                    }
                }
                $_SESSION["degArray"][] = $result;
        
                // Convert Long Number into Exponential Number
                for($i=1; $i<sizeof($result); $i++){
                    for($j=1; $j<7; $j++){
                        if($j == 4 || $j == 5){
                            $result[$i][$j] = $result[$i][$j];
                        }else{
                            $result[$i][$j] = bcdiv($result[$i][$j], 1, 3);
                        }
                    }
                }

                // // HyperLink to the Genes 
                // for($i=1; $i<sizeof($result); $i++){
                //     $vari = $result[$i][0];
        
                //     // NCBI
                //     $qure4 = "SELECT GeneID FROM entrez_id WHERE Symbol = '$vari' OR Aliases = '$vari'" ;
                //     $res4 = mysqli_query($conn, $qure4); 
                //     $cnt3 = mysqli_num_rows($res4);
                //     while($row4 = mysqli_fetch_array($res4)){
                //         $GenID = $row4["GeneID"];
                //         $hyperArr[$m][$i][] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$vari</a>";
                //     }
        
                //     // KEGG 
                //     $qure4 = "SELECT pathwayid FROM `kegg_path` WHERE geneSymbol = '$vari'";
                //     $res4 = mysqli_query($conn, $qure4); 
                //     $cnt3 = mysqli_num_rows($res4);
                //     while($row4 = mysqli_fetch_array($res4)){
                //         $pathID = $row4["pathwayid"];
                //         $hyperArr[$m][$i][] = "<a href=https://www.genome.jp/pathway/$pathID target=_blank>$pathID</a>";
                //     }
                    
                // }

                //////////////////////// Extract Scree Plot Data from TSV file //////////////////////
                $resultScree = array();
                $file="fileUpload/".$rand_name."/variance.txt";
                $fh=fopen($file, 'r');
                while (($line=fgetcsv($fh, 100000000, "\t")) !==false) {
                    $resultScree[]=$line;
                }
                array_shift($resultScree);
                $screePltArr=array();
                $screeXaxisLbel=array();
                $columnArr=array();
                $splineArr=array();
                for($i=0; $i<sizeof($resultScree); $i++) {
                    $screeXaxisLbel[]=$resultScree[$i][1];
                    $columnArr[]=(float)bcdiv($resultScree[$i][0], 1, 3);
                    $splineArr[]=(float)bcdiv($resultScree[$i][2], 1, 3);
                }

                if ( !empty($columnArr) && !empty($splineArr)) {
                    array_push($screePltArr, $columnArr);
                    array_push($screePltArr, $splineArr);
                }

                $screePltCont[$m] = $screePltArr;
                $screePltAxisCont[$m] = $screeXaxisLbel;

                ////////////////////// Extract Number of PCA from TSV file for Score Plot  ////////////////////
                $numPC = array();
                $file = "fileUpload/".$rand_name."/numPC.txt";
                $fh = fopen($file, 'r');
                while (($line = fgetcsv($fh, 1000, "\t")) !== false) {
                    $numPC[] = $line;
                }
                array_shift($numPC);
                $ttlNumPC = array();
                foreach($numPC as $numkey) {
                    array_push($ttlNumPC, $numkey[0]);
                }
                $numPCA[$m] = $ttlNumPC;                          // Store array

                $forwardStr = 'PC1,PC2';
                $abc = exec('Rscript ./R_files/pc_CEL.R '."$rand_name $forwardStr");

                $scorReslt = array();
                $file = "fileUpload/".$rand_name."/scoreplot_data_new.txt";
                $fh = fopen($file, 'r');
                while (($line = fgetcsv($fh, 1000, "\t")) !== false) {
                    $scorReslt[] = $line;
                }
                array_shift($scorReslt);

                $scorpltArr = array();
                $contPC = array();
                $casePC = array();
                for ($i = 0; $i < sizeof($scorReslt); $i++) {
                    if (preg_match("/1/i", $scorReslt[$i][3])) {
                        $contPC[] = array('x' => (float) bcdiv($scorReslt[$i][1], 1, 3), 'y' => (float) bcdiv($scorReslt[$i][2], 1, 3), 'z' => 2, 'nes' => 'PC1');
                    } else {
                        $casePC[] = array('x' => (float) bcdiv($scorReslt[$i][1], 1, 3), 'y' => (float) bcdiv($scorReslt[$i][2], 1, 3), 'z' => 2, 'nes' => 'PC2');
                    }
                }

                if (!empty($contPC)) {
                    $contPCColr = array('color' => '#bb99ff', 'data' => $contPC, 'name' => $conGrpName);
                    array_push($scorpltArr, $contPCColr);
                }

                if (!empty($casePC)) {
                    $casePCColr = array('color' => '#ffb399', 'data' => $casePC, 'name' => $caseGrpName);
                    array_push($scorpltArr, $casePCColr);
                }

                $pltArr[$m] = $scorpltArr;
                $frwdStr[$m] = $forwardStr;

                // Session
                $_SESSION["analTitle"][] = $analTitle;
                $_SESSION["conGrpName"][] = $conGrpName;
                $_SESSION["caseGrpName"][] = $caseGrpName;
                $_SESSION["numControl"][] = $numControl;
                $_SESSION["numCase"][] = $numCase;
                $_SESSION["resultArray"][] = $result;
        
                //////////////////////////////////////////////// GSEA(Gene Set Enrichment Analysis) ////////////////////////////////////////////
        
                // Extract Result Data from CSV file
                $gseaResult = array();
                $gseafh = "fileUpload/".$rand_name."/gseaResult.csv";
                $gseafile = fopen($gseafh, 'r');
                // $l = 0;
                while (($line = fgetcsv($gseafile)) !== FALSE) {
                    $gseaResult[] = str_ireplace("'",  "&apos;", $line);
                    // if($l == 20) { break; }
                    // $l++;
                }
                fclose($gseafile);
                $_SESSION["gseaResultSession"][] = $gseaResult;
        
                // Convert Big Number into Exponential Value like power value
                for($i=1; $i<sizeof($gseaResult); $i++){
                    for($h=3; $h<6; $h++){
                        $gseaResult[$i][$h] = bcdiv($gseaResult[$i][$h], 1, 3);
                    }

                    if(is_numeric($gseaResult[$i][2])){
                        $val = $gseaResult[$i][2];
                        $gseaResult[$i][2] = "<a href=https://reactome.org/content/detail/R-HSA-$val target=_blank>$val</a>";
                    }elseif(preg_match("/^R-HSA/i", $gseaResult[$i][2])){
                        $val = $gseaResult[$i][2];
                        $gseaResult[$i][2] = "<a href=https://reactome.org/content/detail/$val target=_blank>$val</a>";
                    }elseif(preg_match("/PWY/i", $gseaResult[$i][2])){
                        $val = $gseaResult[$i][2];
                        $gseaResult[$i][2] = "<a href=https://humancyc.org/HUMAN/NEW-IMAGE?type=PATHWAY&object=$val target=_blank>$val</a>";
                    }elseif(preg_match("/^WP/i", $gseaResult[$i][2])){
                        $val = $gseaResult[$i][2];
                        $gseaResult[$i][2] = "<a href=https://www.wikipathways.org/index.php/Pathway:$val target=_blank>$val</a>";
                    }elseif(preg_match("/^BIOCARTA/i", $gseaResult[$i][2]) || preg_match("/^HALLMARK/i", $gseaResult[$i][2]) || preg_match("/^PID_/i", $gseaResult[$i][2]) ){
                        $val = $gseaResult[$i][2];
                        $gseaResult[$i][2] = "<a href=https://www.gsea-msigdb.org/gsea/msigdb/cards/$val target=_blank>$val</a>";
                    }elseif(preg_match("/^P/i", $gseaResult[$i][2])){
                        $val = $gseaResult[$i][2];
                        $gseaResult[$i][2] = "<a href=http://www.pantherdb.org/pathway/pathwayDiagram.jsp?catAccession=$val target=_blank>$val</a>";
                    }else{
                        $gseaResult[$i][2] = $gseaResult[$i][2];
                    }
                }

                $_SESSION["gseaMainResultSession"][] = $gseaResult;

                ////////////////////  Lollipop Plot  //////////////////////////
                $lolliResult = array();
                $file = "fileUpload/".$rand_name."/lollipop_Plot_data.txt";
                $fh = fopen($file, 'r');
                while (($line = fgetcsv($fh, 1000, "\t")) !== false) {
                    $lolliResult[] = $line;
                }
                array_shift($lolliResult);
        
                $lolliUp = array(); 
                $lolliDown = array(); 
                $lolliArr = array(); 
        
                foreach($lolliResult as $lolliVal){
                    if(preg_match("/Upregulated/i", $lolliVal[2])){
                        $lolliUp[] =  array('name'=>$lolliVal[0], 'low'=>(float)bcdiv($lolliVal[1], 1, 4));
                    }else{
                        $lolliDown[] =  array('name'=>$lolliVal[0], 'low'=>(float)bcdiv($lolliVal[1], 1, 4));
                    }
                }
        
                if (!empty($lolliUp)) {
                    $lolliUpColr = array('color'=>'red','name' =>'Upregulated','marker' => array('symbol'=>'circle'), 'data' => $lolliUp);
                    array_push($lolliArr, $lolliUpColr);
                }

                if (!empty($lolliUp) && !empty($lolliDown)) {
                    $lolliDownColr = array('color'=>'blue','name' =>'Downregulated','marker' => array('symbol'=>'circle'), 'data' => $lolliDown);
                    array_push($lolliArr, $lolliDownColr);
                }

                $lolliArrVari[$m] = $lolliArr;
            }
        }elseif(isset($_POST['degList_Submit'])){
            ////////////////////////////////// Direct DEG_list Uplaod //////////////////////////////////////////

            // will destroy all the session;
            if (isset($_SESSION["degList_Session_Array"])) {
                if (basename($_SERVER['PHP_SELF']) != $_SESSION["degList_Session_Array"]) {
                    unset($_SESSION['randNum']);
                    unset($_SESSION['analTitle']);
                    unset($_SESSION['tPvalue_deg']);
                    unset($_SESSION['tLogfcu_deg']);
                    unset($_SESSION['tLogfcd_deg']);
                    unset($_SESSION['degArray']);
                    unset($_SESSION['degList_Session_Array']);

                }
            }
            echo "<input id='degList_Anal' type='hidden' value='degList'>";

            // study Count
            $AnalCount = $_POST['degList_AnalCount'];
            for($m=0; $m <= $AnalCount; $m++) {
                // Session for randNum
                $rand_name = rand(1, 10000);
                $_SESSION["randNum"][] = $rand_name;
                $upload_dir='fileUpload/'. $rand_name .'/';

                // Configure upload directory and allowed file types
                $allowed_types=array('CEL', 'gz', 'txt');

                // Define maxsize for files i.e 10MB 
                $maxsize=10 * 1024 * 1024;

                if(mkdir($upload_dir, 0777)) {
                    $temp = explode(".", $_FILES['degList'.$m]["name"]);
                    $newfilename = "degList" . '.' . end($temp);
                    move_uploaded_file($_FILES['degList'.$m]["tmp_name"], $upload_dir.$newfilename);
                    // echo "File is Successfully upload to: ".$upload_dir.$newfilename;
                }

                // Extract Result Data from TSV file
                $awdxResult = array();
                $file = "fileUpload/".$rand_name."/degList.txt";
                $fh = fopen($file, 'r');
                while (($line = fgetcsv($fh, 100000000, "\t")) !== false) {
                    $awdxResult[] = $line;
                }
                $headerResult = array_shift($awdxResult);
                
                $analTitle = $_POST['degListName'.$m];
                $tPvalue = $_POST['tPvalue_deg'];
                $tLogfcu = $_POST['tLogfcu_deg'];
                $tLogfcd = $_POST['tLogfcd_deg'];

                $result = array();
                for($i=1; $i<sizeof($awdxResult); $i++){
                    if($awdxResult[$i][2] < $tPvalue){
                        if($awdxResult[$i][1] >= $tLogfcu || $awdxResult[$i][1] <= $tLogfcd){
                            $result[] = $awdxResult[$i];
                        }
                    }
                }
                array_unshift($result , $headerResult);

                // Added Regulation Column in the table
                array_push($result[0],"Regulation Status");
                for($i=1; $i<sizeof($result); $i++){
                    if($result[$i][1] > 0){
                        array_push($result[$i],"Upregulated");
                    }else{
                        array_push($result[$i],"Downregulated");
                    }
                }

                // Convert Long Number into Exponential Number
                for($i=1; $i<sizeof($result); $i++){
                    for($j=1; $j<3; $j++){
                        $result[$i][$j] = sprintf("%.2E",$result[$i][$j]);
                    }
                }
                $_SESSION["degArray"][] = $result;

                // echo "<pre>";
                // print_r($result);
                // echo "</pre>";

                $xyz = exec('"C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/R_file/convtDEGintoModT.R" '."$rand_name");

                // HyperLink to the Genes 
                $newResult = array();
                $GenID = array();
                $newResult2 = array();
            
                for($i=1; $i<sizeof($result); $i++){
                    array_push($newResult, $result[$i][0]);
                }
                
                for($i=0; $i<sizeof($newResult); $i++){
                    $vari =  $newResult[$i];
                    $qure4 = "SELECT GeneID FROM entrez_id WHERE Symbol = '$vari'";
                    $res4 = mysqli_query($conn, $qure4); 
                    $cnt3=mysqli_num_rows($res4);
                    while($row4 = mysqli_fetch_array($res4))
                    {
                        $GenID[] = $row4["GeneID"];
                    }
                }
            
                for($i=0; $i<sizeof($newResult); $i++){
                    $newGenID = $GenID[$i];
                    $result2 = $newResult[$i];
                    $newResult2[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$newGenID target=_blank>$result2</a>";
                }
            
                for($i=1; $i<=sizeof($newResult); $i++){
                    $result[$i][0] = $newResult2[$i-1];
                }

                // Session for Result DEG_list
                $_SESSION["analTitle"][] = $analTitle;
                $_SESSION["degList_Session_Array"][] = $result;
            }

            // Hidden Input using Session of All 
            for($i=0; $i<sizeof($_SESSION["analTitle"]); $i++){
                $titleIndex = "title".$i;
                echo sprintf("<input id='$titleIndex' type='hidden' value='%s'/>", json_encode($_SESSION["analTitle"][$i]));
            }

            for($i=0; $i<sizeof($_SESSION["tPvalue"]); $i++){
                $titleIndex = "tPvalue".$i;
                echo sprintf("<input id='$titleIndex' type='hidden' value='%s'/>", json_encode($_SESSION["tPvalue"][$i]));
            }

            for($i=0; $i<sizeof($_SESSION["tLogfcu"]); $i++){
                $titleIndex = "tLogfcu".$i;
                echo sprintf("<input id='$titleIndex' type='hidden' value='%s'/>", json_encode($_SESSION["tLogfcu"][$i]));
            }

            for($i=0; $i<sizeof($_SESSION["tLogfcd"]); $i++){
                $titleIndex = "tLogfcd".$i;
                echo sprintf("<input id='$titleIndex' type='hidden' value='%s'/>", json_encode($_SESSION["tLogfcd"][$i]));
            } 

            for($i=0; $i<sizeof($_SESSION["degList_Session_Array"]); $i++){
                $resultIndex = "phpResult".$i;
                echo sprintf("<input id='$resultIndex' type='hidden' value='%s'/>", json_encode($_SESSION["degList_Session_Array"][$i]));
            }
        }elseif(isset($_POST['rnk_Submit'])){
            ////////////////////////////////// Direct DEG_list Uplaod //////////////////////////////////////////

            // will destroy all the session;
            if (isset($_SESSION["gseaMainResultSession"])) {
                if (basename($_SERVER['PHP_SELF']) != $_SESSION["gseaMainResultSession"]) {
                    unset($_SESSION['randNum']);
                    unset($_SESSION['analTitle']);
                    unset($_SESSION['gseaResultSession']);
                    unset($_SESSION['gseaMainResultSession']);
                }
            }
            
            echo "<input id='rnkFile_Anal' type='hidden' value='rnkFile'>";

            // study Count
            $AnalCount = $_POST['rnk_AnalCount'];
            for($m=0; $m <= $AnalCount; $m++) {
                // Session for randNum
                $rand_name = rand(1, 10000);
                $_SESSION["randNum"][] = $rand_name;
                $upload_dir='fileUpload/'. $rand_name .'/';

                // Configure upload directory and allowed file types
                $allowed_types = array('rnk', 'txt');

                // Define maxsize for files i.e 10MB 
                $maxsize=10 * 1024 * 1024;

                if(mkdir($upload_dir, 0777)) {
                    $temp = explode(".", $_FILES['rnkFle'.$m]["name"]);
                    $newfilename = "Significant_DEGs" . '.' . end($temp);
                    move_uploaded_file($_FILES['rnkFle'.$m]["tmp_name"], $upload_dir.$newfilename);
                    // echo "File is Successfully upload to: ".$upload_dir.$newfilename;
                }

                $xyz = exec('"C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/R_file/gseaR.R" '."$rand_name");
                
                // Extract Result Data from CSV file
                $gseaResult = array();
                $gseafh = "fileUpload/".$rand_name."/gseaResult.csv";
                $gseafile = fopen($gseafh, 'r');
                // $l = 0;
                while (($line = fgetcsv($gseafile)) !== FALSE) {
                    $gseaResult[] = str_ireplace("'",  "&apos;", $line);
                    // if($l == 20) { break; }
                    // $l++;
                }
                fclose($gseafile);
                $_SESSION["gseaResultSession"][] = $gseaResult;
        
                // Convert Big Number into Exponential Value like power value
                for($g=1; $g<sizeof($gseaResult); $g++){
                    for($h=3; $h<6; $h++){
                        $gseaResult[$g][$h] = sprintf("%.2E",$gseaResult[$g][$h]);
                    }
                }

                // // HyperLink to the Database ID
                // $pathID = array();
                // for($i=1; $i<sizeof($gseaResult); $i++){
                //     if(is_numeric($gseaResult[$i][2])){
                //         $val = $gseaResult[$i][2];
                //         $pathID[] = "<a href=https://reactome.org/content/detail/R-HSA-$val target=_blank>$val</a>";
                //     }elseif(preg_match("/^R-HSA/i", $gseaResult[$i][2])){
                //         $val = $gseaResult[$i][2];
                //         $pathID[] = "<a href=https://reactome.org/content/detail/$val target=_blank>$val</a>";
                //     }elseif(preg_match("/PWY/i", $gseaResult[$i][2])){
                //         $val = $gseaResult[$i][2];
                //         $pathID[] = "<a href=https://humancyc.org/HUMAN/NEW-IMAGE?type=PATHWAY&object=$val target=_blank>$val</a>";
                //     }elseif(preg_match("/^WP/i", $gseaResult[$i][2])){
                //         $val = $gseaResult[$i][2];
                //         $pathID[] = "<a href=https://www.wikipathways.org/index.php/Pathway:$val target=_blank>$val</a>";
                //     }elseif(preg_match("/^BIOCARTA/i", $gseaResult[$i][2]) || preg_match("/^HALLMARK/i", $gseaResult[$i][2]) || preg_match("/^PID_/i", $gseaResult[$i][2]) ){
                //         $val = $gseaResult[$i][2];
                //         $pathID[] = "<a href=https://www.gsea-msigdb.org/gsea/msigdb/cards/$val target=_blank>$val</a>";
                //     }elseif(preg_match("/^P/i", $gseaResult[$i][2])){
                //         $val = $gseaResult[$i][2];
                //         $pathID[] = "<a href=http://www.pantherdb.org/pathway/pathwayDiagram.jsp?catAccession=$val target=_blank>$val</a>";
                //     }else{
                //         $pathID[] = $gseaResult[$i][2];
                //     }
                // }
                // for($i=1; $i<=sizeof($pathID); $i++){
                //     $gseaResult[$i][2] = $pathID[$i-1];
                // }
        
                // // Hyperlink to the Genes of the GSEA
                // $newResultgsea = array();
                // $newResultgsea2 = array();
        
                // for($i=1; $i<sizeof($gseaResult); $i++){
                //     $newResultgsea[] = $gseaResult[$i][7];
                // }
        
                // for($i=0; $i<sizeof($newResultgsea); $i++){
                //     $vari = explode(", ", $newResultgsea[$i]);
                //     $vari4 = array();
                //     foreach($vari as $value){
                //         $vari2 =  $value;
                //         $qry = "SELECT GeneID FROM entrez_id WHERE Symbol = '$vari2'";
                //         $res = mysqli_query($conn, $qry); 
                //         $cnt = mysqli_num_rows($res);
                //         while($row = mysqli_fetch_array($res))
                //         {
                //             $vari3 = $row["GeneID"];
                //             $vari4[] = "<a href=https://www.ncbi.nlm.nih.gov/gene/$vari3 target=_blank>$vari2</a>";
                //         }
                //     }
                //     $newResultgsea2[] = implode(", ", $vari4);
                // }
                // for($i=1; $i<=sizeof($newResultgsea); $i++){
                //     $gseaResult[$i][7] = $newResultgsea2[$i-1];
                // }
        
                // echo "<pre>";
                // print_r($_SESSION["gseaMainResultSession"]);
                // echo "<pre>";

                // echo sizeof($_SESSION["gseaMainResultSession"]);

                // Session
                $analTitle = $_POST['rnkName'.$m];
                $_SESSION["analTitle"][] = $analTitle;
                $_SESSION["gseaMainResultSession"][] = $gseaResult;
            }

            // hidden input using session
            for($i=0; $i<sizeof($_SESSION["analTitle"]); $i++){
                $titleIndex = "title".$i;
                echo sprintf("<input id='$titleIndex' type='hidden' value='%s'/>", json_encode($_SESSION["analTitle"][$i]));
            }

            for($i=0; $i<sizeof($_SESSION["gseaMainResultSession"]); $i++){
                $resultIndex = "gseaResArr".$i;
                echo sprintf("<input id='$resultIndex' type='hidden' value='%s'/>", json_encode($_SESSION["gseaMainResultSession"][$i]));
            }
        }else{
            echo "Nothing has been found!!!!!!!!!!";
        }
    }else{
        echo  "Something went wrong!!!!!!!";
    }

    //////////////////////////////////////////////// Meta-Aanlysis Tab //////////////////////////////////////////////
    
    // Blank array
    $baP_Gene = array();
    $heatArray_Gen = array(); 
    $baP_Path = array();
    $heatArray_Path = array();

    if(sizeof($_SESSION["randNum"]) > 1){
        // $metaAnalTitle = $_SESSION["analTitle"];
        // $AllrandNum = $_SESSION["randNum"];
        // $randNumStrg = implode(",",$AllrandNum);

        function metaGene(){
            global $baP_Gene;
            global $heatArray_Gen;

            $metaAnalTitle = $_SESSION["analTitle"];
            $AllrandNum = $_SESSION["randNum"];
            $randNumStrg = implode(",",$AllrandNum);

            //////////////////////////////////////////////  Genes  //////////////////////////////////////////
            $metadegResult = $_SESSION["degArray"];
    
            ////////// BarPlot for Genes ///////////////////
            $countUp_Gene = array();
            $countdown_Gene = array();
            for($i=0; $i<sizeof($metadegResult); $i++){
                $result2 = $metadegResult[$i];
                array_shift($result2);
                $geneUp = array();
                $geneDown = array();
                for($j=0; $j<sizeof($result2); $j++) {
                    $res = $result2[$j];
                    if(preg_match("/Upregulated/i", end($res))) {
                        $geneUp[] = $res[0];
                    }else{
                        $geneDown[] = $res[0];
                    }
                }
                $countUp_Gene[] = count($geneUp);
                $countdown_Gene[] = count($geneDown);
            }
            if ( !empty($countUp_Gene) && !empty($countdown_Gene)) {
                $bar_Up_Colr = array('xlbl'=>$metaAnalTitle, 'color'=>'red', 'data'=> $countUp_Gene, 'name'=>'Upregulated');
                array_push($baP_Gene, $bar_Up_Colr);
         
                $bar_Down_Colr = array('color'=>'blue', 'data'=> $countdown_Gene, 'name'=>'Downregulated');
                array_push($baP_Gene, $bar_Down_Colr);
            }

            /////////////// HeatMap for Genes ///////////////// 
            $t_Pval = $_SESSION['tPvalue'];
            $t_logU = $_SESSION['tLogfcu'];
            $t_logD = $_SESSION['tLogfcd'];
            $heatVari = exec('Rscript ./R_files/metaDEGs_CEL.R '."$randNumStrg $t_Pval $t_logU $t_logD");

            $heatMapGen = array();
            $file = "fileUpload/".end($AllrandNum)."/heat_meta_gene.txt";
            if(file_exists($file)){
                $fh = fopen($file, 'r');
                while (($line = fgetcsv($fh, 10000, "\t")) !== false) {
                    $heatMapGen[] = $line;
                }
                
                $heatforX_Gen = array();        // number of study
                $heatforY_Gen = array();        // list of genes
                $heatforZ_Gen = array();        // expression value of genes
                $heatforReg_Gen = array();      // Regulation status of study
             
                $numStudy = $heatMapGen[0];
                array_shift($numStudy);
                for ($i = 0; $i < sizeof($numStudy); $i++){
                    $heatforX_Gen[] = $metaAnalTitle[$i];
                }
             
                for ($i = 1; $i < sizeof($heatMapGen); $i++) {
                    $heatforY_Gen[] = $heatMapGen[$i][0];
                }
             
                for ($i = 1; $i <  sizeof($heatMapGen); $i++) {
                    for($j = 1; $j<sizeof($heatMapGen[$i]); $j++){
                        $heatforZ_Gen[$i - 1][] = (float)$heatMapGen[$i][$j];
                    }
                }
                
                for($i=0; $i < sizeof($heatforZ_Gen); $i++){
                     foreach($heatforZ_Gen[$i] as $key){
                         if($key > 0) {
                             $heatforReg_Gen[$i][]="Upregulated";
                         }elseif($key == 0) {
                             $heatforReg_Gen[$i][]="No regulation";
                         }else {
                             $heatforReg_Gen[$i][]="Downregulated";
                         }
                     }
                 }
             
                 $heatArray_Gen[] = array("hoverongaps"=>"false", "type"=>"heatmap", "x" => $heatforX_Gen, "y" => $heatforY_Gen, "z"=>$heatforZ_Gen,'text'=> $heatforReg_Gen, 'hovertemplate' => "Gene: %{y}<br>"."Study: %{x}<br>"."Log<sub>2</sub> fold change: %{z}<br>"."Regulation status: %{text}<br><extra></extra>");
            }
        }
    
        function metaPathway(){
            global $baP_Path;
            global $heatArray_Path;

            $metaAnalTitle = $_SESSION["analTitle"];
            $AllrandNum = $_SESSION["randNum"];
            $randNumStrg = implode(",",$AllrandNum);

            /////////////////////////////////////////    Pathways    ////////////////////////////////////////
            $metaGseaResult=$_SESSION["gseaResultSession"];

            //////////// BarPlot for Pathways /////////////////
            $countUp_Path = array();
            $countdown_Path = array();

            for($i=0; $i<sizeof($metaGseaResult); $i++) {
                $result2=$metaGseaResult[$i];
                array_shift($result2);
                $geneUp=array();
                $geneDown=array();

                for($j=0; $j<sizeof($result2); $j++) {
                    if(preg_match("/Upregulated/i", $result2[$j][8])) {
                        $geneUp[]=$result2[$j][0];
                    }

                    if(preg_match("/Downregulated/i", $result2[$j][8])) {
                        $geneDown[]=$result2[$j][0];
                    }
                }

                $countUp_Path[]=count($geneUp);
                $countdown_Path[]=count($geneDown);
            }
            if ( !empty($countUp_Path) && !empty($countdown_Path)) {
                $bar_Up_Col_P = array('xlbl'=>$metaAnalTitle, 'color'=>'red', 'data'=> $countUp_Path, 'name'=>'Upregulated');
                array_push($baP_Path, $bar_Up_Col_P);
         
                $bar_Down_Colr_P = array('color'=>'blue', 'data'=> $countdown_Path, 'name'=>'Downregulated');
                array_push($baP_Path, $bar_Down_Colr_P);
            }

            // echo sprintf("<input id='barPlt_Path' type='hidden' value='%s'/>", json_encode($baP_Path));

            /////////////// HeatMap for Pathways ///////////////
            $metaBubblePltExec = exec('Rscript ./R_files/metaGSEA_CEL.R '."$randNumStrg");
            $heatMapPath = array();
            $file = "fileUpload/".end($AllrandNum)."/heat_meta_pathway.txt";
            if(file_exists($file)){
                $fh=fopen($file, 'r');
                while (($line=fgetcsv($fh, 10000, "\t")) !==false) {
                    $heatMapPath[]=$line;
                }

                $heatforX_Path = array();           // number of study       \\
                $heatforY_Path = array();          // list of genes           \\
                $heatforZ_Path = array();         // expression value of genes \\
                $heatforReg_Path = array();      // Regulation status of study  \\
            
                $numStudy=$heatMapPath[0];
                array_shift($numStudy);
            
                for ($i=0; $i < sizeof($numStudy); $i++) {
                    $heatforX_Path[]=$metaAnalTitle[$i];
                }
            
                for ($i=1; $i < sizeof($heatMapPath); $i++) {
                    $heatforY_Path[] = str_ireplace("'",  "&apos;", $heatMapPath[$i][0]);
                }
            
                for ($i=1; $i < sizeof($heatMapPath); $i++) {
                    for($j=1; $j<sizeof($heatMapPath[$i]); $j++) {
                        $heatforZ_Path[$i - 1][]=(float)$heatMapPath[$i][$j];
                    }
                }
            
                for($i=0; $i < sizeof($heatforZ_Path); $i++) {
                    foreach($heatforZ_Path[$i] as $key) {
                        if($key > 0) {
                            $heatforReg_Path[$i][]="Upregulated";
                        }
            
                        elseif($key==0) {
                            $heatforReg_Path[$i][]="No regulation";
                        }
            
                        else {
                            $heatforReg_Path[$i][]="Downregulated";
                        }
                    }
                }
            
                $heatArray_Path[] = array("hoverongaps"=>"false", "type"=>"heatmap", "x"=> $heatforX_Path, "y"=> $heatforY_Path, "z"=>$heatforZ_Path, 'text'=> $heatforReg_Path, 'hovertemplate'=> "Pathway: %{y}<br>"."Study: %{x}<br>"."Log<sub>2</sub> fold change: %{z}<br>"."Regulation status: %{text}<br><extra></extra>");
            }
        }

        if (!empty($_POST['degList_AnalCount'])) {
            metaGene();
        }elseif(!empty($_POST['rnk_AnalCount'])){
            metaPathway();
        }else{
            metaGene();
            metaPathway();
        }
    }

    // will pass for total number of randNumber to javascript
    echo sprintf("<input id='freeID' type='hidden' value='%s'/>", json_encode($_SESSION["randNum"]));

    include("header.php");
?>


<p align="justify">&nbsp;</p>
<table width="98%" border="0" cellspacing="0" cellpadding="6" align="center" id="mdTable">
    <tr>
        <td>
            <!-- Model for Gene -->
            <button type="button" id="btnModal" class="btn btn-primary" data-toggle="modal"
                style="display: none;">&nbsp;</button>
            <?php
                foreach($hyperArr as $sigArr){
                    foreach($sigArr as $sigSArr){
                        ?>
                            <div class="modal fade" id="<?php $strpGen = strip_tags($sigSArr[0]); echo $strpGen; ?>"
                                tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel"><?php echo $strpGen; ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <label class="ref">NCBI</label>
                                                <ol class="olRef">
                                                    <li style="list-style-type: disc;">
                                                        <?php
                                                            echo $sigSArr[0];
                                                        ?>
                                                    </li>
                                                </ol>
                                            </div>
                                            <div>
                                                <label class="ref">Kegg</label>
                                                <ol class="olRef">
                                                    <?php
                                                        array_shift($sigSArr);
                                                        foreach($sigSArr as $sigTArr){
                                                    ?>
                                                    <li style="list-style-type: disc;">
                                                        <?php
                                                            echo $sigTArr."<br>";
                                                        ?>
                                                    </li>
                                                    <?php
                                                        }
                                                    ?>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                }
            ?>
            <div>
                <!-- Navigation Tab -->
                <ul class="nav nav-tabs">
                    <li id="degToggle" class="active"><a data-toggle="tab" href="#degTab">DEGs</a></li>
                    <li id="gseaToggle"><a data-toggle="tab" href="#gseaTab">GSEA</a></li>
                    <li id="plotToggle"><a data-toggle="tab" href="#plotTab">Plots</a></li>
                    <li id="metaToggle"><a data-toggle="tab" href="#meta-analysis">Meta-analysis</a></li>
                    <li id="vennToggle"><a data-toggle="tab" href="#venn_analysis_tab">Venn-analysis</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <div id="degTab" class="tab-pane fade in active">
                        <p></p>
                    </div>
                    <!-- <div id="degTab" class="tab-pane fade"><p></p></div> -->
                    <div id="gseaTab" class="tab-pane fade">
                        <p></p>
                    </div>
                    <div id="plotTab" class="tab-pane fade">
                        <p></p>
                    </div>
                    <?php
                        if(sizeof($_SESSION["resultArray"]) > 1){
                            ?>
                                <div id="meta-analysis" class="tab-pane fade">
                                    <p></p>
                                    <div class="metaBP" style="text-align: center;">
                                        <div id="metaG_Mdiv">
                                            <h5 class="metaBar">Gene</h5>
                                            <div id="barP_Gene_div">
                                                <div class="metaPstyle">Barplot depicting the genes across different
                                                    studies</div>
                                                <div id="barP_Gene" style="width: 900px; height:650px; margin: 0 auto;">
                                                </div>
                                            </div>
                                            <div id="heatMap_Gene_div">
                                                <div class="metaPstyle">Heatmap</div>
                                                <?php
                                                    if(!empty($heatArray_Gen)){
                                                        ?>
                                                            <div id="heatMap_Gene" style="text-align: -webkit-center;"></div>
                                                        <?php
                                                    }else{
                                                        ?>
                                                            <div>
                                                                <h5 align="center">No Data has been found for Heat Map</h5>
                                                            </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                            <div id="bubblePlt_Gene_div">
                                                <div class="metaPstyle">Bubble plot</div>
                                                <div id="bubble_Gene" style="margin: 20px 0px;">
                                                    <?php include('bubbMeta_DEG.php'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="metaP_Mdiv">
                                            <h5 class="metaBar">Pathways</h5>
                                            <div id="barP_Path_div">
                                                <div class="metaPstyle">Barplot depicting the enriched pathways across
                                                    different studies</div>
                                                <div id="barP_Path" style="width: 900px; height:650px; margin: 0 auto;">
                                                </div>
                                            </div>
                                            <div id="heatMap_Path_div">
                                                <div class="metaPstyle">Heatmap</div>
                                                <?php
                                                    if(!empty($heatArray_Path)){
                                                        ?>
                                                            <div id="heatMap_Path" style="text-align: -webkit-center;"></div>
                                                        <?php
                                                    }else{
                                                        ?>
                                                            <div>
                                                                <h5 align="center">No Data has been found for Heat Map</h5>
                                                            </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                            <div id="bubblePlt_Path_div">
                                                <div class="metaPstyle">Bubble plot</div>
                                                <div id="bubble_Path" style="margin: 20px 0px;">
                                                    <?php include('bubbMeta_GSEA.php'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                    <div id="venn_analysis_tab" class="tab-pane fade">
                        <p>&nbsp;</p>
                        <div style="padding: 0px 10px;">
                            <!-- Venn Analysis for Genes -->
                            <div class="mt-4 mb-5 vennGenes" id="vennGenes">
                                <div class="GvennLablcls"><label class="lablClsVenn">Genes: </label>
                                </div>
                                <div id="Gvenn_analysis_tab"></div>
                            </div>

                            <!-- Venn Analysis for Pathways -->
                            <div class="mb-4 vennPathway" id="vennPathway">
                                <div class="PvennLablcls"><label class="lablClsVenn">Pathway: </label>
                                </div>
                                <div id="Pvenn_analysis_tab"></div>
                            </div>
                        </div>
                        <!-- Display Venn Div -->
                        <div id="GvennAnalLoadID"></div>
                        <div id="PvennAnalLoadID"></div>
                        <p></p>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
<div id="hiddenTble"></div>

<script src="js/d3.v3.min.js"></script>
<script src="miArrayResultScript.js"></script>

<script>
    // Store in Javascript variable from PHP variable
    var randID = <?= json_encode($_SESSION["randNum"]); ?>;            //Random number 
    var analTitle = <?= json_encode($_SESSION["analTitle"]); ?>;       //Title of the analysis
    
    if (document.getElementById("degList_Anal") != null) {
        degTab();
        $('.selctTh, .chckBxGenBas').hide();
        $('.genBasAnalBtn').hide();
        $('#gseaToggle').hide();
    } else if (document.getElementById("rnkFile_Anal") != null) {
        gseaTab();
        $('#degToggle').hide();
    } else {
        degTab();
        gseaTab();
    }
    
    function degTab() {
        $('#degToggle').show();
        var mnTble = document.getElementById("degTab");
        mnTble.style.fontSize = "12px";

        // heading of Differentially Expressed Genes
        var maintitDEGdiv = document.createElement('div');
        maintitDEGdiv.className = "mb-4";
        mnTble.appendChild(maintitDEGdiv);

        var mainTitleDEGdiv = document.createElement('h5');
        mainTitleDEGdiv.innerHTML = "Differentially Expressed Genes (DEGs)";
        mainTitleDEGdiv.style.alignSelf = "center";
        mainTitleDEGdiv.style.fontWeight = "bold";
        mainTitleDEGdiv.style.textAlign = "center";
        maintitDEGdiv.appendChild(mainTitleDEGdiv);

        // Store in Javascript variable from PHP variable
        var conGrpName = <?= json_encode($_SESSION["conGrpName"]); ?>;     //Control Group name
        var numControl = <?= json_encode($_SESSION["numControl"]); ?>;     //Control Group name count 
        var caseGrpName = <?= json_encode($_SESSION["caseGrpName"]); ?>;   //Case Group name
        var numCase = <?= json_encode($_SESSION["numCase"]); ?>;           //Case Group name

        for (var i = 0; i < randID.length; i++) {
            var titTR = document.createElement('div');
            titTR.className = "d-flex mb-2";
            titTR.style.background = "rgb(241, 232, 209)";
            titTR.style.border = "1px solid rgb(185, 156, 107)";
            titTR.style.borderRadius = "5px";
            mnTble.appendChild(titTR);

            // Title
            var mainTitDiv = document.createElement('div');
            var titleAnal = analTitle[i]
            mainTitDiv.innerHTML = titleAnal;
            mainTitDiv.className = "mr-auto";
            mainTitDiv.style.alignSelf = "center";
            mainTitDiv.style.fontWeight = "bold";
            mainTitDiv.style.marginLeft = "6px";
            mainTitDiv.id = "degAnalTitleID" + i;
            titTR.appendChild(mainTitDiv);

            // Show and Hide Column using Dropdown
            var shwCols = document.createElement('div');
            shwCols.className = "shwColTD";
            shwCols.style.paddingRight = "48px"

            shwColDiv = document.createElement("div");
            shwColDiv.id = "hideANDshow" + i;
            shwColDiv.className = "dropdown-check-list";
            titTR.appendChild(shwCols);
            shwCols.appendChild(shwColDiv);

            shwColSpan = document.createElement("button");
            shwColSpan.className = "anchor btn btn-default";
            shwColSpan.id = "anchorID" + i;
            $(shwColSpan).attr({
                "type": "button",
                "data-toggle": "dropdown"
            }).css({
                "padding": "4px",
                "font-size": "20px",
                "background": "#f1E8d1",
                "border": "1px solid #b99c6b"
            });

            shwColSpan.addEventListener('click', function () {
                str = this.id.replace("anchorID", "");

                if ($('#hideANDshow' + str).hasClass("visible")) {
                    $('#hideANDshow' + str).removeClass("visible");
                } else {
                    $('#hideANDshow' + str).addClass("visible");
                }
                $(function () {
                    var $chk = $(`#hideANDshow${str} input:checkbox`);
                    var $tbl = $(`#degTable_${str}`);
                    var $tblhead = $tbl.find("th");
                    $chk.click(function(){
                        n = $(this).attr('name');       // Selected value from checkbox
                        t = $tblhead.filter(function(){     //filter will you same match dropdown and column name
                                return n == $(this).html();
                            });
                        iC = $(t).index();               // index of column
                        $tbl.find('tr :nth-child(' + (iC + 1) + ')').toggle(100);
                    });
                });
            }, false);

            shwColSpanI = document.createElement("i");
            shwColSpanI.className = "glyphicon glyphicon glyphicon-check shwColSpanI";
            shwColSpanSpan = document.createElement("span");
            shwColSpanSpan.className = "caret";
            shwColDiv.appendChild(shwColSpan);
            shwColSpan.appendChild(shwColSpanI);
            shwColSpan.appendChild(shwColSpanSpan);

            $("#hideANDshow" + i).append(`
                        <ul class="shwHideUl items">
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Gene symbol" checked Disabled>Gene Symbol</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Log<sub>2</sub> fold change" checked>Log<sub>2</sub> Fold Change</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Average expression" checked>Average Expression</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Moderated t-statistics (t)" checked>Moderated T-Statistics (T)</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="P-value" checked>P-Value</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Adjusted p-Value / q-value" checked>Adjusted P-Value / Q-Value</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="B-statistics" checked>B-Statistics</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Regulation Status" checked>Regulation Status</li>
                        </ul>`);

            // Summary table row
            var sumRow = document.createElement('tr');
            mnTble.appendChild(sumRow);

            // Summary table Data
            var sumData = document.createElement('td');
            sumData.style.fontSize = "12px";
            sumRow.appendChild(sumData);

            var titDownDiv = document.createElement("tr");
            sumData.appendChild(titDownDiv)

            if (conGrpName[i] != null) {
                // Number of Control 
                var controlDiv = document.createElement('tr');
                controlDiv.className = "d-flex flex-row bd-highlight mb-2";
                sumData.appendChild(controlDiv);

                // controlLeft Div
                var controlLeft = document.createElement('td');
                controlLeft.style.width = "30%";
                var conGrpN = conGrpName[i];
                controlLeft.innerHTML = "Number of samples (" + conGrpN + "):";
                controlLeft.style.fontWeight = "600";
                controlLeft.style.padding = "6px";
                controlDiv.appendChild(controlLeft);

                // controlRight Div
                var controlRight = document.createElement('td');
                controlRight.style.width = "60%";
                controlRight.style.padding = "6px";
                controlRight.innerHTML = numControl[i]
                controlDiv.appendChild(controlRight);
            }

            if (caseGrpName[i] != null) {
                // Number of Cases 
                var caseDiv = document.createElement('tr');
                caseDiv.className = "d-flex flex-row bd-highlight mb-2";
                sumData.appendChild(caseDiv);

                // caseLeft Div
                var caseLeft = document.createElement('td');
                caseLeft.style.width = "30%";
                var caseGrpN = caseGrpName[i];
                caseLeft.innerHTML = "Number of samples (" + caseGrpN + "):";
                caseLeft.style.fontWeight = "600";
                caseLeft.style.padding = "6px";
                caseDiv.appendChild(caseLeft);

                // caseRight Div
                var caseRight = document.createElement('td');
                caseRight.style.width = "60%";
                caseRight.style.padding = "6px";
                caseRight.innerHTML = numCase[i]
                caseDiv.appendChild(caseRight);
            }

            // P-Value
            var pvalueDiv = document.createElement('tr');
            pvalueDiv.className = "d-flex flex-row bd-highlight mb-2";
            sumData.appendChild(pvalueDiv);

            // pvalueLeft Div
            var pvalueLeft = document.createElement('td');
            pvalueLeft.style.width = "30%";
            pvalueLeft.innerHTML = "P-value cutoff:";
            pvalueLeft.style.fontWeight = "600";
            pvalueLeft.style.padding = "6px";
            pvalueDiv.appendChild(pvalueLeft);

            // pvaluetRight Div
            var pvaluetRight = document.createElement('td');
            pvaluetRight.style.width = "60%";
            pvaluetRight.style.padding = "6px";
            // var tPvalueID = "tPvalue" + i;
            var tPvalueIDCount = <?= json_encode($tPvalue_CEL) ?>;
            pvaluetRight.innerHTML = tPvalueIDCount
            pvalueDiv.appendChild(pvaluetRight);

            // logfc UpRegulated
            var logfcUDiv = document.createElement('tr');
            logfcUDiv.className = "d-flex flex-row bd-highlight mb-2";
            sumData.appendChild(logfcUDiv);

            // logfc UpRegulated Left Div
            var logfcULeft = document.createElement('td');
            logfcULeft.style.width = "30%";
            logfcULeft.innerHTML = "Log<sub>2</sub> fold change cutoff for upregulation: ";
            logfcULeft.style.fontWeight = "600";
            logfcULeft.style.padding = "6px";
            logfcUDiv.appendChild(logfcULeft);

            // logfc UpRegulated Right DivN
            var logfcURight = document.createElement('td');
            logfcURight.style.width = "60%";
            logfcURight.style.padding = "6px";
            // var tLogfcuID = "tLogfcu" + i;
            var tLogfcuCount = <?= json_encode($tLogfcu_CEL) ?>;
            logfcURight.innerHTML = tLogfcuCount;
            logfcUDiv.appendChild(logfcURight);

            // logfc DownRegulated
            var logfcDDiv = document.createElement('tr');
            logfcDDiv.className = "d-flex flex-row bd-highlight mb-2";
            sumData.appendChild(logfcDDiv);

            // logfc DownRegulated Left Div
            var logfcDLeft = document.createElement('td');
            logfcDLeft.style.width = "30%";
            logfcDLeft.innerHTML = "Log<sub>2</sub> fold change cutoff for downregulation: ";
            logfcDLeft.style.fontWeight = "600";
            logfcDLeft.style.padding = "6px";
            logfcDDiv.appendChild(logfcDLeft);

            // logfc DownRegulated Right Div
            var logfcDRight = document.createElement('td');
            logfcDRight.style.width = "60%";
            logfcDRight.style.padding = "6px";
            // var tLogfcdID = "tLogfcd" + i;
            var tLogfcdCount = <?= json_encode($tLogfcd_CEL) ?>;
            logfcDRight.innerHTML = tLogfcdCount
            logfcDDiv.appendChild(logfcDRight);

            //Go to Study directly 
            var degseltTD = document.createElement('div');
            $(degseltTD).css({
                "alignSelf": "center",
                "padding": "6px 10px 6px 10px",
                "background": "#f1E8d1",
                "border": "1px solid #b99c6b",
                "border-radius": "5px"
            });
            var degdrpdwnSelDiv = document.createElement('div');
            degdrpdwnSelDiv.className = "dropdown";
            var degfstSel = document.createElement('a');
            degfstSel.id = "my-dropdown";
            degfstSel.className = "dropdown-toggle";
            $(degfstSel).attr("data-toggle", "dropdown").css({
                "color": "black",
                "text-decoration-line": "none",
                "cursor": "pointer"
            });
            degfstSel.innerText = "Go to: ";
            var degSecSelUL = document.createElement('ul');
            degSecSelUL.className = "dropdown-menu degGoToStuLst";

            titTR.appendChild(degseltTD);
            degseltTD.appendChild(degdrpdwnSelDiv);
            degdrpdwnSelDiv.appendChild(degfstSel);
            degdrpdwnSelDiv.appendChild(degSecSelUL);

            // Study Table Tr
            var tRow = document.createElement('tr');
            mnTble.appendChild(tRow);

            var tData = document.createElement('td');
            tRow.appendChild(tData);

            // whole Div containing all div 
            var tmpDiv = document.createElement('div');
            tmpDiv.id = "final_result";
            tmpDiv.className = "final_result";
            tData.appendChild(tmpDiv);

            // Div containing table 
            var Div2 = document.createElement('div');
            Div2.id = "degResult" + i;
            tmpDiv.appendChild(Div2);
            
            var blankDiv = document.createElement('div');
            blankDiv.className = "d-flex justify-content-around";
            blankDiv.style.padding = "20px";

            //Gene-Based Analysis
            var genBaseA = document.createElement('a');
            var geneBase = document.createElement('input');
            geneBase.className = "btn genBasAnalBtn";
            geneBase.id = "holdValue" + i;
            geneBase.type = "submit";
            geneBase.style.fontSize = "12px";
            geneBase.style.border = "groove";
            geneBase.style.padding = "5px";
            geneBase.value = "Gene-based analysis";
            geneBase.name = "gebasAnal";
            geneBase.target = "_blank";

            tmpDiv.appendChild(blankDiv);
            blankDiv.appendChild(genBaseA);
            genBaseA.appendChild(geneBase);
        }

        for (var i = 0; i < randID.length; i++) {
            var degDrpDwnLst = document.createElement('li');
            var degDrpDwnLstA = document.createElement('a');
            degDrpDwnLstA.href = "#degAnalTitleID" + i
            degDrpDwnLstA.innerHTML = $("#degAnalTitleID" + i).text();
            degDrpDwnLst.appendChild(degDrpDwnLstA);
            $('.degGoToStuLst').append(degDrpDwnLst);
        }

        var resultArray = <?= json_encode($_SESSION["resultArray"]); ?>;
        function degRsltTble() {
            for (var r = 0; r < randID.length; r++) {
                var result = resultArray[r];
                var dsplyID = document.getElementById("degResult" + r);
                var html = `<table id='degTable_${r}' class = 'Ftable table table-bordered'>`;
                html += "<thead>";
                html += `<th>Select</th>`;
                result[0].forEach(function (key) {
                    let newVal = key.replace(/_/g, " ");
                    html += `<th>${newVal}</th>`;
                });
                html += "</tr>";
                html += "</thead>";

                result = result.splice(1);
                html += "<tbody>";
                result.forEach(function (row) {
                    html += "<tr>";
                    html += `<td></td>`;
                    Object.keys(row).forEach(function (key) {
                        if(key == 0){
                            html += `<td>${row[key]}</td>`;
                        }else if(key == 4 || key == 5){
                            let newVal = parseFloat(row[key]).toExponential(2);
                            html += `<td>${newVal}</td>`;
                        }else if(key == 7) {
                            html += `<td>${row[key]}</td>`;
                        }else{
                            let newVal = parseFloat(row[key]);
                            html += `<td>${newVal}</td>`;
                        }
                    }); 
                    html += "</tr>";
                });
                html += "</tbody>";
                html += "</table>";
                dsplyID.innerHTML = html;
            }

            for (var d = 0; d < randID.length; d++) {
                var dt = $(`#degTable_${d}`).DataTable({
                    "iDisplayLength": 10,
                    dom: 'QBfrltip',
                    buttons: [{
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            {
                                extend: 'excel',
                                title: analTitle[d]
                            }, {
                                extend: 'csv',
                                title: analTitle[d],
                                exportOptions: {
                                    columns: ':gt(0)'
                                }
                            }
                        ]
                    }],
                    columnDefs: [{
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0,
                    }],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    order: [
                        [1, 'asc']
                    ],
                    searchBuilder: {
                        conditions: {
                            "num": {
                                // Overwrite the equals condition for the num type
                                '=': null,
                                '!=': null,
                                '!null': null,
                                'null': null,
                                'between': null,
                                '!between': null,
                            },
                            "string": {
                                '=': null,
                                '!=': null,
                                '!starts': null,
                                '!contains': null,
                                'ends': null,
                                '!ends': null,
                                '!null': null,
                                'null': null,
                                'between': null,
                                '!between': null,
                            }
                        }
                    }
                });
            }
        }
        degRsltTble();

        // Gene-Based Analysis
        jQuery("div[id=final_result]").on("click", "input[name='gebasAnal']", function () {
            var inputID = this.id.replace('holdValue', '');
            var geneList = $(`#degTable_${inputID} td.select-checkbox`).parent('.selected').find('td:eq(1)').map(function (key, val) {
                return val.innerHTML;
            }).get();
            if (geneList.length > 0 && geneList.length <= 10) {
                hrefGeneBased = "geBaAnal.php?geneVal=" + geneList + "&Num=" + inputID;
                window.open(hrefGeneBased, '_blank');
                return true;
            } else {
                if (geneList.length > 10) {
                    alert("Please Select upto 10 genes");
                    return false;
                } else {
                    alert("Please Select Genes");
                    return false;
                }
            }
        })
    }

    function gseaTab() {
        var tab2Div = document.getElementById("gseaTab");

        // heading of Differentially Expressed Genes
        var maintitGSEAdiv = document.createElement('div');
        maintitGSEAdiv.className = "mb-4";
        tab2Div.appendChild(maintitGSEAdiv);

        var mainTitleGSEAdiv = document.createElement('h5');
        mainTitleGSEAdiv.innerHTML = "Gene Set Enrichment Analysis (GSEA)";
        mainTitleGSEAdiv.style.alignSelf = "center";
        mainTitleGSEAdiv.style.fontWeight = "bold";
        mainTitleGSEAdiv.style.textAlign = "center";
        maintitGSEAdiv.appendChild(mainTitleGSEAdiv);

        for (var i = 0; i < randID.length; i++) {
            var analTitleMDiv = document.createElement('div');
            analTitleMDiv.className = "d-flex mb-2";
            analTitleMDiv.style.background = "rgb(241, 232, 209)";
            analTitleMDiv.style.border = "1px solid rgb(185, 156, 107)";
            analTitleMDiv.style.borderRadius = "5px";
            tab2Div.appendChild(analTitleMDiv);

            var mainTitDiv = document.createElement('div');
            var titleAnal = analTitle[i];
            mainTitDiv.innerHTML = titleAnal;
            mainTitDiv.className = "mr-auto";
            mainTitDiv.style.fontWeight = "bold";
            mainTitDiv.style.alignSelf = "center";
            mainTitDiv.style.marginLeft = "6px";
            mainTitDiv.id = "analTitleID" + i;
            analTitleMDiv.appendChild(mainTitDiv);

            var shwCols = document.createElement('div');
            shwCols.className = "shwColTD";
            shwCols.style.paddingRight = "48px"

            var shwColDiv = document.createElement("div");
            shwColDiv.id = "gseahideANDshow" + i;
            shwColDiv.className = "dropdown-check-list";
            analTitleMDiv.appendChild(shwCols);
            shwCols.appendChild(shwColDiv);

            var shwColSpan = document.createElement("button");
            shwColSpan.className = "anchor btn btn-default";
            shwColSpan.id = "gseanchorID" + i;
            $(shwColSpan).attr({
                "type": "button",
                "data-toggle": "dropdown"
            }).css({
                "padding": "4px",
                "font-size": "20px",
                "background": "#f1E8d1",
                "border": "1px solid #b99c6b"
            });

            shwColSpan.addEventListener('click', function () {
                str = this.id.replace("gseanchorID", "");

                if ($('#gseahideANDshow' + str).hasClass("visible")) {
                    $('#gseahideANDshow' + str).removeClass("visible");
                } else {
                    $('#gseahideANDshow' + str).addClass("visible");
                }

                $(function () {
                    var $chk = $(`#gseahideANDshow${str} input:checkbox`);
                    var $tbl = $(`#gseaTableDiv${str}`);
                    var $tblhead = $tbl.find("th");
                    $chk.click(function(){
                        n = $(this).attr('name');       // Selected value from checkbox
                        t = $tblhead.filter(function(){     //filter will you same match dropdown and column name
                                return n == $(this).html();
                            });
                        iC = $(t).index();               // index of column
                        $tbl.find('tr :nth-child(' + (iC + 1) + ')').toggle(100);
                    });
                });
            }, false);
            
            shwColSpanI = document.createElement("i");
            shwColSpanI.className = "glyphicon glyphicon glyphicon-check shwColSpanI";
            shwColSpanSpan = document.createElement("span");
            shwColSpanSpan.className = "caret";
            shwColDiv.appendChild(shwColSpan);
            shwColSpan.appendChild(shwColSpanI);
            shwColSpan.appendChild(shwColSpanSpan);

            $("#gseahideANDshow" + i).append(`
                        <ul class="shwHideUl items">
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Gene symbol" checked Disabled>Pathways</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Database" checked>Database</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Database ID" checked>Database ID</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="P-value" checked>P-value</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Adjusted P-value / Q-value" checked>Adjusted P-value / Q-value</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Normalized enrichment score (NES)" checked>Normalized enrichment score (NES)</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="No. of genes" checked>No. of genes</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Genes" checked>Genes</li>
                            <li><input type="checkbox" class="hide_show" style = "margin-right: 5px;" name="Regulation status" checked>Regulation status</li>
                        </ul>`);

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

            analTitleMDiv.appendChild(seltTD);
            seltTD.appendChild(drpdwnSelDiv);
            drpdwnSelDiv.appendChild(fstSel);
            drpdwnSelDiv.appendChild(SecSelUL);

            // P-Value TR 
            var pValTR = document.createElement('tr');
            pValTR.className = "d-flex flex-row bd-highlight mt-2";
            pValTR.style.marginBottom = "-10px";
            pValTR.style.fontSize = "12px";
            tab2Div.appendChild(pValTR);

            // left-side P-Value
            var pValLeftTD = document.createElement('td');
            pValLeftTD.innerHTML = "P-value cutoff:";
            pValLeftTD.style.width = "30%";
            pValLeftTD.style.fontWeight = "600";
            pValLeftTD.style.padding = "6px";
            pValTR.appendChild(pValLeftTD);

            // Right-side P-Value
            var pValRightTD = document.createElement('td');
            pValRightTD.style.width = "60%";
            pValRightTD.style.padding = "6px";
            pValRightTD.innerHTML = "0.05";
            pValTR.appendChild(pValRightTD);

            var tRow = document.createElement('tr');
            tab2Div.appendChild(tRow);

            var tData = document.createElement('td');
            tRow.appendChild(tData);
            var lineBreak = document.createElement('br');
            tData.appendChild(lineBreak);

            // whole Div containing all div 
            var tmpDiv = document.createElement('div');
            tmpDiv.id = "final_result";
            tmpDiv.className = "final_result";
            tData.appendChild(tmpDiv);

            // Div containing table 
            var Div2 = document.createElement('div');
            Div2.id = "gseaTableDiv" + i;
            tmpDiv.appendChild(Div2);
            var blankDiv = document.createElement('div');
            blankDiv.className = "gseaDownld d-flex justify-content-around";
            blankDiv.style.padding = "20px";
            tmpDiv.appendChild(blankDiv);

            var downLd = document.createElement('a');
            downLd.href = "fileUpload/" + randID[i] + "/gseaResult.csv";
            downLd.style.float = "right";
            var dwnldRedbutton = document.createElement('button');
            dwnldRedbutton.className = "btn";
            dwnldRedbutton.innerHTML = "Download Table";
            downLd.download = "Processed Data"
            downLd.target = "_blank";

            // Pathway-Gene Netwotk Button
            var pathG = document.createElement('a');
            pathG.href = "pathgeneNet.php?Num=" + i;
            pathG.target = "_blank";
            var pathGbutton = document.createElement('button');
            pathGbutton.innerHTML = "Pathway Gene Network";
            pathGbutton.style.fontSize = "12px";
            pathGbutton.style.border = "1px solid black";
            pathGbutton.style.padding = "5px";
            pathGbutton.style.color = "black";
            pathGbutton.className = "btn";
            blankDiv.appendChild(pathG);
            pathG.appendChild(pathGbutton);

            //Bubble Plot
            var bubbA = document.createElement('a');
            var bubbPlt = document.createElement('input');
            bubbPlt.className = "btn";
            bubbPlt.id = "holdValBubb" + i;
            bubbPlt.type = "submit";
            bubbPlt.style.fontSize = "12px";
            bubbPlt.style.border = "groove";
            bubbPlt.style.padding = "5px";
            bubbPlt.value = "Bubble Plot";
            bubbPlt.name = "bubbPltNm";
            bubbPlt.target = "_blank";

            blankDiv.appendChild(bubbA);
            bubbA.appendChild(bubbPlt);
        }
        for (var i = 0; i < randID.length; i++) {
            var DrpDwnLst = document.createElement('li');
            var DrpDwnLstA = document.createElement('a');
            DrpDwnLstA.href = "#analTitleID" + i
            DrpDwnLstA.innerText = $("#analTitleID" + i).text();
            DrpDwnLst.appendChild(DrpDwnLstA);
            $('.gseaGoToStuLst').append(DrpDwnLst);
        }

        $(document).ready(function () {
            $('#selDropID').on('change', function () {
                if ($(this).children('option:first-child').is(':selected')) {
                    alert("please select option");
                } else {
                    var $form = $(this).closest('form');
                    $form.find('input[type=submit]').click();
                    // $(this).children('option:first').prop('selected',true);
                }
            });
        });

        var gsearesultArray = <?= json_encode($_SESSION["gseaMainResultSession"]); ?>;
        function gseaRsltTble() {
            for (var r = 0; r < randID.length; r++) {
                var result = gsearesultArray[r];
                var dsplyID = document.getElementById("gseaTableDiv" + r);
                var html = `<table id='gseaTable_${r}' class = 'Ftable table table-bordered'>`;
                html += "<thead>";
                html += `<th>Select</th>`;
                result[0].forEach(function (key) {
                    let newVal = key.replace(/_/g, " ");
                    html += `<th>${newVal}</th>`;
                });
                html += "</tr>";
                html += "</thead>";

                result = result.splice(1);
                html += "<tbody>";
                result.forEach(function (row) {
                    html += "<tr>";
                    html += `<td></td>`;
                    Object.keys(row).forEach(function (key) {
                        if (key == 3 || key == 4) {
                            let newVal = parseFloat(row[key]).toExponential(2);
                            html += `<td>${newVal}</td>`;
                        } else if (key == 5) {
                            let newVal = parseFloat(row[key]).toFixed(4);
                            html += `<td>${newVal}</td>`;
                        } else {
                            html += `<td>${row[key]}</td>`;
                        }
                    });
                    html += "</tr>";
                });
                html += "</tbody>";
                html += "</table>";
                dsplyID.innerHTML = html;
            }

            for (var d = 0; d < randID.length; d++) {
                var dt = $(`#gseaTable_${d}`).DataTable({
                    "iDisplayLength": 10,
                    dom: 'QBfrltip',
                    buttons: [{
                        extend: 'collection',
                        text: 'Export',
                        buttons: [{
                            extend: 'excel',
                            title: analTitle[d]
                        }, {
                            extend: 'csv',
                            title: analTitle[d],
                            exportOptions: {
                                columns: ':gt(0)'
                            }
                        }]
                    }],
                    columnDefs: [{
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0,
                    }],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    order: [
                        [1, 'asc']
                    ],
                    searchBuilder: {
                        conditions: {
                            "num": {
                                // Overwrite the equals condition for the num type
                                '=': null,
                                '!=': null,
                                '!null': null,
                                'null': null,
                                'between': null,
                                '!between': null,
                            },
                            "string": {
                                '=': null,
                                '!=': null,
                                '!starts': null,
                                '!contains': null,
                                'ends': null,
                                '!ends': null,
                                '!null': null,
                                'null': null,
                                'between': null,
                                '!between': null,
                            }
                        }
                    }
                });
            }
        }
        gseaRsltTble();

        // Bubble plot analysis
        jQuery("div[id=final_result]").on("click", "input[name='bubbPltNm']", function () {
            var inputID = this.id.replace('holdValBubb', '');
            var geneList = $(`#gseaTable_${inputID} td.select-checkbox`).parent('.selected').find('td:eq(1)').map(function (key, val) {
                return val.innerHTML;
            }).get();
            // console.log(geneList);
            var geneListStr = geneList.join('||');
            if (geneList.length > 0 && geneList.length <= 10) {
                hrefGeneBased = "bubblePltInter.php?geneVal=" + geneListStr + "&Num=" + inputID;
                window.open(hrefGeneBased, '_blank');
                return true;
            } else {
                if (geneList.length > 10) {
                    alert("Please select upto 10 pathways");
                    return false;
                } else {
                    alert("Please select pathways");
                    return false;
                }
            }
        })
    }

    function plotTab() {
        var tab3Div = document.getElementById("plotTab");

        var PCADiv = document.createElement("div");
        PCADiv.style.fontSize = "12px";
        tab3Div.appendChild(PCADiv);

        var PCAHead = document.createElement("h5");
        PCAHead.innerHTML = "Principle component analysis(PCA) Plot, Volcano Plot and Lollipop Plot";
        PCAHead.style.alignSelf = "center";
        PCAHead.style.fontWeight = "bold";
        PCAHead.style.textAlign = "center";
        PCADiv.appendChild(PCAHead);

        //php variable into javascript variable
        var screePltCont = <?= json_encode($screePltCont); ?>;                           //Scree plot variable
        var screePltAxisCont = <?= json_encode($screePltAxisCont); ?>;                   //Scree plot variable
        var methType = <?= json_encode($methType); ?>;                                   //Scree plot variable

        var forwardStr = <?= json_encode($frwdStr); ?>;                                  //Score plot variable
        var scorpltArr = <?= json_encode($pltArr); ?>;                                   //Score plot variable
        var numScorPC = <?= json_encode($numPCA); ?>;                                    //Score plot variable

        var lolliArrVari = <?= json_encode($lolliArrVari); ?>;                           //Lollipop plot variable

        var l = 0;
        randID.forEach(function (key) {
            var allDivM = document.createElement("div");
            PCADiv.appendChild(allDivM);

            var headDiv = document.createElement("div");
            headDiv.className = "d-flex mb-4";
            headDiv.style.background = "rgb(241, 232, 209)";
            headDiv.style.border = "1px solid rgb(185, 156, 107)";
            headDiv.style.borderRadius = "5px";
            allDivM.appendChild(headDiv);

            var stType = document.createElement("div");
            stType.innerHTML = analTitle[l];
            stType.id = "degAnalTitlepltID" + l;
            stType.className = "mr-auto";
            stType.style.alignSelf = "center";
            stType.style.fontWeight = "bold";
            stType.style.marginLeft = "6px";
            headDiv.appendChild(stType);

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
            SecSelUL.className = "dropdown-menu goToStuLst";

            headDiv.appendChild(seltTD);
            seltTD.appendChild(drpdwnSelDiv);
            drpdwnSelDiv.appendChild(fstSel);
            drpdwnSelDiv.appendChild(SecSelUL);

            if (document.getElementById("degList_Anal") != null) {
                volcanoPlot();
            } else if (document.getElementById("rnkFile_Anal") != null) {
                LolliPop_Plot();
            } else {
                PCA_Plot();
                volcanoPlot();
                LolliPop_Plot();
            }

            function PCA_Plot() {
                // Scree Plot with description
                function screePlt() {
                    var ScreePltDesc = document.createElement("div");
                    ScreePltDesc.className = "col-12 mb-4";
                    ScreePltDesc.style.textAlign = "-webkit-center";
                    allDivM.appendChild(ScreePltDesc);

                    var ScreeDiv = document.createElement("div");
                    ScreeDiv.align = "center";
                    ScreeDiv.className = "screeContainer"
                    ScreeDiv.id = `screeContainer_${l}`;
                    ScreeDiv.style.border = "1px solid black";
                    ScreePltDesc.appendChild(ScreeDiv);

                    var screeToolTip = document.createElement("div");
                    screeToolTip.className = "tooltipScree";
                    screeToolTip.id = `tooltipScree${l}`;
                    screeToolTip.innerText = "Elbow method";
                    ScreePltDesc.appendChild(screeToolTip);

                    screeData = screePltCont[l];  
                    screeAxisLbl = screePltAxisCont[l];  
                    methdSEl = methType[l];  
                    
                    Highcharts.chart(ScreeDiv, {
                        title: {
                            text: 'Scree Plot',
                            style: {
                                color: 'black',
                                fontSize: '14px',
                                fontFamily: 'Verdana, Geneva, sans-serif',
                                fontWeight: 'bold'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            title: {
                                text: 'Principle components',
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
                            categories: screeAxisLbl,
                            plotLines: [{
                                dashStyle: 'shortdash',
                                color: '#FF0000', // Red
                                width: 2,
                                value: parseInt(methdSEl) - 1, // Position, you'll have to translate this to the values on your x axis (Elbow Method),
                                events: {
                                    mouseover() {
                                        let lineBBox = this.svgElem.getBBox();
                                        screeToolTip.style.display = 'block';
                                        screeToolTip.style.left = lineBBox.x + 'px';
                                    },
                                    mouseout() {
                                        screeToolTip.style.display = 'none'
                                    }
                                }
                            }]
                        },
                        yAxis: {
                            min: 0,
                            max: 110,
                            tickInterval: 10,
                            title: {
                                text: 'Explained variation(%)',
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
                        tooltip: {
                            formatter: function () {
                                return "Principle component: " + this.x +
                                    '<br>' + "Explained variation: " + this.y;
                            }
                        },

                        series: [{
                            type: 'column',
                            data: screeData[0],
                        }, {
                            type: 'spline',
                            name: '',
                            data: screeData[1],
                            marker: {
                                lineWidth: 2,
                                lineColor: Highcharts.getOptions().colors[3],
                                fillColor: 'white'
                            }
                        }]
                    });

                    var ScreeDesDiv = document.createElement("div");
                    ScreeDesDiv.className = "col-12 col-4";
                    ScreePltDesc.appendChild(ScreeDesDiv);

                    var ScreeDesP = document.createElement("p");
                    ScreeDesP.className = "col-10 m-auto";
                    ScreeDesP.innerHTML = "Scree plot is the plot which use to represents the variance explained (eigenvalues) for each principal component.In the above scree plot, the x - axis represents the principal components(PCs) and y - axis represents the percentage explained variance( % ) by each PCs.The red line represents the cumulative explained variance( % ) by PCs.The dotted vertical line represents the number of PCs to retains by using Elbow method. ";
                    ScreeDesDiv.appendChild(ScreeDesP);
                }
                screePlt();

                var spceP = document.createElement("p");
                spceP.innerHTML = "&nbsp;";
                allDivM.appendChild(spceP);

                // Score Plot with description
                var ScorePltDesc = document.createElement("div");
                ScorePltDesc.className = "col-12 mb-4";
                ScorePltDesc.style.textAlign = "-webkit-center";
                allDivM.appendChild(ScorePltDesc);

                var ScoreDiv = document.createElement("div");
                ScoreDiv.align = "center";
                ScoreDiv.id = "LoadID";
                ScoreDiv.style.border = "1px solid black";
                ScoreDiv.className = "pb-2";
                ScorePltDesc.appendChild(ScoreDiv);

                var ScoreH = document.createElement("h4");
                ScoreH.innerText = "Score Plot";
                ScoreH.style.fontWeight = "600";
                ScoreH.style.fontSize = "15px";
                ScoreDiv.appendChild(ScoreH);

                var ScoreDivSel = document.createElement("div");
                ScoreDivSel.align = "center";
                ScoreDivSel.id = "LoadID";
                ScoreDivSel.className = "pt-3 pb-2";
                ScoreDiv.appendChild(ScoreDivSel);

                function scorePlt() {
                    forwardStr = forwardStr[l];
                    forwardStr = forwardStr.split(",");
                    scorpltArr = scorpltArr[l];

                    var holdContainer = document.createElement("div");
                    holdContainer.className = "HoldContent";
                    holdContainer.id = "HoldContent" + l;
                    ScoreDiv.appendChild(holdContainer);

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
                }
                scorePlt();

                var XLab = document.createElement("label");
                XLab.innerHTML = "X-axis:&nbsp;";
                ScoreDivSel.appendChild(XLab);

                var scoreXSel = document.createElement("select");
                scoreXSel.setAttribute("name", "gseaVenn");
                scoreXSel.setAttribute("id", "forX_sel" + l);
                scoreXSel.className = "btn";
                scoreXSel.style.backgroundColor = "#EFEFEF";
                scoreXSel.style.fontSize = "12px";
                scoreXSel.style.border = "groove";
                scoreXSel.style.padding = "0px 5px 2px 0px";
                scoreXSel.style.marginRight = "35px";
                ScoreDivSel.appendChild(scoreXSel);

                var ScorefrstOtpnDrop = document.createElement("option");
                var ScorefrstOtpn = document.createTextNode("Select");
                ScorefrstOtpnDrop.appendChild(ScorefrstOtpn);
                scoreXSel.appendChild(ScorefrstOtpnDrop);

                var YLab = document.createElement("label");
                YLab.innerHTML = "Y-axis:&nbsp;";
                ScoreDivSel.appendChild(YLab);

                var scoreYSel = document.createElement("select");
                scoreYSel.setAttribute("name", "gseaVenn");
                scoreYSel.setAttribute("id", "forY_sel" + l);
                scoreYSel.className = "btn";
                scoreYSel.style.backgroundColor = "#EFEFEF";
                scoreYSel.style.fontSize = "12px";
                scoreYSel.style.border = "groove";
                scoreYSel.style.padding = "0px 5px 2px 0px";
                scoreYSel.style.marginRight = "35px";
                ScoreDivSel.appendChild(scoreYSel);

                var ScorefrstOtpnDrop = document.createElement("option");
                var ScorefrstOtpn = document.createTextNode("Select");
                ScorefrstOtpnDrop.appendChild(ScorefrstOtpn);
                scoreYSel.appendChild(ScorefrstOtpnDrop);

                var ttlNumPC = numScorPC[l];
                m = 1;
                ttlNumPC.forEach(function (key) {
                    // X-axis selection
                    var scoreSecndDrpOpt = document.createElement("option");
                    scoreSecndDrpOpt.setAttribute('value', key)
                    if (m == 1) {
                        scoreSecndDrpOpt.setAttribute('selected', 'true')
                    }
                    var scoreSecndOpt = document.createTextNode("PC " + m);
                    scoreSecndDrpOpt.appendChild(scoreSecndOpt);
                    scoreXSel.appendChild(scoreSecndDrpOpt);

                    // Y-axis selection
                    var scoreSecndDrpOpt = document.createElement("option");
                    scoreSecndDrpOpt.setAttribute('value', key)
                    if (m == 2) {
                        scoreSecndDrpOpt.setAttribute('selected', 'true')
                    }
                    var scoreSecndOpt = document.createTextNode("PC " + m);
                    scoreSecndDrpOpt.appendChild(scoreSecndOpt);
                    scoreYSel.appendChild(scoreSecndDrpOpt);
                    m++;
                });

                var scoreSubmit = document.createElement("input");
                scoreSubmit.class = "btn submitXY";
                scoreSubmit.type = "submit"
                scoreSubmit.id = "submitXY" + l;
                scoreSubmit.onclick = function () {
                    submitID = this.id;
                    submitIDNum = submitID.replace("submitXY", "");
                    if ($('#forX_sel' + submitIDNum).children('option:first-child').is(':selected') || $('#forY_sel' + submitIDNum).children('option:first-child').is(':selected')) {
                        alert("please select option for x-axis and y-axis");
                        return false;
                    } else {
                        $forXVal = $('#forX_sel' + submitIDNum).val();
                        $forYVal = $('#forY_sel' + submitIDNum).val();

                        $.ajax({
                            type: "post",
                            url: "scorePlt.php",
                            data: {
                                'forX': $forXVal,
                                'forY': $forYVal,
                                'scoreNum': submitIDNum
                            },
                            cache: false,
                            success: function (html) {
                                jQuery(`#HoldContent${submitIDNum}`).fadeOut(100, function () {
                                    jQuery(this).html(html);
                                }).fadeIn(1000);
                            }
                        });
                        return false;
                    }
                };
                scoreSubmit.value = "Show";
                scoreSubmit.style.borderRadius = "4px";
                scoreSubmit.style.border = "groove";
                scoreSubmit.style.padding = "0px 5px 3px 5px";
                ScoreDivSel.appendChild(scoreSubmit);

                // var ScorePltDiv = document.createElement("div");
                // ScorePltDiv.className = "seltPC"
                // ScorePltDiv.id = "scorPlotMainID" + l;
                // ScorePltDesc.appendChild(ScorePltDiv);

                var ScoreDesDiv = document.createElement("div");
                ScoreDesDiv.className = "col-12 col-4";
                ScorePltDesc.appendChild(ScoreDesDiv);

                var ScoreDesP = document.createElement("p");
                ScoreDesP.className = "col-10 m-auto";
                ScoreDesP.innerHTML = "The score plots indicate the projection of the data onto the span of the principal components. In the above score plot, the x-axis and y-axis represents the scores of PCs. The bubble represents the sample and its color represents the group of samples.  ";
                ScoreDesDiv.appendChild(ScoreDesP);

                var spceP = document.createElement("p");
                spceP.innerHTML = "&nbsp;";
                allDivM.appendChild(spceP);

                // Loading Plot
                var LoadPltDesc = document.createElement("div");
                LoadPltDesc.className = "col-12 mb-4";
                LoadPltDesc.style.textAlign = "-webkit-center";
                allDivM.appendChild(LoadPltDesc);

                var LoadDiv = document.createElement("div");
                LoadDiv.align = "center";
                LoadDiv.id = "LoadID";
                LoadDiv.style.border = "1px solid black";
                LoadPltDesc.appendChild(LoadDiv);

                var LoadDiv_child = document.createElement("div");
                LoadDiv_child.id = "LoadID";
                LoadDiv_child.className = "d-flex m-3";
                LoadDiv.appendChild(LoadDiv_child);

                var LoadH = document.createElement("div");
                LoadH.className = "m-auto";
                LoadH.innerText = "Loading Plot";
                LoadH.style.fontWeight = "600";
                LoadH.style.fontSize = "15px";
                LoadDiv_child.appendChild(LoadH);

                var loadDiv_drpdwn_div = document.createElement("span");
                LoadDiv_child.appendChild(loadDiv_drpdwn_div);

                // Loading Plot for selection based on elbow method
                var loadDiv_drpdwn_Sel = document.createElement("div");
                loadDiv_drpdwn_Sel.className = "btn-group";
                loadDiv_drpdwn_div.appendChild(loadDiv_drpdwn_Sel);

                var load_dropPCs = document.createElement("select");
                load_dropPCs.className = "btn btn-default resizing_select";
                load_dropPCs.id = "resizing_select_ID" + l;
                load_dropPCs.style.padding = "3px 5px 5px 7px";
                load_dropPCs.style.borderRadius = "10px 0px 0px 10px";
                load_dropPCs.style.fontSize = "12px";
                loadDiv_drpdwn_Sel.appendChild(load_dropPCs);

                var load_dropPCs_Option_1 = document.createElement("option");
                load_dropPCs_Option_1.id = "all_PCs_ID" + l;
                load_dropPCs_Option_1.innerHTML = "All PCs";
                load_dropPCs_Option_1.className = "all_PCs_cls";
                load_dropPCs.appendChild(load_dropPCs_Option_1);

                var load_dropPCs_Option_2 = document.createElement("option");
                load_dropPCs_Option_2.id = "Seltd_PCs_ID" + l;
                load_dropPCs_Option_2.innerHTML = "Selected PC (Elbow method)";
                load_dropPCs_Option_2.className = "Seltd_PCs_cls";
                load_dropPCs.appendChild(load_dropPCs_Option_2);

                var hidden_Dropdwn = document.createElement("select");
                hidden_Dropdwn.id = "width_tmp_select";
                loadDiv_drpdwn_Sel.appendChild(hidden_Dropdwn);

                var hidden_Dropdwn_Option = document.createElement("option");
                hidden_Dropdwn_Option.id = "width_tmp_option";
                hidden_Dropdwn.appendChild(hidden_Dropdwn_Option);

                // Loading Plot drop down for download the file and image
                var loadDiv_drpdwn_downld = document.createElement("div");
                loadDiv_drpdwn_downld.className = "btn-group";
                loadDiv_drpdwn_div.appendChild(loadDiv_drpdwn_downld);

                var loadDropbtn_downld = document.createElement("button");
                loadDropbtn_downld.className = "btn btn-default dropdown-toggle";
                $(loadDropbtn_downld).attr("data-toggle", "dropdown");
                loadDropbtn_downld.style.padding = "4px 5px 6px 7px";
                loadDropbtn_downld.style.borderRadius = "0px 10px 10px 0px";
                loadDropbtn_downld.type = "button";
                loadDiv_drpdwn_downld.appendChild(loadDropbtn_downld);

                var loadDrop_downld_I = document.createElement("i");
                loadDrop_downld_I.className = "glyphicon glyphicon glyphicon-export icon-share";
                loadDrop_downld_I.style.fontSize = "14px";
                loadDropbtn_downld.appendChild(loadDrop_downld_I);

                var load_dropPCs_downldUl = document.createElement("ul");
                load_dropPCs_downldUl.className = "dropdown-menu dropMenu_PCs_downld";
                $(load_dropPCs_downldUl).attr("role", "menu");
                load_dropPCs_downldUl.style.fontSize = "12px";
                loadDiv_drpdwn_downld.appendChild(load_dropPCs_downldUl);

                var load_dropPCs_downldLi1 = document.createElement("li");
                var load_dropPCs_downldLi1_a = document.createElement("a");
                load_dropPCs_downldLi1_a.innerHTML = "TSV";
                load_dropPCs_downldLi1_a.id = "tsv_" + l;

                var load_dropPCs_downldLi1_hidden = document.createElement("input");
                load_dropPCs_downldLi1_hidden.type = "hidden";
                load_dropPCs_downldLi1_hidden.value = key;
                load_dropPCs_downldUl.appendChild(load_dropPCs_downldLi1);
                load_dropPCs_downldLi1.appendChild(load_dropPCs_downldLi1_a);
                load_dropPCs_downldLi1_a.appendChild(load_dropPCs_downldLi1_hidden);

                var load_dropPCs_downldLi2 = document.createElement("li");
                var load_dropPCs_downldLi2_a = document.createElement("a");
                load_dropPCs_downldLi2_a.innerHTML = "Plot";
                load_dropPCs_downldLi2_a.id = "plot_" + l;

                var load_dropPCs_downldLi2_hidden = document.createElement("input");
                load_dropPCs_downldLi2_hidden.type = "hidden";
                load_dropPCs_downldLi2_hidden.value = key;
                load_dropPCs_downldUl.appendChild(load_dropPCs_downldLi2);
                load_dropPCs_downldLi2.appendChild(load_dropPCs_downldLi2_a);
                load_dropPCs_downldLi2_a.appendChild(load_dropPCs_downldLi2_hidden);

                $('.resizing_select').change(function () {
                    $("#width_tmp_option").html($('.resizing_select option:selected').text());
                    $(this).width($("#width_tmp_select").width());

                    if ($(this).find('option:selected').attr("class") == "all_PCs_cls") {
                        $('.allPCs_Plot').show();
                        $('.SeltdPCs_Plot').hide();
                    } else {
                        $('.allPCs_Plot').hide();
                        $('.SeltdPCs_Plot').show();
                    }
                });

                $('.dropMenu_PCs_downld a').on('click', function () {
                    x = this.id.split("_").pop();
                    numVal = $(this).children().val()
                    if (this.id.match(/tsv_/)) {
                        if ($('#resizing_select_ID' + x).find('option:selected').attr("id") == "all_PCs_ID" + x) {
                            load_dropPCs_downldLi1_a.href = "fileUpload/" + numVal + "/Loadingplot_table_all_PCs.txt";
                            load_dropPCs_downldLi1_a.download = "Loading Plot.txt";
                        } else {
                            load_dropPCs_downldLi1_a.href = "fileUpload/" + numVal + "/Loadingplot_table_selected_PCs.txt";
                            load_dropPCs_downldLi1_a.download = "Loading Plot.txt";
                        }
                    } else {
                        if ($('#resizing_select_ID' + x).find('option:selected').attr("id") == "all_PCs_ID" + x) {
                            $("#plot_" + x).attr({
                                "href": "fileUpload/" + numVal + "/loading_plot.png",
                                'download': 'Loading plot'
                            });
                        } else {
                            $("#plot_" + x).attr({
                                "href": "fileUpload/" + numVal + "/loading_plot_SelectedComp.png",
                                'download': 'Selected PCs Loading Plot'
                            });
                        }
                    }
                })

                // Loading Plot Image 1 
                var LoadImg = document.createElement("img");
                LoadImg.className = "allPCs_Plot";
                LoadImg.id = "allPCs_" + l;
                LoadImg.src = "fileUpload/" + key + "/loading_plot.png";
                LoadImg.width = "850";
                LoadImg.height = "850";
                LoadDiv.appendChild(LoadImg);

                // Loading Plot Image 2
                var LoadImg = document.createElement("img");
                LoadImg.className = "SeltdPCs_Plot";
                LoadImg.id = "SeltdPCs_" + l;
                LoadImg.src = "fileUpload/" + key + "/loading_plot_SelectedComp.png";
                LoadImg.width = "850";
                LoadImg.height = "750";
                LoadImg.style.display = "none";
                LoadDiv.appendChild(LoadImg);

                var LoadDesDiv = document.createElement("div");
                LoadDesDiv.className = "col-12 col-4";
                LoadPltDesc.appendChild(LoadDesDiv);

                var LoadDesP = document.createElement("p");
                LoadDesP.className = "col-10 m-auto";
                LoadDesP.innerHTML = "Loadings plot is the plot which use to represents the eigenvectors for each principal component. Eigenvectors are a list of coefficients which shows how much each input variable contributes to each new derived variable. In the above loadings plot, the x-axis represents the principal components (PCs) and y-axis represents the loadings by each variable. Bubbles represents the genes those falling within top/bottom 5% of loading range and bubble color represents the loadings values for PCs. The yellow, white and blue color of bubble represents the negative, zero and positive loadings value.";
                LoadDesDiv.appendChild(LoadDesP);

                var spceP = document.createElement("p");
                spceP.innerHTML = "&nbsp;";
                allDivM.appendChild(spceP);
            }

            function LolliPop_Plot() {
                var data = lolliArrVari[l];
                
                // Lollipop Plot
                var lolliPltDesc = document.createElement("div");
                lolliPltDesc.className = "col-12 mb-4";
                lolliPltDesc.style.textAlign = "-webkit-center";
                allDivM.appendChild(lolliPltDesc);

                var lolliDiv = document.createElement("div");
                lolliDiv.align = "center";
                lolliDiv.id = "lolliID";
                lolliDiv.style.border = "1px solid black";
                lolliPltDesc.appendChild(lolliDiv);

                var lolliH = document.createElement("h4");
                lolliH.innerText = "Lollipop Plot";
                lolliH.style.fontWeight = "600";
                lolliH.style.fontSize = "15px";
                lolliDiv.appendChild(lolliH);

                var lolliContainer = document.createElement("div");
                lolliDiv.appendChild(lolliContainer);
                lolliContainer.style.height = "800px";

                if(data.length != 0){
                    // if (data.legnth > 8) {
                    //     lolliContainer.style.height = "800px";
                    // }

                    function whiteSpacer(howMany) {
                        var spaceString = '';

                        while (howMany) {
                            spaceString += '&nbsp';
                            howMany--;
                        }

                        return spaceString;
                    }
                    Highcharts.chart(lolliContainer, {
                        chart: {
                            type: 'lollipop',
                            inverted: true
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
                                text: 'Enriched pathways',
                                style: {
                                    color: 'black',
                                    fontSize: '14px',
                                    fontFamily: 'Verdana, Geneva, sans-serif',
                                    fontWeight: 'bold'
                                }
                            },
                            type: 'category',
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
                                text: 'Normalized Enrichment Score (NES)',
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

                        series: data,

                        tooltip: {
                            useHTML: true,
                            headerFormat: '<table>',
                            pointFormat: '<tr><th>Pathway: </th><td>{point.name}</td></tr>' +
                                '<tr><th>NES : </th><td>{point.y}</td></tr>',
                            footerFormat: '</table>',
                            followPointer: true,
                        },
                        exporting: {
                            filename: 'Lollipop Plot'
                        }
                    });

                    var lolliDesDiv = document.createElement("div");
                    lolliDesDiv.className = "col-12 mb-4";
                    lolliPltDesc.appendChild(lolliDesDiv);

                    var lolliDesP = document.createElement("p");
                    lolliDesP.className = "col-10 m-auto";
                    lolliDesP.innerHTML = "Lollipop plot showing top 10 upregulated and downregulated pathways obtained from Gene Set Enrichment Analysis (GSEA). The x-axis represents normalized enrichment scores (NES) and y-axis represents the enriched pathways. The red and green color of lollipops respectively represents downregulation and upregulation of enriched pathways. ";
                    lolliDesDiv.appendChild(lolliDesP);
                }else{
                    var lolliEmpty = document.createElement("h5");
                    lolliEmpty.innerText = "No data has been found for lollipop plot";
                    lolliEmpty.align = "center";
                    lolliContainer.appendChild(lolliEmpty);
                }

                var spceP = document.createElement("p");
                spceP.innerHTML = "&nbsp;";
                allDivM.appendChild(spceP);
            }

            function volcanoPlot() {
                // Volcano Plot
                var volcanoPltDesc = document.createElement("div");
                volcanoPltDesc.className = "col-12 mb-4";
                volcanoPltDesc.style.textAlign = "-webkit-center";
                allDivM.appendChild(volcanoPltDesc);

                var volacanoDiv = document.createElement("div");
                volacanoDiv.align = "center";
                volacanoDiv.id = "chart" + l;
                volacanoDiv.className = "chart";
                volacanoDiv.style.border = "1px solid black";
                volcanoPltDesc.appendChild(volacanoDiv);

                var btnLegndDiv = document.createElement("div");
                btnLegndDiv.className = "btnLegnds";
                volacanoDiv.appendChild(btnLegndDiv);

                var rstBtnDiv = document.createElement("div");
                rstBtnDiv.className = "rstBtn";
                btnLegndDiv.appendChild(rstBtnDiv);

                var rstbtn = document.createElement("button");
                rstbtn.id = "resetBtn";
                rstbtn.innerHTML = "Reset";
                rstBtnDiv.appendChild(rstbtn);

                var legendDiv = document.createElement("div");
                legendDiv.className = "legends";
                legendDiv.id = "legends";
                btnLegndDiv.appendChild(legendDiv);

                var triangleUpRDiv = document.createElement("div");
                triangleUpRDiv.className = "triangleUpR";
                legendDiv.appendChild(triangleUpRDiv);

                var triangleUpDiv = document.createElement("div");
                triangleUpDiv.className = "triangle-up";
                triangleUpRDiv.appendChild(triangleUpDiv);

                var lbelUp = document.createElement("label");
                lbelUp.innerHTML = "Upregulated";
                triangleUpRDiv.appendChild(lbelUp);

                var triangleDownRDiv = document.createElement("div");
                triangleDownRDiv.className = "triangleDownR";
                legendDiv.appendChild(triangleDownRDiv);

                var triangleDownDiv = document.createElement("div");
                triangleDownDiv.className = "triangle-down";
                triangleDownRDiv.appendChild(triangleDownDiv);

                var lbelDown = document.createElement("label");
                lbelDown.innerHTML = "Downregulated";
                triangleDownRDiv.appendChild(lbelDown);

                var circularGeneDiv = document.createElement("div");
                circularGeneDiv.className = "circularGene";
                legendDiv.appendChild(circularGeneDiv);

                var circularDiv = document.createElement("div");
                circularDiv.className = "circular";
                circularGeneDiv.appendChild(circularDiv);

                var lbelNotSig = document.createElement("label");
                lbelNotSig.innerHTML = "Non-significant";
                circularGeneDiv.appendChild(lbelNotSig);

                var downldDiv = document.createElement("div");
                downldDiv.className = "rstBtn";
                btnLegndDiv.appendChild(downldDiv);

                var dwnldbtn = document.createElement("button");
                dwnldbtn.className = "fa fa-download downldBtn";
                dwnldbtn.id = "dwnldVolcano" + l;
                dwnldbtn.onclick = function () {
                    dnlodID = $(this).attr("id").replace('dwnldVolcano', '');
                    saveSvgAsPng(document.getElementById("volcPltID_" + dnlodID), "Volcano plot.png", {
                        scale: 2,
                        backgroundColor: "#FFFFFF"
                    });
                };
                downldDiv.appendChild(dwnldbtn);

                var volacDesDiv = document.createElement("div");
                volacDesDiv.className = "col-12 mb-4";
                volcanoPltDesc.appendChild(volacDesDiv);

                var volacDesP = document.createElement("p");
                volacDesP.className = "col-10 m-auto";
                volacDesP.innerHTML = "A volcano plot displaying the result of microarray data analysis. A volcano plot is a type of scatter plot showing fold-change and p-values for genes. In volcano plot, the x-axis represents log2 fold change and y-axis represents the p-value (-log10p-value). Genes are represented by dots and the color of dots shows their regulation status. The red and green colored dots represent the downregulated and upregulated genes respectively.  The grey colored dots shows statistically non-significant genes. ";
                volacDesDiv.appendChild(volacDesP);
            }
            l++;
        });

        var k = 0;
        randID.forEach(function (key) {
            var DrpDwnLst = document.createElement('li');
            var DrpDwnLstA = document.createElement('a');
            DrpDwnLstA.href = "#degAnalTitlepltID" + k;
            DrpDwnLstA.innerText = $("#degAnalTitlepltID" + k).text();
            DrpDwnLst.appendChild(DrpDwnLstA);
            $('.goToStuLst').append(DrpDwnLst);

            k++;
        });
    }
    plotTab();

    /////////////////////////////////     Meta-Analysis tab     ///////////////////////////////// 
    if (randID.length > 1) {
        if (document.getElementById("degList_Anal") != null) {
            degMeta();
            $('#metaP_Mdiv').hide();
        } else if (document.getElementById("rnkFile_Anal") != null) {
            gseaMeta();
            $('#metaG_Mdiv').hide();
        } else {
            degMeta();
            gseaMeta();
        }


        function degMeta() {
            function metaG_barP() {
                var result = <?= json_encode($baP_Gene); ?>; 

                function whiteSpacer(howMany) {
                    var spaceString = '';

                    while (howMany) {
                        spaceString += '&nbsp';
                        howMany--;
                    }

                    return spaceString;
                }
                $(function () {
                    $('#barP_Gene').highcharts({
                        chart: {
                            type: 'column'
                        },
                        title: {
                            useHTML: true,
                            text: whiteSpacer(50)
                        },

                        credits: {
                            enabled: false
                        },

                        xAxis: {
                            categories: result[0]['xlbl'],
                            title: {
                                text: 'Studies',
                                style: {
                                    color: 'black',
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, Geneva, sans-serif'
                                },
                                margin: 15
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
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, Geneva, sans-serif',
                                    fill: 'black',
                                    color: 'black'
                                }
                            },
                            title: {
                                text: 'Number of DEGs',
                                style: {
                                    color: 'black',
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, Geneva, sans-serif'
                                },
                                margin: 30
                            }
                        },

                        plotOptions: {
                            series: {
                                groupPadding: 0.4
                            }
                        },

                        series: result,

                        tooltip: {
                            formatter: function () {
                                return 'Number of DEGs: <b>' + this.y + '</b>';
                            }
                        },

                        exporting: {
                            filename: 'Bar plot'
                        }
                    });
                });
            };
            metaG_barP();


            function metaG_HeatMap() {
                var heatData = <?= json_encode($heatArray_Gen); ?>;
                if (heatData.length != 0) {
                    var layout = {
                        height: 1000,
                        width: 1000,
                        title: ' ',
                        xaxis: {
                            title: '<b>Samples</b>',
                            titlefont: {
                                family: 'Verdana, Geneva, sans-serif',
                                size: 14,
                                color: 'black'
                            },
                            showticklabels: true,
                            tickangle: 'auto',
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
                    };
                    Plotly.newPlot('heatMap_Gene', heatData, layout);
                }
            }
            metaG_HeatMap();
        };
        degMeta();


        function gseaMeta() {
            function metaP_barPlt() {
                var result = <?= json_encode($baP_Path); ?>;  

                function whiteSpacer(howMany) {
                    var spaceString = '';

                    while (howMany) {
                        spaceString += '&nbsp';
                        howMany--;
                    }

                    return spaceString;
                }
                $(function () {
                    $('#barP_Path').highcharts({
                        chart: {
                            type: 'column'
                        },
                        title: {
                            useHTML: true,
                            text: whiteSpacer(50)
                        },

                        credits: {
                            enabled: false
                        },

                        xAxis: {
                            categories: result[0]['xlbl'],
                            title: {
                                text: 'Studies',
                                style: {
                                    color: 'black',
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, Geneva, sans-serif'
                                },
                                margin: 15
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
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, Geneva, sans-serif',
                                    fill: 'black',
                                    color: 'black'
                                }
                            },
                            title: {
                                text: 'Number of enriched pathways',
                                style: {
                                    color: 'black',
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, Geneva, sans-serif'
                                },
                                margin: 30
                            }
                        },

                        plotOptions: {
                            series: {
                                groupPadding: 0.4
                            }
                        },

                        series: result,
                        
                        tooltip: {
                            formatter: function () {
                                return 'Number of Enriched pathway: <b>' + this.y + '</b>';
                            }
                        },

                        exporting: {
                            filename: 'Bar plot'
                        }
                    });
                });
            }
            metaP_barPlt();

            function metaP_HeatMap() {
                var heatData = <?= json_encode($heatArray_Path); ?>;
                if (heatData.length != 0) {
                    var layout = {
                        height: 1000,
                        width: 1000,
                        title: ' ',
                        xaxis: {
                            title: '<b>Samples</b>',
                            titlefont: {
                                family: 'Verdana, Geneva, sans-serif',
                                size: 14,
                                color: 'black'
                            },
                            showticklabels: true,
                            tickangle: 'auto',
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
                                text: '<b>Common pathways</b>',
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
                    };
                    Plotly.newPlot('heatMap_Path', heatData, layout);
                }
            }
            metaP_HeatMap();
        };
        gseaMeta();
    } else {
        $("#metaToggle").hide();
    }

    // Venn-analysis
    if (randID.length > 1 && randID.length < 7) {
        if (document.getElementById("degList_Anal") != null) {
            $('#vennPathway').hide();
            vennGene_func()
        } else if (document.getElementById("rnkFile_Anal") != null) {
            $('#vennGenes').hide();
            vennPathway_func();
        } else {
            vennPathway_func();
            vennGene_func();
        }

        function vennPathway_func() {
            var PvennTab = document.getElementById("Pvenn_analysis_tab");
            var PselDrop = document.createElement("select");
            PselDrop.setAttribute("name", "gseaVenn");
            PselDrop.setAttribute("id", "PselDropID");
            PselDrop.className = "btn";
            PselDrop.style.backgroundColor = "#EFEFEF";
            PselDrop.style.fontSize = "12px";
            PselDrop.style.border = "groove";
            PselDrop.style.padding = "5px";
            PvennTab.appendChild(PselDrop);

            var PoptionDrop = document.createElement("option");
            var PoptionDroptext = document.createTextNode("Select regulation");
            PoptionDrop.appendChild(PoptionDroptext);
            var PoptionDrop1 = document.createElement("option");
            PoptionDrop1.setAttribute("value", "All");
            var PoptionDroptext1 = document.createTextNode("All");
            PoptionDrop1.appendChild(PoptionDroptext1);
            var PoptionDrop2 = document.createElement("option");
            PoptionDrop2.setAttribute("value", "Upregulated");
            var PoptionDroptext2 = document.createTextNode("Upregulated");
            PoptionDrop2.appendChild(PoptionDroptext2);
            var PoptionDrop3 = document.createElement("option");
            PoptionDrop3.setAttribute("value", "Downregulated");
            var PoptionDroptext3 = document.createTextNode("Downregulated");
            PoptionDrop3.appendChild(PoptionDroptext3);

            PselDrop.appendChild(PoptionDrop);
            PselDrop.appendChild(PoptionDrop1);
            PselDrop.appendChild(PoptionDrop2);
            PselDrop.appendChild(PoptionDrop3);

            $(document).ready(function () {
                $('#PselDropID').on('change', function () {
                    if ($(this).children('option:first-child').is(':selected')) {
                        alert("please select option");
                        $('#PvennAnalLoadID').empty();
                    } else {
                        $('#GselDropID').prop('selectedIndex', 0);
                        $('#GvennAnalLoadID').empty();
                        $selVal = $(this).val();
                        $('#PvennAnalLoadID').load("venn_pathway.php?selectedValue=" + $selVal);
                    }
                });
            });
        }

        function vennGene_func() {
            var Gvenn_analysis_tab = document.getElementById("Gvenn_analysis_tab");
            var GselDrop = document.createElement("select");
            GselDrop.setAttribute("name", "gseaVenn");
            GselDrop.setAttribute("id", "GselDropID");
            GselDrop.className = "btn";
            GselDrop.style.backgroundColor = "#EFEFEF";
            GselDrop.style.fontSize = "12px";
            GselDrop.style.border = "groove";
            GselDrop.style.padding = "5px";
            Gvenn_analysis_tab.appendChild(GselDrop);

            var GoptionDrop = document.createElement("option");
            var GoptionDroptext = document.createTextNode("Select regulation");
            GoptionDrop.appendChild(GoptionDroptext);
            var GoptionDrop1 = document.createElement("option");
            GoptionDrop1.setAttribute("value", "All");
            var GoptionDroptext1 = document.createTextNode("All");
            GoptionDrop1.appendChild(GoptionDroptext1);
            var GoptionDrop2 = document.createElement("option");
            GoptionDrop2.setAttribute("value", "Upregulated");
            var GoptionDroptext2 = document.createTextNode("Upregulated");
            GoptionDrop2.appendChild(GoptionDroptext2);
            var GoptionDrop3 = document.createElement("option");
            GoptionDrop3.setAttribute("value", "Downregulated");
            var GoptionDroptext3 = document.createTextNode("Downregulated");
            GoptionDrop3.appendChild(GoptionDroptext3);

            GselDrop.appendChild(GoptionDrop);
            GselDrop.appendChild(GoptionDrop1);
            GselDrop.appendChild(GoptionDrop2);
            GselDrop.appendChild(GoptionDrop3);

            $(document).ready(function () {
                $('#GselDropID').on('change', function () {
                    if ($(this).children('option:first-child').is(':selected')) {
                        alert("please select option");
                        $('#GvennAnalLoadID').empty();
                    } else {
                        $('#PselDropID').prop('selectedIndex', 0);
                        $('#PvennAnalLoadID').empty();
                        $selVal = $(this).val();
                        $('#GvennAnalLoadID').load("venn_genes.php?selectedValue=" + $selVal);
                    }
                });
            });
        }
    } else {
        $('#vennToggle').hide();
    }
</script>
<script src="js/d3.v4.min.js"></script>
<script>
    var randID = <?= json_encode($_SESSION["randNum"]); ?>;
    if (document.getElementById("degList_Anal") != null) {
        volcanoPltDEG();
    }else if(document.getElementById("rnkFile_Anal") != null){
        console.log("Nothing to do!!!!!!!!!");
    }else{
        volcanoPltDEG();
    }

    function volcanoPltDEG() {
        var pvalCutOff = <?= json_encode($_POST['tPvalue_CEL']); ?>; 
        var logFC_U = <?= json_encode($_POST['tLogfcu_CEL']); ?>; 
        var logFC_D = <?= json_encode($_POST['tLogfcd_CEL']); ?>; 

        var l = 0;
        randID.forEach(function (key) {
            var idVari = "volcPltID_" + l;
            var yLabel = '-log<tspan baseline-shift="sub">10</tspan>(p-value)',
                xLabel = 'log<tspan baseline-shift="sub">2</tspan>Fold-change',
                file = "fileUpload/" + key + "/ModT-Results.txt";

            var volcanoPlot = volcanoPlot()
                .xAxisLabel(xLabel)
                .yAxisLabel(yLabel)
                .sampleID("Gene symbol")
                .xColumn("Log2 fold change")
                .yColumn("P-value");

            var callIDVar = document.getElementById("chart" + l);
            d3.tsv(file, parser, function (error, data) {
                if (error) console.log(error);
                d3.select(callIDVar)
                    .data([data])
                    .call(volcanoPlot);
            });

            // row parser to convert key values into numbers if possible
            function parser(d) {
                for (var key in d) {
                    if (d.hasOwnProperty(key)) {
                        d[key] = numberParser(d[key]);
                    }
                }
                return d;
            }

            // function to turn string into number if possible
            function numberParser(value) {
                return (+value) ? +value : value;
            }

            function volcanoPlot() {
                var width = 1000,
                    height = 700,
                    margin = {
                        top: 40,
                        right: 20,
                        bottom: 40,
                        left: 80
                    },
                    xColumn, // name of the variable to be plotted on the axis
                    yColumn,
                    xAxisLabel, // label for the axis
                    yAxisLabel,
                    xAxisLabelOffset, // offset for the label of the axis
                    yAxisLabelOffset,
                    xTicks, // number of ticks on the axis
                    yTicks,
                    sampleID = "Gene",
                    significanceThreshold = pvalCutOff, // significance threshold to colour by
                    // foldChangeThreshold = 2.0, // fold change level to colour by
                    colorRange, // colour range to use in the plot
                    xScale = d3.scaleLinear(), // the values for the axes will be continuous
                    yScale = d3.scaleLog();

                function chart(selection) {
                    var innerWidth = width - margin.left - margin.right, // set the size of the chart within its container
                        innerHeight = height - margin.top - margin.bottom - 25;

                    selection.each(function (data) {

                        // set up the scaling for the axes based on the inner width/height of the chart and also the range
                        // of value for the x and y axis variables. This range is defined by their min and max values as
                        // calculated by d3.extent()
                        xScale.range([0, innerWidth])
                            .domain(d3.extent(data, function (d) {
                                return d[xColumn];
                            }))
                            .nice();

                        // normally would set the y-range to [height, 0] but by swapping it I can flip the axis and thus
                        // have -log10 scale without having to do extra parsing
                        yScale.range([0, innerHeight])
                            .domain(d3.extent(data, function (d) {
                                return d[yColumn];
                            }))
                            .nice(); // adds "padding" so the domain extent is exactly the min and max values

                        var zoom = d3.zoom()
                            .scaleExtent([1, 20])
                            .translateExtent([
                                [0, 0],
                                [width, height]
                            ])
                            .on('zoom', zoomFunction);

                        // append the svg object to the selection
                        var svg = d3.select(this).append('svg')
                            .attr('id', idVari)
                            .attr('height', height)
                            .attr('width', width)
                            // .style('border', '1px solid black')
                            .append('g')
                            .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')
                            .call(zoom);

                        // position the reset button and attach reset function
                        d3.selectAll('#resetBtn')
                            // .style('top', margin.top + 'px')
                            // .style('left', margin.left * 1.25 + 'px')
                            .on('click', reset);

                        // d3.select('#legends')
                        //     .style('top', margin.top + 'px')
                        //     .style('left', margin.left * 1.5 + 'px');

                        svg.append('defs').append('clipPath')
                            .attr('id', 'clip')
                            .append('rect')
                            .attr('height', innerHeight)
                            .attr('width', innerWidth);

                        // add the axes
                        var xAxis = d3.axisBottom(xScale);
                        var yAxis = d3.axisLeft(yScale)
                            .ticks(5)
                            .tickFormat(yTickFormat);

                        var gX = svg.append('g')
                            .attr('class', 'x axis')
                            .attr('transform', 'translate(0,' + innerHeight + ')')
                            .call(xAxis);

                        gX.append('text')
                            .attr('class', 'label')
                            .attr('transform', 'translate(' + width / 2.5 + ',' + (margin.bottom - 6) + ')')
                            .attr('text-anchor', 'middle')
                            .html(xAxisLabel || xColumn);

                        var gY = svg.append('g')
                            .attr('class', 'y axis')
                            .call(yAxis);

                        gY.append('text')
                            .attr('class', 'label')
                            .attr('transform', 'translate(' + (0 - margin.left / 1.25) + ',' + (height / 2.5) +
                                ') rotate(-90)')
                            .style('text-anchor', 'middle')
                            .html(yAxisLabel || yColumn);

                        // this rect acts as a layer so that zooming works anywhere in the svg. otherwise, if zoom is called on
                        // just svg, zoom functionality will only work when the pointer is over a circle.
                        var zoomBox = svg.append('rect')
                            .attr('class', 'zoom')
                            .attr('height', innerHeight)
                            .attr('width', innerWidth);

                        var circles = svg.append('g')
                            .attr('class', 'circlesContainer');

                        circles.selectAll(".dot")
                            .data(data)
                            .enter().append('circle')
                            .attr('r', 3)
                            .attr('cx', function (d) {
                                return xScale(d[xColumn]);
                            })
                            .attr('cy', function (d) {
                                return yScale(d[yColumn]);
                            })
                            .attr('class', circleClass)
                            .on('mouseenter', tipEnter)
                            .on("mousemove", tipMove)
                            .on('mouseleave', function (d) {
                                return tooltip.style('visibility', 'hidden');
                            });

                        var thresholdLines = svg.append('g')
                            .attr('class', 'thresholdLines');

                        // add horizontal line at significance threshold
                        thresholdLines.append("svg:line")
                            .attr('class', 'threshold')
                            .attr("x1", 0)
                            .attr("x2", innerWidth)
                            .attr("y1", yScale(significanceThreshold))
                            .attr("y2", yScale(significanceThreshold));

                        // add vertical line(s) at fold-change threshold (and negative fold-change)
                        [logFC_U, logFC_D].forEach(function (threshold) {
                            thresholdLines.append("svg:line")
                                .attr('class', 'threshold')
                                .attr("x1", xScale(threshold))
                                .attr("x2", xScale(threshold))
                                .attr("y1", 0)
                                .attr("y2", innerHeight);
                        });

                        var tooltip = d3.select("body")
                            .append("div")
                            .attr('class', 'tooltip');

                        function tipEnter(d) {
                            tooltip.style('visibility', 'visible')
                                .style('font-size', '11px')
                                .html(
                                    '<strong>' + sampleID + '</strong>: ' + d[sampleID] + '<br/>' +
                                    '<strong>' + xColumn + '</strong>: ' + d3.format('.2f')(d[xColumn]) + '<br/>' +
                                    '<strong>' + yColumn + '</strong>: ' + d[yColumn]
                                );
                        }

                        function tipMove() {
                            tooltip.style("top", (event.pageY - 5) + "px")
                                .style("left", (event.pageX + 20) + "px");
                        }

                        function yTickFormat(n) {
                            return d3.format(".2r")(getBaseLog(10, n));

                            function getBaseLog(x, y) {
                                return Math.log(y) / Math.log(x);
                            }
                        }

                        function zoomFunction() {
                            var transform = d3.zoomTransform(this);
                            d3.selectAll('.dot')
                                .attr('transform', transform)
                                .attr('r', 3 / Math.sqrt(transform.k));
                            gX.call(xAxis.scale(d3.event.transform.rescaleX(xScale)));
                            gY.call(yAxis.scale(d3.event.transform.rescaleY(yScale)));
                            svg.selectAll('.threshold')
                                .attr('transform', transform)
                                .attr('stroke-width', 1 / transform.k);
                        }

                        function circleClass(d) {
                            if (d[yColumn] < significanceThreshold && d[xColumn] >= logFC_U)
                                return 'dot sigfoldU';
                            else if (d[yColumn] < significanceThreshold && d[xColumn] <= logFC_D)
                                return 'dot sigfoldD';
                            else return 'dot nonSig';
                        }

                        function reset() {
                            var ease = d3.easePolyIn.exponent(4.0);
                            svg.transition().duration(750)
                                .ease(ease)
                                .call(zoom.transform, d3.zoomIdentity);
                        }
                    });
                }

                chart.width = function (value) {
                    if (!arguments.length) return width;
                    width = value;
                    return chart;
                };

                chart.height = function (value) {
                    if (!arguments.length) return height;
                    height = value;
                    return chart;
                };

                chart.margin = function (value) {
                    if (!arguments.length) return margin;
                    margin = value;
                    return chart;
                };

                chart.xColumn = function (value) {
                    if (!arguments.length) return xColumn;
                    xColumn = value;
                    return chart;
                };

                chart.yColumn = function (value) {
                    if (!arguments.length) return yColumn;
                    yColumn = value;
                    return chart;
                };

                chart.xAxisLabel = function (value) {
                    if (!arguments.length) return xAxisLabel;
                    xAxisLabel = value;
                    return chart;
                };

                chart.yAxisLabel = function (value) {
                    if (!arguments.length) return yAxisLabel;
                    yAxisLabel = value;
                    return chart;
                };

                chart.xAxisLabelOffset = function (value) {
                    if (!arguments.length) return xAxisLabelOffset;
                    xAxisLabelOffset = value;
                    return chart;
                };

                chart.yAxisLabelOffset = function (value) {
                    if (!arguments.length) return yAxisLabelOffset;
                    yAxisLabelOffset = value;
                    return chart;
                };

                chart.xTicks = function (value) {
                    if (!arguments.length) return xTicks;
                    xTicks = value;
                    return chart;
                };

                chart.yTicks = function (value) {
                    if (!arguments.length) return yTicks;
                    yTicks = value;
                    return chart;
                };

                chart.significanceThreshold = function (value) {
                    if (!arguments.length) return significanceThreshold;
                    significanceThreshold = value;
                    return chart;
                };

                // chart.foldChangeThreshold = function (value) {
                //     if (!arguments.length) return foldChangeThreshold;
                //     foldChangeThreshold = value;
                //     return chart;
                // };

                chart.colorRange = function (value) {
                    if (!arguments.length) return colorRange;
                    colorRange = value;
                    return chart;
                };

                chart.sampleID = function (value) {
                    if (!arguments.length) return sampleID;
                    sampleID = value;
                    return chart;
                };
                return chart;
            }
            l++;
        });
    }
</script>

<?php
    include('footer.php');
?>