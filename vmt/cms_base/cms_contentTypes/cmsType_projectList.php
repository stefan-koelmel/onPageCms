<?php // charset:UTF-8
class cmsType_projectList_base extends cmsType_contentTypes_base {

    function getName (){
        return "Projekt List";
    }

    function projectList_show($contentData,$frameWidth) {
         $data = $contentData[data];
         if (!is_array($data)) $data = array();
         
         $viewMode = $data[viewMode];
         
         
         switch ($viewMode) {
             case "table" :
                 $this->projectList_showTable($contentData,$frameWidth);
                 break;
             case "list" :
                 $this->projectShow_showList($contentData,$frameWidth);
                 break;

             case "slider" :
                 $this->projectShow_showSlider($contentData,$frameWidth);
                 break;
             default: 
                 echo ("Unkown ShowMode in projectList_show '$viewMode' <br />");
                 
         }
    }
    
    
    function projectList_showTable($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
         
        
        
        $projectList = $this->project_getList($contentData);
        if (!count($projectList)) {
            echo ("Keine Projekte gefunden <br>");
            return 0;
        }
        
        div_start("projectList","width:".$frameWidth."px;");

        $border = 1;
        $padding = 5;
        
        $imgRow = intval($data[imgRow]);
        $imgRowAbs = intval($data[imgRowAbs]);
        $imgColAbs = intval($data[imgColAbs]);
        $imgColHeight = intval($data[imgColHeight]);
        
        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;
        $rowWidth = floor(($frameWidth - (($imgRow-1)*$imgRowAbs )-($imgRow*2*$border) - ($imgRow*2*$padding)) / $imgRow);

        
        $this->showList_customFilter($contentData,$frameWidth);


        $nr = 0;
        for ($i = 0; $i<count($projectList); $i++) {
            $project = $projectList[$i];

            $nr++;
            if ($nr == 1) {
                div_start("projectListLine","margin-bottom:".$imgColAbs."px");
                $zeile ++;
            }


            $style = "";
            $boxData = array();
            $style .= "width:".$rowWidth."px;";
            if ($nr<$imgRow) $style.= "margin-right:".$imgRowAbs."px;";
            $style .= "float:left;";
            $style .= "border-width:".$border."px;";
            if ($imgColHeight) $style .= "height:".$imgColHeight."px;overflow:hidden;";
            if ($padding) $style .= "padding:".$padding."px;";
            $boxData[style] = $style;

            div_start("projectListItem tableItem",$boxData);

            $out = $this->projectBox_show($project,$contentData,$rowWidth);
            echo ($out);

            div_end("projectListItem tableItem");

            if ($nr == $imgRow) { // close Line
                $nr = 0;
                div_end("projectListLine","before");
            }                
        } // end of List

        if ($nr != 0) {
            div_end("projectListLine","before");
        }

        div_end("projectList","before");                
    }

    function projectShow_showSlider($contentData,$frameWidth) {
        $projectList = $this->project_getList($contentData);
        $data = $contentData[data];
        if (!is_array($data)) $date = array();

        $imageWidth = 300;

        $ratio = 1.0 * 4 / 3;
        $randomImage = 1;


        $contentList = array();

        for ($i=0;$i<count($projectList);$i++) {
            $project = $projectList[$i];
            $out = "";
            $name = $project[name];
            $image = $project[image];
            if ($image) {
                if (intval($image)) $image = "|$image|";
                $imageList = explode("|",$image);
                if ($randomImage) {
                    $rand = rand(1,count($imageList)-2);
                    $imageId = $imageList[$rand];
                } else {
                    $imageId = $imageList[1];
                }
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    $showData = array();
                    $showData[frameWidth] = $imageWidth;
                    $showData[frameHeight] = $imageWidth / $ratio;
                    $showData[ratio] = $ratio;
                    $showData[vAlign] = "top";
                    $showData[hAlign] = "left";
                    // show_array($showData);
                    $imgStr = cmsImage_showImage($imageData, $imageWidth, $showData);
                    $out .= $imgStr."<br />";
                    //echo ("$imgStr<br>");
                }

                $out .= "<b>$name</b>";
                $divStr = $this->projectBox_show($project,$contentData,$frameWidth);
//                 echo ($divStr);
                // echo ("SliderContent $name , $image , $imageId <br>");
                $contentList[] = $divStr;

            } else {
                //  echo ("No Bild for $name<br>");
            }
        }

        $type = null;
        $name = "projectSlider";
        $showData = array();
        $width = $frameWidth;
        // $height =  $imageWidth / $ratio;



        $direction = $data[direction];
        if (!$direction) $direction = "horizontal";
        $loop      = $data[loop];
        if (!$loop) $loop = 0;
        $pause = $data[pause];
        if (!$pause) $pause = 5000;
        $speed = $data[speed];
        if (!$speed) $speed = 500;
        $navigate = $data[navigate];
        $pager     = $data[pager];

        $showData[loop] = $loop;
        $directionList = array("vertical","horizontal","fade");
        $showData[direction] = $direction; // $directionList[0];

        $showData[speed] = $speed;

        $showData[pause] = $pause;
        $showData[navigate] = $navigate;
        $showData[page] = $pager;
        // show_array($showData);


        cmsSlider($type,$name,$contentList,$showData,$width,$height);


    }
    
    
    function projectBox_show($project,$contentData,$boxWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $showList = $this->projectShow_List($contentData);
        
        $targetList = array();
        $targetList[top] = "";
        $targetList[left] = "";
        $targetList[right] = "";
        $targetList[bottom] = "";
        
        $LR_left = $data[LR_left];
        $LR_abs  = $data[LR_abs];
        $LR_right = $data[LR_right];
        
        
         if ($LR_abs) {
            if (strpos($LR_abs,"%")) { 
                $proz = intval(substr($LR_abs,0,strpos($LR_abs,"%")));
                $LR_abs = floor($boxWidth * $proz / 100);
                //  echo ("Prozent $proz $LR_abs <br>");
            }
            if (strpos($LR_abs,"px")) { 
                $LR_abs = intval(substr($LR_abs,0,strpos($LR_abs,"px")));
                // echo ("Pixel $proz $LR_abs <br>");
            }   
            // echo ("LR_abs = $LR_abs <br>");
        } else {
            $LR_abs = 10;
        }
        
        
        if ($LR_left OR $LR_right) {
            
           
            
            $LR_width = $boxWidth - $LR_abs;
            
            if ($LR_left) {
                if (strpos($LR_left,"%")) { 
                    $proz = intval(substr($LR_left,0,strpos($LR_left,"%")));
                    $LR_left = floor($LR_width * $proz / 100);
                    // echo ("Prozent $proz $LR_left <br>");
                }
                if (strpos($LR_left,"px")) { 
                    $LR_left = intval(substr($LR_left,0,strpos($LR_left,"px")));
                    // echo ("Pixel $proz $LR_left <br>");
                }                                
            }
            
            if ($LR_right) {
                if (strpos($LR_right,"%")) { 
                    $proz = intval(substr($LR_right,0,strpos($LR_right,"%")));
                    $LR_right = floor($LR_width * $proz / 100);
                    // echo ("Prozent $proz $LR_right <br>");
                }
                if (strpos($LR_right,"px")) { 
                    $LR_right = intval(substr($LR_right,0,strpos($LR_right,"px")));
                    // echo ("Pixel $proz $LR_right <br>");
                }                                
            }
            // echo ("Links = $LR_left Rechts = $LR_right / Abstand = $LR_abs <br> ");
         
            
            if (intval($LR_left)) {
                $leftWidth = $LR_left;
                if (!$LR_right) {
                    $rightWidth = $boxWidth - $LR_abs - $LR_left;
                }
            }
            
            if (intval($LR_right)) {
                $rightWidth = $LR_right;
                if (!$LR_left) {
                    $leftWidth = $boxWidth - $LR_abs - $LR_right;
                }
            }
            
        } else {
            $leftWidth = ($boxWidth - $LR_abs) / 2;
            $rightWidth = ($boxWidth - $LR_abs) / 2;
        }
        
        foreach ($showList as $key => $value) {
            $show = $data[$key."_show"];
            if ($show) {
                $content = $project[$key];
                $target = $data[$key."_position"];
                
                $targetWidth = $boxWidth;
                switch ($target) {
                    case "left" : $targetWidth = $leftWidth; break;
                    case "right" : $targetWidth = $rightWidth; break;
                }
                
                
                
                $out = "";
                switch ($key) {
                    case "image" : $out = $this->projectBox_show_image($content,$targetWidth); break;
                    case "name" : $out = $this->projectBox_show_name($content,$targetWidth); break;
                    case "info" : $out = $this->projectBox_show_info($content,$targetWidth); break;
                    default :
                        $out = "#$key= $content<br />";
                }
                if ($out) {
                    // echo ("add $key to $target <br>");
                    if (is_string($targetList[$target])) {
                        if ($targetList[$target]) $targetList[$target].= "";
                        $targetList[$target] .= $out;                    
                    } else {
                        if ($targetList[notSet]) $targetList[notSet].= "";
                        $targetList[notSet] .= $out;   
                    }
                }
            }
        }
        

        $outPut = "";

        // Top
        if ($targetList[top]) {
            $outPut .= div_start_str("projectItemTop tableItemTop");
            $outPut .= $targetList[top];
            $outPut .= div_end_str("projectItemTop tableItemTop");
        }
        
        // LEFT / RIGHT
        if ($targetList[left] OR $targetList[right]) {
            $outPut .= div_start_str("projectItemLR tableItemLR");
            
            if ($targetList[left]) {
                $outPut .= div_start_str("projectItemLeft tableItemLeft","width:".$leftWidth."px;margin-right:".$LR_abs."px;");
                $outPut .= $targetList[left];
                $outPut .= div_end_str("projectItemLeft tableItemLeft");
            }
            
             if ($targetList[right]) {
                $outPut .= div_start_str("projectItemRight tableItemRight","width:".$rightWidth."px;");
                $outPut .= $targetList[right];
                $outPut .= div_end_str("projectItemRight tableItemRight");
            }           
            $outPut .= div_end_str("projectItemLR tableItemLR","before");
        }
        
        // BOTTOM
        if ($targetList[bottom]) {
            $outPut .= div_start_str("projectItemBottom tableItemBottom");
            $outPut .= $targetList[bottom];
            $outPut .= div_end_str("projectItemBottom tableItemBottom");
        }
        
       
        
        return $outPut;
        
    }
    
    function projectBox_show_name($content){
        $out = "";
        $out .= div_start_str("projectItemHead tableItemHead");
        $out .= "$content";
        $out .= div_end_str("projectItemHead tableItemHead");
        return $out;
    }
    
    function projectBox_show_info($content) {
        $out = "";
        $out .= div_start_str("projectItemSubHead tableItemSubHead");
        $out .= "$content";
        $out .= div_end_str("projectItemSubHead tableItemSubHead");       
        return $out;
    }
    
    function projectBox_show_image($content,$boxWidth) {
        if (intval($content)) $content = "|$content|";
        $imageList = explode("|",$content);
        
        $selectImage = "random";
        switch ($selectImage) {
            case "first" :  $imageId = $imageList[1]; break;
            case "random" : 
                $count = count($imageList);
                $random = rand(1, $count-2);
                
                $imageId = $imageList[$random];
                // echo ("count $count $random $imageId <br>");
                break;
                
        }
        if ($imageId) {
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $showData = array();
                $showData[frameWidth] = $boxWidth;
                $showData[frameHeight] = $boxWidth / 4 * 3;
                $showData[ratio] = 4 / 3;
                $showData[vAlign] = "top";
                $showData[hAlign] = "left";
                // show_array($showData);
                $imgStr = cmsImage_showImage($imageData, $boxWidth,$showData);
                
                return $imgStr;
            }
            
        }
            
        
        $out = "Bild $content";
        return $out;
    }
    
    
    function projectShow_showList($contentData,$frameWidth) {
        echo ("projectShow_showList($contentData,$frameWidth) -> Not Ready !!<br />");
        return 0;
    }
    
    function project_getList($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $filter = array();
        
        if ($data[dynamicProject]) {
            $pageData = $GLOBALS[pageData];
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            $dynamic_1 = $pageData[dynamic];
            $dynamic_2 = $dynamicData[dynamic2];
            
            if ($dynamic_1) {
                $dynamic_1_type = $dynamicData[dataSource];
                $dynamic_1_value = $_GET[$dynamic_1_type];
                if ($dynamic_1_value) {
                    switch ($dynamic_1_type) {
                        case "category" :
                            $filter[category] = $dynamic_1_value;
                            break;
                        case "project" : 
                            break;
                        default:
                            echo ("unkown dynamicTyp '$dynamic_1_type' = $dynamic_1_value <br> ");
                    }
                }
            }
            
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                if ($dynamic_2_value) {
                    switch ($dynamic_2_type) {
                        case "category" :
                            $filter[category] = $dynamic_2_value;
                            break;
                        case "project" : 
                            break;
                        default:
                            echo ("unkown dynamicTyp '$dynamic_2_type' = $dynamic_2_value <br> ");
                    }
                }
            }
            
            
            
        }
        
        
        $maxCount = intval($data[maxCount]);
        
       
    
           // FILTER Hersteller
        $filterCompany = $data[filterCompany];
        if ($filterCompany) {
            if ($debug) echo ("Filter Company $filterCompany <br />");
            $filter["company"] = $filterCompany;
        }
        // FILTER Category
        $filterCategory = $data[filterCategory];
        if ($filterCategory) {
            if ($debug) echo ("Filter Category $filterCategory <br />");
            $filter["category"] = filterCategory;
        }
        $sort = "name";


        $projectList = cmsProject_getList($filter,$sort,"out_");


        if (!count($projectList)) {
            return array();
        }
           // echo ("$maxCount anzahl=".count($projectList)."<br>");
        if ($maxCount>0 AND count($projectList)>$maxCount) {
            // echo ("<h1>Anzahl Projekte $maxCount </h1>");
            $newList = array();
            $idList = array();

            while (count($newList) < $maxCount) {
                $randomNr = rand(0, count($projectList)-1);
                $randomId = $projectList[$randomNr][id];
                if (!$idList["$randomNr"]) {
                    $idList["$randomNr"] = 1;
                    $newList[] = $projectList[$randomNr];
                    // echo ("RandomNr=$randomNr RandomId=$randomId<br />");
                } else {
                    // echo ("Allready in List $randomNr<br />");
                }
            }
           //  echo ("NewList count =".count($newList)."<br />");
            // echo ("Random IdList<br />");
            $projectList = $newList;
        }
        return $projectList;
    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for projectListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["slider"] = "Projekt Slider";
        return $res;
    }

    
    function emptyListSelect() {
        return array();        
    }
    


    function editContent_filter_getList_own() {
        $filterList = array();
        $filterList[produkt] = 0;
        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Kategorie wählen");
        $filterList[category]["filter"] = array("mainCat"=>0,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[company]   = array();
        $filterList[company]["name"] = "Hersteller";
        $filterList[company]["type"] = "company";
        $filterList[company]["showData"] = array("submit"=>1,"empty"=>"Hersteller wählen");
        $filterList[company]["filter"] = array("show"=>1);
        $filterList[company]["sort"] = "name";
        $filterList[company]["dataName"] = "company";
        $filterList[company][customFilter] = 1;

        return $filterList;
    }








    function projectList_editContent($editContent,$frameWidth) {
        $res = array();
        $mainTab = "projectList";
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }
        
        // ShowList
        $showList = $this->projectShow_List();
        $LR = 0;
        foreach ($showList as $key => $value) {
            $addData = array();
            $name = $value;
            if (is_array($value)) {
                $name = $value[name];
            }
            
            $addData["text"] = $name;
            $checkData = $data[$key."_show"];
            if ($checkData) $checked = "checked='checked'";
            else $checked = "";
            $input = "<input type='checkbox' value='1' $checked name='editContent[data][".$key."_show]' />";
            if ($checked) {
                $target = $data[$key."_position"];
                if ($target=="left") $LR = 1;
                if ($target=="right") $LR = 1;
                $targetData = array("empty"=>"Position wählen");
                $input .= "Position: ".$this->selectTarget($target,"editContent[data][".$key."_position]",$targetData);
                // $input = "<input type='checkbox' value='1' $checked name='editContent[data][$key]' />";
            }
            
            $addData["input"] = $input;
            $res[projectShow][] = $addData;
        }
        if ($LR) {
            $addData["text"] = "Rechts / Links";
            $input = "";
            $input .= "Breite Links: <input type='text' style='width:40px;' value='$data[LR_left]' name='editContent[data][LR_left]' />";
            $input .= "Abstand: <input type='text' style='width:40px;'value='$data[LR_abs]' name='editContent[data][LR_abs]' />";
            $input .= "Breite Rechts: <input type='text' style='width:40px;'value='$data[LR_right]' name='editContent[data][LR_right]' />";
            $addData["input"] = $input;
            $res[projectShow][] = $addData;
        }
        
        
        // Add ViewMode
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

        // Mouse ACTION
        $mouseAction = $editContent[data][mouseAction];
        if ($_POST[editContent][data][mouseAction]) $mouseAction = $_POST[editContent][data][mouseAction];
        else if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Maus über";
        $input  = $this->mouseAction_select($mouseAction,"editContent[data][mouseAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];
        else if ($_POST[editContent][data]) $clickAction = $_POST[editContent][data][clickAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;


        if ($clickAction) {
            if ($clickAction == "showProject" OR $clickAction == "showCategory") {

                $clickTarget = $editContent[data][clickTarget];
                if ($_POST[editContent][data][clickTarget]) $clickTarget = $_POST[editContent][data][clickTarget];
                else if ($_POST[editContent][data]) $clickTarget = $_POST[editContent][data][clickTarget];
                $addData = array();
                $addData["text"] = "Zeigen in";
                $addData["input"] = $this->target_select($clickTarget,"editContent[data][clickTarget]",array("submit"=>1));
                $res[action][] = $addData;


                switch ($clickTarget) {
                    case "page" :

                        $clickPage = $editContent[data][clickTarget];
                        if ($_POST[editContent][data][clickPage]) $clickPage = $_POST[editContent][data][clickPage];
                        else if ($_POST[editContent][data]) $clickPage = $_POST[editContent][data][clickPage];

                        $addData = array();
                        $addData["text"] = "Seite auswählen";
                        $addData["input"] = $this->page_select($clickPage,"editContent[data][clickPage]",array("submit"=>1));
                        $res[action][] = $addData;

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

                        $res[action][] = $addData;
                        break;
                }
            }
        }


        return $res;
    }
        
    
    function selectTarget($code,$dataName,$showData=array()) {
        $selectList = array("top"=>"oben","left"=>"Links","right"=>"Rechts","bottom"=>"unten");


        $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Kein Filter";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        foreach ($selectList as $key => $value) {
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
    
     function projectShow_List() {
        $show = array();
    
        $show[name] = "Überschrift";
        $show[info] = "2. Überschrift";
        $show[longInfo] = "Text";
        
        $show[category] = "Kategorie";
        $show[year] = "Jahr";
        $show[customer] = "Kunde";
        $show[dealer] = "Auftraggeber";
        $show[image] = "Bilder";
        $show[url] = "Webseite";
        return $show;
    }


    function filter_select_getOwnList($filterType,$filter,$sort) {}

    function project_filter_select_getList($filter,$sort) {
        $res = array();
        $res["all"] = "Alle Projekte";
        $res["new"] = "Neue Projekte";
        $res["highlight"] = "Highlight Projekte";

        $ownList = $this->project_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function project_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }


    function company_filter_select_getList($filter,$sort) {
        $res = array();
        $companyList = cmsCompany_getList($filter, $sort);
        for ($i=0;$i<count($companyList);$i++) {
            $id = $companyList[$i][id];
            $name = $companyList[$i][name];
            $res[$id] = $name;
        }

       
        $ownList = $this->company_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function company_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

    function category_filter_select_getList($filter,$sort) {
        $res = array();
        $res = array();
        $categoryList = cmsCategory_getList($filter, $sort);
        for ($i=0;$i<count($categoryList);$i++) {
            $id = $categoryList[$i][id];
            $name = $categoryList[$i][name];
            $res[$id] = $name;
        }

        $ownList = $this->category_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function category_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

    //  function clickAction_select($code,$dataName,$showData) {

    function clickAction_getList() {
        $res = array();
        $res["showProject"] = "Projekte zeigen";
        
        $ownList = $this->clickAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function clickAction_getOwnList() {
        $res = array();
        return $res;
    }


    // function mouseAction_select($code,$dataName,$showData) {

    function mouseAction_getList() {
        $res = array();

        $res["showProject"] = "ProjektInfo zeigen";
        
        $ownList = $this->mouseAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function mouseAction_getOwnList() {
        $res = array();
        return $res;
    }




}

function cmsType_projectList_class() {
    if ($GLOBALS[cmsTypes]["cmsType_projectList.php"] == "own") $projectListClass = new cmsType_projectList();
    else $projectListClass = new cmsType_projectList_base();
    return $projectListClass;
}

function cmsType_projectList($contentData,$frameWidth) {
    $projectListClass = cmsType_projectList_class();
    $projectListClass->projectList_show($contentData,$frameWidth);
}



function cmsType_projectList_editContent($editContent,$frameWidth) {
    $projectListClass = cmsType_projectList_class();
    return $projectListClass->projectList_editContent($editContent,$frameWidth);
}



?>
