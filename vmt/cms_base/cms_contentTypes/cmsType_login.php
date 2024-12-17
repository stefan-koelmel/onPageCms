<?php // charset:UTF-8


class cmsType_login_base extends cmsType_contentTypes_base {
    function getName (){
        return "Anmelden/Abmelden/Registrieren";
    }

    function login_show($contentData,$frameWidth) {
        $userLevel = $_SESSION[userLevel];
        if ($userLevel > 0) {
            $this->login_showLogout($contentData,$frameWidth);
            return 0;
        }
        $this->login_showLogin($contentData, $frameWidth);
    }

    function login_showLogin($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();


        if (!$data[showLogin]) {
            // Abmelden nicht zeigen
            return "";
        }

        global $pageInfo,$pageData;

        $login = $_POST[login];
        if (is_array($login)) {

            $loginUser = $login[userName];
            $loginPass = $login[password];

            $resLogin = cms_user_login($login);
            if ($resLogin == 1) {
                cms_infoBox("Angemeldet");
                //show_array($_SESSION);

                reloadPage($pageInfo[page],2);
                return 0;
            } else {
                echo ("Fehler bei der Anmeldung - $resLogin <br />");
            }

        } else {

        }

        $border = 0;
        $background = "#eee";
        $borderColor = "#555";
        $padding = 0;
        $innerWidth = $frameWidth - (2*$border) - (2*$padding);



        $leftWidth = 200;
        $leftAlign = "right";

        $inputWidth = 300;
        if ($innerWidth<$inputWidth) {
            $leftWidth = $innerWidth;
            $leftAlign = "left";

            $inputWidth = $innerWidth ;
        }



        $style = "width:".$innerWidth."px;";
        if ($border) $style .= "border:".$border."px solid $borderColor;";
        $style.="padding:".$padding."px;";#
        if ($background) $style.="background:$background;";

        div_start("cmsLogin"); //,$style);
        echo("<form method='post' class='login'>");
        echo("<span style='width:".$leftWidth."px;text-align:$leftAlign;display:inline-block;' >");
        echo("Benutzername oder eMail");
        echo("</span>");
        echo ("<input type='text' name='login[userName]' value='$login[userName]' style='width:".$inputWidth."px;margin-bottom:".$padding."px;' class='loginUserName' ><br />");

        echo("<span style='width:".$leftWidth."px;text-align:$leftAlign;display:inline-block;' >");
        echo("Password");
        echo("</span>");
        echo("<input type='password' name='login[password]' value='$login[password]' style='width:".$inputWidth."px;margin-bottom:".$padding."px;' class='loginUserPass' ><br />");

        echo("<input type='submit' class='mainInputButton login' name='login[login]' value='anmelden' > ");

        echo("</form>");

        div_end("cmsLogin");
    }

    function login_showLogout($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();


        if (!$data[showLogout]) {
            // Abmelden nicht zeigen
            return "";
        }


        $userId = $_SESSION[userId];
        global $pageInfo;
        $logout = $_POST[logout];
        if (is_array($logout)) {
            $logoutResult = cms_user_logout($userId);
            if ($logoutResult == 1) {
                cms_infoBox("Sie haben sich erfolgreich abgemeldet");

                reloadPage($pageInfo[page],2);
                return 1;

            }
            echo ("Logout $logout[logout]<br />");
        }
        // echo ("UserId = $userId <br />");
        $userData = cms_user_getInfo($userId,array("userName"));
        //show_array($userData);


        $border = 0;
        $background = "#eee";
        $borderColor = "#555";
        $padding = 0;
        $innerWidth = $frameWidth - (2*$border) - (2*$padding);



        $leftWidth = 200;
        $leftAlign = "right";

        $inputWidth = 300;
        if ($innerWidth<$leftWidth) {
            $leftWidth = $innerWidth;
            $leftAlign = "left";
            $inputWidth = $innerWidth ;
        }



        $style = "width:".$innerWidth."px;";
        if ($border) $style .= "border:".$border."px solid $borderColor;";
        $style.="padding:".$padding."px;";#
        if ($background) $style.="background:$background;";

        div_start("cmsLogout");
        echo ("Sie sind angemeldet als '<strong>$userData[userName]</strong>' <br />&nbsp; <br />");
        echo ("<form method='post'>");
        echo("<input type='submit' class='mainInputButton logout mainSecond' name='logout[logout]' value='abmelden' > ");

        echo("</form>");
        div_end("cmsLogout");

    }

    function login_edit($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        // show_array($data);
        $res = array();

        $mainTab = "register";
        // Add ViewMode
        $viewModeList = $this->login_edit_register($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }


        $mainTab = "login";
        $addData = array();
        $addData["text"] = "Anmelden zeigen";
        $checked = "";
        if ($editContent[data][showLogin]) $checked = "checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][showLogin]' $checked value='1'  >";
        $res[$mainTab][] = $addData;

        $addData = array();
        $addData["text"] = "Abmelden zeigen";
        $checked = "";
        if ($editContent[data][showLogout]) $checked = "checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][showLogout]' $checked value='1' >";
        $res[$mainTab][] = $addData;
       
        $addData = array();
        $addData["text"] = "Registrieren zeigen";
        $checked = "";
        if ($editContent[data][showRegister]) $checked = "checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][showRegister]' $checked value='1' >";
        $res[$mainTab][] = $addData;
        return $res;      
    }

    function login_edit_register($editContent,$frameWidth) {
        $add = array();

        $addData = array();
        $addData["text"] = "Anrede";
        $checked = "";
        if ($editContent[data][salut]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][salut]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_salut]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_salut]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;

        $addData["text"] = "Vorname";
        $checked = "";
        if ($editContent[data][vName]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][vName]' value='1'  $checked >\n";
        $checked = "";
        if ($editContent[data][need_vName]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_vName]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;

        $addData["text"] = "Nachname";
        $checked = "";
        if ($editContent[data][nName]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][nName]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_nName]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_nName]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;


        $addData["text"] = "Benutzername";
        $checked = "";
        if ($editContent[data][uName]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][uName]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_uName]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_uName]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;

        $addData["text"] = "Firma";
        $checked = "";
        if ($editContent[data][company]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][company]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_company]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_company]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;

        $addData["text"] = "Adresse";
        $checked = "";
        if ($editContent[data][adress]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][adress]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_adress]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_adress]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;

        $addData["text"] = "Telefon";
        $checked = "";
        if ($editContent[data][phone]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][phone]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_phone]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_phone]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;

        $addData["text"] = "eMail";
        $checked = "";
        if ($editContent[data][email]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][email]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_email]) $checked = "checked='checked'";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_email]' value='1' $checked >\n";
        $addData["input"] = $input;
        $add[] = $addData;
        return $add;
    }
}


//function cmsType_Login($contentData,$frameWidth) {
//
//    $userLevel = $_SESSION[userLevel];
//    if ($userLevel > 0) {
//        cmsType_logout($contentData,$frameWidth);
//        return 0;
//    }
//
//
//
//    global $pageInfo,$pageData;
//
//    $login = $_POST[login];
//    if (is_array($login)) {
//
//        $loginUser = $login[userName];
//        $loginPass = $login[password];
//
//        $resLogin = cms_user_login($login);
//        if ($resLogin == 1) {
//            cms_infoBox("Angemeldet");
//            //show_array($_SESSION);
//
//            reloadPage($pageInfo[page],2);
//            return 0;
//        } else {
//            echo ("Fehler bei der Anmeldung - $resLogin <br />");
//        }
//
//    } else {
//
//    }
//
//    $border = 0;
//    $background = "#eee";
//    $borderColor = "#555";
//    $padding = 0;
//    $innerWidth = $frameWidth - (2*$border) - (2*$padding);
//
//
//
//    $leftWidth = 200;
//    $leftAlign = "right";
//
//    $inputWidth = 300;
//    if ($innerWidth<$inputWidth) {
//        $leftWidth = $innerWidth;
//        $leftAlign = "left";
//
//        $inputWidth = $innerWidth ;
//    }
//
//
//
//    $style = "width:".$innerWidth."px;";
//    if ($border) $style .= "border:".$border."px solid $borderColor;";
//    $style.="padding:".$padding."px;";#
//    if ($background) $style.="background:$background;";
//
//    div_start("cmsLogin"); //,$style);
//    echo("<form method='post' class='login'>");
//    echo("<span style='width:".$leftWidth."px;text-align:$leftAlign;display:inline-block;' >");
//    echo("Benutzername oder eMail");
//    echo("</span>");
//    echo ("<input type='text' name='login[userName]' value='$login[userName]' style='width:".$inputWidth."px;margin-bottom:".$padding."px;' class='loginUserName' ><br />");
//
//    echo("<span style='width:".$leftWidth."px;text-align:$leftAlign;display:inline-block;' >");
//    echo("Password");
//    echo("</span>");
//    echo("<input type='password' name='login[password]' value='$login[password]' style='width:".$inputWidth."px;margin-bottom:".$padding."px;' class='loginUserPass' ><br />");
//
//    echo("<input type='submit' class='inputButton login' name='login[login]' value='anmelden' > ");
//
//    echo("</form>");
//
//    div_end("cmsLogin");
//
//
//}
//
//function cmsType_logout($contentData,$frameWidth) {
//    $userId = $_SESSION[userId];
//    global $pageInfo;
//    $logout = $_POST[logout];
//    if (is_array($logout)) {
//        $logoutResult = cms_user_logout($userId);
//        if ($logoutResult == 1) {
//            cms_infoBox("Sie haben sich erfolgreich abgemeldet");
//
//            reloadPage($pageInfo[page],2);
//            return 1;
//
//        }
//        echo ("Logout $logout[logout]<br />");
//    }
//
//    $userData = cms_user_getInfo($userId,array("userName"));
//    //show_array($userData);
//
//
//    $border = 0;
//    $background = "#eee";
//    $borderColor = "#555";
//    $padding = 0;
//    $innerWidth = $frameWidth - (2*$border) - (2*$padding);
//
//
//
//    $leftWidth = 200;
//    $leftAlign = "right";
//
//    $inputWidth = 300;
//    if ($innerWidth<$leftWidth) {
//        $leftWidth = $innerWidth;
//        $leftAlign = "left";
//        $inputWidth = $innerWidth ;
//    }
//
//
//
//    $style = "width:".$innerWidth."px;";
//    if ($border) $style .= "border:".$border."px solid $borderColor;";
//    $style.="padding:".$padding."px;";#
//    if ($background) $style.="background:$background;";
//
//    div_start("cmsLogout");
//    echo ("Sie sind angemeldet als '<strong>$userData[userName]</strong>' <br />&nbsp; <br />");
//    echo ("<form method='post'>");
//    echo("<input type='submit' class='inputButton logout' name='logout[logout]' value='abmelden' > ");
//
//    echo("</form>");
//    div_end("cmsLogout");
//
//}
//
//function cmsType_Login_editContent($editContent) {
//    $data = $editContent[data];
//    if (!is_array($data)) $data = array();
//
//    $res = array();
//
//
//    // MainData
//    $addData = array();
//    $addData["text"] = "Rahmen Stärke";
//    $addData["input"] = "<input type='text' name='editContent[data][mainBorder]' value='$data[mainBorder]' >";
//    $res[] = $addData;
//    $addData["text"] = "Rahmenfarbe";
//    $addData["input"] = "<input type='text' name='editContent[data][mainBorderColor]' value='$data[mainBorderColor]' >";
//    $res[] = $addData;
//    $addData["text"] = "Hintergrundfarbe";
//    $addData["input"] = "<input type='text' name='editContent[data][mainBackColor]' value='$data[mainBackColor]' >";
//    $res[] = $addData;
//
//    return $res;
//}

function cmsType_login_class() {
    if ($GLOBALS[cmsTypes]["cmsType_login.php"] == "own") $loginClass = new cmsType_login();
    else $loginClass = new cmsType_login_base();
    return $loginClass;
}


function cmsType_login($contentData,$frameWidth) {
    $loginClass = cmsType_login_class();
    $loginClass->login_show($contentData,$frameWidth);
}



function cmsType_login_editContent($editContent,$frameWidth) {
    $loginClass = cmsType_login_class();
    return $loginClass->login_edit($editContent,$frameWidth);
}


?>
