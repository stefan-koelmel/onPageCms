<?php // charset:UTF-8    session_start();    header("Content-Type: text/html; charset=utf-8");    global $cmsName,$cmsVersion;    if (file_exists("cmsSettings.php")) include("cmsSettings.php");    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");        include("incs/klappeAufData.php");    global $pageInfo;    $pageInfo = cms_page_getInfo();        global $pageData;    $pageName = $pageInfo[pageName];    $pageData = cms_page_getData($pageName);    // show Header    // cms_header_show($pageData,$pageInfo);    include("incs/header-document.php");?><link type="text/css" rel="stylesheet" href="css/coda-slider.css" /><link type="text/css" rel="stylesheet" href="css/jquery.shadow.css" /><link type="text/css" rel="stylesheet" href="css/calendar.css" /><meta name="description" content="" /><meta name="keywords" content="" /><style type="text/css">.zoomimage { border:none; cursor: -webkit-zoom-in; cursor: -moz-zoom-in; }</style><link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-style.css" media="screen" /><title>Klappe auf - Das Kulturmagazin der Region Karlsruhe</title></head><body><div class="page_container"><!-- ######################################################### HEADER ###################################### -->      <div class="header">        <div class="maincontent">            <div class="header_logo"><a href="index.php"><img src="img/logo.jpg" alt="KlappeAuf Karlsruhe" class="noborder" /></a></div>            <div class="main_menu" id="mainmenu">              <? include("incs/header-mainmenu.php"); ?>            </div>        <div class="clearboth"></div>        </div>      </div><!-- ######################################################### HEADER-ENDE ###################################### -->      <div class="maincontent maincontainer"  ><!-- ######################################################### SIDEBAR ###################################### -->        <div class="sidebar">          <?           include("incs/sidebar.php");           sidebar_show();          ?>        </div><!-- ######################################################### SIDEBAR ENDE ###################################### -->    <div class="content_right" >           <!-- ######################################################### Kategorien von Adressen ###################################### --> <?php    $selectetDateRange = $_GET[dateRange];       include("incs/archive.php");    $link = "archive.php";    if ($selectetDateRange) {        $selectedMainCatId = $_GET[artCat];        $selectedSubCatId = $_GET[subCat];                if (!intval($selectedMainCatId)){            $catList = categoryGetList("RubrikList");            foreach ($catList as $catId => $value) {                $shortName = $value[shortName];                if ($shortName == $selectedMainCatId) {                    $selectedMainCatId = $catId;                }                   }        }        $selectedSubCatId = $_GET[subCat];        if (!intval($selectedSubCatId)){            $subCatList = categoryGetList("RubrikSubList_".$selectedMainCatId);            foreach ($subCatList as $catId => $value) {                $shortName = $value[shortName];                if ($shortName == $selectedSubCatId) {                    $selectedSubCatId = $catId;                }                   }        }                        $selectedArticleId = $_GET[articleId];        if ($selectedArticleId > 0) {            // ######################################################### ARCHIVE TITLEBOX ###################################### -->            // archive_showTitleBox($selectetDateRange,$link);                                    // ######################################################### ZEIGE DETAIL VON ADRESSE ###################################### -->            archive_articleDetail($selectetDateRange,$selectedMainCatId,$selectedSubCatId,$selectedArticleId,$link);        } else {            // ######################################################### ARCHIVE TITLEBOX ###################################### -->            archive_showTitleBox($selectetDateRange,$link);                        // ######################################################### ZEIGE Filter ###################################### -->            archive_showFilter($selectetDateRange,$selectedMainCatId,$selectedSubCatId,$link);//            // ######################################################### Kategorien von ARTICLE ###################################### -->//            archive_showMainCategory($selectedMainCatId,$link);////            // ######################################################### Unter-Kategorie von ARTICLE ###################################### -->//            archive_showSubCategory($selectedMainCatId,$selectedSubCatId,$link);            // ######################################################### ZEIGE LISTE mit Artikeln ###################################### -->            archive_articleList($selectetDateRange,$selectedMainCatId,$selectedSubCatId,$link);            // ######################################################### ZEIGE Termine VON ADRESSE ###################################### -->            // adressen_showrticles($selectedLocationId,$link);            // ######################################################### ZEIGE DETAIL VON ADRESSE - ENDE ###################################### -->        }    } else {        // ######################################################### Zeige ALLE Cover ###################################### -->        archive_showDateRanges($link);           }?>    <div class="clearboth"> </div>    </div>    <div class="clearboth"> </div>    <div class="push">&nbsp;</div>    </div><!-- ######################################################### FOOTER ###################################### --><div class="footer">    <? include("incs/footer-content.php"); ?></div><!-- ######################################################### FOOTER-ENDE ###################################### -->    		<!-- JS -->		    <script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>       <script type="text/javascript" src="js/script-select-content.js"></script>    <script type='text/javascript' src='js/script-calendar.js'></script>    <script type="text/javascript" src="js/script.js"></script>    <?php         if ($selectedArticleId) {            echo("<script type='text/javascript' src='js/fancybox/jquery.fancybox-script.js'></script>\n");            echo("<script type='text/javascript' src='js/fancybox/js-fancybox-config.js'></script>\n");        }    ?>    </body></html>