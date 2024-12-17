<?php

class cms_userData_base  {
    function get($getData=array()) {
        $query = "";

        foreach ($getData as $key => $value) {
            if ($query) $query .= " AND ";
            $query.= "`$key`='$value'";
        }
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_userData` WHERE $query";
        // echo ($query);
        $result = mysql_query($query);
        if (!$result) {
            echo ("\n $query");        
            return 0;
        }

        $anz = mysql_num_rows($result);
        if ($anz == 0) return 0;
        if ($anz > 1) return 0;

        $data = mysql_fetch_assoc($result);

        if (strlen($data[data])) $data[data] = str2Array ($data[data]);
        else $data[data] = array();

        return $data;
     

    }
    
    function getList($filter,$sort="",$out="") {
        $query = "";

        foreach ($filter as $key => $value) {
            if ($query) $query .= " AND ";
            $query.= "`$key`='$value'";
        }
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_userData` WHERE $query";


        if ($sort) {
            $upPos = strpos($sort, "_up");
            $sortQuery = "";
            if ($upPos) {
                $sortValue = substr($sort,0,$upPos);
                $sortQuery = "ORDER BY `$sortValue` DESC ";

            }
            if ($sortQuery=="") {
               $sortQuery = "ORDER BY `$sort` ASC ";
            }
        } else {
            $sortQuery = "ORDER BY `name` ASC ";
        }

        if ($out == "out") echo ("Query = '$query'<br>");

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query '$query");
            return 0;
        }

        $res = array();
        while ($data = mysql_fetch_assoc($result)) {
            if (strlen($data[data])) $data[data] = str2Array ($data[data]);
            else $data[data] = array();
            $res[] = $data;
        }
        return ($res);

    }

    function bookmarks_state($userId,$url) {
        $setPage = str_replace(array("?","&"), "|",$url);
        $setPage = str_replace("=","-", $setPage);
        $res = $this->get(array("userId"=>$userId,"type"=>"bookmark","url"=>$setPage));
        if (is_array($res)) return $res[id];
        else return 0;    
    }

    function bookmarks_deleteBookmark($bookmarkId) {
        // echo ("\n delete Bookmark with id $bookmarkId");
        $query = "DELETE FROM `$GLOBALS[cmsName]_cms_userData` WHERE `id`=$bookmarkId";
        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query '$query' ");
            return 0;
        } 
        return 1;      
    }
    
    
    
    function bookmarks_setBookmark($userId,$url,$name="",$breadCrumb="") {
        // echo ("\n Set Bookmark $userId $url $name $breadCrumb ");
        $query = "INSERT INTO `$GLOBALS[cmsName]_cms_userData` SET ";
        $query .= "`userId`=$userId, ";
        $query .= "`type`='bookmark', ";
        $query .= " `url`='$url', ";
        $query .= " `breadCrumb`='$breadCrumb'";

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query '$query' ");
            return 0;
        } 
        return 1;    
    }
    
    function bookmarkList($userId) {
        $filter = array();
        $filter["userId"] = $userId;
        $filter["type"] = "bookmark";

        $sort = "breadCrumb";
        $out = "";

        $bookmarkList = $this->getList($filter,$sort,$out);
        return $bookmarkList;
    }
}

function cmsUserData_class() {
    $cmsName = $GLOBALS[cmsName];
    $fn = $_SERVER[DOCUMENT_ROOT]."/$cmsName/cms/data/cms_userData_own.php";
    if (file_exists($fn)) {
        include_once($fn);
    
       // echo ("<h1>EXIST</h1>");
        $class = new cms_userData_own();
    } else {
        $class = new cms_userData_base();
    }
    return $class;
}

function cmsUserData_get($getData=array()) {
    $class = cmsUserData_class();
    $res = $class->get($getData);
    return $res;
}


function cmsUserData_getList($filter,$sort="",$out="") {
    $class = cmsUserData_class();
    $res = $class->getList($filter,$sort,$out);
    return $res;
}



function cmsUserData_bookmarks_state($userId,$url) {
    $class = cmsUserData_class();
    $res = $class->bookmarks_state($userId,$url);
    return $res;
}
    
   

function cmsUserData_bookmarks_deleteBookmark($bookmarkId) {
    $class = cmsUserData_class();
    $res = $class->bookmarks_deleteBookmark($bookmarkId);
    return $res;         
}

function cmsUserData_bookmarks_setBookmark($userId,$url,$name="",$breadCrumb="") {
    $class = cmsUserData_class();
    $res = $class->bookmarks_setBookmark($userId,$url,$name,$breadCrumb);
    return $res;    
}

function cmsUserData_bookmarkList($userId) {
    $class = cmsUserData_class();
    $res = $class->bookmarkList($userId);
    return $res;
}

?>
