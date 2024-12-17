<?php // charset:UTF-8
class cmsType_table_base extends cmsType_contentData_show_base {

    function getName (){
        return "Tabellen";
    }

     function table_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $viewMode = $data[viewMode];
        $table = $_GET[table];
        if ($table) {
            $viewMode = "single";
        }
        
        $rowCount = $data[rowCount];
        $columnCount = $data[columnCount];
        
        $this->tableBox_show($contentData,$frameWidth);
        return 0;
        
        switch ($viewMode) {
            case "sss" : break;
            default :
                echo ("UNKOWN VIEWMODE IN table_show ".$data[viewMode]."<br>");                            
        }
    }
    




    function table_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();

        $mainTab = "table";
        
        $add = array();
        $colCount = $data[columnCount];
        if (!$colCount) $colCount = 3;
        $add[text] = "Spalten Anzahl";
        $add[input] = "<input type='text' value='$colCount' style='width:50px;' name='editContent[data][columnCount]' />";
        $res[$mainTab][] = $add;

        $add = array();
        $colHead = $data[columnHead];
        if ($colHead) $checked="checked='checked'"; 
        else $checked = "";
        $add[text] = "Tabellen Kopf";
        $add[input] = "<input type='checkbox' value='1' $checked name='editContent[data][columnHead]' />";
        $res[$mainTab][] = $add;
        
       
        $add = array();
        $rowCount = $data[rowCount]; 
        if (!$rowCount) $rowCount = 5;
        $add[text] = "Zeilen Anzahl";
        $add[input] = "<input type='text' value='$rowCount' style='width:30px;' name='editContent[data][rowCount]' />";
        $res[$mainTab][] = $add;
        
        $add = array();
        $rowHead = $data[rowHead];
        if ($rowHead) $checked="checked='checked'"; 
        else $checked = "";
        $add[text] = "Zeilen Name";
        $add[input] = "<input type='checkbox' value='1' $checked name='editContent[data][rowHead]' />";
        $res[$mainTab][] = $add;
        
        
        
        $addToTab = "Spalten";
        $res[$addToTab] = array();
        $columnData = $data[columnData];
        if (is_string($columnData)) $columnData = str2Array ($columnData);
        if (!is_array($columnData)) $columnData = array();
        
        $startNr = 1;
        if ($rowHead) {
            $startNr = 0;           
        }
        for ($i=$startNr;$i<=$colCount;$i++) {
            $add = array();
            $colKey = "column_".$i;
            $colData = $columnData[$colKey];
            if (!is_array($colData)) $colData = array();
            
            if ($i == 0) {
                 $add[text] = "Breite Zeilen-Titel:";
            } else {
                $add[text] = "Spalte 1";
            }
            $input = "Breite: <input type='text' value='$colData[width]' style='width:50px;' name='editContent[data][columnData][$colKey][width]' />";
            if ($colHead) {
                $input .= "Spalten-Titel: <input type='text' stye='width:200px;' value='$colData[title]' name='editContent[data][columnData][$colKey][title]' />";
            }
            
            $input .= " Spalten-Inhalt: ".$this->tableBox_edit_selectContentType($colData[showType],"editContent[data][columnData][$colKey][showType]",$showData);
            
            $add[input] = $input;
            $res[$addToTab][] = $add;
        }
        
        
        $addToTab = "Zeilen";
        $res[$addToTab] = array();
        $rowData = $data[rowData];
        if (is_string($rowData)) $rowData = str2Array($rowData);
        if (!is_array($rowData)) $rowData = array();
        
        $startNr = 1;
        if ($colHead) {
            $startNr = 0;           
        }
        for ($i=$startNr;$i<=$rowCount;$i++) {
            $add = array();
            $rowKey = "row_".$i;
            $rData = $rowData[$rowKey];
            if (!is_array($colData)) $colData = array();
            
            if ($i == 0) {
                 $add[text] = "Höhe Kopfzeile:";
            } else {
                $add[text] = "Zeile $i";
            }
            $input = "Höhe: <input type='text' value='$rData[height]' style='width:50px;' name='editContent[data][rowData][$rowKey][height]' />";
            if ($rowHead) {
                $input .= "Zeilen-Titel: <input type='text' stye='width:200px;' value='$rData[title]' name='editContent[data][rowData][$rowKey][title]' />";
            }
            // CONTENTTYPE FOR ROW
            $input .= " Zeilen-Inhalt: ".$this->tableBox_edit_selectContentType($rData[showType],"editContent[data][rowData][$rowKey][showType]",$showData);
            
            $add[input] = $input;
            
            
            
            
            
            
            $res[$addToTab][] = $add;
        }
        
        
        $addToTab = "Inhalt";
        $add = array();
        $add[text] = "Inhalt";
        $input  = "<table class='cmsTable' style='width:".($frameWidth-20)."px;table-layout:auto;' >";

        if ($colHead) {
            $lineClass = "cmsTableLine";
            $lineClass .= " cmsTableLine_first";
            $lineClass .= " cmsTableLine_head";
            $input .= "<tr class='$lineClass'>";

            if ($rowHead) {
                $columnClass = "cmsTableColumn cmsTableColumn_head cmsTableColumn_first cmsTableColumn_head_corner";

                $input .= "<td class='$columnClass'>";
                $input .= "OBEN ____ ECKE";
                $input .= "</td>";
            }

            for ($c=1;$c<=$colCount;$c++) {
                $cont = $data[columnData]["column_$c"][title];
                if (!$cont) $cont = "HEAD $c";

                $columnClass = "cmsTableColumn cmsTableColumn_head";
                if ($c == $colCount) $columnClass .= " cmsTableColumn_last";
                
                $input .= "<td class='$columnClass'>";
                $input .= $cont;
                $input .= "</td>";
            }
            $input .= "</tr>";
        }

        for ($r=1;$r<=$rowCount;$r++) {
            $lineClass = "cmsTableLine";
            if ($i==1 AND !$rowHead) $lineClass .= " cmsTableLine_first";
            $input .= "<tr class='$lineClass'>";

            if ($rowHead) {
                $columnClass = "cmsTableColumn cmsTableColumn_first cmsTableColumn_rowTitle";
                $input .= "<td class='$columnClass'>";
                $cont = $data[rowData]["row_$r"][title];
                if (!$cont) $cont = "Zeile $r";
                $input .= $cont;
                $input .= "</td>";
            }


            for ($c=1;$c<=$colCount;$c++) {

                $columnClass = "cmsTableColumn";
                if ($c==1 AND !$rowHead) $columnClass .= " cmsTableColumn_first";
                $input .= "<td class='$columnClass' style='padding:2px;' >";
                $cont = $data[content][$r][$c];
                $columnKey = "column_".$c;
                $rowKey = "row_".$r;
                $typeCol = $data[columnData][$columnKey][showType];
                $typeRow = $data[rowData][$rowKey][showType];
                $typeInput = "";
                if ($typeCol) {
                    $typeInput = $this->tableBox_editContent($typeCol,$cont,"editContent[data][content][".$r."][".$c."]");
                }
                if ($typeRow) {
                    $typeInput = $this->tableBox_editContent($typeRow,$cont,"editContent[data][content][".$r."][".$c."]");                    
                }
                if ($typeInput) {
                    $input .= $typeInput; 
                } else {
                    $input .= "$typeCol / $typeRow <br>";
                    $input .= "<input type='text' style='width:100%' value='$cont' name='editContent[data][content][".$r."][".$c."]' />";
                }
                $input .= "</td>";

            }
            $input .= "</tr>";
        }
        $input .= "</table>";

        $add[input] = $input;
        $res [$addToTab][] = $add;


        return $res;

       
    }

    function tableBox_showContent_own($showType,$content,$colWidth,$colHeight) {
        switch ($showType) {
            case "currency" :
                $res = $this->tableBox_showContent_currency($showType,$content,$colWidth,$colHeight);
                break;
            default :
                $res = "unkown Showtype $showType<br/>".$content;

        }

        return $res;
    }
    

    function tabeBox_edit_contentTypes_own() {
        $res = array();
        $res[currency] = array("name"=>"Währung","deci"=>2,"deli"=>",","deli1000"=>".","currency"=>"€");
        $res[basket] = 0;
        return $res;
    }


     function tableBox_editContent_own($showType,$content,$dataName,$showData) {
        $showTypes = $this->tableBox_edit_contentTypes();
        $res = "";
        switch ($showType) {
            case "currency" :
                $data = $showTypes[$showType];


                $res = "<input type='text' value='$content' name='$dataName' />";
                break;
            default :
                 $res = "unkown Showtype $showType in editContent tableBox<br/>".$content;

        }

        return $res;
    }
    
    
    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for tableListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["slider"] = "Slider";
        $res["single"] = "Hersteller";
            
        return $res;
    }
    
     function dataShow_List() {
        return $this->tableShow_List();
    }
    
    function tableShow_List() {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[info] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[longInfo] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        
//        $show[vk] = array("name"=>"Verkauspreis","description"=>"Bezeichnung zeigen","position"=>1);
//        $show[shipping] = array("name"=>"Porto","description"=>"Bezeichnung zeigen","position"=>1);
//        $show[count] = array("name"=>"Anzahl","description"=>"Bezeichnung zeigen","position"=>1);
//        
//        $show[basket] = array("name"=>"Warenkorb","description"=>"Bezeichnung zeigen","position"=>1);
        $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
        return $show;
    }


}

function cmsType_table_class() {
    if ($GLOBALS[cmsTypes]["cmsType_table.php"] == "own") $tableClass = new cmsType_table();
    else $tableClass = new cmsType_table_base();
    return $tableClass;
}

function cmsType_table($contentData,$frameWidth) {
    
    $tableClass = cmsType_table_class();
    $tableClass->table_show($contentData,$frameWidth);
}



function cmsType_table_editContent($editContent,$frameWidth) {
    $tableClass = cmsType_table_class();
    return $tableClass->table_editContent($editContent,$frameWidth);
}


?>
