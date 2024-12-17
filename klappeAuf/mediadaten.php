<?php // charset:UTF-8
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

    include("incs/header-document.php");
?>


<meta name="description" content="Mediadaten | Klappe auf - Karlsruhe | Alle Daten auf einen Blick" />
<meta name="keywords" content="klappeauf, karlsruhe, kultur, magazin, kulturmagazin, mediadaten, kalender, veranstaltungskalender, musik, konzerte, kunst, gastronomie" />

<title>Klappe auf - Das Kulturmagazin der Region Karlsruhe ** Mediadaten</title>

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
          sidebar_show();
          ?>                               
        </div>      
<!-- ######################################################### SIDEBAR ENDE ###################################### -->   
        
        
        <div class="content_right">
          
<!-- ######################################################### MAIN-CONTENT ###################################### --> 
        <div class="content_box">
          <div class="content_box_inner">
            <h1>Mediadaten</h1>
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

    

<!--[if lte IE 7]> 
    <script type="text/javascript" src="ie7/IE9.js">IE7_PNG_SUFFIX=".png";</script>
    <script type="text/javascript" src="ie7/ie7-squish.js" type="text/javascript"></script>
<![endif]-->

</body>
</html>