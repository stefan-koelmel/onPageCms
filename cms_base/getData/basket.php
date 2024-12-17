<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    
    
    
    $mainCat = $_GET[mainCat];
    $mode    = $_GET[mode];
    $query   = $_GET[query];

    $type = $_GET[type];
    
    include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/data/cms_basket.php");

  
    switch ($mode) {
        case "showInfo" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms_contentType.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms_contentTypes/cmsType_basket.php");
            $contentData = array();
            $contentData[data] = array();
            $contentData[data][viewMode] = "info";
            $contentData[data][noDiv] = 1;


            $contentData[data][showItems] = 1;
            $contentData[data][showParts] = 0;
            $contentData[data][showValue] = 1;

            cmsType_basket($contentData,$frameWidth);

            // $basketValue = cmsBasket_getValue();
//            foreach ($basketValue as $key => $value) {
//                echo ("$key=$value <br>");
//            }
            break;
        case "showBasketItem" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms_contentType.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms_contentTypes/cmsType_basket.php");

            $showData = array();
            $showData[hideDiv] = 1;
            $basketItem = array();
            $basketItem[basketId] = $_GET[basketId];
            $basketItem[name]     = $_GET[name];
            $basketItem[vk]       = $_GET[value];
            $basketItem[shipping] = $_GET[shipping];
            $basketItem[anz]      = $_GET[anz];
            $basketItem[maxAdd]   = $_GET[maxAdd];

            $basketItem[shipping] = $_GET[shipping];
            
            
            if ($basketItem[basketId]) {
                list($dataSource,$dataId) = explode("_",$basketItem[basketId]);
                if ($dataSource) $basketItem[dataSource] = $dataSource;
                if ($dataId) $basketItem[dataId] = $dataId;
                // echo ("Data $dataSource / $dataId <br>");
            }
            
            
            $out = cmsType_basket_showItem($basketItem,$showData);
            echo ($out);
            break;



        case "add" :
            $basketId = $_GET[basketId];
            $count    = $_GET[count];
            $name     = $_GET[name];
            $value    = $_GET[value];
            $shipping = $_GET[shipping];

            $itemData = array();
            $itemData[basketId]  = $_GET[basketId];
            $itemData[amount] = $_GET[count];
            $itemData[name] = $_GET[name];
            $itemData[value] = $_GET[value];
            $itemData[shipping] = $_GET[shipping];
            $itemData[dataSource] = $_GET[dataSource];

            $res = cmsBasket_addItem($itemData);
            echo ($res);
            
            
            // echo ("Add = $basketId $count name=$name value=$value shipping=$shipping <br>");
            break;
            
        default :
            echo ("UNKOWN MODE '$mode' <br>");
            
        
    }
    

    


    /*    if ($res == '') $res .= '["';
        else $res.= '","';
        $res.= $category[name];
    }
    $res.='"]';
    echo ($res);*/
?>
