<?php // charset:UTF-8
class cmsType_text_base extends cmsType_contentTypes_base {
    function getName() {
        return "Text";
    }
    

    function text_show($contentData) {
        $id = $contentData[id];
        $pageId = $contentData[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        //show_array($GLOBALS[pageInfo]);

        $contentCode = "text_$id";


        if ($editMode == "editText" AND $editId == $contentCode) {
            cms_contentType_TextEdit($contentCode,$contentData);
        } else {
            $divName = "textBox textboxId_$id textBoxPage_".$GLOBALS[pageInfo][pageName];
            div_start($divName);

           // echo ("Show Text for $contentCode <br />");

            $textList = cms_text_getForContent($contentCode);


            // show HeadLine
            $this->text_showHeadLine($textList[headline],$id);

            // show Text
            $this->text_showText($textList[text],$id);

            // show Buttons
            $this->text_showButton($textList,$id);

            div_end($divName);

        }
    }




    function text_showHeadLine($headlineData,$id) {
        // echo ("Show Text for $contentCode <br />");
        $headline = $headlineData[text];
        $headlineCss = $headlineData[css];
        if ($headline) {
            $divName_headLine = "textHeadline textHeadlineId_$id textHeadlinePage_".$GLOBALS[pageInfo][pageName];
            if ($headlineCss) $divName_headLine .= " textHeadline_$headlineCss";
            div_start($divName_headLine);
            if ($headlineCss) echo ("<$headlineCss>$headline</$headlineCss>");
            else echo ("<h4>$headline</h4> ");
            div_end($divName_headLine);
        }
    }

    function text_showText($textData,$id) {
        $text = $textData[text];
        $textCss = $textData[css];
        if ($text) {
            $divName_text = "textText textTextId_$id textTextPage_".$GLOBALS[pageInfo][pageName];
            if ($textCss) $divName_text .= " textText_$textCss";
            div_start($divName_text);
            $lineList = explode("\r\n",$text);
            for ($i=0;$i<count($lineList);$i++) {
                $line = $lineList[$i];
                if (strlen($line)) echo ("$line<br />");
                else {
                    if ($i+1 < count($lineList)) echo ("&nbsp;<br />");
                }
            }
            div_end($divName_text);
        }
    }

    function text_showButton($textList,$id) {
        $buttonNr = 1;
        $buttonName = "button".$buttonNr;


        if (is_array($textList[$buttonName])) {
            $divName_button = "textButton textButtonId_$id textButtonPage_".$GLOBALS[pageInfo][pageName];
            div_start($divName_button);
            while (is_array($textList[$buttonName])) {
                $buttonData = $textList[$buttonName];
                $buttonText =$buttonData[text];
                $buttonCss = $buttonData[css];
                $buttonLink = $buttonData[data];
                if (substr($buttonLink,0,5) == "page_") {
                    $pageId = intval((substr($buttonLink,5)));
                    if ($pageId > 0) {
                        $pageData = cms_page_getData($pageId);
                        if (is_array($pageData)) $buttonLink = $pageData[name].".php";
                        // foreach ($pageData as $key => $value) echo ("PD for $pageId $key => $value <br />");
                    }
                }
                echo ("<a href='$buttonLink' ");
                switch ($buttonCss) {
                    case "main" : echo ("class='linkButton' "); break;
                    case "second" : echo ("class='linkButton second' "); break;
                }
                echo (" >$buttonText</a> ");
                $buttonNr++;
                $buttonName = "button".$buttonNr;
            }
            div_end($divName_button);
        }
    }

    function text_save($editText) {
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
            if ($text=="") { // kein Inhalt
                $id = $value[id];
                // echo ("Dont Save text for $key because empty <br>");
                if ($id) {
                    // echo ("--> Text has id ($id) & contentId = $contentId <br>");
                    // echo ("Delete Text with id !<br>");
                    $save = 0;
                    cms_text_delete($id);
                    $delete = 1;
                } else {
                    //echo ("--> Dont Save because no Content<br>");
                    $save = 0;
                }
                
            }
            
            if ($save) {

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
                            $saveData[data] = "page_".$value[data];
                            $saveData[contentId] = $contentId;
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
                            else $change++;
                        } else {
                            if (is_string($key)) {

                               //  echo ("<h3>unkown Key in text_save $key = $value </h3>");
                                $value[name] = $key;
                                $value[contentId] = $contentId;

                                $res = cms_text_save($value);
                                if ($res != 1) {
                                    $error++; 
                                    echo ("Save Result for saveHeadline $res<br />");
                                    show_array($value);
                                }
                                else $change++;
                            } else {




                                echo ("unkown Key in text_save $key = $value <br />");
                            }
                        }
                }
            }

        }
        return array("change"=>$change,"error"=>$error);
    }




    function text_editContent($editContent,$frameWidth) {    
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $id = $editContent[id];
        $pageId = $editContent[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $contentCode = "text_$id";

        // show_array($data);
        // foreach ($editContent as $key => $value ) echo (" editContent $key = $value <br />");
        // foreach ($editContent[data] as $key => $value ) echo (" data $key = $value <br />");

        $editText = $_POST[editText];
        if (!is_array($editText)) {

            $editText = cms_text_getForContent($contentCode);
        } else {
           // show_array($editText);
        }
        //show_array($editContent);

        $res = array();
        $res[text] = array();
        $res[buttons] = array();

        //width
        $editType = $editContent[type];


        $addData = array();
        $addData["text"] = "hidden-Text Id";
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
        $res[text][] = $addData;

        if ($editType == "text") {
            $addData = array();
            $addData["text"] = "Text Fluß Breite";
            $addData["input"] = "<input type='text' name='editContent[data][width]' value='$data[width]' >";
            $res[text][] = $addData;
        }

        $addData = array();
        $addData["text"] = "Überschrift";
        $input  = "<input type='text' style='width:".$frameWidth."px;' name='editText[headline][text]' value='".$editText[headline][text]."' >";
        $input .= "<input type='hidden' value='".$editText[headline][id]."' name='editText[headline][id]'>";  
        $addData["input"] = $this->filter_select("style", $editText[headline][css],"editText[headline][css]",array("empty"=>"Bitte Überschrift wählen"),"headline");
        $addData["secondLine"] = $input;
        $res[text][] = $addData;

        $addData = array();
        $addData["text"] = "Text";
        $input  = "<textarea name='editText[text][text]' style='width:".$frameWidth."px;height:150px;' >".$editText[text][text]."</textarea>";
        $input .= "<input type='hidden' value='".$editText[text][id]."' name='editText[text][id]'>";    
        $addData["input"] = $this->filter_select("style", $editText[text][css],"editText[text][css]",array("empty"=>"Bitte Text-Darstellung wählen"),"text");
        $addData["secondLine"] = $input;
        $res[text][] = $addData;

        $buttonCount = 1;
        $buttonName = "button".$buttonCount;
        while (is_array($editText[$buttonName])) {
            if (strlen($editText[$buttonName][text])>0) {
                // echo "Gefunden Button $buttonCount";
                // show_array($editText[$buttonName]);
                $buttonLink = $editText[$buttonName][data];
                if (substr($buttonLink,0,5)=="page_") {
                    $buttonLink = substr($buttonLink,5);
                } else {
                    // echo ("no Page before in buttonLink ($buttonLink)".substr($buttonLink,0,5)."<br />");

                }
                // echo ("ButtonLink = $buttonLink <br />" );

                $input = "<input type='text' style='width:200px;' name='editText[$buttonName][text]' value='".$editText[$buttonName][text]."' > ";
                $input .= cms_page_SelectMainPage($buttonLink, "editText[$buttonName][data]");
                $input .= cms_content_selectStyle("button",$editText[$buttonName][css],"editText[$buttonName][css]");
                // echo ("<input type='text' style='width:200px;' name='editText[button1][link] value='".$editText[button1][link]."' ><br />");
                $input .= "<input type='hidden' value='".$editText[$buttonName][id]."' name='editText[$buttonName][id]'>";

                $addData = array();
                $addData["text"] = "Button $buttonCount";
                $addData["input"] = $input;
                $res[buttons][] = $addData;

                $buttonCount++;
                $buttonName = "button".$buttonCount;

            } else {
                // echo ("nicht gefunden $buttonName <br />");
                $buttonName = "last";
            }

        }

        $buttonName = "button".$buttonCount;
        // Leerer Button
        $input = "<input type='text' style='width:200px;' name='editText[$buttonName][text]' value='".$editText[$buttonName][text]."' > ";
        $input .= cms_page_SelectMainPage($editText[$buttonName][link], "editText[$buttonName][link]");
        $input .= cms_content_selectStyle("button",$editText[$buttonName][css],"editText[$buttonName][css]");
        // echo ("<input type='text' style='width:200px;' name='editText[button1][link] value='".$editText[button1][link]."' ><br />");
        $input .= "<input type='hidden' value='".$editText[$buttonName][id]."' name='editText[$buttonName][id]'>";

        $addData = array();
        $addData["text"] = "Button $buttonNr";
        $addData["input"] = $input;
        $res[buttons][] = $addData;

        // foreach($res[text] as $key => $val ) echo (" $key = $val[text] <br />");


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
            $editText[pageId] = $contentData[pageId];


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
    $textClass->text_show($contentData,$frameWidth);
}



function cms_contentType_text_editContent($editContent,$frameWidth) {
    $textClass = cmsType_text_class();
    return $textClass->text_editContent($editContent,$frameWidth);
}


function cms_contentType_text_save($editText) {
    $textClass = cmsType_text_class();
    return $textClass->text_save($editText);
}



function cms_contentType_TextEdit($contentCode,$contentData) {
    $textClass = cmsType_text_class();
    $textClass->text_textEdit($contentCode,$contentData);
}

function cmsType_text_editContent($editContent,$frameWidth) {
    return cms_contentType_TextEdit($editContent,$frameWidth);
}

function cmsType_text($contentData,$frameWidth) {
    cms_contenttype_text($contentData,$frameWidth);
}

?>
