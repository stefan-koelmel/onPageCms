<?php // charset:UTF-8
class cmsType_vmt_base extends cmsClass_content_show {

    function getName (){
        return "vmt";
    }

    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        
        $view = $data[view];
        if (!$view) $view = "content";
        
        switch ($view) {
            case "project" : $this->vmt_project($contentData,$frameWidth); break;
            case "drill"   : $this->vmt_drill($contentData,$frameWidth); break;
            case "content" : $this->vmt_content($contentData,$frameWidth); break;
            case "dashboard" : $this->vmt_dashboard($contentData,$frameWidth); break;
            default :
                echo ("Unkown View '$view' in 'vmt_show' <br>");
        }

        
    }

    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth = $this->frameWidth;
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $view = $data[view];
        if ($_POST) {
            //show_array($_POST);
            if ($_POST[editContent][data]) {
                if ($_POST[editContent][data][view]) $view = $_POST[editContent][data][view];
            }
        }

        $res = array();
        
        $add = array();
        $add[text] = "Hallo";
        $add[input] = "<input type='text' name='editContent[data][type]' value='$data[type]'>";
        $res[] = $add;

        $add = array();
        $add[text] = "vmt-Ansicht";
        $showData = array();
        $showData[submit] = 1;
        $add[input] = $this->vmt_viewMode($view,"editContent[data][view]",$showData);
        $res[] = $add;

        return $res;
    }

    function vmt_viewMode($code,$dataName,$showData=array()) {

        $selectList = $this->vmt_viewMode_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:80px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Keine Ansicht</option>";

        foreach ($selectList as $key => $value) {
            if (is_string($value)) {
                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$value</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }

    function vmt_viewMode_getList() {
        $res = array();
        $res["project"] = "Projektwahl";
        $res["drill"] = "Bohrerwahl";
        $res["content"] = "Inhalt";
        $res["dashboard"] = "Dashboard";

        return $res;
    }

    function vmt_project($contentData,$frameWidth) {

        $page = $GLOBALS[pageInfo][page];


        $actProcect = $_SESSION[project];
        // echo ("<h3>Aktuelles Project = $actProcect </h3>");
        
        $actDrill = $_SESSION[drill];
        

        $setProject = $_GET[setProject];
        if (!is_null($setProject)) {
            echo ("SET PROJECT TO $setProject <br />");
            $setDrill = $_GET[setDrill];
            if ($setDrill) {
                echo ("Set BOHRER to $setDrill <br />");
            }
            $_SESSION[project] = $setProject;
            $_SESSION[drill] = $setDrill;
            if ($setProject AND $setDrill) $page = "index.php";
            reloadPage($page,0);
            return 0;
        }


        // show_array($GLOBALS[pageData]);
        $filter = array();
        $filter[mainCat] = 1;
        $sort = "name"; // "name_up";
        
        $projectList = cmsCategory_getList($filter,$sort,"out_");
        for ($i=0;$i<count($projectList);$i++) {
            $proj = $projectList[$i];
            $name = $proj[name];
            $subName = $proj[subName];
            $info    = $proj[info];
            $id = $proj[id];
           
            $projClass = "vmtProjectFrame";
            $buttonClass = "mainLinkButton mainSecond";
            $setId = $id;
            if ($id == $actProcect) {
                $projClass .= " vmtActProjectFrame";
                $buttonClass = "mainLinkButton";
                $setId = 0;
            }
            
            div_start($projClass);
            span_text("ProjektName;");
            echo ("<b>$name</b><br>");
            span_text("Bezeichnung:");
            echo ("$subName<br>");
            span_text("Beschreibung:");
            echo ("$info<br>");
            
            
            

            echo ("<a href='$page?setProject=$setId' class='$buttonClass'>Projekt wählen</a></br>");

            echo ("<h4>Bohrer</h4>");
            $drillList = cmsCategory_getList(array("mainCat"=>$id),"name");
           
            
            if (is_array($drillList) AND count($drillList)) {
                for ($d=0;$d<count($drillList);$d++) {
                    
                    $drillName = $drillList[$d][name];
                    $drillId   = $drillList[$d][id];
                    
                    $drillClass = "mainLinkButton mainSecond";
                    $setDrill = $drillId;
                    if ($drillId == $actDrill AND $actProcect == $id) {
                        $drillClass = "mainLinkButton";
                        $setDrill = 0;
                    }
                    
                    

                    echo ("<a href='$page?setProject=$id&setDrill=$setDrill' class='$drillClass'>$drillName</a>");

                }
                echo ("<br>");
            }
            div_end($projClass);

            // show_array($proj);
            
        }
    }

    function vmt_drill($contentData,$frameWidth) {
        $page = $GLOBALS[pageInfo][page];


        $actProject = $_SESSION[project];
        $actDrill   = $_SESSION[drill];

        if (!$actProject) {
            echo ("<h3>Kein Projekt gewählt!</h3>");
            echo ("<a href='projectChoice.php' class='mainLinkButton mainSecond'>Jetzt Projekt wählen</a>");
            
            return 0;
        }

        $projData = cmsCategory_get(array("id"=>$actProject));
       
        $name = $projData[name];
        $subName = $projData[subName];
        $info    = $projData[info];
        $id = $proj[id];
           
        echo ("<h3>Aktuelles Projekt</h3>");
        
        span_text("Projekt-Name;");
        echo ("<b>$name</b><br>");
        span_text("Bezeichnung:");
        echo ("$subName<br>");
        span_text("Beschreibung:");
        echo ("$info<br>");
        
        echo ("<h4>Wählen Sie den Bohrer aus</h4>");
            
        
        
        
        // echo ("<h3>Aktuelles Project = $actProject </h3>");
        // echo ("<h4>Aktueller Bohrer = $actDrill </h4>");


        $setDrill = $_GET[setDrill];
        if (!is_null($setDrill)) {
            echo ("SET Boher TO $setDrill <br>");
            $_SESSION[drill] = $setDrill;

            reloadPage($page,0);
            return 0;
        }

        $filter = array();
        $filter[mainCat] = $actProject;
        $sort = "name"; // "name_up";

        $projectList = cmsCategory_getList($filter,$sort,"out_");
        for ($i=0;$i<count($projectList);$i++) {
            $proj = $projectList[$i];
            $name = $proj[name];
            $id = $proj[id];
            $setDrill = $id;
            if ($id == $actDrill) {
                $class = "mainLinkButton";
                $setDrill = 0;
            } else {
                $class = "mainLinkButton mainSecond";
            }
            echo ("<a href='$page?setDrill=$setDrill' class='$class'>$name</a>");
        }
        echo ("<br>");



    }
    
    
    function vmt_content($contentData,$frameWidth) {
        $pageData = $GLOBALS[pageData];
        
        $title = $pageData[title];
        $width = $frameWidth;
        $height =floor($width / 4 * 1.5 );
        
        $class = ""; // "vmtContent";
        cmsWireframe_frameStart($width, $height, $class);
        if (is_array($title)) $title=$title["dt"];
        echo ("<h1>$title</h1>");
        
        $project = $_SESSION[project];
        if ($project) {
            $projData = cmsCategory_get(array("id"=>$project));
            if ($projData) echo ("Projekt: $projData[name] ");
            
            $drill = $_SESSION[drill];
            if ($drill) {
                $drillData = cmsCategory_get(array("id"=>$drill));
                if ($drillData) echo ("| Bohrer: $drillData[name] ");
            }
            echo ("<br />");
        }
        
        
        
        cmsWireframe_frameEnd($width, $height, $class);
 
        
        
//        // zurück zur übergeordneten Seite
//        $mainPage = intval($pageData[mainPage]);
//        if ($mainPage) {
//            $mainPageData = cms_page_getData($mainPage);
//            if (is_array($mainPageData)) {
//                $mainPageUrl = $mainPageData[name].".php";
//                $mainPageName = $mainPageData[title];
//                echo ("<a href='$mainPageUrl' class='mainLinkButton mainSecond' >Übergeodnete Seite '$mainPageName'</a><br /> ");
//            }
//        }
    }
    
    function vmt_dashboard($contentData,$frameWidth) {
        echo ("<h1>Dashboard</h1>");
        $project = $_SESSION[project];
        if ($project) {
            $projData = cmsCategory_get(array("id"=>$project));
            if ($projData) echo ("Projekt: $projData[name] ");
            
            $drill = $_SESSION[drill];
            if ($drill) {
                $drillData = cmsCategory_get(array("id"=>$drill));
                if ($drillData) echo ("| Bohrer: $drillData[name] ");
            }
            echo ("<br />");
        } else {
            echo ("<h3>Kein Projekt gewählt!</h3>");
            echo ("<a href='projectChoice.php' class='mainLinkButton mainSecond' >Projekt wählen</a><br />");
            return 0;
        }
        
        $abs = 20;
        $border = 1;
        
        $frame2Width = floor(($frameWidth-$abs)/2) - 2*$border;
        $frame2Height = floor($frame2Width / 4 * 2);
        $class = "wireframe_FrameRoll";
        // $img = cmsWireframe_image($frame2Width,$frame2Height);
        echo ("<div style='margin-bottom:".$abs."px;' >");
        
        echo ("<div style='width:".$frame2Width."px;float:left;margin-right:".$abs."px;'>");
        cmsWireframe_frameStart($frame2Width, $frame2Height, $class);
        echo ("Inhalt 1");
        cmsWireframe_frameEnd($frame2Width, $frame2Height, $class);
        echo ("</div>");
        
        echo ("<div style='width:".$frame2Width."px;float:left;'>");
        cmsWireframe_frameStart($frame2Width, $frame2Height, $class);
        echo ("Inhalt 1");
        cmsWireframe_frameEnd($frame2Width, $frame2Height, $class);
        echo ("</div>");
        
        echo ("<div style='clear:both;'></div>");
        echo ("</div>");
        
        $frame4Width = floor(($frameWidth-3*$abs)/4) - 2*$border;
        $frame4Height = floor($frame4Width / 4 * 2.5);
        
        echo ("<div style='margin-bottom:".$abs."px;' >");
        echo ("<div style='width:".$frame4Width."px;float:left;margin-right:".$abs."px;margin-bottom:0px;'>");
        cmsWireframe_frameStart($frame4Width, $frame4Height, $class);
        echo ("Inhalt 3");
        cmsWireframe_frameEnd($frame4Width, $frame4Height, $class);
        echo ("</div>");
        
        echo ("<div style='width:".$frame4Width."px;float:left;margin-right:".$abs."px;margin-bottom:0px;'>");
        cmsWireframe_frameStart($frame4Width, $frame4Height, $class);
        echo ("Inhalt 4");
        cmsWireframe_frameEnd($frame4Width, $frame4Height, $class);
        echo ("</div>");
        
        echo ("<div style='width:".$frame4Width."px;float:left;margin-right:".$abs."px;margin-bottom:0px;'>");
        cmsWireframe_frameStart($frame4Width, $frame4Height, $class);
        echo ("Inhalt 5");
        cmsWireframe_frameEnd($frame4Width, $frame4Height, $class);
        echo ("</div>");
        
        echo ("<div style='width:".$frame4Width."px;float:left;margin-bottom:0px;'>");
        cmsWireframe_frameStart($frame4Width, $frame4Height, $class);
        echo ("Inhalt 6");
        cmsWireframe_frameEnd($frame4Width, $frame4Height, $class);
        echo ("</div>");
        
        echo ("<div style='clear:both;'></div>");
        echo ("</div>");
        
        
        $frame1Width = $frameWidth - 2*$border;
        $frame1Height = $frame4Height;
        
        echo ("<div  >");
        $img = cmsWireframe_image($frame1Width,$frame1Height);
        echo ("<div style='width:".$frame1Width."px;'>");
        
        cmsWireframe_frameStart($frame1Width, $frame1Height, $class);
        echo ("Inhalt 7");
        cmsWireframe_frameEnd($frame1Width, $frame1Height, $class);
        // echo ("<img src='$img'>");
        echo ("</div>");
                
        
        echo ("<div style='clear:both;'></div>");
        echo ("</div>");
        
        
        
        
        
        
    }
}

function cmsType_vmt_class() {
    $vmtClass = new cmsType_vmt_base();
    return $vmtClass;
}

function cmsType_vmt($contentData,$frameWidth) {
    $vmtClass = cmsType_vmt_class();
    $vmtClass->vmt_show($contentData,$frameWidth);
}

?>
