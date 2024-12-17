<?php // charset:UTF-8
    /*ini_set("allow_url_fopen",array("access"=>4,"global_value"=>"1","local_value"=>"1"));
    echo ("fOpen = ".ini_get("allow_url_fopen")."<br>");
    $iniData = ini_get("allow_url_fopen");
    echo ("fopen acess ".$iniData[access]."<br>");
    $ini = ini_get_all();
    //echo ("all Ini".$ini."<br>");
    show_array($ini[allow_url_fopen]);
     phpinfo();*/
    
 class cms_importExport_base {
    var $serverPath = "";
    var $phpGetFile = "";

    function getLinkButtons() {
        $res = array();


        $res["database"] = array("name"=>"externe Datenbank","url"=>$GLOBALS[pageInfo][page]."?view=database");
        $res["convertDatabase"] = array("name"=>"Datenbank Konvertieren","userLevel"=>9);

        $addOwn = $this->getLinkButtons_own();
        if (is_array($addOwn) AND count($addOwn)) {
            foreach($addOwn as $key => $value) {
                $res[$key] = $value;
            }
        }
        return $res;

    }
    function getLinkButtons_own() {
        $res = array();
        return $res;
    }


     function showView($type,$frameWidth) {
        $res = 0;
        switch ($type) {
            case "database" :
                $this->showDatabase($frameWidth);
                $res = 1;
                break;

            case "convertDatabase" :
                $this->convertDatabase($frameWidth);
                $res = 1;
                break;

           default :
               $res = $this->showView_own($type,$frameWidth);
               if (!$res) {
                   echo "unkown view ";                  
               }
        }
        return $res;
    }


    function convertDatabase($frameWidth) {
        echo ("Konvertiere Database <br>");
        global $cmsName;
        echo ("cmsName = $cmsName<br>");

        if ($_POST[convert]) {
            echo ("UMWANDELN");
            $tableList = $_POST[tableList];
            $convertFrom = $_POST[convertFrom];
            $convertTo = $_POST[convertTo];

            if ($convertFrom != $convertTo) {
                foreach ($tableList as $tableName => $value) {
                    echo ("<h1>Umwandeln ÄÖÜ von $tableName von $convertFrom -> $convertTo </h1>");
                    $query = "SELECT `id` FROM `$tableName` ";
                    $result = mysql_query($query);
                    if (!$result) {
                        echo ("error in query $query<br>");
                    } else {
                        while ($data = mysql_fetch_assoc($result)) {
                            $id = $data[id];
                            $query2 = "SELECT * FROM `$tableName` WHERE `id` = $id";
                            $result2 = mysql_query($query2);
                            while ($data = mysql_fetch_assoc($result2)) {
                                $data = php_clearQuery($data);
                                $queryList = array();
                                $saveId = 0;
                                foreach($data as $key => $value) {
                                    switch ($key) {
                                        case "id" : $saveId = $value; break;
                                        case "show" : break;
                                        case "changeLog" : break;
                                        case "lastMod" : break;
                                        case "data" :
                                            $found = 0;
                                            if (is_array($value) AND count($value)) {
                                                foreach ($value as $key2 => $value2) {
                                                    if (!is_int($value2)) {
                                                        if (!intval($value2)) {
                                                            // echo ("$key2 = $value2 <br>");
                                                            $value2New = iconv($convertFrom,$convertTo,$value2);
                                                            $data[data][$key2] =  php_clearStr($value2New);;
                                                            if ($value2New != $value2) $found++;
                                                        }
                                                    }
                                                    if ($found > 0) {
                                                        $queryList[$key] = array2Str($data[data]);
                                                    }
                                                }
                                            }
                                        default :
                                            if (!is_int($value)) {
                                                if (!intval($value)) {
                                                    // echo ("$key = $value <br>");
                                                    $valueNew = iconv($convertFrom,$convertTo,$value);
                                                    if ($valueNew != $value) {
                                                        $queryList[$key] = php_clearStr($valueNew);
                                                    }
                                                    $data[$key] = $valueNew;
                                                }
                                            }

                                    }
                                }


                                /*
                                 open So-Do 18-2 Uhr, Fr+Sa 18-3 Uhr
                                 Wundersch�n begr�nter Innenhof f�r lauschige Sommerabende. Essen: verschiedene frische Tapas, Pasta-Gerichte, Snacks, Sommersalate und eine abwechslungsreiche Tageskarte, wie immer alles hausgemacht. Getr�nke: verschiedene leckere Sommer -Weine, -Cocktails, -Drinks.
                                 Szenetreff im Kaffehausstil mit Cocktailbar. Frische Tageskarte. 70 verschiedene Cocktails und 50 verschiedene Weine. Sonntags Weinprobiertag (immer 3 neue Sorten zu verkosten). Montags Cocktails g�nstiger. Ausgew�hlte Hintergrundmusik mit aktuellen Clubsounds. Stadtbekannte Feten an Halloween, Silvester, Fasching, Tanz in den Mai. K�che t�glich 18-1 Uhr, alle Gerichte hausgemacht. KSC+Champions-League-Live�bertragung (dann fr�her ge�ffnet)
                                 *
                                 */
                                if (count($queryList)) {
                                    $queryUpdate = "";
                                    foreach ($queryList as $key => $value) {
                                        if ($queryUpdate != "")  $queryUpdate .= ", ";
                                        $queryUpdate .= "`$key`='$value'";
                                    }
                                    $queryUpdate = "UPDATE `$tableName` SET ".$queryUpdate." WHERE `id`=$saveId";
                                    $save = 1;
                                    
                                    if ($save) {
                                        $saveResult = mysql_query($queryUpdate);
                                        if (!$saveResult) echo ("error in $queryUpdate <br>");
                                    } else {
                                        // echo ("UpdateQuery = $queryUpdate <br>");
                                    }
                                }
//                                $data = php_clearPost($data);
//                                show_array($data);
                            }
                            
                        }
                    }


                }


            }



        }


        $dbName = "db360967548";
        $dbName = "usr_web723_1";
        $query = "SHOW TABLES FROM `$dbName` ";// LIKE 'klappe%' ";
        $query .= " LIKE '".$cmsName."%' ";

        $result = mysql_query($query);
    
        if (!$result) {
            if ($debug) echo "DB Fehler, konnte Tabellen nicht auflisten\n";
            if ($debug) echo 'MySQL Fehler: ' . mysql_error();
            if ($debug) echo ($query."<br>");
            return "error";
            exit;
        }
        
        $tables = array();
        while ($row = mysql_fetch_row($result)) {
            $table = $row[0];
            $tables[] = $table;
            // echo "Tabelle: {$row[0]}<br>";
        }
        $charSets = array("ISO-8859-15","UTF-8");
        echo ("<form method='post' >");
        echo ("<select name='convertFrom' value'$convertFrom'>");
        for ($i=0;$i<count($charSets);$i++) {
            if ($convertFrom == $charSets[$i]) $selected = "selected='1'";
            else $selected="";
            echo ("<option $selected value='$charSets[$i]'>$charSets[$i]</option>");
        }
        echo ("</select><br>");


        echo ("<select name='convertTo' value'$convertTo'>");
        for ($i=0;$i<count($charSets);$i++) {
            if ($convertTo == $charSets[$i]) $selected = "selected='1'";
            else $selected="";
            echo ("<option $selected value='$charSets[$i]'>$charSets[$i]</option>");
        }
        echo ("</select><br>");

        for ($i=0;$i<count($tables);$i++) {
            $table = $tables[$i];
            $checked = "";
            if ($tableList[$table] ) $checked = "checked='checked'";
            echo ("<input type='checkbox' $checked value='1' name='tableList[$table]'>");
            echo ("Tabelle $tables[$i] <br>");
        }

        echo ("<input type='submit' name='convert' value='umwandeln'> <br>");

   





    }

    function showView_own($type,$frameWidth) {
        return 0;
    }

    function show($frameWidth) {
        $linkButtons = $this->getLinkButtons();
        $view = $_GET[view];
        $pageInfo = $GLOBALS[pageInfo];

        if ($view) {
            echo ("<a href='$pageInfo[page]' class='cmsLinkButton cmsSecond'>zurück</a><br>");
            $res = $this->showView($view,$frameWidth);
            if (!$res) echo ("unkown $view <br>");
            
        } else { // no View
            foreach ($linkButtons as $key => $value) {
                $showLevel = $value[userLevel];
                if (!$showLevel) $showLevel = 9;
                $width = ($frameWidth / 2) -40;
                if ($_SESSION[showLevel] >= $showLevel) {

                    echo ("<a href='".$pageInfo[page]."?view=$key' style='width:".$width."px;' class='cmsLinkButton'>$value[name]</a>");
                }
            }
        }
    }

    function showDatabase($frameWidth) {
        $mode = "showTables";
        if ($_GET[mode]) $mode = $_GET[mode];
        $debug = 0;
        switch ($mode) {
            case "showTables" :
                $res = $this->show_tables($debug);
                break;

            case "tableInfo" :
                $res = $this->show_tableInfo($_GET,$debug);
                break;

             case "getData" :
                $res = $this->getData($_GET,$page,$debug);
                break;

             case "importData" :
                 $limitCount = 100;
                 $maxPage = 100;
                 $emptyPages = 0;
                 for ($limitPage=1;$limitPage<$maxPage;$limitPage++) {
                    //echo ("GET DATA FOR PAGE $limitPage (anz=$limitCount)<br>");
                    $res = $this->getData($_GET,$limitPage,$limitCount,$debug);
                    if (count($res) == 0) {
                        $emptyPages++;
                        //echo ("ende Erreicht? $emptyPages<br>");
                        if ($emptyPages > 2) $maxPage = 0;
                    } else {
                        $emptyPages = 0;

                        // echo ("anz = ".count($res)."<br>");
                        $res = $this->importData($res,$_GET,$debug);
                        // echo ("import Ready page $limitPage / $limitCount <br>");
                    }
                 }
                break;


            default:
                echo ("unkown Mode '$mode' <br>");
        }
        
   
     }

     function get_url($url,$data) {
         $data = admin_getData($url,$getData);
         $dataArray = str2Array($data);
         if (is_array($dataArray)) {
             return $dataArray;
         }
         return $data;
     }

    function show_tables($debug) {
        $data = $this->__init();

        // $this->serverPath = $data[serverPath];
        show_array($data);

        echo ("Show<br>");
        echo ("phpVersion = " . phpversion()."<br>");
        echo ("severpath ".$this->serverPath." <br>");
        echo ("getData ".$this->phpGetFile." <br>");

        $mode = "info";
        $show = "tables";
        $getData = array("mode"=>$mode,"show"=>$show);
        if ($debug) $getData[debug] = $debug;
        if (is_array($data[tables])) {
            foreach($data[tables] as $key => $value) $getData[$key] = $value;
        }

        $info = admin_getData($this->serverPath.$this->phpGetFile,$getData);
        while ($info[0] != "a") $info = substr($info,1);
       
        $infoArray = str2Array($info);
        if (is_array($infoArray)) {
            global $pageInfo;
            // show_array($pageInfo);
            for ($i=0;$i<count($infoArray);$i++ ) {
                $tableName = $infoArray[$i];
                echo ("Tabelle '$tableName' <a href='".$pageInfo[page]."?view=database&mode=tableInfo&tableName=$tableName'>TabellenInfo</a><br>");
            }
            // show_array($infoArray);
            return $infoArray;
        } else {
            echo ("Keine Tabelle<br>'$info' <br>");
        }
     }

     function show_tableInfo($data,$debug) {
         $serverData = $this->__init();
         echo ("<h2>Tabellen-Information</h2>");
         $tableName = $data[tableName];
         echo ("<b>Tabelle: '$tableName'<br>");
         
        $mode = "info";
        $show = "tableInfo";
        $debug = 0;
        $getData = array("mode"=>$mode,"show"=>$show);
        if ($debug) $getData[debug] = $debug;
        $getData[tableName] = $tableName;
        if (is_array($serverData[tableInfo])) {
            foreach($serverData[tableInfo] as $key => $value) $getData[$key] = $value;
        }

        $info = admin_getData($this->serverPath.$this->phpGetFile,$getData);
        while ($info[0] != "a") $info = substr($info,1);
       

        $infoArray = str2Array($info);
        if (is_array($infoArray)) {
            global $pageInfo;

            foreach ($infoArray as $name => $value) {
                echo ("$name = $value[type] <br>");
            }

            echo ("<a class='cmsLinkButton' href='$pageInfo[page]?view=database&mode=getData&tableName=$tableName'>Daten lesen </a> ");
            echo ("<a class='cmsLinkButton' href='$pageInfo[page]?view=database&mode=importData&tableName=$tableName'>Daten importieren </a> ");

            //  show_array($infoArray);
            return $infoArray;
        } else {
            echo ("'$info' <br>");
        }

         
         
     }

     function getData($data,$limitPage,$limitCount,$debug) {
         $serverData = $this->__init();
         echo ("<h2>Tabellen-Lesen Seite $tableName $limitPage (Anzahl=$limitCount)</h2>");
         $tableName = $data[tableName];
         //echo ("<b>Tabelle: '$tableName'<br>");

        $mode = "getData";
        $show = "tableData";
        $debug = 0;

        
        $getData = array("mode"=>$mode,"show"=>$show);


        
        if ($limitCount>0) {
           $getData[limitCount] = $limitCount;
           $getData[limitPage] = $limitPage;
        }
        if ($debug) $getData[debug] = $debug;
        $getData[tableName] = $tableName;
        if (is_array($serverData[tableInfo])) {
            foreach($serverData[tableInfo] as $key => $value) $getData[$key] = $value;
        }

        // show_array($getData);


        $info = admin_getData($this->serverPath.$this->phpGetFile,$getData);
        $infoArray = str2Array($info);
        if (is_array($infoArray)) {
            return $infoArray;
        } else {
            echo "Kein Array <br> $infoArray <br>";
            echo ($infoArray);
        }

     }

     function importData($res,$data,$debug) {
         // echo ("<h2>Import Data </h2>");
         $tableName = $data[tableName];
         //echo ("<b>Tabelle: $tableName </b><br>");
         //echo ("Anzahl : ".count($res)."<br>");
         for($i=0;$i<count($res);$i++) {
             $newData = $this->convertData($tableName,$res[$i]);
             if (is_array($newData)) {
                
                 $saveRes = $this->saveData($tableName,$newData);
                 if ($saveRes != 1) {
                    echo ("<h4>Zeile $i - converted // ");
                    echo (" nicht gespeichert : $saveRes </h4>");
                    return 0;
                 }
                 //echo ("Zeile $i - converted // ");
                 // echo (" SAVE !!!<br>");
             } else {
                echo ("Zeile $i - not converted ($newData) <br>");
                return 0;
             }
         }
     }

    function convertData($tableName,$data) {
        
        $explodeList = explode("_",$tableName);
        if ($explodeList[1] == "cms" AND count($explodeList)==3) {
            $tableName = "cms_".$explodeList[2];
        }
        // echo ("Convert Data tableName = $tableName <br>");
        switch ($tableName) {
            case "cms_pages"    : $res = $data; break;
            case "cms_text"     : $res = $data; break;
            case "cms_content"  : $res = $data; break;
            
            case "cms_category" : $res = $data; break;
            case "cms_user"     : $res = $data; break;
            case "cms_dates"    : $res = $data; break;
            case "cms_images"   : $res = $data; break;
            case "cms_articles" : $res = $data; break;
            case "cms_location" : $res = $data; break;
            case "cms_project"  : $res = $data; break;
            case "cms_company"  : $res = $data; break;
            case "cms_produkte" : $res = $data; break;
           
            
        
                
            default :
                $res = $this->convertData_own($tableName,$data);

        }
        return $res;
    }

    function convertData_own($tableName,$data) {
        return "not Set for tableName ".$tableName;
    }

    function saveData($tableName,$data) {
        //echo ("saveData($tableName,$data) <br>");
        $explodeList = explode("_",$tableName);
        if ($explodeList[1] == "cms" AND count($explodeList)==3) {
            $tableName = "cms_".$explodeList[2];
        }
        //echo ("saveData($tableName,$data) <br>");
        switch ($tableName) {
            case "cms_pages" : $res = cms_page_save($data); break;
            case "cms_content" : $res = cms_content_save($data[id],$data); break;
            case "cms_text" :
                $res = cmsText_save($data);
                break;
            
            case "cms_project" :
                $res = cmsProject_save($data);
                break;
            
            
            case "cms_user" :
                $res = cmsUser_save($data);
                break;
            case "cms_category" :
                $res = cmsCategory_save($data);
                break;            
            case "cms_dates" :
                $res = cmsDates_save($data);
                break;
            case "cms_images" :
                $res = cmsImage_save($data);
                break;
            case "cms_articles" :
                $res = 1;
                $id = $data[id];
                $minId = 0;
                $maxId = 19900;

                if ($data[url] == "http://") $data[url] = "";
                if (!$data[subName]) $data[subName] = "";
                if (!$data[link]) $data[link] = "";
                if (!$data["new"]) $data["new"] = "";
                $convKey = array("name"=>1,"subName"=>1,"info"=>1);
                foreach ($convKey as $key => $str) {
                    $str = $data[$key];
                    if (is_string($str)) {
                        $str =  str_replace("\\","",$str);
                        if ($str != $data[$key]) $data[$key] = $str;
                    }
                }
//                if (strpos($info,"\\")) {
//                    // echo ("Replace Info $info<br>");
//                    $info = str_replace("\\","",$info);
//                    $info = str_replace('\"','"',$info);
//                    if ($info != $data[info]) $data[info] = $info;
//
//                }
                
                //  $maxId = 841;
                if ($id >= $minId AND $id <= $maxId) {
                    $res = cmsArticles_save($data,1);
                } else {
                    // echo ("save with id = $id <br />");
                }
                return $res;
                //
                break;
            case "cms_location" :
                $res = cmsLocation_save($data);                
                break;
            case "cms_produkte" :
                echo ("cms_produkt_import ");
                $res = $data;
                break;

            case "cms_company" :
                echo ("cms_company saveData <br>");
                show_array($data);

                $id = intval($data[id]);
                if ($id) {
                    echo "SaveData has id $id <br>";
                    $existData = cmsCompany_get(array("id"=>$id));
                    if (is_array($existData)) {
                        echo ("exist local !!<br>");
                        show_array($existData);
                    }
                }

                
                $res = $data;
                break;


            default :
                
                $res = $this->saveData_own($tableName,$data);
                if (!$res) echo ("SaveData Result in base = $res <br>");
               //   echo ("SaveData Result in base = $res <br>");

        }
        
        if (intval($res) AND $res > 0) $res = 1;
        return $res;
    }

    function saveData_own($tableName,$data) {
        return "not Set for tableName ".$tableName;
    }
    





     function __init() {
         $data = array();
         return $data;
     }
 }





function cms_admin_importExport($frameWidth,$ownAdminPath=""){
    // echo ("$GLOBALS[cmsVersion]<br>");
    $getPath = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/admin/admin_getData.php4";
    if (!file_exists($getPath)) echo ("$getPath<br>");
    include ($getPath);

    $ownPhpFile = $ownAdminPath."/cms_admin_importExport_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cms_importExport();
        
    } else {
        $class = new cms_importExport_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    $class->show($frameWidth);


    
}

?>
