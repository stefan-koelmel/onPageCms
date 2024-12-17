<?php // charset:UTF-8
class cmsType_social_base extends cmsClass_content_show {

    function getName(){
        return "Soziale Dienste";
    }
    
    function setMainClass($mainClass=0) {
        if (is_object($mainClass)) {
            $this->mainClass = $mainClass;
            // echo ("<h1>Set Main Class in ".$this->getName()."</h1>");
        }
    }

    function contentType_show() {
        $useClass = $this;
        if ($this->mainClass) $useClass = $this->mainClass;
        
        $contentData = $useClass->contentData;
        $frameWidth = $useClass->frameWidth;
        // echo ("Soziale Dienste $contentData $frameWidth<br />");
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $style = "";
        if ($frameWidth > 200) $style.= "width:200px;float:left";
        else $style .= "width:".($frameWidth-2-5)."px;";

        $direction = $data[direction];
        if (!$direction) $direction = "hori";
        
        $socialList = $this->socialList();
        
        switch ($direction) {
            case "hori" :
                $anz = 0;
                foreach ($socialList as $key => $name) {
                    if ($data[$key]) $anz++;
                    // echo ("social $key => $name <br>");                              
                    
                }
                if ($anz == 0) $width=$frameWidth;
                else {
                    $width = floor($frameWidth / $anz);
                    $margin = 5;
                    $width = $width - $margin;
                }
                break;
            
            case "vert" :
                $width = $frameWidth;
                break;
        }
        $windowBackOpen = 0;
        if ($_POST[advise]) {
            if ($_POST[advise][contentId] == $useClass->contentId) $windowBackOpen = 1;
        }
        
        $backDiv = "socialMediaWindowBack";
        if ($windowBackOpen) $backDiv .= " socialMediaWindowBack_show";
        
        echo ("<div class='$backDiv'></div>");
        
        div_start("cmsSocialFrame");
        
        foreach ($socialList as $key => $name) {
            $this->showSocial($key, $contentData, $width,$direction);            
        }
        div_end("cmsSocialFrame","before");


    }
    
    function socialList() {
        $res = array();
        $res[advise] = "Seite Empfehlen";
        $res[facebook] = "facebook";
        $res[twitter] = "twitter";
        $res[googlePlus] = "googlePlus";
        $res[youtube] = "youTube";
        $res[vimeo] = "vimeo";
        $res[rss] = "rss Fees";
        return $res;        
    }
    
    function showSocial($sociacType,$contentData,$frameWidth,$direction) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $wireFrame = cmsWireframe_state();
        
       
        
        switch ($sociacType) {
            case "advise"     : 
                $use = $data[advise];
                if ($use) {
                    $use_icon = $data[adviseIcon];
                    $use_color = $data[adviseColor];
                    $res = $this->socialShow_advise($use_icon,$use_color,$wireFrame,$frameWidth); 
                }
                break;
            
            case "facebook"   : 
                $use = $data[facebook];
                if ($use) {
                    $use_icon = $data[facebookIcon];
                    $use_color = $data[facebookColor];
                    $res = $this->socialShow_facebook($use_icon,$use_color,$wireFrame,$frameWidth); 
                }
                break;
                
            case "twitter"    : 
                $use = $data[twitter];
                if ($use) {
                    $use_icon = $data[twitterIcon];
                    $use_color = $data[twitterColor];
                    $res = $this->socialShow_twitter($use_icon,$use_color,$wireFrame,$frameWidth); 
                    break;
                }
            case "googlePlus" : 
                $use = $data[googlePlus];
                if ($use) {
                    $use_icon = $data[googlePlusIcon];
                    $use_color = $data[googleColor];
                    $res = $this->socialShow_googlePlus($use_icon,$use_color,$wireFrame,$frameWidth); 
                }
                break;
            case "youtube"    : 
                $use = $data[youtube];
                if ($use) {
                    $use_icon = $data[youtubeIcon];
                     $use_color = $data[youtubeColor];
                    $res = $this->socialShow_youtube($use_icon,$use_color,$wireFrame,$frameWidth);
                }
                break;
            case "rss"        : 
                $use = $data[rss];
                if ($use) {
                    $use_icon = $data[rssIcon];
                    $use_color = $data[rssColor];
                    $res = $this->socialShow_rss($use_icon,$use_color,$wireFrame,$frameWidth);
                }
                break;
        }
        
        if ($res) {
            $style = "";
            if ($frameWidth) $style .= "width:".$frameWidth."px;";
            $divName = "socialMediaFrame socialMediaFrame_".$direction;
            // $divName .= " socialMediaWindow_button";
            div_start($divName,$style);
            echo ($res);
            div_end($divName,"before");            
        }
    }
    
    function socialShow_window($socialType,$windowPosition,$open=0) {
        $res = "";
        
        $windowName = "socialMediaWindow socialMedia_window_".$socialType;
        
        
        $windowName .= " socialMediaWindow_".$windowPosition;
        if ($open) $windowName .= " socialMediaWindow_show";
        
        $windowData = array();
        $windowData[id] = "socialWindow_".$socialType;
        $windowData[style] = "";
        $res .= div_start_str($windowName,$windowData);
        $res .= $this->socialShow_windowContent($socialType);
        $res .= div_end_str($windowName,$windowData);
        return $res;
    }
    
    function socialShow_windowContent($socialType) {
        switch ($socialType) {
            case "facebook" : $res .= $this->socialShow_facebook_windowContent(); break;
            case "twitter" : $res .= $this->socialShow_twitter_windowContent(); break;
            case "google" : $res .= $this->socialShow_google_windowContent(); break;
            case "youtube" : $res .= $this->socialShow_youtube_windowContent(); break;
            case "advise" : $res .= $this->socialShow_advise_windowContent(); break;
            case "rss" : $res .= $this->socialShow_rss_windowContent(); break;
            
            default :
                $res .= "SocialWindow no definition for socialtype ($socialType)";                                
        }
        return $res;
    }
    
    function social_getName($socialType) {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $name = $useClass->lg("contentType_social",$socialType."Name");
        return $name;
    }
    
    
    function socialShow_advise($use_icon,$use_color,$wireFrame,$frameWidth) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $socialClass = "advise";
        
        
        $title = $this->social_getName($socialClass);
        
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = $title ; //"Seite empfehlen";
        $divName .= " socialMediaAdviseButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaAdvise_".$use_color;
        
        $windowPosition = $useClass->contentData[data][windowDirection];
        if (!$windowPosition) $windowPosition = "none";
        
        
        $mainDiv = "socialMedia_item";
        if ($windowPosition == "none") {
            $showWindow = 0;
            $showLink = 1;
            
            $link = $useClass->contentData[data][$socialClass."Link"];
            if ($link) $divData[link] = $link;
            
        } else {
            $open = $this->socialShow_adviseCheck();
            $showWindow = 1;
            $showLink = 0;
            $windowStr = $this->socialShow_window("advise",$windowPosition,$open);
            $res .= $windowStr;
            $mainDiv .= " socialMediaWindow_button";
        }
        
        $res .= div_start_str($mainDiv);
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp"; 
        $res .= div_end_str($divName);
        // $res .= "Type=".$useClass->contentType;
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= $title; 
            $res .= div_end_str("socialMediaText");
        }          
        $res.= div_end_str($mainDiv);
        return $res;
    }
    
     
    
    
    function socialShow_adviseCheck() {
        $open = 0;
        if (!$_POST[adviseSend]) return $open;
        
        $error = 0;
        
        
        $res = array();
        $advise = $_POST[advise];
        
        // Check ID
        $sendId = $advise[contentId];
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        if ($sendId != $useClass->contentId) {
            // echo ("Diffrent id send='$sendId' myId='".$useClass->contentId."' <br />");
            return 0;
        }
        
        
        $res .= "id=".$useClass->contentId."<br />";
        
        $type = $advise[type];
        $res["type"] = array("data"=>$type);
        // vName 
        $my_vName = $advise[my_vName];
        $my_error = 0;
        if (!$my_vName) $my_error = 1;
        $res["my_vName"] = array("data"=>$my_vName,"error"=>$my_error);
        
        // nName 
        $my_nName = $advise[my_nName];
        $my_error = 0;
        if (!$my_nName) $my_error = 1;
        $res["my_nName"] = array("data"=>$my_nName,"error"=>$my_error);
        
        // eMail 
        $my_eMail = $advise[my_eMail];
        $my_error = 0;
        if (!$my_eMail) $my_error = 1;
        $res["my_eMail"] = array("data"=>$my_eMail,"error"=>$my_error);
        
        
        // send_vName 
        $send_vName = $advise[send_vName];
        $send_error = 0;
        if (!$send_vName) $send_error = 1;
        $res["send_vName"] = array("data"=>$send_vName,"error"=>$send_error);
        
        // nName 
        $send_nName = $advise[send_nName];
        $send_error = 0;
        if (!$send_nName) $send_error = 1;
        $res["send_nName"] = array("data"=>$send_nName,"error"=>$send_error);
        
        // eMail 
        $send_eMail = $advise[send_eMail];
        $send_error = 0;
        if (!$send_eMail) $send_error = 1;
        $res["send_eMail"] = array("data"=>$send_eMail,"error"=>$send_error);
        
        
        return $res;
        
    }
    
    function socialShow_advise_windowContent() {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        
        $res .= "<h2 class='socialMediaWindow_headline' >Seite empfehlen</h2>";
        $adviseData = $this->socialShow_adviseCheck();
        if (is_array($adviseData)) {
//            foreach ($adviseData as $key => $value ) {
//                $res .= "data $key = ".$value[data]." / error =$value[error] <br />";
//            }
        } else {
            $adviseData = array();
            $adviseData[type] = array("data"=>"site");
            $adviseData[my_vName] = array("data"=>"");
            $adviseData[my_nName] = array("data"=>"");
            $adviseData[my_eMail] = array("data"=>"");
            
            $adviseData[send_vName] = array("data"=>"");
            $adviseData[send_nName] = array("data"=>"");
            $adviseData[send_eMail] = array("data"=>"");
            
            $adviseData[send_message] = array("data"=>"");
        }
        
        if ($_SESSION[showLevel]>0) {
            $userId = $_SESSION[userId];
            if ($userId) {
                $userData = cms_user_getById($userId);
                if (is_array($userData)) {
                    $adviseData[my_vName][data] = $userData[vName];
                    $adviseData[my_nName][data] = $userData[nName];
                    $adviseData[my_eMail][data] = $userData[email];
                }
            }            
        }
        
        
        $res .= "<form method='post' action=''>";
        
        $res .= "<input type='hidden' name='advise[contentId]' value='".$useClass->contentId."' >";
       
        
        if ($adviseData["type"]["data"] == "site" ) $checked="checked='checked"; else $checked ="";
        $res .= "<input type='radio' name='advise[type]' $checked value='site' />allgemein diese Seite<br />"; 
        if ($adviseData["type"]["data"] == "page" ) $checked="checked='checked"; else $checked ="";
        $res .= "<input type='radio' name='advise[type]' $checked value='page' />diese Spezielle Seite<br />"; 
        
        
        $res .= "Ihr Name<br />";
       
        
        $res .= "<input type='text' style='width:45%;' name='advise[my_vName]' value='".$adviseData[my_vName][data]."' /> ";
        $res .= "<input type='text' style='width:45%;' name='advise[my_nName]' value='".$adviseData[my_nName][data]."' /><br />";
        $res .= "Ihre eMail Adresse<br />";
        $res .= "<input type='text' style='width:90%;' name='advise[my_eMail]' value='".$adviseData[my_eMail][data]."' /><br />";
        $res .= "<br />";
        $res .= "Empfehlen an:<br />";
        $res .= "<input type='text' style='width:45%;' name='advise[send_vName]' value='".$adviseData[send_vName][data]."' /> ";
        $res .= "<input type='text' style='width:45%;' name='advise[send_nName]' value='".$adviseData[send_nName][data]."' /><br />";
        $res .= "Ihre eMail Adresse<br />";
        $res .= "<input type='text' style='width:90%;' name='advise[send_eMail]' value='".$adviseData[send_eMail][data]."' /><br />";
        $res .= "Ihre Nachricht:<br />";
        
        $res .= "<br />";
        
        $res .="<input class='inputButton'  type='submit' value='Senden' name='adviseSend' />";
        $res .= "<div class='javaButton second socialWindowClose' >abbrechen</div>";
        // $res .="<input class='cmsInputButton cmsSecond socialWindowClose' type='button' value='abbrechen' name='adviseCancel' />";

        $res .= "</form>";
        
        
        
        return $res;
    }
    
    
    function socialShow_facebook($use_icon,$use_color,$wireFrame,$frameWidth) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $title = $this->social_getName("facebook");
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = $title;
        $divName .= " socialMediaFacebookButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaFacebook_".$use_color;
        
        $windowPosition = $useClass->contentData[data][windowDirection];
        if (!$windowPosition) $windowPosition = "none";
        $mainDiv = "socialMedia_item";
        
        if ($windowPosition == "none") {
            $showWindow = 0;
            $showLink = 1;
            
            $link = $useClass->contentData[data][facebookLink];
            if ($link) $divData[link] = $link;
            
        } else {
            $showWindow = 1;
            $showLink = 0;
            $windowStr = $this->socialShow_window("facebook",$windowPosition);
            $res .= $windowStr;
            $mainDiv .= " socialMediaWindow_button";
        }
        $res .= div_start_str($mainDiv);
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        $showLikes = 0;
        if ($showLikes) {
            $res .= $this->socialShow_facebook_likes(null,null);
        }
       
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= $title;
            $res .= div_end_str("socialMediaText");
        }                
        
        $res.= div_end_str($mainDiv);
        return $res;
    }
    
    function socialShow_facebook_windowContent() {
        // appPasswort ; 75YPTIJDUU
        
        $res .= "<div class='fb-like' data-href='https://www.facebook.de/onPageCms' data-width='200' data-height='200' data-colorscheme='dark' data-layout='standard' data-action='like' data-show-faces='true' data-send='false'></div>";
        
        $res .= "<h2 class='socialMediaWindow_headline' >Facebook</h2>";
        
        $res.= "<a class='socialFacebookPage' target='facebook' href='http://www.facebook.de/onPageCms' >Meine Facebook Seite</a><br/>";
        $res .= $this->socialShow_facebook_likes($style, $frameWidth);
        $res .= "<div class='javaButton second socialWindowClose' >abbrechen</div>";

        $res .= "<div class='fb-like-box' data-href='http://www.facebook.com/onPageCms' data-width='200' data-height='100' data-colorscheme='light' data-show-faces='true' data-header='false' data-stream='false' data-show-border='false'></div>";
        
        
        return $res;
    }
    
    function socialShow_twitter($use_icon,$use_color,$wireFrame,$frameWidth) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $title = $this->social_getName("twitter");
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = $title;
        $divName .= " socialMediaTwitterButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaTwitter_".$use_color;
        
        $windowPosition = $useClass->contentData[data][windowDirection];
        if (!$windowPosition) $windowPosition = "none";
        
        $mainDiv = "socialMedia_item";
        
        if ($windowPosition == "none") {
            $showWindow = 0;
            $showLink = 1;
            
            $link = $useClass->contentData[data][facebookLink];
            if ($link) $divData[link] = $link;
            
        } else {
            $showWindow = 1;
            $showLink = 0;
            $windowStr = $this->socialShow_window("twitter",$windowPosition);
            $res .= $windowStr;
             $mainDiv .= " socialMediaWindow_button";
        }
        $res .= div_start_str($mainDiv);
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= $title;
            $res .= div_end_str("socialMediaText");
        }                
        $res .= div_end_str($mainDiv);
        return $res;
    }  
    
    function socialShow_twitter_windowContent() {
        $res .= "<h2 class='socialMediaWindow_headline' >twitter</h2>";
        $res .= "<div class='javaButton second socialWindowClose' >abbrechen</div>";
        return $res;
    }
    
    
    function socialShow_googlePlus($use_icon,$use_color,$wireFrame,$frameWidth) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $title = $this->social_getName("googlePlus");
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = $title;
        $divName .= " socialMediaGoogleButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaGoogle_".$use_color;
        
        $windowPosition = $useClass->contentData[data][windowDirection];
        if (!$windowPosition) $windowPosition = "none";
        
        $mainDiv = "socialMedia_item";
        
        if ($windowPosition == "none") {
            $showWindow = 0;
            $showLink = 1;
            
            $link = $useClass->contentData[data][facebookLink];
            if ($link) $divData[link] = $link;
            
        } else {
            $showWindow = 1;
            $showLink = 0;
            $windowStr = $this->socialShow_window("google",$windowPosition);
            $res .= $windowStr;
             $mainDiv .= " socialMediaWindow_button";
        }
        $res .= div_start_str($mainDiv);
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= $title;
            $res .= div_end_str("socialMediaText");
        }                
        $res .= div_end_str($mainDiv);
        return $res;
    }
    function socialShow_google_windowContent() {
        $res .= "<h2 class='socialMediaWindow_headline' >googlePlus</h2>";
        $res .= "<div class='javaButton second socialWindowClose' >abbrechen</div>";
        return $res;
    }

    function socialShow_youtube($use_icon,$use_color,$wireFrame,$frameWidth) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $title = $this->social_getName("youTube");
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = $title;
        $divName .= " socialMediaYoutubeButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaYoutube_".$use_color;
        
        $windowPosition = $useClass->contentData[data][windowDirection];
        if (!$windowPosition) $windowPosition = "none";
        
        $mainDiv = "socialMedia_item";
        
        if ($windowPosition == "none") {
            $showWindow = 0;
            $showLink = 1;
            
            $link = $useClass->contentData[data][facebookLink];
            if ($link) $divData[link] = $link;
            
        } else {
            $showWindow = 1;
            $showLink = 0;
            $windowStr = $this->socialShow_window("youtube",$windowPosition);
            $res .= $windowStr;
             $mainDiv .= " socialMediaWindow_button";
        }
        $res .= div_start_str($mainDiv);
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= $title;
            $res .= div_end_str("socialMediaText");
        }                
        $res .= div_end_str($mainDiv);
        return $res;
    }
    
    function socialShow_youtube_windowContent() {
        $res .= "<h2 class='socialMediaWindow_headline' >youTube</h2>";
        $res .= "<div class='javaButton second socialWindowClose' >abbrechen</div>";
        return $res;
    }

    function socialShow_rss($use_icon,$use_color,$wireFrame,$frameWidth) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $title = $this->social_getName("rss");
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = $title;
        $divName .= " socialMediaRssButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaRss_".$use_color;
        
        $windowPosition = $useClass->contentData[data][windowDirection];
        if (!$windowPosition) $windowPosition = "none";
        
        $mainDiv = "socialMedia_item";
        
        if ($windowPosition == "none") {
            $showWindow = 0;
            $showLink = 1;
            
            $link = $useClass->contentData[data][facebookLink];
            if ($link) $divData[link] = $link;
            
        } else {
            $showWindow = 1;
            $showLink = 0;
            $windowStr = $this->socialShow_window("rss",$windowPosition);
            $res .= $windowStr;
             $mainDiv .= " socialMediaWindow_button";
        }
        $res .= div_start_str($mainDiv);
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= $title;
            $res .= div_end_str("socialMediaText");
        }
        $res .= div_end_str($mainDiv);
        return $res;
    }
    
    function socialShow_rss_windowContent() {
        $res .= "<h2 class='socialMediaWindow_headline' >RSS Feed</h2>";
        $res .= "<div class='javaButton second socialWindowClose' >abbrechen</div>";
        return $res;
    }
    
    

//    function socialShow_adviseShow($style) {
//        div_start("SocialButton socialAdviseButton",$style);
//        echo ("Seite empfehlen");
//        div_end("SocialButton socialAdviseButton");
//    }

    
    function socialShow_FacebookShow($style,$frameWidth){
        $str = $this->socialShow_facebook_likes($style,$frameWidth);
        echo ($str);
    }

    function socialShow_facebook_likes($style,$frameWidth){
       //    $style .= "height:100px;";
        $str = "";
        $str .= "HALLO!!";
        //$str .= "<script src='http://connect.facebook.net/de_DE/all.js#xfbml=1></script>";
        //$str .= "<fb:like-box href='http://www.onpagecms.com width='292' show_faces='true' stream='false' header='true'></fb:like-box>";
            
        
//        $str .= div_start_str("SocialButton socialFacebookButton",$style);
//        
        $str.= '<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.onpagecms.com&amp;layout=button_count&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100%; height:80px;" allowTransparency="true"></iframe>';
//        
//        
//        
//        $str .= "<div id='fb-root'></div>";
//        $str .= "<script>(function(d, s, id) {";
//        $str .= "var js, fjs = d.getElementsByTagName(s)[0];";
//        $str .= "if (d.getElementById(id)) return;";
//        $str .= "js = d.createElement(s); js.id = id;";
//        $str .= "js.src = '//connect.facebook.net/de_DE/all.js#xfbml=1';";
//        $str .= "fjs.parentNode.insertBefore(js, fjs);";
//        $str .= "}(document, 'script', 'facebook-jssdk'));</script>";
//
//        $str .= "<div class='fb-like' data-href='http://cms.stefan.koelmel.com' data-send='false' data-layout='button_count' data-width='200' data-show-faces='true' data-font='tahoma'>";
//        $str .= "</div>";
//        $str .= div_end_str("SocialButton socialFacebookButton");
        return $str;
    }
    
    function socialShow_TwitterShow($style){
        // $style .= "height:100px;";
        div_start("SocialButton socialTwitterButton",$style);
        echo ("Twitter".$style."<br>");


        echo("<a href='https://twitter.com/share' class='twitter-share-button' data-url='cms.stefan-koelmel.com' data-text='Test' data-lang='de'>Twittern</a>");
        echo("<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>");



       // echo ("<a href='https://twitter.com/share' class='twitter-share-button' data-url='http://cms.stefan-koelmel.com' data-via='skstefankoelmelcom' data-lang='de' data-size='large'>Twittern</a>");
       // echo ("<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js;fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>");
        div_end("SocialButton socialTwitterButton");
    }
    
    function socialShow_GooglePlusShow($style){
        //$style .= "height:100px;";
        div_start("SocialButton socialGooglePlusButton",$style);
        echo ("<g:plusone></g:plusone>");
        //<!-- Update your html tag to include the itemscope and itemtype attributes -->
        //echo("<html itemscope itemtype='http://schema.org/Organization'>");

    //<!-- Add the following three tags to your body -->
    //echo("<span itemprop='name'>Title of your content</span>");
    //echo("<span itemprop='description'>This would be a description of the content your users are sharing</span>");
        div_end("SocialButton socialGooglePlusButton");
    }
    
    function socialShow_RssShow($style) {
        div_start("SocialButton socialRssButton",$style);
        echo ("RSS");
        div_end("SocialButton socialRssButton");
    }



   function contentType_editContent() {
        $useClass = $this;
        if ($this->mainClass) $useClass = $this->mainClass;
        
        
        
        
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        $res[social] = array();
  
        $textType = "contentType_social";
        
        $onlyIcon = $useClass->lga($textType,"onlyIcon",": ");
        $iconColor = $useClass->lga($textType,"iconColor",": ");
        
        $res[social][showName] = $this->lga("content","socialTab"); //"Wechselnde Inhalte";
        $res[social][showTab] = "Simple";
        
        $addData = array();
        $addData["text"] = $useClass->lga($textType,"direction"); //"Ausrichtung";
        $addData["input"] = $this->edit_direction($data[direction],"editContent[data][direction]");
        $addData["mode"] = "Simple";
        $res[social][] = $addData;
        
        
        // Window Direction
        $addData = array();
        $addData["text"] = $useClass->lga($textType,"windowDirection"); //"Ausrichtung";
        $addData["input"] = $this->edit_windowDirection($data[windowDirection],"editContent[data][windowDirection]");
        $addData["mode"] = "Simple";
        $res[social][] = $addData;
        
        //$directionList = array("hori"=>"Hoizontal","vert"=>"Vertikal");
        
        // Empfehlen
        $addData = array();
        $addData["text"] =  $useClass->lga($textType,"advise"); //"Seite empfehlen";
        if ($data[advise]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][advise]' />";
        $input .= " $onlyIcon";
        if ($data[adviseIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][adviseIcon]'>";
        $input .= " $iconColor ".$this->edit_iconColor($data[adviseColor],"editContent[data][adviseColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[social][] = $addData;

        // Facebook
        $addData = array();
        $addData["text"] =  $useClass->lga($textType,"facebook"); //"Facebook";
        if ($data[facebook]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][facebook]'>";
        $input .= " $onlyIcon";
        if ($data[facebookIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][facebookIcon]'>";
        $input .= " $iconColor ".$this->edit_iconColor($data[facebookColor],"editContent[data][facebookColor]");
        // link
        if (!$data[facebookLink]) $data[facebookLink] = "http://www.facebook.de/";
        $input .= " Link:<input type='text' style='width:200px;' name='editContent[data][facebookLink]' value='$data[facebookLink]' />";        
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[social][] = $addData;

        // Twitter
        $addData = array();
        $addData["text"] =  $useClass->lga($textType,"twitter"); //"Twitter";
        if ($data[twitter]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][twitter]'>";
        $input .= " $onlyIcon";
        if ($data[twitterIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][twitterIcon]'>";
        $input .= " $iconColor ".$this->edit_iconColor($data[twitterColor],"editContent[data][twitterColor]");
        // link
        if (!$data[twitterLink]) $data[twitterLink] = "http://www.twitter.com/";
        $input .= " Link:<input type='text' style='width:200px;' name='editContent[data][twitterLink]' value='$data[twitterLink]' />";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[social][] = $addData;
        

        // GooglePlus
        $addData = array();
        $addData["text"] =  $useClass->lga($textType,"googlePlus"); //"GooglePlus";
        if ($data[googlePlus]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][googlePlus]'>";
        // GooglePlus-Icon
        $input .= " $onlyIcon";
        if ($data[googlePlusIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][googlePlusIcon]'>";
        $input .= " $iconColor ".$this->edit_iconColor($data[googleColor],"editContent[data][googleColor]");
        // link
        if (!$data[googleLink]) $data[googleLink] = "https://plus.google.com/";
        $input .= " Link:<input type='text' style='width:200px;' name='editContent[data][googleLink]' value='$data[googleLink]' />";        
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[social][] = $addData;
        
        
        // YouTube
        $addData = array();
        $addData["text"] =  $useClass->lga($textType,"youTube"); //"YouTube";
        if ($data[youtube]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][youtube]'>";
        // GooglePlus-Icon
        $input .= " $onlyIcon";
        if ($data[youtubeIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][youtubeIcon]'>";
        $input .= " $iconColor ".$this->edit_iconColor($data[youtubeColor],"editContent[data][youtubeColor]");
        // link
        if (!$data[youtubeLink]) $data[youtubeLink] = "http://www.youtube.com/";
        $input .= " Link:<input type='text' style='width:200px;' name='editContent[data][youtubeLink]' value='$data[youtubeLink]' />";        
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[social][] = $addData;

        // RSS
        $addData = array();
        $addData["text"] =  $useClass->lga($textType,"rss"); //"RSS Feed";
        if ($data[rss]) $checked = "checked='checked' ";
        else $checked = "";
        $input  = "<input type='checkbox' $checked value='1' name='editContent[data][rss]'>";
        $input .= " $onlyIcon";
        if ($data[rssIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][rssIcon]'>";
        $input .= " $iconColor ".$this->edit_iconColor($data[rssColor],"editContent[data][rssColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[social][] = $addData;

        return $res;
    }
    
    function edit_direction($value,$dataName) {
        $directionList = array("hori"=>"Hoizontal","vert"=>"Vertikal","none"=>"Nebeneinander");
        $res = "";
        $res.= "<select style='width:150px;' value='$value' name='$dataName' >";
        
        foreach ($directionList as $key => $name) {
            if ($value == $key) $selected="selected='selected'";
            else $selected = "";
            $res .= "<option value='$key' $selected >$name</option>";
        }
        
        $res .= "</select>";
        return $res;
    }
        
    
    function edit_windowDirection($value,$dataName) {
        $useClass = $this;
        if ($this->mainClass) $useClass = $this->mainClass;
        $textType = "contentType_social";
        
        
        
        $directionList = array("none"=>"","top"=>"nach oben","bottom"=>"nach unten","left"=>"nach links","right"=>"nach rechts","window"=>"Fenster");
        foreach ($directionList as $key => $val) {
            $text = $useClass->lga($textType,"windowDirection_".$key);
            $directionList[$key] = $text;
        }
        $res = "";
        $res.= "<select style='width:150px;' value='$value' name='$dataName' >";
        
        foreach ($directionList as $key => $name) {
            if ($value == $key) $selected="selected='selected'";
            else $selected = "";
            $res .= "<option value='$key' $selected >$name</option>";
        }
        
        $res .= "</select>";
        return $res;
    }
    
    function edit_iconColor($value,$dataName) {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        
        $textType = "contentType_social";
        
        $colorList = array("white"=>"Weiß","black"=>"Schwarz","color"=>"Farbig");
        foreach ($colorList as $key => $colorName) {
             $colorList[$key] = $useClass->lga($textType,"icon_".$key);
        }
        $res = "";
        $res.= "<select value='$value' name='$dataName' style='width:100px;' >";
        
        foreach ($colorList as $key => $name) {
            if ($value == $key) $selected="selected='selected'";
            else $selected = "";
            $res .= "<option value='$key' $selected >$name</option>";
        }
        
        $res .= "</select>";
        return $res;
    }
}


function cmsType_social_class() {
    if ($GLOBALS[cmsTypes]["cmsType_social.php"] == "own") $socialClass = new cmsType_social();
    else $socialClass = new cmsType_social_base();
    return $socialClass;
}


function cmsType_social($contentData,$frameWidth) {
    $socialClass = cmsType_social_class();
    $socialClass->show($contentData,$frameWidth);
}


function cmsType_socialView($sociacType,$contentData,$frameWidth) {
    $socialClass = cmsType_social_class();
    $socialClass->showSocial($sociacType,$contentData,$frameWidth);
}


function cmsType_social_editContent($editContent,$frameWidth) {
    $socialClass = cmsType_social_class();
    return $socialClass->editContent($editContent,$frameWidth);
}


?>
