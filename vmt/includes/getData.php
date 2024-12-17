<?php

$dbName = "db360967548";
$debug = $_GET[debug];
switch ($_GET[mode]) {
    case "info" :
        getInfo($_GET,$debug);
        break;
    case "getData" :
        getData($_GET,$debug);
        break;
    case "setData" :
        echo ("setData");
        break;

    default :
        echo "unkown";

}






function getInfo($data,$debug) {
    switch ($data[show]) {
        case "tables" :
            $res = getInfo_Tables($data,$debug);
            break;
        case "tableInfo" :
            $res = getInfo_TableInfo($data,$debug);
            break;

        default :
            echo ("Show Info :<br>");
            echo (" - show=tables -> Show Tables [startName] [contain] <br>");

    }
    if (is_array($res) ) {
        // echo ("Ergebnis ist Array!<br>");
        $res = array2Str($res);
        echo ($res);
        return $res;
    }
}

 function str2Array($str) {
    $ar = unserialize($str);
    return $ar;
}


function array2Str($ar) {
    $str = serialize($ar);
    return $str;
}

function getInfo_Tables($data,$debug) {
    include ("connect.php");
    global $dbName;
    if ($debug) echo ("DB Name = $dbName $link<br>");
    $query = "SHOW TABLES FROM `$dbName` ";// LIKE 'klappe%' ";
    if ($data[startName]) $query .= " LIKE '".$data[startName]."%' ";
    if ($data[contain]) $query .= " LIKE '%".$data[contain]."%' ";
    $result = mysql_query($query,$link);
   
    if (!$result) {
        if ($debug) echo "DB Fehler, konnte Tabellen nicht auflisten\n";
        if ($debug) echo 'MySQL Fehler: ' . mysql_error();
        if ($debug) echo ($query."<br>");
        return "error";
        exit;
    }

    $res = array();
    while ($row = mysql_fetch_row($result)) {
        $table = $row[0];
        $res[] = $table;
        if ($debug) echo "Tabelle: {$row[0]}<br>";

    }
    return $res;

}

function getInfo_TableInfo($data,$debug) {
    include ("connect.php");
    global $dbName;
    $tableName = $data[tableName];
    if ($debug) echo ("DB Name = $dbName $link<br>");
    if ($debug) echo ("TableName = $tableName<br>");


    $queryRow = "select * from `".$tableName."`";
    $resultRow = mysql_query($queryRow);
    if (!$resultRow) {
        die('Anfrage fehlgeschlagen: $queryRow ' . mysql_error());
    }
            
    $i = 0;
    $res = array();
    while ($i < mysql_num_fields($resultRow)) {
        // echo "Information für Feld $i:<br />\n";
        $meta = mysql_fetch_field($resultRow, $i);
        if (!$meta) {
            echo "Keine Information vorhanden<br />\n";
        } else {
            $name = $meta->name;
            $field = array();

            $field[blob]=$meta->blob;
            $field[max_length]=$meta->max_length;
            $field[multiple_key]=$meta->multiple_key;
            $field[name]=$meta->name;
            $field[not_null]=$meta->not_null;
            $field[numeric]=$meta->numeric;
            $field[primary_key]=$meta->primary_key;
            $field[table]=$meta->table;
            $field[type]=$meta->type;
            $field[unique_key]=$meta->unique_key;
            $field[unsigned]=$meta->unsigned;
            $field[zerofill]=$meta->zerofill;
            $res[$name] = $field;                    
        }
        $i++;
    }
    return $res;
}



function getData($data,$debug) {
    switch ($data[show]) {
        case "tableData" :
            $res = getData_Tables($data,$debug);
            break;
        case "tableInfo" :
            $res = getInfo_TableInfo($data,$debug);
            break;

        default :
            echo ("Show Info :<br>");
            echo (" - show=tables -> Show Tables [startName] [contain] <br>");

    }
    if (is_array($res) ) {
        // echo ("Ergebnis ist Array!<br>");
        $res = array2Str($res);
        echo ($res);
        return $res;
    }
}

function getData_Tables($data,$debug) {
    if ($debug) echo ("GETDATA_TABLES<br>");
    $tableName = $data[tableName];
    if ($debug) echo ("TableName $tableName<br>");

    include ("connect.php");
    if ($debug) echo ("DB Name = $dbName $link<br>");
    if ($debug) echo ("TableName = $tableName<br>");


    if ($debug) {
        foreach($data as $key => $value) echo("arguments $key = $value<br>");
    }

    if ($data[filter]) {
        if ($debug) echo ("Filter = $data[filter] <br>");
        $filterList = explode("|",$data[filter]);
        $filterQuery = "";
        for ($i=0;$i<count($filterList);$i++) {
            $filterData = explode("*",$filterList[$i]);
            if (count($filterData)==2) {
                if ($filterQuery == "") $filterQuery = "WHERE ";
                else $filterQuery .= " AND ";
                $filterQuery .= " `$filterData[0]` = '$filterData[1]' ";

            }
        }
        if ($debug) echo ("Filter  $filterQuery <br>");
    }

    $query = "SELECT * FROM `$tableName` ".$filterQuery;
    $result = mysql_query($query);

    $res = array();
    $i = 0;
    while ($line = mysql_fetch_assoc($result)) {
        $res[] = $line;
        $i++;
        if ($debug) echo ($i."<br>");

    }
    return $res;


 


}

        
        // put your code here
        ?>
  