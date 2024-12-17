<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=iso-8859-1');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

    global $cmsName,$cmsVersion;
    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php";
    include($cmsFile);
    $name = $_GET[name];
    $frame = $_GET[frame];
    $type = $_GET[type];
    $frameWidth = $_GET[frameWidth];
    $getData = $_GET;

   
    // echo("<script src='/cms/cms_contentTypes/cmsType_image.js'></script>");
    $imgList = $_GET[imageList];
    $imageId = $_GET[imageId];

    $width = 500;
    $frameWidth = $width;
    $frameHeight = $width;
    if ($_GET[width]) $frameWidth = $_GET[width];
    if ($_GET[height]) $frameHeight = $_GET[height];
    // echo ("ImageList = $imgList / imageId = $imageId <br>");
    if ($imgList) {
        $imgList = explode(",",$imgList);
        for($i=0;$i<count($imgList);$i++) {
            if ($imageId == $imgList[$i]) {
                $found = 1;
                if ($i<count($imgList)-1) $nextImage = $imgList[$i+1];
                else $nextImage = $imgList[0];
                
                if ($i>0) $beforeImage = $imgList[$i-1];
                else $beforeImage = $imgList[count($imgList)-1];
            }
        }
    }
    // echo ("Next = $nextImage Befor = $beforeImage <br>");


    // echo ("<h1>cms_imageShow_get.php $getData[imageId]</h1>");
    
    $imageData = cmsImage_getData_by_Id($imageId);
    $showData = array();
    $showData["class"] = "imagePreviewImage";
    $showData["nextImage"] = $nextImage;
    $showData["beforeImage"] = $beforeImage;
    $showData["frameHeight"] = $frameHeight;
    $showData["frameWidth"] = $frameWidth;
    $imageStr = cmsImage_showImage($imageData, $frameWidth, $showData);
    echo ($imageStr);

  

?>
