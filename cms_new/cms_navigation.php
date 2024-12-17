<?php // charset:UTF-8
function cms_navi_getDirections() {
     $res = array();
     $res[hori] = array("name"=>"Horizontal");
     $res[vert] = array("name"=>"Vertikal");
     return $res;
 }


function cms_navi_SelectDirection($type,$dataName,$showData=array()) {
    $typeList = cms_navi_getDirections();
    $str = "";
    
    if ($showData[submit]) {
        $submitStr = "onChange='submit()'";
    } else {
        $submitStr = "";
    }
    
    $str.= "<select name='$dataName' class='cmsSelectType' value='$type' $submitStr >";
    foreach ($typeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">$typeData[name]</option>";
    }
    $str.= "</select>";
    return $str;
}


function cms_navi_getNaviTypes() {
     $res = array();
     $res[subPage] = array("name"=>"Untergeordnete Seiten");
     $res[mainPage] = array("name"=>"Übergeordnete Seiten");
     $res[index] = array("name"=>"Unterseiten von Startseite");
     $res[parallel] = array("name"=>"Parallele Seite");
     $res[scrollTop] = array("name"=>"Sprungpunkt Seitenanfang");
     $res[scrollList] = array("name"=>"Sprungpunkt Liste");
     return $res;
 }


function cms_navi_SelectNaviType($type,$dataName) {
    $typeList = cms_navi_getNaviTypes();
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' value='$type' >";
    foreach ($typeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">$typeData[name]</option>";
    }
    $str.= "</select>";
    return $str;
}

?>
