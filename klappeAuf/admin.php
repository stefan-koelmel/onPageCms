<?php    // charset:UTF-8
    header("Content-Type: text/html; charset=utf-8");
    session_start();
    global $cmsName,$cmsVersion;
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");

    include("incs/klappeAufData.php");
    global $pageInfo;
    $pageInfo = cms_page_getInfo();

    global $pageData;
    $pageName = $pageInfo[pageName];
    $pageData = cms_page_getData($pageName);

    // show Header
    cms_header_show($pageData,$pageInfo);
?>
    <body>
        <?php
            cms_page_show();
        ?>
    </body>
</html>