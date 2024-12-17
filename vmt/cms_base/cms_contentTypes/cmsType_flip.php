<?php // charset:UTF-8

class cmsType_flip_base extends cmsType_contentTypes_base {
    function getName (){
        return "Wechselnder Inhalt";        
    }

    function flip_show($contentData,$frameWidth,$getData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        // show_array($contentData);



        $res = array();

        // MainData

        $type = $data[typeSelect];
        $layerWidth = $data[layerWidth];
        $layerHeight = $data[layerHeight];
        $layerCount = $data[layerCount];


        //div_start("FlipFrame","border:1px solid #cfc");
        $edit = $_SESSION[edit];
        $flipId = $contentData[id];

        $flipFrameNr = 1;
        $addTo = $_GET[addTo];
        if (subStr($addTo,0,4) == "flip") {
            echo ("Addto is flip - $addTo <br />");// _"$flipId."_")
            $flipList = explode("_",$addTo);
            //for ($i=0;$i<count($flipList);$i++) echo ("$i = $flipList[$i] <br />");
            $flipNr = $flipList[1];
            if ($flipNr == $flipId) {
                echo ("FlipNr = $flipNr <br />");
                $flipFrameNr = $flipList[2];
            }
        }

        if ($_GET[flipId]== $flipId) {


            $editLayer = $_GET[flipLayerNr];
            echo ("Edit FlipLayer $flipId / $editLayer <br />");
            $flipFrameNr = $editLayer;
        }

        if ($edit) {
            //echo ("Type = $type<br />");
            //echo ("Frame W/H $layerWidth,$layerHeight <br />");
            //echo ("LayerCount = $layerCount <br />");
            //$layerNr = 1;
            if (is_array($getData[layerNr])) $layerNr = $getData[layerNr];
            div_start("cmsFlipSelectFrame");
            for ($i=1;$i<=$layerCount;$i++) {
                $divName = "cmsFlipFrameSelector";
                if ($i == $flipFrameNr) $divName .= " cmsFlipFrameSelectorSelected";
                $divName .= " cmsFlipSelector_".$flipId."_".$i;

                div_start($divName,array("style"=>"float:left;","flipId"=>$flipId,"name"=>"flip_".$flipId,"layerNr"=>$i,"frameWidth"=>$frameWidth));
                echo("Ebene $i");
                div_end($divName);
            }
            div_end("cmsFlipSelectFrame","before");
        }


        $flipFrame = $_GET[showFlip];
        $compareName = "flip_".$flipId;
        if ($compareName == substr($flipFrame,0,strlen($compareName))) {
            $flipFrameNr = intval(substr($flipFrame,strlen($compareName)+1));
           // echo ("Compare is same - $flipFrame = '$flipFrameNr' ".substr($flipFrame,strlen($compareName)+1)."<br />");
        }
        // echo ("compare '".$compareName."' <> '".substr($flipFrame,0,strlen($compareName))."' <br />");


        if (!$flipFrameNr) $flipFrameNr = 1;
        $divData = array("flipType"=>$type,"style"=>"min-height:$layerHeight;overflow:visible;","name"=>"flip_".$flipId,"frameWidth"=>$frameWidth);
        if ($edit) $divData[active] = 0;
        else $divData[active] = 1;
        $divData[layerCount] = $layerCount;
        $divData[layerNr] = $flipFrameNr;
        $divData[flipId] = $flipId;
        if ($type == "time") $divData[flipMs] = $data[msChange];

        div_start("cmsFlipFrameContent flip_".$flipId,$divData);
        $showFlip = "flip_".$flipId."_".$flipFrameNr;
        cms_content_show($showFlip, $frameWidth);
        div_end("cmsFlipFrameContent flip_".$flipId);

        // echo ("showContent $showFlip <br />");
        //div_end("FlipFrame","before");
    }



    function flip_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();

        // MainData

        $type = $data[typeSelect];
        $addData = array();
        $addData["text"] = "Wechel-Art";
        $addData["input"] = $this->flip_selectType($data[typeSelect],"editContent[data][typeSelect]",array("onChange"=>"submit()"));
        $res[] = $addData;

        /* $addData = array();
        $addData["text"] = "Rahmen Breite/Höhe";
        $addData["input"] = "<input type='text' name='editContent[data][layerWidth]' style='width:70px;' value='$data[layerWidth]' > / <input type='text' name='editContent[data][layerHeight]' style='width:70px;' value='$data[layerHeight]' >";
        $res[] = $addData;
        */

        switch ($type) {
            case "roll" :
                $data[layerCount] = 2;
                $addData = array();
                $addData["text"] = "Anzahl Ebenen";
                $addData["input"] = "<input type='hidden' name='editContent[data][layerCount]'  value='2' ><input type='text' name='editContent[data][layerCount]' style='width:70px;' value='2' DISABLED>";
                $res[] = $addData;
                break;

            case "time" :
                $addData = array();
                $addData["text"] = "Zeitwechsel nach";
                $addData["input"] = "<input type='text' name='editContent[data][msChange]' style='width:70px;' value='$data[msChange]'> ms";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Anzahl Ebenen";
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' style='width:70px;' value='$data[layerCount]'> in Milli-Sekunden";
                $res[] = $addData;
                break;

            case "click" :
                $addData = array();
                $addData["text"] = "Anzahl Ebenen";
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' value='$data[layerCount]'> in Milli-Sekunden";
                $res[] = $addData;
                break;
        }
        return $res;
    }


    function flip_selectType($type,$dataName,$dataAction=array()) {
        $typeList = array();
        $typeList[roll] = array("name"=>"Maus über Rahmen");
        $typeList[time] = array("name"=>"Zeitgesteuert");
        $typeList[click] = array("name"=>"Maus Klick");

        $str = "";
        $str.= "<select name='$dataName' class='cmsSelectType' value='$type' ";
        foreach ($dataAction as $key => $value) {
            $str .= " $key='$value'";
        }
        $str .= " >";


        foreach ($typeList as $code => $typeData) {
             $str.= "<option value='$code'";
             if ($code == $type)  $str.= " selected='1' ";
             $str.= ">$typeData[name]</option>";
        }
        $str.= "</select>";
        return $str;
    }
}

function cmsType_flip_class() {
    if ($GLOBALS[cmsTypes]["cmsType_flip.php"] == "own") $flipClass = new cmsType_flip();
    else $flipClass = new cmsType_flip_base();
    return $flipClass;
}


function cmsType_flip($contentData,$frameWidth) {
    $flipClass = cmsType_flip_class();
    $flipClass->flip_show($contentData,$frameWidth);
}


function cmsType_flip_editContent($editContent,$frameWidth) {
    $flipClass = cmsType_flip_class();
    return $flipClass->flip_editContent($editContent,$frameWidth);
}





?>
