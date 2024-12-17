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



        div_start("cmsSocialFrame");
        foreach($data as $key => $value) {
            if ($value == "1") {
                switch ($key) {
                    case "advise"     : $this->socialShow_AdviseShow($style,$freameWidth); break;
                    case "facebook"   : $this->socialShow_FacebookShow($style,$freameWidth); break;
                    case "twitter"    : $this->socialShow_TwitterShow($style,$freameWidth); break;
                    case "googlePlus" : $this->socialShow_GooglePlusShow($style,$freameWidth); break;
                    case "rss"        : $this->socialShow_RssShow($style,$freameWidth); break;
                    default:
                        echo ("$key = $value <br />");
                }
            }


        }
        div_end("cmsSocialFrame","before");



    }

    function socialShow_AdviseShow($style) {
        div_start("SocialButton socialAdviseButton",$style);
        echo ("Seite empfehlen");
        div_end("SocialButton socialAdviseButton");
    }



    function  socialShow_FacebookShow($style,$frameWidth){
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



        // echo ("Facebook");
        /*echo ("<iframe src='http://www.facebook.com/plugins/like.php?href=YOUR_URL'");
        echo ("scrolling='no' frameborder='0'");
        echo ("style='border:none; width:".$frameWidth."px;height:100px;' ></iframe>");*/

        div_end("SocialButton socialFacebookButton");
    }
    
    function socialShow_TwitterShow($style){
        // $style .= "height:100px;";
        div_start("SocialButton socialTwitterButton",$style);
        // echo ("Twitter");


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

        // MainData
        $addData = array();
        $addData["text"] = "Seite empfehlen";
        if ($data[advise]) $checked = "checked='checked' ";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked value='1' name='editContent[data][advise]'>";
        $res[] = $addData;

        // Facebook
        $addData = array();
        $addData["text"] = "Facebook";
        if ($data[facebook]) $checked = "checked='checked' ";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked value='1' name='editContent[data][facebook]'>";
        $res[] = $addData;

        // Twitter
        $addData = array();
        $addData["text"] = "Twitter";
        if ($data[twitter]) $checked = "checked='checked' ";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked value='1' name='editContent[data][twitter]'>";
        $res[] = $addData;

        // GooglePlus
        $addData = array();
        $addData["text"] = "GooglePlus";
        if ($data[googlePlus]) $checked = "checked='checked' ";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked value='1' name='editContent[data][googlePlus]'>";
        $res[] = $addData;

        // RSS
        $addData = array();
        $addData["text"] = "RSS Feed";
        if ($data[rss]) $checked = "checked='checked' ";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked value='1' name='editContent[data][rss]'>";
        $res[] = $addData;



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



function cmsType_social_editContent($editContent,$frameWidth) {
    $socialClass = cmsType_social_class();
    return $socialClass->editContent($editContent,$frameWidth);
}


?>
