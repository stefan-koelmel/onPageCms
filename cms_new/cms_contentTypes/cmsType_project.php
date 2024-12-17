<?php // charset:UTF-8
class cmsType_project_base extends cmsClass_content_data_show {

    function getName (){
        return "Projekt";
    }

    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
        $this->tableName = "project";
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $viewMode = $data[viewMode];
        // show_array($GLOBALS[pageData]);
         
         
         switch ($viewMode) {
             case "table" :
                 $this->project_showTable();
                 break;
             case "list" :
                 $this->project_showList();
                 break;
             case "slider" :
                 $this->project_showSlider();
                 break;

             case "project" :
                 $this->project_showProject();
                 break;
             
             case "single" :
                 $this->project_showProject();
                 break;

             default: 
                 echo ("Unkown ShowMode in project_show '$viewMode' <br />");
                 
         }
    }
    
    
    function project_showTable() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
         
        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
        }
        
        $this->filter = array("show"=>1);
        $this->sort   = "id";
        $this->data_showFilter();
        // foreach ($this->filter as $key => $value ) echo ("Filter in SHOW TABLE  $key = $value <br>");

        
        $projectList = $this->project_getList($contentData);
        if (!count($projectList)) {
            echo ("Keine Projekte gefunden <br>");
            return 0;
        }
        
        
        // show_array($projectList[0]);
        div_start("projectList"); //,"width:".$frameWidth."px;");
       
        $this->data_showTable("project",$projectList,$contentData,$frameWidth);

        div_end("projectList","before");
    }

    function project_showSlider() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
        $projectList = $this->project_getList($contentData);
        $data = $contentData[data];
        if (!is_array($data)) $date = array();

        $imageWidth = 300;

        $ratio = 1.0 * 4 / 3;
        $randomImage = 1;


        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
        }
        
        
        $wireFrameOn = $data[wireframe];
        $wireframeState = $this->wireframeState;
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            // $wireframeNr = $project[id];
            // echo ("<h1> WirfreameNr $wireframeNr </h1>");
        }
        
        
        $contentList = array();

        for ($i=0;$i<count($projectList);$i++) {
            $project = $projectList[$i];
            $divStr = "";
          
            if ($clickAction) {
                $divStr .= "<div class='projectSliderItem sliderItem sliderItemClick'>";
                $divStr .= "<a href='$project[goPage]' class='hiddenLink' >$project[name]</a>";
                // foreach ($project as $key => $value) $divStr .= "| $key=$value |";
            }

            $dataType = "prodjectShow";
            $outData = $this->dataBox_show($dataType,$project,$contentData,$frameWidth);

            $divStr .= $outData; // $this->projectBox_show($project,$contentData,$frameWidth);
           
            if ($clickAction) $divStr .= "</div>";
            $contentList[] = $divStr;
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

    function project_showProject() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
        

        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        // show_array($data);

        if ($data[dynamicProject]) {
            $projectGet = $_GET[project];
            if ($projectGet) $projectId = $projectGet;
        }

        if (!$projectId) {
            if ($data[filterProject]) {
                $projectId = $data[filterProject];
            }
        }
        
        
        if (!$projectId) {
            if ($data[filter_id]) {
                $projectId = $data[filter_id];
            }
        }

        if (!$projectId) {
            echo ("Kein Projekt ausgewählt !<br />");
            return 0;
        }
        
        $this->filter = array("show"=>1);
        $this->sort   = "id";
        $this->data_showFilter();
        

        $project = cmsProject_getById($projectId);
        // foreach ($project as $key => $value) echo ("<b>$key</b> = $value <br>");
        if (!is_array($project)) {
            echo ("Kein Projekt Daten erhalten <br />");
            return 0;
        }

        div_start("projectShow","width:".$frameWidth."px;");

        $dataType = "prodjectShow";
        $out = $this->dataBox_show($dataType,$project,$contentData,$frameWidth);
        echo ($out);
       
        if ($this->edit AND $this->showLevels>=7) {
            echo ("<a href='admin_cmsProject.php?view=edit&id=$project[id]' class='cmsLinkButton'>Projekt editieren</a> <br />");
        }


        div_end("projectShow","before");

    }
    
    
    function projectShow_showList() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
        
        $this->filter = array("show"=>1);
        $this->sort   = "id";
        $this->data_showFilter();
        
        $projectList = $this->project_getList($contentData);
        $this->data_showList("project",$projectList);
        return 1;
        
        echo ("projectShow_showList($contentData,$frameWidth) -> Not Ready !!<br />");
        return 0;
    }
    
    function project_getList($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $filter = array();
        
        $pageData = $GLOBALS[pageData];
        
        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
            
            if ($clickPage AND $clickPage != $pageData[id]) {
                // echo ("Klickpage ist not Actual Page");
                $clickPageData = cms_page_get(array("id"=>$clickPage));
                // show_array($clickPageData);
            }
        }
        
        if ($data[dynamicProject]) {
            
            // if (!is_array($clickPageData))
            
            if (is_array($clickPageData)) $link = $clickPageData[name].".php";
            else $link = $pageData[name].".php";
            $addLink = "";
            
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            $dynamic_1 = $pageData[dynamic];
            $dynamic_2 = $dynamicData[dynamic2];
            
            if ($dynamic_1) {
                $dynamic_1_type = $dynamicData[dataSource];
                $dynamic_1_value = $_GET[$dynamic_1_type];
                if ($dynamic_1_value) {
                    if ($addLink) $addLink .= "&";
                    $addLink .= $dynamic_1_type."=".$dynamic_1_value;
                    switch ($dynamic_1_type) {
                        case "category" :
                            $filter[category] = $dynamic_1_value;
                            break;
                        case "project" : 
                            break;
                        default:
                            echo ("unkown dynamicTyp '$dynamic_1_type' = $dynamic_1_value <br> ");
                    }
                } else {
                    
                    
                }
            }
            
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                if ($dynamic_2_value) {
                    if ($addLink) $addLink .= "&";
                    $addLink .= $dynamic_2_type."=".$dynamic_2_value;
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

        
        $filter = array();
        foreach ($this->filter as $key => $value) {
            $filter[$key] = "$value";
            // echo ("FILTER in getProjectList $key => $value <br>");
        }

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
            $projectList = $newList;
        }
        if ($data[dynamicProject]) {
            
//            echo ("<h1> $link -> $addLink </h1>");
//            echo ("dyn1 $dynamic_1_type $dynamic_1_value <br>");
//            echo ("dyn2 $dynamic_2_type $dynamic_2_value <br>");
            for ($i=0;$i<count($projectList);$i++) {
                $id = $projectList[$i][id];
                if ($dynamic_2_type == "project" AND !$addLink) {
                    // echo ("NO $dynamic_1_value from Type $dynamic_1_type <br>");
                    $dyn_value = "";
                    switch ($dynamic_1_type) {
                        case "category" :
                            $dyn_value = $projectList[$i][category];
                            // echo ("$dyn_value<br>");
                            break;
                        default :
                            echo ("unkown $dynamic_1_value in addLink to ProjectList<br>");
                    
                        
                    }
                    if ($dyn_value) {
                        $dyn_list = explode("|",$dyn_value);
                        if (count($dyn_list)>1) {
                            $dyn_value = $dyn_list[1];
                        }
                        $addLink = $dynamic_1_type."=".$dyn_value;
                    }
                    
                    
                    
                }
                
                if ($addLink) $addProject = $addLink."&";
                else $addProject = "";
                $addProject .= "project=".$id;
                
                // echo ("Go To $link ? $addProject <br>");
                $projectList[$i][goPage] = $link."?".$addProject;
                
            }
        }
        
        
        return $projectList;
    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for projectListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["slider"] = "Projekt Slider";
        $res["single"] = "Projekt";

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



    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        $this->tableName = "project";
        $res = array();
        $mainTab = "project";
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            $res[$mainTab][showName] = $this->lga("content",$this->tableName."Tab");
            $res[$mainTab][showTab] = "Simple";
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$mainTab][] = $viewModeList[$i];
            }
        }

        $showList = $this->projectShow_List();
        $addList = $this->dataBox_editContent($data,$showList);
        $addToTab = "projectShow";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        $res[$addToTab][showName] = $this->lga("content",$this->tableName."ViewTab");
        $res[$addToTab][showTab] = "Simple";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        // Image Settings
        $dontShow = array();
        $addList = $this->cmsImage_editSettings($editContent,$frameWidth,$dontShow);
        $addToTab = "image";
        $res[$addToTab][showName] = $this->lga("content",$this->tableName."ImageTab");
        $res[$addToTab][showTab] = "Simple";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        // Add FILTER
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            $res[$addToTab][showName] = $this->lga("content",$this->tableName."FilterTab");
            $res[$addToTab][showTab] = "Simple";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

        // Mouse ACTION
        $mouseAction = $editContent[data][mouseAction];
        if ($_POST[editContent][data][mouseAction]) $mouseAction = $_POST[editContent][data][mouseAction];
        else if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        
        $addToTab = "action";
        $res[$addToTab][showName] = $this->lga("content",$this->tableName."ActionTab");
        $res[$addToTab][showTab] = "Simple";
        
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
        
    

    function dataShow_List($contentData) {
        return $this->projectShow_List();        
    }

    function projectShow_List() {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[subName] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
       
        $show[info] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[longInfo] = array("name"=>"langer Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[year] = array("name"=>"Jahr","description"=>"Bezeichnung zeigen","position"=>1);
        $show[customer] = array("name"=>"Kunde","description"=>"Bezeichnung zeigen","position"=>1);
        $show[dealer] = array("name"=>"Auftraggeber","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
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
        if (function_exists("cmsCompany_getList")) {
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

function cmsType_project_class() {
    if ($GLOBALS[cmsTypes]["cmsType_project.php"] == "own") $projectClass = new cmsType_project();
    else $projectClass = new cmsType_project_base();
    return $projectClass;
}

function cmsType_project($contentData,$frameWidth) {
    $projectClass = cmsType_project_class();
    return $projectClass->show($contentData,$frameWidth);
}



function cmsType_project_editContent($editContent,$frameWidth) {
    $projectClass = cmsType_project_class();
    return $projectClass->editContent($editContent,$frameWidth);
}



?>
