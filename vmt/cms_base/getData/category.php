<?php // charset:UTF-8
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];
    $mode    = $_GET[mode];
    $query   = $_GET[query];

    $type = $_GET[type];
    
   
  
    if (!$type) $type = "autoComplete";
    switch ($type) {
        case "toggle" :
            // include CMS
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php");
            
            $filter = array("show"=>1);
            $mainCat = $_GET[mainCat];
            if ($mainCat) $filter[mainCat] = $mainCat;
            $sort = "name";
            $dataName = "saveData[subCategory__]";
            if ($_GET[dataName]) $dataName = $_GET[count];
            $showData = array();
            if ($_GET[count]) $showData[count] = $_GET[count];
            if ($_GET[width]) $showData[width] = $_GET[width];
            if ($_GET["class"]) $showData["class"] = $_GET["class"];
            $showData[dontMainFrame] = 1;

            $code = "";
            
            
            $out = cmsCategory_selectCategory_toogle($code, $dataName, $showData, $filter, $sort);
            echo ($out);
            break;
            
            
        case "autoComplete" :
            
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php")) {        
                include($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php");
            } else {
                include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
            }
            
            
            
            $query = utf8_decode($query);
            $counter='0';
            echo "{";
            echo "query:'$query',";
            echo "suggestions:[";
            //echo "'info:$cmsName','info:$cmsVersion','info:$mainCat',";

            $mode = "simple";
            //$mode = "onlySub";
            // $mode = "withSub";

            switch ($mode) {
                case "simple" :
                    $getQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 ";
                    $getQuery .= " AND `name` like '%$query%' ";
                    if ($mainCat) $getQuery .= " AND `mainCat` = $mainCat ";
                    $result = mysql_query($getQuery);
                    $res = '';
                    WHILE ($category = mysql_fetch_assoc($result)) {
                        $catId = $category[id];
                        $counter++;
                        if ($counter > 1) {
                            echo ",";
                        }
                        $name=$category["name"];
                        echo "'$name'";
                    }
                    echo "],}";
                    break;

                case "withSub" :
                    $getQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 ";
                    //$getQuery .= " AND `name` like '%$query%' ";
                    if ($mainCat) $getQuery .= " AND `mainCat` = $mainCat ";
                    $result = mysql_query($getQuery);


                    $lowerQuery = strtolower($query);

                    $res = '';
                    WHILE ($category = mysql_fetch_assoc($result)) {
                        $catId = $category[id];
                        $name=$category["name"];

                        $lowerName = strtolower($name);
                        $ofSet = strpos($lowerName,$lowerQuery);
                        if (is_integer($ofSet)) {
                            $counter++;
                            if ($counter > 1) echo ",";

                            $name=$category["name"];
                            echo "'$name'";
                        }

                        $subQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 AND `mainCat` = $catId ";
                        $subQuery .= " AND `name` like '%$query%' ";
                        $subResult = mysql_query($subQuery);
                        if ($subResult) {
                            while ($subCategory = mysql_fetch_assoc($subResult)) {
                                $counter++;
                                if ($counter > 1) echo ",";
                                $subName=$subCategory["name"];
                                echo "'$subName'";
                            }
                        }

                    }
                    echo "],}";
                    break;



                case "onlySub" :
                    
                    
                    $getQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 ";
                    //$getQuery .= " AND `name` like '%$query%' ";
                    if ($mainCat) $getQuery .= " AND `mainCat` = $mainCat ";
                    // echo ("Query = $getQuery<bR>");
                    $result = mysql_query($getQuery);

                    $res = '';
                    WHILE ($category = mysql_fetch_assoc($result)) {
                        $catId = $category[id];
                        $subQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 AND `mainCat` = $catId ";
                        $subQuery .= " AND `name` like '%$query%' ";

                        $subResult = mysql_query($subQuery);
                        if ($subResult) {
                            while ($subCategory = mysql_fetch_assoc($subResult)) {
                                $counter++;
                                if ($counter > 1) echo ",";
                                $subName=$subCategory["name"];
                                echo "'$subName'";
                            }
                        }
                    }
                    echo "],}";
                    break;
            }
    }


    


    /*    if ($res == '') $res .= '["';
        else $res.= '","';
        $res.= $category[name];
    }
    $res.='"]';
    echo ($res);*/
?>
