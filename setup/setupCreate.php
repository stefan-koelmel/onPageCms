<?php
    $view = $_GET[view];
    
    switch ($view) {
        case "selectType" : 
            $type = $_GET[type];
            
            $typeList = array("simple"=>"Basic","data"=>"Daten","more"=>"Komplett");
            
            foreach ($typeList as $key => $value) {
                $selected = "";
                if ($type == $key) $selected = "selected='1'";                
                echo ("<option $selected value='$key' >$value</option>\n");
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
            
            $root = $_SERVER[DOCUMENT_ROOT]."/";
            
            $myPath = $root.$path;
            // echo ("myPath = $myPath <br>");
            if (file_exists($myPath)) {
                $handle = opendir($myPath);
                // $res.= "suche in Folder $folder <br>";
                while ($file = readdir ($handle)) {
                    if($file != "." && $file != "..") {
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
                                        default :
                                            if (substr($file,0,8) == "cmsEdit-") $add=0;
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
                
            // file&path=$path&file=$fn

            $type = $_GET[type];
            $version = $_GET[version];
            $path = $_GET[path];
            $file = $_GET[file];

            if (!$type) { echo ("no Type defined");  die();}
            if (!$version) { echo ("no Version defined"); die();}
            if (!$path) { echo ("no Path defined"); die();}
            if (!$file) { echo ("no File defined"); die();}


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
            echo ("unkownType $view <br>");
    }
    
    
    function loadFile($fn) {
        $fp = fopen($fn, "r");
        $fs = filesize($fn);
//        $all = 1;
//        // by Blocks
//        if (!$all) {
//            $contents = '';
//            $i = 0;
//            while (!feof($fp)) {
//                $contents .= fread($fp, 8192);
//                $i++;
//                echo ("Block $i <br>");
//            }
//        } else {
           // echo ("$fn $fs <br>");
            $contents = fread($fp,$fs);
            //echo (" read <br>");
        // }
        fclose($fp);
        //echo (" Close <br>");
       // $t = "SELECT * FROM `cR_user`";
        return $contents;
    }
    
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
