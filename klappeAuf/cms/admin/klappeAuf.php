

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


function addToOutput($str,$mode) {
    if (strlen($str)<2) return "";

    $offSet = strpos($str, "$$");
    switch($mode) {
        case "html" : break;
        case "quark" : break;
        default :
            $mode = "quark";
    }

  
    if ($offSet > 0) {
        //echo ("Str has Zeichen $$ '$str' <br>");


        switch($mode) {
            case "html" :
                $newStr = substr($str,0,$offSet)."<b><i>".substr($str,$offSet+2);
                break;
            case "quark" :
                $newStr = substr($str,0,$offSet)."<@TerminBrotFETT>".substr($str,$offSet+2);
                break;
        }
        //$newStr = substr($str,0,$offSet)."<@TerminBrotFETT>".substr($str,$offSet+2);
        //echo ("NEW STRING is $newStr<br>");
        $offSet2 = strpos($newStr, "$$");
        if ($offSet2 > 0) {

            switch($mode) {
                case "html" :
                    $str = substr($newStr,0,$offSet2)."</i></b>".substr($newStr,$offSet2+2);
                    break;
                case "quark" :
                    $str = substr($newStr,0,$offSet2)."<@\$p>".substr($newStr,$offSet2+2);
                    break;
            }

            //$str = substr($newStr,0,$offSet2)."<@\$p>".substr($newStr,$offSet2+2);
        }
        $str = addToOutput($str,$mode);
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

        include("includes/help.php");


        $host     = "db153.puretec.de";
        $user     = "dbo280303012";
        $database = "db280303012";
        $password = "u3aWcFFA";

        @$link = mysql_connect($host, $user, $password);
        @mysql_select_db($database, $link);


        // get Page Kat
        $date_kat = array();
        $date_typeList = array();
        $date_kat_id = 0;
        $query = "SELECT * FROM `klappe_date_kat` ";
        $result = mysql_query($query);
        if ($result) {
            while ($kat = mysql_fetch_assoc($result)) {
                $id = $kat[id];
                $name = $kat[name];
                $date_kat[$name] = $id;
                $date_typeList["".$id] = $name;


                if ($id >= $date_kat_id) $date_kat_id = $id;
            }
        } else {
            echo ("Error in Query '$query' <br>");
        }

        // get Page Kat
        $date_city = array();
        $date_city_id = 0;
        $query = "SELECT * FROM `klappe_date_city` ";
        $result = mysql_query($query);
        if ($result) {
            while ($kat = mysql_fetch_assoc($result)) {
                $id = $kat[id];
                $name = $kat[name];
                $date_city[$name] = $id;
                $date_cityList["".$id] = $name;
                if ($id >= $date_kat_id) $date_kat_id = $id;
            }
        } else {
            echo ("Error in Query '$query' <br>");
        }

        // get Page Kat
        $date_location = array();
        $date_locationList = array();
        $date_location_id = 0;
        $query = "SELECT * FROM `klappe_date_location` ";
        $result = mysql_query($query);
        if ($result) {
            while ($kat = mysql_fetch_assoc($result)) {
                $id = $kat[id];
                $name = $kat[name];
                $adress = $kat[adress];
                $loc = strtolower($name);
                $date_locationList["".$id]= array("id"=>$id,"name"=>$name,"adress"=>$adress);
                $date_location[$loc] = array("id"=>$id,"name"=>$name,"adress"=>$adress);
                // echo ("Get from database $loc $name ,$adress <br>");
                if ($id >= $date_location_id) $date_location_id = $id;
            }
        } else {
            echo ("Error in Query '$query' <br>");
        }

        $query = "SELECT * FROM `klappe_date_dates` ";
        $filterLocation = $_GET[location];

        if ($filterLocation) {
            $query .= " WHERE `location`= $filterLocation ";
        }

        $date = "2012-03-19";
        // $query = "SELECT * FROM `klappe_date_dates` WHERE `location`= 10 "; //WHERE `date`= '$date' ";
        $result = mysql_query($query);
        $data = array();
        if ($result) {
            $day = array();
           /* $day[$date] = array();
            foreach ($date_typeList as $key => $value) {
                $day[$date][$value] = array();
                foreach ($date_cityList as $cityId => $cityName) {
                    $day[$date][$value][$cityName] = array();
                }
            }*/



            while ($termin = mysql_fetch_assoc($result)) {
                $date = $termin[date];
                if (!is_array($day[$date])) {
                    $day[$date] = array();
                    // echo ("CREATE DATE ARRAY for $date");
                    foreach ($date_typeList as $key => $value) {
                        $day[$date][$value] = array();
                        foreach ($date_cityList as $cityId => $cityName) {
                            $day[$date][$value][$cityName] = array();
                        }
                    }
                }

                $id = $termin[id];
                $typeId = $termin[kat];
                $type = $date_typeList[$typeId];

                $cityId = $termin[city];
                $city = $date_cityList[$cityId];

                $title = addToOutput($termin[headline],"html");

                $location = $termin[location];
                $locationName   = $date_locationList[$location][name];
                $locationAdress = $date_locationList[$location][adress];

                $termin["locStr"] = "<a href='klappeAuf.php?location=$location'>".$locationName."</a>";
                if ($locationAdress)  $termin["locStr"].= " (".$locationAdress.")";

                $day[$date][$type][$city][] = $termin;
            }
        }

        foreach ($day as $datum => $value ) {
            echo ("<h1>Termine für $datum </h2>");

            $typeStr = "";
            foreach ($value as $type => $data) {
                //echo ("<h2> $type </h2>");
                $cityStr = "";
                foreach ($data as $cityName => $terminList) {
                    
                    // echo ("Stadt = $cityName <br>");
                    if (count($terminList)>0) {
                        $cityStr .= "$cityName<br>";
                        for ($i =0;$i<count($terminList);$i++) {
                            $termin = $terminList[$i];
                            $title = addToOutput($terminList[$i][headline],"html");
                            $text  = addToOutput($terminList[$i][text],"html");
                            $locStr = $terminList[$i][locStr];

                            $time = $terminList[$i][time];


                            $str = " &nbsp;<strong>$title</strong>";
                            if ($text) $str .= " - ".$text;
                            if ($locStr) $str .= " - ".$locStr;
                            if ($time) $str.= " - ".$time;


                            $cityStr .= $str."<br>";
                        }
                    } else {
                       // echo "No Termin for city $cityName<br>";
                    }
                }

                if (strlen($cityStr)>0) {
                    $typeStr .= "<h2>$type</h2>".$cityStr."<br>";
                }
                
            }

            if (strlen($typeStr)>0) {
                echo ($typeStr);
            }

        }



        die();


        $lastType = "";
        $lastCity = "";


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
        $outFile = 'termin-Xpress-iso.txt';
        saveText($output, $outFile);

        echo ("<a href='$outFile'>Hier als ISO</a>");

        $output = iconv("ISO8859-15","macintosh",$output);
       // echo ("Output UTF8= $output <br>");
        $outFile = 'termin-Xpress-mac.txt';
        saveText($output, $outFile);

        echo ("<a href='$outFile'>Hier als MAC</a>");





        // put your code here
        ?>
    </body>
</html>
