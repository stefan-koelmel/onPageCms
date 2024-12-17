<?php // charset:UTF-8

function cms_admin_show($view,$frameWidth) {

    $border = 2;
    $padding = 10;
    $frameWidth = $frameWidth - (2*border) - (2*padding);
    $userLevel = $_SESSION[userLevel];
    if (!$userLevel ) {
        cms_admin_login($frameWidth);
        return 0;
    }

    if ($userLevel < 7) {
        cms_admin_NoRights($frameWidth);
        return 0;
    }


    // includ EditClass
    global $cmsName,$cmsVersion;
    $ownAdminPath = $_SERVER['DOCUMENT_ROOT']."/$cmsName/cms/admin/";
    if (!file_exists($ownAdminPath)) {
        // echo "Own Admin Path not exist $ownAdminPath <br>";

        $ownAdminPath = $_SERVER['DOCUMENT_ROOT']."/cms/admin/";
        if (!file_exists($ownAdminPath)) {
            echo "Own Admin Path not in Root exist $ownAdminPath <br>";
        }
    }
    // echo ("<h1> $ownAdminPath jdlskfjsdlkfjl</h1>");

    

    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_editClass.php");
    $ownPhpFile = $ownAdminPath."cms_admin_editClass_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_editClass();

    } else {
        $class = new cmsAdmin_editClass_base();
        // echo ("File $ownPhpFile not found <br>");
    }
   


    $offPhp = strpos($view,".php");
    if ($offPhp > 0 ) $view = substr($view,0,$offPhp);
    $pageInfo = $GLOBALS[pageInfo];
    $divName = "adminLayer $view";
    div_start($divName,"width:".$frameWidth."px;");
    global $cmsVersion;
    //echo ("<h1>View = $view </h1>");
    switch($view) {
        case "cmsLayout" : 
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_layout.php");
            cms_admin_cmsLayout($frameWidth);
            break;
        
        case "cmsSettings":
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_settings.php");
            cms_admin_Settings($frameWidth);
            break;

         case "cmsImages":
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_images.php");
            cms_admin_images($frameWidth,$ownAdminPath);
            break;
        
        case "images":
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_images.php");
            cms_admin_images($frameWidth,$ownAdminPath);
            break;
        
        case "projects":
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_project.php");
            cms_admin_project($frameWidth,$ownAdminPath);
            break;


        case "cmsCms" :

            show_cmsSettings_Links();
            break;

        case "data" :
            show_cmsData_Links();
            break;

        case "cmsDates" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_dates.php");
            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_dates.js";
            cms_admin_dates($frameWidth,$ownAdminPath);
            if (file_exists($jsFile)) {
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_dates.js'></script>");
            }
            break;

        case "cmsUser" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_user.php");
            cms_admin_user($frameWidth,$ownAdminPath);
            break;

         case "cmsMail" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_email.php");
            cms_admin_email($frameWidth,$ownAdminPath);
            break;

        case "company" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_company.php");
            cms_admin_company($frameWidth,$ownAdminPath);
            break;

         case "category" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_category.php");
            cms_admin_category($frameWidth,$ownAdminPath);

            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_category.js";
            if (file_exists($jsFile)) {
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_category.js'></script>");
            }
            break;
            break;

        case "articles" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_articles.php");
            cms_admin_articles($frameWidth,$ownAdminPath);
            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_articles.js";
            if (file_exists($jsFile)) {
                //echo ("<h1> Javascript $jsFile loaded</h1>");
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_articles.js'></script>");
            }
            break;


        case "product" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_product.php");
            cms_admin_product($frameWidth,$ownAdminPath);
            break;

        case "location" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_location.php");
            cms_admin_location($frameWidth,$ownAdminPath);
            break;

        case "importExport" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_importExport.php");
            cms_admin_importExport($frameWidth,$ownAdminPath);
            break;
            
        default:
            if ($view) {
                echo ("SHOW ADMIN AREA - '$view' <br>");
                div_end($divName);
                return 0;
            }
            
            show_cmsSettings_Links();
            echo ("&nbsp;<br>");
            show_cmsData_Links();
    }
    div_end($divName);
}

function show_cmsSettings_Links() {
    $myPageData = cms_page_getData("admin_cmsCms");
    $myPageId = $myPageData[id];
    echo ("<h1>$myPageData[title]</h1> ");

    
    global $cmsSettings;
    // show_array($cmsSettings);

    if (is_string($cmsSettings[specialData])) $cmsSettings[specialData] = str2Array ($cmsSettings[specialData]);

    
    $pageList = cms_page_getSubPage($myPageId);
    for ($i=0;$i<count($pageList);$i++) {
        $showName  = $pageList[$i][title];
        $name      = $pageList[$i][name];
        $showLevel = $pageList[$i][showLevel];        
        if ($showLevel <= $_SESSION[showLevel]) {
            $type = cmsAdmin_getType_forFile($name);
            switch ($name) {
                case "admin_company"       : $type = "company"; break;
                case "admin_cmsMail"       : $type = "email"; break;
                case "admin_cmsUser"       : $type = "user"; break;
                case "admin_cmsDates"      : $type = "dates"; break;
                case "admin_product"       : $type = "product"; break;
                case "admin_category"      : $type = "category"; break;
                case "admin_location"      : $type = "location"; break;
                case "admin_importExport"  : $type = "importExport"; break;
                case "admin_articles"      : $type = "articles"; break;
                case "admin_project"       : $type = "project"; break;
                case "admin_images"        : $type = "images"; break;    
            
                case "admin_cmsSettings" : $type = "settings"; break;
                
                case "admin_cmsLayout" : $type = "layout"; break;
                case "admin_cmsImages" : $type = "images"; break;
            
                default :
                    echo ("unkownType $name <br>");
                    $type = "unkown";
            }
            
            $use = $cmsSettings[specialData][$type];
            $cmsSettings[specialData][$type] = 2;
            if ($use) {
                $show .= "<a class='cmsLinkButton' style='width:300px' href='".$name.".php'>$showName</a>";
            } else {
                if ($_SESSION[showLevel] >= 9) {
                    $dontShow .= "Don´t Show <a href='".$name.".php'>$showName</a> because disabled in cmsSettings $type<br>";
                }
            }
            
            
            if (!$showName) $showName = $name;
            echo ("<a href='".$name.".php' class='cmsLinkButton' style='width:300px'>$showName</a>");
        }
    }
    
    cms_adminPage_Create($cmsSettings[specialData],$myPageData);
}

function show_cmsData_Links() {
    $myPageData = cms_page_getData("admin_data");
    $myPageId = $myPageData[id];
    

    global $cmsSettings;
    // show_array($cmsSettings);

    if (is_string($cmsSettings[specialData])) $cmsSettings[specialData] = str2Array ($cmsSettings[specialData]);


    $pageList = cms_page_getSubPage($myPageId);

    $dontShow = "";
    $show = "";
    for ($i=0;$i<count($pageList);$i++) {

        // show_array($pageList[$i]);
        $showName  = $pageList[$i][title];
        $name      = $pageList[$i][name];
        $showLevel = $pageList[$i][showLevel];
        if ($showLevel <= $_SESSION[showLevel]) {


            switch ($name) {
                case "admin_company"       : $type = "company"; break;
                case "admin_cmsMail"       : $type = "email"; break;
                case "admin_cmsUser"       : $type = "user"; break;
                case "admin_cmsDates"      : $type = "dates"; break;
                case "admin_product"       : $type = "product"; break;
                case "admin_category"      : $type = "category"; break;
                case "admin_location"      : $type = "location"; break;
                case "admin_importExport"  : $type = "importExport"; break;
                case "admin_articles"      : $type = "articles"; break;
                case "admin_project"       : $type = "project"; break;
                case "admin_images"        : $type = "images"; break;    
                case "admin_project"       : $type = "project"; break;
            
                default :
                    echo ("unkownType $name <br>");
                    $type = "unkown";
            }
            if (!$showName) $showName = $name;

            $use = $cmsSettings[specialData][$type];
            if ($use = 1) $cmsSettings[specialData][$type] = 2;
            if ($use) {
                $show .= "<a class='cmsLinkButton' style='width:300px' href='".$name.".php'>$showName</a>";
            } else {
                if ($_SESSION[showLevel] >= 9) {
                    $dontShow .= "Don´t Show <a href='".$name.".php'>$showName</a> because disabled in cmsSettings $type<br>";
                }
            }
        }
    }
    if ($show) {
        echo ("<h1>$myPageData[title]</h1> ");
        echo ($show);
    }
    if ($dontShow) {
        echo ("<br>$dontShow <br>");
    }
    
    cms_adminPage_Create($cmsSettings[specialData],$myPageData);
    
    
}


function cms_adminPage_Create($specialData,$myPageData) {
    if ($_SESSION[showLevel] < 9) return "";
    global $pageInfo;
    echo ("<br>");
    if ($_GET[createPage]) {
        $create = $_GET[createPage];
        switch ($create) {
            case "company" : $file = "admin_company"; $name="Hersteller"; break;
            case "email" : $file = "admin_mail"; $name="eMail Verwaltung"; break;
            case "user" : $file = "admin_user"; $name="Benutzer"; break;
            case "dates" : $file = "admin_cmsDates"; $name="Termine"; break;                
            case "product" : $file = "admin_product"; $name="Produkte"; break;
            case "category" : $file = "admin_category"; $name="Kategorien";  break;
            case "location" : $file = "admin_location"; $name="Adressen";  break;
            case "importExport" : $file = "importExport";  $name="Import / Export";break;
            case "articles" : $file = "admin_articles";  $name="Artikel";break;
            case "project" : $file = "admin_projects"; $name="Projekte"; break;
            case "images" : $file = "admin_images"; $name="Bild Verwaltung"; break;
            default :
                echo "unkown CreatePage $create <br>";

        }
        
        if ($name AND $file) {

             // show_array($myPageData);

            
            
            $newName = $file;
            $newData = array();
            $newData[name] = $file;
            $newData[title] = $name;
            $newData[layout] = $myPageData[layout];
            $newData[navigation] = $myPageData[navigation];
            $newData[breadcrumb] = $myPageData[breadcrumb];
            // $newData[sort] = 
            $newData[showLevel] = $myPageData[showLevel];
            $newData[mainPage] = $myPageData[id];
             
            // echo ("create Page <b>$name</b> $file <br>");

            $res =  cms_page_create($newName, $newData);
            if ($res) {
                cms_infoBox("create Page <b>$name</b> $file ");
                // echo ("$pageInfo[page]<br>");
                reloadPage($pageInfo[page],1);
                return "";
            } else {
                cms_errorBox("create Page <b>$name</b> $file schlug fehl ");
            }

            
        }

    }
        
        
    // show_array($myPageData);
    // show_array($pageInfo);
    //  show_array($cmsSettings);
    foreach ($specialData as $key => $value ) {
        if ($value == 1) {
            echo ("no Page exist for <b>$key</b> ");
            echo ("<a href='$pageInfo[page]?createPage=$key'>create Page</a>");
            echo ("<br>");
        }
    }
    
}



function cms_admin_pages($pageInfo,$pageData) {
    $showPage = $pageData[name];
    if (!$showPage) {
        $showPage = $pageInfo[requestPageName];
    }
    $reload = 0;
    switch ($showPage) {
        case "cmsCms"      : $reload = "admin.php?view=cms"; break;
        case "cmsLayout"   : $reload = "admin.php?view=layout"; break;
        case "cmsData"     : $reload = "admin.php?view=data"; break;
        case "cmsUser"     : $reload = "admin.php?view=user"; break;
        case "cmsDates"    : $reload = "admin.php?view=dates"; break;
        default :
            echo "cms_admin_page unkown '$showPage' <br>";
    }

    if ($reload) {
        reloadPage($reload);
        return 1;
    }
    return 0;
}


function cms_admin_login($frameWidth) {
     echo ("Um diese Seite ansehen zu können, müssen Sie angemeldet sein!<br>");

     $contentData = array();
     $contentData[data] = array();
     $contentData[data][showLogin] = 1;

     cmsType_Login($contentData,$frameWidth);
}

function cms_admin_NoRights($frameWidth) {
    echo ("Um diese Seite ansehen, haben Sie nicht die ausreichenden Rechte!<br>");
    cmsType_logout($contentData, $frameWidth);
}



/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
