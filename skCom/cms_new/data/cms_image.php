<?php // charset:UTF-8

function cmsImage_rootPath($root=0) {
    global $cmsName;

    $imageRootPath = $_SERVER['DOCUMENT_ROOT']."/images/";
    if (file_exists($imageRootPath)) {
        if ($root) return $_SERVER['DOCUMENT_ROOT']."/";
        return "/";
    }


    $imageRootPath = $_SERVER['DOCUMENT_ROOT']."/".$cmsName."/images/";
    if (file_exists($imageRootPath)) {
        if ($root) return $_SERVER['DOCUMENT_ROOT']."/".$cmsName."/";
        return "/".$cmsName."/";
    }

    

    
}


function cmsImage_checkFileName($fileName,$showInfo=1) {
    $fn = str_replace(array("ä","ö","ü","Ä","Ö","Ü","ß"," "), array("ae","oe","ue","Ae","Oe","Ue","ss","_"), $fileName);
    // echo ("FileName ohne Sonderzeichen : $fn<br>");
    
    // liefert die falschen Zeichen 
    $notChar = preg_replace("/[a-zA-Z0-9_. ]/" , "" , $fn);
    
    // entfernt die falschen Sonderzeichen
    $useChar = preg_replace("/[^a-zA-Z0-9_. -]/" , "" , $fn);
    
    if (strlen($notChar)) {
        $notCharList = array();
        for ($i=0;$i<strlen($notChar);$i++) {
            $char = $notChar[$i];
            $notCharList[$char] = 1;
        }
        
        if ($showInfo) {
            $str = "Hochgeladene Datei enthält nicht erlaubte Zeichen!<br>";
            $str .= $fileName."<br>";
            $str .= "Folgende Zeichen sollte nicht in einem Dateinamen vorkommen: <br>";
            foreach ($notCharList as $key => $value) $str.= "'$key' ";
            $str .= "<br>";
            $str .= "Diese Zeichen wurden entfernt. Der neue Dateiname ist:<br>";
            $str .= $useChar;

            cms_infoBox($str);
        }
    }
    return $useChar;
}


function cmsImage_upload_File($fileData,$imageFolder,$showInfo=1) {
    // echo ("cmsImage_upload_File($fileData,$imageFolder)<br>");
    global $cmsName;
    $imageRootPath = cmsImage_rootPath(1);
    
    if (!file_exists($imageRootPath)) {
        echo ("ImageRootPath not exist <br>");
        $imageRootPath = $_SERVER['DOCUMENT_ROOT']."/images/";
        if (!file_exists($imageRootPath)) {
            echo ("ImageRootPath not exist in Root<br>");
        }
    }

   // $imageRootPath .= "images/";

    if ($imageFolder[0] == "/") $imageFolder = substr($imageFolder,1);

//    if (substr($imageFolder,0,7) == "images/") {
//        $imageFolder = substr($imageFolder,7);
//        // echo ("Remove images/ ->$imageFolder <br>");
//    } else {
//       // echo ("no Images at start -> $imageFolder '".substr($imageFolder,0,7)."' <br>");
//    }

    

    // check TargetFolder
    if ($imageFolder) {
        if ($imageFolder[strlen($imageFolder)-1] != "/") $imageFolder .= "/";
        if (!file_exists($imageRootPath.$imageFolder)) {
            // echo ("Target Path ".$imageRootPath.$imageFolder." does not exist <br>");
            $folderList = explode("/",$imageFolder);
            $checkFolder = $imageRootPath;
            for ($i=0;$i<count($folderList)-1;$i++) {
                $checkFolder .= $folderList[$i]."/";
                if (!file_exists($checkFolder)) {
                    //echo ("Folder not exist $checkFolder <br>");
                    mkdir($checkFolder,0777);
                    if (!file_exists($checkFolder)) {
                        echo "existiert nicht nach erstellem <br>";
                    }
                }
                // echo ("Checke Order $folderList[$i]  $checkFolder <br>");
            }
        }
    }

    if (!file_exists($imageRootPath.$imageFolder)) {
        echo ("Folder not exist $imageFolder <br>");
        return 0;
    }

    $size = $fileData[size];
    $name = $fileData[name];
    $temp = $fileData[tmp_name];
    
    
    $name = cmsImage_checkFileName($name,$showInfo);
    //echo ("Target File = ".$imageRootPath.$imageFolder.$name." <br>");

    $imageInfo = getimagesize($temp);
    // echo ("ImageInfo von $temp $imageInfo <br>");
    if (is_array($imageInfo)) {
        $imgUploadWidth = $imageInfo[0];
        $imgUploadHeight = $imageInfo[1];
        if ($imgUploadWidth > 1024 OR $imgUploadHeight>1024) {
            if ($showInfo) cms_errorBox("Bild ist größer als die erlaubte Maximalgröße<br>Bild hat ein Auflösung von  $imgUploadWidth x $imgUploadHeight Pixel<br>Das Bild wurde nicht hochgeladen!");
            return ("errorImageSize");
        }
    }
    
    
    $existTargetFile = file_exists($imageRootPath.$imageFolder.$name);
    if ($existTargetFile) {
        // echo ("<h1>TARGET FILE EXIST </h1>");
        
        $imageData = cmsImage_get(array("fileName"=>$name,"orgpath"=>$imageFolder));
        if (is_array($imageData)) { // file is in Database
            $update = 0;
            // show_array($imageData);
            if (is_array($imageInfo)) {
//                $imgUploadWidth = $imageData[0];
//                $imgUploadHeight = $imageData[1];
                
                if ($imgUploadWidth != $imageData[width]) { echo ("update because with dirfent width $imgUploadWidth != $imageData[width] <br>"); $update = 1;}
               // echo ("Width $imageInfo[0] database = $imageData[width] <br>");
                if ($imgUploadHeight != $imageData[height]) { echo ("update because with dirfent height $imgUploadHeight != $imageData[height] <br>"); $update = 1;}
                // echo ("Height $imageInfo[1] database = $imageData[height] <br>");
                $md5 = md5_file($temp);
                if ($md5 != $imageData[md5]) { echo ("update because with difrent md5<br>"); $update = 1;}

                // echo ("Md5 $md5 database = $imageData[md5] <br>");
             }

             // $update = 1;

             if ($update) {
                $imageData[width] = $imageInfo[0];
                $imageData[height] = $imageInfo[1];
                $imageData[md5] = $md5;
             } else {
                 return $imageData[id];
             }
        } else {
            // echo ("NoT ImagData $name $imageFolder <br>");
            
        }
    } else {
        // echo "NOT EXIST";
        
    }
    // return "0STOPP ";
    // echo ("Datei = $name <br>");
    // echo ("Folder = $folder<br>");
    move_uploaded_file($temp, $imageRootPath.$imageFolder.$name);

    $existAfterCopy = file_exists($imageRootPath.$imageFolder.$name);
    if ($existAfterCopy) {
        //  echo "File Upload erfolgreich $update <br>";
        if ($update) {
            $imageId = cmsImage_saveImage($imageData);
            return $imageId;
        }
            //foreach ($imageInfo as $key => $value) echo("$key = $value <br>");
        
        $imageData = array();
        $imageInfo = getimagesize($imageRootPath.$imageFolder.$name);
        if (is_array($imageInfo)) {
             $imageData[width] = $imageInfo[0];
             $imageData[height] = $imageInfo[1];
            //foreach ($imageInfo as $key => $value) echo("$key = $value <br>");
        } else {
            echo ("Keine Image Info erhalten von ".$imageRootPath.$imageFolder.$name." <br>");
            return 0;
        }


        $imageData[fileName] = $name;
        $imageData[orgpath] = $imageFolder;

        if (!$imageData[width]) {
            $imageInfo = getimagesize($temp);
            $imageData[width] = $imageInfo[0];
            $imageData[height] = $imageInfo[1];
        }

        if (!$imageData[md5]) {
            $md5 = md5_file($imageRootPath.$imageFolder.$name);
            $imageData[md5] = $md5;
        }
        $imageId =  cmsImage_addImage($imageData);
        // echo ("cmsImage Add Result $imageId <br>");
        return $imageId;
    } else {
        echo ("Fehler bei move ".$imageRootPath.$imageFolder.$name." <br>");
        return "errorMove";
    }

}

function cmsImage_ImageType($fileEnd) {
    $fileEnd = strtolower($fileEnd);
    switch ($fileEnd) {
         case "jpg" : return ("JPG");
         case "jpeg" : return ("JPG");
         case "png" : return( "PNG");
         case "gif" : return("GIF");
    }
}

function cmsImage_getList($filter,$out="") {
    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";
            foreach($filter as $key => $value) {
               //  echo ("$key ($value[0] / $value[1]) = '$value' <br>");
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "`$key`='$value'";
            }
            $filterQuery = "WHERE ".$filterQuery;
        }
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_images` ".$filterQuery." ".$sortQuery;
    // echo ("Query $query <br>");
    $result = mysql_query($query);
    $imgList = array();
    if ($result) {
        WHILE ($imgData = mysql_fetch_assoc($result)) {
            $imageName = $imgData[name];
            switch ($out) {
                case "list" : $imgList[] = $imgData; break;
                
                case "fileName" : 
                    $fileName = $imgData[fileName];
                    $double = 0;
                    if (is_array($imgList[$fileName])) {
                       
                        $double = 1;
                        $found = $imgList[$fileName][found];                       
                        if ($found) $found++;
                        else $found = 2;
                        
                        $imgData[found] = $found;
                        // echo ("ist Doppelt $fileName $found <br>");
                    }
                    $imgList[$fileName] = $imgData; 
                    break;
                    
                default :
                    $imgList[$imageName] = $imgData;
            }
            
        }
    }
    return $imgList;
}



function cmsImage_get($filter) {
    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";
            foreach($filter as $key => $value) {
               //  echo ("$key ($value[0] / $value[1]) = '$value' <br>");
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "`$key`='$value'";
            }
            $filterQuery = "WHERE ".$filterQuery;
        }
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_images` ".$filterQuery." ".$sortQuery;
    // echo ("Query $query <br>");
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz==0) {
        // echo ("not found <br>");
        return 0;
    }
    if ($anz > 1) {
        // echo ("more found <br>");
        return "more";
    }
    $imageData = mysql_fetch_assoc($result);
    return $imageData;
}

function cmsImage_getData_by_Id($imageId) {
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_images` WHERE `id`=$imageId";
    $result = mysql_query($query);
    if (!$result) return "error";
    $anz = mysql_num_rows($result);
    // echo ("$query<br>");
    if ($anz == 0) return "notFound";
    if ($anz > 1) return "moreFound";
    $imageData = mysql_fetch_assoc($result);
    
    if (intval($imageData[width]) AND intval($imageData[height])) {
        $ratio = $imageData[width] / $imageData[height];
        $imageData[ratio] = $ratio;
    }    
    return $imageData;
}

function cmsImage_getData_by_md5($md5) {
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_images` WHERE `md5` LIKE '$md5'";
    $result = mysql_query($query);
    if (!$result) return "error";
    $anz = mysql_num_rows($result);
    // echo ("$query<br>");
    if ($anz == 0) return "notFound";
    if ($anz > 1) return "moreFound";
    $imageData = mysql_fetch_assoc($result);
    return $imageData;
}


function cmsImage_getImageStr($imageData,$imageSize,$showData=array()) {
    global $cmsName;
    $homePath = cmsImage_rootPath(); //"/";
   // if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$cmsName."/")) $homePath = "/".$cmsName."/";

    $path = $homePath.$imageData[orgpath];

    //  echo ("Path Home = '$homePath' -> $path <br>");
    $fileName = $imageData[fileName];

    $createSmall = 1;
    if (is_int($showData[createSmall])) $createSmall = $showData[createSmall];

    $frameWidth  = $imageSize;
    $frameHeight = $imageSize;

    // Frame Width
    if ($showData[frameSize]) {
        $frameWidth  = $showData[frameSize];
        $frameHeight = $showData[frameSize];
    }
    if ($showData[frameWidth]) $frameWidth = $showData[frameWidth];
    if ($showData[frameHeight]) $frameHeight = $showData[frameHeight];

    $imageWidth = $showData[imageWidth];
    if (!$imageWidth) $imageWidth = $imageSize;

    $imageHeight = $showData[imageHeight];
    if (!$imageHeight) $imageHeight = $imageSize;

    $vAlign = $showData[vAlign];
    if (!$vAlign) $vAlign = "middle";

    $hAlign = $showData[hAlign];
    if (!$hAlign) $hAlign = "center";

    // echo ("hAlign = $hAlign vAlign =$vAlign <br>");


    $imgWidth = $imageData[width];
    $imgHeight = $imageData[height];

    $orgWidth = $imgWidth;
    $orgHeight = $imgHeight;

    $off_X = 0;
    $off_Y = 0;
    $ratio = $showData[ratio];
    if ($ratio) {
        $newWidth = $imgWidth;
        $newHeight = floor($newWidth / $ratio);
        if ($newHeight > $imgHeight) {
            $newHeight = $imgHeight;
            $newWidth = floor($newHeight * $ratio);
            $off_X = floor(($imgWidth - $newWidth) / 2);
            $imgWidth = $imgWidth - (2*$off_X);
        } else {
            $off_Y = floor(($imgHeight - $newHeight) / 2);
            $imgHeight = $imgHeight - (2*$off_Y);
        }
        // echo ("$newWidth x $newHeight Offset =$off_X $off_Y<br>");
        $imgWidth = $newWidth;
        $imgHeight = $newHeight;
    }

    $showWidth = $imgWidth;
    $showHeight = $imgHeight;

    if ($imgHeight > $frameHeight) {
        $showWidth = intval($showWidth * $frameHeight / $showHeight);
        $showHeight = $frameHeight;
    }
    if ($showWidth > $frameWidth AND $frameWidth > 0) {
        $showHeight = intval($showHeight * $frameWidth / $showWidth);
        $showWidth = $frameWidth;
    }

//    echo ("image = $imgWidth x $imgHeight <br>");
//    echo ("Show = $showWidth x $showHeight <br>");

    if ($orgWidth != $showWidth OR $orgHeight != $showHeight ) {
        // echo ("Diffrent width / height<br>");
        // echo ("org = $imgWidth x $imgHeight <br>");
        // echo ("org = $showWidth x $showHeight <br>");
        if ($createSmall ) {
            $showWidth = floor($showWidth);
            $showHeight = floor($showHeight);
            // echo ("Create Thumbnails <br>");
            $smallExist = cmsImage_ImageSmallExist($imageData,$showWidth,$showHeight,$imgWidth,$imgHeight,$off_X,$off_Y);
            if ($smallExist) $fileName = $smallExist;
        } else {
            // echo ("dont Create Thumbnails");
        }
       //  echo ("Small Exist $smallExist <br>");
    }

    //$absVer = intval(($imageSize - $showHeight) / 2);


    return $imageData[orgpath].$fileName;
}




function cmsImage_showImage($imageData,$imageSize,$showData=array()) {
    global $cmsName;
    $homePath = cmsImage_rootPath(); //"/";    
   // if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$cmsName."/")) $homePath = "/".$cmsName."/";

    $path = $homePath.$imageData[orgpath];

    //  echo ("Path Home = '$homePath' -> $path <br>");
    $fileName = $imageData[fileName];

    $createSmall = 1;
    if (is_int($showData[createSmall])) $createSmall = $showData[createSmall];

    $frameWidth  = $imageSize;
    $frameHeight = $imageSize;

    // Frame Width
    if ($showData[frameSize]) {
        $frameWidth  = $showData[frameSize];
        $frameHeight = $showData[frameSize];
    }
    if ($showData[frameWidth]) $frameWidth = $showData[frameWidth];
    if ($showData[frameHeight]) $frameHeight = $showData[frameHeight];

    $imageWidth = $showData[imageWidth];
    if (!$imageWidth) $imageWidth = $imageSize;

    $imageHeight = $showData[imageHeight];
    if (!$imageHeight) $imageHeight = $imageSize;
    
    $vAlign = $showData[vAlign];
    if (!$vAlign) $vAlign = "middle";
    
    $hAlign = $showData[hAlign];
    if (!$hAlign) $hAlign = "center";

    // echo ("hAlign = $hAlign vAlign =$vAlign <br>");


    $imgWidth = $imageData[width];
    $imgHeight = $imageData[height];

    $orgWidth = $imgWidth;
    $orgHeight = $imgHeight;
   
    $off_X = 0;
    $off_Y = 0;
    $ratio = $showData[ratio];
    $crop  = $showData[crop];
    // echo ("<h1>Ratio $ratio $hAlign / $vAlign</h1>");
    if ($ratio ) {
        $newWidth = $imgWidth;
        $newHeight = floor($newWidth / $ratio);
        if ($crop) {
            if ($newHeight > $imgHeight) {
                $newHeight = $imgHeight;
                $newWidth = floor($newHeight * $ratio);
                switch ($hAlign) {
                    case "left" : $off_X = 0; break;
                    case "center" : $off_X = floor(($imgWidth - $newWidth) / 2); break;
                    case "right" : $off_X = floor(($imgWidth - $newWidth)); break;
                    default :
                        $off_X = floor(($imgWidth - $newWidth) / 2);                   
                } 
                $imgWidth = $imgWidth - (2*$off_X);
            } else {

                 switch ($vAlign) {
                    case "top"    : $off_Y = 0; break;
                    case "middle" : $off_Y = floor(($imgHeight - $newHeight) / 2); break;
                    case "bottom" : $off_Y = floor(($imgHeight - $newHeight)); break;
                    default :
                        $off_Y = floor(($imgHeight - $newHeight) / 2);              
                } 
                // $off_Y = floor(($imgHeight - $newHeight) / 2);
                $imgHeight = $imgHeight - (2*$off_Y);
            }
            $imgWidth = $newWidth;
            $imgHeight = $newHeight;
        }
        
        if ($frameWidth) $frameHeight = floor($frameWidth / $ratio);
        
        
        // echo ("$newWidth x $newHeight Offset =$off_X $off_Y<br>");
        
    }

    $showWidth = $imgWidth;
    $showHeight = $imgHeight;

    if ($imgHeight > $frameHeight) {
        $showWidth = intval($showWidth * $frameHeight / $showHeight);
        $showHeight = $frameHeight;
    }
    if ($showWidth > $frameWidth AND $frameWidth > 0) {
        $showHeight = intval($showHeight * $frameWidth / $showWidth);
        $showWidth = $frameWidth;
    }
    
    $resize = $showData[resize];
 
    if ($orgWidth != $showWidth OR $orgHeight != $showHeight ) {
//         echo ("Diffrent width / height<br>");
//         echo ("org = $imgWidth x $imgHeight <br>");
//         echo ("show = $showWidth x $showHeight <br>");
        if ($createSmall ) {
            $showWidth = floor($showWidth);
            $showHeight = floor($showHeight);
            // echo ("<h1> !! </h1>");
            $smallExist = cmsImage_ImageSmallExist($imageData,$showWidth,$showHeight,$imgWidth,$imgHeight,$off_X,$off_Y,$showData);
           // echo ("create Small = $smallExist <br>");
            if ($smallExist) $fileName = $smallExist;
            else return 0;
        } else {
           // echo ("dont Create Thumbnails");
        }
       //  echo ("Small Exist $smallExist <br>");
    }

    
    
    $spaceVer = intval($frameHeight - $showHeight);
    $spaceHor = intval($frameWidth - $showWidth);
    if ($imgHeight < $frameHeight AND $imgWidth < $frameWidth AND $resize) {
        
        $shoudWidth = $frameWidth;
        $shoudHeight = floor($imgHeight / $imgWidth  * $frameWidth);
        
        if ($shoudHeight > $frameHeight) {
            $shoudHeight = $frameHeight;
            $shoudWidth = floor($imgWidth / $imgHeight * $shoudHeight);
        }
        
        $spaceHor = intval($frameWidth - $shoudWidth);
        $spaceVer = intval($frameHeight - $shoudHeight);
        
        // echo ("Should $shoudWidth / $shoudHeight  Space: $spaceHor x $spaceVer <br>");
//        echo ("Breite: F / s / i $frameWidth $showWidth $imageHeight $imgWidth ==> $spaceHor <br>");
//
//        echo ("Höhe  : F / s / i $frameHeight $showHeight $imageHeight $imgHeight ==> $spaceVer <br>");
    } 
    //$absVer = intval(($imageSize - $showHeight) / 2);

    $absVerBottom = 0;
    switch ($vAlign) {
        case "top" : $absVer = 0; $absVerBottom = $spaceVer; break;
        case "middle" : $absVer = floor($spaceVer / 2); $absVerBottom = $spaceVer - $absVer ;break;
        case "bottom" : $absVer = $spaceVer; $absVerBottom = 0; break;
        default : $absVer = 0;
    }



    switch ($hAlign) {
        case "left"   : $absHor = 0; $absHorRight = $spaceHor; break;
        case "center" : $absHor = floor($spaceHor / 2); $absHorRight = $spaceHor-$absHor; break;
        case "right"  : $absHor = $spaceHor; $absHorRight = 0; break;
        default : $absHor = 0;
    }
   


    $title = $imageData[name];
    $alt   = $imageData[name];
    $name  = $imageData[name];
    if ($showData[title]) $title = $showData[title];
    if ($showData[alt]) $alt = $showData[alt];
    if ($showData[name]) $name = $showData[name];

    //echo ("P> $path $imageData[orgpath] <br>");
    if ($path[0] != "/") $path = "/".$path;


    if ($showData[out] == "url") {
        return $path.$fileName;
        //return $imageData[orgpath].$fileName;
    }

    // $name = php_clearLink($name);
   //  $name = str_replace(" ","&nbsp;",$name)
   // if ($path[0] == "/") $path = substr($path,1);
    if ($path[0] != "/") $path = "/".$path; //substr($path,1);
    $imgStr = "<img src='".$path.$fileName."' ";
    $imgStr .= "alt='$alt' title='$title' "; //name='$name'  ";
    if ($showData["class"]) $imgStr.= "class='".$showData["class"]."' ";
    if ($showData["id"]) $imgStr.= "id='".$showData["id"]."' ";
//    if ($showData["nextImage"]) $imgStr.= "nextImage='".$showData["nextImage"]."' ";
//    if ($showData["beforeImage"]) $imgStr.= "nextImage='".$showData["beforeImage"]."' ";
    
    $imgStyle = "";
    $imgStyle .= "padding-top:".$absVer."px;padding-left:".$absHor."px;";
    
    // $imgStr .= "background:#f00;";
    
    
    
    if ($absVerBottom>0) $imgStyle .= "padding-bottom:".$absVerBottom."px;";    
    if ($absHorRight > 0) $imgStyle .= "padding-right:".$absHorRight."px";
    
    if ($imgStyle) $imgStr .= "style='$imgStyle' ";
   
    
    if ($shoudWidth) $imgStr .= " width='".$shoudWidth."px'";
    else $imgStr .= "' width='".$showWidth."px'";

    if ($shoudHeight) $imgStr .= "' height='".$shoudHeight."px'";
    else $imgStr .= "' height='".$showHeight."px'";
    
    // $imgStr .= " height='40px' width='80px' ";
        
    $imgStr .= " />";
  
    return $imgStr;
}

function cmsImage_getShowHeight($imageData,$showWidth) {
    $imgWidth = $imageData[width];
    $imgHeight = $imageData[height];
    // echo ("$imgWidth x $imgHeight px <br>");
//    if ($imgHeight > $imageHeight) {
//        $showWidth = intval($showWidth * $imageHeight / $showHeight);
//        $showHeight = $showWidth;
//    }
    if ($imgWidth > $showWidth AND $imgWidth > 0) {
        $showHeight = intval($showWidth * $imgHeight / $imgWidth );
        $showWidth = $showWidth;
    }
    // echo( "cmsImage_getShowHeight($imageData,$showWidth) -> $showHeight <br>");
    return $showHeight;
}

function cmsImage_saveImage($imageData) {
    $query = "";
    foreach ($imageData as $key => $value) {
        switch ($key) {
            case "id" : $imageId = $value;break;
            default :
                if ($query != "") $query .= ", ";
                $query .= "`$key`='$value'";
        }
    }
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_images` SET ".$query." WHERE `id` = $imageId ";

    $result = mysql_query($query);
    if ($result) return $imageId;
    echo ("Error in Query $query <br>");

    return 0;
}

function cmsImage_editSettings($editContent,$frameWidth,$dontShow=array()) {
    $res = array();
    $data = $editContent[data];
    if (!is_array($data)) $data = array();
    
    

    if (!$dontShow[ratio]) {
        $addData = array();
        $addData[text] = "Festes Verhältnis";        
        $ratio = $data[ratio];
        if ($ratio) $checked="checked='checked'";
        else $checked = "";
        $input .= "<input class='cmsShowCheckBox' id='checkbox_ratio' type='checkbox' $checked name='editContent[data][ratio]' />";
        
        $className = "cmsCheckBoxDiv";
        if (!$ratio) $className .= " cmsShowEdit_hidden";
        $input .= "<div id='cmsEditType_ratio' class='$className' style=''>";
        
        $input .= "<input type='text' style='width:30px' name='editContent[data][ratioX]' value='$data[ratioX]' />";
        $input .= ":";
        $input .= "<input type='text' style='width:30px' name='editContent[data][ratioY]' value='$data[ratioY]' />";
        $input .= "</div>";
    }

    if (!$dontShow[crop]) {
        $addData[input] = $input;
        $addData[mode] = "Simple";
        $res[] = $addData;
        
        $addData = array();
        $crop = $data[crop];
        if ($crop) $checked="checked='checked'";
        else $checked = "";
        $addData[text] = "Beschneiden";
        
        $className = "cmsCheckBoxDiv";
        if (!$crop) $className .= " cmsShowEdit_hidden";
        $input = "<input class='cmsCropCheckBox' type='checkbox' $checked name='editContent[data][crop]' />";
    }

    if (!$dontShow[position]) {
        $addData[input] = $input;
        $addData[mode] = "More";
        $res[] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Bild-Position";
        $addData["input"] = cmsEdit_imagePosition("editContent[data][hAlign]","editContent[data][vAlign]",$data[hAlign],$data[vAlign]);
        $addData[mode] = "Simple";
        $res[] = $addData;
    }

    if (!$dontShow[zoom]) {
        
        $addData = array();
        $addData["text"] = "Zoom Bild";       
        $checked = "";
        if ($editContent[data][zoom]) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' value='1' name='editContent[data][zoom]' $checked >";
        $addData[mode] = "Simple";
        $res[] = $addData;
    }

    if (!$dontShow[resize]) {    
        
        $addData = array();
        $resize = $data[resize];
        if ($resize) $checked="checked='checked'";
        else $checked = "";
        $addData[text] = "Vergrößern wenn zu klein";
        $input = "<input type='checkbox' $checked name='editContent[data][resize]' />";
        $addData["input"] = $input;
        $addData[mode] = "More";
        $res[] = $addData;
    }
    return $res;
}


function cmsImage_update($imageData,$existData=array()) {
    $query = "";
    foreach ($imageData as $key => $value) {
        switch ($key) {
            case "id" : $imageId = $value; break;
            default :
                if ($value == $existData[$key]) {
                    // same Data
                } else {
                    if ($query != "" ) $query.= ", ";
                    $query.= "`$key`='$value'";
                }
        }
    }
    if ($query == "") {
        // echo ("No Change in Data <br>");
        return 1;
    }
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_images` SET ".$query." WHERE `id` = $imageId ";

    $result = mysql_query($query);
    if ($result) return $imageId;
    echo ("Error in Query $query <br>");

    return 0;
   
    
}

function cmsImage_save($imageData) {
    $id = $imageData[id];
    if ($id) {
        $existData = cmsImage_getData_by_Id($id);
        if (is_array($existData)) {
            $res = cmsImage_update($imageData,$existData);
            return $res;
        }
    }
    
    
    $query = "";
    foreach ($imageData as $key => $value) {
    
        if ($query != "") $query .= ", ";
        $query .= "`$key`='$value'";
   
    }
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_images` SET ".$query;

    $result = mysql_query($query);
    if ($result) return 1;
    echo ("Error in Query $query <br>");

    return 0;
}

function cmsImage_del_withId($imageId) {
    if ($imageId>0) {
        $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_images` ".$query." WHERE `id` = $imageId ";
        $result = mysql_query($query);
        if (!$result) {
            echo ("Error by delete Image with id = $imageId<br>");
            echo ("Query = $query <br>");
            return 0;
        }
        return 1;
    }
    echo ("Error by delete Image -> No Id '$imageId'<br>");
    return 0;
}

function cmsImage_createFolder($mainFolder,$newName) {
    $rootPath = cmsImage_rootPath(1);
    // echo ("cmsImage_createFolder($mainFolder,$newName) <br>");
    // echo ("RootPath ='$rootPath'<br>");
    $pathName = $rootPath.$mainFolder.$newName;
    if (file_exists($pathName)) {
        // echo ("Folder Exist allready<br>");
        $res = "exist";
    } else {
        // echo ("PathName = '$pathName'<br>");
        $res = mkdir($pathName,0777);
    }
    return $res;
}

function cmsImage_addImage($imageData) {
    // echo ("cmsImage_addImage($imageData)<br>");
    if (!is_array($imageData)) return "noData";


    $fileName = $imageData[fileName];
    $folder   = $imageData[orgpath];


    if (!file_exists($folder.$fileName)) {
        show_array($imageData);
        return "fileNotExist $folder.$fileName";
    }

    $fileStrList = explode(".",$fileName);
    $name = "";
    for ($i=0;$i<count($fileStrList)-1;$i++) {
        if ($name != "") $name .= ".";
        $name .= $fileStrList[$i];
    }
    $endung = $fileStrList[count($fileStrList)-1];
    $fileType = cmsImage_ImageType($endung);
    // echo ("FileType =$fileType / $endung <br>");
   
    
    $md5 = md5_file($folder.$fileName);

    $width = $imageData[width];
    $height = $imageData[height];

    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_images` SET ";
    $query .= "`filename`='$fileName'";
    $query .= ", `name`='$name'";
    $query .= ", `md5`='$md5'";
    $query .= ", `width`=$width";
    $query .= ", `height`=$height";
    $query .= ", `type`='$fileType'";
    $query .= ", `orgpath`='$folder'";
    
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query $query<br>");
        return "error in insert";
    }
    $id = mysql_insert_id();
    return $id;
}

function cmsImage_ImageSmallExist($imageData,$showWidth,$showHeight,$imgWidth,$imgHeight,$off_X,$off_Y,$showData=array()) {
    // echo ("cmsImage_ImageSmallExist($imageData, Show= $showWidth,$showHeight, Image= $imgWidth,$imgHeight ,$off_X,$off_Y) <br>");

    $fileName = $imageData[fileName];
    $folder   = $imageData[orgpath];

    $imageType = $imageData[type];
    if (!$imageType) {
        echo ("No ImageType $fileName <br>");
    }

    $homePath = cmsImage_rootPath(0);
    $homePathServer = $_SERVER['DOCUMENT_ROOT'].$homePath;
    
    
    
    $newFileName = "thumbs/_".$showWidth."_".$showHeight."_".$fileName;
    // echo ("<h1> Ratio = $showData[ratio] </h1>");
    if ($showData[crop] AND $showData[ratio]) {
        $newFileName = "thumbs/_".$showWidth."_".$showHeight;
        // echo ("<h1> $showData[hAlign] $showData[vAlign] </h1>");
        if ($showData[hAlign] == "left") $newFileName .= "_l";
        if ($showData[hAlign] == "center") $newFileName .= "_c";
        if ($showData[hAlign] == "right") $newFileName .= "_r";
        
        if ($showData[vAlign] == "top") $newFileName .= "_t";
        if ($showData[vAlign] == "middle") $newFileName .= "_m";
        if ($showData[vAlign] == "bottom") $newFileName .= "_b";
        $newFileName .= "_".$fileName;
        // echo ("cmsImage_ImageSmallExist NewFile = $newFileName <br>");
    }
    // 
   
   // echo ($folder."thumbs/".$newFileName);
    if (file_exists($homePathServer.$folder.$newFileName)) {
        // echo ("exist $homePath $folder $newFileName");
        return $newFileName;
    }
    //else ("not exist $homePath $folder $newFileName <br>");

    if (!file_exists($homePathServer.$folder."thumbs/")) {
        echo ("Create Folder ".$homePathServer.$folder."thumbs/");
        mkdir($homePathServer.$folder."thumbs/",0777);        
    }
//    echo ("not exist small ".$homePathServer.$folder.$newFileName." <br>");
//    echo ("home $homePath <br>");
//    echo ("homeServer $homePathServer <br>");
//    echo ("folder $folder <br>");
//    echo ("file $fileName <br>");
    cmsImage_resizeImage($homePathServer.$folder.$fileName, $homePathServer.$folder.$newFileName, $imageType,$showWidth,$showHeight,$imgWidth,$imgHeight,$off_X,$off_Y);

    if (file_exists($folder.$newFileName)) {
        return $newFileName;
    }
    return 0;
}

function cmsImage_resizeImage($orgFile, $newFile, $type, $showWidth,$showHeight,$imgWidth,$imgHeight,$off_X,$off_Y) {
    // echo ("cmsImage_resizeImage($orgFile, $newFile, $type)<br>");
   
    if (!$type) {
//        $liste = explode(".",$orgFile);
//        $type = $liste[count($liste)-1];
        $type = cms_FileIsImage($orgFile);
        echo ("<h1>$type</h1>");
    }


    //echo ("imageWidth $imgWidth x $imgHeight => ".($imgWidth*$imgHeight)." -> $showHeight x $showHeight <br>");
    if ($imgWidth * $imgHeight > 1400000) {
        if ($_SESSION[userLevel] > 5) {
            echo ("Bild zu groß: ");
            echo ("$imgWidth x $imgHeight <br />");
            if ($_SESSION[userLevel] > 5) {
         
            }
        }
        return 0;
    }
    //if ($orgFile[0] != "/") $orgFile = "/".$orgFile;
    // show_array(gd_info());

    $homePath = "";
   //  if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS[cmsName]."/")) $homePath = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS[cmsName];


    if (file_exists($homePath.$orgFile)) {
       //  echo ("exist $type");
    } else {
        if ($_SESSION[userLevel] >= 5) echo ("<b>Not exist $homePath $orgFile</b><br />");
        return 0;
    }

    //$iniData = ini_get_all();
    //show_array($iniData);
    //foreach ($iniData as $key => $value) echo ("ini $key => $value<br>");
    switch ($type) {
       case "GIF": // GIF
            $image_old = imagecreatefromgif($homePath.$orgFile);
            $image_new = imagecreate($showWidth, $showHeight);
            imagecopyresampled($image_new, $image_old, 0, 0, $off_X, $off_Y, $showWidth, $showHeight, $imgWidth, $imgHeight);
            imagegif($image_new, $newFile);
            break;

       case "JPG": // JPEG
            //echo ("<h1>Create JPG $imgWidth * $imgHeight</h1>");
            $image_old = imagecreatefromjpeg($homePath.$orgFile);
            // echo ("load $showWidth, $showHeight <br>");
            $image_new = imagecreatetruecolor($showWidth, $showHeight);
            // echo ("create<br>");
            imagecopyresampled($image_new, $image_old, 0, 0, $off_X, $off_Y, $showWidth, $showHeight, $imgWidth, $imgHeight);
            // echo ("small<br>");

            imagejpeg($image_new,$newFile);
            // echo ("saveto $newFile");
            break;

       case "PNG": // PNG
            $image_old = imagecreatefrompng($homePath.$orgFile);
            $image_colordepth = imagecolorstotal($image_old);

            if ($image_colordepth == 0 || $image_colordepth > 255) {
                $image_new = imagecreatetruecolor($showWidth, $showHeight);
            } else {
                $image_new = imagecreate($showWidth, $showHeight);
            }

            imagealphablending($image_new, false);
            imagecopyresampled($image_new, $image_old, 0, 0, $off_X, $off_Y, $showWidth, $showHeight, $imgWidth, $imgHeight);
            imagesavealpha($image_new, true);
            imagepng($image_new, $newFile);
            break;

       default:
           echo ("UNKOWN TYPE in cmsImageResize '$type' <br>");
        return false;
  }

  imagedestroy($image_old);
  imagedestroy($image_new);
  return true;




}

function cmsImage_selectList($folder,$showImages=1) {
    $res = "";
    // $res .= "Bildauswahl: $folder<br>";

    $imageHeight = 100;
    $abs = 5;
    $frameHeight = 2*($imageHeight+$abs)+$abs;
    $divData = array();
    $divData["style"] = "width:100%;overflow:auto;height:".$frameHeight."px;";
    $divData[cmsName] = $GLOBALS[cmsName];

    $res .= div_start_str("cmsImageScroll",$divData);

    if ($showImages) {
        $res.= cmsImage_SelectList_getContent($folder);
    }

    $res .= div_end_str("cmsImageScroll","before");

    $res .= div_start_str("cmsImageAction","margin-top:5px;");
    
    $res .= div_start_str("cmsImageSelectSelect cmsJavaButton","float:left;");
    $res .= "auswählen";
    $res .= div_end_str("cmsImageSelectSelect cmsJavaButton");

    $res .= div_start_str("cmsImageSelectCancel cmsJavaButton cmsSecond","float:left;");
    $res .= "abbrechen";
    $res .= div_end_str("cmsImageSelectCancel cmsJavaButton cmsSecond");
   
    $res .= div_end_str("cmsImageAction","before");

    return $res;
}


function fileIsImage($file) {
    $fileEnd = end(explode(".", $file));
    $fileEnd = strtolower($fileEnd);
    return cmsImage_ImageType($fileEnd);   
}

function cmsImage_SelectList_getContent($folder) {
    // echo ("cmsImage_SelectList_getContent($folder)<br>");
    $showName = 0;
    $border = 3;
    $width = 80;
    $height = $width;
    if ($showName) $height = $height + 15;

    $frameHeight = 2*($height+$border+border)+$border;

    $res = "";

    if (!$folder) $folder="images/";
    $rootPath = cmsImage_rootPath(1);
    $folders = array();
    if (file_exists($rootPath.$folder)) {
        $handle = opendir($rootPath.$folder);
        // $res.= "suche in Folder $folder <br>";
        while ($file = readdir ($handle)) {
            if($file != "." && $file != "..") {
                if(is_dir($rootPath.$folder."/".$file)) {
                    if ($file != "thumbs") {
                        $folders[$file] = array("name"=>"$file","showType"=>"folder","path"=>$folder.$file."/");                    
                        // $res.= "<h1> FOLDER $file </h1>";
                    }
                } else {
                    $fileName = $rootPath.$folder.$file;
                    $fileType = filetype($fileName);
                    $fileSize = filesize($fileName);
                    $fileMd5  = md5_file($fileName);
                    $file_name = "";
                    $fileData = array("fileName"=>$file,"type"=>$fileType,"orgpath"=>$folder,"md5"=>$fileMd5);
                    $imageType = fileIsImage($file);
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
    } else {
        $res .= "Folder $rootPath $folder not exist!";
    }
    
    
    $pathList = array();
    $imageList = array();

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_images` WHERE `orgpath` LIKE '$folder%' ";
    $result = mysql_query($query);

    $list = explode("/",$folder);
    $folderCount = count($list);
    

//    $res .= "Ordner: <b>$folder</b>";
//    
//    $res .= "<div class='cmsModulNewFolder'>new</div>";
//    
//    $res .= "<div class='cmsModulUploadImage'>upload</div>";
//    $res .= "<br>";
    
    $upPath = "";
    if ($folderCount>2) {
        for ($i=0;$i<count($list)-2;$i++) {
            // $res .= "MainPath $i $list[$i]<br>";
            $upPath .= $list[$i]."/";
        }
    }
    
    


    While ($imageData = mysql_fetch_assoc($result)) {
        $orgpath = $imageData[orgpath];
        $imageFileName = $imageData[name];
        if ($orgpath == $folder) {
            $imageList[$imageFileName] = $imageData;
        } else {
            $list = explode("/", $orgpath);
            $pathCount = count($list);
            if (($pathCount -1) == $folderCount) { // is subFolder
                $subFolderName = $list[$pathCount-2];
                if (!$pathList[$subFolderName]) {
                    $pathList[$subFolderName] = 1;
                } else {
                    $pathList[$subFolderName]++;
                }
            }
        }

    }

    // PATH DOWN
    if ($upPath != "") {
        //$divData[style] = "width:".($width-40)."px;height:".($height-40)."px;float:left;padding:23px;font-size:20px;";
        $divData[style] = "width:".($width-7)."px;height:".($height-17)."px;float:left;";
        // $divData[folderName] = $upPath;
        $divData[id] = $upPath;
        
        $res.= div_start_str("cmsFolderSelectFrame cmsUpPath",$divData); //"background-color:#ccc;padding:3px;margin-right:3px;margin-bottom:3px;width:".$width."px;height:".$width."px;float:left;");
        if ($showName) {
            $res.= $imageCount;
            $res.= div_start_str("cmsImageName","style:with:100%;height:15px;overflow:hidden;font-size:9px;");
            $res.= "/..";
            $res.=div_end_str("cmsImageName");
        } else {
           $res.= "/..";
           
        }
        $res.= div_end_str("cmsFolderSelectFrame cmsUpPath");


    }

    $divData[style] = "width:".($width-7)."px;height:".($height-17)."px;float:left;";
    foreach ($pathList as $folderName => $imageCount) {
        $divData[id] = $folder.$folderName."/";
        // $divData[imageSrc] = $imageSrc;

        $res.= div_start_str("cmsFolderSelectFrame",$divData); //"background-color:#ccc;padding:3px;margin-right:3px;margin-bottom:3px;width:".$width."px;height:".$width."px;float:left;");
        if ($showName) {
            $res.= $imageCount;
            $res.= div_start_str("cmsImageName","style:with:100%;height:15px;background-color:#ccc;overflow:hidden;font-size:9px;");
            $res.= "$folderName";
            $res.= div_end_str("cmsImageName");
        } else {
           $res.= "/".$folderName;
           $res.= "<br>".$imageCount." _Bilder";
        }
        $res.= div_end_str("cmsFolderSelectFrame");


    }
    
    // zeige weitere Folder
    $divData[style] = "width:".($width-7)."px;height:".($height-17)."px;float:left;";
    foreach ($folders as $folderName => $folderData) {
        if (!$pathList[$folderName]) {
            $divData[id] = $folder.$folderName."/";
            $res.= div_start_str("cmsFolderSelectFrame",$divData); 
            $res .= "/".$folderName;
            $res.= div_end_str("cmsFolderSelectFrame");
        }
    }
    
    
    $searchName = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_images` WHERE `orgpath` LIKE '$folder' ";
    if ($searchName) $query.= " AND `name` LIKE '%".$searchName."%' ";
    $query .= " ORDER BY `name` ASC  ";
    $result = mysql_query($query);
    if (!$result) {
        $res.="Error in Query $query <br>";
    } else {

        $dragAble = 1;
        $height = $width;
        $showName = 1;
        if ($showName) $height = $height + 15;

        While ($imageData = mysql_fetch_assoc($result)) {
            $imgStr = cmsImage_showImage($imageData,$width,array("createSmall"=>1));
            $imgUrl = cmsImage_showImage($imageData,$width,array("createSmalL"=>1,"out"=>"url"));
            if ($imgStr ) {
                $divData = array();
                $path = $imageData[orgpath];
                $fileName = $imageData[fileName];
                $imageName = $imageData[name];
                $imageSrc = $path.$fileName;
                $imageId = $imageData[id];
                $imagePath = $imageData[orgpath];

                $imgStyle = "width:".$width."px;height:".($height+20)."px;float:left;";
                $divData[style] = $imgStyle;
                $divData[imageId] = "".$imageData[id];
                $divData[imageSrc] = $imageSrc;

                $res.= div_start_str("cmsImageSelectFrame",$divData); //"background-color:#ccc;padding:3px;margin-right:3px;margin-bottom:3px;width:".$width."px;height:".$width."px;float:left;");
                
                
                $imgFrameStyle = "";
                // $imgFrameStyle .= "width:".$width."px;height:".$width."px;margin:0px;padding:0px;";
                //$imgFrameStyle .= "width:100%;height:100%;margin:0px;padding:0px;";
                $imgFrameStyle .= 'background-image:url("'.$imgUrl.'");';
                
                // $imgFrameStyle = "background-image:url('".$imgUrl."');";
                //$imgFrameStyle .= "background-position:50% 50%;background-repeat:no-repeat;z-index:98;";
                $imgFrameId = "id:$imageId|path:$imagePath";
                $imgFrameClass = "cmsImageListItem";
                if ($dragAble) {
                    $imgFrameClass .= " dragImage";
                }
                
//                $dragStyle = "";
//                $dragStyle .= "background-image:url('$imgUrl');";
//                $dragStyle .= "background-position:50% 50%;background-repeat:no-repeat;";
//                $dragStyle .= "width:".$width."px;height:".$width."px;";
                
                
                $res.= "<div class='$imgFrameClass' id='$imgFrameId' style='$imgFrameStyle'>";
                    
                
                    
                

                // if ($dragAble) $res.= "<div class='' id='id:$imageId|path:$imagePath' style='$dragStyle'>";
                // $res.= "name";
                // $res.= $imgStr;
                // $res.=$imgUrl;
                // if ($dragAble) $res.= "</div>";
                // $res.="$imageData[fileName]";
                $res.="</div>";

                if ($showName) $res.= div_start_str("cmsImageName","style:with:100%;height:15px;background-color:#ccc;overflow:hidden;font-size:9px;");
                if ($showName) $res.= "$imageName";
                if ($showName) $res.=div_end_str("cmsImageName");
                $res.= div_end_str("cmsImageSelectFrame");

            }

        }
    }
    return $res;
}


function cmsImage_List($imageListStr,$showData=array()) {
    $width =  400;
    $height = 200;
    $imgWidth = 100;
    $imgHeight = 75;
    $imageAdd = 0;
    $imageUpload = 0;
    $imageSortAble = 0;
    $imageDeleteAble = 0;
    $delimiter = "|";
    $dataName = "imageList";
    $showMode = "line";
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "width"           : $width = $value; break;
            case "height"          : $height = $value; break;
            case "imageUpload"     : $imageUpload = $value; break;
            case "imageAdd"        : $imageAdd = $value; break;
            case "imageFolder"     : $imageFolder = $value; break;
            case "dataName"        : $dataName = $value; break;
            case "imgWidth"        : $imgWidth = $value; break;
            case "imgHeight"       : $imgHeight = $value; break;
            case "showMode"        : $showMode = $value; break;
            case "imageSortAble"   : $imageSortAble = $value; break;
            case "imageDeleteAble" : $imageDeleteAble = $value; break;
                

        }
    }

    if (!$imageFolder) $imageFolder = "images/";
    $imgListData = cmsImage_imageList_getListFormString($imageListStr);
   
    $imgList = $imgListData[imgList];
    $delimiter = $imgListData[delimiter];
    // show_array($imgList);
    $str = "";   
   
    $divData = array();
    $divData["class"] = "";
    // if ($imageSortAble) $divData["class"].= " cmsImageSortList";
    if ($imageDeleteAble) $divData["class"].= " cmsImageDeleteList";
    $divData[style] = "width:".$width."px;display:inline-block;";
    $str.= div_start_str("cmsImage_listSelect",$divData);
   // show_array($showData);


    $showListData = array();
    
    $showListData["showMode"] = $showMode;
    $showListData["imageSortAble"] = $imageSortAble;            
    $showListData["imageDeleteAble"] = $imageDeleteAble;
    
    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    
    
    $str .= cmsImage_listContent($imgList,$delimiter,$dataName,$width,$height,$imgWidth,$imgHeight,$showListData);
    
    if ($imageAdd) {
        
        switch ($cmsEditMode) {
            case "onPage2" :
                $str .= lga("contentType_image","imageAdd",":"); //"Bild hinzufügen:";
                $str .= "<img id='path:$imageFolder' src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='30px' height='30px' class='cmsImageSelectModul'> ";
                break;
                // cmsImageSelect cmsImageSelectModul
            
            default : 
                $str .= lga("contentType_image","imageAdd",":"); //"Bild hinzufügen:";
                $str .= "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='30px' height='30px' class='cmsImageSelect'> ";

                $str .= "<input type='text' class='cmsImageId' tabindex='5000' style='width:30px;' name='imageAdd' value='' onFocus='submit()' >";    

                $divName = "cmsImageSelector";
                $divData = array();
                $divData[style] = "height:0px;background-color:#bbb;visible:none;overflow:hidden;";
                $divData["folderName"] = $imageFolder;
                $str.= div_start_str($divName,$divData);
                $str.= cmsImage_selectList($imageFolder,0);
                $str.= div_end_str($divName);  
                
                if ($imageUpload) {
                    $str .= "<input name='uploadImage' tabindex='5000' type='file' size='50' '  >"; //  maxlength='10000000' onChang='submit()
                    $str .= "<input name='uploadFolder' type='text' size='50' class='cmsImagePathSelector' readonly='readonly'  value='$imageFolder' >";
                }

                
        }
        
        
        
       
    }

    
    $str.= div_end_str("cmsImage_listSelect");


    return $str;
}

function cmsImage_imageList_getListFormString($imageListStr) {
    // echo ("<h3>cmsImage_imageList_getListFormString($imageListStr)</h3>");
    $imgList = array();
    $delimiter = "|";

    if ($imageListStr[0] == "|") {
        // echo ("sub ".substr($imageListStr,1,strlen($imageListStr)-2)."<br>");
        $imgList = explode("|",substr($imageListStr,1,strlen($imageListStr)-2));
        // echo ("Anzahl = ".count($imgList)."<br>");
        $delimiter = "|";
        return array("imgList"=>$imgList,"delimiter"=>$delimiter);
    }

    $imageId = intval($imageListStr);
    if ($imageId>0) {
        $imgList[]=$imageId;
        $delimiter = "|";
        // echo("ist Id ");
        return array("imgList"=>$imgList,"delimiter"=>$delimiter);
    }

    if (!is_null(strpos($imageListStr,","))) {
        $imgList = explode(",",$imageListStr);
        $delimiter = ",";
        echo ("Delimiter = ',' '$imageListStr' pos=".strpos($imageListStr,",")." <br>");
    }
    return array("imgList"=>$imgList,"delimiter"=>$delimiter);
}

function cmsImage_imageList_action($action,$value,$imageStr) {
    switch($action) {
        case "down" :
            foreach ($value as $moveDown => $moveStr) {}
            // echo ("MoveDown $moveDown <br>");
            break;
        case "up" :
            foreach ($value as $moveUp => $moveStr) {} //echo ("$moveUp = $moveStr <br>");
            // echo ("MoveUp $moveUp <br>");
            break;
        case "del" :
            foreach ($value as $del => $moveStr) {}
            //echo ("Delete $del <br>");
            break;
        case "add" :
            // echo ("ADD $value<br>");
            $addImage = $value;
            break;

        default :
            echo ("unkown Action in cmsImage_imageList_action $action <br>");
            return 0;
    }
    // echo ("Image Str = $imageStr <br>");

    $imgListData = cmsImage_imageList_getListFormString($imageStr);
    $imgList = $imgListData[imgList];
    $delimiter = $imgListData[delimiter];

    //echo ("Delimiter = $delimiter <br>");
    // echo ("ImgList = $imgList ".count($imgList)." <br>");

    $newImgList = array();
    for ($i=0;$i<count($imgList);$i++) {
        $imageId = $imgList[$i];
        // echo ("IMAGE ID = $imageId");

        $add = 1;
        if($imageId == $del) {
            //echo (" <b>delete this image</b> ");
            $add = 0;
        }

        if ($imageId == $moveDown) {
            // echo (" <b>move Down this image</b> ");
            $add = 0;
        }

        if ($imageId == $moveUp) {
            // echo (" <b>move Up this image</b> ");
            $anz = count($newImgList);
            if ($anz>=1) {
                // echo ("Wert davor= ".$newImgList[$anz-1]." ");
                $before = $newImgList[$anz-1];
                $newImgList[$anz-1] = $imageId;
                $newImgList[] = $before;
                $add = 0;
            }
        }

        if ($add) {
            $newImgList[] = $imageId;
        }


        if ($addNext) {
            $newImgList[] = $addNext;
            $addNext = 0;
        }
        
        if ($imageId == $moveDown) {
            $addNext = $imageId;
        }
        

        // echo ("<br>");
    }

    if ($addNext) {
       $newImgList[] = $addNext;
       $addNext = 0;
    }

    if ($addImage) {
        // echo ("<h2>Add to List $addImage</h2>");
        $newImgList[] = $addImage;
    }

    $newImageStr = "";
    for($i=0;$i<count($newImgList);$i++) {
        $imageId = $newImgList[$i];
        switch ($delimiter) {
            case "," :
                if ($newImageStr != "") $newImageStr .= ",".$imageId;
                else $newImageStr .= $imageId;
                break;
            case "|" :
                if ($newImageStr != "") $newImageStr .= $imageId."|";
                else $newImageStr .= "|".$imageId."|";
                break;
        }
    }
    // echo ("<h1> NEW IMAGESTR = '$newImageStr' </h1>");
    return $newImageStr;    
}

function cmsImage_listContent($imgList,$delimiter,$dataName,$width,$height,$imgWidth=100,$imgHeight=75,$showListData=array()){
    
    
    $showMode = $showListData["showMode"];
    $imageSortAble = $showListData["imageSortAble"];            
    $imageDeleteAble = $showListData["imageDeleteAble"];
    
    if ($imageSortAble) $str.= "ImageSortAble <br/>";
    if ($imageDeleteAble) $str .= "imageDeleteAble <br />";
    
    
    if (!$delimiter) $delimiter = "|";
    $valueText = "";

    if (!$imgWidth) $imgWidth = 100;
    if (!$imgHeight) $imgHeight = floor($imgWidth / 4 * 3);


    // echo ("cmsImage_listContent($imgList,$width,$height)<br />");
    $scrollAdd = 0;
    $imgCount = count($imgList);
    if ((80 * $imgCount) > $height) {
        $height = 80 * $imgCount;
        if ($height > 400) {
            $height = 400;
            $scrollAdd = 20;
        }
    }
    
    $divCont = "";
    
    
    switch ($showMode) {
        case "block" :
             $divName = "cmsImage_listBlock";
             break;
        case "line" :
            $divName = "cmsImage_listList";
            break;
        default :
             $divName = "cmsImage_listList";
    }

    

    // $divName = "cmsImage_listList";
    $divData = array();
    $divData[style] = "width:".$width."px;visible:visible;overflow:auto;height:".$height."px;border-bottom;1px solid #fff;";
    if ($imageSortAble) $divData["class"].= " cmsImageSortList";
   //  if ($imageDeleteAble) $divData["class"].= " cmsImageDeleteList";
    $divCont .= div_start_str($divName,$divData);

    $imageSize = $imgWidth;
    $imageHeight = $imgHeight;
    $showData = array();
    $showData[frameWidth] = $imageSize;
    $showData[frameHeight] = $imageHeight;
    $showData[hAlign] = "center";
    $showData[vAlign] = "top";

    $imageAddClass = "";
    if ($imageSortAble) $imageAddClass.= " cmsImageSortItem ";
    if ($imageDeleteAble) $imageAddClass .= " cmsImageDeleteItem";
    
    for ($i=0;$i<count($imgList);$i++) {
        $imageId = intval($imgList[$i]);
        if ($imageId > 0) {
            switch ($delimiter) {
                case "," :
                    if ($valueText != "") $valueText .= ",".$imageId;
                    else $valueText .= $imageId;
                    break;
                case "|" :
                    if ($valueText != "") $valueText .= $imageId."|";
                    else $valueText .= "|".$imageId."|";
                    break;
            }

            $imgData = cmsImage_getData_by_Id($imageId);
            $imgName = $imgData[name];
            
            $orgImagePath   = $imgData[orgpath];
            $imgPath = substr($orgImagePath,  strpos($orgImagePath, "/")+1);
            
            
            switch ($showMode) {
                case "block" :
                    $divCont .= "<div class='cmsImageListItem $imageAddClass' id='id:$imageId|path:$orgImagePath' style='width:".($imgWidth+2)."px;height:".($imgHeight+2)."px;'>";
                    $divCont .= "<div class='cmsImageListDelete'>&nbsp;</div>";
                    
                    // $divCont .= div_start_str("imageListLine_Image","width:".$imageSize."px;overflow:hidden;");
                    $divCont .= cmsImage_showImage($imgData, $imageSize, $showData);
                    $divCont .= "</div>";
                    
//                    $divCont .= div_end_str("imageListLine_Image");
                    break;
                    
                
                case "line" :
                    $imgWidth = $imgData[width];
                    $imgHeight = $imgData[height];
                    $divLineData = array();
                    $divLineData[id] = "id:$imageId|path:$orgImagePath";
                    $divLineData["class"] = $imageAddClass;
                    $divLineData[style] = "height:$imageHeight px;";
                    
                    $divCont.= div_start_str("imageListLine",$divLineData);
           
                    // FileImage
                    $divCont .= div_start_str("imageListLine_Image","float:left;width:".$imageSize."px;overflow:hidden;");
                    $divCont .= cmsImage_showImage($imgData, $imageSize, $showData);
                    $divCont .= div_end_str("imageListLine_Image");

                    $divCont .= div_start_str("imageListLineContent","height:20px;float:left;width:".($width-$imageSize-$scrollAdd)."px;");


                    // FileName
                    $divCont .= div_start_str("imageListLine_Name","float:left;width:100px;overflow:hidden;");
                    $divCont .= $imgName;
                    $divCont .= div_end_str("imageListLine_Name");


                    // FileSize
                    $divCont .= div_start_str("imageListLine_Size","float:left;width:100px;overflow:hidden;");
                    $divCont .= $imgWidth."x".$imgHeight;
                    $divCont .= div_end_str("imageListLine_Size");

                    // FilePath
                    $divCont .= div_start_str("imageListLine_Path","float:left;width:200px;overflow:hidden;");
                    $divCont .= $imgPath;
                    $divCont .= div_end_str("imageListLine_Path");

                    // FileAction
                    $divCont .= div_start_str("imageListLine_Action","float:left;width:".($width-$imageSize-400-$scrollAdd)."px;overflow:hidden;");
                    //$divCont .= "<a class='cmsContentHeadButton' href='#'>x</a>";
                    $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='x' name='deleteFromList[$imageId]' >";
                    if ($i>0) $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='&#8593;' name='moveUpInList[$imageId]' >";

                    if ($i<count($imgList)-1) $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='&#8595;' name='moveDownInList[$imageId]' >";

                    //$divCont .= "<a class='cmsContentHeadButton' href='#'>&#8593;</a>";
                    // $divCont .= "<a class='cmsContentHeadButton' href='#'>&#8595;</a>";
                    $divCont .= div_end_str("imageListLine_Action");



                    $divCont .= div_end_str("imageListLineContent","before");


                    // subTitle
                    $divCont .= div_start_str("imageListsubTitleLine","overflow:hidden;height:40px;float:left;width:".($width-$imageSize-$scrollAdd)."px;");
                    $divCont .= div_start_str("imageListsubTitle","width:400px;overflow:hidden;float:left;");
                    $subTitle = $imgData[subTitle];
                    if ($subTitle) {
                        $divCont .= "Untertitel: '$subTitle'";
                    } else {
                        $divCont .= "keine Bildunterschrift";
                    }
                    $divCont .= div_end_str("imageListsubTitle");

                    $divCont.= div_start_str("imageListsubTitleAction","overflow:hidden;float:left;width:".($width-$imageSize-400-$scrollAdd)."px;");
                    $editLink = "admin_cmsImages.php?folder=$imgPath&imageId=$imageId";
                    $editLink = htmlspecialchars($editLink);
                    $divCont .= "<a href='$editLink' class='cmsContentHeadInputButton'>edit</a>";
                        //deleteFromList[$imageId]' >";

                    $divCont.= div_end_str("imageListsubTitleAction");




                    $divCont.= div_end_str("imageListsubTitleLine","before");



                    $divCont.= div_end_str("imageListLine","before");
                    break;


            }

            
            
            

           // $divCont .= "Image with ID $imageId <br>";
        }
    }

    $divCont .= div_end_str($divName);
    if ($_SESSION[showLevel]>=9) {
        $valueText = "<input readonly='readonly' id='cmsImage_list_imageStr' type='text' name='$dataName' value='$valueText' ><br>";
    } else {
        $valueText = "<input id='cmsImage_list_imageStr' type='hidden' name='$dataName' value='$valueText' ><br>";
    }
    

    return $valueText.$divCont;
}

?>
