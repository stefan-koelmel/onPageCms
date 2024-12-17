<?php // charset:UTF-8

function cmsLocation_getList($filter,$sort,$out=null) {
  
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
        $sortQuery = "ORDER BY `name` ASC ";
    }

    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";            
            foreach($filter as $key => $value) {
                // echo ("Filter $key = '$value' <br>");
                if (is_integer($value) AND $value == 0) $value = "0";
                // if (int$value == 0) $value = "0";
                if ($value != "-") {
                    //echo ("Filter $key = '$value' <br>");
                    if ($filterQuery != "") $filterQuery .= " AND ";


                    switch ($value[0]) {
                        case ">" :
                            $filterQuery .= "`$key`$value";
                            break;
                        case "%" :
                            $filterQuery .= "`$key`LIKE '$value'";
                            break;

                        case "<" :
                            $filterQuery .= "`$key`$value";
                            break;
                        default :
                            switch ($key) {
                                case "category" :  // multiselect from category
                                    if ($value) {
                                        $filterQuery .= " (`$key` = '$value' OR `$key` LIKE  '%|$value|%') ";
                                    } else {
                                        $filterQuery .= "`$key`LIKE'0'";                                     
                                    }
                                    break;
                                case "search" :
                                    if ($value) {
                                        //$filterQuery .= " (`name` LIKE '%$value%' OR `street` LIKE  '%$value%') ";
                                        $filterQuery .= " `name` LIKE '%$value%' ";
                                    }
                                    break;
                                    
                                case "show" :
                                    $filterQuery .= "`$key`=$value";
                                    break;
                                default :
                                    $filterQuery .= "`$key`='$value'";

                            }
                    }

                } 
                
            }
            if ($filterQuery) $filterQuery = "WHERE ".$filterQuery;
        }

        switch ($filter) {
            case "new" :
                $filterQuery = "WHERE `new` = 1";
        }

       
    } else {
        $filterQuery = "WHERE `show` = 1";
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_location` ".$filterQuery." ".$sortQuery;
    if ($out == "out") echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($location = mysql_fetch_assoc($result)) {
        if ($location[data]) {
            $data = str2Array($location[data]);
            $location[data] = array();
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $location[data][$key] = php_unclearStr($value);
                }
            }
        }
        $res[] = $location;

    }
    return $res;
}

function cmsLocation_selectLocation_auto($code,$dataName,$showData,$filter,$sort) {
    global $cmsName,$cmsVersion;
    
    $url = "/cms_".$cmsVersion."/getData/location.php";
    $url .= "?cmsVersion=$cmsVersion&cmsName=$cmsName";

    $class = "cmsEditLocationAuto";
    if (is_array($showData)) {
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "style"   : $style = $value; break;
                case "class"   : $class .= " ".$value; break;
                case "submit"  : break;
                case "empty" : break;
                case "content" : $locationName = $value; break;
                case "showName" : $locationName = $value; break;
                case "type" : break;
                default :
                    echo ("showData location_autoComplete $key = $value <br>");
            }
        }
    }
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "mainCat" : $url.= "&mainCat=$value"; break;
                case "show"    :
                    if ($value!=1) $url .= "&show=0";
                    else $url.= "&show=1";
                    break;
                default :
                    echo ("filter location_autoComplete $key = $value <br>");
            }
        }
    }


    if (is_string($code) AND strlen($code) AND !$locationName) {
        $locationName = $code;
    }


    if ($code > 0) {
        if (!$locationName) {
            echo ("Location Id = $code <br>");
            $locationData = cmsLocation_get(array("id"=>$code));
            if (is_array($locationData)) {
                $locationName = $locationData[name];
                // show_array($locationData);
                echo ("ShowName = $locationName <br>");
            }
        }
    } 

    

    $str = "$url<br>";
    $str = "";
    $submit = "";
    if ($showData[submit]) $submit .= "onChange='submit()'";
    $str .= "<div id='locationGetUrl' style='display:none;'>$url</div>";
    // echo ("input type='text' class='$class' $submit style='$style' name='$dataName' id='queryLocation' url='$url' value='$code'<br>");
    $str .= "<input type='text' class='$class' $submit style='$style' name='$dataName' id='queryLocation' value='$locationName' />";

    return $str;

}

function cmsLocation_selectLocation($code,$dataName,$showData,$filter,$sort) {
    $companyList = cmsLocation_getList($filter,$sort);

    $str = "";

    $empty ="Bitte wählen";
    if ($showData["empty"]) $empty = $showData["empty"];

    $str.= "<select name='$dataName' class='cmsSelectType' ";
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $str.= "style='min-width:200px;' value='$code' >";

    if ($empty) {
        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";
        $str.= ">$empty</option>";
    }

    for($i=0;$i<count($companyList);$i++) {
        $companyId = $companyList[$i][id];
        $companyName = $companyList[$i][name];
         $str.= "<option value='$companyId'";
         if ($code == $companyId)  $str.= " selected='1' ";
         $str.= ">$companyName</option>";
    }
    $str.= "</select>";
    return $str;


}

function cmsLocation_getById($locationId,$output="") {
    $locationData=cmsLocation_get(array("id"=>$locationId),$output);
    return $locationData;
}




function cmsLocation_getByName($locationName,$mainLocation) {

    $filterQuery = "WHERE `name` LIKE '$locationName'";
    if ($mainLocation) $filterQuery .= " AND `mainCat` = $mainLocation";
    $sortQuery = "";

    // SELECT * FROM `klappeAuf_cms_location` WHERE `name` LIKE 'Lesung' AND `mainCat` = 1
    //SELECT * FROM `klappeAuf_cms_location` WHERE `name` LIKE `Lesung` AND `mainCat` = 1


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_location` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return 0;
    }
    $locationData =  mysql_fetch_assoc($result);
    $locationData[data] = str2Array($locationData[data]);

    return $locationData;
}

function cmsLocation_get($filter,$output="") {
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_location` ".$filterQuery." ".$sortQuery;
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
    $locationData = mysql_fetch_assoc($result);
    $locationData = php_clearQuery($locationData);
    
   // echo ("Output = $output <br>");
    switch ($output) {
        case "text" :
            $locationId = $locationData[id];
            $categoryList = cmsCategory_getListFromString($locationData[category],$output);
            $locationData[category] = $categoryList;

            $regionId = $locationData[region];
            if ($regionId) {
                $regionData = cmsCategory_getById($regionId);
                if (is_array($regionData)) $locationData[region] = $regionData[name];
            }

            // Search Dates
            $dateList = cmsDates_getList(array("location"=>$locationId,"show"=>1,"fromDate"=>date("Y-m-d")));
            if (is_array($dateList)) {
                if (count($dateList)) {
                    for ($i=0;$i<count($dateList);$i++) {
                        $dateData = $dateList[$i];
                        echo ("<h1>Termin im $locationData[name]</h1>");
                        $dateInfo = array();
                        $dateInfo[id] = $dateData[id];
                        $dateInfo[name] = $dateData[name];
                        $dateInfo[subName] = $dateData[subName];
                        $dateInfo[info] = $dateData[info];
                        $dateInfo[date] = $dateData[date];
                        $dateInfo[time] = $dateData[time];
                        $dateInfo[image] = $dateData[image];
                        $location_dateList[$dateInfo[date]] = $dateInfo;

                        $dateLink = $dateData[link];
                        if ($dateLink) {
                            $linkList = explode("|", $dateLink);
                            for ($j=0;$j<count($linkList);$j++) {
                                //echo ("Linki = $linkList[$j]<br>");
                                if (substr($linkList[$j],0,5)=="date:") {
                                    $linkedDates = substr($linkList[$j],5);
                                    $dateIds = explode(",",$linkedDates);
                                    for ($k=0;$k<count($dateIds);$k++) {
                                        $dateData = cmsDates_getById($dateIds[$k]);
                                        $dateDate = $dateData[date];
                                        if ($dateDate >= date("Y-m-d")) {
                                            $dateInfo = array();
                                            $dateInfo[id] = $dateData[id];
                                            $dateInfo[name] = $dateData[name];
                                            $dateInfo[subName] = $dateData[subName];
                                            $dateInfo[info] = $dateData[info];
                                            $dateInfo[date] = $dateData[date];
                                            $dateInfo[time] = $dateData[time];
                                            $dateInfo[image] = $dateData[image];
                                            $location_dateList[$dateInfo[date]] = $dateInfo;
                                        }
                                    }
                                    // echo ("Linked Dates = $linkedDates<br>");
                                }
                            }
                        }


                        $dateInfo[link] = $dateData[link];

                        
                       //  show_array($dateData);
                    }
                    ksort($location_dateList);
                    $locationData[dateList] = $location_dateList;
                }
            }

            // show_array($categoryList);
            break;
    }

    return $locationData;
}


function cmsLocation_existName($locationName,$category) {
    echo ("cmsLocation_existName($locationName,$category)<br>");
    $filterQuery = "WHERE `name` LIKE '$locationName'";
    if ($category) $filterQuery .= " AND `category` = $category";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_location` ".$filterQuery." ".$sortQuery;
    echo ($query."<br>");
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}


function cmsLocation_existID($locationId,$category) {

    $filterQuery = "WHERE `id` = $locationId";
    if ($category) $filterQuery .= " AND `$category` = $category";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_location` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}

function cmsLocation_save($data) {
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cmsLocation_getById($id);
        if ($existData) {
            return cmsLocation_update($data,$existData);
        }
    }
    // echo ("Not Found - no Id <br> ");
   
    $name = php_clearStr($data[name]);
    $category = $data[category];
    $existData = cmsLocation_get(array("name"=>$name,"category"=>$data[category]));
    if (is_array($existData)) {
        echo ("Found with Name $name <br>");
        $data[id] = $existData[id];
        return cmsLocation_update($data,$existData);
    }

    // a:1:{s:9:"imageFile";s:16:"AlterBrauhof.jpg";}

    // echo ("not Found with Name / Id<br>");

    $query = "";
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                $value = php_clearStr($value);
        }
        if ($query != "" ) $query.= ", ";
        $query.= "`$key`='$value'";

    }
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_location` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in cmsLocation_save $query <br>";
        return 0;
    }
    $locationId = mysql_insert_id();
    return $locationId;
}

function cmsLocation_update($data,$existData=array()) {
    $query = "";
    $id = $data[id];
    foreach ($data as $key => $value ) {
        if ($value != $existData[$key]) { 
            switch ($key) {
                case "data" : break;
                default :
                    $value = php_clearStr($value);
            }        

            if ($value AND $key != "id") {
                if ($query != "" ) $query.= ", ";
                $query.= "`$key`='$value'";
            }
        }
    }
    if ($query == "") {
        echo ("No Change<br>");
        return 1;
    }
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_location` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}

?>
