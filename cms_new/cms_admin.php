<?php // charset:UTF-8

function cms_admin_show($view,$frameWidth,$pageClass) {
    $border = 2;
    $padding = 10;
    $frameWidth = $frameWidth - (2*border) - (2*padding);
    $userLevel = session::get(userLevel);
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
    div_start($divName,"");//width:".$frameWidth."px;");
    global $cmsVersion;
    
    
    $newView = cmsAdmin_showOld($view);
    if ($newView ) {
        echo ("New View = $newView <br>");
        $view = $newView;
        
    }
    // echo ("<h1>View = $view </h1>");
    switch($view) {
        case "cmsLayout" : $fn = "layout"; break;
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_layout.php");
            $adminClass = cms_admin_cmsLayout($frameWidth);
//            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_layout.js";
//            if (file_exists($jsFile)) {
//                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_layout.js'></script>");
//            }
            break;
        
        case "cmsSettings": $fn = "settings"; break;
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_settings.php");
            cms_admin_Settings($frameWidth);
            break;

         case "cmsImages":
             $fn = "images";
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_images.php");
//            cms_admin_images($frameWidth,$ownAdminPath);
            break;
        
        case "images":
            $fn = "images";
            //include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_images.php");
            // cms_admin_images($frameWidth,$ownAdminPath);
            break;
        
        case "cmsProject":
            $fn = "project";
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_project.php");
//            $adminClass = cms_admin_projectClass($ownAdminPath);
//            if ()
//            
//            
//            $adminClass = cms_admin_project($frameWidth,$ownAdminPath);
            break;


        case "cmsCms" :

            $adminClass = show_cmsSettings_Links();
            break;

        case "data" :
            $adminClass = show_cmsData_Links();
            break;

        case "cmsDates" :
            $fn = "dates";
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_dates.php");
//            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_dates.js";
//            if (file_exists($jsFile)) {
//                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_dates.js'></script>");
//            }
//            $adminClass = cms_admin_datesClass($ownAdminPath);            
            break;

        case "cmsUser" :
            $fn = "user";
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_user.php");
//            $adminClass = cms_admin_user($frameWidth,$ownAdminPath);
            break;

        case "cmsMail"          : $fn = "email"; break;
        case "cmsEmail"         :
            $fn= "email";
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_email.php");
//            $adminClass = cms_admin_email($frameWidth,$ownAdminPath);
            break;

        case "cmsCompany" :
            $fn = "company";
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_company.php");
//            // $adminClass = cms_admin_company($frameWidth,$ownAdminPath);
//            
//            $adminClass = cms_admin_companyClass($ownAdminPath);
//            
            break;

         case "cmsCategory" : $fn = "category"; break;
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_category.php");
            
            
             
            $adminClass = cms_admin_category($frameWidth,$ownAdminPath);

            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_category.js";
            if (file_exists($jsFile)) {
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_category.js'></script>");
            }
            break;
            break;

        case "cmsArticles" : $fn = "articles"; break;
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_articles.php");
            $adminClass = cms_admin_articles($frameWidth,$ownAdminPath);
            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_articles.js";
            if (file_exists($jsFile)) {
                //echo ("<h1> Javascript $jsFile loaded/<h1>");
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_articles.js'></script>");
            }
            break;


        case "cmsProduct" : $fn = "product"; break;
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_product.php");
//            $adminClass = cms_admin_productClass($ownAdminPath);
//            
//            // $adminClass = cms_admin_product($frameWidth,$ownAdminPath);
            break;

        case "cmsLocation" : $fn = "location"; break;
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_location.php");
            $adminClass = cms_admin_location($frameWidth,$ownAdminPath);
            break;

        case "importExport" : $fn = "importExport"; break;
        case "cmsImportExport" : $fn = "importExport"; break;
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_importExport.php");
            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_importExport.js";
            if (file_exists($jsFile)) {
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_importExport.js'></script>");
            }
            $adminClass = cms_admin_importExport($frameWidth,$ownAdminPath);
            break;
        
         case "cmsOrder" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_order.php");
            $adminClass = cms_admin_order($frameWidth,$ownAdminPath);
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
    
    if ($fn) {
        $rootPath = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/";
        $classFile = "cms_admin_".$fn.".php";
        if (file_exists($rootPath.$classFile)) {
            include($rootPath.$classFile);
            $own = 0;
            $ownClassFile = "cms_admin_".$fn."_own.php";
            $className = "cmsAdmin_".$fn."_base";
            if (file_exists($ownAdminPath.$ownClassFile)) {
                $own = 1;
                include($ownAdminPath.$ownClassFile);
                $className = "cmsAdmin_".$fn;                
            }
            
            if ( class_exists($className)) {               
                $adminClass = new $className;
            } else {
                echo ("CLASS '<b>$className</b>' not exist <br>");
            }
            
            $jsFile = "cms_admin_".$fn.".js";
            if (file_exists($rootPath.$jsFile)) {
                // echo ("JAVA FILE $jsFile exist <br>");
                echo("<script src='/cms_".$cmsVersion."/admin/".$jsFile."'></script>");
            }
            
            
            
        }
        
        
    }
    
    
    if (is_object($adminClass) AND is_object($pageClass)) {
        $adminClass->admin_show($frameWidth,$pageClass);
        // $adminClass->admin_init_PageClass($pageClass);
        
        // $adminClass->show($frameWidth);
    }
    
    div_end($divName);
}

function show_cmsSettings_Links() {
    
    if (!function_exists("cmsAdmin_getType_forFile()")) {
        global $cmsVersion;
        include($_SERVER[DOCUMENT_ROOT]."/cms_".$cmsVersion."/data/cms_admin.php");
    }
    
    $myPageData = cms_page_getData("admin_cmsCms");
    $myPageId = intval($myPageData[id]);
    $title = cms_text_getLg($myPageData[title]);
    echo ("<h1>$title</h1> ");

    
    //  global $cmsSettings;
    $cmsSettings = site_session_get(cmsSettings);
    // show_array($cmsSettings);

    if (is_string($cmsSettings[specialData])) {
        $cmsSettings[specialData] = str2Array ($cmsSettings[specialData]);
        site_session_set("cmsSettings,specialData", $cmsSettings[specialData]);
    }

    
    $pageList = cms_page_getSubPage($myPageId);
    //for ($i=0;$i<count($pageList);$i++) {
    foreach ($pageList as $key => $pageList) {
        $showName  = $pageList[title];
        
        $showName = cms_text_getLg($showName);
        $name      = $pageList[name];
        $showLevel = $pageList[showLevel];        
        if ($showLevel <= session::get(showLevel)) {
            $type = cmsAdmin_getType_forFile($name);
            if (!$type) {
                echo ("Not Found '$name' $type <br>");
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
            }
            
            $use = $cmsSettings[specialData][$type];
            $cmsSettings[specialData][$type] = 2;
            site_session_set("cmsSettings,specialData,specialData",2);
            if ($use) {
                $show .= "<a class='cmsLinkButton' style='width:300px' href='".$name.".php'>$showName</a>";
            } else {
                if (session::get(showLevel) >= 9) {
                    $dontShow .= "Don´t $key Show <a href='".$name.".php'>$showName</a> because disabled in cmsSettings $type<br>";
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
    $myPageId = intval($myPageData[id]);
    

    $cmsSettings = site_session_get(cmsSettings);
    // show_array($cmsSettings);

    if (is_string($cmsSettings[specialData])) {
        $cmsSettings[specialData] = str2Array ($cmsSettings[specialData]);
        site_session_set("cmsSettings,specialData", $cmsSettings[specialData]);
    }
    
    

    $pageList = cms_page_getSubPage($myPageId);

    $dontShow = "";
    $show = "";
    // for ($i=0;$i<count($pageList);$i++) {
    foreach ($pageList as $pageName => $pageData) {    
        // show_array($pageList[$i]);
        $showName  = $pageData[title];
        $showName  = cms_text_getLg($showName);
        $name      = $pageData[name];
        $showLevel = $pageData[showLevel];
        if ($showLevel <= session::get(showLevel)) {


            switch ($name) {
                case "admin_company"       : $type = "company"; break;
                case "admin_cmsCompany"    : $type = "company"; break;
                
                case "admin_cmsMail"       : $type = "email"; break;
                case "admin_cmsEmail"      : $type = "email"; break;
                
                case "admin_cmsUser"       : $type = "user"; break;
                case "admin_cmsUserData"   : $type = "userData"; break;
            
            
                case "admin_cmsDates"      : $type = "dates"; break;
                
                case "admin_product"       : $type = "product"; break;
                case "admin_cmsProduct"    : $type = "product"; break;
                
                case "admin_category"      : $type = "category"; break;
                case "admin_cmsCategory"      : $type = "category"; break;
                
                case "admin_location"      : $type = "location"; break;
                case "admin_cmsLocation"   : $type = "location"; break;
                
                case "admin_importExport"  : $type = "importExport"; break;
                
                case "admin_articles"      : $type = "articles"; break;
                case "admin_cmsArticles"   : $type = "articles"; break;
                
                case "admin_project"       : $type = "project"; break;
                case "admin_cmsProject"    : $type = "project"; break;    
            
                case "admin_images"        : $type = "images"; break;    
                case "admin_project"       : $type = "project"; break;
            
                default :
                    echo ("unkownType $name <br>");
                    $type = "unkown";
            }
            if (!$showName) $showName = $name;

            $use = $cmsSettings[specialData][$type];
            if ($use == 1) {
                $cmsSettings[specialData][$type] = 2;
                site_session_set("cmsSettings,specialData,".$type, 2);
            }
            if ($use) {
                $show .= "<a class='cmsLinkButton' style='width:300px' href='".$name.".php'>$showName</a>";
            } else {
                if (session::get(showLevel) >= 9) {
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
    if (session::get(showLevel) < 9) return "";
    global $pageInfo;
    echo ("<br>");
    if ($_GET[createPage]) {
        $create = $_GET[createPage];
        switch ($create) {
            case "company" : $file = "admin_cmsCompany"; $name="Hersteller"; break;
            case "email" : $file = "admin_mail"; $name="eMail Verwaltung"; break;
            case "user" : $file = "admin_cmsUser"; $name="Benutzer"; break;
            case "dates" : $file = "admin_cmsDates"; $name="Termine"; break;                
            case "product" : $file = "admin_cmsProduct"; $name="Produkte"; break;
            case "category" : $file = "admin_cmsCategory"; $name="Kategorien";  break;
            case "location" : $file = "admin_cmsLocation"; $name="Adressen";  break;
            case "importExport" : $file = "cmsImportExport";  $name="Import / Export";break;
            case "articles" : $file = "admin_cmsArticles";  $name="Artikel";break;
            case "project" : $file = "admin_cmsProjects"; $name="Projekte"; break;
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
            $dataName = "admin_cms".ucfirst($key);
            $fn = $dataName.".php";
            
            $fileExist = file_exists($fn);
            $dataExist = cms_page_get(array("name"=>$dataName));
            
            if ($fileExist AND is_array($dataExist)) {
                
            } else {
                echo ("<b>not exist $key $dataName</b> - ");
                
                if (!is_array($dataExist)) echo ("DB not exist - ");
                if (!$fileExist) echo ("FILE not exist - ");

                




                echo ("<a href='$pageInfo[page]?createPage=$key'>create Page</a>");
                echo ("<br>");
            }
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
    div_start("pageNotAllowed");
    // echo ("SIE HABEN KEINE BERECHTIGUNG FÜR DIESE SEITE");
    echo ("Um diese Seite anzusehen, haben Sie nicht die ausreichenden Rechte!<br>");
    div_end("pageNotAllowed");
    
    $contentData = array();
    cmsType_logout($contentData, $frameWidth);
}

function  cmsAdmin_showold($view) {
    switch ($view) {
        case "company"          : return "cmsCompany";
        case "product"          : return "cmsProduct";
        case "category"          : return "cmsCategory";
    }
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
