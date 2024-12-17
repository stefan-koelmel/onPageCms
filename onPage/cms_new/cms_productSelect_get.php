<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=iso-8859-1');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

    include("cms.php");
    $name = $_GET[name];
    $frame = $_GET[frame];
    $type = $_GET[type];
    $frameWidth = $_GET[frameWidth];
    $getData = $_GET;

    global $cmsName;
    $cmsName = $_GET[cmsName];
   
    // echo ("<h1>cms_productSelect_get.php $getData[folder]</h1>");
    // echo ("cmsName = $cmsName<br>");

    $companyId = $_GET[companyId];

    $frameWidth = $_GET[frameWidth];
    if ($companyId) {
        // echo ("Zeige Produkte mit CompanyId = $companyId ($frameWidth)<br>");
    }

    $filter = array("company"=>$companyId);
    // $productList = cmsProduct_getList($filter);
    // show_array($productList);

    $editContent = array();
    $editContent[data] = array();
    $editContent[data][filter] = $filter;

     cmsType_productList($editContent,$frameWidth);

    //$res = cmsImage_SelectList_getContent($folder);

//    echo ($res);

    // show_array($getData);
    
    
    // foreach ($_GET as $key => $value) echo ("$key = $value <br>");
    //cms_content_show($name,$frameWidth,$getData);
    //echo("Show Content for Name '$name' with frame= '$frame' <br>");
    //return "jkhkjhkj";

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
