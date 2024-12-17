<?php // charset:UTF-8


class cmsClass_content_showData extends cmsClass_content_edit {
    
    
    function contentShow_link($link,$linkData,$linkStyle,$hiddenLink=0) {
        if (is_string($linkData)) $linkData = $this->contentShow_getLinkData($linkData);
        if (!is_array($linkData)) $linkData = array();
        foreach ($linkData as $key => $value) {
            $str .= "$key => $value <br>";
        }
        
        $linkType = $linkData[type];
        if (!$linkType) $linkType = "page";
        
        
        
        $style = $linkStyle;
        if ($linkData[style]) $style = $linkData[style];
        
        $buttonClass = "Link";
        if ($hiddenLink ) $buttonClass = "Java";
        
        $class = "";
        
        switch ($style) {
            case "main" :  $class.= "main".$buttonClass."Button"; break;
            case "second" :  $class .= "main".$buttonClass."Button mainSecond"; break;
            case "readMore" : $class .= "main".$buttonClass."Button mainReadMore"; break;
        }
        
        
        
        $name = "LINK";
        switch ($linkType) {
            case "url" :
                $href = $linkData[url];
                break;

            case "page" :
                $href = $link;
                $pageId = intval($linkData[page]);
                if ($pageId) {
                    $pageData = $this->page_getData($pageId);
                    if (is_array($pageData)) {
                        $href = $pageData[name].".php";
                        $name = $pageData[title];
                        if (is_array($name)) $name = $this->lgStr($name);
                    }
                }
                break;
                
            default: 
                $str .= "unkown LinkType $linkType <br>";
        }
        
        
        $target = $linkData[target];
        if ($target) $target = "target='$target'"; else $target = "";
        
        if ($linkData[button]) $name = $linkData[button];
        
        
        $str = "";
        $str .= "<a href='$href' $target class='$class' >$name</a>";
        
        
        // $str .= "contentShow_link($link,$linkData,$linkStyle)";
        return $str;
        
    }
    function contentShow_getLinkData($linkStr) {
        $codeList = explode("|",$linkStr);
        $linkData = array();
        for ($i=0;$i<count($codeList);$i++) {
            $str = $codeList[$i];
            if (!$str) continue;
            $pos = strpos($str,":");
            if (!$pos) {
                echo ("NO DOPPEL IN '$str' <br>");
            } else {
                $key = substr($str,0,$pos);
                $value = substr($str,$pos+1);
                $linkData[$key]  = $value;
            }
        }
        return $linkData;
    }
    
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
            
            case "gallery" :
                $res = $this->contentShow_image_gallery($imgWidth,$out);
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
        
        $wireType = "image";
        $wireOn = $this->wireframe_use($wireType);
        // $wireOn = $wireframeData[headLine];
        
        if ($wireOn) {
            // $wireText = $this->contentData[wireframe][$wireType."Text"];
            // echo (" wireOn = $wireOn wireText = $wireText <br>");
            $wireData = array();
            $wireData[orgText]  = $text;          // Normaler Text
            $wireData[id]       = $imageId;
            $wireData[name] = $imageData[name];
            $wireData[title] = $imageData[subTitle];
            //$wireData[wireText] = $wireText;
            $wireData[type] = $wireType;
            $wireData[debug]    = 0;
            
            
            $width = $imageWidth;
            if (!intval($width)) $width = $frameWidth;
            $height = $imageHeight;
            if (!intval($height)) $height = floor($width / 4 * 3);
            
            $wireData[width] = $width;
            $wireData[height] = $height;
            
            $imageText = $this->wireframe_image($wireData);
            // return $imageText;
            
                    
            // $text = $useClass->wireframe_text($wireData);
        }
        
        
//        if ($this->wireFrameState AND $this->wireFrameContentEnabled) {
//            // $wireFrameOn AND $wireframeState) {
//            $wireframeImage = $this->contentData[wireframe][image];
//            if ($wireframeImage ) {
//                $wireframeImageText = $this->contentData[wireframe][imageText];
//                
//                $info = array();
//                $info[id]   = $imageId;
//                $info[nr]   = 1;
//                $info[name] = $imageData[name];
//                $info[title] = $imageData[subTitle];
//                $wireframeImageText = $this->text_wireText("BILD ",$wireframeImageText,$info);
//            }
//        }
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

        
        //if ($wireframeImage) {
        
        if ($wireOn) {
//            $wireInfo = array();
//            $wireInfo[name] = $imageData[name];
//            $wireInfo[title] = $imageData[subTitle];
//            $wireInfo[divClass] = "zoom_div";
//            $wireInfo[wireColor] = "#ff00ff";
//            
//            $width = $imageWidth;
//            if (!intval($width)) $width = $frameWidth;
//            $height = $imageHeight;
//            if (!intval($height)) $height = floor($width / 4 * 3);
//            // echo ("$width $height <br>");
//
           if ($zoom) {
                $bigWidth = 800;
                $bigHeight = floor($bigWidth * $height / $width);
                $bigImageStr = $this->text_wireImage($bigWidth,$bigHeight,null,$wireInfo);
                //  $bigImageStr = cmsWireframe_image($bigWidth,$bigHeight,"#ff00ff");
                //echo ($bigImageStr."<br>");

                $outPut .= "<a href='$bigImageStr' class='zoomimage'>";
            }

            $outPut .= $imageText;
//            if ($wireframeImageText) {
//               
//                $outPut .= $imageText; //  $this->text_wireImage($width, $height,$wireframeImageText,$wireInfo);
//
////                $outPut .=  cmsWireframe_frameStart_str($width, $height,"zoom_Div");
////                // $out .= "<a href='$bigImageStr' class=''>$wireframeImageText</a>";
////                $outPut .= $wireframeImageText;
////                $outPut .= cmsWireframe_frameEnd_str();
//                
//
//            } else {
//                $imgStr = $this->text_wireImage($width,$height,null,$wireInfo);
//                // $imgStr .= cmsWireframe_image($width,$height,"#ff00ff");
//                // if ($zoom) echo ("<a href='$bigImageStr' class='zoomimage'>");
//                $outPut .= "<img src='$imgStr' class='noBorder' />";
//                // if ($zoom) echo ("</a>");
//            }

            if ($zoom) {
                $outPut .= "</a>";
            }
            //  $imageText = $wireframeData[imageText];
             // $imageText = $outPut ;
           
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
        // $outPut .= "RowWidth = $rowWidth / $frameWidth <br>";
        $imgRowAbsLast = $frameWidth - ($imgRow*$rowWidth) - (($imgRow-2)*$imgRowAbs);
        
        // echo ("last = $imgRowAbs $imgRow $rowWidth $imgRowAbs <br>");
        
        $imgList = $data[image];
        if (!$imgList) $imgList = $data[imgList];
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        
        $wireType = "image";
        $wireframeImage = $this->wireframe_use($wireType);
        if ($wireframeImage) {
            $wireImageText = $this->wireframe_wireText($wireType);
            
            $wireData = array();
            $wireData[type] = $wireType;
            $wireData[wireText] = $wireImageText;
        }
        
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
        
        
        $zoomSize = 800;
        // $ratio = 4/3;
        $nr = 0;
        $lnr = 0;
        if (count($imgDataList)) {
            $outPut .= div_start_str("imageList",$divData);
            for ($i = 0; $i<count($imgDataList); $i++) {
                $imageId = $imgDataList[$i];
                
                $width = $rowWidth;
                if ($ratio) {
                    $height = floor($width / $ratio);
                } else {
                    $height = floor($width / 4 * 3);
                }
                
                
                if ($wireframeImage) {
                    $wireData[nr] = $i+1;
                    $wireData[idStr] = $imageId;
                    $wireData[name] = "trulla";
                    $wireData[width] = $width;
                    $wireData[height] = $height;
                    $imgStr = $this->wireframe_image($wireData);
                    if ($zoom ) {
                        $bigWidth = $zoomSize;
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
                        $bigImage = cmsImage_showImage($imgData, $zoomSize,array("out"=>"url"));                        
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

        echo ("HIER <br />");
        if ($out) return $outPut;
        
        echo ($outPut);
    }

    
    function contentShow_image_gallery($imgWidth,$out="") {
        $contentData = $this->contentData;
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
       
        
        // GET IMAGE LIST
        $imgHelp = explode("|",$data[image]);
        $imageList = "";
        for ($i=0;$i<count($imgHelp);$i++) {
            $imageId = intval($imgHelp[$i]);
            if ($imageId) {
                $imageData = cmsImage_getData_by_Id($imageId);
                $imageList[$imageId] = $imageData;
            }            
        }
        
        $direction = $data[galleryDirection];
        if (!$direction) $direction = "vertical";
        $position  = $data[galleryPosition];
        
        
        $thumbSize = intval($data[thumbSize]);
        $ratio = $data[ratio];
        if ($ratio) {
            $ratioX = $data[ratioX];
            if (!$ratioX) $ratioX = 1;
            $ratioY = $data[ratioY];
            if (!$ratioY) $ratioY = 1;
            $ratio = 1.0 * $ratioX / $ratioY;            
        }
        
        // echo ("<b>GALLERY SHOW $imgWidth dir=$direction pos = $position </b><br>");
        
        
        $moveSize = 10;
        $zoomSize = 800;
        $zoom = $data[zoom];
       
        
        $thumbDistance = $data[thumbDistance];
        $thumbCount    = $data[thumbCount];
        
        $imageStyle = "";
        $imageClass = "";
        
        $thumbData = array();
        $thumbData[out] = "url";
        $thumbData[ratio] = $ratio;
        $thumbData[crop] = $data[crop];
        switch ($direction) {
            case "vertical" :
                if (!$position) $position = "bottom";
                
                $thumbData[frameHeight] = $thumbSize;
                if ($ratio) $thumbData[frameWidth] = intval($thumbSize * $ratio);
                
                $imageStyle .= "margin-right:".($thumbDistance/2)."px;margin-left:".($thumbDistance/2)."px;";
                
                $moveStyle = "width:".($imgWidth-2*$moveSize)."px;";
                
                break;
                
                
            case "horizontal" :
                if (!$position) $position = "right";
                
                $thumbData[frameWidth] = $thumbSize;
                if ($ratio) $thumbData[frameHeight] = intval($thumbSize / $ratio);
                $imageStyle .= "margin-bottom:".($thumbDistance/2)."px;margin-top:".($thumbDistance/2)."px;";
                break;    
        }
        switch ($position) {
        }

        
        $bigWidth = $imgWidth;
        if ($direction == "horizontal") $bigWidth = $imgWidth - $thumbSize - 10;
        if ($ratio) $bigHeight = intval($bigWidth / $ratio);
            
        
        $wireType = "image";
        $wireframeImage = $this->wireframe_use($wireType);
        if ($wireframeImage) {
            $wireData = array();
            $wireImageText = $this->wireframe_wireText($wireType);
            
            $wireThumbData = array();
            $wireThumbData[wireText] = $wireImageText;
            $wireThumbData[width] = $thumbData[frameWidth];
            $wireThumbData[height] = $thumbData[frameHeight];
            
            $wireBigData = array();
            $wireBigData[wireText] = $wireImageText; // WireText aus dataWireframe 
            $wireBigData[width] = $bigWidth;
            $wireBigData[height] = $bigHeight;
        }
        
        $bigStr = "";
 
        $bigShow = array();
        $bigShow[out] = "url";
        $bigShow[ratio] = $ratio;
        $bigShow[crop] = $data[crop];
        $bigShow[frameWidth] = $bigWidth;
        $hidden = 0;
        $nr = 0;
        
        if ($zoom AND $wireframeImage) {
            $zoomWidth = $zoomSize;
            $zommHeight = floor($zoomWidth / $ratio);
            $bigImage = $this->wireframe_imageFile($zoomWidth, $zoomHeight); 
        }
        
        
        foreach ($imageList as $imageId => $imageData) {
            $bigClass = "";
            
            $setClass = "cmsGalleryImage_$imageId cmsGalleryImage ";
            if ($hidden) $setClass .= "cmsGalleryImage_hidden ";
            else $hidden = 1;
            $setClass .= $bigClass;
            
            $bigStr .= "<div class='$setClass' >";
            if ($wireframeImage) {
                $nr++;
                $wireBigData[nr]       = $nr;
                $wireBigData[id]       = $imageId;
                $title = $imageData[subTitle];
                if (!$title) $title = $imageData[name];
                $wireBigData[name]     = $title;
                 
                // foreach ($imageData as $key => $value) echo ("$key = $value <br>");
                $imgUrl = $this->wireframe_image($wireBigData);
                if ($zoom) {
                    //$bigStr .= "<a class='zoomimage' href='$bigImage'>";
                    $bigStr .= $imgUrl;
                    //$bigStr .= "</a>";
                } else {
                    $bigStr .= $imgUrl;
                }                
            } else {
                $imgUrl = cmsImage_showImage($imageData, $bigWidth, $bigShow);
                if ($zoom) {
                    $zoomImage = cmsImage_showImage($imageData, $zoomSize,array("out"=>"url"));
                    $bigStr .= "<a class='zoomimage' href='$zoomImage'>";                    
                }
                $bigStr .= "<image src='$imgUrl'  />";
                if ($zoom) {
                    $bigStr .= "</a>";
                }
            }
            
            //style='$bigStyle' class='$setClass'
            $bigStr .= "</div>";
        }
        
        
        
        $thumbStr = "<div class='cmsGalleryMoveFrame' style='$moveStyle'>";
        $active = 1;
        $nr = 0;
        //cmsGalleryThumbnail_10 cmsGalleryThumbnail cmsGalleryThumbnail_selected
        foreach ($imageList as $imageId => $imageData) {
            
            $setClass = "cmsGalleryThumbnail_".$imageId." cmsGalleryThumbnail $imageClass ";
            if ($active) {
                $setClass .= "cmsGalleryThumbnail_selected ";
                $active = 0;                
            }

            
            if ($wireframeImage) {
                $nr++;
                $wireThumbData[nr]       = $nr;
                $wireThumbData[id]       = $imageId;
                $title = $imageData[subTitle];
                if (!$title) $title = $imageData[name];
                $wireThumbData[name] = $title;
                $wireThumbData["class"] = $setClass;
                $wireThumbData["style"] = $imageStyle;
                $imgStr = $this->wireframe_image($wireThumbData);
                
            } else {
               $imageSize = $thumbData[frameWidth];
               $imgUrl = cmsImage_showImage($imageData, null, $thumbData);
               $imgStr = "<image src='$imgUrl' style='$imageStyle' class='$setClass'  />";
            }
            
            $thumbStr .= $imgStr;
        }
        $thumbStr .= "</div>";
        // echo ("DIRECTION $direction POSITION $position <br>");
        switch ($direction) {
            
            case "horizontal" :
                $styleTop = "border-left-width:".($thumbSize/2)."px;border-right-width:".($thumbSize/2)."px"; 
                $before = "<div class='cmsGalleryMove cmsGalleryMove_vertical cmsGalleryMove_up' style='$styleTop' ></div>"; // style='width:".$thumbSize."px;'
                $after  = "<div class='cmsGalleryMove cmsGalleryMove_vertical cmsGalleryMove_down'  style='$styleTop' ></div>";
                $thumbStr = $before.$thumbStr.$after;
                
                switch ($position) {
                    case "left" : 
                        echo ("<div class='cmsGalleryThumbnailFrame' style='display:inline-block;width:".$thumbSize."px;margin-right:10px;' >");
                        echo ($thumbStr);
                        echo ("</div>");
                        
                        echo ("<div class='cmsGalleryImageFrame' style='display:inline-block;width:".$bigWidth."px;'>");
                        echo ($bigStr);
                        echo ("</div>");
                        
                        
                        break;
                    
                    case "right" : 
                        echo ("<div class='cmsGalleryImageFrame' style='display:inline-block;width:".$bigWidth."px;margin-right:10px;'>");
                        echo ($bigStr);
                        echo ("</div>");
                        
                        echo ("<div class='cmsGalleryThumbnailFrame' style='display:inline-block;width:".$thumbSize."px;' >");
                        echo ($thumbStr);
                        echo ("</div>");
                        break;
                }
                break;
            
            case "vertical" :
                
                $styleTop = "border-top-width:".($thumbSize/2)."px;border-bottom-width:".($thumbSize/2)."px"; 
                $before = "<div class='cmsGalleryMove cmsGalleryMove_horizontal cmsGalleryMove_horizontal_left' style='$styleTop' ></div>"; // style='width:".$thumbSize."px;'
                $after  = "<div class='cmsGalleryMove cmsGalleryMove_horizontal cmsGalleryMove_horizontal_right'  style='$styleTop' ></div>";
                
                
                $thumbStr = $before.$thumbStr.$after;
                
                switch ($position) {
                    case "top" : 
                        echo ("<div class='cmsGalleryThumbnailFrame' style='display:block;margin-bottom:10px;height:".$thumbSize."px;' >");
                        echo ($thumbStr);
                        echo ("</div>");
                        
                        echo ("<div class='cmsGalleryImageFrame' style='display:block;' >");
                        echo ($bigStr);
                        echo ("</div>");
                        
                        
                        break;
                    
                    case "bottom" : 
                        echo ("<div class='cmsGalleryImageFrame' style='display:block;margin-bottom:10px;'>");
                        echo ($bigStr);
                        echo ("</div>");
                        
                        echo ("<div class='cmsGalleryThumbnailFrame' style='display:inline-block;height:".$thumbSize."px;' >");
                        echo ($thumbStr);
                        echo ("</div>");
                        break;
                }
                break;
                
                
                
        }
        
        
        
        
        
        
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
        $zoomSize = 800;
        
        $wireType = "image";
        $wireframeImage = $this->wireframe_use($wireType);
        if ($wireframeImage) {
            $wireData = array();
            $wireData[type] = $wireType;
            
        } 
        $showData = array();
        $showData[frameWidth] = $frameWidth-(2*$absHor);
        $showData[frameHeight] = $imageHeight;
        $showData[ratio] = $ratio;                    
        $showData[crop] = $crop;
        $showData[hAlign] = $data[hAlign];            
        $showData[vAlign] = $data[vAlign];
        $showData[resize] = $data[resize];

        for ($i=0;$i<count($imgIdList);$i++) {
            $imgStr = $imgIdList[$i];
            if (!$imgStr) continue;
            
            $imageId = intval($imgStr);
            $imageData = cmsImage_getData_by_Id($imageId);
            
           
           
            if ($wireframeImage) {
                $wireOut = "";

                $width = $imageWidth;
                if (!intval($width)) $width = $frameWidth;
                $height = $imageHeight;
                if (!intval($height)) $height = floor($width / 4 * 3);
                
                $wireData[width] = $width;
                $wireData[height] = $height;
                $wireData[nr] = $i+1;
                $wireData[id] = $imageId;
                
                $wireOut = "";
                if ($zoom) {
                    $bigImage = $this->wireframe_imageFile($zoomSize, floor($zoomSize * $height / $width));
                    $wireOut .= "<a class='zoomimage' href='$bigImage' >";
                }
                
                $wireOut .= $this->wireframe_image($wireData);
                if ($zoom) {
                    $wireOut .= "</a>";
                }
                
                $imgDataList[] = $wireOut;
               
            } else {
                $imageOut = "";
                if ($zoom) {
                    $imgStrBigStr = cmsImage_showImage($imageData, 800,array("out"=>"url"));
                    $imageOut .= "<a class='zoomimage' href='$imgStrBigStr'>";
                }

                $imageOut .= cmsImage_showImage($imageData, $frameWidth-(2*$absHor), $showData);
                
                if ($zoom) {
                    $imageOut .= "</a>";
                }
                $imgDataList[] = $imageOut;               
            }            
        }
        
        $sliderName = "ImageSlider_".$this->contentId;
        
        
        $outPut .= slider::show($sliderName, $imgDataList, $contentData);
        
        $outPut .= div_end_str("imageSlider",$divData);
        if ($out) return $outPut;
        else echo($outPut);
    }
    
    function data_showFilter() {
        
        
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        
        $filterDataList = $this->editContent_filter_getList();
        
        $this->data_showFilter_dataFilter($filterDataList);
        
        $this->filterShow = 0;
        
        $filter = array();
        
        $customFilter = array();
        $contentFilter = array();
        
        
        foreach ($data as $key => $value) {
            if (substr($key,0,13) == "customFilter_") {
                $view = $value;
                $type = substr($key,13);
                $mode = $data["customFilterView_".$type];
                
                $add = array("type"=>"custom","mode"=>$mode,"view"=>$view);
                if ($_GET[$type]) $add[value] = $_GET[$type];
                if ($_POST[$type]) $add[value] = $_POST[$type];
                $customFilter[$type] = $add;
            }
            if (substr($key,0,7) == "filter_") {
                $type = substr($key,7);
                $add = array();
                $add[value] = $value;
                $add[type]  = "content";
                $contentFilter[$type] = $add;
                
                if ($value) {
                    $this->filter[$type] = $value;
                
                    // echo ("[data][filter_ $type ] = $value <br />");
                }
                
                
                // echo ("<h1> filter $key $value </h1>");
            }
            
            // echo ("Filter $key (".substr($key,0,7).")=> $value <br>");
        }
        
        
        // $this->showList_customFilter($contentData,$frameWidth,$divName=null);
        
       
        
//        foreach ($filterDataList as $key => $value) {
//            echo ("filterData $key => $value <br>");
//        }
        
        $getFilter = $this->filter;
        foreach($_GET as $key => $value) {
            if (substr($key,0,7) == "filter_") {
                $filterKey = substr($key,7);
                if ($value != "none") {
                    $getFilter[$filterKey] = $value;
                    $this->filter[$filterKey] = $value;
                } else {
                    unset($this->filter[$filterKey]);
                }
                
                
                
                // echo ("Filter $filterKey = $value <br> ");
            } else {
                // echo ("Get $key is not Filter '".substr($key,0,7)."' <br>");
            }
        }
        
        $filterStr = "";
        foreach ($customFilter as $key => $value) {
            $mode = $value[mode];
            
            $name = $key;
            $filterData = array();
            if ($filterDataList[$key][name]) {
                $name = $filterDataList[$key][name];
                $filterData = $filterDataList[$key];
            }
            
            $filterStr .= div_start_str("cmsCustomFilter_line");
            $filterStr .= div_start_str("cmsCustomFilter_left");
            $filterStr .= "$name:";
            $filterStr .= div_end_str("cmsCustomFilter_left");
            $filterStr .= div_start_str("cmsCustomFilter_right");
            $filterStr .= $this->data_showFilter_item($key,$mode,$filterData,$getFilter);
            $filterStr .= div_end_str("cmsCustomFilter_right");
            $filterStr .= div_end_str("cmsCustomFilter_line","before");
        }
        
        if ($filterStr) {
            
            div_start("cmsFilterFrame");
            
            $buttonClass = "cmsFilterButton";
            if ($this->filterShow) $buttonClass.= " cmsFilterButtonActive";
            div_start($buttonClass);
            echo ("&nbsp;");
            div_end($buttonClass);

            $filterClass = "cmsFilterList";
            if (!$this->filterShow) $filterClass .= " cmsFilterHidden";

            div_start($filterClass);
            echo ("FILTER LISTE <br>");
            echo ("<form method='POST' >");
            echo ($filterStr);
            echo ("<input type='submit' class='mainInputButton' value='filtern' name='filter' />");
            echo ("</form>");

            if ($_SESSION[showLevel] >= 9) {
                div_start("cmsFilterOutput");
                div_start("cmsFilterOutputButton");
                echo ("Filter zeigen");
                div_end("cmsFilterOutputButton");
                div_start("cmsFilterOutputList cmsFilterHidden");
                foreach ($this->filter as $key => $value) {
                    span_text($key.":",100);

                    echo ("$value <br />");               
                }
                div_end("cmsFilterOutputList cmsFilterHidden");

                div_end("cmsFilterOutput");
            }

            div_end($filterClass);
            div_end("cmsFilterFrame");
        }
        
        foreach ($this->filter as $key => $value) {
            switch ($value) {
                case "on" : $this->filter[$key] = 1; break;
                case "off" : $this->filter[$key] = 0; break;    
            }
        }
        return $filter;                
    }
    
    function data_showFilter_dataFilter($filterData) {
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        foreach ($data as $key => $value ) {
            if (substr($key,0,7)!= "filter_") continue;
            $filterKey = substr($key,7);            
            if ($value) {
                // echo ("SET FILTER $filterKey = $value <br>");
                
               $filterData = $this->filter_select_getList($filterKey);
//                foreach ($filterData as $k => $v ) {
//                    echo ("FD = $k => $v <br>");
//                }
//                
               $myFilterData = $filterData[$value];
//                
//                
//                
                if (is_array($myFilterData)) {
                    foreach ($myFilterData[filter] as $k => $v ) {
                        $this->filter[$k]=$v;
                        //echo ("SET FILTER $k => $v <br>");
                    }
                }
            }
        }
    }
        
    
    
    function data_showFilter_item($key,$mode,$filterData=array(),$getFilter=array()) {
        $str = "";
        $type = $filterData[type];
        $filter_data = $filterData[filter];
        $filter_sort = $filterData[sort];
        $filter_name = $filterData[dataName];
        
        
        $str = ""; // "FILTER $key $mode $type <br>";
        // $str.="<h1>TYPE = $type </h1>";
//        foreach ($filterData as $k => $v ) {
//            $str .= " $k = $v | ";
//        }
        // $str .= "<br>";
        
        $list = array();
        switch ($type) {
            case "dataFilter" :
                $list = array("none"=>"Alle","0"=>"NEIN","1"=>"JA");
                break;
            default :
                // $str .= "get LIst for '$key' ";
                $filterList = $this->filter_select_getList($key);
                // show_array($filterList);
                if (is_array($filterList)) {
                    $list["none"] = "nicht gewählt";
                    $filter_data = array();
                    foreach ($filterList as $filterKey => $filterName) {
                        if (is_array($filterName)) {
                            $filter_data[$filterKey] = $filterName[filter];
                            $str .= "SET FILTER DATA for $filterKey = $filterName[filter] <br>";
                            $filterName = $filterName[name];
                        }
                        
                        $list[$filterKey] = $filterName;
                    }            
                }
                
        }
        
       // if ($mode == "toggleSc")
        $mode = "dropdown";
        
      
        if (!is_null($getFilter[$key])) {
            $getValue = $getFilter[$key];
            // $str .= "FILTER '$key' gesetzt = $getValue <br>";
            if (is_array($filter_data)) {
                // $str .= "filter_data is array ".count($filter_data)."<br>";
                if (count($filter_data)) {

                    if (is_array($filter_data[$getValue])) {
                        $str .= " USE FILTER From $getValue <br> ";
                        $filter_data = $filter_data[$getValue];
                    }

                    show_array($filter_data);


                    foreach ($filter_data as $filterKey => $filterValue ) {
                        if ($filterValue == "setValue") {
                            $filterValue = $getFilter[$key];

                        }  
                        $str .= "SET FILTER $filterKey = '$filterValue' <br>";
                        $this->filter[$filterKey] = $filterValue;
                        $this->filterShow = 1;

                    }
                } else {
                    // $str .= "NO FILTER DATA $key $getValue  $filter_name<br>";
                    if ($filter_name) {
                        $this->filter[$filter_name] = $getValue;
                        $this->filterShow = 1;
                    }
                }
            } else {
                $str .= "filter_data is NO array !!<br>";
            }
            
            
        }
        
        
        
        
        
        
        switch ($mode) {
            case "dropdown" :
                $str .= "<select name='filter_$key' style='width:200px;' >";
                foreach ($list as $item => $value) {
                    
                    $selected = "";
                    if (!is_null($getFilter[$key]) AND $getFilter[$key] == $item) { 
                        $selected = "selected='selected'";
                        if ($filterValue) {
                            // foreach ($filterValue as $filterKey => $filterValue) 
                        }
                    }
                    $str .= "<option value='$item' $selected >$value</option>";
                }
                $str .= "</select>";
                break;                                        
        }
        
//        $str = "FILTER $key $mode <br>";
//        foreach ($filterData as $key => $value ) {
//            $str .= " $key = $value | ";
//        }
//        $str .= "<br>";
//        
        return $str;
        
        
        
        
    }
    
    
    // ********************************************************************** //
    // ** SHOW DATA LIST                                                    ** //
    // ********************************************************************** //
    
    function data_showList($dataType,$dataList) {
        $frameWidth = $this->frameWidth;
        
       
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        
        $showList = array();
        foreach($this->contentData[data] as $key => $value ) {
            $pos = strpos($key,"_show");
            if ($pos) {
                $showKey = substr($key,0,$pos);
                $showWidth = $this->contentData[data][$showKey."_width"];
                // echo ("SHOW LIST $showKey $value $showWidth <br />");
                $showList[$showKey]=array("name"=>"NAME $key","width"=>$showWidth);
                                
            }
        }
        
        $showList = $this->dataBox_showList_sort($data, $showList);
        
        $showData = array();
            // pageing
        $showData[pageing] = array();
        $showData[pageing][count] = 10;
        $showData[pageing][showTop] = 0;
        $showData[pageing][showBottom] = 0;
        $showData[pageing][viewMode] = "small"; // small | all
        $showData[titleLine] = 0;
        
        
        $showData = array();
        $showData[pageing] = array();
        foreach ($this->contentData[data] as $key => $value ) {
            switch ($key) {
                case "titleLine" : $showData["titleLine"] = $value; break;
                case "pageing"  : break; //$showData["pageing"] = $value; break;
                case "pageingCount"  : $showData[pageing][count] = $value; break;
                case "pageingTop"  : $showData[pageing][showTop] = $value; break;
                case "pageingBottom"  : $showData[pageing][showBottom] = $value; break;
                default:
                    // echo ("$key => $value <br>");
            }
        }
        
        // SHOW FILTER
        // $this->showList_customFilter($this->contentData,$this->frameWidth);
       
        // SHOW LIST
        $this->showList_List($dataList,$showList,$showData,$frameWidth);
        
        
    }
     
    // ********************************************************************** //
    // ** SHOW DATA BOX                                                    ** //
    // ********************************************************************** //
    
    
    
    function data_showTable($dataType,$dataList,$contentData,$frameWidth) {
        $data = $contentData[data];
        $frameWidth = $this->innerWidth;
        $contentData = $this->contentData;
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        if ($dataType != $this->tableName) {
            echo ("<h2> Diffrent Type dataType=$dataType tableName=$this->tableName </h2>");
        }
       
        
        
        // $this->showList_List($article,$showList,$showData,$frameWidth);
        
        
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
        
        $dataRow = intval($data[dataRow]);
        $dataRowAbs = intval($data[dataRowAbs]);
        $dataColAbs = intval($data[dataColAbs]);
        $dataColHeight = intval($data[dataColHeight]);
        
        if (!$dataRow) $dataRow = 3;
        if (!$dataRowAbs) $dataRowAbs = 10;
        if (!$dataColAbs) $dataColAbs = 10;
        $rowWidth = floor(($frameWidth - (($dataRow-1)*$dataRowAbs )-($dataRow*2*$border) - ($dataRow*2*$padding)) / $dataRow);

        
        
        if ($this->mobileEnabled) {
            $mobileTableView = $data[mobileTableView];
            $mobileTarget = $this->targetData[target];
            $mobileOrientation = $this->targetData[orientation];
            
            // echo ("Target $mobileTarget orientation=$mobileOrientation <br>");
            switch ($mobileTableView) {
                case "below" : 
                    if ($mobileTarget != "pc") $dataRow = 1;
                    break;
                case "show" : break;
                
                case "columnCount" :
                    if ($mobileTarget != "pc") {
                        $columnCount = $data[mobileTableView_count];
                        if (!$columnCount) $columnCount = 1;
                        $dataRow = $columnCount;
                        $rowWidth = floor(($frameWidth - (($dataRow-1)*$dataRowAbs )-($dataRow*2*$border) - ($dataRow*2*$padding)) / $dataRow);
                    }
                    break;
                    
                
                case "columnWidth" :
                    if ($mobileTarget != "pc") {
                        $minColumnWidth = $data[mobileTableView_width];
                        if (!$minColumnWidth) $minColumnWidth = 150;
                        if ($rowWidth < $minColumnWidth) {
                            $colCount = 0;
                            $checkWidth = $minColumnWidth;
                            // echo ("colCount = $colCount checkWidth = $checkWidth < $frameWidth <br>");
                            while ($checkWidth < $frameWidth) {
                                $colCount++;
                                $checkWidth = $checkWidth + $minColumnWidth + $dataRowAbs;
                                // echo ("colCount = $colCount checkWidth = $checkWidth < $frameWidth <br>");                            
                            }      
                            if (!$colCount) $colCount = 1;
                            $dataRow = $colCount;
                            $rowWidth = floor(($frameWidth - (($dataRow-1)*$dataRowAbs )-($dataRow*2*$border) - ($dataRow*2*$padding)) / $dataRow);
                        }
                    }
                    break;
                    
                default :
                    echo ("unkown TABLEVIEW is mobileTableView ($mobileTableView) <br>");
            }
            
        }
        
        
        // show_array($projectList[0]);
        // div_start($dataType."List","width:".$frameWidth."px;");
        div_start($dataType."List","display:block;"); //:".$frameWidth."px;");

        $border = 1;
        $padding = 5;
        
        
        
        if (!$dataRow) $dataRow = 3;
        if (!$dataRowAbs) $dataRowAbs = 10;
        if (!$dataColAbs) $dataColAbs = 10;
        $rowWidth = floor(($frameWidth - (($dataRow-1)*$dataRowAbs )-($dataRow*2*$border) - ($dataRow*2*$padding)) / $dataRow);

        $divName = "cmsFilterList";        
       //  $this->showList_customFilter($contentData,$frameWidth,$divName);

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
            if ($this->mobileEnabled) {
                $perc = 100.0 * $rowWidth / $frameWidth;
                $style.= "width:".$perc."%;";
                if ($nr<$dataRow) {
                    $perc = 100.0 * $dataRowAbs / $frameWidth;
                    $style.= "margin-right:".$perc."%;";
                }
            } else {   
                $style .= "width:".$rowWidth."px;";
                if ($nr<$dataRow) $style.= "margin-right:".$dataRowAbs."px;";
            }
            $style .= "float:left;";
            $style .= "border-width:".$border."px;";
            if ($dataColHeight) $style .= "height:".$dataColHeight."px;overflow:hidden;";
            if ($padding) $style .= "padding:".$padding."px;";
            $boxData[style] = $style;
            
            $divItemName = $dataType."ListItem tableItem";

            
            if ($clickAction) $divItemName .= " tableItemClick";
            // echo ("GOPAGE $myData[goPage] Datentyp =$dataType / $this->tableName<br>");
            div_start($divItemName,$boxData);
            if ($clickAction) {
                $goPage = $myData[goPage];
                // echo ("GoLink $goPage <br>");
                echo ("<a href='$goPage' class='hiddenLink' >$myData[name]</a>");
            }
            $this->dataBox_id = $myData[id];
            $this->dataBox_nr = $i+1;
            $this->dataBox_name = $myData[name];
            
            //  echo ("Show BOX id= $this->dataBox_id nr=$this->dataBox_nr name=$this->dataBox_name <br>");
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
    }
    
    function dataBox_showKey($key) {
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        
        $show = intval($data[$key."_show"]);
        
        if (!$this->mobileEnabled) return $show;
        
        $mobileOn = $this->targetData[enabled];
        $mobileTarget = $this->targetData[target];
        $mobileOrientation = $this->targetData[orientation];
        $mobileShow = $data[$key."_mobileShow"];

        if ($mobileTarget == "pc") {
            if ($show AND $mobileShow == "only") return 0;
            return $show;
        }
        
        // VIEW is in MOBILE MODE
        
        switch ($mobileShow) {
            case "only" : $show = 1; break;
            case "show" : break;
            case "hide" : $show = 0; break;
            case "landscape" :
                $class = "";
                $class .= " hidePortrait";
                if ($mobileOrientation != $mobileShow) $class.= " orientationHidden";
                // echo ("Class for lanscape = $class <br />");   
                $show = $class;
                break;
            case "portrait" :
                $class = "";
                $class .= " hideLandscape";
                if ($mobileOrientation != $mobileShow) $class.= " orientationHidden";
                // echo ("Class for portrait = $class <br />");                
                $show = $class;
                break;
            default :
                echo "unkown mobileShow ($mobileShow) for key ($key) show=$show<br>";
                
        }            
        return $show;
        
    }


    function dataBox_show($dataType,$data,$contentData,$frameWidth,$showList=null) {
        $dataData = $contentData[data];
        if (!is_array($dataData)) $dataData = array();
        
        if (!is_array($showList)) {
            $showList = $this->dataShow_List($contentData);
        }
        // add Dynamics
        $add = $this->databox_editDynamic();
        foreach ($add as $key => $value) $showList[$key] = $value;
        
        $showList = $this->dataBox_showList_sort($dataData, $showList);


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
        
        $wireFrameOn = $this->wireframeContentEnabled; // $dataData[wireframe];
        $wireframeState = $this->wireframeState; // cmsWireframe_state();
        if ($wireFrameOn AND $wireframeState) {
            
            $wireframeData = $this->wireframeContentData; // $contentData[wireframe];
           
            
            // echo ("wireFrameOn = $wireFrameOn wfS = $wireframeState wfD = $wireframeData <br>");
            if (!is_array($wireframeData)) $wireframeData = array();
            $wireframeNr = $data[id];
            // echo ("<h1> WirfreameNr $wireframeNr </h1>");
        }
         
        $box_mobileView = "default";
        if ($this->mobileEnabled AND $this->targetData[target] != "pc") {
            $box_mobileView = $dataData[mobileDataBoxView];
            echo ("MOBILE VIEW $box_mobileView !!! <br />");
        }
        
        
        foreach ($showList as $key => $value) {
            // $show = $dataData[$key."_show"];
            
            $show = $this->dataBox_showKey($key);
            $addDiv = 0;
            if (is_string($show)) {
                $addDiv = $show;
                $show = 1;                
            }
            
            if ($show) {
//                $no_linebreak = $dataData[$key."_lb"];
//                if ($no_linebreak) echo ("No Linebrak for $key <br>");
                        
//                foreach ($value as $k => $v ) echo ("$k=$v | ");
//                echo ("<br>");
                $out = "";
                $keyName = $value;
                if (is_array($keyName)) {
                    $keyName = $value[name];
                }
                
                $targetWidth = $boxWidth;
              
                $checkBox = $dataData[$key."_checkbox"];
                
                $content = $data[$key];
               
                if ($wireFrameOn AND $wireframeState) {
                    $wireData = array();
                    $wireData[orgText]  = $content;          // Normaler Text
                    $wireData[nr]       = $this->dataBox_nr;
                    $wireData[id]       = $this->dataBox_id;
                    $wireData[name]     = $this->dataBox_name;
                    $wireData[debug]    = 0;
                    
                   
                    
                    switch($key) {
                        case "name" :
                            $wireType = "headLine";
                            $wireOn = $this->wireframe_use($wireType);
                            if ($wireOn) {
                                $wireData[type] = $wireType;                                
                                $content = $this->wireframe_text($wireData);                                 
                            }
                            break;
                        case "subName" :
                            $wireType = "subHeadLine";
                            $wireOn = $this->wireframe_use($wireType);
                            if ($wireOn) {
                                $wireData[type] = $wireType;
                                $content = $this->wireframe_text($wireData);                                
                            }
                            break;
                            
                            
                        case "info" :
                            $wireType = "text";
                            $wireOn = $this->wireframe_use($wireType);
                            if ($wireOn) {
                                $wireData[type] = $wireType;
                                $content = $this->wireframe_text($wireData);                                
                            }
                            break;
                        case "longInfo" :
                            $wireType = "text";
                            $wireOn = $this->wireframe_use($wireType);
                            if ($wireOn) {
                                $wireData[type] = $wireType;
                                $content = $this->wireframe_text($wireData);                                
                            }
                            break;
                    }
                }
                
                
                
               
                
                $pos = $dataData[$key."_position"];
                $checkBox = $dataData[$key."_checkbox"];
                $description = $dataData[$key."_description"];
                $view = $dataData[$key."_view"];
                $type = $value[type];

                if ($box_mobileView) {
                    switch ($box_mobileView) {
                        case "below" :
                            $pos = "top";
                            // echo ("SET To TOP $box_mobileView <br>");
                            break;
                        default :

                    }   
                }
                
                // echo ("ADD $key to $pos $description <br>");

                $targetWidth = $frameWidth;
                switch ($pos) {
                    case "left" : $targetWidth = $leftWidth; break;
                    case "right" : $targetWidth = $rightWidth; break;
                    case "center" : $targetWidth = $centerWidth; break;
                }
                $out = "";
                switch ($key) {
                    case "image" :
                        $wireImage = 0;
                        $wireImageText = 0;
                        
                        $wireType = "image";
                        $wireOn = $this->wireframe_use($wireType);
                        if ($wireOn) {
                            $wireImage = $wireOn;
                            $wireImageText = $wireframeData[$wireType."Text"];
                        }
//                        if ($wireFrameOn AND $wireframeState) {
//                            $wireImage = $wireframeData[image];
//                            if ($wireImage) $wireImageText = $wireframeData[imageText];
//                            // echo ("WireframeImage $wireImage '$wireImageText' <br>");
//                        }
                        $out = $this->dataBox_show_image($data,$contentData,$content,$view,$targetWidth,$wireImage,$wireImageText);
                        if (!$out) $out = "Bild nicht gefunden";
                        break;
                        
                    case "dynLink" :
                        
                        $link = $data[goPage]; //"games.php";
                        $out = $this->dataBox_show_link($link,$key,$value);
                        // $out .= "link=$link";
                        // foreach ($data as $k => $v) $out .= "data $k = $v <br />";
                        
                        break;
                    
                    case "url" :
                        $link = "games.php";
                        $out = $this->dataBox_show_link($link,$key,$value);
                        break;


                    case "name" : $out = $this->dataBox_show_name($dataType,$key,$content,$targetWidth); break;
                    case "subName" : $out = $this->dataBox_show_name($dataType,$key,$content,$targetWidth); break;
                    case "info" : $out = $this->dataBox_show_info($dataType,$key,$content,$targetWidth); break;
                    case "longInfo" : $out = $this->dataBox_show_info($dataType,$key,$content,$targetWidth); break;
                    
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
                    if ($addDiv) {
                        $out = "<div class='$addDiv'>$out</div>";
                    }
                    
                   
                    
                    $posData[$pos."_text"][$key] = $out;
                }
                
            }
        }
        

        $outPut = $this->dataBox_frameShow($dataType,$posData,$class,$frameWidth);
        
        return $outPut;
        
    }
    
    
    function dataBox_show_link($link,$key,$value) {
        $str = "_show_link($key,$value) ";
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
//        foreach ($data as $k => $v) {
//            if (substr($k,0,strlen($key)) != $key ) continue;
//            $str .= " $k=$v |";
//        }
        $linebreak = $data[$key."_lb"];
        $linkData = $data[$key."_link"];
        $linkStyle = $data[$key."_linkStyle"];
        
        $str = $this->contentShow_link($link, $linkData, $linkStyle);
        
        
        // $str .= "lb=$linebreak link=$link style=$style ";
        
        return $str;
        
        
    }

    function dataBox_frameShow($dataType,$posData,$class,$frameWidth) {
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
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
                $contentFrameName = "positionFrame_content postionFrame_content_$textKey";
                if ($class) $contentFrameName .= " ".$class."_".$textKey;
                $noLineBreak = $data[$textKey."_lb"];
                if ($noLineBreak) $contentFrameName .= " positionFrame_noLineBerak";      
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
                    $contentFrameName = "positionFrame_content postionFrame_content_$textKey";
                    if ($class) $contentFrameName .= " ".$class."_".$textKey;
                    $noLineBreak = $data[$textKey."_lb"];
                    if ($noLineBreak) $contentFrameName .= " positionFrame_noLineBerak";      
                    
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
                    $contentFrameName = "positionFrame_content postionFrame_content_$textKey";
                    if ($class) $contentFrameName .= " ".$class."_".$textKey;
                    $noLineBreak = $data[$textKey."_lb"];
                    if ($noLineBreak) $contentFrameName .= " positionFrame_noLineBerak";      
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
                    $contentFrameName = "positionFrame_content postionFrame_content_$textKey";
                    if ($class) $contentFrameName .= " ".$class."_".$textKey;
                    $noLineBreak = $data[$textKey."_lb"];
                    if ($noLineBreak) $contentFrameName .= " positionFrame_noLineBerak";                        
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
                $contentFrameName = "positionFrame_content postionFrame_content_$textKey";
                if ($class) $contentFrameName .= " ".$class."_".$textKey;
                $noLineBreak = $data[$textKey."_lb"];
                if ($noLineBreak) $contentFrameName .= " positionFrame_noLineBerak";      
                $str .= div_start_str($contentFrameName);                
                $str .= $text;
                $str .= div_end_str($contentFrameName);
            }
            $str .= div_end_str($divName);
        }
        $str.= div_end_str("dataBox dataBox_".$dataType);
        return $str;
    }



    
    function dataBox_show_name($dataType,$key,$content){
        if (!$content) return "";
        $maxChar  = $this->contentData[data][$key."_maxChar"];
        $readMore = $this->contentData[data][$key."_maxChar"];
        $out = "";
        // $out = "show_name $dataType $key <br>";
        
        $divClass = "ItemHead";
        if ($key == "subName") $divClass = "ItemSubHead";
        $divName = $dataType.$divClass." table".$divClass; // ItemSubHead tableItemSubHead""
        $out .= div_start_str($divName);
        
        if (is_array($content)) {
            $content = $this->lgStr($content);
        }
        
        $out .= $this->showText($content,$maxChar,$readMore);
         // $out .= div_start_str($dataType."ItemHead tableItemHead");
        // $out .= "$content";
        $out .= div_end_str($divName);
        return $out;
    }
    
    function dataBox_show_info($dataType,$key,$content) {
        if (!$content) return "";
        
        $maxChar  = $this->contentData[data][$key."_maxChar"];
        $readMore = $this->contentData[data][$key."_maxChar"];
        
        
        
        $out = ""; // show_info $dataType $key <br>";
        // $out .= "$dataType $key $maxChar $readMore <br />";
        
        $divClass = "ItemInfo";
        if ($key == "longInfo") $divClass = "ItemLongInfo";
        $divName = $dataType.$divClass." table".$divClass; // ItemSubHead tableItemSubHead""
        $out .= div_start_str($divName);
        $out .= $this->showText($content,$maxChar,$readMore);
        $out .= div_end_str($divName); 
       //  $out .= "<br />ende!";
        return $out;
    }
    
    function dataBox_show_image($data,$contentData,$content,$view,$boxWidth,$wireImage,$wireImageText) {
        
        //echo ("dataBox_show_image($data,$contentData,$content,$view,$boxWidth,$wireImage,$wireImageText) <br>");
        $dataData = $contentData[data];
        if (!is_array($dataData)) $dataData = array();
        if (intval($content)) $content = "|$content|";
        $imageList = explode("|",$content);
        
        $maxImage = $this->contentData[data][image_maxImage];
        // if ($maxImage) echo ("MaxImage = $maxImage <br>");
        
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
            
            default :
                $imageId = $imageList[1]; 
                break;  
                
        }

//        $wireFrameOn = $this->wireframeContentEnabled; // $dataData[wireframe];
//        $wireframeState = $this->wireframeState; // cmsWireframe_state();
//        if ($wireFrameOn AND $wireframeState) {
//            
//            $wireframeData = $this->wireframeContentData; // $contentData[wireframe];
//            
//            $this->dataBox_id = $myData[id];
//            $this->dataBox_nr = $i+1;
//            $this->databox_name
//       
        if ($imageId) {
            $imageData = cmsImage_getData_by_Id($imageId);
            // foreach ($imageData as $k => $v ) echo ("image $k = $v <br>");
        }
        
        $wireType = "image";
        $wireOn = $this->wireframe_use($wireType);
        
         if ($wireOn) {
             $width = $boxWidth;
             $height = floor($boxWidth / 4 * 3);
             $wireData = array();
             $wireData[orgText]  = $imageData[name];
             $wireData[type]     = $wireType;
             // $wireData[wireText] = $this->wireframeContentData[imageText]; // wireImageText; // WireText aus dataWireframe 
             $wireData[width]    = $width;
             $wireData[height]   = $height;
             $wireData[nr]       = $this->dataBox_nr;
             $wireData[id]       = $this->dataBox_id;
             
             $name = $imageData[name];
             if (!$name) $name = $this->dataBox_name;
             $wireData[name]     = $name;
             
             $imgStr = $this->wireframe_image($wireData);
             return $imgStr;
             
             
             
           
            if ($wireImageText) {
                // $imgStr = $this->text_wi
                
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
        
        $maxImage = $this->contentData[data][image_maxImage];
        // if ($maxImage) echo ("MaxImage = $maxImage <br>");
       

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
        
        
        $showImageCount = count($imageList);
        if ($maxImage AND $showImageCount > $maxImage) {
            // echo ("SET maxImage $showImageCount to $maxImage <br>");
            $showImageCount = $maxImage;
        }
        
        $wireType = "image";
        $wireOn = $this->wireframe_use($wireType);
        if ($wireOn) {
            $wireData = array();
            $wireData[type] = $wireType;
            $wireData[text] = $this->wireframe_wireText($wireType);
            $wireData[width] = $width;
            $wireData[height] = $height;
            
        }
        // echo ("SET maxImage $showImageCount to $maxImage <br>");
        
        for ($i=0;$i<$showImageCount;$i++) {
            $imageId = $imageList[$i];
            if ($imageId) {
                if ($wireOn) {
                    $wireData[nr] = $imageId;
                    
                    $imgStr = $this->wireframe_image($wireData);
                    $contentList[] = $imgStr;
//                }
//                if ($wireImage) {
//                    $imgStr = "WIRE IMAGE $imageId";
//                    if ($wireImageText) {
//                        $imgStr = cmsWireframe_frameStart_str($width, $height);
//                        $wireData = array("id"=>$data[id],"nr"=>1);
//
//                        $imgStr .= cmsWireframe_text($wireImageText, $wireData);
//                        $imgStr .= cmsWireframe_frameEnd_str();
//                    } else {
//                        $wireImage = cmsWireframe_image($width, $height);
//                        $imgStr = "<image src='$wireImage' class='noBorder' />";
//                    }
//                    $contentList[] = $imgStr;
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
        $showData[width] = $width;
        $showData[height] = $height;
        
        $res = slider::show($name,$contentList,$contentData,$showData);
        
       //  $res = cmsSlider($type,$name,$contentList,$showData,$width,$height,0);
        $out .= $res;

        return $out;
   }

    function dataBox_show_gallery($imageList,$contentData,$boxWidth,$wireImage,$wireImageText) {
        $dataData = $this->contentData[data];
        if (!is_array($dataData)) $dataData = array();
        $maxImage = $dataData["image_maxImage"];       
        $dataData = $contentData[data];
       
        
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
        $wireType = "image";
        $wireOn = $this->wireframe_use($wireType);
        if ($wireOn) {
            $wireData = array();
            $wireData[width] = $imageWidth;
            $wireData[height] = $imageHeight;
            $wireData[id] = $this->dataBox_id;
            $wireData[name] = $this->dataBox_name;
            $wireData[type] = $wireType;
            $wireData[wireText] = $this->wireframeContentData[$wireType."Text"];
            $wireData[wireText] = $this->wireframe_wireText($wireType);
            
            // echo ("WireOn $imageWidth x $imageHeight <br>");
        }
        
        
        $wireframeNr = 0;
        $showImageCount = count($imageList);
        if ($maxImage AND $showImageCount > $maxImage) {
            //echo ("SET maxImage $showImageCount to $maxImage <br>");
            $showImageCount = $maxImage;
        }
        for ($i=0;$i<$showImageCount;$i++) {
            $imageId = $imageList[$i];
            $imgStr = "";
            
            if ($wireOn) {
                $wireData[nr] = $i+1;
                $wireData[zoom] = $zoom;
                $imgStr = $this->wireframe_image($wireData);
                
//                $wireframeNr++;
//                if ($wireImageText) {
//                    $imgStr = cmsWireframe_frameStart_str($imageWidth,$imageHeight);
//                    $imgStr .= cmsWireframe_text($wireImageText,$wireframeNr);
//                    $imgStr .= cmsWireframe_frameEnd_str();
//                } else {
//                    $wireImage = cmsWireframe_image($imageWidth,$imageHeight);
//                    $imgStr = "<image src='$wireImage' class='noBorder' >";
//                }
//
//                if ($zoom) {
//                    $bigWidth = 800;
//                    $bigHeight = floor($bigWidth * $imageHeight / $imageWidth);
//                    $bigImage = cmsWireframe_image($bigWidth,$bigHeight);
//                }

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


    function dataBox_showList_sort($data,$showList) {
        $sortShowList = array();
        $unsort = 100;
        foreach ($showList as $key => $value) {
            $sort = 0;
            if ($data[$key."_sort"]) {
                $sort = $data[$key."_sort"];
            } else {
                $sort = $unsort;
                $unsort++;
            }
            // echo ("Show $key $sort<br>");
            $value["key"] = $key;
            $sortShowList[$sort] = $value;            
        }
        ksort($sortShowList);
        $showList = array();
        foreach ($sortShowList as $sortNr => $value) {
            $key = $value[key];
            unset ($value[key]);
            // echo ("Show $key $sortNr $value <br>");
            $showList[$key] = $value;
        }
        return $showList;
    }
    
    function databox_editDynamic() {
        $add = array();
        if (!$this->pageData[dynamic]) return $add;
            // echo ("DYMNAMIC PAGE ".$this->pageCode." name = $this->pageName id = $this->pageId <br>");
        list($dynPage,$dynStr) = explode("-",$this->pageCode);

        if (is_integer(strpos($dynStr,"0"))) $lastDyn = 0;
        else $lastDyn = 1;

        // echo ("dynPage = $dynPage dynStr = $dynStr lastDyn = $lastDyn <br>");

        if ($lastDyn ) return $add;
        
        
        $dynLink = array();
        $dynLink[name] = "Dynamischer Link";
        $dynLink[position] = 1;
        $dynLink[linebreak] = 1;
        $dynLink[linkStyle] = 1;
        $dynLink[linkSelect] = 0;
        
        $add[dynLink] =  $dynLink;
        return $add;
    }
    
    
    function dataBox_editContent($data,$showList) {
        $res = array();
        
        $add = $this->databox_editDynamic();
        foreach ($add as $key => $value) $showList[$key] = $value;

        $sortList = 1;
        if ($sortList) {
            $showList = $this->dataBox_showList_sort($data,$showList);            
        }
        
       
        
        
        $viewMode = $this->editContent[data][viewMode];
       //  echo ("<h2>VIEWMODE = $viewMode </h2>");
        // foreach ($data as $key => $value ) echo ("DATA[$key] = $value <br>");

        $LR = 0;
        $LRC = 0;
        $SPAN = 0;
        
        $lgaCode = "databox";
        
        $lg_show       = $this->lga($lgaCode,"edit_show",":");
        $lg_position   = $this->lga($lgaCode,"edit_position",":");
        $lg_style      = $this->lga($lgaCode,"edit_style",":");
        $lg_linebreak  = $this->lga($lgaCode,"edit_linebreak",":");
        $lg_linkStyle  = $this->lga($lgaCode,"edit_linkStyle",":");
        $lg_linkSelect = $this->lga($lgaCode,"edit_linkSelect",":");
        
        if ($this->mobileEnabled) {
            $lg_mobile = $this->lga("databox","edit_mobileShow",":");
        }
        
        foreach ($showList as $key => $value) {
            $addData = array();
            $addData[text] = $value[name];

            if ($data[$key."_show"]) $checked = "checked='checked'";
            else $checked = "";
            $input = $lg_show."<input type='checkbox' $checked value='1' name='editContent[data][".$key."_show]' />";

            if ($value[position]) {
                $position = $data[$key."_position"];
                
                $mode = "box";
                $showData[mode] = $mode;
                
                switch ($viewMode) {
                    case "list" :
                        $width = $data[$key."_width"];
                        $input .= " Breite <input type='text' value='$width' name='editContent[data][".$key."_width]' style='width:30px;' />px/%";
                        
                        break;
                    
                    
                    default :
                        $input .= " ".$lg_position;
                        $input .= $this->selectPosition($position,"editContent[data][".$key."_position]", $showData, $showFilter, $showSort);

                        if ($position) {
                            if ($position=="left") $LR = 1;
                            if ($position=="right") $LR = 1;
                            if ($position=="center") $LRC = 1;
                        }
                        
                        if ($value[description]) {
                            if ($data[$key."_description"]) $checked = "checked='checked'";
                            else $checked = "";
                            $input .= " $value[description]: <input type='checkbox' $checked value='1' name='editContent[data][".$key."_description]' />";
                            $SPAN++;
                        }


                        if (is_array($value[view])) {
                            $view = $value[view];
                            $input .= " ".$lg_style;
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
                        
                        $is_text = 0;
                        if ($key == "info") $is_text = 1;
                        if ($key == "longInfo") $is_text = 1;
                        
                        if ($is_text) {
                          
                            $maxChar = $data[$key."_maxChar"];
                            $formName = "editContent[data][".$useType."_maxChar]";
                            $input .= " maxChar: <input type='text' value='$maxChar' name='editContent[data][".$key."_maxChar]' style='width:30px;' />";
                            $readMore = $data[$key."_readMore"];
                            if ($readMore) $checked="checked='checked'"; else $checked = 0;
                            $input .= " readMore: <input type='checkbox' cvalue='1' $checked name='editContent[data][".$key."_readMore]' />";
                        }
                        
                        if ($key == "image") {
                            $maxImage = $data[$key."_maxImage"];
                            $input .= " maxImage: <input type='text' style='width:20px;' value='$maxImage' name='editContent[data][".$key."_maxImage]' />";
                        }
                        
                        $linkSelect = $value[linkSelect];
                        $linkStyle = $value[linkStyle];
                        $lineBreak = $value[linebreak];
                        
                        $addLink = 0;
                        switch ($key) {
                            case "link " : $linkSelect = 1; $linkStyle = 1; $lineBreak = 1; break;
                            case "url"   : $linkSelect = 1; $linkStyle = 1; $lineBreak = 1; break;
                            case "image" : $addLink; break;
                                
                        }
                        
                        if ($lineBreak) {
                            // $input .= " ".$lg_linebreak;
                            $lbData = array();
                            $lbData[viewMode] = "select";
                            $lbData[str] = $lg_linebreak;
                            $input .= $this->editContent_selectSettings("linebreak",$data[$key."_lb"],"editContent[data][".$key."_lb]",$lbData);
//                            if ($data[$key."_lb"]) $checked = "checked='checked'"; else $checked = "";
//                            $input .= "<input type='checkbox' $checked value='1' name='editContent[data][".$key."_lb]' />";
                        }
                        
                        
                        if ($linkSelect OR $linkStyle) {
                            $linkShowData = array();
                            $linkShowData[viewMode] = "linkWindow"; // "linkShow" / "linkWindow"
                            $linkShowData[linkSelect] = $linkSelect;
                            if ($linkSelect) {
                                $linkShowData[linkSelect_code] = $data[$key."_link"];
                                $linkShowData[linkSelect_formName] = "editContent[data][".$key."_link]";
                                $linkShowData[linkSelect_str] = $lg_linkSelect;
                            } else {
                                $linkShowData[linkName] = $value[name];
                               //  $input .= "linkName = $value[name]";
                            }
                            
                            $linkShowData[linkStyle] = $linkStyle;
                            if ($linkStyle) {
                                $input .= "style = ".$data[$key."_linkStyle"];
                                $linkShowData[linkStyle_code] = $data[$key."_linkStyle"];
                                $linkShowData[linkStyle_formName] = "editContent[data][".$key."_linkStyle]";
                                $linkShowData[linkStyle_str] = $lg_linkStyle;
                            }
                            $input .= $this->editContent_selectSettings("link",$data[$key."_link"],"editContent[data][".$key."_link]",$linkShowData);
                        }
                        
                        if ($this->mobileEnabled) {
                            $input .= " $lg_mobile";
                              
                            $mobileData = array();
                            $mobileData[mode] = "simpleShow";
                            $mobileData[viewMode] = "select";
            
                            $selectValue = $data[$key."_mobileShow"];
                            $formName    = "editContent[data][".$key."_mobileShow]";
                            $input .= $this->editContent_selectSettings("mobileShow",$selectValue,$formName,$mobileData);
                        }
//                        if ($this->wireframeEnabled) $input .= "wfE ";
//                        if ($this->wireframeContentEnabled) $input .= "wfCE ";
//                        if ($this->wireframeEnabled AND $this->wireframeContentEnabled) {
//                            
//                            
//                            $input .= "Wire";
//                            $wireOn = $data[$key."_wireframe"];
//                            if ($wireOn) $checked = "checked='checked'"; else $checked = "";
//                            $input .= "<input class='editWireframe' type='checkbox' value='1' name='editContent[data][wireframe]' $checked />";
////                            $add[mode] = "More";
////                            $wireList[] = $add;
//                        }
                        
                }
            }

            $sort = 1;
            if ($sort) {
                $sort = $data[$key."_sort"];
                $input .= "<input class='inputLineSortNr' type='hidden' value='$sort' name='editContent[data][".$key."_sort]' style='width:30px;' />";
                $addData["sort"] = 1;
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
            $addData["sort"] = 0;
            $res[] = $addData;
        }

        if ($SPAN) {
            $addData["text"] = "Bezeichnungsbreite";
            $input = "";
            $input .= "<input type='text' style='width:40px;' value='$data[spanWidth]' name='editContent[data][spanWidth]' />";
            $addData["input"] = $input;
            $addData["mode"] = "More";
            $addData["sortable"] = 0;
            $res[] = $addData;
        }

        
        if ($this->mobileEnabled) {
            $addData = array();
            $addData["text"] = $this->lga($lgaCode,"edit_mobileView"); //  "Mobile Show";
            $selectValue = $data[mobileDataBoxView];
            $formName = "editContent[data][mobileDataBoxView]";
            $showData = array();
            $showData["viewMode"] = "select";
            $showData["mode"] = "showType";
            $showData["type"] = "dataBox";

//            $showData["columnCount_value"] = $data[mobileTableView_count];
//            $showData["columnCount_formName"] = "editContent[data][mobileTableView_count]";
//
//            $showData["columnWidth_value"] = $data[mobileTableView_width];
//            $showData["columnWidth_formName"] = "editContent[data][mobileTableView_width]";

            $input  = $this->editContent_selectSettings("mobileShow", $selectValue, $formName, $showData);
            $addData["input"] = $input;
            $addData[mode] = "More";
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
