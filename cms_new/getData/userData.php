<?php
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];

    $out = $_GET[out];

    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/help.php");
//    $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php";
//    // echo ("cmsFile = $cmsFile <br>");
//    include($cmsFile);
//
//    $out = $_GET[out];
    switch ($out) {
        case "setBookmark" :
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/class/session.php");
            session::init($cmsName);
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/class/cms.php");
            cms::init($cmsName, $cmsVersion);
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/class/user.php");
            $pageName = $_GET[pageName];
            
//            echo ("SET BOOKMARK for $pageName<br>");
//            $activ = user::bookmark_isBookMark($pageName);
//            echo ("ACTIV = $activ<br>");
//            
            $toggleResult = user::bookmark_toogle($pageName);
            echo ($toggleResult);
            // echo ("toggleResult = $toggleResult <br>");
            die();
            
            $userId = session::get(userId);
           
            // echo ("userId $userId pageName =$pageName <br>");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_userData.php");
            $res = cmsUserData_bookmarks_toggle($userId,$pageName);
            if ($res == 1) echo ("0");
            else echo ("1");
            return 0;
            
            $userId = $_GET[userId];
            $mode   = $_GET[mode];
            $url    = $_GET[url];
            $name   = $_GET[name];
            $breadCrumb = $_GET[breadCrumb];
            // echo ("Set Bookmark for $url \n UserId=$userId mode=$mode breadCrumb=$breadCrumb ");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_userData.php");
            
            switch ($mode) {
                case "toggle" :
                    $bookmarkId = cmsUserData_bookmarks_state($userId,$pageName);
                    break;
                case "0" : // DELTE BOOKMARK
                    $oldId = cmsUserData_bookmarks_state($userId,$url);
                    if ($oldId) $bookmarkId = $oldId;
                    else {
                        echo("0");
                        die();
                    }
                    break;
                case "1" : // SET BOOKMARK
                    $bookmarkId = 0;
                    break;
            }
            
            
            $bookmarkId = cmsUserData_bookmarks_state($userId,$url);
            
            if ($bookmarkId) {
                $res = cmsUserData_bookmarks_deleteBookmark($bookmarkId);    
                if ($res == 1) echo ("0");
                else echo ("1");
            } else {
                $res = cmsUserData_bookmarks_setBookmark($userId,$url,$name,$breadCrumb);
                if ($res == 1) echo ("1");
                else echo ("0");
            }
            break;

        case "bookmarkList" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_userData.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_titleLine.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_page.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_dynamicPage.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/pageStyles.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_image.php");
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_category.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_project.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_product.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_dates.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_company.php");


            $userId = $_GET[userId];
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/class/session.php");
            session::init($cmsName);
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/class/page.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/class/lg.php");
            
            $userId = session::get(userId);
            cms_titleLine_bookmarkList($userId,0);
            break;

            
    }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
