<?php // charset:UTF-8

class cmsType_header_base extends cmsClass_content_show {
    function getName() {
        return "Kopfzeile";
    }
    
    
    function contentType_init() {
        $str = "";

        $this->loginClass = cmsType_login_Class();
        if (!is_object($this->loginClass)) $str .= " NO loginClass Class ";
        else $this->loginClass->setMainClass($this);

        $this->imageClass = cmsType_image_Class();
        if (!is_object($this->imageClass))  $str .= " NO IMAGE Class ";
        else $this->imageClass->setMainClass($this);

        $this->searchClass = cmsType_search_Class();
        if (!is_object($this->searchClass))  $str .= " NO searchClass Class ";
        else $this->searchClass->setMainClass($this);



        if ($str) {
            echo ("<h1>$str</h1>");
        }
    }
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->innerWidth;

        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $height = $data[height];
        // if (!$height) $height = 80;
        $background = $data[background];
        if (!$background) $background="#cfc";
        
        
        // getText
        $id = $this->contentId;
        $contentCode = "text_$id";
        
        // show_array($textData);
        
        $show_logo = $data[logo];
        $show_name = $data[name];
        $show_slogan = $data[slogan];
        $show_user = $data[user];
        $show_language = $data[language];
        $show_search = $data[search];
        $show_wireframeSwitch = $data[wireframeSwitch];
        $show_special = $data[special];
        $show_basket = $data[basket];
        
        $topFrame = "";
        $leftFrame = "";
        $middleFrame = "";
        $rightFrame = "";
        $bottomFrame = "";
        
        
        $LR_left = $data[LR_left];
        $LR_abs = $data[LR_abs];
        $LR_center = $data[LR_center];
        $LRC_abs = $data[LRC_abs];
        $LR_right = $data[LR_right];
       // show_array($data);
        $posData = $this->show_frameValue($this->contentData,$this->innerWidth);
//        foreach ($posData as $key => $value) {
//            echo ("POSData $key <br>");
//            // show_array($value);
//        }
        

        $border = 0;
        $innerWidth = $frameWidth;// - 10 - 2*$border;
        // foreach ( $GLOBALS[pageInfo] as $key => $value ) echo ("$key = $value <br> ");
        // foreach ( $_SESSION as $key => $value ) echo ("$key = $value <br> ");
        
        $pageWidth = $GLOBALS[cmsSettings][width];
        //echo ("<h1>$pageWidth</h1>");
        
        // $style = "width:".$innerWidth."px;";
        $headerStyle = $style;
        if ($height) $headerStyle .= "height:".$height."px;";
        
        // LOGO
        if ($show_logo) {
            $str ="";
            $frame = $data[logoFrame];
            if (!$frame) $frame = "left";
            $useWidth = $posData[$frame."_width"];
            
            $goLink = 0;
            $logoLink = $data[logoLink];
            if ($logoLink == "noLink") $logoLink = 0;
            if ($logoLink) {
                $linkPageData = cms_page_getData(intval($logoLink));
                if (is_array($linkPageData)) {
                    $goLink = $linkPageData[name].".php";
                    $goName = $this->text_getFromArray($linkPageData[title]);
                    // $str .= "GEHE $goLink $goName <br />";
                }   
            }
         
            if ($goLink) $str .= "<a href='$goLink' title='$goName'>";
            $str .= $this->headerLogo($useWidth);
            if ($goLink) $str .= "</a>";
            // $str .= "</a>";
           
            switch ($frame) {
                case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }           
        }
        
        // NAME         
        if ($show_name) {
            $str = "";
            $goLink = 0;
            $nameLink = $data[nameLink];
            if ($nameLink == "noLink") $nameLink = 0;
            if ($nameLink) {
                $linkPageData = cms_page_getData(intval($nameLink));
                if (is_array($linkPageData)) {
                    $goLink = $linkPageData[name].".php";
                    $goName = $this->text_getFromArray($linkPageData[title]);                   
                }                
            }
            
            $str  .= div_start_str("headerItem headerName");
            if ($goLink) $str .= "<a href='$goLink' title='$goName'>";
            
            $str .= $this->headerName();
            if ($goLink) $str .= "</a>";
            $str .= div_end_str("headerItem headerName");
            $frame = $data[nameFrame];
            if (!$frame) $frame = "middle";
            switch ($frame) {
                case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }           
            
            
            
           //  span_text($this->headerName(),array("width"=>"auto","text-align"=>"right","class"=>"headerName"));
        }
        
        
        // SLOGAN
        if ($show_slogan) {
            $str  = div_start_str("headerItem headerSlogan");
            
            $sloganLink = $data[sloganLink];
            if ($sloganLink == "noLink") $sloganLink = 0;
            if ($sloganLink) {
                $linkPageData = cms_page_getData(intval($sloganLink));
                if (is_array($linkPageData)) {
                    $goLink = $linkPageData[name].".php";
                    $goName = $this->text_getFromArray($linkPageData[title]);                    
                }                
            } else {
                $goLink = 0;
            }
            if ($goLink) $str .= "<a href='$goLink' title='$goName'>";
            
            $str .= $this->headerSlogan();
            
            if ($goLink) $str .= "</a>";
            $str .= div_end_str("headerItem headerSlogan");
            $frame = $data[sloganFrame];
            if (!$frame) $frame = "middle";
            switch ($frame) {
                case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }    
        }
        
        // USER
        if ($show_user) {
            $str  = div_start_str("headerItem headerUser");
            $str .= $this->headerUser($contentData,$textData);
            $str .= div_end_str("headerItem headerUser");
            $frame = $data[userFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
                case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }               
        }

        //suche
        if ($show_search) {
            $str  = div_start_str("headerItem headerSearch");
            $str .= $this->headerSearch($contentData,$textData);
            $str .= div_end_str("headerItem headerSearch");
            $frame = $data[seacrchFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
                case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }               
        }
        
        
        //language
        if ($show_language) {
            $str  = div_start_str("headerItem headerLanguage");
            $str .= $this->headerLanguage($contentData,$textData);
            $str .= div_end_str("headerItem headerLanguage");
            $frame = $data[languageFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
                case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }               
        }


        //Warenkorb
        if ($show_basket) {
            $basketStr = $this->headerBasket($contentData);
            if ($basketStr) {
            
                $str  = div_start_str("headerItem headerBasket");
                $str .= $basketStr;


                $str .= div_end_str("headerItem headerBasket");
                $frame = $data[basketFrame];
                if (!$frame) $frame = "right";
                switch ($frame) {
                    case "top" : $topFrame .= $str."\n"; break;
                    case "left" : $leftFrame .= $str."\n"; break;
                    case "middle" : $middleFrame .= $str."\n"; break;
                    case "center" : $middleFrame .= $str."\n"; break;
                    case "right" : $rightFrame .= $str."\n"; break;
                    case "bottom" : $bottomFrame .= $str."\n"; break;
                    default : echo("dontAdd because $frame <br>");
                }
            } 
        }
        
        
        // Wireframe Switch
        $wireFrame_enabled = cmsWireframe_enabled();
        if ($show_wireframeSwitch AND $wireFrame_enabled) {
            
            $str  = div_start_str("headerItem headerWireframe");
            $str .= $this->headerWireframe($contentData,$textData);
            $str .= div_end_str("headerItem headerWireframe");
            $frame = $data[wireframeSwitchFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
               case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }             
        }
        
        // SPECIAL
        if ($show_special) {
            $str  = div_start_str("headerItem headerSpecial");
            $str .= $this->headerSpecial($contentData,$textData);
            $str .= div_end_str("headerItem headerSpecial");
            $frame = $data[specialFrame];
            if (!$frame) $frame = "right";
            switch ($frame) {
               case "top" : $topFrame .= $str."\n"; break;
                case "left" : $leftFrame .= $str."\n"; break;
                case "middle" : $middleFrame .= $str."\n"; break;
                case "center" : $middleFrame .= $str."\n"; break;
                case "right" : $rightFrame .= $str."\n"; break;
                case "bottom" : $bottomFrame .= $str."\n"; break;
                default : echo("dontAdd because $frame <br>");
            }             
        }
        
        div_start("header",$headerStyle);

        if ($topFrame) {
            $style = "";
            if ($posData[top_width]) $style .= "width:".$posData[top_width]."px;";
            if ($posData[top_abs]) $style .= "margin-right:".$posData[top_abs]."px;";
            div_start("headerTop",$style);
            echo ($topFrame);
            div_end("headerTop");
        }
        


        if ($leftFrame OR $middleFrame OR $rightFrame) {
            div_start("headerMain",$style);
            if ($leftFrame) {
                $style = "";
                if ($posData[left_width]) $style .= "width:".$posData[left_width]."px;";
                if ($posData[left_abs]) $style .= "margin-right:".$posData[left_abs]."px;";
                div_start("headerLeft",$style);
                echo ($leftFrame);
                div_end("headerLeft");
            }

            if ($middleFrame) {
                $style = "";
                if ($posData[center_width]) $style .= "width:".$posData[center_width]."px;";
                if ($posData[center_abs]) $style .= "margin-right:".$posData[center_abs]."px;";
                div_start("headerMiddle",$style);
                echo ($middleFrame);
                div_end("headerMiddle");
            }

            if ($rightFrame) {
                $style = "";
                if ($posData[right_width]) $style .= "width:".$posData[right_width]."px;";
                if ($posData[right_abs]) $style .= "margin-right:".$posData[right_abs]."px;";
                div_start("headerRight",$style);
                echo ($rightFrame);
                div_end("headerRight");
            }
            div_end("headerMain","before");
        }



        if ($bottomFrame) {
            $style = "";
            if ($posData[bottom_width]) $style .= "width:".$posData[bottom_width]."px;";
            if ($posData[bottom_abs]) $style .= "margin-right:".$posData[bottom_abs]."px;";
            div_start("headerBottom",$style);
            echo ($bottomFrame);
            div_end("headerBottom");
        }
        div_end("header");       
    }
    
    
    function headerLogo($useWidth) {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $data = $this->contentData[data];
     
        
        $imgStr = $this->contentShow_image($useWidth,"outString");
        return $imgStr;
        $imageId = $data[imageId];
        if (!$imageId) $imageId = $data[image];
        
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
       
        
        
        
        return ($imageId);
        return $name;
    }

    function headerName() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $data = $this->contentData[data];
        $textCode = "name";
        $text = $this->text_getForCode($textCode);
        if (is_string($text)) return $text;
        echo ("Text from Code = $text <br>" );
        
        $textData = $this->textData[$textCode];
        $text = $this->text_getFromArray($textData);
        echo ("Text from Array = $text <br>" );
        
        return $text;
        $text = cms_text_getLg($textData);
        return $text;
        echo ("Text for Header = $text<br>");
        
        echo ("textData $this->textData <br/>");
        foreach($this->textData as $key => $value) {
            echo ("$key=$value <br>");
            foreach ($value as $key2 => $value2) {
                echo ("$key2 = $value2 <br >");
            }
        }
        $name = $this->textData[name][text];
        if (!$name) {
            $name = $GLOBALS[cmsSettings][longName];
            if (!$name) $name = $GLOBALS[cmsSettings][name];
            if (!$name) $name = "Mein neues CMS";        
        }        
        return $name;
    }

    function headerSlogan () {
        $textCode = "slogan";
        $text = $this->text_getForCode($textCode);
        if (is_string($text)) return $text;
        echo ("Text from Code = $text <br>" );
         $name = $textData[slogan][text];
        if (!$name) {
            $name = $GLOBALS[cmsSettings][longName];
            if (!$name) $name = $GLOBALS[cmsSettings][name];
            if (!$name) $name = "simple and top!";        
        }        
        return $name;
    }

    
    function headerUser() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $data = $this->contentData[data];
        
        
        $showLogin = $data[showLogin];
        $showLogout = $data[showLogout];
        $showRegister = $data[showRegister];
        
        $str .= "";
        $userLevel = $_SESSION[showLevel];
        $userName  = $_SESSION[userName];
        $userId    = $_SESSION[userId];
       
        if ($userLevel) {
            $userData = cms_user_getById($userId);
            $userName = $userData[userName];
            // angemeldet
            $str .= "<span class='cmsLogin_name'>".$this->lg("login","loggedInText",":")."</span>";
            $str .= "<span class='cmsLogin_userName'>".$userName."</span>";
            
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
                $str .= "<input class='mainInputButton logout mainSecond mainSmallButton ' type='submit' value='".$this->lg("login","logoutButton")."' name='logout[logout]'  />";
                $str .= "</form>";
                
                
                
            }
        } else {
            // $str .= "Nicht angemeldet ";
            if ($showLogin) {
                $showLogin = 1;

                if ($_POST[login]) {
                    if ($_POST[login][login]) {
                        $loginResult = cms_user_login($_POST[login]);
                        if ($loginResult) {
                            $goPage = $GLOBALS[pageData][name].".php";
                            reloadPage($goPage,0);
                            $showLogin = 0;
                        }
                    }
                }
                if ($showLogin) {
                    $str .= "<form class='login' method='post'>";

                    //$str .= "Anmelden:<br>";
                    $str .= span_text_str($this->lg("login","userText",":"),70)."<input type='text' style='width:100px;margin-bottom:0px;' value='' name='login[userName]' /><br>";
                    $str .= span_text_str($this->lg("login","passText",":"),70)."<input type='password' style='width:100px;margin-bottom:0px;' value='' name='login[password]' /><br />";
                    $str .= " <input class='mainInputButton login' type='submit' value='".$this->lg("login","loginButton")."' name='login[login]' style='height:20px;padding:0;' />";
                    $str .= "</form>";
                }
            }
        }
        
        
        foreach($contentData[data] as $key => $value ) {
           //  $str .= "$key = $value <br />";
        }
        return $str;
    }

    function headerBasket($contentData) {
        if (!function_exists("cmsType_basket")) return "";
        
        
        $str = "";
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $emptyBasket = $data[emptyBasket];
        
        $basketData = array("data"=>array());
        $basketData[data][viewMode] = "info";
        $basketData[data][showEmpty] = $emptyBasket;
        $basketData[data][out] = 1;

        $basketData[data][showItems] = 1;
        $basketData[data][showParts] = 0;
        $basketData[data][showValue] = 1;
        $basketData[data][showShipping] = 0;
        
        
        $str .= cmsType_basket($basketData,200);
        return $str;
    }
    
    function headerSearch($contentData,$textData) {
        if (!function_exists("cmsType_search")) return "";
        // $searchClass = cmsType_search_class();
        
        if (is_object( $this->searchClass)) {
            $contentData = array();
            $contentData[out] = "str";
            // $this->searchClass->init_own($this->contentData,$this->editContent,$this->frameWidth,$this->textData,$editText);
            $str .= $this->searchClass->contentType_show();
            return $str;          
        }
        
        $str = "SUCHE";
        $contentData[out] = "str";
        // $str = cmsType_search($contentData, $frameWidth);
        return $str;
    }
    
    function headerLanguage($contentData,$textData) {
        $activeLanguage = cms_text_getLanguage();
        $languageList = cms_text_getSettings();
        
        // foreach($_SESSION as $key => $value ) echo ("$key => $value <br>");
        
        $useMode = "enabled";
        if ($_SESSION[editable]) $useMode = "editable";
        $lgList = array();
        foreach ($languageList as $key => $value) {
            if (!is_array($value)) continue;
            $use = $value[$useMode];
            if ($use) $lgList[$key] = $value[name];
        }
        
        if (count($lgList)<2) return "";
        $str = "";
        
        $str .= "<span class='cmsLanguage_name'>".$this->lg("language","languageName",":")."</span>";
       
        $wirframeState = cmsWireframe_state();
        if ($wirframeState) {
            $str .= "<select class='cmsLanguageSelectList cmsLanguge_$activeLanguage' value='$activeLanguage' >";
            foreach ($lgList as $key => $languageName) {
                if ($key == $activeLanguage) $select = "selected='selected'";
                else $select = "";
                $str .= "<option value='$key' $select />$languageName</option>";
            }
            $str .= "</select>";            
        } else {
            $str .= div_start_str("cmsLanguage_line","display:inline-block;");
            $nr = 1;
            $anz = count($lgList);
            foreach ($lgList as $key => $languageName) {
                if ($key == $activeLanguage) {
                    $active = 1;
                    $className = "cmsLanguageSelect cmsLanguage_$key cmsLanguage_selected ";
                } else {
                    $active = 0;
                    $className = "cmsLanguageSelect cmsLanguage_$key";                
                }
                if ($nr == 1) $className .= " cmsLanguage_first";
                if ($nr == $anz) $className .= " cmsLanguage_last";
                $str .= div_start_str($className,array("id"=>"setLanguage_$key"));
                $str .= $languageName;
                $str .= div_end_str($className);
                $nr++;
            }
            $str .= div_end_str("cmsLanguage_line","before");
        }
        
        return $str;
    }
    
    
    function headerWireframe($contentData,$textData) {
        $wireframeState = cmsWireframe_state();
        $out = "";
        $setState = $_GET[setWireframe];
        if (!is_null($setState)) {
            
            $out .= "SET Wireframe TO '$setState' <br />";
            cmsWireframe_setState($setState);
        }
        
        if ($wireframeState) {
            // Darstellung ist Wireframe
            $out .= "<span class='cmsWireframe_name'>".$this->lg("wireframeOn","displayName",":")."</span>";
            
            $goPage = cms_page_goPage();
            // $out .= "<form action='$goPage' method='get' >";
            // $out .= "<input type='hidden' name='setWireframe' value='0' >";
            $out .= "<input class='cmsWireframeSwitch' type='checkbox' checked='checked' name='wireCheckbox' />";
           //  $out .= "</form>";
        } else {
            // Darstellung is normal
            $goPage = cms_page_goPage("setWireframe=1");
            $out .= "<span class='cmsWireframe_name'>".$this->lg("wireframe","displayName",":")."</span>";
            $out .= div_start_str("cmsWireframeSwitch");
            
            $out .= div_start_str("cmsWireframe_on");
            $out .= $this->lg("wireframe","displayNormal");
            $out .= div_end_str("cmsWireframe_on");
         
            $out .= div_start_str("cmsWireframe_off",array("title"=>"Seite als Wireframe darstellen"));
           // $out .= "<div class='hiddenData' ><a href='$goPage' class='hiddenLink'>Wireframe</a></div>";
           $out .= $this->lg("wireframe","displayWireframe");
            $out .= div_end_str("cmsWireframe_off");
            
            $out .= div_end_str("cmsWireframeSwitch","before");
        }
        return $out;
    }
    
    function headerSpecial($contentData,$textData) {
        $str = "Special Data";
        return $str;
    }
    
    
    

     function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth = $this->frameWidth;

        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        $res["header"] = array();

        $res = array();
        $res[header][showName] = $this->lga("content","headerTab");
        $res[header][showTab] = "Simple";
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
        $addData["mode"] = "Simple";
        $res[header][] = $addData;

        $lgaCode = "contentType_header";
        $showTarget = $this->lga($lgaCode,"Position",":");
        $goLink     = " ".$this->lga($lgaCode,"goLink",":");
        
        $frameData = array("empty"=>"Standard","mode"=>"box");

        // SHOW LOGO
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showLogo"); //"Logo zeigen";
        if ($data[logo]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][logo]' value='1' />"; //
        $input .= $showTarget.$this->selectPosition($data[logoFrame],"editContent[data][logoFrame]",$frameData); //, $showData, $showFilter, $showSort);
        $input .= $goLink.$this->editContent_SelectSettings("link", $data[logoLink],"editContent[data][logoLink]",$showLinkData);

        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        
        // NAME
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showName"); //"Name zeigen";
        if ($data[name]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][name]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[nameFrame],"editContent[data][nameFrame]",array("mode"=>"box"));
        $input .= $goLink.$this->editContent_SelectSettings("link", $data[nameLink],"editContent[data][nameLink]",$showLinkData);
        
        $showData = array();
        $showData[out] = "input";
        $showData[width] = 200;
        $showData[viewMode] = "line";
        $input .= "<br />".$this->editContent_text("name",$this->textData,$showData);
        
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        
        $res[header][] = $addData;
     
        // Slogan
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showSlogan"); //"Slogan zeigen";
        if ($data[slogan]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][slogan]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[sloganFrame],"editContent[data][sloganFrame]",array("mode"=>"box"));
        $input .= $goLink.$this->editContent_SelectSettings("link", $data[sloganLink],"editContent[data][sloganLink]",$showLinkData);
        // $input .= $showTarget.$this->selectFrame($this->editContent[data][sloganFrame],"editContent[data][sloganFrame]",$frameData);
        $input .= "<br />".$this->editContent_text("slogan",$this->textData,$showData);
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        
//        
        // USER 
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showUser"); //"Benutzer zeigen";
        if ($data[user]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][user]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[userFrame],"editContent[data][userFrame]",array("mode"=>"box"));
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        
        
        // LANGUAGE 
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showLanguage"); //"Sprachwahl zeigen";
        if ($data[language]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][language]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[languageFrame],"editContent[data][languageFrame]",array("mode"=>"box"));
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        
        // Suche 
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showSearch"); //"Suche zeigen";
        $search = $data[search];
        if ($search) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][search]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[searchFrame],"editContent[data][searchFrame]",array("mode"=>"box"));
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        


         // BASKET
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showBasket"); //"Warenkorb zeigen";
        if ($data[basket]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][basket]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[basketFrame],"editContent[data][basketFrame]",array("mode"=>"box"));
        $emptyBasket = $data[emptyBasket];
        if ($emptyBasket) $checked="checked='checked'";
        else $checked = "";
        $input .= " Leeren Warenkrob zeigen <input type='checkbox' $checked name='editContent[data][emptyBasket]' value='1' />";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        
        
        // WireframeSwicth
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showWireframe"); //"Wireframe Schalter";
        if ($data[wireframeSwitch]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][wireframeSwitch]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[wireframeSwitchFrame],"editContent[data][wireframeSwitchFrame]",array("mode"=>"box"));
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[header][] = $addData;
        
        
        // Special
        $addData = array();
        $addData["text"] = $this->lga($lgaCode,"showSpecial"); //"Sonderanzeige";
        if ($data[special]) $checked="checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][special]' value='1' />";
        $input .= $showTarget.$this->selectPosition($data[specialFrame],"editContent[data][specialFrame]",array("mode"=>"box"));        
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[header][] = $addData;
        
        $addData = array();
        $addData["text"] = "Rechts / Mitte / Links";
           
        
        
        $input = "";
        $input .= "Breite Links: <input type='text' style='width:40px;' value='$data[LR_left]' name='editContent[data][LR_left]' />";
        $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LR_abs]' name='editContent[data][LR_abs]' />";
        $input .= "Breite Mitte: <input type='text' style='width:40px;' value='$data[LR_center]' name='editContent[data][LR_center]' />";
        $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LRC_abs]' name='editContent[data][LRC_abs]' />";
        $input .= "Breite Rechts: <input type='text' style='width:40px;'value='$data[LR_right]' name='editContent[data][LR_right]' />";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        // $res[header][] = $addData;
        
        $showLeftRight = 1;
        $showMiddle = 0;
        $showDescription = 0;
        foreach ($data as $key => $value) {
            if ($value == "center") $showMiddle = 1;
            if ($value == "middle") $showMiddle = 1;
        }
        $add = $this->editcontent_dataBox_definition($showLeftRight,$showMiddle,$showDescription);
        foreach ($add as $key => $addData) $res[header][] = $addData;
        
            
        

        
       //  $res["text"] = cms_contentType_text_editContent($editContent,$frameWidth);

        if ($this->imageClass) {
             $searchEdit = $this->imageClass->contentType_editContent();
             foreach ($searchEdit as $searchKey => $searchValue) {
                 if ($searchKey == "image") {
                     $searchKey = "logo";
                     $searchValue[showName] = $this->lga("content","logoTab");
                 }
                 $res[$searchKey] = $searchValue;
            }
        }
       //  $res["logo"] = $this->editContent_imageSettings($dontShow); // cmsType_image_editContent($editContent,$frameWidth);


        if ($this->loginClass) {
            // $res[user] = $this->loginClass->contentType_editContent();
            $searchEdit = $this->loginClass->contentType_editContent();
            foreach ($searchEdit as $searchKey => $searchValue) {
                $res[$searchKey] = $searchValue;
            }


        }
        // $res["user"] = cmsType_login_editContent($editContent, $frameWidth);

         if ($this->searchClass) {
             $searchEdit = $this->searchClass->contentType_editContent();
             foreach ($searchEdit as $searchKey => $searchValue) {
                 // echo "$searchKey = $searchValue <br >";
                 $res[$searchKey] = $searchValue;
            }
            // $res[search] = $this->searchClass->contentType_editContent();
        }

        if ($search) {
//            $searchEdit = cmsType_search_editContent($editContent);
//            foreach ($searchEdit as $searchKey => $searchValue) {
//                $res[$searchKey] = $searchValue;
//            }
        }
        

        //$res["more"][] = $addData;
        
        return $res;
    }
    
    function selectFrame($code,$dataName,$showData=array()) {
        $selectList = array("top"=>"Oben","left"=>"Links","middle"=>"Mitte","right"=>"Rechts","bottom"=>"Unten");


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
