<?php



function cms_titleLine($pageData,$frameWidth) {
    
    
    $show_breadCrumb = $pageData[breadcrumb];
    if ($show_breadCrumb) {
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/cms_breadCrumb.php");
    }
    $show_bookmarks = session::get("cmsSettings,bookmarks");
    $show_historyCount = session::get("cmsSettings,history");
   
    $userLevel = session::get("userLevel");
    
    $reload = array();
    $reload[url] = "index.php";
    $reload[wait] = 10;
    $relaod[cancel] = 1;
    
    // out::errorBox("ICH BIN EIN FEHLER !!",$reload);
    
    $reload[wait] = 10;
    $reload[url] = 0;
    
    
//    $actPage = page::actPage();
//    $lastPage = page::lastPage();
//    echo ("AKTUELLE SEITE ist $actPage / $lastPage <br>");
    
    // $groupList = page::groupList();
    // echo ("GROUPLIST = $groupList <br>");
    
    
    
//    foreach ($backList as $key => $value) {
//        echo ("backList $key => $value[mainPage] <br>");
//    }
    // out::InfoBox("ICH STARTE NEU in  !!",$reload);
    
   
    
//    $show_bookmarks = $GLOBALS[cmsSettings][bookmarks];    
//    $show_historyCount = $GLOBALS[cmsSettings][history];
    $show_history = 0;
    if ($show_historyCount) $show_history = 1;
    $userLevel = user::userLevel();
    if (! $userLevel) {
        // $show_history = 0;
        $show_bookmarks = 0;
    }
    
    $show_titleLine = 0;
    
    if ($show_bookmarks) $show_titleLine = 1;
    if ($show_history) $show_titleLine = 1;
    if ($show_breadCrumb) $show_titleLine = 1;
    
    
    
    if ($show_titleLine == 0) {
        return 0;
    }
    
    div_start("titleLine");
    
    div_start("titleLineBack titleLineBack_close");
    echo ("&nbsp;");
    div_end("titleLineBack titleLineBack_close");
    
    
    if ($show_history) {
        $frameWidth = $frameWidth - 30;
        div_start("titleLine_history");
        echo ("<a href='#' class='historyButton'></a>");
        div_end("titleLine_history");
    } else $frameWidth = $frameWidth - 6;
    
    if ($show_breadCrumb) {
        $breadCrumbWidth = $frameWidth;
        if ($show_bookmarks) $breadCrumbWidth = $breadCrumbWidth - 25 - 10;
        div_start("titleLine_breadCrumb");
        cms_page_breadcrumb($pageData,$breadCrumbWidth);
        div_end("titleLine_breadCrumb");
    }
    
    $userLevel = session::get(userLevel);
    if ($show_bookmarks AND $userLevel) {
        cms_TitleLine_bookmarks();       
    }
    
    
    
    //cms_page_breadcrumb($pageData);
    
    
   
    
    if ($show_history) {
        div_start("history_box history_box_hidden");
        cmsHistory_show($pageData,$show_historyCount,$frameWidth+20);
       
        div_end("history_box history_box_hidden");
    }
    div_end ("titleLine","before");        
}


function cms_TitleLine_bookmarks() {
    $activePage = page::actPage(); // cmsHistory_aktUrl();
    
//    $userId = session::get(userId); // _SESSION[userId];
//    //echo ("<h1>Actic $activePage / $userId </h1>");
//    // if (!function_exists("cmsUserData_bookmarks_state")) return 0;
//    $state = cmsUserData_bookmarks_state($userId, $activePage);
    $state = user::bookmark_isBookMark($activePage);
    
    div_start("titleLine_bookmarks");
    
    div_start("bookmarkFrame");
    
    $class = "bookmarkButton";
    if ($state) $class .= " bookmarkActiveButton";
    $pageInfo = cms_page_getInfoBack($activePage);
    //show_array($pageInfo);
    $breadCrumb = $pageInfo[breadCrumb];
    
    if ($state) {
        $title = array("dt"=>"Favorit entfernen","en"=>"remove Bookmark");
        $title = lg::lgStr($title);        
    } else {
        $title = array("dt"=>"Favorit setzen","en"=>"add Bookmark");
        $title = lg::lgStr($title);  
    }
    
    
    
    $setPage = str_replace(array("?","&"), "|",$activePage);
    $setPage = str_replace("=","-", $setPage);
    // echo ("AvtivePage = $setPage $breadCrumb<br>");
    echo ("<a href='#' class='$class' title='$title'></a>");
    echo ("<div class='bookmarkDropdown'>");
    // echo ("<a href='#' class='bookmarkList' >^</a>");
    echo ("</div>");
    
    echo ("<div class ='hiddenBookmark'>$activePage</div>");// ) href='$activePage'
    
    
//    echo ("<div class='hiddenData'>");
//    echo ("<a class ='hiddenBookmark' href='$activePage' title='$breadCrumb' id='$userId' name='$state'>bookmark</a>");
//    echo ("</div>");
   
    div_end("bookmarkFrame");
    
    $divName = "bookmark_frame bookmark_frame_hidden";
    div_start($divName);
    //  echo ("$activePage<br>");
    $bookMarkList = user::bookmark_List(); // cmsUserData_bookmarkList($userId);
    cms_titleLine_bookmarkList($userId,$bookMarkList);
    div_end($divName);
    div_end("titleLine_bookmarks");
    
    
    
}

function cms_titleLine_bookmarkList($userId,$bookMarkList) {
    if (!is_array($bookMarkList)) {
        $bookMarkList = user::bookmark_List();
    }

    div_start("history_title");
    echo ("<a href='#' class='bookMarkButton'></a>");
    echo ("Lesezeichen");
    div_end("history_title");
    
    if (is_array($bookMarkList) and count($bookMarkList)) {
        foreach ($bookMarkList as $nr => $bookMarkValue ) {
            $url = $bookMarkValue[url];
            $pageName = $bookMarkValue[name];
            
            if (!$pageName) {
                list($pageName,$addUrl) = explode(".php",$url);
                if ($addUrl[0]=="?") $addUrl = substr($addUrl,1);
                if ($addUrl) {  
                    echo ("$url --> $pageName $addUrl <br>");
                    continue;
                }
            }
            
            $pageData = page::data_byName($pageName);
            if (!is_array($pageData)) {
                // echo ("no PageData Found for $pageName <br>");
                continue;
            }
            
            $pageInfo = page::infoBack($pageName);
            
//            
//            
//            
//            // echo ("url = '$url' <br>");
//            $urlList = explode("|",$url);
//            $addUrl = "";
//            if (count($urlList)>1) {
//                $addUrl = "";
//                $addArray = array();
//                for ($a=1;$a<count($urlList);$a++) {
//                    if ($addUrl) $addUrl .= "&";
//                    else $addUrl .= "?";
//                    list($urlKey,$urlValue) = explode("-",$urlList[$a]);
//                    $addArray[$urlKey]= $urlValue;
//                    
//                    $addUrl .= str_replace("-","=", $urlList[$a]);
//                }
//                // echo ("Before $url <br>");
//                $url = $urlList[0];
//                // echo ("ADD DATA = $url $addUrl <br>");
//                
//                
//                $pageInfo = cms_page_getInfoBack($url,$addArray);
//                // show_array($pageInfo);
//                
//            } else {
//                
//                $pageInfo = cms_page_getInfoBack($url);
//                // echo ("$url $pageInfo <vbr>");
//            }
            if (!is_array($pageInfo)) continue;
            
            $name = $pageInfo[name];
            $breadCrumb = $pageInfo[breadCrumb];
            $icon = $pageInfo[icon];
            $url = $pageInfo[url];
            $name = lg::lgStr($name);


            $divNameLine = "bookmarkLine";

            div_start($divNameLine);



            div_start("bookmarkIcon");
            if ($icon) {
                if (intval($icon)) $imageId = $icon;
                else {
                    $imageList = explode("|",$icon);
                    if (count($imageList)>1) $imageId = $imageList[1];
                }


                $imgData = cmsImage_getData_by_Id($imageId);
                $showData = array();
                $img = cmsImage_showImage($imgData, 28, $showData);
            } else {
                $img = "&nbsp;";
            }

            // echo ("$icon - $imageId <br>");

            echo ($img);
            div_end("bookmarkIcon");

            div_start("bookmarkTitle");
            echo ("<a href='$url' class='bookmarkLink'>");
            echo ($name);
            echo ("</a>");
            echo ("<br />");
            echo ("url = $url <br>");
            echo ($breadCrumb);

            div_end("bookmarkTitle");

            div_end($divNameLine,"before");
            
        }     
    } else { // keine Lesezeichen
        echo ("Keine Lesezeichen vorhanden");
    }
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
