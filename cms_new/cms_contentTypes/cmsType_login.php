<?php // charset:UTF-8


class cmsType_login_base extends cmsClass_content_show {
    function getName (){
        return "Anmelden/Abmelden/Registrieren";
    }

    function setMainClass($mainClass=0) {
        if (is_object($mainClass)) {
            $this->mainClass = $mainClass;
            // echo ("<h1>Set Main Class in ".$this->getName()."</h1>");
        }
    }


    function contentType_show() {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        $userLevel = $this->session_get(showLevel);
        if ($userLevel > 0) {
            $this->login_showLogout();
            return 0;
        }
        $this->login_showLogin();
    }

    function login_showLogin() {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;

        $data = $this->contentData[data];
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

    function login_showLogout() {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;

        $data = $contentData[data];
        if (!is_array($data)) $data = array();


        if (!$data[showLogout]) {
            // Abmelden nicht zeigen
            return "";
        }


        $userId = $this->session_get(userId); 
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

     function contentType_editContent() {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $editContent = $useClass->editContent;
        $frameWidth  = $useClass->frameWidth;
        
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();

        // show_array($data);
        $res = array();

        $mainTab = "login";
        $res[$mainTab] = array();
        $res[$mainTab][showName] = $useClass->lga("content","loginTab");
        $res[$mainTab][showTab] = "Simple";

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_login","loginShow"); // "Anmelden zeigen";
        $checked = "";
        if ($editContent[data][showLogin]) $checked = "checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][showLogin]' $checked value='1'  >";
        $addData["mode"] = "Simple";
        $res[$mainTab][] = $addData;

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_login","logoutShow"); //"Abmelden zeigen";
        $checked = "";
        if ($editContent[data][showLogout]) $checked = "checked='checked'";
        $addData["mode"] = "Simple";
        $addData["input"] = "<input type='checkbox' name='editContent[data][showLogout]' $checked value='1' >";
        $res[$mainTab][] = $addData;
       
        $addData = array();
        $addData["text"] = $useClass->lga("contentType_login","registerShow"); //"Registrieren zeigen";
        $checked = "";
        if ($editContent[data][showRegister]) $checked = "checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][showRegister]' $checked value='1' >";
        $addData["mode"] = "Simple";
        $res[$mainTab][] = $addData;

        // REGISTER
        // Add ViewMode
        $viewModeList = $this->login_edit_register();
        $res[register] = $viewModeList;
       


        return $res;      
    }

    function login_edit_register() {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $editContent = $useClass->editContent;
        $frameWidth  = $useClass->frameWidth;

        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();


        $res = array();
        $lgaName = "contentType_user";

        $res[showName] = $useClass->lga("content","registerTab");
        $res[showTab] = "Simple";

        $needText = $useClass->lga($lgaName,"needText",": ");

        $addData = array();
        $addData["text"] = $useClass->lga($lgaName,"nameSalut"); // "Anrede";
        $checked = "";
        if ($editContent[data][salut]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][salut]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_salut]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_salut]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData["text"] =  $useClass->lga($lgaName,"nameVName"); // "Vorname";
        $checked = "";
        if ($editContent[data][vName]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][vName]' value='1'  $checked >\n";
        $checked = "";
        if ($editContent[data][need_vName]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_vName]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData["text"] =  $useClass->lga($lgaName,"nameNName"); // "Nachname";
        $checked = "";
        if ($editContent[data][nName]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][nName]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_nName]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_nName]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;


        $addData["text"] =  $useClass->lga($lgaName,"nameUserName"); // "Benutzername";
        $checked = "";
        if ($editContent[data][uName]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][uName]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_uName]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_uName]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData["text"] =  $useClass->lga($lgaName,"nameCompany"); // "Firma";
        $checked = "";
        if ($editContent[data][company]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][company]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_company]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_company]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData["text"] =  $useClass->lga($lgaName,"nameAdress"); // "Adresse";
        $checked = "";
        if ($editContent[data][adress]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][adress]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_adress]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_adress]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData["text"] =  $useClass->lga($lgaName,"namePhone"); // "Telefon";
        $checked = "";
        if ($editContent[data][phone]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][phone]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_phone]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_phone]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData["text"] =  $useClass->lga($lgaName,"nameEMail"); // "eMail";
        $checked = "";
        if ($editContent[data][email]) $checked = "checked='checked'";
        $input = "<input type='checkbox' name='editContent[data][email]' value='1' $checked >\n";
        $checked = "";
        if ($editContent[data][need_email]) $checked = "checked='checked'";
        $input .= $needText."<input type='checkbox' name='editContent[data][need_email]' value='1' $checked >\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        return $res;
    }
}

function cmsType_login_class() {
    if ($GLOBALS[cmsTypes]["cmsType_login.php"] == "own") $loginClass = new cmsType_login();
    else $loginClass = new cmsType_login_base();
    return $loginClass;
}


function cmsType_login($contentData,$frameWidth) {
    $loginClass = cmsType_login_class();
    $loginClass->show($contentData,$frameWidth);
}

function cmsType_logout($contentData,$frameWidth) {
    $loginClass = cmsType_login_class();
    $loginClass->show($contentData,$frameWidth);
}



function cmsType_login_editContent($editContent,$frameWidth) {
    $loginClass = cmsType_login_class();
    return $loginClass->contentType_editContent($editContent,$frameWidth);
}


?>
