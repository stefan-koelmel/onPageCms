<?php // charset:UTF-8

class cmsType_axure_base extends cmsClass_content_show {
    function getName (){
        return "Axure Einbindung";        
    }
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
       
        $mapWidth = $frameWidth;
        $mapHeight = 500;
        // foreach($_SERVER as $key => $value) echo ("$key = $value <br>");        
        $root = $this->axure_rootFolder();
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $axureFile = $data[axureFile];
        if (!$axureFile) {
            echo ("Keine Axure-File ausgewählt <br/ >");
            return 0;
        }
        $axurePage = $data[axurePage];
        if (!$axurePage) {
            echo ("Keine Axure Startseite ausgeählt <br />");
            $axurePage = "Home.html";
            
        }
        
        $urlMap = "/".$axureFile."/".$axurePage;
        
        
        //$urlMap = "wireframe/test/Home.html";
        // echo ($root." ".$urlMap."<br>");
        if ($axurePage AND file_exists($root.$urlMap)) {

            //echo ("AxurePage:<input type='text' value='' readonly='readonly' id='axurePage' /><br /> ");
            $vars = explode("|",$data[vars]);
            for ($i=0;$i<count($vars);$i++) {
                $var = $vars[$i];
                $varList[$var] = "112";
                
                //echo ("Variable <b>$var</b>:<input type='text' value='' readonly='readonly' id='axure_$var' /><br /> ");
            }
           
            $addUrl = "";
            foreach ($data as $key => $value) {
               
                if (substr($key,0,5) != "vars_") continue;
                $key = substr($key,5);
                switch ($key) {
                    case "login" :
                        if ($_SESSION[showLevel]>0) $value = 1;
                        else $value = 0;
                        break;
                    case "userLevel" :
                        $value = $_SESSION[showLevel];
                        if (!$value) $value = 0;
                        break;
                    case "wireframe" :
                        $value = cmsWireframe_state();
                        break;
                    case "page" :
                        $value = $GLOBALS[pageData][name];
                        break;
                    case "pageName" :
                        $value = $GLOBALS[pageData][title];
                        break;
                }
                $addUrl .= "&".$key."=".$value;
                if ($varList[$key]) $varList[$key] = $value;
                // echo ("$key = $value <br>");
            }

            if ($addUrl) {
                $addUrl[0] = "#";
                $addUrl .= "&CSUM=1";
            }

            if ($_SESSION[showLevel]>=8) {
                div_start("axure_open","margin-left:-30px;text-align:center;cursor:pointer;border:1px solid #333;float:right;width:20px;height:16px;position:absolute;left:100%;");
                echo ("var");
                div_end("axure_open");
                div_start("axure_vars","display:none;overflow:hidden;border:1px solid #333;padding:5px;");
                echo (span_text_str("AxurePage:",200)."<input type='text' value='' readonly='readonly' id='axurePage' /><br /> ");
                foreach ($varList as $key => $value ) {
                    echo (span_text_str("Variable <b>$key</b>:",200)."<input type='text' value='$value' readonly='readonly' id='axure_$key' /><br /> ");
                }
                div_end("axure_vars");
            }

            // echo ("$addUrl <br>");
             
            
            if (!file_exists($root.$urlMap)) {
                echo ("<h1>File Not exit $urlMap </h1>");
            }
            $urlMap = $root.$urlMap.$addUrl;     
            
            // echo ("<div class='axure_url' style='background:#ff0;padding:10px;'>START</div>");
            echo ("<iframe class='axure' width='".$mapWidth."px' ");
            if ($mapHeight) echo ("height='".$mapHeight."px' ");
            echo ("frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='$urlMap'>");
            echo("</iframe>");
        } else {
            if ($axurePage) echo ("File not found $root $axureFile $axurePage  <br /><b>$urlMap</b><br/>");
        }       
    }
    
    function axure_rootFolder() {
       // $root = $_SERVER[DOCUMENT_ROOT];///"/".$GLOBALS[cmsName]."/";
        $root .= "axure";
        return $root;
    }
                
    
    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        $res[axure][showName] = $this->lga("content","axureTab");
        $res[axure][showTab] = "Simple";
        
        
        // MainData
        $addData = array();
        $addData["text"] = "Wireframe wählen";
        $axureFile = $data[axureFile];
        $addData["input"] = $this->edit_selectAxure($data[axureFile],"editContent[data][axureFile]");
        $addData["mode"] = "Simple";
        $res[axure][] = $addData;

        if ($axureFile) {
            $addData = array();
            $addData["text"] = "Startseite wählen";
            $axurePage = $data[axurePage];
            $addData["input"] = $this->edit_selectAxurePage($axurePage,"editContent[data][axurePage]",$axureFile);
            $addData["mode"] = "More";
            $res[axure][] = $addData;
        }
        
        if ($axurePage) {
            $vars = $this->axure_checkFiles($axureFile,$axurePage);
            $standardVars = $this->axure_standardVars();
            $varStr = "";
            foreach ($vars as $key => $value) {

                if ($varStr) $varStr .= "|";
                $varStr .= $key;

                $addData = array();
                $addData["text"] = "Variable <b>$key</b>";

                if ($standardVars[$key]) {
                    if ($data["vars_".$key]) $selected = "checked='checked'";
                    else $selected = "";
                    $addData["input"] = "<input value='1' type='checkbox' $selected name='editContent[data][vars_".$key."]' />";
                } else {
                    $addData["input"] = "<input type='text' value='".$data["vars_".$key]."' name='editContent[data][vars_".$key."]' />";
                }
                $addData["mode"] = "More";
                $res[axure][] = $addData;
                
            }

            $addData = array();
            $addData["text"] = "Variablen";
            $addData["input"] = "<input type='text' readonly='readonly' value='$varStr' name='editContent[data][vars]' />";
            $addData["mode"] = "Admin";
            $res[axure][] = $addData;


        }
        
        
        
        return $res;
    } 
    
    
    function axure_checkFiles($axureFile,$axurePage) {
        $fileList = $this->axure_getPages($axureFile);
        
        $root = $this->axure_rootFolder();
        // $vars = array();
        $standardVars = $this->axure_standardVars();
        $vars = $standardVars;
        
        
        $folder = $root."/".$axureFile."/";
        // echo ("Folder $folder <br>");
        
        foreach ($fileList as  $file => $name) {
            // echo ("CheckFile $file = $name <br>");
            
            $jsFile = $folder.$name."_files/axurerp_pagespecificscript.js";
            if (file_exists($jsFile)) {
                $change = 0;
                $js = loadText($jsFile);
                // echo ("$js");
                
                // vars off
                $offset = strpos($js,"PopulateVariables");
                if ($offset) {
                    $startPos = strpos($js,"{",$offset);
                    $endPos = strpos($js,"}",$offset);
                    
                    $varStr = substr($js,$startPos+1,$endPos-$startPos-1);
                    $varList = explode("\r\n", $varStr);
                    // echo ("<h1>Variablen</h1>");
                    for ($i=0;$i<count($varList);$i++) {
                        $varLine = $varList[$i];
                        if ($varLine) {
                            $varStart = strpos($varLine,"$");
                            if ($varStart) {
                                $varName = substr($varLine,$varStart+1);
                                $varEnd = strpos($varName,")");
                                if ($varEnd) $varName = substr ($varName,0,$varEnd);
                                if ($varName) {
                                    $vars[$varName] = $varName;
                                }
                            }
                        }
                    }
                } // end Of Variablen
                
                
                $posLink = strpos($js,"location.href");
                while ($posLink) {
                    $maxChar = 0;
                    while ($js[$posLink] != "\t" AND $maxChar < 20) {
                        // echo ("Startpos $maxChar $posLink = '".substr($js,$posLink,4)."' ascii=".ord($js[$posLink])." <br>");
                        $posLink--;
                        $maxChar++;  
                        if ($maxChar > 20) return 0;
                    }
                    // echo ("Char is ".$js[$posLink]." (".ord($js[$posLink]).") is ".$js[$posLink-1]." (".ord($js[$posLink-1]).") <br>");
                    
                    
                    
                    
                    $startPos = $posLink+1; //strpos($js,"\r\n",$posLink-20);
                    $endPos = strpos($js,"\n",$startPos+2);
                    $link = substr($js,$startPos,$endPos-$startPos);
                    if (strPos($link,"//cmsChanged")) {
                        // echo ("ALLREADY CHANGED --$link <br>");
                    } else {
                    
                    
                        $link1_start = strpos($link,'"')+1;
                        $link1_end = strpos($link,'"',$link1_start);
                        $link1 = substr($link,$link1_start,$link1_end-$link1_start);

                        if (strpos($link1,".php")) {
                            echo "<h1> PHP LINK $link1 </h1>";
                            $newLink = "";
                            // $newLink = "// ".$link." //cmsChanged \r\n";
                            $newLink .= 'top.location.href="/'.$GLOBALS[cmsName].'/'.$link1.'"  + GetQuerystring(); //cmsChanged';
//                            $newLink .= "\r\n";
//                            $newLink .= chr(9).'cmsAxureLink("'.$link1.'");'; 
                            echo ("old = '$link' <br>");
                            echo ("new = '$newLink' <br>");
                            $change = 1;
                            $js = substr($js,0,$startPos).$newLink.substr($js,$endPos);

                            echo ("NEW JS = <br>");
                            $sample = substr($js,$startPos-20,200);
                            $sample = str_replace("\n", "<br>", $sample);
                            echo (" $sample <br>");


                        }
                    }                    
                    
                    $posLink = strpos($js,"location.href",$endPos+20);
                    // echo ("start = $startPos end=$endPos pos=$posLink <br>");
               }
               
               
                if ($change) {
                    $newFile = $jsFile; // ."_new";
                    echo ("Save NewFile =$newFile ");
                    $res  = saveText($js,$newFile); 
                   
                    echo ("Result =$res <br>");
                    
                }
                
                
                //$js = saveText($t, $fn);
                // echo ("jsFile Exist<br>");
            } else {
                echo ("jsFile $jsFile not exist <br>");
            }
            
            
            
        }
        return $vars;
        
    }
    
    function axure_standardVars() {
        $vars = array();
        $vars[login] = "login";
        $vars[userLevel] = "userLevel";
        $vars[wireframe] = "wireframe";
        $vars[page] = "page";
        $vars[pageName] = "pageName";
        return $vars;
    }
    
    function edit_selectAxure($value,$dataName) {
        
        $fileList = array(); // array("test"=>"test");
        
        $folder = $this->axure_rootFolder();
        if (file_exists($folder)) {
            $handle = opendir($folder);
            while ($file = readdir ($handle)) {
                if ($file == "." OR $file == "..") continue;
                

                if(is_dir($folder."/".$file)) {      
                    // echo ("$file is Directory <br>");
                    if (is_dir($folder."/".$file."/Resources")) {
                        // echo ("Resources exist <br>");
                        $fileList[$file] = "Axure File '$file' ";
                    }
//                } else {
//                    echo ("FILE $file <br>");
                }
            }
            closedir($handle);
        }
        
        
        
        $str .= "<select value='$value' name='$dataName' onChange='submit()'>";
        $str .= "<option value='' >Kein Wireframe gewählt</option>";
        foreach ($fileList as $fnName => $name) {
            if ($value == $fnName) $select="selected='selected'";
            else $select = "";
            $str .= "<option value='$fnName' $select >$name</option>";            
        }
        $str .= "</select>";
        return $str;        
    }
    
    
    function axure_getPages($axureFile) {
        $fileList = array(); // array("test"=>"test");

        $folder = $this->axure_rootFolder();
        $folder .= "/".$axureFile;
        
        $folderList = array();
        $pageList = array();
        
        if (file_exists($folder)) {
            $handle = opendir($folder);
            while ($file = readdir ($handle)) {
                if ($file == "." OR $file == "..") continue;
                if(is_dir($folder."/".$file)) {      
                    switch ($file) {
                        case "Resources" : break;
                        default :
                            if (substr($file,strlen($file)-6) == "_files") {
                                $pageFolder = substr($file,0,strlen($file)-6);
                                // echo ("PageFolder = '$pageFolder' ");
                                $folderList[$pageFolder] = $file;
                            } else {
                                //echo (substr($file,strlen($file)-6));
                                echo ("  -> $file is Directory <br>");
                            }
                    }
                } else {
                    if (substr($file,strlen($file)-5) == ".html") {
                        if ($file[0] == "_") continue;
                        $pageName = substr($file,0,strlen($file)-5);
                        
                        $pageList[$file] = $pageName;
                        // echo ("HTML File is $file <br>");
                    } else {
                        //echo (substr($file,strlen($file)-5));
                        echo ("FILE $file is not html <br>");
                    }
                }
                
            }
            closedir($handle);
        }
        
        foreach($pageList as $key => $val) {
            if ($folderList[$val]) {
                $fileList[$key] = $val;              
            } 
        }
        return $fileList;
    }
    
    function edit_selectAxurePage($value,$dataName,$axureFile) {
        $fileList = array(); // array("test"=>"test");

        $folder = $this->axure_rootFolder();
        $folder .= "/".$axureFile;
        
        $folderList = array();
        $pageList = array();
        
        if (file_exists($folder)) {
            $handle = opendir($folder);
            while ($file = readdir ($handle)) {
                if ($file == "." OR $file == "..") continue;
                if(is_dir($folder."/".$file)) {      
                    switch ($file) {
                        case "Resources" : break;
                        default :
                            if (substr($file,strlen($file)-6) == "_files") {
                                $pageFolder = substr($file,0,strlen($file)-6);
                                // echo ("PageFolder = '$pageFolder' ");
                                $folderList[$pageFolder] = $file;
                            } else {
                                //echo (substr($file,strlen($file)-6));
                                echo ("  -> $file is Directory <br>");
                            }
                    }
                } else {
                    if (substr($file,strlen($file)-5) == ".html") {
                        if ($file[0] == "_") continue;
                        $pageName = substr($file,0,strlen($file)-5);
                        
                        $pageList[$file] = $pageName;
                        // echo ("HTML File is $file <br>");
                    } else {
                        //echo (substr($file,strlen($file)-5));
                        echo ("FILE $file is not html <br>");
                    }
                }
                
            }
            closedir($handle);
        }
        
        foreach($pageList as $key => $val) {
            if ($folderList[$val]) {
                $fileList[$key] = $val;              
            } 
        }
       
        $str .= "<select value='$value' name='$dataName' onChange='submit()'>";
        $str .= "<option value='' >Kein Seite gewählt</option>";
        foreach ($fileList as $fnName => $name) {
            if ($value == $fnName) $select="selected='selected'";
            else $select = "";
            $str .= "<option value='$fnName' $select >$name</option>";            
        }
        $str .= "</select>";
        return $str;
    }
}



function cmsType_axure_class() {
    if ($GLOBALS[cmsTypes]["cmsType_axure.php"] == "own") $axureClass = new cmsType_axure();
    else $axureClass = new cmsType_axure_base();
    return $axureClass;
}

function cmsType_axure($contentData,$frameWidth) {
    $axureClass = cmsType_axure_class();
    $res = $axureClass->show($contentData,$frameWidth);
    return $res;  
}



function cmsType_axure_editContent($editContent) {
    $axureClass = cmsType_axure_class();
    $res = $axureClass->axure_editContent($editContent, $frameWidth);
    return $res;
}
    


?>
