<?php

class cmsAdmin_settings_base extends cmsAdmin_editClass_base {
    
    function show($frameWidth) {
        
        // global $cmsSettings;
        // show_array($cmsSettings);
        $cmsName = $GLOBALS[cmsName];
        global $cmsName;
        $this->cmsName = $cmsName;
        // $cmsName =cms_getCmsName();

        $tabName = "settings";
        if ($_POST[selectedTab]) $tabName = $_POST[selectedTab];
        if ($_GET[selectedTab]) $tabName = $_GET[selectedTab];
        
        
        // echo ('<script type="text/javascript">var activeEditTab="'.$tabName.'";</script>');

        $tabList = array();
        $tabList[settings] = "Seiten-Einstellungen";
        if ($_SESSION[showLevel] >= 9) {// superAdmin
            $tabList[data] = "Daten - Quellen";
            $tabList[modul] = "Module";            
        }
        $tabList[update] = "CMS-Version / Update";

        $editData = $this->saveSettings();
        echo ("<form method='post'>");
        echo ("<input  type='hidden' value='$editData[id]' name='editData[id]'>");
        echo ("<input  type='hidden' value='$editData[name]' name='editData[name]'>");
//        echo (span_text_str("Name:", 200));
//        echo ("<input type='text' value='$editData[name]' name='unneed' DISABLED><br>");
        
        div_start("cmsContentEditFrame");
        div_start("cmsEditTabLine");
        foreach($tabList as $key => $value) {
            $divName = "cmsEditTab cmsEditTab_$key"; //editMode_More editMode_hidden";
            if ($key == $tabName) $divName .= " cmsEditTab_selected";
            $divData = array();
            $divData["id"] = "cmsEditTab_$key";

            div_start($divName,$divData);
            echo ($value);
            div_end($divName);

        }
        echo ("<input class='cmsEditTabName' name='selectedTab' value='$tabName' style='width:50px;height:12px;font-size:10px' type='hidden'>");
        div_end("cmsEditTabLine","before");

        // Content for Frames
        foreach($tabList as $key => $value) {
            $divName = "cmsEditFrame cmsEditFrame_$key";
            if ($key != $tabName) $divName .= " cmsEditFrameHidden";
            $divData = array();
            $divData["id"] = "cmsEditTabFrame_$key";

            div_start($divName,$divData);
            switch ($key) {
                case "settings" : $this->show_settings($frameWidth, $editData); break;
                case "modul" : $this->show_modul($frameWidth, $editData); break;
                case "data" : $this->show_data($frameWidth,$editData); break;
                case "update" : $this->show_update($frameWidth, $editData); break;
                
                default :
                    echo ("Hier ist der Inhalt von '$value' <br />");

            }
            
            div_end($divName);        
        }
        
        // Show Buttons
        $this->show_buttons($frameWidth,$editData);
        echo ("</form>");
        div_end("cmsContentEditFrame");
    }
    
    function show_settings($frameWidth,$editData) {
     
        span_text("StandardTitle:", 200);
        echo ("<textarea name='editData[title]' style='width:400px;height:80px;' >$editData[title]</textarea><br>");


        span_text("Standard-Beschreibung:", 200);
        echo ("<textarea name='editData[description]' style='width:400px;height:80px;' >$editData[description]</textarea><br>");


        span_text("Standard-Keywords:", 200);
        echo ("<textarea name='editData[keywords]' style='width:400px;height:80px;' >$editData[keywords]</textarea><br>");

        
        span_text("Status von Seite:", 200);
        echo ($this->settings_SelectState($editData[state], "editData[state]")."<br />");
        // echo ("<input type='text' value='$editData[width]' name='editData[width]' ><br>");

        
        span_text("Fenster-Breite:", 200);
        echo ("<input type='text' value='$editData[width]' name='editData[width]' ><br>");


        span_text("StandardLayout:", 200);
        echo( cms_layout_SelectLayout($editData[layout], "editData[layout]") . "<br>");

        span_text("Stil:", 200);
        echo (cmsStyle_selectTheme($editData[normal_theme],"editData[normal_theme]","normal"));
        echo (" Wirframe:".cmsStyle_selectTheme($editData[wireframe_theme],"editData[wireframe_theme]","wireframe")."<br />");

        span_text("Editier Farbe:", 200);
        echo ($this->settings_SelectColor($editData[editColor], "editData[editColor]")."<br />");

        span_text("Editier Modus:", 200);
        echo( cms_contentType_SelectEditMode($editData[editMode], "editData[editMode]") . "<br>");

        span_text("Sprache:", 200);
        echo ($this->settings_SelectLanguage($editData[language], "editData[language]")."<br />");

       
        
        // Mobil Pages
        span_text("Mobile Anpassungen:", 200);
        if ($editData[mobilPages]) $checked = "checked='checked'";
        else $checked = "";
        echo("<input type='checkbox' value='1' name='editData[mobilPages]' $checked /><br>");


        // Cache
        span_text("Cache:", 200);
        echo("<input type='checkbox' value='1' name='editData[cache]' ");
        if ($editData[cache]) echo (" checked='checked'");
        echo ("><br>");

         // Bookmarks
        span_text("Bookmarks:", 200);
        echo("<input type='checkbox' value='1' name='editData[bookmarks]' ");
        if ($editData[bookmarks]) echo (" checked='checked'");
        echo ("><br>");

         // History
        $historyCount = $editData[history];
        if (is_null($historyCount)) $historyCount = 5;

       // span_text("History:", 200);
    //    if ($historyCount) $value = $historyCount;
    //    else $value = 5;

        span_text("Anzahl History Seiten:", 200);
        echo ("<input type='text' value='$historyCount' name='editData[history]' /> 0 für aus <br/>");




        if ($_SESSION[showLevel] >= 9) {
            span_text("Wireframe:", 200);
            echo("<input type='checkbox' value='1' name='editData[wireframe]' ");
            if ($editData[wireframe])
                echo (" checked='checked'");
            echo ("><br>");
            
            if ($editData[wireframe]) {
                span_text("Wireframe Startzusand:", 200);
                echo("<input type='checkbox' value='1' name='editData[wireframeOn]' ");
                if ($editData[wireframeOn])
                    echo (" checked='checked'");
                echo ("><br>");
                
            }
        }

    }
    
    function show_modul($frameWidth, $editData) {
       
        $typeList = cms_contentType_getSortetList("all");
        $allwaysUse = cms_contentType_allwaysUse();
        global $cmsTypes;
            

        $usedType = array();

        $dataType =  $editData[specialData];
        
        $targetTypes = array("page"=>"Seiten Module","layout"=>"Layout Module","data"=>"Daten-Module");
        
        foreach ($targetTypes as $target => $targetName) {
            if (!is_array($typeList[$target])) continue;
            div_start("typeList");
            echo ("<b>$targetName</b><br />");
            $nr = 1;
            foreach ($typeList[$target] as $type => $typeData) {
                if (substr($type,0,5)=="frame") {
                    $type = "frame";
                    $typeData[name] = "Spalten (1-4)";
                    // echo ("<b>SPALTEN </b>$target ");
                }
                
                if ($usedType[$type]) continue;
                

                $usedType[$type] = 1;

                // Name
                $typeName = $type;
                if ($typeData[name]) $typeName = $typeData[name];
              
                // show
                $show = 1;

                switch ($target) {
                    case "data" :
                        switch ($type) {
                            case "date" : $searchIn = "dates"; break;
                            case "article" : $searchIn = "articles"; break;
                            default:
                                $searchIn = $type;
                        }

                        $useData = $dataType[$searchIn];
                        $show = 0;
                        if ($useData) $show = 1;
                        //echo ("data $type - in Target $target / useData = $useData <br>");
                        break;
                }

                if ($show) {
                    $style = "width:32%;float:left;padding:0px;margin-bottom:5px;";
                    if ($nr < 3) $style .= "margin-right:2%;";

                    div_start("typeSelect",$style);
                    
                    $readOnly = 0;
                    if ($allwaysUse[$type]) $readOnly = 1;
//                    switch ($type) {
//                        case "frame" : $readOnly = 1; break;
//                        case "login" : $readOnly = 1; break;
//                            //
//                    }
                    if ($editData[useType][$type]) $checked = "checked='checked'";
                    else $checked = "";
                    if ($readOnly) {
                         echo("<input type='checkbox' value='1' disabled='disabled' name='hiddenType' checked='checked' />");
                         echo ("<input type='hidden' value='1' name='editData[useType][$type]' >");                        
                    } else {
                        echo("<input type='checkbox' value='1' name='editData[useType][$type]' $checked />");
                    }

                    echo ("$typeName");
                    div_end("typeSelect");
                    $nr++;
                    if ($nr>3) $nr = 1;
                }
                
                $use = $typeData["use"];               
            }
            div_end("typeList","before");            
        }
        
    }
    
    function show_update($frameWidth,$editData) {
         // if ($_SESSION[showLevel]==9) {
            echo (span_text_str("cmsVersion:", 200));
            $this->update_selectVersion("cms");
            echo ("<br />");

            echo (span_text_str("Test - cmsVersion:", 200));
            $this->update_selectVersion("test");
            echo ("<br />");
        //}    
    }
    
    
    function update_selectVersion($mode) {
        $standardVersion = $GLOBALS[cmsVersion];
        global $defaultCmsVersion;
        if ($defaultCmsVersion) {
            // echo ("Desfault Version  = $defaultCmsVersion<br>");
            $standardVersion = $defaultCmsVersion;        
        }
        $testVersion = $_SESSION[cmsVersion];
        
        if (!$this->versionList) {
            $this->versionList = $this->update_findCmsVersion();
        }
        $versionList = $this->update_findCmsVersion();

        switch ($mode) {
            case "cms" :
                echo ("<select value='$standardVersion' name='editData[cmsVersion]'>");
                // echo ("<option value='' >nicht Testen</option>");

                foreach ($this->versionList as $code => $value) {
                    if ($standardVersion == $code) $selected = "selected='1'";
                    else $selected = "";
                    echo ("<option value='$code' $selected >CMS-Version '$code'</option>");
                }
                echo ("</select>");

                // echo ("<input type='text' value='$standardVersion' name='cmsVersion'><br>");
                break;

            case "test" :
                echo ("<select value='$testVersion' name='editData[testVersion]' >");
                echo ("<option value='' >nicht Testen</option>");

                foreach ($this->versionList as $code => $value) {
                    if ($testVersion == $code) $selected = "selected='1'";
                    else $selected = "";
                    echo ("<option value='$code' $selected >CMS-Version '$code' </option>");
                }
                echo ("</select>");
                // echo ("<input type='text' value='$testVersion' name='testVersion'><br>");
                break;

        }
    }

    function update_setVersion() {
        $editData = $_POST[editData];
        if (!is_array($editData) ) return 0;
        $set_cmsVersion = $editData["cmsVersion"];
        $set_testVersion = $editData["testVersion"];

        // foreach ($editData as $key => $value ) echo ("$key = $value <br />");
        
//        echo ("CMSNAME = $this->cmsName <br />");
//        echo ("SET VERSION = $set_cmsVersion <br />");
//        echo ("TEST VERSION = $set_testVersion <br />");
        
        $standardVersion = $GLOBALS[cmsVersion];
        global $defaultCmsVersion;
        if ($defaultCmsVersion) $standardVersion = $defaultCmsVersion;        
        $testVersion = $_SESSION[cmsVersion];

        if (!$set_cmsVersion) return 0;
        $change = 0;
        $out = "";
        if ($set_cmsVersion != $standardVersion) {
            $out .= "Change cmsVersion  from '$standardVersion' to '$set_cmsVersion' <br />";
            $change = 1;
        }
        if ($set_testVersion != $standardVersion) {
            $out .= "Change testVersion  from '$testVersion' to '$set_testVersion' <br />";
            $change = 1;
        }
        if (!$change) return 0;
        $phpStr = "";
        $lf = "\n";
        $phpStr .= '<?php'.$lf;
        $phpStr .= 'global $cmsName,$cmsVersion,$defaultCmsVersion;'.$lf;
        $phpStr .= '$cmsName = "'.$this->cmsName.'";'.$lf;
        $phpStr .= '$cmsVersion = "'.$set_cmsVersion.'";'.$lf;
        if ($set_testVersion) {
            $phpStr .= '$useVersion = $_SESSION[cmsVersion];'.$lf;
            $phpStr .= 'if ($useVersion != $cmsVersion AND $useVersion) {'.$lf;

            $phpStr .= '$defaultCmsVersion = $cmsVersion;'.$lf;
            $phpStr .= '$cmsVersion = $useVersion;'.$lf;
            $phpStr .= '}'.$lf;
            $_SESSION[cmsVersion] = $set_testVersion;
        } else {
            unset($_SESSION[cmsVersion]);
        }
        $phpStr .= '?>';
        saveText($phpStr,"cmsSettings.php");

        cms_infoBox($out);
        echo ($phpStr);
    }

    function update_findCmsVersion() {
        $root = $_SERVER[DOCUMENT_ROOT]."/";
        $res = array();
        if (file_exists($root)) {
            $handle = opendir($root);
            // $res.= "suche in Folder $folder <br>";
            while ($file = readdir ($handle)) {
                if($file != "." && $file != ".." AND !$dontUse[$file]) {
                    if(!is_dir($root."/".$file)) continue;
                    if (substr($file,0,4) != "cms_") continue;

                    $version = substr($file,4);
                    $checkFile = $root."cms_".$version."/cms.php";
                    if (!file_exists($checkFile)) {
                        //echo ("No CheckFile $checkFile <br>");
                        continue;
                    }


                    $checkFile = $root."cms_".$version."/version.info";
                    if (!file_exists($checkFile)) {
                        // echo ("No InfoFile $checkFile <br>");
                        continue;
                    }

                    // echo ("CMS-Version:$version<br />");

                    $info = loadText($checkFile);
                    // echo ("CMS-Info:$info<br />");        
                    $res[$version] = array();
                    $res[$version][name] = "$version";
                    $res[$version][info] = $info;
                }
            }
        }
        return $res;
    }


    function settings_SelectState($type,$dataName) {
        $typeList = array();
        // $typeList[grey] = array("name"=>"Grau");
        $typeList[online] = array("name"=>"Online");
        $typeList[construction] = array("name"=>"Baustelle");
        $typeList[inWork] = array("name"=>"Wartung");
        
        $str = "";
        $str.= "<select name='$dataName' class='cmsSelectType' value='$type' >";
        
        if (!$type) $type = "online";

        foreach ($typeList as $code => $typeData) {
             $str.= "<option value='$code'";
             if ($code == $type)  $str.= " selected='1' ";
             $str.= ">$typeData[name]</option>";
        }
        $str.= "</select>";

        return $str;

    }
    
    function settings_selectLanguage($language,$dataName) {
        
        $languages = cms_text_languageList();
        $languageList = cms_text_getSettings();
        $activeLanguage = cms_text_getLanguage();
        $adminLanguage = cms_text_adminLg();
        
        $str = "<div style='display:inline-block;' >";
  
        $str.= "<select name='activeLanguages' class='cmsSelectType' value='$activeLanguage' >";

        if (!$activeLanguage ) {
            $str.= "<option value='0' selected='1' >Default</option>";
        }
        
        foreach ($languageList as $code => $typeData) {
             $active = $value[active];
             $str.= "<option value='$code'";
             
             if ($code == $activeLanguage)  $str.= " selected='1' ";
             if (!$languageList[$code][enabled]) $str.= "disabled='disabled' ";
             $languageName = $languages[$code];
             $str.= ">$languageName</option>";
        }
        $str.= "</select> <span class='cmsEditLanguage'>Sprachen bearbeiten</span><br />";
        $activeLanguage = array();
        $activeLanguage[en] = 1;
        
        foreach ($languages as $key => $languageName) {
            $languageData = $languageList[$key];
            
            $editable = $languageData[editable];
            $enabled   = $languageData[enabled];
            
            $str .= "<span style='width:100px;display:inline-block;'>$languageName :</span>";
            if ($editable) $checked = "checked='checked' ";
            else $checked = "";
            $str.= "<input type='checkbox' name='languageEditable[$key]' value='1' $checked />editierbar  ";
            
            
            if ($enabled) $checked = "checked='checked' ";
            else $checked = "";
            $str.= "<input type='checkbox' name='languageEnabled[$key]' value='1' $checked />auswählbar  ";
            
            
            $str .= "<br />";            
        }
        
        
        // ADMIN LAnguage
        
        $str .= "ADMIN LAnguage : ".$this->settings_SelectAdminLanguage($adminLanguage,"adminLanguage");
        
        
        $str .= "</div>";
        return $str;
    }

    
    function settings_SelectAdminLanguage($language,$dataName) {
        
        $languages = cms_text_languageList();
        $languageList = cms_text_getSettings();
        // $activeLanguage = cms_text_getLanguage();
        
        $str.= "<select name='$dataName' class='cmsSelectType' >";

        if (!$language ) {
            $str.= "<option value='0' selected='1' >Default</option>";
        }
        
        foreach ($languageList as $code => $typeData) {
             // $active = $value[active];
             $str.= "<option value='$code'";
             
             if ($code == $language)  $str.= " selected='1' ";
             if (!$typeData[enabled]) $str.= "disabled='disabled' ";
             
             $languageName = $typeData[name];
             $str.= ">$languageName</option>";
        }
        $str.= "</select>";
        return $str;        
    }

    function settings_SelectColor($type,$dataName) {
        $typeList = array();
        // $typeList[grey] = array("name"=>"Grau");
        $typeList[white] = array("name"=>"Weiß");
        $typeList[black] = array("name"=>"Schwarz");
        
        $typeList[orange] = array("name"=>"Orange");
        $typeList[blue] = array("name"=>"Blau");
        $typeList[green] = array("name"=>"Grün");
        $typeList[olive] = array("name"=>"Olive");
        $typeList[lightBlue] = array("name"=>"Hellblau");
        $typeList[pink] = array("name"=>"Pink");
        $typeList[red] = array("name"=>"Rot");

        $str = "";
        $str.= "<select name='$dataName' class='cmsSelectType' value='$type' >";

         $str.= "<option value='0'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">Default</option>";

        foreach ($typeList as $code => $typeData) {
             $str.= "<option value='$code'";
             if ($code == $type)  $str.= " selected='1' ";
             $str.= ">$typeData[name]</option>";
        }
        $str.= "</select>";

        return $str;


    }
    
    
    function show_data($frameWidth,$editData) {
        if ($_SESSION[showLevel] >= 9) {// superAdmin
            echo ("<h1>Use Special Data</h1>");
            // $showList = array("dates" => "Termine", "company" => "Hersteller", "product" => "Produkte", "category" => "Kategorien", "email" => "eMail Verwaltung", "user" => "Benutzer", "location" => "Locations", "importExport" => "Import & Export", "articles" => "Artikel", "images" => "Bilder","project"=>"Projekte");

            $showList = cmsAdmin_data_getAll();
            $showList = array();
            $showList["dates"] = "Termine";
            $showList["company"] = "Hersteller";
            $showList["product"] = "Produkte";
            $showList["category"] = "Kategorien";
            $showList["email"] = "eMail Verwaltung";
            $showList["user"] = "Benutzer";
            $showList["location"] = "Locations";
            $showList["importExport"] = "Import & Export";
            $showList["articles"] = "Artikel";
            $showList["images"] = "Bilder";
            $showList["project"] = "Projekte";
            $showList["basket"] = "Warenkorb";
            $showList["order"] = "Bestellungen";
            $showList["faq"] = "Fragen und Antworten";


            if (is_string($editData[specialData])) $editData[specialData] = str2Array($editData[specialData]);
            
            foreach ($showList as $type => $name) {
                span_text($name . ":", 200);
                echo("<input type='checkbox' value='1' name='editData[specialData][$type]' ");
                if ($editData[specialData][$type]) echo (" checked='checked'");
                echo ("><br>");
                if ($editData[specialData][$type]) {
                    cmsAdmin_checkTable($type, $this->cmsName);
                    cmsAdmin_checkPage($type, $this->cmsName);
                }
            }
        }

    }
    
    function show_buttons($frameWidth,$editData) {
         echo ("<input type='submit' class='cmsInputButton' name='editSave' value='Einstellungen Speichern'>");
    }
    
    
    function saveSettings() {
        global $cmsSettings;
        if (!$_POST) return $cmsSettings;
        
        if (!$_POST[editSave]) return $cmsSettings;
        
        
        echo ("<h1> SAVE SETTINGS </h1>");
        $reload = 1;
        $editData = $_POST[editData];
        if (!is_array($editData)) return $cmsSettings;
        
        $activeLanguage = $_POST[activeLanguages];
        $enabledLanguages = $_POST[languageEnabled];
        $editableLanguages = $_POST[languageEditable];
        $adminLanguage = $_POST[adminLanguage];
        
        if ($activeLanguage) {
            cms_text_getLanguage($activeLanguage);
        }
        
        if ($adminLanguage) {
            cms_text_adminLg($adminLanguage);
        }
        
//        echo ("ACTIVE LANGUAGE = $activeLanguage <br />");
//        echo ("editable Language = $enabledLanguages <br />");
//        if (is_array($editableLanguages)) foreach($editableLanguages as $key => $value) echo("$key => $value <br>");
//        echo ("enabled Language = $enabledLanguages <br />");
//        if (is_array($enabledLanguages)) foreach($enabledLanguages as $key => $value) echo("$key => $value <br>");
        $languages = cms_text_languageList();
        
        $languageStr = "";
        $editLanguages = array();
        foreach ($languages as $key => $languageName) {
            $editAble = $editableLanguages[$key];
            $enabled  = $enabledLanguages[$key];
            
            if ($editAble OR $enabled) {
                $add = "";
                $add .= $key.":";
                if ($enabled) $add .= "1:";
                else $add.= "0:";
                
                if ($editAble) {
                    $add .= "1:";
                    $editLanguages[$key] = 1;
                } else $add.= "0:";
                
                if ($key == $activeLanguage ) $add.= "1:";
                else $add .= "0:";
                
                if ($key == $adminLanguage ) $add.= "1";
                else $add .= "0";
                
                
                
                if ($languageStr) $languageStr .= "|";
                $languageStr .= $add;
            }
        }
        $lgCheckResult = cms_text_checkLanguages($editLanguages);
        if ($lgCheckResult) {
            cms_errorBox($lgCheckResult);
            $reload = 0;            
        }
        $editData[language] = $languageStr;
        
        
        $query = "";

        if (!$editData[cache]) $editData[cache] = 0;
        if (!$editData[bookmarks]) $editData[bookmarks] = 0;
        if (!$editData[history]) $editData[history] = 0;
        if (!$editData[wireframe]) $editData[wireframe] = 0;
        if (!$editData[wireframeOn]) $editData[wireframeOn] = 0;


        foreach ($editData as $key => $value) {
            // echo ("Save $key = $value <br>");
            switch ($key) {
                case "name" : break;
                case "id" : $saveId = $value;
                    break;
                
                case "testVersion" : break;

                case "data" :
                    $dataStr = array2Str($value);
                    if ($query != "") $query.= ", ";
                    $query .= " `data` = '$dataStr' ";
                    break;

                case "useType" :
                    $useTypes = $value;
                    $useTypeStr = array2str($value);
                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$useTypeStr'";
                    break;
                    
                case "specialData" :
                    $useSpecialData = array2str($value);
                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$useSpecialData'";

                    foreach ($value as $key => $data) {
                        // echo ("special $key = $data <br>");
                    }

                    break;
                default :
                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$value'";
            }
        }
        $query = "UPDATE `cms_settings` SET $query WHERE `id`=$saveId ";
        
        // echo ("$query <br>");
        $result = mysql_query($query);
        if ($result) {
            cms_infoBox("Einstellungen gespeichert");
            $cacheState = cmsCache_state();
            if ($editData[cache] AND !$cacheState) { cmsCache_enable(); $cacheStateChange = 1;}
            if (!$editData[cache] AND $cacheState) { cmsCache_disable(); $cacheStateChange = 1;}
            if ($cacheStateChange) {
                $cacheState = cmsCache_state();
                if ($cacheState) cms_infoBox ("Cache wurde eingeschaltet");
                else cms_infoBox ("Cache wurde ausgeschaltet");
            }
            $cmsSettings = $editData;
            $_SESSION[cmsSettings] = $cmsSettings;
            
            
            
            $goPage .= "?selectedTab=$_POST[selectedTab]";
            
            
            if ($_SESSION[showLevel]==9) $this->update_setVersion();
            
            if ($reload) reloadPage($goPage, 2);
            
        } else {
            cms_errorBox("Fehler beim Einstellungen speichern<br />$query");
        }
        return $editData;

    }
    
}


function cms_admin_settings($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_settings_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_settings();

    } else {
        $class = new cmsAdmin_settings_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    $class->show($frameWidth);
}
// charset:UTF-8

function cms_admin_settings_old($frameWidth) {
    global $cmsSettings;
    // show_array($cmsSettings);
    $cmsName = $GLOBALS[cmsName];
    global $cmsName;
    // $cmsName =cms_getCmsName();

    $query = "SELECT * FROM `cms_settings` WHERE `name` = '$cmsName' ";
    //global $cmsName;
    // $cmsName = "empty";
    
    


    echo ("CMS NAME = '$cmsName' <br> ");
    //$editData = cms_settings_get();


    $goPage = $GLOBALS[pageInfo][page];
    // echo ("GOPAGE = $goPage<br>");

    $editData = $_POST[editData];
    if (is_array($editData)) {
        $query = "";

        if (!$editData[cache]) $editData[cache] = 0;
        if (!$editData[bookmarks]) $editData[bookmarks] = 0;
        if (!$editData[history]) $editData[history] = 0;
        if (!$editData[wireframe]) $editData[wireframe] = 0;


        foreach ($editData as $key => $value) {
            // echo ("Save $key = $value <br>");
            switch ($key) {
                case "name" : break;
                case "id" : $saveId = $value;
                    break;

                case "data" :
                    $dataStr = array2Str($value);
                    if ($query != "") $query.= ", ";
                    $query .= " `data` = '$dataStr' ";
                    break;

                case "useType" :
                    $useTypes = $value;
                    $useTypeStr = array2str($value);
                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$useTypeStr'";
                    break;
                case "specialData" :
                    $useSpecialData = array2str($value);
                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$useSpecialData'";

                    foreach ($value as $key => $data) {
                        // echo ("special $key = $data <br>");
                    }

                    break;
                default :
                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$value'";
            }
        }
        $query = "UPDATE `cms_settings` SET $query WHERE `id`=$saveId ";
        $result = mysql_query($query);
        if ($result) {
            cms_infoBox("Einstellungen gespeichert");
            $cacheState = cmsCache_state();
            if ($editData[cache] AND !$cacheState) { cmsCache_enable(); $cacheStateChange = 1;}
            if (!$editData[cache] AND $cacheState) { cmsCache_disable(); $cacheStateChange = 1;}
            if ($cacheStateChange) {
                $cacheState = cmsCache_state();
                if ($cacheState) cms_infoBox ("Cache wurde eingeschaltet");
                else cms_infoBox ("Cache wurde ausgeschaltet");
            }
            $cmsSettings = $editData;
            $_SESSION[cmsSettings] = $cmsSettings;
            
            
            
            
            
            
             if ($_SESSION[showLevel]==9) cmsAdmin_setVersion();
            
             reloadPage($goPage, 2);
        } else {
            cms_errorBox("Fehler beim Einstellungen speichern<br />$query");
        }
        
       
        
        
        
        
    } else {
        $editData = $GLOBALS[cmsSettings];
    }


    echo ("<form method='post'>");
    echo ("<input  type='hidden' value='$editData[id]' name='editData[id]'>");
    echo ("<input  type='hidden' value='$editData[name]' name='editData[name]'>");
    echo (span_text_str("Name:", 200));
    echo ("<input type='text' value='$editData[name]' name='unneed' DISABLED><br>");

    if ($_SESSION[showLevel]==9) {
        echo (span_text_str("cmsVersion:", 200));
        cmsAdmin_selectVersion("cms");
        echo ("<br />");
        
        echo (span_text_str("Test - cmsVersion:", 200));
        cmsAdmin_selectVersion("test");
        echo ("<br />");
    }    
    
    
    span_text("StandardTitle:", 200);
    echo ("<textarea name='editData[title]' style='width:400px;height:80px;' >$editData[title]</textarea><br>");


    span_text("Standard-Beschreibung:", 200);
    echo ("<textarea name='editData[description]' style='width:400px;height:80px;' >$editData[description]</textarea><br>");


    span_text("Standard-Keywords:", 200);
    echo ("<textarea name='editData[keywords]' style='width:400px;height:80px;' >$editData[keywords]</textarea><br>");

    span_text("FensterBreite:", 200);
    echo ("<input type='text' value='$editData[width]' name='editData[width]' ><br>");


    span_text("StandardLayout:", 200);
    echo( cms_layout_SelectLayout($editData[layout], "editData[layout]") . "<br>");

    span_text("Stil:", 200);
    echo (cmsStyle_selectTheme($editData[normal_theme],"editData[normal_theme]","normal"));
    echo (" Wirframe:".cmsStyle_selectTheme($editData[wireframe_theme],"editData[wireframe_theme]","wireframe")."<br />");

    span_text("Editier Farbe:", 200);
    echo (cmsSettings_SelectColor($editData[editColor], "editData[editColor]")."<br />");

    span_text("Editier Modus:", 200);
    echo( cms_contentType_SelectEditMode($editData[editMode], "editData[editMode]") . "<br>");


    // Bookmarks
    span_text("Mobile Anpassungen:", 200);
    if ($editData[mobilPages]) $checked = "checked='checked'";
    else $checked = "";
    echo("<input type='checkbox' value='1' name='editData[mobilPages]' $checked /><br>");
    
    
    // Cache
    span_text("Cache:", 200);
    echo("<input type='checkbox' value='1' name='editData[cache]' ");
    if ($editData[cache]) echo (" checked='checked'");
    echo ("><br>");

     // Bookmarks
    span_text("Bookmarks:", 200);
    echo("<input type='checkbox' value='1' name='editData[bookmarks]' ");
    if ($editData[bookmarks]) echo (" checked='checked'");
    echo ("><br>");

     // History
    $historyCount = $editData[history];
    if (is_null($historyCount)) $historyCount = 5;

   // span_text("History:", 200);
//    if ($historyCount) $value = $historyCount;
//    else $value = 5;

    span_text("Anzahl History Seiten:", 200);
    echo ("<input type='text' value='$historyCount' name='editData[history]' /> 0 für aus <br/>");




    if ($_SESSION[showLevel] >= 9) {
        span_text("Wireframe:", 200);
        echo("<input type='checkbox' value='1' name='editData[wireframe]' ");
        if ($editData[wireframe])
            echo (" checked='checked'");
        echo ("><br>");
    }


    if ($_SESSION[showLevel] >= 9) {// superAdmin
        echo ("<h1>DATA</h1>");

        $data = $editData[data];
        if (!is_array($data)) {
            if ($editData[data]) $data = str2Array($editData[data]);
            else $data = array();
        }


        // need
        span_text("spezielle Daten:", 200);
        echo("<input type='text' value='".$data[need]."' name='editData[data][need]' >");

        echo ("<br>");



    }



    if ($_SESSION[showLevel] >= 9) {// superAdmin
        echo ("<h1>Use Special Data</h1>");
        // $showList = array("dates" => "Termine", "company" => "Hersteller", "product" => "Produkte", "category" => "Kategorien", "email" => "eMail Verwaltung", "user" => "Benutzer", "location" => "Locations", "importExport" => "Import & Export", "articles" => "Artikel", "images" => "Bilder","project"=>"Projekte");

        $showList = cmsAdmin_data_getAll();
        $showList = array();
        $showList["dates"] = "Termine";
        $showList["company"] = "Hersteller";
        $showList["product"] = "Produkte";
        $showList["category"] = "Kategorien";
        $showList["email"] = "eMail Verwaltung";
        $showList["user"] = "Benutzer";
        $showList["location"] = "Locations";
        $showList["importExport"] = "Import & Export";
        $showList["articles"] = "Artikel";
        $showList["images"] = "Bilder";
        $showList["project"] = "Projekte";
        $showList["basket"] = "Warenkorb";
        $showList["order"] = "Bestellungen";


        if (is_string($editData[specialData]))
        $editData[specialData] = str2Array($editData[specialData]);
        foreach ($showList as $type => $name) {
            span_text($name . ":", 200);
            echo("<input type='checkbox' value='1' name='editData[specialData][$type]' ");
            if ($editData[specialData][$type])
                echo (" checked='checked'");
            echo ("><br>");
            if ($editData[specialData][$type]) {
                cmsAdmin_checkTable($type, $cmsName);
                cmsAdmin_checkPage($type, $cmsName);
            }
        }

        echo ("<h1>Use Types </h1>");
        $typeList = cms_contentType_getTypes();
        if (is_string($editData[useType])) $editData[useType] = str2Array($editData[useType]);
        foreach ($typeList as $type => $typedata) {
            $name = $typedata[name];
            span_text($name . ":", 200);
            echo("<input type='checkbox' value='1' name='editData[useType][$type]' ");
            if ($editData[useType][$type])
                echo (" checked='checked'");
            echo ("><br>");
        }


        // show_array($typeList);
    }





    echo ("<input type='submit' class='cmsInputButton' name='editSave' value='Einstellungen Speichern'>");




    echo ("</form>");
//
//    $dbname = "db360967548";
//
//        $query = "SHOW TABLES FROM $dbname LIKE 'schaufenster%' ";
//        $result = mysql_query($query);
//
//        if (!$result) {
//            echo "DB Fehler, konnte Tabellen nicht auflisten\n";
//            echo 'MySQL Fehler: ' . mysql_error();
//            exit;
//        }
//
//        while ($row = mysql_fetch_row($result)) {
//            $table = $row[0];
//            echo "Tabelle: {$row[0]}<br>";
//            //foreach ($row as $key => $value) echo (" - $key = $value <br>");
//
//            $queryRow = "select * from `".$table."`";
//            $resultRow = mysql_query($queryRow);
//            if (!$resultRow) {
//                die('Anfrage fehlgeschlagen: $queryRow ' . mysql_error());
//            }
//            /* Metadaten der Felder */
//            $i = 0;
//            while ($i < mysql_num_fields($resultRow)) {
//                // echo "Information für Feld $i:<br />\n";
//                $meta = mysql_fetch_field($resultRow, $i);
//                if (!$meta) {
//                    echo "Keine Information vorhanden<br />\n";
//                } else {
//                    echo (" -- ".$meta->name." (".$meta->type.")<br>");
//                    // show_array($meta);
//                }
//                $i++;
//            }
//
//        }
    //cms_frame_editor($frameWidth);
    //show_array($editData);
}

function cmsAdmin_selectVersion($mode) {
    
    
    
    $standardVersion = $GLOBALS[cmsVersion];
    global $defaultCmsVersion;
    if ($defaultCmsVersion) {
        // echo ("Desfault Version  = $defaultCmsVersion<br>");
        $standardVersion = $defaultCmsVersion;        
    }
    $testVersion = $_SESSION[cmsVersion];
    
    $versionList = cmsAdmin_findCmsVersion();
    
    switch ($mode) {
        case "cms" :
            echo ("<select value='$standardVersion' name='cmsVersion'>");
            // echo ("<option value='' >nicht Testen</option>");
            
            foreach ($versionList as $code => $value) {
                if ($standardVersion == $code) $selected = "selected='1'";
                else $selected = "";
                echo ("<option value='$code' $selected >CMS-Version '$code'</option>");
            }
            echo ("</select>");
            
            // echo ("<input type='text' value='$standardVersion' name='cmsVersion'><br>");
            break;
        
        case "test" :
            echo ("<select value='$testVersion' name='testVersion' >");
            echo ("<option value='' >nicht Testen</option>");
            
            foreach ($versionList as $code => $value) {
                if ($testVersion == $code) $selected = "selected='1'";
                else $selected = "";
                echo ("<option value='$code' $selected >CMS-Version '$code' </option>");
            }
            echo ("</select>");
            // echo ("<input type='text' value='$testVersion' name='testVersion'><br>");
            break;
            
    }
}

function cmsAdmin_setVersion() {
    $set_cmsVersion = $_POST["cmsVersion"];
    $set_testVersion = $_POST["testVersion"];
    
    
    $standardVersion = $GLOBALS[cmsVersion];
    global $defaultCmsVersion;
    if ($defaultCmsVersion) $standardVersion = $defaultCmsVersion;        
    $testVersion = $_SESSION[cmsVersion];
    
    $change = 0;
    $out = "";
    if ($set_cmsVersion != $standardVersion) {
        $out .= "Change cmsVersion  from '$standardVersion' to '$set_cmsVersion' <br />";
        $change = 1;
    }
    if ($set_testVersion != $standardVersion) {
        $out .= "Change testVersion  from '$testVersion' to '$set_testVersion' <br />";
        $change = 1;
    }
    if (!$change) return 0;
    $phpStr = "";
    $lf = "\n";
    //$lf = "<br>";
    
    $phpStr .= '<?php'.$lf;
    $phpStr .= 'global $cmsName,$cmsVersion,$defaultCmsVersion;'.$lf;
    $phpStr .= '$cmsName = "'.$GLOBALS[cmsName].'";'.$lf;
    $phpStr .= '$cmsVersion = "'.$set_cmsVersion.'";'.$lf;
    if ($set_testVersion) {
        $phpStr .= '$useVersion = $_SESSION[cmsVersion];'.$lf;
        $phpStr .= 'if ($useVersion != $cmsVersion AND $useVersion) {'.$lf;

        $phpStr .= '$defaultCmsVersion = $cmsVersion;'.$lf;
        $phpStr .= '$cmsVersion = $useVersion;'.$lf;
        $phpStr .= '}'.$lf;
        $_SESSION[cmsVersion] = $set_testVersion;
    } else {
        unset($_SESSION[cmsVersion]);
    }
    $phpStr .= '?>';
    saveText($phpStr,"cmsSettings.php");

    cms_infoBox($out);
    echo ($phpStr);
    
}

function cmsAdmin_findCmsVersion() {
    $root = $_SERVER[DOCUMENT_ROOT]."/";
    $res = array();
    if (file_exists($root)) {
        $handle = opendir($root);
        // $res.= "suche in Folder $folder <br>";
        while ($file = readdir ($handle)) {
            if($file != "." && $file != ".." AND !$dontUse[$file]) {
                if(!is_dir($root."/".$file)) continue;
                if (substr($file,0,4) != "cms_") continue;

                $version = substr($file,4);
                $checkFile = $root."cms_".$version."/cms.php";
                if (!file_exists($checkFile)) {
                    //echo ("No CheckFile $checkFile <br>");
                    continue;
                }


                $checkFile = $root."cms_".$version."/version.info";
                if (!file_exists($checkFile)) {
                    // echo ("No InfoFile $checkFile <br>");
                    continue;
                }

                // echo ("CMS-Version:$version<br />");

                $info = loadText($checkFile);
                // echo ("CMS-Info:$info<br />");        
                $res[$version] = array();
                $res[$version][name] = "$version";
               // $res[$version][info] = $info;
            }
        }
    }
    return $res;
}

        
        


function cmsSettings_SelectColor($type,$dataName) {
    $typeList = array();
    // $typeList[grey] = array("name"=>"Grau");
    $typeList[white] = array("name"=>"Weiß");
    $typeList[black] = array("name"=>"Schwarz");
   
    $typeList[orange] = array("name"=>"Orange");
    $typeList[blue] = array("name"=>"Blau");
    $typeList[green] = array("name"=>"Grün");
    $typeList[olive] = array("name"=>"Olive");
    $typeList[lightBlue] = array("name"=>"Hellblau");
    $typeList[pink] = array("name"=>"Pink");
    $typeList[red] = array("name"=>"Rot");

    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' value='$type' >";

     $str.= "<option value='0'";
     if ($code == $type)  $str.= " selected='1' ";
     $str.= ">Default</option>";

    foreach ($typeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">$typeData[name]</option>";
    }
    $str.= "</select>";

    return $str;


}


function cmsAdmin_checkPage($type, $cmsName) {

}

function cmsAdmin_checkTable($tableName, $cmsName) {
    switch ($tableName) {
        case "importExport" : return 0;
        case "basket" : return 0;
    }
    if ($tableName == "cms_settings") $table = $tableName;
    else $table = $cmsName . "_cms_" . $tableName;
    $query = "SELECT * FROM `$table` LIMIT 0,1";
    $result = mysql_query($query);
    if ($result) {

        $query2 = "DESCRIBE `$table` ";
        $result2 = mysql_query($query2);
        $tableData = tableData($tableName);
        if ($result2 AND is_array($tableData)) {
            while ($row = mysql_fetch_array($result2)) {
                $key = $row['Field'];
                $type = $row['Type'];
                if (is_array($tableData[$key])) {
                    $tableType = $tableData[$key][0];
                    if ($tableType != $type) {
                        echo ("Unterschiedlicher Typ in Tablle '$table' in Feld '$key' -> $tableType != $type <br>");
                    }
                    $tableData[$key] = "exist";
                } else {
                    echo ("Feld not Exist '$key' in Tablle '$table' $type <br>");

                }
            }

            $addQuery = "";
            $addText = "";
            foreach ($tableData as $key => $value) {

                if ($value != "exist") {
                    if ($addText)
                        $addText .= "<br />";
                    $addText .= "neues Feld '$key'";
                    // echo ("Not Exist $key $value <br>");
                    if ($addQuery)
                        $addQuery .= ", \n";
                    $addQuery .= "ADD `$key` ";
                    for ($i = 0; $i < count($value); $i++) {
                        $addQuery.= $value[$i] . " ";
                    }
                    if ($lastKey)
                        $addQuery.= " AFTER `$lastKey`";
                }
                $lastKey = $key;
            }
            if ($addQuery) {
                $addQuery = "ALTER TABLE `$table` " . $addQuery;
                // echo ("Query <br>$addQuery <br>");
                $result = mysql_query($addQuery);
                if ($result) {
                    cms_infoBox("Neue Felder angelegt <br>$addText");
                } else {
                    cms_errorBox("Fehler beim anlegen von Feldern <br>$addText<br>$query");
                }
            }
        }
    } else {
        echo ("cmsTable Check $tableName, $cmsName $table not exist <br />");
        $tableData = tableData($tableName);
        if (is_array($tableData)) {
            $query = "CREATE TABLE `$table` (\n";
            foreach ($tableData as $key => $value) {
                $query.= "`$key` ";
                for ($i = 0; $i < count($value); $i++) {
                    $query.= $value[$i] . " ";
                }
                $query .= ",\n";
                //echo ("$key = $value <br>");
            }
            $query .= "KEY `id` (`id`)\n";
            $query .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";
            $result = mysql_query($query);
            if (!$result) {
                cms_errorBox("Fehler beim Anlegen von '$table' <br>" . str_replace("\n", "<br>", $query));
            } else {
                cms_infoBox("Datenbank '$table' angelegt!");
            }
        }
    }
}

function tableList($cmsType="") {
    $res = array();
    $res["user"] = array("name"=>"BENUTZER","title"=>"CMS Benutzerveraltung","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>1);
    $res["userData"] = array("name"=>"BENUTZER-DATEN","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    $res["bookmarks"] = array("name"=>"BOOKMARKS","createDB"=>1,"adminPage"=>0,"target"=>"DATA","createDATA"=>0);


    $res["settings"] = array("name"=>"SETTINGS","title"=>"CMS Einstellungen","createDB"=>0,"adminPage"=>1,"target"=>"SETTINGS","createDATA"=>0);

    $res["pages"] = array("name"=>"SEITEN","createDB"=>1,"adminPage"=>0,"target"=>"","createDATA"=>1);
    $res["layout"] = array("name"=>"LAYOUT","title"=>"CMS Layouts","createDB"=>0,"adminPage"=>1,"target"=>"SETTINGS","createDATA"=>1);
    $res["content"] = array("name"=>"INHALT","createDB"=>1,"adminPage"=>0,"target"=>"","createDATA"=>1);
    $res["text"] = array("name"=>"TEXT","createDB"=>1,"adminPage"=>0,"target"=>"","createDATA"=>1);

    $res["images"] = array("name"=>"BILD","title"=>"CMS Bilder","createDB"=>1,"adminPage"=>1,"target"=>"SETTINGS","createDATA"=>0);
    $res["importExport"] = array("name"=>"IMPORT/EXPORT","title"=>"CMS Import / Export","createDB"=>0,"adminPage"=>1,"target"=>"SETTINGS","createDATA"=>0);

    $res["style"] = array("name"=>"Stile","title"=>"CMS LAYOUTS","createDB"=>1,"adminPage"=>0,"target"=>"DATA","createDATA"=>0);


    $use = array("dates"=>1,"product"=>1,"company"=>1,"category"=>1,"email","location"=>1,"articles"=>1,"project"=>1,"faq"=>1,"order"=>1);
    $dontUse = array();
    switch($cmsType) {
        case "basic" :
            $dontUse = array("dates"=>0,"product"=>0,"company"=>0,"location"=>0,"articles"=>0,"project"=>0,"faq"=>0,"order"=>0);
            break;
        case "data" :
            $dontUse = array("dates"=>0,"order"=>0);
            break;
        
        case "dataPlus" :
            $dontUse = array("order"=>0);
            break;
        case "basket" : break;
        case "unlimited" : 
            $dontUse = array("order"=>0);
            break;
        case "single" : 
            $dontUse = array("order"=>0);
            break;
        case "u5" : break;    
        case "u10" : break;            
    }
    foreach ($dontUse as $key => $value) $use[$key] = $value;

    if ($use["dates"]) $res["dates"] = array("name"=>"TERMIN","title"=>"CMS Termine","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["product"]) $res["product"] = array("name"=>"PRODUKT","title"=>"CMS Pruduktverwaltung","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["company"])  $res["company"] = array("name"=>"HERSTELLER","title"=>"CMS Herstellerverwaltung","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["category"])  $res["category"] = array("name"=>"KATEGORIE","title"=>"CMS Kategorien","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["email"])  $res["email"] = array("name"=>"EMAIL","title"=>"CMS Emailvorlagen","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["location"])  $res["location"] = array("name"=>"ORTE","title"=>"CMS Ortverwaltung","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["articles"])  $res["articles"] = array("name"=>"ARTIKEL","title"=>"CMS Artikelverwaltung","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["project"])  $res["project"] = array("name"=>"PROJEKTE","title"=>"CMS Projektverwaltung","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);
    if ($use["faq"]) $res["faq"] = array("name"=>"FAQ","title"=>"CMS FAQ","createDB"=>1,"adminPage"=>0,"target"=>"","createDATA"=>0);
    if ($use["order"]) $res["order"] = array("name"=>"BESTELLUNGEN","title"=>"CMS Bestellungen","createDB"=>1,"adminPage"=>1,"target"=>"DATA","createDATA"=>0);

    return $res;
}

function tableData($type) {
    switch ($type) {

        case "cms_settings" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL AUTO_INCREMENT");
            $table["name"] = array("tinytext","CHARACTER SET latin1 NOT NULL");
            $table["longName"] = array("tinytext","CHARACTER SET latin1 NOT NULL");
            $table["title"] = array("tinytext","CHARACTER SET latin1 NOT NULL");
            $table["description"] = array("tinytext","CHARACTER SET latin1");
            $table["keywords"] = array("tinytext","CHARACTER SET latin1 NOT NULL");
            $table["cmsVersion"] = array("tinytext","CHARACTER SET latin1 NOT NULL");
            $table["show"] = array("tinyint(4)","NOT NULL DEFAULT '1'");
            $table["cache"] = array("varchar(1)","NOT NULL DEFAULT '1'");
            $table["bookmarks"] = array("varchar(1)","NOT NULL");
            $table["history"] = array("varchar(1)","NOT NULL");
            $table["wireframe"] = array("varchar(1)","NOT NULL DEFAULT '0'");
            $table["layout"] = array("tinytext","CHARACTER SET latin1 NOT NULL");
            $table["normal_theme"] = array("tinytext","NOT NULL");
            $table["wireframe_theme"] = array("tinytext","NOT NULL");
            $table["editColor"] = array("tinytext","NOT NULL");
            $table["editMode"] = array("tinytext","NOT NULL");
            $table["width"] = array("int(11)","NOT NULL");
            $table["border"] = array("text","CHARACTER SET latin1 NOT NULL");
            $table["background"] = array("text","CHARACTER SET latin1 NOT NULL");
            $table["data"] = array("text","NOT NULL");
            $table["specialData"] = array("text","CHARACTER SET latin1 NOT NULL");
            $table["useType"] = array("text","CHARACTER SET latin1");
            return $table;
            break;

        case "text" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["contentId"] = array("tinytext","NOT NULL");
            $table["name"] = array("tinytext","NOT NULL");
            $table["css"] = array("tinytext");
            $table["data"] = array("text","NOT NULL");
            $table["lg_dt"] = array("text","NOT NULL");
            return $table;
            break;

        case "pages" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["name"] = array("tinytext","NOT NULL");
            $table["title"] = array("tinytext");
            $table["description"] = array("text");
            $table["keywords"] = array("text", "NOT", "NULL");
            $table["imageId"] = array("tinytext");
            $table["dynamic"] = array("varchar(1)","NOT NULL default '0'");
            $table["layout"] = array("tinytext","NOT NULL");
            $table["navigation"] = array("tinyint(4)","NOT NULL");
            $table["breadcrumb"] = array("varchar(1)","NOT NULL");
            $table["sort"] = array("int(11)","default '0'");
            $table["showLevel"] = array("tinyint(4)","NOT NULL default '0'");
            $table["toLevel"] = array("tinyint(4)","NOT NULL default '0'");
            $table["mainPage"] = array("int(11)","NOT NULL");
            $table["data"] = array("text","NOT NULL");
            return $table;
            break;

        case "content" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["pageId"] = array("tinytext");
            $table["title"] = array("tinytext");
            $table["contentName"] = array("tinytext","NOT NULL");
            $table["sort"] = array("tinyint(4)","NOT NULL default '0'");
            $table["showLevel"] = array("tinyint(4)","NOT NULL default '0'");
            $table["toLevel"] = array("tinyint(4)","NOT NULL default '0'");
            $table["type"] = array("tinytext");
            $table["frameLink"] = array("tinytext");
            $table["frameStyle"] = array("tinytext");
            $table["frameFloat"] = array("tinytext");
            $table["frameWidth"] = array("tinytext");
            $table["frameHeight"] = array("tinytext");
            $table["data"] = array("text","NOT NULL");
            $table["wireframe"] = array("text","NOT NULL");
            return $table;
            break;
        case "images" :
            $table = array();
            $table["id"] = array("int(11)", "NOT NULL auto_increment");
            $table["fileName"] = array("text");
            $table["name"] = array("text");
            $table["subTitle"] = array("text", "NOT NULL");
            $table["md5"] = array("tinytext", "NOT NULL");
            $table["width"] = array("int(11)", "NOT NULL");
            $table["height"] = array("int(11)", "NOT NULL");
            $table["type"] = array("tinytext", "NOT NULL");
            $table["orgpath"] = array("text");
            $table["lastMod"] = array("datetime", "NOT NULL");
            $table["changeLog"] = array("text", "NOT NULL");
            return $table;
            break;

        case "category" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("text", "NOT", "NULL");
            $table["mainCat"] = array("int(11)", "NOT", "NULL");
            $table["sort"] = array("tinyint(4)", "NOT", "NULL");
            $table["image"] = array("int(11)", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");
            return $table;

       case "dates" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment", "");
            $table["date"] = array("date", "NOT", "NULL");
            $table["toDate"] = array("date", "NOT", "NULL");
            $table["name"] = array("text", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("text", "NOT", "NULL");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["category"] = array("int(11)", "NOT", "NULL");
            $table["region"] = array("int(11)", "NOT", "NULL");
            $table["location"] = array("int(11)", "NOT", "NULL");
            $table["locationStr"] = array("tinytext", "NOT", "NULL");
            $table["time"] = array("time", "NOT", "NULL");
            $table["toTime"] = array("time", "NOT", "NULL");
            $table["image"] = array("tinytext", "NOT", "NULL");
            $table["data"] = array("longtext", "NOT NULL");
            $table["link"] = array("tinytext", "NOT", "NULL");
            $table["highlight"] = array("varchar(1)", "NOT", "NULL");
            $table["new"] = array("varchar(1)", "NOT", "NULL");
            $table["cancel"] = array("varchar(1)", "NOT", "NULL", "default", "'0'");
            $table["show"] = array("varchar(1)", "NOT NULL", "default", "'0'");
            $table["lastMod"] = array("timestamp", "NOT", "NULL");
            $table["changeLog"] = array("text", "NOT", "NULL");
            return $table;
            break;


        case "product" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", " NULL");
            $table["info"] = array("text", "NOT", "NULL");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["company"] = array("int(11)", "NOT", "NULL");
            $table["category"] = array("tinytext");
            $table["subCategory"] = array("tinytext");
            $table["image"] = array("text", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL", "default '1'");
            $table["new"] = array("varchar(1)", "NOT", "NULL", "default '0'");
            $table["highlight"] = array("varchar(1)", "NOT", "NULL", "default '0'");
            $table["vk"] = array("float", "NOT", "NULL");
            $table["shipping"] = array("float", "NOT", "NULL");
            $table["count"] = array("tinyint(4)", "NOT", "NULL");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");
            return $table;

        case "company" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("text", "NOT", "NULL");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["category"] = array("tinytext", "NOT", "NULL");
            $table["subCategory"] = array("tinytext");
            $table["image"] = array("text");
            $table["url"] = array("tinytext", "NOT", "NULL");
            $table["data"] = array("text", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL default '1'");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");
            return $table;



        case "email" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("text", "NOT NULL");
            $table["data"] = array("text", "NOT NULL");
            $table["show"] = array("varchar(1)", "default '1'");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT NULL");
            return $table;

        case "location" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext");
            $table["subName"] = array("tinytext");
            $table["info"] = array("text");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["category"] = array("tinytext");
            $table["subCategory"] = array("tinytext");
            $table["url"] = array("tinytext");
            $table["ticketUrl"] = array("tinytext");
            $table["street"] = array("tinytext");
            $table["streetNr"] = array("tinytext");
            $table["plz"] = array("tinytext");
            $table["city"] = array("tinytext");
            $table["region"] = array("int(11)", "default", "NULL");
            $table["phoneRegion"] = array("tinytext");
            $table["phonePhone"] = array("tinytext");
            $table["phoneFax"] = array("tinytext");
            $table["phoneMobil"] = array("tinytext");
            $table["email"] = array("tinytext");
            $table["contactName"] = array("tinytext");
            $table["data"] = array("longtext");
            $table["image"] = array("tinytext");
            $table["show"] = array("varchar(1)", "default", "NULL");
            $table["lastMod"] = array("datetime", "default", "NULL");
            $table["changeLog"] = array("tinytext");
            return $table;

        case "articles" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("text");
            $table["subName"] = array("tinytext");
            $table["info"] = array("text");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["fromDate"] = array("date", "default", "NULL");
            $table["toDate"] = array("date", "default", "NULL");
            $table["dateRange"] = array("tinytext");
            $table["category"] = array("int(11)", "default", "NULL");
            $table["subCategory"] = array("int(11)", "default", "NULL");
            $table["region"] = array("int(11)", "default", "NULL");
            $table["location"] = array("int(11)", "default", "NULL");
            $table["url"] = array("text");
            $table["ticketUrl"] = array("text");
            $table["sort"] = array("tinyint(4)", "default", "NULL");
            $table["image"] = array("tinytext");
            $table["data"] = array("text");
            $table["link"] = array("tinytext");
            $table["show"] = array("varchar(1)", "default", "NULL");
            $table["highlight"] = array("varchar(1)", "default '3'");
            $table["new"] = array("varchar(1)", "default", "NULL");
            $table["lastMod"] = array("datetime", "default", "NULL");
            $table["changeLog"] = array("tinytext");
            return $table;

        case "user" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL auto_increment");
            $table["userName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("text");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["password"] = array("tinytext", "NOT", "NULL");
            $table["userLevel"] = array("tinyint(4)", "NOT", "NULL");
            $table["sessionId"] = array("tinytext", "NOT", "NULL");
            $table["email"] = array("tinytext", "NOT", "NULL");
            $table["newEmail"] = array("tinytext", "NOT", "NULL");
            $table["confirm"] = array("tinytext", "NOT", "NULL");
            $table["salut"] = array("varchar(1)", "NOT", "NULL");
            $table["vName"] = array("tinytext", "NOT", "NULL");
            $table["nName"] = array("tinytext", "NOT", "NULL");
            $table["company"] = array("tinytext", "NOT", "NULL");
            $table["street"] = array("tinytext", "NOT", "NULL");
            $table["streetNr"] = array("tinytext", "NOT", "NULL");
            $table["plz"] = array("tinytext", "NOT", "NULL");
            $table["city"] = array("tinytext", "NOT", "NULL");
            $table["country"] = array("tinytext", "NOT", "NULL");
            $table["phone"] = array("tinytext", "NOT", "NULL");
            $table["fax"] = array("tinytext", "NOT", "NULL");
            $table["mobil"] = array("tinytext", "NOT", "NULL");
            $table["url"] = array("tinytext", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL");
            $table["lastLogin"] = array("timestamp", "NOT", "NULL default '0000-00-00 00:00:00'");
            $table["first_log"] = array("timestamp", "NOT", "NULL default '0000-00-00 00:00:00'");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");


            return $table;
            break;

        case "userData" :

            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["userId"] = array("int(11)","NOT NULL");
            $table["type"] = array("tinytext","NOT NULL");
            $table["name"] = array("tinytext","NOT NULL");
            $table["breadCrumb"] = array("tinytext","NOT NULL");
            $table["url"] = array("tinytext","NOT NULL");
            $table["data"] = array("text","NOT NULL");
            return $table;
            break;

        case "project" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["name"] = array("text","");
            $table["subName"] = array("text","");
            $table["category"] = array("tinytext","");
            $table["subCategory"] = array("tinytext","");
            $table["info"] = array("text","");
            $table["longInfo"] = array("longtext","");
            $table["year"] = array("tinytext","");
            $table["customer"] = array("tinytext","");
            $table["dealer"] = array("tinytext","");
            $table["image"] = array("text","");
            $table["url"] = array("tinytext","NOT NULL");
            $table["sort"] = array("varchar(1)","NOT NULL default '3'");
            $table["highlight"] = array("varchar(1)","NOT NULL default '0'");
            $table["show"] = array("varchar(1)","NOT NULL default '1'");
            $table["lastMod"] = array("datetime","NOT NULL");
            $table["changeLog"] = array("tinytext","NOT NULL");
            return $table;
            break;

        case "faq" :
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["cat"] = array("tinyint(4)","");
            $table["sort"] = array("tinyint(4)","");
            $table["head_dt"] = array("tinytext","");
            $table["text_dt"] = array("text","");
            return $table;

         case "order" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["userId"] = array("int(11)","NOT NULL");
            $table["state"] = array("tinytext","NOT NULL");
            $table["info"] = array("longtext");
            $table["adress"] = array("text","NOT NULL");
            $table["shippingAdress"] = array("text","NOT NULL");

            $table["basket"] = array("text","NOT NULL");

            $table["value"] = array("float","NOT NULL");
            $table["shipping"] = array("float","NOT NULL");

            $table["payment"] = array("text","NOT NULL");

            $table["history"] = array("text","NOT NULL");
            $table["data"] = array("text","NOT NULL");

            $table["lastMod"] = array("datetime","NOT NULL");
            $table["changeLog"] = array("tinytext","NOT NULL");

            return $table;
            break;


        case "style" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["theme"] = array("tinytext","NOT NULL");
            $table["type"] = array("tinytext","NOT NULL");
            $table["name"] = array("tinytext","NOT NULL");
            $table["background"] = array("tinytext","NOT NULL");

            $table["color"] = array("tinytext","NOT NULL");

            $table["margin"] = array("tinytext","NOT NULL");
            $table["border"] = array("tinytext","NOT NULL");

            $table["radius"] = array("tinytext","NOT NULL");

            $table["padding"] = array("tinytext","NOT NULL");
            $table["font"] = array("tinytext","NOT NULL");

            $table["data"] = array("tinytext","NOT NULL");

            return $table;
            break;

    }
}





?>
