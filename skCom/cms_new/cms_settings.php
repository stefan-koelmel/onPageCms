<?php // charset:UTF-8
function cms_settings_get() {
    // echo ("GET cms_settings_get()<br />");
    $cmsName = $GLOBALS[cmsName];
    if (!$cmsName) {
        $cmsName = $_SESSION[cmsName];
        if (!$cmsName) {
            $pageInfo = cms_page_getInfo();
            
            $cmsName = "empty";
            $_SESSION[cmsName] = $cmsName;
        }
    }

    $query = "SELECT * FROM `cms_settings` WHERE `name` = '$cmsName' ";
    //global $cmsName;
   

   // echo ("CMS NAME = '$cmsName' <br> ");
    $result = mysql_query($query);
    if (!$result) {
        cms_errorBox("Fehler beim Abfragen der CMS-Settings");
        return 0;
    }

    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        echo($query."<br>");
        cms_errorBox("KEINE CMS-DATEN ERHALTEN - $cmsName");
        die();
    }

    if ($anz > 1) {
        cms_errorBox("NICHT EINDEUTIGE CMS-DATEN ERHALTEN $anz");
        die();
    }
    global $cmsSettings;
    $cmsSettings = mysql_fetch_assoc($result);

    if (is_string($cmsSettings[useTypes])) $cmsSettings[useTypes] = str2Array($cmsSettings[useTypes]);
    if (is_string($cmsSettings[specialData])) $cmsSettings[specialData] = str2Array($cmsSettings[specialData]);

    //show_array($cmsSettings);
    $_SESSION[cmsSettings] = $cmsSettings;
    return $cmsSettings;
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
