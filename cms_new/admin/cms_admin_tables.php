<?php
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
                    if (strpos($key,"_dt")) echo ("IS LANGUAGE dt - ");
                    if (strpos($key,"_en")) echo ("IS LANGUAGE en - ");
                    if (strpos($key,"_fr")) echo ("IS LANGUAGE fr - ");
                    echo ("Feld not Exist '$key' in Table '$table' $type <b>cmsAdmin_checkTable()</b> <br>");

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

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
