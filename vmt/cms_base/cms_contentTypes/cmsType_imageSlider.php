<?php // charset:UTF-8
class cmsType_imageSlider_base extends cmsType_contentTypes_base {
    function getName (){
        return "Bild Slider";
    }
    
    function imageSlider_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];
        $clickAction = $data[clickAction];

        if (!$imgRow) $imgRow = 1;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = ($frameWidth - ($imgRow-1)*$imgRowAbs ) / $imgRow;
        
        $imgList = $data[imgList];

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        $divData[clickAction] = $clickAction;//"previewImage";
        $divData[imageSlider] = $imgList;
        
        div_start("imageSlider",$divData);

//         echo ("frame $frameWidth $rowWidth <br />");
//         echo ("Data = $imgRow $imgRowAbs $imgColAbs <br />");
//         echo ("RowWidth = $rowWidth <br />");
//         echo ("ImageList = $imgList <br />");

        
        $delimiter = ",";
        if (!is_null(strpos($imgList,"|"))) $delimiter = "|";
        
        $imgIdList = explode($delimiter,$imgList);
        $imgDataList = array();

        for ($i=0;$i<count($imgIdList);$i++) {
            $imgStr = $imgIdList[$i];
            if ($imgStr) {
                $imageId = intval($imgStr);
                //  echo ("Show Image with id $imageId <br>");
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    $imgDataList[] = $imageData;
                } else {
                   echo ("Bild mit Id $imageId nicht gefunden <br />");
                }
            }
        }
        

        $absHor = 2;
        $absVer = 0;
        
        $ratio = 0.7; // 5 / 8;
        if ($ratio) $imageHeight = floor(($rowWidth-(2*$absHor)) / $ratio);
        $nr = 0;

        $slider = "bxSlider";
        // $slider = "bxSlider";
        $sliderShowCaption = 0;
        
        
        /// SLIDER
        if (count($imgDataList)) {
            // echo ("<div class='slider_highlight content_box box-shadow' $style  >");

            $frameWidth = $rowWidth;

            $imageSize = $frameWidth-(2*$absHor);
            $frameHeight = $imageHeight-(2*$absVer);

            // echo ("Type = $slider Size $frameHeight x $frameHeight<br />");

            $innerContainer = 1;
            switch ($slider) {
                case "bxSlider" :
                    // http://bxslider.com
                    $mainId = "slider-bxSlider";
                    $mainClass = "slider-bxSlider";
                    // $mainStyle = "margin-top:20px;";
                    $innerContainer = 0;
                    break;
                
                case "slides" :
                    // http://www.slidesjs.com/
                    $mainId = "slider-slides";
                    $mainClass = "slider-slides";    
                    $mainStyle = "margin-top:20px;";

                    $containerId = "";
                    $containerClass = "slides_container";

                    $slideFrameId = "";
                    $slideFrameClass = "slide";
                    $slideFrameStyle = "width:".$frameWidth."px;height:".$frameHeight."px;";

                    $sliderCaptionClass = "caption";
                    $sliderCaptionId = "";
                    $sliderCaptionStyle = "bottom:0px;";
                    $sliderCaptionStyle .= "z-index:500;position:absolute;bottom:-35px;left:0;height:30px;padding:5px 20px 0 0;background:#fff;background:rgba(1.0,1.0,1.0,.5);width:100%;font-size:1.3em;line-height:1.33;color:#333;border-top:1px solid #000;text-shadow:none;";

                    break;
                case "coda" :
                    $mainClass = "coda-slider";
                    $mainId = "slider-id";
                    break;

            }

            // Start Slider Frame
            echo ("<div class='$mainClass' id='$mainId' style='width:".$frameWidth."px;height:".$frameHeight."px;");
            if ($mainStyle) echo ("$mainStyle");
            echo ("' > ");


            // echo ("<div class='coda-slider-wrapper' id='slider-id-wrapper' style='width:".$frameWidth."px;height:".$frameHeight."px;'>");
               //<div id="slider-id" class="coda-slider"> <div class="panel-container" style="margin-left: 0px; width: 400px;"><div style="background:url(images/articles/2012-06/thumbs/_400_300_MaxGiesinger.jpg);" class="slider_content panel"><div class="panel-wrapper"><div class="slider_info boxlink" style="opacity: 0.7;">Freitag, den 12.10.2012 - 20:30<br /><b>Stefan Singt</b><br />Letzter Auftritt für Immer<div class="hidden_url"><a href="kalender.php?dateId=1776">Link zum Artikel</a></div></div></div></div></div></div><div class="coda-nav"><ul style="margin-right: 0px; float: right;"><li class="tab1"><a href="#1" class="current">Freitag, den 12.10.2012 - 20:30Stefan SingtLetzter Auftritt für ImmerLink zum Artikel</a></li></ul></div></div>")

            // Innser Container
            if ($innerContainer) {
                echo ("<div ");
                if ($containerId) echo ("id='$containerId' ");
                if ($containerClass) echo ("class='$containerClass' ");
                echo (">");
            }

            $contentList = array();
            
            
            $sliderFrameStyle .= "padding:".$absVer."px ".$absHor."px;";
            
            for ($i = 0; $i<count($imgDataList); $i++) {
                $imgData = $imgDataList[$i];
                $imageId = $imgData[id];
                // show_array($imgData);
                $showData[frameWidth] = $frameWidth-(2*$absHor);
                $showData[frameHeight] = $imageHeight;
                if ($ratio) $showData[ratio] = $ratio;
                $subTitle = $imgData[subTitle];
                // $showData[out] = "url";
                
                $imgStr = cmsImage_showImage($imgData, $frameWidth-(2*$absHor), $showData);
                
                
                $contentStr = "";

                
            
                // Create Slide Container
                $contentStr .="<div ";
                if ($sliderFrameId) $contentStr .="id='$sliderFrameId' ";
                if ($sliderFrameClass) $contentStr .="class='$slideFrameClass' ";
                if ($sliderFrameStyle) $contentStr .="style='$sliderFrameStyle' ";
                $contentStr .=">";
                $contentStr .= $imgStr;
                //$contentStr .= "<img src='$imgStr' width='".($frameWidth-(2*$absHor))."px' height='".$frameHeight."px' title='$subTitle' >";

                $contentStr .="</div>";
                
                $contentList["image_".$imageId] = $contentStr;
            }
            
//            if ($innerContainer) {
//                $contentStr .="</div>";
//            }

            // end of Slider Frame
            // $contentStr .="</div>";
            
            //echo ("<img src='$imgStr' >");
            
        } else {
            echo ("<div class='cmsContentNoData'>");
            echo ("Keine Bilder gefunden <br />");
        }
        
        
         if (count($contentList)) {
            //show_array($contentList);
            $showData = array();
            $showData[mainDiv] = $divData;
            $showData[startFrame] = $startFrame;
            $showData[loop] = 0;
            $showData[navigate] = 0;
            $showData[pager] = 0;
            if ($contentData[data][loop] == "on") $showData[loop] = 1;
            if ($contentData[data][navigate] == "on") $showData[navigate] = 1;
            if ($contentData[data][pager] == "on") $showData[pager] = 1;
            
            $direction = "horizontal"; // horizontal  vertical  fade
            if ($contentData[data][direction]) $direction = $contentData[data][direction];
            $showData[direction] = $direction;
            
            $speed = 500;
            $pause = 4000;
            if (intval($contentData[data][pager])) $speed = $contentData[data][pager];
            if (intval($contentData[data][pause])) $pause = $contentData[data][pause];
            $showData[speed] = $speed;
            $showData[pause] = $pause;
            $width = $frameWidth;
            $name = "calendarSlider_".$contentData[id];
           
            cmsSlider($slider, $name, $contentList, $showData, $width, $height);
            // show_array($contentData[data]);
        }
        echo ("</div>");
        
        
          
     

        div_end("imageSlider","before");
        
        switch ($clickAction) {
            case "fullPreview" :
                //                div_start("imagePreviewWindow",array("cmsName"=>$GLOBALS[cmsName]));
                //                div_end("imagePreviewWindow");
                //
                //                div_start("imagePreviewContent");
                //                echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                //                div_end("imagePreviewContent");
                break;

            case "framePreview":
                div_start("imagePreviewFrame");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end("imagePreviewFrame");
                break;
        }


        //div_start("imagePreviewWindow open");
        
    }

    function imageSlider_editContent($editContent,$frameWidth) {
        $res = array();
        $res[view] = array();
        
        $addData = array();
        $addData["text"] = "Wechsel";
        $direction = $editContent[data][direction];
        $input  = $this->slider_direction_select($direction,"editContent[data][direction]",array());
        $addData["input"] = $input;
        $res[view][] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Auto Loop";
        $loop = $editContent[data][loop];
        $checked = "";
        if ($loop) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][loop]' $checked >";
        $res[view][] = $addData;
        
        $addData = array();
        $addData["text"] = "Zeit für Bild in ms";
        $addData["input"] = "<input name='editContent[data][pause]' style='width:100px;' value='".$editContent[data][pause]."'>";
        $res[view][] = $addData;
        
        $addData = array();
        $addData["text"] = "Zeit für Wechsel in ms";
        $addData["input"] = "<input name='editContent[data][speed]' style='width:100px;' value='".$editContent[data][speed]."'>";
        $res[view][] = $addData;
        
        $addData = array();
        $addData["text"] = "Navigation";
        $navigate = $editContent[data][navigate];
        $checked = "";
        if ($navigate) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][navigate]' $checked >";
        $res[view][] = $addData;
        
        $addData = array();
        $addData["text"] = "Einzelauswahl";
        $pager = $editContent[data][pager];
        $checked = "";
        if ($pager) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][pager]' $checked >";
        $res[view][] = $addData;
        

        $addData = array();
        $addData["text"] = "Anzahl Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$editContent[data][imgRow]."'>";
        $addData["input"] = $input;
        $res[view][] = $addData;

        $addData = array();
        $addData["text"] = "Abstand Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$editContent[data][imgRowAbs]."'>";
        $addData["input"] = $input;
        $res[view][] = $addData;

        $addData = array();
        $addData["text"] = "Abstand Zeilen";
        $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$editContent[data][imgColAbs]."'>";
        $addData["input"] = $input;
        $res[view][] = $addData;


        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];

        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        

        $imageIdStr = $editContent[data][imgList];
        if ($_POST[editContent][data][imgList]) {
            $imageIdStr = $_POST[editContent][data][imgList];
        }

        $imgIdList = explode(",",$imageIdStr);


        /// ACTION //////////////
        $actionChange = 0;
        /// ACTION - DELETE

        $delList = $_POST[deleteFromList];
        if ($delList) {
            // echo ("DeleteFromList<br />");
            $newIdList = array();
            for ($i=0;$i<count($imgIdList);$i++) {
                $imageId = $imgIdList[$i];
                if ($delList[$imageId] == "x") {
                    echo ("Delete $imageId from List <br />");
                } else {                    
                    $newIdList[] = $imageId;
                }
            }
            $imgIdList = $newIdList;
            $actionChange = 1;
        }

        /// ACTION - MOVE UP
        $moveUp = $_POST[moveUpInList];
        if ($moveUp) {
            echo ("Move Up<br />");
            $actionChange = 1;
        }

        /// ACTION - MOVE UP
        $moveDown = $_POST[moveDownInList];
        if ($moveDown) {
            echo ("Move Down<br />");
            $actionChange = 1;
        }

        // ACTION - IMAGE ADD
        $imageAdd = $_POST[imageAdd];
        if ($imageAdd) {
            echo ("<h1> IMAGE ADD $imageAdd</h1>");
            $newIdList = array();
            $found = 0;
            for ($i=0;$i<count($imgIdList);$i++) {
                $imageId = $imgIdList[$i];
                if ($imageId == $imageAdd) { // allready in List -> dont Add
                    $found = 1;
                }
            }

            if ($found == 0) {
                echo ("Add Image with Id $imageAdd<br />");
                $imgIdList[] = $imageAdd;
            }            
            $actionChange = 1;
        }

        /// ACTION - CREATE NEW LIST
        if ($actionChange) {
            $imageIdStr  = "";
            for ($i=0;$i<count($imgIdList);$i++) {
                $imageId = $imgIdList[$i];
                if ($i>0) $imageIdStr .= ",";
                $imageIdStr .= $imageId;
            }
            echo "NEW ImageIdStr = '$imageIdStr' <br /> ";
        }



        $addData["text"] =  "Bild-Liste";
        $addData["input"] = "Trulla";
        $div = array();
        $div[divname] = "cmsImageList";
        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:visible;";
        // $div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";
        
        $showData = array();
        $showData[width] = $frameWidth - 4;
        $showData[imageAdd] = 1;
        $showData[imageUpload] = 1;
        $showData[delimiter] = ",";
        $showData[imageFolder] = "images/";
        $showData[imageSortAble] = 1;
        $showData[imageDeleteAble] = 1;
        $showData[showMode] = "block"; // array("line","block")[1];
        $showData[dataName] = "editContent[data][imgList]";
        
        $div[content] = $this->editContent_imageList($imageIdStr,$showData);
        

        $addData["div"] = $div;
        $res[imageSlider][] = $addData;




       
//        $addData["text"] = "BildListe";
//        $addData["input"] = "<input name='editContent[data][imgList]' style='width:100%;' value='".$imageIdStr."'>";
//        $res[imageSlider][] = $addData;
//
//      
//        $addData["text"] =  "Bild-Liste";
//        $addData["input"] = "Trulla";
//        $div = array();
//        $div[divname] = "cmsImageList";
//        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:auto;height:200px;";
//        //$div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";
//        $div[content] = $this->imageSlider_ImageList($imageIdStr);
//
//
//
//        $addData["div"] = $div;
//        $res[imageSlider][] = $addData;
//
//
//
//
//        switch ($GLOBALS[cmsSettings][editMode]) {
//            case "onPage2" :
//                $imageClickClass = "cmsImageSelectModul";
//                break;
//            default:
//                $imageClickClass = "cmsImageSelect";
//        }
//        // ImageAdd
//        $addData = array();
//        $addData["text"] =  "Bild hinzufügen";
//        $img = "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='90px' height='90px' class='$imageClickClass'> ";
//        $imageId = intval($product[image]);
//        if ($imageId > 0) {
//            $imageData = cmsImage_getData_by_Id($imageId);
//            if (is_array($imageData)) {
//                $img = cmsImage_showImage($imageData,100,array("class"=>"$imageClickClass"));
//            }
//        }
//        $addData["input"] = $img."<input type='text' class='cmsImageId' action='submit' formName='cmsEditContentForm' style='width:30px;' name='imageAdd' value='' onChange='submit()' >";
//        $res[imageSlider][] = $addData;
//
//        $div = array();
//        $div[divName] = "cmsImageSelector";
//        $div[folderName] = "images/";
//        $div[style] = "height:0px;background-color:#bbb;visible:none;overflow:hidden;";
//        //$div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";
//        $div[content] = cmsImage_selectList($folder);
//        $addData["div"] = $div;
//        $res[imageSlider][] = $addData;

       

        return $res;
    }

    function imageSlider_ImageList($imgList){
        $divCont = "";
        $imgIdList = explode(",",$imgList);
        for ($i=0;$i<count($imgIdList);$i++) {

            $imageId = intval($imgIdList[$i]);
            if ($imageId > 0) {
                $divCont.= div_start_str("imageSliderLine","width:100%;height:30px;");


                $imgData = cmsImage_getData_by_Id($imageId);
                $imgName = $imgData[name];
                $imgWidth = $imgData[width];
                $imgHeight = $imgData[height];
                $imgPath   = $imgData[orgpath];

                $imgPath = substr($imgPath,  strpos($imgPath, "/")+1);

                // FileImage
                $divCont .= div_start_str("imageSliderLine_Name","float:left;width:42px;overflow:hidden;");
                $divCont .= cmsImage_showImage($imgData, 40, array("frameWidth"=>40,"frameHeight"=>30));
                $divCont .= div_end_str("imageSliderLine_Name");


                // FileName
                $divCont .= div_start_str("imageSliderLine_Name","float:left;width:100px;overflow:hidden;");
                $divCont .= $imgName;
                $divCont .= div_end_str("imageSliderLine_Name");


                // FileSize
                $divCont .= div_start_str("imageSliderLine_Size","float:left;width:100px;overflow:hidden;");
                $divCont .= $imgWidth."x".$imgHeight;
                $divCont .= div_end_str("imageSliderLine_Size");

                // FilePath
                $divCont .= div_start_str("imageSliderLine_Path","float:left;width:30%;overflow:hidden;");
                $divCont .= $imgPath;
                $divCont .= div_end_str("imageSliderLine_Path");

                // FileAction
                $divCont .= div_start_str("imageSliderLine_Action","float:left;width:30%px;overflow:hidden;");
                //$divCont .= "<a class='cmsContentHeadButton' href='#'>x</a>";
                $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='x' name='deleteFromList[$imageId]' >";
                if ($i>0) $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='&#8593;' name='moveUpInList[$imageId]' >";
                else $divCont .= "___";
                $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='&#8595;' name='moveDownInList[$imageId]' >";

                //$divCont .= "<a class='cmsContentHeadButton' href='#'>&#8593;</a>";
                // $divCont .= "<a class='cmsContentHeadButton' href='#'>&#8595;</a>";
                $divCont .= div_end_str("imageSliderLine_Action");

              

                $divCont.= div_end_str("imageSliderLine","before");

               
               

               // $divCont .= "Image with ID $imageId <br />";
            }
        }

        return $divCont;
    }

    

    function clickAction_getList() {
        $res = array();
        $res["fullPreview"] = "Vorschau auf ganzer Seite";
        $res["framePreview"] = "Vorschau in Rahmen";

        $ownList = $this->clickAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function clickAction_getOwnList() {
        $res = array();
        return $res;
    }

}


function cmsType_imageSlider_class() {
    if ($GLOBALS[cmsTypes]["cmsType_imageSlider.php"] == "own") $imageSliderClass = new cmsType_imageSlider();
    else $imageSliderClass = new cmsType_imageSlider_base();
    return $imageSliderClass;
}


function cmsType_imageSlider($contentData,$frameWidth) {
    //  echo ("imageSlider");
    if ($GLOBALS[cmsTypes]["cmsType_imageSlider.php"] == "own") $imageSliderClass = new cmsType_imageSlider();
    else $imageSliderClass = new cmsType_imageSlider_base();

    $imageSliderClass->imageSlider_show($contentData,$frameWidth);
}



function cmsType_imageSlider_editContent($editContent,$frameWidth) {
    if ($GLOBALS[cmsTypes]["cmsType_imageSlider.php"] == "own") $imageSliderClass = new cmsType_imageSlider();
    else $imageSliderClass = new cmsType_imageSlider_base();

    return $imageSliderClass->imageSlider_editContent($editContent,$frameWidth);
}


?>
