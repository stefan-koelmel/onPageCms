<?php
    $view = $_GET[view];
    
    switch ($view) {
        case "selectType" : 
            $type = $_GET[type];
            
            $typeList = array();
            $typeList["basic"] = "onPage Basic";
            $typeList["data"] = "onPage Data";
            $typeList["dataPlus"] = "onPage Data+";
            $typeList["basket"]="onPage Basket";
            $typeList["unlimited"] = "onPage unlimited";
            $typeList["single"] = "onWire single";
            $typeList["u5"] = "onWire u5";
            $typeList["u10"] = "onWire u5";
            
            
            foreach ($typeList as $key => $value) {
                $selected = "";
                if ($type == $key) $selected = "selected='1'";                
                echo ("<option $selected value='$key' >$value</option>\n");
            }
            break;
            
        case "selectVersion" :
            $type = $_GET[version];
            
            $root = $_SERVER[DOCUMENT_ROOT]."/";
            
            // $myPath = $root.$path;
            // echo ("myPath = $myPath <br>");
            if (file_exists($root)) {
                $handle = opendir($root);
                // $res.= "suche in Folder $folder <br>";
                while ($file = readdir ($handle)) {
                    if($file != "." && $file != ".." AND !$dontUse[$file]) {
                        if(!is_dir($root."/".$file)) continue;
                        if (substr($file,0,4) != "cms_") continue;
                        
                        $version = substr($file,4);
                        $checkFile = $root."cms_".$version."/cms.php";
                        if (!file_exists($checkFile)) {
                            //echo ("No CheckFile $checkFile <br>");
                            continue;
                        }
                        
                        
                        $checkFile = $root."cms_".$version."/version.info";
                        if (!file_exists($checkFile)) {
                            // echo ("No InfoFile $checkFile <br>");
                            continue;
                        }
                        
                        echo ("CMS-Version:$version<br />");
                        
                        $info = loadFile($checkFile);
                        echo ("CMS-Info:$info<br />");                  
                    }
                }
            }
            break;
            
            
            
            
            
        case "pathList" :
            $type = $_GET[type];
            $version = $_GET[version];
            
            if (!$type) { echo ("no Type defined");  die();}
            if (!$version) { echo ("no Version defined"); die();}
            $pathList = array();
            
            $root = "";
            $pathList[] = $root."cms_$version/";
            $pathList[] = $root."cms_$version/admin/";
            $pathList[] = $root."cms_$version/cms_contentTypes/";
            $pathList[] = $root."cms_$version/cmsImages/";
            $pathList[] = $root."cms_$version/data/";
            $pathList[] = $root."cms_$version/getData/";
            $pathList[] = $root."cms_$version/images/";
            $pathList[] = $root."cms_$version/styles/";
            
            
            // Includes
            if (!$_GET[onlyCMS]) {
                $pathList[] = $root."includes/";
                $pathList[] = $root."includes/fancybox/";

                $pathList[] = $root."cms/";
                $pathList[] = $root."cms/admin/";
                $pathList[] = $root."cms/cms_contentTypes/";

                $pathList[] = $root."images/";
                $pathList[] = $root."cache/";
                $pathList[] = $root."style/";
                $pathList[] = $root."wireframe/";
            }
            
            for ($i=0;$i<count($pathList);$i++) {
                $path = $pathList[$i];
                echo ("$path<br />");
            }            
            break;
            
            
        case "fileList" :
            $type = $_GET[type];
            $version = $_GET[version];
            $path = $_GET[path];
            
            if (!$type) { echo ("no Type defined");  die();}
            if (!$version) { echo ("no Version defined"); die();}
            if (!$path) { echo ("no Path defined"); die();}
            $pathList = array();
            
            $dontUseGroup = array();
            switch($type) {
                case "basic" :
                    $dontUseGroup = array("location","company","product","project","dates","article","faq","wireframe","basket");
                    break;
                case "data" :
                    $dontUseGroup = array("dates","wireframe","basket");
                    break;
                case "dataPlus" :
                    $dontUseGroup = array("wireframe","basket");
                    break;
                
                 case "basket" :
                    $dontUseGroup = array("wireframe");
                    break;
                
                case "unlimited" :
                    $dontUseGroup = array("wireframe","basket");
                    break;
                
                case "single" :
                    $dontUseGroup = array("basket");
                    break;
                
                case "u5" :
                    $dontUseGroup = array();
                    break;
                case "u10" :
                    $dontUseGroup = array();
                    break;
                
                default : 
                    echo ("unkown Type $type <br />");
                    die();
            }
            $dontUse = array();
            for ($i=0;$i<count($dontUseGroup);$i++) {
                $groupName = $dontUseGroup[$i];
                $groupFiles = setupDontUseFiles($groupName);
                if (is_array($groupFiles)) {
                    foreach ($groupFiles as $fn => $use) {
                        $dontUse[$fn] = $use;
                    }
                }
                    
                
            }
                    
                    

            
            $root = $_SERVER[DOCUMENT_ROOT]."/";
            
            $myPath = $root.$path;
            // echo ("myPath = $myPath <br>");
            if (file_exists($myPath)) {
                $handle = opendir($myPath);
                // $res.= "suche in Folder $folder <br>";
                while ($file = readdir ($handle)) {
                    if($file != "." && $file != ".." AND !$dontUse[$file]) {
                        if(is_dir($myPath."/".$file)) {
                            // echo ("File is dir $file <br>");
                        } else {
                            $add = 1;
                            switch ($path) {
                                case "cms_base" :
                                    switch ($file) {
                                        case "cms_connect.php" : $add = 0; break;
                                        
                                    }
                                    break;
                                case "cms_base/styles/" :
                                    switch ($file) {
                                        case "cmsEdit-colorize.css" ; break;
                                    
                                        default :
                                            if (substr($file,0,8) == "cmsEdit-") $add = 0;                                                                                                                                           
                                    }
                                    break;

                            }
                            if ($add) {
                                $fileName = $myPath.$file;
                                // $fileType = filetype($fileName);
                                $fileSize = filesize($fileName);
                                $fileMd5  = md5_file($fileName);

                                echo ("$file|$fileSize|$fileMd5<br />");
                            }
                        }
                    }
                }
                closedir($handle);
            } else {
                echo ("Folder $myPath not exist <br>");
            }
            break;
       
        case "file" :
            $type = $_GET[type];
            $version = $_GET[version];
            $path = $_GET[path];
            $file = $_GET[file];

            if (!$type) { echo ("0 - no Type defined");  die();}
            if (!$version) { echo ("0 - no Version defined"); die();}
            if (!$path) { echo ("0 - no Path defined"); die();}
            if (!$file) { echo ("0 - no File defined"); die();}


            $root = $_SERVER[DOCUMENT_ROOT]."/";

            $myFile = $root.$path.$file;
            // echo ("myPath = $myFile");
            if (file_exists($myFile)) {
                $fileContent = loadFile($myFile);
                echo ($fileContent);
                // echo (" - 1 - ".  strlen($fileContent));
            } else {
                echo ("0");
                // echo (" - 0 NOT EXIST");
            }
            // echo ("<br />");
            break;
            
            
        default :
            echo ("0 - unkownType $view <br>");
    }
    
    
    function loadFile($fn) {
        $fp = fopen($fn, "r");
        $fs = filesize($fn);
        $contents = fread($fp,$fs);
        fclose($fp);
        return $contents;
    }
    
    function setupDontUseFiles($groupName) {
        $dontUse = array();
    
        switch ($groupName) {
            case "faq" :
                $dontUse["cmsType_faq.js"] = 1;
                $dontUse["cmsType_faq.php"] = 1;
                break;

            case "basket" :
                $dontUse["cmsType_basket.php"] = 1;
                $dontUse["cms_admin_order.php"] = 1;
                break;
            
            case "location" :
                $dontUse["cmsType_location.php"] = 1;
                $dontUse["cmsType_location.js"] = 1;
                $dontUse["cmsType_locationList.php"] = 1;
                $dontUse["cms_admin_location.php"] = 1;
                $dontUse["cms_admin_location.js"] = 1;
                break;
            case "product" :
                $dontUse["cmsType_product.php"] = 1;
                $dontUse["cmsType_product.js"] = 1;
                $dontUse["cms_admin_product.php"] = 1;
                $dontUse["cms_admin_product.js"] = 1;
                break;
            case "project" :
                $dontUse["cmsType_project.php"] = 1;
                $dontUse["cmsType_project.js"] = 1;
                $dontUse["cms_admin_project.php"] = 1;
                $dontUse["cms_admin_project.php"] = 1;
                $dontUse["cms_admin_project.js"] = 1;
                break;

            case "company" :
                $dontUse["cmsType_company.php"] = 1;
                $dontUse["cmsType_company.js"] = 1;
                $dontUse["cms_admin_company.php"] = 1;
                $dontUse["cms_admin_company.js"] = 1;
                break;

            case "dates" :
                $dontUse["cmsType_date.php"] = 1;
                $dontUse["cmsType_dateList.php"] = 1;
                $dontUse["cmsType_dateList.js"] = 1;
                $dontUse["cms_admin_dates.php"] = 1;
                $dontUse["cms_admin_dates.js"] = 1;
                break;

            case "article" :
                $dontUse["cmsType_article.php"] = 1;
                $dontUse["cmsType_articlesList.php"] = 1;
                $dontUse["cms_admin_articles.php"] = 1;
                $dontUse["cms_admin_articles.js"] = 1;
                break;

            case "wireframe" :
                $dontUse["cmsType_axure.php"] = 1;
                $dontUse["cmsType_axure.js"] = 1;
                $dontUse["cmsType_wireframe.php"] = 1;
                break;

            default:
                echo ("unkown $groupName in setupDontUseFiles<br>");

        }
        return $dontUse;
    }

?>
