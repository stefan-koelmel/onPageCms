<?php // charset:UTF-8
class cmsType_social_base extends cmsType_contentTypes_base {

    function getName(){
        return "Soziale Dienste";
    }

    function show($contentData,$frameWidth) {
        // echo ("Soziale Dienste $contentData $frameWidth<br />");
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $style = "";
        if ($frameWidth > 200) $style.= "width:200px;float:left";
        else $style .= "width:".($frameWidth-2-5)."px;";

        $direction = $data[direction];
        if (!$direction) $direction = "hori";
        
        switch ($direction) {
            case "hori" :
                $anz = 6;
                $width = floor($frameWidth / $anz);
                break;
            
            case "vert" :
                $width = $frameWidth;
                break;
        }
        
        div_start("cmsSocialFrame");
        $socialList = $this->socialList();
        foreach ($socialList as $key => $name) {
            $this->showSocial($key, $contentData, $width);            
        }
        div_end("cmsSocialFrame","before");

//        div_start("cmsSocialFrame");
//        foreach($data as $key => $value) {
//            if ($value == "1") {
//                switch ($key) {
//                    case "advise"     : $this->socialShow_AdviseShow($style,$frameWidth); break;
//                    case "facebook"   : $this->socialShow_FacebookShow($style,$frameWidth); break;
//                    case "twitter"    : $this->socialShow_TwitterShow($style,$frameWidth); break;
//                    case "googlePlus" : $this->socialShow_GooglePlusShow($style,$frameWidth); break;
//                    case "rss"        : $this->socialShow_RssShow($style,$frameWidth); break;
//                    default:
//                        echo ("$key = $value <br />");
//                }
//            }
//
//
//        }
//        div_end("cmsSocialFrame","before");



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
    
    function showSocial($sociacType,$contentData,$frameWidth) {
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
            div_start("socialMediaFrame",$style);
            echo ($res);
            div_end("socialMediaFrame","before");            
        }
    }
    
    
    function socialShow_advise($use_icon,$use_color,$wireFrame,$frameWidth) {
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = "Seite empfehlen";
        $divName .= " socialMediaAdviseButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaAdvise_".$use_color;
        
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= "Seite empfehlen";
            $res .= div_end_str("socialMediaText");
        }                
        return $res;
    }
    
    function socialShow_facebook($use_icon,$use_color,$wireFrame,$frameWidth) {
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = "facebook";
        $divName .= " socialMediaFacebookButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaFacebook_".$use_color;
        
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= "facebook";
            $res .= div_end_str("socialMediaText");
        }                
        return $res;
    }
    
    function socialShow_twitter($use_icon,$use_color,$wireFrame,$frameWidth) {
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = "twitter";
        $divName .= " socialMediaTwitterButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaTwitter_".$use_color;
        
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= "twitter";
            $res .= div_end_str("socialMediaText");
        }                
        return $res;
    }    
    
    
    function socialShow_googlePlus($use_icon,$use_color,$wireFrame,$frameWidth) {
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = "googlePlus Seite";
        $divName .= " socialMediaGoogleButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaGoogle_".$use_color;
        
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= "googlePlus";
            $res .= div_end_str("socialMediaText");
        }                
        return $res;
    }

    function socialShow_youtube($use_icon,$use_color,$wireFrame,$frameWidth) {
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = "youTube Channel";
        $divName .= " socialMediaYoutubeButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaYoutube_".$use_color;
        
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= "youTube";
            $res .= div_end_str("socialMediaText");
        }                
        return $res;
    }

    function socialShow_rss($use_icon,$use_color,$wireFrame,$frameWidth) {
        $res = "";        
        $divName = "socialMediaButton";
        $divData = array();
        $divData[title] = "rss Feed";
        $divName .= " socialMediaRssButton";
        if ($wireFrame AND $use_color == "color") $use_color = null;
        if ($use_color) $divName .= "  socialMediaRss_".$use_color;
        
        $res .= div_start_str($divName,$divData);
        $res .= "&nbsp";
        $res .= div_end_str($divName);
            
        if (!$use_icon) {
            $res .= div_start_str("socialMediaText");
            $res .= "rss";
            $res .= div_end_str("socialMediaText");
        }                
        return $res;
    }
    
    

    function socialShow_AdviseShow($style) {
        div_start("SocialButton socialAdviseButton",$style);
        echo ("Seite empfehlen");
        div_end("SocialButton socialAdviseButton");
    }



    function socialShow_FacebookShow($style,$frameWidth){
       //    $style .= "height:100px;";
        div_start("SocialButton socialFacebookButton",$style);
        echo("<div id='fb-root'></div>");
        echo("<script>(function(d, s, id) {");
        echo("var js, fjs = d.getElementsByTagName(s)[0];");
        echo("if (d.getElementById(id)) return;");
        echo("js = d.createElement(s); js.id = id;");
        echo("js.src = '//connect.facebook.net/de_DE/all.js#xfbml=1';");
        echo("fjs.parentNode.insertBefore(js, fjs);");
        echo("}(document, 'script', 'facebook-jssdk'));</script>");

        echo("<div class='fb-like' data-href='http://cms.stefan.koelmel.com' data-send='false' data-layout='button_count' data-width='200' data-show-faces='true' data-font='tahoma'>");
        echo("</div>");
        div_end("SocialButton socialFacebookButton");
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



    function editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        
        $addData = array();
        $addData["text"] = "Ausrichtung";
        $addData["input"] = $this->edit_direction($data[direction],"editContent[data][direction]");
        $addData["mode"] = "Simple";
        $res[] = $addData;
        //$directionList = array("hori"=>"Hoizontal","vert"=>"Vertikal");
        
        
        
        // Empfehlen
        $addData = array();
        $addData["text"] = "Seite empfehlen";
        if ($data[advise]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][advise]' />";
        $input .= " nur Icon";
        if ($data[adviseIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][adviseIcon]'>";
        $input .= " Icon-Farbe ".$this->edit_iconColor($data[adviseColor],"editContent[data][adviseColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        // Facebook
        $addData = array();
        $addData["text"] = "Facebook";
        if ($data[facebook]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][facebook]'>";
        $input .= " nur Icon";
        if ($data[facebookIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][facebookIcon]'>";
        $input .= " Icon-Farbe ".$this->edit_iconColor($data[facebookColor],"editContent[data][facebookColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        // Twitter
        $addData = array();
        $addData["text"] = "Twitter";
        if ($data[twitter]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][twitter]'>";
        $input .= " nur Icon";
        if ($data[twitterIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][twitterIcon]'>";
        $input .= " Icon-Farbe ".$this->edit_iconColor($data[twitterColor],"editContent[data][twitterColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        

        // GooglePlus
        $addData = array();
        $addData["text"] = "GooglePlus";
        if ($data[googlePlus]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][googlePlus]'>";
        // GooglePlus-Icon
        $input .= " nur Icon";
        if ($data[googlePlusIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][googlePlusIcon]'>";
        $input .= " Icon-Farbe ".$this->edit_iconColor($data[googleColor],"editContent[data][googleColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        
        // YouTube
        $addData = array();
        $addData["text"] = "YouTube";
        if ($data[youtube]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][youtube]'>";
        // GooglePlus-Icon
        $input .= " nur Icon";
        if ($data[youtubeIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][youtubeIcon]'>";
        $input .= " Icon-Farbe ".$this->edit_iconColor($data[youtubeColor],"editContent[data][youtubeColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        // RSS
        $addData = array();
        $addData["text"] = "RSS Feed";
        if ($data[rss]) $checked = "checked='checked' ";
        else $checked = "";
        $input  = "<input type='checkbox' $checked value='1' name='editContent[data][rss]'>";
        $input .= " nur Icon";
        if ($data[rssIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][rssIcon]'>";
        $input .= " Icon-Farbe ".$this->edit_iconColor($data[rssColor],"editContent[data][rssColor]");
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        return $res;
    }
    
    function edit_direction($value,$dataName) {
        $directionList = array("hori"=>"Hoizontal","vert"=>"Vertikal","none"=>"Nebeneinander");
        $res = "";
        $res.= "<select value='$value' name='$dataName' >";
        
        foreach ($directionList as $key => $name) {
            if ($value == $key) $selected="selected='selected'";
            else $selected = "";
            $res .= "<option value='$key' $selected >$name</option>";
        }
        
        $res .= "</select>";
        return $res;
    }
        
    
    function edit_iconColor($value,$dataName) {
        $colorList = array("white"=>"Weiß","black"=>"Schwarz","color"=>"Farbig");
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
