<?php

// charset:UTF-8

function cms_admin_settings($frameWidth) {
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
            echo ("Save $key = $value <br>");
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
                    if ($query != "")
                        $query.= ", ";
                    $query .= "`$key`='$value'";
            }
        }
        // echo($query."<br>");
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
            reloadPage($goPage, 2);
        } else {
            cms_errorBox("Fehler beim Einstellungen speichern<br$query");
        }
    } else {
        $editData = $GLOBALS[cmsSettings];
    }


    echo ("<form method='post'>");
    echo ("<input  type='hidden' value='$editData[id]' name='editData[id]'>");
    echo ("<input  type='hidden' value='$editData[name]' name='editData[name]'>");
    echo (span_text_str("Name:", 200));
    echo ("<input type='text' value='$editData[name]' name='unneed' DISABLED><br>");

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

    
    span_text("Editier Farbe:", 200);
    
    
    echo (cmsSettings_SelectColor($editData[editColor], "editData[editColor]")."<br />");
   
    span_text("Editier Modus:", 200);
    echo( cms_contentType_SelectEditMode($editData[editMode], "editData[editMode]") . "<br>");
 

    
    

    // echo ("<input type='text' value='$editData[layout]' name='editData[layout]' ><br>");

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
    if ($historyCount) $value = $historyCount;
    else $value = 5;
    
//    echo("<input type='checkbox' value='$value' name='editData[history]' ");
//    if ($editData[history]) echo (" checked='checked'");
//    echo ("><br>");
    
    //if ($editData[history]) {        
        span_text("Anzahl History Seiten:", 200);
        echo ("<input type='text' value='$historyCount' name='editData[history]' /> 0 für aus <br/>");
     //}
    
    
    
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
        if (is_string($editData[useType]))
            $editData[useType] = str2Array($editData[useType]);
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


function cmsSettings_SelectColor($type,$dataName) {
    $typeList = array();
    // $typeList[grey] = array("name"=>"Grau");
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
    }
    $table = $cmsName . "_cms_" . $tableName;
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

function tableData($type) {
    switch ($type) {
        
        case "text" :
            $table = array();
            $table["id"] = array("int(11) NOT NULL auto_increment");
            $table["contentId"] = array("tinytext NOT NULL");
            $table["name"] = array("tinytext NOT NULL");
            $table["css"] = array("tinytext");
            $table["data"] = array("text NOT NULL");
            $table["lg_dt"] = array("text NOT NULL");
            return $table;
            break;
        
        case "pages" :
            $table = array();
            $table["id"] = array("int(11) NOT NULL auto_increment");
            $table["name"] = array("tinytext NOT NULL");
            $table["title"] = array("tinytext");
            $table["dynamic"] = array("varchar(1)","NOT NULL default '0'");
            $table["layout"] = array("tinytext NOT NULL");
            $table["navigation"] = array("tinyint(4) NOT NULL");
            $table["breadcrumb"] = array("varchar(1) NOT NULL");
            $table["sort"] = array("int(11) default '0'");
            $table["showLevel"] = array("tinyint(4) NOT NULL default '0'");
            $table["toLevel"] = array("tinyint(4)","NOT NULL default '0'");
            $table["mainPage"] = array("int(11) NOT NULL");
            $table["data"] = array("text","NOT NULL");

            return $table;
            break;
          
            
        
        case "dates" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment", "");
            $table["date"] = array("date", "NOT", "NULL");
            $table["toDate"] = array("date", "NOT", "NULL");
            $table["name"] = array("text", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("longtext", "NOT", "NULL");
            $table["longInfo"] = array("longtext", "NOT", "NULL");
            $table["category"] = array("int(11)", "NOT", "NULL");
            $table["region"] = array("int(11)", "NOT", "NULL");
            $table["location"] = array("int(11)", "NOT", "NULL");
            $table["locationStr"] = array("tinytext", "NOT", "NULL");
            $table["time"] = array("time", "NOT", "NULL");
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
        
        case "content" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["pageId"] = array("tinytext");
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
            return $table;
            break;
            
            
        case "product" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", " NULL");
            $table["info"] = array("text", "NOT", "NULL");
            $table["company"] = array("int(11)", "NOT", "NULL");
            $table["category"] = array("tinytext");
            $table["image"] = array("int(11)", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL", "default '1'");
            $table["new"] = array("varchar(1)", "NOT", "NULL", "default '0'");
            $table["highlight"] = array("varchar(1)", "NOT", "NULL", "default '0'");
            $table["vk"] = array("float", "NOT", "NULL");
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
            $table["category"] = array("tinytext", "NOT", "NULL");
            $table["image"] = array("tinytext");
            $table["url"] = array("tinytext", "NOT", "NULL");
            $table["data"] = array("text", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL default '1'");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");
            
            return $table;

        case "category" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL", "auto_increment");
            $table["name"] = array("tinytext", "NOT", "NULL");
            $table["subName"] = array("tinytext", "NOT", "NULL");
            $table["info"] = array("text", "NOT", "NULL");
            $table["mainCat"] = array("int(11)", "NOT", "NULL");
            $table["image"] = array("int(11)", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL");
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
            $table["category"] = array("tinytext");
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
            $table["info"] = array("longtext");
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
            $table["info"] = array("longtext");
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
            $table["password"] = array("tinytext", "NOT", "NULL");
            $table["userLevel"] = array("tinyint(4)", "NOT", "NULL");
            $table["sessionId"] = array("tinytext", "NOT", "NULL");
            $table["email"] = array("tinytext", "NOT", "NULL");
            $table["salut"] = array("varchar(1)", "NOT", "NULL");
            $table["vName"] = array("tinytext", "NOT", "NULL");
            $table["nName"] = array("tinytext", "NOT", "NULL");
            $table["company"] = array("tinytext", "NOT", "NULL");
            $table["street"] = array("tinytext", "NOT", "NULL");
            $table["streetNr"] = array("tinytext", "NOT", "NULL");
            $table["plz"] = array("tinytext", "NOT", "NULL");
            $table["city"] = array("tinytext", "NOT", "NULL");
            $table["phone"] = array("tinytext", "NOT", "NULL");
            $table["fax"] = array("tinytext", "NOT", "NULL");
            $table["mobil"] = array("tinytext", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL");
            $table["lastLogin"] = array("timestamp", "NOT", "NULL default '0000-00-00 00:00:00'");
            $table["first_log"] = array("timestamp", "NOT", "NULL default '0000-00-00 00:00:00'");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");
            return $table;
            break;
        
        case "userData" :
            $table = array();
            $table["id"] = array("int(11)", "NOT", "NULL auto_increment");
            $table["userName"] = array("tinytext", "NOT", "NULL");
            $table["password"] = array("tinytext", "NOT", "NULL");
            $table["userLevel"] = array("tinyint(4)", "NOT", "NULL");
            $table["sessionId"] = array("tinytext", "NOT", "NULL");
            $table["email"] = array("tinytext", "NOT", "NULL");
            $table["salut"] = array("varchar(1)", "NOT", "NULL");
            $table["vName"] = array("tinytext", "NOT", "NULL");
            $table["nName"] = array("tinytext", "NOT", "NULL");
            $table["company"] = array("tinytext", "NOT", "NULL");
            $table["street"] = array("tinytext", "NOT", "NULL");
            $table["streetNr"] = array("tinytext", "NOT", "NULL");
            $table["plz"] = array("tinytext", "NOT", "NULL");
            $table["city"] = array("tinytext", "NOT", "NULL");
            $table["phone"] = array("tinytext", "NOT", "NULL");
            $table["fax"] = array("tinytext", "NOT", "NULL");
            $table["mobil"] = array("tinytext", "NOT", "NULL");
            $table["show"] = array("varchar(1)", "NOT", "NULL");
            $table["lastLogin"] = array("timestamp", "NOT", "NULL default '0000-00-00 00:00:00'");
            $table["first_log"] = array("timestamp", "NOT", "NULL default '0000-00-00 00:00:00'");
            $table["lastMod"] = array("datetime", "NOT", "NULL");
            $table["changeLog"] = array("tinytext", "NOT", "NULL");
            return $table;
            break;
            
        case "project" :                
            $table = array();
            $table["id"] = array("tinyint(4)","NOT NULL auto_increment");
            $table["name"] = array(" text","");
            $table["category"] = array("tinytext","");
            $table["info"] = array("text","");
            $table["longInfo"] = array("text","");
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
        case "bookmarks" :
            $table = array();
            $table["id"] = array("int(11)","NOT NULL auto_increment");
            $table["userId"] = array("int(11)","NOT NULL");
            $table["name"] = array("tinytext","NOT NULL");
            $table["breadCrumb"] = array("tinytext","NOT NULL");
            $table["url"] = array("tinytext","NOT NULL");
            $table["data"] = array("text","NOT NULL");   
            return $table;
            break;
            
    }
}

//    CREATE TABLE `klappeAuf_cms_dates` (
//  `id` int(11) NOT NULL auto_increment,
//  `date` date NOT NULL,
//  `toDate` date NOT NULL,
//  `name` text character set latin1 NOT NULL,
//  `subName` tinytext character set latin1 NOT NULL,
//  `info` longtext character set latin1 NOT NULL,
//  `longInfo` longtext NOT NULL,
//  `category` int(11) NOT NULL,
//  `region` int(11) NOT NULL,
//  `location` int(11) NOT NULL,
//  `locationStr` tinytext character set latin1 NOT NULL,
//  `time` time NOT NULL,
//  `image` tinytext character set latin1 NOT NULL,
//  `data` longtext character set latin1 NOT NULL,
//  `link` tinytext character set latin1 NOT NULL,
//  `highlight` varchar(1) character set latin1 NOT NULL,
//  `new` varchar(1) character set latin1 NOT NULL,
//  `print` varchar(1) NOT NULL default '1',
//  `cancel` varchar(1) NOT NULL default '0',
//  `lastMod` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
//  `changeLog` text character set latin1 NOT NULL,
//  `show` varchar(1) character set latin1 NOT NULL,
//  KEY `id` (`id`)
//) ENGINE=MyISAM AUTO_INCREMENT=2826 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2826 ;


function cms_frame_editor($frameWidth) {





    $borderWidth = 5;
    $borderColor = "#f66;";
    $backgroundColor = "9999ff";


    echo ("<form method='post'>");





    $editFrame = $_POST[editFrame];
    if (is_array($editFrame)) {
        echo ("Save $_POST[editFrameSave]<br>");
        echo ("Cancel $_POST[editFrameCancel]<br>");

        // Margin Style
        $marginTop = intval($editFrame[marginTop]);
        $marginRight = intval($editFrame[marginRight]);
        $marginBottom = intval($editFrame[marginBottom]);
        $marginLeft = intval($editFrame[marginLeft]);

        if ($marginTop == $marginRight AND $marginTop == $marginBottom AND $marginTop == $marginLeft) {
            if ($marginTop > 0)
                $margin = "margin:" . $marginTop . "px;";
            else
                $margin = null;
        } else {
            $margin = "margin:";
            if ($marginTop > 0)
                $margin.= $marginTop . "px ";
            else
                $margin .= "0 ";
            if ($marginRight > 0)
                $margin.= $marginRight . "px ";
            else
                $margin .= "0 ";
            if ($marginBottom > 0)
                $margin.= $marginBottom . "px ";
            else
                $margin .= "0 ";
            if ($marginLeft > 0)
                $margin.= $marginLeft . "px;";
            else
                $margin .= "0;";
        }
        echo ("margin = $margin<br>");

        // Padding Style
        $paddingTop = intval($editFrame[paddingTop]);
        $paddingRight = intval($editFrame[paddingRight]);
        $paddingBottom = intval($editFrame[paddingBottom]);
        $paddingLeft = intval($editFrame[paddingLeft]);

        if ($paddingTop == $paddingRight AND $paddingTop == $paddingBottom AND $paddingTop == $paddingLeft) {
            if ($paddingTop > 0)
                $padding = "padding:" . $paddingTop . "px;";
            else
                $padding = null;
        } else {
            $padding = "padding:";
            if ($paddingTop > 0)
                $padding.= $paddingTop . "px ";
            else
                $padding .= "0 ";
            if ($paddingRight > 0)
                $padding.= $paddingRight . "px ";
            else
                $padding .= "0 ";
            if ($paddingBottom > 0)
                $padding.= $paddingBottom . "px ";
            else
                $padding .= "0 ";
            if ($paddingLeft > 0)
                $padding.= $paddingLeft . "px;";
            else
                $padding .= "0;";
        }
        echo ("padding = $padding<br>");


        // background-color
        $background_color_str = $editFrame[backgroundColor];
        if (strlen($background_color_str) == 6)
            $backgroundColor = $background_color_str;

        $border_color_str = $editFrame[borderColor];
        if (strlen($border_color_str) == 6)
            $borderColor = $border_color_str;



        show_array($editFrame);
    }


    div_start("cms_frameEditor", "width:" . $frameWidth . "px;border:1px solid #777;");





    $innerWidth = $frameWidth - (2 * 1);
    // margin Top
    div_start("cms_frameEditor_topMargin", "width:" . $innerWidth . "px;text-align:center;");
    echo ("Abstand Oben:<input type='text' style='width:50px' name='editFrame[marginTop]' value='$editFrame[marginTop]'>");
    div_end("cms_frameEditor_topMargin");

    div_start("cms_frameEditor_horMargin", "width:" . $innerWidth . "px;");

    div_start("cms_frameEditor_leftMargin", "width:60px;text-align:center;float:left;vertical-align:bottom;");
    echo ("Abstand Links:<input type='text' style='width:50px' name='editFrame[marginLeft]' value='$editFrame[marginLeft]'>");
    div_end("cms_frameEditor_leftMargin");


    $innerFrame = $innerWidth - (2 * 60) - (2 * $borderWidth);

    // innerer Rahmen
    div_start("cms_frameEditor_frameBorder", "width:" . $innerFrame . "px;text-align:center;float:left;border:" . $borderWidth . "px solid #$borderColor;background-color:$backgroundColor;");

    div_start("cms_frameEditor_topPadding", "width:" . $innerFrame . "px;text-align:center;background-color:#$backgroundColor;");
    echo ("Innen Oben:<input type='text' style='width:50px' name='editFrame[paddingTop]' value='$editFrame[paddingTop]'>");
    div_end("cms_frameEditor_topPadding");

    div_start("cms_frameEditor_horPadding", "width:" . $innerFrame . "px;background-color:#$backgroundColor;");

    div_start("cms_frameEditor_leftPadding", "width:60px;text-align:center;float:left;background-color:#$backgroundColor;");
    echo ("Abstand Links:<input type='text' style='width:50px' name='editFrame[paddingLeft]' value='$editFrame[paddingLeft]'>");
    div_end("cms_frameEditor_leftPadding");

    $borderWidth = 5;
    $innerPadding = $innerFrame - (2 * 60);

    // Inhalt
    div_start("cms_frameEditor_framePadding", "width:" . $innerPadding . "px;text-align:center;float:left;background-color:$backgroundColor;min-height:50px;");
    echo ("Hier ist der Inhalt<br>");

    // echo("<div style='float:left;width:65px;display:block'>\n");

    echo ("Rahmen-Farbe:");
    echo("<input type='text' id='myBorderColor' value='$borderColor' name='editFrame[borderColor]' style='width:60px;'>\n");
    echo("<a href='javascript:void(0);' rel='colorpicker&objcode=myBorderColor&objshow=myShowBorderColor&showrgb=1&okfunc=myokfunc' style='text-decoration:none;' >\n");
    echo("<div id='myShowBorderColor' style='width:15px;height:15px;border:1px solid black;background-color:#$borderColor;'>&nbsp;</div>");
    echo("</a>\n");





    echo ("Hintergrund-Farbe:");
    echo("<input type='text' id='myhexcode' value='$backgroundColor' name='editFrame[backgroundColor]' style='width:60px;'>\n");
    //echo("</div>\n");
    //echo("<div style='float:left'>\n");
    echo("<a href='javascript:void(0);' rel='colorpicker&objcode=myhexcode&objshow=myshowcolor&showrgb=1&okfunc=myokfunc' style='text-decoration:none;' >\n");
    echo("<div id='myshowcolor' style='width:15px;height:15px;border:1px solid black;background-color:#$backgroundColor;'>&nbsp;</div>");
    echo("</a>\n");
    // echo("</div>\n");

    div_end("cms_frameEditor_framePadding");
    // ende Inhalt

    div_start("cms_frameEditor_rightPadding", "width:60px;text-align:center;float:left;background-color:$backgroundColor;");
    echo ("Innen Rechts:<input type='text' style='width:50px' name='editFrame[paddingRight]' value='$editFrame[paddingRight]'>");
    div_end("cms_frameEditor_rightPadding");

    div_end("cms_frameEditor_horPadding", "before");

    div_start("cms_frameEditor_bottomPadding", "width:" . $innerFrame . "px;text-align:center;");
    echo ("Innen Unten:<input type='text' style='width:50px' name='editFrame[paddingBottom]' value='$editFrame[paddingBottom]'>");
    div_end("cms_frameEditor_bottomPadding");

    div_end("cms_frameEditor_frameBorder");
    // ende Inneter Rahmen

    div_start("cms_frameEditor_rightMargin", "width:60px;text-align:center;float:left;");
    echo ("Abstand Rechts:<input type='text' style='width:50px' name='editFrame[marginRight]' value='$editFrame[marginRight]'>");
    div_end("cms_frameEditor_rightMargin");

    div_end("cms_frameEditor_horMargin", "before");

    div_start("cms_frameEditor_bottomMargin", "width:" . $innerWidth . "px;text-align:center;");
    echo ("Abstand Unten:<input type='text' style='width:50px' name='editFrame[marginBottom]' value='$editFrame[marginBottom]'>");
    div_end("cms_frameEditor_bottomMargin");

    echo ("<input type='submit' value='speichern' name='editFrameSave' >");
    echo ("<input type='submit' value='abbrechen' name='editFrameCancel' >");
    div_end("cms_frameEditor");

    echo ("</form>");

    echo("<script language='Javascript'>\n");

    echo("function myokfunc(){\n");
    echo("  alert('This is my custom function which is launched after setting the color');");
    echo("}\n");

    //init colorpicker:
    echo("$(document).ready(\n");
    echo("  function(){\n");
    echo("      $.ColorPicker.init();\n");
    echo("  }\n");
    echo(");\n");
    echo("</script>\n");
}

?>
