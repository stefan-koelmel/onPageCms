<?php // charset:UTF-8

class cmsAdmin_dates extends cmsAdmin_dates_base {
    
    
    function checkList($dataList,$action) {
        if ($_SESSION[userLevel] < 9) return 0;
        
//        $ticketUrl = "http://www.amazon.de/s/ref=nb_sb_noss?__mk_de_DE=%C5M%C5Z%D5%D1&url=search-alias%3Ddigital-music&field-keywords=beth+hart";
//        echo (php_clearLink($ticketUrl)."<br>");
//        echo (php_unClearLink($ticketUrl)."<br>");
//        echo (html_entity_decode($ticketUrl)."<br>");
//        echo (php_clearStr($ticketUrl)."<br>");
        
        
        switch ($action) {
            
            case "ticketUrl" :
                echo ("checkList $action <br>");
                $query = "SELECT `id`,`data`,`name`,`date`,`location` FROM `klappeAuf_cms_dates` WHERE `data` != ''";
                //  $query = "SELECT * FORM `klappeAuf_cms_articles` WHERE `link` != ''";
                // $query = "SELECT `id` , `name` , `link` FROM `klappeAuf_cms_articles` WHERE 1 AND `link` != '\'\'' LIMIT 0 , 30";
                $result = mysql_query($query);
                if (!$result) {
                    echo ("$query<br>");
                    return 0;
                }
                $locationList = array();
                while ($dateData = mysql_fetch_assoc($result)) {
                    $dateId = $dateData[id];
                    $dateName = $dateData[name];
                    $data= str2Array($dateData[data]);
                    if (is_array($data)) {
                        $ticketUrl = $data[ticketUrl];
                        $update = 0;
                        if ($ticketUrl) {
                            $removeTicket = 0;
                        
                            // echo ("$dateId $dateName -> '$ticketUrl' - ".substr($ticketUrl,0,20)." <a href='admin_cmsDates.php?id=$dateId'>zeigen</a> <br>");
                            if (substr($ticketUrl,0,20) == "http://staatstheater") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "http://www.kammerthe") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "Auf der seite mögli") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "per mail") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "karten@kulturhaus-os") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "www.foerderkreis-kul") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "Kartenverkauf@BStaat") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "Verkauf über Anfrag") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "http://www.marotte-f") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "http://www.karlsruhe") $removeTicket = 1;
                           
                            
                            if (substr($ticketUrl,0,20) == "www.galeriebode.de") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "www.forderkreis-kult") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "www.marotte-figurent") $removeTicket = 1;
                            if (substr($ticketUrl,0,20) == "www.hfm-karlsruhe.de") $removeTicket = 1;
                            
                            
                            if ($removeTicket == 1) {
                                $update = 1;
                                echo ("<b> -> removeTicket $ticketUrl </b><br>");
                                unset($data[ticketUrl]);                                
                            }
                        }
                        /// check Url;
                        $url = $data[url];
                        if ($url) {
                            $removeUrl = 0;
                            if ($url == "x-url-x") $removeUrl = 1;
                            if ($url == "http://") $removeUrl = 1;
                            if ($url == "http://www.") $removeUrl = 1;
                            
                            if ($removeUrl == 1) {
                                $update = 1;
                                echo ("<b> $dateId -> removeUrl $url </b><br>");
                                unset($data[url]);                                
                            }
                        }
                        
                        if (intval($dateData[location])) {
                            $locationId = intval($dateData[location]);
                            $compare = array("street","streetNr","plz","city","url");
                             
                            if (is_array($locationList[$locationId])) {
                                $locationData = $locationList[$locationId];
                            } else {
                                $locationData = cmsLocation_getById($locationId);
                                $locationList[$locationId] = $locationData;
                            }
                            
                            if (is_array($locationData)) {
                                $removeAdress = 0;
                                
                                for($c=0;$c<count($compare);$c++) {
                                    $locKey = $compare[$c];
                                    if (!is_null($data[$locKey])) {
                                        echo ("$dateId remove $locKey $data[$locKey] <br>");
                                        unset($data[$locKey]);
                                        $removeAdress = 1;
                                    }
                                }
                            }
                            
//                            foreach ($compare as $key => $locKey) {
//                                if ($data[$locKey]) {
//                                    if ($locationData[$locKey]) {
//                                        
//                                        if ($data[$locKey] == $locationData[$locKey]) {
//                                            // echo ("<b> SAME </b>");  
//                                            $removeAdress = 1;
//                                            unset($data[$locKey]);
//                                        } else {
//                                            if ($locKey == "url" ) {
//                                                $removeAdress = 1;
//                                                unset($data[$locKey]);
//                                            } else {
//                                                echo ("diffrent ");
//                                                echo ("$locKey = $data[$locKey] <-> $locationData[$locKey] <br>");
//                                            }
//                                        }
//                                        
//                                    }
//                                } else {
//                                    unset($data[$locKey]);
//                                    // $removeAdress = 1;
//                                }
//                            }
                            if ($removeAdress)  {
                                echo ("$dateId => Change in $locationData restAnz = ".count($data));
                                if (count($data)) foreach ($data as $k => $v) echo (" | $k = $v");
                                echo ("<br> ");
                                $update = 1;
                            }
                            
                        }
                        
                        
                        
                        if ($update) {
                            
                            foreach ($data as $dataKey => $dataValue) {
                                if ($dataValue) {
                                    $data[$dataKey] = php_clearStr($dataValue);
                                } else {
                                    unset($data[$dataKey]);
                                }
                            }
                            if (count($data)) {
                                $dataStr = array2Str($data);
                            } else {
                                $dataStr = "";
                            }
                            
                            $updateQuery = "UPDATE `klappeAuf_cms_dates` SET `data` = '$dataStr' WHERE `id` = $dateId";
                            $updateResult = mysql_query($updateQuery);
                            if (!$updateResult) echo ("Error in Query <br>$updateQuery<br>");
                        }
                        
                    } else {
                        $remove = 0;
                        if (strpos($dateData[data],"jump=1")) {
                            echo ("<b>remove Data </b>$dateData[data] <br>");
                            $remove = 1;
                        }
                        
                        
                        
                        
                        echo ("$dateId $dateName --> No array in $dateData[data] <br>");
                        echo ("<a href='admin_cmsDates.php?id=$dateId'>zeigen</a><br>&nbsp;<br> ");
                        if ($remove) {
                            $dataStr = "";
                            $updateQuery = "UPDATE `klappeAuf_cms_dates` SET `data` = '$dataStr' WHERE `id` = $dateId";
                            $updateResult = mysql_query($updateQuery);
                            if (!$updateResult) echo ("Error in Query <br>$updateQuery<br>");
                        }
                    }

                }
                break;
            
            
            case "link" :
                echo ("checkList $action <br>");
                $query = "SELECT `id`,`link`,`name`,`date` FROM `klappeAuf_cms_dates` WHERE `link` != ''";
                //  $query = "SELECT * FORM `klappeAuf_cms_articles` WHERE `link` != ''";
                // $query = "SELECT `id` , `name` , `link` FROM `klappeAuf_cms_articles` WHERE 1 AND `link` != '\'\'' LIMIT 0 , 30";
                $result = mysql_query($query);
                if (!$result) echo ("$query<br>");
                else {
                    while ($dateData = mysql_fetch_assoc($result)) {
                        $link = $dateData[link];
                        $dateId = $dateData[id];
                        $newLink = "";
                        if ($link) {
                            if (strpos($link,"{")) {
                                
                                $linkList = str2Array($link);
                                if (is_array($linkList)) {
                                    foreach ($linkList as $linkType => $linkValue) {
                                        switch ($linkType) {
                                            case "article" :
                                                if (intval($linkValue)) {
                                                    echo ("Link to Article $linkValue <br>");
                                                } else {
                                                    if (!$linkValue) {
                                                        // echo ("ist null '$linkValue' <br>");
                                                    } else {
                                                        
                                                        if (substr($linkValue,0,5) == "date:") { 
                                                            echo "$dateId - maybe array $link <br>";
                                                            
                                                            $query = "UPDATE `klappeAuf_cms_dates` SET `link` = '$linkValue' WHERE `id`=$dateId";
                                                            $resultUpdate = mysql_query($query);
                                                            if (!$resultUpdate) echo ("Error in $query <br>");
                                                            else echo ("Update Link ArticleLink is Link DateData $linkValue <br>");
                                                        } else {
                                                            // echo ("unkown ArticleLink $linkValue <br>");
                                                        }
                                                    }
                                                }
                                                break;
                                            case "date" :
                                                echo ("<h1> --> hast $dateId linkList $linkValue </h1> ");
                                                break;
                                            default :
                                                echo "unkown $linkType <br>" ;
                                        }
                                            
                                         // echo ("-> $linkKey=  $linkValue <br>");
                                    }
//                                    for ($i=0;$i<count($linkList);$i++) {
//                                        echo ("-> $i=  $linkList[$i] <br>");
//                                    }
                                } else {
                                    // echo (" --->>> no Array Get <br>");
                                }
                            } else {
                            
                            
//                                $linkList = explode("|",$link);
//                                echo ("Termin $dateData[id] $dateData[name] $dateData[date] <br>");
//                                for ($i=0;$i<count($linkList);$i++) {
//                                    echo ("-> $linkList[$i] <br>");
//                                }                        
                            }
                        }
                    }
                }
                    
                break;
            default :
                echo ("unkown $action in TERMINE CheckList <br>");
        }
    }
    
    function emptyListSelect_own() {
        $ownSelect = array();
        if (!$_GET[date]) $ownSelect["dateRange"] = "month"; // month / nextMonth / week /nextWeek
       
        //$ownSelect["specialView"] = "hidden";
        //$ownSelect["region"] = 181;
        //$ownSelect["category"] = 3;
        return $ownSelect;
    }
    
    function emptyListFilter_own() {
        $ownFilter = array();
        return $ownFilter;
    }      
    
    function datesShow_showList() {
        $showList = array();
        $showList["image"]     = array("name"=>"Bild","width"=>70,"height"=>40,"sort"=>0);
        $showList["date"]      = array("name"=>"Datum","width"=>60,"sort"=>1);
        $showList["category"]  = array("name"=>"Rubrik","width"=>160,"align"=>"left");
        $showList["location"]  = array("name"=>"Location","width"=>140,"sort"=>1);
        $showList["name"]      = array("name"=>"Titel","width"=>140,"align"=>"left");
        $showList["time"]      = array("name"=>"Uhrzeit","width"=>50);
        $showList["show"]      = array("name"=>"zeigen","width"=>50,"align"=>"center","sort"=>0);
        $showList["highlight"] = array("name"=>"Tip","width"=>50,"align"=>"center","sort"=>1);
        $showList["print"]     = array("name"=>"Print","width"=>50,"align"=>"center","sort"=>1,"type"=>"checkbox");
       // $showList["new"]       = array("name"=>"Neu","width"=>50,"align"=>"center","sort"=>1);
        return $showList;
    }

    function showList_checkData($data) {
        // echo ("showList_checkData <br>");
        if (is_array($data)) {
            $name = $data[name];
            // $id   = $data[id];
            if (!$name) {
                // echo ("No Name in id $id<br>");
                $link = $data[link];
                if (substr($link,0,9) == "dateMain:") {
                    $linkId = substr($link,9);
                    if (intval($linkId)) {
                        $linkedDate = cmsDates_getById($linkId);
                        if (is_array($linkedDate)) {
                            if ($linkedDate[name]) $data[name] = $linkedDate[name];                            
                        }
                    }                    
                } else {
                    // echo ("No Linked Main Id ".substr($link,0,9)."<br>");
                }
                
                
                
            }
        }
        
        return $data;
    }
    
    function admin_get_specialFilterList_own() {
        $specialList = array();
        $specialList[hidden] = array("id"=>"hidden","name"=>"Unsichtbare Termine");
        $specialList[hidden][filter] = array("show"=>0);
        $specialList[hidden][sort] = "date";
        
        $specialList[highlight] = array("id"=>"highlight","name"=>"Tagestipps");
        $specialList[highlight][filter] = array("highlight"=>1,"show"=>1);
        $specialList[highlight][sort] = "date";

        $specialList["print"] = array("id"=>"print","name"=>"Printausgabe");
        $specialList["print"][filter] = array("print"=>1,"show"=>1);
        $specialList["print"][sort] = "date";

        $specialList["notPrint"] = array("id"=>"notPrint","name"=>"nicht Printausgabe");
        $specialList["notPrint"][filter] = array("print"=>0,"show"=>1);
        $specialList["notPrint"][sort] = "date";


        $specialList["cancel"] = array("id"=>"cancel","name"=>"Abgesagt");
        $specialList["cancel"][filter] = array("cancel"=>1,"show"=>1);
        $specialList["cancel"][sort] = "date";
        
        $specialList["lastChange"] = array("id"=>"lastChange","name"=>"letzte Änderungen");
        $specialList["lastChange"][filter] = array("show"=>1,"lastMod"=>">='2012-10-01 00:00:00'"); // "cancel"=>1,"show"=>1);
        $specialList["lastChange"][sort] = "lastMod_up";



//        $specialList["print"] = array("id"=>"print","name"=>"Printausgabe");
//        $specialList["print"][filter] = array("new"=>1,"show"=>1);
//        $specialList["print"][sort] = "date";
//
//        $specialList["noPrint"] = array("id"=>"noPrint","name"=>"nicht Printausgabe");
//        $specialList["noPrint"][filter] = array("new"=>0,"show"=>1);
//        $specialList["noPrint"][sort] = "date";

        return $specialList;

    }




    function admin_get_filterList_own() {
        $filterList = array();

        $filterList[specialView]   = array();
        $filterList[specialView]["name"] = "Spezielle Ansichten";
        $filterList[specialView]["type"] = "specialView";
        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        //$filterList[specialView]["filter"] = array("mainCat"=>180,"show"=>1);
        // $filterList[specialView]["sort"] = "name";
        $filterList[specialView]["dataName"] = "specialView";
        $filterList[specialView][customFilter] = 1;

        $filterList[dateRange]   = array();
        $filterList[dateRange]["name"] = "Zeitraum";
        $filterList[dateRange]["type"] = "dateRange";
        $filterList[dateRange]["showData"] = array("submit"=>1,"empty"=>"Gesamter Zeitraum");
        // $filterList[dateRange]["filter"] = array("mainCat"=>140,"show"=>1);
        // $filterList[dateRange]["sort"] = "name_up";
        $filterList[dateRange]["dataName"] = "dateRange";
        $filterList[dateRange][customFilter] = 1;

       


//        $filterList[month]   = array();
//        $filterList[month]["name"] = "Ausgabe";
//        $filterList[month]["type"] = "category";
//        $filterList[month]["showData"] = array("submit"=>1,"empty"=>"Alle Ausgaben","out"=>"info");
//        $filterList[month]["filter"] = array("mainCat"=>140,"show"=>1);
//        $filterList[month]["sort"] = "name_up";
//        $filterList[month]["dataName"] = "month";
//        $filterList[month][customFilter] = 1;

       
        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Alle Kategorien zeigen");
        $filterList[category]["filter"] = array("mainCat"=>1,"show"=>1);
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
        
        
        $filterList[location] = array();
        $filterList[location]["name"] = "Ort";
        $filterList[location]["type"] = "location";
        $filterList[location]["dataName"] = "location";
        $filterList[location]["showData"] = array("submit"=>1,"type"=>"autoComplete","empty"=>"Ort wählen");
        $filterList[location]["filter"] = array(); // "show"=>"1");
        $filterList[location]["sort"] = "name";
        $filterList[location][customFilter] = 1;

        $filterList[content] = array();
        $filterList[content]["name"] = "Inhalt";
        $filterList[content]["type"] = "text";
        $filterList[content]["dataName"] = "content";
        $filterList[content]["showData"] = array("submit"=>1,"type"=>"text",);
        $filterList[content]["sort"] = "name";
        $filterList[content][customFilter] = 1;

        return $filterList;

        return $filterList;
    }
    
    function deleteMoreText($saveData) {
        $out = "";
        foreach ($saveData[link] as $key => $value) {
            $out .= "$key => $value <br>";
        }
        return $out;
    }


     function edit_show_own($tableName,$specialData) {
         
        
        $formatStr = "Formatierung: <br><b>Fett</b> = #f# Fett #f#<br><i>Kursiv</i> = #k# kursiv #k#";
        $formatStr .= "<br>&sbquo;einfache Anführungszeichen&lsquo; => #a# Text #a#";
        $formatStr .= "<br>&bdquo;doppelte Anführungszeichen&ldquo; => #A# Text #A#";
         
        $editShow = array();
        $editShow[id]      = array("name"=>"Termin Id","show"=>1,"showLevel"=>9,"type"=>"text","width"=>"small");
        $editShow[id][readonly] = 1;
        $editShow[id][needed] = 0;

//        $editShow[mainId]      = array("name"=>"Haupt Termin Id","show"=>0,"showLevel"=>9,"type"=>"text","width"=>"small");
//        $editShow[mainId][readonly] = 1;
//        $editShow[mainId][needed] = 0;
        
        

        $editShow[date] = array("name"=>"Datum","show"=>1,"type"=>"date","mode"=>"simple","width"=>"100","class"=>"adminDates","id"=>"firstFocus");
        // $editShow[date][showData] = array("class"=>"adminDates");
        $editShow[date][needed] = "date";
        $editShow[date][needError] = "Keine Datum ausgewählt";
        $editShow[date][next] = "toDate";

        $editShow[toDate]    = array("name"=>"bis Datum","show"=>1,"type"=>"date","mode"=>"simple","width"=>"100");
        $editShow[toDate][nextDelimiter] = "bis";
        $editShow[toDate][tip] = "Das bis Datum wird nur bei Kunst und Ausstellungen verwendet";
        $editShow[toDate][needed] = 0;


         // Rubrik - AutoComplete
        $editShow[category] = array("name"=>"Rubrik","show"=>1,"type"=>"autoComplete","width"=>"standard");
        $editShow[category][showData] = array("class"=>"adminDates_Category");
        $editShow[category][showFilter] = array("mainCat"=>1,"show"=>1);
        $editShow[category][showSort] = "name";
        $editShow[category][needed] = 1;
        $editShow[category][needError] = "Keine Rubrik ausgewählt";
        $editShow[category][tip] = "Die Rubriken Kunst und Ausstellungen erscheinen nicht im Terminkalender (online und print)";

        // Ort
        $editShow[location] = array("name"=>"Veranstaltungs-Ort","show"=>1,"type"=>"autoComplete","width"=>"standard");
        $editShow[location][showData] = array("class"=>"adminDates_Location");
        $editShow[location][showFilter] = array(); // array("show"=>1);
        $editShow[location][showSort] = "name";
        $editShow[location][needed] = 1;
        $editShow[location][needError] = "Kein Veranstalter ausgewählt";

       
        // Region
        $editShow[region] = array("name"=>"Stadt - Region","show"=>1,"type"=>"autoComplete","dataSource"=>"category","width"=>"standard","id"=>"queryRegion");
        $editShow[region][showData] = array("class"=>"adminDates_Region","id"=>"queryRegion");
        $editShow[region][showFilter] = array("mainCat"=>180,"show"=>1);
        $editShow[region][showSort] = "name";
        $editShow[region][needed] = 1;
        $editShow[region][needError] = "Keine Region ausgewählt";

        $editShow[name]    = array("name"=>"Titel","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[name][needed] = "textContent";
        $editShow[name][needError] = "Kein Terminnamen eingeben";
        
       
        $editShow[info] = array("name"=>"Kurzinfo","show"=>1,"type"=>"textarea","width"=>"standard","height"=>"70px");
        $editShow[info][tip] = $formatStr;
        $editShow[info][needed] = 0;

        $editShow[time]      = array("name"=>"Uhrzeit","show"=>1,"type"=>"time","mode"=>"simple","width"=>"small");
        $editShow[time][next] = "subName";
        $editShow[time][needed] = "time";
        $editShow[time][needError] = "Keine Uhrzeit ausgewählt";
        
        $editShow[subName]    = array("name"=>"Termin-Zusatz","show"=>1,"type"=>"text","width"=>"80%");
        $editShow[subName][tip] = "Termin-Zusatz<br>Hier sollten Informationen wie Premiere, Eröffnung, etc. rein!";
            
        if ($specialData[id]) {
            $editShow[link] = array("name"=>"Verknüpfungen","show"=>1,"type"=>"data","width"=>"standard");
            $editShow[link][showData] = array();
            $editShow[link][showData][date]     = array("name"=>"weitere Termine","show"=>1,"type"=>"special","width"=>"standard");
            $editShow[link][showData][date][tip] = "weitere Termine anlegen, bearbeiten und löschen";
            $editShow[link][showData][article] = array("name"=>"Artikel-Verknüpfung","show"=>1,"type"=>"special","width"=>"standard");
            $editShow[link][showData][article][showLevel] = 8;
        }   $editShow[link][showData][article][tip] = "Artikel-Verknüpfung funktioniert nur mit Adressen aus der Datenbank!";


        
        
        $editShow[highlight] = array("name"=>"Tagestipp","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[highlight][tip] = "Tagestips werden auf der Startseite nur angezeigt wenn auch ein Bild vorhanden ist!";
        $editShow[highlight][showLevel] = 8;
        $editShow[longInfo]    = array("name"=>"Tagestipp Text","show"=>1,"type"=>"textarea","width"=>"standard","height"=>200,"openClose"=>1);
        $editShow[longInfo][needed] = 0;
        $editShow[longInfo][showLevel] = 8;
        

        $editShow[image] = array("name"=>"Termin-Bild","show"=>1,"type"=>"imageSelectList","width"=>"standard","height"=>100);
        $editShow[image][showLevel] = 8;
        $editShow[image][needed] = 0;
        $editShow[image][imageUpload] = 1;
        $editShow[image][imageFolder] = "/dates/";
        $editShow[image][tip] = "Auflösung maximal 1000x1000Pixel<br>Keine Umlaute oder Sonderzeichen<br>";
        

        


        //$editShow[lastMod]    = array("name"=>"Letzte Änderung","show"=>1,"type"=>"text","width"=>"small","readonly"=>1);
        // $editShow[lastMod][needed] = 0;
        // $editShow[sort] = array("name"=>"Sortierung","show"=>1,"type"=>"text","width"=>"standard");
        // $editShow[sort][needed] = 0;

        $editShow[locationStr] = array("name"=>"Veranstaltung-Ort (nicht DB)","show"=>1,"type"=>"text","disabled"=>0,"readOnly"=>0,"width"=>"standard");
        $editShow[locationStr][tip] = "Wird nur angezeigt wenn kein Veranstaltungsort aus der Datenbank ausgwählt ist";
        
        
        $editShow[data] = array("name"=>"Data","show"=>1,"type"=>"data","width"=>"standard");
        $editShow[data][showData] = array();


        $editShow[data][showData][street] = array("name"=>"Straße","show"=>1,"type"=>"text","width"=>"50%");
        $editShow[data][showData][street][id] = "locationStreet";
        $editShow[data][showData][street][next] = "streetNr";
        $editShow[data][showData][streetNr] = array("name"=>"HausNr.","show"=>1,"type"=>"text","width"=>"10%");
        $editShow[data][showData][streetNr][id] = "locationStreetNr";
        $editShow[data][showData][plz] = array("name"=>"Plz","show"=>1,"type"=>"text","width"=>"10%");
        $editShow[data][showData][plz][id] = "locationPlz";
        $editShow[data][showData][plz][next] = "city";
        $editShow[data][showData][city] = array("name"=>"Ort","show"=>1,"type"=>"text","width"=>"50%");
        $editShow[data][showData][city][id] = "locationCity";


        $editShow[data][showData][url]    = array("name"=>"Webseite","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[data][showData][url][id] = "locationUrl";
        $editShow[data][showData][url][needed] = 0;

        $editShow[data][showData][ticketUrl]    = array("name"=>"Weitere Links","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[data][showData][ticketUrl][id] = "locationTicketUrl";
        $editShow[data][showData][ticketUrl][needed] = 0;
        $editShow[data][showData][ticketUrl][tip] = "Die Bezeichnung des Links kann man hinter die URL getrennt durch ein '#' scheiben.<br>Mehrere Links kann mann durch ein '*' trennen.<br>Beispiel:test.de#Gehe zu Test.de'123.de#mehr Infos";

//          $editShow[data][showData]["tip"] = array("name"=>"Tagestip","show"=>1,"type"=>"checkbox","width"=>"80%");

//        $editShow[data][showData]["print"] = array("name"=>"Printausgabe","show"=>1,"type"=>"checkbox","width"=>"80%");
//        $editShow[data][needed] = 0;

        $editShow["print"] = array("name"=>"Printausgabe","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow["print"][needed] = 0;
        $editShow["print"][tip] = "Wenn Printausgabe aktiviert ist erscheint der Termin in der Print-Ausgabe der Klappe Auf, sonst nicht!";



        $editShow[show] = array("name"=>"Anzeigen","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[show][needed] = 0;
        $editShow[show][showLevel] = 8;
        $editShow[show][tip] = "Wenn Anzeigen aktiviert ist erscheint der Termin, sonst nicht!";


        $editShow[cancel] = array("name"=>"Abgesagt","show"=>1,"type"=>"checkbox","width"=>"standard");
        $editShow[cancel][tip] = "Termin erscheint als Abgesagt im Linken Bereich der Webseite";
        $editShow[cancel][showLevel] = 8;
        $editShow["new"] = array("name"=>"Anzeigen","show"=>0,"type"=>"checkbox","width"=>"standard");
        

        
        

        return $editShow;

  
    }
    
    
    function cacheRefresh($id,$saveData,$mainId,$mode="") {
        if ($_SESSION[showLevel] >= 9) echo ("<h1> cacheRefresh($id,$saveData,$mainId)</h1>");
        cmsCache_deleteId("kalender.php", $id, array("short","art"));
        if ($saveData[link]) {
            $linkList = explode("|",$saveData[link]);
            for ($i=0;$i<count($linkList);$i++) {
                list ($linkType,$linkId) = explode(":",$linkList[$i]);
                switch ($linkType) {
                    case "date" : 
                        $dateLinkId = explode(",",$linkId);
                        for ($di=0;$di<count($dateLinkId);$di++) {
                            if ($_SESSION[userLevel] >= 9) echo ("Linked Date for Date $linkId2 -> $dateLinkId[$di]<br>");
                            cmsCache_deleteId("kalender.php", $dateLinkId[$di], array("short"));
                        }
                        break;
                    case "article" : 
                        // echo ("Linked Artikel for Date $linkId2 <br>");
                        break;
                            
                }   
            }
        }
        
        if (intval($mainId)) {
            $linkMainDate = cmsDates_getById($mainId);
            if ($linkMainDate[link]) {
                $linkList = explode("|",$linkMainDate[link]);
                for ($i=0;$i<count($linkList);$i++) {
                    list ($linkType,$linkId) = explode(":",$linkList[$i]);
                    switch ($linkType) {
                        case "date" : 
                            $dateLinkId = explode(",",$linkId);
                            for ($di=0;$di<count($dateLinkId);$di++) {
                                if ($_SESSION[userLevel] >= 9) echo ("Linked Date from MainId $mainId, $dateLinkId[$di]<br>");
                                cmsCache_deleteId("kalender.php", $dateLinkId[$di], array("short"));
                            }
                            break;
                        case "article" : 
                            // echo ("Linked Artikel for Date $linkId2 <br>");
                            break;
                        
                    }   
                }
            }            
        }               
    }
    
    function editButtons_own($buttonList,$saveData) {
        // $buttonList = array();
        // show_array($saveData);
        
        if ($saveData[link]) {
            $offSetdateLink = strpos($saveData[link],"date:");
            if (is_integer($offSetdateLink)) {
                // echo ("Dont delete Link date<br>");
                if (is_array($buttonList[delete])) {
                    $buttonList[delete][disabled] = 1;
                }
            }
        }
        
        //$buttonList[save][mainButton] = 0;
        //$buttonList[cancel][mainButton] = 1;
        
        $addData = array();
        $addData[type] = "submit";
        $addData["class"] = 'cmsInputButton cmsSecond';
        $addData[name] = "Adresse speichern";
        $addData[value] = "saveAdress";

        if ($saveData[location]) $addData[disabled] = 1;
        $buttonList[saveAdress] = $addData;




        if ($saveData[id]) {
            if ($saveData[mainId]) {
                $buttonList[save][name] = "Termin speichern";
                
                $addData[type] = "link";
                $addData["class"] = "cmsLinkButton";
        
                $addData[name] = "Haupt Termin bearbeiten";
                $addData[value] = "showData";
                $addData[link] = "admin_cmsDates.php?view=edit&id=$saveData[mainId]";

                $buttonList[mainDate] = $addData;

                $buttonList[delete][show] = 0;
                $buttonList[saveAdress][show] = 0;
                
                
            } else {
                $buttonList[save][name] = "Termin speichern";
            }
        } else {
            $buttonList[save][name] = "Termin anlegen";
        }


        
        // Zeige Datensatz
        //if (!$saveData[location]) {
       
        
        $addData[type] = "link";
        $addData["class"] = "cmsLinkButton cmsSecond";
        
        $addData[name] = "Termin Zeigen";
        $addData[value] = "showData";
        $addData[link] = "kalender.php?dateId=$saveData[id]";
        
        $buttonList[show2] = $addData;
        
        return $buttonList;
    }



    function linkList_getArray($linkString) {
        $linkList = explode("|",$linkString);
        for ($i=0;$i<count($linkList);$i++) {
            if ($linkList[$i]) {
                $ofSet = strpos($linkList[$i],":");
                $first = substr($linkList[$i],0,$ofSet);
                $second = substr($linkList[$i],$ofSet+1);
                $linkData[$first] = $first.":".$second; // explode(",",$second);
            }
        }
        return $linkData;
    }

    function linkList_getString($linkData) {
        $linkString = "";
        foreach($linkData as $key => $value) {
            if ($value) {
                if ($linkString != "") $linkString .= "|";
                $linkString .= $value;
            }
        }
        return $linkString;
    }


    function editShowInput_special_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        //  echo ("$key $code $dataName<br>");

        // echo ("<input type='text' name='$dataName' value='$code'>");
        $linkData = $this->linkList_getArray($code);
        //echo ("$key => $linkData $code <br>");
        // foreach ($linkData as $key => $value) echo ("$key = $value <br>");


        switch ($key) {
            case "article" :
                $code = $linkData[$key];
                $res = $this->editShowInput_articles($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                return $res;
                break;
            case "date" :
                if (substr($code,0,8) == "dateMain" ) {
                    $res = $this->editShowInput_mainDates($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                    return $res;
                }
                $code = $linkData[$key];
                
                // foreach ($linkData as $k => $v) echo ("linkData $k = $v <br>");
                // echo ("$code <br>");
                $res = $this->editShowInput_dates($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                return $res;
                break;
            
            case "dateMain" :
                $code = $linkData[$key];
                $res = $this->editShowInput_mainDates($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                return $res;
                break;
            default :
                echo ("Unkown Key $key in editShowInput_special_own <br>");
        }
    }


    function editShowInput_articles($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        echo ($titleStr);

         if (strlen($code)) {
            $articleId = substr($code,strpos($code,":")+1);
            // echo ("ArticleId $articleId<br>");
        }

        $divData = array();
        $divData[style] = "width=".($width-10)."px;display:inline-block;";
        $divData[locationId] = 623;
        $divData[date] = "2012-08-01";
        $divData[articlesUrl] = "/cms_$GLOBALS[cmsVersion]/getData/articlesData.php?cmsVersion=$GLOBALS[cmsVersion]&cmsName=$GLOBALS[cmsName]&dataName=linkArticle&out=dropdown";
        $divData[articleId] = $articleId;
        div_start("articleLink_box",$divData);
        if ($_SESSION[showLevel] >= 9) {
            echo ("<input type='text' name='link[article]' value='$code' readonly='readonly' style='width:".$width."px;'><br>");
        } else {
            echo ("<input type='hidden' name='link[article]' value='$code'>");
        }
       

        $articleName = "Artikel verknüpfen";
        if ($articleId) {
            $articleData = cmsArticles_getById($articleId);
            if (is_array($articleData)) {
                $articleName = $articleData[name];
            }
        }
        div_start("articleSelector","padding:10px;background-color:#fcc;display:inline-block;");
        echo ($articleName);
        div_end("articleSelector");
        
        if ($articleId) {
            $goLink = "admin_articles.php?view=edit&id=$articleId";
            echo ("<a href='$goLink' class='cmsContentHeadInputButton' >editieren</a>");
        }

        div_Start("articleDropdownFrame");
        // $showData = array("empty"=>"Artikel wählen","style"=>"width:".$width."px","class"=>"ArticleDropdown");
        // echo (cmsArticles_selectArticles($articleId,"linkArticle",$showData,array("location"=>$locationId),"fromDate"));
        div_end("articleDropdownFrame");
        
        // echo ("editShowInput_articles($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth)<br>");
       
        
        div_end("articleLink_box");
        $break = 1;

        return array("break"=>$break);
    }

    
    function editShowInput_mainDates($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        echo ($titleStr);
        
        list($linkType,$linkId) = explode(":",$code);
        
        // div_start("linkDate_box","width=".($width)."px;display:inline-block;");
        if ($_SESSION[showLevel] >= 9) {
            echo ("<input type='text' name='link[date]' value='$code' readonly='readonly' style='width:120px;'>");
        } else {
            echo ("<input type='hidden' name='link[date]' value='$code'>");
        }
        
        
        
        
        echo ("<a href='admin_cmsDates.php?view=edit&id=$linkId' class='cmsContentHeadInputButton' >Haupttermin bearbeiten</a>");
        $break = 1;
        return array("break"=>$break);
    }
    
    function editShowInput_dates($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        echo ($titleStr);
        div_start("linkDate_box","width=".($width)."px;display:inline-block;");
        // echo ("$key $code $value <br>");
        $dateList = array();
        $linkSearch = $value[showName];
        if ($code) {
            //echo ("$code <br>");
            $splitList = explode(":",$code);
            // echo ("id s = $splitList[1] <br>");
            
            
            if ($splitList[1] == "") {
                echo ("No DateLink $code <br>");
                $code = "";
            } else {
                
            }
            
            $splitList = explode(",",$splitList[1]);
            $idList = array();
            for ($i=0;$i<count($splitList);$i++) {
                $linkId = intval($splitList[$i]);
                // echo ("Found LinkId '$linkId' <br>");
                $idList[$linkId] = 0;
            }
        }
        if ($linkSearch) {
            $linkSearch = "dateMain:".$linkSearch;
            $dateList = cmsDates_getList(array("link"=>$linkSearch),"date");

        }
        
        
        $linkedDateStr = "";
        for ($i=0;$i<count($dateList);$i++) {
            $dateData = $dateList[$i];
            $id = $dateData[id];
            $idList[$id] = 1;
            
            if (is_array($dateData)) {
                div_start("linkDate_$id","width=".($width)."px;display:inline-block;");
                
                
                if ($linkedDateStr) $linkedDateStr .= ",";
                $linkedDateStr .= $id;
                
                
                $date = $dateData[date];
                $time = $dateData[time];
                $subName = $dateData[subName];

                $day = substr($date,8,2);
                $month = substr($date,5,2);
                $year = substr($date,0,4);
                
                $hour = substr($time,0,2);
                $min  = substr($time,3,2);
                $sec  = substr($time,6,2);
                
                echo ("Datum: <input type='text' name='linkDate[".$id."][day]' value='$day' style='width:20px;' >");
                echo ("<input type='text' name='linkDate[".$id."][month]' value='$month' style='width:20px;' >");
                echo ("<input type='text' name='linkDate[".$id."][year]' value='$year' style='width:40px;' >");

                echo ("&nbsp; Uhrzeit: <input type='text' name='linkDate[".$id."][hour]' value='$hour' style='width:20px;' >");
                echo ("<input type='text' name='linkDate[".$id."][min]' value='$min' style='width:20px;' >");

                echo (" Zusatz: <input type='text' name='linkDate[".$id."][subName]' value='$subName' style='width:180px;' >");
                echo ("<input type='submit' class='cmsContentHeadInputButton' value='löschen' name='delSubDate_$id'>");
                echo ("<a href='admin_cmsDates.php?view=edit&id=$id' class='cmsContentHeadInputButton' >edit</a>");
                div_end("linkDate_$id","width=".($width)."px;");
                
            } else {
                echo ("<h1>No Data for id $id <br></h1>");
            }

        }
        if ($linkedDateStr) {
            $linkedDateStr = "date:".$linkedDateStr;
        }
        if ($code != $linkedDateStr) {
            // echo ("Found $linkedDateStr <br>");
            $code = $linkedDateStr; 
        }
        
        if ($_SESSION[showLevel] >= 9) {
            echo ("<input type='text' name='link[date]' value='$code' readonly='readonly' style='width:".$width."px;'><br>");
        } else {
            echo ("<input type='hidden' name='link[date]' value='$code'>");
        }
        if (is_array($idList) AND count($idList) ) {
            foreach ($idList as $linkId => $found) {
                if ($found == 0) {
                    echo ("Link ID $linkId -> Found $found <br>");
                    $linkData = cmsDates_getById($linkId);
                    if (is_array($linkData)) {
                        echo ("$linkId $linkData[link] <br>");
                        if ($linkData[link] != "dateMain:".$linkSearch) {
                            echo "Set Link to dateMain:".$linkSearch."<br>";
                            $query = "UPDATE `klappeAuf_cms_dates` SET `link`='$linkSearch' WHERE `id` = $linkId ";
                            $result = mysql_query($query);
                            if (!$result) {
                                echo ("Error in $query <br>");
                            }
                        }
                    } else {
                         cms_errorBox("Termin mit id $linkId nicht gefunden");
                         echo ("<input type='submit' class='cmsContentHeadInputButton' value='löschen' name='delSubDate_$linkId'>");

                    }
                }
            }
        }

        // ADD NEW
        $id = "new";
        div_start("linkDate_$id","width=".($width)."px;display:inline-block;");
        $date = date("Y-m-d"); //  "2012";// $dateData[date];
        $time = "  :00:00";
        $subName = "";

        list($year,$month) = explode("-",$date);
//        $day = substr($date,8,2);
//        $month = substr($date,5,2);
//        $year = substr($date,0,4);
//        
        if($month=="12") {
            $year++;
            $month = "01";
        }
        
       
        $hour = substr($time,0,2);
        $min  = substr($time,3,2);
        $sec  = substr($time,6,2);
        echo ("<h3>Neuer Termin zu diesem Termin anlegen</h3>");
        echo ("Datum: <input type='text' name='linkDate[".$id."][day]' value='$day' style='width:20px;' >");
        echo ("<input type='text' name='linkDate[".$id."][month]' value='$month' style='width:20px;' >");
        echo ("<input type='text' name='linkDate[".$id."][year]' value='$year' style='width:40px;' >");

        echo ("&nbsp; Uhrzeit: <input type='text' name='linkDate[".$id."][hour]' value='$hour' style='width:20px;' >");
        echo ("<input type='text' name='linkDate[".$id."][min]' value='$min' style='width:20px;' >");

        echo (" Termin-Zusatz: <input type='text' name='linkDate[".$id."][subName]' value='$subName' style='width:190px;' >");

        echo ("<input type='submit' class='cmsContentHeadInputButton' value='anlegen' name='addSubDate'>");

        div_end("linkDate_$id","width=".($width)."px;");
        

        div_end("linkDate_box");
        $break = 1;
        
        return array("break"=>$break);        
    }

    function emptyData() {
        $saveData = array();
        $saveData["print"] = 1;
        $saveData['show'] = 1; 
       
        list($year,$month) = explode("-",date("Y-m"));
        $month++;
        if ($month > 12) {
            $month = "01";
            $year++;
        } else {
            if ($month<10) $month = "0".$month;            
        }
        $saveData[date] = "$year-$month-  ";
        $saveData[time] = "  :00";

        return $saveData;
    }
    
    
    
    function checkError_own($error,$saveData,$editShow) {
        if (is_array($error) ) {
            
            // echo ("check Eroor Own <br>");
            foreach ($error as $key => $value) {
                switch ($key) {
                    case "time" :
                        if ($saveData[category] == 327) unset($error[$key]);
                        if ($saveData[category] == 330) unset($error[$key]);                       
                        break;
                     default :   
                         // echo ("$key => $value <br>" );
                }
                
            }
            
        }
        return $error;
    }
    

    function checkData($saveData) {
        //echo ("<h1>Check Data</h1>");
        $data = $saveData[data];
        if (!is_array($data)) $data = array();


        // ADD IMAGE AND TEXT TO DATE IF IS TIP AND HAS NO IMAGE OR TEXT");
        $image = $saveData[image];
        $link  = $saveData[link];
        $tip   = $saveData[highlight];
        $tipText = $saveData[longInfo];
        
        if ($saveData[mainId]) {
            echo ("Check Data with MainId $saveData[mainId]<br>");
        }
        
        
        $search = 0;
        if ($tip AND !$image) {$search =1; $searchImage=1;}
        if ($tip AND !$tipText) {$search =1; $searchText=1;}
        
        echo ("<h1> LINK = '$link' </h1>");

        if ($search AND $link) {
            $linkList = explode("|",$link);
            for ($i=0;$i<count($linkList);$i++) {
                list($linkType,$linkId)=explode(":",$linkList[$i]);
                if ($linkType == "article" AND intval($linkId)) {
                    $linkArticleId = $linkId;
                    $articleData = cmsArticles_getById($linkArticleId);
                }
            }
            if ($linkArticleId AND is_array($articleData)) {
                // echo "Article is linkes $linkId";

                if ($searchImage) {
                    $articleImage = $articleData[image];
                    // echo("SUCHE Image '$image' <= $articleImage <br>");
                    if ($articleImage) {
                        cms_infoBox("Kein Bild im Termin gefunden<br>Übernehme Bild aus Artikel");
                        $saveData[image] = $articleImage;
                        $saveData[dontReload] = 1; 
                        $searchImage = 0;
                    }
                }

                if ($searchText) {
                    $articleText = $articleData[info];
                    // echo ("Suche Text '$tipText' <= $articleText <br>");
                    if ($articleText) {
                        cms_infoBox("Kein Tagestipptext im Termin gefunden<br>Übernehme Text aus Artikel");
                        $saveData[longInfo] = $articleText;
                        $saveData[dontReload] = 1;    
                        $searchText = 0;
                    }
                }
            }
        }
        if ($searchImage OR $searchText) {
            $output = "";
            if ($searchImage) {
                if ($output) $output .= "<br />";
                $output .= "Tagestipp hat kein Bild, wird auf der Startseite nicht angezeigt";
            }
            if ($searchText) {
                if ($output) $output .= "<br />";
                $output .= "Tagestipp hat keinen Text";
            }
            if ($output) $output .= "<br />";
            $output .= "Termin wurde aber gespeichert";
            cms_errorBox($output);
            $saveData[dontReload] = 1;                    
        }
        
        if (intval($saveData[location])) {
//            echo ("<h1>unset DATA street,streetNr,plz city url </h1>");
//            unset ($data[street]);
//            unset ($data[streetNr]);
//            
//            unset ($data[plz]);
//            unset ($data[city]);
//            
//            unset ($data[url]);
//            if (!$data[ticketUrl]) unset($data[ticketUrl]);
        } else {
            // show_array($data);
            // Check Straße HausNummer
            $change = 0;
            $street = trim($data[street]);
            $streetNr = trim($data[streetNr]);
            //echo ("Vorher => '$street' '$streetNr' <br>");
            if ($streetNr) {
                $offSet = strpos($street,$streetNr);
                if ($offSet) {
                    $street = substr($street,0,$offSet);
                    $street = trim($street);
                    $change = 1;
                }
            } else {
                $numStr = str_replace(array("0","1","2","3","4","5","6","7","8","9"),"#",$street);
                //echo ("NumStr = '$numStr<br>");
                $offSet = strpos($numStr,"#");
                if ($offSet) {
                    $streetNr = substr($street,$offSet);
                    $street = substr($street,0,$offSet);
                    $street = trim($street);
                    $streetNr = trim($streetNr);
                    $change = 1;
                }
            }
            if ($change) {
                //echo ("Danach => '$street' '$streetNr' <br>");
                $data[street] = $street;
                $data[streetNr] = $streetNr;
            }
            // End of Street / StreetNr ////////////////////////////////////////////
            // Check plz city //////////////////////////////////////////////////////
            $change = 0;
            $city = trim($data[city]);
            $plz = trim($data[plz]);
            // echo ("Vorher => '$plz' '$city' <br>");
            if ($plz) {
                $isPlzStr = substr($city,0,5);
                if (intval($plz) AND intval($isPlzStr)) {
                    if ($plz == $isPlzStr) {
                        // echo "PLZ is same '$plz' = '$isPlzStr' <br>";
                        $city = substr($city,5);
                        $city = trim($city);
                        $change = 1;
                    }
                }
            } else {
                $isPlzStr = substr($city,0,5);
                if (intval($isPlzStr)) {
                    $plz = $isPlzStr;
                    $city = substr($city,5);
                    $city = trim($city);
                    $change = 1;
                }
            }
            $smallCity = strtolower($city);
            $offKa = strpos($city,"ka");
            if (!is_null($offKa)) {
                $kaPlus = strtolower(substr($city,$offKa,3));
                // echo ("KAPLUS = '$kaPlus' <br>");
                if ($kaPlus == "ka" OR $kaPlus == "ka-" OR $kaPlus == "ka ") {
                    $city = "Karlsruhe".substr($city,$offKa+2);
                    $change = 1;
                }
            }

            if ($change) {
                // echo ("Danach => '$plz' '$city' <br>");
                $data[city] = $city;
                $data[plz] = $plz;
            }
            // End of City / PLZ ///////////////////////////////////////////////////


        }
        
        
        if ($data[ticketUrl]) {
            // http://www.amazon.de/s/ref=nb_sb_noss?__mk_de_DE=%C5M%C5Z%D5%D1&url=search-alias%3Ddigital-music&field-keywords=beth+hart
        }

        $saveData[data] = $data;
        // End of Phone / Fax //////////////////////////////////////////////////
        return $saveData;
    }


    function linkDate_Convert($linkData) {
        $day = intval($linkData[day]);
        if ($day<1 or $day > 31) $day = 0;
        $month = intval($linkData[month]);
        if ($month<1 or $month > 12)$month = 0;
        $year = intval($linkData[year]);
        if ($year>0 and $year<100) $year = $year+2000;
        if ($year < 1800) $year = 0;
        if ($year AND $month AND $day) {
            if ($day < 10) $day = "0".$day;
            else $day ="".$day;
            if ($month < 10) $month = "0".$month;
            else $month ="".$month;
            $date = $year."-".$month."-".$day;
        } else {
            return "Kein Datum in weiteren Termin anlegen";
            //cms_errorBox("Kein Datum in weiteren Termin anlegen");
            //return 0;
        }

        $hour = intval($linkData[hour]);
        if ($hour<0 or $hour > 24) $hour = -1;
        $min = intval($linkData[min]);
        if ($min<0 or $min > 60) $min = -1;
        //echo ("Convert = $hour : $min <br>");
        if ($hour>=0 AND $min >= 0) {
            if ($hour < 10) $hour = "0".$hour;
            else $hour ="".$hour;
            if ($min < 10) $min = "0".$min;
            else $min ="".$min;
            $time = $hour.":".$min.":00";
        } else {
            return "Kein Zeit in weiteren Termin anlegen";
            //cms_errorBox("Kein Zeit in weiteren Termin anlegen");
            //return 0;
        }
        // echo ("Convert = $time <br>");
        $subName = php_clearStr($linkData[subName]);
        return array("date"=>$date,"time"=>$time,"subName"=>$subName);
    }

    function linkArticle($mode,$value,$linkData,$dateId) {
        // echo ("linkArticle($mode,$value,$linkData)<br>");
        switch ($mode) {
            case "set" :
                $articleStr = $linkData[article];
                 // echo ("DateString = $dateStr<br>");
                $articleId = substr($articleStr,strpos($articleStr,":")+1);
                if (intval($articleId)) {
                    echo ("REMOVE DateId = $dateId FROM ARTICLE $articleId <br>");
                    $articleData = cmsArticles_getById($articleId);
                    if (is_array($articleData)) {
                        $article_link = $articleData[link];
                        //echo ("link = $article_link<br>");
                        $article_linkData = $this->linkList_getArray($article_link);
                        show_array($date_linkData);
                        $article_linkData[date] = "";
                        $article_link = $this->linkList_getString($article_linkData);
                        //echo ("new LinkString for Date $date_link <br>");
                        $query = "UPDATE `$GLOBALS[cmsName]_cms_articles` SET `link`='$article_link' WHERE `id`=$articleId";
                        $result = mysql_query($query);
                        if (!$result) {
                            cms_errorBox("Error in Update ARTICLES by Remove Date fron article<br>$query");
                        }
                    }
                }


                if ($value) { 
                    
                    $articleId = $value;
                    if (intval($articleId)) { // SETZE ARTICLE TO Date
                        $articleData = cmsArticles_getById($articleId);
                        if (is_array($articleData)) {
                            $article_link = $articleData[link];

                            // echo ("link = $article_link<br>");
                            $article_linkData = $this->linkList_getArray($article_link);
                            $article_linkData[date] = "date:$dateId";
                            show_array($date_linkData);
                            $article_link = $this->linkList_getString($article_linkData);
                            // echo ("new LinkString for Article: $article_link <br>");
                            $query = "UPDATE `$GLOBALS[cmsName]_cms_articles` SET `link`='$article_link' WHERE `id`=$articleId";
                            $result = mysql_query($query);
                            if (!$result) {
                                cms_errorBox("Error in Update Article by ADD Article to Date <br>$query");
                            }
                        }
                    }

                    $articleLink = "article:".$value;
                } else {
                    $articleLink = "";
                }
                $linkData[article] = $articleLink;

                $linkString = $this->linkList_getString($linkData);
                // echo ("<h1>New LinkString = '$linkString' </h1>");
                return ($linkString);
                break;
        }
        
    }

    function linkDate($mode,$linkDateList,$linkData,$saveData,$deleteId=0) {
        // echo ("linkDate($mode,$linkDateList,$linkData,$saveData,$deleteId)<br>");
        // show_array($linkDateList);
        // show_array($linkData);
        
        $linkStr = "";
        $dateLinkStr = "";
        if (is_array($linkData[date]) AND count($linkData[date])) {
            foreach ($linkData[date] as $linkId => $linkData) {
                $linkId = intval($linkId);
                if ($linkId > 0) {
                    if ($dateLinkStr != "")  $dateLinkStr .=",";
                    $dateLinkStr .= $linkId;
                    // echo ("LinkList $linkId = $linkData <br>");
                }
            }
        }

       
        switch ($mode) {
            case "check" :
                // echo ("<h1> CHECK </h1>");
                $changeAll = 0;
                foreach ($linkDateList as $linkId => $linkData) {
                    $linkId = intval($linkId);
                    if ($linkId>0) {
                        $linkData = $this->linkDate_Convert($linkData);
                        if (is_array($linkData)) {
                            $date = $linkData[date];
                            $time = $linkData[time];
                            $subName = $linkData[subName];
                            $location = $saveData[location];
                            $category = $saveData[category];
                            $region = $saveData[region];
                            // echo ("Datum = $date <br>");
                            // echo ("Zeit = $time<br>");
                            // echo ("SubName = $subName<br>");
                            $dateData = cmsDates_getById($linkId);
                            if (is_array($dateData)) {
                                $changes = 0;
                                if ($dateData[location]!=$location) {
                                    echo ("change $location -> location from $dateData[location] to $location <br>");
                                    $changes++;
                                }
                                if ($dateData[category]!=$category) {
                                    echo ("change $category -> category from $dateData[category] to $category <br>");
                                    $changes++;
                                }
                                if ($dateData[region]!=$region) {
                                    echo ("change $region -> region from $dateData[region] to $region <br>");
                                    $changes++;
                                }

                                if ($dateData[date]!=$date) {
                                    echo ("change $linkId -> date from $dateData[date] to $date <br>");
                                    $changes++;
                                }
                                if ($dateData[time]!=$time) {
                                    echo ("change $linkId -> time from $dateData[time] to $time <br>");
                                    $changes++;
                                }
                                if ($dateData[subName]!=$subName) {
                                    echo ("change $linkId -> subName from $dateData[subName] to $subName <br>");
                                    $changes++;
                                }
                                
                                if ($dateData[show]!=$saveData[show]) {
                                    echo ("change $linkId -> show from $dateData[show] to $saveData[show] <br>");
                                    $changes++;
                                }

                                if ($changes) {
                                    $query = "UPDATE `$GLOBALS[cmsName]_cms_dates` SET `date`='$date', `time`='$time', `link`='dateMain:$saveData[id]', `subName`='$subName' ";
                                    $query .= ", `location`=$saveData[location], `region`='$saveData[region]', `category`='$saveData[category]'";
                                    $query .= ", `show`='$saveData[show]'";
                                    $query .= ", `lastMod`='".$this->query_addLastMod()."'";
                                    $query .= ", `changeLog`='".$this->query_addChangeLog($mode,$dateData[changeLog])."'";
                                    $query .= " WHERE `id`=$linkId ";
                                    $result = mysql_query($query);
                                    if (!$result) {
                                        $out = "Fehler bei weiteren Termin anlegen";
                                        if ($_SESSION[userLevel]==9) $out .= "<br>$query";
                                        cms_errorBox($out);                                       
                                        return 0;
                                    }
                                    $changeAll++;
                                }
                            }

                        }
                    }
                }
                // echo ("<h1> LINKDATE CHECK = $changeAll </h1>");
                return $changeAll;
                break;

            case "delete" :
                echo ("Lösche verknüpfter Termin mit Id = $deleteId<br>");
                $query = "DELETE FROM `$GLOBALS[cmsName]_cms_dates` WHERE `id` = $deleteId";
                $result = mysql_query($query);
                if (!$result) {
                    $out = "Fehler beim löschen eines weiteren Termins";
                    if ($_SESSION[userLevel]==9) $out .= "<br>$query";
                    cms_errorBox($out);
                    return 0;
                }

                $dateLinkStr = $linkData[date];
                if ($dateLinkStr) {
                    $dateLinkStr = subStr($dateLinkStr,strpos($dateLinkStr,":")+1);
                }

                //   echo ("LinkList = '$dateLinkStr' <br>");
                $list = explode(",",$dateLinkStr);
                $dateLinkStr = "";
                for ($i=0;$i<count($list);$i++) {
                    $linkId = intval($list[$i]);
                    if ($linkId > 0 AND $linkId != $deleteId) {
                        if ($dateLinkStr != "")  $dateLinkStr .=",";
                        $dateLinkStr .= $linkId;
                    }
                }
                // echo ("LinkList after Remove $deleteId = '$dateLinkStr' <br>");

               
                if ($dateLinkStr) { 
                     $linkData[date] = "date:".$dateLinkStr;
                } else { //empty
                    $linkData[date] = "";
                    echo ("Kein verknüpfter Termin mehr vorhanden<br>");
                }

                $linkString = "";
                foreach($linkData as $key => $value) {
                    if ($value) {
                        if ($linkString != "") $linkString .= "|";
                        $linkString .= $value;
                    }
                }
                echo ("<h1>New LinkString = '$linkString' </h1>");
                return $linkString;
                break;

            case "add" :
                $dateLinkStr = $linkData[date];
                if ($dateLinkStr) {
                    $dateLinkStr = substr($dateLinkStr,strpos($dateLinkStr,":")+1);
                }
//                echo ("<h1> = $dateLinkStr</h1>");

                //show_array($linkDateList);
                $linkAdd = $linkDateList["new"];
                if (!is_array($linkAdd)) {
                    cms_errorBox("keine Daten erhalten in 'weiteren Termin anlegen'");
                    return 0;
                }
                $linkAdd= $this->linkDate_Convert($linkAdd);
                if (!is_array($linkAdd)) {
                    cms_errorBox("keine Daten erhalten in 'weiteren Termin anlegen'");
                    return 0;
                }
                $addId = $saveData[id];
                $date = $linkAdd[date];
                $time = $linkAdd[time];
                $subName = $linkAdd[subName];
//                echo ("Datum = $date <br>");
//                echo ("Zeit = $time<br>");
//                echo ("MainId = $addId<br>");
//                echo ("SubName = $subName<br>");
               
                global $cmsName;
                $query = "INSERT INTO `$GLOBALS[cmsName]_cms_dates` SET `date`='$date', `time`='$time', `link`='dateMain:$addId', `subName`='$subName' ";
                $query .= ", `show`='$saveData[show]'";
                $query .= ", `location`='$saveData[location]', `region`='$saveData[region]', `category`='$saveData[category]'";
                $query .= ", `lastMod`='".$this->query_addLastMod()."'";
                $query .= ", `changeLog`='".$this->query_addChangeLog($mode,"")."'";
                $result = mysql_query($query);
                if (!$result) {
                    $out = "Fehler bei weiteren Termin anlegen";
                    if ($_SESSION[userLevel]==9) $out .= "<br>$query";
                    cms_errorBox($out);
                    return 0;
                }
                $insertId = mysql_insert_id();
                // echo ("InsertId = $insertId <br>");

                if ($dateLinkStr != "")  $dateLinkStr .=",";
                $dateLinkStr .= $insertId;

                $dateStr = cmsDate_getDayString($date, 1);
                $timeStr = cmsDate_getTimeString($time, 2);
                cms_infoBox("Weitere Termin am $dateStr um $timeStr Uhr angelegt.");
                $dateLinkStr = "date:$dateLinkStr";

                $linkData[date] = $dateLinkStr;
                $linkString = "";
                foreach($linkData as $key => $value) {
                    if ($value) {
                        if ($linkString != "") $linkString .= "|";
                        $linkString .= $value;
                    }
                }
                // echo ("<h1>New LinkString = '$linkString' </h1>");
                return $linkString;
                break;
            default :
                echo ("unkown Mode $mode in linkDate <br>");

        }
    }


 
}











?>
