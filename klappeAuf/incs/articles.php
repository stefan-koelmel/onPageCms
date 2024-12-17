<?php // charset:UTF-8

function articles_getLink($link,$set,$to=0) {
    if (!is_array($set)) {
        $setList[$set] = array("value"=>$to,"found"=>0);
    } else {
        $setList = array();
        foreach ($set as $key=> $value) {
            $setList[$key] = array("value"=>$value,"found"=>0);
        }
    }

    $goPage = "";
    $found = 0;
    foreach ($_GET as $key => $value) {
        // replace setValue
        $add = 1;
        if (is_array($setList[$key])) {
            $add=0;
            $setList[$key][found] = 1;
        }
        if ($add AND $value) {
            if ($goPage == "") $goPage.="?";
            else $goPage.="&";
            $goPage .= $key."=".$value;
        }
    }

    foreach($setList as $key => $data) {
        $value = $data[value];
        $found = $data[found];
        // NOT IN GETLIST and $to has value
        if ($value) {
            if ($goPage == "") $goPage.="?";
            else $goPage.="&";
            $goPage .= $key."=".$value;
        }
    }

    // add LINK
    $goPage = $link.$goPage;
    return htmlspecialchars($goPage);
}

function articles_showFilter($selectedMainCatId,$selectedSubCatId,$link) {
    // headLine
    
    $showBlock = 1;
    $useX = 1;
    $headline = "Artikel";
    
    echo ("<h1>$headline</h1>");
    $showBlock = 1;
    $class = "current-info-container clearfloat ";
    if ($showBlock) $class .= " filterBlock";
    
    echo ("<div class='$class'>");
    if ($showBlock) echo ("<h4>Anzeige filtern nach:</h4>");
    
    
    
    $catList = categoryGetList("RubrikList");
    unset($catList[332]); // PR-Texte
  
    
    $doubleSelect = 0;

    if ($selectedMainCatId>0) { // Hauptkategorie ausgewählt
        
        $catName = $catList[$selectedMainCatId][name];
        $subCatList = categoryGetList("RubrikSubList_".$selectedMainCatId);
        
        if ($showBlock) {
            $mainCatGo = $catList[$selectedMainCatId][shortName];
            if (!$doubleSelect) $shortName = "subCat";
            echo ("<span class='filterType'>Rubriken:</span>");
            echo ("<a href='#' class='change-rubrik' title='Filter nach Rubriken'>$catName</a> ");
            $goLink = articles_getLink($link,array("artCat"=>0,"subCat"=>0));
            $removeClass = "change-current";
            $removeStr = "Filter entfernen";
            if ($useX) {
                $removeClass .= " remove-current";
                $removeStr = "x";
            } else {
                echo ("<span class='quo'>&rsaquo;</span> ");
            }            
            echo (" <a href='$goLink' class='$removeClass' title='Filter entfernen - Alle Rubriken zeigen'>$removeStr</a>");
            // echo (" <a href='$goLink' class='$removeClass' title='Filter entfernen - Alle Kategorien zeigen'>$removeStr</a>");
            
            if (count($subCatList)) { // Unterkategorien existieren
            
                if ($selectedSubCatId>0) { 
                    $subCatName = $subCatList[$selectedSubCatId][name];
                    echo ("<br />");
                    echo ("<span class='filterType'>Unterrubrik:</span>");
                    echo ("<a href='#'  id='$shortName' class='select-filter-link change-".$shortName."' title='Filter nach Unterrubriken'>$subCatName</a> ");
                    $goLink = articles_getLink($link,array("subCat"=>0));
                    $removeClass = "change-current";
                    $removeStr = "Filter entfernen";
                    if ($useX) {
                        $removeClass .= " remove-current";
                        $removeStr = "x";
                    } else {
                        echo ("<span class='quo'>&rsaquo;</span> ");
                    }            
                    echo (" <a href='$goLink' class='$removeClass' title='Filter entfernen - Alle Unterrubriken zeigen'>$removeStr</a><br />");
                } else {
                    echo ("<br />");
                     echo ("<span class='filterType'>Unterrubrik:</span>");
                    $className = "select-filter-link change-".$shortName;
                    echo("<a href='#' class='$className' id='$shortName' title='Filter nach Unterrubriken' >Alle Unterrubriken</a><br />");
                }
            } else {
                // keine Unterrubriken
            }
        } else {
            $mainCatGo = $catList[$selectedMainCatId][shortName];
            echo ("$catName ");
            echo ("<a href='#' class='change-current change-rubrik'>(&auml;ndern)</a>");
            $goLink = articles_getLink($link,array("artCat"=>0,"subCat"=>0));
            echo ("<a href='$goLink' class='change-current'>(l&ouml;schen)</a> ");

            if (count($subCatList)) { // Unterkategorien existieren
                echo ("<span class='quo'>&rsaquo;</span> ");
                if (!$doubleSelect) $shortName = "subCat";
                // Unterkategorien
                if ($selectedSubCatId>0) { // Unterkategorie ausgewählt
                    $subCatName = $subCatList[$selectedSubCatId][name];
                    //$subCatName = cmsCategory_getName_byId($selectedSubCatId);
                    echo ("$subCatName ");
                    $className = "change-current select-filter-link change-".$shortName;
                    echo ("<a href='#' id='$shortName' class='$className' >(&auml;ndern)</a>");
                    $goLink = articles_getLink($link,array("subCat"=>0));
                    echo ("<a href='$goLink' class='change-current'>(l&ouml;schen)</a> ");
                } else {
                    $className = "change-current select-filter-link change-".$shortName;
                    echo("Alle Unterrubriken <a href='#' class='$className' id='$shortName'>(&auml;ndern)</a>");
                }
            } else {
                // keine Unterrubriken
            }
        }
            
            
            
            
        
        
        

    } else { // keine Auptkategorie ausgewählt
        if ($showBlock) {
            echo ("<span class='filterType'>Rubriken:</span>");
            echo("<a href='#' class='change-rubrik' title='Filter nach Rubriken'>Alle Rubriken</a>");
        } else {
            echo("Alle Rubriken <a href='#' class='change-current change-rubrik'>(&auml;ndern)</a>");
        }
    }
    echo("</div>");

    
            
    // Hauptkategorien
    if (is_array($catList) AND count($catList)) {
        echo("<div class='select-container select-mainrubrik clearfloat'>");
        echo("<div class='select-title'>Rubriken");
        if ($selectedMainCatId) {
             $goLink = articles_getLink($link,array("artCat"=>0,"subCat"=>0));
             echo (" <a href='$goLink' class='change-current'>(alle Rubriken zeigen)</a> ");
        }
        echo ("<a href='#' class='close_this_container'>&#215;</a></div>");
        $nr = 0;
        $nrPerLine = 3;
        
        foreach ($catList as $catId => $catValue) {
            //$i=0;$i<count($catList);$i++) {
            $catName = $catValue[name];
            $mainShortName = $catValue[shortName];
           // $catId   = $catList[$i][id];
            $show = 1;
            switch ($catId) {
                case 319; $show = 1; break;
            }

            if ($show) {
                // Create Short Name for Style , maybe link
                $nr++;

                $className = "select-link"; // select-filter-link";
                if ($doubleSelect) $className .= " select-filter-link";

                if ($nr >= $nrPerLine) $className .= " select-link-last";


                // Category Selected
                if ($selectedMainCatId == $catId) {
                    $className .= " select-selected";
                    $goLink = articles_getLink($link,array("artCat"=>0,"subCat"=>0));
                } else {
                    $goLink = articles_getLink($link,array("artCat"=>$mainShortName,"subCat"=>0));
                }

                $idStr = "";
                if ($doubleSelect) $idStr = "id='$mainShortName'";

                echo("<a href='$goLink' class='$className' $idStr >$catName</a>");


                if ($nr >= $nrPerLine) {
                    $nr = 0;
                }
            }


        }
        echo ("</div>\n");

        // Single Select
        if ($selectedMainCatId) {
            $catName = $catList[$selectedMainCatId][name];
            
            if (is_array($subCatList) AND count($subCatList)) {
                $nr = 0;
                $nrPerLine = 3;

                echo("<div class='select-container select-subCat clearfloat' style='display:block;'>");
                echo("<div class='select-title'>Unterrubrik von $catName");
                if ($selectedSubCatId) {
                    $goLink = articles_getLink($link,array("subCat"=>0));
                    echo (" <a href='$goLink' class='change-current'>(alle Unterrubriken zeigen)</a> ");
                }
                echo ("<a href='#' class='close_this_container'>&#215;</a></div>");

                
                foreach ($subCatList as $subCatId => $subCatData) {
                    $subCatName = $subCatData[name];
                    $subShortName = $subCatData[shortName];
                    $nr++;

                    $className = "select-link";
                    if ($nr >= $nrPerLine) $className .= " select-link-last";

                    // Sub Category Selected
                    if ($selectedSubCatId == $subCatId) {
                        $className .= " select-selected";
                        $goLink = articles_getLink($link,array("artCat"=>$mainCatGo,"subCat"=>0));
                    } else {
                        $goLink = articles_getLink($link,array("artCat"=>$mainCatGo,"subCat"=>$subShortName));
                    }


                    echo("<a href='$goLink' class='$className' >$subCatName</a>");


                    if ($nr >= $nrPerLine) {
                        $nr = 0;
                    }


                }
                echo ("</div>");
            }
        }


    }

    echo("<div class='slidespacer'>&nbsp;</div>");
    echo ("</div>\n");
    echo ("</div>\n");
    echo("<p />");
 
}



function articles_articleList($selectedMainCatId,$selectedSubCatId,$link) {
    $filter = array();
    switch ($selectedMainCatId) {
        case 328 : // Vorshau
            $filter[fromDate] = ">='".date("Y-m-d")."'";
            $selectedMainCatId = 0;
            break;
        case 319;
            $filter[date] = date("Y-m-d");
            if ($selectedMainCatId) $filter[category] = $selectedMainCatId;
            if ($selectedSubCatId) $filter[subCategory] = $selectedSubCatId;
            $selectedMainCatId = 0;
        default :
            
            $filter[date] = date("Y-m-d");
            if ($selectedMainCatId) $filter[category] = $selectedMainCatId;
            if ($selectedSubCatId) $filter[subCategory] = $selectedSubCatId;
    }
    
    
    // $filter = array("dateRange"=>"2012-06");


    

    // No FILTER SET ??????
    if (count($filter) == 0) {
        // return 0;
    }
    
    // Set Default Link, if not set
    if (!$link) $link = 'ausgabe.php';


    // show Only Active Articles
    $filter[show] = 1;
    
    $sort = "highlight_up";
    $sort = "name";
    // $filter[date] = date("Y-m-d");
   //  show_array($filter);

    if (!$link) $link = "adressen.php";
    
    $useCache = cmsCache_state();
    $useSingleCache = 1;
    if ($useCache) {
        if ($useSingleCache) {
            $replaceSave = cmsCache_replaceStr_save();
            $replaceGet = cmsCache_replaceStr_get();
            $cachePath = cmsCache_getPath($link);
        } else {
            $cachedText = cmsCache_get($link, $filter, $sort);
            if ($cachedText) {
                echo ($cachedText);
                return 0;
            } 
        }
    }
    
    switch ($selectedMainCatId) {
        case 318 : // Gastronomie
            switch ($selectedSubCatId) {
                case 320 : // Kneipen Cafes  
                    $locationCat = 18;
                    $filterLocation[category] = 18;
                    break;
                case 321 : // Restaurants
                    $locationCat = 22;
                    $filterLocation[category] = 22;
                    break;
                case 322 : // Biergärten
                    $locationCat = 20;
                    $filterLocation[category] = 20;
                    break;
                case 323 : // Clubs
                    $locationCat = 21;
                    $filterLocation[category] = 21;
                    break;
                case 324 ; // Neueröffnungen
                    break;
                    
                default :
                    $locationCat = "gastro";
            }            
            break;
    }
    
    $locationShow = array();
    if ($locationCat AND $locationCat != "gastro") {
        
        $filterLocation = array();
        $filterLocation[show] = 1;
        $filterLocation[category] = $locationCat;
        $locationList = cmsLocation_getList($filterLocation,"name");
        for ($i=0;$i<count($locationList);$i++) {
            $location = $locationList[$i];
            $locationId = $location[id];
            $locationName = $location[name];
            $locationText = $location[data]["info_".$locationCat];
//            if (!$locationText) {
//                $locationText = $location[info];
////                if (!$locationText) {
////                    if ($location[data]["info_18"]) $locationText = $location[data]["info_18"];
////                    if ($location[data]["info_20"]) $locationText = $location[data]["info_20"];
////                    if ($location[data]["info_21"]) $locationText = $location[data]["info_21"];
////                    if ($location[data]["info_22"]) $locationText = $location[data]["info_22"];
////                }
//               // echo ("kein Text  $locationId - > $locationText <br>");
//            }
            $show = 1;
//            if ($locationCat == "gastro") {
//                $show = 0;
//                $locCat = $location[category];
//                if (intval($locCat)) $locCat = "|$locCat|";
//                $locCatList = explode("|",$locCat);
//
//                // echo ("gastro $locationName $locCat ");
//                for ($c=1;$c<count($locCatList)-1;$c++) {
//                    $locCatId = $locCatList[$c];
//                    if ($locCatId == 18) $show = 1;
//                    if ($locCatId == 20) $show = 1;
//                    if ($locCatId == 21) $show = 1;
//                    if ($locCatId == 22) $show = 1;
//                   // echo ("$c = $locCatId ");
//                }
//          }
//            if (!$locationText) {
//                if ($location[data]["info_18"]) {
//                    $locationText = $location[data]["info_18"];
//                    $location[info] = $locationText;
//                    $locationShow[] = $location;
//                }
//                if ($location[data]["info_20"]) {
//                    $locationText = $location[data]["info_20"];
//                    $location[info] = $locationText;
//                    $locationShow[] = $location;
//                }
//                if ($location[data]["info_21"]) {
//                    $locationText = $location[data]["info_21"];
//                    $location[info] = $locationText;
//                    $locationShow[] = $location;
//                }
//                if ($location[data]["info_22"]) {
//                    $locationText = $location[data]["info_22"];
//                    $location[info] = $locationText;
//                    $locationShow[] = $location;
//                }
//
//                $show = 0;
//            }
//
            if ($show) {
                $location[info] = $locationText;
                $locationShow[] = $location;
            }
        }
    }
    
    if (!$filter[category]) $filter[category] = "!=332";
    
    $articleList = cmsArticles_getList($filter,$sort,"out_");
    
    if (is_array($articleList) AND count($articleList)==0) {
        if (count($locationShow)==0) {
            if ($locationCat != "gastro") {
                echo ("<div class='noData' >");
                echo ("Keine Artikel für diese Auswahl vorhanden!");
                echo ("</div>");
                return "";
            }
        }
    }
    
    $out = "";
    
    
    // GET CATEGORY LIST FOR MAIN CATEGORIES
    $catList = categoryGetList("RubrikList");
    
    // GET SUB CATEGORY LIST FOR SELETED MAIN CATEGORIES
    if ($selectedMainCatId) {
        $subCatList = categoryGetList("RubrikSubList_".$selectedMainCatId);                
    }
    
    
    // show_array($catList);
    
    $randomImage = 0;

    // Variables for DIV / LINE BREAK;
    $nr = 0;
    $nrPerLine = 3;

    // SHOWDATA FOR IMAGES
    $imageWidth = 228;
    $imageShowData = array();
    $imageShowData[frameWidth] = $imageWidth;
    $imageShowData[frameHeight] = floor($imageWidth / 4 * 3);
    $imageShowData[ratio] = 4 / 3;
    $imageShowData[vAlign] = "bottom";
    $imageShowData[hAlign] = "center";

    $themesLineNr = 0;
    
    
  

    if ($locationCat == "gastro")  {
        $nr = 0;
        $gastroList = array("kneipen"=>"Kneipen und Cafés","restaurants"=>"Restaurants","biergärten"=>"Biergärten");
        foreach ($gastroList as $shortName => $longName) {
            $nr++;
            if ($nr == 1) {
                $themesLineNr++;
                $out .= "<div class='themes_container' id='themeboxes-$themesLineNr'>";
            }

            $divName = "content_box theme_box";
            if ($nr >= $nrPerLine) $divName .= " theme_box_last";

            $out .= "  <div class='$divName'>\n";
            $out .= "      <div class='theme_box_inner tb_inner_home ' style='min-height:60px;'>\n"; //autoheight
            $out .= "          <div class='theme_header tb_gastronomie'>\n";
            $out .= "              $longName";
            $out .= "          </div>\n";

            // BOX Content
            $out .= "      <div class='tb_content boxlink'>\n";
            $out .= "<div class='tb_content_txt'>\n";
            $out .= "Gastroführer für <b>$longName</b>";
            $goPage = articles_getLink($link,array("artCat"=>"gastronomie","subCat"=>"$shortName"));

            $out .= "              <div class='hidden_url'><a href='$goPage'>Link zum Artikel</a></div>\n";
            $out .= "          </div>\n";
            $out .= "      </div>\n";

            $out .= $outArticle;
            $out .= "  </div>\n";
            $out .= "</div>\n";

           
            if ($nr >= $nrPerLine) {
                $out .= "<div class='clearleft'></div>";
                $out .= "</div>";
                $nr = 0;
            }
        }
        echo ($out);
        $out = "";
    }
    
    
    if (is_array($articleList) AND count($articleList)) {
         // echo ("<h1>Artikel Liste</h1>\n");
         // echo ("Anzahl".count($articleList)."<br />");

        // Main Cat Set - Set $shortName for Colorize Header
        if ($selectedMainCatId) {
            $catName = $catList[$selectedMainCatId][name];
            $shortName = $catList[$selectedMainCatId][shortName];            
        }

        // Take Random Image if more than one exist
     
        for($i=0;$i<count($articleList);$i++) {
            $article     = $articleList[$i];            
            $articleId   = $article[id];
            $name        = php_clearOutPut($article[name],0);
            $subName     = php_clearOutPut($article[subName],0);
            $info        = php_clearOutPut($article[info],0);
            $image       = $article[image];

            $mainCatId       = $article[category];
            $subCatId        = $article[subCategory];

            // Anzahl an Zeichen
            $maxLength = 300;


            if ($selectedMainCatId) {
                
                $catName = $subCatList[$subCatId][name];
             
            } else {
                $catName   = $catList[$mainCatId][name];
                $shortName = $catList[$mainCatId][shortName];             
            }

            $nr++;
            // erstes Element -> Öffne themes_container
            if ($nr == 1) {
                $themesLineNr++;
                $out .= "<div class='themes_container' id='themeboxes-$themesLineNr'>";               
            }

            $divName = "content_box theme_box";
            if ($nr >= $nrPerLine) $divName .= " theme_box_last";

            $out .= "  <div class='$divName'>\n";
            
            $out .= "      <div class='theme_box_inner tb_inner_home autoheight'>\n";

            // Box Head
            $out .= "          <div class='theme_header tb_$shortName'>\n";
            $out .= "              ".$catName."$catId\n";         
            $out .= "          </div>\n";
            
            
            
            $outArticle = "";
            if ($useCache AND $useSingleCache) {
                $cacheFile = cmsCache_getFileName($link,"$articleId","box");
                if (file_exists($cachePath.$cacheFile)) {
                    $outArticle = loadText($cachePath.$cacheFile);
                    if (is_array($replaceGet)) $outArticle = str_replace($replaceGet[0],$replaceGet[1],$outArticle);
                }
            } 
            if (!$outArticle) {
                
                // BOX Content
                $outArticle .= "      <div class='tb_content boxlink'>\n";
                if ($image) {
                    if (intval($image) > 0) $imageId = $image;
                    else {
                        $imageList = explode("|",$image);
                        if ($randomImage) {
                            $takeImageNr = rand(1,count($imageList)-2);

                            $imageId = $imageList[$takeImageNr];
                            // $out .= "Random $takeImageNr von ".count($imageList)." - $imageId<br />");
                        } else {
                            $imageId = $imageList[1];
                        }
                    }
                    $imageData = cmsImage_getData_by_Id($imageId);
                    if ($imageData) {
                        $imgStr = cmsImage_showImage($imageData,$imageWidth,$imageShowData);
                        $outArticle .= $imgStr;
                    } else {
                        // no Image
                        $maxLength = $maxLength + 400;
                    }
                } else {
                    // no Image
                    $maxLength = $maxLength + 400;
                }
                $outArticle .= "<div class='tb_content_txt'>\n";

                if ($name) {
                    $outArticle .= "<h3>$name</h3>";
                    $maxLength = $maxLength - (3*strlen($name));
                }
                if ($subName) {
                    $outArticle .= "<h4>$subName</h4>";
                    $maxLength = $maxLength - (2*strlen($subName));
                }

                if ($maxLength <= 50) $maxLength = 50;

                if (strlen($info)>$maxLength) {
                    $endPos = strpos($info," ",$maxLength);
                    if ($endPos) $info = substr($info,0,$endPos)." ...";
                }
                $outArticle .= "$info";
                $goPage = articles_getLink($link,array("articleId"=>$articleId));


                $outArticle .= "              <div class='hidden_url'><a href='$goPage'>Link zum Artikel</a></div>\n";
                $outArticle .= "          </div>\n";
                $outArticle .= "      </div>\n";
                
                
                if ($useCache AND $useSingleCache) {
                    if (is_array($replaceSave)) {
                        $outArticle = str_replace($replaceSave[0],$replaceSave[1],$outArticle);
                    }   
                    saveText($outArticle,$cachePath.$cacheFile);
                    if ($_SESSION[userLevel] >= 9) $outArticle.= "<span style='color:#f00;'>Cache File $cacheFile created </span><br>";
                }
                
            }
            
            $out .= $outArticle;
            $out .= "  </div>\n";
            
            $out .= "</div>\n";

            if ($nr >= $nrPerLine) {
                $out .= "<div class='clearleft'></div>";
                $out .= "</div>";
                $nr = 0;
            }
        }
    }
    
    
    
   
    
    
       
    if (is_array($locationShow) AND count($locationShow)) {
        $mainCatId = 318;
        $catName = "Gastronomie";
        $shortName = $catList[$mainCatId][shortName]; 
        for ($i=0;$i<count($locationShow);$i++) {
            $location = $locationShow[$i];
            $locationId   = $location[id];
            
            
            $name        = php_clearOutPut($location[name],0);
            $info        = php_clearOutPut($location[info],0);
            $image       = $location[image];


            // Anzahl an Zeichen
            $maxLength = 300;


            if ($selectedSubCatId) {
                $catName   = $catList[$mainCatId][name];
                $shortName = $catList[$mainCatId][shortName]; 
                $catName = $subCatList[$selectedSubCatId][name];
            } 

            $nr++;
            // erstes Element -> Öffne themes_container
            if ($nr == 1) {
                $themesLineNr++;
                $out .= "<div class='themes_container' id='themeboxes-$themesLineNr'>";               
            }

            $divName = "content_box theme_box";
            if ($nr >= $nrPerLine) $divName .= " theme_box_last";

            $out .= "  <div class='$divName'>\n";
            $out .= "      <div class='theme_box_inner tb_inner_home autoheight'>\n";

            // Box Head
            $out .= "          <div class='theme_header tb_$shortName'>\n";
            $out .= "              ".$catName."$catId\n";         
            $out .= "          </div>\n";

            // BOX Content
            $out .= "      <div class='tb_content boxlink'>\n";
            if ($image) {
                if (intval($image) > 0) $imageId = $image;
                else {
                    $imageList = explode("|",$image);
                    $imageId = $imageList[1];
                    if ($selectedSubCatId == 322) { // Biergarten
                        if (count($imageList)>=4) {
                            $imageId = $imageList[2];
                        } 
                    }                                           
                }
                $imageData = cmsImage_getData_by_Id($imageId);
                if ($imageData) {
                    $imgStr = cmsImage_showImage($imageData,$imageWidth,$imageShowData);
                    $out .= $imgStr;
                } else {
                    // no Image
                    $maxLength = $maxLength + 400;
                }
            } else {
                // no Image
                $maxLength = $maxLength + 400;
            }
            $out .= "<div class='tb_content_txt'>\n";

            if ($name) {
                $out .= "<h3>$name</h3>";
                $maxLength = $maxLength - (3*strlen($name));
            }

            if ($maxLength <= 50) $maxLength = 50;

            if (strlen($info)>$maxLength) {
                $endPos = strpos($info," ",$maxLength);
                if ($endPos) $info = substr($info,0,$endPos)." ...";
            }
            $out .= "$info";
           // $goPage = articles_getLink($link,array("articleId"=>$articleId));
              $goPage = "adressen.php?location=$locationId";

            $out .= "              <div class='hidden_url'><a href='$goPage'>Link zum Artikel</a></div>\n";
            $out .= "          </div>\n";
            $out .= "      </div>\n";
            $out .= "  </div>\n";
            $out .= "</div>\n";

            if ($nr >= $nrPerLine) {
                $out .= "<div class='clearleft'></div>";
                $out .= "</div>";
                $nr = 0;
            }   
        }
    }
       

    if ($nr>0) { // themeContainer Open - >Close
        $out .= "<div class='clearleft'></div>";
        $out .= "</div>";
    }
    
    echo ($out);
    
    
    
    
    if ($useCache AND !$useSingleCache) {
        cmsCache_save($link, $filter, $sort, $out);
    }
    
    
    switch ($selectedMainCatId) {
        case 307 :
            // Show Austellungen
            articles_show_art($selectedSubCatId,$link);
            break;
    }
}

function articles_show_art($selectedSubCatId,$link) {
    $mode = "Location"; // Holt Location und sucht dann nach Terminen
    $mode = "Dates"; // Holt Kunst/Ausstellungen und sucht dann nach Location
    
    include ("incs/kalender.php");
    
    $selectedDate = date("Y-m-d");
    $selectedRegion = null;
    $selectedCatId = $selectedSubCatId;
    
    switch ($selectedSubCatId) {
        case 309 : $headline = "Ausstellungen"; break;// Auststellungen
        case 308 : $headline = "Kunst"; break;// Kunst
        default :
            $headline = "Kunst und Ausstellungen";
    }        
        
    echo ("<div class='content_box box-shadow current_mag'>");
    echo ("<h1 class='hl_art'>$headline</h1>");
    echo ("<div class='current_mag_content'>");
        
    
    kalender_show_art($selectedDate,$selectedRegionId,$selectedCatId,0,$link);
       
       //  echo ($out);
     echo ("</div>\n");
     echo ("</div>");         
}



function articles_articleDetail($selectedArticleId,$pagelink) {
    $backLink = articles_getLink($link,array("articleId"=>0));
    // echo ("<a href='$backLink' >zurück</a>");
    $articleData = cmsArticles_get(array("id"=>$selectedArticleId));

    
    // show_array($articleData);

    $articleId   = $articleData[id];
    $name        = php_clearOutPut($articleData[name]);
    $subName     = php_clearOutPut($articleData[subName]);
    $url         = $articleData[url];
    $ticketUrl   = $articleData[ticketUrl];
    $info        = php_clearOutPut($articleData[info]);
   
    $image       = $articleData[image];
    if (intval($image)>0) $image = "|".$image."|";
    $imageList = explode("|",$image);

    $location    = $articleData[location];
    if ($location >0) $locationData = cmsLocation_get(array("id"=>$location));

    $link        = $articleData[link];
    $list = explode("|",$link);
    $linkList = array();
    for ($i=0;$i<count($list);$i++) {
        if ($list[$i]) {
            list($key,$dataStr) = explode(":",$list[$i]);
            $linkList[$key] = $dataStr;
        }
    }

    
    $catList = categoryGetList("RubrikList");
   
    $mainCatId   = $articleData[category];
    $mainCatStr  = $catList[$mainCatId][name];
    $shortName   = $catList[$mainCatId][shortName];
    // $mainCatStr  = cmsCategory_getName_byId($mainCatId);
    $subCatId    = $articleData[subCategory];
    if ($mainCatId AND $subCatId) {
        $subCatList = categoryGetList("RubrikSubList_".$mainCatId);     
        $subCatStr = $subCatList[$subCatId][name];
        $subCatShortName = $subCatList[$subCatId][shortName];
    }
    // $subCatStr = $_SESSION["RubrikSubList_".$mainCatId][$subCatId][name];
    
    
    
    
    // Titel Navigation
    echo("<div class='article-current-info'>");
    if ($mainCatId == 332) {
        echo ("<h3>Meldung</h3>");
    } else {
        echo ("<a href='".$pagelink."'>Alle Artikel</a> ");

        $fromDate = strtotime($articleData[fromDate]);
        $fromDate = $articleData[fromDate];
        if ($fromDate > date("Y-m-d")) { // Vorschau Artikel
            $previewCat = $catList["328"][shortName];
            echo ("<span class='quo'>&rsaquo;</span> ");
            echo ("<a href='".$pagelink."?artCat=$previewCat'>Vorschau</a> ");
        } else {
            if ($mainCatId) {
                echo ("<span class='quo'>&rsaquo;</span> ");
                echo ("<a href='".$pagelink."?artCat=$shortName'>$mainCatStr</a> ");
            }
            if ($subCatId) {
                echo ("<span class='quo'>&rsaquo;</span> ");
                echo ("<a href='".$pagelink."?artCat=$shortName&subCat=$subCatShortName'>$subCatStr</a> ");
            }
        }
    }
    echo("</div>");

    if (!is_array($articleData)) {
        echo ("<div class='noData' >");
        echo ("Dieser Artikel wurde nicht gefunden!");
        echo ("</div>");
        echo("</div>\n");

        echo("</div>\n");
        echo("</div>\n");
        echo("</div>\n");
        echo("</div>\n");
        return 0;
    }


    if ($name) echo("<h1>$name</h1>");
    if ($subName) echo("<h2>$subName</h2>");

    echo("<p />");
    echo("<div class='clearfloat'>");

    // Start Linkes DIV
    echo("<div class='article-content-left'>");

    $showData_Big = array();
    $showData_Big[frameWidth] = 800;
    $showData_Big[frameHeight] = 600;
    $showData_Big[vAlign] = "top";
    $showData_Big[hAlign] = "left";
    $showData_Big[out] = "url";

    // Bild
    // show_array($imageList);
    if (count($imageList)) {
        $imageId = intval($imageList[1]);
        if ($imageId) {
           
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $showData = array();
                $showData[frameWidth] = 400;
                $showData[frameHeight] = 300;
                $showData[vAlign] = "top";
                $showData[hAlign] = "left";
                $showData[out] = "url";
                $imgStr     = cmsImage_showImage($imageData,null, $showData);
                $imgStr_big = cmsImage_showImage($imageData,800, $showData_Big);
                echo("<div class='article-main-img-container'>");
                //echo ("<div class='article-images-gallery clearfloat'>");
                //echo ("<div class='article-images'>");
                echo ("<a href='$imgStr_big' class='zoomimage' title='vergr&ouml;&szlig;ern'>");
                echo ("<img src='$imgStr' class='noborder' alt='' />");
                echo ("</a>");
                //echo ("</div>");
                //echo ("</div>");

                
                // echo ($imgStr);
                // echo("<img src='$imgStr' alt='' />");
                echo("</div>");
            }
        }
    }


//    // Text
//    // echo ("Info = $info <br>");
//    if ($info) echo ($info."<br />");
//    if ($_SESSION[showLevel]>=8) {
//        echo ("<a href='admin_articles.php?view=edit&id=$articleId'>Artikel bearbeiten</a><br />");
//    }
//    echo("</div>"); // ende Linkes Div

    // Start Rechtes Div
    
    
    $rightStr = "";
    // Location
    if (is_array($locationData)) {
        $locationDontShow = array();
        $locationDontShow[name] = 0;
        $locationDontShow[subName] = 1;
        $locationDontShow[info] = 1;
        $locationDontShow[adress] = 0;
        $locationDontShow[phone] = 1;
        $locationDontShow[url] = 0;
        $locationDontShow[ticketUrl] = 1;
        $locationDontShow[infoGoLink] = 0;
        $locationDontShow[editLink] = 1;
        $locationDontShow[showLink] = 1;

        $locationId = $locationData[id];

        $rightStr .= "<div class='article-content-right-infos'>";
        $innerClass = "article-content-right-infos-inner";
        if ($locationId) $innerClass .= " right-info-link-box";

        $rightStr .= "<div class='$innerClass'>";


        $locationName = $locationData[name];
        // echo ("<h3>$locationName</h3>");
        include_once("incs/adressen.php");
        $adressOut = adressen_showInfo_str($locationData,$locationDontShow);
        $rightStr .= $adressOut;

        $rightStr .= "</div>";
        $rightStr .= "</div>";
    }

    $link = $articleData[link];
    if ($link) {
        $dontShowDates = array();
        $dontShowDates["name"] = 0;
        $dontShowDates["subName"] = 0;
        $dontShowDates["info"] = 0;
        $dontShowDates["category"] = 1;
        $dontShowDates["region"] = 1;
        $dontShowDates["location"] = 1;
        $dontShowDates["linkDate"] = 1;
        $dontShowDates["dateRange"] = 1;
        $dontShowDates["toDate"] = 1;
        $dontShowDates["editLink"] = 1;
        $dontShowDates["date"] = 1;
        $dontShowDates["maxDate"] = "none";
        $dontShowDates[cancel] = 0;
        $delimiter = "&#149;";

        $dateList = array();

        $linkList = explode("|",$link);
       
        for ($i=0;$i<count($linkList);$i++) {
            list($linkType,$idListStr) = explode(":",$linkList[$i]);
            $idList = explode(",",$idListStr);
            for ($k=0;$k<count($idList);$k++) {
                $linkId = $idList[$k];
                switch ($linkType) {
                    case "date" :
                        // echo ("Date $link $linkId <br>");
                        $dateData = cmsDates_getById($linkId);
                        $dateDate = $dateData[date];
                        $dateCancel = $dateData[cancel];
                        $dateUrl = $dateData[url];
                        if (is_array($dateData[data] )) {
                            $dateTicketLink = $dateData[data][ticketUrl];                            
                        }



                        if (strtotime($dateDate) >= strtotime(date("Y-m-d")) AND !$dateCancel) { // Termin ist heute oder in Zukunft
                            if (!is_array($dateList[$dateDate])) $dateList[$dateDate] = array();
                            $dateList[$dateDate][] = $dateData;
                        }

                        if ($dateData[link]) {
                            $dateLinkList = explode("|",$dateData[link]);
                            for ($l=0;$l<count($dateLinkList);$l++) {
                                list($dateLinkType,$dateLinkIdStr) = explode(":",$dateLinkList[$l]);
                                if ($dateLinkType == "date") {
                                    $dateLinkIdList = explode(",",$dateLinkIdStr);
                                    for ($d=0;$d<count($dateLinkIdList);$d++) {
                                        $dateLinkId = $dateLinkIdList[$d];
                                        // echo ("DateLink $dateLinkType = $dateLinkId <br>");

                                        $dateData = cmsDates_getById($dateLinkId);
                                        $dateDate = $dateData[date];
                                        $dateCancel = $dateData[cancel];



                                        //echo ("DateLink $dateLinkType = $dateLinkId => $dateDate $dateCancel <br>");

                                        if (strtotime($dateDate) >= strtotime(date("Y-m-d")) AND !$dateCancel) { // Termin ist heute oder in Zukunft
                                            if (!is_array($dateList[$dateDate])) $dateList[$dateDate] = array();
                                            $dateList[$dateDate][] = $dateData;
                                        }

                                    }
                                }


                            }

                        }
                        break;

                    default :
                        echo ("Zeige $linkType mit $linkId <br />");
                }
            }
        } // ende of link-schleife
        ksort($dateList);
    }

    if ($url or $ticketUrl or $dateTicketLink) {
        $target = externalLinkTarget($link);
        $rightStr .= "<div class='article-content-right-infos'>";
        $innerClass = "article-content-right-infos-inner";
        $rightStr .= "<div class='$innerClass'>";
        $rightStr .= "<h3>Weiterführende Links </h3>";
        
        if ($url AND $url != $locationData[url]) {
            $linkList = external_link_get($url);
            for($i=0;$i<count($linkList);$i++) {
                $linkUrl = $linkList[$i][url];
                $linkName = $linkList[$i][name];
                $linkTarget = $linkList[$i][target];
                if (!$linkName) $linkName = "weitere Infos";
                $rightStr .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a><br />";
            }
        }
        
        if ($ticketUrl AND $ticketUrl != $locationData[ticketUrl]) {
            
            $linkList = external_link_get($ticketUrl);
            for($i=0;$i<count($linkList);$i++) {
                $linkUrl = $linkList[$i][url];
                $linkName = $linkList[$i][name];
                $linkTarget = $linkList[$i][target];
                if (!$linkName) $linkName = "weitere Infos";
                $rightStr .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a><br />";
            }
        }
        
        
        //if ($url) $rightStr .= "<a href='$url' target='$target'>Info</a> <br>";
        // if ($ticketUrl) $rightStr .= "<a href='$ticketUrl' target='$target'>Karten</a> <br>";

        if ($dateTicketLink) {
            // echo ("Date Link = $dateTicketLink <br>");
            $linkList = external_link_get($dateTicketLink);
            for($i=0;$i<count($linkList);$i++) {
                $linkUrl = $linkList[$i][url];
                $linkName = $linkList[$i][name];
                $linkTarget = $linkList[$i][target];
                if (!$linkName) $linkName = "Ticket-Webseite";
                $rightStr .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a><br />";
            }




        }
        // echo ("<h3>$locationName</h3>");
      

        $rightStr .= "</div>";
        $rightStr .= "</div>";
        
        
        
    }
    
    
    

    if (count($dateList)) {
        include("incs/kalender.php");
        $rightStr .= "<div class='article-content-right-infos'>";
        $rightStr .= "<div class='article-content-right-infos-inner'>";
        $rightStr .= "<h3>Termine</h3>";
        $nr = 0;
        foreach ($dateList as $day => $datesOnDay) {
            $dayStr = cmsDate_getDayString($day, 1);
            $nr++;
            if ($nr > 1) $rightStr .= "<div style='height:5px;'>&nbsp;</div>";
            $rightStr .= "$dayStr <br />";
            for ($i=0;$i<count($datesOnDay);$i++) {
                $dateStr = date_showSmall_str($datesOnDay[$i],$dontShowDates,$delimiter);
                $rightStr .= $dateStr; //."<br />");
            }
        }
        $rightStr .= "</div>";
        $rightStr .= "</div>";
    }


    


    // Bilder
    $imageStr = "";
    if (count($imageList)>3) {
        $imageStr .= "<div class='article-images-gallery clearfloat'>";
        //  echo (count($imageList)."<br />");
        $showData_Small = array();
        $showData_Small[frameWidth] = 50;
        $showData_Small[frameHeight] = 50;
        $showData_Small[ratio] = 1;
        $showData_Small[vAlign] = "middle";
        $showData_Small[hAlign] = "center";
        $showData_Small[out] = "url";



        for ($i=2;$i<count($imageList)-1;$i++) {
            $imageId = intval($imageList[$i]);
            if ($imageId) {
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    $imgStr_small = cmsImage_showImage($imageData,50, $showData_Small);
                    $imgStr_big = cmsImage_showImage($imageData,800, $showData_Big);
                    $imageStr .= "<div class='article-images'>";
                    $imageStr .= "<a href='$imgStr_big' class='zoomimage' title='vergr&ouml;&szlig;ern'>";
                    $imageStr .= "<img src='$imgStr_small' class='noborder' alt='' />";
                    $imageStr .= "</a>";
                    $imageStr .= "</div>";
                }
            }
        }
        $imageStr .= "</div>";
    }
    
    if ($rightStr) {
        
        if ($info) echo ($info."<br />");
        if ($_SESSION[showLevel]>=8) {
            echo ("<a href='admin_articles.php?view=edit&id=$articleId'>Artikel bearbeiten</a><br />");
        }
        echo("</div>"); // ende Linkes Div
        
        // rechtes Div
        echo("<div class='article-content-right'>");
        echo ($rightStr);
        echo ($imageStr);
        echo("</div>\n");
        // geteiltes DIV zu
        echo("</div>\n");
    } else {
        echo("</div>"); // ende Linkes Div
        
        // rechtes Div
        echo("<div class='article-content-right'>");
        echo ($imageStr);
        echo ("&nbsp;");        
        echo("</div>\n");
        
        // geteiltes DIV zu
        echo("</div>\n");
        
        if ($info) echo ($info."<br />");
        if ($_SESSION[showLevel]>=8) {
            echo ("<a href='admin_articles.php?view=edit&id=$articleId'>Artikel bearbeiten</a><br />");
        }
        
    }
    

    

    
    echo("</div>\n");
    echo("</div>\n");
    echo("</div>\n");
}

function article_showInfo($articleData,$ownDontShow) {
    $out = article_showInfo_str($articleData,$ownDontShow);
    echo($out);
}

function article_showInfo_str($articleData,$ownDontShow=array()) {
    
    
    $dontShow = array();
    $dontShow[mainCat] = 1;
    $dontShow[subCat] = 1;
    $dontShow[editLink] = 1;
    $dontShow[showLink] = 1;
    $dontShow[showImageLink] = 1;


    
    foreach ($ownDontShow as $key => $value) {
        $dontShow[$key] = $value;
    }
    
   
    $articleId   = $articleData[id];
    $name        = php_clearOutput($articleData[name],0);
    $subName     = php_clearOutput($articleData[subName],0);
    $info        = $articleData[info];
    $image       = $articleData[image];

    
    $catList = categoryGetList("RubrikList");
    

    $mainCatId   = $articleData[category];
    //$mainCatStr  = cmsCategory_getName_byId($mainCatId);
    //echo ("$mainCatStr");
    $mainCatStr  = $catList[$mainCatId][name];
    //echo (" -> $mainCatStr <br>");
    
    $subCatId    = $articleData[subCategory];
    $subCatList = $_SESSION["RubrikSubList_".$mainCatId];
    // show_array($articleData);
    //$subCatStr   = cmsCategory_getName_byId($subCatId);
    // echo ("$subCatStr");
    $subCatStr = $subCatList[$subCatId][name];
    // echo (" -> $subCatStr <br>");

    $showLink = "ausgabe.php?articleId=$articleId";
    $showLink = php_clearLink($showLink);
    $editLink = "admin_location.php?view=edit&id=$editId";
    $editLink = php_clearLink($editLink);


    $maxChars = 300;
    $out = "";

    $showImage = 0;
    if (!$dontShow[image] AND $image) {
        // $out .= "Image '$image'";
        if (intval($image)) $image = "|$image|";
        $imgList = explode("|",$image);
        if (count($imgList)==3) $imageId = $imgList[1]; //nur ein Bild
        else { // mehrere Bilder
            $imageNr = rand(1,count($imgList)-2);
            $imageId = $imgList[$imageNr];
        }
        $imageData = cmsImage_getData_by_Id($imageId);
        if (is_array($imageData)) {
            $imageSize = 70;
            if ($dontShow[imageSize]) $imageSize = $dontShow[imageSize];
            $ratio = $imageData[ratio];
            $imageHeight = floor($imageSize / $ratio);
            $showData = array("frameWidth"=>$imageSize,"frameHeight"=>$imageHeight,"vAlign"=>"top","hAlign"=>"left");
            $imageStr = cmsImage_showImage($imageData, $imageSize, $showData);
            $out .= "<div class='article_list_imagebox'>";
            if (!$dontShow[showImageLink] AND $showLink) $out .= "<a href='$showLink' class='showImageLink' >";
            $out .= $imageStr;
            if (!$dontShow[showImageLink] AND $showLink) $out .= "</a>";
            $out .= "</div>";
            $showImage = 1;
        }

    }
    if (!$showImage) {
        $maxChars = $maxChars + 200;
        $out .= "<div class='article_list_textbox_noneImage' >";
    } else {
        $out .= "<div class='article_list_textbox' >";
    }

    if (!$dontShow[name] AND $name) {
        $out .= "<h4 class='article-list-headline'>$name</h4>";
        $maxChars = $maxChars - (strlen($name)*3);
    }
    
   
    
    if (!$dontShow[subName] AND  $subName) {
        $out .= "<strong>$subName</strong><br />";
        $maxChars = $maxChars - (strlen($subName)*2);
    }
    
    // Cahtegory
    $catStr = "";
    if (!$dontShow[mainCat]) $catStr .= "$mainCatStr ";
    if (!$dontShow[subCat] AND $subCatStr) $catStr .= "/ $subCatStr";
    if ($catStr) $out .= $catStr."<br />";
    
    
    if (strlen($info) > $maxChars) {
        $endChar = strpos($info, " ", $maxChars);
        
        $out .= php_clearOutput(substr($info,0,$endChar+1)."...",0);

    } else {
        $out .= $info;
    }

    if (!$dontShow[showLink] AND $showLink) {
        $out .= " &nbsp; <a href='$showLink' class='readmore_link showLink' >weiter Lesen</a> ";
    }


    if (!$dontShow[editLink] AND $editLink ) {
        $out .= "<a href='$editLink' class='editLink' >Artikel editieren</a>";
    }
    // $out .= "<br>";
    $out .= "</div>";

    return $out;
}

?>
