<?php // charset:UTF-8

class cmsType_image_base extends cmsType_contentTypes_base {
    function getName() {
        return "Bilder";
    }
    
    

    function image_show($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "single";
        
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


       // show_array($contentData);
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
        
        
        
//        $sliderHeightData = $data[sliderHeight];
//        if (intval($sliderHeightData)) $sliderHeight = intval($sliderHeightData);
//        $ratioOff = strpos($sliderHeightData,":");
//        if ($ratioOff) {
//            $ratioX = intval(substr($sliderHeightData,0,$ratioOff));
//            $ratioY = intval(substr($sliderHeightData,$ratioOff+1));
//            if ($ratioX AND $ratioY) {
//                $sliderHeight = floor($sliderWidth / $ratioX * $ratioY);
//                $ratio = 1.0 * $ratioX / $ratioY;
//            }            
//        }
//        $prozOff = strpos($sliderHeightData,"%");
//        if ($prozOff) {
//            $proz = intval(substr($sliderHeightData,0,$prozOff));
//            // echo ("PROZ = $proz $sliderHeightData<br>");
//            if ($proz) {
//                $sliderHeight = floor($sliderWidth *$proz / 100);
//            }            
//        }
//        
//        $pxOff = strpos($sliderHeightData,"px");
//        if ($pxOff) {
//            $pixel = intval(substr($sliderHeightData,0,$pxOff));
//            // echo ("PROZ = $proz $sliderHeightData<br>");
//            if ($pixel) {
//                $sliderHeight = $pixel;
//            }            
//        }
        
        
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


    function editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        
        if (!is_array($data)) $data = array();
        
        $res = array();
        
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "single";
        
        $addData = array();
        $addData[text] = "Bild Darstellung";
        $showData = array("empty"=>"Darstellung wählen","submit"=>1);
        $addData[input] = $this->editContent_SelectViewMode($viewMode,"editContent[data][viewMode]",$showData);
        $addData[mode] = "Simple";
        $res[] = $addData;
        
        
        $dontShow = array("viewMode"=>0);
        $addData = cmsImage_editSettings($editContent,$frameWidth,$dontShow);
        for ($i=0;$i<count($addData);$i++) {
            $res[] = $addData[$i];
        }
        
        $addList = array();
        switch ($viewMode) {
            case "single" : 
                $addList = $this->editContent_imageSettings($editContent,$frameWidth);
                break;
            case "table" :
                $addList = $this->editContent_imageListSettings($editContent,$frameWidth);
                break;
            
            case "slider" : 
                $addList = $this->editContent_imageSliderSettings($editContent,$frameWidth);
                break;
                
            default :
                echo ("unkown SviewMode '$viewMode' <br>");
        }
        
        if (is_array($addList)) {
            foreach ($addList as $key => $value) {
                if (is_string($key)) {
                   if (!is_array($res[$key])) $res[$key] = array();
                    for($i=0;$i<count($value);$i++) {
                        $res[$key][] = $value[$i];
                    }
                } else {
                    $res[] = $value;
                }                            
            }
        }         
      
        return $res;
    }
    
    
    function editContent_imageSettings($editContent,$frameWidth) {
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
        $addData["text"] = "Bild";
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
                //$res[] = $addData;
        }



        
//            $editType = $editContent[type];
//            $addData = array();
//            $addData["text"] = "Breite";
//            $addData["input"] = "<input type='text'  name='editContent[data][imageWidth]' style='width:100px;' value='$data[imageWidth]' > (px,%,auto)";
//            $res[] = $addData;
//            $addData = array();
//            $addData["text"] = "Höhe";
//            $addData["input"] = "<input type='text' name='editContent[data][imageHeight]' style='width:100px;' value='$data[imageHeight]' > (px)";
//            $res[] = $addData;
//            
//            $addData = array();
//            $addData["text"] = "Verhältnis";
//            if ($editContent[data][ratio]) $checked="checked='checked'"; else $checked="";
//            $addData["input"] = "<input type='checkbox' $checked name='editContent[data][ratio]'  value='1' >";
//            $res[] = $addData;
//
//            $addData = array();
//            $addData["text"] = "Vollbild";
//            if ($editContent[data][zoom]) $checked="checked='checked'"; else $checked="";
//            $addData["input"] = "<input type='checkbox' $checked name='editContent[data][zoom]'  value='1' >";
//            $res[] = $addData;
//
//            $addData = array();
//            $addData["text"] = "Horizonatle Ausrichtung";
//            if (!$data[hAlign]) $data[hAlign] = "left";
//            $addData["input"] = cmsEdit_HorizontalAlign("editContent[data][hAlign]",$data[hAlign]);
//            $res[] = $addData;
//
//            $addData = array();
//            if (!$data[vAlign]) $data[vAlign] = "top";
//            $addData["text"] = "Vertikale Ausrichtung:";
//            $addData["input"] = cmsEdit_VerticalAlign("editContent[data][vAlign]",$data[vAlign]);
//            $res[] = $addData;

        // }
        return $res;

    }

    function editContent_imageListSettings($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        $res = array();
        
        
        $addData = array();
        $imgRow = $data[imgRow];
        if (!$imgRow) $imgRow = 3;
        $addData["text"] = "Anzahl Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='$imgRow'>";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData = array();
        $imgRowAbs = $data[imgRowAbs];
        if (!$imgRowAbs) $imgRowAbs = 10;
        $addData["text"] = "Abstand Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='$imgRowAbs'>";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[] = $addData;

        $addData = array();
        $imgColAbs = $data[imgColAbs];
        if (!$imgColAbs) $imgColAbs = 10;
        $addData["text"] = "Abstand Zeilen";
        $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='$imgColAbs'>";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[] = $addData;


        $imageIdStr = $data[image];
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

        
        $addData["text"] =  "Bild-Liste";
        $addData["input"] = "Trulla";
        $div = array();
        $div[divname] = "cmsImageList";
        $div[style] = "width:100%;visible:visible;overflow:visible;";
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
        $showData[dataName] = "editContent[data][image]";
        
        $div[content] = $this->editContent_imageList($imageIdStr,$showData);
        

        $addData["div"] = $div;
        $res[imageList][] = $addData;



      
        
        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];

        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;
        
        return $res;    
        
    }
    
    function editContent_imageSliderSettings($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        $res[slider] = array();
        
        $addData = array();
        $addData["text"] = "Wechsel";
        $direction = $editContent[data][direction];
        $input  = $this->slider_direction_select($direction,"editContent[data][direction]",array());
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[slider][] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Auto Loop";
        $loop = $editContent[data][loop];
        $checked = "";
        if ($loop) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][loop]' $checked >";
        $addData["mode"] = "Simple";
        $res[slider][] = $addData;
        
        $addData = array();
        $addData["text"] = "Zeit für Bild in ms";
        $addData["input"] = "<input name='editContent[data][pause]' style='width:100px;' value='".$editContent[data][pause]."'>";
        $addData["mode"] = "More";
        $res[slider][] = $addData;
        
        $addData = array();
        $addData["text"] = "Zeit für Wechsel in ms";
        $addData["input"] = "<input name='editContent[data][speed]' style='width:100px;' value='".$editContent[data][speed]."'>";
        $addData["mode"] = "More";
        $res[slider][] = $addData;
        
        $addData = array();
        $addData["text"] = "Navigation";
        $navigate = $editContent[data][navigate];
        $checked = "";
        if ($navigate) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][navigate]' $checked >";
        $addData["mode"] = "More";
        $res[slider][] = $addData;
        
        $addData = array();
        $addData["text"] = "Einzelauswahl";
        $pager = $editContent[data][pager];
        $checked = "";
        if ($pager) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][pager]' $checked >";
        $addData["mode"] = "More";
        $res[slider][] = $addData;
     
        
//        $addData = array();
//        $addData["text"] = "Zoom Bild";       
//        $checked = "";
//        if ($editContent[data][zoom]) $checked = " checked='checked'";
//        $addData["input"] = "<input type='checkbox' value='1' name='editContent[data][zoom]' $checked >";
//        $res[slider][] = $addData;
//        
//        
//        $addData = array();
//        $addData["text"] = "Höhe";    
//        $addData["input"] = "<input name='editContent[data][sliderHeight]' style='width:100px;' value='".$editContent[data][sliderHeight]."'>";
//        $res[slider][] = $addData;
        
        

        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];

        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
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
        $showData[width] = $frameWidth - 4;
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
    $imageClass->image_show($contentData,$frameWidth);
}



function cmsType_image_editContent($editContent,$frameWidth) {
    $imageClass = cmsType_image_class();
    return $imageClass->editContent($editContent,$frameWidth);
}





?>
