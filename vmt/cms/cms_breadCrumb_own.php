<?php




class cms_breadCrumb_own extends cms_breadCrumb_base {

    function breadCrumb_getList($pageData) {
        $activePage = $pageData[name];
        $pageInfo = cms_page_getInfoBack($activePage);


        // show_array($pageInfo);

        $breadCrumbList = $pageInfo[breadCrumbList ];

        if (!$_SESSION[userLevel]) return $breadCrumbList;
        
        $actProject = $_SESSION[project];
        if ($actProject) {
            $catData = cmsCategory_get(array("id"=>$actProject));
            $projName = $catData[name];            
        }

        $actDrill = $_SESSION[drill];
        if ($actDrill) {
            $catData = cmsCategory_get(array("id"=>$actDrill));
            $drillName = $catData[name];
        }

        if (!$actProject) return $breadCrumbList;

        $newBreadCrumb = array();
        for ($i=0;$i<count($breadCrumbList)-1;$i++) {
            $newBreadCrumb[] = $breadCrumbList[$i];
        }

        if ($actDrill) {
            $add = array();
            $add[name] = $drillName;
            $add[url] = "drillChoice.php";
            $add[icon] = 2;

            $drillList = cmsCategory_getList(array("mainCat"=>$actProject),"name");
            if (is_array($drillList) AND count($drillList)>1) {
                $add[dropList] = array();
                $add[dropId]="drill";
                for ($i=0;$i<count($drillList);$i++) {
                    $name = $drillList[$i][name];
                    $drillId   = $drillList[$i][id];
                    $add[dropList][] = array("name"=>$name,"url"=>"session:drill|$drillId");
                    // echo ("$name $drillId <br>");                    
                }                
            }



            $newBreadCrumb[] = $add;
        }

        if ($actProject) {
            $add = array();
            $add[name] = $projName;
            $add[url] = "projectChoice.php";
            $add[icon] = 2;


            $projectList = cmsCategory_getList(array("mainCat"=>1),"name");
            if (is_array($projectList) AND count($projectList)>1) {
                $add[dropList] = array();
                $add[dropId]="project";
                for ($i=0;$i<count($projectList);$i++) {
                    $name = $projectList[$i][name];
                    $projId   = $projectList[$i][id];
                    $url = "projectChoice.php?setProject=$projId";
                    $add[dropList][] = array("name"=>$name,"url"=>$url);
                    // echo ("$name $drillId <br>");                    
                }                
            }


            $newBreadCrumb[] = $add;




        }


       //  show_array($breadCrumbList);


        return $newBreadCrumb;
    }
}



?>
