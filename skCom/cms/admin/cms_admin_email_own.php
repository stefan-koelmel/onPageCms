<?php
 // charset:UTF-8

class  cmsAdmin_email extends cmsAdmin_email_base {


    function admin_get_specialFilterList_own() {
        $specialList = array();
        
        /*$specialList[hidden] = array("id"=>"hidden","name"=>"Unsichtbare Orte");
        $specialList[hidden][filter] = array("show"=>0);
        $specialList[hidden][sort] = "name";

        $specialList[noRegion] = array("id"=>"noRegion","name"=>"Orte ohne Region");
        $specialList[noRegion][filter] = array("region"=>"0","show"=>"-");
        $specialList[noRegion][sort] = "name";

        $specialList[noCategory] = array("id"=>"noCategory","name"=>"Orte ohne Kategorie");
        $specialList[noCategory][filter] = array("category"=>"0","show"=>"-");
        $specialList[noCategory][sort] = "name";

        $specialList[noWeb] = array("id"=>"noWeb","name"=>"Orte ohne Internetadresse");
        $specialList[noWeb][filter] = array("url"=>"","show"=>"-");
        $specialList[noWeb][sort] = "name";*/



        return $specialList;
    }


    function admin_get_filterList_own() {
        $filterList = array();

        /*   $filterList[specialView]   = array();
        $filterList[specialView]["name"] = "Spezielle Ansichten";
        $filterList[specialView]["type"] = "specialView";
        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        $filterList[specialView]["dataName"] = "specialView";
        $filterList[specialView][customFilter] = 1;

        $filterList[produkt] = 0;

        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Kategorie wählen");
        $filterList[category]["filter"] = array("mainCat"=>8,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[region]   = array();
        $filterList[region]["name"] = "Region";
        $filterList[region]["type"] = "category";
        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Region wählen");
        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
        $filterList[region]["sort"] = "name";
        $filterList[region]["dataName"] = "region";
        $filterList[region][customFilter] = 1;

        $filterList[location] = array();
        $filterList[location]["name"] = "Ort";
        $filterList[location]["type"] = "location";
        $filterList[location]["dataName"] = "location";
        $filterList[location]["showData"] = array("submit"=>1,"type"=>"simple","empty"=>"Ort wählen");
        $filterList[location]["filter"] = array("show"=>"1");
        $filterList[location]["sort"] = "name";
        $filterList[location][customFilter] = 1;*/

        return $filterList;
    }

    function email_showList() {
        $showList = array();
        //$showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>300);
        $showList["category"] = array("name"=>"Kategorie","width"=>180);
        //$showList["region"] = array("name"=>"Region","width"=>150);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }

    function edit_show_own($tableName,$specialData) {

        $editShow = array();
        $editShow[id]      = array("name"=>"Ort Id","show"=>1,"showLevel"=>9,"type"=>"text","width"=>"small");
        $editShow[id][needed] = 0;

        $editShow[name]    = array("name"=>"eMail Bezeichnung","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[name][needed] = "textContent";
        $editShow[name][needError] = "Kein Artikelname eingeben";

        $editShow[subName]    = array("name"=>"Betreff","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[subName][needed] = 1;
        $editShow[subName][needError] = "Kein Betreff eingeben";



        $editShow[info]    = array("name"=>"Text","show"=>1,"type"=>"textarea","width"=>"standard","height"=>400);
        $editShow[info][needed] = 1;
        $editShow[info][needError] = "Kein Text eingeben";

        // Category
        /*$editShow[category] = array("name"=>"Kategorie","show"=>1,"type"=>"toggle","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminArticles_Category","count"=>5,"mode"=>"multi");
        $editShow[category][showFilter] = array("mainCat"=>8,"show"=>1);
        $editShow[category][showSort] = "name";
        $editShow[category][needed] = 1;
        $editShow[category][needError] = "Keine Kategorie ausgewählt";

        $editShow[image] = array("name"=>"Ort-Bild","show"=>1,"type"=>"imageSelectList","width"=>"standard","imgWidth"=>100,"imgHeight"=>75);
        $editShow[image][needed] = 0;
        $editShow[image][imageUpload] = 1;
        $editShow[image][imageFolder] = "/locations/";

        $editShow[url]    = array("name"=>"Webseite","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[url][needed] = 0;

        $editShow[ticketUrl]    = array("name"=>"Ticket Webseite","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[ticketUrl][needed] = 0;*/


        $editShow[show] = array("name"=>"Anzeigen","show"=>0,"type"=>"checkbox","width"=>"standard");
        $editShow[show][needed] = 0;
        
        
        // $editShow[sort] = array("name"=>"Sortierung","show"=>1,"type"=>"text","width"=>"standard");
        // $editShow[sort][needed] = 0;

        $editShow[data] = array("name"=>"Data","show"=>0,"type"=>"data","width"=>"standard");
        /*$editShow[data][showData] = array();
        $editShow[data][showData][open] = array("name"=>"Öffnungszeiten","show"=>1,"type"=>"textarea","height"=>50);
        $editShow[data][showData][notice] = array("name"=>"Bemerkung","show"=>1,"type"=>"textarea");
        $editShow[data][showData][kitchen] = array("name"=>"Küche","show"=>1,"type"=>"textarea");
        $editShow[data][showData][info_20] = array("name"=>"Biergarten-Text","show"=>1,"type"=>"textarea");
        $editShow[data][showData][info_19] = array("name"=>"Café-Text","show"=>1,"type"=>"textarea");
        $editShow[data][showData][info_18] = array("name"=>"Kneipen-Text","show"=>1,"type"=>"textarea");
        $editShow[data][showData][info_22] = array("name"=>"Restaurant-Text","show"=>1,"type"=>"textarea");*/

        $editShow[lastMod] = array("name"=>"Letzte Änderung","show"=>1,"showLevel"=>8,"type"=>"text","width"=>"small","readonly"=>1);
        $editShow[lastMod][needed] = 0;


        $editShow[changeLog] = array("name"=>"Protokoll","show"=>1,"showLevel"=>9,"type"=>"changeLog","width"=>"standard","readonly"=>1);
        $editShow[changeLog][needed] = 0;

        


        return $editShow;
    }
    
     function editButtons_own($buttonList,$saveData) {
        if ($saveData[id]) {
            $buttonList[save][name] = "eMail speichern";           
        } else {
            $buttonList[save][name] = "eMail anlegen";
        }
        return $buttonList;
    }



}








?>

