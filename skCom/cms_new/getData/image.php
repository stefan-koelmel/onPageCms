<?php // charset:UTF-8
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];

    $out = $_GET[out];

    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php";
    echo ("cmsFile = $cmsFile <br>");
    include($cmsFile);

    
?>
