<?php // charset:UTF-8

class cmsType_user_base extends cmsType_contentTypes_base {
    function getName (){
        return "Benutzer";        
    }
    
    
    function show($contentData,$frameWidth) {
        if (!is_null($_GET[confirm])) {
           
        }
        
        
        $logout = $_POST[logout];
        if (is_array($logout)) {
            $logoutResult = cms_user_logout($userId);
            if ($logoutResult == 1) {
                cms_infoBox("Sie haben sich erfolgreich abgemeldet");

                reloadPage($pageInfo[page],2);
                return 1;
            } else {
                cms_errorBox("Fehler beim abmelden");
            }
        }
        
        if ($_POST[loginUser]) {
            $userName = $_POST[userName];
            $password = $_POST[password];
            $findUser = cmsUser_find($userName); 
            if (!is_array($findUser)) {
                $str .= cms_errorBox_str("Die von Ihnen angegebene eMail-Adresse oder Benutzername konnte nicht in der Datenbank gefunden werden");
            } else {
                $str .= "BENUTZER $userName wurd gefunden <br>";
                if ($findUser[password] != $password) {
                    $str .= cms_errorBox_str("Das angegebene Passowrt ist nicht korrekt");
                } else {
                    cms_infoBox("Sie haben sich erfolgreich angemeldet");
                    $loginResult = cmsUser_doLogin($findUser);
                    $str .= "LOGIN RESULT = $loginResult <br>";
                    reloadPage($goPage,2);
                    return "break";
                }
            }

        }
        
        
        if ($_GET[mode]) {
            switch ($_GET[mode]) {
                case "confirm" :
                    $this->show_confirmLink($contentData,$frameWidth);
                    return 0;
                    
                    break;
               case "emailReset" :
                    $this->show_emailResetLink($contentData,$frameWidth);
                    return 0;
                    break;
            }
        }
        
        
        $viewMode = $contentData[data][viewMode];
        switch ($viewMode) {
            case "userCenter" :
                $this->show_userCenter($contentData,$frameWidth);
                break;
            case "userData" :
                $this->show_userData($contentData,$frameWidth);
                break;
            
            case "userOrder" :
                $this->show_userOrder($contentData,$frameWidth);
                break;
            
            case "userLogin" :
                $this->show_userLogin($contentData,$frameWidth);
                break;
            
            default :
                echo ("Unkown ViewMode '$viewMode' in cmsType_user <br />");
        }
    }
    
    
    
    
    function show_noUser($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        // foreach ($data as $key => $value) echo ("$key = $value <br>");
        
        $showLogin = $data[notLogged_login];
        $showLogin_Pos = $data[notLogged_position_login];
        if (!$showLogin_Pos) $showLogin_Pos = "top";
        
        $showForgot = $data[notLogged_forgot];
        $showForgot_Pos = $data[notLogged_position_forgot];
        if ($showForgot_Pos) $showForgot_Pos = $showLogin_Pos;
        
        $showRegister = $data[notLogged_register];
        $showRegister_Pos = $data[notLogged_position_register];
        
        $posData = $this->position_frameValue($contentData,$frameWidth);
        
        $leftWidth = $posData[left_width];
        $leftAbs = $posData[left_abs];
        $centerWidth = $posData[center_width];
        $centerAbs = $posData[center_abs];
        $rightWidth = $posData[right_width];
        $rightAbs = 0;
        
        // show_array($posData);
        
        $mainDivName = "cmsUser cmsUser_noUser";
        $mainDivData = array("style"=>"width:".$frameWidth."px;");
        div_start($mainDivName,$mainDivData);
        
        $break = 0;
        // LOGIN
        if ($data["notLogged_login"]) {
            $pos = $data["notLogged_position_login"];
            if (!$pos) $pos = "top";
            
            $width = $posData[$pos."_width"];
            
            $showForget = $data["notLogged_forgot"];
            
            $text = $this->show_login($contentData,$width,$showForget);
            if ($text=="break") $break = 1;
            $posData[$pos."_text"][login] = $text;            

        }
        
        
        // REGISTER
         if ($data["notLogged_register"]) {
            $pos = $data["notLogged_position_register"];
            if (!$pos) $pos = "top";
            
            $width = $posData[$pos."_width"];
            
            $text = $this->show_register($contentData,$width);
            if ($text=="break") $break = 1;
            $posData[$pos."_text"][register] = $text;            
        }
        
       
        
        if ($break) {
            div_end($mainDivName,"before");
            return 0;
        }
       
        $class = "cmsUser_noUser";
        $this->position_frameShow($posData,$class,$frameWidth);
        
        div_end($mainDivName,"before");
        return 0;
    }
    
    
    function show_login($contentData,$frameWidth,$showForgot=0) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $goPage = $GLOBALS[pageData][name].".php";         
        if ($frameWidth > 400) {
            $abs = 10;
            $inline = 1;
            $leftWidth = 200;
            $inputWidth = 200;
            if (($inputWidth + $leftWidth + $abs) > $frameWidth) {
                $leftWidth = $frameWidth - $inputWidth - $abs;
            }
        } else {
            $inline = 0;
            // $leftWidth = 200;
            $inputWidth = $frameWidth -10;           
        }
        
        $showLogin = 1;
        $str = "";
        
        if ($showForgot) {
            if ($_GET[forget]) {
                $showLogin = 0;
                $str .= "<h1>Passort vergessen</h1>";
                if ($_POST[forgetCancel]) {
                    $str .= cms_infoBox_str("Passwort vergessen abgebrochen");
                    reloadPage($goPage,2);
                    return $str;
                }
                
                if ($_POST[forgetSend]) {
                    $userName = $_POST[userName];
                    $userExist = cmsUser_find($userName);
                    if ($userExist) {
                        $sendMailResult = $this->show_emailSend("forget", $userExist, $contentData, $frameWidth, "silent");
                        if ($sendMailResult) {
                            $str .= cms_infoBox_str("Wir haben Ihnen ein neues Passwort an die gespeicherte Adresse gesendet");
                            reloadPage($goPage,2);
                            return $str;
                        } else {
                            $str .= cms_errorBox_str("Wir konnten Ihnen kein neues Passwort zusenden");
                        }
                    } else {
                        $str .= cms_errorBox_str("Die von Ihnen angegebene eMail-Adresse oder Benutzername konnte nicht in der Datenbank gefunden werden<br />Bitte überprüfen Sie Ihre Eingabe");
                    }
                   
                }
                
                $goPage = $goPage."?forget=1";
                $str .= "<form action='$goPage' method='post'>";
                
                if ($inline) {
                    $str .= div_start_str("cmsUser_userName cmsUser_userName_inline");
                    $str .= span_text_str("eMail oder Benutzername:",array("style"=>"width:".$leftWidth."px;","class"=>"cmsUser_loginSpan"));
                    $str .= "<input type='text' style='width:".$inputWidth."px;' class='cmsUser_loginInput' value='$userName' name='userName' /><br />";
                    $str .= div_end_str("cmsUser_userName cmsUser_userName_inline");                  
                } else {
                    $str .= div_start_str("cmsUser_userName");
                    $str .= span_text_str("eMail oder Benutzername:",array("class"=>"cmsUser_loginSpan"))."<br />";
                    $str .= "<input type='text' style='width:".$inputWidth."px;' class='cmsUser_loginInput' value='$userName' name='userName' /><br />";
                    $str .= div_end_str("cmsUser_userName");
                }
                
                $str .= "<input type='submit' class='mainInputButton' value='eMail senden' name='forgetSend' />";
                $str .= "<input type='submit' class='mainInputButton mainSecond' value='abbrechen' name='forgetCancel' />";
                $str .= "</form>";
                
            }
        }
        
        if ($showLogin) {
            if ($_POST[loginUser]) {
                $userName = $_POST[userName];
                $password = $_POST[password];
                $findUser = cmsUser_find($userName); 
                if (!is_array($findUser)) {
                    $str .= cms_errorBox_str("Die von Ihnen angegebene eMail-Adresse oder Benutzername konnte nicht in der Datenbank gefunden werden");
                } else {
                    $str .= "BENUTZER $userName wurd gefunden <br>";
                    if ($findUser[password] != $password) {
                        $str .= cms_errorBox_str("Das angegebene Passowrt ist nicht korrekt");
                    } else {
                        cms_infoBox("Sie haben sich erfolgreich angemeldet");
                        $loginResult = cmsUser_doLogin($findUser);
                        $str .= "LOGIN RESULT = $loginResult <br>";
                        reloadPage($goPage,2);
                        return "break";
                    }
                }
                
            }
            $str .= "<h1>Anmelden</h1>";
            $str .= "<form action='$goPage' method='post'>";

            if ($inline) {
                $str .= div_start_str("cmsUser_userName cmsUser_userName_inline");
                $str .= span_text_str("eMail oder Benutzername:",array("style"=>"width:".$leftWidth."px;","class"=>"cmsUser_loginSpan"));
                $str .= "<input type='text' style='width:".$inputWidth."px;' class='cmsUser_loginInput' value='$userName' name='userName' /><br />";
                $str .= div_end_str("cmsUser_userName cmsUser_userName_inline");

                $str .= div_start_str("cmsUser_password cmsUser_password_inline");
                $str .= span_text_str("Passwort:",array("style"=>"width:".$leftWidth."px;","class"=>"cmsUser_loginSpan"));
                $str .= "<input type='passworf' style='width:".$inputWidth."px;' class='cmsUser_loginInput' value='$password' name='password' /><br />";
                $str .= div_end_str("cmsUser_password cmsUser_password_inline");
            } else {
                $str .= div_start_str("cmsUser_userName");
                $str .= span_text_str("eMail oder Benutzername:",array("class"=>"cmsUser_loginSpan"))."<br />";
                $str .= "<input type='text' style='width:".$inputWidth."px;' class='cmsUser_loginInput' value='$userName' name='userName' /><br />";
                $str .= div_end_str("cmsUser_userName");

                $str .= div_start_str("cmsUser_password");
                $str .= span_text_str("Passwort:",array("class"=>"cmsUser_loginSpan"))."<br />";
                $str .= "<input type='password' style='width:".$inputWidth."px;' class='cmsUser_loginInput' value='$password' name='password' /><br />";
                $str .= div_end_str("cmsUser_password");
            }


            if ($showForgot) {
                $str .= div_start_str("cmsUser_foregtLine");
                $goPage .= "?forget=1";
                $link = "<a href='$goPage' class='cmsUser_forgetLink' >Passwort vergessen</a>";
                if ($inline) {
                    $str .= span_text_str("&nbsp;",array("style"=>"width:".$leftWidth."px;","class"=>"cmsUser_loginSpan"));
                    $str .= $link;
                } else {
                    $str .= $link;
                }
                $str .= div_end_str("cmsUser_foregtLine");
            }


            $str .= div_start_str("cmsUser_button");
            $str .= "<input type='submit' class='mainInputButton' value='Anmelden' name='loginUser' />";
            $str .= div_end_str("cmsUser_button");
            $str .= "</form>";
        }
           
        return $str;
        
    }
    
    function show_forgot($contentData,$width) {
//        $goPage = $GLOBALS[pageData][name].".php";
//        $forget = $_GET[forget];
//        if ($forget) {
//            if ($_POST[forgetCancel]) {
//                $str .= cms_infoBox_str("Passwort vergessen abgebrochen");
//                reloadPage($goPage,2);
//                return $str;
//            }
//            $goPage = $goPage."?forget=1";
//            $str .= "<form action='$goPage' method='post'>";
//            $str .= "email oder Benutzername:</br>";
//            $str .= "<input type='text' value='$forgetStr' name='forgetStr' /><br />";
//           
//            $str .= "<input type='submit' class='mainInputButton' value='eMail senden' name='forgetSend' />";
//            $str .= "<input type='submit' class='mainInputButton mainSecond' value='abbrechen' name='forgetCancel' />";
//            $str .= "</form>";
//        } else {
//            $goPage = $goPage."?forget=1";
//            $str = "<a href='$goPage' class='cmsUser_forgetLink' >Passwort vergessen</a>";
//        }
        return $str;
    }
    
    function show_register($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $goPage = $GLOBALS[pageData][name].".php";
        // show_array($data);
        // Create SHOWLIST
        $showList = $this->get_showList($data);
        $str = "";
        
        if ($_POST[registerUser]) {
            $saveData = $_POST[saveData];
            $saveData = $this->convert_userData($saveData,$showList);
            $errorList = $this->check_userData($saveData,$showList,"new");
            
            
            
            
            
            
            // echo ("CHECK ERRORLIST $errorList <br>");
            if ($errorList === 0) {
                // echo ("NO ERROR<br>");
                $saveData[userLevel] = 1;
                $saveData[confirm] = cmsUser_getConfirmCode();
                $saveData[lastMod] = cmsUser_lastMod();
                $saveData[changeLog] = cmsUser_changeLog("userCreate",$userData[changeLog]);

                $saveResult = cmsUser_save($saveData);
                
                if ($saveResult) {
                    $userId = $saveData[id];
                    if (!$userId) {
                        echo ("KEINE USERID $userId -> saveResult =$saveResult <br>");
                        $saveData[id] = $saveResult;
                    }
                        
              
                    
                    $sendMailResult = $this->show_emailSend("first",$saveData, $contentData, $frameWidth,"silent");
                    
                    $outPut = "Sie haben sich erfolgreich auf unserer Seite Registriert<br />Sie erhalten in Kürze ein eMail um Ihre eMail-Adresse zu bestättigen.";
                    
                    $registerAction = $this->show_register_action($contentData,$saveData);
                    $registerAction = $this->show_register_action($contentData,$saveData);
                    if ($registerAction) {
                        // echo ("Register Action = $registerAction <br>");
                        if (is_string($registerAction)) $outPut = $registerAction;
                        if (is_array($registerAction)) {
                            if ($registerAction[error]) {
                                cms_errorBox ($registerAction[error]);
                                $outPut .= "";
                            }
                            if (is_string($registerAction[outPut])) $outPut .= "<br />".$registerAction[outPut];
                        }
                    }
                    
                    // $str.= "sendResult $sendMailResult <br />";
                    if ($outPut) cms_infoBox($outPut);
                    
                    $doLogin = 1;
                    if ($doLogin) {
                        $loginResult = cmsUser_doLogin($saveData);
                    }
                    reloadPage($page,3);
                    return "break";
                    
                } else {
                    $str .= cms_errorBox_str("Benutzer nicht gespeichert!");
                }
                
            } else {
                // $str .= cms_errorBox_str("Fehler beim Benutzeranlegen");
                // show_array($errorList);
            }
            

        }
       
        $str .= "<h1>Registrieren</h1>";
        
        // show_array($showList);
        $str .= "<form action='$goPage' method='post'>";
        
        $str .= $this->show_editUserName_input($saveData,$contentData,$frameWidth,$showList,$errorList);
        $str .= $this->show_editEmail_input($saveData,$contentData,$frameWidth,$showList,$errorList);
        $str .= $this->show_editPassword_input($saveData,$contentData,$frameWidth,$showList,$errorList);
        $str .= $this->show_editAdress_input($saveData, $contentData, $frameWidth,$showList,$errorList);
        $str .= $this->show_editContact_input($saveData, $contentData, $frameWidth,$showList,$errorList);
        $str .= "<input type='submit' class='mainInputButton' value='Registrieren' name='registerUser' />";
        $str .= "</form>";
        
        return $str;
    }
    
    function show_register_action($contentData,$saveData) {
        return 0;
    }

    
    function convert_userData($saveData,$showList=array()) {
        $saveData = cmsUser_convert($saveData,$showList);
        return $saveData;
        return $saveData;
        foreach ($saveData as $key => $value) {
            if (is_array($value)) {
                // echo ("ARRAY in $key <br>");
                // show_array($value);
                // echo ("showData<br>");
                //show_array($showList[$key]);
                
                switch ($key) {
                    case "phone" :
                        $newStr = "";
                        foreach ($value as $key2 => $value2) {
                            if ($newStr) $newStr.= "|";
                            $newStr .= $value2;
                        }
                        // echo ("NEW STRING for $key = '$newStr' <br>");
                        $saveData[$key] = $newStr;
                        break;
                    case "fax" :
                        $newStr = "";
                        foreach ($value as $key2 => $value2) {
                            if ($newStr) $newStr.= "|";
                            $newStr .= $value2;
                        }
                        // echo ("NEW STRING for $key = '$newStr' <br>");
                        $saveData[$key] = $newStr;
                        break;
                    case "mobil" :
                        $newStr = "";
                        foreach ($value as $key2 => $value2) {
                            if ($newStr) $newStr.= "|";
                            $newStr .= $value2;
                        }
                        // echo ("NEW STRING for $key = '$newStr' <br>");
                        $saveData[$key] = $newStr;
                        break;
                        
                }
                
            }
            switch ($key) {
                case "name" :
                    $posLeer = strpos($value," ");
                    if ($posLeer) {
                        $vName = substr($value,0,$posLeer);
                        $nName = substr($value,$posLeer+1);
                    } else {
                        $vName = "";
                        $nName = $value;
                    }
                    //echo ("SPliT NAME '$value' => '$vName' '$nName' <br> ");
                    $saveData[vName] = $vName;
                    $saveData[nName] = $nName;
                    unset($saveData[$key]);
                    break;

                case "streetSingle" :
                    $posLeer = strpos($value," ");
                    if ($posLeer) {
                        $street = substr($value,0,$posLeer);
                        $streetNr = substr($value,$posLeer+1);
                    } else {
                        $street = $value;
                        $streetNr = "";
                    }
                    // echo ("SPliT Street '$value' => '$street' '$streetNr' <br> ");
                    $saveData[street] = $street;
                    $saveData[streetNr] = $streetNr;
                    unset($saveData[$key]);
                    break;
                    
                case "citySingle" :
                    $posLeer = strpos($value," ");
                    if ($posLeer) {
                        $plz = substr($value,0,$posLeer);
                        $city = substr($value,$posLeer+1);
                    } else {
                        $city = $value;
                        $plz = "";
                    }
                    // echo ("SPliT CITY '$value' => '$plz' '$city' <br> ");
                    $saveData[plz] = $plz;
                    $saveData[city] = $city;
                    unset($saveData[$key]);
                    break;     
                    
                    
            }
            // echo ("convert $key => $value <br>");
        }
        
//        echo ("<h1> NEW $saveData[phone] </h1>");
//        echo ("<h1> NEW $saveData[fax] </h1>");
        
        return $saveData;
    }
    
    
    function check_userData($saveData,$showList=array(),$mode="edit") {
        $error = cmsUser_checkData($saveData,$showList,$mode);
        return $error;
        $error = array();
        
        foreach ($showList as $key => $value) {
            $show = 1;
            $need = $value[need];
            $view = $value[view];
            $name = $value[name];
            $length = 0;
            
            $value = $saveData[$key];
            if ($need) {
                if (!$value) {
                    $ok = 0;
                    switch ($key) {
                        case "name" :
                            $vName = $saveData[vName];
                            $nName = $saveData[nName];
                            if ($vName AND $nName) {
                                // echo ("CHECK $nName $vName <br>");
                                $ok = 1;
                            }
                            break;
                    }
                    if ($ok) {
                        
                    } else {
                    // empty but need
                        $error[$key] = "Hier ist eine Eingabe erforderlich";
                    }
                    // echo ($error[$key]);
                } else {
                    switch ($key) {
                        case "userName" : 
                            $length = 6;
                            break;
                        case "password" : 
                            $length = 6;
                            break;
                        
                       
                    }
                    
                    if ($length) {
                        if (strlen($value) < $length) {
                            $error[$key] .= "Die Eingabe muss mindestens $length Zeichen haben";
                        }
                    }
                    
                    if (!$error[$key]) {
                        switch ($key) {
                            case "userName" : 
                                if ($mode == "new") {
                                    $findUser = cmsUser_find($value);
                                    if (is_array($findUser)) {
                                        $error[$key] .= "Der Benutzername '$value' ist bereits vergeben";
                                    }
                                }
                                break;
                                
                            case "email" :
                                $res = cmsUser_checkMailAdress($value);
                                if ($res) $error[$key] .= $res;
                                if ($mode == "new") {
                                    $findUser = cmsUser_find($value);
                                    if (is_array($findUser)) {
                                        $error[$key] .= "Die eMail-Adresse '$value' ist bereits vergeben";
                                    }
                                }
                                break;
                        }
                    }
                    
                    
                }
            }
            
        }
        
        if (count($error)) return $error;
        return 0;
    }


    function show_confirmLink($contentData,$frameWidth) {
        echo ("<h1>CONFIRM</h1>");
        $email = $_GET[email];
        $confirm = $_GET[confirm];
        
        $page = $GLOBALS[pageData][name].".php";
        
        
//        echo ("eMail:$email<br />");
//        echo ("Confirm:'$confirm'<br>");
        
        $userData = cmsUser_get(array("email"=>$email));
        if (is_array($userData)) {
            // echo ("<h2>USER mit email gefunden</h2>");
            $userConfirm = $userData[confirm];
            if (strpos($userConfirm,"|")) {
                list($oldConfirm,$newConfirm) = explode("|",$userConfirm);
                $userConfirm = $oldConfirm;
            } 
            $userId = $userData[id];
            if ($userConfirm === $confirm) {
                // echo ("User Confirm: '$userConfirm'");
               
                $userMode = "confirmEmail";
            } else {
                if ($userConfirm === "1") {
                    $userId = $userData[id];
                    cms_infoBox("Die Angegebene eMail Adresse ist bereits bestättigt");
                    reloadPage($page,5);
                    return 0;
                } else {
                    cms_errorBox("Die Bestättigung Ihrer eMail Adresse ist fehlgeschlagen<br />");
                    reloadPage($page,10);
                    return 0;
                }
                echo ("DIFFRENT CONFIRM $userConfirm $confirm<br>");
                $userMode = "confirmEmail";
            }
        }
        
        if (!$userId) {
            // SERACH IN NEW-EMAIL
            $userData = cmsUser_get(array("newEmail"=>$email));
            if (is_array($userData)) {
                // echo ("<h2>USER mit NEW-Email gefunden</h2>");
                $userConfirm = $userData[confirm];
                if (strpos($userConfirm,"|")) {
                    list($oldConfirm,$newConfirm) = explode("|",$userConfirm);
                    $userConfirm = $newConfirm;
                }
                if ($userConfirm === $confirm) {
                    // echo ("User Confirm: '$userConfirm'");
                    $userId = $userData[id];
                    $userMode = "changeEmail";
                }
            }
        }
        
        
        // echo ("UserId = $userId // $userMode <br>");
        
        if (!$userId) {
            cms_errorBox("eMail Adresse nicht gefunden <br>");
            return 0;
        }
        if (!$userMode) {
            cms_errorBox("unbekannter Bestättigungs Modus");
            return 0;
        }
        
       
        
        $saveData = array("id"=>$userId,"confirm"=>"1");
        switch ($userMode) {
            case "confirmEmail" :
                
                break;
            case "changeEmail" :
                $newMail = $userData[newEmail];
                $saveData[email] = $newMail;
                $saveData[newEmail] = "not";
        }
        
        $saveData[lastMod] = cmsUser_lastMod();
        $saveData[changeLog] = cmsUser_changeLog($userMode,$userData[changeLog]);
        
        // show_array($saveData);
        
        
        $res = cmsUser_save($saveData);
        // echo ("SAVE RESULT = $res <br />");

        if ($res) {
            cms_infoBox("eMail Adresse wurde bestättigt<br>");
            reloadPage($page,0);
            return 0;
        } else {
            cms_errorBox("Fehler beim Bestättigen der eMail");
        }
    }
    
    function show_emailResetLink($contentData,$frameWidth) {
        echo ("<h1>show_emailResetLink</h1>");
        $email = $_GET[email];
        echo ("email:$email<br>");
        if (!$email) {
            cms_errorBox("Keine eMail erhalten");
            return 0;
        }
        
        $userData = cmsUser_get(array("newEmail"=>$email));
        if (!is_array($userData)) {
            cms_errorBox("Benutzer daten nicht erhalten");
            return 0;
        }
        
       
    
        $userConfirm = $userData[confirm];
        if (strpos($userConfirm,"|")) {
            list($oldConfirm,$newConfirm) = explode("|",$userConfirm);
            $setConfirm = $oldConfirm;
        }
        $userId = $userData[id];
        
        $page = $GLOBALS[pageData][name].".php";
        
        if (!$setConfirm) {
            cms_errorBox("Kein Bestättingscode für alte eMail adresse gefunden <br>");
            return 0;
        }
        
        $saveData = array("id"=>$userId, "confirm"=>$setConfirm);
        
        $saveData[newEmail] = "not";
        $saveData[lastMod] = cmsUser_lastMod();
        $saveData[changeLog] = cmsUser_changeLog("cancelEmailChange",$userData[changeLog]);
        
        // show_array($saveData);
        
        
        $res = cmsUser_save($saveData);
        // echo ("SAVE RESULT = $res <br />");

        if ($res) {
            cms_infoBox("eMail Adresse wurde verworfen<br />Ihr alte eMail Adresse ($userData[email] ist weiterhin gültig");
            reloadPage($page,2);
            return 0;
        } else {
            if ($_SESSION[userLevel]>9) show_array($saveData);
            cms_errorBox("Fehler beim verwerfen der neuen eMail Adresse");
        }
        return 0;
    }
            

    function get_StyleData() {
        $infoWidth = 200;
        $dataWidth = 300;
        $infoStyle = "width:".($infoWidth-10)."px;text-align:right;margin-right:10px;";
        $dataStyle = "width:".$dataWidth."px;text-align:left;margin-right:10px;font-weight:bold;";
        
        $res = array();
        $res[infoStyle] = array("class"=>"cmsUser_infoSpan","style"=>"css",);
        $res[dataStyle] = array("class"=>"cmsUser_dataSpan","style"=>"css"); // $dataStyle;
        $res[errorStyle] = array("class"=>"cmsUser_errorSpan","style"=>"padding-left:200px;"); // $dataStyle;
        return $res;
        
    }
    
    function get_infoStyle() {
        $res = $this->get_StyleData();
        return ($res[infoStyle]);
    }
    
    function get_dataStyle() {
        $res = $this->get_StyleData();
        return ($res[dataStyle]);
    }
    
    function get_errorStyle() {        
        $res = $this->get_StyleData();
        return ($res[errorStyle]);
    }
    
    function get_showList($data) {
        $showList = array();
        $editList = $this->user_edit_userShowList();
        foreach ($data as $key => $value) {
            if (substr($key,0,5)== "show_") {
                $showName = substr($key,5);
                $show = $value;
                if ($show) $showList[$showName] = array();
                $need = $data["need_".$showName];
                if ($need) $showList[$showName][need] = $need;
                $view = $data[$showName."_view"];
                if ($view) $showList[$showName][view] = $view;
                
                if (is_array($editList[$showName])) {
                    $showList[$showName][name] = $editList[$showName][name];                 
                }
            }            
        }
        return $showList;
    }
    
    function show_userLogin($contentData,$frameWidth) {
        $showLevel = $_SESSION[showLevel];
        if (is_null($showLevel)) $showLevel = 0;
        if (!$showLevel) {
            $this->show_noUser($contentData,$frameWidth);
            return 0;
        }
        
        $userId = $_SESSION[userId];
        $userData = cmsUser_get(array("id"=>$userId));


        $divMainName = "cmsUser cmsUserLogin";
        $divMainData = array("style"=>"width:".$frameWidth."px;");
        div_start($divMainName,$divMainData);
        echo ("<h1>Sie sind angemeldet</h1>");
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        
        
        if ($data[dataLogout]) {
            $logout = $_POST[logout];
            
            if (is_array($logout)) {
                $logoutResult = cms_user_logout($userId);
                if ($logoutResult == 1) {
                    cms_infoBox("Sie haben sich erfolgreich abgemeldet");

                    reloadPage($pageInfo[page],2);
                    div_end($divMainName);
                    return 1;
                } else {
                    cms_errorBox("Fehler beim abmelden");
                }
            }
            
            div_start("cmsUser_logout");
            
            echo ("<form method='post'>");
            echo ("<input class='mainInputButton logout mainSecond' type='submit' value='abmelden' name='logout[logout]'>");
            echo ("</form>");
            div_end("cmsUser_logout");
        }
        div_end($divMainName);
        
    }
    
    function show_userCenter($contentData,$frameWidth) {

        $showLevel = $_SESSION[showLevel];
        if (is_null($showLevel)) $showLevel = 0;
        if (!$showLevel) {
            $this->show_noUser($contentData,$frameWidth);
            return 0;
        }

        $userId = $_SESSION[userId];
        $userData = cmsUser_get(array("id"=>$userId));


        $divMainName = "cmsUser cmsUserCenter";
        $divMainData = array("style"=>"width:".$frameWidth."px;");
        div_start($divMainName,$divMainData);
        echo ("<h1>Benutzer Zentrum</h1>");
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $showList = $this->get_showList($data);
        $view = $_GET[view];
        switch ($view) {
            case "editAdress" :
                if ($data[dataSubPageEdit]) {
                    $res = $this->show_editAdress($contentData,$frameWidth,$showList);
                    if ($res) {
                        div_end($divMainName);
                        return $res;
                    }
                }
                
            case "editContact" :
                if ($data[dataSubPageEdit]) {
                    $res = $this->show_editContact($contentData,$frameWidth,$showList);
                    if ($res) {
                        div_end($divMainName);
                        return $res;
                    }
                }
            
            case "password" :
                $this->show_changePassword($contentData,$frameWidth);
                div_end($divMainName);
                return $res;                    
                break;
            case "username" :
                $this->show_changeUserName($contentData,$frameWidth);
                div_end($divMainName);
                return $res;    
                break;
            case "email" :
                $this->show_changeEmail($contentData,$frameWidth);
                div_end($divMainName);
                return $res;    
                break;
            case "resendConfirm" :
                $res = $this->show_emailSend("second",$userData,$contentData, $frameWidth);
                break;
            
            case "sendConfirm" :
                $res = $this->show_emailSend("first",$userData,$contentData, $frameWidth);
                break;
            
            case "emailReset" :
                $res = $this->show_emailReset($contentData,$frameWidth);
                if ($res) {
                     div_end($divMainName);
                     return 1;
                }
                break;           

        }
        
        
        $logout = $_POST[logout];
        if (is_array($logout)) {
            $logoutResult = cms_user_logout($userId);
            if ($logoutResult == 1) {
                cms_infoBox("Sie haben sich erfolgreich abgemeldet");

                reloadPage($pageInfo[page],2);
                div_end($divMainName);
                return 1;
            } else {
                cms_errorBox("Fehler beim abmelden");
            }
        }
        
        
        // show_array($data);
        
     
        $page = $GLOBALS[pageData][name].".php";
       
        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();
       
        
        if (!$data[dataSubPageEdit]) {
            // echo ("<h1>DIRECTES EDITIEREN </h1>");
            echo ("<form action='$page' method='post'>");
            $directEdit = 1;
            $saveData = $userData;
            if ($_POST[changeAdress]) {
                if ($_POST[saveData]) {
                    $changeData = $_POST[saveData];
                    $changeData = $this->convert_userData($changeData, $showList);
                    foreach ($changeData as $key => $value) {
                        $saveData[$key] = $value;
                    }
                    $errorList = $this->check_userData($changeData, $showList,"edit");
                    if ($errorList === 0) {
                        $changeData[lastMod] = cmsUser_lastMod();
                        $changeData[changeLog] = cmsUser_changeLog("changeAdress",$userData[changeLog]);
                        $saveResult = cmsUser_save($changeData);
                        if ($saveResult) {
                            cms_infoBox("Adresse wurde gespeichert");
                            reloadPage($goPage,2);
                            div_end($divMainName);
                            return 1;
                        } else {
                            cms_errorBox("Fehler beim speichern der Adresse");

                        }
                    }
                }
            }
            
            if ($_POST[cancelAdress]) {
                cms_infoBox("Adresse speichern abgebrochen");
                reloadPage($page,2);
            }
        }
        
        
       

        // show_array($data);
        // echo ("<h3>Benutzer Daten</h3>");
        
        // show_array($showList);
        
        $divData = array("style"=>"width:".$frameWidth."px;");
        
        // USERNAME
        if (is_array($showList[userName])) {
            div_start("cmsUserLine cmsUserLine_userName",$divData);
            span_text("Benutzername:",$spanStyle);
            $out = $userData[userName];
            span_text($out,$dataStyle);
            if ($data[dataUsername]) {
                echo ("<a href='".$page."?view=username' title='Benutzername ändern' style='width:100px;' class='mainSmallButton' >ändern</a>");
            }
            div_end("cmsUserLine cmsUserLine_userName");
        }

        // EMAIL
        if (is_array($showList[email])) {
            div_start("cmsUserLine cmsUserLine_email",$divData);
            span_text("eMail:",$spanStyle);
            $out = $userData[email];
            span_text($out,$dataStyle);
            if ($data[dataEmail]) {
                $confirm = $userData[confirm];
                // echo ("<b> CONFIRM = $confirm </b>");
                $showConfirm = 0;
                switch ($confirm) {
                    case "1" : break;
                    case "0" : $showConfirm=1; break;    
                    default :
                        list($oldConfirm,$newConfirm) = explode("|",$confirm);

                        if ($oldConfirm AND $newConfirm) {
                            // echo ("OLD = $oldConfirm / NEW = $newConfirm ");
                            if ($oldConfirm != "1") $showConfirm = 1;
                        } else {
                            $confirm = "0";
                        }                    
                }

                if (!$userData[newEmail]) {
                    echo ("<a href='".$page."?view=email' title='eMail Adresse ändern' style='width:100px;' class='mainSmallButton mainSecond'>ändern</a>");
                    if ($showConfirm) {
                        echo ("<br />");
                        span_text("&nbsp;",$spanStyle);
                        $out = "Ihre eMail adresse ist noch nicht bestättigt";
                        span_text($out,$dataStyle);
                        echo ("<a href='".$page."?view=sendConfirm' title='Bestättigungs eMail erneut versenden' style='width:100px;' class='mainSmallButton mainSecond'>erneut Senden</a>");                    
                    }
                }

                if ($userData[newEmail]) {
                    echo ("<br>");
                    span_text("neue eMail:",$spanStyle);
                    $out = $userData[newEmail];
                    span_text($out,$dataStyle);
                    echo ("<a href='".$page."?view=emailReset' title='eMail Adresse ändern abbrechen' style='width:100px;' class='mainSmallButton mainSecond'>abbrechen</a>");
                    echo ("<a href='".$page."?view=resendConfirm' title='Bestättigungsemail erneut versenden' style='width:100px;' class='mainSmallButton mainSecond'>erneut Senden</a>");
                }
            }
            div_end("cmsUserLine cmsUserLine_email");
        }
        
        // PASSWORD
        if (is_array($showList[password])) {
            div_start("cmsUserLine cmsUserLine_password",$divData);
            span_text("Passwort:",$spanStyle);
            $out = "";
            for($i=0;$i<strlen($userData[password]);$i++) $out .= "*";
            span_text($out,$dataStyle);
            if ($data[dataPass]) {
                echo ("<a href='".$page."?view=password' title='Passwort ändern' style='width:100px;' class='mainSmallButton mainSecond'>ändern</a>");
            }
            div_end("cmsUserLine cmsUserLine_password");
        }
        
        // ADRESSE
        div_start("cmsUserLine cmsUserLine_adress",$divData);
        
        if ($directEdit) {
            $res = $this->show_editAdress_input($saveData,$contentData, $frameWidth,$showList,$errorList);
            echo ($res);
                    
        } else {
            if (is_array($showList[salut])) {
                span_text("Anrede:",$spanStyle);
                $salut = cmsUser_getSalut($userData[salut]);
                span_text($salut,$dataStyle);                    
            }
            if (is_array($showList[name])) {
                echo ("<br />");
                span_text("Name:",$spanStyle);
                $out = $userData[vName]." ".$userData[nName];
                span_text($out,$dataStyle);                
            }

            if (is_array($showList[street])) {
                echo ("<br />");
                span_text("Adresse:",$spanStyle);
                $out = $userData[street]." ".$userData[streetNr];
                span_text($out,$dataStyle);               
            }
            
            if (is_array($showList[city])) {
                echo ("<br />");
                span_text("Ort:",$spanStyle);
                $out = $userData[plz]." ".$userData[city];
                span_text($out,$dataStyle);                
            }

            if (is_array($showList[county])) {
                echo ("<br />");
                span_text("Land:",$spanStyle);
                $out = cmsUser_getCountryName($userData[country]);
                span_text($out,$dataStyle);
            }
            
            if ($data[dataEdit]) {
                echo ("<a href='".$page."?view=editAdress' title='Adressdaten ändern' style='width:100px;' class='mainSmallButton mainSecond'>ändern</a>");
            }            
        }
        div_end("cmsUserLine cmsUserLine_adress");
        
        // CONTACT
        
        
        
        $outContact = "";

        if ($directEdit) {
            $outContact =  $this->show_editContact_input($saveData,$contentData, $frameWidth,$showList,$errorList);
            
        } else {
            $showContactCount = 0;
            if (is_array($showList[phone])) {
                if ($showContactCount) $outContact .= "<br />";
                $outContact.= span_text_str("Telefon:",$spanStyle);
                $out = str_replace("|"," ",$userData[phone]);
                $outContact.= span_text_str($out,$dataStyle);
                $showContactCount++;
            }

            if (is_array($showList[fax])) {
                if ($showContactCount) $outContact .= "<br />";
                $outContact.= span_text_str("Fax:",$spanStyle);
                $out = str_replace("|"," ",$userData[fax]);
                $outContact.= span_text_str($out,$dataStyle);
                $showContactCount++;
            }

            if (is_array($showList[mobil])) {
                if ($showContactCount) $outContact .= "<br />";
                $outContact.= span_text_str("Mobil:",$spanStyle);
                $out = str_replace("|"," ",$userData[mobil]);
                $outContact.= span_text_str($out,$dataStyle);
                $showContactCount++;
            }
            
            // foreach ($showList[url] as $k => $v) $outContact .= "showlist $k = $v <br>";
            if (is_array($showList[url])) {
                if ($showContactCount) $outContact .= "<br />";
                $outContact.= span_text_str("Webseite:",$spanStyle);
                $out = str_replace("|"," ",$userData[url]);
                $outContact.= span_text_str($out,$dataStyle);
                $showContactCount++;
            }
            
            if ($data[dataEdit] AND $showContactCount) {
                $outContact .= "<a href='".$page."?view=editContact' title='Kontaktdaten ändern' style='width:100px;' class='mainSmallButton mainSecond'>ändern</a>";
            }        
        }
        if ($outContact) {
            div_start("cmsUserLine cmsUserLine_contact",$divData);
            echo ($outContact);
            div_end("cmsUserLine cmsUserLine_contact"); 
        }
        
        
        if ($directEdit) {
            div_start("cmsUser_buttons");
            echo ("<input type='submit' class='mainInputButton' value='Adresse speichern' name='changeAdress' />");
            echo ("<input type='submit' class='mainInputButton mainSecond'value='abbrechen' name='cancelAdress' /><br />");
            div_end("cmsUser_buttons");
            echo ("</form>\n");
        }
        
        if ($data[dataLogout]) {
            div_start("cmsUser_logout");
            
            
            
            echo ("Hier abmelden");
            echo ("<form method='post'>");
            echo ("<input class='mainInputButton logout mainSecond' type='submit' value='abmelden' name='logout[logout]'>");
            echo ("</form>");
            div_end("cmsUser_logout");
        }
        div_end($divMainName);
    }

    function show_editUserName_input($saveData,$contentData,$frameWidth,$showList=array(),$errorList=array()){
        $str = "";
//        
//        $spanStyle = $this->get_infoStyle();
//        $dataStyle = $this->get_dataStyle();
//        $errorStyle = $this->get_errorStyle();
//        
//        // $str .= "<b>$key</b> $show / $need / $view <br> ";
//        
//        
//        $userId = $saveData[id];
//        $str .= "<input type='hidden' name='saveData[id]' value='$userId' />";
//        
//        
        $dataName = "saveData";
        $styleList = $this->get_StyleData();
        $styleList[dataWidth] = 300;
        $styleList[inputAbs] = 10;
        
        // SALUT
        $key = "userName";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        return $str;
    }

    function show_editEmail_input($saveData,$contentData,$frameWidth,$showList=array(),$errorList=array()){
        $str = "";
        
        $dataName = "saveData";
        $styleList = $this->get_StyleData();
        $styleList[dataWidth] = 300;
        $styleList[inputAbs] = 10;
        
        // SALUT
        $key = "email";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        return $str;
        
        
        $key = "email";
        $show = 0;
        if (is_array($showList[$key])) {
            $show = 1;
            $need = $showList[$key][need];
            $view = $showList[$key][view];                        
        }
        
        if (!$show) {
            return $str;             
        }
        
        $error = $errorList[$key];
        
        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();
        $errorStyle = $this->get_errorStyle();
        
        // $str .= "<b>$key</b> $show / $need / $view <br> ";
        
        if ($need) {
            $spanStyle["class"] .= " cmsUser_needSpan";           
        }
        
        if ($error) {
            $str .= div_start_str("cmsUserError");
            $str .= span_text_str($error,$errorStyle);           
        }
        
       
        $str .= span_text_str("eMail-Adresse:",$spanStyle);
        $str .= "<input type='text' name='saveData[email]' style='width:200px;' value='$saveData[email]' />";
        $str .= "<br />";
        if ($error) {
            $str .= div_end_str("cmsUserError");
        }
        return $str;
    }

    function show_editPassword_input($saveData,$contentData,$frameWidth,$showList=array(),$errorList=array()){
        
        $str = "";
        
        $dataName = "saveData";
        $styleList = $this->get_StyleData();
        $styleList[dataWidth] = 300;
        $styleList[inputAbs] = 10;
        
        // SALUT
        $key = "password";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        return $str;
        
//        $str = "";
//        $key = "password";
//        $show = 0;
//        if (is_array($showList[$key])) {
//            $show = 1;
//            $need = $showList[$key][need];
//            $view = $showList[$key][view];            
//            // show_array($showList[$key]);
//        }
//        
//        if (!$show) {
//            return $str;             
//        }
//        
//        $error = $errorList[$key];
//        
//        $spanStyle = $this->get_infoStyle();
//        $dataStyle = $this->get_dataStyle();
//        $errorStyle = $this->get_errorStyle();
//        
//        // $str .= "<b>$key</b> $show / $need / $view <br> ";
//        
//        if ($need) {
//            $spanStyle["class"] .= " cmsUser_needSpan";           
//        }
//        
//        if ($error) {
//            $str .= div_start_str("cmsUserError");
//            $str .= span_text_str($error,$errorStyle);           
//        }
//        
//        $str .= span_text_str("Passwort:",$spanStyle);
//        $str .= "<input type='password' name='saveData[password]' style='width:200px;' value='$saveData[password]' />";
//        $str .= "<br />";
//        if ($error) {
//            $str .= div_end_str("cmsUserError");
//        }
//        return $str;
    }




    function show_editAdress($contentData,$frameWidth,$showList=array()) {
        echo ("<h3>Adresse bearbeiten</h3>");
        $view = "editAdress";
        
        $show = array("salut","name","company","street","city","country");
        
        $myShow = array();
        for ($i=0;$i<count($show);$i++) {
            $myKey = $show[$i];
            $myShow[$myKey] = $showList[$myKey];
        }

        $userId = $_SESSION[userId];
        $saveData = cmsUser_get(array("id"=>$userId));
        
        $goPage = $GLOBALS[pageData][name].".php";
        
        if ($_POST[cancelAdress]) {
            cms_infoBox("Adresse bearbeiten abgebrochen");
            reloadPage($goPage,2);
            return 1;
        }
        
        if ($_POST[changeAdress]) { 
            if ($_POST[saveData]) {
                $changeData = $_POST[saveData];
                $changeData = $this->convert_userData($changeData);
                $errorList = $this->check_userData($changeData, $myShow,"edit");
                if (is_array($errorList)) {
                    show_array($errorList);
                }
                foreach ($changeData as $key => $value) {
                    // echo ("Save $key = '$value' <br> ");
                    $saveData[$key] = $value;
                }
                if ($errorList === 0) {
                    // KEINE FEHLER
                    $changeData[lastMod] = cmsUser_lastMod();
                    $changeData[changeLog] = cmsUser_changeLog("changeAdress",$userData[changeLog]);
                    $saveResult = cmsUser_save($changeData);
                    if ($saveResult) {
                        cms_infoBox("Adresse wurde gespeichert");
                        reloadPage($goPage,2);
                        return 1;
                    } else {
                        cms_errorBox("Fehler beim speichern der Adresse");

                    }
                }
                
            }
        }
        
        
        
        
        echo ("<form method='post' action='".$goPage."?view=$view'>");
        
        
        $out = $this->show_editAdress_input($saveData,$contentData,$frameWidth,$myShow,$errorList);
        echo ($out);

        div_start("cmsUser_buttons");
        echo ("<input type='submit' class='mainInputButton' value='Adresse speichern' name='changeAdress' />");
        echo ("<input type='submit' class='mainInputButton mainSecond'value='abbrechen' name='cancelAdress' /><br />");
        div_end("cmsUser_buttons");
        
        echo ("</form>");
        
        
        return 1;
    }
    
    
   
    
    
    function show_editAdress_input($saveData,$contentData, $frameWidth,$showList=array(),$errorList=array()) {    
        
        $str = "";
        
        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();
        $errorStyle = $this->get_errorStyle();
        
        // $str .= "<b>$key</b> $show / $need / $view <br> ";
        
        
        $userId = $saveData[id];
        $str .= "<input type='hidden' name='saveData[id]' value='$userId' />";
        
        
        $dataName = "saveData";
        $styleList = $this->get_StyleData();
        $styleList[dataWidth] = 300;
        $styleList[inputAbs] = 10;
        
        // SALUT
        $key = "salut";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        // NAME
        $key = "name";
        $value = array("vName"=>$saveData[vName],"nName"=>$saveData[nName]); 
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        // Straße
        $key = "street";
        $value = array("street"=>$saveData[street],"streetNr"=>$saveData[streetNr]);
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        // CITY
        $key = "city";         
        $value = array("plz"=>$saveData[plz],"city"=>$saveData[city]);
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        // County
        $key = "country";
        $value = $saveData[country];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        return $str;
    }
   
    function show_editContact($contentData,$frameWidth,$showList=array()) {
        echo ("<h3>Kontaktdaten bearbeiten</h3>");        
      
        $view = "editContact";
        $userId = $_SESSION[userId];
        $saveData = cmsUser_get(array("id"=>$userId));
        
        $goPage = $GLOBALS[pageData][name].".php";
        
        $show = array("phone","fax","mobil","url");
        
        $myShow = array();
        for ($i=0;$i<count($show);$i++) {
            $myKey = $show[$i];
            $myShow[$myKey] = $showList[$myKey];
        }

       
        
        if ($_POST[cancelAdress]) {
            cms_infoBox("Adresse bearbeiten abgebrochen");
            reloadPage($goPage,2);
            return 1;
        }
        
        if ($_POST[changeAdress]) { 
            if ($_POST[saveData]) {
                
                $changeData = $_POST[saveData];
                $changeData = $this->convert_userData($changeData);
                $errorList = $this->check_userData($changeData, $myShow,"edit");
                foreach ($changeData as $key => $value) {
                    // echo ("Save $key = '$value' <br> ");
                    $saveData[$key] = $value;
                }
                if ($errorList === 0) {
                    // KEINE FEHLER
                    $changeData[lastMod] = cmsUser_lastMod();
                    $changeData[changeLog] = cmsUser_changeLog("changeContact",$userData[changeLog]);
                    $saveResult = cmsUser_save($changeData);
                    if ($saveResult) {
                        cms_infoBox("Kontaktdaten wurden gespeichert");
                        reloadPage($goPage,2);
                        return 1;
                    } else {
                        cms_errorBox("Fehler beim speichern der Kontaktdaten");

                    }
                }
            }
        }
        
        
        
        
        echo ("<form method='post' action='".$goPage."?view=$view'>");
        
        $out = $this->show_editContact_input($saveData,$contentData,$frameWidth,$myShow,$errorList);
        echo ($out);
        
        div_start("cmsUser_buttons");
        echo ("<input type='submit' class='mainInputButton' value='Kontaktdaten speichern' name='changeAdress' />");
        echo ("<input type='submit' class='mainInputButton mainSecond'value='abbrechen' name='cancelAdress' /><br />");
        div_end("cmsUser_buttons");
        echo ("</form>");
        
        return 1;
    }
    
    function show_editContact_input($saveData,$contentData,$frameWidth,$showList=array(),$errorList=array(),$inputWidth=200) {
        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();
        $errorStyle = $this->get_errorStyle();
        
        $userId = $saveData[id];
        $str .= "<input type='hidden' name='saveData[id]' value='$userId' />";

        $dataName = "saveData";
        $styleList = $this->get_StyleData();
        $styleList[dataWidth] = 300;
        $styleList[inputAbs] = 10;
        
        // Telefon
        $key = "phone";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        // FAX
        $key = "fax";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;

        // MOBIL
        $key = "mobil";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        // Webseite
        $key = "url";
        $value = $saveData[$key];
        $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorList);
        $str .= $res;
        
        return $str;
    }
    
    function show_changePassword($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $view = "password";
       
        $page = $GLOBALS[pageData][name].".php";
        
        echo ("<h3>Passwort ändern</h3>");
        
        if ($_POST) {
            $oldPass = $_POST[oldPass];
            $newPass = $_POST[newPass];
            $newPass2 = $_POST[newPass2];
            
            if ($_POST[changePass]) {
                $error = "";
                if ($newPass != $newPass2) {
                    $error = "Neues Passwort ist ungleich der Wiederholung";
                } else {
                    if (strlen($newPass)<6) {
                        $error = "Neues Passwort ist zu kurz.<br />Ein Passwort muss mindestens 6 Zeichen haben";
                    } else {
                        if ($newPass == $oldPass) {
                            $error = "Neues Passwort ist gleich dem alten Passwort";
                        } else {
                        
                            $userData = cmsUser_get(array("id"=>$_SESSION[userId]));
                            if (is_array($userData)) {
                                if ($oldPass != $userData[password]) {
                                    $error = "Altes Passowrt ist nicht korrekt";
                                } else {
                                    $userId = $userData[id];
                                }
                            }
                        }
                    }
                }
                if ($error) {
                    cms_errorBox($error);
                } else {
                    echo ("Change Passwort to $newPass on User $userId <br>");
                    
                    $saveData = array("id"=>$userId,"password"=>$newPass);
                    $saveData[lastMod] = cmsUser_lastMod();
                    $saveData[changeLog] = cmsUser_changeLog("password",$userData[changeLog]);
                    $res = cmsUser_save($saveData);
                    echo ("SAVE RESULT = $res <br />");
                    
                    if ($res) {
                        cms_infoBox("Passwort wurde geändert<br>");
                        reloadPage($page,2);
                        return 0;
                    } else {
                        cms_errorBox("Fehler beim Passwort ändern");
                    }
                  
                }
            }
            if ($_POST[cancelPass]) {
                cms_infoBox("Passwort änden abgebrochen");
                reloadPage($page,2);
                return 0;
            }
        }
        
        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();

        
        echo ("<form method='post' action='".$page."?view=$view'>");
        
        span_text("Altes Passwort:",$spanStyle);
        echo ("<input type='password' name='oldPass' value='$oldPass' /><br />");
        
        
        span_text("Neues Passwort:",$spanStyle);
        echo ("<input type='password' name='newPass' value='$newPass' /><br />");
        
        span_text("Neues Passwort wiederholen:",$spanStyle);
        echo ("<input type='password' name='newPass2' value='$newPass2' /><br />");
        
        
        div_start("cmsUser_buttons");
        echo ("<input type='submit' class='mainInputButton' value='Passwort ändern' name='changePass' />");
        echo ("<input type='submit' class='mainInputButton mainSecond'value='abbrechen' name='cancelPass' />");
        div_end("cmsUser_buttons");
        echo ("</form>");
        
        
        
        
        
        
        
    }
    function show_changeUserName($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $view = "username";

        $page = $GLOBALS[pageData][name].".php";

        $userData = cmsUser_get(array("id"=>$_SESSION[userId]));

        echo ("<h3>Benutzername ändern</h3>");

        if ($_POST) {
            $newUser = $_POST[newUser];
            $newUser2 = $_POST[newUser2];

            if ($_POST[changeUser]) {
                $error = "";
                if ($newUser != $newUser) {
                    $error = "Neues Benutzername ist ungleich der Wiederholung";
                } else {
                    if (strlen($newUser)<4) {
                        $error = "Benutzername ist zu kurz <br />Benutzername muss mindestens 4 Zeichen haben";
                    } else {
                        $existUser = cmsUser_get(array("userName"=>$newUser));
                        if (is_array($existUser) OR $existUser) {
                            $error = "Benutzername ist schon vergeben";                            
                        } else {
                            if (!is_array($userData)) {
                                // echo ("$userData $_SESSION[userId] <br>");
                                $error = "Fehler beim Benutzername ändern";
                            }
                        }
                    }
                }
                if ($error) {
                    cms_errorBox($error);
                } else {
                    echo ("Change Username to $newUser on User $userId <br>");
                    $userId = $userData[id];
                    $saveData = array("id"=>$userId,"userName"=>$newUser);
                    $saveData[lastMod] = cmsUser_lastMod();
                    $saveData[changeLog] = cmsUser_changeLog("userName",$userData[changeLog]);
                    $res = cmsUser_save($saveData);
                   // echo ("SAVE RESULT = $res <br />");

                    if ($res) {
                        cms_infoBox("Benutzername wurde geändert<br>");
                        reloadPage($page,2);
                        return 0;
                    } else {
                        cms_errorBox("Fehler beim Benutzername ändern");
                    }

                }
            }
            if ($_POST[cancelUser]) {
                cms_infoBox("Benutzername änden abgebrochen");
                reloadPage($page,2);
                return 0;
            }
        }
        
        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();

        echo ("<form method='post' action='".$page."?view=$view'>");
        span_text("Alter Benutzername:",$spanStyle);
        $out = $userData[userName];
        span_text($out,$dataStyle);
        echo ("<br />");

        span_text("Neuer Benutzername:",$spanStyle);
        echo ("<input type='text' name='newUser' value='$newUser' /><br />");

        span_text("Neuen Benutzernamen wiederholen:",$spanStyle);
        echo ("<input type='text' name='newUser2' value='$newUser2' /><br />");

        div_start("cmsUser_buttons");        
        echo ("<input type='submit' class='mainInputButton' value='Benutzername ändern' name='changeUser' />");
        echo ("<input type='submit' class='mainInputButton mainSecond'value='abbrechen' name='cancelUser' />");
        div_end("cmsUser_buttons");
        echo ("</form>");
       
    }
    
    function show_changeEmail($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $view = "email";

        $page = $GLOBALS[pageData][name].".php";

        $userData = cmsUser_get(array("id"=>$_SESSION[userId]));

        echo ("<h3>eMail Adresse ändern</h3>");

        if ($_POST) {
            $newEmail = $_POST[newEmail];
            $newEmail2 = $_POST[newEmail2];

            if ($_POST[changeEmail]) {
                $error = "";
                if ($newEmail != $newEmail2) {
                    $error = "Neue eMail Adresse ist ungleich der Wiederholung";
                } else {
                    if (strlen($newEmail)<7) {
                        $error = "eMail Adresse ist zu kurz <br />";
                    } else {
                        $existUser = cmsUser_get(array("email"=>$newEmail));
                        if (is_array($existUser) OR $existUser) {
                            $error = "eMail Adresse ist schon vergeben";
                        } else {
                            if (!is_array($userData)) {
                                // echo ("$userData $_SESSION[userId] <br>");
                                $error = "Fehler beim eMail Adresse ändern";
                            }
                        }
                    }
                }
                if ($error) {
                    cms_errorBox($error);
                } else {
                    echo ("Change EMAIL to $newEmail on User $userId <br>");
                    $userId = $userData[id];
                    $saveData = array("id"=>$userId,"newEmail"=>$newEmail);
                    $oldConfirm = $userData[confirm];
                    if (!$oldConfirm) $oldConfirm = "0";
                    
                    $newConfirm = $oldConfirm."|".cmsUser_getConfirmCode();
                    $saveData[confirm] = $newConfirm;
                    
                    
                    $saveData[lastMod] = cmsUser_lastMod();
                    $saveData[changeLog] = cmsUser_changeLog("email",$userData[changeLog]);
                    $res = cmsUser_save($saveData);
                   

                    if ($res) {
                        $sendMailResult = $this->show_emailSend("second", $contentData, $frameWidth,"silent");
                        echo ("Send Mail Result = $sendMailResult <br>");
                        if ($sendMailResult) {
                            $out = "eMail Adresse wurde geändert";
                            $out .= "<br />Sie erhalten kürze eine eMail von uns zum bestättigen Ihrer neuen eMail Adrese";
                            $out .= "<br />Bis zur Bestättigung Ihrer neuen eMail Adresse bleibt Ihre alte eMail Adresse gültig.";

                            cms_infoBox($out);
                        }
                        reloadPage($page,2);
                        // ZURÜCK
                        echo ("<a href='$page' class='mainLinkButton' >zurück</a>");
                        return 0;
                    } else {
                        cms_errorBox("Fehler beim Benutzername ändern");
                    }

                }
            }
            if ($_POST[cancelEmail]) {
                cms_infoBox("eMail Adresse ändern abgebrochen");
                reloadPage($page,2);
                return 0;
            }
        }

        $spanStyle = $this->get_infoStyle();
        $dataStyle = $this->get_dataStyle();
        
        echo ("<form method='post' action='".$page."?view=$view'>");
        span_text("Alte eMail Adresse:",$spanStyle);
        $out = $userData[email];
        span_text($out,$dataStyle);
        echo ("<br />");

        span_text("Neue eMail Adresse:",$spanStyle);
        echo ("<input type='text' name='newEmail' value='$newEmail' /><br />");

        span_text("Neue eMail Adresse wiederholen:",$spanStyle);
        echo ("<input type='text' name='newEmail2' value='$newEmail2' /><br />");

        div_start("cmsUser_buttons");
        
        echo ("<input type='submit' class='mainInputButton' value='eMail Adresse ändern' name='changeEmail' />");
        echo ("<input type='submit' class='mainInputButton mainSecond'value='abbrechen' name='cancelEmail' />");
        div_end("cmsUser_buttons");
        echo ("</form>");

        
    }

    function show_emailReset($contentData,$frameWidth) {
        $userId = $_SESSION[userId];
        $userData = cmsUser_get(array("id"=>$userId));
        if ($userData[newEmail]) {
            $confirm = $userData[confirm];
            $newEmail = $userData[newEmail];
            // echo ("Reset $newEmail '$confirm' <br>");
                        
            if (strpos($confirm,"|")) {
                list($oldConfirm,$newConfirm) = explode("|",$confirm);                
            } else {
                $newConfirm = 0;
            }
            
            $saveData = array("id"=>$userId,"newEmail"=>"not");
            $saveData[confirm] = $oldConfirm;
            $saveData[lastMod] = cmsUser_lastMod();
            $saveData[changeLog] = cmsUser_changeLog("emailReset",$userData[changeLog]);
            
            $res = cmsUser_save($saveData);
            // show_array($saveData);
            if ($res) {
                cms_infoBox("Ihre eMail Änderung wurde verworfen<br/>Ihre alte eMail ist wieder uneingeschränkt gültig");
                $page = $GLOBALS[pageData][name].".php";
                reloadPage($page,2);
                return 1;
            } else {
                cms_errorBox("Fehler beim rücksetzen Ihrer eMail Adresse");                
            }
        } else {
            cms_errorBox("Keine neue eMail Adresse zum Rücksetzen gefunden<br>");
        }

        
        return 0;
    }

    function show_emailSend($type,$userData,$contentData,$frameWidth,$mode="resend") {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        if (!is_array($userData)) {
            $userId = $_SESSION[userId];
            $userData = cmsUser_get(array("id"=>$userId));
        }
        
        $sendData = array();
        $sendData[user] = $userData;
        
        switch ($type) {
            case "forget" :
                $email = $userData[email];
                $sendMailId = $data[forgotMail];
                $newPass  = "123456";
                $sendData[newPass] = $newPass;
                
                break;
            
            case "first" :
                $email = $userData[email];
                $sendMailId = $data[confirmEmail];
                
                $confirm = $userData[confirm];
                if (strpos($confirm,"|")) {
                    list($oldConfirm,$newConfirm) = explode("|",$confirm);
                    $confirm = $oldConfirm;
                }
                
                break;
            
            case "second" :
                $email = $userData[newEmail];
                $sendMailId = $data[changeEmailConfirm];
                $confirm = $userData[confirm];
                if (strpos($confirm,"|")) {
                    list($oldConfirm,$newConfirm) = explode("|",$confirm);
                    $confirm = $newConfirm;
                }
                // echo ("Send MAIL ID = $sendMailId -> confirm ='$confirm' <br>");
                break;
            default :
                echo ("unkown Type ($type) <br>");
                return 0;
        }
        
        if (!$sendMailId) {
            cms_errorBox("Keine eMail gefunden");
            return 0;
        }
        $mailData = cmsEmail_get(array("id"=>$sendMailId));
        // show_array($mailData);
        
//        foreach ($_SERVER as $key => $value) {
//            echo ("$key => $value <br>");
//        }
        $host = "http://".$_SERVER[HTTP_HOST].$_SERVER[SCRIPT_NAME];
       
        $sendData[link] = $host."?mode=confirm&email=$email&confirm=$confirm";
        $sendData[link2] = $host."?mode=emailReset&email=$email";
        
        // show_array($userData);
                
        $mailText = $mailData[info];
        $mailText = cmsEmail_convertText($mailText, $sendData);

        $subject = $mailData[subName];
        $subject = cmsEmail_convertText($subject, $sendData);

        // echo ("HEADER : $subject <br>");
        
        // echo ("TEXT : $mailText<br>");
            
        // return 0;
        if (!$email) {
            echo "Keine eMail adresse bei mailToSender <br />";
            return 0;
        }
        $res = cmsEmail_sendMail($email,$subject,$mailText,1,"utf-8");
        if ($res) {
            $goPage = $GLOBALS[pageData][name].".php";
            switch ($mode) {
                case "resend" :
                    cms_infoBox("Ihre Bestättigungs eMail wurde erneut versand");
                    reloadPage($goPage,20);
                    break;
                case "silent" :
                    break;
                case "first" :
                    cms_infoBox("Ihre Bestättigungs eMail wurde versand");
                    break;
            }
            
            
            
            return 1;
        } else {
            cms_errorBox("Ihre Bestättigungsemail konnte nicht versendet werden");
        }
        return 0;
    }

    function show_userData($contentData,$frameWidth) {
        echo ("USER DATA <br>");
    }
    
    function show_userOrder($contentData,$frameWidth) {
        echo ("<h1> BESTELLUNGEN </h1>");
    }
    
    
    function user_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        $res[user][showTab] = "Simple";
        $res[user][showName] = "Benutzer Ansicht";
      
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        //show_array($viewModeList);
        $mainTab = "user";
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            foreach ($viewModeList as $key => $value) {
                if (is_string($key)) {
                    if (!is_array($res[$key])) $res[$key] = array();
                    
                    foreach ($value as $key2 => $value2) {
                        //  echo ("Add $key2 to $key <br>");
                        if (is_string($key2)) {
                            $res[$key][$key2] = $value2;
                        } else {
                            $res[$key][] = $value2;
                        }
                    }
//                    for ($i=0;$i<count($value);$i++) {
//                        // echo ("Add to '$key' $value[$i]<br />");
////                        if (is_array($value[$i])) {
////                            foreach ($value[$i] as $k => $v) echo (" -> $k = $v <br>");
////                        }
//                        $res[$key][] = $value[$i];
//                    }
                } else {
                    // echo ("Add NO INT to $addToTab $viewModeList[$key]<br />");
                    $res[$addToTab][] = $value;
                    
                    
                }
            }
        }
        
        $res[notLoggedIn] = $this->user_edit_notLoggedIn($editContent,$frameWidth);
        $res[userShow] = $this->user_edit_userShow($editContent,$frameWidth);
        
        
        // foreach ($res[userData] as $key => $value) echo ("USERDATA $key => $value <br>");
//        // MainData
//        $addData = array();
//        $addData["text"] = "Darstellung";
//        $addData["input"] = "<input type='text' name='editContent[data][mainBorder]' value='$data[mainBorder]' >";
//        $res[] = $addData;

        return $res;
    }    
    
    function user_edit_notLoggedIn($editContent,$frameWidth){
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        $res[showName] = "Abgemeldet";
        $res[showTab] = "Simple";
        
        $showList = array();
        $showList["login"] = array("name"=>"Anmelden","direction"=>1);
        $showList["forgot"] = array("name"=>"Passwort vergessen","direction"=>0,"sendMail"=>array("user"=>1,"admin"=>1));
        $showList["register"] = array("name"=>"Registrieren","direction"=>1);
        
        $LR = 0;
        $LRC = 0;
        $SPAN = 0;
        
        foreach ($showList as $key => $value) {
            $addData = array();
            $addData[text] = $value[name];
            
            if ($data["notLogged_".$key]) $checked = "checked='checked'";
            else $checked = "";
            $input = "Zeigen: <input type='checkbox' $checked value='1' name='editContent[data][notLogged_".$key."]' />";
            
            if ($value[direction]) {
                $position = $data["notLogged_position_".$key];
                $input .= " Position: ".$this->selectPosition($position,"editContent[data][notLogged_position_".$key."]", $showData, $showFilter, $showSort);
            
                if ($position) {
                    if ($position=="left") $LR = 1;
                    if ($position=="right") $LR = 1;
                    if ($position=="center") $LRC = 1;
                }
            } 
            if (is_array($value[view])) {
                $input .= " Darstellung: ";
                $viewValue = $data[$key."_view"];
                $viewData = array("empty"=>"Darstellung wählen");
                $input .= $this->selectView($viewValue,"editContent[data][".$key."_view]",$view,$viewData);
            }
                
            if ($value[sendMail]) {
                $input .= " eMail: ".cmsEmail_selectEmail($data[$key."Mail"], "editContent[data][".$key."Mail]", $showData); // , $filter, $sort)
            }
            
            if (is_array($value[style])) {
                $input .= " Stil: ";
                $styleValue = $data[$key."_style"];
                $viewData = array("empty"=>"Stil wählen");
                $input .= $this->selectStyle($styleValue,"editContent[data][".$key."_style]",$style,$viewData);
            }
            $addData[input] = $input;
            $addData["mode"] = "Simple";
            $res[] = $addData;
        }
        
        if ($LR) {
            if ($LRC) $addData["text"] = "Rechts / Mitte / Links";
            else $addData["text"] = "Rechts / Links";
            $input = "";
            $input .= "Breite Links: <input type='text' style='width:40px;' value='$data[LR_left]' name='editContent[data][LR_left]' />";
            $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LR_abs]' name='editContent[data][LR_abs]' />";
            if ($LRC) {
                $input .= "Breite Mitte: <input type='text' style='width:40px;' value='$data[LR_center]' name='editContent[data][LR_center]' />";
                $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LRC_abs]' name='editContent[data][LRC_abs]' />";                
            }
            $input .= "Breite Rechts: <input type='text' style='width:40px;'value='$data[LR_right]' name='editContent[data][LR_right]' />";
            $addData["input"] = $input;
            $addData["mode"] = "Simple";
            $res[] = $addData;
        }
        
        if ($SPAN) {
            $addData["text"] = "Bezeichnungsbreite";
            $input = "";
            $input .= "<input type='text' style='width:40px;' value='$data[spanWidth]' name='editContent[data][spanWidth]' />";
            $addData["input"] = $input;
            $addData["mode"] = "More";
            
            $res[] = $addData;
        }        
        
        
        $ownAdd = $this->user_edit_notLoggedIn_own($editContent, $frameWidth);
        if (is_array($ownAdd)) {
            foreach($ownAdd as $key => $value) {
                echo "Add $key = $value <br>";
                if (is_string($key)) {
                    $res[$key] = $value;
                } else {
                    $res[] = $value;
                }
            }
        }
        
        return $res;  
        
    }
    
    
    function user_edit_notLoggedIn_own($editContent, $frameWidth) {
        return 0;
    }
    
    function user_edit_userShowList() {
        $res = array();
        
        $res[salut] = array("name"=>"Anrede","need"=>1);
        $res[name] = array("name"=>"Name","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"));
        $res[street] = array("name"=>"Straße","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"));
        $res[city] = array("name"=>"Ort","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"));
        $res[country] = array("name"=>"Land","need"=>1,"view"=>array("select"=>"Auswahlfeld","text"=>"Textfeld"));
        $res[email] = array("name"=>"eMail","need"=>1);
        $res[password] = array("name"=>"Passwort","need"=>1);
        $res[userName] = array("name"=>"Benutzername","need"=>1);
        
        $res[phone] = array("name"=>"Telefon","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder","all"=>"Drei Felder"));
        $res[fax] = array("name"=>"Telefax","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder","all"=>"Drei Felder"));
        $res[mobil] = array("name"=>"Mobil","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder","all"=>"Drei Felder"));

        $res[url] = array("name"=>"Webseite","need"=>1);
        
        return $res;
    }
    
    
    function user_edit_userShow($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        $res[showName] = "Benutzer Felder";
        $res[showTab] = "Simple";
        
        $showList = $this->user_edit_userShowList();
        foreach ($showList as $key => $value) {
            $addData = array();
            $addData[text] = $value[name];
            
            if ($data["show_".$key]) $checked = "checked='checked'";
            else $checked = "";
            $input = "<input type='checkbox' $checked value='1' name='editContent[data][show_".$key."]' />";
            
            
            if ($value[need]) {
                if ($data["need_".$key]) $checked = "checked='checked'";
                else $checked = "";
                $input .= " Benötigt: <input type='checkbox' $checked value='1' name='editContent[data][need_".$key."]' />";             
            }
            
            if (is_array($value[view])) {
                $input .= " Darstellung: ";
                $viewValue = $data[$key."_view"];
                $viewData = array("empty"=>"Darstellung wählen");
                $input .= $this->selectView($viewValue,"editContent[data][".$key."_view]",$value[view],$viewData);
            }
            
            
            
            
            $addData[input] = $input;
            $addData[mode] = "Simple";
            $res[] = $addData;
        }
        return $res;
    }
    
    
    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for productListe </h1>");
        $res = array();
        $res["userLogin"] = "Benutzer Login";
        $res["userCenter"] = "Benutzer Zentrum";
        $res["userData"] = "Benutzer Daten";
        $res["userOrder"] = "Bestellungen";
        
        // $res["single"] = "Produkt";
            
        return $res;
    }
    
    
    function editContent_ViewMode($editContent,$frameWidth) {
        // echo ("editContent_ViewMode($editContent,$frameWidth)<br />");
        $viewModeList = $this->filter_select_getList("viewMode");
        if (!is_array($viewModeList)) return 0;
        if (count($viewModeList) == 0) return 0;
        $res = array();
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $viewMode = $data[viewMode];
        if ($_POST[editContent][data][viewMode]) $viewMode = $_POST[editContent][data][viewMode];
        else if ($_POST[editContent][data]) $viewMode = $_POST[editContent][data][viewMode];
        
        $res[user] = array();
        
        $addData = array();
        $addData["text"] = "Darstellung";
        $addData["input"] = $this->filter_select("viewMode",$viewMode,"editContent[data][viewMode]",array("submit"=>1,"empty"=>"Bitte Darstellung wählen"));
        $addData["mode"] = "Simple";
        $res[user][] = $addData;
        
        switch ($viewMode) {
            case "userCenter" :
                $userData = $data[userData];
                $addData["text"] = "Benutzer Daten ";
                if ($userData) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][userData]' $checked value='1' >\n";
                $addData["mode"] = "More";
                $res[user][] = $addData;
                
                $userOrder = $data[userOrder];
                $addData["text"] = "Bestellungen";
                if ($userOrder) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][userOrder]' $checked value='1' >\n";
                $addData["mode"] = "More";
                $res[user][] = $addData;
                
                $userNotice = $data[userNotice];
                $addData["text"] = "Merkzettel";
                if ($userNotice) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][userNotice]' $checked value='1' >\n";
                $addData["mode"] = "More";
                $res[user][] = $addData;
                
                
                if ($userData) {
                    $addTo = "userData";
                    $res[$addTo] = array();
                    
                    
                    $res[$addTo][showTab] = "Simple";
                    $res[$addTo][showName] = "Angemeldet";
                    
                    
                    $addData["text"] = "Daten editieren";
                    if ($data[dataEdit]) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataEdit]' $checked value='1' >\n";
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    if ($data[dataEdit]) {
                        $addData["text"] = "Daten auf Unterseite editieren";
                        if ($data[dataSubPageEdit]) $checked = "checked='checked'"; else $checked = "";
                        $addData["input"] = "<input type='checkbox' name='editContent[data][dataSubPageEdit]' $checked value='1' >\n";
                        $addData["mode"] = "Simple";
                        $res[$addTo][] = $addData;
                    }
                    
                    
                    $addData["text"] = "eMail Bestättigen";
                    $confirmEmail = $data[confirmEmail];
                    if ($confirmEmail) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][confirmEmail]' $checked value='1' >\n";
                    if ($confirmEmail) {
                        $addData[input] .= "eMail an Benutzer: ";
                        $addData[input] .= cmsEmail_selectEmail($data[mailConfirm],"editContent[data][mailConfirm]" ,array("empty"=>"keine Mail an Absender"),array() ,"name");                       
                    }
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    $addData["text"] = "Benutzername ändern";
                    if ($data[dataUsername]) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataUsername]' $checked value='1' >\n";
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    $addData["text"] = "eMail ändern";
                    if ($data[dataEmail]) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataEmail]' $checked value='1' >\n";
                    if ($data[dataEmail] AND $confirmEmail) {
                        $addData[input] .= "eMail an Benutzer: ";
                        $addData[input] .= cmsEmail_selectEmail($data[changeEmailConfirm],"editContent[data][changeEmailConfirm]" ,array("empty"=>"keine Mail an Absender"),array() ,"name");                        
                    }
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    $addData["text"] = "Passwort ändern";
                    if ($data[dataPass]) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataPass]' $checked value='1' >\n";
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    
                    $addData["text"] = "Abmelden zeigen";
                    if ($data[dataLogout]) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataLogout]' $checked value='1' >\n";
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    // foreach ($res[$addTo] as $k => $v) echo ("USER $addTo $k = $v <br>");
                }
                
               
                break;
                
            case "userLogin" :
                $userData = $data[userData];
                $addData["text"] = "Benutzer Daten ";
                if ($userData) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][userData]' $checked value='1' >\n";
                $addData["mode"] = "More";
                $res[user][] = $addData;
                
                $userOrder = $data[userOrder];
                $addData["text"] = "Bestellungen";
                if ($userOrder) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][userOrder]' $checked value='1' >\n";
                $addData["mode"] = "More";
                $res[user][] = $addData;
                
                $userNotice = $data[userNotice];
                $addData["text"] = "Merkzettel";
                if ($userNotice) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][userNotice]' $checked value='1' >\n";
                $addData["mode"] = "More";
                $res[user][] = $addData;
                
                
                if ($userData) {
                    $addTo = "userData";
                    $res[$addTo] = array();
                    
                    
                    $res[$addTo][showTab] = "Simple";
                    $res[$addTo][showName] = "Angemeldet";
                    
                    
//                    $addData["text"] = "Daten editieren";
//                    if ($data[dataEdit]) $checked = "checked='checked'"; else $checked = "";
//                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataEdit]' $checked value='1' >\n";
//                    $addData["mode"] = "Simple";
//                    $res[$addTo][] = $addData;
//                    
//                    if ($data[dataEdit]) {
//                        $addData["text"] = "Daten auf Unterseite editieren";
//                        if ($data[dataSubPageEdit]) $checked = "checked='checked'"; else $checked = "";
//                        $addData["input"] = "<input type='checkbox' name='editContent[data][dataSubPageEdit]' $checked value='1' >\n";
//                        $addData["mode"] = "Simple";
//                        $res[$addTo][] = $addData;
//                    }
//                    
//                    
//                    $addData["text"] = "eMail Bestättigen";
//                    $confirmEmail = $data[confirmEmail];
//                    if ($confirmEmail) $checked = "checked='checked'"; else $checked = "";
//                    $addData["input"] = "<input type='checkbox' name='editContent[data][confirmEmail]' $checked value='1' >\n";
//                    if ($confirmEmail) {
//                        $addData[input] .= "eMail an Benutzer: ";
//                        $addData[input] .= cmsEmail_selectEmail($data[mailConfirm],"editContent[data][mailConfirm]" ,array("empty"=>"keine Mail an Absender"),array() ,"name");                       
//                    }
//                    $addData["mode"] = "Simple";
//                    $res[$addTo][] = $addData;
//                    
//                    $addData["text"] = "Benutzername ändern";
//                    if ($data[dataUsername]) $checked = "checked='checked'"; else $checked = "";
//                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataUsername]' $checked value='1' >\n";
//                    $addData["mode"] = "Simple";
//                    $res[$addTo][] = $addData;
//                    
//                    $addData["text"] = "eMail ändern";
//                    if ($data[dataEmail]) $checked = "checked='checked'"; else $checked = "";
//                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataEmail]' $checked value='1' >\n";
//                    if ($data[dataEmail] AND $confirmEmail) {
//                        $addData[input] .= "eMail an Benutzer: ";
//                        $addData[input] .= cmsEmail_selectEmail($data[changeEmailConfirm],"editContent[data][changeEmailConfirm]" ,array("empty"=>"keine Mail an Absender"),array() ,"name");                        
//                    }
//                    $addData["mode"] = "Simple";
//                    $res[$addTo][] = $addData;
//                    
//                    $addData["text"] = "Passwort ändern";
//                    if ($data[dataPass]) $checked = "checked='checked'"; else $checked = "";
//                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataPass]' $checked value='1' >\n";
//                    $addData["mode"] = "Simple";
//                    $res[$addTo][] = $addData;
                    
                    
                    $addData["text"] = "Abmelden zeigen";
                    if ($data[dataLogout]) $checked = "checked='checked'"; else $checked = "";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][dataLogout]' $checked value='1' >\n";
                    $addData["mode"] = "Simple";
                    $res[$addTo][] = $addData;
                    
                    // foreach ($res[$addTo] as $k => $v) echo ("USER $addTo $k = $v <br>");
                }
                
               
                break;                
                

            case "table" :
                $addData["text"] = "Anzahl in Reihe";
                if (!$data[imgRow]) $data[imgRow] = 3;
                $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$data[imgRow]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Abstand in Reihe";
                if (!$data[imgRowAbs]) $data[imgRowAbs] = 10;
                $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$data[imgRowAbs]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Reihen höhe";
                $input  = "<input name='editContent[data][imgColHeight]' style='width:100px;' value='".$data[imgColHeight]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Abstand Zeilen";
                if (!$data[imgColAbs]) $data[imgColAbs] = 10;
                $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$data[imgColAbs]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Maximale Projekttanzahl";
                $input  = "<input name='editContent[data][maxCount]' style='width:100px;' value='".$data[maxCount]."'>";
                $addData["input"] = $input;
                $res[] = $addData;
                break;

            case "slider" :
                $addData = array();
                $addData["text"] = "Wechsel";
                $direction = $editContent[data][direction];
                $input  = $this->slider_direction_select($direction,"editContent[data][direction]",array());
                $addData["input"] = $input;
                $res[] = $addData;


                $addData = array();
                $addData["text"] = "Auto Loop";
                $loop = $editContent[data][loop];
                $checked = "";
                if ($loop) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][loop]' $checked >";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Zeit für Bild in ms";
                $addData["input"] = "<input name='editContent[data][pause]' style='width:100px;' value='".$editContent[data][pause]."'>";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Zeit für Wechsel in ms";
                $addData["input"] = "<input name='editContent[data][speed]' style='width:100px;' value='".$editContent[data][speed]."'>";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Navigation";
                $navigate = $editContent[data][navigate];
                $checked = "";
                if ($navigate) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][navigate]' $checked >";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Einzelauswahl";
                $pager = $editContent[data][pager];
                $checked = "";
                if ($pager) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pager]' $checked >";
                $res[] = $addData;
                break;

           

            default :
                $addEdit = $this->editContent_ViewMode_ownViewMode($viewMode,$editContent,$frameWidth);
                if (is_array($addEdit)) {
                    for ($i=0;$i<count($addEdit);$i++) {
                        $res[] = $addEdit[$i];
                    }
                }

        }
        return $res;
    }
    
}

function cmsType_user_class() {
    if ($GLOBALS[cmsTypes]["cmsType_user.php"] == "own") $userClass = new cmsType_user();
    else $userClass = new cmsType_user_base();
    return $userClass;
}

function cmsType_user($contentData,$frameWidth) {
    $userClass = cmsType_user_class();
    $userClass->show($contentData,$frameWidth);
}



function cmsType_user_editContent($editContent) {
    $userClass = cmsType_user_class();
    $res = $userClass->user_editContent($editContent, $frameWidth);
    return $res;
}
    


?>
