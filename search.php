<?php
    header('Content-Type: text/html; charset=iso-8859-1');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');
    $cmsName = "klappeAuf";
    $cmsVersion ="base";
    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

    $query.= "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 AND `mainCat` = 8 ";
    $result = mysql_query($query);
    $res = '';
    WHILE ($category = mysql_fetch_assoc($result)) {
        if ($res == '') $res .= '["';
        else $res.= '","';
        $res.= $category[name];
    }
    $res.='"]';
    echo ($res);
?>
