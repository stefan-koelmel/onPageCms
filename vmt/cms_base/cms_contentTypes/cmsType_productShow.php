<?php // charset:UTF-8
class cmsType_productShow_base extends cmsType_contentTypes_base {

    function getName(){
        return "Produkt zeigen";
    }
    
    function productShow_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("productShow","width:".$frameWidth."px;");


        $data = $contentData[data];
        
        $filter = array();
        $debug = 0;
        // FILTER PRODUCT
        $filterProduct = $data[filterProduct];
        if ($filterProduct) {
            if ($debug) echo ("Filter Product $filterProduct <br />");
            switch ($filterProduct) {
                case "new" :
                    $filter["new"] = 1;
                    break;
                case "highlight" :
                    $filter["highlight"] = 1;
                    break;
            }
        }
        // FILTER Hersteller
        $filterCompany = $data[filterCompany];
        if ($filterCompany) {
            if ($debug) echo ("Filter Company $filterCompany <br />");
            $filter["company"] = $filterCompany;
        }
        // FILTER Category
        $filterCategory = $data[filterCategory];
        if ($filterCategory) {
            if ($debug) echo ("Filter Category $filterCategory <br />");
            $filter["category"] = filterCategory;
        }

        if ($debug AND count($filter)>0) {
            echo ("<h1>Filter</h1>");
            show_array($filter);
        }


        $productList = cmsProduct_getList($filter,$sort);
        $anz = count($productList);
        //echo ("Produkt anzahl $anz <br />");

        $productId = rand(0,$anz-1);
        $product = $productList[$productId];

        
        $name = $product[name];
        $info = $product[info];
        $company = $product[company];
        $show = $product[show];
        $new = $product["new"];
        $highlight = $product[highlight];
        $imageId = intval($product[image]);


        $imageData = cmsImage_getData_by_Id($imageId);

        $showData = array();
        $showData[frameWidth] = $frameWidth*1;
        $showData[frameHeight] = $frameWidth*1;
        $showData[vAlign] = "bottom";
        $showData[hAlign] = "left";
        $showData[title] = $product[name];
        $showData[alt] = $product[name];
        $showData[name] = $product[name];

        $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
        echo ($imgStr);
        echo ("<h1>$name</h1>");
        echo ($info);
        //echo ("Produkt anzahl $anz $productId <br />");

        
        
        

        div_end("productShow","before");
    }

    function productShow_editContent($editContent,$frameWidth) {
        $res = array();
        // FILTER PRODUKT
        $filterProduct = $editContent[data][filterProduct];
        if ($_POST[editContent][data][filterProduct]) $filterProduct = $_POST[editContent][data][filterProduct];
        else if ($_POST[editContent][data]) $filterProduct = $_POST[editContent][data][filterProduct];
        $addData = array();
        $addData["text"] = "Produkte Filtern";
        $addData["input"] = $this->filter_select("product",$filterProduct,"editContent[data][filterProduct]",array("submit"=>1));
        $res[productShow][] = $addData;
        
        // FILTER Hersteller
        $filterCompany = $editContent[data][filterCompany];
        if ($_POST[editContent][data][filterCompany]) $filterCompany = $_POST[editContent][data][filterCompany];
        else if ($_POST[editContent][data]) $filterCompany = $_POST[editContent][data][filterCompany];
        $addData = array();
        $addData["text"] = "nach Herstellern";
        $addData["input"] = $this->filter_select("company",$filterCompany,"editContent[data][filterCompany]",array("submit"=>1));
        $res[productShow][] = $addData;

        // FILTER Category
        $filterCategory = $editContent[data][filterCategory];
        if ($_POST[editContent][data][filterCategory]) $filterCategory = $_POST[editContent][data][filterCategory];
        else if ($_POST[editContent][data]) $filterCategory = $_POST[editContent][data][filterCategory];
        $addData = array();
        $addData["text"] = "nach Kategorie";
        $addData["input"] = $this->filter_select("category",$filterCategory,"editContent[data][filterCategory]",array("submit"=>1));
        $res[productShow][] = $addData;



       /* $addData["text"] = "Anzahl Hersteller in Reihe";
        $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$editContent[data][imgRow]."'>";
        $addData["input"] = $input;
        $res[productShow][] = $addData;

        $addData["text"] = "Abstand Hersteller in Reihe";
        $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$editContent[data][imgRowAbs]."'>";
        $addData["input"] = $input;
        $res[productShow][] = $addData;

        $addData["text"] = "Abstand Zeilen";
        $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$editContent[data][imgColAbs]."'>";
        $addData["input"] = $input;
        $res[productShow][] = $addData; */
        
        return $res;
    }


    // function filter_select($filterType,$code,$dataName,$showData) {

    // function filter_select_getList($filterType,$filter,$sort) {

    // function product_filter_select_getList($filter,$sort) {
      
    function product_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }


    // function company_filter_select_getList($filter,$sort) {

    function company_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

    // function category_filter_select_getList($filter,$sort) {

    function category_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

}

function cmsType_productShow_class() {
    if ($GLOBALS[cmsTypes]["cmsType_productShow.php"] == "own") $productShowClass = new cmsType_productShow();
    else $productShowClass = new cmsType_productShow_base();
    return $productShowClass;
}


function cmsType_productShow($contentData,$frameWidth) {
    $productShowClass = cmsType_productShow_class();
    $productShowClass->productShow_show($contentData,$frameWidth);
}



function cmsType_productShow_editContent($editContent,$frameWidth) {
    $productShowClass = cmsType_productShow_class();
    return $productShowClass->productShow_editContent($editContent,$frameWidth);
}


?>
