<?php // charset:UTF-8

class cmsAdmin_company extends cmsAdmin_company_base {

    function company_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Logo","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["category"] = array("name"=>"Kategory","width"=>200);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }
   
    function edit_show_own($tableName) {

        $editShow = array();
        $editShow[id]      = array("name"=>"Hersteller Id","show"=>1,"showLevel"=>9,"type"=>"text","width"=>"small");
        $editShow[id][needed] = 0;

        $editShow[name]    = array("name"=>"Hersteller","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[name][id] = 'firstFocus';
        $editShow[name][needed] = "textContent";
        $editShow[name][needError] = "Kein Herstellername eingeben";

        $editShow[subName]    = array("name"=>"Hersteller","show"=>0,"type"=>"text","width"=>"standard");

        $editShow[info]    = array("name"=>"Info","show"=>1,"type"=>"textarea","width"=>"standard","height"=>"standard");
        $editShow[info][needed] = 0;

        // Auto Complete
        $editShow[category] = array("name"=>"Kategorie","show"=>1,"type"=>"autoComplete","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminCompany_Category","show"=>1,"style"=>"width:".$width."px;");
        $editShow[category][showFilter] = array("mainCat">0,"show"=>1);
        $editShow[category][showSort] = "name";

        // DropDown
        $editShow[category] = array("name"=>"Kategorie","show"=>1,"type"=>"dropdown","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminCompany_Category","show"=>1,"empty"=>"Kategorie wählen");
        $editShow[category][showFilter] = array("mainCat"=>0,"show"=>1);
        $editShow[category][showSort] = "name";

        // Toggle
        $editShow[category] = array("name"=>"Kategorie","show"=>1,"type"=>"toggle","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminCompany_Category","width"=>$rightWidth,"count"=>3,"mode"=>"multi");
        $editShow[category][showFilter] = array("mainCat"=>0,"show"=>1);
        $editShow[category][showSort] = "name";
        $editShow[category][needed] = 1;
        $editShow[category][needError] = "Keine Kategory ausgewählt";


        $editShow[image] = array("name"=>"Hersteller-Logo","show"=>1,"type"=>"imageSelect","width"=>"standard","height"=>150);
        $editShow[image][needed] = 0;
        $editShow[image][imageUpload] = 1;
        $editShow[image][imageFolder] = "/hersteller/";


        $editShow[url] = array("name"=>"Hersteller-Webseite","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[url][needed] = 0;


        $editShow[show] = array("name"=>"Anzeigen","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[show][needed] = 1;


        return $editShow;
    }
    
    function editButtons_own($buttonList,$saveData) {
        if ($saveData[id]) {
            $buttonList[save][name] = "Hersteller speichern";           
        } else {
            $buttonList[save][name] = "Hersteller anlegen";
        }
        return $buttonList;
    }

    

 
}


?>
