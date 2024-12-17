<?php

class cms_userData_own extends cms_userData_base {
//    function get($getData=array()) {
//        $query = "";
//
//        foreach ($getData as $key => $value) {
//            if ($query) $query .= " AND ";
//            $query.= "`$key`='$value'";
//        }
//        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_userData` WHERE $query";
//
//
//        $result = mysql_query($query);
//        if (!$result) {
//            echo ("\n $query");        
//            return 0;
//        }
//
//        $anz = mysql_num_rows($result);
//        if ($anz == 0) return 0;
//        if ($anz > 1) return 0;
//
//        $data = mysql_fetch_assoc($result);
//
//        if (strlen($data[data])) $data[data] = str2Array ($data[data]);
//        else $data[data] = array();
//
//        return $data;
//
//    }
//    
//    function getList($filter,$sort="",$out="") {
//        $query = "";
//
//        foreach ($filter as $key => $value) {
//            if ($query) $query .= " AND ";
//            $query.= "`$key`='$value'";
//        }
//        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_userData` WHERE $query";
//
//
//        if ($sort) {
//            $upPos = strpos($sort, "_up");
//            $sortQuery = "";
//            if ($upPos) {
//                $sortValue = substr($sort,0,$upPos);
//                $sortQuery = "ORDER BY `$sortValue` DESC ";
//
//            }
//            if ($sortQuery=="") {
//               $sortQuery = "ORDER BY `$sort` ASC ";
//            }
//        } else {
//            $sortQuery = "ORDER BY `name` ASC ";
//        }
//
//        if ($out == "out") echo ("Query = '$query'<br>");
//
//        $result = mysql_query($query);
//        if (!$result) {
//            echo ("Error in Query '$query");
//            return 0;
//        }
//
//        $res = array();
//        while ($data = mysql_fetch_assoc($result)) {
//            if (strlen($data[data])) $data[data] = str2Array ($data[data]);
//            else $data[data] = array();
//            $res[] = $data;
//        }
//        return ($res);
//
//    }
//
   function bookmarks_state($userId,$url) {
       $actProject = $_SESSION[project];
       $actDrill   = $_SESSION[drill];
       // echo ("VMT $actProject $actDrill <br>");
       
       
       $res = $this->getList(array("userId"=>$userId,"type"=>"bookmark","url"=>$url));
       if (!is_array($res)) return 0; // kein Array;
       if (count($res)==0) return 0; // Keine Daten

       
       for ($i=0;$i<count($res);$i++) {
           $bookmark = $res[$i];
           if ($bookmark[data]) {
               $data = str2Array($bookmark[data]);
               if ($data[project]=$actProject AND $data[drill]==$actDrill) {
                   return 1;
               }
           }
       }
       return 0;
       
       
       
       
       
       if (is_array($res)) return $res[id];
       else return 0;    
    }
//
//    function bookmarks_deleteBookmark($bookmarkId) {
//        // echo ("\n delete Bookmark with id $bookmarkId");
//        $query = "DELETE FROM `$GLOBALS[cmsName]_cms_userData` WHERE `id`=$bookmarkId";
//        $result = mysql_query($query);
//        if (!$result) {
//            echo ("Error in Query '$query' ");
//            return 0;
//        } 
//        return 1;      
//    }
//    
//    
//    
    function bookmarks_setBookmark($userId,$url,$name="",$breadCrumb="") {
        // echo ("\n Set Bookmark $userId $url $name $breadCrumb ");
        $query = "INSERT INTO `$GLOBALS[cmsName]_cms_userData` SET ";
        $query .= "`userId`=$userId, ";
        $query .= "`type`='bookmark', ";
        $query .= " `url`='$url', ";
        $query .= " `breadCrumb`='$breadCrumb'";
        
        $dataStr = "";
        
        $actProject = $_SESSION[project];
        $actDrill   = $_SESSION[drill];
        $data = array();
        if ($actProject) {
            $data[project] = $actProject;
            if ($actDrill) {
                $data[drill] = $actDrill;
            }
            
            $dataStr = array2Str($data);
        }
        $query .= ", `data`='$dataStr' ";
        

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query '$query' ");
            return 0;
        } 
        return 1;    
   }
//    
//    function bookmarkList($userId) {
//        $filter = array();
//        $filter["userId"] = $userId;
//        $filter["type"] = "bookmark";
//
//        $sort = "breadCrumb";
//        $out = "";
//
//        $bookmarkList = $this->getList($filter,$sort,$out);
//        return $bookmarkList;
//    }
}

?>
