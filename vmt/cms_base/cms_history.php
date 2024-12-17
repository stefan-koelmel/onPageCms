<?php


function cmsHistory_set($pageData) {
    
    $historyCount = $GLOBALS[cmsSettings][history];
    if (!$historyCount) return 0;
    
    $aktUrl = cmsHistory_aktUrl();
    
    $paraPos = strpos($aktUrl,"?");
    if ($paraPos) {
        $parameter = substr($aktUrl,$paraPos+1);
        $paraSplit = explode("&",$parameter);
        $paraList = array();
        for($i=0;$i<count($paraSplit);$i++) {
            list($paraKey,$paraValue) = explode("=",$paraSplit[$i]);
            $paraList[$paraKey]=$paraValue;
        }
        $aktUrl = substr($aktUrl,0,$paraPos);
        // echo ("Page = '$aktUrl' para='$parameter'<br>");


        if ($pageData[dynamic]) {
            $addUrl = "";
            $dynamicData = $pageData[data];
            // echo ("Dynamic PAGE $dynamicData ".count($paraList)." <br>");

            $dynamic_1_type = $dynamicData[dataSource];
            if ($paraList[$dynamic_1_type]) {
                if ($addUrl) $addUrl.= "&";
                else $addUrl .= "?";
                $addUrl .= $dynamic_1_type."=".$paraList[$dynamic_1_type];
                unset($paraList[$dynamic_1_type]);
            }

            $dynamic_2_on = $dynamicData[dynamic2];
            if ($dynamic_2_on) {
                $dynamic_2_type = $dynamicData[dataSource2];
                if ($paraList[$dynamic_2_type]) {
                    if ($addUrl) $addUrl.= "&";
                    else $addUrl .= "?";
                    $addUrl .= $dynamic_2_type."=".$paraList[$dynamic_2_type];
                    unset($paraList[$dynamic_2_type]);
                }
            }
            // echo ("New addUrl = '$addUrl' ".count($paraList)." <br />");
            $aktUrl .= $addUrl;

        }



        foreach ($paraList as $key => $value ) {





            //echo ("$key = $value | ");
        }
        //echo ("<br>");
    } else {
        $paraList = array();
    }
    // show_array($GLOBALS[cmsSettings]);
    
    // $_SESSION[lastPages] = array();
  
    if (!is_array($_SESSION[lastPages])) $_SESSION[lastPages] = array();
    $anz = count($_SESSION[lastPages]);
    
    // echo ("Add Page to History '$aktUrl' anz = $anz / $historyCount -Title = $pageData[title] <br>");
   //  show_array($pageData);
    
    $setData = array();
    $setData[title] = $pageData[title];
    $setData[mainPage] = $pageData[mainPage];
    $setData[id] = $pageData[id];
    $setData[imageId] = $pageData[imageId];
    $setData[para] = $paraList;
    
    
    $_SESSION[lastPages][$aktUrl] = $setData; // $setData;
    $anz = count($_SESSION[lastPages]);
   
    if ($anz > $historyCount) {
        // echo ("remove $anz $historyCount ".($anz-$historyCount)."<br>");
        $removeCount = $anz-$historyCount;
        $removed = 0;
        foreach ($_SESSION[lastPages] as $key => $value) {
            if ($removed < $removeCount) {
                $removed++;
                unset($_SESSION[lastPages][$key]);   
                // echo ("Remove $key $removed inh = '".$_SESSION[lastPages][$key]."'<br>");
                             
            }
            
        }
        $anz = count($_SESSION[lastPages]);
       //  echo ("after Remove $anz $historyCount ".($anz-$historyCount)."<br>");
        
    }
    return 1;    
}


function cmsHistory_aktUrl() {
    
    $allUrl = $_SERVER[REQUEST_URI];
    // echo ("Set History $allUrl <br>");
    $folderList = explode("/",$allUrl);
    if (count($folderList)>1 AND strpos($allUrl,".php")) {
        $maxBack = $historyCount;
        $aktUrl = $folderList[count($folderList)-1];
    }
    //echo ("--> $aktUrl <br>");
    return $aktUrl;
}


function cmsHistory_show($pageData,$historyCount,$frameWidth) {
    $historyCount = $GLOBALS[cmsSettings][history];
    if (!$historyCount) return 0;
    
    div_start("history_title");
    echo ("<a href='#' class='historyButton'></a>");
    echo ("Verlauf");
    div_end("history_title");
    // echo ("<a href='#' class='favoButton favoActiveButton'></a><br />");
   //  echo ("<img src='../cms_base/cmsImages/iconSet-16-100.png' /><br />");
   // div_start("history","width:".$frameWidth."px;background-color:#f9f;");
    
    $aktUrl = cms_page_goPage(); // cmsHistory_aktUrl();
    
    // echo ("<h1>$aktUrl</h1>");
  
    
    $anz = count($_SESSION[lastPages]);
    $showList = array();
    for ($i=0;$i<count($anz);$i++) {
        $showList[] = array();
    }
    
    $getBackList = 1;
    $backDelimiter = " - ";
    
    $showArray = array_reverse($_SESSION[lastPages]);
    
    $nr = count($showArray);
    $icon = 0;
    foreach ($showArray as $url => $data) {
         $pos = strpos($url,"?");
         $addArray = array();
         if ($pos) {
             $addStr = substr($url,$pos+1);
             //$url = substr($url,0,$pos);

             // $pos = strpos($pageName_pageId,".php");
             //if ($pos) $pageName_pageId = substr($pageName_pageId,0,$pos);

             $splitPara = explode("&",$addStr);
            
             for ($i=0;$i<count($splitPara);$i++) {
                 list($paraKey,$paraValue) = explode("=",$splitPara[$i]);
                 // echo ("$paraKey = $paraValue <br>");
                 $addArray[$paraKey] = $paraValue;
             }
             // show_array($addArray);
             // echo ("Has Para $url '$addStr' anz=".count($splitPara)." <br>");
         }




        $pageInfo = cms_page_getInfoBack($url,$addArray);
        $name = $pageInfo[name];
        $breadCrumb = $pageInfo[breadCrumb];
        $icon = $pageInfo[icon];
        $para = $data[para];

        $divName = "historyLine";

        if ($url == $aktUrl) {
            $divName .= " historyLineActive";
        }

        div_start($divName);
        
        div_start("historyIcon","float:left;");
        $imageId = 0;
        if ($icon) {
            if (intval($icon)) $imageId = $icon;
            else {
                $imageList = explode("|",$icon);
                if (count($imageList)>1) $imageId = $imageList[1];
            }
        }
        if ($imageId) {
            $imgData = cmsImage_getData_by_Id($imageId);
            $showData = array();
            $img = cmsImage_showImage($imgData, 30, $showData);
        } else {
            $img = "&nbsp;";
        }
            
        echo ($img);
        div_end("historyIcon");
        
        
        div_start("historyTitle");
        $paraStr = "";
        $urlAdd = "";
        if (is_array($para) AND count($para)) {
            foreach($para as $paraKey => $paraValue) {
                $paraStr .= "$paraKey=$paraValue | ";
                if ($urlAdd) $urlAdd .= "&$paraKey=$paraValue";
                else $urlAdd .= "?$paraKey=$paraValue";
            }
        }
        echo ("<a href='".$url.$urlAdd."' class='historyLink'>$name</a>");
         echo ("<br />");
        // echo ("$urlAdd --> ");
        echo ($breadCrumb);
        // echo ("<br />$url $urlAdd");
        div_end("historyTitle");
        
       
        
        
        
        if ($backStr) {
            div_start("historyBackList");
            echo ($backStr);
            div_end("historyBackList");
        }
        
        div_end($divName,"before");
        
        //show_array($showArray);
            
            
        $nr--;
    }
    
    
}
?>
