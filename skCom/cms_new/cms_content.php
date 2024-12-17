<?php // charset:UTF-8
function cms_content_insert($addContent=null) {
    if (!is_array($addContent)) return "noAddContent";

    $query = "";

    //echo ("cms_content_insert($pageId,$addContent)<br>");
    foreach ($addContent as $key => $value) {
        if ($key == "pageId") $pageId = $value;

        if (strlen($query)) $query .= ", ";
        $query .= "`$key`='$value'";

        // echo "add $key = $value <br>";
    }

    if (!$pageId) return "noPageId";

    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_content` SET $query ";
   
    $result = mysql_query($query);
    if ($result) {
        $insertId = mysql_insert_id();
        return $insertId;
    }
    echo ("Echo error in query '$query' <br>");
    return 0;
}

function cms_content_delete($contentId,$deleteText=0) {
    // echo ("<h1>Delete Content $contentId $deleteText </h1>");
    
    $contentData = cms_content_get(array("id"=>$contentId));
    if (!is_array($contentData)) return 0;
    $contentType = $contentData[type];
    
    if (substr($contentType,0,5) == "frame") {
        $frameAnz = substr($contentType,5);
        for ($f=1;$f<=$frameAnz;$f++) {

            $frameContentStr = "frame_".$contentId."_".$f;
            // echo ("DELETE CONTENT FOR $contentId Frame $f -> '$frameContentStr'<br>");
            // cms_content_delete($frameContentStr, $deleteText);
            $frameContentList =  cms_content_getList($frameContentStr);
            if (is_array($frameContentList) AND count($frameContentList)) {
                for ($fc=0;$fc<count($frameContentList);$fc++) {
                    $frameContent = $frameContentList[$fc];
                    $frameContentId = $frameContent[id];
                   //  $frameContentType = $frameContent[type];
//                    // echo ("Find Content for frame <b>$frameContentStr</b> $frameContentId $frameContentType <br>");
                    $res = cms_content_delete($frameContentId,1);
                }
            }
        }
    }
    
    // show_array($contentData);
    
    if ($deleteText) {
        $textId = "text_".$contentId;
        cms_text_deleteContent($textId);        
    }
    
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_content` WHERE `id` = $contentId";
    $result = mysql_query($query);
    if ($result) return 1;

    echo ("Error in $query <br>");
    return 0;
}


function cms_content_getList($pageId,$sort=null) {
    if (is_integer($pageId)) {
        // echo ("Page Id is Integre $pageId");
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_content` WHERE `id` = '$pageId' ORDER BY `sort` ASC";
    } else {

    // echo ("Get Content for $pageId <br>");
  
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_content` WHERE `pageId` = '$pageId' ORDER BY `sort` ASC";
    }
    $result = mysql_query($query);

    if (!$result) {
        echo ("error in Query '$query'<br>");
        return $contentList;
    }
    
    // echo("$query <br>");
    $contentList = array();
    while ($contentData = mysql_fetch_assoc($result)) {

        if (is_string($contentData[data])) $contentData[data] = str2Array($contentData[data]);
        if (!is_array($contentData[data])) $contentData[data] = array();


        if (is_string($contentData[wireframe]) AND $contentData[wireframe]) $contentData[wireframe] = str2Array($contentData[wireframe]);
        if (!is_array($contentData[wireframe])) $contentData[wireframe] = array();
        // echo ("Found !!!<br>");
        $contentList[] = $contentData;
    }
    return $contentList;

}

function cms_content_getAllList($pageId,$sort=null) {
    $myScroll = array();
    // echo ("<h1>GET CONTENT FOR $pageId </h1>");
    $list = cms_content_getList($pageId,$sort); // array("pageId"=>$pageName,$sort));
    
    if (!is_array($list)) return $myScroll;
    
    for ($i=0;$i<count($list);$i++) {
        $contData = $list[$i];
        $contId = $contData[id];
        $contType = $contData[type];
        $contTitle = $contData[title];
        $contCode  = $contData[pageId];

        $id = "inh_".$contId;
        $myScroll[$id] = array("id"=>$contId,"type"=>$contType,"title"=>$contTitle,"contCode"=>$contCode);

        if (substr($contType,0,5) == "frame") {
            $frameNr = substr($contType,5);
            for ($f=1;$f<=$frameNr;$f++) {
                $frameName = "frame_".$contId."_".$f;
                $frameList = cms_content_getAllList($frameName,$sort);
                foreach ($frameList as $frameId => $frameContData) {
                    if (!$frameContData["mainId"]) $frameContData["mainId"] = $id;
                    if (!$frameContData["mainType"]) $frameContData["mainType"] = "frame";
                    $myScroll[$frameId] = $frameContData;
                }
            }              
        }

        if ($contType == "flip") {
            $anz = $contData[data][layerCount];
            // echo ("Fount Content width $anz Layer <br>");
            for ($f=1;$f<$anz+1;$f++) {
                $layerCode = "layer_".$contId."_".$f;
                // echo ("Get Content for $layerCode <br>");
                $layerList = cms_content_getAllList($layerCode,$sort);
                foreach ($layerList as $layerId => $layerData) {
                    if (!$layerData["mainId"])   $layerData["mainId"] = $id;
                    if (!$layerData["mainType"]) $layerData["mainType"] = "flip";
                    // echo ("ADD $layerId Id to scrollList is=".$layerData["mainId"]." typ = '".$layerData["mainType"]."' <br>");
                    $myScroll[$layerId] = $layerData;
                }
            }              
        }

    }    
    return ($myScroll);
}

function cms_content_getId($contentId) {
    // echo ("Get Content for $contentId <br>");
    $contentList = array();
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_content` WHERE `id` = $contentId ";
    // echo ($query."<br>");
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz==0) return "notFound";
    if ($anz>1) return "moreFound";

    $contentData = mysql_fetch_assoc($result);
    if (is_string($contentData[data])) $contentData[data] = str2Array($contentData[data]);
    if(!is_array($contentData[data])) $contentData[data] = array();

    if (is_string($data[wireframe]) AND $data[wireframe]) $data[wireframe] = str2Array($data[wireframe]);
    if (!is_array($data[wireframe])) $data[wireframe] = array();

    return $contentData;
}


function cms_content_get($getData) {
    $query = "";
    foreach($getData as $key => $value) {
        if ($value) {
            if ($query) $query .= ", ";
            $query .= "`$key` = '$value' ";
        }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_content` WHERE $query";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        echo ("Not found Data for Page<br>");
        return 0;
    }
    if ($anz > 1) {
        echo ("More found <br>");
        return 0;
    }   
    $data = mysql_fetch_assoc($result);
    if (is_string($data[data]) AND $data[data]) $data[data] = str2Array($data[data]);
    if (!is_array($data[data])) $data[data] = array();

    if (is_string($data[wireframe]) AND $data[wireframe]) $data[wireframe] = str2Array($data[wireframe]);
    if (!is_array($data[wireframe])) $data[wireframe] = array();

    return $data;
}

function cms_content_changeSort($contentId,$newSort) {
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_content` SET `sort`=$newSort WHERE `id` = $contentId";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query cms_content_change Sort ($query)<br>");
        return 0;
    }
    return 1;
}

function cms_content_update($newData,$compareData) {
    $query = "";
    $diff = 0;
    $contentId = $newData[id];
    
    
    if (!$contentId) {
        $contentId = $compareData[id];
        if (!$contentId) {
            echo ("<h1>No ContentId $contentId </h1>");
            show_array($newData);
            show_array($compareData);
        }
    }
    
    //show_array($compareData);
    
    foreach ($newData as $key => $value) {
        // 
//        echo ("Save $key = $value <br>");
//        if (is_array($value)) {
//            foreach ($value as $k => $v) {
//                echo (" --> $k = $v <br />");
//            }
//        }
        if (is_array($value)) $value = array2Str ($value);
        $add = 1;
        if (is_array($compareData)) {
            if (is_array($compareData[$key])) $compareData[$key] = array2Str($compareData[$key]);
            
            if ($compareData[$key] == $value) {
                // echo ("Save $key is same $compareData[$key] == $value <br>");
                $add = 0;
            } else {
                // echo ("Diffrent for key =$key => old ='$compareData[$key]' new = '$value' <br>");
            }
            if ($add == 0 AND $key == "type" AND $compareData[oldType]) {
                // echo "Doch Speichern da Oldtyp<br>";
                $add = 1;
            }
        }
        
        if ($add AND $key!="id") {
            $diff++;
            if ($query!="") $query .= ", ";
            $query .= "`$key`='$value'";
        }
    }
    
    if ($diff AND $contentId) {
       
        $query = "UPDATE `".$GLOBALS[cmsName]."_cms_content` SET $query WHERE `id` = '$contentId' ";
        // echo ($query."<br>");
        $result = mysql_query($query);
        if ($result) return 1;
        echo ("Error in Query '$query' <br />");
        return 0;
    
    } else {
        // echo "No Change for $contentId<br>";
        return "noChange";
    }
    
}


function cms_content_save($id,$saveData,$compareData=null) {
    if (!is_array($saveData)) {
        cms_errorBox("Keine Daten erhalten in cms_content_save");
        return 0;
    }
    
    if ($id) {
        if (!is_array($compareData)) {
            echo ("<h1>Load CompareData $compareData</h1>");
            $compareData = cms_content_get(array("id"=>$id));
        }
        if (is_array($compareData)) {
            $res = cms_content_update($saveData,$compareData);
            if ($res == "noChange") $res = 1;
            // echo ("Update Result = $res <br>");
            return $res;
        }
        
            
    }
   //  show_array($saveData);
    

    $query = "";
    foreach ($saveData as $key => $value ) {
        if ($query != "") $query .= ", ";
        if (is_array($value)) $value = array2Str ($value);
        
        $query .= "`$key`='$value'";
    }

    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_content` SET ".$query;
    $result = mysql_query($query);
    // $result = 0;
    if ($result) {
        $insertId = mysql_insert_id();
        return $insertId;
    }

  //  cms_errorBox("Error in cms_content_save Query <br>$query ");
    return 0;
    

}


function cmsContentName_getList($filter,$sort) {
    $res = cms_content_contentNameList($filter,$sort);
    return $res;
}

function cms_content_contentNameList(){
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_content` WHERE `contentName` != '' ORDER by `contentName`";
    $result = mysql_query($query);
    $contentNameList = array();
    if ($result) {
        // echo ("Found!!!!!!!!!<br>");
        while ($contentData = mysql_fetch_assoc($result)) {
            $contentName = $contentData[contentName];
            $contentId = $contentData[id];
            $contentNameList[$contentId] = $contentName;
            // echo ("find $contentId - $contentName <br>");
        }
    } else {
        echo ("error in Query $query <br>");
    }
    asort($contentNameList);
    return $contentNameList;

}


function cms_content_Select_contentName($contentId,$dataName) {
    $contentNameList = cms_content_contentNameList();

    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectContentName' value='$contentId' >";

    // empty Select
    $str.= "<option value='0'";
    if (!$contentId)  $str.= " selected='1' ";
    $str.= ">Bitte wählen</option>";


    foreach ($contentNameList as $listId => $contentName) {
        $str.= "<option value='$listId'";
        if ($listId == $contentId)  $str.= " selected='1' ";
         $str.= ">$contentName</option>";
    }
    $str.= "</select>";
    return $str;

}





function cms_content_selectStyle($type,$value,$dataName,$showData=array()) {
    $addEmpty = 1;
    $empytText = null;
    switch ($type) {
        case "button" :
            $emptyText = "Button Art Wählen";
            $styleList = array ("main"=>"Haupt - Button","second"=>"Sekundärer Button","readMore"=>"weiter lesen");
            $convert = 1;
            
            break;
        case "headline" :
            $styleList = array ("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4");
            break;

        case "frameStyle" :
            $styleList = cmsFrame_getStyles();
            break;

        case "imagePosition" :
            $styleList = array ("left"=>"Links vom Text","right"=>"Rechts vom Text","top"=>"Über dem Text","between"=>"Zwischen Überschrift und Text","bottom"=>"Unter dem Text","leftUnder"=>"Links unter Überschrift","rightUnder"=>"Rechts unter Überschrift","behind"=>"Hinter Text");
            $styleList = array();
            $styleList["between"] = "Zwischen Überschrift und Text";
            $styleList["top"] = "Über dem Text";            
            $styleList["bottom"] = "Unter dem Text";
            $styleList["behind"] = "Hinter Text";
            
            $styleList["left"] ="Links vom Text";
            $styleList["leftUnder"] = "Links unter Überschrift";
            $styleList["floatLeft"] = "Umfließend Links";
            $styleList["floatLeftUnder"] = "Umfließend Links unter Überschrift";
            
            $styleList["right"] ="Rechts vom Text";
            $styleList["rightUnder"] = "Rechts unter Überschrift";
            $styleList["floatRight"] = "Umfließend Rechts";
            $styleList["floatRightUnder"] = "Umfließend Rechts unter Überschrift";
            break;


        case "text" :
            $styleList = array ("left"=>"Links-Bündig","center"=>"Zentriert","right"=>"Rechts-Bündig");
            break;

         case "float" :
            $styleList = array ("none"=>"Unten","left"=>"Links","right"=>"Rechts");
            break;
        
        default :
            $styleList = array ("unkown"=>"Unbekannter Typ $type");
    }
    
    
    if ($convert) {
        $editClass = cms_contentTypes_class();
        foreach ($styleList as $key => $text) {
            $str = $editClass->lga("select",$type."_$key","",array("dt"=>$text));
            if (is_string($str)) {
                $styleList[$key] = $str;
            }
        }
        if ($emptyText) {
            $str = $editClass->lga("select",$type."_empty","",array("dt"=>$emptyText));
            if (is_string($str) AND $str != "select_".$type."_empty") {
                $emptyText = $str;
            }
        }
    }
    
    $str = "";
    
    $submit = "";
    if ($showData[submit]) $submit = "onChange='submit()'";
    $str.= "<select name='$dataName' class='cmsSelectType' $submit style='min-width:200px;' value='$value' >";
    if (!$styleList[$value] AND $addEmpty) {
        if (!$emptyText) $emptyText = "Bitte wählen";
        $str.= "<option value='' selected='1' >$emptyText</option>";
    }

    foreach ($styleList as $code => $styleName) {
        $str.= "<option value='$code'";
        if ($code == $value) $str.= " selected='1' ";
        $str.= ">$styleName</option>";
    }
    $str.= "</select>";
    
    if ($type=="imagePosition") {
        if (!$value) $value = "between";
        $str = div_start_str("cmsTextPos_select");
        $str .= "<img class='cmsTextPos_selectedImage' src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/textImagePos_".$value.".png' >";
        $str .= "<input type='hidden' class='cmsTextPosInput' value ='$value' name='$dataName' />";
        
        $str .= div_start_str("cmsTextPos_selectFrame");
        foreach ($styleList as $key => $name) {
            $posDivName = "cmsTextPos_selectPos cmsTextPos_$key";
            if ($key == $value) $posDivName .= " cmsTextPos_selectPos_selected";
            $str .= div_start_str($posDivName,array("title"=>$name));
            $str.="<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/textImagePos_".$key.".png' >";            
            // $str.= "$key <> $value ";
            $str .= div_end_str($posDivName);
        }
        $str .= div_end_str("cmsTextPos_selectFrame","before");
        $str .= div_end_str("cmsTextPos_select");
    }
    
    
    
    return $str;


}

function cmsEdit_VerticalAlign($valueName,$value) {
    global $cmsVersion;
    $res = "";
    $res .= "<input type='hidden' class='cmsEditVerInput' name='$valueName' style='width:30px' value='$value' >";
    $res .= div_start_str("cmsEditVer","border:1px solid #777;");

    $divName = "cmsEditVerButton";
    if ($value == "top") $divName .= " selectedVAlign";
    $res .= div_start_str($divName,array("title"=>"cmsEditVerInput","id"=>"top"));
    $res .= "<img src='/cms_".$cmsVersion."/images/top.gif' >";
    $res .= div_end_str($divName);

    $divName = "cmsEditVerButton";
    if ($value == "middle") $divName .= " selectedVAlign";
    $res .= div_start_str($divName,array("title"=>"cmsEditVerInput","id"=>"middle"));
    $res .= "<img src='/cms_".$cmsVersion."/images/middle.gif' >";
    $res .= div_end_str($divName);

    $divName = "cmsEditVerButton";
    if ($value == "bottom") $divName .= " selectedVAlign";
    $res .= div_start_str($divName,array("title"=>"cmsEditVerInput","id"=>"bottom"));
    $res .= "<img src='/cms_".$cmsVersion."/images/bottom.gif' >";
    $res .= div_end_str($divName);

    $res .= div_end_str("cmsEditVer","before");
    return $res;
}

function cmsEdit_HorizontalAlign($valueName,$value) {
    global $cmsVersion;
    $res = "";
    $res .= "<input type='hidden' class='cmsEditHorInput' name='$valueName' style='width:30px' value='$value' >";
    $res .= div_start_str("cmsEditHor");

    $divName = "cmsEditHorButton leftHAlign";
    if ($value == "left") $divName .= " selectedHAlign";
    $res .= div_start_str($divName,array("title"=>"cmsEditHorInput","id"=>"left"));
    $res .= "<img src='/cms_".$cmsVersion."/images/left.gif' >";
    $res .= div_end_str($divName);

    $divName = "cmsEditHorButton";
    if ($value == "center") $divName .= " selectedHAlign";
    $res .= div_start_str($divName,array("title"=>"cmsEditHorInput","id"=>"center"));
    $res .= "<img src='/cms_".$cmsVersion."/images/center.gif' >";
    $res .= div_end_str($divName);

    $divName = "cmsEditHorButton";
    if ($value == "right") $divName .= " selectedHAlign";
    $res .= div_start_str($divName,array("title"=>"cmsEditHorInput","id"=>"right"));
    $res .= "<img src='/cms_".$cmsVersion."/images/right.gif' >";
    $res .= div_end_str($divName);

    $res .= div_end_str("cmsEditHor","before");
    return $res;
}

function cmsEdit_imagePosition($nameH,$nameV,$valueH,$valueV) {
    $res = "";
    
    if (!$valueH) $valueH = "center";
    if (!$valueV) $valueV = "middle";
    
    // $res.= "H:$valueH / V:$valueV <br />";
    
   
    
    $res.= "<div class='cmsPosSelectFrame' >";
    $res.= "<input type='hidden' name='$nameH' value='$valueH' class='cmsPosInput_posH' >";
    $res.= "<input type='hidden' name='$nameV' value='$valueV' class='cmsPosInput_posV' >"; //  <br />";
    
    $li_V = array("t"=>"top","m"=>"middle","b"=>"bottom");
    $li_H = array("l"=>"left","c"=>"center","r"=>"right");
    
    foreach ($li_V as $v_key => $v_name) {
        
        foreach ($li_H as $h_key => $h_name) {
            
            $className = "cmsPosSelect";
            if ($valueH == $h_name AND $valueV == $v_name) $className .= " cmsPosSelected";
            
            $res.= "<div class='$className' id='pos_".$v_name."_".$h_name."' ></div>";
            
        }
        
        
    }
    
    
//    for ($i=0;$i<9;$i++) {
//        $res.= "<div style='width:7px;height:7px;border:1px solid #999;display:inline-block;overflow:hidden;' ></div>";
//    }
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
//    $res.= "<div style='width:3px;height:3px;border:1px solid #666;display:inline;' ></div>";
    
    $res .= "</div>";
//    $res .= "<input type='hidden' class='cmsEditHorInput' name='$valueName' style='width:30px' value='$value' >";
//    $res .= div_start_str("cmsEditHor","border:1px solid #777;");
//
//    $divName = "cmsEditHorButton leftHAlign";
//    if ($value == "left") $divName .= " selectedHAlign";
//    $res .= div_start_str($divName,array("title"=>"cmsEditHorInput","id"=>"left"));
//    $res .= "<img src='/cms_".$cmsVersion."/images/left.gif' >";
//    $res .= div_end_str($divName);
//
//    $divName = "cmsEditHorButton";
//    if ($value == "center") $divName .= " selectedHAlign";
//    $res .= div_start_str($divName,array("title"=>"cmsEditHorInput","id"=>"center"));
//    $res .= "<img src='/cms_".$cmsVersion."/images/center.gif' >";
//    $res .= div_end_str($divName);
//
//    $divName = "cmsEditHorButton";
//    if ($value == "right") $divName .= " selectedHAlign";
//    $res .= div_start_str($divName,array("title"=>"cmsEditHorInput","id"=>"right"));
//    $res .= "<img src='/cms_".$cmsVersion."/images/right.gif' >";
//    $res .= div_end_str($divName);
//
//    $res .= div_end_str("cmsEditHor","before");
    return $res;
    
}



function cmsEdit_SelectList($valueName,$value,$list) {
    $res = "";
    $res .= "<input type='hidden' class='cmsEditSelectButtonInput' name='$valueName' style='width:30px' value='$value' >";
    $res .= div_start_str("cmsEditHor","border:1px solid #777;");


    foreach($list as $key => $valueText) {
        $divName = "cmsEditSelectButton ";
        if ($value == $key) $divName .= " selectButtonSelected";
        $res .= div_start_str($divName,array("valueName"=>"cmsEditSelectButtonInput","value"=>$key,"style"=>"padding:0 5px 0 5px;"));
        $res .= $valueText;
        // $res .= " $key <> $value ";
        // if ($value == $key) $res .= " - sel";
        $res .= div_end_str($divName);

    }

    $res .= div_end_str("cmsEditHor","before");
    return $res;
}

function cms_getWidth($width,$frameWidth) {
    if (strlen($width)) {
        if (strpos($width,"%")) {
            $prozWidth = intval(substr($width,0,strpos($width,"%")));
            $newWidth = intval($frameWidth * $prozWidth / 100);
        } else {
            if (strpos($width,"px")) { // removePixel
                $newWidth = substr($width,0,strpos($width,"px"));
            }
            if (intVal($width)>0) {
                $newWidth = intval($width);
            } else {
                switch ($width) {
                    case "auto" : $newWidth = $frameWidth; $imageHeight = $frameWidth; break;
                    case "full" : $newWidth = $frameWidth; break;
                 }
            }
        }
        return $newWidth;
    }
    return null;
}

?>
