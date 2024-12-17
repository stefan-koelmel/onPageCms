<?php // charset:UTF-8
class cmsType_text extends cmsType_text_base {

    function styleList_filter_select_getOwnList($styleName,$sort) {
        $res = array();
        switch ($styleName) {
            case ("text") :
                $res[big] = "Großer Text";
                $res[small] = "Kleiner Text";
                break;
        }

        return $res;
    }



}
?>
