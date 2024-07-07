<script src="js/jquery_3.2.1.js"></script>
<script src="js/bootstrap.js"></script>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="miArraystyle.css">

<?php
    include('header.php');
?>

<table width="98%" align="center" id="mnTbl">
    <p class="OnlyForSpace">&nbsp;</p>
    <tr class="frstTR">
        <td id="uploadF">
            <p align="justify">&nbsp;</p>
            <!-- <h4 align="center">Microarray Data Analysis</h4> -->
            <div class="mb-4">
                <h6 align="center"><strong>Microarray Data Analysis </strong></h6>
            </div>
            <div class="upload_filter" id="upload_filter">
                <form action="miArrayResult.php" enctype="multipart/form-data" id="myform" method="post">
                    <table width="98%">
                        <tr>
                            <td width="10%"></td>
                            <td width="60%">
                                <div class="row upload_cel_file" id="celDiv">
                                    <div class="col-12 pr-3 d-flex justify-content-between">
                                        <label><strong>Select type of file: </strong></label>
                                        <div class="selOption" style="margin-right: 4%;">
                                            <select class="btn selBut" id="selButID"
                                                style="font-size: 13px; padding-left: 5px;">
                                                <option value="" disabled="disabled" selected="selected">
                                                    Select type of file</option>
                                                <option value="text">Text File</option>
                                                <option value="cel">CEL File</option>
                                                <option value="degList">DEG list</option>
                                                <option value="rnk">RNK file</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!---------------------------------------------------------- CEL File ----------------------------------------------------->
                                    <div id="celAnal">
                                        <div class="row col-12 mainUpldDIV" id="mainUpldDIV">
                                            <div class="row col-12 upldANDdrag" id="duplicetor0"
                                                style="padding-right: 0;">
                                                <div class="col-12 tleAnl" style="padding-right: 0;">
                                                    <div class="leftTle">
                                                        <label><strong>Title of study:</strong></label>
                                                        <input type="text" name="titleAlyName0" id="titleAlyID0"
                                                            autocomplete="off" placeholder="" value="">
                                                    </div>
                                                </div>
                                                <div class="col-12 upldClss" style="padding-right: 0;">
                                                    <div class="flexDiv">
                                                        <div>
                                                            <div class="leftGrp">
                                                                <label><strong>Name for group 1:
                                                                    </strong></label>
                                                                <input type="text" name="frstGrpName0" id="frstGrpID0"
                                                                    autocomplete="off" placeholder="" value="">
                                                            </div>
                                                            <ul class="list-group-item connectedList" id="showleftGrp0"
                                                                style="display:none;">
                                                            </ul>
                                                            <input id="leftGrp0" class="clkFle" type="file"
                                                                name="files0[]" accept=".CEL, .gz" multiple />
                                                        </div>
                                                        <div>
                                                            <p>&nbsp;</p>
                                                        </div>
                                                        <div class="rightGrpS">
                                                            <div class="rightGrp">
                                                                <label><strong>Name for group 2:
                                                                    </strong></label>
                                                                <input type="text" name="SecGrpName0" id="SecGrpID0"
                                                                    autocomplete="off" placeholder="" value="">
                                                            </div>
                                                            <ul class="list-group-item connectedList" id="showRightGrp0"
                                                                style="display:none;">
                                                            </ul>
                                                            <input id="rightGrp0" class="clkFle" type="file"
                                                                name="files0[]" accept=".CEL, .gz" multiple />
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="control0" id="controlbox0" value="" />
                                                <input type="hidden" name="treatment0" id="treatmentbox0" value="" />
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <p>&nbsp;</p>
                                        </div>

                                        <div class="col-12">
                                            <div class="row col-12">
                                                <label class="mb-3"><strong>Parameters </strong></label>
                                                <div class="col-12" style="padding: 0;">
                                                    <label>Enter P-Value cut-off:</label>
                                                    <input type="text" name="tPvalue_CEL" claas="tPvalue" placeholder=""
                                                        value="0.05" required />
                                                </div>
                                                <div class="col-12" style="padding: 0;">
                                                    <label>Enter Log2 fold change cut-off for
                                                        upregulated
                                                        genes:</label>
                                                    <input type="text" name="tLogfcu_CEL" claas="tLogfcu" placeholder=""
                                                        value="1" required />
                                                </div>
                                                <div class="col-12" style="padding: 0;">
                                                    <label>Enter Log2 fold change cut-off for
                                                        downregulated
                                                        genes:</label>
                                                    <input type="text" name="tLogfcd_CEL" claas="tLogfcd" placeholder=""
                                                        value="-1" required />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload button-->
                                        <div class="col-12 allBtn">
                                            <a class="btn btn-sm mt-3 duplicBtn" id="dupliBut">Add Study</a>
                                            <a class="btn btn-sm mt-3 removeBnt" id="removBut">Remove Study</a>
                                            <button type="submit" name="cel_Submit" class="btn btn-sm mt-3 submitBnt"
                                                id="cel_Submit">Analyze</button>
                                        </div>
                                        <input type="hidden" name="cel_AnalCount" id="cel_AnalCountBox" value="">
                                    </div>

                                    <!------------------------------------------------------ For DEG List -------------------------------------------->
                                    <div id="degList">
                                        <div class="row col-12 mainUpldDIV" id="degLstMainDiv">
                                            <div class="row col-12 upldANDdrag" id="degLstDuplicetor0"
                                                style="padding-right: 0;">
                                                <div class="col-12 tleAnl" style="padding-right: 0;">
                                                    <div class="leftTle">
                                                        <label><strong>Title of study:</strong></label>
                                                        <input type="text" name="degListName0" id="degListTitleID0"
                                                            autocomplete="off" placeholder="" value="">
                                                    </div>
                                                </div>
                                                <div class="col-12 upldClss" style="padding-right: 0;">
                                                    <div class="flexDiv">
                                                        <input id="degLstUpldID0" class="clkFle" type="file"
                                                            name="degList0" accept=".txt" multiple />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <p>&nbsp;</p>
                                        </div>

                                        <div class="col-12">
                                            <div class="row col-12">
                                                <label class="mb-3"><strong>Parameters </strong></label>
                                                <div class="col-12" style="padding: 0;">
                                                    <label>Enter P-Value cut-off:</label>
                                                    <input type="text" name="tPvalue_deg" claas="tPvalue" placeholder=""
                                                        value="0.05" required />
                                                </div>
                                                <div class="col-12" style="padding: 0;">
                                                    <label>Enter Log2 fold change cut-off for
                                                        upregulated
                                                        genes:</label>
                                                    <input type="text" name="tLogfcu_deg" claas="tLogfcu" placeholder=""
                                                        value="1" required />
                                                </div>
                                                <div class="col-12" style="padding: 0;">
                                                    <label>Enter Log2 fold change cut-off for
                                                        downregulated
                                                        genes:</label>
                                                    <input type="text" name="tLogfcd_deg" claas="tLogfcd" placeholder=""
                                                        value="-1" required />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload button -->
                                        <div class="col-12 allBtn">
                                            <a class="btn btn-sm mt-3 duplicBtn" id="dupliDEGlist">Add Study</a>
                                            <a class="btn btn-sm mt-3 removeBnt" id="removDEGlist">Remove Study</a>
                                            <button type="submit" name="degList_Submit"
                                                class="btn btn-sm mt-3 submitBnt" id="degListSubmit">Analyze</button>
                                        </div>
                                        <input type="hidden" name="degList_AnalCount" id="degListAnalCount" value="">
                                    </div>

                                    <!---------------------------- .rnk file which will do only GSEA and its relative content ----------------------->
                                    <div class="col-12" id="rnkFile">
                                        <div class="mainUpldDIV" id="rnkMainDiv">
                                            <div class="upldANDdrag" id="rnkDuplicetor0">
                                                <div class="tleAnl" style="padding-right: 0; margin-right: 31px;">
                                                    <div class="leftTle">
                                                        <label><strong>Title of study:</strong></label>
                                                        <input type="text" name="rnkName0" id="rnkTitleID0"
                                                            autocomplete="off" placeholder="" value="">
                                                    </div>
                                                </div>
                                                <div class="upldClss" style="padding-right: 0;">
                                                    <div class="flexDiv">
                                                        <input id="rnkUpldID0" class="clkFle" type="file" name="rnkFle0"
                                                            accept=".rnk" multiple />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-12">
                                                            <p>&nbsp;</p>
                                                        </div> -->

                                        <!-- <div class="col-12">
                                                            <div class="row col-12">
                                                                <label class="mb-3"><strong>Parameters </strong></label>
                                                                <div class="col-12" style="padding: 0;">
                                                                    <label>Enter P-Value cut-off:</label>
                                                                    <input type="text" name="tPvalue" id="tPvalue"
                                                                        placeholder="" value="0.05" required />
                                                                </div>
                                                                <div class="col-12" style="padding: 0;">
                                                                    <label>Enter Log2 fold change cut-off for
                                                                        upregulated
                                                                        genes:</label>
                                                                    <input type="text" name="tLogfcu" id="tLogfcu"
                                                                        placeholder="" value="1" required />
                                                                </div>
                                                                <div class="col-12" style="padding: 0;">
                                                                    <label>Enter Log2 fold change cut-off for
                                                                        downregulated
                                                                        genes:</label>
                                                                    <input type="text" name="tLogfcd" id="tLogfcd"
                                                                        placeholder="" value="-1" required />
                                                                </div>
                                                            </div>
                                                        </div> -->

                                        <!-- Upload button -->
                                        <div class="col-12 allBtn">
                                            <a class="btn btn-sm mt-3 duplicBtn" id="dupliRnk">Add Study</a>
                                            <a class="btn btn-sm mt-3 removeBnt" id="removRnk">Remove Study</a>
                                            <button type="submit" name="rnk_Submit" class="btn btn-sm mt-3 submitBnt"
                                                id="rnkSubmit">Analyze</button>
                                        </div>
                                        <input type="hidden" name="rnk_AnalCount" id="rnkAnalCount" value="">
                                    </div>
                                </div>
                            </td>
                            <td width="10%"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <p align="justify">&nbsp;</p>
            <p align="justify">&nbsp;</p>
        </td>
    </tr>
</table>

<script src="miArrayScript.js"></script>
<script>
    if ($('#selButID option[value="cel"]').attr("selected", true)) {
        $('#degList').hide();
        $('#rnkFile').hide();
    }
    $('#selButID').on('change', function () {
        $("#celAnal").toggle(this.value == 'cel');
        $("#degList").toggle(this.value == 'degList');
        $("#rnkFile").toggle(this.value == 'rnk');
    });
</script>

<?php
    include('footer.php');
?>