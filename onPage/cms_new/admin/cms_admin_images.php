<?php // charset:UTF-8

class cmsAdmin_images_base extends cmsAdmin_editClass_base {
    
    function show($frameWidth) {
        if (!function_exists("cmsImage_get")) {
            echo ("Bildverwaltung ist deaktiviert !<br>");
            return 0;
        }
        
        echo ("<h1>CMS Bild-Verwaltung</h1>");
        
        $view = $_GET[view];
        $this->tableName = "images";
        
        $folder = $_GET[folder];
        if (!$folder) $folder = 'images/';
        
        $imageId = $_GET[imageId];
        $showFile = $_GET[filename];
        if ($showFile) {
            $imageData = cmsImage_get(array("fileName"=>$showFile,"orgPath"=>$folder));
            if (is_array($imageData)) $imageId = $imageData[id];            
        }
        if ($imageId) $view="edit";
        
        
        switch ($view) {
             case "editShow" :
                $this->edit_editShow();
                break;
            
            case "editList" :
                $this->edit_editList();
                break;
            
            case "edit" :
                $this->show_Images($folder,$imageId,$frameWidth);
                break;
            
            default :
                $check = $_GET[check];
                if ($check) {
                    echo ("<h3>Check Folder '$folder' </h3>");
                    $res = $this->checkImageFolder($folder);
                    echo ("&nbsp;<br /><a href='admin_cmsImages.php?folder=$folder' class='cmsLinkButton cmsSecond' >Bilder überprüfen abbrechen</a>");
                    return $res;                
                }
                
                $res = $this->show_ImageList($folder,$frameWidth);

                $res = $this->show_uploadImage($folder,$frameWidth);

                if ($_SESSION[showLevel]>=9) {
                    echo ("&nbsp;<br /><a href='admin_cmsImages.php?folder=$folder&check=1' class='cmsLinkButton' >Bilder überprüfen</a>");
                }
        }
       
    }
    
    
    
    
    function show_Images($folder,$imageId,$frameWidth) {
        if (!$folder) $folder = 'images/';
    

        $pageInfo = $GLOBALS[pageInfo];

        if ($imageId) {
            $saveData = cmsImage_getData_by_Id($imageId);
            
            if (is_array($_POST[saveData])) {
                foreach ($_POST[saveData] as $key => $value) {
                    $saveData[$key] = $value;
                }
            }
        }

        if (!is_array($saveData)) {
            cms_errorBox("Bild mit ID $imageId nicht gefunden");
            return 0;
        }

        
        $imageId = $saveData[id];
        $showFile = $saveData[fileName];
        $action = $_GET[action];

        
        
        
        // DELETE IMAGE ASK
        if ($_POST[deleteData]) {
             echo ("<form method='post' >");
             echo "<h3>Wollen Sie dieses Bild löschen?</h3>";
             echo ("<input type='submit' value='Löschen' name='delImage' class = 'cmsInputButton'> ");
             echo ("<input type='submit' value='Nein' name='cancelImage' class = 'cmsInputButton cmsSecond'> ");
             echo ("</form>\n");
        }
        
        if ($_POST[cancelImage]) {
            cms_infoBox("Löschen vom Bild abgebrochen");
            $goPage = $GLOBALS[pageInfo][pageName]."?folder=$folder&imageId=$imageId";
            reloadPage($goPage,1);
            $show_ask = 0;
        }
        
        if ($_POST[delImage]) {
            $showFile = $saveData[name];
            
            $anz = $this->deleteThumbs_ofFile($saveData[name],$saveData[orgpath]);
            $imageId = $saveData[id];
            // echo ("Image Id $imageId<br>");


            $goPage = $GLOBALS[pageInfo][pageName].".php?folder=".$saveData[orgpath];
            // echo ("GoPage = $goPage <br>");

            //$imageRoot = ""; // cmsImage_rootPath();

            $delFile = $folder.$saveData[fileName];
            // $delFile = "klappeAuf/".$folder.$showFile;
            if (file_exists($delFile)) {
                cmsImage_del_withId($imageId);
                $imageRoot = cmsImage_rootPath(1);
                $res = unlink($imageRoot.$delFile);
                //echo ($imageRoot.$delFile." res = $res<br>");
                cms_infoBox("Bild gelöscht");
                reloadPage($goPage,1);
                $showAsk = 0;
                return "";
            } else {
                // unlink($delFile);
                $fileSize = filesize($delFile);
                echo ("Dateigröße : ".$fileSize." byte<br>");
                // echo ("File not exist $delFile <br>");
                cms_infoBox("Bild '$delFile' nicht gefunden");
                // reloadPage($goPage,1);
                $showAsk = 0;
            }

        }
        
        if ($_POST[delThumbs]) {
            $anz = $this->deleteThumbs_ofFile($saveData[fileName], $saveData[orgpath]);
            if ($anz>0) {
                // cms_infoBox($anz." Thumbnails von $saveData[fileName] gelöscht");
                reloadPage($pageInfo[page]."?folder=$folder&imageId=$imageId",20);
            }
        }
        


        $tableName = "images";
        $editShow = $this->edit_show($tableName,$specialData);

        $goPage = "";
        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "view" : break;
                case "id" : break;

                default :
                    if ($goPage == "") $goPage.= "?";
                    else $goPage .= "&";
                    $goPage .= "$key=$value";
            }
        }
        $goPage = $pageInfo[page].$goPage;

        $reloadPage = 1;
        if ($_POST[editSave]) {
            // Remove Ratio
            unset($saveData[ratio]);
            
            $error = $this->checkError($saveData,$editShow);
            if (count($error)>0) {
                $errorStr = "";
                foreach ($error as $key => $value) {
                    if ($errorStr != "") $errorStr .= "<br />";
                    $errorStr .= $value;
                }
                cms_errorBox($errorStr);
            }
            if (count($error) == 0 AND is_array($saveData)) {
//                // show_array($saveData);
//                // GET QUERY AND SAVEDATAID FORM saveData
                $queryData = $this->query_queryData($saveData,$editShow);
                $query = $queryData[query];
                $saveDataId = $queryData[saveDataId];
                $mode = "edit";
                
//               
//
                if ($mode == "new") {
                    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_images` SET $query  ";
                }
                if ($mode == "edit") {
                    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_images` SET $query WHERE `id` = $saveDataId ";
                }
                //  echo ("$query<br />");
                $result = mysql_query($query);
                if ($result) {
                    $goPage = $pageInfo[page]."?folder=$saveData[orgpath]";
                    if ($mode == "new") cms_infoBox("Bilddaten angelegt");
                    else cms_infoBox("Bilddaten gespeichert");
                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei Bilddaten angelegen";
                    else $outPut = "Fehler bei Bilddaten speichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br />Query = '$query'";
                    cms_errorBox($outPut);
                }
           }
        }
       if ($_POST[cancelSave]) { // abbrechen
           $goPage = $pageInfo[page]."?folder=$saveData[orgpath]";
           echo ("hoPage $goPage <br>");
            if ($mode == "new") $outPut = "Bild anlegen abgebrochen";
            else $outPut = "BildDaten speichern abgebrochen";
            cms_infoBox($outPut);
            reloadPage($goPage,1);
        }





        div_start("cmsImageEdit","width:".$frameWidth."px;");

        $showSize = 200;
        div_start("cmsImageLeft","float:left;width:".($showSize+10)."px;margin-right:10px;");
        
        div_start("cmsImageShow","background-color:#eee;width:".($showSize+0)."px;height:".($showSize+0)."px;border:1px solid #aaa;padding:5px;margin-right:5px;margin-bottom:5px;");

        $showData = array();
        $showData["createSmall"] = 1;
        // $showData[ratio] = 5/1;
        echo(cmsImage_showImage($saveData,$showSize,$showData)."<br />");

        div_end("cmsImageShow");
        
        $showFile = $saveData[name];
        echo ("<h3>Thumbnails</h3>");
        $imageRoot = cmsImage_rootPath();
        // echo ("<h1>$imageRoot</h1>");
        $thumbList = $this->getThumbList($saveData[fileName],$saveData[orgpath]);
        foreach($thumbList as $fileName => $fileList) {

            $i=0;
            foreach ($fileList as $size => $data) {
                $thumb_width  = $data[width];
                $thumb_height = $data[height];
                $thumb_size   = $data[size];
                $i++;
                echo ("".$thumb_width."x".$thumb_height." Pixel Größe: ".$thumb_size." Byte<br>");
            }
        }

        div_end("cmsImageLeft");
        
        div_start("cmsImageData","float:left;width:".($frameWidth-$showSize-40)."px;");

        echo ("<form method='post' enctype='multipart/form-data' >");
        $leftWidth = 150;
        $rightWidth = $frameWidth - $leftWidth - 40 - $showSize;
        $standardHeight = 100;


        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);

        echo ("</form>");
        div_end("cmsImageData");

        div_end("cmsImageEdit","before");

        $editShow = array();
        $editShow[id] = array();

        div_start("cmsImageEdit");

        div_end("cmsImageEdit","before");
}
    
    
    
    
    function show_ImageList($folder) {
        $imageList = $this->imageList_get($folder);
        
        echo ("<h3>Ordner = '$folder'</h3>");

        $imageSize = 100;
        //div_start("imageBox","width:410px;height:150px;overflow-y:auto;");
        div_start("imageBox");
        // krsort($imageList);

        $showFileName = 1;
        $heightAdd = 0;
        if ($showFileName) $heightAdd = $heightAdd + 10;
        
        foreach($imageList as $fileName => $imageData) { // ($i=0;$i<count($imageList);$i++) {


            //$imageData = $imageList[$i];
            $fileName = $imageData[fileName];            
            switch ($imageData[showType]) {
                case "folder" :
                    div_start("cmsImageFolder cmsFrameLink","width:".($imageSize+0)."px;height:".($imageSize+$heightAdd)."px;");
                    $path = $imageData[path];
                    echo ("<a href='$pageInfo[page]?folder=$path' class='hiddenLink' >$imageData[name]</a>");
                    
                    // foreach($pageInfo as $key => $value ) echo ("$key=$value <br>");
                    echo ("<div class='cmsImageFolderName'>$imageData[name]</div>");
                    div_end("cmsImageFolder cmsFrameLink");
                    break;

                case "file" :
                    
                    // echo ($imageId);
                    
                    $imgStr = cmsImage_showImage($imageData,$imageSize);
                    
                    div_start("cmsImageFile cmsFrameLink","width:".($imageSize+0)."px;height:".($imageSize+$heightAdd)."px;");
                    
                    $imageId = $imageData[id];
                    if (intval($imageId)) {
                        echo("<a class='hiddenLink' href='$pageInfo[page]?folder=$folder&imageId=$imageId'>$fileName</a>");
                    } 
                    
                    if ($imageData[type]) {
                        if ($imgStr) echo ($imgStr);
                        
                        echo("$imageData[name]");
                    } else {
                        echo ("Datei <br> $imageData[name]");
                    }

                    div_end("cmsImageFile cmsFrameLink");
                    
                    break;

                default :
                    echo ("unkownType $imageData[showType]");
                    // foreach($imageList[$i] as $key => $value ) echo ("$key=$value ");
            }

        }
        div_end ("imageBox","before");
    }
    
    function imageList_get($folder) {
        
        if (!$folder) $folder = 'images/';
        $fileList = array();
        
        // FileList from Database
        $imageList = cmsImage_getList(array("orgpath"=>$folder),"fileName");

        ksort($imageList);
        $notFiles = array();
        $dbFiles = array();
        foreach ($imageList as $fileName => $imgData) {
            $found = $imgData[found];
            if ($found) echo ("Doppelte Files $fileName = $imgData $found <br>");
            if (!file_exists($folder.$fileName)) {
                // echo ("File not exist on Server $folder.$fileName<br>");
                $notFile[$fileName] = $imgData;
            } else {
                $dbFiles[$fileName] = $imgData;
            }
        }

        // return $fileList;
        $folders = array();
        $files   = array();
        $bigNames = array();

        if ($folder != "images/") {
            // echo ("Aktuell = $folder <br>");
            $backFolderList = explode("/",$folder);
            $backFolder = "";
            for ($i=0;$i<count($backFolderList)-2;$i++) {
                //echo ("Folder $i $backFolderList[$i] <br>");
                $backFolder .= $backFolderList[$i]."/";
            }
            $folders[".."] = array("name"=>"..","showType"=>"folder","path"=>$backFolder);
        }
        $imageRoot = cmsImage_rootPath();
       //  echo ("<h1>$imageRoot</h1>");

        $handle = opendir($folder);
        while ($file = readdir ($handle)) {
            if($file != "." && $file != "..") {
                if(is_dir($folder."/".$file)) {
                    if ($file != "thumbs") {
                        $folders[$file] = array("name"=>"$file","showType"=>"folder","path"=>$folder.$file."/");                    
                    }

                } else {
                    $fileName = $folder.$file;
                    $fileType = filetype($fileName);
                    $fileSize = filesize($fileName);
                    $fileMd5  = md5_file($fileName);
                    $file_name = "";
                    $fileData = array("fileName"=>$file,"type"=>$fileType,"orgpath"=>$folder,"md5"=>$fileMd5);
                    $imageType = $this->fileIsImage($file);
                    // echo ("$imageType von $file <br>");
                    if ($imageType) {
                        $fileData["imageType"] = $imageType;

                        $imageInfo = getimagesize($fileName);
                        if (is_array($imageInfo)) {
                            $fileData[width] = $imageInfo[0];
                            $fileData[height] = $imageInfo[1];
                            //foreach ($imageInfo as $key => $value) echo("$key = $value <br>");
                        }
                    }
                    $files[$file] = $fileData;                
                 }
            }
        }
        closedir($handle);
        $newFiles = array();
        foreach ($files as $fileName => $fileData) {

            $isThumb = 0;
            list($not,$width,$orgName) = explode("_",$fileName);
            if (intval($width) AND $not == "" AND $orgName) {
                // echo ("is Thumb ?? '$not' $width $orgName <br>");
                $isThumb = 1;
            }

            if (is_array($dbFiles[$fileName])) {

                if ($isThumb) {
                    $imageId = $dbFiles[$fileName][id];
                    cmsImage_del_withId($imageId);
                    echo "delete ThumbFile $folder $fileName and remove from DataBase $imageId<br>";
                    unlink($folder.$fileName);
                } else {
                    // echo ("File $fileName is in db <br>");
                    if ($dbFiles[$fileName][md5] != $fileData[md5]) {
                        echo ("Change in File $fileName :");
                        echo "--> File has diffrent Md5 ".$dbFiles[$fileName][md5]." != $fileData[md5] <br>";

                        $this->deleteThumbs_ofFile($fileName,$folder);
                        $imageData = array();
                        $imageData["md5"]= $fileData[md5];
                        $imageData["width"]=$fileData[width];
                        $imageData["height"]=$fileData[height];
                        $imageData["id"]=$dbFiles[$fileName][id];
                        cmsImage_saveImage($imageData);
                    }
                }

            } else {
                if ($isThumb) {
                    echo "delete ThumbFile $folder $fileName <br>";
                     unlink($folder.$fileName);
                } else {
                    //echo ("FILE $fileName is NOT in DATABASE<br>");
                    $newFiles[$fileName] = $fileData;
                }
            }
        }

        $fileList = array();
        foreach($folders as $key => $folder ) {
            $fileList[$key] = $folder;
            //$fileList[$key][$imageType] = "Folder";
        }

        if (is_array($newFiles) AND count($newFiles)) {
            foreach ($newFiles as $fileName => $fileData) {
                echo "<h3>New File $fileName ";
                // show_array($fileData);
                $id = cmsImage_addImage($fileData);
                if (is_int($id)) {
                    echo ("- Image added -> new Id = $id </h3>");
                    $fileData[id] = $id;
                    $fileData[showType] = "file";
                    $fileList[$fileName] = $fileData;
                } else {
                    echo ("- error!!!! $id </h3");
                }
            }
        }

        if (is_array($notFiles) AND count($notFiles)) {
            foreach ($notFile as $fileName => $imgData) {
                $id = $imgData[id];
                echo ("Delete Image from Database with id = $id<br>");
                cmsImage_del_withId($id);
            }
        }


        foreach ($dbFiles as $fileName => $fileData) {
            $fileList[$fileName] = $fileData;
            $fileList[$fileName][showType] = "file";
        }

        // ksort($fileList);


        return $fileList;
    }
    
    
    function show_uploadImage($folder,$frameWidth) {
//        $res = cmsModul_imageUpload($folder,"cmsImages");
//        
//        if (is_array($res)) {
//            $out = $res[out];
//            echo ("$out <br>");
//            return 0;
//        }
//        
        
        
        
        
        
        
        $showUpload = 1;
        $fn = "";
        if ($_POST[doUpload]) {
            foreach($_POST as $key => $value ) {
                echo ("Upload $key = $value <br>");
            }
            $fn = $_POST[fileName2];
            $folder="images/";
            echo("Upload Result=".imageUpload($folder)."<br>");
            foreach($_FILES[fileName2] as $key => $value) {
                 echo ("File Data => $key = $value <br>");
            }
        }
        
        
        if ($showUpload) {
            div_start("imageUpload","width:".($width-6)."px;background-color:#eee;border:1px solid #555;padding:2px");
            echo ("<h1>Neues Bild hochladen</h1>");
            echo ("Zielordner: '$folder' <br />");
            echo ("&nbsp;<br />");
            
            echo("<form method='post' enctype='multipart/form-data'>");



            echo ("<div class='fileinputs'>");
            echo ("<input name='fileName' class='_file _hidden' type='file'>");
            echo ("<div class='fakefile' onChange='submit'><input><img src='cms/images/image.gif'></div></div>");
                                // http://www.quirksmode.org/dom/inputfile.html
            // echo ("<input type='text' name='fileCode' value='image_$code'>");
            echo ("<input type='submit' class='cmsInputButton reportImageCancel' name='cancelUpload' value='abbrechen'> ");
            echo ("<input type='submit' class='cmsInputButton cmsSecond' name='doUpload' value='hochladen'> ");

            echo ("<br>");
            echo ("Bild:<img src='cms/images/image.gif'>");
            // div_end("reportImageRight",1);
            echo("</form>");
            div_end("imageUpload","before");
        }


    }
    
    
    
    function imageUpload($folder) {
        echo ("Bild Upload!<br>");
        show_array($_FILES);

        foreach($_FILES[fileName] as $key => $value) {
           echo ("$key = $value <br>");
        }

        $size = $_FILES[fileName][size];
        $name = $_FILES[fileName][name];
        $temp = $_FILES[fileName][tmp_name];


        echo ("Datei = $name <br>");
        echo ("Folder = $folder<br>");
        move_uploaded_file($temp, $folder."$name");
        return $name;
        // echo ("<img src='".$folder.$name."' ><br>");
    }


    function fileIsImage($file) {
         $fileEnd = end(explode(".", $file));
         $fileEnd = strtolower($fileEnd);
         return cmsImage_ImageType($fileEnd);   
    }





    function deleteThumbs_ofFile($fileName,$folder) {
        echo ("deleteThumbs_ofFile($fileName,$folder)<br>");
        $anz = 0;
        $thumbList = $this->getThumbList($fileName,$folder);
        foreach ($thumbList as $key => $fileData) {
            foreach ($fileData as $size => $data) {
                $thumbName = $data[thumbName];
                $thumbFile = $folder."thumbs/".$thumbName;
                //echo ("ThumbName = ".$folder."thumbs/".$thumbName."<br>");
                if (file_exists($thumbFile)) {
                    $anz++;
                    // echo ("file exist $thumbFile <br>");
                    if (unlink($thumbFile)) $anz++;
                }


                // show_array($data);
            }
        }

        if ($anz>0) cms_infoBox("$anz Thumbnails gelöscht");
        return $anz;
    }

    function getThumbList($showFile,$folder) {
        $imageRoot = cmsImage_rootPath();
        $searchFolder = $folder."thumbs/";
        //echo ("<h2>$searchFolder</h2>");
        // echo(" getThumbList($showFile,$searchFolder)<br>");
        $folder = $folder."thumbs/";

        $res = array();
        $handle = opendir($searchFolder);
        while ($file = readdir ($handle)) {
            if($file != "." && $file != "..") {
            }

            if(is_dir($folder."/".$file)) {

            } else {
                $ofSet = strpos($file,$showFile);
               // echo ("$file $showFile $ofSet <br>");
                if ($ofSet) {
                    $size = filesize($folder.$file);
                    list($not,$width,$height,$orgName) = explode("_",$file);
                    $res[$orgName][$width."x".$height] = array("width"=>$width,"height"=>$height,"size"=>$size,"thumbName"=>$file);

                }

            }
        }
        closedir($handle);
        foreach ($res as $orgFile => $data) {


            ksort($res[$orgFile]);
        }
        return $res;
    }
    
    
    function checkImageFolder($folder) {
        echo ("<h3>Check Image Folder '$folder'</h3>");
        
        if ($folder[strlen($folder)-1] != "/") {
            echo ("LastChar ".$folder[strlen($folder)-1]."<br>");
            $folder.= "/"; 
            if (!file_exists($folder)) {
                echo ("not exist $folder <br>");
                return 0;
            }
        }
        
        
        
        if ($folder != "images/") {
            // echo ("Aktuell = $folder <br>");
            $backFolderList = explode("/",$folder);
            $backFolder = "";
            for ($i=0;$i<count($backFolderList)-2;$i++) {
                //echo ("Folder $i $backFolderList[$i] <br>");
                $backFolder .= $backFolderList[$i]."/";
            }
            $folders[".."] = array("name"=>"..","showType"=>"folder","path"=>$backFolder);
        }
        $imageRoot = cmsImage_rootPath();
       
        $handle = opendir($folder);
        while ($file = readdir ($handle)) {
            if($file != "." && $file != "..") {
                if(is_dir($folder."/".$file)) {
                    if ($file != "thumbs") {
                        $folders[$file] = array("name"=>"$file","showType"=>"folder","path"=>$folder.$file."/");                    
                    }

                } else {
                    $fileName = $folder.$file;
                    $fileType = filetype($fileName);
                    $fileSize = filesize($fileName);
                    $fileMd5  = md5_file($fileName);
                    $file_name = "";
                    $fileData = array("fileName"=>$file,"type"=>$fileType,"orgPath"=>$folder,"md5"=>$fileMd5);
                    $imageType = $this->fileIsImage($file);
                    // echo ("$imageType von $file <br>");
                    if ($imageType) {
                        $fileData["imageType"] = $imageType;

                        $imageInfo = getimagesize($fileName);
                        if (is_array($imageInfo)) {
                            $fileData[width] = $imageInfo[0];
                            $fileData[height] = $imageInfo[1];
                            //foreach ($imageInfo as $key => $value) echo("$key = $value <br>");
                        }
                    }
                    $files[$file] = $fileData;                
                 }
            }
        }
        closedir($handle);
        
        ksort($folders);
        
        echo ("<h4>Ordner</h4>");
        foreach ($folders as $folderName => $folderData) {
            echo ("Folder '$folderName' $folderData[path] <a href='admin_cmsImages.php?check=1&folder=$folderData[path]'>check Folder </a><br>");
        }
        
        
        echo ("Image Root = '$imageRoot' ".$imageRoot.$folder."<br>");
        
        $imageList = cmsImage_getList(array("orgpath"=>$folder));
        //show_array($imageList);
        foreach ($imageList as $key => $imageData) {
            $imageId = $imageData[id];
            $imageFileName = $imageData[fileName];
            
            $newFileName = cmsImage_checkFileName($imageFileName,0);
            
            $delDatabaseId = 0;
            $exist = file_exists($folder.$imageFileName);
            if (!$exist) {
                
                $fileSpilt = explode("_",$imageFileName);
                if ($fileSpilt[0] == "") {
                    if (intval($fileSpilt[1]) AND intval($fileSpilt[2])) {
                        echo ("Doppelter Größe im File $imageFileName <br>");
                    } else {
                        if (intval($fileSpilt[1])) {
                            echo ("Einfache Größe im $key File $imageFileName <br>");
                            $delDatabaseId = $imageId;
                        }
                    }
                } else {
                    $imageMd5 = $imageData[md5];
                    echo "Image $imageId $imageFileName $imageMd5 not exist <br>";
                    $found = 0;
                    if (is_array($files) AND count ($files)) {
                        foreach ($files as $fileName => $fileData) {
                            $fileMd5 = $fileData[md5];
                            if ($fileMd5 == $imageMd5) {
                                $found = 1;
                                $convFileName = utf8_encode($fileName);
                                if ($convFileName == $imageFileName) {
                                    
                                    echo (" <b>--> ConvertedFileName is correkt </b><br>");
                                    if ($newFileName != $imageFileName) {
                                        echo ("rename(".$folder.$fileName.", ".$folder.$newFileName.")<br>");
                                        $renameRes = rename($folder.$fileName, $folder.$newFileName);
                                        if ($renameRes) {
                                            $query = "UPDATE `klappeAuf_cms_images` SET `fileName`='$newFileName' WHERE `id`=$imageId";
                                            $result = mysql_query($query);
                                            if (!$result) echo ("error in Query<br>$query<br>");
                                        } else {
                                            echo ("Error in Rename <br>rename(".$folder.$imageFileName.", ".$folder.$newFileName.")<br>");
                                        }
                                    } else {
                                        echo ("rename(".$folder.$fileName.", ".$folder.$imageFileName.")<br>");                                        
                                    }
                                    //$renameRes = rename($folder.$fileName, $folder.$imageFileName);
                                    // if ($renameRes) echo ("<b> File wurde umbenannt</b><br>");
                                    // else echo ("<b> File wurde NICHT umbenannt</b><br>");
                                } else { 
                                    echo (" --> $fileName ".utf8_encode($fileName)." Found with md5 <br>");                                    
                                }
                            }
                        }
                    }
                    
                    if ($found == 0) {
                        $deleteIdList[$imageId] = $imageFileName;
                    }
                    
                }
                
            } else {
                if ($files[$imageFileName]) {
                    unset($files[$imageFileName]);
                }
                if ($newFileName != $imageFileName) {
                    echo ("Rename $imageFileName -> $newFileName <br>");
                    
                    $renameRes = rename($folder.$imageFileName, $folder.$newFileName);
                    if ($renameRes) {
                        $query = "UPDATE `klappeAuf_cms_images` SET `fileName`='$newFileName' WHERE `id`=$imageId";
                        $result = mysql_query($query);
                        if (!$result) echo ("error in Query<br>$query<br>");
                    } else {
                        echo ("Error in Rename <br>rename(".$folder.$imageFileName.", ".$folder.$newFileName.")<br>");
                    }
                                
                }
                // echo ("Image $imageId $imageFileName --> $exist<br>");
            }
            
            if ($delDatabaseId > 0) {
                $query = "DELETE FROM `klappeAuf_cms_images` WHERE `id` = $delDatabaseId";
                $result = mysql_query($query);
                if (!$result) {
                    echo ("Error in query <br>$query<br> ");                    
                }
            }
            
            
            
        }
        
        
        if (is_array($deleteIdList) AND count($deleteIdList)) {
            echo ("<h4>nicht gefundene Datensätze aus Datenbank anz=".count($deleteIdList)."</h4>");
            $deleteDatabase = $_GET[deleteDatabase];
            foreach ($deleteIdList as $imageId => $imageFileName) {
                if ($deleteDatabase) echo ("<b> DO DELETE </b>");
                echo ("delete from Database $imageId $imageFileName <br> ");
                if ($deleteDatabase) {
                    $query = "DELETE FROM `klappeAuf_cms_images` WHERE `id` = $imageId";
                    $result = mysql_query($query);
                    if (!$result) {
                        echo ("Error in query <br>$query<br> ");                    
                    }
                }
            }
            if (!$deleteDatabase) {
                $goLink = "admin_cmsImages.php?check=1&folder=$folder&deleteDatabase=1";
                echo ("<a href='$goLink'>Diese Datensätze aus Datenbank löschen </a><br />");
                
            } else {
                echo ("<h2>Datensätze gelöscht</h2>");
                reloadPage("admin_cmsImages.php?check=1&folder=$folder",3);
            }
            

        }
        
        
        if (is_array($files) AND count ($files)) {
            $deleteFile = $_GET[deleteFile];
            echo ("<h4>nicht gefundene Files vom Server anz=".count($files)."</h4>");
        
            foreach ($files as $serverFileName => $serverFileData ) {
                if ($deleteFile) echo ("<b>DO FILE DELETE </b>");
                echo ("$serverFileName <br>");
                if ($deleteFile) {
                    unlink($folder.$serverFileName);
                }
            }
            
             if (!$deleteFile) {
                $goLink = "admin_cmsImages.php?check=1&folder=$folder&deleteFile=1";
                echo ("<a href='$goLink'>Diese Dateien vom Server löschen </a><br />");
                
            } else {
                echo ("<h2>Dateien gelöscht</h2>");
                reloadPage("admin_cmsImages.php?check=1&folder=$folder",3);
            }
            
        }
                
        
        
    }
    
    
    
    
}


function imageUpload($folder) {
    echo ("<h1>OLD imageUpload</h1>");
    echo ("Bild Upload!<br>");
    show_array($_FILES);

    foreach($_FILES[fileName] as $key => $value) {
       echo ("$key = $value <br>");
    }

    $size = $_FILES[fileName][size];
    $name = $_FILES[fileName][name];
    $temp = $_FILES[fileName][tmp_name];


    echo ("Datei = $name <br>");
    echo ("Folder = $folder<br>");
    move_uploaded_file($temp, $folder."$name");
    return $name;
    // echo ("<img src='".$folder.$name."' ><br>");
}


function cms_FileIsImage($file) {
    echo ("<h1>OLD cms_FileIsImage</h1>");
     $fileEnd = end(explode(".", $file));
     $fileEnd = strtolower($fileEnd);
     return cmsImage_ImageType($fileEnd);
//     switch ($fileEnd) {
//         case "jpg" : return ("JPG");
//         case "jpeg" : return ("JPG");
//         case "png" : return( "PNG");
//         case "gif" : return("GIF");
//             default :
//                 echo ("unkown FileFormat $fileEnd<br>");
//     }

}


    function cms_imageClass($ownAdminPath) {
        
        $ownPhpFile = $ownAdminPath."/cms_admin_images_own.php";
        if (file_exists($ownPhpFile)) {
            require_once($ownPhpFile);
            $class = new cmsAdmin_images();
           
        } else {
            $class = new cmsAdmin_images_base();
           
        }
        return $class;        
    }
    
    function cms_showImages($folder,$frameWidth) {
        $imageClass = cms_imageClass();
        return $imageClass->show_Images($folder,$frameWidth);
    }
    
    function cms_ImageList($folder) {
        $imageClass = cms_imageClass();
        $imageClass->show_ImageList($folder);
    }
    
    function cms_admin_images($frameWidth,$ownAdminPath) {
        $imageClass = cms_imageClass($ownAdminPath);
        $imageClass-> show($frameWidth);
    }
    
    
    
    
    ?>
<script language="Javascript" type="text/javascript">
    var W3CDOM = (document.createElement && document.getElementsByTagName);

function initFileUploads() {
	if (!W3CDOM) return;
	var fakeFileUpload = document.createElement('div');
	fakeFileUpload.className = 'fakefile';
	fakeFileUpload.appendChild(document.createElement('input'));
	var image = document.createElement('img');
	image.src='pix/button_select.gif';
	fakeFileUpload.appendChild(image);
	var x = document.getElementsByTagName('input');
	for (var i=0;i<x.length;i++) {
		if (x[i].type != 'file') continue;
		if (x[i].parentNode.className != 'fileinputs') continue;
		x[i].className = 'file hidden';
		var clone = fakeFileUpload.cloneNode(true);
		x[i].parentNode.appendChild(clone);
		x[i].relatedElement = clone.getElementsByTagName('input')[0];
		x[i].onchange = x[i].onmouseout = function () {
			this.relatedElement.value = this.value;
		}
	}
}
</script>

