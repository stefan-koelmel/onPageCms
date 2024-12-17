<?php // charset:UTF-8
function pageStylesExist(){}

function div_start_str($name,$moreData="") {
    $divNr = count($_SESSION[lastDiv]) + 10;
    
    $className = $name;
    $addStr = "";
    if (is_string($moreData)) {
        $style = $moreData;
        if ($style) $addStr .= "style='$style' ";
    }
    if (is_array($moreData)) {
        $hiddenStr = "";
        foreach ($moreData as $key => $value) {
            switch ($key) {
                case "name"  : $addStr.= "$key='$value' ";break;
                case "id"    : $addStr.= "$key='$value' ";break;
                case "title" : $addStr.= "$key='$value' "; break;
                case "style" : $addStr.= "$key='$value' "; break;
                case "class" : $className .= " ".$value; break;

                case "link"  : $hiddenStr .= "<a href='$value' class='hiddenLink'></a>"; break;

                
                default :
                    // $str.= "$key='$value' ";
                    // echo ("add $key = $value --");
                    if ($hiddenStr != "") $hiddenStr .= "|";
                    $hiddenStr .= $key.":". htmlspecialchars($value);
                    
            }
        }
       // if ($langStr) $str.="name='$langStr' ";
    }
    
    $str = "<div class='$className' ".$addStr . ">";

//     $str.= ">";
    //$str .= "\n";
    if (!is_array($_SESSION[lastDiv])) $_SESSION[lastDiv] = array();
    $_SESSION[lastDiv][] = $name;    
    if ($hiddenStr) $str.="<div class='hiddenData'>$hiddenStr</div>";

    return $str;
}

function div_start($name,$style="") {
    $str = div_start_str($name,$style);
    echo($str);
}



function div_end_str($name,$close="") {
    $lastdiv = array_pop($_SESSION[lastDiv]); // $_SESSION[lastDiv][count($_SESSION[lastDiv])];
    $str = "";
    if ($name != $lastdiv) $str.= "wrong Close DIV last = $lastdiv / name = $name <br>";

    if ($close == "before" OR $close == -1) {
        $str.= "<div style='clear:both;'></div>\n";
        $close = 0;
    }

    $str.= "</div>\n";
    if ($close) $str.= "<div style='clear:both;'></div>\n";
    return $str;
}

function div_end($name,$close="") {
    $str = div_end_str($name,$close);
    echo($str);
}

function div_check_str($clear) {
    if ($clear) $_SESSION[lastDiv] = array();
    $str = "";
    for ($i=0; $i<count($_SESSION[lastDiv]); $i++) {
        if ($str != "") $str .= "-|-";
        $str.= $_SESSION[lastDiv][$i];
    }
    return ($str);
}

function div_check($clear) {
    $str = div_check_str($clear);
    if ($str != "" ) echo($str."<br>");
}

function span_text($text,$data=array()) {
    echo span_text_str($text,$data);
}

function span_text_str($text,$data=array()) {
    $width = 150;
    $direction = "left";
    $class = null;
    
    if (is_integer($data)) $width = $data;
    
    if (is_array($data)) {
        foreach($data as $key => $value) {
            switch ($key) {
                case "width" : $width = $value; break;
                case "class" : $class = $value; break;
                case "text-align" : $direction = $value;break;
                case "style" : $styleAdd = $value; break;
                default:
                    $addStr .= "$key='$value' ";
                }
        }
    }
    
    $str = "";
    if (is_string($data)) {
        $str = "<span style='".$data.";display:inline-block;'>";
        
    } else {
        $str = "<span style='";
        $str.= "width:".$width."px;";
        $str.= "text-align:".$direction.";";
        $str .= "display:inline-block;' ";
        if ($class) $str .= "class='".$class."' ";
        if ($addStr) $str .= $addStr;
       
        $str.= ">";
        
    }
    $str.= $text."</span>";
    return $str;
}

?>
