<?php // charset:UTF-8

class cmsType_skCom_base extends cmsClass_content_show {
    function getName (){
        return "sk.com";        
    }
    
    function contentType_show() {
        $viewMode = $this->contentData[data][viewMode];
        switch ($viewMode) {
            case "projectList" : $this->sk_projectList_show(); break;
            default :
                echo ("<h1>Stefan-Koelmel.com");
        }
    }
    
    function sk_projectList_show() {
        $frameWidth = $this->frameWidth;
        
        div_start("skCom","width:".$frameWidth."px;");
        
        // define Image Out
        $imgWidth = 200;
        $imgRatio = 4 / 3;
        $imgHeight = intval($imgWidth / $imgRatio);
      
        $showData = array();
        $showData[ratio] = $imgRatio;
        $showData[crop] = 1;
        $showData[vAlign] = "top";
        $showData[hAlign] = "left";
        $showData[imageHeight] = "auto";
        $showData[frameWidth] = $imgWidth;
        $showData[frameHeight] = $imgHeight;
        $showData[resize] = 0;

       
        $filter = array("mainCat"=>1);
        
        $customer_list = array();
        $customer_nr   = 0;
        $dealer_list   = array();
        $dealer_nr     = 0;
        $category_list = array();
        $catList = cmsCategory_getList($filter, $sort, "assoIdList");  
        foreach ($catList as $catId => $catData) {            
            $category_list["$catId"] = array("name"=>$catData[name],"project"=>array(),"customer"=>array(),"dealer"=>array());
        }
        
        $pageName = $this->pageClass->pageData[name].".php";
        echo ("<div class='skHelp'></div>");
        
        $filter = array("show"=>1);        
        $projectList = cmsProject_getList($filter, $sort);
        
        $projOut = "<div style='display:inline-block;width:100%;'> ";
        $projOut .= "<span class='skTitle'>Projekte</span><br />";
        
        foreach($projectList as $projNr => $projData ) {
            $projId   = $projData[id];
            $projName = $projData[name];
            $dealer   = $projData[dealer];
            $customer = $projData[customer];
            $image    = $projData[image];
            $category = $projData[category];
            $myCats   = explode("|",$category);
            
            // echo ("$projName $customer <br>");
            
            // ADD TO CATEGORY 
            if ($category) {
                for ($i=1;$i<count($myCats)-1;$i++) {
                    $cat = $myCats[$i];   
                    $category_list[$cat][project][$projId] = $projName;
                }  
            }
            
            // ADD TU CUSTOMER
            $customerStr = "";
            $custNr = 0;
            if ($customer) {
                //neuer Kunde
                if (!is_array($customer_list[$customer])) {
                    $customer_nr++;
                    $customer_list[$customer] = array();
                    $customer_list[$customer][nr] = $customer_nr;
                    $customer_list[$customer][project] = array();
                    $customer_list[$customer][category] = array(); 
                    $customer_list[$customer][dealer] = array(); 
                    $custNr = $customer_nr;
                    
                } else {
                    // $customerStr = "customer:".$customer_list[$customer][nr];
                    $custNr = $customer_list[$customer][nr];
                }
                $customerStr = "customer:".$custNr;
                // add Project
                $customer_list[$customer][project][$projId] = $projName;                
                
                // add Category
                for ($i=1;$i<count($myCats)-1;$i++) {
                    $cat = $myCats[$i];                    
                    $customer_list[$customer][category][$cat]++;
                    
                    // set To Category
                    $category_list[$cat][customer][$custNr] = $customer;
                }                  
            }
            
            
            // ADD TO Dealer
            $dealerStr = "";
            if ($dealer) {
                //neuer Kunde
                if (!is_array($dealer_list[$dealer])) {
                    $dealer_nr++;
                    $dealer_list[$dealer] = array();
                    $dealer_list[$dealer][nr] = $dealer_nr;
                    $dealer_list[$dealer][project] = array();
                    $dealer_list[$dealer][category] = array();  
                    $dealer_list[$dealer][customer] = array(); 
                    $dealNr = $dealer_nr;
                } else {
                    $dealNr = $dealer_list[$dealer][nr];
                }
                
                // add Project
                $dealer_list[$dealer][project][$projId] = $projName;                
                
                // add customer
                if ($custNr) {
                    $dealer_list[$dealer][customer][$custNr] = $customer;
                }
                
                if ($customer) $customer_list[$customer][dealer][$dealNr] = $dealer;
                
                // add Category
                for ($i=1;$i<count($myCats)-1;$i++) {
                    $cat = $myCats[$i];                    
                    $dealer_list[$dealer][category][$cat]++;
                    
                    $category_list[$cat][dealer][$dealNr] = $dealer;
                }   
                $dealerStr = "dealer:".$dealNr;
            }
            
            
            $link = "";
            if (count($myCats)>=1) {
                if ($link) $link.="&";
                else $link.= "?";
                $link .= "category=".$myCats[1];
            }
            if ($projId) {
                if ($link) $link.="&";
                else $link.= "?";
                $link .= "project=".$projId;
            }
            $link = $pageName.$link;
           
            $projOut .= "<div id='skProject_$projId' class='skItem skProject' style='' >";
            $projOut .= "<a class='skLink' href='$link' >LINK</a>";
           
            $dataStr = "";
            $projStr = "";
        
            $catStr = "";
            for ($i=1;$i<count($myCats)-1;$i++) {
                $cat = $myCats[$i];  
                if ($catStr) $catStr .= ",";
                $catStr.= $myCats[$i];
            }
            if ($catStr) $catStr = "category:".$catStr;
            
            
            if ($projStr)      { if ($dataStr) $dataStr.="|"; $dataStr .= $projStr; }
            if ($catStr)       { if ($dataStr) $dataStr.="|"; $dataStr .= $catStr; }
            if ($customerStr)  { if ($dataStr) $dataStr.="|"; $dataStr .= $customerStr; }
            if ($dealerStr)    { if ($dataStr) $dataStr.="|"; $dataStr .= $dealerStr; }
            
            // Image
            if ($this->wireframeState) {
                $imgText = "Bild von Projekt ".$projId;
                $imgStr = $this->text_wireImage($imgWidth, $imgHeight,$imgText,$wireInfo);                
            } else {
                $imageList = explode("|",$image);
                if (count($imageList)>= 1) $imageId = $imageList[1];
                if ($imageId) {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    $imgStr = cmsImage_showImage($imageData, $imgWidth, $showData);
                }
            }
            
            $projOut .= "<div class='skData' >$dataStr</div>";
            // $projOut .= "dim = $imgWidth / $imgHeight px <br>";
            $projOut .= $imgStr;
            
            if ($this->wireframeState) $projOut .= "Projekt ".$projId;
            else $projOut .= $projName;
            $projOut .= "</div>";         
        }
        $projOut .= "</div>";
       
        $catOut = $this->sk_projList_category_out($category_list);
        
        $customerOut = $this->sk_projList_customer_out($customer_list);
        
        $dealerOut = $this->sk_projList_dealer_out($dealer_list);
       
        if ($this->wireframeState) { 
            echo ($projOut);
            echo ($catOut);
            echo ($customerOut);
            echo ($dealerOut);
        } else {
            $projWidth = 620;
            $projAbs = 10;
            $rightWidth = $frameWidth - $projAbs - $projWidth -20;
            
            echo ("<div class='skProject_Frame' style='width:".($projAbs+$projWidth)."px;' >");
            echo ("$projOut");
            echo ("</div>");
            
            echo ("<div  class='skData_Frame'style='width:".$rightWidth."px;' >");
            echo ($catOut);
            echo ($customerOut);
            echo ($dealerOut);
            echo ("</div>");
            
            
            
        }
        
        div_end("skCom","before");
    }
    
    
    function sk_projList_category_out($category_list) {
        $catOut = "<div style='display:inline-block;width:100%;'> ";
        $catOut .= "<span class='skTitle'>Kategorien</span><br />";
        foreach ($category_list as $catId => $catData) {
            $catOut .= "<div id='skCategory_$catId' class='skItem skCategory'>";
            $catStr = "";
            $projStr = "";
            foreach ($catData[project] as $projId => $value) {
                if ($projStr) $projStr .= ","; 
                $projStr .= $projId;
            }
            if ($projStr) $projStr = "project:".$projStr;
            
            $customerStr = "";
            foreach ($catData[customer] as $custNr => $value) {
                if ($customerStr) $customerStr .= ","; 
                $customerStr .= $custNr;
            }
            if ($customerStr) $customerStr = "customer:".$customerStr;
            
            $dealerStr = "";
            foreach ($catData[dealer] as $dealerNr => $value) {
                if ($dealerStr) $dealerStr .= ","; 
                $dealerStr .= $dealerNr;
            }
            if ($dealerStr) $dealerStr = "dealer:".$dealerStr;
            
            $dataStr = "";
            if ($projStr)     { if ($dataStr) $dataStr.="|"; $dataStr .= $projStr; }
            if ($catStr)      { if ($dataStr) $dataStr.="|"; $dataStr .= $catStr; }
            if ($customerStr) { if ($dataStr) $dataStr.="|"; $dataStr .= $customerStr; }
            if ($dealerStr) { if ($dataStr) $dataStr.="|"; $dataStr .= $dealerStr; }
           
            
            $catOut .= "<div class='skData' >$dataStr</div>";
            if ($this->wireframeState) $catOut .=  "Kategorie ".$catId;
            else $catOut .= $catData[name];
            $catOut .= "</div>";         
        }
        $catOut .= "</div>";
        return $catOut;
    }
    function sk_projList_customer_out($customer_list) {
        $customerOut .= "<div style='display:inline-block;width:100%;'>";
        $customerOut .= "<span class='skTitle'>Kunden</span><br />";
        foreach ($customer_list as $customerName => $customerData) {
            $customerNr = $customerData[nr];
            $customerOut .="<div id='skCustomer_$customerNr' class='skItem skCustomer'>";
            $customerStr = "";
            
            $projStr = "";
            if (is_array($customerData[project])) foreach ($customerData[project] as $projId => $value) {
                if ($projStr) $projStr .= ","; 
                $projStr .= $projId;
            }
            if ($projStr) $projStr = "project:".$projStr;
            
            $catStr = "";
            if (is_array($customerData[category])) foreach ($customerData[category] as $catId => $value) {
                if ($catStr) $catStr .= ","; 
                $catStr .= $catId;
            }
            if ($catStr) $catStr = "category:".$catStr;
            
            $dealerStr = "";
            foreach ($customerData[dealer] as $dealerNr => $value) {
                if ($dealerStr) $dealerStr .= ","; 
                $dealerStr .= $dealerNr;
            }
            if ($dealerStr) $dealerStr = "dealer:".$dealerStr;
            
            $dataStr = "";
            if ($projStr)     { if ($dataStr) $dataStr.="|"; $dataStr .= $projStr; }
            if ($catStr)      { if ($dataStr) $dataStr.="|"; $dataStr .= $catStr; }
            if ($customerStr) { if ($dataStr) $dataStr.="|"; $dataStr .= $customerStr; }
            if ($dealerStr) { if ($dataStr) $dataStr.="|"; $dataStr .= $dealerStr; }
           
            $customerOut .="<div class='skData' >$dataStr</div>";
            if ($this->wireframeState) $customerOut .=  "Kunde ".$customerNr;
            else $customerOut .=$customerName;
            $customerOut .="</div>";         
        }
        $customerOut .="</div>";
        return $customerOut;
    }
    
    function sk_projList_dealer_out($dealer_list) {
        $dealerOut = "<div style='display:inline-block;width:100%;'>";
        $dealerOut .= "<span class='skTitle'>Auftraggeber</span><br />";
        foreach ($dealer_list as $dealerName => $dealerData) {
            $dealerNr = $dealerData[nr];
            $dealerOut .= "<div id='skDealer_$dealerNr' class='skItem skDealer'>";
            
            $projStr = "";
            foreach ($dealerData[project] as $projId => $value) {
                if ($projStr) $projStr .= ","; 
                $projStr .= $projId;
            }
            if ($projStr) $projStr = "project:".$projStr;
            
            $catStr = "";
            foreach ($dealerData[category] as $catId => $value) {
                if ($catStr) $catStr .= ","; 
                $catStr .= $catId;
            }
            if ($catStr) $catStr = "category:".$catStr;
            
            
            $customerStr = "";
            foreach ($dealerData[customer] as $custNr => $value) {
                if ($customerStr) $customerStr .= ","; 
                $customerStr .= $custNr;
            }
            if ($customerStr) $customerStr = "customer:".$customerStr;
            
            
            $dataStr = "";
            if ($projStr)     { if ($dataStr) $dataStr.="|"; $dataStr .= $projStr; }
            if ($catStr)      { if ($dataStr) $dataStr.="|"; $dataStr .= $catStr; }
            if ($customerStr) { if ($dataStr) $dataStr.="|"; $dataStr .= $customerStr; }
           
            $dealerOut .= "<div class='skData' >$dataStr</div>";
            if ($this->wireframeState) $dealerOut .= "Auftraggeber ".$dealerNr;
            else $dealerOut .= $dealerName;
            $dealerOut .= "</div>";
        }
        $dealerOut .= "</div>";
        return $dealerOut;
    }

    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth = $this->frameWidth;

        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();
        
        $cType = "skCom";
        $res = array();
        $res[$cType][showName] = $this->lga($cType,"contentTab");
        $res[$cType][showTab] = "Simple";
        
        $lgCode = "contentType_skCom";
        
        
        
        
        // viewMode
        $viewList = array("projectList"=>"ProjektListe");
        $viewMode = $data[viewMode];
        $showData = array("empty"=>"keine Auswahl");
        $addData = array();
        $addData["text"] = $this->lga($lgCode,"viewMode"); //"Abstand Oben";
        $addData["input"] = $this->selectView($viewMode,"editContent[data][viewMode]", $viewList, $showData); //"<input type='text' name='editContent[data][minHeight]' value='$data[minHeight]' >";
        $addData[mode] = "Simple";
        $res[$cType][] = $addData;
        
        switch ($viewMode) {
            case "projectList" :
                $addData = array();
                $addData["text"] = $this->lga($lgCode,"minHeight"); //"Abstand Oben";
                $addData["input"] = "<input type='text' name='editContent[data][minHeight]' value='$data[minHeight]' >";
                $addData[mode] = "Simple";
                $res[$cType][] = $addData;
                break;
        }
     
        
        //$res[frame] = "hideTab";
        // $res[wireframe] = "hideTab";
        //$res[frameText] = "hideTab";
        //$res[settings]  = "hideTab";
        
        return $res;
    }    
    
}
//
function cmsType_skCom_class() {
    //  echo ("OWN ?? ".$GLOBALS[cmsTypes]["cmsType_skCom.php"]."<br/>");
    
    if ($GLOBALS[cmsTypes]["cmsType_skCom.php"] == "own") $contentClass = new cmsType_skCom();
    else $contentClass = new cmsType_skCom_base();
    return $contentClass;
}
//
//function cmsType_skCom($contentData,$frameWidth) {
//    $contentClass = cmsType_content_class();
//    $contentClass->show($contentData,$frameWidth);
//}
//
//
//
//function cmsType_content_editContent($editContent) {
//    $contentClass = cmsType_content_class();
//    $res = $contentClass->contentType_editContent();
//    return $res;
//}
//    


?>

