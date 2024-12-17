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
    
    $pageClass = pageClass_class();
    $pageClass->page_show();

    cmsType_addJavaScript();    
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
            site_session_set(pageState,"online");
            // $_SESSION[pageState] = "online";
            reloadPage($goPage,0);
            return 1;
        } else {
            // echo ("FEHLER <br>");
            reloadPage($goPage,0);
            return 0;
        }        
    }
    //global $cmsVersion,$cmsName;
    // echo ("$cmsName <br>");
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
