<?php // charset:UTF-8


class cmsType_contentTypes extends  cmsType_contentTypes_base {

    function useType($type) {

        switch ($type) {
            case "companyList" : $useType = 1; break;
            case "empty" : $useType = 0; break;
            case "content" : $useType = 0; break;
            case "basket" : $useType = 0; break;
                
            default :
                $useType = 1;
        }
        return $useType;
    }

    
}

?>