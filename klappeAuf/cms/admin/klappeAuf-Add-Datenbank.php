<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO8859-15">

        <title></title>
    </head>
    <body>
        <?php

function utf16_to_utf8($str) {
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);

    if ($c0 == 0xFE && $c1 == 0xFF) {
        $be = true;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $be = false;
    } else {
        return $str;
    }

    $str = substr($str, 2);
    $len = strlen($str);
    $dec = '';
    for ($i = 0; $i < $len; $i += 2) {
        $c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) :
                ord($str[$i + 1]) << 8 | ord($str[$i]);
        if ($c >= 0x0001 && $c <= 0x007F) {
            $dec .= chr($c);
        } else if ($c > 0x07FF) {
            $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
            $dec .= chr(0x80 | (($c >>  6) & 0x3F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        } else {
            $dec .= chr(0xC0 | (($c >>  6) & 0x1F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        }
    }
    return $dec;
}


function addToOutput($str) {
    $offSet = strpos($str, "$$");


    if ($offSet > 0) {
        //echo ("Str has Zeichen $$ '$str' <br>");

        $newStr = substr($str,0,$offSet)."<@TerminBrotFETT>".substr($str,$offSet+2);
        //echo ("NEW STRING is $newStr<br>");
        $offSet2 = strpos($newStr, "$$");
        if ($offSet2 > 0) {
            $str = substr($newStr,0,$offSet2)."<@\$p>".substr($newStr,$offSet2+2);
        }
        $str = addToOutput($str);
        // echo ("NEW STRING is $str<br>");
    }
    return $str;
}

function klappe_date_get($date,$type,$locationId,$title) {
    $query = "SELECT * FROM `klappe_date_dates` WHERE `date`='$date' AND`kat` = $type AND `location`=$locationId AND `headline` LIKE '$title'";
    $result = mysql_query($query);
    if ($result) {

        $anz = mysql_num_rows($result);
        if ($anz == 0) return 0;
        if ($anz == 1) {
            $termin = mysql_fetch_assoc($result);
            return $termin;
        }
        while ($termin = mysql_fetch_assoc($result)) {
            foreach($termin as $key => $value) {
                echo ("$key = $value | ");
            }
            echo ("<br>");
        }
    } else {
        echo ("Error in $query <br>");
    }
    return 0;
}

        // include($_SERVER['DOCUMENT_ROOT']."/includes/help.php");
        global $cmsVersion,$cmsName;
        $cmsVersion = "base";
        $cmsName = "klappeAuf";
        // include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
        include($_SERVER['DOCUMENT_ROOT']."/cms_base/cms.php");


//        $host     = "db153.puretec.de";
//        $user     = "dbo280303012";
//        $database = "db280303012";
//        $password = "u3aWcFFA";
//
//        @$link = mysql_connect($host, $user, $password);
//        @mysql_select_db($database, $link);


        // get Page Kat
        $categoryList = cmsCategory_getList(array("mainCat"=>1),""); //, $sort)
        // show_array($categoryList);

        $date_kat = array();
        for ($i=0;$i<count($categoryList);$i++) {
            $name = $categoryList[$i][name];
            $id = $categoryList[$i][id];
            $date_kat[$name] = $id;
        }
        // show_array($date_kat);

        // RegionList
         $regionList = cmsCategory_getList(array("mainCat"=>180),""); //, $sort)
        // show_array($categoryList);

        $date_region = array();
        for ($i=0;$i<count($regionList);$i++) {
            $name = $regionList[$i][name];
            $id = $regionList[$i][id];
            $date_region[$name] = $id;
        }
        // show_array($date_region);

//        $date_kat_id = 0;
//        $query = "SELECT * FROM `klappe_date_kat` ";
//        $result = mysql_query($query);
//        if ($result) {
//            while ($kat = mysql_fetch_assoc($result)) {
//                $id = $kat[id];
//                $name = $kat[name];
//                $date_kat[$name] = $id;
//                if ($id >= $date_kat_id) $date_kat_id = $id;
//            }
//        } else {
//            echo ("Error in Query '$query' <br>");
//        }
//
//        // get Page Kat
//        $date_city = array();
//        $date_city_id = 0;
//        $query = "SELECT * FROM `klappe_date_city` ";
//        $result = mysql_query($query);
//        if ($result) {
//            while ($kat = mysql_fetch_assoc($result)) {
//                $id = $kat[id];
//                $name = $kat[name];
//                $date_city[$name] = $id;
//                if ($id >= $date_kat_id) $date_kat_id = $id;
//            }
//        } else {
//            echo ("Error in Query '$query' <br>");
//        }
//
//        // get Page Kat
//        $date_location = array();
//        $date_location_id = 0;
//        $query = "SELECT * FROM `klappe_date_location` ";
//        $result = mysql_query($query);
//        if ($result) {
//            while ($kat = mysql_fetch_assoc($result)) {
//                $id = $kat[id];
//                $name = $kat[name];
//                $adress = $kat[adress];
//                $loc = strtolower($name);
//                $date_location[$loc] = array("id"=>$id,"name"=>$name,"adress"=>$adress);
//                // echo ("Get from database $loc $name ,$adress <br>");
//                if ($id >= $date_location_id) $date_location_id = $id;
//            }
//        } else {
//            echo ("Error in Query '$query' <br>");
//        }



        /* // get Page Kat
        $date_city = array();
        $date_city_id = 0;
        $query = "SELECT * FROM `klappe_date_city` ";
        $result = mysql_query($query);
        if ($result) {
            while ($kat = mysql_fetch_assoc($result)) {
                $id = $kat[id];
                $name = $kat[name];
                $date_city[$name] = $id;
                if ($id >= $date_kat_id) $date_kat_id = $id;
            }
        } else {
            echo ("Error in Query '$query' <br>");
        }*/





        // $fn = "Termine-August.txt";
        $fn = "Termine-Juli.txt";
        $t = loadText($fn);
        $spacer = $t[1].$t[2].$t[3];



//        for ($i=1;$i<20;$i++) {
//           // echo ("Zeichen $i = $t[$i], ".ord($t[$i])."<br>");
//        }

        //$t = str_replace($spacer, "-#-", $t);
        $t = str_replace("à","-|-", $t);
        $t = str_replace("ð","", $t);
        $t = str_replace("«","&acute;",$t);
        $t = str_replace("É","&hellip;",$t);
        $spacer = "-|-";
        // <meta http-equiv="Content-Type" content="text/html; charset=ISO8859-15">
        // <meta http-equiv="Content-Type" content="text/html; charset=UTF-16">
       // echo ("is utf16 - ".mb_check_encoding($t,"UTF-16")."<br>");

       // $t =  mb_convert_encoding($t,'ISO-8859-15','UTF-8');
       // $t = mb_con
       // $t = utf16_to_utf8($t); //mb_convert_encoding($t, "utf-8", "utf-16");
        // echo ("is utf8 - ".mb_check_encoding($t,"UTF-8")."<br>");
       // $t = utf8_decode($t);


        //$t = utf8_decode($t);
        echo ("Datei: ".$fn."<br>");
        echo ("Länge: ".strlen($t)."<br>");
        $such = "Dylan";
        $such = "Oliver Jordan";
        $off = strpos($t, $such);
        $off = 0;
        /*for ($i=$off;$i<$off+60;$i++) {
          echo ("Zeichen $i = $t[$i], ".ord($t[$i])."<br>");
        }*/

        $t = iconv("macintosh","ISO8859-15", $t);
        echo("Länge:".strlen($t)."<br>");
        echo ("String = ".substr($t,$off,400)."<br>");



        $tList = explode($spacer,$t);
        echo ("Anz Zeilen = ".count($tList)."<br>");

        $step = 0;
        $anz = count($tList);
        // $anz = 20;
        $dayList = array();
        for ($i=1;$i<$anz;$i++) {
            $inh = $tList[$i];
            if ($step == 0) {
                $termin = array();
                $termin["date"] = $inh;
               //  echo("<strong>$i - $step - $tList[$i] </strong><br>");
                $step = 1;
            } else {
                switch ($step) {
                    case 1 : $termin["category"] = $inh; break;
                    case 2 : $termin["region"] = $inh; break;
                    case 3 : $termin["name"] = $inh; break;
                    case 4 : $termin["info"] = $inh; break;
                    case 5 : $termin["location"] = $inh; break;
                    case 6 : $termin["time"] = $inh; break;
                    default :
                        echo("$i - $step - $tList[$i]<br>");
                }

                $step++;
                if ($step>6) {
                    $step = 0;
                    $day = $termin[day];
                    //echo ("Add Termin am $day <br>");
                    if (!is_array($dayList[$day])) $dayList[$day] = array();
                    $dayList[$day][] = $termin;
                    $show = 0;


                    $infoStr = $termin["info"];
                    $open = 0;
                    $start = strpos($infoStr,"$$");
                    if (is_integer($start)) {
                        // echo ("before : $infoStr -$start <br>");
                        while (is_integer($start)) {
                            if ($open) { $add = "</b>"; $open=0;}
                            else { $add = "<b>"; $open=1;}
                            $infoStr = substr($infoStr,0,$start).$add.substr($infoStr,$start+2);
                          
                            $start = strpos($infoStr,"$$");

                        }
                        // echo ("After : $infoStr $start <br>");
                        $termin[info] = $infoStr;                        
                    }

                    // foreach ($termin as $key => $value) {
                    //    if (strlen($value)<3) $show = 1; //echo ("$key = $value<br>");
                    // }


                    // Convert Kategorie
                    $categoryName = $termin[category];

                    if (!$date_kat[$categoryName]) {
                        echo ("Not Found $categoryName in date_kat <br>");
                        die();
                    } else {
                        $termin["category"] = $date_kat[$categoryName];
                        // echo ("found Category id = $date_kat[$categoryName] '$type'<br>");
                    }

                    // Convert City
                    $regionName = $termin[region];
                    // echo ("City = $regionName <br>");
                    if (!$date_region[$regionName]) {
                        echo ("Region $regionName not found <br>");
                        die();
                    } else {
                        $termin["region"] = $date_region[$regionName];
                        // echo ("found Region id = $date_region[$regionName] '$regionName'<br>");
                    }


                    // Convert Location
                    $locationStr = $termin[location];
                   
                    if ($locationStr) {
                        $offset = strpos($locationStr,"(");
                        if ($offset > 0) {
                            $locationName = substr($locationStr,0,$offset-1);

                            $long = strlen($locationStr) - $offset - 2;
                            $adress = substr($locationStr,$offset+1,$long);
                            //echo ("Location = '$locationStr' <br>");
                            //echo ("Locton '$locationName' (-$adress-)<br>");
                        } else {
                            $locationName = $locationStr;
                            $adress = "";
                            // echo ("Location = '$locationStr' <br>");
                            // echo ("kein Offset Location is '$locationName' <br>");
                        }
                        $locationData = cmsLocation_get(array("name"=>$locationName));
                        if (is_array($locationData)) {
                            $id = $locationData[id];
                            $name = $locationData[name];
                            $street = $locationData[street];
                            $streetNr = $locationData[streetNr];
                            //echo ("Location $locationName Found $id / $name <br>");
                            // if ($adress) echo ("  $adress <-> $street $streetNr <br>");
                            $termin[location] = $id;
                            //show_array($locationData);
                        } else {
                            // echo ("Location $locationName not found <br>");
                            $termin[location] = "";
                            $termin[locationStr] = $locationName;
                            $termin[data][street] = $adress;
                        }
                    } else {
                        //echo "no Location <br>";
                        // echo ("Location = '$locationStr' <br>");
                    }

                    $time = $termin["time"];
                    if (strlen($time)== 9) $time = substr($time,0,8);
                    if (strlen($time)== 8) {
                        $termin[time] = $time;
                    } else {
                        echo ("No Time is $time <br>");
                    }

                    

                    $day = $termin[date];
                    $y = subStr($day,6,4);
                    $m = substr($day,3,2);
                    $d = substr($day,0,2);
                    $date = $y."-".$m."-".$d;
                    $termin[date] = $date;
                    // echo ("Datum $date <br>");

                    $termin[show] = 1;
                    $termin[data]["print"]=1;
                    $res = cmsDates_save($termin);
                    if ($res != 1) {
                        echo ("Result is $res <br>");
                        die();
                    }

                    // $getDate = klappe_date_get($date,$termin[typeId],$termin[locationId],$termin[title]);
//                    if (is_array($getDate)) {
//
//
//                        $diff = 0;
//                        if ($termin[title] != $getDate["headline"]) $diff++;
//                        if ($termin[text] != $getDate["text"]) $diff++;
//
//                        if ($diff > 0 ) {
//                            foreach ($getDate as $key => $value) echo ("ALTE DATEN $key = $value <br>");
//                            foreach ($termin as $key => $value) echo ("NEUE DATEN $key = $value <br>");
//
//                            $query = "INSERT INTO `klappe_date_dates` SET ";
//                            $query.= "`date` = '$date'";
//                            $query.= ", `kat` = ".$termin[typeId];
//                            $query.= ", `city` = ".$termin[cityId];
//                            $query.= ", `location` = ".$termin[locationId];
//                            $query.= ", `headline` = '".$termin[title]."'";
//                            $query.= ", `text` = '".$termin[text]."'";
//
//                            $time = substr($termin[time],0,5);
//                            $query.= ", `time` = '$time' ";
//                            $result = mysql_query($query);
//                        if ($result) {
//                            echo ("Termin zur Datenbank hinzugefügt <br>");
//                        } else {
//                            echo ("Error in query $query <br>");
//                        }
//
//                            echo ("&nbsp;<br>");
//                        } else {
//                            echo "Daten gleic <br>";
//                        }
//
//
//
//
//
//
//
//                    } else {



//                        $query = "INSERT INTO `klappe_date_dates` SET ";
//                        $query.= "`date` = '$date'";
//                        $query.= ", `kat` = ".$termin[typeId];
//                        $query.= ", `city` = ".$termin[cityId];
//                        $query.= ", `location` = ".$termin[locationId];
//                        $query.= ", `headline` = '".$termin[title]."'";
//                        $query.= ", `text` = '".$termin[text]."'";
//
//                        $time = substr($termin[time],0,5);
//                        $query.= ", `time` = '$time' ";
//                        $result = mysql_query($query);
//                        if ($result) {
//                            echo ("Termin zur Datenbank hinzugefügt <br>");
//                        } else {
//                            echo ("Error in query $query <br>");
//                        }
//
//
//                    }







                    $show = 0;


                    if ($show) {
                        echo ("Add Termin am $day <br>");
                        foreach ($termin as $key => $value) {
                           if (strlen($value)<3) echo ("<strong>");
                           echo ("$key = $value");
                           if (strlen($value)<3) echo ("</strong><br>");
                           else echo ("<br>");
                        }
                    }
                }
            }
        }

        foreach ($date_kat as $kat => $id) {
        //    echo ("Kategorie = $kat => $id <br>");
        }

        foreach ($date_city as $city => $id) {
        //            echo ("City = $city => $id <br>");
        }


        $lastType = "";
        $lastCity = "";
        die();

        $output = "<v2.05><e2>\r\n";

        foreach($dayList as $day => $dayList) {

            $y = intval(subStr($day,6,4));
            $m = intval(substr($day,3,2));
            $d = intval(substr($day,0,2));

            $weekDay = date("D", mktime(0, 0, 0, $m, $d, $y));
            switch ($weekDay) {
                case "Mon" : $wochenTag = "Montag"; break;
                case "Tue" : $wochenTag = "Dienstag"; break;
                case "Wed" : $wochenTag = "Mittwoch"; break;
                case "Thu" : $wochenTag = "Donnerstag"; break;
                case "Fri" : $wochenTag = "Freitag"; break;
                case "Sat" : $wochenTag = "Samstag"; break;
                case "Sun" : $wochenTag = "Sonntag"; break;
            }

            // $datum = new DateTime($m."/".$d."/".$y);
            // echo ("Datum = ".date("D", mktime(0, 0, 0, $m, $d, $y))."<br>");
            $d = intval(substr($day,0,2));
            echo ("TAG = ".$d.".".$m.".".$y." <br>");
            $outAdd = "@Über.TAG:".$wochenTag."\r\n"."@Über.DATUM:".$d.".\r\n"; // Spaltenumbruch war <\c> nach $d
            $output .= addToOutput($outAdd);
            // $output .= iconv("ISO8859-15","macintosh",$outAdd);

            // echo ("$outAdd<br>");

            if ($day != "41.03.2012") {
                for ($i=0;$i<count($dayList);$i++) {
                    $termin = $dayList[$i];


                    $type = $termin[type];
                    if ($type != $lastType) {
                        //echo ("<h1>new Type $type </h1>");
                        $outAdd = "@Term.RUBRIK:".$type."\r\n";
                        //echo ("$outAdd<br>");
                        $output .= addToOutput($outAdd);
                        // $output .= iconv("ISO8859-15","macintosh",$outAdd);
                        $lastType = $type;
                    }



                    $city = $termin[city];
                    if ($city != $lastCity) {
                        //echo ("<h2>new City $city </h2>");
                        $outAdd = "@Term.ORT:".$city."\r\n";
                        //echo ("$outAdd<br>");
                        $output .= addToOutput($outAdd);
                        // $output .= iconv("ISO8859-15","macintosh",$outAdd);
                        $lastCity = $city;
                    }






                    foreach ($termin as $code => $inh) {

                        while ($inh[strlen($inh)-1] == " ") {
                            // echo ("Leerzeichen am ende - $code ='$inh' (".$inh[strlen($inh)-1].")<br>");
                            $inh = substr($inh,0,strlen($inh)-1);
                            // echo ("--> '$inh' <br>");
                        }


                        switch ($code) {
                            case "day" : break;
                            case "type" : break;
                            case "city" : break;
                            case "title" :
                                $outAdd = "@Term.Brot:<@TerminBrotFETT>".$inh."<@\$p>";
                                // echo("$outAdd<br>");
                                $output .= addToOutput($outAdd);
                                // $output .= iconv("ISO8859-15","macintosh",$outAdd);
                                break;
                            case "text" :
                                if (strlen($inh)>1) {
                                    $outAdd = " · ".$inh;
                                    //echo("$outAdd<br>");
                                    $output .= addToOutput($outAdd);
                                    //$output .= iconv("ISO8859-15","macintosh",$outAdd);
                                } else {
                                    // echo ("Dont ADD - $code = '$inh'<br>");
                                }
                                break;
                            case "where" :
                                if (strlen($inh)>1) {
                                    $outAdd = " · ".$inh;
                                    // echo("$outAdd<br>");
                                    $output .= addToOutput($outAdd);
                                } else {
                                    // echo ("Dont ADD - $code = '$inh'<br>");
                                }
                                // $output .= iconv("ISO8859-15","macintosh",$outAdd);
                                break;
                            case "time" :

                                $inh = substr($inh,0,5);
                                $outAdd = " · ".$inh."\r\n";
                                // echo("$outAdd<br>");
                                $output .= addToOutput($outAdd);
                                //$output .= iconv("ISO8859-15","macintosh",$outAdd);
                                break;


                            default :
                                echo ("$code = $inh <br>");
                        }

                    }
                    // echo ("&nbsp;<br>");


                }


            }





        }


       // echo ("Output  = ".substr($output,0,1000)."<br>");
       // $output = "Über Brücken";
       // echo ("Output als ISO = $output <br>");
        //$output = utf8_encode($output);
        // $output = iconv("ISO8859-15","macintosh",$output);
       // echo ("Output UTF8= $output <br>");



        //$outFile = 'termin-Xpress-iso.txt';
        //saveText($output, $outFile);

        // echo ("<a href='$outFile'>Hier als ISO</a>");

        // $output = iconv("ISO8859-15","macintosh",$output);
       // echo ("Output UTF8= $output <br>");
        //$outFile = 'termin-Xpress-mac.txt';
        //saveText($output, $outFile);

        // echo ("<a href='$outFile'>Hier als MAC</a>");





        // put your code here
        ?>
    </body>
</html>
