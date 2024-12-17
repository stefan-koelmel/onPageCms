<?php // charset:UTF-8

class cmsType_image_base extends cmsType_contentTypes_base {
    function getName() {
        return "Bild";
    }
    
    

    function image_show($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $res = array();

        //$img = "<img src='cms/image/image.png' width='30px' height='30px' class='cmsimageSelect'> ";
        
        $imageId = intval($data[imageId]);
        if ($imageId > 0) {
            div_start("cmsImageType","min-height:30px");
            $width = $data[imageWidth];
            if (strlen($width)) {
                if (strpos($width,"%")) {
                    $prozWidth = intval(substr($width,0,strpos($width,"%")));
                    $imageWidth = intval($frameWidth * $prozWidth / 100);                
                } else {
                    if (strpos($width,"px")) { // removePixel
                        $width = substr($width,0,strpos($width,"px"));
                    }
                    if (intVal($width)>0) {
                        $imageWidth = intval($width);
                    } else {
                        switch ($width) {
                            case "auto" : $imageWidth = $frameWidth; $imageHeight = $frameWidth; break;
                            case "full" : $imageWidth = $frameWidth; break;
                         }
                    }
                }
            }

            $height = $data[imageHeight];
            if (strlen($height)) {

                if (strpos($height,"px")) { // removePixel
                    $height = substr($height,0,strpos($height,"px"));
                }
                if (intVal($height)>0) {
                    $imageHeight = intval($height);
                }
            }
            if (!$imageHeight) $imageHeight = "auto";


            $vAlign = $data[vAlign];
            $hAlign = $data[hAlign];

            $showData = array();

            if ($imageWidth) {
                $showData[imageWidth] = $imageWidth;
            }
            if ($imageHeight) {
                $showData[imageHeight] = $imageHeight;
            }
            if ($vAlign) $showData[vAlign] = $vAlign;
            if ($hAlign) $showData[hAlign] = $hAlign;
            $showData[frameWidth] = $imageWidth;
            $showData[frameHeight] = $imageHeight;

            // show_array($showData);



            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $img = cmsImage_showImage($imageData,$frameWidth,$showData);
                echo ($img);
            }
            div_end("cmsImageType");
        } else {
            echo ("<div class='cmsContentNoData'>");
            echo ("Kein Bild!");
            echo ("</div>");
        }
       


    }


    function image_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        
        
        switch ($GLOBALS[cmsSettings][editMode]) {
            case "onPage2" :
                $imageClickClass = "cmsImageSelectModul";
                break;
            default:
                $imageClickClass = "cmsImageSelect";
        }

        $img = "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='90px' height='90px' class='$imageClickClass'> ";
        $imageId = intval($data[imageId]);
        if ($imageId > 0) {
            $imageData = cmsImage_getData_by_Id($imageId);
            
            if (is_array($imageData)) {
                $imagePath = $imageData[orgpath];
                $idStr = "id:$imageId|path:$imagePath";
                $img = cmsImage_showImage($imageData,100,array("class"=>$imageClickClass,"id"=>$idStr));
            }
        }
        // MainData
        $addData = array();
        $addData["text"] = "Bild";
        $input = "";
        $input .= "<div class='cmsImageDropFrame cmsDropSingle' >";
        $input .= "<div class='cmsImageFrame' >";
        $input .= $img;
        $input .= "</div>";
        $inputType = "hidden";
        
        $input .= "<input type='$inputType' class='cmsImageId' style='width:30px;' name='editContent[data][imageId]' value='$data[imageId]' />";
        $input .= "</div>";
        $addData["input"] = $input;
        $res[] = $addData;

        $addData = array();

        $cmsEditMode = $GLOBALS[cmsSettings][editMode];
        switch ($cmsEditMode) {
            case "onPage2" :
                break;
            default :
                $folder = "images/";
                $div = array();
                $div[divname] = "cmsImageSelector";
                $div[style] = "height:0px;background-color:#bbb;visible:none;overflow:hidden;";
                $div[folderName] = $folder;
                //$div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";

                $div[content] = cmsImage_selectList($folder);
                $addData["div"] = $div;
                $res[] = $addData;
        }



        //if ($imageId>0) {
            $editType = $editContent[type];
            // if ($editType == "image") {
                $addData = array();
                $addData["text"] = "Breite";
                $addData["input"] = "<input type='text'  name='editContent[data][imageWidth]' style='width:100px;' value='$data[imageWidth]' > (px,%,auto)";
                $res[] = $addData;
            //}

            // if ($editType == "image") {
                $addData = array();
                $addData["text"] = "Höhe";
                $addData["input"] = "<input type='text' name='editContent[data][imageHeight]' style='width:100px;' value='$data[imageHeight]' > (px)";
                $res[] = $addData;
            // }
            
            $addData = array();
            $addData["text"] = "Verhältnis";
            if ($editContent[data][ratio]) $checked="checked='checked'"; else $checked="";
            $addData["input"] = "<input type='checkbox' $checked name='editContent[data][ratio]'  value='1' >";
            $res[] = $addData;

            $addData = array();
            $addData["text"] = "Horizonatle Ausrichtung";
            if (!$data[hAlign]) $data[hAlign] = "left";
            $addData["input"] = cmsEdit_HorizontalAlign("editContent[data][hAlign]",$data[hAlign]);
            $res[] = $addData;

            $addData = array();
            if (!$data[vAlign]) $data[vAlign] = "top";
            $addData["text"] = "Vertikale Ausrichtung:";
            $addData["input"] = cmsEdit_VerticalAlign("editContent[data][vAlign]",$data[vAlign]);
            $res[] = $addData;

        // }
        return $res;

    }



}


function cmsType_image_class() {
    if ($GLOBALS[cmsTypes]["cmsType_image.php"] == "own") $imageClass = new cmsType_image();
    else $imageClass = new cmsType_image_base();
    return $imageClass;
}


function cmsType_image($contentData,$frameWidth) {
    $imageClass = cmsType_image_class();
    $imageClass->image_show($contentData,$frameWidth);
}



function cmsType_image_editContent($editContent,$frameWidth) {
    $imageClass = cmsType_image_class();
    return $imageClass->image_editContent($editContent,$frameWidth);
}





?>
