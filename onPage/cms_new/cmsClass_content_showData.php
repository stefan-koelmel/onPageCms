<?php // charset:UTF-8


class cmsClass_content_showData extends cmsClass_content_edit {
    
    // ********************************************************************** //
    // ** SHOW IMAGE                                                       ** //
    // ********************************************************************** //
    
    
    function contentShow_image($imgWidth=0,$out=0) {
//        if (is_object($this->mainClass)) $useClass = $this->mainClass;
//        else $useClass => $this;
        if (!$imgWidth) $imgWidth = $this->innerWidth;
        

        $viewMode = $this->contentData[data][viewMode];
        if (!$viewMode) $viewMode = "single";
        
        if (!$imgWidth) $imgWidth = $this->innerWidth; 
        
        switch ($viewMode) {
            case "single" :
                $res = $this->contentShow_image_showSingle($imgWidth,$out);
                break;
            case "table" :
                $res = $this->contentShow_image_showTable($imgWidth,$out);
                break;
            
            case "slider" :
                $res = $this->contentShow_image_showSlider($imgWidth,$out);
                break;
            
            default:
                echo ("unkown ViewMode");
                
        }
        return $res;
    }
    
    function contentShow_image_showSingle($frameWidth,$out="") {
        $contentData = $this->contentData;
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $outPut = "";
        
        
        
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

        $outPut .= div_start_str("cmsImageType","min-height:30px");
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


        // get ImageData
        $imageData = cmsImage_getData_by_Id($imageId);

        if ($this->wireFrameState AND $this->wireFrameEnabled) {
            // $wireFrameOn AND $wireframeState) {
            $wireframeImage = $this->contentData[wireframe][image];
            if ($wireframeImage ) {
                $wireframeImageText = $this->contentData[wireframe][imageText];
                
                $info = array();
                $info[id]   = $imageId;
                $info[nr]   = 1;
                $info[name] = $imageData[name];
                $info[title] = $imageData[subTitle];
                $wireframeImageText = $this->text_wireText("BILD ",$wireframeImageText,$info);
            }
        }
       // show_array($contentData);
//        $wireFrameOn = $data[wireframe];
//        $wireframeState = cmsWireframe_state();
//        if ($wireFrameOn AND $wireframeState) {
//
//            // echo ("<h1> Wireframe State is $wireframeState </h1>");
//            $wireframeData = $contentData[wireframe];
//            if (!is_array($wireframeData)) $wireframeData = array();
//            $wireframeImage = $wireframeData[image];
//            $wireframeImageText = $wireframeData[imageText];
//            // show_array($wireframeData);
//        }
        // show_array($showData);

        $zoom = $data[zoom];

        
        if ($wireframeImage) {
            $wireInfo = array();
            $wireInfo[name] = $imageData[name];
            $wireInfo[title] = $imageData[subTitle];
            $wireInfo[divClass] = "zoom_div";
            $wireInfo[wireColor] = "#ff00ff";
            
            $width = $imageWidth;
            if (!intval($width)) $width = $frameWidth;
            $height = $imageHeight;
            if (!intval($height)) $height = floor($width / 4 * 3);
            // echo ("$width $height <br>");

            if ($zoom) {
                $bigWidth = 800;
                $bigHeight = floor($bigWidth * $height / $width);
                $bigImageStr = $this->text_wireImage($bigWidth,$bigHeight,null,$wireInfo);
                //  $bigImageStr = cmsWireframe_image($bigWidth,$bigHeight,"#ff00ff");
                //echo ($bigImageStr."<br>");

                $outPut .= "<a href='$bigImageStr' class='zoomimage'>";
            }

            if ($wireframeImageText) {
               
                $outPut .= $this->text_wireImage($width, $height,$wireframeImageText,$wireInfo);

//                $outPut .=  cmsWireframe_frameStart_str($width, $height,"zoom_Div");
//                // $out .= "<a href='$bigImageStr' class=''>$wireframeImageText</a>";
//                $outPut .= $wireframeImageText;
//                $outPut .= cmsWireframe_frameEnd_str();
                

            } else {
                $imgStr = $this->text_wireImage($width,$height,null,$wireInfo);
                // $imgStr .= cmsWireframe_image($width,$height,"#ff00ff");
                // if ($zoom) echo ("<a href='$bigImageStr' class='zoomimage'>");
                $outPut .= "<img src='$imgStr' class='noBorder' />";
                // if ($zoom) echo ("</a>");
            }

            if ($zoom) {
                $outPut .= $outPut .= "</a>";
            }
            $imageText = $wireframeData[imageText];
           
        } else {



           
            if (is_array($imageData)) {
                $img = cmsImage_showImage($imageData,$frameWidth,$showData);

                if ($zoom) {
                    $bigWidth = 1000;
                    $bigShowData = array("out"=>"url");
                    $bigImage = cmsImage_showImage($imageData,$bigWidth,$bigShowData);


                    $outPut .= "<a href='$bigImage' class='zoomimage'>";
                }

                $outPut .= $img;

                if ($zoom) {
                    $outPut .= "</a>";
                }
            }
        }
        $outPut .= div_end_str("cmsImageType");
        
        if ($out) return $outPut;
        echo ($outPut);
        return $showData;

    }
    
    function contentShow_image_showTable($frameWidth,$out="") {
        $contentData = $this->contentData;
        
        $outPut = "";
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
        $outPut .= "RowWidth = $rowWidth / $frameWidth <br>";
        $imgRowAbsLast = $frameWidth - ($imgRow*$rowWidth) - (($imgRow-2)*$imgRowAbs);
        
        // echo ("last = $imgRowAbs $imgRow $rowWidth $imgRowAbs <br>");
        
        $imgList = $data[image];
        if (!$imgList) $imgList = $data[imgList];
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        
        $wireFrameOn = $data[wireframe];
        $wireframeState = cmsWireframe_state();
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
            $outPut .= div_start_str("imageList",$divData);
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
                        $outPut .= div_start_str("imgListLine",$lineStyle);                        
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
                    $outPut .= div_start_str("imgListImageBox",$divData);
                    

                    if ($zoom) {
                        $outPut .= "<a href='$bigImage' class='zoomimage'>";
                    }

                    $outPut .= $imgStr;
                    if ($zoom) {
                        $outPut .= "</a>";
                    }
                    $outPut .= div_end_str("imgListImageBox");

                    if ($nr == $imgRow) { // close Line
                        $nr = 0;
                        $outPut .= div_end_str("imgListLine","before");
                    }
                }
            
            }

            if ($nr != 0) {
                $outPut .= div_end_str("imgListLine","before");
            }




           $outPut .= div_end_str("imageList","before");
        } else {
            $outPut .= "<div class='cmsContentNoData'>";
            $outPut .= "Kein Bilder in Liste!";
            $outPut .= "</div>";
        }

        
        if ($out) return $outPut;
        
        echo ($outPut);
    }

    
    function contentShow_image_showSlider($frameWidth,$out="") {     
        $contentData = $this->contentData;
        
        $outPut = "";
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
        
        $outPut .= div_start_str("imageSlider",$divData);

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
        $wireframeState = cmsWireframe_state();
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

        $slider = "bxSlider";
        // $slider = "bxSlider";
        $sliderShowCaption = 0;
        
        
        /// SLIDER
        if (count($imgDataList)) {
            // echo ("<div class='slider_highlight content_box box-shadow' $style  >");

            $frameWidth = $rowWidth;

            $imageSize = $frameWidth-(2*$absHor);
            if ($imageHeight) $frameHeight = $imageHeight-(2*$absVer);

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
            $style = "width:".$frameWidth."px;";
            if ($frameHeight) $style .= "width:".$frameWidth."px;";
            // Start Slider Frame
             $outPut .= "<div class='$mainClass' id='$mainId' style='width:".$frameWidth."px;height:".$frameHeight."px;";
             if ($mainStyle)  $outPut .= "$mainStyle";
             $outPut .= "' > ";


            // echo ("<div class='coda-slider-wrapper' id='slider-id-wrapper' style='width:".$frameWidth."px;height:".$frameHeight."px;'>");
               //<div id="slider-id" class="coda-slider"> <div class="panel-container" style="margin-left: 0px; width: 400px;"><div style="background:url(images/articles/2012-06/thumbs/_400_300_MaxGiesinger.jpg);" class="slider_content panel"><div class="panel-wrapper"><div class="slider_info boxlink" style="opacity: 0.7;">Freitag, den 12.10.2012 - 20:30<br /><b>Stefan Singt</b><br />Letzter Auftritt für Immer<div class="hidden_url"><a href="kalender.php?dateId=1776">Link zum Artikel</a></div></div></div></div></div></div><div class="coda-nav"><ul style="margin-right: 0px; float: right;"><li class="tab1"><a href="#1" class="current">Freitag, den 12.10.2012 - 20:30Stefan SingtLetzter Auftritt für ImmerLink zum Artikel</a></li></ul></div></div>")

            // Innser Container
            if ($innerContainer) {
                 $outPut .= "<div ";
                if ($containerId)  $outPut .= "id='$containerId' ";
                if ($containerClass)  $outPut .= "class='$containerClass' ";
                 $outPut .= ">";
            }

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


                    // Create Slide Container
                    //$contentStr .="<div ";
//                    if ($sliderFrameId) $contentStr .="id='$sliderFrameId' ";
//                    if ($sliderFrameClass) $contentStr .="class='$slideFrameClass' ";
//                    if ($sliderFrameStyle) $contentStr .="style='$sliderFrameStyle' ";
//                    $contentStr .=">";
                    $contentStr .= $imgStr;
                    //$contentStr .= "<img src='$imgStr' width='".($frameWidth-(2*$absHor))."px' height='".$frameHeight."px' title='$subTitle' >";

                    // $contentStr .="</div>";
                    if ($zoom) {
                        $contentStr .= "</a>";
                    }

                    $contentList["image_".$imageId] = $contentStr;
                    
                } else {
                    $contentList["image_".$i] = $imgData;
                }
            }
            
//            if ($innerContainer) {
//                $contentStr .="</div>";
//            }

            // end of Slider Frame
            // $contentStr .="</div>";
            
            //echo ("<img src='$imgStr' >");
            
        } else {
             $outPut .= "<div class='cmsContentNoData'>";
             $outPut .= "Keine Bilder gefunden <br />";
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
           
            $outPut .= cmsSlider($slider, $name, $contentList, $showData, $width, $height,0);
            // show_array($contentData[data]);
        }
        
        $outPut .= "</div>";
        
        $outPut .= div_end_str("imageSlider","before");
        
        if ($out) return $outPut;

        echo ($outPut);
        
        

        //div_start("imagePreviewWindow open");
            
    }
     
    // ********************************************************************** //
    // ** SHOW DATA BOX                                                    ** //
    // ********************************************************************** //
    
    
    
    function data_showTable($dataType,$dataList,$contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
         
        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
        }
        
        
        if (!count($dataList)) {
            echo ("Keine $dataType gefunden <br>");
            return 0;
        }
        
        
        // show_array($projectList[0]);
        div_start($dataType."List","width:".$frameWidth."px;");

        $border = 1;
        $padding = 5;
        
        $dataRow = intval($data[dataRow]);
        $dataRowAbs = intval($data[dataRowAbs]);
        $dataColAbs = intval($data[dataColAbs]);
        $dataColHeight = intval($data[dataColHeight]);
        
        if (!$dataRow) $dataRow = 3;
        if (!$dataRowAbs) $dataRowAbs = 10;
        if (!$dataColAbs) $dataColAbs = 10;
        $rowWidth = floor(($frameWidth - (($dataRow-1)*$dataRowAbs )-($dataRow*2*$border) - ($dataRow*2*$padding)) / $dataRow);

        $divName = "cmsFilterList";        
        $this->showList_customFilter($contentData,$frameWidth,$divName);

        $clickAble = 1;
        $nr = 0;
        $zeile = 0;
        for ($i = 0; $i<count($dataList); $i++) {
            $myData = $dataList[$i];

            $nr++;
            if ($nr == 1) {
                if ($zeile) $lineStyle = "padding-top:".$dataColAbs."px";
                else $lineStyle = "";
                div_start($dataType."ListLine tableListLine",$lineStyle);
                $zeile ++;
            }


            $style = "";
            $boxData = array();
            $style .= "width:".$rowWidth."px;";
            if ($nr<$dataRow) $style.= "margin-right:".$dataRowAbs."px;";
            $style .= "float:left;";
            $style .= "border-width:".$border."px;";
            if ($dataColHeight) $style .= "height:".$dataColHeight."px;overflow:hidden;";
            if ($padding) $style .= "padding:".$padding."px;";
            $boxData[style] = $style;
            
            $divItemName = $dataType."ListItem tableItem";

            
            if ($clickAction) $divItemName .= " tableItemClick";
           
            div_start($divItemName,$boxData);
            if ($clickAction) {
                $goPage = $myData[goPage];
                // echo ("GoLink $goPage <br>");
                echo ("<a href='$goPage' class='hiddenLink' >$myData[name]</a>");
            }
            
            $out = $this->dataBox_show($dataType,$myData,$contentData,$rowWidth);
            echo ($out);

            div_end($divItemName);

            if ($nr == $dataRow) { // close Line
                $nr = 0;
                div_end($dataType."ListLine tableListLine","before");
            }                
        } // end of List

        if ($nr != 0) {
            div_end($dataType."ListLine tableListLine","before");
        }

        div_end($dataType."List","before");                
    }

    function dataBox_frameValue($contentData,$frameWidth) {
        $res = $this->show_frameValue($contentData, $frameWidth);
        return $res;
//         $data = $contentData[data];
//        if (!is_array($data)) $data = array();
//
//        $LR_left = $data[LR_left];
//        $LR_left_abs = $data[LR_abs];
//        $LR_center = $data[LR_center];
//        $LR_center_abs = $data[LRC_abs];
//        $LR_right = $data[LR_right];
//
////        echo ("Left '$LR_left' '$LR_left_abs' <br>");
////        echo ("Right '$LR_right' '$LR_right_abs' <br>");
////        echo ("Center '$LR_center' '$LR_center_abs' <br>");
//
//
//        if ($LR_left_abs) {
//            if (strpos($LR_left_abs,"%")) {
//                $proz = intval(substr($LR_left_abs,0,strpos($LR_left_abs,"%")));
//                $LR_left_abs = floor($frameWidth * $proz / 100);
//            }
//            if (strpos($LR_left_abs,"px")) {
//                $LR_left_abs = intval(substr($LR_left_abs,0,strpos($LR_left_abs,"px")));
//            }
//        } else {
//            $LR_left_abs = 10;
//        }
//
//        if ($LR_center_abs) {
//            if (strpos($LR_center_abs,"%")) {
//                $proz = intval(substr($LR_center_abs,0,strpos($LR_center_abs,"%")));
//                $LR_center_abs = floor($frameWidth * $proz / 100);
//            }
//            if (strpos($LR_center_abs,"px")) {
//                $LR_center_abs = intval(substr($LR_center_abs,0,strpos($LR_center_abs,"px")));
//            }
//        }
//
//        // leftWidth
//        if ($LR_left) {
//            if (strpos($LR_left,"%")) {
//                $proz = intval(substr($LR_left,0,strpos($LR_left,"%")));
//                $LR_left = floor($frameWidth * $proz / 100);
//            }
//            if (strpos($LR_left_abs,"px")) {
//                $LR_left = intval(substr($LR_left,0,strpos($LR_left,"px")));
//            }
//        }
//
//        // centerWidth
//        if ($LR_center) {
//            if (strpos($LR_center,"%")) {
//                $proz = intval(substr($LR_center,0,strpos($LR_center,"%")));
//                $LR_center = floor($frameWidth * $proz / 100);
//            }
//            if (strpos($LR_center,"px")) {
//                $LR_center = intval(substr($LR_center,0,strpos($LR_center,"px")));
//            }
//        }
//
//        // rightWidth
//        if ($LR_right) {
//            if (strpos($LR_right,"%")) {
//                $proz = intval(substr($LR_right,0,strpos($LR_right,"%")));
//                $LR_right = floor($frameWidth * $proz / 100);
//            }
//            if (strpos($LR_right,"px")) {
//                $LR_right = intval(substr($LR_right,0,strpos($LR_right,"px")));
//            }
//        }
//
//
//        // EXIST MIDDLE
//        $middle = 0;
//        if ($LR_center or $LR_center_abs) $middle = 1;
//
//
//        $space = $frameWidth;
//        if ($LR_left_abs) {
//            $space = $space-$LR_left_abs;
//            $anz = 2;
//        }
//        if ($middle) {
//            $anz = 3;
//            $space = $space-$LR_center_abs;
//        }
//
//        // echo ("FrameWidth = $frameWidth Space = $space Anzahl =$anz <br>");
//        // echo ("Breite links=$LR_left mitte=$LR_center rechts=$LR_right <br>");
//
//        if ($LR_left) {
//            $leftWidth = $LR_left;
//            $space = $space - $LR_left;
//            $anz--;
//        } else {
//            //echo ("<h1>HIER $LR_left </h1>");
//            $leftWidth = "auto";
//        }
//
//        if ($middle) {
//            if ($LR_center) {
//                $centerWidth = $LR_center;
//                $space = $space - $LR_center;
//                $anz--;
//            } else {
//                $centerWidth = "auto";
//            }
//        }
//
//
//        if ($LR_right) {
//            $rightWidth = $LR_right;
//            $space = $space - $LR_right;
//            $anz--;
//        } else {
//            $rightWidth = "auto";
//        }
//
//
//        // echo ("Danach $anz $space / links=$leftWidth mitte=$centerWidth rechts=$rightWidth <br> ");
//
//        if ($leftWidth == "auto") {
//            $width = floor($space / $anz);
//            $leftWidth = $width;
//            $space = $space - $width;
//            $anz--;
//        }
//
//        if ($middle AND $centerWidth == "auto") {
//            $width = floor($space / $anz);
//            $centerWidth = $width;
//            $space = $space - $width;
//            $anz--;
//        }
//
//        if ($rightWidth == "auto") {
//            $width = floor($space / $anz);
//            $rightWidth = $width;
//            $space = $space - $width;
//            $anz--;
//        }
//
//        $res = array();
//
//        $res[top_width] = $frameWidth;
//        $res[top_abs] = 0;
//        $res[top_text] = array();
//
//        $res[left_width] = $leftWidth;
//        $res[left_abs] = $LR_left_abs;
//        $res[left_text] = array();
//
//        if ($middle) {
//            $res[center_width] = $centerWidth;
//            $res[center_abs] = $LR_center_abs;
//            $res[center_text] = array();
//        }
//
//        $res[right_width] = $rightWidth;
//        $res[right_abs] = 0;
//        $res[right_text] = array();
//
//        $res[bottom_width] = $frameWidth;
//        $res[bottom_abs] = 0;
//        $res[bottom_text] = array();
//
//        return $res;
    }


    function dataBox_show($dataType,$data,$contentData,$frameWidth,$showList=null) {
        $dataData = $contentData[data];
        if (!is_array($dataData)) $dataData = array();
        
        if (!is_array($showList)) {
            $showList = $this->dataShow_List($contentData);
        }


        $abs = 0;
        $frameWidth = $frameWidth - 2*$abs;

        $posData = $this->dataBox_frameValue($contentData,$frameWidth);

        $leftWidth = $posData[left_width];
        $leftAbs = $posData[left_abs];
        $centerWidth = $posData[center_width];
        $centerAbs = $posData[center_abs];
        $rightWidth = $posData[right_width];
        $rightAbs = 0;
        
        $spanWidth = intval($dataData[spanWidth]);
        if (!$spanWidth) $spanWidth = 200;
        
        $wireFrameOn = $dataData[wireframe];
        $wireframeState = cmsWireframe_state();
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            $wireframeNr = $data[id];
            // echo ("<h1> WirfreameNr $wireframeNr </h1>");
        }

        
        
        foreach ($showList as $key => $value) {
            $show = $dataData[$key."_show"];
            if ($show) {
                $out = "";
                $keyName = $value;
                if (is_array($keyName)) {
                    $keyName = $value[name];
                }
                
                $targetWidth = $boxWidth;
              
                $checkBox = $dataData[$key."_checkbox"];
                
                
                
                
                $content = $data[$key];
               
                if ($wireFrameOn AND $wireframeState) {
                    switch($key) {
                        case "name" :
                            $wireHeadLine = $wireframeData[headLine];
                            if ($wireHeadLine) {
                                $wireHeadLine_text= $wireframeData[headLineText];
                                if ($wireHeadLine_text) $content =  cmsWireframe_text($wireHeadLine_text,$wireframeNr);
                                else $content = cmsWireframe_text(strlen($content),$wireframeNr);
                            }
                            break;
                        case "info" :
                            $wireSubHeadLine = $wireframeData[subHeadLine];
                            if ($wireSubHeadLine) {
                                $wireSubHeadLine_text= $wireframeData[subHeadLineText];
                                if ($wireSubHeadLine_text) $content =  cmsWireframe_text($wireSubHeadLine_text,$wireframeNr);
                                else $content = cmsWireframe_text(strlen($content),$wireframeNr);
                                // echo ("Info Content = $content <br>");
                            }
                            break;
                        case "longInfo" :
                            $wireText = $wireframeData[text];
                            if ($wireText) {
                                $wireText_text= $wireframeData[textText];
                                if ($wireText_text) $content =  cmsWireframe_text($wireText_text,$wireframeNr);
                                else $content = cmsWireframe_text(strlen($content),$wireframeNr);
                                // echo ("Info Content = $content <br>");
                            }
                            break;
                            
                            
                    }
                }
                
                
                
               
                    $target = $dataData[$key."_position"];
                    $pos = $dataData[$key."_position"];
                    $checkBox = $dataData[$key."_checkbox"];
                    $description = $dataData[$key."_description"];
                    $view = $dataData[$key."_view"];
                    $type = $value[type];

                    // echo ("ADD $key to $pos $description <br>");
                    
                    $targetWidth = $frameWidth;
                    switch ($target) {
                        case "left" : $targetWidth = $leftWidth; break;
                        case "right" : $targetWidth = $rightWidth; break;
                        case "center" : $targetWidth = $centerWidth; break;
                    }
                    $out = "";
                    switch ($key) {
                        case "image" :
                            $wireImage = 0;
                            $wireImageText = 0;
                            if ($wireFrameOn AND $wireframeState) {
                                $wireImage = $wireframeData[image];
                                if ($wireImage) $wireImageText = $wireframeData[imageText];
                                // echo ("WireframeImage $wireImage '$wireImageText' <br>");
                            }
                            $out = $this->dataBox_show_image($data,$contentData,$content,$view,$targetWidth,$wireImage,$wireImageText);
                            if (!$out) $out = "Bild nicht gefunden";
                            break;
                        case "name" : $out = $this->dataBox_show_name($dataType,$content,$targetWidth); break;
                        case "info" : $out = $this->dataBox_show_info($dataType,$content,$targetWidth); break;
                       
                        case "category" : 
                            if ($checkBox) $out .= span_text_str("Kategorie: ",$spanWidth);
                            $out .= $this->dataBox_show_category($dataType,$content,$targetWidth); 

                            break;
                            
                        case "basket" :
                            $basketAvailible = function_exists("cmsBasket_getItemCount");
                            if ($basketAvailible) {
                                $out .= $this->dataBox_show_basket($dataType,$data,$content,$targetWidth); 
                            }
                            break;
                        
                        case "date" :
                            $dateViewMode = $dataData[date_view];
                            
                            
                            $showWeekDay=0;
                            $longYear = 1;
                            
                            switch ($dateViewMode) {
                                case "short" : $longYear = 0; break;
                                case "long" : break;
                                case "weekDay" : $showWeekDay = 1; break;                                    
                            }
                            
                            $showToDate = $dataData[toDate_show];
                            
                            
                            $fromDate = $data[date];
                            $fromDateStr = cmsDate_getDayString($fromDate,$showWeekDay,$longYear);
                            $out = $fromDateStr;
                            $toDate   = $data[toDate];
                            
                            if ($toDate AND $toDate != "0000-00-00" AND $showToDate) {
                                $toDateStr = cmsDate_getDayString($toDate,$showWeekDay,$longYear);
                                $out .= " bis $toDateStr";
                            }
                            break;
                        
                        case "time" :
                            $fromTime = $data[time];
                            $fromTimeStr = cmsDate_getTimeString($fromTime, 2);
                            $out = $fromTimeStr;
                            $toTime   = $data[toTime];
                           
                            $showToTime = $dataData[toTime_show];
                            
                            if ($toTime AND $toTime != "00:00:00" AND $showToTime) {
                                $toTimeStr = cmsDate_getTimeString($toTime, 2);                            
                                $out .= "- $toTimeStr";
                            }
                            break;
                            
                        case "toDate" : $out = ""; break;
                        case "toTime" : $out = ""; break;
                        
                        default :
                            
                            switch ($type) {
                                case "date" : $out .= "DATUM : ";break;
                                case "time" : $out .= "TIME : ";break;
                                default :
                                    if ($type) $out .= "UNKOWN TYPE ($type) :";
                            }
                            if ($content) {
                                $out .= $content; // <br />";
                            }
                    }
                   
                 
                    if ($out) {
                        if ($description) {
                             $out = span_text_str($keyName.":",$spanWidth).$out;
                        }

                        $posData[$pos."_text"][$key] = $out;                      
                    }
                
            }
        }
        

        $outPut = $this->dataBox_frameShow($dataType,$posData,$class,$frameWidth);
        
        return $outPut;
        
    }

    function dataBox_frameShow($dataType,$posData,$class,$frameWidth) {
        
        $str = "";
        $str.= div_start_str("dataBox dataBox_".$dataType);
        $topText = $posData[top_text];

        $leftText = $posData[left_text];
        $centerText = $posData[center_text];
        $rightText = $posData[right_text];

        $bottomText = $posData[bottom_text];

        if (count($topText)) {
            $key = "top";
            $width = $posData[$key."_width"];
            $abs   = $posData[$key."_abs"];
            $style = "width:".$width."px;";
            if ($abs) $style .= "margin-right:".$abs."px;";
            $divName = "dataBox_".$key." dataBox_".$dataType."_".$key;
            $str .= div_start_str($divName,$style);
            foreach ($topText as $textKey => $text) {
                $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                $str .= div_start_str($contentFrameName);
                // echo ("<h1>$contentFrameName</h1>");
                $str .= $text;
                $str .= div_end_str($contentFrameName);
            }
            $str .= div_end_str($divName);
        }

        if (count($leftText) OR count($centerText) or count($rightText)) {
            $str .= div_start_str("positionFrame_LR");
            if (count($leftText)) {
                $key = "left";
                $width = $posData[$key."_width"];
                $abs   = $posData[$key."_abs"];
                $style = "width:".$width."px;";
                if ($abs) $style .= "margin-right:".$abs."px;";
                $divName = "dataBox_".$key." dataBox_".$dataType."_".$key;
                $str .= div_start_str($divName,$style);
                foreach ($leftText as $textKey => $text) {
                    $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                    $str .= div_start_str($contentFrameName);
                    // echo ("<h1>$contentFrameName</h1>");
                    $str .= $text;
                    $str .= div_end_str($contentFrameName);
                }
                $str .= div_end_str($divName);
            }

            if (count($centerText)) {
                $key = "center";
                $width = $posData[$key."_width"];
                $abs   = $posData[$key."_abs"];
                $style = "width:".$width."px;";
                if ($abs) $style .= "margin-right:".$abs."px;";
                $divName = "dataBox_".$key." dataBox_".$dataType."_".$key;
                $str .= div_start_str($divName,$style);
                foreach ($centerText as $textKey => $text) {
                    $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                    $str .= div_start_str($contentFrameName);
                    // echo ("<h1>$contentFrameName</h1>");
                    $str .= $text;
                    $str .= div_end_str($contentFrameName);
                }
                $str .= div_end_str($divName);
            }

            if (count($rightText)) {
                $key = "right";
                $width = $posData[$key."_width"];
                $abs   = $posData[$key."_abs"];
                $style = "width:".$width."px;";
                if ($abs) $style .= "margin-right:".$abs."px;";
                $divName = "dataBox_".$key." dataBox_".$dataType."_".$key;
                $str .= div_start_str($divName,$style);
                foreach ($rightText as $textKey => $text) {
                    $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                    $str .= div_start_str($contentFrameName);
                    // echo ("<h1>$contentFrameName</h1>");
                    $str .=$text;
                    $str .= div_end_str($contentFrameName);
                }
                $str .= div_end_str($divName);
            }
            $str .= div_end_str("positionFrame_LR","before");

        }


        if (count($bottomText)) {
            $key = "bottom";
            $width = $posData[$key."_width"];
            $abs   = $posData[$key."_abs"];
            $style = "width:".$width."px;";
            if ($abs) $style .= "margin-right:".$abs."px;";
            $divName = "dataBox_".$key." dataBox_".$dataType."_".$key;
            $str .= div_start_str($divName,$style);
            foreach ($bottomText as $textKey => $text) {
                $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                $str .= div_start_str($contentFrameName);
                // echo ("<h1>$contentFrameName</h1>");
                $str .= $text;
                $str .= div_end_str($contentFrameName);
            }
            $str .= div_end_str($divName);
        }
        $str.= div_end_str("dataBox dataBox_".$dataType);
        return $str;
    }



    
    function dataBox_show_name($dataType,$content){
        $out = "";
        $out .= div_start_str($dataType."ItemHead tableItemHead");
        $out .= "$content";
        $out .= div_end_str($dataType."ItemHead tableItemHead");
        return $out;
    }
    
    function dataBox_show_info($dataType,$content) {
        $out = "";
        $out .= div_start_str($dataType."ItemSubHead tableItemSubHead");
        $out .= "$content";
        $out .= div_end_str($dataType."ItemSubHead tableItemSubHead");       
        return $out;
    }
    
    function dataBox_show_image($data,$contentData,$content,$view,$boxWidth,$wireImage,$wireImageText) {
        
        //echo ("dataBox_show_image($data,$contentData,$content,$view,$boxWidth,$wireImage,$wireImageText) <br>");
        $dataData = $contentData[data];
        if (!is_array($dataData)) $dataData = array();
        if (intval($content)) $content = "|$content|";
        $imageList = explode("|",$content);
        
        // $selectImage = "random";
        switch ($view) {
            case "first" :  
                $imageId = $imageList[1]; 
                break;
            
            case "random" : 
                $count = count($imageList);
                $random = rand(1, $count-2);
                
                $imageId = $imageList[$random];
                // echo ("count $count $random $imageId <br>");
                break;
            case "slider" :
                $out = $this->dataBox_show_imageSlider($data,$contentData,$imageList, $boxWidth,$wireImage,$wireImageText);
                $imageId = 0;
                return ($out);
                break;

            case "gallery" :
                $out = $this->dataBox_show_gallery($imageList,$contentData,$boxWidth,$wireImage,$wireImageText);
                $imageId = 0;
                return $out;
                break;
                
        }

         if ($wireImage) {
            $width = $boxWidth;
            $height = floor($boxWidth / 4 * 3);
            if ($wireImageText) {
                $imgStr = cmsWireframe_frameStart_str($width, $height);
                $wireData = array("id"=>$data[id],"nr"=>1);
                $imgStr .= cmsWireframe_text($wireImageText, $wireData);
                $imgStr .= cmsWireframe_frameEnd_str();
            } else {
                $wireImage = cmsWireframe_image($width, $height);
                $imgStr = "<image src='$wireImage' class='noBorder' />";
            }
            return $imgStr;

        }
        
        if ($imageId) {
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $zoom = $dataData[zoom];
                $ratio = $dataData[ratio];
                if ($ratio) {
                    $ratioX = intval($dataData[ratioX]);
                    $ratioY = intval($dataData[ratioY]);
                    if ($ratioX AND $ratioY) {
                        
                    } else {
                        $ratioX = 4;
                        $ratioY = 3;
                    }
                    $ratio = 1.0 * $ratioX / $ratioY;
                }
                
                
                $showData = array();
                $showData[frameWidth] = $boxWidth;
                $showData[frameHeight] = $boxWidth / 4 * 3;
                $showData[ratio] = $ratio;
                $showData[resize] = $dataData[resize];
                $showData[crop]   = $dataData[crop];
                $showData[vAlign] = $dataData[vAlign];
                $showData[hAlign] = $dataData[hAlign];
                
                
                // show_array($showData);
               
                $imgStr = cmsImage_showImage($imageData, $boxWidth,$showData);
                
                if ($zoom) {
                    $bigImage = cmsImage_showImage($imageData, 800,array("out"=>"url"));                        
                    if ($bigImage) {                    
                        $imgStr = "<a href='$bigImage' class='zoomimage'>".$imgStr."</a>";
                    }
                }
                
                return $imgStr;
            }
            
        }

       
            
        
        $out = "Bild ($view) $imageId $content";
        return $out;
    }



    function dataBox_show_imageSlider($data,$contentData,$imageList,$boxWidth,$wireImage,$wireImageText) {
        $dataData = $contentData[data];
        if (!is_array($dataData)) $dataData = array();
        $imageWidth = $boxWidth;
        

       

        $contentList = array();
        $out = "";
        
        
        $width = $boxWidth;
        
        $zoom = $dataData[zoom];
        $ratio = $dataData[ratio];
        if ($ratio) {
            $ratioX = intval($dataData[ratioX]);
            $ratioY = intval($dataData[ratioY]);
            if ($ratioX AND $ratioY) {

            } else {
                $ratioX = 4;
                $ratioY = 3;
            }
            $ratio = 1.0 * $ratioX / $ratioY;
            
            $height = $boxWidth / $ratio;
            
        } else {
            $height = $boxWidth;
        }
        
        
        $ratio = 1.0 * 4 / 3;
        $randomImage = 1;
        
        
        

        $showData = array();
        $showData[frameWidth] = $boxWidth;
        $showData[frameHeight] = $height;
        $showData[ratio] = $ratio;
        $showData[resize] = $dataData[resize];
        $showData[crop]   = $dataData[crop];
        $showData[vAlign] = $dataData[vAlign];
        $showData[hAlign] = $dataData[hAlign];
        
        
        for ($i=0;$i<count($imageList);$i++) {
            $imageId = $imageList[$i];
            if ($imageId) {
                if ($wireImage) {
                    $imgStr = "WIRE IMAGE $imageId";
                    if ($wireImageText) {
                        $imgStr = cmsWireframe_frameStart_str($width, $height);
                        $wireData = array("id"=>$data[id],"nr"=>1);

                        $imgStr .= cmsWireframe_text($wireImageText, $wireData);
                        $imgStr .= cmsWireframe_frameEnd_str();
                    } else {
                        $wireImage = cmsWireframe_image($width, $height);
                        $imgStr = "<image src='$wireImage' class='noBorder' />";
                    }
                    $contentList[] = $imgStr;
                } else {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    if (is_array($imageData)) {
                        $imgStr = cmsImage_showImage($imageData, $boxWidth,$showData);
                        
                        if ($zoom) {
                            $bigImage = cmsImage_showImage($imageData, 800,array("out"=>"url","resize"=>1));  
                            if ($bigImage) {
                                $imgStr = "<a href='$bigImage' class='zoomimage'>".$imgStr."</a>";
                            }
                            // $zoomImageList[] = $bigImage;                
                        }


                       // $divStr = "<div style='width:".$boxWidth$this->projectBox_show($project,$contentData,$frameWidth);
    ////                 echo ($divStr);
    //                // echo ("SliderContent $name , $image , $imageId <br>");
                        $contentList[] = $imgStr;
                        
                        
                    }

                }
            }
        }


        $type = null;
        $name = "projectImageSlider_".$data[id];
        $showData = array();
        $width = $boxWidth;

        $direction = $dataData[direction];
        if (!$direction) $direction = "horizontal";
        $loop      = $dataData[loop];
        if (!$loop) $loop = 1;
        if (count($contentList)<2) $loop = 0;
        
        $pause = $dataData[pause];
        if (!$pause) $pause = 5000;
        $speed = $data[speed];
        if (!$speed) $speed = 500;
        $navigate = $dataData[navigate];
        $pager     = $dataData[pager];

        $showData[loop] = $loop;
        $directionList = array("vertical","horizontal","fade");
        $showData[direction] = $direction; // $directionList[0];

        $showData[speed] = $speed;

        $showData[pause] = $pause;
        $showData[navigate] = $navigate;
        $showData[page] = $pager;
        
        if ($zoom) {
            $showData[zoomImage] = $zoomImageList;
        }
        
        // show_array($showData);


        $res = cmsSlider($type,$name,$contentList,$showData,$width,$height,0);
        $out .= $res;

        return $out;
   }

    function dataBox_show_gallery($imageList,$contentData,$boxWidth,$wireImage,$wireImageText) {
        $dataData = $contentData[data];
        if (!is_array($dataData)) $dataData = array();
        
        $anz = $dataData[imgRow];
        if (!$anz) $anz = 3;
        
        $rowAbs = intval($dataData[imgRowAbs]);
        if (!$rowAbs) $rowAbs = 10;
        
        
        
        $colAbs = intval($dataData[imgColAbs]);
        if (!$colAbs) $colAbs = 10;
       
        $abs = 10;
        

        $imageWidth = floor(($boxWidth - (($anz-1)*$rowAbs)) / $anz);
        
        
        
        $zoom = $dataData[zoom];
        $ratio = $dataData[ratio];
        if ($ratio) {
            $ratioX = intval($dataData[ratioX]);
            $ratioY = intval($dataData[ratioY]);
            if ($ratioX AND $ratioY) {

            } else {
                $ratioX = 4;
                $ratioY = 3;
            }
            $ratio = 1.0 * $ratioX / $ratioY;
            
            $imageHeight = floor($imageWidth / $ratio);
            
        } else {
            $imageHeight = $imageWidth;
        }
        
        

        $showData = array();
        $showData[frameWidth] = $imageWidth;
        $showData[frameHeight] = $imageHeight;
        $showData[ratio] = $ratio;
        $showData[resize] = $dataData[resize];
        $showData[crop]   = $dataData[crop];
        $showData[vAlign] = $dataData[vAlign];
        $showData[hAlign] = $dataData[hAlign];

        // $ratio = 1.0 * 4 / 3;
        // $randomImage = 1;
        // $imageHeight = floor($imageWidth / $ratio);

        // $wireImage = 0;
        $out = "";
        $znr = 0;
        $lnr = 0;
        
        $wireframeNr = 0;
        for ($i=0;$i<count($imageList);$i++) {

            $imgStr = "";
            if ($wireImage) {
                $wireframeNr++;
                if ($wireImageText) {
                    $imgStr = cmsWireframe_frameStart_str($imageWidth,$imageHeight);
                    $imgStr .= cmsWireframe_text($wireImageText,$wireframeNr);
                    $imgStr .= cmsWireframe_frameEnd_str();
                } else {
                    $wireImage = cmsWireframe_image($imageWidth,$imageHeight);
                    $imgStr = "<image src='$wireImage' class='noBorder' >";
                }

                if ($zoom) {
                    $bigWidth = 800;
                    $bigHeight = floor($bigWidth * $imageHeight / $imageWidth);
                    $bigImage = cmsWireframe_image($bigWidth,$bigHeight);
                }

            } else {
                $imageId = $imageList[$i];
                if ($imageId) {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    if (is_array($imageData)) {
                       
                        $imgStr = cmsImage_showImage($imageData, $imageWidth,$showData);

                        if ($zoom) {
                            $showBigData = array("out"=>"url");
                            $bigImage = cmsImage_showImage($imageData, 1000,$showBigData);
                        }
                    }

                }
            }

            if ($imgStr) {
                $lnr ++;
                if ($lnr==1) {
                    $style = "";
                    if ($znr > 0) $style .= "margin-top:".$colAbs."px;";
                    $out .= div_start_str("projectImageGalleryLine",$style);
                }

                $style = "float:left;";
                if ($lnr<$anz) $style .= "margin-right:".$rowAbs."px";

                $out .= "<div style='$style'>";
                if ($zoom) {
                    $out .= "<a class='zoomimage' title='vergrößern' href='$bigImage'>";
                    $out .= $imgStr;//<img class="noborder" alt="" src="/images/locations/thumbs/_200_150_Kap.jpg">
                    $out .= "</a>";
                } else {
                   $out .= $imgStr;
                }
                    $out .= "</div>";
                if ($lnr == $anz) {
                    $lnr = 0;
                    $znr ++;
                    $out .= div_end_str("projectImageGalleryLine","before");
                }
            }
        }
        if ($lnr != 0) {
            $out .= div_end_str("projectImageGalleryLine","before");
        }
        if ($out) {
            $outPut = div_start_str("projectImageGalleryFrame");
            $outPut .= $out;
            $outPut .= div_end_str("projectImageGalleryFrame","before");
            return $outPut;
        }
        return $out;
   }

   function dataBox_show_category($dataType,$content,$targetWidth) {
        $out = "";
        $delimiter = " | ";
        if (intval($content)) $catList = array($content);
        else {            
            $catList = explode("|",substr($content,1,  strlen($content)-2));
        }
        for ($i= 0;$i<count($catList);$i++) {
            $catId = $catList[$i];
            $catName = cmsCategory_getName_byId($catId);
            
            if ($out) $out .= $delimiter;
            $out .= $catName;
            //$out .= "catID = $catId ";
        }
        
        
        $out .= "<br />";
        return $out;
    }    
    
    
    function dataBox_show_basket($dataType,$data,$content,$targetWidth) {


        $basketId = $content[basketId];
        $inBasket = $content[inBasket];

        $basket = $data[basket];
       
        $basketItem = array();
        $basketItem[basketId] = $basketId;
        $basketItem[name] = $data[name];
        $basketItem[vk] = $data[vk];
        $basketItem[shipping] = $data[shipping];
        $basketItem[dataSource] = $basket[dataSource];
        $basketItem[dataId] = $basket[dataId];
        $basketItem[maxCount] = $data[count];

        $showData = array();
        $showData[hideDiv] = 0;
        $showData["class"] = "tableItemNoClick";


        
        $out = cmsType_basket_showItem($basketItem,$showData);
        return $out;
    }



    function dataBox_editContent($data,$showList) {
        $res = array();

        $LR = 0;
        $LRC = 0;
        $SPAN = 0;
        
        foreach ($showList as $key => $value) {
            $addData = array();
            $addData[text] = $value[name];

            if ($data[$key."_show"]) $checked = "checked='checked'";
            else $checked = "";
            $input = "Zeigen: <input type='checkbox' $checked value='1' name='editContent[data][".$key."_show]' />";

            if ($value[position]) {
                $position = $data[$key."_position"];
                
                $mode = "box";
                $showData[mode] = $mode;
                
                
                $input .= " Position: ".$this->selectPosition($position,"editContent[data][".$key."_position]", $showData, $showFilter, $showSort);

                if ($position) {
                    if ($position=="left") $LR = 1;
                    if ($position=="right") $LR = 1;
                    if ($position=="center") $LRC = 1;
                }
            }

            if ($value[description]) {
                if ($data[$key."_description"]) $checked = "checked='checked'";
                else $checked = "";
                $input .= " $value[description]: <input type='checkbox' $checked value='1' name='editContent[data][".$key."_description]' />";
                $SPAN++;
            }


            if (is_array($value[view])) {
                $view = $value[view];
                $input .= " Darstellung: ";
                $viewValue = $data[$key."_view"];
                $viewData = array("empty"=>"Darstellung wählen");
                $input .= $this->selectView($viewValue,"editContent[data][".$key."_view]",$view,$viewData);
            }

            if ($value[sendMail]) {
                $input .= " eMail: ".cmsEmail_selectEmail($data[$key."Mail"], "editContent[data][".$key."Mail]", $showData); // , $filter, $sort)
            }

            if (is_array($value[style])) {
                $style = $value[style];
                $input .= " Stil: ";
                $styleValue = $data[$key."_style"];
                $viewData = array("empty"=>"Stil wählen");
                $input .= $this->selectStyle($styleValue,"editContent[data][".$key."_style]",$style,$viewData);
            }
            $addData[input] = $input;
            $addData["mode"] = "Simple";
            $res[] = $addData;
        }

        if ($LR) {
            if ($LRC) $addData["text"] = "Rechts / Mitte / Links";
            else $addData["text"] = "Rechts / Links";
            $input = "";
            $input .= "Breite Links: <input type='text' style='width:40px;' value='$data[LR_left]' name='editContent[data][LR_left]' />";
            $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LR_abs]' name='editContent[data][LR_abs]' />";
            if ($LRC) {
                $input .= "Breite Mitte: <input type='text' style='width:40px;' value='$data[LR_center]' name='editContent[data][LR_center]' />";
                $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LRC_abs]' name='editContent[data][LRC_abs]' />";
            }
            $input .= "Breite Rechts: <input type='text' style='width:40px;'value='$data[LR_right]' name='editContent[data][LR_right]' />";
            $addData["input"] = $input;
            $addData["mode"] = "More";
            $res[] = $addData;
        }

        if ($SPAN) {
            $addData["text"] = "Bezeichnungsbreite";
            $input = "";
            $input .= "<input type='text' style='width:40px;' value='$data[spanWidth]' name='editContent[data][spanWidth]' />";
            $addData["input"] = $input;
            $addData["mode"] = "More";
            
            $res[] = $addData;
        }



        return $res;
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
            $addData["mode"] = "More";
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
            $addData["mode"] = "More";
            $res[] = $addData;


            $addData = array();
            $addData["text"] = "Bild-Position";
            $addData["input"] = cmsEdit_imagePosition("editContent[data][hAlign]","editContent[data][vAlign]",$data[hAlign],$data[vAlign]);
            $addData["mode"] = "Simple";
            $res[] = $addData;
        }

        if (!$dontShow[zoom]) {

            $addData = array();
            $addData["text"] = "Zoom Bild";       
            $checked = "";
            if ($editContent[data][zoom]) $checked = " checked='checked'";
            $addData["input"] = "<input type='checkbox' value='1' name='editContent[data][zoom]' $checked >";
            $addData["mode"] = "Simple";
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
            $addData["mode"] = "More";
            $res[] = $addData;
        }
        
        $imageView = null;
        if ($data[viewMode]) $imageView = $data[viewMode];
        if ($data[image_view]) $imageView = $data[image_view];
        if ($imageView) {
            switch ($imageView) {
                case "random" : break;
                case "first" : break;
                case "gallery" :
                    
        
                    
                    $addData = array();           
                    $addData[text] = "Anzahl pro Reihe";
                    $addData["input"] = "<input type='text' style='width:50px' value='$data[imgRow]' name='editContent[data][imgRow]' \>";
                    $addData["mode"] = "Simple";
                    $res[] = $addData;
                    
                    $addData = array();           
                    $addData[text] = "Abstand Bilder in Reihe";
                    $addData["input"] = "<input type='text' style='width:50px' value='$data[imgRowAbs]' name='editContent[data][imgRowAbs]' \>";
                    $addData["mode"] = "More";
                    $res[] = $addData;
                    
                    $addData = array();           
                    $addData[text] = "Abstand Zeilen";
                    $addData["input"] = "<input type='text' style='width:50px' value='$data[imgColAbs]' name='editContent[data][imgColAbs]' \>";
                    $addData["mode"] = "More";
                    $res[] = $addData;
                    
                    break;
                
                case "slider" :
                    $addData = array();           
                    $addData[text] = "Anzahl pro Reihe";
                    $addData["input"] = $imageView;
                    $addData["mode"] = "More";
                    $res[] = $addData;
                    
                    $addData = array();
                    $addData["text"] = "Wechsel";
                    $direction = $editContent[data][direction];
                    $input  = $this->slider_direction_select($direction,"editContent[data][direction]",array());
                    $addData["input"] = $input;
                    $addData["mode"] = "Simple";
                    $res[] = $addData;


                    $addData = array();
                    $addData["text"] = "Auto Loop";
                    $loop = $editContent[data][loop];
                    $checked = "";
                    if ($loop) $checked = " checked='checked'";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][loop]' $checked >";
                    $addData["mode"] = "More";
                    $res[] = $addData;

                    $addData = array();
                    $addData["text"] = "Zeit für Bild in ms";
                    $addData["input"] = "<input name='editContent[data][pause]' style='width:100px;' value='".$editContent[data][pause]."'>";
                    $addData["mode"] = "More";
                    $res[] = $addData;

                    $addData = array();
                    $addData["text"] = "Zeit für Wechsel in ms";
                    $addData["input"] = "<input name='editContent[data][speed]' style='width:100px;' value='".$editContent[data][speed]."'>";
                    $addData["mode"] = "More";
                    $res[] = $addData;

                    $addData = array();
                    $addData["text"] = "Navigation";
                    $navigate = $editContent[data][navigate];
                    $checked = "";
                    if ($navigate) $checked = " checked='checked'";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][navigate]' $checked >";
                    $addData["mode"] = "More";
                    $res[] = $addData;

                    $addData = array();
                    $addData["text"] = "Einzelauswahl";
                    $pager = $editContent[data][pager];
                    $checked = "";
                    if ($pager) $checked = " checked='checked'";
                    $addData["input"] = "<input type='checkbox' name='editContent[data][pager]' $checked >";
                    $addData["mode"] = "More";
                    $res[] = $addData;
                    break;
                
                default :
                    $addData = array();           
                    $addData[text] = "Unbekannter viewMode";
                    $addData["input"] = $imageView;
                    $res[] = $addData;
                    
                
                
            }
           
        }
        
        return $res;
    }
    
    function action_editContent($data,$showList) {
        $res = array();
        
        // Mouse ACTION
        $mouseAction = $data[mouseAction];
        if ($_POST[editContent][data][mouseAction]) $mouseAction = $_POST[editContent][data][mouseAction];
        else if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Maus über";
        $input  = $this->mouseAction_select($mouseAction,"editContent[data][mouseAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[] = $addData;

        // KLICK ACTION
        $clickAction = $data[clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];
        else if ($_POST[editContent][data]) $clickAction = $_POST[editContent][data][clickAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[] = $addData;


        if ($clickAction) {
            if ($clickAction == "showProduct" OR $clickAction == "showCategory") {

                $clickTarget = $data[clickTarget];
                if ($_POST[editContent][data][clickTarget]) $clickTarget = $_POST[editContent][data][clickTarget];
                else if ($_POST[editContent][data]) $clickTarget = $_POST[editContent][data][clickTarget];
                $addData = array();
                $addData["text"] = "Zeigen in";
                $addData["input"] = $this->target_select($clickTarget,"editContent[data][clickTarget]",array("submit"=>1));
                $res[] = $addData;


                switch ($clickTarget) {
                    case "page" :

                        $clickPage = $data[clickTarget];
                        if ($_POST[editContent][data][clickPage]) $clickPage = $_POST[editContent][data][clickPage];
                        else if ($_POST[editContent][data]) $clickPage = $_POST[editContent][data][clickPage];

                        $addData = array();
                        $addData["text"] = "Seite auswählen";
                        $addData["input"] = $this->page_select($clickPage,"editContent[data][clickPage]",array("submit"=>1));
                        $res[] = $addData;

                        break;
                    case "frame" :

                        break;
                    case "popup" :
                        $addData = array();
                        $addData["text"] = "Breite PopUp Fenster";
                        $addData["input"] = "<input name='editContent[data][popUpWidth]' style='width:100px;' value='".$editContent[data][popUpWidth]."'>";
                        $res[action][] = $addData;

                        $addData = array();
                        $addData["text"] = "Höhe PopUp Fenster";
                        $addData["input"] = "<input name='editContent[data][popUpHeight]' style='width:100px;' value='".$editContent[data][popUpHeight]."'>";

                        $res[] = $addData;
                        break;
                }
            }
        }
        
        return $res;
        
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // TABLE                                                                 ///
    ////////////////////////////////////////////////////////////////////////////
    
    function tableBox_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        
        
        $tableData = $this->tableBox_getData($data, $frameWidth);
        // show_array($tableData[rowData]);
        
        if (!is_array($tableData)) return 0;
        // echo ("Zeige Tabelle mit $columnCount Spalten und $rowCount Zeilen<br>");
        //$res = div_start_str("cmsTable","width:".$frameWidth."px;");
        //$res .= "<table class='cmsTable' cellspacing='10' cellpadding='20' style='width:".$frameWidth."px;cell-spacing:0;'>";
        $res .= "<table class='cmsTable' style='width:".$frameWidth."px;cell-spacing:0;'>";
        $res .= $this->tableBox_showRow($contentData,$tableData,$frameWidth);
        //$res .= div_end_str("cmsTable");
        $res .= "</table>";
        
        echo ($res);
    }
    
    
    function tableBox_showRow($contentData,$tableData,$frameWidth) {
        $res = "";
        $rowCount = $tableData[rowCount];
        $columnHead = $tableData[columnHead];
        
        $startNr = 1;
        if ($columnHead) { // Tabellen Kopf
            $startNr = 0;
        }
        
        // foreach($tableData as $key => $value ) echo ("$key = $value <br />");
        
        for ($rowNr=$startNr;$rowNr<=$rowCount;$rowNr++) {
            
            $rowKey = "row_".$rowNr;
            $rowData = $tableData[rowData][$rowKey];
            if (!is_array($rowData)) $rowData = array();
            
            $height = $this->getValue_fromString($rowData[height],$frameWidth);
            
            
            
            $lineClass = 'cmsTableLine';
            if ($rowNr == $startNr) $lineClass.= " cmsTableLine_first";
            if ($rowNr == $rowCount) $lineClass.= " cmsTableLine_last";

            if ($rowNr == 0) $lineClass .= " cmsTable_head";
            
            $lineStyle = "";
            if ($height) {
                $useHeight = $height + $tableData[paddingTop] + + $tableData[paddingBottom];
                
                $lineStyle.="height:".$useHeight."px;";
            }
            
            //$res .= div_start_str($lineClass,$lineStyle);
            $res .= "<tr class='$lineClass' style='$lineStyle' >";
            if ($rowNr == 0) {
                $res .= $this->tableBox_showTableHead($contentData,$tableData,$frameWidth,$height);                
            } else {
                $res .= $this->tableBox_showColumn($rowNr,$contentData,$tableData,$frameWidth,$height);
                
            }
            //$res .= div_end_str($lineClass,"before");
            $res .= "</tr>";
                    
        }
        return $res;
        
        
    }
    
    function tableBox_showTableHead($contentData,$tableData,$frameWidth) {
        $res = "";
        // $res .= "<h1> TABLE HEAD </h1>";
        // $res .= $this->tableBox_showColumn(0,$contentData,$tableData,$frameWidth);
        
        $columnCount = $tableData[columnCount];
        $columnData = $tableData[columnData];
        $rowHead = $tableData[rowHead];
        
       
        $startNr = 1;
        if ($rowHead) $startNr = 0;
       // echo ("START NR = $startNr<br>");
        foreach ($columnData as $colKey => $colData) {
            $colWidth = $colData[width];
            $colTitle = $colData[title];
            //echo ("$colKey width $colWidth <br>");
            $columnClass = "cmsTableColumn cmsTableColumn_headLine";
            if ($colKey == "column_".$startNr) $columnClass.= " cmsTableColumn_first";
            if ($colKey == "column_".$columnCount) $columnClass.= " cmsTableColumn_last";
            if ($colKey == "column_0") $columnClass .= " cmsTableColumn_head_corner";
            //$res.= div_start_str($columnClass,"width:".$colWidth."px;");
            $res .= "<td class='$columnClass' style='width:".$colWidth."px;'>";
            $res.= $colTitle;
            //$res.= div_end_str($columnClass);
            $res .= "</td>";
        }        
        return $res;       
    }
    
    function tableBox_showColumn($rowNr,$contentData,$tableData,$frameWidth,$rowHeight=null) {
        $res = "";
        $columnCount = $tableData[columnCount];
        $columnData = $tableData[columnData];
        $rowHead = $tableData[rowHead];
        $startNr = 1;
        if ($rowHead) $startNr = 0;
        
        $rowKey = "row_".$rowNr;
        $rowData = $tableData[rowData][$rowKey];
        if (!is_array($rowData)) $rowData = array();
        foreach ($columnData as $colKey => $colData) {
            $colWidth = $colData[width];
            //echo ("$colKey width $colWidth <br>");
            $columnClass = "cmsTableColumn";
            if ($colKey == "column_$startNr") $columnClass.= " cmsTableColumn_first";
            if ($colKey == "column_".$columnCount) $columnClass.= " cmsTableColumn_last";
            
            if ($colKey == "column_0") {
                $columnClass.= " cmsTableColumn_title";
            }
            
            //$res.= div_start_str($columnClass,"width:".$colWidth."px;");

            $res .= "<td class='$columnClass' style='width:".$colWidth."px;' >"; // style:"

            if ($colKey == "column_0") { // ZEILEN TITLE
                $title = $rowData[title];
                if (!$title) $title = "ZEILE $rowNr";
                $res .= "<b>$title</b>";
            } else {
                $colNr = substr($colKey,7);
                $content = $tableData[content][$rowNr][$colNr];
                $colType = $colData[showType];
                $rowType = $rowData[showType];
                if ($colType AND $rowType) {
                    $contentStr = "$colType / $rowType "; //<br/>".$content;                    
                } else {
                    if ($colType) {
                        $contentStr = $this->tableBox_showContent($colType,$content,$colWidth,$rowHeight);
                    } else {
                        if ($rowType) {
                            $contentStr = $this->tableBox_showContent($rowType,$content,$colWidth,$rowHeight);
                        } else {
                            $contentStr = "noType ".$content;
                        }
                    }
                    
                }
                $res .= $contentStr; //Str;
            }            
            //$res.= div_end_str($columnClass);
            $res .= "</td>";
        }        
        return $res;
    }
    
    function tableBox_showContent($showType,$content,$colWidth,$colHeight=null) {
        
        switch ($showType) {
            case "text" : $res = $this->tableBox_showContent_text($content,$colWidth,$colHeight); break;
            case "image"   : $res = $this->tableBox_showContent_image($content,$colWidth,$colHeight); break;
            case "state" : $res = $this->tableBox_showContent_state($content,$colWidth,$colHeight); break;
            case "integer" : $res = $this->tableBox_showContent_integer($content,$colWidth,$colHeight); break;
            case "float" : $res = $this->tableBox_showContent_float($content,$colWidth,$colHeight); break;
            case "basket" : $res = $this->tableBox_showContent_basket($content,$colWidth,$colHeight); break;
            default :
                $res = $this->tableBox_showContent_own($showType,$content,$colWidth,$colHeight);
        }     
        if (!$res) $res .= "&nbsp;";
        return $res;
    }
    
    function tableBox_showContent_own($showType,$content,$colWidth,$colHeight) {
        $res = "unkown Showtype $showType<br/>".$content;
        return $res;
    }

    function tableBox_editContent($showType,$content,$dataName,$showData=array()) {
         switch ($showType) {
            case "text" : $res = $this->tableBox_editContent_text($content,$dataName,$showData); break;
            case "image"   : $res = $this->tableBox_editContent_image($content,$dataName,$showData); break;
            case "state" : $res = $this->tableBox_editContent_state($content,$dataName,$showData); break;
            case "integer" : $res = $this->tableBox_editContent_integer($content,$dataName,$showData); break;
            case "float" : $res = $this->tableBox_editContent_float($content,$dataName,$showData); break;
            case "basket" : $res = $this->tableBox_editContent_basket($content,$dataName,$showData); break;
            default :
                $res = $this->tableBox_editContent_own($showType,$content,$dataName,$showData);
        }
        if (!$res) $res .= "&nbsp;";
        return $res;
    }

    function tableBox_editContent_own($showType,$content,$dataName,$showData)  {
        $res = "unkown Showtype $showType in editContent tableBox<br/>".$content;
        return $res;
    }
    
    function tableBox_showContent_text($content,$colWidth,$colHeight){
        $res = "";
        $res .= "$content";
        return $res;
    }
    function tableBox_showContent_image($content,$colWidth,$colHeight){
        $res = "";
        $width = $colWidth;
        
        if ($colHeight) $height = $colHeight;
        else $height = $width;
        $imgStr = cmsWireframe_image($width,$height);
        $res .= "<img src='$imgStr' class='noBorder'>";



        $imageId = $content;
        if ($imageId > 0) {
            $imageData = cmsImage_getData_by_Id($imageId);

            if (is_array($imageData)) {
                $imagePath = $imageData[orgpath];
                $idStr = "id:$imageId|path:$imagePath";
                $img = cmsImage_showImage($imageData,100,array("class"=>$imageClickClass,"id"=>$idStr));
            }
        }
        return $img;

        // $res = "bild $colWidth x $colHeight ";
        return $res;
    }
    
    function tableBox_showContent_state($content,$colWidth,$colHeight){
        $res = "";
        $w = 16;
        $h= 16;
        $bw = 3;
        $color = "#ad7";
        $style = "border:".$bw."px solid $color;border-radius:".($w/2+$bw)."px;width:".$w."px;height:".$h."px;display:inline-block;";
        If ($content) $style.="background-color:$color;";
        else $style .= "background-color:#bf9;";
        $res = "<div style='$style' >&nbsp;</div>";

//        if ($content) {
//            $res .= "X";
//        } else {
//            $res .= "-";
//        }
        return $res;
    }
    
    function tableBox_showContent_integer($content,$colWidth,$colHeight){
        $res = "";
        $int = intval($content);
        $res = $int;
        return $res;
    }
    
    function tableBox_showContent_float($content,$colWidth,$colHeight){
        $res = "";
        $res .= $content;
        $val = floatval($content);
        
        $deci_deli = ",";
        $deci = 2;
        $deci_1000 = ".";
        
        $res = number_format($val, $deci, $deci_deli, $deli_1000);
        return $res;
    }


    function tableBox_showContent_currency($showType,$content,$colWidth,$colHeight) {

        $typeList = $this->tableBox_edit_contentTypes();
        $showData = $typeList["currency"];

        $currency = $showData["currency"];

        $val = floatval($content);

        $deci_deli = ",";
        $deci = 2;
        $deci_1000 = ".";

        $res = number_format($val, $deci, $deci_deli, $deli_1000);
        $res .= " $currency";
        return $res;
    }
    function tableBox_showContent_basket($content,$colWidth,$colHeight){
        $res = "";
        $res .= "BASKET"; //$content;
        return $res;
    }


    function tableBox_edit_contentTypes() {
        $res = array();
        $res["text"] = array("name"=>"Text");
        $res["image"] = array("name"=>"Bild");
        $res["state"] = array("name"=>"Status");
        $res["integer"] = array("name"=>"Zahl");
        $res["float"] = array("name"=>"Komma-Zahl");
        $res["basket"] = array("name"=>"Warenkorb");

        $addList = $this->tabeBox_edit_contentTypes_own();
        if (is_array($addList)) {
            foreach ($addList as $key => $value) {
                if ($value) {
                    $res[$key] = $value;
                } else {
                    unset($res[$key]);
                }
            }
        }

        return $res;
    }

    function tabeBox_edit_contentTypes_own() {
        $res = array();
        return $res;
    }

    function tableBox_edit_selectContentType($code,$dataName,$showData=array()) {
        $typeList = $this->tableBox_edit_contentTypes();

        $str.= "<select name='$dataName' class='cmsSelectVontentType' style='min-width:70px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Kein Inhalt";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        foreach ($typeList as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $name = $value[$outValue];
                } else {
                    $name = $value;
                }

                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$name</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }
    
    function tableBox_editContent_text($content,$dataName,$showData) {
        $res .= "<input type='text' value='$content' name='$dataName' />";
        return $res;
    }

    function tableBox_editContent_image($content,$dataName,$showData) {


        $imageClickClass = "cmsImageSelectModul";
        $img = "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='90px' height='90px' class='$imageClickClass'> ";
        $imageId = $content;
        if ($imageId > 0) {
            $imageData = cmsImage_getData_by_Id($imageId);

            if (is_array($imageData)) {
                $imagePath = $imageData[orgpath];
                $idStr = "id:$imageId|path:$imagePath";
                $img = cmsImage_showImage($imageData,100,array("class"=>$imageClickClass,"id"=>$idStr));
            }
        }
        $input = "";
        $input .= "<div class='cmsImageDropFrame cmsDropSingle' >";
        $input .= "<div class='cmsImageFrame' >";
        $input .= $img;
        $input .= "</div>";
        $inputType = "hidden";
        $input .= "<input type='$inputType' class='cmsImageId' style='width:30px;' name='$dataName' value='$content' />";
        $input .= "</div>";

        return $input;

        $res .= "<input type='text' value='$content' name='$dataName' />";
        return $res;
    }

    function tableBox_editContent_state($content,$dataName,$showData) {
        if ($content) $checked="checked='checked'";
        else $checked = "";
        $res .= "<input type='checkbox' value='1' $checked name='$dataName' />";
        return $res;
    }

    function tableBox_editContent_integer($content,$dataName,$showData) {
        $res .= "<input type='text' value='$content' name='$dataName' />";
        return $res;
    }

    function tableBox_editContent_float($content,$dataName,$showData) {
        $res .= "<input type='text' value='$content' name='$dataName' />";
        return $res;
    }

    function tableBox_editContent_basket($content,$dataName,$showData) {
        $res .= "<input type='text' value='$content' name='$dataName' />";
        return $res;
    }

    
    function tableBox_getData($data, $frameWidth) {
        $res = array();
        $rowCount = intval($data[rowCount]);
        $columnCount = intval($data[columnCount]);
        if ($columnCount < 1) {
            echo ("Keine Spalten Anzahl definiert <br>");
            return 0;
        }
        if ($columnCount < 1) {
            echo ("Keine Zeilen Anzahl definiert <br>");
            return 0;
        }
        $res[columnCount] = $columnCount;
        $res[rowCount]    = $rowCount;
        
        $res[rowData]     = $data[rowData];
        $res[content]     = $data[content];
        
        $rowHead = $data[rowHead];
        $columnHead = $data[columnHead];
        $res[columnHead] = $columnHead;
        $res[rowHead] = $rowHead;
        
        // foreach($data as $key => $value ) echo ("$key = $value <br>");
        
        $startNr = 1;
        $borderWidth = 1;
        $padding_top = 5;
        $padding_bottom = 5;
        $padding_left = 5;
        $padding_right = 5;
        $res[paddingTop] = $padding_top;
        $res[paddingBottom] = $padding_bottom;
        $res[paddingLeft] = $padding_left;
        $res[paddingRight] = $padding_right;
        
        $leftSpace = $frameWidth - ($borderWidth*($columnCount+1));
        $leftCount = $columnCount;
        if ($rowHead) {
            $startNr = 0;
            $leftSpace = $leftSpace - $borderWidth;   
            $leftCount++;           
        }
         
        $columnData = $data[columnData];
        if (!is_array($columnData)) $columnData = array();
        
        // Check existing Width
        for ($i=$startNr;$i<=$columnCount;$i++) {
            $colData = $columnData["column_".$i];
            if (!is_array($colData)) {
                $columnData["column_".$i] = array();
                $colData = array();
            }
            $columnWidth = $colData[width];
            if ($columnWidth) {
               
                $columnWidth = $this->getValue_fromString($columnWidth,$frameWidth);    
                
            } 
            if (is_int($columnWidth)) {
                // padding 
               
                $leftSpace = $leftSpace - $columnWidth;
                $columnWidth = $columnWidth - $padding_left - $padding_right;
                $leftCount--;
                $columnData["column_".$i][width] = $columnWidth;
            } else {
                $columnData["column_".$i][width] = "auto";
            }
        }
        
        ksort($columnData);
        
        // Check existing Width
        foreach ($columnData as $key => $colData) {
            // echo ("$key = $colData<br>");
            $width = $colData[width];
            if ($width == "auto") {
                $useWidth = floor($leftSpace / $leftCount);
                // padding
               
                $leftSpace = $leftSpace - $useWidth;
                $useWidth = $useWidth - $padding_left - $padding_right;
                $leftCount--;
                $columnData[$key][width] = $useWidth;
                // echo ("USE WIDTH = $useWidth left = $leftSpace<br>");
            }
        }
        
        
        $res[columnData] = $columnData;
        return $res;
    }
        
    
    function getValue_fromString($str,$frameWidth) {
        $prozOff = strpos($str,"%");
        if ($prozOff) {
            $proz = substr($str,0,$prozOff);
            if (intval($proz)) {
                $val = intval(floor($frameWidth * $proz / 100));
                // echo ("PROZENT = $proz $val <br>");
                if ($val) return $val;
            }
        }
        
        $pixelOff = strpos($str,"px");
        if ($pixelOff) {
            $pixel = substr($str,0,$pixelOff);
            if (intval($pixel)) {
                return intval($pixel);
                
            }
        }
        
        
        
        
        // check Integer
        $int = intval($str);
        if (is_int($int)) return $int;       
    }
        
        
    
}




?>
