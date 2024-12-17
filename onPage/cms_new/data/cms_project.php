<?php // charset:UTF-8

function cmsProject_getList($filter,$sort,$out="normal") {
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
               // echo ("Filter $key = $value <br>");
                if ($key == "content") $key = "search";
                if ($value != "-") {
                    switch ($key) {

                        case "search" :
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= "(`name` LIKE '%$value%' OR `subName` LIKE '%$value%' )";
                            break;

                        case "searchText" :
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= "(`name` LIKE '%$value%' OR `subName` LIKE '%$value%' OR `info` LIKE '%$value%')";
                            break;


                        case "date" :
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= "(`fromDate` <= '$value' AND `toDate` >= '$value')";
                            break;
                    
                        case "category" :  // multiselect from category
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            if ($value) {
                                $filterQuery .= " (`$key` = '$value' OR `$key` LIKE  '%|$value|%') ";
                            } else {
                                $filterQuery .= "`$key`LIKE'0'";                                     
                            }
                            break;
                            
                        default :
                            $add = "";
                            if (substr($value,0,2)==">=") $add = "`$key` $value";
                            if (substr($value,0,1)==">") $add = "`$key` $value";
                            if (substr($value,0,2)=="<=") $add = "`$key` $value";
                            if (substr($value,0,2)=="!=") $add = "`$key` $value";


                            if ($filterQuery != "") $filterQuery .= " AND ";
                            if ($add) {
                                $filterQuery .= $add;
                            } else {
                                $like = 0;
                                switch ($key) {
                                    case "name" : $like = 1; break;
                                    case "subName" : $like = 1; break;
                                    case "info" : $like = 1; break;
                                }

                                if (is_string($value)) {
                                    if ($like) $filterQuery .= "`$key` LIKE '$value'";
                                    else $filterQuery .= "`$key` = '$value'";
                                }
                                else $filterQuery .= "`$key` = $value";
                            }

                    }
                }
            }
            $filterQuery = "WHERE ".$filterQuery;
        }

        switch ($filter) {
            case "new" :
                $filterQuery = "WHERE `new` = 1";
        }

       
    } else {
        $filterQuery = "WHERE `show` = 1";
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_project` ".$filterQuery." ".$sortQuery;
    if ($out == "out") echo("<h4>Query $query </h4>");
    if ($out == "query") {
        $query2 =  "SELECT `id` FROM `".$GLOBALS[cmsName]."_cms_project` ".$filterQuery." ".$sortQuery;
        $result2 = mysql_query($query2);
        $anz = mysql_num_rows($result2);
        // echo ("OUTPOT = $query anz = '$anz' <br>");

        return array("query"=>$query,"count"=>$anz);
    }
    // echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($project = mysql_fetch_assoc($result)) {
        $project = php_clearQuery($project);
//         if ($project[data]) {
//            $data = str2Array($project[data]);
//
//            if (is_array($data)) {
//                $project[data] = array();
//                foreach ($data as $key => $value) {
//                    $project[data][$key] = php_unclearStr($value);
//                }
//            }
//        }
//        foreach ($project as $key => $value) {
//            switch ($key) {
//                case "name"    : $project[$key] = php_unclearStr($value); break;
//                case "subName" : $project[$key] = php_unclearStr($value,1); break;
//                case "info"    : $project[$key] = php_unclearStr($value); break;
//                case "data"    :
//                    $data = str2Array($project[data]);
//                    if (is_array($data)) {
//                        $project[data] = array();
//                        foreach ($data as $key2 => $value2) {
//                            $project[data][$key2] = php_unclearStr($value2);
//                        }
//                    }
//                    break;
//            }
//        }
        $res[] = $project;

    }
    return $res;
}

function cmsProject_search($searchString,$searchText,$filter){
    if (!is_array($filter)) $filter = array();
    if ($searchText) $filter["searchText"] = $searchString;
    else $filter["search"] = $searchString;
    $out = "";
    $sort ="";
    $res = cmsProject_getList($filter,$sort,$out);
    return $res;
}

function cmsProject_getById($projectId) {
    $projectData = cmsProject_get(array("id"=>$projectId));
    return $projectData;   
}


function cmsProject_get($filter,$clearData=1) {
    $filterQuery = "";
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            if ($filterQuery == "") $filterQuery .= "WHERE ";
            else $filterQuery .= "AND ";
            switch ($key) {
                case "name" ; $type = "text"; break;
                case "info" ; $type = "text"; break;
                default:
                    $type = "normal";
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_project` ".$filterQuery." ".$sortQuery;
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
    $projectData =  mysql_fetch_assoc($result);
    foreach ($projectData as $key => $value) {
        if ($clearData) $projectData = php_clearQuery($projectData);
//        switch ($key) {
//            case "data" :
//                $data = str2Array($projectData[data]);
//                if (is_array($data)) {
//                    $projectData[data] = array();
//                    foreach ($data as $key2 => $value2) {
//                        $projectData[data][$key2] = php_unclearStr($value2);
//                    }
//                }
//                break;
//            default :
//                 if (is_string($value)) {
//                    $value = str_replace("'","&#039;", $value);
//                    $value = str_replace('"',"&#034;", $value);
//                    $projectData[$key] = $value;
//                 }
//        }
    }
    return $projectData;
}



function cmsProject_existID($id,$clearData=1) {
    $res = cmsProject_get(array("id"=>$id),$clearData);
    return $res;
}


function cmsProject_save($data,$clearData=1) {

    
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cmsProject_existID($id,$clearData);
        if (is_array($existData)) {
            // if (is_array($existData[data])) $existData[data] = array2Str($existData[data]);
            return cmsProject_update($data,$existData,$clearData);
        }
    }
    // echo ("Not Found - no Id <br> ");

//    $name = php_clearStr($data[name]);
//    $category = $data[category];
//    $existData = cmsProject_get(array("name"=>$name,"category"=>$data[category]));
//    if (is_array($existData)) {
//        // echo ("Found with Name <br>");
//        $data[id] = $existData[id];
//        return cmsProject_update($data,$existData);
//    }

    // a:1:{s:9:"imageFile";s:16:"AlterBrauhof.jpg";}

    // echo ("not Found with Name / Id<br>");

    $query = "";
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                if ($clearData) $value = php_clearStr($value);

        }
        if (!is_null($value)) {
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_project` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in cmsProject_save $query <br>";
        return 0;
    }
    return 1;
}

function cmsProject_update($data,$existData=array(),$clearData=1) {
    $query = "";
    $id = $data[id];
    
//    if ($data[sort]) {
//
//        if (!$data[highlight]) {
//            if ($data[sort]>= 10 AND $data[sort] <= 20) {
//                $newSort = ($data[sort]-10) / 2;
//                if ($newSort == 0) $newSort = 1;
//                if ($newSort > 5) $newSort = 5;
//                echo ("<h3>Change Sort from $data[sort] -> $newSort </h3>");
//                $data[highlight] = $newSort;
//                // $date[sort]
//            }
//
//        } else {
//            echo ("Highlight is set to '$data[highlight]' <br>");
//        }
//    } else echo ("Sort is not set sort = '$data[sort]' highlight =  '$data[highlight]' <br>");
        
    foreach ($data as $key => $value ) {
        
         switch ($key) {                
           case "sort" : 
                $newValue = 15;
                if ($value > 20) $newValue = 20;
                if ($value < 1) $newValue = 1;
                $value = $newValue;
                break;
            case "highlight" : 
                $newValue = 3;
                $newValue = intval($value);
                if ($newValue > 5) $newValue = 5;
                if ($newValue < 1) $newValue = 1;
                if ($value != $newValue) echo ("change value old='$value' new='$newValue' <br>");
                
                $value = $newValue;
                break;
           
         }
                    
        
        if ($value != $existData[$key]) { 
            switch ($key) {
                case "data" : break;               
                
                default :
                    if ($clearData) $value = php_clearStr($value);
            }
            
            if (!is_null($value) AND $key != "id") {
                echo ("$id -- change data in $key = " );
                if (is_string($value)) {
                    echo ("<br>");                
                    echo ("new : $value<br>");
                    echo ("old : $existData[$key]<br>");
                }
                else echo ($value."' was '$existData[$key]'<br>");
                if ($query != "" ) $query.= ", ";
                $query.= "`$key`='$value'";
            }
        }        
    }
    
    if ($query == "") {
        echo ("No Change in $id clear=$clearData <br>");
        return 1;
    }
    echo ("change in $id <br>&nbsp;<br>");
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_project` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}

function cmsProject_selectProject($code,$dataName,$showData,$filter,$sort) {
    // echo ("function cmsCategory_selectCategory($code,$dataName,$showData,$filter,$sort) {<br>");
    $projectList = cmsProject_getList($filter,$sort);

    $str = "";
    $emptyStr = "Artikel wählen";
    if ($showData["empty"]) {
        $emptyStr = $showData["empty"];
    }

    $editStyle = "min-width:200px;";
    if ($showData["style"]) $editStyle .= $showData[style];

    $editClass = "cmsSelectType";
    if ($showData["class"]) $editClass.= " ".$showData["class"];

    $disabled = "";
    if ($showData[disabled]) $disabled = "disabled='disabled'";
    $readonly = "";
    if ($showData[readonly]) $readonly = "readonly='readonly'";


    // echo ("EditStyle ='$editStyle'<br>");
    // echo ("EditClass ='$editClass'<br>");

    $str.= "<select name='$dataName' class='$editClass'  style='$editStyle' ";
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $str .= "value='$code' $disabled $readonly >";

    if ($emptyStr) {
        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";
        $str.= ">$emptyStr</option>";
    }


    for($i=0;$i<count($projectList);$i++) {
        $articleId = $projectList[$i][id];
        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        $articleName = $projectList[$i][$outValue];
        $str.= "<option value='$articleId'";
        if ($code == $articleId)  $str.= " selected='1' ";
        $str.= ">$articleName</option>";
    }
    $str.= "</select>";
    return $str;
}

?>
