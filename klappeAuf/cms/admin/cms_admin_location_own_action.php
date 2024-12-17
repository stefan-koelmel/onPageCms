<?php
function checkList_own($locationList,$specialView) {
        // echo ("checkList($locationList,$specialView) <br>");
        global $cmsName;
        //$mode = "checkImage";
        //$mode = "compareTxt";
        // $mode = "checkDouble";
        if (!$mode AND $specialView) $mode = $specialView;
        if (!$mode) $mode = "nothing";
        switch ($mode) {
            case "nothing": break;
            case "hidden" : break;
            case "noWeb" : break;
            case "noRegion" :
                if ($_POST[setRegion]) {
                    $setCityList = $_POST[setLocation ];
                    if (is_array($setCityList)) {
                        foreach ($setCityList as $city => $regionId) {
                            if ($regionId > 0) {
                                echo ("Setze $city = $regionId <br>");
                                $query = "UPDATE `".$cmsName."_cms_location` SET `region`=$regionId WHERE `city` LIKE '$city'";
                                $result = mysql_query($query);
                                if (!$result) echo ("Error in QUERY $query <br>");

                            }
                        }
                    }
                }

                $regionList = array();
                $getList = cmsCategory_getList(array("mainCat"=>180),"");
                for($i=0;$i<count($getList);$i++) {
                    $name = $getList[$i][name];
                    $id   = $getList[$i][id];
                    // echo ("Name $name = $id <br>");
                    $regionList[$name]=$id;
                }
                $cityList = array();

            case "checkImage" :
                break;
            case "checkDouble" :
                $sameNames = array();
                $getList = cmsCategory_getList(array("mainCat"=>8), "name");
                for ($i=0;$i<count($getList);$i++) {
                    $name = $getList[$i][name];
                    $id   = $getList[$i][id];
                    // echo ("$id $name <br>");
                    $catList[$id] = $name;
                }
                break;
            case "compareTxt" :
                global $cmsName;
                $fn = $_SERVER['DOCUMENT_ROOT']."/$cmsName/cms/admin/adressen.csv";
                $query = "UPDATE `".$cmsName."_cms_location` SET `show` = '0' ";
                $result = mysql_query($query);
                if (!$result) {
                    echo ("Error in Reset Location List $query<br>");
                    return 0;
                }
                echo ("FileName to Compare $fn<br>");
                if (file_exists($fn)) {
                    $t = loadText($fn);
                    $adressList = explode("\r\n",$t);
                    echo ($adressList."Anzahl = ".count($adressList)."<br>");
                    $to = count($adressList);
                   //  $to = 20;
                    for ($i=0; $i<$to;$i++) {
                        if ($i== 0) { // Title
                            $data = explode(";",$adressList[$i]);
                            $setData = array();
                            for ($d=0;$d<count($data);$d++) {
                                // echo ("DATA $d , $data[$d] <br>");
                                $setData[$data[$d]]= "";
                            }
                        } else {
                            $idList = array();
                            $set = array();
                            $getData = explode(";",$adressList[$i]);
                            $name = $getData[2];
                            $name = iconv('ISO-8859-1', 'ISO-8859-15', $name);
                            $id = $getData[0];
                            if ($id) {
                                // echo ("CheckId  $id,$name <br>");
                                $locData = cmsLocation_get(array("id"=>$id));
                                if (!is_array($locData)) {
                                    // echo ("CheckId  $id,$name nicht gefunden <br>");
                                    $locDataList = cmsLocation_getList(array("name"=>$name));
                                    if (is_array($locDataList)) {
                                        for ($l=0;$l<count($locDataList);$l++) {
                                            $loc = $locDataList[$l];
                                            $dataName = $loc[name];
                                            $dataId    = $loc[id];
                                            // echo ("Found with Name $dataId $name $dataName<br>");
                                            $idList[] = $dataId;
                                        }
                                    } else {
                                        echo ("CheckId  $id,$name nicht gefunden <br>");
                                        echo ("Not Found with Name Res =$locData <br>");
                                    }
                                } else {
                                    $dataName = $locData[name];
                                    if ($name == $dataName) {
                                        $idList[] = $id;
                                    } else {
                                    //    echo ("Not Same  $id, '$name' <-> '$dataName' <br>");
                                    }
                                }
                            }
                            for ($l=0;$l<count($idList);$l++) {
                                $id = $idList[$l];
                                echo ("update $id with show = 1<br>");
                                $query = "UPDATE `".$cmsName."_cms_location` SET `show` = '1' WHERE `id`=$id ";
                                $result = mysql_query($query);
                                if (!$result) {
                                    echo ("Error in Reset Location List $query<br>");
                                }
                            }
                        }
                    }
                }
                break;
            default :
                echo ("unkown $mode in admin_location_checkList <br>");
        }
        // LOOP LIST
        $mode = "checkData";
        for ($i=0;$i<count($locationList);$i++) {
            $location = $locationList[$i];

            switch ($mode) {
                case "checkData" :
                    $name = $location[name];
                    $newLocation = $this->checkData($location);

//                    echo ("$newLocation[street]#$newLocation[streetNr]' ");
//                    echo ("$newLocation[plz]#$newLocation[city]' ");
//                    echo ("$newLocation[phoneRegion]#$newLocation[phonePhone]'<br>");
                    $compList = array("street","streetNr","plz","city","phoneRegion","phonePhone","phoneFax");
                    $query = "";
                    for ($c=0;$c<count($compList);$c++) {
                        $key = $compList[$c];
                        if ($location[$key] != $newLocation[$key]) {
                            echo (" --> change/$key) '$location[$key]'=>'$newLocation[$key] <br>");
                            if ($query!="") $query.=", ";
                            $query .= "`$key`='$newLocation[$key]'";
                        }
                    }
                    if ($query) {
                        echo ("changeData <b>$name</b><br>");
                        $locationId = $location[id];
                        $query = "UPDATE `klappeAuf_cms_location` SET ".$query." WHERE `id`=$locationId";
                        $result = mysql_query($query);
                        if (!$result) echo ("Query = $query <br>");
                    }
                    break;
                case "noRegion" :
                    $name = $location[name];
                    $id   = $location[id];
                    $city = $location[city];
                    switch ($city) {
                        case "KA" : $city = "Karlsruhe"; break;
                        case "Baden - Baden" : $city = "Baden-Baden"; break;
                    }
                    if ($regionList[$city]) {
                        echo ("City $city found in $regionList[$city] <br>");
                        $query = "UPDATE `".$cmsName."_cms_location` SET `region`=$regionList[$city] WHERE `id`=$id ";
                        $result = mysql_query($query);
                        if (!$result) {
                            echo ("Error in Query $query<br>");
                        }

                    } else {
                        if ($city) {
                            if (!is_array($cityList[$city])) $cityList[$city] = array("name"=>array(),"id"=>array());
                            $cityList[$city][name][] = $name;
                            $cityList[$city][id][] = $id;
                            // echo ("Name = $name $city <br>");
                        }
                    }
                    break;
                case "checkImage" :
                    // echo ("<h1>CheckImage</h1>");
                    $name = $location[name];
                    $data = $location[data];
                    if (is_array($data)) {
                        // echo ("Data is array from $location[data]<br>");
                        $imageFile = $data[imageFile];
                        if ($imageFile) {
                            $imageData = cmsImage_get(array("fileName"=>$imageFile));
                            if (is_array($imageData)) {
                                $imageId = $imageData[id];
                                echo "<h1>ImageData erhalten $imageId </h1>";
                                $newData = array();
                                foreach ($data as $key => $value) {
                                    if ($key != "imageFile") $newData[$key] = php_clearStr ($value);
                                }
                                // show_array($newData);
                                // echo ("&nbsp;<br>");

                                $newData = array2Str($newData);
                                $query = "UPDATE `$GLOBALS[cmsName]_cms_location` SET `image`=$imageId, `data`='$newData' WHERE `id` = $location[id]";
                                $result = mysql_query($query);
                                if (!$result) {
                                    echo ("Error in Query $query <br>");
                                }
                            } else {
                               // echo ("$name Image File nicht gefunden $imageFile<br>");
                            }
                        }
                    } else {
//                        $str = $data;
//                       //  $compareStr = 'a:1:{s:9:"imageFile";s:16:"AlterBrauhof.jpg";}';
//                        $id = $location[id];
//                       //  echo ("ID = $id <br>");
//                        if ($id==1046) { // $compareStr == $str) {
//
//                        //    $str = str_replace("&#034;", '"', $str);
//                            echo ("NoArray1($str) ".strlen($str)."<br>\n");
//                            $str = php_unclearStr($str,1);
//                            $compareArray = str2Array($str);
//                            if (!is_array($compareArray)) {
//                                $compareStr = 'a:3:{s:4:"open";s:0:"";s:6:"notice";s:148:"25 x 8 m Schwimmbecken 25 m x 8 m, abgegrenzter Nichtschwimmerbereich, Temp. 28°C, Therapiebecken, Kinderplanschbecken, 1m - und 3 m Sprungbrett, ";s:7:"kitchen";s:0:"";}';
//                                $compareArray = str2Array($compareStr);
//                                if (!is_array($compareArray)) {
//
//                                    echo ("NoArray2($compareArray) ".strlen($compareStr)." <br>");
//                                    for($i=0;$i<strlen($str);$i++) {
//                                        echo ("<h2>Zeichen $i ".$str[$i]." <-> ".$compareStr[$i]."</h2>");
//                                    }
//                                }
//                            }
//
//                        } else {
//                            echo ("NoArray1($str) ".strlen($str)."<br>\n");
//                        }
                    }
                    break;
                case "checkDouble" :
                    $name = $location[name];
                    // echo ("Name =$name<br>");
                    if ($name == $lastName) {
                        // echo("Gleich wie letzter Name <br>");
                        $sameNames[] = $location;
                    } else {
                        if (is_array($sameNames) and count($sameNames)>1) {
                            if (count($sameNames)>=2) {
                                $newData = array("data"=>array());
                                for ($c=0;$c<count($sameNames);$c++) {
                                    $loc = $sameNames[$c];
                                    foreach($loc as $key => $value) {
                                        switch ($key) {
                                            case "id" :
                                                // echo ("ID = $id<br>");
                                                break;
                                            case "category" :
                                                $catStr = $newData[category];
                                                if (strlen($catStr)> 2) $catStr .="";
                                                else $catStr ="|";
                                                $catStr .= $value."|";
                                                $newData[category] = $catStr;
                                                // echo ("Category = $catStr <br>");
                                                break;
                                            case "image" :
                                                $imageId = $value;
                                                $imageStr = $newData[image];
                                                if ($imageId>0) {
                                                    if (strlen($imageStr)> 2) $imageStr .="";
                                                    else $imageStr ="|";
                                                    $imageStr .= $value."|";
                                                    $newData[image] = $imageStr;
                                                    // echo ("Image = $imageStr <br>");
                                                }
                                                break;
                                            case "data" :
                                                // echo ("Data = $value <br>");
                                                foreach($value as $dataKey => $dataValue) {

                                                    switch ($dataKey) {
                                                        case "notice" :
                                                            $catId = $loc[category];
                                                            //echo ("Put Notice in $catId<br>");
                                                            $newData[data]["info_".$catId] = $dataValue;
                                                            break;
                                                        default :
                                                            //echo ("$dataKey = $dataValue <br>");
                                                            if ($newData[data][$dataKey]) {
                                                                if ($newData[data][$dataKey] == $dataValue) {
                                                                      echo ("New = old in $dataKey ===>> $dataValue <br>");
                                                                } else {
                                                                    if (strlen($dataValue)>strlen($newData[$dataKey])) {
                                                                         //echo ("<b>Take New in $dataKey because longer</b><br>");
                                                                         //echo ("New = '$dataValue' ");
                                                                         //echo ("Old = '".$newData[data][$dataKey]."'<br>");
                                                                         $newData[data][$dataKey] = $dataValue;
                                                                    } else {
                                                                        //echo ("<b>shorter dont Take  $dataKey </b><br>");
                                                                        //echo ("New = '$dataValue' ");
                                                                        //echo ("Old = '".$newData[data][$key]."'<br>");
                                                                    }
                                                                }
                                                            } else {
                                                                $newData[data][$dataKey] = $dataValue;
                                                            }
                                                    }
                                                }
                                                break;
                                            case "lastMod" :
                                                break;
                                            default :
                                                if ($newData[$key]) {
                                                    if ($newData[$key] == $value) {
                                                        // echo ("New = old in $key ===>> $value <br>");
                                                    } else {
                                                        if (strlen($value)>strlen($newData[$key])) {
                                                             //echo ("<b>Take New in $key because longer</b><br>");
                                                             //echo ("New = '$value' ");
                                                             //echo ("Old = '$newData[$key]'<br>");
                                                             $newData[$key] = $value;
                                                        } else {
                                                            //echo ("<b>shorter dont Take  $key </b><br>");
                                                            //echo ("New = '$value' ");
                                                            // echo ("Old = '$newData[$key]'<br>");
                                                        }
                                                    }
                                                } else {
                                                    $newData[$key] = $value;
                                                }
                                        }
                                    }
                                }
                                for ($c=0;$c<count($sameNames);$c++) {
                                    $id = $sameNames[$c][id];
                                    if ($c==0 ) {
                                        echo ("<h3>Setze newData on id = $id </h3>");
                                        $newData[id] = $id;
                                        $saveRes = cmsLocation_save($newData);
                                        // $saveRes = 1;
                                        if (!$saveRes) {
                                            echo ("Error in Update first Same Location $saveRes <br>");
                                            die();
                                        }
                                        foreach($newData as $key => $value) {
                                            switch ($key){
                                                case "data" :
                                                    foreach ($value as $dataKey => $dataValue ) {
                                                        $newData[data][$dataKey] = php_clearStr($dataValue);
                                                        echo (" -- >> Data $dataKey = '".subStr($dataValue,0,30)."..'<br>");
                                                    }
                                                    break;
                                                default :
                                                    echo (" -> $key = $value <br>");
                                            }
                                        }
                                    } else {
                                        if ($saveRes) {
                                            echo ("Lösche satz mit ID = $id<br>");
                                            $query = "DELETE FROM `klappeAuf_cms_location` WHERE `id`=$id ";
                                            $result = mysql_query($query);
                                            // $result = 1;
                                            if (!$result ) {
                                                echo ("Error in Delete second ID form sameLocations $id <br>$query<br>");
                                                die();
                                            }
                                        }
                                    }
                                }
                            } else {
                                echo ("<h1> More  than 2 Locations </h1>");
                            }
//                            for ($c=0;$c<count($sameNames);$c++) {
//                                $loc = $sameNames[$c];
//                                $name = $loc[name];
//                                $id   = $loc[id];
//                                $cat  = $loc[category];
//                                $catName = $catList[$cat];
//                                echo ("<h2>SAME $id, $name, $cat $catName ".count($sameNames)." </h2>");
//                                // echo ("info $loc[info] <br>");
//
//                                if ($cat == 20) {
//                                    $notice = $loc[data][notice];
//                                   // echo ("Biergarten = $notice <br>");
//                                } else {
//                                    $notice = $loc[data][notice];
//                                   // echo ("Nicht Biergarten $notice <br>");
//                                }
                           // }

                        }
                        $sameNames = array();
                        $sameNames[] = $location;
                        $lastName = $name;
                    }
                    break;
                case "compareTxt" :
                    // not in Loop Locations
                    break;

            }
        }
        // AFTER LOOP
         switch ($mode) {
                case "noRegion" :
                    if (count($cityList)) {
                        echo ("<form method='post'>");
                        foreach ($cityList as $city => $data) {
                            echo ("<h3>Keine Region für Stadt '$city' </h3>");
                            for ($i=0;$i<count($data[name]);$i++) {
                                if ($i>0) echo (" | ");
                                else echo (" Locations : ");
                                echo ("<b>'".$data[name][$i]."'</b>");
                            }
                            echo ("<br>&nbsp;<br>Setze Region = ");
                            echo (cmsCategory_selectCategory(0,"setLocation[$city]", array("empty"=>"Bitte Region wählen"),array("mainCat"=>180),"id")."<br>");
                        }
                        echo ("<input type='submit' value='Region Setzen' name='setRegion' >");
                        echo ("<input type='submit' value='abbrechen' name='cancel' >");
                        echo ("</form>");

                    }
                    break;
         }
    }
?>
