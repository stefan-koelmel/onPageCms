<?php // charset:UTF-8


class cmsType_contentName_base  extends cmsType_contentTypes_base {
    function getName() {
        return "gespeicherter Inhalt";
    }
    //put your code he
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function show($contentData,$frameWidth) {
    $data = $contentData[data];
    if (!is_array($data)) {
        cms_errorBox("Keine Daten für das Anzeigen von eigenen Inhalten");
        return 0;
    }

    $showContentId = $data[contentName];
    $showContentData = 0;
    if ($showContentId > 0) {
        $showContentData = cms_content_getId($showContentId);
        
    }

    if (!is_array($showContentData)) {
        cms_errorBox("Eigener Inhalt nicht gefunden mit ID $showContentId - $showContentData");
        return 0;
    }

    //foreach ($contentData as $key => $value) echo ("cont $key = $value <br />");

    // echo ("Eigener Inhalt <br />");
    cms_contentType_show($showContentData,$frameWidth,"contentName");
    
}

    function contentName_data($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) {
            // cms_errorBox("Keine Daten für das Anzeigen von eigenen Inhalten");
            // return array();
            return 0;
        }

        $showContentId = $data[contentName];
        $showContentData = 0;
        if ($showContentId > 0) {
            $showContentData = cms_content_getId($showContentId);

        }

        if (!is_array($showContentData)) {
            cms_errorBox("Eigener Inhalt nicht gefunden mit ID $showContentId - $showContentData");
            return 0;
        }

        $mergedData = $showContentData;


        foreach ($showContentData as $key => $value) {
            if (is_array($value)) {
                foreach($value as $dataKey => $dataValue ) {
                    if ($contentData[$key][$dataKey]) {

                        if ($contentData[$key][$dataKey] != $dataValue ) {
             //               echo ("Modification for $key | $dataKey => ".$contentData[$key][$dataKey]." ");
               //             echo (" -> ist diffrent - was '$dataValue' <br />");
                            $mergedData[$key][$dataKey] = $contentData[$key][$dataKey];

                        }
                    }
                    // echo (" -- DATA - $dataKey => from ContentName Data = '$dataValue' | from Modification = '".$contentData[$key][$dataKey]."' <br /> ");
                }
            } else {
                if ($value != $contentData[$key]) {
                    switch ($key) {
                        case "type" : break;
                        case "id" : break;
                        case "pageId" : break;
                        case "contentName" : break;

                        default:
                            //echo ("Modification for $key => ".$contentData[$key]." ");
                            // secho (" -> ist diffrent - was '$value' <br />");
                            $mergedData[$key] = $contentData[$key];
                    }


                }
                //echo ("$key => from ContentName Data = '$value' | from Modification = '$contentData[$key]' <br /> ");
            }
        }


        // echo ("Eigener Inhalt make Modification for getData<br />");
        return $mergedData;
        //cms_contentType_show($showContentData,$frameWidth);






    }

    function contentName_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $contentList = cms_content_contentNameList();
        if (is_array($contentList)) {
            // show_array($contentList);
        }


        $res = array();

        //width1
        $addData = array();
        $addData["text"] = "Inhalt auswählen";
        $addData["input"] = cms_content_Select_contentName($data[contentName],"editContent[data][contentName]");
        $addData["mode"] = "Simple";
        //"<input type='text' name='editContent[data][contentName]' value='$data[contentName]' >";
        $res[] = $addData;

        return $res;

    }
}

function cmsType_contentName_class() {
    if ($GLOBALS[cmsTypes]["cmsType_contentName.php"] == "own") $contentNameClass = new cmsType_contentName();
    else $contentNameClass = new cmsType_contentName_base();
    return $contentNameClass;
}


function cmsType_contentName_data($contentData,$frameWidth) {
    $contentNameClass = cmsType_contentName_class();
    return $contentNameClass->contentName_data($contentData, $frameWidth);
}


function cmsType_contentName($contentData,$frameWidth) {
    $contentNameClass = cmsType_contentName_class();
    $contentNameClass->show($contentData,$frameWidth);
}



function cmsType_contentName_editContent($editContent,$frameWidth) {
    $contentNameClass = cmsType_contentName_class();
    return $contentNameClass->contentName_editContent($editContent,$frameWidth);
}




?>
