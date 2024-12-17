<?php // charset:UTF-8

class cms_base { //extends cmsAdmin_editClass_base {

function cms_start() {
    $debugGet = $_GET[debug];
    if ($debugGet) {
        global $debug;
        $debug = $debugGet;
    }
    
    
    
//echo ("<title>Hallo CMS</title>");
  
    global $cmsName,$cmsVersion;
    $helpExist = function_exists("helpExist");
    if (!$helpExist) {
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/help.php");
        $helpExist = function_exists("helpExist");       
    }

     
    if ($debug) echo ("<h1>$cmsVersion,$cmsName</h1>");
    
    $pageStylesExist = function_exists("pageStylesExist");
    if (!$pageStylesExist) {
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/pageStyles.php");
    }
    
    // Connection to cms-Datenbank
    $cmsConnected = 0;    
    if ($_SERVER["HTTP_HOST"] == "cms.stefan-koelmel.com" ) {
        // echo "My Test Server<br>";
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_connect.php");
        $cmsConnected = 1;
    }
    if ($_SERVER["HTTP_HOST"] == "cms.2-pi-r.de" ) {
        // echo "My Test Server<br>";
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_connect.php");
        $cmsConnected = 1;
    }
 
    if ($cmsConnected == 0) {
        $ownConnect = $_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php";
        if (file_exists($ownConnect)) {
            if ($debug) echo ("found in $ownConnect<br>");
            include($ownConnect);
        } else {
            $ownConnect = $_SERVER['DOCUMENT_ROOT']."/".$cmsName."/cms/cms_connect.php";
            if (file_exists($ownConnect)) {
                if ($debug) echo ("found in <b>$cmsName</b> $ownConnect<br>");
                include($ownConnect);
            } else {
                 echo ("<h1> DBData not found </h1>");
                if ($debug) show_array($_SERVER);
                die();

            }

        }
    }
    
    
    $defaultText_exist = is_array($_SESSION[defaultText]);
    $adminText_exist = is_array($_SESSION[adminText]);
    
    if (!$editText_exist OR !$defaultText_exist) {
        include ($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_defaultText.php");
    
        if (!$defaultText_exist) load_defaultText();
        if (!$adminText_exist) load_adminText();
    }
    
   
    
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_styles.php");

    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_settings.php");

    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_cache.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_history.php");
//    $file = "http://setup.onPageCMS.com/index.php";
//    if (file_exists($file)) {
//        echo ("<h1>File exist $file</h1>");
//        include($file);
//    }
    //include("http://cms.stefan-koelmel.com/cms_base/cms_titleLine.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_titleLine.php");
    
   
   
    if (is_array($GLOBALS[cmsSettings])) $useCache = $GLOBALS[cmsSettings][cache];
    else {
        if (!is_array($_SESSION[cmsSettings])) {
            
            $cmsSettings = cms_settings_get();
            // echo "No cmsSettings = $cmsSettings<br>";
        }
        
        if (is_array($_SESSION[cmsSettings])) {
            $GLOBALS[cmsSettings] = $_SESSION[cmsSettings];
            $useCache = $_SESSION[cmsSettings][cache];
        }
    }
   
    $cacheState = cmsCache_State();
    // echo ("Hier $useCache - $cacheState <br>");
    if (is_null($cacheState)) {
        if ($useCache) cmsCache_enable();
        else cmsCache_disable();
    }
   
    
    $wireFrame = $GLOBALS[cmsSettings][wireframe];
    $wireFrame = 1;
    if ($wireFrame) {
        //echo ("<h1>Wireframe</h1>");
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_wireframe.php");
        $wireframeState = cmsWireframe_state();
//        if (is_null($wireframeState)) {
//            $_SESSION[wireframe] = 1;            
//        }
    }

 

    
    // cmsCache_enable();
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_layout.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_navigation.php");
    
    if (!function_exists("cms_page_getSortList")) {
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_page.php");
    }
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_dynamicPage.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_content.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentType.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentData_show.php");
    //  include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_text.php");
    
    switch ($GLOBALS[cmsSettings][editMode]) {
        case "onPage2" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_siteModul.php");
            break;
        case "siteBar" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_editBox.php");
            break;
    }
    
   
    
    $this->cms_load_DataFiles($cmsName,$cmsVersion,$debug);
    
    
    
    
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentFrame.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_systemFrame.php");
    

    
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_admin.php");
 
    
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/header.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/navigation.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/page.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/content.php");

    // include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/footer.php");

    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_Slider.php");
 
  //  foreach ($_SESSION as $key => $val ) echo ("GLOBAL $key = $val <br>");

    
   
    
    $folder = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentTypes/";

    global $cmsTypes;
    $cmsTypes = array();
    global $cmsName;
    if ($cmsName == "cms") {
        if ($_SESSION[cmsName] != "cms") $cmsName = $_SESSION[cmsName];
        else {
            $cmsName = cms_getCmsName();
        }
    }
    
    //echo ("<h3>CMS-Name '$cmsName'</h3>");

   
    $this->cms_load_contentTypes($cmsName,$cmsVersion,$debug);

     
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/ownPhp.php")) {
        include($_SERVER['DOCUMENT_ROOT']."/ownPhp.php");
    }
}


    function cms_load_DataFiles($cmsName,$cmsVersion,$debug) {
        $detectFile = 1;
         
        if ($detectFile) {
            global $cmsSettings;
            $folder = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/";
            $handle = opendir($folder);
            // echo ("Start<br>");
            
            $specialData = $cmsSettings[specialData];
            if (!is_array($specialData)) $specialData = array();
            while ($file = readdir ($handle)) {
                if($file != "." && $file != "..") {
                    
                    $load = 0;                
                    if (substr($file,0,4) == "cms_") {
                        $checkFile = substr($file,4,strlen($file)-8);
                        // echo ("$file / $checkFile - <br />");
                        switch ($checkFile) {
                            case "style" : $load = 1; break;
                            case "text"  : $load = 1; break;
                            
                            case "image" : $load = 1; $checkFile = "images"; break;
                            
                            case "userData" : $checkFile = "user"; break;
                            case "user" : $load = 1; break;
                            case "image" : $load = 1; break;
                            case "admin" :
                                if ($_SESSION[userLevel]>5) {
                                    $load = 1;
                                }
                        }
                        
                        if ($specialData[$checkFile]) {
                            if ($debug) echo ("Load File ".$file." because is activated in Settings <br>");
                            $load = 1;
                        } else {
                            //echo ("dontLoad $file / $checkFile <br>");
                            // $load = 1;
                        }
                        // echo ("$checkFile <br>");
                    }
                    if ($load) {
                        // echo ("load File $folder $file <br>");
                        include($folder.$file);
                    } else {
                        if ($debug) echo "<b>Not loaded $file</b><br>";
                    } 
                }
            }           
        } else {
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_image.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_user.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_email.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_dates.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_company.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_category.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_location.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_product.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_articles.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/cms_project.php");
        }
        
    }


    function cms_load_contentTypes($cmsName,$cmsVersion,$debug) {
        $folder = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentTypes/";

        $handle = opendir($folder);
        global $cmsTypes;
        $cmsTypes = array();
        
        $ownPhpPath = $_SERVER['DOCUMENT_ROOT']."/cms/cms_contentTypes/";

        if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$cmsName."/")) {
            $ownPhpPath = $_SERVER['DOCUMENT_ROOT']."/".$cmsName."/cms/cms_contentTypes/";
        }
        
        $checkTypes = 1;
        $useTypes = $_SESSION[cmsSettings][useType];
        if (!is_array($useTypes)) $checkTypes = 0;
        $_SESSION[disabledTypes] = array();
        
        
        if ($debug) echo ("OwnContentTypes = $ownPhpPath <br>");
       //  $dontUseTypes = array();
        $dontUseTypes = $this->dontuse_contentTypes();
        //  echo ("<h1>Hier</h1>");
        while ($file = readdir ($handle)) {
            if ($file == ".") continue;
            if ($file == "..") continue;
            if (is_dir($file)) continue;
            
           
            $fileName = $folder.$file;
            $fileTypeList = explode(".",$file);
            $fileType = $fileTypeList[count($fileTypeList)-1];
            if ($fileType != "php") continue;

            $add = 1;

            if (substr($file,0,8) != "cmsType_") continue;
            
           
            $contentTypeFile = substr($file,8);
            $contentTypeName = substr($contentTypeFile,0,strlen($contentTypeFile)-strlen($fileType)-1);
            
            
            
            if ($checkTypes) {
                $use = $useTypes[$contentTypeName];
                // if ($type == "frame") $use = 1;
                if (!$use) {
                    if ($debug) echo ("dont Use $contentTypeName because not enabled <br>");
                    $_SESSION[disabledTypes][$contentTypeName] = 1;
                    // $add = 0;
                }
            }
            //echo ("$contentTypeFile $contentTypeName <br>");
            if ($dontUseTypes[$contentTypeName]) {
                $add = 0;
                // echo ("dont Use contenType FileName = $contentTypeFile $contentTypeName <br>");
            } else {
                // echo ("use $contentTypeName <br>");
            }

           

            if ($add) {
                $cmsTypes[$file] = "base";
                // echo ("n=$file t=$fileType s=$fileSize cms=$GLOBALS[cmsName]<br>");
                require ($folder.$file);

                $jsFile = $fileTypeList[0].".js";
                //if ($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentTypes/".$jsFile)
                if (file_exists($_SERVER['DOCUMENT_ROOT']."/cms_$GLOBALS[cmsVersion]/cms_contentTypes/".$jsFile)) {
                     //echo ("Check jsFile $jsFile<br>");
                     //echo("<script src='/cms/cms_contentTypes/".$jsFile."' type='text/javascript'></script>");
                }

                $ownFile = substr($file,0,strlen($file)-4);
                $ownFile .= "_own.".$fileType;


                $ownPhpFile = $ownPhpPath.$ownFile;
                // echo ("Own php_file $cmsName = $file - $ownFile<br>");
                if ($cmsName == "cms") {
                    // echo ("cmsName = cms !!!!!!!!!!!!!!<br>");
                } else {
                    if (file_exists($ownPhpFile)) {
                        if ($debug) echo ("File $ownFile exist <br>");
                        include($ownPhpFile);
                        $cmsTypes[$file] = "own";
                    }
                }
            }
        }
        $debug = 0;
        $this->cms_load_own_contentTypes($cmsName,$cmsVersion,$ownPhpPath,$debug);        
    }
    
    
    
    function cms_load_own_contentTypes($cmsName,$cmsVersion,$ownPhpPath,$debug) {
        
        if (!file_exists($ownPhpPath)) return 0;
        global $cmsTypes;
        
        $handle = opendir($ownPhpPath);
        
        $checkTypes = 1;
        $useTypes = $_SESSION[cmsSettings][useType];
        if (!is_array($useTypes)) $checkTypes = 0;
        if (!is_array($_SESSION[disabledTypes])) $_SESSION[disabledTypes] = array();
        
        
        if ($debug) echo ("OwnContentTypes = $ownPhpPath <br>");
      
        while ($file = readdir ($handle)) {
            if ($file == ".") continue;
            if ($file == "..") continue;
            if (is_dir($file)) continue;
            
           
            $fileName = $folder.$file;
            $fileTypeList = explode(".",$file);
            $fileType = $fileTypeList[count($fileTypeList)-1];
            if ($fileType != "php") continue;
            
            if (substr($file,0,8) != "cmsType_") continue;
            $add = 1;
            
            $contentTypeFile = substr($file,8);
            $contentTypeName = substr($contentTypeFile,0,strlen($contentTypeFile)-strlen($fileType)-1);
            
            // echo ("own? ".substr($contentTypeName,strlen($contentTypeName)-4)."<br>");
            if (substr($contentTypeName,strlen($contentTypeName)-4) == "_own") continue;
            
            
            if ($debug) echo ("OWN $contentTypeFile $contentTypeName <br />");

            $cmsTypes[$file] = "own";
            
            
            $use = $useTypes[$contentTypeName];
            if (!$use) {
                if ($debug) echo ("dont Use $contentTypeName because not enabled <br>");
                $_SESSION[disabledTypes][$contentTypeName] = 1;
                $add = 0;
            }
            //                // echo ("n=$file t=$fileType s=$fileSize cms=$GLOBALS[cmsName]<br>");
            require ($ownPhpPath.$file);
            //
            $jsFile = $fileTypeList[0].".js";
            //if ($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentTypes/".$jsFile)
            if (file_exists($ownPhpPath.$jsFile)) {
                 if ($debug) echo ("Check jsFile $jsFile<br>");
                 // echo("<script src='/cms/cms_contentTypes/".$jsFile."' type='text/javascript'></script>");
            }

        }
    }

    function dontuse_contentTypes() {
        return array();
    }

}


function cms_class() {
    $root = $_SERVER[DOCUMENT_ROOT];
    $cmsName = $GLOBALS[cmsName];

    if (file_exists($root."/$cmsName")) $root .= "/$cmsName";
    
    if (file_exists($root."/cms/cms_own.php")) {
        include ($root."/cms/cms_own.php");
        $cmsClass = new cms_own();
    }
    else  $cmsClass = new cms_base();
    return $cmsClass;
}
   
    $cmsClass = cms_class();
   
    $cmsClass->cms_start();
 
?>
