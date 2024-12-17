<?php // charset:UTF-8
class cmsType_basket_base  extends cmsType_contentTypes_base {
    function getName() {
        return "Warenkorb";
    }
    //put your code here


    function show($contenData,$frameWidth) {
        
    }
    
    function basket_editContent($editContent, $frameWidth) {
        $res = array();
        return $res;
    }
    
    function payPal_showButton($sandbox,$value,$bookingNr,$customerNr) {
        $homePageName = "car";
        // echo ("HomePage = $homePageName<br />");
        if ($sandbox == 1) {
            $payPalPage = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $payPalPage = "https://www.paypal.com/cgi-bin/webscr";
        }

        $cancelPage = "http://cms.stefan-koelmel.com/inner/house.php?paypal=cancel";
        $payedPage = "http://cms.stefan-koelmel.com/inner/house.php?paypal=payed";

        $netto = $value / 1.19;
        $tax = $value - $netto;

        $netto = number_format($netto,2,".","");
        $tax = number_format($tax,2,".","");

      //  echo ("Brutto = $value Netto = $netto Tax = $tax <br />");


        echo("<form action='$payPalPage' method='post' >\n"); // target='paypal'>\n");

        echo("<input type='hidden' name='cmd' value='_xclick'>\n");
        echo("<input type='hidden' name='business' value='paypal@car-reporter.de'>\n");
        echo("<input type='hidden' name='item_name' value='Bestellung $bookingNr'>\n");
       // echo("<input type='hidden' name='item_number' value='Artikelanzahl'>\n");
        echo("<input type='hidden' name='amount' value='$netto'>");
        echo("<input type='hidden' name='no_shipping' value='1'>");
        echo("<input type='hidden' name='custom' value='$customerNr'>");
        echo("<input type='hidden' name='cancel_return' value='$cancelPage'>\n");
        echo("<input type='hidden' name='return' value='$payedPage'>\n");
        echo("<input type='hidden' name='tax' value='$tax'>");

        switch($homePageName) {
            case "car" :
                echo('<input type="hidden" name="page_style" value="carreporter">');
                break;
            case "bike" :
                echo('<input type="hidden" name="page_style" value="bikereporter">');
                break;
        }
        echo('<input type="hidden" name="no_note" value="1">');
        echo('<input type="hidden" name="currency_code" value="EUR">');
        echo('<input type="hidden" name="bn" value="IC_Beispiel">');
        echo('<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen ? mit PayPal.">');
        echo('<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">');

        echo('</form>');

    }

}


function cmsType_basket_class() {
    if ($GLOBALS[cmsTypes]["cmsType_basket.php"] == "own") $basketClass = new cmsType_basket();
    else $basketClass = new cmsType_basket_base();
    return $basketClass;
}


function cmsType_basket($contentData,$frameWidth) {
    $basketClass = cmsType_basket_class();
    $basketClass->show($contentData,$frameWidth);
}



function cmsType_basket_editContent($editContent,$frameWidth) {
    $basketClass = cmsType_basket_class();
    return $basketClass->basket_editContent($editContent,$frameWidth);
}


?>
