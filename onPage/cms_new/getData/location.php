<?php // charset:UTF-8
    //header('Content-Type: text/html; charset=UTF-8');
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

   // $query = utf8_decode($query);

    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];
    $query = $_GET[query];

    $show = 0;
    
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php")) {        
        include($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php");
    } else {
        include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    }

    $getQuery .= "Select * FROM `".$cmsName."_cms_location` WHERE ";
    if ($show) $getQuery .= "`show` = 1 AND ";
    $getQuery .= "`name` like '%$query%' ";
    if ($mainCat) $getQuery .= " AND `mainCat` = $mainCat ";
    $result = mysql_query($getQuery);

    
    $counter='0';
    echo "{";
    echo "query:'$query',";
//    echo ("getQuery:'$getQuery',");
//    if ($result) {
//        $count = mysql_num_rows($result);
//        echo ("ok:'1',anzahl:'".$count."',");
//    } else {
//        echo ("ok:'false',");
//    }
    echo "suggestions:[";
    //echo "'info:$cmsName','info:$cmsVersion','info:$mainCat',";



    $res = '';
    $counter =0;
    
    $addQuery = 0;
    if ($addQuery) {
        echo "'Anfrage = -$query- '";
        $counter = 1;
    }

    WHILE ($category = mysql_fetch_assoc($result)) {

            $counter++;
            if ($counter > 1) {
                echo ",";
            }
            $name=$category["name"];
            //$name = str_replace("&#180;", "&acute;", $name);
            // $name = utf8_decode($name);
          // $name = iconv($name,"UTF-8","ISO-8859-15");
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
