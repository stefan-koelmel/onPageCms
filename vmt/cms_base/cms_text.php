<?php // charset:UTF-8

function cms_text_getForContent($contentName) {
   // echo ("Get Text for contentName $contentName <br>");
    $lg = $GLOBALS[pageInfo][lg];
    if (!$lg) {
        $lg="dt";
        $pageInfo[lg] = $lg;
        echo ("Set LG to $lg <br>");
    }




    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_text` WHERE `contentId` = '$contentName' ";

    $textList = array();
    $result = mysql_query($query);
    if(!$result) {
        echo ("error in query $query<br>");
        return $textList;
    }

    while ($textData = mysql_fetch_assoc($result)) {
        $name = $textData[name];
        $id = $textData[id];
        switch ($lg) {
            case "dt" : $text = $textData[lg_dt]; break;

        }
        $css = $textData["css"];
        // echo ("Get from db $name: $text $css <br>");
        $textList[$name] = array("id"=>$id,"text"=>$text,"css"=>$css,"data"=>$textData[data]);
    }
    // echo ("Found ".count($textList)."<br>");
    return ($textList);

}

function cmsText_get($getData) {
    $query = "";
    foreach($getData as $key => $value) {
        if ($value) {
            if ($query) $query .= ", ";
            $query .= "`$key` = '$value' ";
        }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_text` WHERE $query";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        echo ("Not found <br>");
        return 0;
    }
    if ($anz > 1) {
        echo ("More found <br>");
        return 0;
    }   
    $data = mysql_fetch_assoc($result);
    return $data;
}


function cms_text_insert($data) {
    if (!is_array($data)) { echo ("noData for cms_text_insert ($data)<br>"); return 0;}
    foreach ($data as $key => $value ) {
        echo ("cms_insert $key = $value <br>");
    }
    return 0;
}

function cms_text_delete($textId) {
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_text` WHERE `id`=$textId ";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in query $query <br>");
    }
}

function cms_text_deleteContent($contentName) {
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_text` WHERE `contentId`='$contentName' ";
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in query $query <br>");
    }
}

function cms_text_save($data) {
    if (!is_array($data)) { echo ("noData for cms_text_insert ($data)<br>"); return 0;}

    global $pageInfo;
 
    $id = $data[id];
    $css = $data[css];
    $contentId = $data[contentId];
    $name = $data[name];
    $text = $data[text];


    if (!$contentId) {
        $editId = $_GET[editId];
        if ($editId) $contentId = $editId;
    }

    if (!$contentId) {
        echo ("no ContentId in cms_text_save<br>");
        return 0;
    }
    
    if (!$text) {
        if ($id) {
            echo ("Delete Text with id $id because empty <br>");
            cms_text_delete($id);
            // cms_text_deleteContent("text_27");
            return 1;
        }
    }

    if ($id) $query = "UPDATE `".$GLOBALS[cmsName]."_cms_text` SET ";
    else $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_text` SET ";
    $query .= "`contentId`='$contentId' ";
    $query .= ", `css`='$css' ";
    $query .= ", `name`='$name' ";
    if ($data[data]) $query .= ", `data`='$data[data]'";
    switch ($pageInfo[lg]) {
        case "dt" : $query .= ", `lg_dt`='$text'"; break;
        case "en" : $query .= ", `lg_en`='$text'"; break;
        default :
            echo ("unkown lg '$lg' in cms_save_text<br>");
            foreach($pageInfo as $key => $value )echo ("pI $key = $value<br>");
            return 0;
    }

    if ($id) $query .= " WHERE `id` = $id ";

    $result = mysql_query($query);
    // $result = 0;
    if (!$result) {
         echo ("Error in Query for cms_save_text '$query'<br>");
         return 0;
    }
    return 1;
}


function cmsText_update($data,$compareData) {
    
    $id = $data[id];
    $query = "";
    $diff = 0;
    foreach ($data as $key => $value) {
        if ($value == $compareData[$key]) {
            // echo ("Same $value == $compareData[$key] <br>");
        } else {
            echo ("Diffrent $value != $compareData[$key] <br>");
            $diff++;
            if ($query) $query .= ", ";
            $query .= "`$key`='$value'";
        }
    }
    if ($diff == 0) {
        // echo ("No Change for $id! <br>");
        return 1;
    } 
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_text` SET ".$query." WHERE `id` = $id";
    $result = mysql_query($query);
    if (!$result) {
        echo ("$diff Changes !<br>");
        echo ("Error in Query = '$query' <br>");
        return 0;
    }
    
    return 1;
}

function cmsText_save($data) {
    
    $id = $data[id];
    if ($id) {
        $compareData = cmsText_get(array("id"=>$id));
        if (is_array($compareData)) {
            $res = cmsText_update($data,$compareData);
            return $res;
        } 
        
    }
    
    
    $query = "";
    foreach ($data as $key => $value) {    
        if ($query) $query .= ", ";
        $query .= "`$key`='$value'";    
    }
    
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_text` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error by Insert $query <br>");
        return 0;
    }
    $insertId = mysql_insert_id();
    echo ("Insert id = $data[id] -> insertid = $insertId <br>");
    
    
    // show_array($data);
    return 1;
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
