<?php // charset:UTF-8
class cmsType_navi extends cmsType_navi_base {
    
    
    function show($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        if ($data[startLevel]>0) {
            cms_navi_showLayout($contentData,$frameWidth);
        } else {
            $host = $_SERVER[HTTP_HOST];
            $root = $_SERVER[DOCUMENT_ROOT];
           // echo ("Host $host / $root <br>");
            
            $root = $_SERVER[DOCUMENT_ROOT];
            $cmsName = $GLOBALS[cmsName];
            if (file_exists($root."/".$cmsName)) $root .= "/$cmsName";
            
            if (file_exists($root."/incs/header-mainmenu.php")) {
            
            
            // if (file_exists($root."/klappeAuf/incs/header-mainmenu.php")) {
                
             
                
                
            } else {
                cms_navi_showLayout($contentData,$frameWidth);
            }
          
            //show_array($_SERVER);
            
            //show_array($GLOBALS[pageInfo]);
            //show_array($contentData);
            //include("klappeAuf/incs/header-mainmenu.php");
           // echo ("<h1>NAVI</h1>");
           // 
        }
    }
       
    

}


?>
