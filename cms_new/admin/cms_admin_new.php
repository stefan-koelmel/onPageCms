
<?php // charset:UTF-8

function cmsAdminEdit($cmsData) {
    echo ("<h1>BERBEITEN</h1>");
    echo ("<form method='post' >");
    
    if (is_array($_POST[cmsData])) {
        $cmsId = $_GET[id];
        $cmsData = $_POST[cmsData];

        $query = "";
        foreach ($cmsData as $key => $value ) {
            if (is_array($value)) $add = array2Str ($value);
            else $add=$value;
            
            if ($query) $query.= ", ";
            $query .= "`$key`='$add' ";
        }
        $query = "UPDATE `cms_settings` SET $query WHERE `id`=$cmsId ";
        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query <br> $query <br />");
        }
        
        
    }
    
    foreach ($cmsData as $key => $value) {
        $show = 1;
        
        switch ($key ) {
            case "id" : $cmsId = $value; $show = 0; break;
            case "title" : $show = 0; break;
            case "title" : $show = 0; break;
            case "description" : $show = 0; break;
            case "keywords" : $show = 0; break;
            case "border" : $show = 0; break;
    
            case "background" : $show = 0; break;
            case "data" : $show = 0; break;
            
            
        }
        if (!$show) continue;
        
        echo ("<span style='width:200px;display:inline-block;vertical-align: top;'>$key</span>") ;
        
        $readonly = 0; 
        $standard = "";
        $type     = "text";
        switch ($key) {
            case "mobilPages" : $type = "checkbox"; break;
            case "cache" : $type = "checkbox"; break;
            case "show" : $type = "checkbox"; break;
            case "bookmarks" : $type = "checkbox"; break;
            case "history" : $type = "checkbox"; break;
            case "wireframe" : $type = "checkbox"; break;
            case "wireframeOn" : $type = "checkbox"; break;
            case "cmsVersion" : $type="cmsSelect"; break;
            case "specialData" : $type = "dataSelect"; break;
            case "useType" : $type = "typeSelect"; break;
            case "layout" : $type = "layoutSelect"; break;
            case "state" : $type = "stateSelect"; break;
            case "editColor" : $type = "colorSelect"; break;
            case "editMode" : $type = "editSelect"; break;
            
            case "normal_theme" : $readonly = 1; $standard="none"; $type = "text"; break;
            case "wireframe_theme" : $readonly = 1; $standard="none"; $type = "text"; break;
            case "name" : $readonly = 1; $standard="none"; $type = "text"; break;
        
            default :
                $type = "text";
        }
        
        $cmsVersion = $cmsData[cmsVersion];
        
        if (!$value and $standard) $value = $standard;
        
        if ($readonly) $readonly="readonly='readonly'"; else $readonly="";
        if ($disabled) $disabled="disabled='disabled'"; else $disabled="";
        
        
        switch ($type) {
            case "text" :
                
                
                echo ("<input $readonly $disabled type='text' name='cmsData[$key]' value='$value' style='width:600px;' /><br />");
                break;
            case "checkbox" :
                if ($value) $checked = "checked='checked'"; else $checked="";
                echo ("<input type='checkbox' name='cmsData[$key]' $checked value='1' /><br />");
                break;
               
            case "cmsSelect" :
                $cmsList = cmsAdminCmsList($cmsVersion);
                echo ("<select name='cmsData[$key]' >");
                foreach ($cmsList as $k => $v) {
                    if ($k == $value) $selected="selected='selected'"; else $selected = "";
                    echo ("<option value='$k' $selected >$v</option>");                  
                }
                echo ("</select><br />");
                break;
                
           
            case "typeSelect" :
                $typeList = cmsAdminTypeList($cmsVersion);
                echo ("<div style='width:600px;display:inline-block;' >");
                $use = str2Array($value);
                foreach ($typeList as $k => $v) {
                    echo ("<span style='width:200px;display:inline-block;'>");
                    if ($use[$k]) $checked ="checked='checked'"; else $checked = "";
                    echo ("<input type='checkbox' name='cmsData[useType][$k]' value='1'  $checked />");
                    echo ("$k");
                    echo ("</span>");                    
                }
                echo ("<div style='clear:both;'></div>");
                echo ("</div><br />&nbsp;<br />");
                break;
               
                
            case "dataSelect" :
                $dataList = cmsAdminDataList($cmsVersion);
                $use = str2Array($value);
                echo ("<div style='width:600px;display:inline-block;' >");
                $use = str2Array($value);
                foreach ($dataList as $k => $v) {
                    echo ("<span style='width:200px;display:inline-block;'>");
                    if ($use[$k]) $checked ="checked='checked'"; else $checked = "";
                    echo ("<input type='checkbox' name='cmsData[specialData][$k]' value='1' $checked />");
                    echo ("$k");
                    echo ("</span>");                    
                }
                echo ("<div style='clear:both;'></div>");
                echo ("</div><br />&nbsp;<br />");
                break;
                
            case "layoutSelect" :
                $layoutList = cmsAdminLayoutList($cmsData[name]);
                echo ("<select name='cmsData[$key]' >");
                foreach ($layoutList as $k => $v) {
                    if ($k == $value) $selected="selected='selected'"; else $selected = "";
                    echo ("<option value='$k' $selected >$v</option>");                  
                }
                echo ("</select><br />");
                break;
                
            case "colorSelect" :
                $colorList = cmsAdminColorList();
                echo ("<select name='cmsData[$key]' >");
                foreach ($colorList as $k => $v) {
                    if ($k == $value) $selected="selected='selected'"; else $selected = "";
                    echo ("<option value='$k' $selected >$v[name]</option>");                  
                }
                echo ("</select><br />");
                break;
            
            case "editSelect" :
                $editList = cmsAdminEditList();
                echo ("<select name='cmsData[$key]' >");
                foreach ($editList as $k => $v) {
                    if ($k == $value) $selected="selected='selected'"; else $selected = "";
                    echo ("<option value='$k' $selected >$v[name]</option>");                  
                }
                echo ("</select><br />");
                break;
            
            case "stateSelect" :
                $stateList = cmsAdminStateList($cmsData[name]);
                echo ("<select name='cmsData[$key]' >");
                foreach ($stateList as $k => $v) {
                    if ($k == $value) $selected="selected='selected'"; else $selected = "";
                    echo ("<option value='$k' $selected >$v[name]</option>");                  
                }
                echo ("</select><br />");
                break;
                
            default : 
                echo "unkown Type ($type) $value <br />";
                
        }
    }
    echo ("<input type='submit' value ='speichern' name='save' />"); 
    echo ("<a href='index.php'>ABBECHEN</a>");
    echo ("</form>");
}

function  cmsAdminColorList() {
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
    return $typeList;
}
function cmsAdminEditList() {
    $typeList = array();
    $typeList[onPage] = array("name"=>"auf der Seite");
    // if ($_SESSION[userLevel] >= 9) 
    $typeList[onPage2] = array("name"=>"auf der Seite - Neu");
    $typeList[siteBar] = array("name"=>"NavigationsListe");
    $typeList[window] = array("name"=>"Fenster");
    return $typeList;
}
function cmsAdminCmsList($cmsVersion) {
    $res = array("new"=>"New","base"=>"base");
    $folder = $_SERVER['DOCUMENT_ROOT']."/";
    $handle = opendir($folder);
          
    
    $res = array();
    while ($file = readdir ($handle)) {
        if ($file == ".") continue;
        if ($file == "..") continue;
        
        if (!is_dir($folder.$file)) continue;
        if (substr($file,0,4)!="cms_") continue;
        
        $name = substr($file,4);
        if (!file_exists($folder.$file."/cms.php")) continue;
        $res[$name] = $file;
    }
    return $res;
}


function cmsAdminStateList() {
    $typeList = array();
    // $typeList[grey] = array("name"=>"Grau");
    $typeList[online] = array("name"=>"Online");
    $typeList[construction] = array("name"=>"Baustelle");
    $typeList[inWork] = array("name"=>"Wartung");
    return $typeList;
}

function cmsAdminLayoutList($name) {
    $res = array("new"=>"New","base"=>"base");
    $folder = $_SERVER['DOCUMENT_ROOT']."/".$name."/";
    $handle = opendir($folder);
          
    
    $res = array();
    while ($file = readdir ($handle)) {
        if ($file == ".") continue;
        if ($file == "..") continue;

        
        if (is_dir($folder.$file)) continue;
        if (substr($file,0,7)!= "layout_") continue;
        $name = substr($file,0,strlen($file)-4);
        $res[$name] = $name;
    }
    return $res;
}

function cmsAdminTypeList($cmsVersion) {
    $res = array();
    
    $folder = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentTypes/";
    $handle = opendir($folder);
          
    
    $res = array();
    while ($file = readdir ($handle)) {
        if ($file == "cmsType_categoryList.php") continue;
        if ($file == "cmsType_locationList.php") continue;
        if ($file == "cmsType_dateList.php") continue;
                
        if ($file == ".") continue;
        if ($file == "..") continue;
        
        if (substr($file,0,8) != "cmsType_") continue;
        if (substr($file,strlen($file)-4)!=".php") continue;
        
        $file = substr($file,8,  strlen($file)-8-4 );
        $res[$file] = 1;
        // echo ("File $file <br>");
        
        
        
    }
    
    return $res;
}

function cmsAdminDataList($cmsVersion) {
     
    $folder = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/data/";
    $handle = opendir($folder);
          
    
    $res = array();
    while ($file = readdir ($handle)) {
        if ($file == "cms_defaultText.php") continue;
                
        if ($file == ".") continue;
        if ($file == "..") continue;
        
        if (substr($file,0,4) != "cms_") continue;
        
        $file = substr($file,4,  strlen($file)-4-4 );
        $res[$file] = 1;
        // echo ("File $file <br>");
        
        
        
    }
    
   
    
    
    return $res;
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


function cmsAdminNew($newName,$longName="",$cmsAdminPass="",$type="") {
    if (!$newName) {
        cms_errorBox ("kein Name angegeben");
        return 0;
    }

    global $cmsVersion,$cmsPassword;
    $cmsPassword = $cmsAdminPass;
    $cmsVersion = "new";
    $root = $_SERVER['DOCUMENT_ROOT'];

    if (!function_exists("tableList")) {
        $adminTableFn = $root."/cms_$cmsVersion/admin/cms_admin_tables.php";
        if (!file_exists($adminTableFn)) return "adminTables Missing";
        echo ($root."/cms_$cmsVersion/admin/cms_admin_settings.php<br>");
        include ($adminTableFn);
        
        
        
        
        $adminSettingsFn = $root."/cms_$cmsVersion/admin/cms_admin_settings.php";
        if (!file_exists($adminSettingsFn)) return "adminSettings Missing";
        echo ($root."/cms_$cmsVersion/admin/cms_admin_settings.php<br>");
        // include ($adminSettingsFn);
    }

    
    
   
   
    $myServerPos = strpos($_SERVER[HTTP_HOST],"stefan-koelmel.com");
    // $myServerPos = strpos($_SERVER[HTTP_HOST],"2-pi-r.de");
    
    global $targetPath,$ownServer,$createOut,$cmsType;
    $cmsType = $type;
    
    $createOut = 0;
    $ownServer = 0;
    if (!is_int($myServerPos)) { 
        $targetPath = $root;
    } else {
        $targetPath = $root."/".$newName;
        $ownServer = 1;
    }
    if ($createOut) echo ("Target ($ownServer)=  $targetPath <br>");
   
        
    // CREATE FOLDER 
    $res = cmsAdminNew_createFolder($newName);
    if ($res) {
        echo ("<h3>Verzeichnisse angelegt </h3>");        
    } else {
        errorStr("Fehler beim Verzeichnisse anlegen");
        return 0;
    }
    
    
    
    // create cmsSettings
    $res = cmsAdminNew_settings($newName,$cmsVersion);
    if ($res) {
        echo ("<h3>CMS Einstellungen angelegt </h3>");        
    } else {
        errorStr("Fehler beim Anlegen der CMS Einstellungen");
        return 0;
    }
    
   
    // include OUTPUT HELP FILE
    include("cms_".$cmsVersion."/cms_page.php");
    $cmsName = $newName;
    $GLOBALS[cmsName] = $newName;
    $GLOBALS[cmsVersion] = $cmsVersion;
 
    
    // Create Database
    $res = cmsAdminNew_createDatabase();
     if ($res) {
        echo ("<h3>CMS Datenbanken angelegt </h3>");        
    } else {
        errorStr("Fehler beim Anlegen der CMS Datenbanken");
        return 0;
    }
        
    
    // Create Data
    $res = cmsAdminNew_createData();
     if ($res) {
        echo ("<h3>CMS Daten angelegt </h3>");        
    } else {
        errorStr("Fehler beim Anlegen der CMS Datenbanken");
        return 0;
    }
    
    return 1;



}


function cmsAdminNew_createFolder($newName) {
    // need
    global $ownServer,$targetPath,$createOut;
    $root = $_SERVER['DOCUMENT_ROOT']."/";
    echo ("CReate Folder $ownServer $targetPath $createOut ,$root,$newName <br />");
    if ($ownServer) {
        echo ("Check $root $newName <br>");
        if (is_dir($root.$newName."/")) {
            if ($createOut) echo "Folder '$newName' exist allready <br>";
        } else {
            $root = $_SERVER['DOCUMENT_ROOT']."/";
            $res = mkdir($root.$newName, 0777);
            if (!$res) {
                echo "Folder not Created <br>";
                return 0;
            }
        }
    }
    
    
    // Create path
    $pathList = array("images","style","cms","cms/admin","cms_contentTypes","cache","wireframe","help");
    $ok = 0;
    for ($i = 0;$i<count($pathList);$i++) {
        $path = $pathList[$i];
        echo ("Check Path $targetPath $path <br>");
        if (!is_dir($targetPath."/".$path."/")) {
            $res = mkdir($targetPath."/".$path, 0777);
            if ($res) { 
                if ($createOut) echo ("Path $path created <br />");
                $ok++;
            } else {
                echo ("Path $path not Created <br />");
            }            
        } else {
            $ok++;
            if ($createOut) echo("Path $path exist allready<br />");
        }
    }
    if ($ok == count($pathList)) {
        // echo ("Verzeichnisse angelegt <br />");
        return 1;
    } else {
        echo ("Fehler: Nur $ok / ".count($pathList)." Verzeichnisse angelegt <br>");
        return 0;
    }
}


function cmsAdminNew_createDatabase() {
    global $targetPath,$createOut;
    
    global $cmsName,$cmsVersion,$cmsType;
    global $dataBaseList;
    
    if ($createOut) echo ("<h2> Datenbanken anlegen </h2>");
    $dataBaseList = tableList($cmsType);
    
    unset($dataBaseList[bookmarks]);
    
    
    $ok = 0;
    $error = 0;
    foreach ($dataBaseList as $key => $value) {
        $name = $value["name"];
        $adminPage = $value["adminPage"];
        $adminTarget = $value["target"];
        $createData = $value["createDATA"];
        $createDB = $value["createDB"];

        if ($createDB) {
            if ($createOut) echo ("Create DB for $name ($key): ");
            $tableData = tableData($key);
            if (is_array($tableData)) {
                // echo("DATA GET ");

                $exist = cmsAdminNew_TableExist($cmsName,$key);
                // echo "EXIST = $exist";
                if (is_int($exist)) {
                    if ($createOut) echo ("- EXIST ");
                    $res = cmsAdmin_checkTable($key, $cmsName);
                    if ($createOut)echo (" - CHECKED");
                    $ok++;

                } else {
                    if ($createOut) echo ("- notExist");
                    $createQuery = createTable($cmsName,$key, $tableData);
                    if ($createQuery) {
                        if ($createOut) echo ("- CREATE TABLE");
                        $ok++;
                    } else {
                        echo ("- Fehler beim anlegen von `".$cmsName."_cms_".$key."` <br>");
                        $error++;                        
                    }
                }
            } else {
                errorStr("NOT GET DATA for $key <br>");
            }
            if ($createOut) echo ("<br />");
        } else {
            if ($createOut) echo ("Keine Tabelle anlegen für $key <br />");
            $ok++;
        }       
    }
    
    if ($error) {
        return 0;
    }
    
    if (count($dataBaseList) == $ok) return 1;
    echo ("FEHLER $ok / ".count($dataBaseList)." Tabellen angelegt <br>");
    
    return 0;
   


}

function cmsAdminNew_createData() {
    // need
    global $targetPath,$createOut;
    
    global $cmsName,$cmsVersion;
    global $dataBaseList;
    
    if ($createOut) echo ("<h2> DATEN ERSTELLEN </h2>");
    
    $ok = 0;
    $error;
    foreach ($dataBaseList as $key => $value) {
        $name = $value["name"];
        $adminPage = $value["adminPage"];
        $adminTarget = $value["target"];
        $createData = $value["createDATA"];
        $createDB = $value["createDB"];

        if ($createData) {
            if ($createOut) echo ("CREATE DATA for $name ($key): ");
            $res = "Trulla";
            $tableData = tableData($key);
            switch($key) {
                case "user" : $res = cmsAdminNew_DataUser($cmsName,$key,$tableData); break;
                case "pages" : $res = cmsAdminNew_DataPages($cmsName,$key,$tableData); break;
                case "content" : $res = 1; break;
                case "text" : $res = 1; break;
                case "layout" : $res = cmsAdminNew_DataLayout($cmsName,$key,$tableData,$cmsName,$longName); break;
                default :
                    echo errorStr("not defined Create Data for $name ($key)<br />");
                    $res = "not";
            }
            if ($res) {
                $ok++;
            } else {
                echo (errorStr("Fehler beim Anlegen von Daten für $name ($key) - $res<br />"));
                $error++;
            }
            
            
            //if ($res != "not") {
            if ($createOut) echo ("RESULT = $res");
            //}
            if ($createOut) echo ("<br>");
        } else {
            if ($createOut) echo ("Keine Daten erstellen für $name ($key) <br />");
            $ok ++;
        }
    }
    if ($error) {
        return 0;
    }
    
    if (count($dataBaseList) == $ok) return 1;
    echo ("FEHLER $ok / ".count($dataBaseList)." Tabellen angelegt <br>");
    
    return 0;
    
    
}



    

function errorStr($str) {
    // need
    echo ("<span style='color:#f00;font-weight:bold;'>$str</span>");
}

function cms_errorBox($str) {
    errorStr($str);
}

function cms_infoBox($str) {
    errorStr($str);
}

function cmsAdminNew_settings($newName,$cmsVersion) {
    // need
    global $targetPath,$createOut;

    $zub = "\r";
    $outFile = $targetPath."/cmsSettings.php";
    $saveText = "<?php ".$zub;
    $saveText .= "   global $"."cmsName,$"."cmsVersion;".$zub;
    $saveText .= '   $cmsName = "'.$newName.'";'.$zub;
    $saveText .= '   $cmsVersion = "'.$cmsVersion.'";'.$zub;
    $saveText .= '?>';
    if ($createOut) echo ("Create CMS SETTINGS'$outFile' $newName $cmsVersion<br>");

    saveText($saveText, $outFile);

    if (file_exists($outFile)) return 1;

    return 0;
}
        // put your code here

function cmsAdminNew_TableExist($cmsName,$dbName) {
    // need
    global $createOut;
    
    if ($dbName == "cms_settings") $tableName = $dbName;
    else $tableName = $cmsName."_cms_".$dbName;


    $query = "SELECT * FROM `$tableName` ";
    $result = mysql_query($query);
    if (!$result) {
        if ($createOut) echo "NOT Exist `$tableName` $query <br>";
        return "notExist";
    }


    $anz = mysql_num_rows($result);
    // if ($createOut) echo " Exist `$tableName` $anz <br>";
    return $anz;
}

function createTable($newName,$dbName,$tableData) {
    // need
    global $createOut;
    foreach ($tableData as $key => $value) {
        if ($query) $query.= ", ";
        $query .= "`$key` ";
        foreach($value as $nr => $data) {
            $query.= "$data ";
        }

    }
    if ($dbName == "cms_settings") $query = "CREATE TABLE `$dbName` (".$query;
    else $query = "CREATE TABLE `".$newName."_cms_".$dbName."` (".$query;

    $query .= ", INDEX(`id`) ) CHARACTER SET = utf8;";
    if ($createOut) echo ($query."<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo ("<br>Error in <br>$query <br>");
        return 0;
    }
    return 1;

}

function insertContent($cmsName,$code,$tableData,$insertData) {
    // need
    $type = $insertData[type];
    $pageId = $insertData[pageId];

    
    global $createOut;
    
    $query = "SELECT * FROM `".$cmsName."_cms_".$code."` WHERE `pageId` ='$pageId' AND `type` = '$type' ";
    // echo ("$query <br>");
    $result = mysql_query($query);
    if ($result) {
        // echo ("RESULT = OK <BR>");
        $data = mysql_fetch_assoc($result);
        if (is_array($data)) {
            if ($createOut) echo " Exist $pageId / $type <br>";
            return $data[id];
        }
    }
    if ($createOut) echo "NOT EXIST $pageId / $type - Create ";
    $res = insertTable($cmsName,$code,$tableData,$insertData);
    if ($createOut) {
        if ($res) echo ("OK");
        else errorStr("FEHLER");
        echo ("<br>");
    }
    return $res;

}


function insertTable($cmsName,$code,$tableData,$insertData) {
    // need
    $query = "";
   // foreach ($tableData as $key => $value) echo ("$key <br>");
    foreach ($insertData as $key => $value ) {
        // echo ("$key => $value table = $tableData[$key]<br>");
        if ($tableData[$key]) {
            if ($query) $query .= ", ";
            $query .= "`$key`='$value' ";
        }
    }
    if ($query) {
        $query = "INSERT INTO `".$cmsName."_cms_".$code."` SET ".$query;
        // echo ("INSERT $query <br>");
        $result = mysql_query($query);
        if ($result) {
            $insertId = mysql_insert_id();
            // echo ("Daten mit $insertId angelegt in $cmsName $code <br>");
            return $insertId;
        } else {
            echo ("Error in Query $query <br>");
            return 0;
        }
    }
}


function cmsAdminNew_user($newName,$tableData) {
    $code = "user";

    $exist = cmsAdminNew_TableExist($newName,$code);

    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_user` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_user` <br>");
            return 0;
        }
        $exist = 0;
    }
    $res = cmsAdminNew_Data($cmsName,$key);
}

function cmsCheckId($cmsName,$key,$id) {
    // need
    $query = "SELECT * FROM `".$cmsName."_cms_".$key."` WHERE `id`=$id";
    // echo ($query);
    $result = mysql_query($query);
    if ($result) {
        $data = mysql_fetch_assoc($result);
        // echo($data);
        if (is_array($data)) return 1;
    }

    return 0;
}


function cmsCheckPage($cmsName,$code,$name) {
    // need
    $query = "SELECT * FROM `".$cmsName."_cms_".$code."` WHERE `name`='$name'";
    // echo ($query);
    $result = mysql_query($query);
    if ($result) {
        $data = mysql_fetch_assoc($result);
        // echo($data);
        if (is_array($data)) {


            return $data[id];
        }
    }

    return 0;
}



function cmsAdminNew_DataUser($cmsName,$code,$tableData) {
    // need
    global $createOut,$cmsPassword,$ownServer;
    
    if (!$cmsName) $cmsPassword = $cmsName;
   
    $exist = cmsCheckId($cmsName,$code,1);
    if (!$exist AND $ownServer) {
        $insertData = array("id"=>1,"userName"=>"superadmin","password"=>"nmzu70wsx","userLevel"=>9,"email"=>"sk@stefan-koelmel.com","vName"=>"Stefan","nName"=>"Kölmel");
        $insertResult = insertTable($cmsName,$code,$tableData,$insertData);
        
        if (!$insertResult) {
            echo (errorStr("Fehler beim Angelegen von Benutzer 'superadmin'"));
        }
        if ($createOut) echo ("- Superadmin create <br />");
    } else {
        if ($createOut) echo ("- Superadmin exist <br />");
    }

    
    
    $exist = cmsCheckId($cmsName,$code,2);
    if (!$exist) {
        $insertData = array("id"=>2,"userName"=>"cmsadmin","password"=>$cmsPassword,"userLevel"=>8,"email"=>"cmsadmin@stefan-koelmel.com","vName"=>"CMS","nName"=>"Admin");
        $insertResult = insertTable($cmsName,$code,$tableData,$insertData);
        if (!$insertResult) {
            echo (errorStr("Fehler beim Angelegen von Benutzer 'cmsAdmin'"));
            return 0;
        }
        if ($createOut) echo ("Benutzer - cmsAdmin create <br />");
    } else {
        if ($createOut) echo ("Benutzer - cmsAdmin exist <br />");
    }
    return 1;
}

function cmsAdminNew_text($newName,$tableData) {
    $code = "text";
    $exist = cmsAdminNew_TableExist($newName,$code);


    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_text` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_user` <br>");
            return 0;
        }
        $exist = 0;
    }
    return 1;

}


function cmsAdminNew_pages($newName,$tableData) {
    $code = "pages";
    $exist = cmsAdminNew_TableExist($newName,$code);

    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_$code` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_$code` <br>");
            return 0;
        }
    }
    $exist = 0;
    if ($exist == 0) {
        cms_page_create("index",$newData=array("title"=>"Startseite","navigation"=>1,"breadcrumb"=>1));
        $exist++;
    }

    if ($exist == 1) {
        cms_page_create("sitemap",$newData=array("title"=>"Sitemap","navigation"=>1,"breadcrumb"=>1));
        $exist++;
    }

    if ($exist == 2) {
        cms_page_create("impressum",$newData=array("title"=>"Impressum","navigation"=>1,"breadcrumb"=>1));
        $exist++;
    }

    if ($exist == 3) {
        cms_page_create("admin",$newData=array("title"=>"CMS Administration","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8));
        $exist++;
    }


    if ($exist == 4) {
        cms_page_create("admin_cmsCms",$newData=array("title"=>"CMS Verwaltung","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>4));
        $exist++;
    }

    if ($exist == 5) {
        cms_page_create("admin_data",$newData=array("title"=>"CMS Daten","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>4));
        $exist++;
    }


    if ($exist == 6) {
        cms_page_create("admin_cmsSettings",$newData=array("title"=>"CMS Einstellungen","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>5));
        $exist++;
    }

    if ($exist == 7) {
        cms_page_create("admin_cmsLayout",$newData=array("title"=>"CMS Layout","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>5));
        $exist++;
    }

    if ($exist == 8) {
        cms_page_create("admin_cmsImages",$newData=array("title"=>"CMS Bilder","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>5));
        $exist++;
    }


    if ($exist == 9) {
        cms_page_create("admin_cmsUser",$newData=array("title"=>"Benutzer","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));
        $exist++;
    }

    if ($exist == 10) {
        cms_page_create("admin_cmsDates",$newData=array("title"=>"Termine","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));
        $exist++;
    }


    if ($exist == 11) {
        cms_page_create("admin_cmsMail",$newData=array("title"=>"eMail Verwaltung","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));
        $exist++;
    }

    if ($exist == 12) {
        cms_page_create("layout_standard",$newData=array("title"=>"Standard Layout","navigation"=>0,"breadcrumb"=>0,"showLevel"=>0,"mainPage"=>0));
        $exist++;
    }

    return 1;
}


function cmsAdminNew_DataLayout($cmsName,$code,$tableData,$newName,$longName) {
    // need
    $tableData = tableData("content");
    
    
    global $targetPath,$createOut;
    

    $createName = "layout_standard";
    $createData = array("title"=>"Standard Layout","navigation"=>0,"breadcrumb"=>0,"showLevel"=>0,"mainPage"=>0);
    $creatData[targetPath] = $targetPath;
    $layoutID = cmsCheckPage($cmsName,$code,$createName);
    if (!$layoutID) {
        if ($createOut) echo "CREATE $createName ";
        $createData[targetPath] = $targetPath;
        $layoutID = cms_page_create($createName,$createData);
    } else {
        if ($createOut) echo ("EXIST");
    }
    if ($createOut) echo ("<br>");

    // echo ("<h1>TABLEDATE</h1>");
   //  foreach ($tableData as $key => $value) echo ("$key = $value <br>");


    $data = array();
    $data["logoFrame"] = 0;
    $data["name"] = 1;
    $data["nameFrame"] = "left";
    $data["slogan"] = 1;
    $data["sloganFrame"] = "left";
    $data["userFrame"] = 0;
    $data["languageFrame"] = 0;
    $data["basketFrame"] = 0;
    $data["wireframeSwitch"] = 1;
    $data["wireframeSwitchFrame"] = "right";
    $data["specialFrame"] = 0;
    $headerData = array2Str($data);


    $data = array("kontakt"=>1,"impressum"=>1,"sitemap"=>1);
    $footerData = array2Str($data);

    $i=0;
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"header","data"=>"$headerData");
    $insertID = insertContent($cmsName,"content",$tableData,$addData);

    $name = $newName;
    $slogan = $longName;
    $insert = array("contentId"=>"text_$insertID","name"=>"name","lg_dt"=>$name);
    cmsAdminNew_insertText($newName,$insert);
    $insert = array("contentId"=>"text_$insertID","name"=>"slogan","lg_dt"=>$slogan);
    cmsAdminNew_insertText($newName,$insert);

    $i++;
    $navData = array("direction"=>"hori","startLevel"=>0);
    $navStr = array2Str($navData);
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"navi","data"=>"$navStr");
    $insertID = insertContent($cmsName,"content",$tableData,$addData);



    $i++;
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"content","data"=>"");
    insertContent($cmsName,"content",$tableData,$addData);

    $i++;

    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"footer","data"=>"$footerData");
    insertContent($cmsName,"content",$tableData,$addData);

    $createName = "layout_left";
    $createData = array("title"=>"Navigation links","navigation"=>0,"breadcrumb"=>0,"showLevel"=>0,"mainPage"=>0);
    $layoutID = cmsCheckPage($cmsName,$code,$createName);
    if (!$layoutID) {
        if ($createOut) echo "CREATE $createName ";
        $createData[targetPath] = $targetPath;
        $layoutID = cms_page_create($createName,$createData);
    } else {
        if ($createOut) echo ("EXIST");
    }
    if ($createOut) echo ("<br>");

    $i=0;
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"header","data"=>"$headerData");
    $insertID = insertContent($cmsName,"content",$tableData,$addData);

    $name = $newName;
    $slogan = $longName;
    $insert = array();
    $insert = array("contentId"=>"text_$insertID","name"=>"name","lg_dt"=>$name);
    cmsAdminNew_insertText($newName,$insert);
    $insert = array("contentId"=>"text_$insertID","name"=>"slogan","lg_dt"=>$slogan);
    cmsAdminNew_insertText($newName,$insert);


    $i++;
    $navData = array("direction"=>"hori","startLevel"=>0);
    $navStr = array2Str($navData);
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"navi","data"=>$navStr);
    
    insertContent($cmsName,"content",$tableData,$addData);


    $i++;
    $frameArray = array("width1"=>180,"abs1"=>10);
    $frameArray = array2Str($frameArray);
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"frame2","data"=>$frameArray);
    $frameId = insertContent($cmsName,"content",$tableData,$addData);


    // INSERT TO LEFT FRAME
    // $i++;
    $navData = array("direction"=>"vert","startLevel"=>1);
    $navStr = array2Str($navData);
    $addData = array("pageId"=>"frame_".$frameId."_1","sort"=>1,"showLevel"=>0,"type"=>"navi","data"=>$navStr);
    insertContent($cmsName,"content",$tableData,$addData);

    //$i++;
    $loginData = array("showLogin"=>1,"showLogout"=>1);
    $loginStr = array2Str($loginData);
    $addData = array("pageId"=>"frame_".$frameId."_1","sort"=>2,"showLevel"=>0,"type"=>"login","data"=>$loginStr);
    insertContent($cmsName,"content",$tableData,$addData);



    // INSERT TO RIGHT FRAME
    // $i++;
    $addData = array("pageId"=>"frame_".$frameId."_2","sort"=>1,"showLevel"=>0,"type"=>"content","data"=>"");
    insertContent($cmsName,"content",$tableData,$addData);

    $i++;
    $data = array("kontakt"=>1,"impressum"=>1,"sitemap"=>1);
    $data = array2Str($data);
    $addData = array("pageId"=>$createName,"sort"=>$i,"showLevel"=>0,"type"=>"footer","data"=>"$footerData");
    insertContent($cmsName,"content",$tableData,$addData);


    return 1;
}


function cmsAdminNew_DataPages($cmsName,$code,$tableData) {
    // need
    $createPage = array();


    global $targetPath,$ownServer,$creatOut;
    global $dataBaseList;
    
    $createPage["index"] = array("title"=>"Startseite","navigation"=>1,"breadcrumb"=>1,"sort"=>0,"layout"=>"layout_standard");

    $createPage["kontakt"] = array("title"=>"Kontakt","navigation"=>0,"breadcrumb"=>1,"sort"=>1,"layout"=>"layout_standard");
    $createPage["sitemap"] = array("title"=>"Sitemap","navigation"=>0,"breadcrumb"=>1,"sort"=>2,"layout"=>"layout_standard");
    $createPage["impressum"] = array("title"=>"Impressum","navigation"=>0,"breadcrumb"=>1,"sort"=>3,"layout"=>"layout_standard");
    $createPage["user"] = array("title"=>"Benutzer","navigation"=>0,"breadcrumb"=>1,"sort"=>4,"layout"=>"layout_standard");
    $createPage["basket"] = array("title"=>"Warenkorb","navigation"=>0,"breadcrumb"=>1,"sort"=>5,"layout"=>"layout_standard");

    // ADMIN
    $createPage["admin"] = array("title"=>"CMS Administration","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"sort"=>6,"layout"=>"layout_left");

   
    $i = 0;
    foreach ($createPage as $key => $value) {
        if ($creatOut) echo ("Create Page $key / $value[title]");
        $value[targetPath] = $targetPath;
        $existID = cms_page_create($key,$value);
        // $existID = cmsCheckPage($cmsName,$code,$key);
        if ($existID ) {
            if ($creatOut) echo " CREATE $key ";
            // $existID = cms_page_create($key,$value);
        } else {
            if ($creatOut) echo (" EXIST id = $existID ");

        }

        if ($creatOut) echo ("<br />");

        switch ($key) {
            case "admin" :
               $adminID = $existID;
               if ($creatOut) echo ("ADMIN ID = $adminID <br>");

               $createName = "admin_cmsCms";
               $createData =  array("title"=>"CMS Verwaltung","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>$adminID,"layout"=>"layout_left");
               $createData[targetPath] = $targetPath;
               $settingsID = cms_page_create($createName,$createData);
               if ($settingsID) {
                   if ($creatOut) echo "CREATE $createName ";
                   // $settingsID = cms_page_create($createName,$createData);
               }
               if ($creatOut) echo ("ADMIN SETTINGS ID = $settingsID <br>");


               $createName = "admin_data";
               $createData =  array("title"=>"CMS Daten","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>$adminID,"layout"=>"layout_left");
               // $dataID = cmsCheckPage($cmsName,$code,$createName);
               $createData[targetPath] = $targetPath;
               $dataID = cms_page_create($createName,$createData);
               if ($dataID) {
                   if ($creatOut) echo "CREATE $createName ";
                   // $dataID = cms_page_create($createName,$createData);
               }
               if ($creatOut) echo ("ADMIN DATA ID = $dataID <br>");

               foreach ($dataBaseList as $key => $value) {
                    $name = $value["name"];
                    $title = $value["title"];
                    $adminPage = $value["adminPage"];
                    $adminTarget = $value["target"];
                    $createData = $value["createDATA"];
                    $createDB = $value["createDB"];
                    if ($adminPage) {
                        if ($creatOut) echo ("Create AdminPages in $adminTarget - ($key) $title ");
                        switch ($adminTarget) {
                            case "DATA" :
                                $mainPage = $dataID;
                                break;
                            case "SETTINGS" :
                                $mainPage = $settingsID;
                                break;
                            default :
                                errorStr("NO ADMINTARGET $adminTarget in $name <br>");
                                $mainPage = 0;
                        }
                        if ($mainPage) {
                            $createName = "admin_cms".ucfirst($key);
                            if (!$title) {
                                $title = "CMS ".ucfirst(strtolower($name));
                            }
                            $createData =  array("title"=>$title,"navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>$mainPage,"layout"=>"layout_left");
                            $createData[targetPath] = $targetPath;
                            $adminID = cms_page_create($createName,$createData);
                            //$adminID = cmsCheckPage($cmsName,$code,$createName);
                            if ($adminID) {
                                if ($creatOut) echo "CREATE $createName ";
                                //$adminID = cms_page_create($createName,$createData);
                            } else {
                                if ($creatOut) echo ("NOT CREATE");
                            }
                        }
                        if ($creatOut) echo ("<br />");
                    }
              }
              break;


        }

    }
    return 1;

}
function cmsAdminNew_insertText($newName,$insert) {
    // need

    $query = "";
    foreach ($insert as $key => $value) {
        if ($query) $query .= ", ";
        $query .= "`$key`='$value' ";
    }

    $query = "INSERT INTO `".$newName."_cms_text` SET ".$query;


    $result = mysql_query($query);
    if (!$result) { echo ("Error in $query <br>"); return 0;}
    return 1;

}

?>