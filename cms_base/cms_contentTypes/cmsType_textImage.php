<?php // charset:UTF-8
class cmsType_textImage_base extends cmsType_contentTypes_base {
    
    function getName() {
        return "Text und Bild";
    }
    
    
    function textImage_show($contentData,$frameWidth) {

        $data = $contentData[data];
        if (!is_array($data)) {
            $data = array();
        }

        $id = $contentData[id];
        $pageId = $contentData[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        // show_array($contentData);

        $contentCode = "text_$id";

        $textClass = cmsType_text_Class();
        $imageClass = cmsType_image_class();


        $ownframeWidth = cms_getWidth($data[frameWidth],$frameWidth);
        $ownframeHeight = cms_getWidth($data[frameHeight],$frameWidth);
        // echo ("FrameWidth = $frameWidth frameHeight = $frameHeight <br />");

        $innerWidth = $frameWidth;
        if ($ownframeWidth) $innerWidth = $ownframeWidth;

        $spacing = 10;
        $padding = 0;
        $border =0;
        $innerWidth = $innerWidth - (2*$border) - (2*$padding);


        $divName = "textImageBox textImageboxId_$id textImageBoxPage_".$GLOBALS[pageInfo][pageName];
        $frameStyle = $data[frameStyle];
        if ($frameStyle) $divName .= " $frameStyle";
        $divStyle = "";
        if ($ownframeWidth) $divStyle .= "width:".$ownframeWidth."px;";
        if ($ownframeHeight) $divStyle .= "height:".$ownframeHeight."px;";
        div_start($divName,$divStyle);
        $textList = cms_text_getForContent($contentCode);

        
        $wireFrameOn = $data[wireframe];
        $wireframeState = cmsWireframe_state();
         
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
                
            $wireHeadLine = $wireframeData[headLine];
            if ($wireHeadLine) {
                $wireHeadLine_text= $wireframeData[headLineText];
                // echo ("Wireframe Text = $wireHeadLine_text <br>");
                if ($wireHeadLine_text) $textList[headline][text] = cmsWireframe_text($wireHeadLine_text);
                else $textList[headline][text] = cmsWireframe_text(strlen($textList[headline][text]));
            }
               

            $wireText = $wireframeData[text];
            if ($wireText) {
                $wiretext_text= $wireframeData[textText];
                // echo ("Wireframe Text = $wiretext_text <br>");
                if ($wiretext_text) $textList[text][text] = cmsWireframe_text($wiretext_text);
                else $textList[text][text] = cmsWireframe_text(strlen($textList[text][text]));                              
            }
        }
        // echo ("frame = $frameWidth inner = $innerWidth <br>");
        
        
        $content = $this->getPositionData($contentData,$innerWidth);
        $showContentData = 0;
        if ($showContentData) {
            foreach ($content as $key => $value ) {
                echo ("<b>$key</b><br>");
                foreach ($value as $k2 => $v2) {
                    if ($k2 == "content") {
                        echo (" --> $k2 ");
                        foreach ($v2 as $k3 => $v3) echo (" | $k3=$v2");
                        echo ("<br>");                
                    } else {
                        echo (" --> $k2 = $v2 <br>");                
                    }
                }
            }
        }
        // echo ("IMAGE POSITION = '$imagePos' $frameWidth <br>");
        
        foreach ($content as $target => $contentTarget) {
            switch ($target) {
                case "behind" :
                    if (count($contentTarget)) {
                        div_start("textImageFrame_behind","width:".$innerWidth."px;");
                        div_start("textImageFrame_imageFrameBehind","width:".$innerWidth."px;position:absolute;");
                        
                        $imageClass->image_show($contentData, $innerWidth);
                        $imageShowData = $imageClass->image_getShowData($contentData, $innerWidth);
                        
                        
                        $frontStyle = "width:".$innerWidth."px;position:relative;";
                        if ($imageShowData[height]) {
                            $frontStyle .= "min-height:".$imageShowData[height]."px;";
                        }
                        div_end("textImageFrame_imageFrameBehind");
                
                        div_start("textImageFrame_textFrameFront",$frontStyle);
                        $closeBehindDiv = 1;
                    }
                    break;
               
                case "left" :   
                    if (count($contentTarget[content])) {
                        //foreach($content as $key => $value ) echo ("content $key =")
                        div_start("textImageFrame_LR","width:".$innerWidth."px;");
                        $style = "";
                        foreach ($contentTarget as $key => $value) {
                            switch ($key) {
                                case "width" : 
                                    $width = cms_getWidth($value,$innerWidth);
                                    $style .= "width:".$width."px;";
                                    break;
                                case "marginLeft" : $style .="margin-left:".$value."px;"; break;
                                case "marginRight" : $style .="margin-right:".$value."px;"; break;
                                case "float" : $style .= "float:$value;"; break;     
                                case "display" : $style .= "display:$value;"; break;   
                                case "content" : break;
                                default : 
                                    echo ("Unkown KEY $key in imageFrame_LEFT => $value <br>");
                            }
                        }

                        div_start("textImageFrame_left",$style);
                        foreach ($contentTarget[content] as $contentType => $contentWidth) {
                            switch ($contentType) {
                                case "headline" :    
                                    $textClass->text_showHeadLine($textList[headline],$id);
                                    //if ($width != $innerWidth) div_end("textImageFrame_HeadLine textImageFrame_$target");                        
                                    break;

                                case "text" :
                                    $textClass->text_showText($textList[text],$id);
                                    break;

                                case "image" :
                                    $imageClass->image_show($contentData, $width);
                                    break;
                            }

                        }
                        div_end("textImageFrame_left");
                    }
                    
                    break;
                        
                case "right" :
                    if (count($contentTarget[content])) {
                        $style = "";
                        foreach ($contentTarget as $key => $value) {
                            switch ($key) {
                                case "width" : 
                                    $width = cms_getWidth($value,$innerWidth);
                                    $style .= "width:".$width."px;";
                                    break;
                                case "marginLeft" : $style .="margin-left:".$value."px;"; break;
                                case "marginRight" : $style .="margin-right:".$value."px;"; break;
                                case "float" : $style .= "float:$value;"; break;        
                                case "display" : $style .= "display:$value;"; break;        
                                case "content" : break;
                                default : 
                                    echo ("Unkown KEY $key in imageFrame_RIGHT => $value <br>");
                            }
                        }

                        div_start("textImageFrame_right",$style);
                        foreach ($contentTarget[content] as $contentType => $contentWidth) {
                            switch ($contentType) {
                                case "headline" :    
                                    $textClass->text_showHeadLine($textList[headline],$id);
                                    break;

                                case "text" :
                                    $textClass->text_showText($textList[text],$id);
                                    $textClass->text_showButton($textList, $id);
                                    break;

                                case "image" :
                                    $imageClass->image_show($contentData, $width);
                                    break;
                            }

                        }
                        div_end("textImageFrame_right");



                        div_end("textImageFrame_LR","before");
                    }
                    break;
                
                case "float" :
                    if (count($contentTarget[content])) {
                        $style = "";
                        foreach ($contentTarget as $key => $value) {
                            switch ($key) {
                                case "width" : 
                                    $width = cms_getWidth($value,$innerWidth);
                                    $style .= "width:".$width."px;";
                                    break;
                                case "marginLeft" : $style .="margin-left:".$value."px;"; break;
                                case "marginRight" : $style .="margin-right:".$value."px;"; break;
                                case "marginBottom" : $style .="margin-bottom:".$value."px;"; break;
                                case "floatImage" : $style .= "float:$value;"; break;
                                case "float" : $style .= "float:$value;"; $imageFloat= $value; break;        
                                case "display" : $style .= "display:$value;"; break;        
                                case "content" : break;
                                default : 
                                    echo ("Unkown KEY $key in imageFrame_FLOAT => $value <br>");
                            }
                        }

                        div_start("textImageFrame_float");
                        foreach ($contentTarget[content] as $contentType => $contentWidth) {
                            switch ($contentType) {
                                case "headline" :    
                                    if ($contentWidth != $innerWidth) {
                                        $headStyle = "width:".$contentWidth."px;";
                                        if ($imageFloat == "left") $headStyle.="float:right;";
                                        div_start("textImageFrame_HeadLine",$headStyle);
                                    }
                                    $textClass->text_showHeadLine($textList[headline],$id);
                                    if ($contentWidth != $innerWidth) div_end("textImageFrame_HeadLine");                        
                                    break;

                                case "text" :
                                    $textClass->text_showText($textList[text],$id);
                                    break;

                                case "image" :                                    
                                    div_start("textImageFrame_imageFloat",$style); //"width:".$contentWidth."px;float:$floatImage;margin:$margin;");
                                    $imageClass->image_show($contentData, $contentWidth);
                                    div_end("textImageFrame_imageFloat");
                    
                                    break;
                            }

                        }
                        div_end("textImageFrame_float","before");
                    }
                    break;      
              
                    
            }
            
            if ($target == "behind") continue;
            if ($target == "left") continue;
            if ($target == "right") continue;
            if ($target == "float") continue;
            
            foreach ($contentTarget as $contentType => $width) {
            
                switch ($contentType) {
                    case "headline" :
                        if ($width != $innerWidth) div_start("textImageFrame_HeadLine textImageFrame_$target","width:".$width."px;");                            
                        $textClass->text_showHeadLine($textList[headline],$id);
                        if ($width != $innerWidth) div_end("textImageFrame_HeadLine textImageFrame_$target");                        
                        break;
                        
                   case "image" :
                        $imageClass->image_show($contentData, $width);                     
                        break;   
                        
                   case "text" :
                        if ($width != $innerWidth) div_start("textImageFrame_text textImageFrame_$target","width:".$width."px;");                                                   
                        $textClass->text_showText($textList[text],$id);
                        if ($width != $innerWidth) div_end("textImageFrame_text textImageFrame_$target");                        
                        break;     

                   case "left" :
                        break;
                        
                   case "right" :
                        break;
                        
                   default :
//                       switch ($target) {
//                            case "left" : break;
//                            case "right" : break;                                
//                            case "float" : break;
//                            default :
                                echo ("Inhalt Target = $target - Type = $contentType  width = $width <br>"); 
                       // }
                }
            }
        }
        
        
        
        if ($closeBehindDiv) {
            div_end("textImageFrame_textFrameFront");
            div_end("textImageFrame_behind");            
        }
        
        
         
        div_end($divName);
        
        return 1;      

    }
    
    function getPositionData($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $imagePos = $data[imagePos];
        if (!$imagePos) $imagePos = "between";
        $content = array();
        $content[behind] = array();
        $content[top] = array();
        $content[middle] = array();
        $content[bottom] = array();
        
        $content[left] = array("content"=>array());
        $content[right] = array("content"=>array());
        $content[float] = array("content"=>array());
        
        $spacing = 10;
        if ($data[spacing]) $spacing= cms_getWidth($data[spacing],$frameWidth);
    
        $imageWidth = "30%";
        if ($data[imageWidth]) $imageWidth = $data[imageWidth];
        $imageWidth = cms_getWidth($imageWidth,$frameWidth);
        
        $textWidth = $frameWidth;
        if ($data[textWidth]) $textWidth = $data[textWidth];
        $textWidth = cms_getWidth($textWidth,$frameWidth);
        
        switch ($imagePos) {
            case "top" :
                $imageWidth = $frameWidth;
                if ($data[imageWidth]) $imageWidth = $data[imageWidth];
                $content[top][image] = $imageWidth;
                $content[middle][headline] = $textWidth;
                $content[bottom][text] = $textWidth;           
                break;

            case "between" :
                $content[top][headline] = $textWidth;
                $imageWidth = $frameWidth;
                if ($data[imageWidth]) $imageWidth = $data[imageWidth];
                $content[middle][image] = $imageWidth;
                $content[bottom][text] = $textWidth;
                break;

            case "bottom" :
                $content[top][headline] = $textWidth;
                $content[middle][text] = $textWidth;
                $imageWidth = $frameWidth;
                if ($data[imageWidth]) $imageWidth = $data[imageWidth];
                $content[bottom][image] = $imageWidth;
                break;
                
            case "behind" :
                $imageWidth = $frameWidth;
                if ($data[imageWidth]) $imageWidth = $data[imageWidth];
                $content[behind][image] = $imageWidth;          
                
                $content[top][headline] = $textWidth;
                $content[middle][text] = $textWidth;                
                break;

            case "left" :
                $content[left][width] = $imageWidth;
                $content[left][marginRight] = $spacing;
                // $content[left][display] = "inline-block";
                $content[left][float] = "left";
                $content[left][content] = array();
                $content[left][content][image] = $imageWidth;
                
                $rightWidth = $frameWidth - $imageWidth - $spacing;
                $content[right][width] = $rightWidth;
                $content[right][float] = "left";
                $content[right][content] = array();
                $content[right][content][headline] = $rightWidth;
                $content[right][content][text] = $rightWidth;
                break;
                
            case "leftUnder" :
                $content[top][headline] = $textWidth;
                
                $content[left][width] = $imageWidth;
                $content[left][marginRight] = $spacing;
               
                $content[left][float] = "left";
                $content[left][content] = array();
                $content[left][content][image] = $imageWidth;
                
                $rightWidth = $frameWidth - $imageWidth - $spacing;
                $content[right][width] = $rightWidth;
                $content[right][float] = "left";
                $content[right][content] = array();
                $content[right][content][text] = $rightWidth;
                break;
            
            case "floatLeft" :
                $rightWidth = $frameWidth - $imageWidth - $spacing;
                
                $content[float][width] = $imageWidth;
                $content[float][marginRight] = $spacing;
                $content[float][marginBottom] = $spacing;
                $content[float][float] = "left";
                
                $content[float][content] = array();
                $content[float][content][image] = $imageWidth;
                $content[float][content][headline] = $rightWidth;
                $content[float][content][text] = $frameWidth;
                break;
            
            
             case "floatLeftUnder" :
                $content[top][headline] = $textWidth;
                 
                $rightWidth = $frameWidth - $imageWidth - $spacing;
                
                $content[float][width] = $imageWidth;
                $content[float][marginRight] = $spacing;
                $content[float][marginBottom] = $spacing;
                $content[float][float] = "left";
                
                $content[float][content] = array();
                $content[float][content][image] = $imageWidth;
                $content[float][content][text] = $frameWidth;
                
                break;
            
             
            case "right" :
                $content[right][width] = $imageWidth;
                $content[right][float] = "left";
                $content[right][content] = array();
                $content[right][content][image] = $imageWidth;
                
                $leftWidth = $frameWidth - $imageWidth - $spacing;
                $content[left][width] = $leftWidth;
                $content[left][marginRight] = $spacing;
                $content[left][float] = "left";
                $content[left][content] = array();
                $content[left][content][headline] = $leftWidth;
                $content[left][content][text] = $leftWidth;
                break;
                
            case "rightUnder" :
                $content[top][headline] = $textWidth;
                
                $leftWidth = $frameWidth - $imageWidth - $spacing;
                $content[left][width] = $leftWidth;
                $content[left][marginRight] = $spacing;
                $content[left][float] = "left";
                $content[left][content] = array();
                // $content[left][content][headline] = $leftWidth;
                $content[left][content][text] = $leftWidth;
                
                $content[right][width] = $imageWidth;
                $content[right][float] = "left";
                $content[right][content] = array();
                $content[right][content][image] = $imageWidth;
                
               
                break;
            
            case "floatRight" :
                $leftWidth = $frameWidth - $imageWidth - $spacing;
                
                $content[float][width] = $imageWidth;
                $content[float][marginLeft] = $spacing;
                $content[float][marginBottom] = $spacing;
                $content[float][float] = "right";
                
                $content[float][content] = array();
                $content[float][content][image] = $imageWidth;
                $content[float][content][headline] = $leftWidth;
                $content[float][content][text] = $frameWidth;
                break;
            
            case "floatRightUnder" :
                $content[top][headline] = $textWidth;
                 
                $rightWidth = $frameWidth - $imageWidth - $spacing;
                $content[float][width] = $imageWidth;
                $content[float][marginLeft] = $spacing;
                $content[float][marginBottom] = $spacing;
                $content[float][float] = "right";
                
                $content[float][content] = array();
                $content[float][content][image] = $imageWidth;
                $content[float][content][text] = $frameWidth;
                break;
            
            default :
                echo ("unkown Image Position in textImage_show $imagePos<br />");

        }
        return $content;
    }

    function textImage_show_old($contentData,$frameWidth) {

        $data = $contentData[data];
        if (!is_array($data)) {
            $data = array();
        }

        $id = $contentData[id];
        $pageId = $contentData[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        // show_array($contentData);

        $contentCode = "text_$id";

        $textClass = cmsType_text_Class();
        $imageClass = cmsType_image_class();


        $ownframeWidth = cms_getWidth($data[frameWidth],$frameWidth);
        $ownframeHeight = cms_getWidth($data[frameHeight],$frameWidth);
        // echo ("FrameWidth = $frameWidth frameHeight = $frameHeight <br />");

        $innerWidth = $frameWidth;
        if ($ownframeWidth) $innerWidth = $ownframeWidth;

        $spacing = 10;
        $padding = 0;
        $border =0;
        $innerWidth = $innerWidth - (2*$border) - (2*$padding);


        



        $divName = "textImageBox textImageboxId_$id textImageBoxPage_".$GLOBALS[pageInfo][pageName];
        $frameStyle = $data[frameStyle];
        if ($frameStyle) $divName .= " $frameStyle";
        $divStyle = "";
        if ($ownframeWidth) $divStyle .= "width:".$ownframeWidth."px;";
        if ($ownframeHeight) $divStyle .= "height:".$ownframeHeight."px;";
        div_start($divName,$divStyle);
        $textList = cms_text_getForContent($contentCode);

        
        $wireFrameOn = $data[wireframe];
        $wireframeState = cmsWireframe_state();
         
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
                
            $wireHeadLine = $wireframeData[headLine];
            if ($wireHeadLine) {
                $wireHeadLine_text= $wireframeData[headLineText];
                // echo ("Wireframe Text = $wireHeadLine_text <br>");
                if ($wireHeadLine_text) $textList[headline][text] = cmsWireframe_text($wireHeadLine_text);
                else $textList[headline][text] = cmsWireframe_text(strlen($textList[headline][text]));
            }
               

            $wireText = $wireframeData[text];
            if ($wireText) {
                $wiretext_text= $wireframeData[textText];
                // echo ("Wireframe Text = $wiretext_text <br>");
                if ($wiretext_text) $textList[text][text] = cmsWireframe_text($wiretext_text);
                else $textList[text][text] = cmsWireframe_text(strlen($textList[text][text]));                              
            }
        }



        $imagePos = $data[imagePos];
        if (!$imagePos) $imagePos = "between";
        $headLineShow = 0;
        $imageFloat = 0;
        
        switch ($imagePos) {
            case "leftUnder" : 
                $textClass->text_showHeadLine($textList[headline],$id);
                $imagePosition = "left";
                $textPosition = "right";
                $headLineShow = 1;
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
            
            case "floatLeftUnder" :
                $textClass->text_showHeadLine($textList[headline],$id);
                $imagePosition = "left";
                $imageFloat = "left";
                $textPosition = "right";
                $headLineShow = 1;
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
                
            case "rightUnder" : 
                $textClass->text_showHeadLine($textList[headline],$id);
                $imagePosition = "right";
                $textPosition = "left";
                $headLineShow = 1;
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
            
            case "floatRightUnder" : 
                $textClass->text_showHeadLine($textList[headline],$id);
                $imagePosition = "right";
                $textPosition = "left";
                $headLineShow = 1;
                $imageFloat = "right";
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
                
            case "left" :
                $imagePosition = "left";
                $textPosition = "right";
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
                
            case "floatLeft" :
                $imagePosition = "left";
                $textPosition = "right";
                $imageFloat = "left";
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
                
            case "right" :
                $imagePosition = "right";
                $textPosition = "left";
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
               
                break;
                
            case "floatRight" :
                $imagePosition = "right";
                $textPosition = "left";
                $imageFloat = "right";
                if (!$data[imageWidth]) $data[imageWidth] = "30%";
                break;
                
            case "top" :
                $imagePosition = "top";
                $textPosition = "bottom";
                if (!$data[imageWidth]) $data[imageWidth] = "100%";
                break;

            case "between" :
                $imagePosition = "top";
                $textPosition = "bottom";
                if (!$data[imageWidth]) $data[imageWidth] = "100%";
                break;

            case "bottom" :
                $imagePosition = "bottom";
                $textPosition = "top";
                if (!$data[imageWidth]) $data[imageWidth] = "100%";
                break;
                
            case "behind" :
                $imagePosition = "behind";
                $textPosition = "front";
                if (!$data[imageWidth]) $data[imageWidth] = "100%";
                break;

            default :
                echo ("unkown Image Position in textImage_show $imagePos<br />");

        }
        
        
       
        
        $imageWidth = cms_getWidth($data[textImageWidth],$innerWidth);
        // echo ("IMAGE BREITE $data[imageWidth] $imageWidth $innerWidth $imagePosition <br>");

        switch ($imagePosition) {
            case "left" :
                $frameLeftWidth = $imageWidth;
                $frameRightWidth = $innerWidth - $imageWidth - $spacing;
                $frameDirection = "horizontal";
                break;

            case "right" :
                $frameRightWidth = $imageWidth;
                $frameLeftWidth = $innerWidth - $imageWidth - $spacing;
                // echo ("Right $frameWidth - $spacing - $imageWidth - (2*$padding) - (2*$border) -$frameLeftWidth <br />");
                $frameDirection = "horizontal";
                break;

            case "top" :
                $frameTopWidth = $innerWidth;;
                $frameBottomWidth = $innerWidth;
                $frameDirection = "vertical";
                break;



            case "bottom" :
                $frameTopWidth = $innerWidth;
                $frameBottomWidth = $innerWidth;
                $frameDirection = "vertical";
                // echo ("<h1> $frameTopWidth $frameBottomWidth // $frameWidth $innerWidth </h1>");
                break;
            
            case "behind" :
                $frameTopWidth = $frameWidth;
                $frameBottomWidth = $frameWidth;
                $frameDirection = "behind";
                break;

            default :
                // echo ("unkowb $imagePosition <br>");
                $frameLeftWidth = ($frameWidth - $spacing) / 2;
                $frameRightWidth = ($frameWidth - $spacing) / 2;
        }
       
        echo ("<h1> Direction $frameDirection imgPos = $imagePos</h1>");
        
        switch ($frameDirection) {
            case "horizontal" :

                // LEFT FRAME
               
                if ($imageFloat=="right") {
                    div_start("textImageFrame_Left","width:".$frameWidth."px;margin-right:".$spacing."px;");
                } else {
                    div_start("textImageFrame_Left","float:left;width:".$frameLeftWidth."px;margin-right:".$spacing."px;");
                }
                
                // div_start("textImageFrame_Left","float:left;width:".$frameLeftWidth."px;margin-right:".$spacing."px;");
                if ($imagePosition == "left") {
                    $imageClass->image_show($contentData, $frameLeftWidth);
                }
                
                if ($textPosition == "left") {
                    // show HeadLine
                    if (!$headLineShow)  {
                        if ($imageFloat=="right") div_start("textImageFrame_LeftHeadLine","float:left;width:".$frameLeftWidth."px;");                            
                        
                        $textClass->text_showHeadLine($textList[headline],$id);
                        
                        if ($imageFloat=="right") div_end("textImageFrame_LeftHeadLine");
                    }
                    
                    if ($imagePosition == "right" AND $imageFloat=="right") {
                        div_start("textImageFrame_Right","width:".$frameRightWidth."px;float:right;");
                        $imageClass->image_show($contentData, $frameRightWidth);
                        div_end("textImageFrame_Right");
                    }
                     
                        

                    // show Text
                    $textClass->text_showText($textList[text],$id);

                    // show Buttons
                    $textClass->text_showButton($textList,$id);
                }
                div_end("textImageFrame_Left");

                // RIGHT FRAME
                if ($imageFloat) {
                    div_start("textImageFrame_Float","width:".$frameWidth."px;");
                } else {
                    if ($imagePosition == "right") div_start("textImageFrame_Right","float:left;width:".$frameRightWidth."px;");
                    else div_start("textImageFrame_Left","float:left;width:".$frameLeftWidth."px;");
                    
                }
                
                
                if ($imagePosition == "right" AND $imageFloat != "right") {
                    $imageClass->image_show($contentData, $frameRightWidth);
                }
                if ($textPosition == "right") {
                    // show HeadLine
                    if (!$headLineShow) {
                        
                        if ($imageFloat) div_start("textImageFrame_RightHeadLine","float:left;width:".$frameRightWidth."px;");                            
                        
                        $textClass->text_showHeadLine($textList[headline],$id);
                        
                        if ($imageFloat) div_end("textImageFrame_RightHeadLine");
                        
                    }
                    

                    // show Text
                    $textClass->text_showText($textList[text],$id);

                    // show Buttons
                    $textClass->text_showButton($textList,$id);
                }
                if ($imageFloat) {
                    div_end("textImageFrame_Float");
                } else {
                    if ($imagePosition == "right") div_end("textImageFrame_Right");
                    else div_end("textImageFrame_Left");
                }
                break;
                
            case "vertical" :
            // TOP FRAME
                div_start("textImageFrame_Top","width:".$frameTopWidth."px;");
                if ($imagePosition == "top") {
                    if ($imagePos == "between") {
                        $textClass->text_showHeadLine($textList[headline],$id);
                        $headLineShow = 1;
                    }
                    $imageClass->image_show($contentData, $frameWidth);
                }
                if ($textPosition == "top") {
                    // show HeadLine
                    if (!$headLineShow) $textClass->text_showHeadLine($textList[headline],$id);

                    // show Text
                    $textClass->text_showText($textList[text],$id);

                    // show Buttons
                    $textClass->text_showButton($textList,$id);
                }
                div_end("textImageFrame_Top");

                // Bottom FRAME
                div_start("textImageFrame_Bottom","width:".$frameBottomWidth."px;");
                if ($imagePosition == "bottom") {
                    $imageClass->image_show($contentData, $frameWidth);
                }
                if ($textPosition == "bottom") {
                    // show HeadLine
                    if (!$headLineShow) $textClass->text_showHeadLine($textList[headline],$id);

                    // show Text
                    $textClass->text_showText($textList[text],$id);

                    // show Buttons
                    $textClass->text_showButton($textList,$id);
                }

                div_end("textImageFrame_Bottom");
                break;
                
                
            case "behind" :
                div_start("textImageFrame_behind","width:".$frameWidth."px;");
                
                div_start("textImageFrame_imageFrame","width:".$frameWidth."px;position:absolute;");
                // echo ("<h1> HIER BILD </h1>");
                $imageClass->image_show($contentData, $frameWidth);
                div_end("textImageFrame_imageFrame");
                
                div_start("textImageFrame_textFrame","width:".$frameWidth."px;position:relative");
                
            
                $textClass->text_showHeadLine($textList[headline],$id);
                $headLineShow = 1;
                 

                // show Text
                $textClass->text_showText($textList[text],$id);

                // show Buttons
                $textClass->text_showButton($textList,$id);
                div_end("textImageFrame_textFrame");
                div_end("textImageFrame_behind");
                break;
                
                
        }
       

        div_end($divName,"before");

    }


    function textImage_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $id = $editContent[id];
        $pageId = $editContent[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $contentCode = "text_$id";

        $editText = $_POST[editText];

        $res = array();

        if (!$data[imagePos]) $data[imagePos] = "leftUnder";
        $addData = array();
        $addData["text"] = "Bildposition";
        $input  = "<input type='text' style='width:100px;' name='editContent[data][position]' value='".$data[position]."' >";
        $addData["input"] = cms_content_selectStyle("imagePosition",$data[imagePos],"editContent[data][imagePos]");;
        $addData["mode"] = "Simple";
        $res[] = $addData;


        if (!$data[imageWidth]) {
            $data[imageWidth] = "50%";
        }
        $addData = array();
        
        $addData["text"] = "Bild Breite";
        $input  = "<input type='text' style='width:50px;' name='editContent[data][textImageWidth]' value='".$data[textImageWidth]."' > (px,%,auto)";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;

        
        $addData["text"] = "Abstand";
        $input  = "<input type='text' style='width:50px;' name='editContent[data][spacing]' value='".$data[spacing]."' > (px,%,auto)";
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[] = $addData;
        
        // get Input List from textClass
        $res["text"] = cms_contentType_text_editContent($editContent,$frameWidth);

        // get Input List from imageClass
        $res["image"] = cmsType_image_editContent($editContent,$frameWidth);

        return $res;
    }






}

function cmsType_textImage_Class() {
    if ($GLOBALS[cmsTypes]["cmsType_textImage.php"] == "own") $textImageClass = new cmsType_textImage();
    else $textImageClass = new cmsType_textImage_base();
    return $textImageClass;
}


function cmsType_textImage($contentData,$frameWidth) {
    $textImageClass = cmsType_textImage_Class($contentData,$frameWidth);
    $textImageClass->textImage_show($contentData,$frameWidth);
}



function cmsType_textImage_editContent($editContent,$frameWidth) {    
    $textImageClass = cmsType_textImage_Class();
    return $textImageClass->textImage_editContent($editContent,$frameWidth);
}



?>
