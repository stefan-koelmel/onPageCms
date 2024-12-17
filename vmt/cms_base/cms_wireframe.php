<?php




class cms_wireframe_base {
    function color($type) {
        switch ($type) {
            case "back" : return 0; return "#333333";
            case "line" : return "#777777";                    
        }
    }
    
    
    function hex2rgb($hex) {
        $rgb[0]=hexdec(substr($hex,1,2));
        $rgb[1]=hexdec(substr($hex,3,2));
        $rgb[2]=hexdec(substr($hex,5,2));
        return($rgb);
    }
    
    function imageCross($lineColor) {
        
        if ($lineColor[0] == "#") $lineColor = substr($lineColor,1);
        
        $path = $_SERVER[DOCUMENT_ROOT]."/cms_".$GLOBALS[cmsVersion]."/images/wireframe/";
        
        //  echo ("Check ".$path."cross-".$lineColor.".png <br>");
        if (file_exists($path."cross-".$lineColor.".png")) {
            
            return $path."cross-".$lineColor.".png";
        }
        $fn = "cross.png";
        // echo ("$path / $fn <br>");
        if (file_exists($path.$fn)) {
            return $path.$fn;
        }        
    }
    
    
    function image($width=400,$height=300,$color=0) {
        
        if (!$color) {
            $backColor = $this->color("back");
            $lineColor = $this->color("line");
        }
        
        switch ($_SERVER[HTTP_HOST]) {
            case "cms.stefan-koelmel.com" :
                $path = $_SERVER[DOCUMENT_ROOT]."/".$GLOBALS[cmsName]."/wireframe/";
                break;
            default :
                 $path = $_SERVER[DOCUMENT_ROOT]."/wireframe/";                
        }
        
        
        
        
        $fn = "wire_".$width."_".$height."_";
        if ($backColor[0] == "#") $fn .= substr($backColor,1)."_";
        else $fn .= $backColor."_";
        
        if ($lineColor[0] == "#") $fn .= substr($lineColor,1);
        else $fn .= $lineColor;
        
        
        
        $fn .= ".png";
        
        
//        echo ("fn = $fn <br>");
//        echo ("DestinageionPath = $path <br>");

        if (file_exists($path.$fn)) return "wireframe/".$fn;
        
        
        
        
        // echo ("$path <br>");
        
        
        // Create Image
        $image_new = imagecreatetruecolor($width, $height);
        
       
        $lineWidth = 1;
        if ($backColor) {
            $backRgb = $this->hex2rgb($backColor);
            $colBack=imagecolorallocate($image_new,$backRgb[0],$backRgb[1],$backRgb[2]);
        }
        
        $lineRgb = $this->hex2rgb($lineColor);
        
        // echo ("Create Image $width x $height $backColor / $lineColor <br>");
        
        
        
        
       
        // echo ("ColBack = $colBack<br>");
        
        $colLine=imagecolorallocate($image_new,$lineRgb[0],$lineRgb[1],$lineRgb[2]);
        // echo ("colLine = $colLine<br>");
        
        
        $alpha = imagecolorallocatealpha($image_new, 0, 0, 0, 127);
        imagefill($image_new, 0, 0, $alpha);
        
        if ($backColor) {
            // Hintergrund
            imagefill($image_new,0,0,$colBack);
        }
         
         
         
        
         
         $crossWithImage = 0;
         if ($crossWithImage) {
            // get FileName for Cross
            $imgCrossFileName = $this->imageCross($lineColor);
            if ($imgCrossFileName) {
                $imgCross = imagecreatefrompng($imgCrossFileName);
                $maxSize = $width;
                if ($height > $maxSize) $maxSize = $height;

                $crossWidth = imagesx($imgCross);
                $crossHeight = imagesy($imgCross);
                imageFill($imgCross,0,0,$colLine);
                
                
                $crossColor =  imagecolorat($imgCross , floor($crossWidth/2) , floor($crossHeight/2) );
                // echo ("Kreuzfarber = $crossColor <br>");
                $colLine = $crossColor;

                // echo ("Cross Image $imgCross $crossWidth x $crossHeight <br>");

                $crossOffX = floor(($crossWidth - $maxSize) / 2);
                $crossOffY = floor(($crossHeight - $maxSize) / 2);

                imagecopyresampled($image_new, $imgCross, 0, 0, $crossOffX, $crossOffY, $width, $height, $maxSize, $maxSize);
                imagedestroy($imgCross);
             } else {
                 // no CrossFileName
                 $crossWithImage = 0;
             }
         }
         
         if (!$crossWithImage) { 
             // Create Cross with Line
             imageline($image_new,0,0,$width+1,$height-1, $colLine);
             imageline($image_new,0,$height-1,$width,0, $colLine);             
         }
         
         
         // Rahmen
         // oben
         imagefilledrectangle($image_new, 0, 0, $width, $lineWidth-1, $colLine);
         
         // left
         imagefilledrectangle($image_new, 0, $lineWidth-1, $lineWidth-1, $height-$lineWidth, $colLine);
         
         // right
         imagefilledrectangle($image_new, $width-$lineWidth, $lineWidth, $width, $height-$lineWidth, $colLine);
         
         // bottom
         imagefilledrectangle($image_new, 0, $height-$lineWidth, $width, $height, $colLine);
         
         
        
        
        
        imagesavealpha($image_new, true);
        
        imagepng($image_new, $path.$fn);
        imagedestroy($image_new);
        
        return "wireframe/".$fn;   
    }
    
    function frameStart($width,$height,$class) {
       
        $backImage = $this->image($width,$height);
        echo ("<div class='wireframe_Frame $class' style='background-image:url(".$backImage.");width:".$width."px;height:".$height."px;' >");
        //  echo ("Create Frame $width x $height $class <br>");
        
        
        
    }
    
    function frameEnd($width,$height,$class) {
        // echo ("End of Frame $width x $height $class <br>");
        echo ("</div>");
    }
    
    function text($length) {
        echo ("text mit länge $length <br>");
    }
    
    function state() {
        $state = $_SESSION[wireframe];
        return $state;
    }
    
    function setState($set) {
        $state = $this->state();
        if ($state == $set) {
            return "noChange";
        }
        
        // echo ("Set WireframeState to $set <br>");
        
        $_SESSION[wireframe] = $set;
        $goPage = cms_page_goPage();
        reloadPage($goPage,0);
    }
}



function cms_wireframe_Class() {
    $cmsName = $GLOBALS[cmsName];

    $ownFn = $_SERVER[DOCUMENT_ROOT]."/$cmsName/cms/cms_wireframe_own.php";
    // echo ("OnwFile = $ownFn <br>");
    if (file_exists($ownFn)) {
        include($ownFn);
        // echo ("<h1>EXIST</h1>");
        $wireframeClass = new cms_wireframe_own();
    } else {
        $wireframeClass = new cms_wireframe_base();
    }
    return $wireframeClass;
}

function cmsWireframe_image($width,$height,$color=0) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->image($width,$height,$color=0);   
    return $res;
}

function cmsWireframe_frameStart($width,$height,$class) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->frameStart($width,$height,$class);   
    return $res;
}

function cmsWireframe_frameEnd($width,$height,$class) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->frameEnd($width,$height,$class);   
    return $res;
}

function cmsWireframe_text($length) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->frame($length);   
    return $res;
}


function cmsWireframe_state() {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->state();   
    return $res;
}

function cmsWireframe_setState($set) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->setState($set);   
    return $res;
}


?>
