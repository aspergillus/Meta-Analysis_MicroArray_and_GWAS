<?php
    session_start();
    $selDis = explode("; ", $_POST['sel_dis']);
    if($selDis[0] == "on"){
        array_shift($selDis);
        $selDis = $selDis;
    }else{
        $selDis = $selDis;
    }

    ///////// include header  //////
    include('header.php');                      /// Don't move from here   
?>
<style>
    .qtl_cls{
        cursor: pointer;
    }
</style>
<script src="js/jquery_3.2.1.js"></script>
<div>
    <h2 align="center">Haplotype analysis</h2>
    <table width="99%" border="1" cellspacing="0" cellpadding="4" bordercolor="#B99C6B">
        <tr>
            <td>
                <?php 
                    if($_SESSION['dataset']=="disease") {
                        ?><strong>Disease/Trait</strong><?php
                    }

                    if($_SESSION['dataset']=="efo") {
                        ?><strong>EFO term </strong><?php
                    }
                ?>
            </td>
            <td>Chr</td>
            <td>Chr Pos</td>
            <td><strong>Query SNPs ID</strong></td>
            <td><strong>Associated SNPs ID</strong></td>
            <td><strong>r2</strong></td>
            <td><strong>D'</strong></td>
            <td><strong>eQTL</strong></td>
        </tr>
        <?php
            foreach($selDis as $key){
                $disID = explode("***", $key);
                if($disID[1] != ""){
                    $rsIDs = str_replace(' ', '', $disID[1]);
                    $xyz = exec('"C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/R_file/gwas_haplo.R" '."$rsIDs");
                    
                    $result = array();
                    $file = "R_file/Output/haplo_output.txt";
                    $fh = fopen($file, 'r');
                    while (($line = fgetcsv($fh, 10000, "\t")) !== false) {
                        $result[] = $line;
                    }
                    array_shift($result);
                    
                    for($r=0, $q=0; $r<sizeof($result); $r++){
                        if($result[$r][4] == 0){
                            ?>
                                <tr>
                                    <?php
                                        if($r == 0){
                                            ?>
                                                <td valign="top" rowspan="<?= count($result); ?>"><?= $disID[0] ?></td>
                                            <?php
                                        }
                                    ?>
                                    <td><?= $result[$r][0] ?></td>
                                    <td><?= $result[$r][1] ?></td>
                                    <td><?= $result[$r][32] ?></td>
                                    <td><?= $result[$r][5] ?></td>
                                    <td><?= $result[$r][2] ?></td>
                                    <td><?= $result[$r][3] ?></td>
                                    <td>
                                        <?php
                                            if($result[$r][19] != '.'){
                                                ?>
                                                    <form action="tissue_bar.php" method="post" target="_blank">
                                                        <a class="qtl_cls" id="<?php echo "qTl_id_".$q ?>" onclick="tans_dat(this.id);">Click here</a>
                                                        <input type="hidden" value = "<?php echo $disID[0].'***'.$result[$r][19]; ?>">
                                                        <input type="hidden" name="tis_dat" id="eQTL_data">
                                                        <input type="submit" style="display: none; ">
                                                    </form>
                                                <?php
                                            }else{
                                                echo " - ";
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            $q++;
                        }
                    }
                }
            }
        ?>
    </table>
</div>
<script>
    function tans_dat(qID){
        var x = document.getElementById('eQTL_data');
        x.value = $('#'+qID).next().val();
        $(x).closest("form").submit();
    }
</script>
<?php
    include('footer.php');
?>
