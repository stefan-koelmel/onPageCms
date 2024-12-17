<?php
class cms {
    
    public static $cmsName;
    public static $cmsVersion;
    public static $cmsSettings;
    
    public static function init($cmsName,$cmsVersion) {
        self::$cmsName = $cmsName;
        self::$cmsVersion = $cmsVersion;
        
        
        self::$cmsSettings = self::cmsSettings_get();
    }
    
    
    public static function cmsSettings_get() {
        // $cmsSettings = session::get("cmsSettings");
        if (is_array($cmsSettings)) return $cmsSettings;
        
        if (!function_exists("cms_settings_get")) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".self::$cmsVersion."/data/cms_settings.php";
            if (file_exists($settingsFile)) {
                include($settingsFile);
            } else {
                echo ("NOT EXIST settingsFile $settingsFile <br>");
                die();
            }
        }


        $getSettings = cms_settings_get(self::$cmsName);
        if (!is_array($getSettings)) {
            echo ("NO SETTINGS GET FOR ".self::$cmsName."<br>");
            die();
        }
        session::set("cmsSettings",$getSettings);
        
        
        return $getSettings;
    }
    
    
    
    
}

?>
