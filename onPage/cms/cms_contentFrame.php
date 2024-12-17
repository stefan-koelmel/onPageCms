<?php // charset:UTF-8

class cmsFrame extends cmsFrame_base {

    function addStyles () {
        $styleList = array();
        $styleList[frame1] = "Grauer Rahmen";
        $styleList[frame2] = "Orangener Rahmen";
        $styleList[frame3] = "Weißer Rahmen";
        $styleList[starFrame] = "Stern Rahmen";
        return $styleList;
    }


    function getFrameData_rollFrame() {
        $border = 0;
        $padding = 5;
        $spacing = 0;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_frame1() {
        $border = 0;
        $padding = 5;
        $spacing = 0;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_frame2() {
        $border = 0;
        $padding = 10;
        $spacing = 0;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_own($frameStyle) {
        switch ($frameStyle) {
            case "starFrame" :
                $border = 0;
                $padding = 32;
                $spacing = 36;
                return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
                break;

            case "frame3" :
                $border = 0;
                $padding = 32;
                $spacing = 36;
                return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
                break;

            default :
                $border = 0;
                $padding = 0;
                $spacing = 0;
                return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
        }

    }


    function getSpecial_own_before($frameStyle,$contentData) {
        switch ($frameStyle) {
            case "starFrame" :
                $id = $contentData[id];
                $textId = "text_".$id;

                $editText = cms_text_getForContent($textId);
               
                $className = "starBox";
                //$starText = $contentData[data][starText];
                if (!$starText) $starText = "unkown $textId";

                if (is_array($editText)) {
                    if (is_array($editText[starHeadline])) {
                        $starText = $editText[starHeadline][text];
                        $className .= " starBox_".$editText[starHeadline][css];
                       
                    }
                   //  show_array($editText);
                }


                $res = array();
                $res[output] = "<div class='$className' >$starText</div>";

                $border = 0;
                $padding = 32;
                $spacing = 36;
                return $res; //array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
                break;
        }
        return $res;
    }

     function getSpecial_own_after($frameStyle,$contentData) {
        switch ($frameStyle) {
            case "starFrame" :
                
                break;
        }
        return $res;
    }


    function getSpecial_own_edit($frameStyle,$editContent,$frameWidth){
        switch ($frameStyle) {
            case "starFrame" :
               
                $res = array();
                $res[frame] = array();

                $textId = "text_".$editContent[id];
                //show_array($editContent);
                $editText = cms_text_getForContent($textId);

                if (is_array($editText)) {
                    if (is_array($editText[starHeadline])) {
                        $starText  = $editText[starHeadline][text];
                        $starClass = $editText[starHeadline][css];
                        $starId    = $editText[starHeadline][id];
                    }
                }

                $addData = array();
                $addData["text"] = "hidden-Text Id";
                $addData["input"] = "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
                $res[frame][] = $addData;

                $addData = array();
                $addData["text"] = "Stern Text";
                $input  = "<input type='text' style='width:".$frameWidth."px;' name='editText[starHeadline][text]' value='".$starText."' >";
                $input .= "<input type='hidden' value='".$editText[starHeadline][id]."' name='editText[starHeadline][id]'>";
                $addData["input"] = cms_content_selectStyle("headline",$starClass,"editText[starHeadline][css]");
                $addData["secondLine"] = $input;
                $res[frame][] = $addData;

                $addData = array();
                $addData["text"] = "Abstand oben";
                $addData["input"] = "<input type='text'  name='editContent[data][paddingTop]' value='".$editContent[data][paddingTop]."' >";
                $res[frame][] = $addData;


        }
        return $res;
    }
    
}


?>
