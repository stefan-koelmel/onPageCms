<?php // charset:UTF-8
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function cms_footer_show($contentData,$frameWidth) {
    if (!$frameWidth) {
        // echo ("No FOOTER<br>");
        // echo("<script src='cms/cmsEdit.js'></script>");
        return 0;

    }
    $pageInfo = $GLOBALS[pageInfo];

    div_start("footer","width:".$frameWidth."px;");
    
    if (is_string($_GET[edit])) {
        if ($_SESSION[userLevel]>6) {
            $_SESSION[edit] = intval($_GET[edit]);            
        }
        $goPage = $pageInfo[page];
        reloadPage($goPage);
    }
    $class = "footerLink";
    if ($pageInfo[pageName] == "kontakt") $class .= " footerActive";
    echo ("<a href='kontakt.php' class='$class' >Kontakt</a> ");

    $class = "footerLink";
    if ($pageInfo[pageName] == "sitemap") $class .= " footerActive";
    echo ("<a href='sitemap.php' class='$class' >Sitemap</a> ");

    $class = "footerLink";
    if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
    echo ("<a href='impressum.php' class='$class' >Impressum</a> ");

    
    echo ("Footer");
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
        // echo ("Seite anzeigen als: ");
        $showData = array();
        $showData[submit] = 1;
        //echo (cmsUser_selectUserLevel($showLevel,"setShowLevel",$showData,$showFilter,$showSort));
        // echo (cms_user_selectlevel($showLevel,$userLevel,"setShowLevel",array("onChange"=>"submit()")));
        echo ("</form>");


    }
  


    if ($_SESSION[showLevel]>8) {
        $class = "footerLink";
        // if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
        echo (" | <a href='../index.php' class='$class' >CMS-Page</a>");
    }
    div_end("footer","before");
}
?>
