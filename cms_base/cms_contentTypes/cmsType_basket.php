<?php // charset:UTF-8
class cmsType_basket_base  extends cmsType_contentTypes_base {
    function getName() {
        return "Warenkorb";
    }
    //put your code here


    function show($contenData,$frameWidth) {
        $data = $contenData[data];
        if (!is_array($data)) $data = array();
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "basket";
        
        switch ($viewMode) {
            case "info" : 
                $res = $this->show_info($contenData,$frameWidth);
                break;
            case "basket" :
                $res = $this->show_basket($contenData,$frameWidth);
                break;
        }
        return $res;
    }
    
   
    function addToBasket($addData,$goPage) {
        // echo "Add To Basket <br>";
        //show_array($addData);
        $out = "Artikel wurde dem Warenkorb hinzugefügt <br>";
        $out .= "Name: $addData[name] Anzahl: $addData[amount] Preis: $addData[value]";
        if (!is_array($_SESSION[basket])) $_SESSION[basket] = array();
        if (!is_array($_SESSION[basket][basketList])) $_SESSION[basket][basketList] = array();
        // show_array($addData);
        $basketId = $addData[dataSource]."_".$addData[dataId];
        echo ("BASKET ID = $basketId<br>");
        if ($_SESSION[basket][basketList][$basketId]) {
            $_SESSION[basket][basketList][$basketId][amount] = $_SESSION[basket][basketList][$basketId][amount] + $addData[amount];
        } else {
            $_SESSION[basket][basketList][$basketId] = $addData;
        }
        
        cms_infoBox($out);

        if ($goPage) {
            reloadPage($goPage,3);
        }

        return 1;
    }


    function basket_showItem($basketItem,$showData) {
        if (!is_array($basketItem)) return "noBaskestItem";
        if (!is_array($showData)) return "noChowData";

        
        // foreach($basketItem as $key => $value ) echo ("$key=$value <br>");
        
        $basketId = $basketItem[basketId];
        if (!$basketId) return "noBasketId";

        $basketName = $basketItem[name];
        if (!$basketName) return "noBasket - name";

        $basketVK = $basketItem[vk];
        if (is_null($basketVK)) return "noBasket - vk";

        $basketDataSoucre = $basketItem[dataSource];
        if (!$basketDataSoucre) return "noBasket - dataSoucre";
        
        $basketDataId = $basketItem[dataId];
        if (!$basketDataId) return "noBasket - dataId";
        
        
        $basketShipping = $basketItem[shipping];
        if (is_null($basketShipping)) return "noBasket - shipping";

        $showDiv = 1;
        $class = $showData["class"];
        $hideDiv = $showData[hideDiv];
        if ($hideDiv) $showDiv = 0;

        $str = "";
        if ($showDiv) $str .= "<div class='basket $basketId $class' style='border:1px solid #f00;border-radius:10px;background-color:#dff;'>";

        $str.="<div class='hiddenData cmsBasketAddName'>$basketName</div>";
        $str.="<div class='hiddenData cmsBasketAddValue'>$basketVK</div>";
        $str.="<div class='hiddenData cmsBasketAddShipping'>$basketShipping</div>";
        $str.="<div class='hiddenData cmsBasketAddDataSource'>$basketDataSoucre</div>";


        $str .= "<input type='text' class='cmsBasketAddCount' style='width:20px' value='1' name='basket[$basketId][amount]' />";
        $str .= "<div class='mainJavaButton cmsAddBasketButton' name='$basketId'>add</div>";


        $inBasket = cmsBasket_getItemCount($basketId);
        if ($inBasket) {
            $str .= "<b>$inBasket</b> im Warenkorb<br>";
        }
        
        if ($showDiv) $str .= "</div>";
        return $str;



         $out = div_start_str("basket $basketId tableItemNoClick","width:100%;background-color:#dff;z-index:200;");

        $basketId = $content[basketId];
        $inBasket = $content[inBasket];

        $vk = $data[vk];
        $shipping = $data[shipping];
        $anz = $data[count];

        if ($anz) $maxAdd = $anz;
        else $maxAdd = 0;

        if ($inBasket) {
            if ($maxAdd) $maxAdd = $maxAdd - $inBasket;
        }

        $out .= "<input type='text' class='cmsBasketAddCount' style='width:20px' value='1' name='basket[$basketId][amount]' />";
        $out .= "<div class='mainJavaButton cmsAddBasketButton' name='$basketId'>add</div>";
        if ($data[count]) {
            $cont .= "anz ".$data[count]."<br />";
        }

        $out .= cmsType_basket_showItem($basketItem,$showData);

        $out.="<div class='hiddenData cmsBasketAddName'>$data[name]</div>";
        $out.="<div class='hiddenData cmsBasketAddValue'>$data[vk]</div>";
        $out.="<div class='hiddenData cmsBasketAddShipping'>$data[shipping]</div>";


        $vk = $data[vk];
        $shipping = $data[shipping];
        $anz = $data[count];
        $out .= "VK=$vk SHIP=$shipping ANZ=$anz <br>";
        if ($anz) $availible = $anz;
        else $availible = 0;



        $out .= "BasketId = $basketId inBasket = $inBasket <br>";

        if ($inBasket) {
            $out .= $inBasket." im Warenkorb<br>";
        }
        if ($maxAdd) {
            $out .= "Noch $maxAdd verfügbar <br>";
            $out.="<div class='hiddenData cmsBasketMaxCount'>$maxAdd</div>";
        }


       //  foreach ($content as $key => $value ) $out .= "$key => $value <br>";

        //
        $out .= div_end_str("basket $basketId");






        return $str;
    }
    function show_info($contenData,$frameWidth) {
        $str = "";
        if (function_exists("cmsBasket_getValue")) {
            $basketData = cmsBasket_getValue(); // $this->basketValue();
        }
        //  foreach($basketData as $key => $value) echo ("$key = $value <br>");
        $data = $contenData[data];
        if (!is_array($data)) $data = array();
        $showDiv = 1;
        if ($data[noDiv]) $showDiv = 0;
        $basketItems = $basketData[items];
        if ($frameWidth < 180) $frameWidth = 180;
        // 
        
       
        $showEmpty = $data[showEmpty];
        
        
        if ($showDiv) {
            $basketClass = "basketInfo";
            if ($basketItems) {
                $basketClass .= " basketInfo_hasItems";                
            } else { 
                $basketClass .= " basketInfo_noItems"; 
                if (!$showEmpty) {
                    $basketClass .= " basketInfo_hideEmpty"; 
                }
            }
            
            $str .= div_start_str($basketClass,"width:".$frameWidth."px;");
            // $str .= "<div class='basketInfo' style='width:".$frameWidth."px'>";
        }
        if ($basketItems) {
            $str .= "<h2>Warenkorb Info</h2>";
            $basketValue = $basketData[value];
            $basketParts = $basketData[parts];
            $basketItems = $basketData[items];
            $basketShipping = $basketData[shipping];
            
            // show_array($basketData);
            $showItems = $data[showItems];
            $showParts = $data[showParts];
            $showValue = $data[showValue];
            $showShipping = $data[showShipping];
            
            if ($showItems) {
                $str .= "Ihr Warenkorb enthält $basketItems Artikel<br />";
            }
            if ($showParts) {
                $str .= "Ihr Warenkorb enthält $basketParts Teile<br />";
            }
            
            if ($showValue) {
                $str .= "Warenwert: ".number_format($basketValue, 2,",",".")." € <br />";
            }
            
            if ($showShipping) {
                $str .= "Versand: ".number_format($basketShipping, 2,",",".")." € <br />";
            }
            $str .= "<a href='basket.php' class='mainSmallButton cmsGoBasketButton'>Zum Warenkorb</a>";
                      
        } else { // basket Empty
            $showEmpty = $data[showEmpty];
            if ($showEmpty) {
                $str .= "<h3>Warenkorb</h3>";
                $str .= "Ihr Warenkorb ist leer<br />";
            }
        }
        // 
        if ($showDiv) {
            $str .= div_end_str($basketClass);
            //$str .= "</div>";
        }
        
        if ($data[out]) return $str;
        echo ($str);        
    }
    
    function show_basket($contenData,$frameWidth) {
        $basketData = cmsBasket_getValue(); // >basketValue();
        $data = $contenData[data];
        if (!is_array($data)) $data = array();
        
        $basketItems = $basketData[items];
        if ($basketItems) {
            
            $goPage = $GLOBALS[pageData][name].".php";
            
            $step = $_POST[step];
            if (!$step) $step = "list";
            
            if ($_POST) {
                
                if ($_POST[back]) { // GO BACK
                    switch ($step) {
                        case "adress" : $step = "list"; break;
                        case "payment" : $step = "adress"; break;
                        case "order" : $step = "payment"; break;
                    }
                }

                if ($_POST[clearBasket])  {
                    // echo ("<h1> Clear Basket</h1>");
                   
                    cms_infoBox("Warenkorb wurde geleert");
                    cmsBasket_clearBasket();
                    reloadPage($GLOBALS[pageData][name].".php",4);
                    return 0;
                }
                
                if ($_POST[go_list]) $step = "list";
                if ($_POST[go_adress]) $step = "adress";
                if ($_POST[go_payment]) $step = "payment";
                if ($_POST[go_order]) $step = "order";
                
                // GO ADRESSE
                if ($_POST[adress]) {
                    $res = $this->checkBasketPost($step,$_POST,$data);
                    // echo ("Check RESULT = $res <br />");
                    if ($res == 1) $step = "adress";
                    if (is_array($res)) $error = $res;      
                }            
                
                // GO PAYMENT
                 if ($_POST[payment]) {
                    $res = $this->checkBasketPost($step,$_POST,$data);
                    // echo ("Check RESULT = $res <br />");
                    if ($res == 1) $step = "payment";
                    if (is_array($res)) $error = $res;                                           
                }  
                
                // GO PAYMENT
                if ($_POST[order]) {
                    $res = $this->checkBasketPost($step,$_POST,$data);
                    if ($res == 1) $step = "order";
                    if (is_array($res)) $error = $res;                                            
                }  
                
                // GO Confrim
                if ($_POST[confirm]) {
                    $res = $this->checkBasketPost($step,$_POST,$data);
                    if ($res == 1) $step = "confirm";
                    if (is_array($res)) $error = $res;      
                }  
                
                // GO Confrim
                if ($_POST[finish]) {
                    $this->basket_clear();
                    reloadPage("index.php",0);
                    return 0;
                }  
                
            }
            echo ("<h1>Warenkorb</h1>");     
            
            echo ("<form method='post' action='$goPage' >");
            echo ("<input type='hidden' value='$step' name='step' />");

            switch ($step) {
                case ("list") :
                    echo("<input type='submit' value='weiter' class='cmsInputHidden' name='adress' />");
                    break;
                case "adress" :
                    echo("<input type='submit' value='weiter' class='cmsInputHidden' name='payment' />");
                    break;
                case "payment" :
                    echo("<input type='submit' value='weiter' class='cmsInputHidden' name='order' />");
                    break;
                case "order" :
                    break;
                case "confirm" :
                    break;
            }


            
            $this->show_steps($step, $basketData, $contenData,$error, $frameWidth);
            switch ($step) {
                case ("list") :
                    $this->show_basket_list($step,$basketData,$contenData,$error,$frameWidth);
                    break;
                case "adress" :
                    $this->show_basket_adress($step,$basketData,$contenData,$error,$frameWidth);
                    break;
                case "payment" :
                    $this->show_basket_payment($step,$basketData,$contenData,$error,$frameWidth);
                    break;                    
                case "order" :
                    $this->show_basket_order($step,$basketData,$contenData,$error,$frameWidth);
                    break;
                case "confirm" : 
                    break;
            }
            $this->show_button($step, $basketData, $contenData, $error, $frameWidth);
            echo ("</form>");
        } else {
            $showEmpty = $data[showEmpty];
            if ($showEmpty) {
                echo ("<h2>Warenkorb</h2>");
                echo ("Ihr Warenkorb ist leer<br>");
            }
        }
    }
    
    function basket_clear() {
        unset($_SESSION[basket]);
    }
    
    
    function checkBasketPost($step,$value,$data) {
        
        $res = 0;
        switch ($step) {
            case "list" :
                // echo("check List <br>");
                $res = 1;
                break;
            
            case "adress" :
                // echo("check Adresse <br>");
                $res = 0;
                
                $error = array();
                
                $adress = $_POST[basket][adress];
                foreach ($_POST[basket] as $key => $value) echo ("POST $key = $value <br>");
                
                
                $resAdress = $this->checkAdress("adress",$adress,$data);
                // echo ("Result of Check $adress Result = $resAdress <br>");
                if (is_array($resAdress)) {
                    $error[adress] = $resAdress;
                    foreach ($resAdress as $key => $value ) echo ("Fehler in Adresse $key = $value <br>");
                }
                
                
                $useDeliveryAdress = $adress[useDeliveryAdress];
                if ($useDeliveryAdress) {
                    
                
                    $deliveryAdress = $_POST[basket][delivery];
                    $resShipping = $this->checkAdress("deliveryAdress",$deliveryAdress,$data);
                    
                    //echo ("Result Check DeliveryAdress $useDeliveryAdress of Check Result = $resAdress <br>");
                    if (is_array($resShipping)) {
                        $error[delivery] = $resShipping;
                        // foreach ($deliveryAdress as $key => $value ) echo ("adress $key = $value <br>");
                       //  foreach ($resShipping as $key => $value ) echo ("Fehler in ShippingAdresse $key = $value <br>");
                    }
                } else {
                    // echo "Dont Check DeliveryAdess <br>";
                }
                
                if (count($error)) {
                    return $error;
                }
                return 1;
                
                
//                $res = $this->checkAdress($step,$value,$data);
//                $adress = $_POST[basket][adress];
////                echo ("<h1>Value</h1>");
////                show_array($value);
////                echo ("<h2>Data</h1>");
////                show_array($data);
//                
//                if ($res == 1) {
//                    
//                    if (is_array($adress)) {
//                        $_SESSION[basket][adress] = $adress;
//                        $res = 1;
//                    } else {
//                        $res = 0;
//                    }
//                    //foreach ($_SESSION[basket][adress] as $key => $val) echo ("ADRESSE <b>$key</b> = $val <br/>");
//                   
//                }
                break;
             case "payment" :
                $payData = $_SESSION[basket][payment];
                $post = $_POST[basket][payment];
                if (is_array($post)) {
                    $_SESSION[basket][payment] = $post;
                    $payData = $post;
                    if ($post[paySelect]) $paySelect = $post[paySelect];
                } 
                if (!is_array($payData)) return 0;
                
                $paySelect = $payData[paySelect];
                
                $payInfo = array(); 
                
                
                switch ($paySelect) {
                    case "paypal" : $res = 1;break;
                    case "prePay" : $res = 1;break;
                    case "onDelivery" : $res = 1;break;
                    case "bill" : $res = 1; break;
                    case "creditcard" : 
                        $res = 0;
                        $error = array();
                        $creditType = $payData[creditCardType];
                        
                        if (!$creditType) $error[creditCardType] = "Kein Kreditkarten Typ ausgewählt.";
                        
                        $creditNr = array($payData[creditCardNr1],$payData[creditCardNr2],$payData[creditCardNr3],$payData[creditCardNr4]);
                        
                        for ($i=0;$i<count($creditNr);$i++) {
                            $nr = $creditNr[$i];
                            // echo ("Check Nr $nr <br>");
                            if (strlen($nr)!= 4) $error[cerditCardNr] = "Ungültige Kreditkarten Nummer";
                            $checkNr = str_replace(array("0","1","2","3","4","5","6","7","8","9"),"", $nr);
                            if (strlen($checkNr) != 0) {
                                echo ("CheckNr is not empty '$checkNr' <br />");
                                $error[creditCardNr] = "Ungültige Kreditkarten Nummer";
                            }
                        }
                        
                        $creditMonth = intval($payData[creditCardTo_month]);
                        $creditYear  = intval($payData[creditCardTo_year]);
                        if ($creditMonth < 1 or $creditMonth > 12) {
                            $error[cerditCardDate] = "Falsches Datum für Gültigkeit der Kreditkarte";
                        }
                        list($year,$month) = explode("-",date("Y-m-d"));
                        if ($creditYear<$year) {
                            $error[creditCardDate] = "Falsches Datum für Gültigkeit der Kreditkarte";
                        }
                        
                        if (!$error[creditCardData]) {
                            if ($creditYear == $year) {
                                // echo ("Compare Year $year $creditMonth $month <br>");
                                if ($creditMonth < $month) {
                                    $error[cerditCardDate] = "Ihre Kreditkarte ist nicht mehr gültig";
                                }
                            }
                        }
                        
                        // echo ("Gültig bis $creditMonth $creditYear $year <br>");
                        $creditCode = $payData[creditCardCode];
                        if (strlen($creditCode) != 3) {
                            $error[creditCardCode] = "Ungültige Kreditkarten Prüfnummer";
                        }
                        $checkNr = str_replace(array("0","1","2","3","4","5","6","7","8","9"),"", $creditCode);
                        if (strlen($checkNr) != 0) {
                            $error[creditCardCode] = "Ungültige Kreditkarten Prüfnummer";
                        }                             
                         
                        
                        
                        if (count($error)) {
                              return $error;
                        }
                        
                        
                        $payInfo = array();
                        $payInfo[type] = $creditType;
                        
                        $nr = "";
                        for($i=0;$i<count($creditNr);$i++) {
                            if ($nr) $nr .= " ";
                            $nr.=$creditNr[$i];                            
                        }
                        $payInfo[nr] = $nr;
                        $date = $creditYear."-";
                        if ($creditMonth<10) $date.="0".$creditMonth;
                        else $date .= $creditMonth;
                        $payInfo[date] = $date;
                        $payInfo[code] = $creditCode;
                        
                        
                        
                        $res = 1;                        
                        break;
                    default :
                        echo ("unkown Payment $paySelect <br>");
                        $res = 0;
                }
                
                $rebate = $payData[$paySelect."_rebate"];
                $onTop  = $payData[$paySelect."_ontop"];
                
                // echo ("RABATT = $rebate AUFSCHLAG = $onTop <br>");
                
                echo ("CHECK RESULT is $res for $paySelect <br>");
                if ($res==1) {
                    $_SESSION[basket][payment] = array();
                    $_SESSION[basket][payment][paySelect] = $paySelect;
                    $_SESSION[basket][payment][rebate] = $rebate;
                    $_SESSION[basket][payment][ontop]  = $onTop;
                    $_SESSION[basket][payment][info]  = $payInfo;
                    show_array($_SESSION[basket][payment]);
                }
                break;
            
            case "order" :
                echo("check Order <br>");
                $adress = $_SESSION[basket][adress];
                $error = array();
                $orderData = array();
                if (is_array($adress)) {
                    $orderData[adress] = $adress;                    
                } else {
                    $error[adress] = "Keine Adresse";
                }
                
                $payment = $_SESSION[basket][payment];
                if (is_array($payment)) {
                    $orderData[payment] = $payment;                    
                } else {
                    $error[payment] = "Keine Bezahlung ausgewählt";
                }
                
                if (count($error)) {
                    foreach ($error as $key => $value) {
                        echo ("Fehler <b>$key</b> $value <br />");
                    }
                }
                
                
                $res = cmsOrder_add($orderData);
                echo ("Result of AddOrder $res <br />");
                // show_array($data);
                $res = 0;
                break;
            
            default :
                echo ("checkBasketPost($step,$value)<br>");
        }
        return $res;
    }
    
    
    function show_basket_list($step,$basketData,$contenData,$error,$frameWidth) {
        $this->show_list($step,$basketData,$contenData,$error,$frameWidth);
        $this->show_sum($step,$basketData,$contenData,$error,$frameWidth);       
    }
    
    
    
    
    function show_basket_payment($step,$basketData,$contenData,$error,$frameWidth) {
        
        if ($step != "payment") {
            $payData = $_SESSION[basket][payment];
            div_start("basketInfo basketInfo_payment");
            echo ("<h3>Bezahlung</h3>");
            // show_array($payData);
            
            
            $paySelect = $payData[paySelect];
            echo ("Bezahlung: $paySelect <br>");
            
            
            $onTop = $payData[ontop];
            $rebate = $payData[rebate];
            if ($onTop) {
                echo ("Aufschlag:".number_format($onTop,2,",",".")." € <br />");
            }
            if ($rebate) {
                echo ("Rabatt:".number_format($rebate,2,",",".")." € <br />");
            }
            
            div_start("basketInfo_change");
            echo ("<input class='basketChangeButton' type='submit' value='Bezahlung ändern' name='go_payment'>");
            div_end("basketInfo_change");
            
            div_end("basketInfo basketInfo_payment");
            
            return 0;
            
        }
        
        
        
        echo ("<h1>Bezahlung $step</h1>");
        $data = $contenData[data];
        if (!is_array($data)) $data = array();
        
        
        
        $payData = $_SESSION[basket][payment];
        if (is_array($payData)) {
            $paySelect = $payData[paySelect];
        }
      
        $post = $_POST[basket][payment];
        if (is_array($post)) {
            $_SESSION[basket][payment] = $post;
            $payData = $post;
            if ($post[paySelect]) $paySelect = $post[paySelect];
        }
        
        $basketValue = $basketData[value];
        $basketShipping = $basketData[shipping];
        
        
        
        $payment = array();
        $payList = array("paypal","prePay","onDelivery","creditcard","bill");
        
        for ($i=0;$i<=count($payList);$i++) {
            $pay = $payList[$i];
            //echo ("Check $pay <br>");
            
            $payOn = $data[$pay];
            if ($payOn) {
                $rebate = $data[$pay."Rebate"];
                $ontop = $data[$pay."Ontop"];
                $payment[$pay] = array("ontop"=>$ontop,"rebate"=>$rebate);
            }
            // echo ("aktive = $payOn Rabat = $rebate OnTop = $onTop <br />");
            
        }
        
       // echo ("<h1> Selected = $paySelect </h1>");
        
        foreach ($payment as $key => $value) {
            $addPrice = 0;
            $subPrice = 0;
            switch ($key) {
                case "paypal" : $name = "payPal"; break;
                case "prePay" : $name = "Vorkasse"; break;
                case "onDelivery" : $name = "Nachnahme"; break;
                case "creditcard" : $name = "Kreditkarte"; break;
                case "bill" : $name = "Rechnung"; break;
                default :
                    echo "Unkown Pay Type $key <br>";
            }
            
            
            if ($key == $paySelect) $checked="checked='checked'";
            else $checked = "";
            
            echo ("<input type='radio' $checked class='basketpayment basketpayment_$key' name='basket[payment][paySelect]' value='$key' />".$name);
            
            $rebate = $value[rebate];
            if ($rebate) {
                $prozOff = strpos($rebate,"%");
                if ($prozOff) {
                    $prozValue = substr($rebate,0,$prozOff);
                    echo (" - ".$prozValue.",0 % Rabatt");
                    $subPrice = 1.0 * ($basketValue + $basketShipping) * $prozValue / 100;
                    
                } else {
                    $val = floatval($rebate);
                    if ($val) {
                        echo (" - ".number_format($val,2,",",".")." € Rabatt");
                        $subPrice = 1.0 * $val;
                    }
                }                
            }
            
            $ontop = $value[ontop];
            if ($ontop) {
                $prozOff = strpos($ontop,"%");
                if ($prozOff) {
                    $prozValue = substr($ontop,0,$prozOff);
                    echo (" - ".$prozValue.",0 % Aufschlag");
                    $addPrice = 1.0 * ($basketValue + $basketShipping) * $prozValue / 100;
                } else {
                    $val = floatval($ontop);
                    if ($val) {
                        echo (" - ".number_format($val,2,",",".")." € Aufschlag");
                        $addPrice = 1.0 * $val;
                    }
                }
            }  
            echo ("<br />");
            
            $divName = "basketPayment_info basketPayment_info_".$key;
            if ($key == $paySelect) $divName .= " basketPayment_info_selected";
            $infoText = div_start_str($divName);
            
            $infoText .= "Bezahlung <b>$name</b> ausgewählt<br />";
            $infoText .= "&nbsp;<br />";
            $infoText .= $this->show_basket_payment_showDetails($key,$payData,$error);
            $infoText .= "&nbsp;<br />";
            
            $infoText .= "<input type='hidden' value='$subPrice' name='basket[payment][".$key."_rebate]' />";
            $infoText .= "<input type='hidden' value='$addPrice' name='basket[payment][".$key."_ontop]' />";
            
            $total = $basketValue + $basketShipping;
            $infoText .= "Warenwert : ".number_format($basketValue,2,",",".")." € <br />";
            $infoText .= "Versand : ".number_format($basketShipping,2,",",".")." €  <br />";
            
            if ($subPrice) {
                $infoText .= "Rabatt : ".number_format($subPrice,2,",",".")." € <br />";
                $total = $total - $subPrice;
            }
            
            
            if ($addPrice) {
                 $infoText .= "Aufschlag : ".number_format($addPrice,2,",",".")." € <br />";
                 $total = $total + $addPrice;
            }
            
            $infoText .= "<b>Total : ".number_format($total,2,",",".")." € </b><br />";
            $infoText .= div_end_str($divName);
            
            $payment[$key][infoText] = $infoText;
            // show data Array
            
            if ($key == $paySelect) {
                
            }
            
        }
        
        
        foreach ($payment as $key => $value) {
            $infoText = $value[infoText];
            echo ("$infoText");
        }
        
    }
    
    function show_basket_payment_showDetails($key,$payData,$error) {
        $res = "";
        switch ($key) {
            case "paypal" :
                $res .= "Die Bezahlung mit payPal erfolgt am Ende des Bezahlvorganges<br />";
                break;
            case "prePay" :
                $res .= "Nach erfolgreicher Bestellung erhalten Sie eine eMail in der Sie die Details";
                $res .= " zu Ihrer Bestellung und die Rechnung im PDF-Format.<br />";
                break;
            case "onDelivery" :
                $res .= "Die Bezahlung erfolgt auf Nachnahme. Das heißt der Paket-Bote kassiert das Geld <br />";
                break;
            case "creditcard" :
                
                if (is_array($error)) {
                    foreach($error as $key => $value ) {
                        echo "FEHLER $key $value <br>";
                    }
                }
                
                if ($error[creditCardType]) {
                    $res .= "<div class='cmsUserError'>";
                    $res .= "<span class='cmsUser_errorSpan'>".$error[creditCardType]."</span><br>";
                }
                $res .= "<span class='cmsBasket_infoSpan cmsBasket_needSpan'>Kreditkarte-Typ:</span><input type='text' value='$payData[creditCardType]' name='basket[payment][creditCardType]' /><br>";
                if ($error[creditCardType]) {
                    $res .= "</div>";
                }
                
              //  <span class="cmsUser_infoSpan cmsUser_needSpan" 
                
                // Kreditkarten Nr
                if ($error[cerditCardNr]) {
                    $res .= "<div class='cmsUserError'>";
                    $res .= "<span class='cmsUser_errorSpan'>".$error[cerditCardNr]."</span><br>";
                }
                $res .= "<span class='cmsBasket_infoSpan cmsBasket_needSpan'>Kreditkarten-Nummer:</span>";
                $res .= "<input type='text' value='$payData[creditCardNr1]' style='width:40px;' name='basket[payment][creditCardNr1]' /> ";
                $res .= "<input type='text' value='$payData[creditCardNr2]' style='width:40px;' name='basket[payment][creditCardNr2]' /> ";
                $res .= "<input type='text' value='$payData[creditCardNr3]' style='width:40px;' name='basket[payment][creditCardNr3]' /> ";
                $res .= "<input type='text' value='$payData[creditCardNr4]' style='width:40px;' name='basket[payment][creditCardNr4]' /><br />";
                if ($error[cerditCardNr]) {
                    $res .= "</div>";
                }
                
                // Gültig bis
                if ($error[cerditCardDate]) {
                    $res .= "<div class='cmsUserError'>";
                    $res .= "<span class='cmsUser_errorSpan'>".$error[cerditCardDate]."</span><br>";
                }
                $res .= "<span class='cmsBasket_infoSpan cmsBasket_needSpan'>Gültig bis:</span>";
                $res .= "<input type='text' style='width:20px;' value='$payData[creditCardTo_month]' name='basket[payment][creditCardTo_month]' />";
                $res .= "<input type='text' style='width:40px;' value='$payData[creditCardTo_year]' name='basket[payment][creditCardTo_year]' /><br>";
                if ($error[cerditCardDate]) {
                    $res .= "</div>";
                }
                
                // Prüfziffer
                if ($error[creditCardCode]) {
                    $res .= "<div class='cmsUserError'>";
                    $res .= "<span class='cmsUser_errorSpan'>".$error[creditCardCode]."</span><br>";
                }
                $res .= "<span class='cmsBasket_infoSpan cmsBasket_needSpan'>Kreditkarten-Code:</span>";
                $res .= "<input type='text' style='width:30px;' value='$payData[creditCardCode]' name='basket[payment][creditCardCode]' /><br>";
                if ($error[creditCardCode]) {
                    $res .= "</div>";
                }
                
                break;
            
            case "bill":
                $res .= "Nach erfolgreicher Bestellung erhalten Sie eine eMail in der Sie die Details";
                $res .= " zu Ihrer Bestellung und die Rechnung im PDF-Format. <br />";
                break;
                
            default:
                $res .= "Unbekannter Typ($key)<br />";
                break;
        }
        return $res;
    }
    
    
    function show_basket_order($step,$basketData,$contenData,$error,$frameWidth) {
        $this->show_basket_adress($step, $basketData, $contenData, $error,$frameWidth);
        
       //  $this->show_basket_list($step, $basketData, $contenData, $error, $frameWidth);
        $this->show_list($step,$basketData,$contenData,$error,$frameWidth);
        
        $this->show_basket_payment($step,$basketData,$contentData,$error,$frameWidth);
        $this->show_sum($step,$basketData,$contenData,$error,$frameWidth);
    }
    
    function show_steps($step,$basketData,$contenData,$error,$frameWidth) {
        
        div_start("basketSteps","width:".$frameWidth."px;");
        
        // List
        $divName = "basketStep basketStepList basketStepFirst";
        
        switch ($step) {
            case "list"    : $divName .= " basketStepSelected"; break;
            case "confirm" :$divName .= " basketStepInactive"; break;
            default :
                $divName .= " basketStepActive"; 
            
        }        
        div_start($divName,"float:left;");
        $str = "Warenkorb";
        if (strpos($divName,"basketStepActive")) {
            echo ("<input type='submit' class='basketStepButton' name='go_list' value='$str' />");
        } else {
            echo ($str);
        }
        div_end($divName);
        
        
        // Adresse
        $divName = "basketStep basketStepUser";
        switch ($step) {
            case "payment" : $divName .= " basketStepActive"; break;
            case "order"   : $divName .= " basketStepActive"; break;
            case "adress"  : $divName .= " basketStepSelected"; break;            
            default : 
                $divName .= " basketStepInactive";
        }    
        div_start($divName,"float:left;");
        $str = "Adresse";
        if (strpos($divName,"basketStepActive")) {
            echo ("<input type='submit' class='basketStepButton' name='go_adress' value='$str' />");
        } else {
            echo ($str);
        }
        div_end($divName);
        
        // Payment
        $divName = "basketStep basketStepPayment";
        switch ($step) {
            case "payment" : $divName .= " basketStepSelected"; break;
            case "order"   : $divName .= " basketStepActive"; break;
            default : 
                $divName .= " basketStepInactive";
        }    
        div_start($divName,"float:left;");
        $str = "Bezahlung";
        if (strpos($divName,"basketStepActive")) {
            echo ("<input type='submit' class='basketStepButton' name='go_payment' value='$str' />");
        } else {
            echo ($str);
        }
        div_end($divName);
        
         // order
        $divName = "basketStep basketStepOrder";
        switch ($step) {            
            case "order"   : $divName .= " basketStepSelected"; break;          
            default : 
                $divName .= " basketStepInactive";
        }    
        div_start($divName,"float:left;");
        echo ("Bestellen");
        div_end($divName);
        
        // confirm
        $divName = "basketStep basketStepConfirm";
        switch ($step) {
            case "confirm" : $divName .= " basketStepActive"; break;
            default : 
                $divName .= " basketStepInactive";
        }    
        div_start($divName,"float:left;");
        $str = "Bestättigung";
        if (strpos($divName,"basketStepActive")) {
            echo ("<input type='submit' class='basketStepButton' name='go_order' value='$str' />");
        } else {
            echo ($str);
        }
        
        div_end($divName);
        
        div_end("basketSteps","before");
        
    }
    
    function show_list($step,$basketData,$contenData,$error,$frameWidth) {
        if ($step != "list") {
            div_start("basketInfo basketInfo_list");
           
            $this->show_listHeader($step,$basketData,$contenData,$error,$frameWidth);
            
           
            $basketList = $basketData[basketList];
            if (!is_array($basketList)) return 0;
            if (!count($basketList)) return 0;    
            foreach ($basketList as $basketId => $basketItem) { // ($i=0;$i<count($basketList);$i++) {
                // $basketItem = $basketList[$i];
                $this->show_listItem($basketItem,$basketId,$step,$contenData,$error,$frameWidth);
            }   
            
            
            
//            foreach ($adress as $key => $value) {
//                echo ("ADRESS $key = $value <br>");
//            }
            
            // Ändern
            div_start("basketInfo_change");
            echo ("<input class='basketChangeButton' type='submit' value='Artikel ändern' name='go_list'>");
            div_end("basketInfo_change");
           
            div_end("basketInfo basketInfo_list");
            
            return 0;
            
            
            
            
            
            
            return 0;
        }
        
        
        $basketList = $basketData[basketList];
        if (!is_array($basketList)) return 0;
        if (!count($basketList)) return 0;    
        
        $this->show_listHeader($step,$basketData,$contenData,$error,$frameWidth);
        if ($_POST) {
            $change = 0;
            if ($_POST[basketChange]) {
                // echo ("Change Value <br>");
                foreach ($_POST[basketChange] as $key => $value) {

                    $aktValue = $basketList[$key][amount];
                    $newValue = $value[amount];
                    if ($newValue != $aktValue) {
                        // echo "Change for $key from $aktValue => $newValue <br>";
                        $change = 1;
                        if ($newValue >= 1) {
                            $basketList[$key][amount] = $newValue;
                        } else {
                            unset($basketList[$key]);
                        }
                        $out = "Anzahl wurde geändert";
                    }
                    //echo (" --> $key = $value (akt = $aktValue) <br>");
                    //show_array($value);

                }
                // show_array($_POST[basketChange]);
            }

            // show_array($_POST);
            if ($_POST[basketRemove]) {
                echo ("Change Remove<br />");
                foreach ($_POST[basketRemove] as $removeID => $removeValue) {
                    unset($basketList[$removeID]);
                    $out = "Artikel wurde entfernt";
                    $change = 1;
                    // echo ("-> $removeID $removeValue <br>");
                }
                
            }
            if ($change) {
                cms_infoBox($out);
                $_SESSION[basket][basketList] = $basketList;
                reloadPage($GLOBALS[pageData][name].".php",2);
                return 0;
            }

        }
        
        foreach ($basketList as $basketId => $basketItem) { // ($i=0;$i<count($basketList);$i++) {
            // $basketItem = $basketList[$i];
            $this->show_listItem($basketItem,$basketId,$step,$contenData,$error,$frameWidth);
        }        
        
        
    }
    
    function getConvertRate($currencyType) {
        switch ($currencyType) {
            case "dollar"  : $to = "USD"; break;
            case "franken" : $to = "CHF"; break;
            case "pfund"   : $to = "GBP"; break;
        }
        $factor = 0;
        
        if ($to) {
            $from = 'EUR';
            $url = 'http://finance.yahoo.com/d/quotes.csv?f=l1d1t1&s='.$from.$to.'=X';
            $handle = fopen($url, 'r');

            if ($handle) {
                $result = fgetcsv($handle);
                fclose($handle);
            }
            $factor = $result[0];
            // show_array($result);
            // echo '1 '.$from.' is worth '.$result[0].' '.$to.' Based on data on '.$result[1].' '.$result[2];
        }
        // echo ("Get Currency $currencyType $to $factor <br>");
        return $factor;
    }
    
    function showListData() {
        $res = array();
        $res[abs]    = 10;
        $res[padding]  = 10;
        $res[currency] = array("type"=>"dollar","name"=>"$","komma"=>".","1000"=>",","deci"=>2);
        
        $res[currency] = array("type"=>"euro","name"=>"€","komma"=>",","1000"=>".","deci"=>2);
        
        $res[image]  = array("show"=>1,"width"=>100,"ratio"=>4/3);
        $res[action] = array("show"=>1,"width"=>70);
        $res[single] = array("show"=>1,"width"=>70);
        $res[sum]    = array("show"=>1,"width"=>70);
        return $res;
    }
    
    function show_listHeader($step,$basketData,$contenData,$error,$frameWidth) {
        if ($step != "list") {
            echo ("LIST HEADER <br />");
            return 0;
            
        }
        $showListData = $this->showListData();
      
        $showImage = 0;
        $showAction = 0;
        $showSingle = 0;
        $showSum    = 0;
        
        $padding = $showListData[padding];
        $abs = $showListData[abs];
        if ($showListData[image]) {
            $showImage = 1;
            $imgWidth = $showListData[image][width];
            $imgHeight = $showListData[image][height];
            $imgRatio = $showListData[image][ratio];
        } 
        
        if ($showListData[action]) {
            $showAction = 1;
            $actionWidth = $showListData[action][width];          
        } 
        
        if ($showListData[single]) {
            $showSingle = 1;
            $singleWidth = $showListData[single][width];          
        } 
        
        if ($showListData[sum]) {
            $showSum = 1;
            $sumWidth = $showListData[sum][width];          
        } 
        
        $deci = $showListData[currency][deci];
        if (!$deci) $deci = 5;
        
        $komma = $showListData[currency][komma];
        if (!$komma) $komma = "-";
        
        $deci1000 = $showListData[currency]["1000"];
        if (!$deci1000) $deci1000 = "*";
        
        $currencyType = $showListData[currency][type];
        if (!$currencyType) $currencyType = "euro";
        $currency = $showListData[currency][name];
        if (!$currency) $currency = "EUR";
        
         
        
        
        $space = $frameWidth - (2 * $padding);
        
        div_start("basketListHeader","width:".$space."px;");
        
       
        
        if ($showImage) {
            $space = $space - $imgWidth - $abs;
            div_start("basketHeadItem basketHead_image","width:".$imgWidth."px;margin-right:".$abs."px;");
            echo ("Bild");
            div_end("basketHeadItem basketHead_image");            
        }
        
        $space = $space - $abs;
        if ($showAction) $space = $space - $actionWidth - $abs;
        if ($showSingle) $space = $space - $singleWidth - $abs;
        if ($showSum) $space = $space - $sumWidth;
        
        
        div_start("basketHeadItem basketHead_data","width:".$space."px;margin-right:".$abs."px;");
        echo ("Bezeichnung");
        div_end("basketHeadItem basketHead_data");   
        
        if ($showAction) {
            div_start("basketHeadItem basketHead_action","width:".$actionWidth."px;margin-right:".$abs."px;");
            echo ("Aktion");
            div_end("basketHeadItem basketHead_action");   
        }
        
        if ($showSingle) {
            div_start("basketHeadItem basketHead_sum","width:".$sumWidth."px;margin-right:".$abs."px;text-align:right;");
            echo("Preis");
            div_end("basketHeadItem basketHead_sum");   
        }
        
        if ($showSum) {
            div_start("basketHeadItem basketHead_sum","width:".$sumWidth."px;text-align:right;");
            echo ("Summe");
            div_end("basketHeadItem basketHead_sum");   
        }
        
        
        div_end("basketListHeader","before");
    }
    
    
    
    function show_listItem($basketItem,$basketId,$step,$contenData,$error,$frameWidth) {
        if ($step != "list") {
            echo ("Item : $basketItem <br>");
            foreach($basketItem as $key => $value )
                echo ("$key = $value || ");
            
            echo ("<br> basketId :$basketId <br>");
            return 0;
        }
        
        $showListData = $this->showListData();
        
        
        $showImage = 0;
        $showAction = 0;
        $showSingle = 0;
        $showSum    = 0;
        
        $padding = $showListData[padding];
        $abs = $showListData[abs];
        if ($showListData[image]) {
            $showImage = 1;
            $imaWidth = $showListData[image][width];
            $imgHeight = $showListData[image][height];
            $imgRatio = $showListData[image][ratio];
        } 
        
        if ($showListData[action]) {
            $showAction = 1;
            $actionWidth = $showListData[action][width];          
        } 
        
        if ($showListData[single]) {
            $showSingle = 1;
            $singleWidth = $showListData[single][width];          
        } 
        
        if ($showListData[sum]) {
            $showSum = 1;
            $sumWidth = $showListData[sum][width];          
        } 
        
        
        
        
        $deci = $showListData[currency][deci];
        if (!$deci) $deci = 2;
        
        $komma = $showListData[currency][komma];
        if (!$komma) $komma = ",";
        
        $deci1000 = $showListData[currency]["1000"];
        if (!$deci1000) $deci1000 = ".";
        
        $currencyType = $showListData[currency][type];
        if (!$currencyType) $currencyType = "euro";
        $currency = $showListData[currency][name];
        if (!$currency) $currency = "$";
        
              
        
        $space = $frameWidth - (2 * $padding);
        // get Data
        $dataSource = $basketItem[dataSource];
        $dataId = $basketItem[dataId];
        if (!$dataSource) {
            //echo ("<h1>DataSource = $dataSource </h1>");
            list($dataSource,$dataId) = explode("_",$basketId);
            //echo ("GET $dataSource $dataId <br>");
            
            
        }
        switch ($dataSource) {
            case "product" :
                $itemData = cmsProduct_getById($dataId);
                // show_array($itemData);
                break;
            default :
        }
        
        
        div_start("basketListLine","width:".$space."px;");
        
        if ($showImage) {
            $imgWidth = 100;
            $ratio = 4 / 3;
            $imgHeight = floor($imgWidth / $ratio);
            $img = cmsWireframe_image($imgWidth,$imgHeight);
            $imgStr = "<img src='$img' class='noBorder' >";
            $space = $space - $imgWidth - $abs;
            div_start("basketLineItem basketItem_image","width:".$imgWidth."px;margin-right:".$abs."px;");
            echo ($imgStr);
            div_end("basketLineItem basketItem_image");            
        }
        
        $value = $basketItem[value];
        if ($currencyType != "euro") {
            $factor = $this->getConvertRate($currencyType);
            if ($factor) $value = $value * $factor;
            else {
                $currencyType = "EUR";
                $currency = "€";
            }                            
        }
        
        $amount = $basketItem[amount];
        $shipping = $basketItem[shipping];
        
        $space = $space - $abs;
        if ($showAction) $space = $space - $actionWidth - $abs;
        if ($showSingle) $space = $space - $singleWidth - $abs;
        if ($showSum) $space = $space - $sumWidth;
        
        div_start("basketLineItem basketItem_data","width:".$space."px;margin-right:".$abs."px;");
        if (is_array($itemData)) {
            echo ("<h3>$itemData[name]</h3>");
            if ($itemData[subName]) echo ($itemData[subName]."<br />");            
        } 
        div_end("basketLineItem basketItem_data");   
        
        if ($showAction) {
            div_start("basketLineItem basketItem_action","width:".$actionWidth."px;margin-right:".$abs."px;");
            echo ("<input type='text' class='basketAmountInput' onChange='submit()' value='$basketItem[amount]' name='basketChange[$basketId][amount]' />");
            echo ("<input type='submit' class='basketRemoveButton' value='rem' name='basketRemove[$basketId][remove]' />");
            div_end("basketLineItem basketItem_action");   
        }
        
        if ($showSingle) {
            div_start("basketLineItem basketItem_sum","width:".$sumWidth."px;margin-right:".$abs."px;text-align:right;");
            echo (number_format($value,$deci,$komma,$deci1000)." $currency");
            div_end("basketLineItem basketItem_sum");   
        }
        
        if ($showSum) {
            div_start("basketLineItem basketItem_sum","width:".$sumWidth."px;text-align:right;");
            echo (number_format($value*$amount,$deci,$komma,$deci1000)." $currency");
            div_end("basketLineItem basketItem_sum");   
        }
        
        
        div_end("basketListLine","before");
    }
    
    function show_sum($step,$basketData,$contenData,$error,$frameWidth) {
        $showListData = $this->showListData();
      
        $showImage = 0;
        $showAction = 0;
        $showSingle = 0;
        $showSum    = 0;
        
        $padding = $showListData[padding];
        $abs = $showListData[abs];
        
        if ($showListData[sum]) {
            $showSum = 1;
            $sumWidth = $showListData[sum][width];          
        } 
        
        $deci = $showListData[currency][deci];
        if (!$deci) $deci = 5;
        
        $komma = $showListData[currency][komma];
        if (!$komma) $komma = "-";
        
        $deci1000 = $showListData[currency]["1000"];
        if (!$deci1000) $deci1000 = "*";
        
        $currencyType = $showListData[currency][type];
        if (!$currencyType) $currencyType = "euro";
        $currency = $showListData[currency][name];
        if (!$currency) $currency = "EUR";
        
        if ($currencyType != "euro") {
            $factor = $this->getConvertRate($currencyType);
            // echo ("FAKTOR = $factor <br>");
            if (!$factor) {
                $factor = 1.0;
                $currencyType = "euro";
                $currency = "€";
            }
        }
        
        
        $space = $frameWidth - (2 * $padding);
        div_start("basketListSumme","width:".$space."px;");
        // show_array($basketData);
        
        $right = $sumWidth;
        $left = $space - $sumWidth - $abs;
        
        div_start("basketSumLine");
        div_start("basketSumLine_left","width:".$left."px;margin-right:".$abs."px;");
        echo ("Summe");
        div_end("basketSumLine_left");
        
        div_start("basketSumLine_right","width:".$right."px;");
        $value = $basketData[value];
        if ($currencyType != "euro") {
            $value = $value*$factor;
        }
        echo (number_format($value,$deci,$komma,$deci1000)." $currency");
        div_end("basketSumLine_right");
        div_end("basketSumLine","before");
        
        
        
        // Shipping
        div_start("basketSumLine");
        div_start("basketSumLine_left","width:".$left."px;margin-right:".$abs."px;");
        echo ("Versand");
        div_end("basketSumLine_left");
        
        div_start("basketSumLine_right","width:".$right."px;");
        $shipping = $basketData[shipping];
        if ($currencyType != "euro") {
            $shipping = $shipping*$factor;
        }
        echo (number_format($shipping,$deci,$komma,$deci1000)." $currency");
        div_end("basketSumLine_right");
        div_end("basketSumLine","before");
        
        
        // Gesamt
        div_start("basketSumLine");
        div_start("basketSumLine_left","width:".$left."px;margin-right:".$abs."px;");
        echo ("Gesamt");
        div_end("basketSumLine_left");
        
        div_start("basketSumLine_right","width:".$right."px;");
        $total = $value + $shipping;
        echo (number_format($total,$deci,$komma,$deci1000)." $currency");
        div_end("basketSumLine_right");
        div_end("basketSumLine","before");
        
        div_end("basketListSumme","before");
        
        
    }
    
    
    function show_basket_adress($step,$basketData,$contenData,$error,$frameWidth) {
        
        if ($step != "adress") {
            $adress = $_SESSION[basket][adress];
            
            div_start("basketInfo basketInfo_adress");
            $leftWidth = floor(($frameWidth - 10) / 2);
            $rightWidth = $frameWidth - $leftWidth - 10;
            div_start("basketInfo_LR","width:".$frameWidth."px;margin-bottom:10px;");
            div_start("basketInfo_left","display:inline-block;float:left;margin-right:10px;width:".$leftWidth."px;");
            echo ("<h3>Adresse</h3>");
            
            echo (cmsUser_getSalut($adress[salut])."<br />");
            
            echo ($adress[vName]." ".$adress[nName]."<br />");
            if ($adress[company]) {
                echo ($adress[company]."<br />");
            }
            
            echo ($adress[street]." ".$adress[streetNr]."<br />");
            
            echo ($adress[plz]." ".$adress[city]."<br />");
            
            if ($adress[country]) {
                echo ($adress[country]."<br />");
            }
            
            div_end("basketInfo_left");
            
            div_start("basketInfo_right","display:inline-block;float:left;width:".$rightWidth."px;");
            echo ("<h3>Lieferadresse</h3>");
            
            div_end("basketInfo_right");
            
            div_end("basketInfo_LR","before");
            
            echo ("<h3>Kontaktdaten </h3>");
            if ($adress[email]) {
                echo (span_text_str("eMail:",100).$adress[email]."<br />");
            }

            if ($adress[phone]) {
                 echo (span_text_str("Telefon:",100));
                 foreach ($adress[phone] as $key => $value) {
                     echo ("$value ");
                 }
                 echo ("<br />");                
            }
            
            if ($adress[fax]) {
                 echo (span_text_str("Telefax:",100));
                 foreach ($adress[fax] as $key => $value) {
                     echo ("$value ");
                 }
                 echo ("<br />");                
            }
            
             if ($adress[mobil]) {
                 echo (span_text_str("Mobil:",100));
                 foreach ($adress[mobil] as $key => $value) {
                     echo ("$value ");
                 }
                 echo ("<br />");                
            }
          
            // Ändern
            div_start("basketInfo_change");
            echo ("<input class='basketChangeButton' type='submit' value='Adresse ändern' name='go_adress'>");
            div_end("basketInfo_change");
           
            div_end("basketInfo basketInfo_adress");
            
            return 0;
        }

        $adress = $_SESSION[basket][adress];
        $shippingAdress = $_SESSION[basket][shippingAdress];
        if ($_POST[basket]) {
            
            
            if (is_array($_POST[basket][shippingAdress])) {
                $shippingAdress = $_POST[basket][shippingAdress];
                show_array($shippingAdress);
                
            }
        }
        
        
        $this->show_adress($adress,$step,$basketData,$contenData,$error,$frameWidth);
        
        
       
        
        
       //  $this->show_adress_shipping($shippingAdress,$step,$basketData,$contenData,$frameWidth);
        // $this->show_steps($step,$basketData,$contenData,$frameWidth);
        // $this->show_list($step,$basketData,$contenData,$frameWidth);
        // $this->show_sum($step,$basketData,$contenData,$frameWidth);
        // $this->show_button($step,$basketData,$contenData,$frameWidth);
    }
   
    
    function checkAdress($adressType,$adress,$data) {
        if (!is_array($adress)) $adress = array();
        // echo ("CHECK ADRESS fOR $adressType $adress anz=".count($adress)." <br>");
        switch ($adressType) {
            case "adress" :
                $showList_adress = $this->get_showList($data);
                
                $error = $this->userData_check($adress,$showList_adress,"edit");
                break;
                
            case "deliveryAdress" :
                $showList_delivery = $this->get_delivery_showList($data);
                $error = $this->userData_check($adress,$showList_delivery,"edit");
                break;
            default : 
                echo ("<h1> unkown Adress-Type $adressType in CheckAdress </h1>");  
                return 0;
        }
        
        if (is_array($error) AND count($error)) {
//            echo ("Fehler in check $adressType ".count($error)."<br>");
//            foreach($error as $key => $value) {
//                echo ("ERROR in $adressType $key = $value <br>");
//            }
            return $error;
        }
        return 1;
        
        $adress_ok = 0;
        $deliver_ok = 0;


        $showList_adress =  $this->get_showList($data);
        $adress = $value[basket][adress];
        if (!is_array($adress)) $adress = array();
        $errorList_adress = $this->userData_check($adress,$showList_adress,"edit");
        if ($errorList_adress === 0) $adress_ok = 1;
        
        $showList_delivery = $this->get_delivery_showList($data);
        // GET FROM SESSION
        $delivery = $value[basket][delivery];
        if (!is_array($delivery)) $delivery = array();
        $errorList_delivery = $this->userData_check($delivery,$showList_delivery,"edit");
        if ($errorList_delivery === 0) $deliver_ok = 1;

        if ($adress_ok AND $deliver_ok) return 1;

        if (!$adress_ok) {
            echo ("Anzahl Fehler in Adresse:".count($errorList)." -->");
            echo ("<br>");
            //foreach ($errorList as $key => $value) echo (" $key, ");
            foreach ($errorList_adress as $key => $value) {
                echo (" $key = $value ist $adress[$key] <br>");
            }
            echo ("<br>");
        }
        if (!$deliver_ok) {
            echo ("Anzahl Fehler in Liefer-Adresse:".count($errorList_delivery)." -->");
            foreach ($errorList_delivery as $key => $value) echo (" $key, ");
            echo ("<br>");
        }
        return 0;
    }

    function get_StyleData() {
        $infoWidth = 200;
        $dataWidth = 300;
        $infoStyle = "width:".($infoWidth-10)."px;text-align:right;margin-right:10px;";
        $dataStyle = "width:".$dataWidth."px;text-align:left;margin-right:10px;font-weight:bold;";

        $res = array();
        $res[infoStyle] = array("class"=>"cmsUser_infoSpan","style"=>"css",);
        $res[dataStyle] = array("class"=>"cmsUser_dataSpan","style"=>"css"); // $dataStyle;
        $res[errorStyle] = array("class"=>"cmsUser_errorSpan","style"=>"padding-left:200px;"); // $dataStyle;
        return $res;

    }

    function get_showList($data) {
        $showList = array();
        $editList = $this->editContent_adress_userShowList();
        foreach ($data as $key => $value) {
            //  echo("$key = $value <br>");
            if (substr($key,0,5)== "show_") {
                $showName = substr($key,5);
                $show = $value;
                if ($show) $showList[$showName] = array();
                $need = $data["need_".$showName];
                if ($need) $showList[$showName][need] = $need;
                $view = $data[$showName."_view"];
                if ($view) $showList[$showName][view] = $view;

                if (is_array($editList[$showName])) {
                    $showList[$showName][name] = $editList[$showName][name];
                }
            }
        }
        return $showList;
    }

    function get_delivery_showList($data) {
        $showList = array();
        $editList = $this->editContent_adress_userShowList();
        foreach ($data as $key => $value) {
            // echo("$key = $value <br>");
            if (substr($key,0,9)== "delivery_") {
                $showName = substr($key,9);
                $show = $value;
                if ($show) $showList[$showName] = array();
                $need = $data["need_delivery_".$showName];
                // echo ("$showName NEED= $need <br>");
                if ($need) $showList[$showName][need] = $need;
                $view = $data[$showName."_view"];
                if ($view) $showList[$showName][view] = $view;

                if (is_array($editList[$showName])) {
                    $showList[$showName][name] = $editList[$showName][name];
                }
            }
        }
        // show_array($showList);
        return $showList;
    }

    function userData_convert($userData,$showList) {
        $userData = cmsUser_convert($userData,$showList);
        return $userData;
    }

    function userData_check($userData,$showList=array(),$mode="edit") {
        $errorList = cmsUser_checkData($userData,$showList,$mode);
        return $errorList;
    }

    function show_adress($adress,$step,$basketData,$contenData,$error,$frameWidth) {
        $data = $contenData[data];
        if (!is_array($data)) $data = array();
            
        
        $editable = 1;


        ////// ADRESSE                                                        //
        // GET SHOW-LIST
        $showList = $this->get_showList($data);

        // GET FROM SESSION
        $adress = $_SESSION[basket][adress];
        if (!is_array($adress)) {
            $adress = array();

            
            // GET FROM USER-DATA
            $userId = $_SESSION[userId];
            if ($userId) {
                $userData = cmsUser_get(array("id"=>$userId));
                foreach ($userData as $key => $value) {
                    switch ($key) {
                        // case "userName": break;
                        // case "password": break;
                        case "userLevel": break;
                        case "sessionId": break;
                        case "show": break;
                        case "lastLogin": break;
                        case "first_log": break;
                        case "lastMod": break;
                        case "changeLog": break;
                        default :
                            // echo ("USERDATA use $key => $value <br>");
                            if ($value) $adress[$key] = $value;
                    }
                }
            }
        }

        // GET POST DATA
        $_POST_adress= $_POST[basket][adress];
        if (is_array($_POST_adress)) {
            $_POST_adress = $this->userData_convert($_POST_adress,$showList);
            foreach ($_POST_adress as $key => $value) {
                //echo ("POST ADRESS $key = '$value' <br />");
                //if ($value) {
                    $adress[$key] = $value;
                //}
            }
            $_SESSION[basket][adress] = $adress;
        }
        $errorList_adress = $this->userData_check($adress,$showList,"edit");
//        if ($errorList_adress === 0) {
//            echo ("KEINE FEHLER in Adresse <br>");
//        }


        ////////////// DELIVERY DATA                                         ///
        
        
        
        $showList_delivery = $this->get_delivery_showList($data);
        // GET FROM SESSION
        $delivery = $_SESSION[basket][delivery];
        if (!is_array($delivery)) $delivery = array();

        // GET FROM POST
        $_POST_delivery= $_POST[basket][delivery];
        if (is_array($_POST_delivery)) {
            $_POST_delivery = $this->userData_convert($_POST_delivery,$showList_delivery);

            foreach ($_POST[basket][delivery] as $key => $value) {
                // echo ("POST ADRESS $key = '$value' <br />");
                //if ($value) {
                    $delivery[$key] = $value;
                //}
            }
            

            // ADD RESULT TO SESSION
            $_SESSION[basket][delivery] = $delivery;
        }
        // foreach ($delivery as $key => $value ) echo ("deliv $key =$value <br>");
        // $errorList_delivery = $this->userData_check($delivery,$showList_delivery,"edit");
       

        if ($userId) {
            $userData = cmsUser_get(array("id"=>$userId));
            // echo ("USERDATA $userData <br>");
            // show_array($userData);
            $editable = 1;
        }
        // echo ("USER $userId <br>");
        
      
        if (is_array($error)) {
            foreach ($error as $key => $value) {
                echo ("FEHLER <b>$key</b> = $value <br>");
                foreach ($value as $er => $va) {
                    echo (" --> $er $va <br>");
                }
            }
        }
            
        
        
        /// BILLING ADRESSS ///////
        div_start("cmsBasket_adress");
        echo ("<h1>Adresse</h1>");
        $errorAdress = $error[adress];
        if (is_array($errorAdress)) {
            foreach($errorAdress as $key => $value) echo("Fehler in Adresse $key = $value <br>");
        }
        $str = "";
        $dataName = "basket[adress]";
        $styleList = $this->get_StyleData();
        $styleList[dataWidth] = 300;
        $styleList[inputAbs] = 10;

        foreach ($showList as $key => $value) {
            switch ($key) {
                case "name":
                    $value = array("vName"=>$adress[vName],"nName"=>$adress[nName]);
                    break;
                case "street" :
                    $value = array("street"=>$adress[street],"streetNr"=>$adress[streetNr]);
                    break;
                case "city" :
                    $value = array("plz"=>$adress[plz],"city"=>$adress[city]);
                    break;
                default :
                    $value = $adress[$key];
            }

            $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList,$errorAdress);
            $str .= $res;
        }
        echo ($str);
        div_end("cmsBasket_adress");

        
        
        // LIEFERADRESSE
        if ($data[deliveryAdress]) {
            // Lieferadresse ist möglich
            
            $useDeliveryAdress = $value[useDeliveryAdress];
            if ($_POST[basket][adress]) {
                $useDeliveryAdress = $_POST[basket][adress][useDeliveryAdress];
            }
            
            if ($useDeliveryAdress ) $checked = "checked='checked'";
            else $checked = "";
            echo ("<input class='cmsBasketUseDelivery' type='checkbox' value='1' $checked name='basket[adress][useDeliveryAdress]' >Abweichende Lieferadresse verwenden <br />");
            
            
            /// DELIVERY ADRESS ////
            $className = "cmsBasket_delivery"; 
            if (!$useDeliveryAdress) $className .= " cmsBasket_delivery_hidden";
            
            div_start($className);
            echo ("<h1>Lieferadresse</h1>");

            $errorDelivery = $error[delivery];
            if (is_array($errorDelivery)) {
                foreach($errorDelivery as $key => $value) echo("Fehler in DeliverAdresse $key = $value <br>");
            }

            $str = "";
            $dataName = "basket[delivery]";
            $styleList = $this->get_StyleData();
            $styleList[dataWidth] = 300;
            $styleList[inputAbs] = 10;

            foreach ($showList as $key => $value) {
                switch ($key) {
                    case "name":
                        $value = array("vName"=>$delivery[vName],"nName"=>$delivery[nName]);
                        break;
                    case "street" :
                        $value = array("street"=>$delivery[street],"streetNr"=>$delivery[streetNr]);
                        break;
                    case "city" :
                        $value = array("plz"=>$delivery[plz],"city"=>$delivery[city]);
                        break;
                    default :
                        $value = $delivery[$key];
                }

                $res = cmsUser_showInput($key,$value,$dataName,$styleList,$showList_delivery,$errorDelivery);
                $str .= $res;
            }
            echo ($str);
            div_end($className);
        }
        //// END OF DELIVERY ADRESS ///
        return 0;
        
        
        
    }
        
    function show_adress_shipping($shippingAdress,$step,$basketData,$contenData,$frameWidth) {
        $useShipping = 0;
        $data = $contenData[data];
        if (!is_array($data)) $data = array();
        
        if (is_array($shippingAdress)) {
            if ($shippingAdress[on]) $useShipping = 1;
            
        }

        echo ("Lieferadresse:<br />");
        
        $showList = array();
        
        $showList[salut] = "Anrede";
        $showList[vName] = "Vorname";
        $showList[nName] = "Nachname";
        $showList[company] = "Firma";
        $showList[adress] = "Adresse";
        $showList[country] = "Land";
        $showList[phone] = "Telefon";
        $showList[eMail] = "eMail";
        
        
        foreach ($showList as $key => $value )  {
            echo ("<b>STANDARD $key </b>");
            if ($data[$key]) {
                echo (" -> SHOW ");
                if ($data["need_".$key]) {
                    echo ("-> needed ");
                } else {
                    echo "NotNeeded [need_".$key."]";
                }
            }
            echo ("<br>");
        }
        
        foreach ($showList as $key => $value )  {
            echo ("<b>LIEFERAdresse $key </b>");
            if ($data["delivery_".$key]) {
                echo (" -> SHOW ");
                if ($data["delivery_need_".$key]) {
                    echo ("-> needed ");
                } else {
                    echo "NotNeeded [need_".$key."]";
                }
            }
            echo ("<br>");
        }
        
        show_array($contenData[data]);
        
        
        if ($useShipping) $checked = "checked='checked'";
        else $checked = "";
        span_text("Abweichende Lieferadresse:",200);
        echo ("<input type='checkBox' value='1' $checked name='basket[shippingAdress][on]' onChange='submit()' /></br>");

        if ($useShipping) {
            span_text("Anrede:",200);
            echo("<input type='text' value='$shippingAdress[salut]' name='basket[shippingAdress][salut]' /><br />");

            span_text("Vorname / Nachname:",200);
            echo("<input type='text' value='$shippingAdress[vName]' name='basket[shippingAdress][vName]' /> ");
            echo("<input type='text' value='$shippingAdress[nName]' name='basket[shippingAdress][nName]' /><br />");

            span_text("Straße / Hausnummer:",200);
            echo("<input type='text' value='$shippingAdress[street]' name='basket[shippingAdress][street]' /> ");
            echo("<input type='text' value='$shippingAdress[streetNr]' name='basket[shippingAdress][streetNr]' /><br />");

            span_text("PLZ / Ort:",200);
            echo("<input type='text' value='$shippingAdress[plz]' name='basket[shippingAdress][plz]' /> ");
            echo("<input type='text' value='$shippingAdress[city]' name='basket[shippingAdress][city]' /><br />");


            span_text("Country:",200);
            echo("<input type='text' value='$shippingAdress[country]' name='basket[shippingAdress][country]' /> ");
        }
        
    }
        
     
    
    function show_button($step,$basketData,$contenData,$error,$frameWidth) {
        div_start("basketButtons","width:".$frameWidth."px;");
        $buttonList = array();
        switch($step) {
            case "list" :
                $buttonList[back] = array("value"=>"Warenkorb leeren","name"=>"clearBasket");
                $buttonList[forward] = array("value"=>"weiter zur Adresseingabe","name"=>"adress");
                break;
            
            case "adress" :
                $buttonList[back] = array("value"=>"zurück","name"=>"back");
                $buttonList[forward] = array("value"=>"weiter zur Bezahlung","name"=>"payment");
                break;
            
            case "payment" :
                $buttonList[back] = array("value"=>"zurück","name"=>"back");
                $buttonList[forward] = array("value"=>"weiter zur Bestellung","name"=>"order");
                break;
            
            case "order" :
                $buttonList[back] = array("value"=>"zurück","name"=>"back");
                $buttonList[forward] = array("value"=>"Bestellung abschließen","name"=>"confirm");
                break;   
            
            case "confirm" :
                $buttonList[back] = 0;
                $buttonList[forward] = array("value"=>"Startseite","name"=>"finish");
                break;   
        }
        
        
        // forward 
        $forwardValue = $buttonList[forward];
        if (is_array($forwardValue)) {
            div_start("basketButton_right");
            echo ("<input type='submit' class='mainInputButton' name='$forwardValue[name]' value='$forwardValue[value]' />");
            div_end("basketButton_right");
        }
        
        
        // back Button
        $backValue = $buttonList[back];
        if (is_array($backValue)) {
            div_start("basketButton_left");
            echo ("<input type='submit' class='mainInputButton mainSecond' name='$backValue[name]' value='$backValue[value]' />");
            div_end("basketButton_left");
        }

        div_start("basketButton_center");
        echo ("NÜSCHT");
        div_end("basketButton_center");

       
        div_end("basketButtons","before");
    }
    
    
    
    
    function basketList() {
        $res = cmsBasket_getList();
        return $res;
    }
    
    
   
    
    function basket_editContent($editContent, $frameWidth) {
        $res = array();
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        
        
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "basket";
        $add = array();
        $add[text] = "Anzeige";
        $add[input] = $this->basket_selectView($viewMode,"editContent[data][viewMode]",null,array("empty"=>"Anzeige wählen","submit"=>1));
        $res[] = $add;
        
        switch ($viewMode) {
            case "info" :
                $addList = $this->editContent_info($data,$frameWidth);
                for ($i=0;$i<count($addList);$i++) {
                    $res[] = $addList[$i];
                }             
                break;
            case "basket" :
                $addList = $this->editContent_basket($data,$frameWidth);
                foreach ($addList as $target => $addList) {
                    if (!is_array($res[$target])) $res[$target] = array();
                
                    for ($i=0;$i<count($addList);$i++) {
                        $res[$target][] = $addList[$i];
                    } 
                }
                break;
        }
        return $res;
    }
    
    
    function editContent_info($data,$frameWidth) {
        $res = array();
        
        // Items
        $showEmpty = $data[showEmpty];
        $add = array();
        if ($showEmpty) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Leeren Wrenkorb anzeigen";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][showEmpty]' $checked />";
        $res[] = $add;
        
        // Items
        $showItems = $data[showItems];
        $add = array();
        if ($showItems) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Anzahl Produkte anzeigen";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][showItems]' $checked />";
        $res[] = $add;
        
        // Parts
        $showParts = $data[showParts];
        $add = array();
        if ($showParts) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Anzahl Teile anzeigen";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][showParts]' $checked />";
        $res[] = $add;
        
        
         // Value
        $showValue = $data[showValue];
        $add = array();
        if ($showValue) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Warenwert anzeigen";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][showValue]' $checked />";
        $res[] = $add;
        
        // Shipping
        $showShipping = $data[showShipping];
        $add = array();
        if ($showShipping) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Versand anzeigen";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][showShipping]' $checked />";
        $res[] = $add;
        
        return $res;        
    }
    
    function editContent_basket($data,$frameWidth) {
        $res = array();
        
        
        $target = "basket";
        $res[$target] = array();
        // Items
        $showEmpty = $data[showEmpty];
        $add = array();
        if ($showEmpty) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Leeren Warenkorb anzeigen";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][showEmpty]' $checked />";
        $res[$target][] = $add;
        
        
        // Adresss
        $adressEdit = $this->editContent_basket_adress($data,$frameWidth);
        if (is_array($adressEdit)) {
            $target = "adress";
            $res[$target] = array();
            for ($i=0;$i<count($adressEdit);$i++) {
                $res[$target][] = $adressEdit[$i];
            }
        }        
        
        // PAYMENT
        $payEdit = $this->editContent_basket_payment($data,$frameWidth);
        if (is_array($payEdit)) {
            $target = "payment";
            $res[$target] = array();
            for ($i=0;$i<count($payEdit);$i++) {
                $res[$target][] = $payEdit[$i];
            }
        }        
        return $res;
    }
    
    function editContent_adress_userShowList() {
        $res = array();
        
        $res[salut] = array("name"=>"Anrede","need"=>1,"delivery"=>1);
        $res[name] = array("name"=>"Name","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"),"delivery"=>1);
        $res[company] = array("name"=>"Firma","need"=>1,"delivery"=>1); //,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"));
        $res[street] = array("name"=>"Straße","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"),"delivery"=>1);
        $res[city] = array("name"=>"Ort","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder"),"delivery"=>1);
        $res[country] = array("name"=>"Land","need"=>1,"view"=>array("select"=>"Auswahlfeld","text"=>"Textfeld"),"delivery"=>1);
        $res[email] = array("name"=>"eMail","need"=>1,"view"=>array("single"=>"Einfach","check"=>"Mit Wiederholung"));
        $res[password] = array("name"=>"Passwort","need"=>1,"view"=>array("single"=>"Einfach","check"=>"Mit Wiederholung"));
        $res[userName] = array("name"=>"Benutzername","need"=>1,"view"=>array("single"=>"Einfach","check"=>"Mit Wiederholung"));
        
        $res[phone] = array("name"=>"Telefon","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder","all"=>"Drei Felder"));
        $res[fax] = array("name"=>"Telefax","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder","all"=>"Drei Felder"));
        $res[mobil] = array("name"=>"Mobil","need"=>1,"view"=>array("single"=>"Eine Feld","double"=>"Zwei Felder","all"=>"Drei Felder"));

        $res[url] = array("name"=>"Webseite","need"=>1);
        
        return $res;
    }


    function editContent_basket_adress($data,$frameWidth) {

    //function user_edit_userShow($editContent,$frameWidth) {
        $res = array();

        $notRegistrate = $data[notRegistrate];
        $add = array();
        if ($notRegistrate) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Nicht registriete User";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][notRegistrate]' $checked />";
        $res[] = $add;

        $deliveryAdress = $data[deliveryAdress];
        $add = array();
        if ($deliveryAdress) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Lieferadresse";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][deliveryAdress]' $checked />";
        $res[] = $add;


        $showList = $this->editContent_adress_userShowList();
        foreach ($showList as $key => $value) {
            $addData = array();
            $addData[text] = $value[name];

            if ($data["show_".$key]) $checked = "checked='checked'";
            else $checked = "";
            $input = "<input type='checkbox' $checked value='1' name='editContent[data][show_".$key."]' />";


            if ($value[need]) {
                if ($data["need_".$key]) $checked = "checked='checked'";
                else $checked = "";
                $input .= " Benötigt: <input type='checkbox' $checked value='1' name='editContent[data][need_".$key."]' />";
            }

            if (is_array($value[view])) {
                $input .= " Darstellung: ";
                $viewValue = $data[$key."_view"];
                $viewData = array("empty"=>"Darstellung wählen");
                $input .= $this->selectView($viewValue,"editContent[data][".$key."_view]",$value[view],$viewData);
            }

            if ($value[delivery]) {
                $input .= " auch Lieferadresse: ";
                if ($data["delivery_".$key]) $checked = "checked='checked'";
                else $checked = "";
                $input .= "<input type='checkbox' $checked value='1' name='editContent[data][delivery_".$key."]' />";
                $input .= " benötigt: ";
                if ($data["need_delivery_".$key]) $checked = "checked='checked'";
                else $checked = "";
                $input .= "<input type='checkbox' $checked value='1' name='editContent[data][need_delivery_".$key."]' />";
            }




            $addData[input] = $input;

            $res[] = $addData;
        }
        return $res;
   



        $res = array();
        
        $notRegistrate = $data[notRegistrate];
        $add = array();
        if ($notRegistrate) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Nicht registriete User";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][notRegistrate]' $checked />";
        $res[] = $add;
        
        $deliveryAdress = $data[deliveryAdress];
        $add = array();
        if ($deliveryAdress) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Lieferadresse";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][deliveryAdress]' $checked />";
        $res[] = $add;
        
        
        $addData = array();
        $addData["text"] = "Anrede";
        $salut = $data[salut];
        if ($salut) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][salut]' value='1' $checked  />\n";
        $need_salut = $data[need_salut];
        if ($need_salut) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_salut]' value='1' $checked />";
        if ($deliveryAdress AND $salut) {
            $delivery_salut = $data[delivery_salut];
            if ($delivery_salut) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_salut]' value='1' $checked  />\n";
            $delivery_need_salut = $data[delivery_need_salut];
            if ($delivery_need_salut) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_salut]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Vorname";
        $vName = $data[vName];
        if ($vName) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][vName]' value='1' $checked  />\n";
        $need_vName = $data[need_vName];
        if ($need_vName) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_vName]' value='1' $checked />";
        if ($deliveryAdress AND $vName) {
            $delivery_vName = $data[delivery_vName];
            if ($delivery_vName) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_vName]' value='1' $checked  />\n";
            $delivery_need_vName = $data[delivery_need_vName];
            if ($delivery_need_vName) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_vName]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Nachname";
        $nName = $data[nName];
        if ($nName) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][nName]' value='1' $checked  />\n";
        $need_nName = $data[need_nName];
        if ($need_nName) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_nName]' value='1' $checked />";
        if ($deliveryAdress AND $nName) {
            $delivery_nName = $data[delivery_nName];
            if ($delivery_nName) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_nName]' value='1' $checked  />\n";
            $delivery_need_nName = $data[delivery_need_nName];
            if ($delivery_need_nName) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_nName]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;

        
        $addData = array();
        $addData["text"] = "Firma";
        $company = $data[company];
        if ($company) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][company]' value='1' $checked  />\n";
        $need_company = $data[need_company];
        if ($need_company) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_company]' value='1' $checked />";
        if ($deliveryAdress AND $company) {
            $delivery_company = $data[delivery_company];
            if ($delivery_company) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_company]' value='1' $checked  />\n";
            $delivery_need_company = $data[delivery_need_company];
            if ($delivery_need_company) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_company]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = "Adresse";
        $adress = $data[adress];
        if ($adress) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][adress]' value='1' $checked  />\n";
        $need_adress = $data[need_adress];
        if ($need_adress) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_adress]' value='1' $checked />";
        if ($deliveryAdress AND $adress) {
            $delivery_adress = $data[delivery_adress];
            if ($delivery_adress) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_adress]' value='1' $checked  />\n";
            $delivery_need_adress = $data[delivery_need_adress];
            if ($delivery_need_adress) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_adress]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Land";
        $country = $data[country];
        if ($country) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][country]' value='1' $checked  />\n";
        $need_country = $data[need_country];
        if ($need_country) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_country]' value='1' $checked />";
        if ($deliveryAdress AND $country) {
            $delivery_country = $data[delivery_country];
            if ($delivery_country) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_country]' value='1' $checked  />\n";
            $delivery_need_country = $data[delivery_need_country];
            if ($delivery_need_country) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_country]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = "Telefon";
        $phone = $data[phone];
        if ($phone) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][phone]' value='1' $checked  />\n";
        $need_phone = $data[need_phone];
        if ($need_phone) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_phone]' value='1' $checked />";
        if ($deliveryAdress AND $phone) {
            $delivery_phone = $data[delivery_phone];
            if ($delivery_phone) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_phone]' value='1' $checked  />\n";
            $delivery_need_phone = $data[delivery_need_phone];
            if ($delivery_need_phone) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_phone]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = "eMail";
        $eMail = $data[eMail];
        if ($eMail) $checked = "checked='checked'"; else $checked="";
        $input = "<input type='checkbox' name='editContent[data][eMail]' value='1' $checked  />\n";
        $need_eMail = $data[need_eMail];
        if ($need_eMail) $checked = "checked='checked'"; else $checked="";
        $input .= "benötigt: <input type='checkbox' name='editContent[data][need_eMail]' value='1' $checked />";
        if ($deliveryAdress AND $eMail) {
            $delivery_eMail = $data[delivery_eMail];
            if ($delivery_eMail) $checked = "checked='checked'"; else $checked="";
            $input .= "Lieferadresse <input type='checkbox' name='editContent[data][delivery_eMail]' value='1' $checked  />\n";
            $delivery_need_eMail = $data[delivery_need_eMail];
            if ($delivery_need_eMail) $checked = "checked='checked'"; else $checked="";
            $input .= "benötigt: <input type='checkbox' name='editContent[data][delivery_need_eMail]' value='1' $checked />";            
        }
        $addData["input"] = $input;
        $res[] = $addData;
        
        return $res;
        
    }
        
    
    
    function editContent_basket_payment($data,$frameWidth) {
        $res = array();
        
        $payPal = $data[paypal];
        $add = array();
        if ($payPal) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Paypal";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][paypal]' $checked />";
        if ($payPal) {
            $add[input] .= " Rabatt: <input type='text' style='width:50px;' value='$data[paypalRebate]' name='editContent[data][paypalRebate]' $checked />";
            $add[input] .= " Aufschlag: <input type='text' style='width:50px;'value='$data[paypalOntop]' name='editContent[data][paypalOntop]' $checked />";
        }
        $res[] = $add;
        
        
        $prePay = $data[prePay];
        $add = array();
        if ($prePay) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Vorab Überweisung";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][prePay]' $checked />";
        if ($prePay) {
            $add[input] .= " Rabatt: <input type='text' style='width:50px;' value='$data[prePayRebate]' name='editContent[data][prePayRebate]' $checked />";
            $add[input] .= " Aufschlag: <input type='text' style='width:50px;'value='$data[prePayOntop]' name='editContent[data][prePayOntop]' $checked />";
        }
        $res[] = $add;
        
        $onDelivery = $data[onDelivery];
        $add = array();
        if ($onDelivery) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Nachnahme";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][onDelivery]' $checked />";
        if ($onDelivery) {
            $add[input] .= " Rabatt: <input type='text' style='width:50px;' value='$data[onDeliveryRebate]' name='editContent[data][onDeliveryRebate]' $checked />";
            $add[input] .= " Aufschlag: <input type='text' style='width:50px;'value='$data[onDeliveryOntop]' name='editContent[data][onDeliveryOntop]' $checked />";
        }
        $res[] = $add;
        
        $creditcard = $data[creditcard];
        $add = array();
        if ($creditcard) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Kreditkarte";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][creditcard]' $checked />";
        if ($creditcard) {
            $add[input] .= " Rabatt: <input type='text' style='width:50px;' value='$data[creditcardRebate]' name='editContent[data][creditcardRebate]' $checked />";
            $add[input] .= " Aufschlag: <input type='text' style='width:50px;'value='$data[creditcardOntop]' name='editContent[data][creditcardOntop]' $checked />";
        }
        $res[] = $add;
        
        $bill = $data[bill];
        $add = array();
        if ($bill) $checked="checked='checked'";
        else $checked = "";
        $add[text] = "Rechnung";
        $add[input] = "<input type='checkbox' value='1' name='editContent[data][bill]' $checked />";
        if ($bill) {
            $add[input] .= " Rabatt: <input type='text' style='width:50px;' value='$data[billRebate]' name='editContent[data][billRebate]' $checked />";
            $add[input] .= " Aufschlag: <input type='text' style='width:50px;'value='$data[billOntop]' name='editContent[data][billOntop]' $checked />";
        }
        $res[] = $add;
        return $res;
    }
    
    
    function viewMode_getList() {
        $res = array();
        $res[basket] = array("name"=>"Warenkorb");
        $res[info] = array("name"=>"Warenkorb Info");
        return $res;
    }
    
    
    function basket_selectView($code,$dataName,$viewList,$showData) {
        if (!is_array($viewList)) {
            $viewList = $this->viewMode_getList();
        }
        
        $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:100px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Kein Filter";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        foreach ($viewList as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $name = $value[$outValue];
                } else {
                    $name = $value;
                }

                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$name</option>";
            }
        }
        $str.= "</select>";
        return $str;
        
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
    $res = $basketClass->show($contentData,$frameWidth);
    return $res;
}

function cmsType_basket_editContent($editContent,$frameWidth) {
    $basketClass = cmsType_basket_class();
    return $basketClass->basket_editContent($editContent,$frameWidth);
}

function cmsType_basket_addToBasket($addData,$goPage) {
    $basketClass = cmsType_basket_class();
    return $basketClass->addToBasket($addData,$goPage);
}

function cmsType_basket_getItemCount($basketId) {
    $basketClass = cmsType_basket_class();
    return $basketClass->basket_getItemCount($basketId);
}

function cmsType_basket_showItem($basketItem,$showData) {
    $basketClass = cmsType_basket_class();
    return $basketClass->basket_showItem($basketItem,$showData);
}
?>
