<?php // charset:UTF-8
class cmsType_text_base extends cmsClass_content_show {
    function getName() {
        return "Text";
    }
    
    function setMainClass($mainClass=0) {
        if (is_object($mainClass)) {
            $this->mainClass = $mainClass;
            // echo ("<h1>Set Main Class in ".$this->getName()."</h1>");
        }
    }
    
    function init_own($contentData,$editContent,$frameWidth,$textData,$editText,$mainClass=0) {
        $this->contentData = $contentData;
        $this->editContent = $editContent;
        $this->frameWidth = $frameWidth;
        $this->textData = $textData;
        $this->editText = $editText;
        $this->contentId = $this->contentData[id];
        $this->contentType = $this->contentData[type];
        if (is_object($mainClass)) {
            echo ("<h1>MAINCLASS </h1>");
            echo ("$mainClass->contentId <br>");  
            $this->mainClass = $mainClass;
        }
    }
            
   
    function contentType_show() {
        $id = $this->contentData[id];
        $pageId = $this->contentData[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        // echo ("Show Text width=$this->frameWidth<br>");
        //show_array($GLOBALS[pageInfo]);

        $contentCode = "text_$id";

        //show_array($data);
        
        if ($editMode == "editText" AND $editId == $contentCode) {
            cms_contentType_TextEdit($contentCode,$this->contentData);
            return 0;
        } 
        $innerWidth = $this->innerWidth;
        if (intval($data[width])) {
            $innerWidth = $this->getValue_fromString($data[width],$this->frameWidth);
            //echo ("Text Width definiert '$data[width]' innerWidth = $innerWidth <br>");
        }
        $style = "";
        $style .= "width:".$innerWidth."px;";
        $style = "width:auto;";

        $divName = "textBox textboxId_$id textBoxPage_".$GLOBALS[pageInfo][pageName];
        div_start($divName,$style);


        $textList = $this->textData;

        // show HeadLine
        $showText = $textList[headline];
        $this->text_showHeadLine($showText,$id);

        // show Text
        $this->text_showText($showText,$id);

        // show Buttons
        $this->text_showButton($textList,$id);

        div_end($divName);
    }




    function text_showHeadLine($headlineData,$id) {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $textData = $useClass->textData[headline];
        
        $headline = $useClass->lgStr($textData);
        // echo ("Textdata= $textData / text='$headline' <br>");
        
//        if (is_array($textData)) {
//            $text = $textData[text];
//            if (is_array($text)) {
//            
//                echo ("array $text $useClass->showLg ");
//                $text = $text[$useClass->showLg];
//                foreach ($text as $k => $v) echo ("$k=$v | ");
//            }
//            // echo ("'$text' <br>");
//        }
        
        
        
        $wireType = "headLine";
        $wireOn = $useClass->wireframe_use($wireType);
        // $wireOn = $wireframeData[headLine];
        
        if ($wireOn) {
            //$wireText = $useClass->contentData[wireframe][$wireType."Text"];
            //  echo (" wireOn = $wireOn wireText = $wireText <br>");
            $wireData = array();
            $wireData[type] = $wireType;
            $wireData[orgText]  = $headline;          // Normaler Text
            $wireData[id]       = $useClass->contentId;
            $name = $useClass->contentData[title];
            if (is_array($name)) $name = $name[$useClass->showLg];
            $wireData[name]     = $name;
            // $wireData[wireText] = $wireText;
            $wireData[debug]    = 0;
                    
            $headline = $useClass->wireframe_text($wireData);            
        }
 
        
       
                
        // $headline = $headlineData["lg_"."dt"];
        $headlineCss = $headlineData[css];
        $style = "";
        $data = $headlineData[data];
        if (is_array($data)) {
            $showColor = cmsStyle_getColor($data);
            if ($showColor) $style = "color:$showColor;border-color:$showColor;";
            
        }
        // echo ("Head $headline $headlineCss $style<br>");
        
        if ($headline) {
            $divName_headLine = "textHeadline textHeadlineId_$id textHeadlinePage_".$GLOBALS[pageInfo][pageName];
            if ($headlineCss) $divName_headLine .= " textHeadline_$headlineCss";
            $headline = $useClass->showText($headline);
            div_start($divName_headLine,$style);
            if ($headlineCss) echo ("<$headlineCss>$headline</$headlineCss>");
            else echo ("<h4>$headline</h4> ");
            div_end($divName_headLine);
        }
    }

    function text_showText($textData,$id) {
        
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        //  echo ("<h1>ShowText</h1>");
        
        
        $textData = $useClass->textData[text];
        
       
        $text = $useClass->lgStr($textData);
        $wireType = "text";
        $wireOn = $useClass->wireframe_use($wireType);
        // $wireOn = $wireframeData[headLine];
        
        if ($wireOn) {
            $wireText = $useClass->contentData[wireframe][$wireType."Text"];
            // echo (" wireOn = $wireOn wireText = $wireText <br>");
            $wireData = array();
            $wireData[orgText]  = $text;          // Normaler Text
            $wireData[id]       = $useClass->contentId;
            $name = $useClass->contentData[title];
            if (is_array($name)) $name = $name[$useClass->showLg];
            $wireData[name]     = $name;
            // $wireData[wireText] = $wireText;
            $wireData[type] = $wireType;
            $wireData[debug]    = 0;
                    
            $text = $useClass->wireframe_text($wireData);
        }
        
        
        $readMore = $useClass->contentData[data][text_readMore];
        $maxChar = $useClass->contentData[data][text_maxChar];
        
        $text = $this->showText($text,$maxChar,$readMore);
        $textCss = $textData[css];
        $data = $textData[data];
        $style = null;
        if (is_array($data)) {
            $showColor = cmsStyle_getColor($data);
            if ($showColor) $style = "color:$showColor;border-color:$showColor;";
            
        }
        
        if ($text) {
            
            $divName_text = "textText textTextId_$id textTextPage_".$GLOBALS[pageInfo][pageName];
            if ($textCss) $divName_text .= " textText_$textCss";
            div_start($divName_text,$style);
            echo ($text);
//           
//            
//            // $text = nl2br($text); // 
//            // $text = str_replace(array("\0","\n","\n"), "**", $text);
//            $text = str_replace(array("<br/>","&nbsp;","\r"),"",$text);
//            // $lineList = explode("<br />",$text);
//            $lineList = explode("\n",$text);
//            for ($i=0;$i<count($lineList);$i++) {
//                // echo ("<b>$i</b>");
//                $line = $lineList[$i]; 
//                $long = strlen($line);
//                // echo ("'".substr($line,$long-4,$long)."---".$line[$long-2]."'");
//                
//                // $line = trim($line);
//                if (strlen($line)) {
//                    echo ("$line<br />");
//                } else {
//                    // if ($i+1 < count($lineList)) {
//                        if ($lineList[$i-1] == "") echo "&nbsp;";
//                        echo ("<br />");
//                    // }
//                }
//            }
            div_end($divName_text);
        }
    }

    function text_showButton() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;

        $textList = $useClass->textData;
        $id‚ = $useClass->vontentId;
       

        
        $str = "";
        if (!is_array($textList)) return 0;
        ksort($textList);
        foreach ($this->textData as $textKey => $buttonData) {
            if (substr($textKey,0,6) != "button") continue;
//            echo "is button $textKey <br>";
//            
            
            $buttonText = $useClass->lgStr($buttonData);
//            foreach ($buttonData as $key => $value ) {
//                echo ("data is $key = $value <br>");
//            }
//            
//            $buttonText = $buttonData[text];
//            echo ("ButtonText $buttonText <br>");
//            if (!$buttonText) {
//                $lg = cms_text_getLanguage();
//                if ($buttonData["lg_".$lg]) $buttonText = $buttonData["lg_".$lg];
//            }
            $buttonCss  = $buttonData[css];
            $buttonLink = $buttonData[data][link];
            $offSet = strpos($buttonLink,"|");
            if ($offSet) {
                $goPageId = intval(substr($buttonLink,0,$offSet));
                $goPageAdd = substr($buttonLink,$offSet+1);
                // $str .= " id='$goPageId' add='$goPageAdd' ";
            } else {
                $goPageId = intval($buttonLink);
                $goPageAdd = "";
            }
                
            // $str .= "bl = $buttonLink ";
            if ($goPageId) {
                $pageData = $useClass->page_getData($goPageId);
                if (is_array($pageData)) $goLink = $pageData[name].".php";
                
                if ($goPageAdd) $goLink .= "?".$goPageAdd;
            }
           
            $buttonClass = "";
            switch ($buttonCss) {
                case "main"     : $buttonClass .= "mainLinkButton"; break;
                case "second"   : $buttonClass .= "mainLinkButton second"; break;
                case "readMore" : $buttonClass .= "readMore"; break;
                if ($buttonCss) {
                    $buttonClass .= "buttonLink_".$buttonCss;
                }
            }
            
            if ($goLink) {
                $str .= "<a href='$goLink' class='$buttonClass' >$buttonText</a>";
            } else {
                $str .= "noLink<span class='$buttonClass'>$buttonText</span>";
            }
            
            
            
        }
        
        if ($str) {
            $divName_button = "textButton textButtonId_$id textButtonPage_".$GLOBALS[pageInfo][pageName];
//            $str .= div_start_str($divName_button);
            $str = div_start_str($divName_button).$str.div_end_str($divName_button);
            
            echo ($str);
            
           
        }     
    }

    function text_saveOld($editText) {
        $textId = $_POST[textId];
        $editId = $_GET[editId];
        if (!$editId) {
            cms_errorBox("NO EditId by save_Text");
            return 0;
        }
        $contentId = "text_".$textId;

        $change = 0;
        $error = 0;

        foreach ($editText as $key => $value) {
            $text = $value[text];
            $save = 1;
//            if ($text=="") { // kein Inhalt
//                $id = $value[id];
//                // echo ("Dont Save text for $key because empty <br>");
//                if ($id) {
//                    // echo ("--> Text has id ($id) & contentId = $contentId <br>");
//                    // echo ("Delete Text with id !<br>");
//                    $save = 0;
//                    cms_text_delete($id);
//                    $delete = 1;
//                } else {
//                    //echo ("--> Dont Save because no Content<br>");
//                    $save = 0;
//                }
//                
//            }
            
            if ($save) {
                // echo ("<h3> SAVE $key </h3>");
                switch ($key) {
                    case "headline":
                        $value[name] = $key;
                        $value[contentId] = $contentId;               
                        $res = cms_text_save($value);
                        if ($res != 1) {$error++; echo ("Save Result for saveHeadline $res<br />"); }
                        else $change++;
                        break;
                    case "text" :
                        $value[name] = $key;
                        $value[contentId] = $contentId;
                        $res = cms_text_save($value);
                        if ($res != 1) {$error++; echo ("Save Result for saveText $res<br />"); }
                        else $change++;
                        break;

                    case "subText" :
                        $value[name] = $key;
                        $value[contentId] = $contentId;
                        $res = cms_text_save($value);
                        if ($res != 1) {$error++; echo ("Save Result for saveText $res<br />"); }
                        else $change++;
                        break;

                    case "pageId_":
                                // echo ("Save PageId $value<br />");
                        break;
                    default :
                       if (substr($key,0,6) == "button") {
                            $buttonNr = substr($key,6);
                            $saveData = $value;
                            $saveData[name] = $key;
                            $saveData[data] = $value[data];
                            $saveData[contentId] = $contentId;
                            
                            
                            $buttonName = $value[text];
                            $buttonId = $value[id];
                            
                            // echo ("Button nr=$buttonNr name=$buttonName id=$buttonId lang=".strlen($buttonName)."<br />");
                            //if (strlen($buttonName)>0) {
                            $value[name] = $key;
                            $value[contentId] = $contentId;
                            $res = cms_text_save($value);
                          
                            if ($res != 1) {$error++; echo ("Save Result for save $key is $res<br />"); }
                            else $change++;
                            
                        } else {
                            if (is_string($key)) {

                                // echo ("<h3>unkown Key in text_save $key = $value </h3>");
                                $value[name] = $key;
                                $value[contentId] = $contentId;

                                $res = cms_text_save($value);
                                
                                if ($res != 1) {
                                    $error++; 
                                    // echo ("Save Result for saveHeadline $res<br />");
                                    show_array($value);
                                }
                                else $change++;
                            } else {




                                // echo ("unkown Key in text_save $key = $value <br />");
                            }
                        }
                }
            }

        }
        return array("change"=>$change,"error"=>$error);
    }




    function contentType_editContent() {
        $useClass = $this;
        if ($this->mainClass) $useClass = $this->mainClass;
        
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();

        $id = $editContent[id];
        $pageId = $editContent[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $contentCode = "text_$useClass->contentId";
        // echo ("ContentCode = $contentCode <br />");
        // foreach ($this->editText as $key => $text) echo ("Text for $key = $text <br />");
        
        $res = array();
        $res[text] = array();
//        $frameSettings[showName] = $useClass->lga("content","tabFrame");
//        $frameSettings[showTab] = "Simple";
        
       
        $res[text][showName] = $useClass->lga("content","tabText");
        $res[text][showTab] = "Simple";
        
        $res[buttons] = array();
        $res[buttons][showName] = $useClass->lga("content","tabButtons");
        $res[buttons][showTab] = "Simple";

        //width
        $editType = $useClass->editContent[type];


        $addData = array();
        $addData["text"] = "hidden-Text Id";
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
        $res[text][] = $addData;

        if ($editType == "text") {
            $addData = array();
            $addData["text"] = $useClass->lga("contentType_text","textWidthName",":");//"Text Fluß Breite";
            $addData["input"] = "<input type='text' name='editContent[data][width]' value='$data[width]' >";
            $addData["mode"] = "More";
            $res[text][] = $addData;
        }

        $showData = array();
        $showData[css] = 1;
        $showData[color] = 1;
        $showData[width] = $useClass->frameWidth;
        $showData[name] = $useClass->lga("contentType_text","headlineName",":");//"Überschrift";
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $useClass->editContent_text("headline",$useClass->textData[headline], $showData);
        $res[text][] = $addData;

        $addData = array();
        $addData["text"] = "Text";
        
        // Text
        $showData[css] = 1;
        $showData[view] = "textarea";
        $showData[color] = 0;
        $showData[width] =  $useClass->frameWidth;
        $showData[name] = "text w=$useClass->frameWidth".$useClass->lga("contentType_text","textName",":");//"Text";
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $useClass->editContent_text("text",$useClass->textData[text], $showData);
        $res[text][] = $addData;


        
        
        // *** BUTTONS                                                    *** //
        $buttonCount = 1;
        $linkShowData = array("linkType"=>1);
        
        foreach ($useClass->textData as $textKey => $textValue ) {
            if (substr($textKey,0,6) != "button") continue;
            
            $buttonName = $textKey;
            $buttonNr = substr($buttonName,6);
            
            // Leerer Button
            $input = "";
            $showData = array();
            $showData[css] = 0;
            $showData[color] = 0;
            $showData[width] = 150;
            $showData[name] = $useClass->lga("contentType_text","ButtonText")." $buttonCount . :"; //$useClass->lga("contentType_text","headlineName",":");//"Überschrift";
            $showData[lgSelect] = 0;
            $showData[mode] = "More";
            $showData[out] = "input";
            $showData[editMode] = "SimpleLine";
            $input = $useClass->editContent_text($buttonName,$textValue, $showData);
            // $input .= cms_page_SelectMainPage($textValue[data][link], "editText[$buttonName][data][link]");
            $input .= $this->editContent_selectSettings("link",$textValue[data][link], "editText[$buttonName][data][link]",$linkShowData);
            $input .= $useClass->filter_select("button", $textValue[css],"editText[$buttonName][css]",array("empty"=>$styleEmpty,"style"=>"width:150px;"));
            // $input .= "<input type='hidden' value='".$textValue[id]."' name='editText[$buttonName][id]'>";

            $addData = array();
            $addData["text"] = $useClass->lga("contentType_text","ButtonText")." $buttonNr :";
            $addData["input"] = $input;
            $addData["mode"] = "Simple";
            $res[buttons][] = $addData;
            
            $buttonCount++;
        }
        
        
        $buttonName = "button".$buttonCount;
        // Leerer Button
        $input = "";
        $showData = array();
        $showData[css] = 0;
        $showData[color] = 0;
        $showData[width] = 150;
        $showData[name] = $useClass->lga("contentType_text","newButtonText",":"); //$useClass->lga("contentType_text","headlineName",":");//"Überschrift";
        $showData[lgSelect] = 0;
        $showData[mode] = "More";
        $showData[out] = "input";
        $showData[editMode] = "SimpleLine";
        $input = $useClass->editContent_text($buttonName,$editText[$buttonName], $showData);
        
        
        
        // $input .= "<input type='text' style='width:200px;' name='editText[$buttonName][text]' value='".$editText[$buttonName][text]."' > ";
        $input .= ""; //cms_page_SelectMainPage($editText[$buttonName][data][link], "editText[$buttonName][data][link]");
        $input .= $this->editContent_selectSettings("link",$editText[$buttonName][data][link], "editText[$buttonName][data][link]",$linkShowData);
        // $input .= cms_content_selectStyle("button",$editText[$buttonName][css],"editText[$buttonName][css]");
        $input .= $useClass->filter_select("button", $editText[$buttonName][css],"editText[$buttonName][css]",array("empty"=>$styleEmpty,"style"=>"width:150px;"));
        // echo ("<input type='text' style='width:200px;' name='editText[button1][link] value='".$editText[button1][link]."' ><br />");
        //$input .= "<input type='hidden' value='".$editText[$buttonName][id]."' name='editText[$buttonName][id]'>";

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_text","newButtonText",":"); //"Button $buttonNr";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[buttons][] = $addData;

        return $res;
    }


    function text_textEdit($contentCode,$contentData) {
        global $pageInfo,$pageData;
        if ($_POST[editTextCancel]) {
            echo ("CancelSave<br />");
            reloadPage($pageInfo[page]);
        }
        $editTextSave = $_POST[editTextSave];
        $editTextSaveClose = $_POST[editTextSaveClose];
        if ($editTextSave OR $editTextSaveClose) $textSave = 1;
        if ($textSave) {
            $editText = $_POST[editText];
            echo ("Save Text <br />");

            $error = 0;
            foreach ($editText as $key => $value) {
                switch ($key) {
                    case "headline":
                        $value[name] = $key;
                        $res = cms_text_save($value);
                        if ($res != 1) {$error++; echo ("Save Result for saveHeadline $res<br />"); }
                        break;
                    case "text" :
                        $value[name] = $key;
                        $res = cms_text_save($value);
                        if ($res != 1) {$error++; echo ("Save Result for saveText $res<br />"); }
                        break;
                    case "subText" :
                        $value[name] = $key;
                        $res = cms_text_save($value);
                        if ($res != 1) {$error++; echo ("Save Result for saveText $res<br />"); }
                        break;

                    case "pageId":
                        // echo ("Save PageId $value<br />");
                        break;
                    default :
                        echo ("unkown $key in SaveText<br />");
                        if (substr($key,0,6) == "button") {
                            $buttonNr = substr($key,6);
                            $saveData = $value;
                            $saveData[name] = $key;
                            $saveData[data] = "page_".$value[link];
                            $buttonName = $value[text];
                            $buttonId = $value[id];
                            echo ("Button $buttonNr $buttonName $buttonId ".strlen($buttonName)."<br />");
                            if (strlen($buttonName)>0) {
                                $res = cms_text_save($saveData);
                                echo ("ButtonSave <br />");
                            } else {
                                $editText[$key] = null;
                                if ($buttonId) {
                                    cms_text_delete($buttonId);
                                    echo ("No ButtonName, but Id $buttonId -> delete ButtonText()<br />");
                                }
                            }
                            if ($res != 1) {$error++; echo ("Save Result for save $key is $res<br />"); }
                        } else {
                            echo ("unkown $key in SaveText<br />");
                        }
                }
            }



            if ($error == 0) {
                echo ("Texte gespeichert <br />");
                if ($editTextSaveClose) {
                    $goPage = $pageInfo[page];
                    reloadPage($goPage,3);
                    return "save";
                }

            }
        }


        if (!is_array($editText)) {
            $editText = array();
            $editTexT = array("headline"=>array(),"text"=>array(),"button1"=>array());
            $editText[pageId] = $useClass->contentData[pageId];


            $textData = cms_text_getForContent($contentCode);
            $maxButton = 0;
            foreach($textData as $key => $value) {
                switch ($key) {
                    case "headline" :
                        $editText[$key] = $value;
                        break;
                    case "text" :
                        $editText[$key] = $value;
                        break;
                    default :
                        if (substr($key,0,6) == "button") {
                            $buttonNr = substr($key,6);
                            $maxButton = $buttonNr;
                            $data = $value[data];
                            $editText[$key] = $value;
                            if (substr($data,0,5)== "page_") $editText[$key][link] = substr($data,5);
                        } else {
                            echo ("unkown $key from cms_text_getForContent $value <br />");
                        }
                }
            }
        }

        $buttonCount = 1;
        $buttonName = "button".$buttonCount;
        while (is_array($editText[$buttonName])) {
            // echo "Gefunden Button $buttonCount";
            $buttonCount++;
            $buttonName = "button".$buttonCount;
        }
        // echo ("ButtonAnz = $buttonCount <br />");


        div_start("cmsTextEditFrame");
        echo("<form method='post' class='cmsForm' >");
        echo ("<input type='hidden' value='$editText[pageId]' name='editText[pageId]' >");

        echo ("Überschrift:<br /><input type='text' name='editText[headline][text]' value='".$editText[headline][text]."' style='width:100%;' ><br />");
        echo ("<input type='text' value='".$editText[headline][id]."' name='editText[headline][id]'>");
        echo ("<input type='text' value='".$editText[headline][css]."' name='editText[headline][css]'>");

        echo ("Text:<br /><textarea name='editText[text][text]' style='width:100%;height:150px;' >".$editText[text][text]."</textarea><br />");
        echo ("<input type='hidden' value='".$editText[text][id]."' name='editText[text][id]'>");
        echo ("<input type='hidden' value='".$editText[text][css]."' name='editText[text][css]'>");

        // Buttons
        for ($buttonNr=1;$buttonNr<=$buttonCount;$buttonNr++) {
            $buttonName = "button".$buttonNr;
            echo ("Button:<br /><input type='text' style='width:200px;' name='editText[$buttonName][text]' value='".$editText[$buttonName][text]."' > ");
            echo (cms_page_SelectMainPage($editText[$buttonName][link], "editText[$buttonName][link]"));
            echo (cms_content_selectStyle("button",$editText[$buttonName][css],"editText[$buttonName][css]")."<br />");
            // echo ("<input type='text' style='width:200px;' name='editText[button1][link] value='".$editText[button1][link]."' ><br />");
            echo ("<input type='hidden' value='".$editText[$buttonName][id]."' name='editText[$buttonName][id]'>");
        }

        //echo ("- Typ: ".cms_content_SelectType($addContent[type],"editText[type]")." ");
        echo ("<input type='submit' class='cmsInputButton' name='editTextSaveClose' value='speichern und schließen'>");
        echo ("<input type='submit' class='cmsInputButton' name='editTextSave' value='speichern'>");

        echo ("<input type='submit' class='cmsInputButton cmsSecond' name='editTextCancel' value='abbrechen'>");
        echo ("</form>");
        div_end("cmsTextEditFrame","before");
            //}
    }



}

function cmsType_text_class() { 
    if ($GLOBALS[cmsTypes]["cmsType_text.php"] == "own") $textClass = new cmsType_text();
    else $textClass = new cmsType_text_base();
    return $textClass;
}




function cms_contenttype_text($contentData,$frameWidth) {
    $textClass = cmsType_text_class();
    $textClass->show($contentData,$frameWidth);
    return $textClass;
}



function cms_contentType_text_editContent($contentData,$frameWidth) {
    $textClass = cmsType_text_class();
    // return $textClass->text_editContent($editContent,$useClass->frameWidth);
}


function cms_contentType_text_save($editText) {
    $textClass = cmsType_text_class();
    return $textClass->text_saveOld($editText);
}



function cms_contentType_TextEdit($contentCode,$contentData) {
    $textClass = cmsType_text_class();
    $textClass->text_textEdit($contentCode,$contentData);
}

function cmsType_text_editContent($editContent,$frameWidth) {
    return cms_contentType_TextEdit($editContent,$frameWidth);
}

function cmsType_text($contentData,$frameWidth) {
    $textClass = cmsType_text_class();
    $textClass->show($contentData,$frameWidth);
    return $textClass;
}

?>
