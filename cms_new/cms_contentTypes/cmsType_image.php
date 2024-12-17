<?php // charset:UTF-8

class cmsType_image_base extends cmsClass_content_show { // extends cmsType_contentTypes_base {
    function getName() {
        return "Bilder";
    }
    
    function setMainClass($mainClass=0) {
        if (is_object($mainClass)) {
            $this->mainClass = $mainClass;
            // echo ("<h1>Set Main Class in ".$this->getName()."</h1>");
        }
    }
    
    
    function init_own($contentData,$editContent,$frameWidth,$textData,$editText) {
        $this->contentData = $contentData;
        $this->editContent = $editContent;
        $this->frameWidth = $frameWidth;
        $this->textData = $textData;
        $this->editText = $editText;
        $this->contentId = $this->contentData[id];
    }
    
    
    function contentType_show() {
        if (is_object($this->mainClass)) $useClass=$this->mainClass;
        else $useClass = $this;


        $res = $useClass->contentShow_image($useClass->frameWidht);
        return $res;
    }
    
    
    function image_show($contentData,$frameWidth) {

        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "single";
        echo ("IMAGE SHOW $viewMode <br />");
        switch ($viewMode) {
            case "single" :
                $res =  $this->image_showSingle($contentData,$frameWidth);
                break;
            case "table" :
                $res = $this->image_showTable($contentData,$frameWidth);
                break;
            
            case "slider" :
                $res = $this->image_showSlider($contentData,$frameWidth);
                break;
            
            default:
                echo ("unkown ViewMode");
                
        }
        return $res;
    }
    
    function image_showSingle($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        
        
        
        // show_array($data);
        $res = array();

        $image = $data[image];
        if (!$image) $image = $data[imageId];
        if ($image) {
            if (intval($image)) $imageId = intval($image);
            else {
                $imageList = explode("|",$image);
                if (count($imageList)>1) $imageId = intval($imageList[1]);
            }
            
        }
        
        if ($imageId <=  0) {
            echo ("<div class='cmsContentNoData'>");
            echo ("Kein Bild!");
            echo ("</div>");
            return 0;
        }

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

        $ratio = $data[ratio];
        if ($ratio) {
            $ratioX = intval($data[ratioX]);
            $ratioY = intval($data[ratioY]);

            if ($ratioX AND $ratioY) {
            } else {
                $ratioX = 1;
                $ratioY = 1;
            }
            $ratio = $ratioX / $ratioY;
            if ($imageWidth) $imageHeight = floor ($imageWidth / $ratio);
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
        $showData[resize] = $data[resize];
        $showData[crop] = $data[crop];
        $showData[ratio] = $ratio;
        //$showData[resize] = $data[resize];

        echo ("<h1>HIER</h1>");
        
        
       // show_array($contentData);
        $wireFrameOn = $data[wireframe];
        $wireframeState = $this->wireframeState;
        if ($wireFrameOn AND $wireframeState) {

            // echo ("<h1> Wireframe State is $wireframeState </h1>");
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            $wireframeImage = $wireframeData[image];
            $wireframeImageText = $wireframeData[imageText];
            // show_array($wireframeData);
        }
        // show_array($showData);

        $zoom = $data[zoom];


        if ($wireframeImage) {

            $width = $imageWidth;
            if (!intval($width)) $width = $frameWidth;
            $height = $imageHeight;
            if (!intval($height)) $height = floor($width / 4 * 3);
            // echo ("$width $height <br>");

            if ($zoom) {
                $bigWidth = 800;
                $bigHeight = floor($bigWidth * $height / $width);
                $bigImageStr = cmsWireframe_image($bigWidth,$bigHeight,"#ff00ff");
                //echo ($bigImageStr."<br>");

                echo ("<a href='$bigImageStr' class='zoomimage'>");
            }

            if ($wireframeImageText) {
                $out = cmsWireframe_frameStart_str($width, $height,"zoom_Div");
                // $out .= "<a href='$bigImageStr' class=''>$wireframeImageText</a>";
                $out .= $wireframeImageText;
                $out .= cmsWireframe_frameEnd_str();
                echo ($out);

            } else {
                $imgStr = cmsWireframe_image($width,$height,"#ff00ff");
                // if ($zoom) echo ("<a href='$bigImageStr' class='zoomimage'>");
                echo ("<img src='$imgStr' class='noBorder' />");
                // if ($zoom) echo ("</a>");
            }

            if ($zoom) {
                echo ("</a>");
            }
            $imageText = $wireframeData[imageText];
            /*if ($imageText) {




                cmsWireframe_frameStart($width,$height,"wireframeImageFrame");
                echo ($imageText);
                cmsWireframe_frameEnd();
            }*/
        } else {



            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $img = cmsImage_showImage($imageData,$frameWidth,$showData);

                if ($zoom) {
                    $bigWidth = 1000;
                    $bigShowData = array("out"=>"url");
                    $bigImage = cmsImage_showImage($imageData,$bigWidth,$bigShowData);


                    echo ("<a href='$bigImage' class='zoomimage'>");
                }

                echo ($img);

                if ($zoom) {
                    echo ("</a>");
                }
            }
        }
        div_end("cmsImageType");
        return $showData;

    }
    
    function image_showTable($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];
        $clickAction = $data[clickAction];

        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = floor(($frameWidth - ($imgRow-1)*$imgRowAbs ) / $imgRow);
        
        $imgRowAbsLast = $frameWidth - ($imgRow*$rowWidth) - (($imgRow-2)*$imgRowAbs);
        
        // echo ("last = $imgRowAbs $imgRow $rowWidth $imgRowAbs <br>");
        
        $imgList = $data[image];
        if (!$imgList) $imgList = $data[imgList];
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        
        $wireFrameOn = $data[wireframe];
        $wireframeState = $this->wireframeState;
        if ($wireFrameOn AND $wireframeState) {

            // echo ("<h1> Wireframe State is $wireframeState </h1>");
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            $wireframeImage = $wireframeData[image];
            $wireframeImageText = $wireframeData[imageText];
            // show_array($wireframeData);
        }
        
        
        
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

        
        $zoom = $data[zoom]; 
        
        $ratio = $data[ratio];
        if ($ratio) {
            $ratioX = intval($data[ratioX]);
            $ratioY = intval($data[ratioY]);
            
            if ($ratioX AND $ratioY) {
            } else {
                $ratioX = 1;
                $ratioY = 1;
            }
            $ratio = $ratioX / $ratioY;
        }
        
        // $ratio = 4/3;
        $nr = 0;
        $lnr = 0;
        if (count($imgDataList)) {
            div_start("imageList",$divData);
            for ($i = 0; $i<count($imgDataList); $i++) {
                $imageId = $imgDataList[$i];
                if ($wireframeImage) {
                    $width = $rowWidth;
                    if ($ratio) {
                        $height = floor($width / $ratio);
                    } else {
                        $height = floor($width / 4 * 3);
                    }
                    $imgStr = "";
                    if ($wireframeImageText) {
                        $imgStr .= cmsWireframe_frameStart_str($width, $height,"zoom_Div");
                        // $out .= "<a href='$bigImageStr' class=''>$wireframeImageText</a>";
                        $imgStr .= cmsWireframe_text($wireframeImageText,array("nr"=>$i+1,"id"=>$imageId));                    
                        $imgStr .= cmsWireframe_frameEnd_str();
                    } else {
                        $wireImage = cmsWireframe_image($width, $height);
                        $imgStr = "<image src='$wireImage' class='noBorder' />";
                       
                    }
                    
                    if ($zoom ) {
                        $bigWidth = 800;
                        $bigHeight = floor($bigWidth * $height / $width);
                        $bigImage = cmsWireframe_image($bigWidth, $bigHeight);                        
                    }
                    
                } else {
                
                    $imgData = $imgDataList[$i];
                   
                    $fn = "Image ".$i;
                   
                    $showData = array();
                    $showData[frameWidth] = $rowWidth;
                    if ($ratio) {
                        $showData[frameHeight] = floor($rowWidth / $ratio);
                        $showData[ratio] = $ratio;
                    }
                    $showData[crop]   = $data[crop];
                    
                    $showData[vAlign] = $data[vAlign];
                    $showData[hAlign] = $data[hAlign];
                    $showData[resize] = $data[resize];
                    // show_array($showData);
                    $imgStr = cmsImage_showImage($imgData, $rowWidth, $showData);
                    // 
                    if ($zoom) {
                        $bigImage = cmsImage_showImage($imgData, 800,array("out"=>"url"));                        
                    }
                }
                
                if ($imgStr) {
                    $nr++;
                    if ($nr == 1) {
                        $lnr ++;
                        $lineStyle = "";
                        if ($lnr > 1) $lineStyle = "margin-top:".$imgColAbs."px;";
                        
                        //div_start("imgListLine","margin:0 0 ".$imgColAbs."px 0;");                        
                        div_start("imgListLine",$lineStyle);                        
                    }
                    $divData = array();
                    $divData[style] = "width:".$rowWidth."px;float:left;height:".$showHeight."px;";
                    if ($nr < $imgRow) {
                        if ($nr == $imgRow - 1) $divData[style].="margin-right:".$imgRowAbsLast."px";
                        else $divData[style].="margin-right:".$imgRowAbs."px";
                    }
//                    if ($nr < $imgRow) $divData[style].="margin-right:".$imgRowAbs."px";
//                    if ($nr = $imgRow-1) 
                    
                    // $divData[imageId] = $imgData[id];
                    div_start("imgListImageBox",$divData);
                    

                    if ($zoom) {
                        echo ("<a href='$bigImage' class='zoomimage'>");
                    }

                    echo ($imgStr);
                    if ($zoom) {
                        echo ("</a>");
                    }
                    div_end("imgListImageBox");

                    if ($nr == $imgRow) { // close Line
                        $nr = 0;
                        div_end("imgListLine","before");
                    }
                }
            
            }

            if ($nr != 0) {
                div_end("imgListLine","before");
            }




            div_end("imageList","before");
        } else {
            echo ("<div class='cmsContentNoData'>");
            echo ("Kein Bilder in Liste!");
            echo ("</div>");
        }

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
    }

    
    function image_showSlider($contentData,$frameWidth) {        
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
        
        $imgList = $data[image];
        if (!$imgList) $imgList = $data[imgList];

        
        $absHor = 0;
        $absVer = 0;
        
        
        $zoom = $data[zoom];
        $sliderWidth = $frameWidth;
        
        $crop = $data[crop];
        $ratio = $data[ratio];
        
        if ($ratio) {
            $ratioX = intval($data[ratioX]);
            $ratioY = intval($data[ratioY]);
            
            if ($ratioX AND $ratioY) {                
            } else {
                $ratioX = 1;
                $ratioY = 1;
            }
            $ratio = $ratioX / $ratioY;    
            $sliderHeight = floor($sliderWidth / $ratio);
            // echo ("<h1> Slider $ratio $ratioX : $ratioY $sliderHeight </h1>");
        }
        
        // $ratio = 0.7; // 5 / 8;
        if ($sliderHeight) $imageHeight = $sliderHeight;
       
        
        
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
        
        $zoom = $data[zoom];
        
        $wireFrameOn = $data[wireframe];
        $wireframeState = $this->wireframeState;
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            $wireframeImage = $wireframeData[image];
            $wireframeImageText = $wireframeData[imageText];
        }


        for ($i=0;$i<count($imgIdList);$i++) {
            $imgStr = $imgIdList[$i];
            if ($imgStr) {
                $imageId = intval($imgStr);
                
                if ($wireframeImage) {
                    $wireOut = "";
                
                    $width = $imageWidth;
                    if (!intval($width)) $width = $frameWidth;
                    $height = $imageHeight;
                    if (!intval($height)) $height = floor($width / 4 * 3);
                      //echo ("$width $height <br>");

                    if ($zoom) {
                        $bigWidth = 800;
                        $bigHeight = floor($bigWidth * $height / $width);
                        $bigImageStr = cmsWireframe_image($bigWidth,$bigHeight,"#ff00ff");
                        

                        $wireOut .= "<a href='$bigImageStr' class='zoomimage'>";
                    }

                    if ($wireframeImageText) {
                        $wireOut .= cmsWireframe_frameStart_str($width, $height,"zoom_Div");
                        // $out .= "<a href='$bigImageStr' class=''>$wireframeImageText</a>";
                        $wireOut .= cmsWireframe_text($wireframeImageText,array("nr"=>$i,"id"=>$imageId));                    
                        $wireOut .= cmsWireframe_frameEnd_str();
                    } else {
                        $imgStr = cmsWireframe_image($width,$height,"#ff00ff");
                        // if ($zoom) echo ("<a href='$bigImageStr' class='zoomimage'>");                    
                        $wireOut .= "<img src='$imgStr' class='noBorder' />";
                        // if ($zoom) echo ("</a>");
                    }

                    if ($zoom) {
                        $wireOut .= "</a>";
                    }
                    $imgDataList[] = $wireOut;
                } else {
                
                
                    //  echo ("Show Image with id $imageId <br>");
                    $imageData = cmsImage_getData_by_Id($imageId);
                    if (is_array($imageData)) {
                        $imgDataList[] = $imageData;
                    } else {
                       echo ("Bild mit Id $imageId nicht gefunden <br />");
                    }
                }
            }
        }
        
        $absHor = 0;
        $absVer = 0;
        
        // $ratio = 0.7; // 5 / 8;
        
        $nr = 0;

     
        
        
        /// SLIDER
        if (count($imgDataList)) {
            // echo ("<div class='slider_highlight content_box box-shadow' $style  >");

            $frameWidth = $rowWidth;

            $imageSize = $frameWidth-(2*$absHor);
            if ($imageHeight) $frameHeight = $imageHeight-(2*$absVer);

   
            $contentList = array();
            
            
            $sliderFrameStyle .= "padding:".$absVer."px ".$absHor."px;";
            
            $showData = array();
            $showData[frameWidth] = $frameWidth-(2*$absHor);
            $showData[frameHeight] = $imageHeight;
            $showData[ratio] = $ratio;                    
            $showData[crop] = $crop;
            $showData[hAlign] = $data[hAlign];            
            $showData[vAlign] = $data[vAlign];
            $showData[resize] = $data[resize];
            // show_array($showData);
            
            for ($i = 0; $i<count($imgDataList); $i++) {
                $imgData = $imgDataList[$i];
                if (is_array($imageData)) {
                
                
                    $imageId = $imgData[id];
                    // show_array($imgData);
//                    $showData[frameWidth] = $frameWidth-(2*$absHor);
//                    $showData[frameHeight] = $imageHeight;
//                    if ($ratio) $showData[ratio] = $ratio;
//                    
//                    $showData[crop] = $crop;
//                    $showData[hAlign] = $data[hAlign];
//                    $showData[vAlign] = $data[vAlign];
                    $subTitle = $imgData[subTitle];
                    // $showData[out] = "url";

                    $imgStr = cmsImage_showImage($imgData, $frameWidth-(2*$absHor), $showData);
                   

                    $contentStr = "";
                    if ($zoom) {
                        $imgStrBigStr = cmsImage_showImage($imgData, 800,array("out"=>"url"));
                        $contentStr .= "<a class='zoomimage' href='$imgStrBigStr'>";
                    }

                    $contentStr .= $imgStr;
                    if ($zoom) {
                        $contentStr .= "</a>";
                    }

                    $contentList["image_".$imageId] = $contentStr;
                    
                } else {
                    $contentList["image_".$i] = $imgData;
                }
            }
          
            
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
            if (intval($data[speed])) $speed = intval($data[speed]);
            if (intval($data[pause])) $pause = intval($data[pause]);
            
            $showData[speed] = $speed;
            $showData[pause] = $pause;
            // echo ("PAUSE =$pause speed=$speed <br>");
            $width = $frameWidth;
            $name = "calendarSlider_".$contentData[id];
           
            slider::show($name,$contentList,$contentData,$showData);
            // cmsSlider($slider, $name, $contentList, $showData, $width, $height);
            // show_array($contentData[data]);
        }
     
        
        
          
     

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
    
    function image_getShowData($contentData, $frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "single";
        
        $res = array();
        $res[viewMode] = $viewMode;
        $res[width] = $frameWidth;
        
        $ratio = $data[ratio];
        if ($ratio) {
            $ratioX = intval($data[ratioX]);
            $ratioY = intval($data[ratioY]);

            if ($ratioX AND $ratioY) {
            } else {
                $ratioX = 1;
                $ratioY = 1;
            }
            $ratio = $ratioX / $ratioY;
            if ($frameWidth) $res[height] = intval($frameWidth / $ratio);            
        } else {
            $resize = $data[resize];
            $image = $data[image];
            if (!$image) $image = $data[imageId];
            if ($image) {
                if (intval($image)) $imageId = intval($image);
                else {
                    $imageList = explode("|",$image);
                    if (count($imageList)>1) $imageId = intval($imageList[1]);
                }

            }
            if ($imageId) {
                $imageData = cmsImage_getData_by_Id($imageId);
                $imageWidth = $imageData[width];
                $imageHeight = $imageData[height];                
                if ($resize) {
                    $ratio = $imageWidth / $imageHeight;
                    if ($frameWidth) $res[height] = intval($frameWidth / $ratio);   
                } else {
                    
                    echo ("img $imageWidth x $imageHeight px resize = $resize <br>");
                
                }
                
              
            }
             
                    
            
            
            
            
        }
        
        
        
//        switch ($viewMode) {
//            case "single" :
//                $modeRes = $this->image_getShowData_single($contentData,$frameWidth);
//                break;
//            case "table" :
//                $modeRes = $this->image_getShowData_table($contentData,$frameWidth);
//                break;
//            
//            case "slider" :
//                $modeRes = $this->image_getShowData_slider($contentData,$frameWidth);
//                break;
//            
//            default:
//                echo ("unkown ViewMode");
//                
//        }
        if (is_array($modeRes)) {
            foreach ($modeRes as $key => $value) $res[$key] = $value;            
        }
        return $res;
    }

    function contentType_editContent() {
        if (is_object($this->mainClass)){
            $useClass = $this->mainClass;
            //echo ("UseClass SET in ".$this->getName()." for ".$useClass->getName()." <br />");
        } else $useClass = $this;
        
        
        $editContent = $useClass->editContent;
        $frameWidth = $useClass->frameWidth;
        // echo ("<h1>$editContent $frameWidth </h1>");
//        $res = $useClass->image_editContent($editContent,$useClass->frameWidth);
//        return $res;
//    }
//    function image_editContent($editContent,$useClass->frameWidth) {
        $data = $useClass->editContent[data];
        
        if (!is_array($data)) $data = array();
        
        $res = array();
        
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "single";

        // echo ("VIEWMODE $data[viewMode] ".$useClass->editContent."<br>");

        $res[image][showName] = lg::lga("content","imageText");
        $res[image][showTab] = "Simple";
        
        
        $addData = array();
        $addData[text] = lg::lga("contentType_image","displayMode");
        $showData = array("empty"=>"Darstellung wählen","submit"=>1);
        $addData[input] = $this->editContent_SelectViewMode($viewMode,"editContent[data][viewMode]",$showData);
        $addData[mode] = "Simple";
        $res[image][] = $addData;
        
        
        $dontShow = array("viewMode"=>0);
        $addData = $useClass->editContent_imageSettings($dontShow); // cmsImage_editSettings($useClass->editContent,$useClass->frameWidth,$dontShow);
        for ($i=0;$i<count($addData);$i++) {
            $res[image][] = $addData[$i];
            // echo "add <br>";
        }
        
        $addList = array();
        switch ($viewMode) {
            case "single" : 
                $addList = $this->editContent_singleImageSettings();
                break;
            case "table" :
                $addList = $this->editContent_imageListSettings();
                break;
            
            case "slider" : 
                $addList = $this->editContent_imageSliderSettings();
                break;
            
            case "gallery" :
                $addList = $this->editContent_imageGallerySettings();
                break;
                
            default :
                echo ("unkown SviewMode '$viewMode' <br>");
        }
        
        if (is_array($addList)) {
            foreach ($addList as $key => $value) {
                if (is_string($key)) {
                    if (!is_array($res[$key])) $res[$key] = array();
                    foreach($value as $key2 => $value2) {
                        if (is_string($key2)) $res[$key][$key2] = $value2;
                        else $res[$key][] = $value2;
                    }
//                    for($i=0;$i<count($value);$i++) {
//                        $res[$key][] = $value[$i];
//                    }
                } else {
                    $res[image][] = $value;
                }                            
            }
        }         
      
        return $res;
    }
    
    
    function editContent_singleImageSettings() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();
        
        $res = array();
       
        switch ($useClass->editMode) {
            case "onPage2" :
                $imageClickClass = "cmsImageSelectModul";
                break;
            default:
                $imageClickClass = "cmsImageSelect";
        }

        $img = "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='90px' height='90px' class='$imageClickClass'> ";
        $image = $data[image];
        if (!$image) $image = $data[imageId];
        if ($image) {
            if (intval($image)) $imageId = intval($image);
            else {
                // echo ("Image from List $image");
                $imageList = explode("|",$image);
                if (count($imageList)>1) $imageId = intval($imageList[1]);
            }
            
        }
        
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
        $addData["text"] = lg::lga("contentType_image","selectImage");
        $input = "";
        $input .= "<div class='cmsImageDropFrame cmsDropSingle' >";
        $input .= "<div class='cmsImageFrame' >";
        $input .= $img;
        $input .= "</div>";
        $inputType = "hidden";
        
        $input .= "<input type='$inputType' class='cmsImageId' style='width:30px;' name='editContent[data][image]' value='$data[image]' />";
        $input .= "</div>";
        $addData["input"] = $input;
        $addData["mode"] = "Simple"; 
        $res[] = $addData;

        $addData = array();

        switch ($useClass->editMode) {
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
                //$res[] = $addData;
        }

        return $res;

    }

    function editContent_imageListSettings() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;

        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        
        $addData = array();
        $imgRow = $data[imgRow];
        if (!$imgRow) $imgRow = 3;
        $addData["text"] = lg::lga("contentType_image","imageRowCount"); //"Anzahl Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='$imgRow'>";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData = array();
        $imgRowAbs = $data[imgRowAbs];
        if (!$imgRowAbs) $imgRowAbs = 10;
        $addData["text"] = lg::lga("contentType_image","imageRowDist"); //"Abstand Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='$imgRowAbs'>";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[] = $addData;

        $addData = array();
        $imgColAbs = $data[imgColAbs];
        if (!$imgColAbs) $imgColAbs = 10;
        $addData["text"] = lg::lga("contentType_image","imageColumnDist"); //"Abstand Zeilen";
        $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='$imgColAbs'>";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[] = $addData;

      

        $imageIdStr = $data[image];
        if (intval($imageIdStr)) $imageIdStr = "|".$imageIdStr."|";

        if (!$imageIdStr) $imageIdStr = $data[imgList];
        if ($_POST[editContent][data][image]) {
            $imageIdStr = $_POST[editContent][data][image];
        }
        // echo ("<h1> IMAGE = $imageIdStr </h1>");
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

        $res[imageList] = array();
        $res[imageList][showName] = lg::lga("content","imageList");
        $res[imageList][showTab] = "Simple";


        $addData["text"] =  "Bild-Liste";
        $addData["input"] = "Trulla";
        $div = array();
        $div[divname] = "cmsImageList";
        $div[style] = "width:100%;visible:visible;overflow:visible;";
        // $div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";
        
        $showData = array();
        $showData[width] = $useClass->frameWidth - 4;
        $showData[imageAdd] = 1;
        $showData[imageUpload] = 1;
        $showData[delimiter] = ",";
        $showData[imageFolder] = "images/";
        $showData[imageSortAble] = 1;
        $showData[imageDeleteAble] = 1;
        $showData[showMode] = "block"; // array("line","block")[1];
        $showData[dataName] = "editContent[data][image]";
        
        $div[content] = $this->editContent_imageList($imageIdStr,$showData);
        

        $addData["div"] = $div;
        $res[imageList][] = $addData;



      
        
        // KLICK ACTION
        $res[action] = array();
        $res[action][showName] = lg::lga("content","actionTab");
        $res[action][showTab] = "More";
        $clickAction = $this->editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];

        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[action][] = $addData;
        
        return $res;    
        
    }
    
    function editContent_imageSliderSettings() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;


        
        
        
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        
        $res[slider] = array();
        $res[slider][showName] = lg::lga("content","sliderTab");
        $res[slider][showTab] = "Simple";

        $add = $useClass->editContent_sliderSettings();
        $add = slider::input($useClass->editContent);
        foreach ($add as $key => $addData) {
            $res[slider][] = $addData;
        }
    
        // KLICK ACTION

        $res[action] = array();
        $res[action][showName] = lg::lga("content","actionTab");
        $res[action][showTab] = "More";

        $clickAction = $useClass->editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];

        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $useClass->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        
        $imageIdStr = $data[image];
        if (!$imageIdStr) $imageIdStr = $data[imgList];
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
        $showData[width] = $useClass->frameWidth - 4;
        $showData[imageAdd] = 1;
        $showData[imageUpload] = 1;
        $showData[delimiter] = ",";
        $showData[imageFolder] = "images/";
        $showData[imageSortAble] = 1;
        $showData[imageDeleteAble] = 1;
        $showData[showMode] = "block"; // array("line","block")[1];
        $showData[dataName] = "editContent[data][image]";
        
        $div[content] = $useClass->editContent_imageList($imageIdStr,$showData);
        

        $addData["div"] = $div;
        $res[imageList][] = $addData;



        return $res;
   
    }
    
    function editContent_imageGallerySettings() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;

        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        $lgType = "contentType_image";
        
        $addData = array();
        $addData["text"] = lg::lga($lgType,"gallery_direction");
        $addData["input"] = $this->gallery_selectDirection($data);
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = lg::lga($lgType,"gallery_position");
        $addData["input"] = $this->gallery_selectPosition($data);
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = lg::lga($lgType,"gallery_count");
        $imgCount = $data[thumbCount];
        $input  = "<input name='editContent[data][thumbCount]' style='width:100px;' value='$imgCount'>";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = lg::lga($lgType,"gallery_distance");
        $imgCount = $data[thumbDistance];
        $input  = "<input name='editContent[data][thumbDistance]' style='width:100px;' value='$imgCount'>";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        $direction = $data[galleryDirection];
        if (!$direction) $direction = "vertical";
        $addData = array();
        if ($direction == "vertical") $addData["text"] = lg::lga($lgType,"gallery_thumbHeight");
        if ($direction == "horizontal") $addData["text"] = lg::lga($lgType,"gallery_thumbWidth"); 
        $imgSize = $data[thumbSize];
        $input  = "<input name='editContent[data][thumbSize]' style='width:100px;' value='$imgSize'>";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[] = $addData;
        
        
        $imageIdStr = $data[image];
        if (!$imageIdStr) $imageIdStr = $data[imgList];
//        if ($_POST[editContent][data][imgList]) {
//            $imageIdStr = $_POST[editContent][data][imgList];
//        }

        $imgIdList = explode(",",$imageIdStr);
        
        
        // SELCT IMAGE
        $addData = array();
        $addData["text"] =  "Bild-Liste";
        
        $div = array();
        $div[divname] = "cmsImageList";
        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:visible;";
        
        $showData = array();
        $showData[width] = $useClass->frameWidth - 4;
        $showData[imageAdd] = 1;
        $showData[imageUpload] = 0;
        $showData[delimiter] = ",";
        $showData[imageFolder] = "images/";
        $showData[imageSortAble] = 1;
        $showData[imageDeleteAble] = 1;
        $showData[showMode] = "block"; // array("line","block")[1];
        $showData[dataName] = "editContent[data][image]";
        
        $div[content] = $useClass->editContent_imageList($imageIdStr,$showData);
        

        $addData["div"] = $div;
        $res[imageList][] = $addData;
        
        return $res;
    }
    
    function gallery_selectDirection($data) {
        $list = array("vertical"=>"","horizontal"=>"");
        $lgType = "contentType_image";
        
        
        $value = $data[galleryDirection];
        // $res .= "$value / ";
        if (!$value) $value = "vertical";
        
        foreach ($list as $code => $lg) {
            $lg = lg::lga($lgType,"gallery_".$code);
            $list[$code] = $lg;
        }
        
        $res .= "<select name='editContent[data][galleryDirection]' onChange='submit()' style='width:100px;' >";
        foreach ($list as $code => $lg) {
            if ($value == $code) $select = "selected='selected'"; else  $select = "";
            $res .= "<option value='$code' $select >$lg</option>";
        }
        $res .= "</select>";
        return $res;
    }
        
    function gallery_selectPosition($data) {
        $direction = $data[galleryDirection];
        if (!$direction) $direction = "vertical";
        
        
        $value = $data[galleryPosition];
        
        
        $list = array();
        if ($direction == "vertical") {
            if (!$value) $value = "bottom";
            $list["top"] = ""; $list["bottom"] = "";
        }
        if ($direction == "horizontal") {
            if (!$value) $value = "right";
            $list["left"] = ""; $list["right"] = "";
        }
        // GET LANGUAGE
        $lgType = "contentType_image";
        foreach ($list as $code => $lg) {
            $lg = lg::lga($lgType,"gallery_".$code);
            $list[$code] = $lg;
        }
        
        $res .= "<select name='editContent[data][galleryPosition]' style='width:100px;' >";
        foreach ($list as $code => $lg) {
            if ($value == $code) $select = "selected='selected'"; else  $select = "";
            $res .= "<option value='$code' $select >$lg</option>";
        }
        $res .= "</select>";
        return $res;
    }
    

    function editContent_SelectViewMode($viewMode,$dataName,$showData=array()) {
        
        $viewList = $this->viewMode_filter_select_getOwnList(null,null);
        
        $res = "";
        $res .= "<select name='$dataName' value='$viewMode' style='min-width:200px;' ";
        
        if ($showData[submit]) {
            $res .= "onchange='submit()' ";
        }
        $res .= ">\n";
        foreach ($viewList as $type => $name) {
            if ($name) {
                if ($viewMode == $type) $select = "selected='1'";
                else $select = "";
                
                $res .= "<option $select value='$type' >$name</option>";
            }
        }
        $res .= "</select>\n";
        return $res;
        
        
    }


    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for companyListe </h1>");
        $res = array();
        $res["single"] = "Einzelbild";
        $res["list"] = null;
        $res["table"] = "Tabelle";
        $res["gallery"] = "Gallery";
        $res["slider"] = "Slider";
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
    $imageClass->show($contentData,$frameWidth);
    return $imageClass;
}



function cmsType_image_editContent($editContent,$frameWidth) {
    echo ("<h1>image_editContent</h1>");
    $imageClass = cmsType_image_class();
    return $imageClass->image_editContent($editContent,$frameWidth);
}





?>
