<?php // charset:UTF-8
class cmsType_imageList_base extends cmsType_contentTypes_base {
    
    function getName() {
        return "Bild Liste";
    }
    
    function imageList_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];
        $clickAction = $data[clickAction];

        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = ($frameWidth - ($imgRow-1)*$imgRowAbs ) / $imgRow;
        
        $imgList = $data[imgList];

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        $divData[clickAction] = $clickAction;//"previewImage";
        $divData[imageList] = $imgList;
        
        

        // echo ("Data = $imgRow $imgRowAbs $imgColAbs <br />");
        // echo ("RowWidth = $rowWidth <br />");
        // echo ("ImageList = $imgList <br />");

        
        $delimiter = ",";
        if (!is_null(strpos($imgList,"|"))) $delimiter = "|";
        
        
        
        $imgIdList = explode($delimiter,$imgList);
        $imgDataList = array();

        for ($i=0;$i<count($imgIdList);$i++) {
            $imgStr = $imgIdList[$i];
            if ($imgStr) {
                $imageId = intval($imgStr);
                //  echo ("Show Image with id $imageId <br>");
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    $imgDataList[] = $imageData;
                } else {
                   echo ("Bild mit Id $imageId nicht gefunden <br />");
                }
            }
        }

        
        $ratio = 4/3;
        $nr = 0;
        if (count($imgDataList)) {
            div_start("imageList",$divData);
            for ($i = 0; $i<count($imgDataList); $i++) {
                $imgData = $imgDataList[$i];
                $nr++;
                $fn = "Image ".$i;
                if ($nr == 1) {
                    div_start("imgListLine","margin-bottom:".$imgColAbs."px");
                    if (!$ratio) {

                        $showHeight = cmsImage_getShowHeight($imgData,$rowWidth);
                        // echo ("AktImage = $i showHeight = $showHeight <br />");
                        for ($nextNr=$i+1;$nextNr<$i+$imgRow;$nextNr++) {
                            $nextShowHeight = cmsImage_getShowHeight($imgDataList[$nextNr],$rowWidth);
                            if ($nextShowHeight > $showHeight ) $showHeight = $nextShowHeight;
                            // echo ("Check $nextNr $showHeight $nextShowHeight <br />");
                        }
                    } else {
                        $showHeight = $rowWidth / $ratio;
                    }
                }
                $divData = array();
                $divData[style] = "width:".$rowWidth."px;float:left;height:".$showHeight."px;";
                if ($nr < $imgRow) $divData[style].="margin-right:".$imgRowAbs."px";
                $divData[imageId] = $imgData[id];
                div_start("imgListImageBox",$divData);
                $showData = array();
                $showData[frameWidth] = $rowWidth;
                $showData[frameHeight] = floor($rowWidth / 4 * 3);
                $showData[ratio] = 4/3;

    //            $showData[vAlign] = "top";
    //            $showData[hAlign] = "left";

                $imgStr = cmsImage_showImage($imgData, $rowWidth, $showData);
                // show_array($imgData);
                echo ($imgStr);
                div_end("imgListImageBox");

                if ($nr == $imgRow) { // close Line
                    $nr = 0;
                    div_end("imgListLine","before");
                }
            }

            if ($nr != 0) {
                div_end("imgListLine","before");
            }




            div_end("imageList","before");
        } else {
            echo ("<div class='cmsContentNoData'>");
            echo ("Kein Bilder in Liste!");
            echo ("</div>");
        }

        switch ($clickAction) {
            case "fullPreview" :
                //                div_start("imagePreviewWindow",array("cmsName"=>$GLOBALS[cmsName]));
                //                div_end("imagePreviewWindow");
                //
                //                div_start("imagePreviewContent");
                //                echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                //                div_end("imagePreviewContent");
                break;

            case "framePreview":
                div_start("imagePreviewFrame");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end("imagePreviewFrame");
                break;
        }
        
        //div_start("imagePreviewWindow open");
        
    }

    function imageList_editContent($editContent,$frameWidth) {
        $res = array();
        $res[view] = array();

        $addData = array();
        $addData["text"] = "Anzahl Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$editContent[data][imgRow]."'>";
        $addData["input"] = $input;
        $res[view][] = $addData;

        $addData = array();
        $addData["text"] = "Abstand Bilder in Reihe";
        $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$editContent[data][imgRowAbs]."'>";
        $addData["input"] = $input;
        $res[view][] = $addData;

        $addData = array();
        $addData["text"] = "Abstand Zeilen";
        $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$editContent[data][imgColAbs]."'>";
        $addData["input"] = $input;
        $res[view][] = $addData;


        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];

        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        

        $imageIdStr = $editContent[data][imgList];
        if ($_POST[editContent][data][imgList]) {
            $imageIdStr = $_POST[editContent][data][imgList];
        }

        $imgIdList = explode(",",$imageIdStr);


        /// ACTION //////////////
        $actionChange = 0;
        /// ACTION - DELETE

        $delList = $_POST[deleteFromList];
        if ($delList) {
            // echo ("DeleteFromList<br />");
            $newIdList = array();
            for ($i=0;$i<count($imgIdList);$i++) {
                $imageId = $imgIdList[$i];
                if ($delList[$imageId] == "x") {
                    echo ("Delete $imageId from List <br />");
                } else {                    
                    $newIdList[] = $imageId;
                }
            }
            $imgIdList = $newIdList;
            $actionChange = 1;
        }

        /// ACTION - MOVE UP
        $moveUp = $_POST[moveUpInList];
        if ($moveUp) {
            echo ("Move Up<br />");
            $actionChange = 1;
        }

        /// ACTION - MOVE UP
        $moveDown = $_POST[moveDownInList];
        if ($moveDown) {
            echo ("Move Down<br />");
            $actionChange = 1;
        }

        // ACTION - IMAGE ADD
        $imageAdd = $_POST[imageAdd];
        if ($imageAdd) {
            echo ("<h1> IMAGE ADD $imageAdd</h1>");
            $newIdList = array();
            $found = 0;
            for ($i=0;$i<count($imgIdList);$i++) {
                $imageId = $imgIdList[$i];
                if ($imageId == $imageAdd) { // allready in List -> dont Add
                    $found = 1;
                }
            }

            if ($found == 0) {
                echo ("Add Image with Id $imageAdd<br />");
                $imgIdList[] = $imageAdd;
            }            
            $actionChange = 1;
        }

        /// ACTION - CREATE NEW LIST
        if ($actionChange) {
            $imageIdStr  = "";
            for ($i=0;$i<count($imgIdList);$i++) {
                $imageId = $imgIdList[$i];
                if ($i>0) $imageIdStr .= ",";
                $imageIdStr .= $imageId;
            }
            echo "NEW ImageIdStr = '$imageIdStr' <br /> ";
        }

        
        $addData["text"] =  "Bild-Liste";
        $addData["input"] = "Trulla";
        $div = array();
        $div[divname] = "cmsImageList";
        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:visible;";
        // $div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";
        
        $showData = array();
        $showData[width] = $frameWidth - 4;
        $showData[imageAdd] = 1;
        $showData[imageUpload] = 1;
        $showData[delimiter] = ",";
        $showData[imageFolder] = "images/";
        $showData[imageSortAble] = 1;
        $showData[imageDeleteAble] = 1;
        $showData[showMode] = "block"; // array("line","block")[1];
        $showData[dataName] = "editContent[data][imgList]";
        
        $div[content] = $this->editContent_imageList($imageIdStr,$showData);
        

        $addData["div"] = $div;
        $res[imageList][] = $addData;




        // ImageAdd
        $addData = array();
        $addData["text"] =  "Bild hinzufügen";
        $img = "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='30px' height='30px' class='cmsImageSelect'> ";
        $imageId = intval($product[image]);
        if ($imageId > 0) {
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $img = cmsImage_showImage($imageData,100,array("class"=>"cmsImageSelect"));
            }
        }
        $addData["input"] = $img."<input type='text' class='cmsImageId' action='submit' formName='cmsEditContentForm' style='width:30px;' name='imageAdd' value='' onChange='submit()' >";
        $res[imageList][] = $addData;

        $div = array();
        $div[divName] = "cmsImageSelector";
        $div[folderName] = "images/";
        $div[style] = "height:0px;background-color:#bbb;visible:none;overflow:hidden;";
        //$div[style] = "height:100px;background-color:#bbb;visible:none;overflow:hidden;";
        $div[content] = cmsImage_selectList($folder);
        $addData["div"] = $div;
        $res[imageList][] = $addData;

       

        return $res;
    }

    function imageList_ImageList($imgList){
        $divCont = "";
        $imgIdList = explode(",",$imgList);
        for ($i=0;$i<count($imgIdList);$i++) {
            
            $imageId = intval($imgIdList[$i]);
            $divCont.= "ImageId = $imageId <br>";
            if ($imageId > 0) {
                $divCont.= div_start_str("imageListLine","width:100%;height:30px;");


                $imgData = cmsImage_getData_by_Id($imageId);
                $imgName = $imgData[name];
                $imgWidth = $imgData[width];
                $imgHeight = $imgData[height];
                $imgPath   = $imgData[orgpath];

                $imgPath = substr($imgPath,  strpos($imgPath, "/")+1);

                // FileImage
                $divCont .= div_start_str("imageListLine_Name","float:left;width:42px;overflow:hidden;");
                $divCont .= cmsImage_showImage($imgData, 40, array("frameWidth"=>40,"frameHeight"=>30));
                $divCont .= div_end_str("imageListLine_Name");


                // FileName
                $divCont .= div_start_str("imageListLine_Name","float:left;width:100px;overflow:hidden;");
                $divCont .= $imgName;
                $divCont .= div_end_str("imageListLine_Name");


                // FileSize
                $divCont .= div_start_str("imageListLine_Size","float:left;width:100px;overflow:hidden;");
                $divCont .= $imgWidth."x".$imgHeight;
                $divCont .= div_end_str("imageListLine_Size");

                // FilePath
                $divCont .= div_start_str("imageListLine_Path","float:left;width:30%;overflow:hidden;");
                $divCont .= $imgPath;
                $divCont .= div_end_str("imageListLine_Path");

                // FileAction
                $divCont .= div_start_str("imageListLine_Action","float:left;width:30%px;overflow:hidden;");
                //$divCont .= "<a class='cmsContentHeadButton' href='#'>x</a>";
                $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='x' name='deleteFromList[$imageId]' >";
                if ($i>0) $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='&#8593;' name='moveUpInList[$imageId]' >";
                else $divCont .= "___";
                $divCont .= "<input type='submit' class='cmsContentHeadInputButton' value='&#8595;' name='moveDownInList[$imageId]' >";

                //$divCont .= "<a class='cmsContentHeadButton' href='#'>&#8593;</a>";
                // $divCont .= "<a class='cmsContentHeadButton' href='#'>&#8595;</a>";
                $divCont .= div_end_str("imageListLine_Action");

              

                $divCont.= div_end_str("imageListLine","before");
            }
        }

        return $divCont;
    }

    

    function clickAction_getList() {
        $res = array();
        $res["fullPreview"] = "Vorschau auf ganzer Seite";
        $res["framePreview"] = "Vorschau in Rahmen";

        $ownList = $this->clickAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function clickAction_getOwnList() {
        $res = array();
        return $res;
    }

}

function cmsType_imageList_class() {
    //  echo ("imageList");
    if ($GLOBALS[cmsTypes]["cmsType_imageList.php"] == "own") $imageListClass = new cmsType_imageList();
    else $imageListClass = new cmsType_imageList_base();

    return $imageListClass;
}


function cmsType_imageList($contentData,$frameWidth) {
    $imageListClass = cmsType_imageList_class();
    $imageListClass->imageList_show($contentData,$frameWidth);
}



function cmsType_imageList_editContent($editContent,$frameWidth) {
    $imageListClass = cmsType_imageList_class();
    return $imageListClass->imageList_editContent($editContent,$frameWidth);
}


?>
