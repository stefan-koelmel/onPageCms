<?php // charset:UTF-8
class cmsType_map_base extends cmsType_contentTypes_base {
    
    function getName (){
        return "Karte";
    }
    
    function map_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("map","width:".$frameWidth."px;");

        //foreach ($contentData as $key => $value) echo ("cont $key = $value <br />");
        // foreach ($contentData[data] as $key => $value) echo ("data $key = $value <br />");


        $id = $contentData[id];
        $pageId = $contentData[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $contentCode = "text_$id";



        $editText = cms_text_getForContent($contentCode);
        if (is_array($editText)) {
            $textClass = cmsType_text_class();
            // headLine
            if (is_array($editText[headline])) {
                $headline = $editText[headline][text];
                if (strlen($headline)>0) {
                    $textClass->text_showHeadline($editText[headline],$id);
                }
            }

            if (is_array($editText[text])) {
                $text = $editText[text][text];
                if (strlen($text)>0) {
                    $textClass->text_showText($editText[text],$id);
                }
            }
        }
        //show_array($editText);


        $mapWidth = $frameWidth;
        if ($contentData[frameWidth]) $mapWidth = $contentData[frameWidth];

        $mapHeight = $mapWidth * 3 / 4;
        if ($contentData[frameHeight]) $mapHeight = $contentData[frameHeight];
        
        $url     = $contentData[data][url];
        $zoom    = $contentData[data][zoom];
        $showBox = $contentData[data][showBox];

        if ($url) {
        
            // Find Zoom Offet
            $zoomNew = "&z=".$zoom;
            $zoomStart = strpos($url,"&z=");
            if ($zoomStart) {
                $zoomEnd = strpos($url,"&",$zoomStart+2);
                if ($zoomEnd) $zoomText = subStr($url,$zoomStart,($zoomEnd-$zoomStart));
                else $zoomText = subStr($url,$zoomStart);
                //echo ("ZoomStart = $zoomStart ZoomEnd = $zoomEnd ZoomText = '$zoomText'<br />");
                $url = str_replace($zoomText, $zoomNew, $url);
            } else {
                $url .= $zoomNew;
            }





            // makiert
            // &iwloc=A

            $boxNew = "&iwloc=A";
            $boxStart = strpos($url,"&iwLoc=");
            if ($boxStart) {
                $boxEnd = strpos($url,"&",$boxStart+2);
                if ($zoomEnd) $boxText = subStr($url,$boxStart,($boxEnd-$boxStart));
                else $boxText = subStr($url,$boxStart);
                // echo ("BoxStart = $boxStart BoxEnd = $zoomEnd BoxText = '$boxText'<br />");
                if ($showBox) {
                    $url = str_replace($boxText, $boxNew, $url);
                } else {
                    $url = str_replace($boxText, "&iwloc=", $url);
                }


            } else {
                if ($showBox) {
                    $url .= $boxNew;
                } else {
                     $url .= "&iwloc=";
                    /// $url = str_replace($boxText, "", $url);
                }           
            }
            // echo ("After Box: $url <br />");



           //  echo ("Map $zoomStart<br />");

            $zoom =16;
            $lo = "49.412003";
            $wi = "8.709412";
            $lo2 = "0.105474";
            $wi2 = "0.004447";
            $urlMap = "https://www.google.de/maps?f=q&amp;source=s_q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+schreiben+-+schenken+-+geniesen+!,+Karlsruhe&amp;aq=0&amp;oq=Schaufenster+schr&amp;sll=".$lo.",".$wi."&amp;sspn=0.002474,0.004447&amp;t=h&amp;ie=UTF8&amp;hq=SCHAUFENSTER+schreiben+-+schenken+-+geniesen+!,&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.996621,8.393126&amp;spn=$lo2,$wi2&amp;z=$zoom&amp;output=embed";


            // $url = "https://www.google.de/maps?f=q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=16".$zoom;
            // $urlMap    = "https://www.google.de/maps?f=q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=14";
            $urlMap    = $url;
            $urlMap   .= "&source=s_q";
            $urlMap   .= "&output=embed";

            // $urlGoogle = "https://www.google.de/maps?f=q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=14";
            $urlGoogle  = $url;
            $urlGoogle .= "&source=embed";


            //$urlMap    = "https://www.google.de/maps?f=q&amp;source=s_q  &amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=14&amp;output=embed";
            // $urlGoogle = "https://www.google.de/maps?f=q&amp;source=embed&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=14";

            // $urlMap = "https://www.google.de/maps?f=q&amp;source=s_q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+schreiben+-+schenken+-+geniesen+!,+Gebhardstra%C3%9Fe+2,+Karlsruhe&amp;aq=0&amp;oq=Sc&amp;sll=48.661847,9.003665&amp;sspn=4.535316,11.634521&amp;t=h&amp;ie=UTF8&amp;hq=SCHAUFENSTER+schreiben+-+schenken+-+geniesen+!,&amp;hnear=Gebhardstra%C3%9Fe+2,+76137+Karlsruhe,+Baden-W%C3%BCrttemberg&amp;ll=48.996953,8.392771&amp;spn=0.008799,0.022724&amp;z=14&amp;iwloc=A&amp;cid=3324602510630263189&amp;output=embed";
            // $urlMap = "https://www.google.de/maps?f=q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=15&amp;iwloc=A&amp;iwloc=&amp;amp;source=s_q&amp;amp;output=embed";
    //


            $urlMap = htmlspecialchars($urlMap);
           // $urlMap = str_replace("&", "&amp;",$urlMap);
    //        $urlMap = htmlspecialchars($urlMap);
    //        $urlMap =  "https://www.google.de/maps?f=q&amp;hl=de&amp;geocode=&amp;q=SCHAUFENSTER+Karlsruhe&amp;aq=&amp;sll=49.411704,8.708343&amp;sspn=1.256251,2.276917&amp;t=m&amp;ie=UTF8&amp;hq=SCHAUFENSTER&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;cid=3324602510630263189&amp;ll=48.997621,8.391066&amp;spn=0.019709,0.036478&amp;z=15&amp;iwloc=A&amp;iwloc=&amp;amp;source=s_q&amp;amp;output=embed";
    //        echo ($urlMap."<br />");
            echo ("<iframe width='".$mapWidth."px' height='".$mapHeight."px' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='");
            echo ($urlMap);
            echo("'></iframe>"); // end of iFrame
        } else {
            echo ("<div class='cmsContentNoData'>");
            echo ("Keine Karten URL angegeben");
            echo ("</div>");
        }
        
        $showSmall = 0;
        if ($showSmall) {
            echo("<br />");
            echo ("<small>");
            echo ("<a href='");
            echo ("https://www.google.de/maps?f=qsource=embedhl=degeocode=q=SCHAUFENSTER+schreiben+-+schenken+-+geniesen+!,+Karlsruheaq=0oq=Schaufenster+schrsll=48.997381,8.392712sspn=0.002474,0.004447t=hie=UTF8hq=SCHAUFENSTER+schreiben+-+schenken+-+geniesen+!,hnear=Karlsruhe,+Baden-W%C3%BCrttembergcid=3324602510630263189ll=48.996621,8.393126spn=0.004224,0.006437z=16");
            echo ("'  target='googleMaps' >");
            echo ("Größere Kartenansicht");
            echo("</a>");
            echo("</small>");
        }
        
        
        if (is_array($editText)) {
         //   $textClass = cms_contenttype_text_class();


            if (is_array($editText[subText])) {
                $subText = $editText[subText][text];
                if (strlen($subText)>0) {
                    $textClass->text_showText($editText[subText],$id);
                }
            }
        }



       /* echo ("<iframe width='".$frameWidth."px' height='350' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='");
        echo ("https://maps.google.de/maps?f=q&amp;source=s_q&amp;hl=de&amp;geocode=&amp;q=Karlsruhe&amp;aq=&amp;sll=51.151786,10.415039&amp;sspn=8.617162,19.709473&amp;t=h&amp;ie=UTF8&amp;hq=&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;ll=49.009149,8.37994&amp;spn=0.002463,0.00456&amp;z=17&amp;iwloc=A&amp;output=embed'>");
        echo ("</iframe>");
        echo ("<br /><small>");
        echo ("<a href='https://maps.google.de/maps?f=q&amp;source=embed&amp;hl=de&amp;geocode=&amp;q=Karlsruhe&amp;aq=&amp;sll=51.151786,10.415039&amp;sspn=8.617162,19.709473&amp;t=h&amp;ie=UTF8&amp;hq=&amp;hnear=Karlsruhe,+Baden-W%C3%BCrttemberg&amp;ll=49.009149,8.37994&amp;spn=0.002463,0.00456&amp;z=17&amp;iwloc=A' style='color:#0000FF;text-align:left'>Größere Kartenansicht</a>");
        echo("</small>");*/



        div_end("map","before");
    }

    function map_editContent($editContent,$frameWidth) {
        //  foreach ($editContent[data] as $key => $value ) echo ("editCont $key = $value <br />");

        $id = $editContent[id];
        $pageId = $editContent[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $contentCode = "text_$id";

        // show_array($data);
        // foreach ($editContent as $key => $value ) echo (" editContent $key = $value <br />");
        // foreach ($editContent[data] as $key => $value ) echo (" data $key = $value <br />");

        $editText = $_POST[editText];
        if (!is_array($editText)) {
            $editText = cms_text_getForContent($contentCode);
        } else {
           // show_array($editText);
        }
        // show_array($editText);


        $res = array();
        $res[text] = array();
        $res[map] = array();


        //width
        $editType = $editContent[type];


        $addData = array();
        $addData["text"] = "hidden-Text Id";
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
        $res[map][] = $addData;

        $addData = array();
        $addData["text"] = "Überschrift";
        $input  = "<input type='text' style='width:".$frameWidth."px;' name='editText[headline][text]' value='".$editText[headline][text]."' >";
        $input .= "<input type='hidden' value='".$editText[headline][id]."' name='editText[headline][id]'>";
        $addData["input"] = cms_content_selectStyle("headline",$editText[headline][css],"editText[headline][css]");
        $addData["secondLine"] = $input;
        $res[map][] = $addData;


        $addData = array();
        $addData["text"] = "Text über Karte";
        $input  = "<textarea name='editText[text][text]' style='width:".$frameWidth."px;height:100px;' >".$editText[text][text]."</textarea>";
        $input .= "<input type='hidden' value='".$editText[text][id]."' name='editText[text][id]'>";
        $addData["input"] = cms_content_selectStyle("text",$editText[text][css],"editText[text][css]");
        $addData["secondLine"] = $input;
        $res[map][] = $addData;

        // url
        $url = $editContent[data][url];
        if (!$zoom ) $zoom = 16;
        $addData = array();
        $addData["text"] = "GoogleUrl";
        $input = "<input type='text' name='editContent[data][url]' value='$url'>\n ";

        $input = "<textarea name='editContent[data][url]' style='height:100px;width:".($frameWidth-220)."px;' >";
        $input.= $url;
        $input .= "</textarea>\n";
        $addData["input"] = $input;
        $res[map][] = $addData;


        $addData = array();
        $addData["text"] = "Ansicht";
        $viewList = array("m"=>"Karte","h"=>"Satelit","p"=>"Gelände");
        $addData["input"] = cmsEdit_SelectList("editContent[data][view]",$editContent[data][view],$viewList);
        $res[] = $addData;

        // Zoom
        $zoom = $editContent[data][zoom];
        if (!$zoom ) $zoom = 16;
        $addData = array();
        $addData["text"] = "Zoom";
        $input = "<input type='text' name='editContent[data][zoom]' value='$zoom'>\n ";
        if ($editContent[data][kontakt]) $input .= "checked='checked'";
        
        $addData["input"] = $input;
        $res[map][] = $addData;

        // ShowInfoBox
        $addData = array();
        $addData["text"] = "InfoBox zeigen";
        $input = "<input type='checkbox' name='editContent[data][showBox]' value='1' ";
        if ($editContent[data][showBox]) $input .= "checked='checked'";
        $input .= ">\n";
        $addData["input"] = $input;
        $res[map][] = $addData;



        $addData = array();
        $addData["text"] = "Text unter Karte";
        $input  = "<textarea name='editText[subText][text]' style='width:".$frameWidth."px;height:100px;' >".$editText[subText][text]."</textarea>";
        $input .= "<input type='hidden' value='".$editText[subText][id]."' name='editText[subText][id]'>";
        $addData["input"] = cms_content_selectStyle("text",$editText[subText][css],"editText[subText][css]");
        $addData["secondLine"] = $input;
        $res[map][] = $addData;


        return $res;
    }
}


function cmsType_map_class() {
    if ($GLOBALS[cmsTypes]["cmsType_map.php"] == "own") $mapClass = new cmsType_map();
    else $mapClass = new cmsType_map_base();

    return $mapClass;
}

function cmsType_map($contentData,$frameWidth) {
    $mapClass = cmsType_map_class();
    $mapClass->map_show($contentData,$frameWidth);
}



function cmsType_map_editContent($editContent,$frameWidth) {
    $mapClass = cmsType_map_class();
    return $mapClass->map_editContent($editContent,$frameWidth);
}


?>
