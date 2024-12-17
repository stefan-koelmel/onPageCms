<?php // charset:UTF-8
function cmsFaq_getList($filter,$sort=null,$out=null) {
    $lg = $_SESSION[lg];
    if (!$lg) {
        // foreach ($_SESSION as $key => $value) echo("$key => $value <br>");
        $lg = "dt";
        $_SESSION[lg] = $lg;
    }
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
                                $filterQuery .= "`head_$lg` LIKE '%$value%' " ;
                                break;
                                
                            case "searchText" : 
                                if ($filterQuery != "") $filterQuery .= " AND ";
                                $filterQuery .= "(`head_$lg` LIKE '%$value%' OR `text_$lg` LIKE '%$value%')" ;
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


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_faq` ".$filterQuery." ".$sortQuery;
    if ($out == "out") echo ("Query $query <br>");
    
    $result = mysql_query($query);
    $res = array();
    if (!$result) {
        echo ("Error in Query = $query <br>");
    } else {
    
        while ($faq = mysql_fetch_assoc($result)) {

            $faqLg = array();
            $faqLg[id] = $faq[id];
            $faqLg[cat] = $faq[cat];
            $faqLg[sort] = $faq[sort];
            $faqLg[head] = $faq[head_dt];
            $faqLg[text] = $faq[text_dt];

            $res[] = $faqLg;

        }
    }
    return $res;
}


function cmsFaq_search($searchStr,$searchText,$filter=array()) {
    if (!is_array($filter)) $filter = array();
    if ($searchText) $filter[searchText] = $searchStr;
    else $filter["search"] = $searchStr;
    $out = "";
    $sort ="";
    
    $res = cmsFaq_getList($filter,$sort,$out);
    return $res;
    
    
}

function cmsFaq_get($filter,$out=null) {
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_faq` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
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
    $faqData = mysql_fetch_assoc($result);
    $faqData = php_clearQuery($faqData);
    
    switch($out) {
        case "css" :
            $css = array();
            foreach ($faqData as $key => $value) {  
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
            $faqData = $css;
            break;
    } 
    
    return $faqData;
}





function cmsFaq_save($data) {
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }
    
    
    

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cms_user_getById($id);
        if ($existData) {
            return cmsFaq_update($data);
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
    $query = "INSERT `".$GLOBALS[cmsName]."_cms_faq` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in cmsFaq_save $query <br>";
        return 0;
    }
    $insertId = mysql_insert_id();
    return $insertId;
}

function cmsFaq_update($data) {
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
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_faq` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}



?>
