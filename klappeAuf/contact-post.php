<? header("Content-Type: text/html; charset=utf-8");

if($_POST[formsent]=="1")
{
  $email = $_POST[email];

  $muster = "/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/";                          
  if (preg_match($muster, $email) <= 0 OR $email =="") { $mailfehler = "yes"; } else { $mailfehler = "no"; }
  
  if(!$_POST[lastname] OR $_POST[lastname]=="Nachname") { $fehler.="- Bitte geben Sie Ihren Nachnamen an<br />"; };      
  if($mailfehler=="yes") { $fehler.="- Bitte geben Sie eine g&uuml;ltige E-Mail-Adresse an<br />"; };  
     

if($fehler)
    {
    ?>
<script type="text/javascript">
$(function() {
    $('#form_success_mail').hide();
    $('#form_success_res').hide();
    $('#form_error').empty().hide().delay(200).fadeIn('slow');    
    $('#form_error').append('<div class="formmsgerror"><? echo "<p /><span class=\"bold\">Fehler bei der Eingabe - Bitte noch ausf&uuml;llen:</span><br />$fehler"; ?><\/div>');     
  });

  
</script>
    <?
    
    }
else {

$vname    = $_POST[prename];
$lname    = $_POST[lastname];
if($vname!="") { $the_name = $vname." ".$lname; } else { $the_name = $lname; }
$phone    = $_POST[fon];
$fax    = $_POST[fax];
$message = $_POST[message];
          
          //$empfaenger = "mhugel@web.de";                      
          $empfaenger = "info@klappeauf.de"; 
          $betreff = "Klappe auf || Neue E-Mail über Kontakt-Formular";
          $betreff = mb_encode_mimeheader( $betreff, "UTF-8", "Q" );          
          
          $absender = "$email";            
          
          $date = date("d.m.Y");
          $time = date("H:i");

          $mailbody = "Mail "; 
          $mailbody .="gesendet am $date um $time Uhr\n\n";
          $mailbody .= "Absender: ";          
          $mailbody .= "$the_name\n";   
          $mailbody .= "E-Mail-Adresse: $email";     
          
          if($phone !="") { $mailbody .= "\nTelefon: $phone\n"; }
          if($fax !="") { $mailbody .= "\nTelefax: $fax"; }                                                                   
          
          if($message!="")
          {          
            $mailbody .= "\n\nNachricht:\n";
            $mailbody .= "-----------------------------------------\n";
            $mailbody .= "$message\n";                   
          }
          
            $mailbody = stripslashes($mailbody);              

        $badsigns = array("Ã¤","Ã„","Ã¶","Ã–","Ã¼","Ãœ","ÃŸ", "â‚¬"); 
        $goodsigns = array("ä","Ä","ö","Ö","ü","Ü","ß", "€");
        $mailbody = str_replace($badsigns,$goodsigns,$mailbody); 
  
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=ISO-8859-1;\r\n";  
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";                                    
            $headers .= "FROM: $absender\r\n";
            @mail($empfaenger,$betreff,$mailbody,$headers);  
            

?>
<script type="text/javascript">
$(function() {
    $('#form_error').hide();    
    <?
    echo "$('#form_success_mail').hide().delay(200).fadeIn('slow').delay(3000).fadeOut();";    
    ?>    
    $('#contactForm').resetForm();
    $('.forminput').blur(); 
  });

  
</script>
<?
}
 } // ENDE POST
 ?>