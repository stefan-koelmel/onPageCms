<?php // charset:UTF-8
class cmsBasket_base {
    
    function basket_clear() {
        unset($_SESSION[basket]);
    }
    
    function basket_getList() {
        $basketList = $_SESSION[basket][basketList];
        return $basketList;
    }
    
    function basket_getValue() {
        $basketList = $this->basket_getList();
        if (!is_array($basketList)) $basketList = array();
        $basketItems = 0;
        $basketParts = 0;
        $basketValue = 0;
        $basketShipping = 0;


        $shippingList = array("most","eachSingle","each");
        $shippingMode = $shippingList[2];

        foreach ($basketList as $basketId => $basketData) {
            $amount   = $basketData[amount];
            $value    = $basketData[value];
            $shipping = $basketData[shipping];
            // echo ("Add $amount $value $shipping <br>");

            if ($amount) {
                $basketValue = $basketValue + ($amount * $value);
                $basketItems++;
                $basketParts = $basketParts + $amount;

                switch ($shippingMode) {
                    case "most" :
                        if ($shipping > $basketShipping) $basketShipping = $shipping;
                        break;
                    case "eachSingle" :
                        $basketShipping = $basketShipping + $shipping;
                        break;
                    case "each" :
                        $basketShipping = $basketShipping + ($amount * $shipping);                        
                        break;
                }                
            }
        }

        $res = array();
        $res[items] = $basketItems;
        $res[parts] = $basketParts;
        $res[value] = $basketValue;
        $res[shipping] = $basketShipping;

        // show_array($res);
        $res[basketList] = $basketList;
        return $res;
    }

    function basket_getItem($basketId) {
        $basketList = $this->basket_getList();
        if (!is_array($basketList[$basketId])) return 0;
        $basketItem = $basketList[$basketId]; 
        return $basketItem;
    }

    function basket_setItem($basketId,$basketItem) {
        if (!$basketItem) return "noBasketItem";
        if (!is_array($basketItem)) return "noAddItem";


        if (!is_array($_SESSION[basket])) $_SESSION[basket] = array();
        if (!is_array($_SESSION[basket][basketList])) $_SESSION[basket][basketList] = array();

        $_SESSION[basket][basketList][$basketId] = $basketItem;
        return 1;

    }

    function basket_getItemCount($basketId) {
        $basketItem = $this->basket_getItem($basketId);
        if (!is_array($basketItem)) return 0;
        $anz = $basketItem[amount];
        return $anz;    
    }


    function basket_addItem($itemData) {
        // echo ("<h1>cmsBasket_addtem</h1>");
        //show_array($addData);
        //$out = "Artikel wurde dem Warenkorb hinzugefügt <br>";
        // $out .= "Name: $addData[name] Anzahl: $addData[amount] Preis: $addData[value]";

        $basketId = $itemData[basketId];
        $amount = $itemData[amount];
        $name   = $itemData[name];
        $dataSource = $itemData[dataSource];
        $value  = $itemData[value];
        $shipping = $itemData[shipping];

        // echo ("ADD $basketId anz=$amount, name='$name' source='$dataSource' value=$value ship=$shipping <br>");


        $basketItem = $this->basket_getItem($basketId);
        if (is_array($basketItem)) {
            // echo ("exit<br>");
            $basketItem[amount] = $basketItem[amount] + $amount;
        } else {
            $basketItem = $itemData;
        }

        $res = $this->basket_setItem($basketId,$basketItem);
        return $res;
    }


    
    
}


function cmsBasket_class() {
    $basketClass = new cmsBasket_base();
    return $basketClass;
    
    
    if ($GLOBALS[cmsTypes]["cmsType_basket.php"] == "own") $basketClass = new cmsType_basket();
    else $basketClass = new cmsType_basket_base();
    return $basketClass;
}

    function cmsBasket_clearBasket() {
        $basketClass = cmsBasket_class();
        $res = $basketClass->basket_clear();
    }

    function cmsBasket_getList() {
        $basketClass = cmsBasket_class();
        $basketList = $basketClass->basket_getList();
        return $basketList;
    }

    function cmsBasket_getValue() {
        $basketClass = cmsBasket_class();
        $res = $basketClass->basket_getValue();
        return $res;        
    }

    function cmsBasket_getItem($basketId) {
        $basketClass = cmsBasket_class();
        $res = $basketClass->basket_getItem($basketId);
        return $res;
    }
    
    function cmsBasket_getItemCount($basketId) {
        $basketClass = cmsBasket_class();
        $res = $basketClass->basket_getItemCount($basketId);
        return $res;
    }

    function cmsBasket_addItem($itemData) {
        $basketClass = cmsBasket_class();
        $res = $basketClass->basket_addItem($itemData);
        return $res;
    }

?>
