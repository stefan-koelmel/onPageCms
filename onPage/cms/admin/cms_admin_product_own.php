<?php // charset:UTF-8

class cmsAdmin_product extends cmsAdmin_product_base {

    function product_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Produktbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["company"] = array("name"=>"Hersteller","width"=>140);
        $showList["category"] = array("name"=>"Kategorie","width"=>140);
        $showList["new"] = array("name"=>"Neu","width"=>50,"align"=>"center");
        // $showList["highlight"] = array("name"=>"Highlight","width"=>60,"align"=>"center");
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }


    function edit_show_own($tableName,$specialData) {

        $editShow = array();
        $editShow[id]      = array("name"=>"Produkt Id","show"=>1,"showLevel"=>9,"type"=>"text","width"=>"small");
        $editShow[id][needed] = 0;

        $editShow[name]    = array("name"=>"Produkt-Name","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[name][needed] = "textContent";
        $editShow[name][needError] = "Kein Produktname eingeben";


        $editShow[subName]    = array("name"=>"Produkt","show"=>0,"type"=>"text","width"=>"standard");

        $editShow[info]    = array("name"=>"Produkt-Beschreibung","show"=>1,"type"=>"textarea","width"=>"standard","height"=>"standard");
        $editShow[info][needed] = 0;

        // Auto Complete
//        $editShow[company] = array("name"=>"Hersteller","show"=>1,"type"=>"autoComplete","width"=>"standard");
//        $editShow[company][showData] = array("class"=>"adminproduct_Category","show"=>1,"style"=>"width:".$width."px;");
//        $editShow[company][showFilter] = array("mainCat">0,"show"=>1);
//        $editShow[company][showSort] = "name";

        // DropDown
        $editShow[company] = array("name"=>"Hersteller","show"=>1,"type"=>"dropdown","width"=>"standard");
        $editShow[company][showData] = array("class"=>"adminProduct_company","show"=>1,"empty"=>"Hersteller wählen");
        $editShow[company][showFilter] = array("show"=>1);
        $editShow[company][showSort] = "name";
        $editShow[company][needed] = 1;
        $editShow[company][needError] = "Keine Hersteller ausgewählt";

        // Toggle
//        $editShow[company] = array("name"=>"Hersteller","show"=>1,"type"=>"toggle","width"=>"standard");
//        $editShow[company][showData] = array("class"=>"adminProduct_company","width"=>$rightWidth,"count"=>3,"mode"=>"single");
//        $editShow[company][showFilter] = array("show"=>1);
//        $editShow[company][showSort] = "name";
//        $editShow[company][needed] = 1;
//        $editShow[company][needError] = "Keine Hersteller ausgewählt";


        // Toggle
        $editShow[category] = array("name"=>"Kategorie","show"=>1,"type"=>"toggle","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminProduct_Category","width"=>$rightWidth,"count"=>3,"mode"=>"multi");
        $editShow[category][showFilter] = array("mainCat"=>0,"show"=>1);
        $editShow[category][showSort] = "name";
        $editShow[category][needed] = 1;
        $editShow[category][needError] = "Keine Kategory ausgewählt";


         // Category
        $editShow[category] = array("name"=>"Rubrik","show"=>1,"type"=>"toggle","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminArticles_Category","count"=>3,"mode"=>"single");
        $editShow[category][showFilter] = array("mainCat"=>1,"show"=>1);
        $editShow[category][showSort] = "name";
        $editShow[category][needed] = 1;
        $editShow[category][needError] = "Keine Rubrik ausgewählt";


        // Category
        $editShow[subCategory] = array("name"=>"Unter-Rubrik","show"=>0,"type"=>"toggle","width"=>"standard");
        $editShow[subCategory][dataSource] = "category";
        $editShow[subCategory][showData] = array("class"=>"adminArticles_subCategory","count"=>3,"mode"=>"single");
        $editShow[subCategory][showData][url] = "/cms_$GLOBALS[cmsVersion]/getData/category.php?cmsName=$GLOBALS[cmsName]&cmsVersion=$GLOBALS[cmsVersion]&type=toggle&mode=simple";
        $editShow[subCategory][showFilter] = array("mainCat"=>$specialData[category],"show"=>1);
        $editShow[subCategory][showSort] = "id";
        $editShow[subCategory][needed] = 1 ;
        $editShow[subCategory][needError] = "Keine Unter-Rubrik ausgewählt";
        
        
        
        
        
        
        $editShow[image] = array("name"=>"Projekt-Bild","show"=>1,"type"=>"imageSelectList","width"=>"standard","height"=>80);
        $editShow[image][imageUpload] = 1;
        $editShow[image][imageFolder] = "/product/";
        
        $editShow[vk] = array("name"=>"Verkaufspreis","show"=>1,"type"=>"float","width"=>"standard");
        $editShow[vk][needed] = 0;
        $editShow[vk][komma] = ",";
        $editShow[vk]["1000"] = ".";
        $editShow[vk][deci] = 2;
        $editShow[vk][needError] = "Kein Verkaufspreis eingeben";
        
        $editShow[shipping] = array("name"=>"Porto","show"=>1,"type"=>"float","width"=>"standard");
        $editShow[shipping][needed] = 0;
        $editShow[shipping][komma] = ",";
        $editShow[shipping]["1000"] = ".";
        $editShow[shipping][deci] = 2;
        $editShow[shipping][needError] = "Kein Porto eingeben";
        
        $editShow[count] = array("name"=>"Anzahl","show"=>1,"type"=>"integer","width"=>"standard");
        $editShow[count][needed] = 0;
       
        
        
        
        $editShow["new"] = array("name"=>"Neuheit","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow["new"][needed] = 0;

        $editShow[highlight] = array("name"=>"Hervorheben","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[highlight][needed] = 0;

        $editShow[show] = array("name"=>"Anzeigen","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[show][needed] = 0;

//        $editShow[vk] = array("name"=>"Verkaufspreis","show"=>0,"type"=>"text","width"=>"standard");
//        $editShow[count] = array("name"=>"Anzahl","show"=>0,"type"=>"text","width"=>"standard");


        return $editShow;
    }

    function editButtons_own($buttonList,$saveData) {
        if ($saveData[id]) {
            $buttonList[save][name] = "Produkt speichern";           
        } else {
            $buttonList[save][name] = "Produkt anlegen";
        }
        return $buttonList;
    }
    
        
 
}





?>
