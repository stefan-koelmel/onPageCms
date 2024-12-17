<?php // charset:UTF-8
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];

    $out = $_GET[out];

    if (file_exists($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php")) {        
        include($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php");
    } else {
        include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    }

    $day = $_GET[day];
    if (strlen($day) < 2) $day = "0".$day;
    $month = $_GET[mon];
    if (strlen($month) < 2) $month = "0".$month;
    $year = $_GET[yea];
    if (strlen($year) == 2) {
        if ($year > 50) $year = "19".$year;
        else $year = "20"+$year;
    }

    
    switch ($out) {
        case "dropdown" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php");

            $dataName = $_GET[dataName];
            if (!$dataName) {
                echo ("NoDataName");
                die();
            }

            $code = $_GET[code];

            //  $location = 202;
            if ($_GET[location]) {
                $location = $_GET[location];
            }

            // $date = "2012-09-01";
            if ($_GET[date]) {
                $date = $_GET[date];
                list($y,$m,$d) = explode('-',$date);
                $time = mktime(0, 0, 0, $m, $d, $y);
                $startDate = $time - (60 * 24 * 60 * 60);
                $endDate   = $time + (60 * 24 * 60 * 60);

                echo ("Zeige Artikel im Zeitraum von  ".date("d.m.Y",$startDate)." bis ".date("d.m.Y",$endDate)."<br>");
                $startDate = date("Y-m-d",$startDate);
                // $endDate   = date("Y-m-d",$endDate);
            }

            $filter = array();
            if ($location) {
                $filter[location] = $location;
                 echo ("Zeige Artikel von Ort: $location<br>");
            }
            // if ($startDate) $filter[fromDate] = "<='$startDate'";
            // if ($endDate) $filter[toDate] = "<='$endDate'";

            $showData = array();
            $showData[style]= "width:100%";
            $showData[submit] =1;
           // $showData[out] = "id";
            $str = cmsArticles_selectArticles($code,$dataName, $showData, $filter, $sort);
            echo ($str);
            return $str;


        case "locData" :
            $location = utf8_decode($_GET[loc]);
            if ($location) {
                $getQuery = "Select * FROM `".$cmsName."_cms_location` WHERE `show` = 1 AND `name` like '%$location%' ";
                $result = mysql_query($getQuery);
                if ($result) {
                    $str = "location";
                    $anz = mysql_num_rows($result);
                    if ($anz == 1) {
                        $locationData =  mysql_fetch_assoc($result);                       
                    } else {
                        if ($anz>1) {
                            $getQuery = "Select * FROM `".$cmsName."_cms_location` WHERE `show` = 1 AND `name` like '$location%' ";
                            $result = mysql_query($getQuery);
                            $anz = mysql_num_rows($result);
                            if ($anz == 1) {
                                $locationData =  mysql_fetch_assoc($result);
                            } else {
                                $getQuery = "Select * FROM `".$cmsName."_cms_location` WHERE `show` = 1 AND `name`='$location' ";
                                $result = mysql_query($getQuery);
                                $anz = mysql_num_rows($result);
                                if ($anz == 1) {
                                    $locationData =  mysql_fetch_assoc($result);
                                } else {
                                    echo ("NotFound with more Info $anz<br>");
                                    echo ("$getQuery<br>");
                                }
                            }
                        } else {
                        
                        }
                        $str = "notFournd $anz";
                    }
                }

                if (is_array($locationData)) {
                    foreach ($locationData as $key => $value ) {
                        if ($str != "") $str.="|";
                        $str .= "$key#$value";
                    }
                }
            }
            echo ($str);
            die();
    }



    $datum = $day.".".$month.".".$year;
    $date = $year."-".$month."-".$day;
    if ($out=="list") {
        echo ("{");
        echo ("'Datum' = '$datum'");
    } else {
        echo ("Datum: $datum ($date) <br>");
    }
    
    $category = $_GET[cat];
    if ($out=="list") {
        
        echo (",'category' = '$category'");
    } else {
        echo ("Kategorie : $category ($categoryId) <br>");
    }


    

    $location = $_GET[loc];
    if ($location) {
        $getQuery .= "Select * FROM `".$cmsName."_cms_location` WHERE `show` = 1 AND `name` like '$location%' ";
        $result = mysql_query($getQuery);
        if ($result) {
            // echo("$getQuery -> $result \n");
            //            echo()
            $anz = mysql_num_rows($result);
            if ($anz == 1) {
                $locationData =  mysql_fetch_assoc($result);
                foreach ($locationData as $key => $value ) {
               //     if ($key == "id"
                   if ($out=="list") {
                       echo (",'$key'='$value'");
                   } else {
                       echo ("#$key ='$value' <br>");
                   }
                }
            }
        }
    }
    if ($out=="list") {
        echo ("}");
    } else {
        echo ("Ort : $location ($locationId) \n");
    }
    die();


    foreach($_GET as $key => $value) {
        echo ("$key = $value \n");
    }

    $getQuery .= "Select * FROM `".$cmsName."_cms_location` WHERE `show` = 1 ";
    $getQuery .= " AND `name` like '%$query%' ";
    if ($mainCat) $getQuery .= " AND `mainCat` = $mainCat ";
    $result = mysql_query($getQuery);

    
    $counter='0';
    echo "{";
    echo "query:'$query',";
    echo "suggestions:[";
    //echo "'info:$cmsName','info:$cmsVersion','info:$mainCat',";



    $res = '';
    WHILE ($category = mysql_fetch_assoc($result)) {

            $counter++;
            if ($counter > 1) {
                echo ",";
            }
            $name=$category["name"];
            echo "'$name'";
    }
    echo "],}";


    /*    if ($res == '') $res .= '["';
        else $res.= '","';
        $res.= $category[name];
    }
    $res.='"]';
    echo ($res);*/
?>
