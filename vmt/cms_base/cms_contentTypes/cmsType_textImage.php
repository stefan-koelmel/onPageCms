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




        $imagePos = $data[imagePos];
        if (!$imagePos) $imagePos = "between";
        $headLineShow = 0;
        switch ($imagePos) {
            case "leftUnder" : 
                $textClass->text_showHeadLine($textList[headline],$id);
                $imagePosition = "left";
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
            case "left" :
                $imagePosition = "left";
                $textPosition = "right";
                if (!$data[imageWidth]) $data[imageWidth] = "50%";
                break;
            case "right" :
                $imagePosition = "right";
                $textPosition = "left";
                if (!$data[imageWidth]) $data[imageWidth] = "50%";
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

            default :
                echo ("unkown Image Position in textImage_show $imagePos<br />");

        }
        
        $imageWidth = cms_getWidth($data[imageWidth],$innerWidth);
        echo ("IMAGE BREITE $data[imageWidth] $imageWidth $innerWidth $imagePosition <br>");

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
                $frameTopWidth = $imageWidth;
                $frameBottomWidth = $imageWidth;
                $frameDirection = "vertical";
                break;



            case "bottom" :
                $frameTopWidth = $imageWidth;
                $frameBottomWidth = $imageWidth;
                $frameDirection = "vertical";
                break;

            default :
                $frameLeftWidth = ($frameWidth - $spacing) / 2;
                $frameRightWidth = ($frameWidth - $spacing) / 2;
        }


        if ($frameDirection == "horizontal") {
            // LEFT FRAME
            div_start("textImageFrame_Left","float:left;width:".$frameLeftWidth."px;margin-right:".$spacing."px;");
            if ($imagePosition == "left") {

                $imageClass->image_show($contentData, $frameLeftWidth);
            }
            if ($textPosition == "left") {
                // show HeadLine
                if (!$headLineShow) $textClass->text_showHeadLine($textList[headline],$id);

                // show Text
                $textClass->text_showText($textList[text],$id);

                // show Buttons
                $textClass->text_showButton($textList,$id);
            }
            div_end("textImageFrame_Left");

            // RIGHT FRAME
            div_start("textImageFrame_Right","float:left;width:".$frameRightWidth."px;");
            if ($imagePosition == "right") {
                $imageClass->image_show($contentData, $frameRightWidth);
            }
            if ($textPosition == "right") {
                // show HeadLine
                if (!$headLineShow) $textClass->text_showHeadLine($textList[headline],$id);

                // show Text
                $textClass->text_showText($textList[text],$id);

                // show Buttons
                $textClass->text_showButton($textList,$id);
            }

            div_end("textImageFrame_Right");
         }

         if ($frameDirection == "vertical") {
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
        $res[] = $addData;


        if (!$data[imageWidth]) {
            $data[imageWidth] = "50%";
        }
        $addData = array();
        
        $addData["text"] = "Bild Breite";
        $input  = "<input type='text' style='width:50px;' name='editContent[data][imageWidth]' value='".$data[imageWidth]."' > (px,%,auto)";
        $addData["input"] = $input;
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
