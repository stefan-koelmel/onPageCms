<?php // charset:UTF-8
    session_start();
    header("Content-Type: text/html; charset=utf-8");
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

<meta name="description" content="Klappe auf - Das Kulturmagazin der Region Karlsruhe || Kontakt - Klappe auf - Kreuzstra&szlig;e 3 - 76133 Karlsruhe - Tel.: 0721 / 380 893 - Fax: 0721 / 380 121 - E-Mail: info@klappeauf.de" />
<meta name="keywords" content="Karlsruhe,Veranstaltungskalender,Kultur,Stadtmagazin,Klappe auf,Musik,Konzerte,Theater,Comedy,Gastronomie,Kunst,Ausstellungen" />

<title>Kontakt || Klappe auf - Das Kulturmagazin der Region Karlsruhe</title>

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
           
            <h1>Kontakt</h1>
            <p />
            <div class="clearfloat">
                  <div class="contact-left">
                    <h3>Klappe auf</h3>
                    Kreuzstra&szlig;e 3<br />
                    76133 Karlsruhe<p />

                    Tel.: 0721 / 380 893<br />
                    Fax: 0721 / 380 121<br />
                    E-Mail: <a href="mailto:info@klappeauf.de">info@klappeauf.de</a>                                                           
                   
                   <div id="map_canvas" class="map"></div>
                 </div>
                 
                  <div class="contact-right">
                  
                   <div class="contactformcontainer">
                    <h3>Kontaktformular</h3>
                    <h4>Der einfachste Weg zum schnellen Kontakt</h4>
                    <p />
                      <form id="contactForm" action="#cpost" method="post">
                                    <div><input type="hidden" name="formsent" value="1" /></div>                    
                    
                                    <div>
                                      Vorname<br />
                                      <input type="text" class="forminput" size="40" name="prename" title="Vorname" value="" />                                    
                                    </div>                                    
                                    <p />                                 
                                    
                                    <div>
                                      Nachname*<br />
                                      <input type="text" class="forminput" size="40" name="lastname" title="Nachname" value="" />                                    
                                    </div>
                                    <p />
                                    
                                    <div>
                                      E-Mail-Adresse*<br />
                                      <input type="text" class="forminput" size="40" name="email" title="Ihre E-Mail-Adresse" value="" />
                                    </div>                                    
                                    <p />
                                    
                                    <div>
                                    Telefon<br />
                                    <input type="text" class="forminput" size="40" name="fon" title="Telefon" value="" />
                                    </div>
                                    <p />
                                    
                                    <div>
                                    Telefax<br />
                                    <input type="text" class="forminput" size="40" name="fax" title="Telefax" value="" />
                                    </div>
                                    <p />    
                                    
                                    <div>
                                      Frage/Nachricht/Wunsch<br />
                                      <textarea name="message" cols="5" rows="5" class="forminput txtinput"></textarea>                                    
                                    </div>            
                                    
                                    <p />                                     
                                     <div class="clearfloat">
                                      <input type="submit" value="Formular senden" class="contact_submit floatright" name="submit" />
                                     </div>        
                                     
                                    <div id="formoutput"></div>
                                    <div id="form_success_mail" class="displaynone">
                                        <span class="bold">Vielen Dank.</span><br />Ihre Nachricht wurde an uns weitergeleitet.
                                    </div>         
                                    <div id="form_error" class="displaynone"><p /><span class="bold">Fehler</span><br />Check Eingabe</div>                                         
                                      
                                    </form>                                                                                                                                                                                   
                    
                            </div>                  
                 </div>             
             
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