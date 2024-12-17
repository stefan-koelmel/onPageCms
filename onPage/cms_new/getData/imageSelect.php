<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

    global $cmsName,$cmsVersion;
    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];

    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");
    $name = $_GET[name];
    $frame = $_GET[frame];
    $type = $_GET[type];
    $frameWidth = $_GET[frameWidth];
    $getData = $_GET;

    global $cmsName;
    $cmsName = $_GET[cmsName];
    //echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_image.js'></script>");

    //echo ("<h1>cms_imageSelect_get.php $getData[folder]</h1>");
    //echo ("cmsName = $cmsName cmsVersion=$cmsVersion<br>");
    $folder = $getData[folder];
    $res = cmsImage_SelectList_getContent($folder);

    echo ($res);
    echo ("<script>");
    echo ("liveImageDrag();");    
    echo ("</script>");

?>
