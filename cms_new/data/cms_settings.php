<?php // charset:UTF-8
function cms_settings_get($getCmsName) {
    if (!$getCmsName) {
    
        $getCmsName = $GLOBALS[cmsName];
        if (!$getCmsName) {
            $getCmsName = $_SESSION[cmsName];
            if (!$getCmsName) {
                $pageInfo = cms_page_getInfo();
                
                $getCmsName = "empty";
                $_SESSION[cmsName] = $getCmsName;
            }
        }
    }
    
    
    
    
    $query = "SELECT * FROM `cms_settings` WHERE `name` = '$getCmsName' ";
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
  
    // site_session_set(cmsSettings,$cmsSettings);
    return $cmsSettings;
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
