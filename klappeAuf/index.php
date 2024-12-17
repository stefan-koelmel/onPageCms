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

<link type="text/css" rel="stylesheet" href="css/coda-slider.css" />

<meta name="description" content="Klappe auf - Das Kulturmagazin der Region Karlsruhe || Aktuelle Ausgabe und Veranstaltungen in Karlsruhe und Region" />
<meta name="keywords" content="Karlsruhe,Veranstaltungskalender,Kultur,Stadtmagazin,Klappe auf,Musik,Konzerte,Theater,Comedy,Gastronomie,Kunst,Ausstellungen" />

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
<?  
    include("incs/header-mainmenu.php"); 
    timer_add("menue");    
?>
                  </div>            
            </div>    
  
        <div class="clearboth"></div>                   
        </div>
      </div>
<!-- ######################################################### HEADER-ENDE ###################################### --> 
      
      
      
      
      <div class="maincontent maincontainer">
        <?php
            $showdate = $_GET[showdate];
            if ($showdate) {
                echo ("<div style='background-color:#cc0;margin-bottom:10px;padding-left:10px;'>");
                $dateStr = cmsDate_getDayString($showdate,1);
                echo ("<h2>Startseite von $dateStr </h2>");
                echo ("</div>");
            }
        ?>
          
      
<!-- ######################################################### SIDEBAR ###################################### -->        
        <div class="sidebar">    
          <? 
            include("incs/sidebar.php");
            sidebar_show($showdate);
            timer_add("sidebar");
          ?>                               
        </div>      
<!-- ######################################################### SIDEBAR ENDE ###################################### -->   
        
        
        <div class="content_right">
          
<!-- ######################################################### SLIDER - HIGHLIGHT-THEMEN ###################################### --> 

              <div class="slider_highlight content_box box-shadow">
                  <?php 
                    include ("incs/index_tips.php");
                    tip_show($showdate);
                    timer_add("tips");
                    
                    include ("incs/index_articleTip.php");
                    articleTip_show($showdate);
                    timer_add("mainArticle");
                    
                  ?>
                  
              <!--    <div class="home_highlight">
                      <div class="home_highlight_img hh_img_wide">
                        <a href="#"><img src="dummys/tb-dummy.jpg" alt="" /></a>
                      </div>
                    <div class="home_highlight_content">
                      <strong>SuperHighlightInfo</strong><br />
                      Rocken bis der Arzt kommt!<br />
                      BlaBlaBlaBLUBkl klk k&ouml;lk &ouml;l BlaBlaBlaBLUBkl klk k&ouml;lk &ouml;l.
                      BlaBlaBlaBLUBkl klk k&ouml;lk &ouml;l....
                    </div>
                    <div class="hh_readmore"><a href="vorlage-artikel.php" class="hh_readmore_link"><span class="quo">&rsaquo;</span> weiter lesen</a></div>
                  </div>-->
              <div class="clearboth"></div>
              </div>

          
          
<!-- ######################################################### SLIDER - HIGHLIGHT-THEMEN ENDE ###################################### -->               
          
<!-- ######################################################### THEMENBOXEN START ###################################### -->
        <?php
            include ("incs/index_themes.php");
            show_themes($showdate);
            timer_add("themesBoxes");
        ?>
<!-- ######################################################### THEMENBOXEN ENDE ###################################### -->           
          
          
<!-- ######################################################### AKTUELLE AUSGABE ###################################### -->           
        <?php
            include ("incs/index_weitereThemen.php");
            timer_add("furtherDates");
        ?>
 <!-- ######################################################### AKTUELLE AUSGABE ENDE ###################################### -->           
          
       </div>                
       <div class="clearboth">&nbsp;</div>
      </div>


<div class="push">&nbsp;</div>
</div>


<!-- ######################################################### FOOTER ###################################### -->   
<div class="footer">
    <? 
        include("incs/footer-content.php"); 
        timer_add("footer");
    ?>
</div>
<!-- ######################################################### FOOTER-ENDE ###################################### -->   
		

    		<!-- JS-FILES -->
		
    
    <script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
    <script type="text/javascript" src="js/jquery.coda-slider-3.0.js"></script>    
    
    <script type="text/javascript" src="js/script-home.js"></script>
    <script type='text/javascript' src='js/script-calendar.js'></script>

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