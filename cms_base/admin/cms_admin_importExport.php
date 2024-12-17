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
        $res["updateCMS"] = array("name"=>"Auf Updates prüfen","userLevel"=>8);

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
            case "updateCMS" :
                $this->updateCms($frameWidth);
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
            
            $res = $this->showView($view,$frameWidth);
            if (!$res) echo ("unkown $view <br>");
            echo ("<a href='$pageInfo[page]' class='cmsLinkButton cmsSecond'>zurück</a><br>");
            
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
     
     function updateCms($frameWidth) {
         $this->setupUrl = "http://setup.onPageCms.com/";
         
         echo ("<h1>Update CMS</h1>");
         
         echo ("<h3>Aktuell installierte Version</h3>");
         echo (span_text_str("Version:",200)."<b>$GLOBALS[cmsVersion]</b><br />");
         $actInfo = $this->cmsUpdate_actInfo();
         if (is_array($actInfo)) {
             foreach ($actInfo as $key => $value) {
                 switch ($key) {
                     case "Version" :
                         break;
                     default :
                         echo (span_text_str("$key:",200)."<b>$value</b><br />");  
                 }
             }
         } else {
             echo ("Info = $actInfo <br />");
         }
         echo ("<br />");
         
         unset($_SESSION[copyFile]);
         unset($_SESSION[copyNr]);
         unset($_SESSION[copyCount]);
         unset($_SESSION[copySize]);
         unset($_SESSION[copySizeReady]);
         
         // foreach ($_SESSION as $key => $value )echo ("session $key = $value <br/>");
         $versionList = $this->upDateCms_versionList();
         if (!is_array($versionList) OR count($versionList)<1) {
              echo ("<h3>Verfügbare Versionen</h3>");
              echo ("Keine Versionen verfügbar!<br>");
              return 0;
         }
         
         $selectVersion = "";
         
         if (count($versionList)>1) {
             if ($_POST[selectVersion])$selectVersion = $_POST[selectVersion];
             if ($_GET[selectVersion])$selectVersion = $_GET[selectVersion];
             
             if (!$selectVersion) {
                echo ("<form action method='get'>");
                echo ("<input type='hidden' name='view' value='$_GET[view]' />");
             }
             
         } else {
             $selectVersion = "";
         }
         
        if ($selectVersion AND is_array($versionList[$selectVersion])) {
            echo ("<h3>Ausgewählte Version</h3>");
            echo (span_text_str("Version:",200)."<b>$selectVersion</b> <br />");
            if (is_array($versionList[$selectVersion][info])) {
                foreach ($versionList[$selectVersion][info] as $key => $value) {
                   switch ($key) {
                       case "Version" :
                           //echo (span_text_str("$key:",array("style"=>"width:200px;font-weight:bold;"))."$value <br />");
                           break;
                       default :
                           echo (span_text_str("$key:",200)."<b>$value</b><br />");  
                   }
                }
            }     
             
        } else {
            echo ("<h3>Verfügbare Versionen</h3>");
            foreach ($versionList as $version => $versionData) {
                if (count($versionList)>1) {
                    if ($version == $GLOBALS[cmsVersion]) $checked = "checked='checked'"; else $checked='';
                    $str = "<input type='radio' name='selectVersion' value='$version' $checked >Version:";
                } else {
                    $str .= "$version:";
                }


               //  $str = "<input type='radio' name='version' value='$version'>Version:";
                echo (span_text_str($str,200)."<b>$version</b> <br />");

                if (is_array($versionData[info])) {
                    foreach ($versionData[info] as $key => $value) {
                       switch ($key) {
                           case "Version" :
                               //echo (span_text_str("$key:",array("style"=>"width:200px;font-weight:bold;"))."$value <br />");
                               break;
                           default :
                               echo (span_text_str("$key:",200)."<b>$value</b><br />");  
                       }
                    }
                }             
            }
            if (count($versionList)>1) {
                echo ("<input class='inputButton' type='submit' name='select' value='prüfen' />");
                echo ("</form>");
            }
            if (count($versionList)==1 AND $version) {
                $selectVersion = $version;
                // echo ("USE $version <br />");
            }
         }
         
         if (!$selectVersion) return 0;
         
         
         // duplicateDir 
        
         
         
         
         if ($_GET[update] ) {
             $this->updateCms_doUpdate();
             return 0;
         }
         // $this->updateCms_copyDir("cms_temp");
        
         $this->selectVersion = $selectVersion;
         $fileList = $this->updateCms_getFileList();
         $anz = count($fileList);
         $files_Count = 0;
         $files_Size = 0;
       
         $selectOut = "";
         foreach ($fileList as $nr => $fileData) {
             if (!is_array($fileData)) continue;
             
             $fileName = $fileData[file];
             $fileSize = intval($fileData[size]);
             $filePath = $fileData[path];
             
             if ($fileName AND $fileSize) {                 
                 $files_Count++;
                 $files_Size = $files_Size + $fileSize;
                 $selectOut .= "<input type='checkbox' name='files[$fileName]' checked='checked' value='1' />";
                 $selectOut .= span_text_str($fileName,200).span_text_str(number_format($fileSize/1024,0,",",".")." kByte","width:100px;text-align:right;")."<br />";                 
                 
             } else {
                 echo ("No Size or Name $fileName $fileSize $filePath <br />");
                 // show_array($fileData);
             }            
         }
         
         if (!$files_Count) {
             echo ("Keine aktuellen Dateien vorhanden !<br />");
             echo ("Ihr CMS ist aktuell.");
             return 0;
         }
         
         echo ("<h3>Update Informationen</h3>");
         echo (span_text_str("Anzahl an Dateien:",200)."$files_Count <br />");
         echo (span_text_str("Insgesammte Größe:",200).number_format($files_Size/1024,0,",",".")." kByte <br />");
         echo ("<form method='get' >");
         echo ("<input type='hidden' name='view' value='$_GET[view]' />");
         echo ("<input type='hidden' name='selectVersion' value='$_GET[selectVersion]' />");
         
         echo ("<div style='display:none;' >");
         echo ($selectOut);
         echo ("</div>");
         echo ("<input type='submit' class='inputButton' name='update' value='Update Installieren' />");
         echo ("</form>");
         
         
         
         
         
         // show_array($fileList);
         
         return 0;
     }
         
    
     
    function upDateCms_versionList() {
        // SECLECT VERSION
        $url = $this->setupUrl."setupCreate.php?view=selectVersion";
        $versionString = file_get_contents($url, FILE_USE_INCLUDE_PATH);

        $versionList = explode("<br />",$versionString);

        $versions = array();

        for ($i=0;$i<count($versionList);$i++) {
            if (!$versionList[$i]) continue;

            $offSet = strpos($versionList[$i],":");
            if (!$offSet) continue;

            $code = substr($versionList[$i],0,$offSet);
            $content = substr($versionList[$i],$offSet+1);

            switch ($code) {
               case "CMS-Version" :
                    $actVersion = $content;
                    $versions[$actVersion] = array();
                    $versions[$actVersion][name] = $content;                 
                    break;
                case "CMS-Info" :
                    if ($actVersion) {

                       $infoList = explode("|",$content);
                       $versions[$actVersion][info]=array();
                       for ($z=0;$z<count($infoList);$z++) {
                           $info = $infoList[$z];
                           $offSet = strpos($info,":");
                           if ($offSet) {
                               $infoCode = trim(substr($info,0,$offSet));
                               $infoContent = trim(substr($info,$offSet+1));
                               $versions[$actVersion][info][$infoCode] = $infoContent;
                           }
                       }
                    }
                    break;


            default :
                if ($actVersion) {
                    $versions[$actVersion][$code]=$content;
                }
            }
        }
        return $versions;
    }
    
    function cmsUpdate_actInfo() {
        $fn = "cms_".$GLOBALS[cmsVersion]."/version.info";
        if (!file_exists($fn)) {
            echo ("File not exist '$fn' <br />");
            return "Keine Information vorhanden";
        }
        $infoString = loadText($fn);
        if (!$infoString) return "Keine Information vorhanden";
        
        $infoList = explode("|",$infoString);
        $infos = array();
        for ($z=0;$z<count($infoList);$z++) {
            $info = $infoList[$z];
            //echo ("$z = $info <br />");
            $offSet = strpos($info,":");
            if ($offSet) {
                $infoCode = trim(substr($info,0,$offSet));
                $infoContent = trim(substr($info,$offSet+1));
                // echo ("add $infoCode $infoContent <br>");
                $infos[$infoCode] = $infoContent;
            }
        }
        return $infos;
    }
    
    
    function updateCms_copyDir($target) {
        // echo ("<h1> CREATE TEMP DIRECTORY</h1>");
        
        $source = $_SERVER[DOCUMENT_ROOT]."/cms_".$GLOBALS[cmsVersion];
        $target = $_SERVER[DOCUMENT_ROOT]."/cms_temp";
        
        // echo ("Copy all from $source => $target <br />");
        $this->full_copy($source, $target);
        
    }
        
        
    function full_copy( $source, $target ) {
        if ( is_dir( $source ) ) {
            @mkdir( $target );
            $d = dir( $source );
            while ( FALSE !== ( $entry = $d->read() ) ) {
                if ( $entry == '.' || $entry == '..' ) {
                    continue;
                }
                $Entry = $source . '/' . $entry; 
                if ( is_dir( $Entry ) ) {
                    $this->full_copy( $Entry, $target . '/' . $entry );
                    continue;
                }
                copy( $Entry, $target . '/' . $entry );
            }

            $d->close();
        }else {
            copy( $source, $target );
        }
    }

    
    function updateCms_getFileList() {
        $cmsType = "basic";
         
        $url = $this->setupUrl."setupCreate.php?view=pathList";
        $url.= "&type=$cmsType";
        $url.= "&version=$this->selectVersion";

        // echo ("URL $url <br />");
        $pathString = file_get_contents($url, FILE_USE_INCLUDE_PATH);
        $pathList = explode("<br />",$pathString);
        $getFilePathList = array();
        if (is_array($pathList)) {
            for ($i = 0;$i<count($pathList);$i++) {
                $path = $pathList[$i];
                
                $targetPath = $path;
                if (subStr($path,0,5+strlen($this->selectVersion))== "cms_".$this->selectVersion."/") {
                    $targetPath = "cms_temp/".subStr($path,5+strlen($this->selectVersion));
                } 
                
                // echo ("PATH = $path <br />");
                if ($targetPath) {
                    $out = "Erstelle Ordner $targetPath ";

                    if (file_exists($targetPath)) {
                        $out .= " - exist ";
                        $getFilePathList[] = $path;
                        // if ($path != "cms_base/") rmdir($path);    
                        //echo ("-delete ");
                    } else {
                        $createRes = mkdir($targetPath); //,704);

                        if ($createRes) { 
                            $out .= " - ok ";
                            $getFilePathList[] = $path;
                        } else {
                            $out .= " - <b>FEHLER</b>";
                        }
                    } 
                    $out .= "<br />";
                    // echo ($out);
                }
            }
        }
        
        

        
        $root = $_SERVER[DOCUMENT_ROOT]."/";

        $_SESSION[copyFile] = array();
        $_SESSION[copyNr] = 0;
        $_SESSION[copyCount] = 0;
        $_SESSION[copySize] = 0;
        $_SESSION[copySizeReady] = 0;
        
        $copyFile = array();

        for($i=0;$i<count($getFilePathList);$i++) {
            $path = $getFilePathList[$i];
            $url  = $this->setupUrl."setupCreate.php?view=fileList&path=$path&type=$cmsType&version=$this->selectVersion";
            $fileString = file_get_contents($url, FILE_USE_INCLUDE_PATH);
            $fileList = explode("<br />",$fileString);            
            if (is_array($fileList)) {
                for ($f=0;$f<count($fileList);$f++) {
                    $file = $fileList[$f];

                    if ($file) {
                        list($fn,$size,$md5) = explode("|",$file);

                        $out = "";
                        $urlFile = $this->setupUrl."setupCreate.php?view=file&path=$path&file=$fn&type=$cmsType&version=$this->selectVersion";

                        $addFile = 1;
                        
                        $targetPath = $path;
                        if (subStr($path,0,5+strlen($this->selectVersion))== "cms_".$this->selectVersion."/") {
                            $targetPath = "cms_temp/".subStr($path,5+strlen($this->selectVersion));
                        } 
                        // echo (" path = $path file = $fn target='$targetPath' <br> " );
                        
                        
                        if (file_exists($root.$targetPath.$fn)) {
                            $localMd5 = md5_file($root.$path.$fn);
                            if ($localMd5 == $md5) $addFile = 0;
                        }

                        if ($addFile) {
                            // echo ("Add File to Update $fn  <br>");
                            $_SESSION[copySize] = $_SESSION[copySize] + $size;
                            $_SESSION[copyCount]++;
                            
                            $addData =  array("file"=>$fn,"size"=>$size,"url"=>$urlFile,"path"=>$root.$targetPath.$fn);
                            $_SESSION[copyFile][] = $addData; //array("file"=>$fn,"size"=>$size,"url"=>$urlFile,"path"=>$root.$path.$fn);
                            $copyFile[] = $addData; //array("file"=>$fn,"size"=>$size,"url"=>$urlFile,"path"=>$root.$path.$fn);
                        }

                    }
                }
            }
        }
        
        return $copyFile; 
         
     }
     
    
    
    function updateCms_doUpdate() {
        
        echo ("<h1>Dateien installieren</h1>");
        
       
        $manual = 0;
        if ($_SESSION[copyCount]) { 
            for ($i= 0;$i<count($_SESSION[copyFile]);$i++) {
                $fileData = $_SESSION[copyFile][$i];
                // foreach ($fileData as $key => $value) echo ("$key -> $value |");
                // echo ("$fileData[file]<br>");
            }
            
            
            echo ("$_SESSION[copyCount] Dateien werden installiert ... <br />");
            echo ("<div class='cmsupdate_frame copy_1 ' >");
            echo ("<div class='cmsupdate_percent'></div>");
            echo ("<div class='cmsupdate_name' ></div>");
            echo ("</div>");
            
            // DIV Wenn fertig!
            echo ("<div class='cmsUpdate_ready'>");
            echo ("Update war erfolgreich!<br/>");
            echo ("<a href='index.php' class='mainLinkButton' >zur Startseite</a>");
            echo ("</div>");
            // dateien vorhanden
            
            if ($manual) {
                echo ("<a href='javascript:next();' >Starten</a>");
            } else {
                echo ("<script type='text/javascript'>next();</script>");
            }
        } else {
            echo ("Nichts zu kopieren !!<br>");
            $goUrl = "setup.php?view=install";
            if ($manual) {
                echo ("<a href='$goUrl'>nächster Schritt</a>");
            } else {
                reloadPage($goUrl,10);
            }
        }     
 
        $res = array("view"=>"copy");
        return $res;
        
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
