<?php
    session_start();
    global $cmsName,$cmsVersion;
    if (!file_exists("cmsSettings.php")) {
        $sesName = $_SESSION[cmsName];
        $sesVers = $_SESSION[cmsVersion];
        
        if ($sesName) {
            $setFile = $_SERVER[DOCUMENT_ROOT]."/".$sesName."/cmsSettings.php";
            if (file_exists($setFile)) {
                include ($setFile);                
            }                
        }
    }
    
    
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");
    global $pageInfo;
    $pageInfo = cms_page_getInfo();
    
   /* $pageName = $pageInfo[pageName];
    $pageData = cms_page_getData($pageName);*/

    // show Header
    cms_header_show($pageData);

    

?>
    <body>
        <?php
            // show_array($_SERVER);
            //$adminPage = cms_admin_pages($pageInfo,$pageData);
            $showCreate = 1;

            if ($adminPage) $showCreate = 0;

            if ($showCreate) {
                
                $file = "404.php";
                if (file_exists($file)) {
                    echo(" in Root exist <br>");
                    reloadPage($file,0);
                }
                
                
                $file = $_SERVER[DOCUMENT_ROOT]."/".$cmsName."/404.php";
                echo ("404 $file <br>");
                if (file_exists($file)) {
                    echo ("Relaod 404 in $cmsName <br>");
                    // reloadPage($file,0);
                }
                
                echo ("<h1> Create Page</h1>");
                
                
                
                
                
                $pageInfo = $GLOBALS[pageInfo];
                show_array($pageInfo);
                $newName = $pageInfo[requestPageName];
                $newPara = $pageInfo[requestPageParameter];
                
                if ($newPara == "create=1") {
                    echo ("<h1>CREATE '$newName' </h1>");
                    echo ("cmsName = $GLOBALS[cmsName] <br>");

                    $newData = array("layout"=>0,"navigation"=>1,"breadcrumb"=>1);
                    $res = cms_page_create($newName,$newData);

                    if ($res == 1) {
                        $goPage = $newName.".php";
                        echo "Redirect to <a href='$goPage'>$goPage</a><br>";
                        // reloadPage($goPage,1);
                    } else {
                        echo ("Error by create $newName Page -> $res <br>");
                    }
                } else {

                    echo ("SEITE <strong>'$newName'</strong> existiert nicht <br>");

                    echo ("Wollen Sie diese Seite anlegen? ");
                    echo ("<a href='$newName.php?create=1'> JA </a> / ");
                    echo ("<a href='index.php'>NEIN</a><br>");

                    if (is_array($pageData)) {
                        foreach ($pageData as $key => $value) {
                        //    echo ("PageData $key => $value <br>");
                        }
                    }

                    foreach ($pageInfo as $key => $value) {
                        // echo ("pageInfo $key => $value <br>");
                    }

                // foreach ($_SERVER as $key => $value) echo ("SERVER $key => $value <br>");
                    $goPage = "index.php";
                    echo ("<a href='$goPage'>zur Startseite</a>");
                    // reloadPage($goPage,10);
                }
            }
        
        ?>
    </body>
</html>
