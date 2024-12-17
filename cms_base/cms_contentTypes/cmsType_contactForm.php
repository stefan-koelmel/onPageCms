<?php // charset:UTF-8
class cmsType_contactForm_base extends cmsType_contentTypes_base {

    function getName (){
        return "Kontakt Formular";
    }
    
    
    function contactForm_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("contactForm","width:".$frameWidth."px;");

        // foreach ($contentData as $key => $value ) echo ("$key = $value <br />");
        $show = 1;
        $error = array();
        if ($_POST[send]) {
            $error = $this->contactForm_action($contentData, $frameWidth);
            if (is_array($error)) {
                $show=1;
            } else {
                echo ("<h2>Ihre Nachricht wurde erfolgreich verschickt</h2>");
                $show = 0;
            }
        }

        if ($show) {
            if (is_array($error) AND count($error)) {
                $errorStr = "Fehler in der Eingabe ".count($error);
                foreach($error as $key => $value) {
                    $errorStr .= "<br />".$this->contactForm_showGetName($key);
                    $errorStr .= ": $value";
                }
                cms_errorBox($errorStr);
            }


            echo ("<form method='post' action=''>\n");
            foreach ($contentData[data] as $key => $value ) {
                $showData = 0;
                switch ($key) {
                    case "salut" : $showName = $this->contactForm_showGetName($key); $showType="salut"; break;
                    case "vName" : $showName = $this->contactForm_showGetName($key); $showType="line"; break;
                    case "nName" : $showName = $this->contactForm_showGetName($key); $showType="line"; break;
                    case "company" : $showName = $this->contactForm_showGetName($key); $showType="line"; break;
                    case "adress" :
                        $showName = "___";
                        $showData = array("street","city");
                        $showType = array("street","city");
                        break;
                    case "phone" :
                        $showName = "___";
                        $showData = array("phone","fax","mobil");
                        $showType = array("line","line","line");
                        break;
                    case "email" : $showName = $this->contactForm_showGetName($key); $showType="email"; break;
                    case "message" : $showName = $this->contactForm_showGetName($key); $showType="text"; break;
                    default :
                        // echo ("unkown Key ($key)<br />");
                        $showName = 0;
                }

                if ($showName) {
                    $needed = $contentData[data]["need_".$key];
                    $errorKey = $error[$key];
                    // echo ("Needed = $needed , Error=$errorKey <br />");
                    if (is_array($showData)) {
                        for($i= 0;$i<count($showData);$i++) {
                            $key = $showData[$i];
                            $showName = $this->contactForm_showGetName($key);
                            $this->contactForm_showLine($key,$showName, $showType[$i], $needed, $errorKey, $frameWidth);
                        }
                    } else {
                        $this->contactForm_showLine($key ,$showName, $showType, $needed, $errorKey,  $frameWidth);
                       // echo ("<input type='text' value='leer' name='contactForm[$key]'><br />");
                    }
                }
            }
        }


        $this->contactForm_left("",0,0,$frameWidth);

        echo ("<input type='submit' class='mainInputButton' name='send' value ='Nachricht senden' />");

        echo("</form>");

        //echo ("contactForm");
        div_end("contactForm","before");
    }

    function contactForm_showGetName($key) {
        $showName = "'$key'";
        switch($key) {
            case "salut"   : $showName = "Anrede"; break;
            case "vName"   : $showName = "Vorname"; break;
            case "nName"   : $showName = "Nachname";  break;
            case "company" : $showName = "Firma";  break;
            case "street"  : $showName = "Straße";  break;
            case "city"    : $showName = "PLZ / Ort";  break;
            case "phone"   : $showName = "Telefon";  break;
            case "fax"     : $showName = "Telefax";  break;
            case "mobil"   : $showName = "Handy";  break;
            case "email"   : $showName = "eMail"; break;
            case "message" : $showName = "Nachricht"; break;

        }
        return $showName;
    }


    function contactForm_showLine($key,$showName,$showType, $needed, $errorKey, $frameWidth) {
        
        switch ($showType) {
            case "salut" :
                $this->contactForm_showLine_salut($key, $showName, $showType,  $needed, $errorKey, $frameWidth);
                break;
            case "line" :
                $this->contactForm_showLine_line($key, $showName, $showType,  $needed, $errorKey, $frameWidth);
                break;
            case "street" :
                $this->contactForm_showLine_street($key, $showName, $showType,  $needed, $errorKey, $frameWidth);
                break;
            case "city" :
                $this->contactForm_showLine_city($key, $showName, $showType,  $needed, $errorKey, $frameWidth);
                break;

            case "email" :
                $this->contactForm_showLine_line($key, $showName, $showType,  $needed, $errorKey, $frameWidth);
                break;
            case "text" :
                $this->contactForm_showLine_text($key, $showName, $showType,  $needed, $errorKey, $frameWidth);
                break;
            default :
                echo ("<input type='text' value='leer' name='contactForm[$key]' />$showType<br />");
        }
        
    }

    function contactForm_showLine_salut($key, $showName, $showType, $needed, $errorKey,  $frameWidth) {
        $widthLeft = $this->contactForm_left($showName, $needed, $errorKey, $frameWidth);
        $border = 2;
        $rest = $frameWidth - $widthLeft - 2*$border;


        // echo ("$showName: ");
        $this->contactForm_middle($frameWidth);
        $salut = $_POST[contactForm][$key];
        echo (cmsUser_selectSalut($salut,"contactForm[$key]",array("empty"=>"Anrede w&auml;hlen"), array(),"")."<br />");
        //echo ("<input type='text' style='width:".$rest."px' value='".$_POST[contactForm][$key]."' name='contactForm[$key]'><br />");
        $this->contactForm_right($frameWidth);
    }

    function contactForm_showLine_line($key, $showName, $showType,  $needed, $errorKey, $frameWidth){
        $widthLeft = $this->contactForm_left($showName, $needed, $errorKey, $frameWidth);
        $border = 2;
        $rest = $frameWidth - $widthLeft - 2*$border;
        // echo ("$showName:");
        $this->contactForm_middle($frameWidth);
        echo ("<input type='text'  style='width:".$rest."px' value='".$_POST[contactForm][$key]."' name='contactForm[$key]' /><br />");

    }


     function contactForm_showLine_street($key, $showName, $showType,  $needed, $errorKey, $frameWidth){
        $widthLeft = $this->contactForm_left($showName, $needed, $errorKey, $frameWidth);
        $border = 2;
        $rest = $frameWidth - $widthLeft - 2*$border;
        // echo ("$showName:");
        $this->contactForm_middle($frameWidth);
        $streetNrWidth = 30;
        $streetWidth = $rest - $streetNrWidth - 5;
        echo ("<input type='text'  style='width:".$streetWidth."px' value='".$_POST[contactForm][street]."' name='contactForm[street]'>");
        echo ("<input type='text'  style='width:".$streetNrWidth."px' value='".$_POST[contactForm][streetNr]."' name='contactForm[streetNr]'><br />");
    }


     function contactForm_showLine_city($key, $showName, $showType,  $needed, $errorKey, $frameWidth){
        $widthLeft = $this->contactForm_left($showName, $needed, $errorKey, $frameWidth);
        $border = 2;
        $rest = $frameWidth - $widthLeft - 2*$border;
        // echo ("$showName:");
        $this->contactForm_middle($frameWidth);
        $plzWidth = 50;
        $cityWidth = $rest - $plzWidth - 5;
        echo ("<input type='text'  style='width:".$plzWidth."px' value='".$_POST[contactForm][plz]."' name='contactForm[plz]'>");
        echo ("<input type='text'  style='width:".$cityWidth."px' value='".$_POST[contactForm][city]."' name='contactForm[city]'><br />");
    }


    function contactForm_showLine_text($key, $showName, $showType,  $needed, $errorKey, $frameWidth){
        $widthLeft = $this->contactForm_left($showName, $needed, $errorKey, $frameWidth);
        $border = 2;
        $rest = $frameWidth - $widthLeft - 2*$border;
        // echo ("$showName: ");
        $this->contactForm_middle($frameWidth);
        echo ("<textarea  name='contactForm[$key]' style='width:".$rest."px;height:80px;' rows='20' cols='10'>");
        echo ($_POST[contactForm][$key]);
        echo ("</textarea><br />");
    }

   function contactForm_left($text, $needed, $errorKey, $frameWidth) {
       if ($text) $text .= ":";
       else $text = "&nbsp;";
       if ($needed) $text.="*";
       if ( $errorKey) {
           // echo ("Fehler:$errorKey $frameWidth<br />");
       }
       if ($frameWidth > 250) {
           $width = 80;
           $style = "width:".$width."px;";
           if ($errorKey) $style.="color:#f00;";
           span_text($text,$style); //array("style"=>$style));
       } else {
           echo ("$text<br />");
           $width = 0;
       }
       return $width;

       
   }

    function contactForm_middle($frameWidth){

    }
    function contactForm_right($frameWidth) {
        
    }

    function contactForm_action($contentData,$frameWidth) {
       //  echo ("action<br />");
        // show_array($contentData);

       
        $error = array();
        foreach($_POST[contactForm] as $key => $value) {
            $needed = $contentData[data]["need_".$key];
            $check = $this->contactForm_actionCheck($key,$value,$needed);
            if ($check) {
                $error[$key] = $check;
                // echo ("Post $key => $value needed = $needed error=$check<br />");
            }
        }
        if (count($error)) {
           //  echo "Fehler gefunden<br />";
            // show_array($error);
            return $error;
        }
        $mailToSender  = $contentData[data][mailToSender];
        $mailToOwner   = $contentData[data][mailToOwner];
        $mailOwner     = $contentData[data][mailOwner];
        $addToDatabase = $contentData[data][addToDatabase];

        $ok = 1;
        if ($mailToSender) {
            $res = $this->contactForm_mailToSender($mailToSender,$_POST[contactForm]);
            if ($res) {
                // echo ("Nachricht an Absender verschickt <br />");
            } else {
                echo ("<b>Nachricht an Absender NICHT verschickt </b><br />");
                $ok = 0;
            }
        }

        if ($mailToOwner) {
            $res = $this->contactForm_mailToOwner($mailToOwner,$mailOwner,$_POST[contactForm]);
            if ($res) {
               //  echo ("Nachricht an Schaufenster verschickt <br />");
            } else {
                echo ("<b>Nachricht an Schaufenster NICHT verschickt </b><br />");
                $ok = 0;
            }
        }

        if ($addToDatabase) {
            $res = $this->contactForm_addToDataBase($_POST[contactForm]);
            if ($res) {
                // echo ("Benutzer in Datenbank eingetragen<br />");
            } else {
                echo ("<b>Benutzer NICHT in Datenbank eingetragen </b><br />");
                $ok = 0;
            }
        }
        return $ok;
    }

    function contactForm_actionCheck($key,$value,$needed) {
        $error = 0;
        switch ($key) {
            case "email" :
                if ($needed) {
                    if (strlen($value)<8) {
                        $error = "email Adresse zu kurz";
                    } else {
                        $atPos = strpos($value,"@");
                        if ($atPos < 1) $error = "ungültige eMail Adresse '@'";
                        $dotPos = strpos($value,".",$atPos);
                        if ($dotPos < 5) $error = "ungültige eMail $dotPos Adresse '.'";
                        if (strlen($value) < $dotPos+3) $error =  "ungültige eMail $dotPos ".strlen($value)." Adresse '.??'";
                   }
                }
                break;
            case "salut" :
                if ($needed) {
                    if (!$value) $error = "Keine Anrede";
                }
            default :
                if ($needed) {
                    if (!$value) $error = "Keine Eingabe";
                }
        }
        return $error;
    }

    function contactForm_mailToSender($mailToSender,$data) {
        $mailData = cmsEmail_getById($mailToSender);
        if (!is_array($mailData)) {
            echo ("eMail Text nicht gefunden <br />");
            return 0;
        }
        $mailText = $mailData[info];
        $mailText = cmsEmail_convertText($mailText, $data);

        $subject = $mailData[subName];
        $subject = cmsEmail_convertText($subject, $data);

        $email = $data[email];
        if (!$email) {
            echo "Keine eMail adresse bei mailToSender <br />";
            show_array(data);
            return 0;
        }

        $res = cmsEmail_sendMail($email,$subject,$mailText);
        return $res;
    }

    function contactForm_mailToOwner($mailToOwner,$mailOwner,$data) {
        $mailData = cmsEmail_getById($mailToOwner);
        if (!is_array($mailData)) {
            echo ("eMail Text nicht gefunden <br />");
            return 0;
        }
        $mailText = $mailData[info];
        $mailText = cmsEmail_convertText($mailText, $data);

        $subject = $mailData[subName];
        $subject = cmsEmail_convertText($subject, $data);
        $res = cmsEmail_sendMail($mailOwner,$subject,$mailText);
        return $res;
    }

    function contactForm_addToDataBase($data) {
        $userData = array();
        $userData[userLevel] = 0;
        $userData[email] = $data[email];
        $userData[salut] = $data[salut];
        $userData[vName] = $data[vName];
        $userData[nName] = $data[nName];
        $userData[company] = $data[company];
        $userData[street] = $data[street];
        $userData[streetNr] = $data[streetNr];
        $userData[plz] = $data[plz];
        $userData[city] = $data[city];
        $userData[phone] = $data[phone];
        $userData[fax] = $data[fax];
        $userData[mobil] = $data[mobil];

        $email = $userData[email];
        $userGet = cmsUser_get(array("email"=>$email));
        if (is_array($userGet)) {
            // echo "Benutzer existiert bereits - update ???";
            $updateData = array();
            foreach($userGet as $key => $value) {
                if (!$value) {
                    if ($userData[$key]) {
                        $updateData[$key] = $userData[$key];
                    }
                } else {
                    // Database User has Value
                    if ($userData[$key]) {
                        if ($userData[$key] != $value) {
                            // echo ("Diffrent old = $value new=".$userData[$key]."<br />");
                            $updateData[$key] = $userData[$key];
                        }
                    }
                }
            }
            if (count($updateData)>0) {
                // echo ("Do Update <br />");
                $updateData[id] = $userGet[id];
                show_array($updateData);
                $res = cmsUser_update($updateData);
                $res = 0;
            } else {
                // echo "Keine Änderung <br />";
                return 1;
            }
        } else {
            // echo ("Benutzer existiert nicht -> anlegen <br />");
            $res = cmsUser_save($userData);
        }
        return $res;
    }



    
    function contactForm_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        // foreach ($editContent[data] as $key => $value ) echo ("editCont $key = $value <br />");
        $res = array();
        $addData = array();
        $addData["text"] = "eMail an Absender";
        $addData["input"] = cmsEmail_selectEmail($data[mailToSender],"editContent[data][mailToSender]" ,array("empty"=>"keine Mail an Absender"),array() ,"name");
        $res[contactForm][] = $addData;

        $addData = array();
        $addData["text"] = "eMail an Schaufenster";
        $addData["input"] = cmsEmail_selectEmail($data[mailToOwner],"editContent[data][mailToOwner]" ,array("empty"=>"keine Mail an Absender"),array() ,"name");
        $res[contactForm][] = $addData;

        $addData = array();
        $addData["text"] = "eMailAdresse Schaufenster";
        $addData["input"] = "<input type='text' value ='$data[mailOwner]' name='editContent[data][mailOwner]'>";
        $res[contactForm][] = $addData;


        $addData = array();
        $addData["text"] = "Eintragen in Datenbank";
        $input = "<input type='checkbox' name='editContent[data][addToDatabase]' value='1' ";
        if ($editContent[data][addToDatabase]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res[contactForm][] = $addData;


        $res["Felder"]  = array();
        // MainData
        $addData = array();
        $addData["text"] = "Anrede";
        $input = "<input type='checkbox' name='editContent[data][salut]' value='1' ";
        if ($editContent[data][salut]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_salut]' value='1' ";
        if ($editContent[data][need_salut]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

        $addData["text"] = "Vorname";
        $input = "<input type='checkbox' name='editContent[data][vName]' value='1' ";
        if ($editContent[data][vName]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_vName]' value='1' ";
        if ($editContent[data][need_vName]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

        $addData["text"] = "Nachname";
        $input = "<input type='checkbox' name='editContent[data][nName]' value='1' ";
        if ($editContent[data][nName]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_nName]' value='1' ";
        if ($editContent[data][need_nName]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;


        $addData["text"] = "Firma";
        $input = "<input type='checkbox' name='editContent[data][company]' value='1' ";
        if ($editContent[data][company]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_company]' value='1' ";
        if ($editContent[data][need_company]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

         $addData["text"] = "Adresse";
        $input = "<input type='checkbox' name='editContent[data][adress]' value='1' ";
        if ($editContent[data][adress]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_adress]' value='1' ";
        if ($editContent[data][need_adress]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

        $addData["text"] = "Telefon";
        $input = "<input type='checkbox' name='editContent[data][phone]' value='1' ";
        if ($editContent[data][phone]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_phone]' value='1' ";
        if ($editContent[data][need_phone]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

        $addData["text"] = "eMail";
        $input = "<input type='checkbox' name='editContent[data][email]' value='1' ";
        if ($editContent[data][email]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_email]' value='1' ";
        if ($editContent[data][need_email]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

        $addData["text"] = "Nachricht";
        $input = "<input type='checkbox' name='editContent[data][message]' value='1' ";
        if ($editContent[data][message]) $input .= "checked='checked'";
        $input .= ">\n";
        $input .= "benötigt:<input type='checkbox' name='editContent[data][need_message]' value='1' ";
        if ($editContent[data][need_message]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res["Felder"][] = $addData;

        return $res;
    }

}
function cmsType_contactForm_class() {
    if ($GLOBALS[cmsTypes]["cmsType_contactForm.php"] == "own") $contactFormClass = new cmsType_contactForm();
    else $contactFormClass = new cmsType_contactForm_base();
    return $contactFormClass;
}


function cmsType_contactForm($contentData,$frameWidth) {
    $contactFormClass = cmsType_contactForm_class();
    // echo ("HIER <br />");
    $contactFormClass->contactForm_show($contentData,$frameWidth);
}



function cmsType_contactForm_editContent($editContent) {
    $contactFormClass = cmsType_contactForm_class();
    return $contactFormClass->contactForm_editContent($editContent);
}


?>
