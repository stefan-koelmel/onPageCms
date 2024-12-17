<?
$page_url = explode("/","$_SERVER[PHP_SELF]");
// $page_url_file = explode('.',$page_url[count($page_url)-1]);
$page_url = $page_url[count($page_url)-1];


?>        
                      <ul>
        <?PHP
            $link = "index.php";
            if ($page_url == $link) $currentClass = "class='maincurrent'"; else $currentClass = "";
            echo ("<li class='bright'><a href='$link' $currentClass>Startseite</a></li>");
        ?>

<!-- ######################################################### ZEIGE RUBRIKEN mit Unterkategorien ################################## -->
        <?php // charset:UTF-8
        
            $showMain = 1;
            $link = "ausgabe.php";
            if ($page_url == $link) $currentClass = "class='maincurrent'"; else $currentClass = "";
            echo ("<li class='has-sub bright'><a href='$link' $currentClass>Artikel</a>\n");
            
            $catList = categoryGetList("RubrikList");
            unset($catList[332]); // PR-Texte
           //  $catList = cmsCategory_getList(array("mainCat"=>144,"show"=>1),"id");
            $addSpecialMenu = "";
            
            if (is_array($catList) AND count($catList)) {
                echo ("<ul>\n");
                if ($showMain) {
                    echo("<li class='maincurrentAgain'><a href='".$link."'>Alle Artikel</a></li>");
                }
                $i=0;
                foreach ($catList as $catId => $catValue) {
                    $catName = $catValue[name];
                    $shortName = $catValue[shortName];
                    $show = 1;

                    if ($show) {
                        $class = "";
                        if ($i == count($catList)-1 AND $addSpecialMenu == "") $class = "noborder";
                        echo("<li class='has-sub $class'><a href='".$link."?artCat=$shortName'><span class='quo'>&rsaquo;</span> $catName</a>");
                        $subCatList = categoryGetList("RubrikSubList_".$catId);
                        
                        // $subCatList = cmsCategory_getList(array("mainCat"=>$catId,"show"=>1),"id");
                        if (is_array($subCatList) AND count($subCatList)) {
                            echo ("<ul>\n");
                            
                            if ($showMain) {
                                 echo("<li class='maincurrentAgain'><a href='".$link."?artCat=$shortName'>$catName</a></li>");
                            }
                            
                            $class = "";
                            $s = 0;
                            foreach ($subCatList as $subCatId => $subCatData ) {
                                $subCatName = $subCatData[name];
                                $subShortName = $subCatData[shortName];
                                if ($s == count($subCatList)-1) $class = "class='noborder'";
                                $goLink = htmlspecialchars($link."?artCat=$shortName&subCat=$subShortName");
                                echo("<li $class><a href='$goLink'><span class='quo'>&rsaquo;</span> $subCatName</a></li>\n");
                                $s++;
                            }
                            echo ("</ul>\n");
                        }
                        echo ("</li>\n");
                        $i++;
                    }
                }
                if ($addSpecialMenu) echo ($addSpecialMenu);
                echo ("</ul>\n");
            }
            echo ("</li>\n");
        ?>
<!-- ######################################################### ENDE - ZEIGE RUBRIKEN mit Unterkategorien HEADER-ENDE ############### -->


<!-- ######################################################### ZEIGE KALENDER mit Unterkategorien ################################## -->
       <?php
            if ($_SESSION[userLevel] >= 9) $showKalender = 1;
            $showKalender = 1;
            if ($showKalender) {
                $dateRange = "thisWeek";
                if ($_GET[date]) $dateRange = $_GET[date];
                $link = "kalender.php";
                $addArt = 1;
                if ($page_url == $link) $currentClass = "class='maincurrent'"; else $currentClass = "";
                echo ("<li class='has-sub bright'><a href='$link' $currentClass>Kalender</a>\n");
                // show_array($GLOBALS);

                $dateCatList = categoryGetList("TerminCategoryList");
                
                unset($dateCatList[327]);  // Weg Kunst 
                unset($dateCatList[330]);  // und Austellungen
                if (is_array($dateCatList) AND count($dateCatList)) {
                    echo ("<ul>\n");
                    if ($showMain) {
                        echo("<li class=''><a href='".$link."' class='maincurrentAgain' >Alle Termine</a></li>");
                    }
                    $class = "";
                    $i = 0;
                    foreach ($dateCatList as $catId => $catValue) {
                        $catName = $catValue[name];
                        $shortName = $catValue[shortName];
                        
                        if ($i >= (count($dateCatList)-1) AND !$addArt) $class = "class='noborder'";
                        $goLink = htmlspecialchars($link."?cat=$shortName&date=$dateRange");
                        echo("<li $class><a href='$goLink'><span class='quo'>&rsaquo;</span> $catName</a></li>");
                        $i++;
                    }
                    
                    if ($addArt == 1) {
                        $class = "class='noborder'";
                        $goLink = htmlspecialchars($link."?cat=327&date=$dateRange");
                        echo("<li $class><a href='$goLink'><span class='quo'>&rsaquo;</span> Ausstellungen</a></li>");
                    }
                    
                    
                    echo ("</ul>");
                }
                echo ("</li>");
            }
        ?>
<!-- ######################################################### ENDE - ZEIGE KALENDER mit Unterkategorien HEADER-ENDE ############### -->


                        
<!-- ######################################################### ZEIGE ADRESSEN mit Unterkategorien ################################## -->
       <?php
            $link = "adressen.php";
            if ($page_url == $link) $currentClass = "class='maincurrent'"; else $currentClass = "";
            echo ("<li class='has-sub bright'><a href='$link' $currentClass>Adressen</a>\n");
            // show_array($GLOBALS);
            
            $catList = categoryGetList("AdressList");
            if (is_array($catList) AND count($catList)) {
                echo ("<ul>\n");
                if ($showMain) {
                    echo("<li class='mainCurrentAgain'><a href='".$link."'>Alle Adressen</a></li>");
                }
                $class = "";
                $i=0;
                foreach ($catList as $catId => $catValue) {
                    $catName = $catValue[name];
                    $shortName  = $catValue[shortName];
                    if ($i == count($catList)-1) $class = "class='noborder'";
                    $goLink = htmlspecialchars($link."?locCat=$shortName");
                    echo("<li $class><a href='$goLink'><span class='quo'>&rsaquo;</span> $catName</a></li>");
                    $i++;
                }
                echo ("</ul>");
            }
            echo ("</li>");
        ?>
<!-- ######################################################### ENDE - ZEIGE ADRESSEN mit Unterkategorien HEADER-ENDE ############### -->

                       


        <?php
            if ($_SESSION[userLevel]>5) {
                $link = "kontakt.php";
                if ($page_url == $link) $currentClass = "class='maincurrent'"; else $currentClass = "";
                echo("<li class='bright'><a href='$link' $currentClass >Kontakt</a></li>");
                // echo ('<li class="bright"><a href="kontakt.php">Kontakt</a></li>');
                echo ('<li class="has-sub bright"><a href="admin.php">Admin</a>');


                // hole LISTE von UnterSeiten von Admin
                $pageData = cms_page_getData("admin");
                $adminPageId = $pageData[id];
                $mainAdminList = cms_page_getSubPage($adminPageId);
                if (count($mainAdminList)>0) {
                    echo ("<ul>\n");
                    for($i=0;$i<count($mainAdminList);$i++) {
                        $mainName  = $mainAdminList[$i][title];
                        $link      = $mainAdminList[$i][name].".php";
                        $mainId    = $mainAdminList[$i][id];
                        $userlevel = $mainAdminList[$i][showLevel];

                        // UnterSeite von Admin anzeigen ??
                        if ($_SESSION[showLevel] >= $userlevel) {
                            echo("<li><a href='".$link."'><span class='quo'>&rsaquo;</span> $mainName</a></li>");

                            // hole LISTE von UnterUnterSeiten von Admin-Unterseiten
                            $subAdminList = cms_page_getSubPage($mainId);
                            if (count($subAdminList)>0) {
                                //echo ("<ul>\n");
                                for($s=0;$s<count($subAdminList);$s++) {
                                    $subName  = $subAdminList[$s][title];
                                    if (!$mainName) $subName = $subAdminList[$s][name];
                                    $link      = $subAdminList[$s][name].".php";
                                    $userlevel = $subAdminList[$s][showLevel];
                                    // UnterUnterSeite von Admin anzeigen ??
                                    if ($_SESSION[showLevel] >= $userlevel) {
                                        echo("<li><a href='".$link."'><span class='quo'>&rsaquo;</span> - $subName</a></li>");
                                    }
                                }
                                //echo ("</ul>\n");
                            }
                        }
                        //echo ("</li>");
                    }
                    echo ("</ul>");
                }
                echo ('</li>');
            } else {
                $link = "kontakt.php";
                if ($page_url == $link) $currentClass = "class='maincurrent'"; else $currentClass = "";
                echo("<li class='menu_last'><a href='$link' $currentClass >Kontakt</a></li>");
            }
       ?>
                                                
                      </ul>

