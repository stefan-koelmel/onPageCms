<?php // charset:UTF-8

class cmsType_header extends cmsType_header_base {

    function headerShow ($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $height = $data[height];
        if (!$height) $height = 80;
        $background = $data[background];
        if (!$background) $background="#cfc";

        $border = 0;
        $innerWidth = $frameWidth;// - 10 - 2*$border;

        div_start("header","width:".$innerWidth."px;");

        div_start("headerLeft","float:left;");
        echo("<a href='index.php' class='headerStartLink' >");
        echo ($this->headerLogo());
        echo("</a>");
        div_end("headerLeft");

        div_start("headerRight","float:left;");
        // span_text($this->headerName(),array("width"=>"auto","text-align"=>"right","class"=>"headerName"));

        //$this->headerName();
        // echo ("<br>");
        span_text($this->headerSlogan(),array("class"=>"headerSlogan","width"=>"auto"));
        //echo ($this->headerSlogan());
        div_end("headerRight");

        div_end("header","before");
    }


    function headerLogo() {
        global $cmsName;
        $homePath = "";
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$cmsName."/")) $homePath = "/".$cmsName."/";

        return "<img class='headerLogo' src='".$homPath."style/logo.png' title='Schaufenster' name='schaufenster' alt='SCHAUFENSTER' >";
    }

    function headerName() {
        return "Schaufenster";
    }

    function headerSlogan () {
        return "schreiben · schenken · genießen"; // ·??
    }
}


?>
