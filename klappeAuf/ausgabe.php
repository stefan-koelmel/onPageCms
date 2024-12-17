<?php // charset:UTF-8
    session_start();
    header("Content-Type: text/html; charset=utf-8");
    include("incs/klappeAufData.php");
    timer_start();
    global $cmsName,$cmsVersion;
    
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");
    timer_add("cmsLoaded");
    
    
    global $pageInfo;
    $pageInfo = cms_page_getInfo();
    timer_add("pageInfo");
    global $pageData;
    $pageName = $pageInfo[pageName];
    $pageData = cms_page_getData($pageName);
    timer_add("pageData");

    include("incs/header-document.php");
    timer_add("header");
?> 

<meta name="description" content="" />
<meta name="keywords" content="" />

<style type="text/css">.zoomimage { border:none; cursor: -webkit-zoom-in; cursor: -moz-zoom-in; }</style>
<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-style.css" media="screen" />


<title>Klappe auf - Das Kulturmagazin der Region Karlsruhe</title>

<style type="text/css">
</style>
</head>

<body>

<div class="page_container">



<!-- ######################################################### HEADER ###################################### -->
      <div class="header">
        <div class="maincontent">
            <div class="header_logo"><a href="index.php"><img src="img/logo.jpg" alt="KlappeAuf Karlsruhe" class="noborder" /></a></div>

<!-- ########################  MainMenu  ################ -->
            <div class="main_menu" id="mainmenucontainer">
                  <div id="mainmenu">
<? include("incs/header-mainmenu.php"); ?>
                  </div>
            </div>

        <div class="clearboth"></div>
        </div>
      </div>
<!-- ######################################################### HEADER-ENDE ###################################### -->




      <div class="maincontent maincontainer">

<!-- ######################################################### SIDEBAR ###################################### -->
        <div class="sidebar">
          <? 
            include("incs/sidebar.php"); 
            sidebar_show()
          ?>
        </div>
<!-- ######################################################### SIDEBAR ENDE ###################################### -->


        <div class="content_right">

<!-- ######################################################### MAIN-CONTENT ###################################### -->
        <div class="content_box">
          <div class="content_box_inner">

            

            <!-- ######################################################### Kategorien von Adressen ###################################### -->
 <?php
    $selectedMainCatId = $_GET[artCat];
   
    if (!intval($selectedMainCatId)){
        $catList = categoryGetList("RubrikList");
        foreach ($catList as $catId => $value) {
            $shortName = $value[shortName];
            if ($shortName == $selectedMainCatId) {
                $selectedMainCatId = $catId;
            }       
        }
    }
    
    $selectedSubCatId = $_GET[subCat];
    if (!intval($selectedSubCatId)){
        $subCatList = categoryGetList("RubrikSubList_".$selectedMainCatId);
        foreach ($subCatList as $catId => $value) {
            $shortName = $value[shortName];
            if ($shortName == $selectedSubCatId) {
                $selectedSubCatId = $catId;
            }       
        }
    }
    
    
    $selectedArticleId = $_GET[articleId];
    include("incs/articles.php");
    $link = "ausgabe.php";

    if ($selectedArticleId>0) {
        // ######################################################### ZEIGE DETAIL VON ADRESSE ###################################### -->
        articles_articleDetail($selectedArticleId,$link);

        // ######################################################### ZEIGE Termine VON ADRESSE ###################################### -->
        // adressen_showDates($selectedLocationId,$link);

        // ######################################################### ZEIGE Termine VON ADRESSE ###################################### -->
        // adressen_showrticles($selectedLocationId,$link);

        // ######################################################### ZEIGE DETAIL VON ADRESSE - ENDE ###################################### -->
    } else {
        // ######################################################### Filter für Artikel ###################################### -->
        articles_showFilter($selectedMainCatId,$selectedSubCatId,$link);
        
        // ######################################################### Adressen-LISTE -> gefiltert ###################################### -->
        articles_articleList($selectedMainCatId,$selectedSubCatId,$link);
    }
?>


<!-- ######################################################### MAIN-CONTENT-ENDE ###################################### -->
        </div>
       <div class="clearboth">&nbsp;</div>
      </div>


<div class="push">&nbsp;</div>
</div>


<!-- ######################################################### FOOTER ###################################### -->
<div class="footer">
    <? include("incs/footer-content.php"); ?>
</div>
<!-- ######################################################### FOOTER-ENDE ###################################### -->


    		<!-- JS-FILES -->
		
    <script type="text/javascript" src="js/script.js"></script>

    <script type="text/javascript" src="js/script-select-content.js"></script>
    <script type='text/javascript' src='js/script-calendar.js'></script>

    <?php
        if ($selectedArticleId) {
            echo("<script type='text/javascript' src='js/fancybox/jquery.fancybox-script.js'></script>\n");
            echo("<script type='text/javascript' src='js/fancybox/js-fancybox-config.js'></script>\n");
      }
    ?>
    
    <?php
        if ($_GET[out]) {
            timer_add("ready");
            timer_show();
            
            echo ("<h1>Timer</h1>");
            timer_show();
            echo ("<h1>MYSWL-STATUS</h1>");
            mysql_status();                        
        }        

    ?>


<!--[if lte IE 7]>
    <script type="text/javascript" src="ie7/IE9.js">IE7_PNG_SUFFIX=".png";</script>
    <script type="text/javascript" src="ie7/ie7-squish.js" type="text/javascript"></script>
<![endif]-->

</body>
</html>
           
