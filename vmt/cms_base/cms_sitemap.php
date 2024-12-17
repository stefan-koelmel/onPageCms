<?php // charset:UTF-8

function cms_sitemap_show() {
    // CHANGE SORT
    $res = cms_sitemap_changeSort();
    if ($res == 1) {
        echo ("Sortierung geändert<br>");
        reloadPage("sitemap.php",2);
    }

    $pageList = cms_page_getSortList();
    // show_array($pageList);


    global $cmsName;
    $edit = $_SESSION[edit];

    //echo ("<h1>SESSION $_SESSION[cmsName]</h1>");
    //echo ("<h1>CMS NAME = $cmsName </h1>");

    $showLevel = $_SESSION[showLevel];
    if (!$showLevel) $showLevel = 0;

    $ediPage = $_GET[editPage];

    $mainSort = 0;
    $mainNr = 0;

   




    
    foreach($pageList as $idCode => $page) {
        $name = $page[name];
        $showName = $page[title];

        $show = 1;
        switch ($name) {
            case "sitemap" : $show = $page[navigation]; break;
            case "admin" : 
                // echo ("Admin $_SESSION[showLevel] <br>");
                if ($_SESSION[showLevel]<9) $show =9;
                break;

            default :
                if (substr($name,0,7)== "layout_") $show = 0;

        }
        $sort = $page[sort];
        if ($sort != $mainSort) {
            echo ("Change Sort in MainLevel from $sort to $mainSort <br>");
            $res = cms_page_changeSort($page[id],$mainSort);
        }
        
        $pageLevel = $page[showLevel];
        if ($pageLevel > $showLevel) {
            // echo ("dontShow '$page[name]' because $pageLevel > $_SESSION[showLevel] <bR>");
            $show = 0;
        }
        
        $dynamicContent = $page[dynamicContent];
        if ($show) {
            if (!$showName) $showName = $name;

            //cmsSitmap_show($page,0,$edit);
            $divData = array();
            $style = "";
            $divName = "siteMap siteMapLevel_1";
            if ($name == "index") $divName = "siteMap siteMapLevel_0";
            if ($edit) {
                // $style .= "width:200px;";
                $divName .= " siteMapEditBox";
                $divData[pageId] = $page[id];

            } else {
                $style .= "";
                echo ("<a href='$name.php' class='siteMapLink'>");
            }
            $divData[style] = $style;
            $divData["class"] = "cmsSitemapSortItem";
            div_start($divName,$divData);

            if ($edit) {
                echo ("<strong>$showName</strong> $page[sort] ");
                // go Page
                echo ("<a href='$page[name].php' class='cmsContentHeadButton'>zeigen</a>");
                // edit
                // echo ("&nbsp; <a href='sitemap.php?editPage=$page[id]' >e</a> ");
                // move Up
                if ($mainNr > 1) {
                    echo ("<a href='sitemap.php?pageUp=$page[id]' class='cmsContentHeadButton'>&uarr;</a>");
                }
                // move Down
                if ($mainNr+1 < count($pageList) AND $mainNr>0) {
                    echo ("<a href='sitemap.php?pageDown=$page[id]' class='cmsContentHeadButton'>&darr;</a>");
                }
                div_start("cmsContentHeadButton siteMapNew",array("pageId"=>$page[id]));
                echo ("neue Seite");
                div_end("cmsContentHeadButton siteMapNew");

                
                
                $editDivData = array();
                $editDivData[id] = "cmsSitemap_editId_$page[id]";
                //$editDivData["pageId"] = $page[id];
                
                div_start("cmsContentHeadButton siteMapEdit",$editDivData);
                echo ("editieren");
                div_end("cmsContentHeadButton siteMapEdit");

                div_start("cmsContentHeadButton siteMapDelete",array("pageId"=>$page[id]));
                echo ("löschen");
                div_end("cmsContentHeadButton siteMapDelete");

                if ($ediPage == $page[id]) {
                    echo ("<br>");
                    show_array($page);
                }
                $mainNr ++;

            } else { // not Edit
                $pageShowLevel = $page[showLevel];
                if ($pageShowLevel <= $showLevel) {
                    echo ("<strong>$showName</strong>"); // $pageShowLevel = $showLevel <br>");
                }
            }
            div_end($divName);
            if ($edit) {
                $addLevel = 2;
                if ($page[name]=="index") $addLevel = 1;
                cmsSitemap_AddPage($page,$addLevel);
            } else {
                echo ("</a>");
            }
            echo ("<br>");


            if (is_array($page[subNavi])) {
                // echo ("Found ".count($page[subNavi])." subNavi Points <br>");

                $sort_subSort = 10*$sort;
                $subNr = 0;
                foreach ($page[subNavi] as $subIdCode => $subPage) {
                    $name = $subPage[name];
                    $goLink = $name.".php";
                    $showName = $subPage[title];
                    $subDynamicContent = $subPage[dynamicContent];
                    if (!$showName) $showName = $name;

                    $subSort = $subPage[sort];
                    if ($subSort != $sort_subSort AND !$subDynamicContent) {
                        echo ("Change Sort in SubLevel from $subSort to $sort_subSort <br>");
                        $res = cms_page_changeSort($subPage[id],$sort_subSort);
                    }
                    $sort_subSort ++;

                    $divData = array();
                    $style = "";
                    $divName = "siteMap siteMapLevel_2";
                    
                    if ($subDynamicContent) {
                        $goLink .= "?".$subPage[addUrl];
                    }
                    
                    
                    if ($edit) {
                        $style .= "";
                        $divName .= " siteMapEditBox";
                        $divData[pageId] = $subPage[id];
                        if ($subDynamicContent) {
                            //$style .= "background-color:#99f;";     
                            $divData["class"] = "siteMapDynamic";
                        }
                        
                    } else {
                        echo ("<a href='$goLink'  class='siteMapLink'>");
                    }
                    $divData[style] = $style;
                    div_start($divName,$divData);

                    if ($edit) {
                        if ($subDynamicContent) echo ("<i>Dynamische Seite (1) -> </i>");
                        echo ("<strong>$showName</strong> ");
                        // go Page
                        echo ("<a href='$goLink' class='cmsContentHeadButton'>zeigen</a>");
                      
                        // move Up
                        if ($subNr > 0 AND !$subDynamicContent) {
                            echo ("<a href='sitemap.php?pageUp=$subPage[id]' class='cmsContentHeadButton'>&uarr;</a>");
                        }
                        // move Down
                        if ($subNr+1 < count($page[subNavi])  AND !$subDynamicContent) {
                            echo ("<a href='sitemap.php?pageDown=$subPage[id]' class='cmsContentHeadButton'>&darr;</a>");
                        }
                        // New Page
                        if (!$subDynamicContent) {
                            div_start("cmsContentHeadButton siteMapNew",array("pageId"=>$subPage[id]));
                            echo ("neue Seite");
                            div_end("cmsContentHeadButton siteMapNew");
                        }

                        // Edit Page
                        if (!$subDynamicContent) {
                            $editDivData = array();
                            $editDivData[id] = "cmsSitemap_editId_$subPage[id]";
                            div_start("cmsContentHeadButton siteMapEdit",$editDivData);
                            echo ("editieren");
                            div_end("cmsContentHeadButton siteMapEdit");
                        }

                        if (!$subDynamicContent) {
                            $editDivData = array();
                            $editDivData[id] = "cmsSitemap_deleteId_$subPage[id]";
                        
                            div_start("cmsContentHeadButton siteMapDelete",$editDivData);
                            echo ("löschen");
                            div_end("cmsContentHeadButton siteMapDelete");
                        }
                        
                        $subNr ++;



                    } else {
                        echo ("<strong>$showName</strong>");
                    }

                    div_end($divName);
                    if ($edit) { // AND !$subDynamicContent) {
                        $addLevel = 3;
                        cmsSitemap_AddPage($subPage,$addLevel);
                    } else {
                        echo ("</a>");
                    }
                    echo ("<br>");




                    if (is_array($subPage[subNavi])) {
                        $sort_subSubSort = $sort * 100;
                        $subSubNr = 0;
                        foreach ($subPage[subNavi] as $subSubIdCode => $subSubPage) {
                            $name = $subSubPage[name];
                            $showName = $subSubPage[title];
                            $goLink = $subSubPage[name].".php";
                            if (!$showName) $showName = $name;
                            $subSubDynamicContent = $subSubPage[dynamicContent];

                            $subSubSort = $subSubPage[sort];
                            if ($subSubSort != $sort_subSubSort AND !$subSubDynamicContent) {
                                echo ("Change Sort in SubSubLevel from $subSubSort to $sort_subSubSort <br>");
                                $res = cms_page_changeSort($subSubPage[id],$sort_subSubSort);
                                show_array($subSubPage);
                            }
                            $sort_subSubSort++;


                            $editDivData = array();
                            $style = "";
                            $divName = "siteMap siteMapLevel_3";
                            if ($edit) {
                                $style .= "";
                                $divName .= " siteMapEditBox";
                                //$divData[pageId] = $subSubPage[id];
                                
                                if ($subSubDynamicContent) {
                                //$style .= "background-color:#99f;";     
                                $editDivData["class"] = "siteMapDynamic";
                            }
                                
                            } else {
                                $style .= "";
                                echo ("<a href=$name.php class='siteMapLink''>");
                            }
                            
                            
                            
                            if ($subSubDynamicContent) {
                                $editDivData[id] = "cmsSitemap_editId_".$subSubPage[subId]; //"dynamicContent_".$subPage[addUrl];
                            } else {
                                $editDivData[id] = "cmsSitemap_editId_$subSubPage[id]";
                                // $editDivData["pageId"] = $page[id];
                            }
                        
                            // div_start("cmsContentHeadButton siteMapEdit",$editDivData);
                            
                            //$divData[style] = style;
                            $editDivData[style] = $style;
                            div_start($divName,$editDivData);
                            if ($subSubDynamicContent) {
                                echo ("<i>Dynamische Seite (2) -> </i>");
                           
                                $goLink .= "?".$subSubPage[addUrl];
                                // show_array($subSubPage);
                    
                            }
                            echo ("<strong>$showName</strong> ");

    //                        echo (" &nbsp; - $showName sort = $subSubSort");

                            if ($edit) {
                                
                                // go Page
                                echo ("<a href='$goLink' class='cmsContentHeadButton'>zeigen</a> ");
                                // edit
                                //echo ("&nbsp; <a href='sitemap.php?editPage=$subSubPage[id]' class='cmsContentHeadButton'>e</a> ");
                                // move Up
                                if ($subSubNr > 0 AND !$subSubDynamicContent) {
                                    echo ("&nbsp; <a href='sitemap.php?pageUp=$subSubPage[id]' class='cmsContentHeadButton'>&uarr;</a> ");
                                }
                                // move Down
                                if ($subSubNr+1 < count($subPage[subNavi]) AND !$subSubDynamicContent) {
                                    echo ("&nbsp; <a href='sitemap.php?pageDown=$subSubPage[id]' class='cmsContentHeadButton'>&darr;</a> ");
                                }
                                
                                // delete
                                if (!$subSubDynamicContent) {
                                    div_start("cmsContentHeadButton siteMapNew",array("pageId"=>$subSubPage[id]));
                                    echo ("neue Seite");
                                    div_end("cmsContentHeadButton siteMapNew");
                                }
                                
                                // Edit
                                if (!$subSubDynamicContent) {
                                    $editDivData = array();
                                    $editDivData[id] = "cmsSitemap_editId_$page[id]";
                                    div_start("cmsContentHeadButton siteMapEdit",$editDivData);
                                    echo ("editieren");
                                    div_end("cmsContentHeadButton siteMapEdit");
                                }

                                if (!$subSubDynamicContent) {
                                    div_start("cmsContentHeadButton siteMapDelete",array("pageId"=>$subSubPage[id]));
                                    echo ("löschen");
                                    div_end("cmsContentHeadButton siteMapDelete");
                                }  



                                $subSubNr ++;
                            }
                            div_end($divName);
                            if ($edit) {
                                $addLevel = 4;
                                cmsSitemap_AddPage($subSubPage,$addLevel);
                            } else {
                                echo ("</a>");
                            }
                            echo ("<br>");
                        }
                    }
                }
            }

        } // end of Show Level 1
        $mainSort++;

        

        //    ? 	Pfeil links 	&larr; 	&#8592;
        //? 	Pfeil oben 	&uarr; 	&#8593;
        //? 	Pfeil rechts 	&rarr; 	&#8594;
        //? 	Pfeil unten 	&darr; 	&#8595;
        //? 	Pfeil links/rechts 	&harr; 	&#8596;
        //? 	Pfeil unten-Knick-links 	&crarr; 	&#8629;
        //? 	Doppelpfeil links 	&lArr; 	&#8656;
        //? 	Doppelpfeil oben 	&uArr; 	&#8657;
        //? 	Doppelpfeil rechts 	&rArr; 	&#8658;
        //? 	Doppelpfeil unten 	&dArr; 	&#8659;
        //? 	Doppelpfeil links/rechts 	&hArr; 	&#8660;


    }
    // echo ("</div>");
}

function cmsSiteMap_edit($getData=array()) {

    switch ($getData[edit]) {
        case "addPage" :
            $pageId = $getData[pageId];
            cms_siteMap_addPageForm($pageId);
            return 0;
            break;
        case "editPage" :
            $pageId = $getData[pageId];
            cms_siteMap_editPageForm($pageId);
            return 0;
            break;

        case "deletePage" :
            $pageId = $getData[pageId];
            cms_siteMap_deletePage($pageId);
            return 0;
            break;

    }

    echo ("SiteMap_edit<br>");
    foreach($getData as $key => $value) {
        echo ("$key = $value <br>");
    }
}

function cmsSiteMap_AddPage($page,$addLevel) {
    $showId = intval($page[id]);
    $editMode = "page";
    if ($page[dynamicContent]) {
        // echo ("No ID <br>");
        // show_array($page);
        $showId = $page[subId];    
        $editMode = "dynamic";
    } 
    
    
   
   $savePageData = $_POST[savePageData];
   $saveButton = $_POST[saveButton];
   $saveAndClose = $_POST[saveAndClose];

   if ($_POST[saveCancel]) {
        $goPage = $pageInfo[page];
        reloadPage ($goPage, 0);
        return 0;
    }


    $addPage = $_POST[addPage];
    if (is_array($addPage)) {
        $addPageTo = intval($addPage[addPageTo]);
        // echo ("AddPage Sended $addPageTo <> $page[id] $showId<br>");
    }

    $editPage = $_POST[editPage];
    if (is_array($editPage)) {
        $editId = intval($editPage[id]);
    }

    $deletePage = $_POST[deletePage];
    if (is_array($deletePage)) {
        $deleteId = intval($deletePage[id]);
    }

    $divName = "cmsSitemapAddPage siteMapAdd_$showId siteMapLevel_$addLevel";
    $divData = array();
    if ($editMode == "dynamic") $divData["class"] = "siteMapDynamic";
    if ($page[id]) {
        if ($showId == $addPageTo) $divName .= " cmsSitemapAddPageShow";
        if ($showId == $editId) $divName .= " cmsSitemapAddPageShow";
        if ($showId == $deleteId) $divName .= " cmsSitemapAddPageShow";
    }


    div_start($divName,$divData);
    //echo ("SHOW DIV width $page[id] $showId eId=$editId aId=$addPageTo dId=$deleteId <br>");
    
    if ($showId) {
        if ($showId === $addPageTo) cms_siteMap_addPageForm($addPageTo);
        if ($showId === $editId) cms_siteMap_editPageForm($editId);
        if ($showId === $deleteId) cms_siteMap_deletePage($deleteId);    
    }
    div_end($divName);
   
}

function cms_siteMap_deletePage($pageId) {
    if (strpos($pageId,"_")) {
        
    } else {
        $pageId = intval($pageId);
    }
    
    
    $page = cms_page_getData($pageId);

    $showName = $page[title];
    if (!$showName) $showName = $page[name];

    echo ("Wollen Sie die Seite <strong>$showName</strong> wirklick löschen?<br>");

    $deletePage = $_POST[deletePage];
    if (is_array($deletePage)) {
        if ($_POST[deleteButton]) {
            show_array($deletePage);
            $deleteId = $deletePage[id];
            $res = cms_page_delete($deleteId);
            if ($res) {
                cms_infoBox("Seite gelöscht!");
                
                $goPage = $pageInfo[page];
                $seconds = 2;

                reloadPage ($goPage, $seconds);
                return 1;
            } else {
                cms_errorBox("Fehler beim Löschen von Seite");
            }
            
                
        }

        if ($_POST[deleteCancel]) {

            cms_infoBox("Löschen der Seite abgebrochen.");
            $goPage = $pageInfo[page];
            $seconds = 1;

            reloadPage ($goPage, $seconds);
            return 1;
        }

    } else {
        $deletePage = $page;
    }


    

    echo ("<form method='post'>\n");

    echo ("<input type='hidden' name='deletePage[id]' value='$deletePage[id]' >");


    echo ("<input type='submit' class='cmsInputButton' name='deleteButton' value='löschen'> &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton cmsSecond' name='deleteCancel' value='abbrechen' >");
    echo ("<form>");


}

function cms_siteMap_editPageForm($pageId) {
    global $cmsName;
    
    $editMode = "page";
    $disabled = "";
    
    if (strpos($pageId,"_")) {
        $splitList = explode("_",$pageId);
        $mainId = intval($splitList[0]);
        $subId  = intval($splitList[1]);
        
        // echo ("EDIT DYNAMIC PAGE $mainId $subId <br>");
        $editMode = "dynamic";
        $mainPageData = cms_page_getData($mainId);
        
         $page = $mainPageData;
        
        $data = $mainPageData[data];
        if ($data AND is_string($data)) $data = str2Array ($data);
        if (!is_array($data)) $data = array();
        $dataSource = $data[dataSource];
        $page[data] = $data;
        
        echo ("Data $data , $dataSource $mainPageData[data] <br>");
        $disabled = "disabled='disabled'";
        switch ($dataSource) {
            case "category" :
                $mainCatId = $data[mainCat];
                $subCatId = $data[subCat];
                if ($subCatId) $catId = $subCatId;
                else $catId = $mainCatId;
                $catList = cmsCategory_getList(array("mainCat"=>$catId,"show"=>1),"name");
                $catName = $catList[$subId][name];   
                // $catName = cmsCategory_getName_byId($catId);                    
                $page[title] = $catName;
                $page[name] = $page[name]."|"."category=".$subId;
                break;
        }
        
        
       
        
        
    } else {
        $pageId = intval($pageId);
        $page = cms_page_getData($pageId);
        $data = $page[data];
        if ($data AND is_string($data)) $data = str2Array ($data);
        if (!is_array($data)) $data = array();
        $page[data] = $data;
       //echo ("Data $data , $dataSource $page[data] <br>");
    }
    
    
    
    
   //  echo ("EDIT SiteMape ($pageId)!!! <br>");
    //echo ("SESSION $_SESSION[cmsName]<br>");

    
    
    $data = $page[data];
    if ($data AND is_string($data)) {
        $data = str2Array($data);
    }
    if (!is_array($data)) $data = array();
    

    $savePageData = $_POST[savePageData];
    $saveButton = $_POST[saveButton];
    $saveAndClose = $_POST[saveAndClose];

    if ($_POST[saveCancel]) {
        $goPage = $pageInfo[page];
        reloadPage ($goPage, 0);
        return 0;
    }

    $editPage = $_POST[editPage];
    if (is_array($editPage)) {
        // show_array($editPage);

        if ($saveButton OR $saveAndClose) $save = 1;
        if ($save) {
            $changeData = array();

            // correcturen für checkBox
            if (!$editPage[navigation]) $editPage[navigation] = "0";
            if (!$editPage[breadcrumb]) $editPage[breadcrumb] = "0";
            if (!$editPage[showLevel]) $editPage[showLevel] = "0";
            if (!$editPage[dynamic]) $editPage[dynamic] = "0";
            
            if (is_array($editPage[data])) $data = array2Str ($editPage[data]);


            // Check Data
            $error = array();
            if (strlen($editPage[name])<3) $error[name] = "Name zu kurz";
            if (strlen($editPage[title])<3) $error[title] = "Titel zu kurz";


            if (count($error)>0) {
                $str = "";
                foreach ($error as $key => $value) {
                    if ($str != "") $str.= "<br>";
                    $str.= $value;
                }
                cms_errorBox($str);
            } else {
                // DATA OK
                $pageExist = cms_page_exist($editPage[name]);

                $id = $editPage[id];
                
                if (strpos($id,"_")) {
                    echo ("DONT SAVE with id = '$id' <br> ");
                } else {
                    // echo ("SAVE with id = '$id' <br> ");
                    foreach ($editPage as $key => $value ) {
                        switch ($key) {
                            case "data" :
                                // show_array($value);
                                $value = array2Str($value);

                                // echo ("compare Data $value <=> $page[$key] <br>");

                                if ($page[$key] != $value) {
                                    $changeData[$key] = $value;
                                    $pageData[$key] = $value;
                                }
                                break;
                            default :
                                if ($page[$key] != $value) {
                                    $changeData[$key] = $value;
                                    $pageData[$key] = $value;

                                }
                                // echo (" -> $key = $value <br>");
                        }
                    }

                    if (count($changeData) == 0) {
                        cms_infoBox("keine Veränderung / nicht gespeichert");

                        $goPage = $pageInfo[page];                  
                    } else {
                        $res = cms_page_update($id,$changeData);
                        if ($res == 1) {
                            cms_infoBox("Daten gespeichert !!");
                            if ($saveButton) { $goPage = $pageInfo[page]; $seconds=3; }
                            if ($saveAndClose) { $goPage = $pageInfo[page]; $seconds = 1;}

                            reloadPage ($goPage, $seconds);
                            if ($saveAndClose) return 1;
                        }
                    }
                }
            }
       }
    } else {
        $editPage = $page;
    }
    
    


    

    echo ("Seite editieren '$page[name]' <br>");

    // foreach($pageInfo as $key => $value) echo ("PageInfo $key = $value <br>");

    // foreach($pageData as $key => $value) echo ("PageData $key = $value <br>");
    $div1 = div_start_str("inputLine").div_start_str("inputLeft","width:200px;float:left;padding-top:5px;");
    $div2 = div_end_str("inputLeft").div_start_str("inputRight","float:left;");
    $div3 = div_end_str("inputRight").div_end_str("inputLine","before");

    echo ("<form method='post'>\n");

    echo ("<input type='hidden' name='editPage[id]' value='$editPage[id]' >");


    echo ($div1."Name:");
    echo ($div2."<input type='text' $disabled name='editPage[name]' value='$editPage[name]' >\n");
    echo ($div3);

    echo ($div1."Titel:");
    echo ($div2."<input type='text' $disabled name='editPage[title]' value='$editPage[title]' >\n");
    echo ($div3);

    if ($editMode == "page") {
        echo ($div1."Übergeordnete Seite:");
        echo ($div2.cms_page_SelectMainPage($editPage[mainPage],"editPage[mainPage]"));
        echo ($div3);
    }

    if ($editMode == "page") {
        echo ($div1."Zeigen ab UserLevel");
        echo ($div2.cms_user_SelectLevel($editPage[showLevel],$_SESSION[userLevel],"editPage[showLevel]"));
        echo ($div3);
    }
    
    if ($editMode == "page") {
        echo ($div1."Zeigen bis UserLevel");
        echo ($div2.cms_user_SelectLevel($editPage[toLevel],$_SESSION[userLevel],"editPage[toLevel]"));
        echo ($div3);
    }

    if ($editMode == "page") {
        echo ($div1."Layout:");
        echo ($div2.cms_layout_SelectLayout($editPage[layout],"editPage[layout]"));
        echo ($div3);
    }
    
    if ($editMode == "page") {
        $contentType_class = cms_contentTypes_class();
        $special_viewFilter = $contentType_class->use_special_viewFilter($editPage,"editPage");
        if (is_array($special_viewFilter)) {
            foreach($special_viewFilter as $key => $value) {
                echo ($div1.$value[text]);
                echo ($div2.$value[input]);
                //show_array($value);
                echo ($div3);                             
            }
        }
    }
    

    echo ($div1."Seite in Navigation anzeigen:");
    echo ($div2."<input type='checkbox' $disabled name='editPage[navigation]' value='1' ");
    if ($editPage[navigation]) echo "checked='checked'";
    echo (">\n");
    echo ($div3);

    echo ($div1."Seiten-Pfad anzeigen:");
    echo ($div2."<input type='checkbox' $disabled name='editPage[breadcrumb]' value='1' ");
    if ($editPage[breadcrumb]) echo "checked='checked'";
    echo (">\n");
    echo ($div3);

    echo ($div1."Dynamische Inhalte");
    echo ($div2."<input type='checkbox' name='editPage[dynamic]' value='1' ");
    if ($editPage[dynamic]) echo "checked='checked'";
    echo (">\n");echo ($div3);

    if ($editPage[dynamic]) {
        $dataSource = $editPage[data][dataSource];
        
        echo ("Dynamische Seite '$dataSource' <br />");
        echo ($div1."Quelle Dynamische Inhalte:");
        echo ($div2.cms_dynamicPage_Source($dataSource,"editPage[data][dataSource]"));
        echo ($div3);

        $dynamicEditData = cms_dynamicPage_editSource($editPage,$dataSource,1);
        if (is_array($dynamicEditData)) {
            for ($i=0;$i<count($dynamicEditData);$i++) {
                $name = $dynamicEditData[$i][name];
                $input = $dynamicEditData[$i][input];
                echo ($div1.$name.$div2.$input.$div3);
            }
        }


        echo ($div1."Dynamische Inhalte - Ebene2");
        echo ($div2."<input type='checkbox' name='editPage[data][dynamic2]' value='1' ");
        if ($editPage[data][dynamic2]) echo "checked='checked'";
        echo (">\n");echo ($div3);
        
        $dataSource2 = $editPage[data][dataSource2];
        echo ($div1."Quelle 2. Ebene Dynamische Inhalte:");
        echo ($div2.cms_dynamicPage_Source($dataSource2,"editPage[data][dataSource2]"));
        echo ($div3);

        $dynamicEditData = cms_dynamicPage_editSource($editPage,$dataSource2,2);
        if (is_array($dynamicEditData)) {
            for ($i=0;$i<count($dynamicEditData);$i++) {
                $name = $dynamicEditData[$i][name];
                $input = $dynamicEditData[$i][input];
                echo ($div1.$name.$div2.$input.$div3);
            }
        }

        
    }

    echo ("<input type='submit' class='cmsInputButton' name='saveAndClose' value='speichern und schließen' > &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton' name='saveButton' value='speichern'> &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton cmsSecond'name='saveCancel' value='abbrechen' > &nbsp; ");
    echo ("<form>");
}

function cms_siteMap_addPageForm($pageId) {
    // echo ("ADD PAGE $pageId <br>");
    $pageId = intval($pageId);
    
    $page = cms_page_getData($pageId);
    // show_array($page);


    $savePageData = $_POST[savePageData];
    $saveButton = $_POST[saveButton];
    $saveAndClose = $_POST[saveAndClose];

    if ($_POST[saveCancel]) {
        $goPage = $pageInfo[page];
        reloadPage ($goPage, 0);
        return 0;
    }

    $addPage = $_POST[addPage];
    if (is_array($addPage)) {
        // show_array($addPage);

        if ($saveButton OR $saveAndClose) $save = 1;
        if ($save) {
            $changeData = array();

            // correcturen für checkBox
            if (!$addPage[navigation]) $addPage[navigation] = "0";
            if (!$addPage[breadcrumb]) $addPage[breadcrumb] = "0";
            if (!$addPage[showLevel]) $addPage[showLevel] = "0";


            // Check Data
            $error = array();
            if (strlen($addPage[name])<3) $error[name] = "Name zu kurz";
            if (strlen($addPage[title])<3) $error[title] = "Titel zu kurz";


            if (count($error)>0) {
                $str = "";
                foreach ($error as $key => $value) {
                    if ($str != "") $str.= "<br>";
                    $str.= $value;
                }
                cms_errorBox($str);
            } else {
                // DATA OK
                $pageExist = cms_page_exist($addPage[name]);
                if ($pageExist) {
                    cms_errorBox("Seite $addPage[name] existiert bereits");
                } else {
                     $res = cms_page_create($addPage[name],$addPage);
                     echo ("Page Create -> $res<br>");
                     if ($res) {
                         cms_infoBox("Seite $addPage[title] angelegt ");
                        $goPage = $pageInfo[page];
                        reloadPage ($goPage, 5);
                        return 0;
                     }


                }
                echo ("Page Exist = $pageExist<br>");


            }

        
       }



    } else {
        $addPage = array();
        $addPage[mainPage] = $pageId;
        $addPage[showLevel] = $page[showLevel];
        $addPage[layout] = $page[layout];
        $addPage[navigation] = $page[navigation];
        $addPage[breadcrumb] = $page[breadcrumb];
    }





    echo ("Neue Seite einfügen unterhalb von $page[name]<br>");

    // foreach($pageInfo as $key => $value) echo ("PageInfo $key = $value <br>");

    // foreach($pageData as $key => $value) echo ("PageData $key = $value <br>");
    $div1 = div_start_str("inputLine").div_start_str("inputLeft","width:200px;float:left;padding-top:5px;");
    $div2 = div_end_str("inputLeft").div_start_str("inputRight","float:left;");
    $div3 = div_end_str("inputRight").div_end_str("inputLine","before");

    echo ("<form method='post'>\n");

    echo ("<input type='hidden' name='addPage[addPageTo]' value='$pageId' >");


    echo ($div1."Name:");
    echo ($div2."<input type='text' name='addPage[name]' value='$addPage[name]' >\n");
    echo ($div3);

    echo ($div1."Titel:");
    echo ($div2."<input type='text' name='addPage[title]' value='$addPage[title]' >\n");
    echo ($div3);

    echo ($div1."Übergeordnete Seite:");
    echo ($div2.cms_page_SelectMainPage($addPage[mainPage],"addPage[mainPage]"));
    echo ($div3);

    echo ($div1."Zeigen ab UserLevel");
    echo ($div2.cms_user_SelectLevel($addPage[showLevel],$_SESSION[userLevel],"addPage[showLevel]"));
    echo ($div3);


    echo ($div1."Layout:");
    echo ($div2.cms_layout_SelectLayout($addPage[layout],"addPage[layout]"));
    echo ($div3);

    echo ($div1."Seite in Navigation anzeigen:");
    echo ($div2."<input type='checkbox' name='addPage[navigation]' value='1' ");
    if ($addPage[navigation]) echo "checked='checked'";
    echo (">\n");
    echo ($div3);

    echo ($div1."Seiten-Pfad anzeigen:");
    echo ($div2."<input type='checkbox' name='addPage[breadcrumb]' value='1' ");
    if ($addPage[breadcrumb]) echo "checked='checked'";
    echo (">\n");
    echo ($div3);

    echo ($div1."Dynamische Seite");
    echo ($div2."<input type='checkbox' name='addPage[dynamic]' value='1' ");
    if ($addPage[dynamic]) echo "checked='checked'";
    echo (">\n");echo ($div3);



    echo ("<input type='submit' class='cmsInputButton' name='saveAndClose' value='speichern und schließen' > &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton' name='saveButton' value='speichern'> &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton cmsSecond'name='saveCancel' value='abbrechen' > &nbsp; ");
    echo ("<form>");  
}


function cmsSitmap_show($page,$level,$edit) {
    show_array($page);
    $name = $page[name];
    $showName = $page[title];
    if (!$showName) $showName = $name;

    $divData = array();
    $style = "border:1px solid #eee;padding:2px 0 2px 2px;margin-left:".($level*20)."px;margin-bottom:5px;";
    $divName = "siteMap siteMapLevel_".$level;
    if ($edit) {
        $style .= "height:20px;width:200px;";
        $divName .= " siteMapClick";

    } else {
        $style .= "width:150px;height:20px;";
        echo ("<a href='$name.php'>");
    }
    $divData[style] = $style;
    div_start($divName,$divData);

    if ($edit) {
        echo ("<strong>$showName</strong>");
        // go Page
        echo ("<a href='$page[name].php' >zeigen</a> ");
        // edit
        echo ("&nbsp; <a href='sitemap.php?editPage=$page[id]' >e</a> ");
        // move Up
        if ($mainNr > 1) {
            echo ("&nbsp; <a href='sitemap.php?pageUp=$page[id]' >&uarr;</a> ");
        }
        // move Down
        if ($mainNr+1 < count($pageList) AND $mainNr>0) {
            echo ("&nbsp; <a href='sitemap.php?pageDown=$page[id]' >&darr;</a> ");
        }
        $mainNr ++;

    } else { // not Edit
        $pageShowLevel = $page[showLevel];
        if ($pageShowLevel <= $showLevel) {
            echo ("<strong>$showName</strong>"); // $pageShowLevel = $showLevel <br>");
        }
    }
    div_end($divName);
    if (!$edit) echo ("</a>");
}


function cms_sitemap_changeSort() {
    $pageDown = intval($_GET[pageDown]);
    $pageUp   = intval($_GET[pageUp]);

    if ($pageUp + $pageDown == 0) return 0;

    if ($pageDown) { $changeId = $pageDown; $direction = "down"; }
    if ($pageUp) {$changeId = $pageUp; $direction = "up"; }

    //  echo ("Change Sort $direction von Id $changeId <br>");

    // get DATA for ChangeId
    $changeData = cms_page_getData($changeId);
    if (!is_array($changeData)) {
        echo ("No ARrray for $changeId <br>");
        return 0;
    }

    $changeMainPage = $changeData[mainPage];
    // echo ("Change all Pages with MainPage is '$changeMainPage'<br>");
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `mainPage`=$changeMainPage ORDER by `sort`";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query $query <br>");
        return 0;
    }

    // Create ChangeList from Database
    $changeList = array();
    while ($page = mysql_fetch_assoc($result)) {
        $changeList[] = $page;
    }

    // DO SORT
    for ($i=0;$i<count($changeList);$i++) {
        $dataId = $changeList[$i][id];

        // Main Satz gefunden
        if ($changeId == $dataId ) {
            // echo ("Satz gefunden zum ändern <br>");
            $actSort = $changeList[$i][sort];
            $actSortId = $changeList[$i][id];
            if ($direction == "down") {
                $nextSort = $changeList[$i+1][sort];
                $nextSortId = $changeList[$i+1][id];

                // echo ("Change ACT with id = $actSortId to $nextSort <br>");
                cms_page_changeSort($actSortId, $nextSort);

                // echo ("Change NEXT with id = $nextSortId to $actSort <br>");
                cms_page_changeSort($nextSortId, $actSort);

                return 1;
            }
            if ($direction == "up") {
                $beforeSort = $changeList[$i-1][sort];
                $beforeSortId = $changeList[$i-1][id];
                
                // echo ("Change ACT with id = $actSortId to $beforeSort <br>");
                cms_page_changeSort($actSortId, $beforeSort);

                // echo ("Change BEFORE with id = $beforeSortId to $actSort <br>");
                cms_page_changeSort($beforeSortId, $actSort);

                return 1;
            }
        }
    }
    return 0;
}
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
