<?php

class user {

    public static function showLevel() { return session::get("showLevel"); }
    public static function userLevel() { return session::get("userLevel"); }
    public static function userId() { return session::get("userId"); }
    
    
    public static function userData($userId=null) {
        if (!$userId) {
            $userId = self::userId();
        }
        if (!$userId) return "no UserId";
        
        $userData = cms_user_getById($userId);
        return $userData;
    }
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // USER DATA                                                              //
    
    // BOOKMARKS ///////////////////////////////////////////////////////////////
    
    public static function bookmark_List() {
        $bookMarkList = self::userData_bookmarkList();
        return $bookMarkList;        
    }
    
    public static function bookmark_isBookMark($pageName=null) {
        if (!$pageName) $pageName = page::actPage();
        
        $userId = self::userId();
        if (!$userId) return "noUserId";
        
        $filter = array("userId"=>$userId,"type"=>"bookmark","name"=>$pageName);
        $sort = "";
        $out = "assoName";
        $list =  self::userData_getList($filter,$sort,$out);
        if (count($list)) $exist = 1;
        else $exist = 0;
        return $exist;
    }
    
    public static function bookmark_toogle($pageName=null) {
        if (!$pageName) $pageName = page::actPage();
        $exist = self::bookmark_isBookMark($pageName);
        if ($exist) {
            $res = self::bookmark_remove($pageName);
            return 0;
        } else {
            $res = self::bookmark_create($pageName);
            return 1;
        }
        
    }
    
    
    private function bookmark_remove($pageName) {
        $userId = self::userId();
        if (!$userId) return "noUserId";
        $delete = array();
        $delete[userId] = $userId;
        $delete[type] = "bookmark";
        $delete[name] = $pageName;
        
        $res = self::userData_delete($delete);
        return $res;
    }
    
    private function bookmark_create($pageName) {
        if (!$pageName) return "noPageName";
        $userId = self::userId();
        if (!$userId) return "noUserId";
        $insert = array();
        $insert[userId] = $userId;
        $insert[type] = "bookmark";
        $insert[name] = $pageName;
        
        $res = self::userData_create($insert);
        return $res;
    }
    
    
    
    private function userData_bookmarkList() {
        $userId = self::userId();
        if (!$userId) return "noUserId";
        
        $filter = array("userId"=>$userId,"type"=>"bookmark");
        $sort = "";
        $out = "assoName";
        $list =  self::userData_getList($filter,$sort,$out);
        return $list;
    }
    
    private function userData_getList($filter=array(),$sort="",$out="") {
        $cmsName = cms::$cmsName;
        
        $query = "";
        foreach ($filter as $key => $value) {
            if ($query) $query .= " AND ";
            $query .= "`$key` = '$value' ";
        }
        $query = "SELECT * FROM `".$cmsName."_cms_userData` WHERE ".$query;
        
        $result = mysql_query($query);
        if (!$result) {
            echo "Error in Query '$query' <br>";
            return "error";
        }
        $list = array();
        while ($userData = mysql_fetch_assoc($result)) {
            switch ($out) {
                case "assoName" :
                    $asso = $userData[name];
                    break;
            }
            if ($asso) $list[$asso] = $userData;
            else $list[] = $userData;            
        }
        return $list;
    }
    
    private function userData_delete($delete) {
        $cmsName = cms::$cmsName;
        $query = "";
        foreach ($delete as $key => $value) {
            if ($query) $query .= "AND ";
            $query .= "`$key` = '$value' ";
        }
        $query = "DELETE FROM `".$cmsName."_cms_userData` WHERE ".$query;
        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query $query <br>");
            return 0;
        }
        return 1;        
    }
    
    private function userData_create($insert) {
        $cmsName = cms::$cmsName;
        $query = "";
        foreach ($insert as $key => $value) {
            if ($query) $query .= " , ";
            $query .= "`$key` = '$value' ";
        }
        $query = "INSERT INTO `".$cmsName."_cms_userData` SET  ".$query;
        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query $query <br>");
            return 0;
        }
        return 1;
        echo (" $query <br>");
                
    }
    
    
    
    
    
    
    
}

?>
