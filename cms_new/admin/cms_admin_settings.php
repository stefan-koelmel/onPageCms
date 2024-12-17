<?php

class cmsAdmin_settings_base extends cmsAdmin_editClass_base {
    
    function admin_dataSource() {
        return "settings";
    }
    
    function show_list() {
        $this->show();
    }
    
    function show() {
        
        if (!function_exists("tableList")) {
            
            $root = $_SERVER[DOCUMENT_ROOT]."/";
            $fn = "cms_".$this->cmsVersion."/admin/cms_admin_tables.php";
            if (file_exists($root.$fn)) {
                echo ("File $fn exist <br >");
                include($root.$fn);
            } else {
                echo ("File $fn not exist <br>");
            }
            if (!function_exists("tableList")) {
                echo ("Function tableList not exist<br>");
                return 0;
            }
        }
        
        $frameWidth = $this->frameWidth;
        
        $cmsName = $GLOBALS[cmsName];
        global $cmsName;
        $this->cmsName = $cmsName;
        // $cmsName =cms_getCmsName();

        $tabName = "settings";
        if ($_POST[selectedTab]) $tabName = $_POST[selectedTab];
        if ($_GET[selectedTab]) $tabName = $_GET[selectedTab];
        
        
        // echo ('<script type="text/javascript">var activeEditTab="'.$tabName.'";</script>');

        $tabList = array();
        $tabList[settings] = "Seiten-Einstellungen sL = $this->showLevel";
        if ($this->showLevel >= 9) {// superAdmin
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




        if ($this->showLevel >= 9) {
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
        $testVersion = $this->session_get(cmsVersion); // $_SESSION[cmsVersion];
        
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
        $testVersion = $this->session_get(cmsVersion); 

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
        if ($this->showLevel < 9) {
            echo "Not Allowed";
            return 0;
        }
        echo ("<h1>Use Special Data</h1>");
//        return 0;
//        // $showList = array("dates" => "Termine", "company" => "Hersteller", "product" => "Produkte", "category" => "Kategorien", "email" => "eMail Verwaltung", "user" => "Benutzer", "location" => "Locations", "importExport" => "Import & Export", "articles" => "Artikel", "images" => "Bilder","project"=>"Projekte");
//
//        $showList = cmsAdmin_data_getAll();
        $showList = array();
        $showList["dates"] = "Termine";
        $showList["company"] = "Hersteller";
        $showList["product"] = "Produkte";
        $showList["email"] = "eMail Verwaltung";
        $showList["user"] = "Benutzer";
        $showList["category"] = "Kategorien";
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
            if ($editData[specialData][$type]) $checked = "checked='checked'"; else $checked="";
            echo("<input type='checkbox' value='1' name='editData[specialData][$type]' $checked /><br />");
            if ($editData[specialData][$type]) {
                cmsAdmin_checkTable($type, $this->cmsName);
                cmsAdmin_checkPage($type, $this->cmsName);
            }
        }
    }

    function show_buttons($frameWidth,$editData) {
         echo ("<input type='submit' class='cmsInputButton' name='editSave' value='Einstellungen Speichern'>");
    }
    
    
    function saveSettings() {
        $cmsSettings = $this->pageClass->cmsSettings;
//        echo ($this->pageClass->cmsSettings[width]."<br>");
//        echo ($cmsSettings[width]."<br>");
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
        if (function_exists("cms_text_checkLanguages")) {
            $lgCheckResult = cms_text_checkLanguages($editLanguages);
            if ($lgCheckResult) {
                cms_errorBox($lgCheckResult);
                $reload = 0;            
            }
        } else {
            echo ("Funktion 'cms_text_checkLanguages' not exist <br>");
        }
        
        if (function_exists("cmsFaq_checkLanguage")) {
            $faqCheckResult = cmsFaq_checkLanguage($editLanguages);
            if ($faqCheckResult) {
                cms_errorBox($faqCheckResult);
                $reload = 0;            
            }
        } else {
            echo ("Funktion 'cms_text_checkLanguages' not exist <br>");
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
            $this->pageClass->cmsSettings = $editData;
            $cmsSettings = $editData;
            $this->session_set(cmsSettings,$cmsSettings);
            
            $goPage .= "?selectedTab=$_POST[selectedTab]";
            
            
            if ($this>showLevel==9) $this->update_setVersion();
            
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



function cmsAdmin_selectVersion($mode) {
    
    
    
    $standardVersion = $GLOBALS[cmsVersion];
    global $defaultCmsVersion;
    if ($defaultCmsVersion) {
        // echo ("Desfault Version  = $defaultCmsVersion<br>");
        $standardVersion = $defaultCmsVersion;        
    }
    $testVersion = $_SESSION[cmsVersion];
    $testVersion = $this->session_get(cmsVersion);
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
    $testVersion = $this->session_get(cmsVersion); 
    
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
    $phpStr .= '$cmsName = "'.$testVersion.'";'.$lf;
    $phpStr .= '$cmsVersion = "'.$set_cmsVersion.'";'.$lf;
    if ($set_testVersion) {
        $phpStr .= '$useVersion = $_SESSION[cmsVersion];'.$lf;
        $phpStr .= 'if ($useVersion != $cmsVersion AND $useVersion) {'.$lf;

        $phpStr .= '$defaultCmsVersion = $cmsVersion;'.$lf;
        $phpStr .= '$cmsVersion = $useVersion;'.$lf;
        $phpStr .= '}'.$lf;
        $this->session_set(cmsVersion,$set_testVersion);
    } else {
        $this->session_set(cmsVersion,null);        
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




?>
