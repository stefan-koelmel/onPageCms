<?php // charset:UTF-8


class  cmsType_contentName_base extends cmsClass_content_show {
 //cmsType_contentName_base  extends cmsType_contentTypes_base {
    function getName() {
        return "gespeicherter Inhalt";
    }
  
    function contentType_init() {
        //  echo ("<h1>Content Init $this->contentData</h1>");
        
        $contentData = $this->contentData;
        $frameWidth = $this->innerWidth;

        $data = $contentData[data];
        if (!is_array($data)) {
            cms_errorBox("Keine Daten für das Anzeigen von eigenen Inhalten");
            return 0;
        }

        
        $this->contentData[specialType] = "contentName";
        $this->saveContentId = $data[contentName];
        $showContentData = 0;
        if ($this->saveContentId > 0) {
            $this->saveContentData = cms_content_getId($this->saveContentId);
            $orgData = $this->saveContentData;
        }

        if (!is_array($this->saveContentData)) {
            $this->contentData[specialId] = 0;
            // cms_errorBox("Eigener Inhalt nicht gefunden mit ID $this->saveContentId - $this->saveContentData");
            return 0;
        }
        
        $this->contentData[specialId] = $this->saveContentId;

//        echo ("<h3>MyContent</h3>");
//        echo ("Type = $contentData[type]  id = $contentData[id] viewContent = $contentData[viewContent]<br>");
//
//        echo ("<h3>SavedContent</h3>");
//        echo ("Type = ".$this->saveContentData[type]."  id = ".$this->saveContentData[id]." viewContent = ".$this->saveContentData[viewContent]."<br>");
        $selectContent = $data[contentName];
        // echo ("INIT ".$this->contentData[data][contentName]." <br>");
        //$this->saveContentData[data][contentName] = $selectContent;
        
        $this->saveType = $this->saveContentData[type];


        $this->saveContentData[viewContent] = $contentData[viewContent];
        $this->saveContentData[specialType] = "contentName";
        // $this->saveContentData[specialId] = $this->contentData[id];
        $this->saveContentData[specialData] = $this->contentData;
        $this->saveContentData[specialOrg] = $orgData;
        
        $this->saveContentData = $this->contentName_mergeData();
        
        foreach ($this->saveContentData as $key => $value) {
            if (!$value) continue;
            if (is_array($value)) continue;
            if (!is_string($value)) continue;
            $help = str2Array($value);
            if (is_array($help)) $this->saveContentData[$key] = $help;
        }
        

        switch ($this->saveType) {
             case "frame1" : $this->saveType = "frame"; break;
             case "frame2" : $this->saveType = "frame"; break;
             case "frame3" : $this->saveType = "frame"; break;
             case "frame4" : $this->saveType = "frame"; break;
         }

        $functionName = "cmsType_".$this->saveType."_class";
        if (!function_exists($functionName)) {
            echo ("class Function not exist $functionName ");
            return 0;
        }

        $this->saveClass = call_user_func($functionName);
        if (!is_object($this->saveClass)) {
            echo "no Class get";
            return 0;
        }
//
//        // echo ("view=$viewContent editLayout = $this->editLayout <br/>");
//        switch ($viewContent) {
//            case "content" : 
        if (!method_exists ($this->saveClass ,"content_show")) {
            echo ("Method $callFunction not exist");
            return 0;
        }



        if (!is_object($this->pageClass)) {
            echo ("pageClass no exist<br>");
            return 0;
        }

        if (!is_object($this->saveClass)) {
            echo ("NO SAVE CLASS get ");
            return 0;
        }
        $this->saveClass->init_PageClass($this->pageClass);
        // $this->saveClass->setMainClass($this);    
        $this->saveClass->contentType_init();
       
    }

    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->innerWidth;

        
        
        $this->contentName_FrameStart();
        
        if (!is_object($this->saveClass)) {
            echo ("No SaveClass get <br>");  
            return 0;
        }
       
        
        
        $this->saveClass->content_show($this->saveContentData,$frameWidth);
        
        $this->contentName_FrameEnd();
        return 0;
       

    }
    
    
    function contentName_FrameStart() {
        $className = "cmsContent_contentName cmsContent_contentName_start";
        if ($this->pageEditAble) {
            $className .= " cmsEditToggle";
            if (!$this->edit) $className .= " cmsEditHidden";
        } else {
            return 0;
        }
        echo ("<div class='$className' >");
        if (!is_object($this->saveClass)) { 
            echo ("No SaveContent Select ");
        } else {
        
            echo ("CONTENT NAME START   <span class='cmsContent_contentName_mergeButton' >MERGE-DATA</span><br>");

            echo ("<div class='cmsContent_contentName_mergeData cmsEditHidden' >");
            echo ($this->mergeOut);
            echo ("</div>");
        }
//        foreach ($this->saveContentData as $key => $value) {
//            echo ("contentData $key = $value org = ".$this->contentData[$key]." <br>");
//        }
        echo ("</div>");
        
    }
    
    function contentName_FrameEnd() {
        $className = "cmsContent_contentName cmsContent_contentName_end";
        if ($this->pageEditAble) {
            $className .= " cmsEditToggle";
            if (!$this->edit) $className .= " cmsEditHidden";
        } else {
            return 0;
        }
        echo ("<div class='$className' ></div>");
    }

    function contentType_editContent() {
        $editContent = $this->editContent;
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        
        
        
        $contentList = cms_content_contentNameList();
        if (is_array($contentList)) {
            // show_array($contentList);
        }


        $res = array();
        $res[contentName] = array();
        $res[contentName][showName] = $this->lga("content","contentNameTab");
        $res[contentName][showTab] = "Simple";

        //width1
        $addData = array();
        $addData["text"] = "Inhalt auswählen";
        $addData["input"] = cms_content_Select_contentName($data[contentName],"editContent[data][contentName]");
        $addData["mode"] = "Simple";
        //"<input type='text' name='editContent[data][contentName]' value='$data[contentName]' >";
        $res[contentName][] = $addData;
        
        if (!is_object($this->saveClass)) {
            echo ("<h1> NO SAVE CLASS GET in edit contentName </h1>");
            return $res;
        }
        
        
        if (!method_exists ($this->saveClass ,"contentType_editContent")) {
            echo ("<h1>Not exist method contentType_editContent in contentName </h1>");
            return $res;            
        }
        
        $addInput = $this->saveClass->contentType_editContent();
        foreach ($addInput as $addKey => $addValue) {
            if (is_string($addKey)) {
                $res[$addKey] = $addValue;
                foreach ($addValue as $key => $value) echo ("add $key => $value <br>");
            }
            echo ("add to res $addKey = $addValue <br>");
        }
//            $addInput = $this->contentType_editContent();


        return $res;

    }
    
    
    function contentName_mergeData() {
        $mergeOut = "";
        $mergedData = $this->saveContentData;
        
        foreach ($this->contentData as $key => $value) {
            $change = 1;
            switch ($key) {
                case "type"        : $change = 0; break;
                case "sort"        : $change = 0; break;
                case "id"          : $change = 0; break;
                case "pageId"      : $change = 0; break;
                case "contentName" : $change = 0; break;
                case "specialView" : $change = 0; break;
                case "viewContent" : $change = 0; break;
                 
                
                default : 
                    if (is_array($value)) {
                        $mergeOut .= "CHECK ARRAY ($key) <br>";
                        foreach ($value as $dataKey => $dataValue ) {
                            if ($mergedData[$key][$dataKey] == $dataValue ) {
                                $mergeOut .= " --> no Change in array($key) $dataKey | $dataKey = $dataValue <br>";
                            } else {
                                $mergeOut .= " --> change in array($key) $dataKey from ".$mergedData[$key][$dataKey]." to <b>$dataValue</b> <br>";
                                $mergedData[$key][$dataKey] = $dataValue;
                            }

                        }
                        $change = 0;
                    } else {
                        // echo ("unkown key($key) = $value <br>");
                    }
                    
                    
                  
              }
            
            if ($change) {
                if ($value == $mergedData[$key] ) {
                    // echo ("no Change for $key = $value <br>");
                } else {
                    $mergeOut .= "Change '$key' from '$mergedData[$key]' to '<b>$value</b>' <br>";
                    // $mergedData[$key] = $value;
                }
                
            }
            
            
            
        }
        $this->mergeOut = $mergeOut;
        return $mergedData;
        
       



    }
    
    function contentName_data($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) {
            // cms_errorBox("Keine Daten für das Anzeigen von eigenen Inhalten");
            // return array();
            return 0;
        }

        $showContentId = $data[contentName];
        $showContentData = 0;
        if ($showContentId > 0) {
            $showContentData = cms_content_getId($showContentId);

        }

        if (!is_array($showContentData)) {
            cms_errorBox("Eigener Inhalt nicht gefunden mit ID $showContentId - $showContentData");
            return 0;
        }

        $mergedData = $showContentData;


        foreach ($showContentData as $key => $value) {
            if (is_array($value)) {
                foreach($value as $dataKey => $dataValue ) {
                    if ($contentData[$key][$dataKey]) {

                        if ($contentData[$key][$dataKey] != $dataValue ) {
             //               echo ("Modification for $key | $dataKey => ".$contentData[$key][$dataKey]." ");
               //             echo (" -> ist diffrent - was '$dataValue' <br />");
                            $mergedData[$key][$dataKey] = $contentData[$key][$dataKey];

                        }
                    }
                    // echo (" -- DATA - $dataKey => from ContentName Data = '$dataValue' | from Modification = '".$contentData[$key][$dataKey]."' <br /> ");
                }
            } else {
                if ($value != $contentData[$key]) {
                    switch ($key) {
                        case "type" : break;
                        case "id" : break;
                        case "pageId" : break;
                        case "contentName" : break;

                        default:
                            //echo ("Modification for $key => ".$contentData[$key]." ");
                            // secho (" -> ist diffrent - was '$value' <br />");
                            $mergedData[$key] = $contentData[$key];
                    }


                }
                //echo ("$key => from ContentName Data = '$value' | from Modification = '$contentData[$key]' <br /> ");
            }
        }


        // echo ("Eigener Inhalt make Modification for getData<br />");
        return $mergedData;
        //cms_contentType_show($showContentData,$frameWidth);






    }

    
}

function cmsType_contentName_class() {
    if ($GLOBALS[cmsTypes]["cmsType_contentName.php"] == "own") $contentNameClass = new cmsType_contentName();
    else $contentNameClass = new cmsType_contentName_base();
    return $contentNameClass;
}


function cmsType_contentName_data($contentData,$frameWidth) {
    $contentNameClass = cmsType_contentName_class();
    return $contentNameClass->contentName_data($contentData, $frameWidth);
}


function cmsType_contentName($contentData,$frameWidth) {
    $contentNameClass = cmsType_contentName_class();
    $contentNameClass->show($contentData,$frameWidth);
}



function cmsType_contentName_editContent($editContent,$frameWidth) {
    $contentNameClass = cmsType_contentName_class();
    return $contentNameClass->contentName_editContent($editContent,$frameWidth);
}




?>
