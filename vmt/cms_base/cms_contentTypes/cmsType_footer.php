<?php // charset:UTF-8
class cmsType_footer_base extends cmsType_contentTypes_base {

    function getName() {
        return "Fußzeile";
    }
    
    function show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("footer","width:".$frameWidth."px;");
       // echo ("<h1>FOOTER</h1>");
        if (is_string($_GET[edit])) {
            if ($_SESSION[userLevel]>6) {
                $_SESSION[edit] = intval($_GET[edit]);
            }
            $goPage = cms_page_goPage();
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
            // echo ("Kontakt Level = $showLevel<br />");
            if ($myShowLevel >= $showLevel) {
                // show_array($pageData);
                $class = "footerLink";
                if ($pageInfo[pageName] == "kontakt") $class .= " footerActive";
                echo ("<a href='kontakt.php' class='$class' >Kontakt</a> ");
            }
        }

        if ($data[sitemap]) {
            $pageData = cms_page_getData("sitemap");
            $showLevel = $pageData[showLevel];
            if ($myShowLevel >= $showLevel) {
                $class = "footerLink";
                if ($pageInfo[pageName] == "sitemap") $class .= " footerActive";
                echo ("<a href='sitemap.php' class='$class' >Sitemap</a> ");
            }
        }

        if ($data[impressum]) {
            $pageData = cms_page_getData("impressum");
            $showLevel = $pageData[showLevel];
            if ($myShowLevel >= $showLevel) {
                $class = "footerLink";
                if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
                echo ("<a href='impressum.php' class='$class' >Impressum</a> ");
            }
        }

        if ($_SESSION[userLevel]>6) {

            
                
            if ($_SESSION[edit]==1) {
                $goPage = cms_page_goPage("edit=0");
                echo (" | <a href='$goPage'>editiern stoppen!</a>");
            } else {
                $goPage = cms_page_goPage("edit=1");
                echo (" | <a href='$goPage'>editiern</a> ");
            }
                
                
                
            

            $showLevel = $_SESSION[showLevel];
            $userLevel = $_SESSION[userLevel];
            //echo ("showLevel = $showLevel / UserLevel = $userLevel ");
            echo ("<form method='post' style='display:inline-block;' >");

            echo ("Seite anzeigen als: ");
            $showData = array();
            $showData[submit] = 1;
            echo (cmsUser_selectUserLevel($showLevel,"setShowLevel",$showData,$showFilter,$showSort));

            // echo ("Seite anzeigen als: ".cms_user_selectlevel($showLevel,$userLevel,"setShowLevel",array("onChange"=>"submit()")));
            echo ("</form>");


        }



        if ($_SESSION[showLevel]>8) {
            $class = "footerLink";
            // if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
            echo (" | <a href='../index.php' class='$class' >CMS-Page</a>");
        }
        div_end("footer","before");
    }



    function footer_editContent($editContent) {
       //  foreach ($editContent[data] as $key => $value ) echo ("editCont $key = $value <br />");

        $res = array();

        // MainData
        $addData = array();
        $addData["text"] = "Kontakt";
        $input = "<input type='checkbox' name='editContent[data][kontakt]' value='1' ";
        if ($editContent[data][kontakt]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res[] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = "Sitemap";
        $input = "<input type='checkbox' name='editContent[data][sitemap]' value='1' ";
        if ($editContent[data][sitemap]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res[] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = "Impressum";
        $input = "<input type='checkbox' name='editContent[data][impressum]' value='1' ";
        if ($editContent[data][impressum]) $input .= "checked='checked'";
        $input .= ">\n";
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



function cmsType_footer_editContent($editContent) {
    $footerClass = cmsType_footer_class();
    return $footerClass->footer_editContent($editContent);
}


?>
