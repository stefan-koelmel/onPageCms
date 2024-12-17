<?php // charset:UTF-8

function cms_text_languageList() {
    $languages = array();
    $languages[dt] = "Deutsch";
    $languages[en] = "Englisch";
    $languages[fr] = "Französisch";
    return $languages;    
}

function cms_text_getLanguage($setLg=null) {
    if (is_string($setLg)) {
        $_SESSION[lg] = $setLg;
        return $setLg;        
    }
    $lg = $_SESSION[lg];
    if ($lg) return $lg;
    
    $languageList = cms_text_getSettings();
    
    $firstLanguage = 0;
    $activeLanguage = 0;
    foreach ($languageList as $key => $value) {
        if (!$firstLanguage) $firstLanguage = $key;
        
        if ($value[active]) $activeLanguage = $key;
    }
    if ($activeLanguage) $setLanguage = $activeLanguage;
    else $setLanguage = $firstLanguage;
    $_SESSION[lg] = $setLanguage;
    // echo ("$setLanguage <br>");
    return $setLanguage;
}

function cms_text_adminLg($setLg=null) {
    if (is_string($setLg)) {
        echo ("<h1>SET ADMIN LG TO $setLg </h1>");
        $_SESSION[adminLanguage] = $setLg;
        return $setLg;
    }
    
    
    $adminLg = $_SESSION[adminLanguage];
    if ($adminLg) return $adminLg;
    
    $languageList = cms_text_getSettings();
    foreach ($languageList as $lgCode => $lgData ) {
        if ($lgData[admin]) $setAdminLg = $lgCode;
    }
    if (!$setAdminLg) $setAdminLg = "dt";
    
    // echo ("<h4>SET ADMIN LG TO $setAdminLg </h4>");
    $_SESSION[adminLanguage] = $setAdminLg;
    
    return $setAdminLg;
    
}

function cms_text_createArray($str,$lg=null) {
    if (!$lg) $lg = cms_text_getLanguage ();
    $lgList = cms_text_languageList();
    $debug = 0;
    
    // create Empty Array
    $res = array();
    foreach ($lgList as $key => $value) {
        $res[$key] = "";
    }
    
    if (is_string($str)) {
        if (substr($str,0,3) == "lg|") {
            $help = explode("|",$str);
            for ($i=1;$i<count($help);$i++) {
                $off = strpos($help[$i],":");
                list($lgCode,$lgStr) = explode(":",$help[$i]);
                $res[$lgCode] = $lgStr;      
                if ($debug) echo ("HIER $lgCode => $lgStr <br>");
            }
        } else {
            $help = str2Array($str);
            if (is_array($help)) {
                if ($debug) echo ("str is array ".count($help));
                foreach ($help as $key => $value ) {
                    $res[$key] = $value;
                    if ($debug) echo (" -> $key=$value ");
                }
                if ($debug) echo ("<br>");
            } else {
                $res[$lg] = $str;
                if ($debug) echo ("Set STRANDAR $lg to $str <br>");
            }
        }        
    }
    
    
    return $res;
}


function cms_text_getLg($str,$useDefault=1) {
    if (is_array($str)) {
        $lgList = $str;
    } else {
        if (substr($str,0,3)=="lg|") {
            $lgList = array();
            $help = explode("|",$str);           
            for($i=1;$i<count($help);$i++) {
                list($lgCode,$lgStr) = explode(":",$help[$i]);
                if ($lgCode) $lgList[$lgCode] = $lgStr;           
                // echo ("add $lgCode $lgStr <br>");
            }
        }
    }
    
    if (!is_array($lgList)) return $str;
    $lg = cms_text_getLanguage();
    //echo ("Found $lgList $lg $lgList[$lg]<br>");
    
    $str = $lgList[$lg];
    if ($str) return $str;
    
    $str = $lgList["lg_".$lg];
    if (is_string($str)) return $str;
    
    if ($useDefault) {
        foreach($lgList as $lgCode => $lgStr) {
            if ($lgStr) {
                $str = "";
                if ($_SESSION[showLevel]>3) $str .= $lgCode.":";
                $str .= $lgStr;
                return $str;
            }
        }
    }
    return $lgList[$lg];
}



function cms_text_getSettings() {
    global $cmsSettings;
   
    $help = explode("|",  cms_text_lg_edit());
    $lgEdit = array();
    for($i=0;$i<count($help);$i++) $lgEdit[$help[$i]] = 1;
    
    $help = explode("|",  cms_text_lg_show());
    $lgShow = array();
    for($i=0;$i<count($help);$i++) $lgShow[$help[$i]] = 1;
    
    $languageStr = $cmsSettings[language];
    if (strlen($languageStr) < 3) $languageStr = 0;
    if (!$languageStr) {
        $activeLanguage = 0;
        $languages = cms_text_languageList();
        $languageStr = "";
        foreach ($languages as $code => $name) {
            // trenner
            if ($languageStr) $languageStr .= "|";
            
            // code
            $languageStr .= $code.":";
            
            // enabled 
            if ($activeLanguage==0) $languageStr .= "1".":";
            else $languageStr .= "0".":";
            
            // editable 
            if ($activeLanguage==0) $languageStr .= "1".":";
            else $languageStr .= "0".":";
            
            // active 
            if ($activeLanguage==0) $languageStr .= "1";
            else $languageStr .= "0";
            
            // if ($adminLanguage==0) $languageStr .= "1";
            // else $languageStr .= "0";
            
            if ($activeLanguage==0) $activeLanguage = 1;

        }
    }
    
    // $language = "dt:1:1|en:1:1|fr:1:0";
    $languageList = array();
    $help = explode("|",$languageStr);
    $activeLanguage = $_SESSION[lg];;
    $defaultLanguage = "";
    
    $languages = cms_text_languageList();
        
    for ($i=0;$i<count($help);$i++) {
        list($code,$enabled,$editable,$active,$admin) = explode(":",$help[$i]);
        
        $languageList[$code] = array();
        $languageList[$code][name] = $languages[$code];
        
        if ($enabled == "1") {
            $languageList[$code][enabled] = 1;
        } else {
            $languageList[$code][enabled] = 0;
        }
        
        if ($editable) {
            $languageList[$code][editable] = 1;
        } else {
            $languageList[$code][editable] = 0;
        }

        if ($activeLanguage == $code) {
        //if ($active) {
            //$activeLanguage = $code;
            $languageList[$code][active] = 1;
        } else {
            if ($active) {
                if (!$defaultLanguage) {
                    $defaultLanguage = $code;
                    $languageList[$code][active] = 1;
                }
            }
            $languageList[$code][active] = 0;
        }
        if ($admin) {
            $languageList[$code][admin] = 1;              
        } else {
            $languageList[$code][admin] = 0;
        }
        
        $show = "hidden";
        if ($lgShow[$code] == 1) $show = "show";
        if ($lgEdit[$code] == 1) $show = "edit";
        
        $languageList[$code][show] = $show;
    }
    return $languageList;
}



function cms_text_lg_edit() {
    $lgEdit = $_SESSION[lgEdit];
    if ($lgEdit) return $lgEdit;    
    $lgEdit = $_SESSION[lg]; //cms_text_getLanguage();
    $_SESSION[lgEdit] = $lgEdit;
    return $lgEdit;
}

function cms_text_lg_show() {
    $lgShow = $_SESSION[lgShow];
    if ($lgShow) return $lgShow;
    
    $lgShow = "";
    $_SESSION[lgShow] = $lgShow;
    return $lgShow;
}
    
function cms_text_checkLanguages($lgList) {
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_text`  ";
    $result = mysql_query($query);
    if (!$result) return "error";
    
    $lgData = mysql_fetch_assoc($result);
    if (!is_array($lgData)) return "noData for Languages $query ";

    $res = "";
    
    foreach ($lgList as $key => $value ) {
        if (is_null($lgData["lg_".$key])) {
            // echo ("<h1> NOT FOUND $key </h1>");
            $query = "ALTER TABLE `".$GLOBALS[cmsName]."_cms_text` ADD `lg_$key` TEXT NOT NULL ";
            // echo ("$query <br>");
            $result = mysql_query($query);
            
            if ($res) $res.="<br />";
            if ($result) {
                $res .= "Sprache $key angelegt";
            } else {
                $res.= "Fehler beim Sprache '$key' anlegen !";
            }
        }        
    }
    return $res;
}

function cms_text_getForContent($contentName,$allLg=0) {
    // echo ("Get Text for contentName $contentName <br>");
    // $lg = $GLOBALS[pageInfo][lg];
    if (!$lg){ 
        $lg = $_SESSION[lg];
    }
    if (!$lg) {
        $lg="dt";
        $pageInfo[lg] = $lg;
        $_SESSION[lg] = $lg;
        echo ("Set LG to $lg <br>");
    }
    
   
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_text` WHERE `contentId` = '$contentName' ";

    $textList = array();
    $result = mysql_query($query);
    if(!$result) {
        echo ("error in query $query<br>");
        return $textList;
    }

    while ($textData = mysql_fetch_assoc($result)) {
        $name = $textData[name];
        $id = $textData[id];
        
        $text = $textData["lg_".$lg];
        
        if ($text == "") {
            
        }
        
        
        
        
        
        switch ($lg) {
            case "dt" : $text = $textData[lg_dt]; break;

        }
        $css = $textData["css"];
        // echo ("Get from db $name: $text $css <br>");
        
        
        
        if ($textData[data]) {
            $ar = str2Array($textData[data]);
            if (is_array($ar)) $textData[data] = $ar;
        }
        // echo ("getText_forContent $textData[data]<br>");

        $textList[$name] = array("id"=>$id,"text"=>$text,"css"=>$css,"data"=>$textData[data]);
        
        if ($allLg) {
            foreach ($textData as $key => $value ) {
                if (substr($key,0,3) == "lg_" ) {
                    // echo ("found $key for $name = $value <br>");
                    $textList[$name][$key] = $value;
                }
            }
        }
        
        
    }
    // echo ("Found ".count($textList)."<br>");
    ksort($textList);
    return ($textList);

}

function cmsText_get($getData) {
    $query = "";
    foreach($getData as $key => $value) {
        if ($value) {
            if ($query) $query .= "AND ";
            $query .= "`$key` = '$value' ";
        }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_text` WHERE $query";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        // echo ("Not found <br>");
        return 0;
    }
    if ($anz > 1) {
        // echo ("More found <br>");
        return 0;
    }   
    $text = mysql_fetch_assoc($result);
    $lg = "dt";
    $lgText = $text["lg_".$lg];
    if ($lgText) $text[text] = $lgText;
    
    if ($text[data]) {
        $ar = str2Array($text[data]);
        if (is_array($ar)) $text[data] = $ar;
    }
    // echo ("getText $text[data]<br>");
    return $text;
}

function cmsText_getList($filter,$sort=null,$out=null) {

    if ($sort) {
        $upPos = strpos($sort, "_up");
        $sortQuery = "";
        if ($upPos) {
            $sortValue = substr($sort,0,$upPos);
            $sortQuery = "ORDER BY `$sortValue` DESC ";
            // echo ("Sort down '$sortValue' -> $sortQuery <br>");
        }

        if ($sortQuery=="") {
           $sortQuery = "ORDER BY `$sort` ASC ";
        }
    } else {
        $sortQuery = "ORDER BY `id` ASC ";
    }

    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";
            foreach($filter as $key => $value) {
                 switch ($value[0]) {
                    case ">" :
                        $filterQuery .= "`$key`$value";
                        break;
                    case "<" :
                        $filterQuery .= "`$key`$value";
                        break;
                    default :
                        switch ($key) {
                            case "search" : 
                                if ($filterQuery != "") $filterQuery .= " AND ";
                                $filterQuery .= "`lg_".$_SESSION[lg]."` LIKE '%$value%' AND `name` = 'headline' " ;
                                break;
                                
                            case "searchText" : 
                                if ($filterQuery != "") $filterQuery .= " AND ";
                                $filterQuery .= "`lg_".$_SESSION[lg]."` LIKE '%$value%' AND (`name`='headline' OR `name`='text')" ;
                                break;
                                
                            default :
                                if ($filterQuery != "") $filterQuery .= " AND ";
                                $filterQuery .= "`$key`='$value'";
                            
                        }
                        
                        
                }


//                if ($filterQuery != "") $filterQuery .= " AND ";
//                $filterQuery .= "`$key` = $value";
            }
            $filterQuery = "WHERE ".$filterQuery;
        }

        switch ($filter) {
            case "new" :
                $filterQuery = "WHERE `new` = 1";
        }


    } else {
        $filterQuery = ""; //  "WHERE `show` = 1";
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_text` ".$filterQuery." ".$sortQuery;
    if ($out == "out") echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    $lg = $_SESSION[lg];
    while ($text = mysql_fetch_assoc($result)) {
        
        $lgText = $text["lg_".$lg];
        if ($lgText) $text[text] = $lgText;
        
        if ($text[data]) {
            $ar = str2Array($text[data]);
            if (is_array($ar)) $text[data] = $ar;
        }
        // echo ("getTextList $text[data]<br>");
        $res[] = $text;

    }
    return $res;
}



function cmsText_search($searchStr,$searchText) {
    $filter = array();
    if ($searchText) $filter[searchText] = $searchStr;
    else $filter["search"] = $searchStr;
    $out = "";
    $sort ="";
    
    $res = cmsText_getList($filter,$sort,$out);
    return $res;
    
    
}

function cms_text_insert($data) {
    if (!is_array($data)) { echo ("noData for cms_text_insert ($data)<br>"); return 0;}
    foreach ($data as $key => $value ) {
        echo ("cms_insert $key = $value <br>");
    }
    return 0;
}

function cms_text_delete($textId) {
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_text` WHERE `id`=$textId ";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in query $query <br>");
        return 0;
    }
    return 1;
}

function cms_text_deleteContent($contentName) {
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_text` WHERE `contentId`='$contentName' ";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in query $query <br>");
    }
}

function cms_text_save($data) {
    if (!is_array($data)) { echo ("noData for cms_text_insert ($data)<br>"); return 0;}
    
    $languages = cms_text_languageList();
    global $pageInfo;
 
    $id = $data[id];
    $css = $data[css];
    $contentId = $data[contentId];
    $name = $data[name];
    $text = $data[text];
    
    
    $color = $data[color];
    $colorId = $data[colorId];
    $colorBlend = $data[colorBlend];
    $colorSaturation = $data[colorSaturation];
    if ($color[data]) {
        $data[data] = array();
        $data[data][color] = $color;
        $data[data][colorId] = $colorId;
        $data[data][colorBlend] = $colorBlend;
        $data[data][colorSaturation] = $colorSaturation;
    }
    
    
    if (is_array($data[data]) AND count($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    // echo ("<h1> Farbe ?? $contentId / $name $color $colorId $colorBlend $colorSaturation $data[data]</h1>");

    if (!$contentId) {
        $editId = $_GET[editId];
        if ($editId) $contentId = $editId;
    }

    if (!$contentId) {
        echo ("no ContentId in cms_text_save<br>");
        return 0;
    }
    
    
   

    if ($id) $query = "UPDATE `".$GLOBALS[cmsName]."_cms_text` SET ";
    else $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_text` SET ";
    $query .= "`contentId`='$contentId' ";
    $query .= ", `css`='$css' ";
    $query .= ", `name`='$name' ";
    if ($data[data]) $query .= ", `data`='$data[data]'";
    
    $foundLanguageCodes = 0;
    $foundContent = 0;
    
    // echo ("SAVE FOR $contentId name = $name $id <br />");
    
    foreach ($languages as $lgCode => $lgName) {
        $dbCode = "lg_".$lgCode;
        if (!is_null($data[$dbCode])) {
            $query .= ", `$dbCode` = '".$data[$dbCode]."'";
            if ($data[$dbCode]) {
                $foundContent++;
                // echo ("<h1> found Language $lgCode $dbCode $foundContent </h1>");
            } else {
                // echo ("<h2> EMPTY STR $lgCode $dbCode $foundContent </h2>");
            }
            $foundLanguageCodes ++;
        }
    }
    
    if ($foundLanguageCodes) {
        if ($foundContent == 0) {
            // echo ("<h3>Empty Text for $id </h3>");
            if ($id) {
                echo ("Delete Text with id $id because empty <br>");
                cms_text_delete($id);
                return 1;
            }
        }
    } else {
        if (!$text) {
            if ($id) {
                echo ("<h1>Save without lgs '$text' $id </h1>");
                echo ("Delete Text with id $id because empty <br>");
                $res = cms_text_delete($id);
                // cms_text_deleteContent("text_27");
                return $res;
            }
        }
        
        
        switch ($pageInfo[lg]) {
            case "dt" : $query .= ", `lg_dt`='$text'"; break;
            case "en" : $query .= ", `lg_en`='$text'"; break;
            default :
                echo ("unkown lg '$lg' in cms_save_text<br>");
                foreach($pageInfo as $key => $value )echo ("pI $key = $value<br>");
                return 0;
        }
    }
    
    if ($id) $query .= " WHERE `id` = $id ";

    $result = mysql_query($query);
    if (!$result) {
         echo ("Error in Query for cms_save_text '$query'<br>");
         return 0;
    }
    if (!$id) {
        $insertId = mysql_insert_id();
        return $insertId;
    }
    return 1;
}


function cmsText_update($data,$compareData) {
    
    $id = $data[id];
    $query = "";
    $diff = 0;
    foreach ($data as $key => $value) {
        if ($value == $compareData[$key]) {
            // echo ("Same $value == $compareData[$key] <br>");
        } else {
            echo ("Diffrent $value != $compareData[$key] <br>");
            $diff++;
            if ($query) $query .= ", ";
            $query .= "`$key`='$value'";
        }
    }
    if ($diff == 0) {
        // echo ("No Change for $id! <br>");
        return 1;
    } 
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_text` SET ".$query." WHERE `id` = $id";
    $result = mysql_query($query);
    if (!$result) {
        echo ("$diff Changes !<br>");
        echo ("Error in Query = '$query' <br>");
        return 0;
    }
    
    return 1;
}

function cmsText_save($data) {
    
    $id = $data[id];
    if ($id) {
        $compareData = cmsText_get(array("id"=>$id));
        if (is_array($compareData)) {
            $res = cmsText_update($data,$compareData);
            return $res;
        } 
        
    }
    
    
    $query = "";
    foreach ($data as $key => $value) {    
        if ($query) $query .= ", ";
        $query .= "`$key`='$value'";    
    }
    
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_text` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error by Insert $query <br>");
        return 0;
    }
    $insertId = mysql_insert_id();
    echo ("Insert id = $data[id] -> insertid = $insertId <br>");
    
    
    // show_array($data);
    return 1;
}

function cmsText_clearOutPut($str,$linebreak=1) {
    
    
    $replace = array(); // array("&"); // ,"\r");
    $repWith = array(); // array("&#038;"); //,"&nbsp;<br/>");
    
//    $offSet_amp = strpos($str,"&");
//    while (is_int($offSet_amp)) {
//       $change = 1;
//       if ($str[$offSet_amp+1] = "#") $change =0;
//       else {
//           
//            echo ("Zeichen ist & '$str[$offSet_amp]' - nächstes ist '".$str[$offSet_amp+1]."'<br>");
//       }
//       
//       $offSet_amp = strpos($str,"&",$offSet_amp+1);
//        
//    }
    
    
   // $replace = array("&"); // ,"\r");
   // $repWith = array("&amp;"); 
    
    
    
    if ($linebreak) {
        $replace[] = "\r";
        $repWith[] = "&nbsp;<br/>";
    }
    $str = str_replace($replace, $repWith, $str);
    

    
    // BOLD
    $offSet_bold = strpos($str,"#f#");
    $open_bold = 0;
    while (is_int($offSet_bold)) {
        if ($open_bold) {
            $add ="</b>";
            $open_bold = 0;
        } else {
            $add = "<b>";
            $open_bold = 1;
        }
        $str = substr($str,0,$offSet_bold).$add.substr($str,$offSet_bold+3);
        
        $offSet_bold = strpos($str,"#f#");        
    }
    if ($open_bold)  $str.="</b>";
    
    
    // BOLD
    $offSet_underline = strpos($str,"#u#");
    $open_underline = 0;
    while (is_int($offSet_underline)) {
        if ($open_underline) {
            $add ="</u>";
            $open_underline = 0;
        } else {
            $add = "<u>";
            $open_underline = 1;
        }
        $str = substr($str,0,$offSet_underline).$add.substr($str,$offSet_underline+3);
        
        $offSet_underline = strpos($str,"#u#");        
    }
    if ($open_underline)  $str.="</u>";
    
    
    $formatStr = "Formatierung: <br><b>Fett</b> = #f# Fett #f#<br><i>Kursiv</i> = #k# kursiv #k#";
    $formatStr .= "<br>&sbquo;einfache Anführungszeichen&lsquo; => #a# Text #a#";
    $formatStr .= "<br>&bdquo;doppelte Anführungszeichen&ldquo; => #A# Text #A#";
        
    
    // kleine Anführungszeichen
    $offSet_quote = strpos($str,"#a#");
    $open_quote = 0;
    while (is_int($offSet_quote)) {
        if ($open_quote) {
            $add = "&lsquo;";
            $open_quote = 0;
        } else {
            $add ="&sbquo;"; 
            $open_quote = 1;
        }
        $str = substr($str,0,$offSet_quote).$add.substr($str,$offSet_quote+3);        
        $offSet_quote = strpos($str,"#a#");        
    }
    if ($open_quote) $str.="&lsquo;";
    
     // doppelte Anführungszeichen
    $offSet_quote = strpos($str,"#A#");
    $open_quote = 0;
    while (is_int($offSet_quote)) {
        if ($open_quote) {
            $add = "&ldquo;";
            $open_quote = 0;
        } else {
            $add ="&bdquo;"; 
            $open_quote = 1;
        }
        $str = substr($str,0,$offSet_quote).$add.substr($str,$offSet_quote+3);        
        $offSet_quote = strpos($str,"#A#");        
    }
    if ($open_quote) $str.="&ldquo;";
    
    
    // kursiv
    $offSet_italic = strpos($str,'#k#');
    $open_italic = 0;
    while (is_int($offSet_italic)) {
        if ($open_italic) {
            $add = "</i>";
           
            $open_italic = 0;
        } else {
            $add ="<i>"; 
            $open_italic = 1;
        }
        $str = substr($str,0,$offSet_italic).$add.substr($str,$offSet_italic+3);
        
        $offSet_italic = strpos($str,'#k#');
        
    }
    if ($open_italic) $str.= "</i>";
    
    
    // Liste 
    $offSet_list = strpos($str,'#li#');
    $open_list = 0;
    while (is_int($offSet_list)) {
        $listStart = "<ol class='textList' style=''>";
        $listEnd   = "</ol>";

        $listEndPos = strpos($str,'#li#',$offSet_list+3);

        $listCont = substr($str,$offSet_list+4,$listEndPos-$offSet_list-4);
        $listCont = str_replace(array("&nbsp;","\n"),"",$listCont);
        $listLines = explode("<br/>",$listCont);
        $listStr = "";
        for ($i=0;$i<count($listLines);$i++) {
            $line = $listLines[$i];
           
            
            if (is_int(strpos($line,"|"))) {
                
                list($start,$line) = explode("|",$line);
                $listClass = "textList_item";
                switch ($start) {
                    case "o" : 
                        $listClass .= " textList_item_circle"; 
                        break;
                    case "-" : 
                        $listClass .= " textList_item_minus"; 
                        break;
                 
                    case "d" : 
                        $listClass .= " textList_item_disc"; 
                        break;
                    case "s" : 
                        $listClass .= " textList_item_square"; 
                        break;
                    case "n" : 
                        $listClass .= " textList_item_none";
                        break;
                    case ""  : 
                        $listClass .= " textList_item_none"; 
                        break; 
                    default :
                        echo ("<h1>unkownStart Char '$start' lang='".strlen($start)."'</h1>");
                }
                
            }
            
            
            $listStr .= "<li class='$listClass' style='' >$line</li>";
            //  echo ("Line $i of ".count($listLines)." : $line <br />");
        }

        // echo ("<b>$listCont</b><br/>");

        // echo ("LIST END: $listEndPos <br />");

        $add = $listStart.$listStr.$listEnd;
        $open_list = 1;
        
        $str = substr($str,0,$offSet_list).$add.substr($str,$listEndPos+4);
        
        $offSet_list = strpos($str,'#li#');
        
    }
    if ($open_list) $str.= "</li>";
       
   
    return $str;
}

/******************************************************************************/
/* DEFAULT TEXT                                                               */
/******************************************************************************/

function cms_defaultText_show($frameWidth=1000) {
    global $defaultText_notFound;
    if (!is_array($defaultText_notFound)) return 0;
    if (count($defaultText_notFound)==0) return 0;
    
    $lgList = cms_text_languageList();
    div_start("cmsDefaultText_frame","background-color:#333;color:#ddd;padding-bottom:10px;width:".$frameWidth."px;");
    echo ("<form method='post' >");
    echo ("<h1 style='color:#f00;'>Fehlende Texte </h1>");
    $width = floor((($frameWidth - 100) - (count($lgList)*30)) / count($lgList));
    foreach ($defaultText_notFound as $type => $typeValue) {
        echo ("found $type <br />");
        foreach ($typeValue as $code => $codeValue) {
            echo (span_text("$code:",100));
            if ($codeValue[id]) {            
                echo ("<input type='hidden' style='width:30px;' value='$codeValue[id]' name='defaultText[$type][$code][id]' />");            
            }
            foreach ($lgList as $lgCode => $lgStr) {               
                echo ("$lgCode :");
                echo ("<input type='text' style='width:".$width."px;' value='$codeValue[$lgCode]' name='defaultText[$type][$code][$lgCode]' />");               
            }
            echo ("<br />");
        }
    }
    echo ("<input type='submit' name='defaultText_save' value='speichern' /><br />");
    
    echo ("</form>");
    div_end("cmsDefaultText_frame");
    
}

function cms_adminText_show($frameWidth=1000) {
    global $adminText_notFound;
    if (!is_array($adminText_notFound)) return 0;
    if (count($adminText_notFound)==0) return 0;
    
    $lgList = cms_text_languageList();
    div_start("cmsDefaultText_frame","background-color:#555;color:#ddd;padding-bottom:10px;width:".$frameWidth."px;");
    echo ("<form method='post' >");
    echo ("<h1 style='color:#0f0;'>Fehlende adminTexte </h1>");
    $width = floor((($frameWidth - 100) - (count($lgList)*30)) / count($lgList));
    foreach ($adminText_notFound as $type => $typeValue) {
        echo ("found $type <br />");
        foreach ($typeValue as $code => $codeValue) {
            echo (span_text("$code:",100));
            if ($codeValue[id]) {            
                echo ("<input type='hidden' style='width:30px;' value='$codeValue[id]' name='adminText[$type][$code][id]' />");            
            }
            foreach ($lgList as $lgCode => $lgStr) {               
                echo ("$lgCode :");
                echo ("<input type='text' style='width:".$width."px;' value='$codeValue[$lgCode]' name='adminText[$type][$code][$lgCode]' />");               
            }
            echo ("<br />");
        }
    }
    echo ("<input type='submit' name='adminText_save' value='speichern' /><br />");
    
    echo ("</form>");
    div_end("cmsDefaultText_frame");    
    
}

function cms_defaultText_save($defaultText) {
    $error = 0;
    foreach ($defaultText as $type => $typeData) {
        foreach ($typeData as $code => $codeData) {
            $id = 0;
            $query = "";
            $add = array();
            foreach ($codeData as $key => $value) {
                switch ($key) {
                    case "id" : $id = intval($value); break;
                    default : 
                        if ($query) $query.=", ";
                        $query .= " `lg_$key`='$value'";  
                        $add[$key] = $value;
                        
                }
            }
            if ($id) {
                $query = "UPDATE `cms_defaultText` SET ".$query." WHERE `id` = $id ";
            } else {
                $query = "INSERT INTO `cms_defaultText` SET `type`='$type', `code`='$code' ,". $query;
            }
            
            $result = mysql_query($query);
            if (!$result) {
                $error++;
                echo ("Error in $query <br>");
                continue;
            }
            if (!$id) $insertId = mysql_insert_id ();
            
            if (!is_array($_SESSION[defaultText][$type])) $_SESSION[defaultText][$type] = array();
        
            if (!is_array($_SESSION[defaultText][$type][$code])) {
                $add[id] = $insertId;
                $_SESSION[defaultText][$type][$code] = $add;
            } else {
                foreach ($add as $key => $value ) {
                    if ($_SESSION[defaultText][$type][$code][$key] != $value) {
                        $_SESSION[defaultText][$type][$code][$key] = $value;
                        echo ("Update $key in $type $code <br />");
                    }
                }
            }
        }        
    }
    if ($error) return $error;
    
    return 0;
}


function cms_adminText_save($adminText) {
    $error = 0;
    foreach ($adminText as $type => $typeData) {
        foreach ($typeData as $code => $codeData) {
            $id = 0;
            $query = "";
            $add = array();
            foreach ($codeData as $key => $value) {
                switch ($key) {
                    case "id" : $id = intval($value); break;
                    default : 
                        if ($query) $query.=", ";
                        $query .= " `lg_$key`='$value'";  
                        $add[$key] = $value;
                        
                }
            }
            if ($id) {
                $query = "UPDATE `cms_adminText` SET ".$query." WHERE `id` = $id ";
            } else {
                $query = "INSERT INTO `cms_adminText` SET `type`='$type', `code`='$code' ,". $query;
            }
            
            
            $result = mysql_query($query);
            if (!$result) {
                $error++;
                echo ("Error in $query <br>");
                continue;
            }
            if (!$id) $insertId = mysql_insert_id ();
            
            if (!is_array($_SESSION[adminText][$type])) $_SESSION[adminText][$type] = array();
        
            if (!is_array($_SESSION[adminText][$type][$code])) {
                $add[id] = $insertId;
                $_SESSION[adminText][$type][$code] = $add;
            } else {
                foreach ($add as $key => $value ) {
                    if ($_SESSION[adminText][$type][$code][$key] != $value) {
                        $_SESSION[adminText][$type][$code][$key] = $value;
                        // echo ("Update $key in $type $code <br />");
                    }
                }
            }
        }        
    }
    if ($error) return $error;
    return 0;    
}

?>
