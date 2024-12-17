<?php // charset:UTF-8

class cmsAdmin_category extends cmsAdmin_category_base {

    function category_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40);
        $showList["name"] = array("name"=>"Name","width"=>400);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center");
        $showList["subCat"] = array("name"=>"Unterkategorien","width"=>120,"align"=>"center");
        return $showList;
    }
    
    function admin_get_specialFilterList_own() {
        $specialList = array();
        $specialList[hidden] = array("id"=>"hidden","name"=>"Unsichtbare Termine");
        $specialList[hidden][filter] = array("show"=>0);
        $specialList[hidden][sort] = "date";
    }


    function edit_show_own($tableName,$specialData) {

        $editShow = array();
        $editShow[id]      = array("name"=>"Kategorie Id","show"=>1,"showLevel"=>9,"type"=>"text","width"=>"small","readonly"=>1);
        $editShow[id][needed] = 0;

        $editShow[name]    = array("name"=>"Kategoriename","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[name][needed] = "textContent";
        $editShow[name][needError] = "Kein Kategoriename eingeben";

        $editShow[subName]    = array("name"=>"Kategoriename","show"=>0,"type"=>"text","width"=>"standard");

        $editShow[info]    = array("name"=>"Info","show"=>1,"type"=>"textarea","width"=>"standard","height"=>"standard");
        $editShow[info][needed] = 0;

      

        $editShow[image] = array("name"=>"Kategorie-Bild","show"=>1,"type"=>"imageSelect","width"=>"standard","height"=>150);
        $editShow[image][imageUpload] = 1;
        $editShow[image][imageFolder] = "/ausgaben/";


//        $editShow[image][needed] = 0;
//
//        $editShow[url]    = array("name"=>"Webseite","show"=>1,"type"=>"text","width"=>"standard");
//        $editShow[url][needed] = 0;
//
//        $editShow[ticketUrl]    = array("name"=>"Ticket Webseite","show"=>1,"type"=>"text","width"=>"standard");
//        $editShow[ticketUrl][needed] = 0;
//
//        $editShow[sort] = array("name"=>"Sortierung","show"=>1,"type"=>"text","width"=>"standard");
//        $editShow[sort][needed] = 0;
//
        $editShow[mainCat] = array("name"=>"Data","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[mainCat][needed] = 0;

        // Category
        $editShow[mainCat] = array("name"=>"HauptKategorie","show"=>1,"type"=>"dropdown","width"=>"standard");
        $editShow[mainCat][dataSource] = "category";
        $editShow[mainCat][disabled] = 1;
        $editShow[mainCat][readonly] = 1;

        $editShow[mainCat][showData] = array("class"=>"adminCategory_Category","disabled"=>1,"dataSource"=>"category");
        $editShow[mainCat][showFilter] = array("mainCat"=>0,"show"=>1);
        $editShow[mainCat][showSort] = "name";
        $editShow[mainCat][needed] = 1;
        $editShow[mainCat][needError] = "Keine Kategorie ausgewählt";

//
        $editShow[show] = array("name"=>"Anzeigen","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[show][needed] = 0;


        return $editShow;
    }
     

    


 
}











?>
