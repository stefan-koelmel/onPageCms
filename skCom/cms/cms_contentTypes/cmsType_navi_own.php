<?php // charset:UTF-8
class cmsType_navi extends cmsType_navi_base {
    
    function navi_standard($data) {
        $standard = array();
        $standard[border] = 1;
        $standard[naviBorder] = 0;
        $standard[borderColor] = "#fff";
        $standard[backcolor] = "#ccc";
        $standard[paddingStep] = 5;
        $standard[paddingLeft] = 5;
        $standard[maxLevel] = 3;
        $standard[selectMaxLevel] = 3;
        $standard[direction] = "hori";
        $standard[startLevel] = 0;
        
        switch ($data[directon]) {
            case "hori" : 
                break;
            case "vert" :
                break;                
        }
        
        return $standard;
    }
    

    function navi_showItem_own($direction,$page,$subNavi,$class,$goPage,$subBreadCrumb,$subNavi,$aktLevel) {
        return 0;
    }


    function navi_getItem_own($direction,$page,$goPage,$linkClass,$breadCrumb,$subNavi,$level) {

        $title = $page[title];
        $name = $page[name];

        $showName = $title;
        if ($showName == "") $showName = $name;
        $out = "";

        $parameter = array();
        list($urlPage,$urlPara) = explode("?",$goPage);
        if ($urlPara) {
            $paraList = explode("&",$urlPara);
            for ($i=0;$i<count($paraList);$i++) {
                list($paraKey,$paraValue) = explode("=",$paraList[$i]);
                $parameter[$paraKey] = $paraValue;
            }
        }

        if ($parameter[category] AND !$parameter[project]) {
             $out .= "<a href='$goPage' class='$linkClass'>$showName</a>";
             $out = array("out"=>$out,"noLink"=>1,"divLink"=>1);
        }

        if ($parameter[project]) {
            //$out = "<div style='width:500px;'>";
            $imageId = 0;
            $icon = $page[image];
            if (intval($image)) $imageId = $icon;
            else {
                $imageList = explode("|",$icon);
                if (count($imageList)>1) $imageId = $imageList[1];
            }
            if ($imageId) {
                $imgData = cmsImage_getData_by_Id($imageId);
                $showData = array();
                $img = cmsImage_showImage($imgData, 50, $showData);

                $out .= "<div style='width:".$imgWidth."px;margin-right:2px;float:left'>";
                $out .= $img;
                $out .= "</div>";
                $out .= "<div style='float:left;width:200p;'>";
            //    $out .= $img;
            }



           // $out .= "<br/>";
            $out .= "<a href='$goPage' class=''>$showName</a>";
            // $out .= "<br />".$breadCrumb;
            if ($imageId) {
                $out .= "</div>";
            }

            //if ($parameter[project]) $out .= "<br>PROJECT ID = ".$parameter[project];
            // foreach($page as $key => $value) $out .= "<br>$key => $value";
            $out = array("out"=>$out,"noLink"=>1,"divLink"=>1);
        }        
        return $out;
    }



}



 
?>
