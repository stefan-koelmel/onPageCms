<?php // charset:UTF-8
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];

    $out = $_GET[out];

    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php";
    // echo ("cmsFile = $cmsFile <br>");
    include($cmsFile);

    $out = $_GET[out];
    switch ($out) {
        case "productImage" :
            $width=$_GET[width];
            $pxOff = strpos($width,"px");
            if ($pxOff) $width = substr($width,0,$pxOff);

            $height=$_GET[height];
            $pxOff = strpos($height,"px");
            if ($pxOff) $height = substr($height,0,$pxOff);
            
            $companyId = $_GET[companyId];
            $productQuery = "SELECT * FROM `".$cmsName."_cms_product` ";
            if ($companyId) $productQuery .= " WHERE `company` = $companyId ";

            $result = mysql_query($productQuery);
            if ($result) {
                $anz = mysql_num_rows($result);
                if ($anz > 0 ) {
                    $productList = array();
                    while ($product = mysql_fetch_assoc($result)) {
                        if ($product[image]) $productList[] = $product;
                    }
                    $anz = count($productList);
                }
                
                if ($anz == 0) {
//                    echo ("<div style='width:".$width."px;height:".$height."px;text-align:center' >");
//                    echo ("Kein Product vom Hersteller");
//                    echo("</div>");
                    echo ("notExist");
                    die();
                }

                
                if ($anz>1) {
                    $zufall = rand(0,$anz-1);
                    // echo ("zufall = $zufall <br>");
                    $product = $productList[$zufall];
                } else {
                    $product = $productList[0];
                    // echo "nur ein $anz Bild<br>";
                }

                $productName = $product[name];
                $productImage = $product[image];
               // echo ("Product $productName Image = $productImage <br>");

                $imageData = cmsImage_getData_by_Id($productImage);

                //$width = $width -10;
                //$height = $height - 10;
                if (is_array($imageData)) {
                    $showData = array();
                    $showData[frameWidth] = $width;
                    $showData[frameHeight] = $height;
                    $showData[vAlign] = "middle";
                    $showData[hAlign] = "center";

                    $showData[title] = $productName;
                    // show_array($imageData);
                    $imgStr = cmsImage_showImage($imageData,$width,$showData);
                    // echo ("$width x $height ");
                    //echo ("<div style='width:".$width."px;height:".$height."px;background-color:#f00;' >");

                    echo ($imgStr);
                    // echo("</div>");
                }
            } else {
                echo ("Error in $productQuery<br>");
            }

//            echo ("Bild ($width x $height)<br>");
//            echo ("von Company $companyId<br>");
//            echo ("$productQuery");
            break;

        default :
            echo ("unkown Output ($out)");
            show_array($_GET);



    }




    

    
?>
