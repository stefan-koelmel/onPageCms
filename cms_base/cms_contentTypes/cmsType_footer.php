<?php // charset:UTF-8
class cmsType_footer_base extends cmsType_contentTypes_base {

    function getName() {
        return "Fußzeile";
    }
    
    function show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("footer","width:auto;");
        
        
        
        $showEdit = 0;
        if ($_SESSION[userLevel]>6) $showEdit = 1;
        
        if ($showEdit == 0) {
            $thisPageData = $GLOBALS[pageData];
            $pageShowLevel = $thisPageData[showLevel];
            if ($pageShowLevel == 3) { // Special User Mode
                $myId = $_SESSION[userId];
                $allowedUser = $thisPageData[data][allowedUser];
                if ($allowedUser AND $myId) {
                    // echo ("ALLOWED USER ='$allowedUser' $myId <br>");
                    $userPos = strpos($allowedUser,"|$myId|");
                    if (is_int($userPos)) {
                        $showEdit = 1;
                    }
                }
            }                
        }
        
        
        
       // echo ("<h1>FOOTER</h1>");
        if (is_string($_GET[edit])) {
            if ($showEdit) {
                $_SESSION[edit] = intval($_GET[edit]);
            }
            $addPage = "";
            if ($_GET[editLayout]) $addPage.="editLayout=".$_GET[editLayout];
            $goPage = cms_page_goPage($addPage);
           
            reloadPage($goPage);
        }
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $myShowLevel = $_SESSION[showLevel];
        if (!$myShowLevel) $myShowLevel = 0;
        // echo ("myShowLevel $myShowLevel<br />" );
        // foreach ($data as $key => $value) echo ("$key = $value <br />");
        if ($data[kontakt]) {
            $pageData = cms_page_getData("kontakt");
            $showLevel = $pageData[showLevel];
            $showName = $pageData[title];
            $showName = cms_text_getLg($showName);
            // echo ("Kontakt Level = $showLevel<br />");
            if ($myShowLevel >= $showLevel) {
                // show_array($pageData);
                $class = "footerLink";
                if ($pageInfo[pageName] == "kontakt") $class .= " footerActive";
                echo ("<a href='kontakt.php' class='$class' >$showName</a> ");
            }
        }

        if ($data[sitemap]) {
            $pageData = cms_page_getData("sitemap");
            $showLevel = $pageData[showLevel];
            $showName = $pageData[title];
            $showName = cms_text_getLg($showName);
            if ($myShowLevel >= $showLevel) {
                $class = "footerLink";
                if ($pageInfo[pageName] == "sitemap") $class .= " footerActive";
                echo ("<a href='sitemap.php' class='$class' >$showName</a> ");
            }
        }

        if ($data[impressum]) {
            $pageData = cms_page_getData("impressum");
            $showLevel = $pageData[showLevel];
            $showName = $pageData[title];
            $showName = cms_text_getLg($showName);
            if ($myShowLevel >= $showLevel) {
                $class = "footerLink";
                if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
                echo ("<a href='impressum.php' class='$class' >$showName</a> ");
            }
        }

       
        
        if ($showEdit) {
//            if ($_SESSION[edit]==1) {
//                $goPage = cms_page_goPage("edit=0");
//                echo (" | <a href='$goPage'>editiern stoppen!</a>");
//            } else {
//                $goPage = cms_page_goPage("edit=1");
//                if ($_GET[editLayout]) $goPage.="&editLayout=".$_GET[editLayout];
//                echo (" | <a href='$goPage'>editiern</a> ");
//            }
//                
//            $showLevel = $_SESSION[showLevel];
//            $userLevel = $_SESSION[userLevel];
//            
//            if ($userLevel > 6) {    
//                //echo ("showLevel = $showLevel / UserLevel = $userLevel ");
//                echo ("<form method='post' style='display:inline-block;' >");
//
//                echo (" anzeigen:");
//                $showData = array();
//                $showData[submit] = 1;
//                echo (cmsUser_selectUserLevel($showLevel,"setShowLevel",$showData,$showFilter,$showSort));
//
//                // echo ("Seite anzeigen als: ".cms_user_selectlevel($showLevel,$userLevel,"setShowLevel",array("onChange"=>"submit()")));
//                echo ("</form>");
//            }


        }

        $social = $data[social];
        if ($social) $this->show_social($contentData,$frameWidth);

        if ($_SESSION[showLevel]>8) {
            $class = "footerLink";
            // if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
            echo (" | <a href='../index.php' class='$class' >CMS-Page</a>");
        }
        div_end("footer","before");
    }

    function show_social($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $advice = $data[advise];
        $advice_icon = $data[adviseIcon];
        
        $facebook = $data[facebook];
        $facebook_icon = $data[facebookIcon];
        
        $twitter = $data[twitter];
        $twitter_icon = $data[twitterIcon];
        
        $google = $data[googlePlus];
        $googleIcon = $data[googlePlusIcon];
        
        $rss = $data[rss];
        $rssIcon = $data[rssIcon];
        
        $youtube = $data[youtube];
        $youtubeIcon = $data[youtubeIcon];
        
        div_start("socialMedia");
        // show_array($data);
       //  echo ("Social");
        $wireframeState = cmsWireframe_state();
        
        $socialClass = cmsType_social_class();
        // echo ("SocialClass =$socialClass <br />");
        $socialClass->showSocial("advise", $contentData, $width);
        $socialClass->showSocial("facebook", $contentData, $width);
        $socialClass->showSocial("twitter", $contentData, $width);
        $socialClass->showSocial("googlePlus", $contentData, $width);
        $socialClass->showSocial("youtube", $contentData, $width);
        $socialClass->showSocial("rss", $contentData, $width);
        
        div_end("socialMedia","before");
        
        
        
        
    }
    

    function footer_editContent($editContent,$frameWidth) {
       //  foreach ($editContent[data] as $key => $value ) echo ("editCont $key = $value <br />");
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();

        // MainData
        $addData = array();
        $addData["text"] = "Kontakt";
        $input = "<input type='checkbox' name='editContent[data][kontakt]' value='1' ";
        if ($editContent[data][kontakt]) $input .= "checked='checked'";
        $input .= " />\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = "Sitemap";
        $input = "<input type='checkbox' name='editContent[data][sitemap]' value='1' ";
        if ($editContent[data][sitemap]) $input .= "checked='checked'";
        $input .= " />\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = "Impressum";
        $input = "<input type='checkbox' name='editContent[data][impressum]' value='1' ";
        if ($editContent[data][impressum]) $input .= "checked='checked'";
        $input .= " />\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        // Social Media
        $addData = array();
        $addData["text"] = "Social Media";
        $social = $data[social];
        if ($social) $checked = "checked='schecked'";
        else $checked = "";
        $input = "<input type='checkbox' name='editContent[data][social]' value='1' $checked />";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        $res[social] = $this->edit_social($editContent,$frameWidth);
        
        
        return $res;
    }
    
    
    
    function edit_social($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        $socialClass = cmsType_social_class();
        $res = $socialClass->editContent($editContent);
        return $res;
        // MainData
        $addData = array();
        $addData["text"] = "Seite empfehlen";
        if ($data[advise]) $checked = "checked='checked' ";
        else $checked = "";
        $input = "<input type='checkbox' $checked value='1' name='editContent[data][advise]' />";
        $input .= " nur Icon";
        if ($data[adviseIcon]) $checked = "checked='checked' ";
        else $checked = "";
        $input .= "<input type='checkbox' $checked value='1' name='editContent[data][adviseIcon]'>";
        $addData["input"] = $input;
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
        $addData["input"] = $input;
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
        $addData["input"] = $input;
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
        $addData["input"] = $input;
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
        $addData["input"] = $input;
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
        $addData["input"] = $input;
        $res[] = $addData;

        return $res;
    }
}

function cmsType_footer_class() {
    if ($GLOBALS[cmsTypes]["cmsType_footer.php"] == "own") $footerClass = new cmsType_footer();
    else $footerClass = new cmsType_footer_base();

    return $footerClass;
}


function cmsType_footer($contentData,$frameWidth) {
    $footerClass = cmsType_footer_class();
    $footerClass->show($contentData,$frameWidth);
}



function cmsType_footer_editContent($editContent,$frameWidth) {
    $footerClass = cmsType_footer_class();
    return $footerClass->footer_editContent($editContent,$frameWidth);
}


?>
