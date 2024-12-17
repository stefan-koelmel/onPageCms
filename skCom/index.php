<?php // charset:UTF-8
    session_start();
    header("Content-Type: text/html; charset=utf-8");
    global $cmsName,$cmsVersion;
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");

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
            
//            $query = "SELECT * FROM `skCom_cms_pages` ";
//            $result = mysql_query($query);
//            if (!$result) echo ("ERRO in QUERY '$query' <br>");
//            else {
//                
//                while ($page = mysql_fetch_assoc($result)) {
//                    $link = $page[name].".php";
//                    echo ("<a href='$link'>.$page[id] $page[title] </a><br>");
//                }
//            }
        ?>
    </body>
</html>