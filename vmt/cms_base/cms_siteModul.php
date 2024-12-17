<?php

    function cmsModul_show() {
        $out = "";
        echo ("<div class='cmsEditModulFrame'>");
       
        $pageWidth = $GLOBALS[cmsSettings][width];
        $out .= "<div class='cmsModulFrame'>"; //  style='left:".($pageWidth+20)."px;'>";
        // foreach ($GLOBALS[cmsSettings] as $key => $value) $out.= "$key = $value <br>";
        
        
        // Header
        $out .= "<div class='cmsEditBox' style='width:auto;'>";
        // Roll Image
        $out .= "<div class='cmsModulAdd' style='display:inline-block;'>";
        $out .= "<img src='/cms_base/cmsImages/cmsEditAdd.png' border='0px'>";
        $out .= "</div>";
        $out .= "<div class='cmsImageAdd' style='display:inline-block;'>";
        $out .= "<img src='/cms_base/cmsImages/cmsImageAdd.png' border='0px'>";
        $out .= "</div>";

        $out .= "</div>";

        // GETMODULE
        $dontShow = array("page"=>1,"not"=>1);
        $hidden = 1;
        $out .= cms_layout_editModul($dontShow,$hidden);

        // CLOSE FRAMES

        
        // IMAGES 
        $out .= "<div class='cmsModulImageFrame cmsModul_hidden' >";
        $out .= "<div class='cmsModulContentHead'>Bilder</div>";
        $out .= "Ordner:<br />";
        $out .= "/images/projects <br>";
        $out .= "Filter<br />";
        $out .= "<input type='text' value='' name='filterImage' style='width:100%;' /><br/>";

        $out .= "<div class='cmsModulImageScroll ' >"; //dragImageFrame
        $out .= "Bilder:";
        $out .= "</div>";

        $out.= "</div>";

        $out .= "</div>";
        
        $out .= "</div>";

        echo ($out);              
    }
    
    
    
    function cms_layout_editModul($dontShow=array(),$hidden=1) {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";

        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulContentFrame $hidden","");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Module";
        $out .= div_end_str("cmsModulContentHead");
        $typeList = cms_contentType_getSortetList();
        foreach($dontShow as $key => $value) {
            if ($value == 1) unset($typeList[$key]);
        }
        // if (!$showPage) unset($typeList[page]);
        // unset($typeList[not]);
        foreach ($typeList as $key => $value ) {
            switch ($key) {
                 case ("data") :
                     if ($mode == "sort") $frameAdd = "dragFrame";
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key'>$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame $frameAdd cmsModulCat_$key' id='cmsModulCat_$key' >";
                     foreach ($value as $dataType => $dataValue) {

                        // class="cmsModulContentCategory cmsModulContentCategorySecond"
                         $out .= "<div class='cmsModulContentCategory cmsModulContentCategorySecond' id='cmsModulCat_$key_$dataType' >$dataType</div>";
                         $out .= "<div class='cmsModulCategoryFrame cmsModulCategoryFrameSecond  $frameAdd cmsModulCategoryFrameHidden cmsModulCat_$key_$dataType' id='cmsModulCat_$key_$dataType'  >";
                         foreach ($dataValue as $type => $typeValue) {
                             
                             switch ($mode) {
                                case "sort" :
                                   $out .= "<div class='cmsModulContentButton drageBox' id='cmsDragModul_$type' style='$style'>";
                                   $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                   break;
                                case "drag" :
                                    $out .= "<div class='cmsModulContentButton dragNewModul' id='cmsDragModul_$type' style='$style'>";
                                    $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                    break;

                            }
                             
                             
//                             $out .= "<div class='cmsModulContentButton cmsModulContentButtonSecond dragBox' id='cmsDragModul_data_$type'>";
//                             $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";

                             $out .= "&nbsp; ".$typeValue[name];
                             $out .= "</div>";
                         }
                         $out .= "</div>";

                     }
                     $out .= "</div>";
                     break;
                 default:
                     $frameAdd = "";
                     if ($mode == "sort") $frameAdd = "dragFrame";
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key'>$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame $frameAdd cmsModulCat_$key' id='cmsModulCat_$key' >";
                     foreach ($value as $type => $typeValue) {
                         // $style="margin-left:10px";
                         // cmsContentFrameBox dragBox
                         switch ($mode) {
                             case "sort" :
                                $out .= "<div class='cmsModulContentButton drageBox' id='cmsDragModul_$type' style='$style'>";
                                $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                break;
                             case "drag" :
                                 $out .= "<div class='cmsModulContentButton dragNewModul' id='cmsDragModul_$type' style='$style'>";
                                 $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                 break;

                         }

                         
                         $out .= "&nbsp;".$typeValue[name];
                         $out .= "</div>";
                     }
                     $out .= "</div>";
             }
        }

        $out .= div_end_str("cmsModulContentFrame $hidden");
        return $out;
    }


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
