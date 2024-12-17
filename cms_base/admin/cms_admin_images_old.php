<?php // charset:UTF-8
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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


function cms_FileIsImage($file) {
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




//function cms_showImage($imageData,$imageSize,$showData=array()) {
//    $path = $imageData[orgpath];
//    $fileName = $imageData[fileName];
//
//    $createSmall = 1;
//    if (is_int($showData[createSmall])) $createSmall = $showData[createSmall];
//
//
//    $md5 = md5_file($path.$fileName);
//    //$getData = cmsImage_getData_by_md5($md5);
//
//    $getData = cmsImage_get(array("md5"=>$md5,"orgpath"=>$path));
//    //echo($getData);
////    if (is_array($newData)) {
////        show_array($newData);
////    }
//   // echo ("GET DATA = $getData <br>");
//    if (!is_array($getData)) {
//        show_array($imageData);
//        die();
//        switch ($getData) {
//
//            case 0 :
//                $imageId = cmsImage_addImage($imageData);
//                if ($imageId > 0) {
//                    $getData = cmsImage_get(array("id"=>$imageId,"orgpath"=>$path)); //(cmsImage_getData_by_md5($md5);
//                } else {
//                    cms_infoBox("Bild nicht in Datenbank - $imageId");
//                    return "";
//                }
//                break;
//            case "error" :
//                cms_errorBox("Fehler in Query $getData");
//                break;
//
//            default :
//                echo ("getData = $getData <br> ");
//        }
//    }
//
//    $imgWidth = $getData[width];
//    $imgHeight = $getData[height];
//    $showWidth = $imgWidth;
//    $showHeight = $imgHeight;
//    if ($imgHeight > $imageSize) {
//        $showWidth = intval($showWidth * $imageSize / $showHeight);
//        $showHeight = $imageSize;
//    }
//    if ($showWidth > $imageSize) {
//        $showHeight = intval($showHeight * $imageSize / $showWidth);
//        $showWidth = $imageSize;
//    }
//
//    if ($imgWidth != $showWidth OR $imgHeight != $showHeight ) {
//        //echo ("Diffrent width / height<br>");
//        //echo ("org = $imgWidth x $imgHeight <br>");
//        //echo ("org = $showWidth x $showHeight <br>");
//        if ($createSmall ) {
//            //echo ("Create Thumbnails <br>");
//            $smallExist = cmsImage_ImageSmallExist($getData,$showWidth,$showHeight,$imgWidth,$imgHeight);
//            if ($smallExist) $fileName = $smallExist;
//        } else {
//            // echo ("dont Create Thumbnails");
//        }
//       //  echo ("Small Exist $smallExist <br>");
//    }
//
//    $absVer = intval(($imageSize - $showHeight) / 2);
//    $absHor  = intval(($imageSize - $showWidth) / 2);
//
//    $imgStr = "<img src='".$path.$fileName."' style='padding-top:".$absVer."px;padding-left:".$absHor."px;' width='".$showWidth."px' height='".$showHeight."px' >";
//    return $imgStr;
//}


function cms_ImageList($folder) {
    if (!$folder) $folder = 'images/';
    $fileList = array();

    echo ("<h3>Ordner = '$folder'</h3>");
    $imageList = cmsImage_getList(array("orgpath"=>$folder));


    $notFiles = array();
    $dbFiles = array();
    foreach ($imageList as $name => $imgData) {
        // echo ("File $name = $imgData <br>");
        $fileName = $imgData[fileName];
        if (!file_exists($folder.$fileName)) {
            // echo ("File not exist on Server $folder.$fileName<br>");
            $notFile[$fileName] = $imgData;
        } else {
            $dbFiles[$fileName] = $imgData;
        }
    }


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
    echo ("<h1>$imageRoot</h1>");

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
                $imageType = cms_FileIsImage($file);
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

                    deleteThumbs_ofFile($fileName,$folder);
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
                echo ("- error!! $id </h3");
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


    ksort($fileList);


    //for ($i=0;$i<count($folders);$i++) $fileList[] = $folders[$i];
    //foreach($files as $key => $file ) {
    //        $fileList[$key] = $file;
    // }
    // for ($i=0;$i<count($files);$i++) $fileList[] = $files[$i];

    return $fileList;
}

function deleteThumbs_ofFile($fileName,$folder) {
    // echo ("deleteThumbs_ofFile($fileName,$folder)<br>");
    $anz = 0;
    $thumbList = getThumbList($fileName,$folder);
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
    // echo(" getThumbList($showFile,$folder)<br>");
    $folder = $folder."thumbs/";

    $res = array();
    $handle = opendir($folder);
    while ($file = readdir ($handle)) {
        if($file != "." && $file != "..") {
        }

        if(is_dir($folder."/".$file)) {

        } else {
            $ofSet = strpos($file,$showFile);
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


function cms_showImages($folder,$frameWidth) {

    if (!$folder) $folder = 'images/';
    

    $pageInfo = $GLOBALS[pageInfo];
    
    $showFile = $_GET[filename];
    $showId   = $_GET[imageId];
    $showList = 1;
    if ($showFile) {
        $imageData = cmsImage_get(array("fileName"=>$showFile,"orgPath"=>$folder));
        if (is_array($imageData)) {
            // show_array($imageData);
        } else {
            echo "Not found Data for Image $showFile <br>";
        }
    }
    if ($showId) {
        $imageData = cmsImage_getData_by_Id($showId);
    }


    //if ($showFile and is_array($imageList[$showFile])) {
    //    $imageData = $imageList[$showFile];
    if (is_array($imageData)) {
        $imageId = $imageData[id];
        $showFile = $imageData[fileName];
        $action = $_GET[action];
       
        switch ($action) {
            case "del" :
                $showAsk = 1;
                if ($_POST[cancelImage]) {
                    //show_array($GLOBALS["pageInfo"]);
                    cms_infoBox("Löschen vom Bild abgebrochen");
                    $goPage = $GLOBALS[pageInfo][pageName]."?folder=$folder&filename=$showFile";
                    // echo ("GoPage = $goPage <br>");
                    reloadPage($goPage,1);
                    $show_ask = 0;
                }
                if ($_POST[delImage]) {
                    $anz = deleteThumbs_ofFile($showFile, $folder);
                    $imageId = $imageData[id];
                    // echo ("Image Id $imageId<br>");
                    

                    $goPage = $GLOBALS[pageInfo][pageName]."?folder=$folder";
                    // echo ("GoPage = $goPage <br>");
                    
                    //$imageRoot = ""; // cmsImage_rootPath();

                    $delFile = $folder.$showFile;
                    // $delFile = "klappeAuf/".$folder.$showFile;
                    if (file_exists($delFile)) {
                        cmsImage_del_withId($imageId);
                        $imageRoot = cmsImage_rootPath(1);
                        $res = unlink($imageRoot.$delFile);
                        //echo ($imageRoot.$delFile." res = $res<br>");
                        cms_infoBox("Bild gelöscht");
                        reloadPage($goPage,1);
                        $showAsk = 0;
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
                if ($showAsk) {
                    echo ("<form method='post' >");
                    echo "<h3>Wollen Sie dieses Bild löschen?</h3>";
                    echo ("<input type='submit' value='Löschen' name='delImage' class = 'cmsInputButton'> ");
                    echo ("<input type='submit' value='Nein' name='cancelImage' class = 'cmsInputButton cmsSecond'> ");
                    echo ("</form>\n");
                }

                break;
            case "delThumb" :
                $anz = deleteThumbs_ofFile($showFile, $folder);
                if ($anz>0) {
                    reloadPage($pageInfo[page]."?folder=$folder&filename=$showFile",2);
                }
                break;
            
        }

        echo ("Datei: '".$imageData[fileName]."'<br>");
        echo ("Pfad: '".$imageData[orgpath]."'<br>");
        echo ("Größe: ".$imageData[width]."x".$imageData[height]." Pixel<br>");
        echo ("<h1>".$imageRoot.$folder.$imageData[fileName]."</h1>");
        $fileSize = filesize($imageRoot.$folder.$imageData[fileName]);
        echo ("Dateigröße : ".$fileSize." byte<br>");

        div_start("cmsImageEdit");



        $showSize = 200;
        div_start("cmsImageShow","background-color:#eee;float:left;width:".($showSize+0)."px;height:".($showSize+0)."px;border:1px solid #aaa;padding:5px;margin-right:5px;margin-bottom:5px;float:left");

        $showData = array();
        $showData["createSmall"] = 1;
        // $showData[ratio] = 5/1;
        echo(cmsImage_showImage($imageData,$showSize,$showData));
        
        div_end("cmsImageShow");
        div_start("cmsImageData","float:left;");
        // show_array($imageList[$showFile]);
        echo ("<a href='$pageInfo[page]?folder=$folder' class='linkButton'>zurück</a>");
        echo ("<a href='$pageInfo[page]?folder=$folder&imageId=$imageId&action=upload' class='linkButton'>Bild tauschen</a>");
        echo ("<a href='$pageInfo[page]?folder=$folder&imageId=$imageId&action=delThumb' class='linkButton second'>thumbnails löschen</a>");
        echo ("<a href='$pageInfo[page]?folder=$folder&imageId=$imageId&action=del' class='linkButton second'>löschen</a>");

        
        $thumbList = getThumbList($showFile,$folder);
        //show_array($thumbList);
        foreach($thumbList as $fileName => $fileList) {
            echo ("<br><h3>Thumbnails für $fileName </h3>");
            $i=0;
            foreach ($fileList as $size => $data) {
                $thumb_width  = $data[width];
                $thumb_height = $data[height];
                $thumb_size   = $data[size];
                $i++;
                echo ("Thumb $i: ".$thumb_width."x".$thumb_height." Pixel Größe:".$thumb_size." Byte<br>");
            }
        }

        div_end("cmsImageData");
        div_end("cmsImageEdit","before");
        $showList = 0;

    } 
    
    if ($showList) {
        $imageList = cms_ImageList($folder);

        $imageSize = 100;
        //div_start("imageBox","width:410px;height:150px;overflow-y:auto;");
        div_start("imageBox");
    
        foreach($imageList as $fileName => $imageData) { // ($i=0;$i<count($imageList);$i++) {


            //$imageData = $imageList[$i];
            $fileName = $imageData[fileName];
            switch ($imageData[showType]) {
                case "folder" :
                    div_start("imageSingle","background-color:#cee;float:left;width:".($imageSize+0)."px;height:".($imageSize+0)."px;border:1px solid #aaa;padding:5px;margin-right:5px;margin-bottom:5px;");
                    $path = $imageData[path];

                    // foreach($pageInfo as $key => $value ) echo ("$key=$value <br>");
                    echo ("Ordner <br> <a href='$pageInfo[page]?folder=$path'>$imageData[name]</a>");
                    div_end("imageSingle");
                    break;

                case "file" :
                    $imageId = $imageData[id];
                    if (intval($imageId)) {
                        echo("<a href='$pageInfo[page]?folder=$folder&imageId=$imageId'>");
                    } else {
                        echo("<a href='$pageInfo[page]?folder=$folder&filename=$fileName'>");
                    }
                    // echo ($imageId);
                    div_start("imageSingle","background-color:#eee;float:left;width:".($imageSize+0)."px;height:".($imageSize+0)."px;border:1px solid #aaa;padding:5px;margin-right:5px;margin-bottom:5px;");
                    if ($imageData[type]) {
                       
                        echo (cmsImage_showImage($imageData,$imageSize));
                        /*if (is_array($imageData[smallImages])) {
                            foreach($imageData[smallImages] as $key => $value) echo "$key ";
                        }*/
                       
                    } else {
                        echo ("Datei <br> $imageData[name]");
                    }

                    div_end("imageSingle");
                    echo("</a>");
                    break;

                default :
                    echo ("unkownType $imageData[showType]");
                    // foreach($imageList[$i] as $key => $value ) echo ("$key=$value ");
            }

        }
        div_end ("imageBox","before");
    }

    
}
    $folder = $_GET[folder];
    cms_showImages($folder,$frameWidth) ;



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

    echo("<form method='post' enctype='multipart/form-data'>");
    //div_start("reportImageLeft","float:left;width:190px;text-align:right;font-size:11px;padding-right:10px;");
    //echo ("Bild hochladen:<br>");
    //div_end("reportImageLeft");
    //div_start("reportImageRight","float:left;width:500px;text-align:left;font-size:11px;");
                       // echo ("Bild auswählen: $_GET[image] $code=$_GET[field]");
    // echo ("<input type='file' name='fileName' class='loadImage' value='$fn'> ");


    echo ("<div class='fileinputs'>");
    echo ("<input name='fileName' class='file hidden' type='file'>");
    echo ("<div class='fakefile' onChange='submit'><input><img src='cms/images/image.gif'></div></div>");
                        // http://www.quirksmode.org/dom/inputfile.html
    // echo ("<input type='text' name='fileCode' value='image_$code'>");
    echo ("<input type='submit' class='buttonInput second reportImageCancel' name='cancelUpload' value='abbrechen'> ");
    echo ("<input type='submit' class='buttonInput reportImageUpload' name='doUpload' value='hochladen'> ");

    echo ("<br>");
   echo ("Bild:<img src='cms/images/image.gif'>");
    // div_end("reportImageRight",1);
    echo("</form>");

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
<?php

?>
