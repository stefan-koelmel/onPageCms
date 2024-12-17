<?php // charset:UTF-8

function cms_admin_cmsLayout(){
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `name` LIKE 'layout_%' ";
    $result = mysql_query($query);
    if (!$result) {
        cms_errorBox("Fehler beim Abfragen der Layouts");
        return 0;
    }

    $editLayout = $_GET[editLayout];
     if ($editLayout) {
        $pageWidth = $GLOBALS[cmsSettings][width];
        cms_content_show($editLayout,$pageWidth);

    } else {
        while ($layout = mysql_fetch_assoc($result)) {
            $id = $layout[id];
            $name = $layout[name];
            $title= $layout[title];

            echo ("Layout $name $title $id ");
            if ($name != $editLayout) {
                echo ("<a href='$pageInfo[pageName]?editLayout=$name' >editieren </a>");
            }
            echo ("<br>");

        }        
    }

   




}
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
