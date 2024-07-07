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
	include "connect.php";
	header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure");
	session_start();

	//////////////////////////// Uncomment (It is for the Seeds) ////////////////////////////
	// $selDis = explode("; ", $_POST['selDis']);
	// if($selDis[0] == 'on'){
	// 	array_shift($selDis);
	// }

	// foreach($selDis as $sVal){
	// 	$aGen = explode('***', $sVal)[2];
	// 	$aGen = explode(',', $aGen);
	// 	$x = 0;
	// 	while($x < sizeof($aGen)){
	// 		$gwas_genes[] = $aGen[$x];
	// 		$x++;
	// 	}
	// }
	// $gwas_genes = array_values(array_unique($gwas_genes));
	// $gwas_trait = explode(',', $_POST['selectedDis']);
	// foreach($gwas_trait as $disVal){
	// 	$query = mysqli_query($conn, "SELECT * FROM `commonDisease` WHERE diseases_biomitra = '$disVal'");
	// 	while($row = mysqli_fetch_array($query)){
	// 		$diseasename[] = $row['disease_merge_gedipnet'];
	// 	}
	// }
	// $diseasename = array_unique($diseasename);
	
	// // Seeds
	// foreach($diseasename as $dVal){
	// 	$query = mysqli_query($conn, "SELECT * FROM `disease_gdp` WHERE disease_merge = '$dVal'");
	// 	while($row = mysqli_fetch_array($query)){
	// 		$seed[] = $row['geneSymbol'];
	// 	}
	// }
	// $seed = array_values(array_unique($seed));
	////////////////////////////// Till here ///////////////////////////////


	//////////////////////////// Commend or Remove this when no need ////////////////////////////
	$gwas_genes = array("ANO7", "RASSF6", "POGLUT3", "GPER1", "GGCX", "NUDT11", "PPP1R14A", "RGS17", "IRX4", "NCOA4", "FAM57A", "C9orf78", "SNRPC", "GMEB2", "MAD1L1", "PSORS1C3", "SHROOM2", "CALM2", "RAB29", "CREB3L4", "SLC7A3", "HLA-DRB5", "VAMP5", "MLPH", "TNS3", "VPS53", "CTBP2", "VAMP8", "SRF", "HCG4", "C14orf39", "GPR143", "PEX14", "OTX1", "HIBADH", "GEMIN4", "STXBP1", "MSMB", "ZNF652", "C7orf50", "MYO9B", "GJB1", "WBP1L", "PM20D1", "TBX1", "HCG27", "SETD9", "EBF2", "NOL10", "BORCS7", "PSORS1C1", "SESN1", "NOTCH4", "TOR1AIP1");
	// $gwas_genes = array("ANO7", "RASSF6", "POGLUT3");
	$gwas_genes = array_unique($gwas_genes); 
	$gwas_genes = array_values($gwas_genes);
	$gwas_trait = "Prostate cancer";
	$diseasename = "Prostate cancer";
	$seed = array("C10orf143", "VAC14", "AKT1", "AR", "ATM", "AURKA", "AXIN1", "BRAF", "BRCA1", "BRCA2", "CDKN2A", "CHEK2", "CNOT9", "CTNNB1", "EGFR", "EPHB2", "ERBB2", "FANCA", "FOXA1", "HDAC2", "HRAS", "IDH1", "KLF6", "MAD1L1", "MAP2K1", "MED12", "MYC", "PALB2", "PIK3CA", "PPP2R1A", "PTEN", "SMAD4", "SPOP", "TP53", "XPO1", "AAAS", "ABCC4", "ABCC6", "ABCC8", "ABCG5", "ABO", "ABR", "ACE", "ACHE", "ACRBP", "ACSL4", "ACSM3", "ADAM28", "ADAM9", "ADAMTS8", "ADGRG1", "ADI1", "ADNP", "ADRB2", "AFM", "AGMO", "AGR2", "AHCYL2", "AHR", "AKAP13", "AKR1C3", "ALAD", "ALDH1A2", "ALOX12B", "ALOX5", "ALOXE3", "AMACR", "ANKRD44", "ANKRD44-IT1", "ANTXR2", "ANXA1", "ANXA3", "ANXA4", "AOX1", "APC", "APEX1", "APOLD1", "APPL2", "ARG2", "ARHGAP6", "ARHGEF5", "ARID1A", "ARID2", "ARID4A", "ARL6IP1", "ARMC2", "ASH1L", "ASZ1", "ATAD3A", "ATAT1", "ATF3", "ATP10A", "ATP7B", "ATR", "ATXN2", "AZGP1", "B2M", "B3GAT1", "B4GALT4", "BABAM1", "BAD", "BAP1", "BARD1", "BAX", "BAZ2A", "BCAR1", "BCAS1", "BCL2", "BCL2L11", "BGLAP", "BIRC5", "BMP7", "BMPR1A", "BMPR1B", "BNIP3", "BOLL", "BRD4", "BRIP1", "BRPF1", "C10orf90", "CACNA2D3", "CALCA", "CALR", "CAMKK2", "CAMTA1", "CAPNS1", "CASC19", "CASC8", "CASP8", "CASP9", "CASZ1", "CAV1", "CAV2", "CBR1", "CBX1", "CCAT2", "CCHCR1", "CCND1", "CCND2", "CCNH", "CD276", "CD9", "CDC27", "CDCA7", "CDH1", "CDH12", "CDH13", "CDK12", "CDK2AP2", "CDKN1A", "CDKN1B", "CDKN2B-AS1", "CELF2", "CELSR1", "CENPF", "CFAP161", "CHD1", "CHD3", "CHD6", "CHD7", "CHST14", "CIZ1", "CLDN3", "CLDN7", "CLDN9", "CLIC4", "CLPTM1L", "CLU", "CNN3", "CNOT3", "COL15A1", "COL23A1", "COL5A1", "COL5A3", "COMT", "CPNE3", "CRACD", "CREB3L4", "CREBBP", "CREG1", "CRYAB", "CRYL1", "CSMD1", "CSRP1", "CST1", "CST6", "CTBP2", "CTNNBL1", "CTSB", "CTSD", "CUL3", "CX3CL1", "CXCL12", "CXCL8", "CYP11B2", "CYP17A1", "CYP19A1", "CYP1A1", "CYP1B1", "CYP2A6", "CYP2C18", "CYP2C19", "CYP2E1", "CYP3A4", "CYP3A43", "CYP3A5", "CYP7B1", "DAB2IP", "DAG1", "DCAF6", "DCXR", "DDR1", "DEFB1", "DEGS1", "DENND4B", "DGKK", "DHDH", "DHX30", "DIABLO", "DLGAP2", "DNAH5", "DNAJC10", "DNAJC3", "DNASE1L2", "DNM2", "DNMT1", "DNMT3B", "DOCK2", "DUBR", "EAF2", "EBF2", "EEFSEC", "EFEMP2", "EGF", "EGR1", "EHBP1", "EHF", "EHHADH", "EI24", "EIF2AK2", "EIF3A", "EIF3H", "ELAC2", "EMP1", "EMP3", "EMSY", "ENPP5", "EPCAM", "EPHX1", "EPS8L3", "ERBB3", "ERCC2", "ERF", "ERG", "ERP29", "ERP44", "ESR1", "ESR2", "ETV1", "ETV3", "ETV4", "ETV5", "EWSR1", "EZH2", "EZR", "F13A1", "FAF2", "FAM83F", "FARP2", "FASLG", "FBLN1", "FBLN5", "FBRSL1", "FBXO44", "FERMT2", "FGF10", "FGF2", "FGFR2", "FGFR4", "FHIT", "FOLH1", "FOXA3", "FOXC1", "FOXO1", "FOXP4", "FRY", "FSHR", "GABRG3", "GADD45A", "GALNT3", "GATA2-AS1", "GBAP1", "GCNT1", "GDF15", "GDF7", "GGT1", "GHR", "GJA1", "GLB1", "GNG5", "GNMT", "GOLGA4", "GOLPH3L", "GPX3", "GRB7", "GREB1", "GREM1", "GRHL1", "GRHPR", "GRM7", "GRPR", "GSK3B", "GSR", "GSTA1", "GSTCD", "GSTK1", "GSTM1", "GSTM3", "GSTO1", "GSTP1", "GSTT1", "GUCD1", "H2BC8", "H4C8", "HAO1", "HARS1", "HAUS6", "HBG1", "HBG2", "HDAC6", "HERPUD1", "HIF1A", "HLA-B", "HLA-DOA", "HMGB2", "HMGN5", "HMOX1", "HNF1B", "HNRNPH1", "HOXB13", "HOXB9", "HOXD3", "HPGD", "HPN", "HSBP1P2", "HSD17B1", "HSD17B3", "HSD3B1", "HSD3B2", "HSP90AB1", "HSP90B1", "HSPA1A", "HTR3B", "ICAM1", "IGF1", "IGF2R", "IGFBP3", "IGFBP6", "IGFBP7", "IGSF5", "IK", "IL10", "IL16", "IL17RC", "IL17RD", "IL18", "IL1RN", "IL2", "IL24", "IL27", "IL6", "IL6ST", "INCENP", "INS", "INTS4", "IRAK4", "IRS1", "ITGB3", "ITGB7", "ITGB8", "ITPR1", "ITSN1", "ITSN2", "IVNS1ABP", "JADE2", "JAK1", "JAK2", "JAZF1", "JMJD6", "JUP", "KAT6A", "KCND3", "KCNH1", "KCNN3", "KDELR1", "KDM2A", "KDM3B", "KDM6A", "KEAP1", "KLF15", "KLK15", "KLK2", "KLK3", "KLK5", "KLKP1", "KMT2A", "KMT2C", "KMT2D", "KNL1", "KRAS", "KRT8", "LAMB1", "LAMB2", "LAMC1", "LARP4B", "LCE2B", "LDAH", "LDHB", "LEP", "LIFR", "LINC00299", "LINC00595", "LINC01169", "LINC01496", "LINC02055", "LITAF", "LMTK2", "LPAR1", "LPL", "LRP1B", "LRP2", "LSAMP", "LZTS1", "M6PR", "MACIR", "MAGED1", "MAP3K1", "MAP3K7", "MAPK3", "MARCHF8", "MATN4", "MBD1", "MBD2", "MBNL1", "MBTPS1", "MC2R", "MDM2", "MDM4", "MECOM", "MEIS1-AS3", "MET", "MGA", "MIF", "MIR106A", "MIR21", "MIR4435-2HG", "MLH1", "MLPH", "MME", "MMP13", "MMP14", "MMP9", "MOB2", "MOB3B", "MPO", "MRE11", "MS4A5", "MSH3", "MSMB", "MSR1", "MT2A", "MTAP", "MTHFR", "MTX1", "MUC4", "MUTYH", "MXI1", "MYBBP1A", "MYCL", "MYH14", "MYO6", "MYO9B", "NAALADL2", "NAB2", "NAGK", "NAGLU", "NAT1", "NAT2", "NBN", "NCOA1", "NCOA2", "NCOA3", "NCOA7", "NCOR1", "NDRG1", "NDST2", "NDST4", "NEAT1", "NECTIN2", "NEDD9", "NEK10", "NFE2L2", "NFIC", "NGFR", "NIPAL3", "NKX3-1", "NOL8", "NOS3", "NOX3", "NPPA", "NPR3", "NQO1", "NR3C1", "NRP1", "NUCB2", "NUMBL", "OACYLP", "OGG1", "OLFM1", "ONECUT2", "P4HB", "PAK6", "PAQR4", "PARD3", "PARP1", "PATJ", "PAWR", "PAX6", "PAX9", "PCAT1", "PCAT19", "PCDH11Y", "PCDH8", "PCDHA1", "PCDHA2", "PCDHA3", "PCDHA4", "PCDHA5", "PCDHA6", "PCDHA7", "PCDHGA1", "PCDHGA2", "PDE4D", "PDHA1", "PDIA3", "PDLIM5", "PDP1", "PDS5A", "PDZK1", "PDZK1IP1", "PENK", "PEX14", "PGAM2", "PGRMC1", "PHGDH", "PHLPP2", "PIGP", "PIK3CB", "PIK3CD", "PIK3R1", "PIK3R2", "PITX3", "PKD1L3", "PKN2-AS1", "PKNOX2", "PKP3", "PLAU", "PLAUR", "PLCL1", "PLEK2", "PLPP4", "PML", "PMS1", "PODXL", "POLK", "PON1", "POU2F2", "POU5F1B", "PPARA", "PPFIBP2", "PPL", "PPP2R2A", "PPP3CA", "PRDX2", "PRKACB", "PRKCI", "PRKCZ", "PRKDC", "PRNCR1", "PRNP", "PRRX1", "PRSS8", "PSCA", "PSMC3IP", "PTGFRN", "PTGS2", "PTHLH", "PTPRC", "PTPRK", "PTPRS", "PTRH2", "PVR", "PYHIN1", "RAB4B", "RAD23B", "RAD50", "RAD51", "RAD51B", "RAD51C", "RAD51D", "RAG1", "RALBP1", "RALY", "RASA1", "RASD1", "RASSF3", "RB1", "REC8", "RFK", "RFX6", "RFX7", "RGMB", "RGS17", "RLIM", "RLN2", "RMST", "RN7SKP114", "RNASE4", "RNASEL", "RNF130", "RNF31", "RNF43", "RNLS", "RNU6-148P", "RNU6-456P", "RNU6-491P", "ROBO1", "ROBO2", "RPGR", "RPL10", "RPL11", "RPL12", "RPN2", "RPRD2", "RPS19", "RRAS", "RUNX1", "RXRA", "SAMD9", "SCHLAP1", "SDF2L1", "SELENOP", "SELENOS", "SENP6", "SERINC3", "SERPINA3", "SERPINB10", "SERPINE1", "SERPINF1", "SESN3", "SETD2", "SETDB1", "SF3B1", "SH2B3", "SHBG", "SHROOM2", "SIDT1", "SIL1", "SIRT1", "SIX1", "SLC12A2", "SLC22A3", "SLC26A4", "SLC31A1", "SLC39A1", "SLC4A2", "SLC52A3", "SLC5A5", "SLC7A1", "SLCO2A1", "SMARCA1", "SMARCAD1", "SNED1", "SOD2", "SOX2", "SP5", "SPATA18", "SPATA5", "SPATA6L", "SPEN", "SPINK1", "SPINK5", "SPINT2", "SPOCK1", "SPON2", "SRA1", "SRD5A1", "SRD5A2", "SSBP2", "SSR2", "SSX2", "ST14", "STAB2", "STARD10", "STARD3", "STAT3", "STAT6", "STEAP4", "STMN1", "SUCLG2", "SUGCT", "SULT1A1", "SULT1E1", "SULT2A1", "SULT2B1", "SUN2", "SURF4", "TAF1L", "TANC1", "TAP1", "TBC1D2", "TBL1XR1", "TBX1", "TBX3", "TBXAS1", "TCEAL7", "TCF4", "TCF7L2", "TCN2", "TCP1", "TERC", "TERT", "TET2", "TGFA", "TGFB1", "TGFBR2", "THADA", "THEG5", "THYN1", "TIMP4", "TIRAP", "TJP3", "TLR4", "TLR5", "TLR6", "TMEFF2", "TMF1", "TMOD1", "TMPRSS2", "TMSB4X", "TNFRSF10A", "TNFRSF21", "TNFSF10", "TNRC6B", "TNS3", "TOM1L1", "TOP2A", "TOR1A", "TPBG", "TPD52", "TPD52L1", "TPP1", "TPT1", "TRAF1", "TRIM31", "TRIM8", "TRPM4", "TSHZ1", "TSPOAP1-AS1", "TST", "TTC28", "TTC28-AS1", "TTC7A", "TTC9C", "TUBA1C", "TXNDC5", "TXNRD2", "TYMS", "U2AF1", "UCK2", "UCP3", "UGT2B15", "UGT2B17", "UHRF1BP1", "UMPS", "UNC13D", "USHBP1", "USO1", "USP28", "USP7", "UXT", "VAMP8", "VAV3", "VCP", "VDR", "VEGFA", "VIM", "VIP", "VPS52", "VPS53", "WAC", "WASF3", "WDPCP", "WNT10B", "WNT4", "XAGE3", "XRCC1", "ZBED1", "ZBTB16", "ZBTB20", "ZBTB38", "ZBTB7A", "ZFAND5", "ZFHX3", "ZFP36L2", "ZGPAT", "ZIC2", "ZMIZ1-AS1", "ZMYM3", "ZNF160", "ZNF292", "ZNF385D", "ZNF652");
	////////////////////////////// Till here ////////////////////////////

	// array to string (new line separated) for input as a variable in diamond
	$seed_s = implode("\n", $seed);
	$seed_rand_number = rand(10,10000);
	$seed_file = 'gene_prio/'.$seed_rand_number.'_seed_in.txt'; #seed genes file
	$seed_file_open = fopen($seed_file, "w") or die("Unable to open file!");
	fwrite($seed_file_open, $seed_s);
	fclose($seed_file_open);

	// find intersection of input and seed genes; they are all important.
	$prioritised_genes = array_intersect($gwas_genes, $seed); # print this as one part of result
	sort($prioritised_genes);

	// run gene prioritization for the remaining genes
	$toprioritise = array_diff($gwas_genes, $seed);
	$toprioritise = array_values($toprioritise);

	// count number of genes in the toprioritise; if its greater than 100 then use RWR followed by DIAMOnD otherwise use only DIAMOnD
	$countinp = count($toprioritise);

	// find interaction of the input genes (toprioritise) for network based methods
	$nct = 0;
	foreach($toprioritise as $entry){
		if($nct == 0){
			$geneSa = "Symbol='".$entry."'";
			$geneSb = "Interactant='".$entry."'";
		}else{
			$geneSa = $geneSa." OR Symbol='".$entry."'";
			$geneSb = $geneSb." OR Interactant='".$entry."'";
		}
		$nct++;
	}

	$q3 = "SELECT distinct Symbol, Interactant FROM interactions where ($geneSa) and ($geneSb)";
	$q3rr = mysqli_query($conn, $q3);
	$interactions=0;
	while($row3=mysqli_fetch_array($q3rr)){
		$p1=$row3["Symbol"];
		$p2=$row3["Interactant"];
		$ppi_comma[$interactions]=$p1.",".$p2;
		$ppi_tab[$interactions]=$p1."\t".$p2;
		$interactions++;
	}

	$ppi_cs = implode("\n", $ppi_comma); 					# Comma Separated 
	$rand_number = rand(10,10000);
	$ppi_file_comma = 'gene_prio/'.$rand_number.'_ppi_comma.txt'; #seed genes file
	$ppi_file_open = fopen($ppi_file_comma, "w") or die("Unable to open file!");
	fwrite($ppi_file_open, $ppi_cs);
	fclose($ppi_file_open);

	$ppi_ts = implode("\n", $ppi_tab); 					# Tab Separated
	$ppi_file_tab = 'gene_prio/'.$rand_number.'_ppi_tab.txt'; #seed genes file
	$ppi_file_open = fopen($ppi_file_tab, "w") or die("Unable to open file!");
	fwrite($ppi_file_open, $ppi_ts);
	fclose($ppi_file_open);

	if ($countinp > 100){
		$randomWalk = exec("python3 gene_prio/randomwalk_ik.py $ppi_file_tab $seed_file");
	} else {
		$diamond_out = "gene_prio/".$rand_number."_diamond_out".".txt";
		$dmndOut = exec("python3 gene_prio/DIAMOnD_edited.py gene_prio/ppi_edgelist.txt gene_prio/seed_prostate.txt 9 $diamond_out");
	}

    $fh = fopen($diamond_out, 'r');
    while (($line = fgetcsv($fh, 10000, "\t")) !== false) {
        $result[] = $line;
    }
    $rsHead = array_shift($result);
    foreach($result as $key){
        $top_prio[] = $key[1];
    }

	// find pathways of seed genes
	foreach ($seed as $ss) {
		$q4="SELECT distinct pathwayname FROM kegg_path WHERE geneSymbol = '$ss' UNION SELECT DISTINCT pathwayname FROM reactome WHERE geneSymbol = '$ss' UNION SELECT DISTINCT GO_term FROM gene2go WHERE GeneSymbol = '$ss'";
		$q4rr=mysqli_query($conn, $q4);
		$path_s=0;
		while($row4=mysqli_fetch_array($q4rr)) {
			$path1=$row4["pathwayname"];
			$pathway_seed[$path_s]=$path1;
			$path_s++;
		}
	}
	
	// find pathways of toprioritise genes one by one; find pvalue of each gene and rank based on their pvalues
	$genP_data = array();
	foreach ($top_prio as $pp) {
		$pathway_gene = array();

		$q5 = "SELECT distinct pathwayname FROM kegg_path where geneSymbol = '$pp' UNION SELECT DISTINCT pathwayname from reactome where geneSymbol='$pp'union select DISTINCT GO_term from gene2go where GeneSymbol='$pp'";	
		$q5rr=mysqli_query($conn, $q5);

		if(mysqli_num_rows($q5rr) > 0) {
			while($row5=mysqli_fetch_array($q5rr)) {
				$path2 = $row5["pathwayname"];
				array_push($pathway_gene, $path2);
			}
		}

		if(!empty($pathway_gene)){
			// find intesection of pathways 
			$pathway_intersect=array_intersect($pathway_seed, $pathway_gene);
			$countseed = count($seed);
			$totalgenes = $countinp+$countseed;
			$countseedpath = count($pathway_gene);
			$countintersectionpath = count($pathway_intersect);

			$pval = shell_exec("python3 gene_prio/hypergeometric_python.py $totalgenes $countseed $countseedpath $countintersectionpath 2>&1");
			$genP_data['gene'][] = $pp;
			$genP_data['pval'][] = +str_replace(' ', '', $pval);
			$genP_data['rank'][] = (int)$countintersectionpath;
			$pathway_gene=array();
		}
	}

	// echo "<pre>";
	// print_r($rsHead);
	// echo "</pre>";

	// Removing all files which is been created by RandomWalk and Diamond
	unlink($seed_file);
	unlink($ppi_file_comma);
	unlink($ppi_file_tab);
	unlink($diamond_out);
	include_once "header.php";
?>

<style>
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

    /* .dataTables_length{
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
    } */
</style>

<div>
    <h2 class='mHead' align="center">Gene prioritization</h2>
	<table class="mb-3">
		<tr>
			<td width="20%"><strong>Prioritised genes: </strong></td>
			<td width="80%"><?= implode(', ', $prioritised_genes); ?></td>
		</tr>
	</table>
	<table border="1" cellspacing="0" cellpadding="4" style="margin: 0 auto;" class="genPrioTble">
		<thead>
			<tr>
				<td><strong>Genes</strong></td>
				<td><strong>P-value</strong></td>
				<td><strong>Rank</strong></td>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($result as $rowk){
					?>
						<tr>
							<td><?= $rowk[1]; ?></td>
							<td><?= $rowk[2]; ?></td>
							<td><?= $rowk[0]; ?></td>
						</tr>
					<?php 
				}
			?>
		</tbody>
	</table>
	<p></p>

    <?php
        if(!empty($genP_data)){
            ?>
                <table border="1" cellspacing="0" cellpadding="4" style="margin: 0 auto;" class="genPrioTble">
                    <thead>
                        <tr>
                            <td><strong>Gene</strong></td>
                            <td><strong>P-value</strong></td>
                            <td><strong>Rank</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($i=0; $i<sizeof($genP_data['gene']); $i++){
								if ($genP_data['rank'][$i] != 0){
									?>
										<tr>
											<td>
												<?php
													$genSym = $genP_data['gene'][$i];
													$qu = mysqli_query($conn, "SELECT GeneID FROM entrez_id WHERE Symbol = '$genSym'");
													while($rowG = mysqli_fetch_array($qu)){
														$GenID = $rowG["GeneID"];
														if($GenID != ''){
															echo "<a href=https://www.ncbi.nlm.nih.gov/gene/$GenID target=_blank>$genSym</a>";
														}else{
															echo $genSym;
														}
													}
												?>
											</td>
											<td><?= $genP_data['pval'][$i]; ?></td>
											<td><?= $genP_data['rank'][$i]; ?></td>
										</tr>
									<?php
								}
                            }
                        ?>
                    </tbody>
                </table>
                <p>&nbsp;</p>
            <?php
        } else {
            echo "<h6 style='text-align: center;'>No enriched pathway has been found for the selected disease</h6><p></p>";
        }
    ?>
    <div align="center">
        <input type="submit" id="button2" onclick="location.href='gwas.php';" value="Back to main page" />
    </div>
    <p>&nbsp;</p>
</div>

<script>
	$('.genPrioTble').DataTable({
        dom: 'lBfrtip',
        "pageLength": 10,
        buttons: [{
            extend: 'csv',
            title: 'Gene prioritization',
            text: 'Downlaod'
        }],
		order: [[2, 'asc']]
    });
</script>

<?php
	include_once "footer.php";
?>