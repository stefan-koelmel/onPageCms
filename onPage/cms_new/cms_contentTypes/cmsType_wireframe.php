<?php // charset:UTF-8
class cmsType_wireframe_base extends cmsType_contentData_show_base {

    function getName (){
        return "Wireframe";
    }

     function wireframe_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $viewMode = $data[viewMode];
        $wireframe = $_GET[wireframe];
        if ($wireframe) {
            $viewMode = "single";
        }
        
        switch ($viewMode) {
            case "sss" : break;
            default :
                echo ("UNKOWN VIEWMODE IN wireframe_show ".$data[viewMode]."<br>");                            
        }
    }
    




    function wireframe_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();

        $mainTab = "wireframe";
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
        $showList = $this->wireframeShow_List();
        $addList = $this->dataBox_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "wireframeShow";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        
        // Add FILTER
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

        // ACTION 
        $addList = $this->action_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "action";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        return $res;
    }
    
    
    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for wireframeListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["wireframe"] = "Tabelle";
        $res["slider"] = "Slider";
        $res["single"] = "Hersteller";
            
        return $res;
    }
    
     function dataShow_List() {
        return $this->wireframeShow_List();
    }
    
    function wireframeShow_List() {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[info] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[longInfo] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        
//        $show[vk] = array("name"=>"Verkauspreis","description"=>"Bezeichnung zeigen","position"=>1);
//        $show[shipping] = array("name"=>"Porto","description"=>"Bezeichnung zeigen","position"=>1);
//        $show[count] = array("name"=>"Anzahl","description"=>"Bezeichnung zeigen","position"=>1);
//        
//        $show[basket] = array("name"=>"Warenkorb","description"=>"Bezeichnung zeigen","position"=>1);
        $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
        return $show;
    }


}

function cmsType_wireframe_class() {
    if ($GLOBALS[cmsTypes]["cmsType_wireframe.php"] == "own") $wireframeClass = new cmsType_wireframe();
    else $wireframeClass = new cmsType_wireframe_base();
    return $wireframeClass;
}

function cmsType_wireframe($contentData,$frameWidth) {
    
    $wireframeClass = cmsType_wireframe_class();
    $wireframeClass->wireframe_show($contentData,$frameWidth);
}



function cmsType_wireframe_editContent($editContent,$frameWidth) {
    $wireframeClass = cmsType_wireframe_class();
    return $wireframeClass->wireframe_editContent($editContent,$frameWidth);
}


?>
