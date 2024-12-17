<?php // charset:UTF-8
class cmsType_articlesList extends cmsType_articlesList_base {

    function showList_dataLine($data, $showData, $showList, $frameWidth) {
        $leftWidth = 200;
        $padding = 10;
        $rightWidth = $frameWidth - $leftWidth - $padding;
        $height = 100;
        div_start("articlesBox","width:".$frameWidth."px;min-height:".$height."px;border:1px solid #ccc;margin-bottom:10px;");
        $name = $data[name];
        echo ("<h1>$name</h1>");
        
        div_start("articlesBox_left","float:left;width:".$leftWidth."px;margin-right:".$padding."px;");

        $imageId = $data[image];
        $imgStr = "Kein Bilde";
        if ($imageId) {
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $showImageData = array("class"=>"cmsImageSelect","frameWidth"=>$leftWidth,"frameHeight"=>floor($leftWidth*0.75),"vAlign"=>"top","hAlign"=>"left");
                $imgStr = cmsImage_showImage($imageData,100,$showImageData);
            }
        }

        echo ($imgStr);
        div_end("articlesBox_left");
        div_start("articlesBox_right","float:left;width:".$rightWidth."px;");

        $subName = $data[subName];
        if ($subName) echo ("</h2>$subName</h2>");

        $info = $data[info];
        if ($info) echo ($info."<br>");

        $location    = $data[location];
        if ($location) {
            $locationData = cmsLocation_get(array("id"=>$location));
            if (is_array($locationData)) {
                echo ("&nbsp;<br>");
                echo ("Ort : $locationData[name]<br>");
                echo ("Ort : $locationData[street] $locationData[streetNr]<br>");
                echo ("Ort : $locationData[plz] $locationData[city]<br>");
                echo ("Ort : $locationData[url] <br>");
            }
        }
        $category    = $data[category];
        if ($category) {
            $categoryData = cmsCategory_get(array("id"=>$category));
            if (is_array($categoryData)) {
                echo ("Rubrik : $categoryData[name]<br>");
            }
        }
        $region      = $data[region];
        if ($region) {
            $regionData = cmsCategory_get(array("id"=>$region));
            if (is_array($regionData)) {
                echo ("Region : $regionData[name]<br>");
            }
        }
        $url         = $data[url];
        $ticketUrl   = $data[ticketUrl];

        //  show_array($data);
        div_end("articlesBox_right");

        
        div_end("articlesBox","before");


    }

    

    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        return $res;
    }

    function customFilter_specialView_getList_own() {
        $specialList = array();


        $specialList[noneLocation] = array("id"=>"gone","name"=>"Termine ohne Ort");
        $specialList[noneLocation][filter] = array("show"=>1,"location"=>0);
        $specialList[noneLocation][sort] = "locationStr";


        $specialList[noneRegion] = array("id"=>"gone","name"=>"Termine ohne Region");
        $specialList[noneRegion][filter] = array("show"=>1,"region"=>0);
        $specialList[noneRegion][sort] = "date";
        return $specialList;
    }

    function editContent_filter_getList_own() {
        $filterList = array();
        $filterList[produkt] = 0;

        $filterList[specialView]   = array();
        $filterList[specialView]["name"] = "Spezielle Ansichten";
        $filterList[specialView]["type"] = "specialView";
        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        //$filterList[specialView]["filter"] = array("mainCat"=>180,"show"=>1);
        // $filterList[specialView]["sort"] = "name";
        $filterList[specialView]["dataName"] = "specialView";
        $filterList[specialView][customFilter] = 1;
        $filterList[specialView] = 0;

        $filterList[dateRange] = array();
        $filterList[dateRange]["name"] = "Zeitraum";
        $filterList[dateRange]["type"] = "dateRange";
        $filterList[dateRange]["dataName"] = "dateRange";
        $filterList[dateRange]["showData"] = array("submit"=>1,"empty"=>"Zeitraum nicht einschränken");
        $filterList[dateRange]["filter"] = array("mainCat"=>1,"show"=>1);
        $filterList[dateRange]["sort"] = "";
        $filterList[dateRange][customFilter] = 1;

        $filterList[produkt] = 0;
        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Alle Kategorien zeigen");
        $filterList[category]["filter"] = array("mainCat"=>144,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[region]   = array();
        $filterList[region]["name"] = "Region";
        $filterList[region]["type"] = "category";
        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Alle Region zeigen");
        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
        $filterList[region]["sort"] = "name";
        $filterList[region]["dataName"] = "region";
        $filterList[region][customFilter] = 1;

        return $filterList;
    }




}



?>
