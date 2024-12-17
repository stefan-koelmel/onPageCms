<?php // charset:UTF-8
class cmsType_projectShow_base extends cmsType_contentTypes_base {

    function getName(){
        return "Projekt zeigen";
    }
    
    function projectShow_show($contentData,$frameWidth) {
        div_start("projectShow","width:".$frameWidth."px;");


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
            echo ("Kein Projekt ausgewählt !<br />");
            return 0;
        }
        
        $project = cmsProject_getById($projectId);
        if (!is_array($project)) {
            echo ("Kein Projekt Daten erhalten <br />");
            return 0;
        }

        
        
        $showList = $this->projectShow_List();
        foreach ($showList as $key => $value) {
            
            $show = $data[$key];
            if ($show) {
                $content=$project[$key];
            
                switch ($key) {
                    case "url" :
                        if ($content) {
                            echo ("Webseite: ");
                            $urlData = php_link_get($content);
                            if (is_array($urlData)) {
                                for ($i=0;$i<count($urlData);$i++) {
                                    $value = $urlData[$i];
                                    echo ("<a href='$value[url]' target='$value[target]' class='cmsUrlLink' >$value[name]</a><br />");
                                }
                            }
                        }                        
                        break;
                    
                    case "name" :
                        echo ("<h3>$content</h3>");
                        break;
                    
                    case "info" :
                        echo ("<h4>$content</h4>");
                        break;
                    
                    case "longInfo" : 
                        echo ("$content<br />");
                        break;
                    
                    case "year" :
                        $yearList = explode(",",$content);
                        for ($i=0;$i<count($yearList);$i++) {
                            echo ($yearList[$i]." ");
                        }
                        echo ("<br />");
                        break;
                        
                    case "image" :
                        if (intval($content)) $content = "|".$content."|";
                        $imageList = explode("|",$content);
                        for ($i=1;$i<count($imageList)-1;$i++) {
                            $imageId = $imageList[$i];
                            // echo ("Bild $i imageId=$imageId <br>");
                            $imageData = cmsImage_getData_by_Id($imageId);
                            if (is_array($imageData)) {
                                $imageStr = cmsImage_showImage($imageData, 100);
                                echo ("$imageStr ");
                            }
                            
                        }
                        echo ("<br />");
                        break;
                        
                    case "customer" :
                        if ($content) {
                            echo ("Kunde: $content<br />");
                        }
                        break;
                        
                    case "dealer" :
                        if ($content) {
                            echo ("Auftraggeber: $content<br />");
                        }
                        break;
                    
                    case "category" :
                        echo ("Kategorie: ");
                        $out = "";
                        $catList = explode("|",$content);
                        for ($i=1;$i<count($catList)-1;$i++) {
                            $categoryId = $catList[$i];
                            $catData = cmsCategory_getById($categoryId);
                            if (is_array($catData)) {
                                $catName = $catData[name];
                                if ($out) $out .= " | ";
                                $out .= $catName;
                            }                            
                        }
                        echo ($out);                        
                        echo ("<br />");
                        break;
                        
                    
                    default :
                        echo ("<h1>Zeige $key = $value </h1>");
                        // echo ("Inhalte = '$content' <br>");
                
                        
                }
                
                
            }
        }
        

        
//        $name = $project[name];
//        $info = $project[info];
//        $company = $project[company];
//        $show = $project[show];
//        $new = $project["new"];
//        $highlight = $project[highlight];
//        $imageId = intval($project[image]);
//
//
//        $imageData = cmsImage_getData_by_Id($imageId);
//
//        $showData = array();
//        $showData[frameWidth] = $frameWidth*1;
//        $showData[frameHeight] = $frameWidth*1;
//        $showData[vAlign] = "bottom";
//        $showData[hAlign] = "left";
//        $showData[title] = $project[name];
//        $showData[alt] = $project[name];
//        $showData[name] = $project[name];
//
//        $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
//        echo ($imgStr);
//        echo ("<h1>$name</h1>");
//        echo ($info);
//        //echo ("Projekt anzahl $anz $projectId <br />");
//
//        
        if ($_SESSION[edit] AND $_SESSION[showLevel]>=7) {                            
            echo ("<a href='admin_projects.php?view=edit&id=$project[id]' class='cmsLinkButton'>Projekt editieren</a> <br />");
        }
        

        div_end("projectShow","before");
    }

    function projectShow_editContent($editContent,$frameWidth) {
        $res = array();
        $res[projectShow] = array();
        $res[projectFilter] = array();
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        $show = $this->projectShow_List();
        
        foreach ($show as $key => $value) {
            $addData = array();
            $addData["text"] = $value;
            $checkData = $data[$key];
            if ($checkData) $checked = "checked='checked'";
            else $checked = "";
            $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][$key]' />";
            $res[projectShow][] = $addData;
        }
	
        
        $pageData = $GLOBALS[pageData];
        $dynamic = $pageData[dynamic];
        if ($dynamic) {
            $addData = array();
            $addData["text"] = "Dynamisches Projekt";
            $dynamicProject = $data[dynamicProject];
            if ($dynamicProject) $checked = "checked='checked'";
            else $checked = "";
            $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicProject]' />";
            $res[projectFilter][] = $addData;
        }
        
        
        // FILTER PRODUKT
        $filterProject = $editContent[data][filterProject];
        if ($_POST[editContent][data][filterProject]) $filterProject = $_POST[editContent][data][filterProject];
        else if ($_POST[editContent][data]) $filterProject = $_POST[editContent][data][filterProject];
        $addData = array();
        $addData["text"] = "Projekte Filtern";
        $addData["input"] = $this->filter_select("project",$filterProject,"editContent[data][filterProject]",array("submit"=>1));
        $res[projectFilter][] = $addData;
        
        // FILTER Hersteller
        $filterCompany = $editContent[data][filterCompany];
        if ($_POST[editContent][data][filterCompany]) $filterCompany = $_POST[editContent][data][filterCompany];
        else if ($_POST[editContent][data]) $filterCompany = $_POST[editContent][data][filterCompany];
        $addData = array();
        $addData["text"] = "nach Herstellern";
        $addData["input"] = $this->filter_select("company",$filterCompany,"editContent[data][filterCompany]",array("submit"=>1));
        $res[projectFilter][] = $addData;

        // FILTER Category
        $filterCategory = $editContent[data][filterCategory];
        if ($_POST[editContent][data][filterCategory]) $filterCategory = $_POST[editContent][data][filterCategory];
        else if ($_POST[editContent][data]) $filterCategory = $_POST[editContent][data][filterCategory];
        $addData = array();
        $addData["text"] = "nach Kategorie";
        $addData["input"] = $this->filter_select("category",$filterCategory,"editContent[data][filterCategory]",array("submit"=>1));
        $res[projectFilter][] = $addData;



       /* $addData["text"] = "Anzahl Hersteller in Reihe";
        $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$editContent[data][imgRow]."'>";
        $addData["input"] = $input;
        $res[projectShow][] = $addData;

        $addData["text"] = "Abstand Hersteller in Reihe";
        $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$editContent[data][imgRowAbs]."'>";
        $addData["input"] = $input;
        $res[projectShow][] = $addData;

        $addData["text"] = "Abstand Zeilen";
        $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$editContent[data][imgColAbs]."'>";
        $addData["input"] = $input;
        $res[projectShow][] = $addData; */
        
        return $res;
    }

    
    function projectShow_List() {
        $show = array();
    
        $show["name"] = "Überschrift";
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

    // function filter_select($filterType,$code,$dataName,$showData) {

    // function filter_select_getList($filterType,$filter,$sort) {

    // function project_filter_select_getList($filter,$sort) {
      
    function project_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }


    // function company_filter_select_getList($filter,$sort) {

    function company_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

    // function category_filter_select_getList($filter,$sort) {

    function category_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

}

function cmsType_projectShow_class() {
    if ($GLOBALS[cmsTypes]["cmsType_projectShow.php"] == "own") $projectShowClass = new cmsType_projectShow();
    else $projectShowClass = new cmsType_projectShow_base();
    return $projectShowClass;
}


function cmsType_projectShow($contentData,$frameWidth) {
    $projectShowClass = cmsType_projectShow_class();
    $projectShowClass->projectShow_show($contentData,$frameWidth);
}



function cmsType_projectShow_editContent($editContent,$frameWidth) {
    $projectShowClass = cmsType_projectShow_class();
    return $projectShowClass->projectShow_editContent($editContent,$frameWidth);
}


?>
