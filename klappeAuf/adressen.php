<?php // charset:UTF-8
    session_start();
    header("Content-Type: text/html; charset=utf-8");
    global $cmsName,$cmsVersion;
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    include($_SERVER["DOCUMENT_ROOT"]."/cms_".$cmsVersion."/cms.php");

    include("incs/klappeAufData.php");
    global $pageInfo;
    $pageInfo = cms_page_getInfo();

    global $pageData;
    $pageName = $pageInfo[pageName];
    $pageData = cms_page_getData($pageName);

    // show Header
    // cms_header_show($pageData,$pageInfo);
    include("incs/header-document.php");
?>

<link type="text/css" rel="stylesheet" href="css/coda-slider.css" />
<link type="text/css" rel="stylesheet" href="css/jquery.shadow.css" />
<link type="text/css" rel="stylesheet" href="css/calendar.css" />


<meta name="description" content="" />
<meta name="keywords" content="" />
<style type="text/css">.zoomimage { border:none; cursor: -webkit-zoom-in; cursor: -moz-zoom-in; }</style>
<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-style.css" media="screen" />

<title>Klappe auf - Das Kulturmagazin der Region Karlsruhe</title>
</head>

<body>

<div class="page_container">



<!-- ######################################################### HEADER ###################################### -->
      <div class="header">
        <div class="maincontent">
            <div class="header_logo"><a href="index.php"><img src="img/logo.jpg" alt="KlappeAuf Karlsruhe" class="noborder" /></a></div>

            <div class="main_menu" id="mainmenu">
              <? include("incs/header-mainmenu.php"); ?>
            </div>

        <div class="clearboth"></div>
        </div>
      </div>
<!-- ######################################################### HEADER-ENDE ###################################### -->




      <div class="maincontent maincontainer"  >

<!-- ######################################################### SIDEBAR ###################################### -->
        <div class="sidebar">
          <? 
          include("incs/sidebar.php"); 
          sidebar_show();
          ?>
        </div>
<!-- ######################################################### SIDEBAR ENDE ###################################### -->


        <div class="content_right" >

<!-- ######################################################### Kategorien von Adressen ###################################### -->
 <?php
    $selectedCatId = $_GET[locCat];
    if (!intval($selectedCatId)){
        $catList = categoryGetList("AdressList");
        foreach ($catList as $catId => $value) {
            $shortName = $value[shortName];
            if ($shortName == $selectedCatId) {
                $selectedCatId = $catId;
            }       
        }
    }
    
    
    
    $selectedRegionId = $_GET[region];
    if (!intval($selectedRegionId)){
        $regionList = categoryGetList("RegionList");
        foreach ($regionList as $catId => $value) {
            if ($value[shortName] == $selectedRegionId) $selectedRegionId = $catId;                   
        }
    }
    // echo ("Kategorie = $selectedCatId Region = $selectedRegionId <br>");
    
    $selectedLocationId = $_GET[location];
    include("incs/adressen.php");
    $link = "adressen.php";

    if ($selectedLocationId>0) {
        // ######################################################### ZEIGE DETAIL VON ADRESSE ###################################### -->
        adressen_adressDetail($selectedLocationId,$link);

//        // ######################################################### ZEIGE Termine VON ADRESSE ###################################### -->
//        adressen_showDates($selectedLocationId,$link);
//
//        // ######################################################### ZEIGE Termine VON ADRESSE ###################################### -->
//        adressen_showArticles($selectedLocationId,$link);

        // ######################################################### ZEIGE DETAIL VON ADRESSE - ENDE ###################################### -->
        
        
         echo ("</div>");
        echo ("</div>");
        
        
    } else {
        echo("<div class='content_box'>");
        echo("<div class='content_box_inner'>");
        adressen_showFilterLine($selectedCatId,$selectedRegionId,$link);

        
        // ######################################################### Kategorien von Adressen ###################################### -->
        adressen_showCategory($selectedCatId,$link);

        // ######################################################### Regionen von Adressen ###################################### -->
        adressen_showRegion($selectedRegionId,$link);

        echo ("<div class='slidespacer'>&nbsp;</div>");

        // ######################################################### Adressen-LISTE -> gefiltert ###################################### -->
        adressen_adressList($selectedCatId, $selectedRegionId,$link);
        echo ("</div>");
        echo ("</div>");
        echo ("</div>");
    }
?>
    <div class='clearboth'> </div>
    </div>

    
    <div class='push'> </div>
    </div>


<!-- ######################################################### FOOTER ###################################### -->
<div class='footer'>
    <? include('incs/footer-content.php'); ?>
</div>
<!-- ######################################################### FOOTER-ENDE ###################################### -->

    <!-- JS -->
    


     <?php
        if ($selectedLocationId>0) {
            echo ("<script type='text/javascript' src='js/fancybox/jquery.fancybox-script.js'></script>");
            echo ("<script type='text/javascript' src='js/fancybox/js-fancybox-config.js'></script>");
        } else {
            
            echo("<script type='text/javascript' src='js/script-select-content.js'></script>");
        }
        echo ("<script type='text/javascript' src='js/script.js'></script>");

    ?>
    <script type='text/javascript' src='js/script-calendar.js'></script>
    
    
    
</body>
</html>