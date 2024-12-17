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

<meta name="description" content="" />
<meta name="keywords" content="" />

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
          <? include("incs/sidebar.php"); 
            sidebar_show();
          ?>

        </div>
<!-- ######################################################### SIDEBAR ENDE ###################################### -->


        <div class="content_right">

<!-- ######################################################### MAIN-CONTENT ###################################### -->
        <div class="content_box">
          <div class="content_box_inner">

            <h1>Suchergebnis</h1>
            <p />
            <div class="clearfloat">
                <?php
                    $searchStr = $_POST[searchStr];
                    if ($searchStr) { 
                        reloadPage("suche.php?suche=$searchStr",0);
                        
                    } else {
                        $searchStr = $_GET[suche]; 
                        $link = "suche.php";
                        echo ("<div class='adressSearch'  >");
                        echo ("<form class='adressSearchForm' method='post' action='$link' >");
                        echo ("<input type='text' class='adressSearchInput' name='searchStr' value='$searchStr' />");
                        echo ("<input type='submit' class='adressSearchButton' name='adressSearchButton' value='suchen' />");

                        echo ("</form>");
                        echo ("</div>");


                        if ($searchStr) {
                            include("incs/search.php");
                            search($searchStr);
                        }
                    }

                ?>
                 
                

            </div>

          </div>
        </div>


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

    <script type="text/javascript" src="js/jquery.form.js"></script>
    <script type="text/javascript" src="js/jquery.form.autosize.js"></script>
    <script type="text/javascript" src="js/script-contact.js"></script>

    <!-- JS-GoogleMap -->
		<script type="text/javascript" src="js/gmaps/maps-api.js"></script>
		<script type="text/javascript" src="js/gmaps/jquery.ui.map.js"></script>
    <script type="text/javascript" src="js/gmaps/map.js"></script>
    <script type='text/javascript' src='js/script-calendar.js'></script>

<!--[if lte IE 7]>
    <script type="text/javascript" src="ie7/IE9.js">IE7_PNG_SUFFIX=".png";</script>
    <script type="text/javascript" src="ie7/ie7-squish.js" type="text/javascript"></script>
<![endif]-->

</body>
</html>