<?php // charset:UTF-8
function cmsStyle_getList($filter,$sort,$out=null) {

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
                        if ($key == "category") { // multiselect from category
                             $filterQuery .= " (`$key` = '$value' OR `$key` LIKE  '%|$value|%') ";
                        } else {
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


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_style` ".$filterQuery." ".$sortQuery;
    if ($out == "out") echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($style = mysql_fetch_assoc($result)) {
        $style = php_clearQuery($style);
    
        switch ($out) {
            case "assoId" :
                $id = $style[id];
                $res[$id] = $style;
                break;
            case "assoName" :
                $name = $style[name];
                $res[$name] = $style;
                break;
            default :
                $res[] = $style;
        }
    }
    return $res;
}


function cmsStyle_get($filter,$out=null) {
    $filterQuery = "";
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            if ($filterQuery == "") $filterQuery .= "WHERE ";
            else $filterQuery .= "AND ";
            switch ($key) {
                case "name" : $type = "text"; break;
                case "info" : $type = "text"; break;
                case "url" : $type = "text"; break;
                default:
                    $type = "normal";
                    // if (is_string($value)) $type = "text";
            }
            if ($type == "text") {
                $filterQuery .= "`$key` LIKE '$value' ";
            } else {
                $filterQuery .= "`$key` = '$value' ";
            }
        }
    }
    
    $sortQuery = "";
    // echo("$filterQuery <br>");

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_style` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    if (!$result) {
        echo ("NO Style-DATABASE!!!");
        echo ("$filter $out <br>");
        $dbExist = cmsAdmin_checkTableExist("style");
        echo ("DB EXIST = $dbExist <br>");
        return 0;
    }
    // echo("$query -> $result <br>");
    $anz = mysql_num_rows($result);

   //  echo("$query -> $result $anz <br>");
    if ($anz == 0) {
        // cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        // cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return $anz;
    }
    $styleData = mysql_fetch_assoc($result);
    $styleData = php_clearQuery($styleData);
    
    switch($out) {
        case "css" :
            $css = array();
            foreach ($styleData as $key => $value) {  
                switch ($key) {
                    case "id" : $css[id] = $value;
                    case "theme" : break;   
                    case "type" : break;
                    case "name" : break;   
                    default :
                        //  echo ("$key = $value <br> ");
                        // $value = str_replace("px","",$value);
                        $list = explode(";",$value);
                        for($i=0;$i<count($list);$i++) {
                            list($cssKey,$cssValue) = explode(":",$list[$i]);
                            // echo ("  -> $cssKey = $cssValue <br>");
                            $css[$cssKey] = $cssValue;
                        }                        
                }
            }
            $styleData = $css;
            break;
    } 
    
    return $styleData;
}

function cmsStyle_add($styleData) {
    $res = 0;
    
    
    $payment = $styleData[payment];
    if (!$payment) return "noPayment";
    
    $adress = $styleData[adress];
    if (!is_array($adress)) {
        return "noAdress";
    }
    
    
    echo ("ADD ORDER<br>");
    
    
    return "notReady";    
    
    
    
    
    
    
    
}



function cmsStyle_save($data) {
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cms_user_getById($id);
        if ($existData) {
            return cmsStyle_update($data);
        }
    }
    // echo ("Not Found - no Id <br> ");

   
    // echo ("not Found with Name / Id<br>");
    $query = "";
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                $value = php_clearStr($value);
        }

        if ($value) {
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    $query = "INSERT `".$GLOBALS[cmsName]."_cms_style` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in cmsStyle_save $query <br>";
        return 0;
    }
    $insertId = mysql_insert_id();
    return $insertId;
}

function cmsStyle_update($data) {
    $query = "";
    $id = $data[id];
    $addChange = 0;
    $addLastMod = 0;
    
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                $value = php_clearStr($value);
        }
        
        if (!is_null($value) AND $key != "id") {
            if ($value == "not") $value = "";
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_style` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}

function cmsStyle_getTheme() {
    $wireframeState = cmsWireframe_state();
        
        
    if ($wireframeState) {
        $theme = $GLOBALS[cmsSettings][wireframe_theme];
        
    } else {
        $theme = $GLOBALS[cmsSettings][normal_theme];        
    }
    return $theme;
        
}


function cmsStyle_frameSettings($frameStyle,$out) {
    $theme = cmsStyle_getTheme();
    // echo ("cmsStyle_frameSettings($frameStyle,$out) theme = $theme<br>");
    $filter = array("type"=>"frame","name"=>$frameStyle,"theme"=>$theme);
    $frameData = cmsStyle_get($filter);
    if (is_array($frameData)) {
        switch($out) {
            case "css" :
                $css = array();
                foreach ($frameData as $key => $value) {  
                    switch ($key) {
                        case "id" : break;
                        case "theme" : break;   
                        case "type" : break;
                        case "name" : break;   
                        default :
                            $value = str_replace("px","",$value);
                            $list = explode(";",$value);
                            for($i=0;$i<count($list);$i++) {
                                list($cssKey,$cssValue) = explode(":",$list[$i]);
                                $css[$cssKey] = $cssValue;
                            }                        
                    }
                }
                return $css;
                
            default :
                return $frameData;
        } 
   
      
        show_array($frameData);
    }
}

function cmsStyle_selectTheme($value,$dataName,$themeType) {
    $filter = array("type"=>"theme");
    switch ($themeType) {
        case "wireframe" : 
            $filter["theme"] = "wireframe";
            break;
        case "normal" :
            $filter["theme"] = "normal";
            break;
    }
           
    $themeList = cmsStyle_getList($filter,"name","assoName");
    
    
    if (count($themeList)==0 AND $themeType) {
        
        $cssData = array("theme"=>$themeType,"type"=>"theme","name"=>$themeType."-Standard");
        // show_array($cssData);
        $res = cmsStyle_save($cssData);
        if ($res) {
            cms_infoBox("Standard Theme erstellt ");
            $add = "";
            if ($_GET[view]) $add.="view=".$_GET[view];
            $goPage = cms_page_goPage($add);
            // echo ("GoPage = $goPage add='$add' <br>");
            reloadPage($goPage,5);

            return "";
        }
        
    }
    
    
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectTheme' value='$value' >";

    if (!$value) $value = "Standard";
    $str.= "<option value='none'";
    if ($code == $type)  $str.= " selected='1' ";
    $str.= ">Kein Theme</option>";

    foreach ($themeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $value)  $str.= " selected='1' ";
         $name = $typeData[name];
         if (substr($name,0,7)  == "normal-") $name = substr($name,7);
         if (substr($name,0,10) == "wireframe-") $name = substr($name,10);
         
         $str.= ">$name</option>";
    }
    $str.= "</select>";
    return $str;
}


function cmsStyle_color($type) {
    switch ($type) {
        case "back" : return 0; return "#33ff33";
        case "line" : return "#777777";
    }
}

function cmsStyle_saveColorData($colorData) {
    $res = "";
    $res = $colorData[colorId]."|".$colorData[colorBlend]."|".$colorData[colorSaturtion];
    return $res;
}


function cmsStyle_getColor($colorData) {
    if (is_array($colorData)) {
        $id = $colorData[colorId];
        $blend = $colorData[colorBlend];
        $saturation = $colorData[colorSaturation];
    } else {
        list($id,$blend,$saturation) = explode("|",$colorData);
    }
    
    switch ($id) {
        case "black" : 
            $col = 0;
            $fak = 12;
            $col_new= $col + ($saturation * $fak);
            $newColor = cmsStyle_rgb2rgb($col_new, $col_new, $col_new);
            // echo ("Schwarz $newColor <br />");
            return "#".$newColor;
            break;
         case "white" : 
            $col = 255;
            $fak = 12;
            $col_new= $col - ($saturation * $fak);
            $newColor = cmsStyle_rgb2rgb($col_new, $col_new, $col_new);
            // echo ("Weiß $newColor <br />");
            return "#".$newColor;
            break;
        case "trans" :
            return "transparent";
            break;
        case "none":
            return "inherhit";
            break;
        
    }
    
    // echo (" id = $id blend=$blend $saturation <br>");
    $colValue = cmsStyle_get(array("id"=>$id));
    if (is_array($colValue)) {
        $color = $colValue[color];
        // echo ("Color : $color");
        $rgb = cmsStyle_hex2rgb($color);
         if ($saturation) {
            $fak = 0.05;

            $red = $rgb[0];
            $green = $rgb[1];
            $blue = $rgb[2];
            $red_fak = $red*$fak;
            $green_fak = $green*$fak;
            $blue_fak = $blue*$fak;


            $red_new = floor($red + ($saturation*$red_fak));
            $green_new = floor($green + ($saturation*$green_fak));
            $blue_new = floor($blue + ($saturation*$blue_fak));
            $newColor = cmsStyle_rgb2rgb($red_new,$green_new,$blue_new);
            // echo (" newColor $newColor");
            return "#".$newColor;
        } else {
            return "#".$color;
        }


    }
    // echo ("colValue ($colorData) $colValue id = $id <br>");
    return 0;

}

function cmsStyle_colorCheck($hex) {
    if ($hex[0] == "#") $hex= substr($hex,1);
    if (strlen($hex) == 3) {
        $r = $hex[0];
        $g = $hex[1];
        $b = $hex[2];
        $hex = $r.$r.$g.$g.$b.$b;
    }
    return $hex;
}


function cmsStyle_hex2rgb($hex) {
    $hex = cmsStyle_colorCheck($hex);

    $rgb[0]=hexdec(substr($hex,0,2));
    $rgb[1]=hexdec(substr($hex,2,2));
    $rgb[2]=hexdec(substr($hex,4,2));
    return($rgb);
}

function  cmsStyle_rgb2rgb($red,$green,$blue) {
    if ($red > 255) $red = 255;
    if ($red < 0) $red = 0;
    $red_hex = dechex($red);
    if (strlen($red_hex)==1) $red_hex ="0".$red_hex;

    if ($green>255) $green = 255;
    if ($green < 0) $green = 0;
    $green_hex = dechex($green);
    if (strlen($green_hex)==1) $green_hex ="0".$green_hex;

    if ($blue>255) $blue = 255;
    if ($blue < 0) $blue = 0;
    $blue_hex = dechex($blue);
    if (strlen($blue_hex)==1) $blue_hex ="0".$blue_hex;

    $color = "".$red_hex.$green_hex.$blue_hex;
    return $color;
}



?>
