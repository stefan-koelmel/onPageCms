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
            sidebar_show();
          ?>                               
        </div>      
<!-- ######################################################### SIDEBAR ENDE ###################################### -->   
        
        
        <div class="content_right">
          
<!-- ######################################################### MAIN-CONTENT ###################################### --> 
        <div class="content_box">
          <div class="content_box_inner">
          
            <h1>Veranstaltungskalender</h1>
            <?php
                //if ($_GET[var1]) {
                // 
//                if ($_SESSION[userLevel] == 9) {
//                    //show_array($_SERVER);
//                    $redirect = $_SERVER[REDIRECT_SCRIPT_URL];
//                    $paraList = explode("/",substr($redirect,1));
//                    echo "<h1>Hier $redirect $cmsName </h1>";
//                    for($i=0;$i<count($paraList);$i++) {
//                            echo ("parameter $i = $paraList[$i] <br>");
//                    }
//                    
//                    
//                    if ($paraList[0] == $cmsName) {
//                        
//                        $shiftName = array_shift($paraList);
//                        echo ("<h2>CMS ROOT $shiftName </h2>");
//                    }
//                   
//                    if ($paraList[0]=="kalender") {
//                        for($i=1;$i<count($paraList);$i++) {
//                            $para = $paraList[$i];
//                            if (strlen($para)==10) {
//                                if ($para[4]=="-" AND $para[7]=="-" ) {
//                                    echo ("Datum $para<br>");
//                                }
//                                
//                            }
//                        
//                            echo ("parameter $i = $paraList[$i] <br>");
//                        }
//                        
//                        
//                        echo "<h1>Hier $redirect</h1>";
//                            foreach ($_GET as $key => $value) {
//                                echo ("<b>$key</b> = $key <br>");
//                            }
//                    }
//                }
//                
            
            
                include("incs/kalender.php");
                $link = "kalender.php";
                
                $selectedCategory = $_GET[cat];
                if (!intval($selectedCategory)){
                    $catList = categoryGetList("TerminCategoryList");
                    foreach ($catList as $catId => $value) {
                        if ($value[shortName] == $selectedCategory) $selectedCategory = $catId;                                           
                    }
                }
                
                
                
                $selectedRegion = $_GET[region];
                if (!intval($selectedRegion)){
                    $regionList = categoryGetList("RegionList");
                    foreach ($regionList as $catId => $value) {
                        if ($value[shortName] == $selectedRegion) $selectedRegion = $catId;                                           
                    }
                }
                
                $selectedLocation = $_GET[location];
                $selectedDateId = $_GET[dateId];
                $selectedDate  = $_GET[date];
                $highlightDateId = $_GET[id];
                if (!$selectedDate) $selectedDate = "today";

                if ($selectedDateId > 0) { // Detailansicht von Termin
                    kalender_showDetail($selectedDate, $selectedRegion, $selectedCategory, $selectedDateId, $link);
                } else {
                    kalender_showFilter($selectedDate,$selectedRegion,$selectedCategory,$link);

                    kalender_showList($selectedDate,$selectedRegion,$selectedCategory,$highlightDateId,$link);
                }
                
            ?>
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
    <?php
        if ($selectedDateId>0) {
            echo ("<script type='text/javascript' src='js/fancybox/jquery.fancybox-script.js'></script>");
            echo ("<script type='text/javascript' src='js/fancybox/js-fancybox-config.js'></script>");
        } else {
            
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
              
    <script type="text/javascript" src="js/script.js"></script>	 
    
    <script type="text/javascript" src="js/script-select-content.js"></script>		     
    <script type='text/javascript' src='js/script-calendar.js'></script>
    

</body>
</html>