<?php

    function archive_getMain_Sub_Cat($mainCatId) {
        $res = "";
        switch ($mainCatId) {
            case 145 : $res = "dontAdd"; break;
            case 146 : // Ausstellungen
                $res=array("mainCat"=>307,"subCat"=>309); break;
            case 147 : $res = "dontAdd"; break;
            // case 147 : // Ausstellungstermine
            //      $res=array("mainCat"=>307,"subCat"=>309); break;
            case 148 : $res = "dontAdd"; break;
            case 149 : $res = "dontAdd"; break;
            case 150 : $res = "dontAdd"; break;
            case 151 : // Buchtipps
                $res=array("mainCat"=>310,"subCat"=>313); break;
            case 152 : $res = "dontAdd"; break;
            // case 152 : // Club News
            //    $res=array("mainCat"=>318,"subCat"=>323); break;
            case 153 : // Comedy
                $res=array("mainCat"=>304,"subCat"=>306); break;
            case 154 : $res = "dontAdd"; break; // djHeiko
            case 155 : // Dr. Mabuse
                $res=array("mainCat"=>310,"subCat"=>315); break;
            case 156 : // Events
                $res=array("mainCat"=>300,"subCat"=>303); break;
            case 157 : // Film
                $res=array("mainCat"=>310,"subCat"=>314); break;
            case 158 : // Film
                $res=array("mainCat"=>310,"subCat"=>314); break;

            case 159 : $res = "dontAdd"; break; // Freibäder
            case 160 : $res = "dontAdd"; break; // FreizeitTipps
            case 161 : $res = "dontAdd"; break; // Gastro News
            // case 161 : // Gastro News
            //     $res=array("mainCat"=>318,"subCat"=>324); break;
            case 162 : // Inhalt
                $res=array("mainCat"=>310,"subCat"=>311); break;
            case 163 : // Klassik
                $res=array("mainCat"=>300,"subCat"=>302); break;
            case 164 : // Kunst Aktuell
                $res=array("mainCat"=>307,"subCat"=>308); break;
            case 165 : // Lesungen
                $res=array("mainCat"=>310,"subCat"=>312); break;

            case 166 : // Local Heroes
                $res=array("mainCat"=>300,"subCat"=>301); break;
            case 167 : $res = "dontAdd"; break; // Lokalkultur
            case 168 : // Meldungen
                $res=array("mainCat"=>310,"subCat"=>311); break;
            case 169 : $res = "dontAdd"; break; // 	Musik in Kneipen	löschen
            case 170 : $res = "dontAdd"; break; // 	Partys	löschen
            case 171 : $res = "dontAdd"; break; // 	Polizeimeldungen	löschen
            case 172 : $res = "dontAdd"; break; // 	Raucherbereiche	löschen
            case 173 : $res = "dontAdd"; break; // 	Schwimmbäder	löschen
            case 174 : $res = "dontAdd"; break; // 	Sportmeldungen	löschen

            case 175 : // Theater
                $res=array("mainCat"=>304,"subCat"=>305); break;

            case 156 : // Events
                break;
            case 176 : $res = "dontAdd"; break; //Tipps für Kids	löschen
            case 177 : $res = "dontAdd"; break; // Verlosungen	löschen
            case 178 : $res = "dontAdd"; break; // Weihnachten	löschen
            case 179 : $res = "dontAdd"; break; // Wintersport	löschen
            case 297 : $res = "dontAdd"; break; // Kino	löschen
            // case 298 : $res = "dontAdd"; break; // Eventle	303
            case 298 : // Events
                $res=array("mainCat"=>300,"subCat"=>303); break;
            case 299 : // Filme etc.	löschen
                $res=array("mainCat"=>310,"subCat"=>314); break;
        }
        return $res;
    }


    function checkList($articlesList,$specialView) {
        // echo ("checkList($articlesList,$specialView) <br>");
        global $cmsName;

        if (is_string($articlesList[query])) {
            $query = $articlesList[query];
            //echo ("Query = $query <br>");
            $result = mysql_query($query);
            if ($result) {
                $articlesList = array();
                while ($article = mysql_fetch_assoc($result)) {
                    if (is_string($article[data])) $article[data] = str2Array ($article[data]);
                    $articlesList[] = $article;
                }
                //echo ("Anzahl = ".count($articlesList)."<br>");
            }
        }


        $loop = 0;
        if ($loop) {
            $query = "Select * From `klappeAuf_cms_category` WHERE `mainCat` = 140";
            $result = mysql_query($query);
            $rangeList = array();
            while ($cat = mysql_fetch_assoc($result)) {
                $id = $cat[id];
                $name = $cat[name];


                $nameNew = str_replace("/","-", $name);
                if ($nameNew != $name) {
                    $query2 = "UPDATE `klappeAuf_cms_category` SET `name`='$nameNew' WHERE `id` = $id";
                    $result2 = mysql_query($query2);
                    if (!$result2) echo ($query2."<br>");

                }


                $rangeList["$id"]=$nameNew;
                // echo ("DateRange = $id $name <br>");
            }

            foreach ($rangeList as $rangeId => $rangeName) {
                // echo ("<h1> $rangeId = $rangeName </h1>");
                $query = "SELECT * FROM `klappeAuf_cms_articles` WHERE `dateRange`=$rangeId";
                // $query = "UPDATE `klappeAuf_cms_articles` SET `dateRange` = '$rangeName' WHERE `dateRange`=$rangeId";
                $result = mysql_query($query);
                if (!$result) {
                    echo ("<h1>$query</h1>");
                    echo (mysql_error()."<br>");
                }
            }
        }

       //  $mode = "checkImage";
        //$mode = "compareTxt";
        //$mode = "checkDouble";
        // $mode = "oldCategory";

        if (!$mode AND $specialView) $mode = $specialView;
        if (!$mode) $mode = "nothing";
        // echo ("checkList($articlesList,$specialView) Mode =$mode <br>");
        switch ($mode) {



            case "nothing": break;
            case "hidden" :


            case "noWeb" : break;



            case "noRegion" :

//                if ($_POST[setRegion]) {
//                    $setCityList = $_POST[setLocation ];
//                    if (is_array($setCityList)) {
//                        foreach ($setCityList as $city => $regionId) {
//                            if ($regionId > 0) {
//                                echo ("Setze $city = $regionId <br>");
//                                $query = "UPDATE `".$cmsName."_cms_location` SET `region`=$regionId WHERE `city` = '$city'";
//                                $result = mysql_query($query);
//                                if (!$result) echo ("Error in QUERY $query <br>");
//
//                            }
//                        }
//
//                    }
//                }




//                $regionList = array();
//                $getList = cmsCategory_getList(array("mainCat"=>180),"");
//                for($i=0;$i<count($getList);$i++) {
//                    $name = $getList[$i][name];
//                    $id   = $getList[$i][id];
//                    // echo ("Name $name = $id <br>");
//                    $regionList[$name]=$id;
//                }
//                $cityList = array();
                break;

            case "checkImage" :

                //$query = "UPDATE `klappeAuf_cms_images` SET `orgpath`='images/articles/2005-12/' WHERE `orgpath`= 'images/articles/2005_12/' ";
                //$result = mysql_query($query);
                // if (!$result) echo ("Erroro in QUery $query<br>");

                $dateRangeList = array();
                $getList = cmsCategory_getList(array("mainCat"=>140),"");
                for($i=0;$i<count($getList);$i++) {
                    $name = $getList[$i][name];
                    $id   = $getList[$i][id];
                    // echo ("Name $name = $id <br>");
                    $dateRangeList[$id]=$name;
                }
//                $cityList = array();

                break;

            case "checkDouble" :
//                $sameNames = array();
//                $getList = cmsCategory_getList(array("mainCat"=>8), "name");
//                for ($i=0;$i<count($getList);$i++) {
//                    $name = $getList[$i][name];
//                    $id   = $getList[$i][id];
//                    // echo ("$id $name <br>");
//                    $catList[$id] = $name;
//                }
                break;


            case "compareTxt" :
                break;
            case "noLocation" :
                break;

            case "oldCategory" :
                for ($catId=145;$catId<180;$catId++) {

                    $newCat = $this->archive_getMain_Sub_Cat($catId);
                   // echo ("$catId $newCat<br>");
                    if (is_array($newCat)) {
                        $setMainCat = $newCat[mainCat];
                        $setSubCat = $newCat[subCat];

                        $query = "SELECT * FROM `klappeAuf_cms_articles` WHERE `category` = $catId ";
                        $result = mysql_query($query);
                        if ($result) {
                            $anz = mysql_num_rows($result);
                            if ($anz > 0) {
                                echo ("found with old Cat $anz <br>");
                                echo ("id=$id cat=$catId sub=$subCat ==> $setMainCat / $setSubCat <br>");
                            }
                        } else {
                            echo ("error in query $query <br>");
                        }

//                        $updateQuery = "UPDATE `klappeAuf_cms_articles` SET `category`=$setMainCat ,`subCategory`=$setSubCat WHERE `id`=$id";
//                        $resUpdate = mysql_query($updateQuery);
//                        if (!$resUpdate) echo ("$updateQuery<br>");
                    } else {
                        if ($newCat == "dontAdd") {
                            $query = "SELECT `id` FROM `klappeAuf_cms_articles` WHERE `category` = $catId ";
                            $result = mysql_query($query);
                            if ($result) {
                                $anz = mysql_num_rows($result);
                              //  echo ("found with old Cat $anz <br>");
                                $categoryName = cmsCategory_getName_byId($catId);
                                if ($anz > 0 ) {

                                    $delQuery = "DELETE FROM `klappeAuf_cms_articles` WHERE `category`=$catId ";
                                    $result = mysql_query($delQuery);
                                    if ($result) {
                                         echo ("DEL $catId '$categoryName' anz=$anz<br>");
                                    } else {
                                        echo ("error in query $delQuery <br>");
                                    }
                                }

                            } else {
                                echo ("error in query $query <br>");
                            }


                            $delCats[$catId]++;
                        }
                        // echo ("id=$id cat=$cat sub=$subCat ==> $newCat <br>");
                    }

                }

//                foreach ($delCats as $oldCategory => $anz) {
//                    $categoryName = cmsCategory_getName_byId($oldCategory);
//                    echo ("DEL $oldCategory '$categoryName' anz=$anz<br>");
////                    $delQuery = "DELETE FROM `klappeAuf_cms_articles` WHERE `category`=$oldCategory ";
////                    $delResult = mysql_query($delQuery);
////                    if (!$delResult) echo (" -> $delQuery <br>");
//
//                }

                break;

            case "active" : break;


            default :
                echo ("unkown $mode in admin_articles_checkList <br>");


        }


        // LOOP LIST
        for ($i=0;$i<count($articlesList);$i++) {
            $articles = $articlesList[$i];
            $articleId = $articles[id];

            switch ($mode) {

                case "noLocation" :
                    if ($articles[location]) break;

                    $name = $articles[name];
                    $data = $articles[data];
                    $dateRange = $articles[dateRange];
                    $url = $articles[url];

                    if ($url) {
                        $locData = cmsLocation_get(array("url"=>$url));
                        if (is_array($locData)) {
                            $locName = $locData[name];
                            $locId   = $locData[id];
                            $locRegion = $locData[region];
                            //echo ("Ort '$locName'  über url gefunden<br>");
                            $query = "UPDATE `$GLOBALS[cmsName]_cms_articles` SET `location`=$locId, `region`=$locRegion  WHERE `id` = $articles[id]";
                            $result = mysql_query($query);
                            if (!$result) {
                                echo ("Error in Query $query<br>");
                            } else {
                                break;
                            }
                        }
                    }


                    if (is_array($data)) {
                        $ort = $data[ort];
                        if ($ort) {

                            $wwwOff = strpos($ort, "www");
                            //echo ("ORT aus Data = $ort  wwwOff=$wwwOff<br>");
                            if (is_integer($wwwOff)) {
                                if ($wwwOff>0) $url = substr($ort,$wwwOff);
                                else $url = $ort;
                                $slashOff = strpos($url,"/");
                                if ($slashOff) $url = substr($url,0,$slashOff);
                                echo ("Ort ist www $url / $ort<br>");
                                $locData = cmsLocation_get(array("url"=>"%$url%"));
                                if (is_array($locData)) {
                                    $locName = $locData[name];
                                    $locId   = $locData[id];
                                    $locRegion = $locData[region];
                                    echo ("Ort '$locName'  über url gefunden<br>");
                                    $query = "UPDATE `$GLOBALS[cmsName]_cms_articles` SET `location`=$locId, `region`=$locRegion  WHERE `id` = $articles[id]";
                                    //$result = mysql_query($query);
                                    if (!$result) {
                                        echo ("Error in Query $query<br>");
                                    }
                                }


                            } else {
                                echo ("Ort ist Name $ort <br>");
                            }

                        }
                    // echo ("Data is array from $articles[data]<br>");
                    //show_array($data);
                    }
                    break;

                case "noRegion" :
//                    $name = $articles[name];
//                    $id   = $articles[id];
//                    $city = $articles[city];
//                    switch ($city) {
//                        case "KA" : $city = "Karlsruhe"; break;
//                        case "Baden - Baden" : $city = "Baden-Baden"; break;
//
//                    }
//                    if ($regionList[$city]) {
//                        echo ("City $city found in $regionList[$city] <br>");
//                        $query = "UPDATE `".$cmsName."_cms_articles` SET `region`=$regionList[$city] WHERE `id`=$id ";
//                        $result = mysql_query($query);
//                        if (!$result) {
//                            echo ("Error in Query $query<br>");
//                        }
//
//
//                    } else {
//                        if ($city) {
//                            if (!is_array($cityList[$city])) $cityList[$city] = array("name"=>array(),"id"=>array());
//                            $cityList[$city][name][] = $name;
//                            $cityList[$city][id][] = $id;
//                            // echo ("Name = $name $city <br>");
//                        }
//                    }
                   break;




                case "checkImage" :


                    // echo ("CheckImage $i<br>");
                    $name = $articles[name];
                    $data = $articles[data];
                    $dateRange = $articles[dateRange];
                    if (is_array($data)) {
                        // echo ("Data is array from $articles[data]<br>");
                        // show_array($data);
                        $imageFile = $data[imageStr];
                        if ($imageFile) {
                             switch ($imageFile) {
                                    case "Kalinowski_SigneD'uneVague.jpg" : $imageFile = "Kalinowski_SigneDuneVague.jpg"; break;
                                }

                            echo ("$name = '$imageFile' $articleId <br>");
                            $dateRangeName = $dateRangeList[$dateRange];
                            // echo ("$dateRangeName = $dateRange <br>");
                            if ($dateRange) {
                                //$serverPath = "http://www.klappeauf.de/bilder/inhalte/".str_replace("-","_",$dateRange)."/big/";
                                //if (file_exists($serverPath.$imageFile)) {
                                // echo ("exist $imageFile <br>");
                                // } else {
                                //        echo ("not exist $serverPath $imageFile <br>");
                                // }



                                $folder = "images/articles/".str_replace("/","-",$dateRangeName)."/";
                                $folder = "images/articles/$dateRange/";
                                $imageData = cmsImage_get(array("orgpath"=>$folder,"fileName"=>$imageFile));
                               // echo ("Folder = $folder $imageData<br>");
                                $imageId = 0;
                                if (is_array($imageData)) {
                                    $imageId = $imageData[id];
                                } else {
                                     echo ("Article = $articleId $name Image File nicht '$folder' gefunden fileName = '$imageFile'<br>");
                                     $imageDataMore = cmsImage_get(array("fileName"=>$imageFile));
                                     if (is_array($imageDataMore)) {
                                        $imageId = $imageDataMore[id];
                                     } else {
                                         if ($imageDataMore == "more") {
                                             echo "mehrere gefunden für $imageFile<br>";
                                             $imageListMore = cmsImage_getList(array("fileName"=>$imageFile),"list");
                                             if (count($imageListMore)== 1) {
                                                 $imageId = $imageListMore[0][id];
                                                 echo ("nur ein Inhalt $imageListMore ->  $imageId<br>");
                                                 show_array($imageListMore[0]);
                                             } else {
                                                 for ($m=0;$m<count($imageListMore);$m++) {
                                                     $moreData = $imageListMore[$m];
                                                     echo (" $moreData[fileName] $moreData[md5] $moreData[orgpath] <br>");
                                                 }

                                                // show_array($imageListMore);
                                             }
                                         }
                                         echo ("More Data $imageDataMore anzahl = ".count($imageDataMore)."<br>");
                                     }


                                }

                                if ($imageId>0) {
                                    echo "ImageData erhalten $imageId <br>";
                                    $newData = array();
                                    foreach ($data as $key => $value) {
                                        if ($key != "imageStr") $newData[$key] = php_clearStr ($value);
                                    }

                                    $newData = array2Str($newData);

                                    $query = "UPDATE `$GLOBALS[cmsName]_cms_articles` SET `image`=$imageId, `data`='$newData' WHERE `id` = $articles[id]";
                                    $result = mysql_query($query);
                                    if (!$result) {
                                        echo ("Error in Query $query <br>");
                                    }
                                }

                            } else {
                                echo ("No DaterangeName $dateRange <br>");
                            }
                        }
                    } else {
                        echo ("No Data for $name $data <br>");
                    }
                    break;
                case "checkDouble" :
//                    $name = $articles[name];
//                    // echo ("Name =$name<br>");
//                    if ($name == $lastName) {
//                        // echo("Gleich wie letzter Name <br>");
//                        $sameNames[] = $articles;
//
//                    } else {
//
//                        if (is_array($sameNames) and count($sameNames)>1) {
//                            if (count($sameNames)>=2) {
//                                $newData = array("data"=>array());
//                                for ($c=0;$c<count($sameNames);$c++) {
//                                    $loc = $sameNames[$c];
//                                    foreach($loc as $key => $value) {
//                                        switch ($key) {
//                                            case "id" :
//                                                // echo ("ID = $id<br>");
//                                                break;
//                                            case "category" :
//                                                $catStr = $newData[category];
//                                                if (strlen($catStr)> 2) $catStr .="";
//                                                else $catStr ="|";
//                                                $catStr .= $value."|";
//                                                $newData[category] = $catStr;
//                                                // echo ("Category = $catStr <br>");
//                                                break;
//
//                                            case "image" :
//                                                $imageId = $value;
//                                                $imageStr = $newData[image];
//                                                if ($imageId>0) {
//                                                    if (strlen($imageStr)> 2) $imageStr .="";
//                                                    else $imageStr ="|";
//                                                    $imageStr .= $value."|";
//                                                    $newData[image] = $imageStr;
//                                                    // echo ("Image = $imageStr <br>");
//                                                }
//                                                break;
//
//                                            case "data" :
//                                                // echo ("Data = $value <br>");
//                                                foreach($value as $dataKey => $dataValue) {
//
//                                                    switch ($dataKey) {
//                                                        case "notice" :
//                                                            $catId = $loc[category];
//                                                            //echo ("Put Notice in $catId<br>");
//                                                            $newData[data]["info_".$catId] = $dataValue;
//                                                            break;
//                                                        default :
//                                                            //echo ("$dataKey = $dataValue <br>");
//                                                            if ($newData[data][$dataKey]) {
//                                                                if ($newData[data][$dataKey] == $dataValue) {
//                                                                      echo ("New = old in $dataKey ===>> $dataValue <br>");
//                                                                } else {
//                                                                    if (strlen($dataValue)>strlen($newData[$dataKey])) {
//                                                                         //echo ("<b>Take New in $dataKey because longer</b><br>");
//                                                                         //echo ("New = '$dataValue' ");
//                                                                         //echo ("Old = '".$newData[data][$dataKey]."'<br>");
//                                                                         $newData[data][$dataKey] = $dataValue;
//                                                                    } else {
//                                                                        //echo ("<b>shorter dont Take  $dataKey </b><br>");
//                                                                        //echo ("New = '$dataValue' ");
//                                                                        //echo ("Old = '".$newData[data][$key]."'<br>");
//                                                                    }
//                                                                }
//                                                            } else {
//                                                                $newData[data][$dataKey] = $dataValue;
//                                                            }
//
//                                                    }
//                                                }
//                                                break;
//                                            case "lastMod" :
//                                                break;
//
//                                            default :
//                                                if ($newData[$key]) {
//                                                    if ($newData[$key] == $value) {
//                                                        // echo ("New = old in $key ===>> $value <br>");
//                                                    } else {
//                                                        if (strlen($value)>strlen($newData[$key])) {
//                                                             //echo ("<b>Take New in $key because longer</b><br>");
//                                                             //echo ("New = '$value' ");
//                                                             //echo ("Old = '$newData[$key]'<br>");
//                                                             $newData[$key] = $value;
//                                                        } else {
//                                                            //echo ("<b>shorter dont Take  $key </b><br>");
//                                                            //echo ("New = '$value' ");
//                                                            // echo ("Old = '$newData[$key]'<br>");
//                                                        }
//                                                    }
//                                                } else {
//                                                    $newData[$key] = $value;
//                                                }
//
//                                        }
//                                    }
//                                }
//
//                                for ($c=0;$c<count($sameNames);$c++) {
//                                    $id = $sameNames[$c][id];
//                                    if ($c==0 ) {
//                                        echo ("<h3>Setze newData on id = $id </h3>");
//                                        $newData[id] = $id;
//                                        $saveRes = cmsarticles_save($newData);
//                                        // $saveRes = 1;
//                                        if (!$saveRes) {
//                                            echo ("Error in Update first Same articles $saveRes <br>");
//                                            die();
//                                        }
//                                        foreach($newData as $key => $value) {
//                                            switch ($key){
//                                                case "data" :
//
//                                                    foreach ($value as $dataKey => $dataValue ) {
//                                                        $newData[data][$dataKey] = php_clearStr($dataValue);
//                                                        echo (" -- >> Data $dataKey = '".subStr($dataValue,0,30)."..'<br>");
//                                                    }
//                                                    break;
//
//                                                default :
//                                                    echo (" -> $key = $value <br>");
//                                            }
//                                        }
//                                    } else {
//                                        if ($saveRes) {
//                                            echo ("Lösche satz mit ID = $id<br>");
//                                            $query = "DELETE FROM `klappeAuf_cms_articles` WHERE `id`=$id ";
//                                            $result = mysql_query($query);
//                                            // $result = 1;
//                                            if (!$result ) {
//                                                echo ("Error in Delete second ID form samearticless $id <br>$query<br>");
//                                                die();
//                                            }
//                                        }
//
//                                    }
//                                }
//
//                            } else {
//                                echo ("<h1> More  than 2 articless </h1>");
//                            }
//
//
//
////                            for ($c=0;$c<count($sameNames);$c++) {
////                                $loc = $sameNames[$c];
////                                $name = $loc[name];
////                                $id   = $loc[id];
////                                $cat  = $loc[category];
////                                $catName = $catList[$cat];
////                                echo ("<h2>SAME $id, $name, $cat $catName ".count($sameNames)." </h2>");
////                                // echo ("info $loc[info] <br>");
////
////                                if ($cat == 20) {
////                                    $notice = $loc[data][notice];
////                                   // echo ("Biergarten = $notice <br>");
////                                } else {
////                                    $notice = $loc[data][notice];
////                                   // echo ("Nicht Biergarten $notice <br>");
////                                }
//
//                           // }
//
//                        }
//
//                        $sameNames = array();
//                        $sameNames[] = $articles;
//
//                        $lastName = $name;
//
//
//                    }
//                    break;

                case "compareTxt" :
                    // not in Loop articless
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
