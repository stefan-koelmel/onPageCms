<?php // charset:UTF-8
function cms_page_show() {

    global $pageInfo,$pageData;
    
    $pageState = cms_page_state();
    switch ($pageState) {
        case "online" :
            break;
        case "construction" :
            $exit = cms_page_construction();
            if ($exit) return 0;
            break;
        case "inWork" :
            $exit = cms_page_inWork();
            if ($exit) return 0;
            break;
    }
    
    
    
    
    cmsHistory_set($pageData);
    // echo ("Page is $pageInfo[page]");
    // foreach($pageInfo as $key => $value ) echo ("pI $key = $value <br>");

    $userLevel = $_SESSION[userLevel];

    if (!$userLevel) {
        $login = $_GET[login];
        if ($login=="1") {
            echo ("Sie sind angemeldet<br>");
            $_SESSION["userLevel"] = 9;
        } else {

        }
    }

    if ($userLevel > 0)  { // loggedIn
        if ($userLevel > 7) $editAble = 1;
    }
    $edit = $_SESSION[edit];
    $pageWidth = $GLOBALS[cmsSettings][width];

    $editLayout = $_GET[editLayout];

    if (!is_array($GLOBALS[cmsSettings])) {
       
        $GLOBALS[cmsSettings] = cms_settings_get();
        // echo ("No Settings $GLOBALS[cmsName] - $GLOBALS[cmsSettings] <br>");
        $pageWidth = $GLOBALS[cmsSettings][width];
        // show_array($GLOBALS[cmsSettings]);
    }
    
    if ($_SESSION[showLevel] >= 7) {
        $defaultText = $_POST[defaultText];
        $adminText   = $_POST[adminText];
        $error = 0;
        $check = 0;
        if (is_array($defaultText)) {
            $check++;
            $error += cms_defaultText_save($defaultText);            
        }
        if (is_array($adminText)) {
            $check++;
            $error += cms_adminText_save($adminText);            
        }
        if ($check) {
            if ($error == 0) reloadPage();
        }            
    }
   
    $layoutName = $GLOBALS[pageData][layout];
    if (!$layoutName) {
        // show_array($GLOBALS[cmsSettings]);

        if ($layoutName == 0 AND $GLOBALS[cmsSettings][layout]) {
            // echo ("TAke Standard Layout from Settinge ".$GLOBALS[cmsSettings][layout]."<br>");
            $layoutName = $GLOBALS[cmsSettings][layout];
        }
        //show_array($GLOBALS[pageData]);
        // echo ("No LayoutName $layoutName<br>");
        // $layoutName = $GLOBALS[cmsSettings][layoutName];
    }
    if ($layoutName) {
        $layoutData = cms_layout_getLayout($layoutName);
        
        cms_layout_show($layoutName,$pageWidth,$pageData);

       if ($_SESSION[showLevel] >= 7) {
           cms_defaultText_show($pageWidth);
           cms_adminText_show($pageWidth);
       }
        
        
        cmsType_addJavaScript();
       // echo("<script src='cms/cms_contentTypes/cmsType_flip.js'></script>");

       
        div_start("imagePreviewWindow",array("cmsName"=>$GLOBALS[cmsName]));
        div_end("imagePreviewWindow");


        div_start("imagePreviewContent");
        echo("&nbsp;");
        // echo ("<img src='' class='imagePreviewImage' alt='' width='0px' height='0px'>");
        div_end("imagePreviewContent");

        return 0;
    }


    echo ("LayoutName = $layoutName <br>");




    if ($pageData[breadcrumb]) {
       cms_titleLine($pageData,$pageWidth);
        


    }

    $pageWidth = 750;
    switch ($pageInfo[pageName]) {
        case "sitemap" :
            //include("cms/cms_sitemap.php");
            //echo ("Show Sitemap<br>");
            break;

         case "admin" :
             $view = $_GET[view];
             cms_admin_show($view);
            
            // echo ("Show Sitemap<br>");
            break;

        default :
            $normalPage = 1;
            if (substr($pageInfo[pageName],0,6) == "admin_") {
                $view = substr($pageInfo[pageName],6);
                cms_admin_show($view);
                $normalPage = 0;
                 echo ("Admin !!!");
            }
            
            if ($normalPage ) {
                 // echo ("is Admin??? ".substr($pageInfo[pageName],0,6)."<br>");
                 cms_content_show("page_$pageData[id]",$pageWidth);
            }
            
            //cms_page_showCms();
    }
    div_end("content","before");
  
    // echo("<script src='cms/cms_contentTypes/cmsType_flip.js'></script>");
   // echo("<script src='cms/cms_contentTypes/cmsType_Social.js'></script>");
}


function cms_page_construction() {
    $exit = cms_page_login();
    if ($exit) return 1;
    $fn = "construction.html";
    if (file_exists($fn)) {
        include ($fn);
        return 1;
    }
    echo ("HIER IST EINE BAUSTELLE");
    return 1;
}

function cms_page_inWork() {
    $exit = cms_page_login();
    if ($exit) return 1;
    
    
    $fn = "inWork.html";
    if (file_exists($fn)) {
        include ($fn);
        return 1;
    }
    echo ("Diese Seite wird gerade gewartet<br />");
    echo ("Haben Sie noch etwas Geduld!");
    return 1;
}

function cms_page_login() {
    $login = $_GET[login];
    if (!$login == "1") return 0;
    
    if ($_POST[cancel]) return 0;
    
    if ($_POST[login]) {
        $loginData = array();
        $loginData[userName] = $_POST[userName];
        $loginData[password] = $_POST[password];
        $loginData[userLevel] = 7;
        switch ($loginData[userName]) {
            case "hiphip" : $loginData[userLevel] = 1; break;
            case "hip@stefan-koelmel.com" : $loginData[userLevel] = 1; break;
        }
        $res = cms_user_login($loginData);
        global $pageInfo; 
        $goPage = $pageInfo[page];
        if ($res == 1) {
            $_SESSION[pageState] = "online";
            reloadPage($goPage,0);
            return 1;
        } else {
            // echo ("FEHLER <br>");
            reloadPage($goPage,0);
            return 0;
        }        
    }
    echo ("<div class='loginMainFrame' >");
    echo ("<h1>onPageCms</h1>");
    //echo ("<h3>Anmelden</h3>");
    echo ("<form method='post' >");
    echo ("<span class='loginName' >Benutzername:</span>");
    echo ("<input class='loginInput' type='text' value='' name='userName' / ><br />");
    
    echo ("<span class='loginName' >Passwort:</span>");
    echo ("<input class='loginInput' type='password' value='' name='password' /><br />");
    
    echo ("<span class='loginName' >&nbsp;</span>");
    echo ("<input class='loginButton' type='submit' value='ANMELDEN' name='login' />");
    echo ("<input class='loginButton' type='submit' value='ABBRECHEN' name='cancel' /><br />");
    echo ("</form>");
    return 1;
    
    
        
    
    
}



?>
