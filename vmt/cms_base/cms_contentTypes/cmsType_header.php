<?php // charset:UTF-8

class cmsType_header_base extends cmsType_contentTypes_base {
    function getName() {
        return "Kopfzeile";
    }
    
    function headerLogo($contentData,$textData) {
        
        $data = $contentData[data];
        
        $name = $textData[name][text];
        if (!$name) {
            $name = $GLOBALS[cmsSettings][longName];
            if (!$name) $name = $GLOBALS[cmsSettings][name];
            if (!$name) $name = "CMS";
            
           // show_array($GLOBALS[cmsSettings]);
            
            // $name ="CMS";
        }

        $imageId = $data[imageId];
        $imageSize = $data[imageWidth];
        
        if (!$imageSize) $imageSize = 300;
        if ($imageId) {
            $imageData = cmsImage_getData_by_Id($imageId);
            
            $showData = array();
            $showData[hAlign] = $data[hAlign];
            $showData[vAlign] = $data[vAlign];
            if ($data[imageWidth]) $showData[frameWidth] = $data[imageWidth];
            if ($data[imageHeight]) $showData[frameHeight] = $data[imageHeight];
            
            if ($data[imageWidth] AND $data[imageHeight]) {
//                if ($data[ratio]) {
//                    $showData[ratio] = 1.0 * $data[imageWidth] / $data[imageHeight];
//                    $showData[frameWidth] = 300;
//                } else {
//                    $showData[frameHeight] = $data[imageHeight]*100;
//                    $showData[frameWidth] = $data[imageWidth]*100;
                //}
            }
           
           
            $showData[title] = $name;
            
            // show_array($showData);
            $img = cmsImage_showImage($imageData, $imageSize, $showData);
            return $img;
        }
       
        
        
        
        
        return $name;
    }

    function headerName($contentData,$textData) {
        $name = $textData[name][text];
        if (!$name) {
            $name = $GLOBALS[cmsSettings][longName];
            if (!$name) $name = $GLOBALS[cmsSettings][name];
            if (!$name) $name = "Mein neues CMS";        
        }        
        return $name;
    }

    function headerSlogan ($contentData,$textData) {
         $name = $textData[slogan][text];
        if (!$name) {
            $name = $GLOBALS[cmsSettings][longName];
            if (!$name) $name = $GLOBALS[cmsSettings][name];
            if (!$name) $name = "simple and top!";        
        }        
        return $name;
    }

    
    function headerUser($contentData,$textData) {
        $showLogin = $contentData[data][showLogin];
        $showLogout = $contentData[data][showLogout];
        $showRegister = $contentData[data][showRegister];
        
        $str .= "";
        $userLevel = $_SESSION[showLevel];
        $userName  = $_SESSION[userName];
        $userId    = $_SESSION[userId];
       
        if ($userLevel) {
            $userData = cms_user_getById($userId);
            $userName = $userData[userName];
            // angemeldet
            $str .= span_text_str("Angemeldet als:");
            $str .= $userName;  
            
            if ($_POST[logout]) {
                if ($_POST[logout][logout]) {
                    $logoutResult = cms_user_logout($userId);
                    if ($logoutResult == 1) {
                        // cms_infoBox("Sie haben sich erfolgreich abgemeldet");
                        $goPage = "index.php";
                        reloadPage($goPage,0);
                        return "";
                    }
                }
            }
            // foreach ($_SESSION as $key => $value) $str .= " | ".$key;
           
            
            
            if ($showLogout) {
                $str .= " &nbsp;";
                $str .= "<form method='post' style = 'display:inline-block;'>";
                $str .= " <input class='mainInputButton logout mainSecond' type='submit' value='abmelden' name='logout[logout]' style='height:20px;padding:0;' />";
                $str .= "</form>";
                
                
                
            }
        } else {
            $str .= "Nicht angemeldet ";
            if ($showLogin) {
                $str .= "Anmelden:<br>";
                $str .= "user / email:<input type='text' /><br>";
                $str .= "pass : <input type='password' />";                
            }
        }
        
        
        foreach($contentData[data] as $key => $value ) {
           //  $str .= "$key = $value <br />";
        }
        return $str;
    }
    
    function headerLanguage($contentData,$textData) {
        $str = "";
        $str .= span_text_str("Sprache:");
        // $str .= "Sprache: ";
        $str .= div_start_str("languageLine","display:inline-block;");
        
        
        $lgs = array("dt"=>"deutsch","en"=>"english");
        foreach ($lgs as $key => $value) {
            switch ($key) {
                case "dt" :
                    $str .= "<div style='border:1px solid #666;float:left;padding:2px;cursor:pointer;background-color:#eee;'>";
                    break;
                default :
                    $str .= "<div style='border:1px solid #666;float:left;padding:2px;cursor:pointer;'>";
            }                    
            $str .= $value;
            $str .= "</div>";
            
        }
        $str .= div_end_str("languageLine","before");
        
        return $str;
    }
    
    
    function headerSpecial($contentData,$textData) {
        $str = "Special Data";
        return $str;
    }
    
    
    function headerShow ($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $height = $data[height];
        // if (!$height) $height = 80;
        $background = $data[background];
        if (!$background) $background="#cfc";
        
        
        // getText
        $id = $contentData[id];
        $contentCode = "text_$id";
        $textData= cms_text_getForContent($contentCode);
        
        // show_array($textData);
        
        
        
        
        
        $show_logo = $data[logo];
        $show_name = $data[name];
        $show_slogan = $data[slogan];
        $show_user = $data[user];
        $show_language = $data[language];
        $show_special = $data[special];
        
        
        $leftFrame = "";
        $middleFrame = "";
        $rightFrame = "";
        
        
        

        $border = 0;
        $innerWidth = $frameWidth;// - 10 - 2*$border;

        $style = "width:".$innerWidth."px;";
        if ($height) $style .= "height:".$height."px;";
        
        // LOGO
        if ($show_logo) {
            $str ="";
            $str .= "<a href='index.php' class='headerStartLink' >";
            $str .= $this->headerLogo($contentData,$textData);
            $str .= "</a>";
            $frame = $data[logoFrame];
            if (!$frame) $frame = "left";
            switch ($frame) {
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;                
                default : echo("dontAdd because $frame <br>");
            }           
        }
        
        // NAME         
        if ($show_name) {
            $str  = div_start_str("headerName");
            $str .= $this->headerName($contentData,$textData);
            $str .= div_end_str("headerName");
            $frame = $data[nameFrame];
            if (!$frame) $frame = "middle";
            switch ($frame) {
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }           
            
            
            
           //  span_text($this->headerName(),array("width"=>"auto","text-align"=>"right","class"=>"headerName"));
        }
        
        
        // SLOGAN
        if ($show_slogan) {
            $str  = div_start_str("headerSlogan");
            $str .= $this->headerSlogan($contentData,$textData);
            $str .= div_end_str("headerSlogan");
            $frame = $data[sloganFrame];
            if (!$frame) $frame = "middle";
            switch ($frame) {
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }    
        }
        
        // USER
        if ($show_user) {
            $str  = div_start_str("headerUser");
            $str .= $this->headerUser($contentData,$textData);
            $str .= div_end_str("headerUser");
            $frame = $data[userFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }               
        }
        
         if ($show_language) {
            $str  = div_start_str("headerLanguage");
            $str .= $this->headerLanguage($contentData,$textData);
            $str .= div_end_str("headerLanguage");
            $frame = $data[languageFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }               
        }
        
        
        // SPECIAL
        if ($show_special) {
            $str  = div_start_str("headerSpecial");
            $str .= $this->headerSpecial($contentData,$textData);
            $str .= div_end_str("headerSpecial");
            $frame = $data[specialFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }             
        }
        
        div_start("header",$style);
        
        div_start("headerLeft");
        echo ($leftFrame);
        div_end("headerLeft");

        if ($middleFrame) {
            div_start("headerMiddle");
            echo ($middleFrame);
            div_end("headerMiddle");
        }
        
        if ($rightFrame) {
            div_start("headerRight");
            echo ($rightFrame);
            div_end("headerRight");
        }
        div_end("header","before");       
    }

    function header_editContent($editContent) {

        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        $res["header"] = array();
        // $res["more"] = array();
        
        
        // GET TEXT
        $editText = $_POST[editText];
        if (!is_array($editText)) {
            $id = $editContent[id];
            $contentCode = "text_$id";
            $editText = cms_text_getForContent($contentCode);
        } 
        
        $addData = array();
        $addData["text"] = "hidden-Text Id";
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
        $res[header][] = $addData;

        // MainData
//        $addData = array();
//        $addData["text"] = "Kopfzeilen-Höhe";
//        $addData["input"] = "<input type='text' name='editContent[data][height]' value='$data[height]' />";
//        $res[header][] = $addData;
        
        $frameData = array("empty"=>"Standard");
        
        $addData = array();
        $addData["text"] = "Logo zeigen";
        if ($data[logo]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][logo]' value='1' />";
        $input .= " zeigen in Rahmen ".$this->selectFrame($editContent[data][logoFrame],"editContent[data][logoFrame]",$frameData);
        $addData["input"] = $input;
        $res[header][] = $addData;
        
        // NAME
        $addData = array();
        $addData["text"] = "Name zeigen";
        if ($data[name]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][name]' value='1' />";
        $input .= " zeigen in Rahmen ".$this->selectFrame($editContent[data][nameFrame],"editContent[data][nameFrame]",$frameData);
        $addData["input"] = $input;
        $res[header][] = $addData;
        // Name Text
        $addData = array();
        $addData["text"] = "Name";
        $input = "<input type='text' style='width:300px;' name='editText[name][text]' value='".$editText[name][text]."' />";
        $input .= "<input type='hidden' style='width:30px;' value='".$editText[name][id]."' name='editText[name][id]'>";  
        $addData["input"] = $input;
        $res[header][] = $addData;
        
        // Slogan
        $addData = array();
        $addData["text"] = "Slogan zeigen";
        if ($data[slogan]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][slogan]' value='1' />";
        $input .= " zeigen in Rahmen ".$this->selectFrame($editContent[data][sloganFrame],"editContent[data][sloganFrame]",$frameData);
        $addData["input"] = $input;
        $res[header][] = $addData;
        
        // SLogan Text
        $addData = array();
        $addData["text"] = "Slogan";
        $input = "<input type='text' style='width:300px;' name='editText[slogan][text]' value='".$editText[slogan][text]."' />";
        $input .= "<input type='hidden' style='width:30px;' value='".$editText[slogan][id]."' name='editText[slogan][id]'>";  
        $addData["input"] = $input;
        $res[header][] = $addData;
        
        // USER 
        $addData = array();
        $addData["text"] = "Benutzer zeigen";
        if ($data[user]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][user]' value='1' />";
        $input .= " zeigen in Rahmen ".$this->selectFrame($editContent[data][userFrame],"editContent[data][userFrame]",$frameData);
        $addData["input"] = $input;
        $res[header][] = $addData;
        
        
        // LANGUAGE 
        $addData = array();
        $addData["text"] = "Sprachwahl zeigen";
        if ($data[language]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][language]' value='1' />";
        $input .= " zeigen in Rahmen ".$this->selectFrame($editContent[data][languageFrame],"editContent[data][languageFrame]",$frameData);
        $addData["input"] = $input;
        $res[header][] = $addData;
        
        // Special
        $addData = array();
        $addData["text"] = "Sonderanzeige";
        if ($data[special]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][special]' value='1' />";
        $input .= " zeigen in Rahmen ".$this->selectFrame($editContent[data][specialFrame],"editContent[data][specialFrame]",$frameData);
        $addData["input"] = $input;
        $res[header][] = $addData;
        
       //  $res["text"] = cms_contentType_text_editContent($editContent,$frameWidth);
        
        $res["logo"] = cmsType_image_editContent($editContent,$frameWidth);
        
        $res["user"] = cmsType_login_editContent($editContent, $frameWidth);
        

        //$res["more"][] = $addData;
        
        return $res;
    }
    
    function selectFrame($code,$dataName,$showData=array()) {
        $selectList = array("left"=>"Links","middle"=>"Mitte","right"=>"Rechts");


        $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Kein Filter";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        foreach ($selectList as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $name = $value[$outValue];
                } else {
                    $name = $value;
                }

                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$name</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }
    
}


    
    


function cmsType_header_class() {
    // show_array($GLOBALS[cmsTypes]);
    if ($GLOBALS[cmsTypes]["cmsType_header.php"] == "own") $headerClass = new cmsType_header();
    else $headerClass = new cmsType_header_base();

    return $headerClass;
}


function cmsType_header($contentData,$frameWidth) {
    $headerClass = cmsType_header_class();
    $headerClass->headerShow($contentData,$frameWidth);
}



function cmsType_header_editContent($editContent) {
    $headerClass = cmsType_header_class();
    return $headerClass->header_editContent($editContent);
}


?>
