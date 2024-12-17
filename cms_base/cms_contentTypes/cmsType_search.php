<?php // charset:UTF-8

class cmsType_search_base extends cmsType_contentTypes_base {
    function getName (){
        return "Suche";        
    }
    
    function show($contentData,$frameWidth) {
        $this->init($contentData);

        $searchString = $_POST[searchString]; 
        
        $search = $_SESSION[search];
        if (is_array($search)) {
            if ($search[id] == $contentData[id]) {
                $lastSearch = $search[lastSearch];
                if ($lastSearch) $searchString = $lastSearch;
//                foreach ($search as $key => $value ) {
//                    echo ("search $key = $value <br>");
//                }
            }
        } 
        
        // if (!$searchString) $searchString = "layout";
        $str = div_start_str("searchFrame",array("id"=>"searchFrame_".$contentData[id]));
        $this->searchStr = $searchString;
        $str .= $this->show_input($searchString,$contentData,$frameWidth);
        $str .= $this->show_info($searchString,$contentData,$frameWidth);
        $showFrame = 1;
        $str .= $this->show_result($searchString,$contentData,$frameWidth,$showFrame);
        $str .= div_end_str("searchFrame");
        if ($contentData[out] != "str") echo ($str);
        return $str;
        
    }
    
    
    function show_input($searchString,$contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $contentId = $contentData[id];
        $str .= div_start_str("searchInputFrame",array("id"=>"searchInput_".$contentData[id]));
        //echo ("<form method='post' >"); 
        $lgStr = $this->lg("search","searchText",":");
        if ($lgStr) {
            $str .= "<span class='cmsSearch_name'>$lgStr</span>";
        }
        
        $str .= "<input type='text' autocomplete='off' name='searchString' value ='$searchString' class='searchInput' id='search_$contentId' />";
        $str .= "<input type='hidden' name='searchhitCount' value ='$data[hitCount]' class='' id='searchHitCount_$contentId' />";
        $str .= "<div class='searchCancelButton mainJavaButton mainSmallButton mainSecond' >".$this->lg("search","deleteButton")."</div>";
        $str .= "<div class='searchSearchButton mainJavaButton'>".$this->lg("search","searchButton")."</div>";
        $str .= div_end_str("searchInputFrame");
        return $str;
        // echo ("<input type='submit' value='Suchen' name='searchButton' class='mainInputButton searchButton'  />");
        //echo ("</form>");
    }

    function init($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $this->hitCount = $data[hitCount];
        $this->show = array();
        
        $this->inWindow = $data[show_window];
        

        if ($data[show_headline]) $this->show[headline]=array("show"=>1);
        if ($data[show_headline_length]) $this->show[headline][length] = $data[show_headline_length];

        if ($data[show_subHeadline]) $this->show[subHeadline]=array("show"=>1);
        if ($data[show_subHeadline]) $this->show[subHeadline][length] = $data[show_subHeadline_length];

        if ($data[show_text]) $this->show[text]=array("show"=>1);
        if ($data[show_text_length]) $this->show[text][length] = $data[show_text_length];

        if ($data[show_longText]) $this->show[longText]=array("show"=>1);
        if ($data[show_longText_length]) $this->show[longText][length] = $data[show_longText_length];
        
//        show_array($this->show);
    }
      
    function show_info($searchString,$contentData,$frameWidth) {

        $str .= div_start_str("searchValue",array("id"=>"searchValue_".$contentData[id]));        
        if ($searchStr) $str .= "Suche nach '$searchString'";
        $str .= div_end_str("searchValue");
        return $str;
    }
    
    
    function show_result($searchString,$contentData,$frameWidth=null,$showFrame=0) {
        $this->init($contentData);
        if ($searchString AND !$this->searchStr) $this->searchStr = $searchString;
        $contentId = $contentData[id];
        
        $str = "";
        $divName = "searchResult";
        
        if (!$searchString) $divName .= " searchResult_hidden";
        
        if ($this->inWindow) {
            $str .= div_start_str("searchBack searchBack_hidden",array("id"=>"searchBack_".$contentId));
            $str .= div_end_str("searchBack searchBack_hidden");
            $divName .= " searchResult_hidden";
            $divName .= " searchResult_window";
        }

        if ($showFrame) $str .= div_start_str($divName,array("id"=>"searchResult_".$contentId));
       
        if (!$searchString) {
            $str .= div_end_str($divName);
            return $str;
        }

        $str.= "Ergebnisse für suche nach '<b>$searchString</b><br />";    
        
        $totalSearchResult = $this->search_result($contentData,$searchString);
        foreach ($totalSearchResult as $foundIn => $foundData) {
            $searchValue = $foundData[searchValue];
            $searchResult = $foundData[searchResult];
            
            $searchName = $searchValue[name];
            if (is_array($searchResult) AND count($searchResult)) {
                $nr = 1;
                $anz = 0;
                $hitStr = "";
                foreach ($searchResult as $key => $hit ) {
                    //foreach ($hit as $k => $v ) echo ("$k = $val | ");
                    // echo ("<br>");
                    if ($hit[hide]) {
                        // echo ("Nicht zeigen $hit[hide] <br>");
                    } else {
                        $hitStr .= $this->show_hit($foundIn,$hit,$nr);
                        $nr++;
                        $anz++;
                    }
                }
                
                if ($anz > 0) {
                    $str .= div_start_str("searchHitList searchHitList_$foundIn");
                
                    // $anz = count($searchResult);
                    $str .= div_start_str("searchHitHeadline searchHitHeadline_$foundIn",array("id"=>"searchHitHeadLine_$contentId"));
                    $str .= "$anz Treffer im Bereich <b>$searchName</b>";
                    $str .= div_end_str("searchHitHeadline searchHitHeadline_$foundIn");

                    $str .= div_start_str("searchHitResults searchHtResults_$foundIn",array("id"=>"searchHitResults_$contentId"));
                    $str .= $hitStr;
                    // showMore
                    
                    
                    $str .= div_end_str("searchHitResults searchHtResults_$foundIn");
                    if ($nr-1 > $this->hitCount) {
                        $str .= div_start_str("searchHit_showMore");
                        $str .= "Zeige mehr treffer";
                        $str .= div_end_str("searchHit_showMore");
                    }
                    
                    $str .= div_end_str("searchHitList searchHitList_$foundIn");
                }
               
//                div_end("searchHitResults searchHtResults_$foundIn");
//                // showMore
//                if ($nr-1 > $this->hitCount) {
//                    div_start("searchHit_showMore");
//                    echo ("Zeige mehr treffer");
//                    div_end("searchHit_showMore");
//                }
//                div_end("searchHitList searchHitList_$foundIn");
            } else {
                // echo ("Keine Treffer in $searchName <br>");
            }
        }
        if ($showFrame) $str .= div_end_str($divName);
        return $str;
    }
    
    function show_hit($foundIn,$hit,$nr) {
        $res = "";
        if (!is_array($hit)) return 0;
        
        $goPage = $hit[goLink];
        $addLink = $hit[addLink];
        if ($goPage AND $addLink) $goPage = $goPage.$addLink;
        
        $divName = "searchHit searchHit_$foundIn";
        if ($goPage) {
            // echo ("LINK $goPage <br>");
            $divName .= " cmsFrameLink";
            // echo ("<a href='$goPage' >");
        }

        if ($nr>$this->hitCount) $divName .= " searchHit_hidden";
        
        $divData = array("style"=>"");
        $res .= div_start_str($divName,$divData);
        if ($goPage) {
            $res .= "<a href='$goPage' class='hiddenLink' >Zeige Ergebnis auf Seite</a>";
  //          echo ("LINK $goLink <br>");
//            echo ("<a href='$goPage' >");
        }
        foreach ($hit as $key => $value ) {
            $show = $this->show[$key];
            $length = 0;
            if (is_array($show) ) {
                $length = $show[length];
                $show = $show[show];
            } else {
                $show = 0;               
            }
            
            if (!$value) $show = 0;
            
            switch ($key) {
                case "goLink" : break;
                case "addLink" : break;
                case "hide" :break;
                case "pageName" :
                    $res .= "gefunden auf Seite '$value' <br />";
                    break;
                case "headline" :
                    if ($show) {
                        $res .= "<div class='searchHit_$key' >";
                        $str = $this->hit_String($value,$length);
                        $res .= "$str";
                        $res .= "</div>";
                    }
                    break;
                case "subHeadline" : 
                    if ($show) {
                        $res .= "<div class='searchHit_$key' >";
                        $str = $this->hit_String($value,$length);
                        $res .= "<h3>$show $str</h3>";
                        $res .= "</div>";
                    }
                    break;

                case "text" :
                    if ($show) {
                        $res .= "<div class='searchHit_$key' >";
                        $str = $this->hit_String($value,$length);                        
                        $res .= "$str<br />";
                        $res .= "</div>";
                    }
                    break;
                case "longText" :
                    if ($show) {
                        $res .= "<div class='searchHit_$key' >";
                        $str = $this->hit_String($value,$length);
                        $res .= "$str<br />";
                        $res .= "</div>";
                    }
                   
                    break;
                case "adress" :
                    $res .= "<div class='searchHit_$key' >";
                    $str = $this->hit_String($value,$length);
                    $res .= "$str<br />";
                    $res .= "</div>";
                    break;
                
                default :
                    $res .= "unkown $key = $value <br/>";
            }
            
        }
         if ($goPage) {
                $res .= "<a href='$goPage' class='searchGoLink'>zeigen</a>";
            }
         if ($goPage) {
            //echo ("</a>");
        }
        $res .= div_end_str($divName);
        return $res;
        
    }
    
    function hit_String($str,$length=0) {
        
        if ($length AND strlen($str)>$length ) {
            // Text ist länger als maximale Ausgabe
            $searchPos = stripos($str,$this->searchStr);
            
            if (is_int($searchPos)) {
                // SearchString is in Text 
                if ($searchPos+strlen($this->searchStr) > $length ) {
                    // SearchString wäre ausserhalb 
                    $cutStart = strlen($str) - $searchPos;
                    
                    
                    $rest = strlen($str) - $searchPos;
                    $add = "L=$length SP = $searchPos Rest $rest sl = ".strlen($str);
                    if ($rest < $length) {
                        $cutStart = strlen($str) - $length;
                        $add .= " < length   cs = $cutStart ";
                    } else {
                        $cutStart = $searchPos - 20;
                        $add .= "> length cs = $cutStart ";
                    }
                    if ($cutStart < 0) $cutStart = 0;
                    $cutStart = strpos($str," ",$cutStart);
                    
                    
                    $str = "... ".substr($str,$cutStart);
                   //  return $str;
                }
                
            }
                
            if (strlen($str) > $length) {
                $endPos = strpos($str," ",$length-10);
                $str = substr($str,0,$endPos)."...";
            }
            
        }
        
        
        $style = ""; // border:1px solid #999;font-weight:bold;background-color:#eee;
        $relaceStr = "<span class='searchHitStr' style='$style'>$this->searchStr</span>";
        
        $str = str_ireplace(strtolower($this->searchStr),$relaceStr,$str);
        
        return $str;
    }
    
    
    function search_result($contentData,$searchString) {
        
        $cache = 0;
        
        if ($cache) {
            $search = $_SESSION[search];
            if (is_array($search)) {
                $useSearch = 1;
                // echo ("<h1>Session for search exist</h1>");
                // foreach ($search as $key => $value) {
                    //echo ("$key = $value<br>");                
                //}
                if ($useSearch) if ($contentData[id] != $search[id]) $useSearch = 0;
                if ($search[searchStr] != $searchString) $trulla = 1;
                $comp = substr($searchString,0,  strlen($search[searchStr]));
                if ($comp != $search[searchStr]) {
                    echo ("NEU LADEN da $search[searchStr] != $comp <br>");
                    $useSearch = 0;
                } else {
                    // echo ("<h2> USES SEARCH searchString = '$searchString' sess_search = ".$search[searchStr]." com='$comp' </h2>");
                }                


                if ($useSearch) {
                    // echo "Suche in Search nach '$searchString' <br>";
                    foreach($search[result] as $searchType => $searchList) {
                        // echo ("search $searchType = $searchList <br>");
                        if (is_array($searchList[searchResult])) {
                            foreach ($searchList[searchResult] as $searchId => $searchResult) {
                                // echo (" -> $searchId => $searchResult <br>");
                                if (is_array($searchResult)) {
                                    $show = 0;
                                    foreach ($searchResult as $key => $value ) {

                                        switch ($key) {
                                            case "pageName" : break;
                                            case "goLink" : break;
                                            case "addLink" : break;                                        
                                            default :
                                                $pos = stripos($value,$searchString);
                                                if (is_integer($pos)) $show = 1;
                                        }
                                    }
                                    if ($show == 1) {
                                        $search[result][$searchType][searchResult][$searchId][hide] = 0;
                                        // echo ("SHOW result $searchId in $searchType <br>");
                                    } else {
                                        $search[result][$searchType][searchResult][$searchId][hide] = 1;
                                    }
                                }
                            }
                        }
                    } // end of LOOP SerachResult
                    $_SESSION[search][lastSearch] = $searchString;
                    return $search[result];
                }
            }
        }
        
        
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
    
        
        $res = array();
    
        $searchList = $this->searchList();
        foreach ($searchList as $searchKey => $searchValue) {
            $search = $data["search_".$searchKey];            
            if ($search) {
                
                $searchRes = 0;
                
                $name = $searchValue[name];
                $searchText = $data["search_".$searchKey."_text"];
                $pageId = $data["search_".$searchKey."_page"];
                if ($pageId AND $pageId != "noLink") {
                    // echo ("Page $pageId in $searchKey <br>");
                    $pageData = cms_page_get(array("id"=>$pageId));
                } else $pageData = null;
                
                
                switch ($searchKey) {
                    case "page":
                        $searchRes = $this->search_result_page($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "content":
                        $searchRes = $this->search_result_content($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "text":
                        $searchRes = $this->search_result_text($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "faq":
                        $searchRes = $this->search_result_faq($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "article":
                        $searchRes = $this->search_result_article($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "company":
                        $searchRes = $this->search_result_company($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "product":
                        $searchRes = $this->search_result_product($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                   
                    case "project":
                        $searchRes = $this->search_result_project($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "date":
                        $searchRes = $this->search_result_date($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    case "location":
                        $searchRes = $this->search_result_location($searchString,$searchValue,$searchText,$contentData,$pageData);
                        break;
                    default :
                        $ownRes = $this->search_result_own($searchString,$searchKey,$searchValue,$searchText,$contentData);
                        if (is_array($ownRes)) {
                            $searchRes = $ownRes;
                        } else {
                            echo ("unkown SearchKey '$searchKey' in search_result <br />");
                        }
                }
                if (is_array($searchRes)) {
                    $res[$searchKey] = array();
                    $res[$searchKey][searchValue] = $searchValue;
                    $res[$searchKey][searchResult] = $searchRes;
                }
            }
        }   
        $search = array();
        $search[id] = $contentData[id];
        $search[searchStr] = $searchString;
        $search[lastSearch] = $searchString;
        $search[result] = $res;
        $_SESSION[search] = $search;
        return $res;
    }
    
    function search_result_own($searchString,$searchKey,$searchValue,$searchText,$contentData) {
        $searchRes = 0;
        return $searchRes;
    }
    
    function search_result_page($searchString,$searchValue,$searchText,$contentData,$pageData) {
         if (!function_exists("cmsPage_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/cms_page.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");               
                include ($dataFile);                
                if (function_exists("cmsPage_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsPage_search() not exist<br />");
                return 0;
            }
        }   
        $res = array();        
        $pageList = cmsPage_search($searchString,$searchText);
        if (!is_array($pageList)) return 0;
        if (count($pageList)==0) return 0;
        for ($i=0;$i<count($pageList);$i++) {
            $page = $pageList[$i];
           
            
            $pageId = $page[id];
            
            $pageHead = $page[title];
            $pageText = $page[description];
            
            $goPage = $page[name].".php";
            
            // $addLink = "#page_".$pageId;
            
            $res[$pageId] = array();
            $res[$pageId][headline] = $pageHead;
            $res[$pageId][text] = $pageText;
                    
            // $pageData = $this->search_getPageData($pageId);
//                    
            $res[$pageId]["pageName"] = $page[title];
            $res[$pageId][goLink] = $goPage;
            // $res[$pageId][addLink] = $addLink;     
            //  show_array($pageList);
        }        
        return $res;
    }
    function search_result_content($searchString,$searchValue,$searchText,$contentData,$pageData) {
        $res = array();
        return $res;
    }
    
    function search_result_text($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsText_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/cms_text.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");               
                include ($dataFile);                
                if (function_exists("cmsText_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsText_search() not exist<br />");
                return 0;
            }
        }   
        $res = array();
        $textList = cmsText_search($searchString,$searchText);
        
        if (is_array($textList)) {
            for ($i=0;$i<count($textList);$i++) {
                $text = $textList[$i];
                $contentId = $text[contentId];
                if (substr($contentId,0,5) == "text_") {
                    $contentId = substr($contentId,5);
                    $type = $text[name];
                    // echo ("Text $contentId type =$type <br />");
                    if (!is_array($res[$contentId])) $res[$contentId] = array("headline"=>"","text"=>"");
                    $res[$contentId][$type] = $text[text];
                }
            }
                
        }
        
        foreach ($res as $textId => $text) {
            // get HeadLine if not exist
            if (!$text[headline]) {
                $getText = cmsText_get(array("contentId"=>"text_".$textId,"name"=>"headline"));
                if (is_array($getText)) $res[$textId][headline] = $getText[text];                                    
            }
            // get Text if not exist
            if (!$text[text]) {
                $getText = cmsText_get(array("contentId"=>"text_".$textId,"name"=>"text"));
                if (is_array($getText)) $res[$textId][text] = $getText[text];                                                    
            }          
            
            // get ContentData 
            $contentData = cms_content_getId($textId);
            if (is_array($contentData)) {
                $contentId = $contentData[id];
                $pageId = $contentData[pageId];               
                if (substr($pageId,0,5) == "page_") {
                    $pageId = substr($pageId,5);
                    // echo ("Page ID = $pageId <br>");
                    
                    $pageData = $this->search_getPageData($pageId);
//                    
                    $res[$textId]["pageName"] = $pageData[title];
                    $res[$textId][goLink] = $pageData[name].".php";
                    $res[$textId][addLink] = "#inh_".$contentId;
                   
                }
                
            }            
        }       
        return $res;
    }
    function search_result_faq($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsFaq_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_faq.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");               
                include ($dataFile);                
                if (function_exists("cmsFaq_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsFaq_search() not exist<br />");
                return 0;
            }
        }   
        $res = array();
        
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
       
        $filter = array();
        
        
        $faqList = cmsFaq_search($searchString,$searchText,$filter);
        if (!is_array($faqList)) return 0;
        if (count($faqList)==0) return 0;
        for ($i=0;$i<count($faqList);$i++) {
            $faq = $faqList[$i];
            
            $faqId = $faq[id];
            
            $faqHead = $faq[head];
            $faqText = $faq[text];
            
            
            $addLink = "#faq_".$faqId;
            
            $res[$faqId] = array();
            $res[$faqId][headline] = $faqHead;
            $res[$faqId][text] = $faqText;
                    
            // $pageData = $this->search_getPageData($pageId);
//                    
            $res[$faqId]["pageName"] = $pageName;
            $res[$faqId][goLink] = $goPage;
            $res[$faqId][addLink] = $addLink;        
        }
        return $res;
    }
    
    function search_result_article($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsArticles_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_articles.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");
                include ($dataFile);
                if (function_exists("cmsArticles_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsArticles_search() not exist<br />");
                return 0;
            }
        }
        $res = array();
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
       
        $filter = array();
        $articleList = cmsArticles_search($searchString,$searchText,$filter);
        if (!is_array($articleList)) return 0;
        if (count($articleList)==0) return 0;
        // show_array($articleList);
        for ($i=0;$i<count($articleList);$i++) {
            $article = $articleList[$i];
            $articleId = $article[id];

            $addLink = "#date_".$articleId;

            //  show_array($article);

            $add = array();
            $add[headline] = $article[name];
            $add[subHeadline] = $article[subName];
            $add[text] = $article[info];
            $add[longText] = $article[longInfo];

            $add["pageName"] = $pageName;
            $add[goLink] = $goPage;
            $add[addLink] = $addLink;

            $res[$articleId] = $add;

        }

        return $res;
    }

    function search_result_company($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsCompany_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_company.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");
                include ($dataFile);
                if (function_exists("cmsCompany_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsCompany_search() not exist<br />");
                return 0;
            }
        }
        $res = array();
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
        $filter = array();
        $companyList = cmsCompany_search($searchString,$searchText,$filter);
        if (!is_array($companyList)) return 0;
        if (count($companyList)==0) return 0;
        // show_array($companyList);
        for ($i=0;$i<count($companyList);$i++) {
            $company = $companyList[$i];
            $companyId = $company[id];

            $addLink = "#date_".$companyId;

            //  show_array($company);

            $add = array();
            $add[headline] = $company[name];
            $add[subHeadline] = $company[subName];
            $add[text] = $company[info];
            $add[longText] = $company[longInfo];

            $add["pageName"] = $pageName;
            $add[goLink] = $goPage;
            $add[addLink] = $addLink;

            $res[$companyId] = $add;

        }

        return $res;
    }

    function search_result_product($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsProduct_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_product.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");
                include ($dataFile);
                if (function_exists("cmsProduct_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsProduct_search() not exist<br />");
                return 0;
            }
        }
        $res = array();
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
        $filter = array();
        $productList = cmsProduct_search($searchString,$searchText,$filter);
        if (!is_array($productList)) return 0;
        if (count($productList)==0) return 0;
        // show_array($productList);
        for ($i=0;$i<count($productList);$i++) {
            $product = $productList[$i];
            $productId = $product[id];

            $addLink = "#date_".$productId;

            //  show_array($product);

            $add = array();
            $add[headline] = $product[name];
            $add[subHeadline] = $product[subName];
            $add[text] = $product[info];
            $add[longText] = $product[longInfo];

            $add["pageName"] = $pageName;
            $add[goLink] = $goPage;
            $add[addLink] = $addLink;

            $res[$productId] = $add;

        }

        return $res;
    }

    function search_result_project($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsProject_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_project.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");
                include ($dataFile);
                if (function_exists("cmsProject_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsProject_search() not exist<br />");
                return 0;
            }
        }
        $res = array();
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
        $filter = array();
        $projectList = cmsProject_search($searchString,$searchText,$filter);
        if (!is_array($projectList)) return 0;
        if (count($projectList)==0) return 0;
        // show_array($projectList);
        for ($i=0;$i<count($projectList);$i++) {
            $project = $projectList[$i];
            $projectId = $project[id];

            $addLink = "#date_".$projectId;

            //  show_array($project);

            $add = array();
            $add[headline] = $project[name];
            $add[subHeadline] = $project[subName];
            $add[text] = $project[info];
            $add[longText] = $project[longInfo];
          
            $add["pageName"] = $pageName;
            $add[goLink] = $goPage;
            $add[addLink] = $addLink;

            $res[$projectId] = $add;

        }

        return $res;
    }

    function search_result_date($searchString,$searchValue,$searchText,$contentData,$pageData) {
        if (!function_exists("cmsDates_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_dates.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");
                include ($dataFile);
                if (function_exists("cmsDates_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsDates_search() not exist<br />");
                return 0;
            }
        }
        $res = array();
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
        $filter = array();
        $dateList = cmsDates_search($searchString,$searchText,$filter);
        if (!is_array($dateList)) return 0;
        if (count($dateList)==0) return 0;
        // show_array($dateList);
        for ($i=0;$i<count($dateList);$i++) {
            $date = $dateList[$i];
            $dateId = $date[id];

            $addLink = "#date_".$dateId;

            $add = array();
            $add[headline] = $date[head];
            $add[subHeadline] = $date[subName];
            $add[text] = $date[info];
            $add[longText] = $date[longInfo];

            $add["pageName"] = $pageName;
            $add[goLink] = $goPage;
            $add[addLink] = $addLink;

            $res[$dateId] = $add;
            
        }
        
        return $res;
    }
    function search_result_location($searchString,$searchValue,$searchText,$contentData,$pageData) {
        // echo ("search_result_date($searchString,$searchValue,$searchText,$contentData,$pageData)<br>");
        if (!function_exists("cmsLocation_search")) {
            $dataFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/data/cms_location.php";
            $error = 1;
            if (file_exists($dataFile)) {
                // echo ("Data File exist<br>");
                include ($dataFile);
                if (function_exists("cmsLocation_search")) $error = 0;
            }
            if ($error) {
                echo ("Function cmsLocation_search() not exist<br />");
                return 0;
            }
        }
        $res = array();
        if (is_array($pageData)) {
            $goPage = $pageData[name].".php";
            $pageName = $pageData[title];
        }
        $filter = array();
        $locationList = cmsLocation_search($searchString,$searchText,$filter);
        if (!is_array($locationList)) return 0;
        if (count($locationList)==0) return 0;
       
        // show_array($locationList);
        for ($i=0;$i<count($locationList);$i++) {
            $location = $locationList[$i];
            $locationId = $location[id];

            $addLink = "#date_".$dateId;

            $add = array();
            $add[headline] = $location[head];
            $add[subHeadline] = $location[subName];
            $add[text] = $location[info];

            $add[adress] = $location[street]." ".$location[streetNr]."<br>".$location[plz]." ".$location[city];
            $add["pageName"] = $pageName;
            $add[goLink] = $goPage;
            $add[addLink] = $addLink;

            $res[$locationId] = $add;

        }

        return $res;
    }
    
    function search_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array("search"=>array());
        
        $addData = array();
        $addData["text"] = "Treffer im Fenster";
         if ($data[show_window]) $checked = "checked='checked'";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked name='editContent[data][show_window]' value='1' s />";
        $res[search][] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = "Treffer pro Bereich";
        $addData["input"] = "<input type='text' name='editContent[data][hitCount]' value='$data[hitCount]' style='width:50px;' />";
        $res[search][] = $addData;

        $addData = array();
        $addData["text"] = "Ausgabe Überschrift";
        if ($data[show_headline]) $checked = "checked='checked'";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked name='editContent[data][show_headline]' value='1' />";
        $res[search][] = $addData;

        $addData = array();
        $addData["text"] = "Ausgabe 2. Überschrift";
        if ($data[show_subHeadline]) $checked = "checked='checked'";
        else $checked = "";
        $addData["input"] = "<input type='checkbox' $checked name='editContent[data][show_subHeadline]' value='1' />";
        $res[search][] = $addData;

        $addData = array();
        $addData["text"] = "Ausgabe Text";
        if ($data[show_text]) $checked = "checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][show_text]' value='1' />";
        $input .= " max. Anzahl Zeichen: <input type='text' name='editContent[data][show_text_length]' value='".$data["show_text_length"]."' style='width:50px;' />";
        $addData["input"] =  $input;
        $res[search][] = $addData;

        $addData = array();
        $addData["text"] = "Ausgabe Langer Text";
        if ($data[show_longText]) $checked = "checked='checked'";
        else $checked = "";
        $input = "<input type='checkbox' $checked name='editContent[data][show_longText]' value='1' />";
        $input .= " max. Anzahl Zeichen: <input type='text' name='editContent[data][show_longText_length]' value='".$data["show_longText_length"]."' style='width:50px;' />";
        $addData["input"] =  $input;
        $res[search][] = $addData;


        $res[searchIn] = array();        
        $searchList = $this->searchList();
        foreach($searchList as $key => $value) {
            $name = $value[name];
            $addData = array();
            $addData[text] = "Suchen in <b>$name</b>";
            if ($data["search_$key"]) $checked="checked='checked'";
            else $checked = "";
            $input = "<input type='checkbox' $checked value='1' name='editContent[data][search_$key]' />";
            
            if ($data["search_".$key."_text"]) $checked="checked='checked'";
            else $checked = "";
            $input .= " im Inhalt suchen <input type='checkbox' $checked value='1' name='editContent[data][search_".$key."_text]' />";
            
            if ($value[pageSelect]) {
                $pageId = $data["search_".$key."_page"];
                $input .= " Zielseite: ".  cms_page_SelectMainPage($pageId,"editContent[data][search_".$key."_page]");
            }
            
            $addData["input"] = $input;
            $res[searchIn][] = $addData;
        }
        
        
        return $res;
    }    
    
    function searchList() {
        $res = array();
        $res[page] = array("name"=>"Seiten");
        $res[content] = array("name"=>"Inhalt");
        $res[text] = array("name"=>"Text");
        $res[faq] = array("name"=>"Fragen und Antworten","pageSelect"=>1);
        $res[article] = array("name"=>"Artikel","pageSelect"=>1);
        $res[company] = array("name"=>"Hersteller","pageSelect"=>1);
        $res[product] = array("name"=>"Produkte","pageSelect"=>1);
        $res[project] = array("name"=>"Projekte","pageSelect"=>1);
        $res[date] = array("name"=>"Termine","pageSelect"=>1);
        $res[location] = array("name"=>"Orte","pageSelect"=>1);
        return $res;
    }
    
    
    function search_getPageData($pageId) {
        if (!$pageId) return "noPageId";
        if (!is_array($this->pageList)) $this->pageList = array();
        if (is_array($this->pageList[$pageId])) return $this->pageList[$pageId];
        
        $page = cms_page_get(array("id"=>$pageId));
        if (is_array($page)) {
            $this->pageList[$pageId] = $page;
            return $page;
        }
        return "pageNotFound";
        
        
        
        
    }
    
}

function cmsType_search_class() {
    if ($GLOBALS[cmsTypes]["cmsType_search.php"] == "own") $searchClass = new cmsType_search();
    else $searchClass = new cmsType_search_base();
    return $searchClass;
}

function cmsType_search($contentData,$frameWidth) {
    $searchClass = cmsType_search_class();
    $res = $searchClass->show($contentData,$frameWidth);
    return $res;
}



function cmsType_search_editContent($editContent) {
    $searchClass = cmsType_search_class();
    $res = $searchClass->search_editContent($editContent, $frameWidth);
    return $res;
}
    


?>
