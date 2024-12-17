<?php

class cmsHistory {
    
    function cmsHistory($pageClass=0) {
        if (!is_object($pageClass)) return 0;
        $this->pageClass= $pageClass;        
        $this->init();        
    }
    
    function init() {
        if (!is_object($this->pageClass)) return 0;
        
        $this->historyCount = $this->pageClass->session_get("cmsSettings,history");
        
    }
    
    
    function historyCount() {
        if (!is_object($this->pageClass)) return 0;
        $historyCount = $this->pageClass->session_get("cmsSettings,history");
        return $historyCount;
    }
    
    
    function setHistory($pageData,$pageClass=0) {
        if (!$this->historyCount()) return 0;
        // History is on
        
        $aktPage = $this->actPage($pageData);
        $mainPage = $this->aktMainPage($aktPage);
        $parameter = $this->aktParameter($aktPage);
        
        $pageCode = $pageData[name];
        $dynamic = $pageData[dynamic];
        
        $backList = $this->getBackList("micro_start_more");
        
        
        
        
        
        if ($dynamic) {
//            $dynamicPageCode = $this->pageClass->page_getPageCode();
//            echo ("dynamic PageCode = $dynamicPageCode <br>");
//            
//            $pageGroupList = $this->pageClass->session_get("pageGroupList");
//            echo ("groupList $pageGroupList <br>");
//            if (is_array($pageGroupList)) {
//                foreach ($pageGroupList as $key => $value ) {
//                    echo ("$key => $value <br>");
//                    if (is_array($value[subNavi])) {
//                        foreach ($value[subNavi] as $key2 => $value2) {
//                            echo (" --> $key2 = $value2 <br>");      
//                            
//                             if (is_array($value2[subNavi])) {
//                                foreach ($value2[subNavi] as $key3 => $value3) {
//                                    echo (" --> --> $key3 = $value3 <br>");               
//                                }
//                            }
//                            
//                        }
//                    }
//                }
//            }
            
        }
        
//        $myPageList = $this->pageClass->session_get("pageList,".$mainPage);
//        foreach ($myPageList as $key => $value ) echo ("page $key = $value <br>");
//        echo ("$myPageList <br>");
        
        
        
       //  echo ("actPage = '$aktPage' $mainPage $parameter <br>");
        
//        foreach ($pageData as $key => $value ) {
//            echo ("page $key => $value <br>");
//        }
        
        
    }
    
    function page_getData_byId($pageId) {
        $pageName = $this->page_getName_byId($pageId);
        return $this->page_getData_byName($pageName);
    }
    
    function page_getData_byName($pageName) {
        return $this->pageClass->session_get("pageList,".$pageName);
    }
    
    function page_getName_byId($pageId) {
        return $this->pageClass->session_get("pageIdList,".$pageId);
    }
    
    function page_getBack_byId($pageId) {
        $pageName = $this->page_getName_byId($pageId);
        return $this->page_getBack_byName($pageName);
    }
    
    function page_getBack_byName($pageName) {
        return $this->pageClass->session_get("pageBackList,".$pageName);
    }
    
    function page_get_Back($pageId) {
        return $this->pageClass->session_get("pageBackList,pageId_".$pageId);
    }
    
    function page_get_Name($pageId) {
        return $this->pageClass->session_get("pageIdList,".$pageId);
    }
    
    
    function getBackList($pageName) {
        
        $backStr = $this->pageClass->page_getBack_byName($pageName);
        echo ("$backStr get By Name <br>");
        
        $pageData = $this->pageClass->page_getData_byName($pageName);
        $pageId = $pageData[id];
        $backStr = $this->page_getBack_byId($pageId);
        echo ("$backStr get By Id <br>");
        
      
        
        return $backStr;
        
    }
    
    
    function actPage($pageData){
        $allUrl = $_SERVER[REQUEST_URI];
        $folderList = explode("/",$allUrl);
        $count = count($folderList);
        if ($count > 1 AND strpos($allUrl,".php")) {
            $aktUrl = $folderList[count($folderList)-1];
        } else {
            $aktUrl = $allUrl;
            // echo ("F=".count($folderList)." $allUrl ");
        }
        return $aktUrl;
    }
    
    function aktMainPage($aktPage) {
        $mainPage = $aktPage;
        $end = strpos($aktPage,".php");
        if ($end) $mainPage = substr($aktPage,0,$end);
        return $mainPage;
        
    }
    function aktParameter($aktPage) {
        $parameter = array();
        $end = strpos($aktPage,".php");
        if (!$end) return $parameter;
        
        $paraString = substr($aktPage,$end+4);
        if ($paraString[0] == "?") $paraString = substr($paraString,1);
        
        if ($paraString) {
            $paras = explode("&",$paraString);
            foreach ($paras as $nr => $para) {
                list($paraKey,$paraValue) = explode("=",$para);
                $parameter[$paraKey] = $paraValue;
            }
        }
        return $parameter;
    }
    
    
    
}






function cmsHistory_set($pageData,$pageClass=0) {
    
    global $cmsName;
    if ($cmsName == "game") { 
        $historyClass = new cmsHistory($pageClass);
        $historyClass->setHistory($pageData);
    }
   
    //$cl = new page();
    // $cl->show_page();
    $historyCount = session::get("cmsSettings,history");
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



      
        //echo ("<br>");
    } else {
        $paraList = array();
    }
    // show_array($GLOBALS[cmsSettings]);
    
    // $_SESSION[lastPages] = array();
  
    $lastPages = session::get(lastPages);
    if (!is_array($lastPages)) $lastPages = array();
    $anz = count($lastPages);
    
    $setData = array();
    $setData[title] = $pageData[title];
    $setData[mainPage] = $pageData[mainPage];
    $setData[id] = $pageData[id];
    $setData[imageId] = $pageData[imageId];
    $setData[para] = $paraList;
    
    $lastPages[$aktUrl] = $setData;
    site_session_set("lastPages,".$aktUrl,$setData);
    
    
//    foreach ($lastPages as $key => $value) {
//        echo ("$key | ");
//    }
//    echo ("<br>");
    
    
    
    
    
    
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
        $aktUrl = $folderList[count($folderList)-1];
    } else {
        // echo ("F=".count($folderList)." $allUrl ");
    }
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
    
    
    if (!function_exists("cmsImage_getData_by_Id")) return 0;
    $aktUrl = cms_page_goPage(); // cmsHistory_aktUrl();
    
    $aktUrl = page::actPage();
    $lastPages = session::get("lastPages");
    
    $lastPages = page::lastPageList();
    $anz = count($lastPages);
    $showList = array();
    for ($i=0;$i<count($anz);$i++) {
        $showList[] = array();
    }
    
    $getBackList = 1;
    $backDelimiter = " - ";
    
    $showArray = array_reverse($lastPages);
    
    $nr = count($showArray);
    $icon = 0;
   
    foreach ($showArray as $pageCode => $data) {
        
        $pageData = page::data_byName($pageCode);
        
        if (!is_array($pageData)) {
            // echo "Not Found pageData for $pageCode <br>";
            continue;
        }
        
        
        
        
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




        $pageInfo = page::infoBack($pageCode); // cms_page_getInfoBack($pageData,$addArray);
        $name = $pageInfo[name];
        $url  = $pageInfo[url];
        $breadCrumb = $pageInfo[breadCrumb];
        $icon = $pageInfo[icon];
        $para = $data[para];
        
        
//        foreach ($pageInfo as $key => $value) {
//            echo ("backInfo = $key = $value <br>");
//            if (is_array($value)) {
//                foreach ($value as $k => $v ) {
//                    echo ("-> $k = $v <br>");
//                    if (is_array($v)) {
//                        foreach ($v as $k2 => $v2 ) {
//                            echo (" -> -> $k2 = $v2 <br>");
//                        }
//                    }
//                }
//            }
//        }
        

        $divName = "historyLine";

        if ($pageCode == $aktUrl) {
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
        // $name = $pageData[name];
        $title = lg::lgStr($pageData[title]);
        // echo ($title);
//        
//
//        
//        echo ("title='$title' <br>");
//        echo ("URL = '$url' <br>");
//        echo ("Breadcrumb = '$breadCrumb' <br>");
//        
        
        
        // echo ("url => $url AND data = $data");
//        $paraStr = "";
//        $urlAdd = "";
//        if (is_array($para) AND count($para)) {
//            foreach($para as $paraKey => $paraValue) {
//                $paraStr .= "$paraKey=$paraValue | ";
//                if ($urlAdd) $urlAdd .= "&$paraKey=$paraValue";
//                else $urlAdd .= "?$paraKey=$paraValue";
//            }
//        }
       echo ("<a href='".$url.$urlAdd."' class='historyLink'>$title</a>");
//         echo ("<br />");
//        // echo ("$urlAdd --> ");
//        echo ($breadCrumb);
//        // echo ("<br />$url $urlAdd");
        div_end("historyTitle");
        
       
        
        
        
        if ($breadCrumb) {
            div_start("historyBackList");
            echo ($breadCrumb);
            div_end("historyBackList");
        }
        
        div_end($divName,"before");
        
        //show_array($showArray);
            
            
        $nr--;
    }
    
    
}
?>
