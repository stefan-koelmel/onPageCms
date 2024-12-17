<?php
class cmsSystemFrame_base {
    
    
    function frameStart($contentData,$frameWidth,$textData=array()) {
        $out = "";
        $out .= div_start_str("systemFrame_title");
      
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        if ($data[winRestore]) $showList["winRestore"] = 1;
        
        
        if ($data[winMin]) $showList["winMin"] = "PDF";        
        if ($data[winMax]) $showList["winMax"] = "Exel";
        if ($data[reload]) $showList["reload"] = "PDF";
        if ($data[reloadPage]) $showList["reloadPage"] = "Exel";
        
        if ($data[lock]) $showList["lock"] = "Word";
        if ($data[unLock]) $showList["unLock"] = "PDF";
        if ($data[bookmarkActive]) $showList["bookmarkActive"] = "Exel";
        if ($data[bookmark]) $showList["bookmark"] = "Exel";
        if ($data[history]) $showList["history"] = "Exel";
        if ($data[pin]) $showList["pin"] = "Exel";
        
        if ($data[exportWord]) $showList["exportWord"] = "Word";
        if ($data[exportPdf]) $showList["exportPdf"] = "PDF";
        if ($data[exportExel]) $showList["exportExel"] = "Exel";
        
        if ($data[zoom]) $showList["zoom"] = "Word";
        if ($data[zoomIn]) $showList["zoomIn"] = "PDF";
        if ($data[zoomOut]) $showList["zoomOut"] = "Exel";
        
        if ($data[note]) $showList["note"] = "Exel";
        if ($data[noteEdit]) $showList["noteEdit"] = "Word";
        if ($data[noteAdd]) $showList["noteAdd"] = "PDF";
        if ($data[noteDelete]) $showList["noteDelete"] = "Exel";
        if ($data[noteStar]) $showList["noteStar"] = "PDF";
        if ($data[noteBox]) $showList["noteBox"] = "Exel";
        
        
        if ($data[viewTable]) $showList["viewTable"] = "Exel";
        if ($data[viewChart]) $showList["viewChart"] = "Word";
        if ($data[viewLine]) $showList["viewLine"] = "PDF";
        if ($data[viewBar]) $showList["viewBar"] = "Exel";
        
        $anz = count($showList);
        // $out .="Anz $anz";
       
        
        if (!is_array($textData)) {
            $textId = "text_".$contentData[id];
            $textData = cms_text_getForContent($textId);            
        }
        
        if (is_array($textData)) {
            // show_Array($textData);
            if (is_array($textData[systemFrameTitle])) {
                $title = $textData[systemFrameTitle][text];
            }
        }
        
        if (!$title) $title = "Fenster Name";
        
        $abs = 3;
        $padding = -3;
        
        $buttonWidth = $anz * (16+(2*$abs));
        $titleWidth = $frameWidth - $buttonWidth - (2*$padding);
        $style = "width:".$titleWidth."px";
        $out.= "<div class='systemFrame_titleTitle' style='' >";
        $out .= $title;
        $out .= "</div>";
                
        
        
        $out.= "<div class='systemFrame_titleButton' style='width:".$buttonWidth."px;' >";
        if (is_array($showList) AND $anz) {
            foreach ($showList as $key => $name) {
                $out .= $this->button("div", $key,$abs);
               //  echo ("$key / $name <br>");
            }
        }
        $out .= "</div>";
        
        $out .= div_end_str("systemFrame_title");
        $out .= div_start_str("systemFrame_content");
        return array("output"=>$out);
    }
    
    function frameEnd($contentData,$frameWidth) {
        $out = "";
        $out .= div_end_str("systemFrame_content");
        $out .= div_start_str("systemFrame_footer");
        $out .= "Footer";
        $out .= div_end_str("systemFrame_footer");
        return array("output"=>$out);
    }
    
    function editList($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
            
        $zoom = $data[zoom];
        
        $systemFrame = array();
        
        $textId = "text_".$editContent[id];
        
        
        $editText = $_POST[editText];
        if (!is_array($editText)) {
            $editText = cms_text_getForContent($textId);
        }
    
        $systemFrame[showTab] = "More";
        $systemFrame[showName] = "Aktiver Rahmen";
        
    
        $addData = array();
        $addData["text"] = "hidden-Text Id";
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
        $addData["mode"] = "More";
        $systemFrame[] = $addData;

        $addData = array();
        $addData["text"] = "Fenster Name";
        $input  = "<input type='text' style='width:".($frameWidth-10)."px;' name='editText[systemFrameTitle][text]' value='".$editText[systemFrameTitle][text]."' >";
        $input .= "<input type='hidden' value='".$editText[systemFrameTitle][id]."' name='editText[systemFrameTitle][id]'>";  
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        // Window
        $showList = array();
        $showList["winRestore"] = "Word";
        $showList["winMin"] = "PDF";        
        $showList["winMax"] = "Exel";
        $showList["reload"] = "PDF";
        $showList["reloadPage"] = "Exel";
        $addData = array();
        $addData["text"] = "Fenster";
        $input = "";
        foreach ($showList as $key => $name) {
            if ($data[$key])$checked="checked='checked'"; 
            else $checked ="";
            $input .= "<input type='checkbox' name='editContent[data][$key]' $checked value='1' > ";
            $input .= $this->button("show",$key)." ";
        }
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        
         // Special
        $showList = array();
        $showList["lock"] = "Word";
        $showList["unLock"] = "PDF";
        $showList["bookmarkActive"] = "Exel";
        $showList["bookmark"] = "Exel";
        $showList["history"] = "Exel";
        $showList["pin"] = "Exel";
        $addData = array();
        $addData["text"] = "Spezial";
        $input = "";
        foreach ($showList as $key => $name) {
            if ($data[$key])$checked="checked='checked'"; 
            else $checked ="";
            $input .= "<input type='checkbox' name='editContent[data][$key]' $checked value='1' > ";
            $input .= $this->button("show",$key)." ";
        }
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        
        // Export
        $showList = array();
        $showList["exportWord"] = "Word";
        $showList["exportPdf"] = "PDF";
        $showList["exportExel"] = "Exel";
        $addData = array();
        $addData["text"] = "Export";
        $input = "";
        foreach ($showList as $key => $name) {
            if ($data[$key])$checked="checked='checked'"; 
            else $checked ="";
            $input .= "<input type='checkbox' name='editContent[data][$key]' $checked value='1' > ";
            $input .= $this->button("show",$key)." ";
        }
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        
        // Zoom
        $showList = array();
        $showList["zoom"] = "Word";
        $showList["zoomIn"] = "PDF";
        $showList["zoomOut"] = "Exel";
        $addData = array();
        $addData["text"] = "Zoom";
        $input = "";
        foreach ($showList as $key => $name) {
            if ($data[$key])$checked="checked='checked'"; 
            else $checked ="";
            $input .= "<input type='checkbox' name='editContent[data][$key]' $checked value='1' > ";
            $input .= $this->button("show",$key)." ";
        }
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        // Dashboard
        $showList = array();
        $showList["note"] = "Exel";
        $showList["noteEdit"] = "Word";
        $showList["noteAdd"] = "PDF";
        $showList["noteDelete"] = "Exel";
        $showList["noteStar"] = "PDF";
        $showList["noteBox"] = "Exel";
        
        $addData = array();
        $addData["text"] = "Dashboard";        
        $input = "";
        foreach ($showList as $key => $name) {
            if ($data[$key])$checked="checked='checked'"; 
            else $checked ="";
            $input .= "<input type='checkbox' name='editContent[data][$key]' $checked value='1' > ";
            $input .= $this->button("show",$key)." ";
        }
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        
        // View
        $showList = array();
        $showList["viewTable"] = "Exel";
        $showList["viewChart"] = "Word";
        $showList["viewLine"] = "PDF";
        $showList["viewBar"] = "Exel";
        $addData = array();
        $addData["text"] = "Ansichten";
        $input = "";
        foreach ($showList as $key => $name) {
            if ($data[$key])$checked="checked='checked'"; 
            else $checked ="";
            $input .= "<input type='checkbox' name='editContent[data][$key]' $checked value='1' > ";
            $input .= $this->button("show",$key)." ";
        }
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $systemFrame[] = $addData;
        
        
        
        return $systemFrame;
    }
    
    function button($mode,$type,$abs=0) {
        
        switch($mode) {
            case "show" :
                $str = "<div class='systemFrameButtonShow systemFrame_".$type."'></div>";
                break;
            
            case "div" :
                if ($abs) $style = "style='margin-left:".$abs."px;margin-right:".$abs."px;'";
                $str = "<div class='systemFrameButton systemFrame_".$type."' $style title='Hallo'></div>";
                break;
            
            case "link" :
                $str = "<a href='#' class='systemFrameButton systemFrame_".$type."'></a>";
                break;
                
        }        
        return $str;
    }
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



function cmsSystemFrame_instance() {
    $ownPhpFile = "cms/cms_systemFrame_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsSystemFrame();

    } else {
        $class = new cmsSystemFrame_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    return $class;    
}


function cmsSystemFrame_editList($editContent) {
    $instance = cmsSystemFrame_instance();
    $res = $instance->editList($editContent);
    return $res;
}



function cmsSystemFrame_frameStart($contentData,$frameWidth,$textData=array()) {
    $instance = cmsSystemFrame_instance();
    $res = $instance->frameStart($contentData,$frameWidth,$textData);
    return $res;
}

function cmsSystemFrame_frameEnd($contentData,$frameWidth,$textData=array()) {
    $instance = cmsSystemFrame_instance();
    $res = $instance->frameEnd($contentData,$frameWidth,$textData);
    return $res;
}

?>
