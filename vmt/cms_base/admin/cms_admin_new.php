
<?php // charset:UTF-8
function cmsAdminNew($newName) {
    if (!$newName) {
        cms_errorBox ("kein Name angegeben");
        return 0;
    }
    
    global $cmsVersion;
    $cmsVersion = "base";
    $root = $_SERVER['DOCUMENT_ROOT']."/";
    
    
    echo ($root."cms_$cmsVersion/admin/cms_admin_settings.php<br>");
    include ($root."cms_$cmsVersion/admin/cms_admin_settings.php");
        
   
    // Create Folder
    if (is_dir($root.$newName."/")) {
        echo "Folder '$newName' exist allready <br>";
    } else {
        $root = $_SERVER['DOCUMENT_ROOT']."/";
        $res = mkdir($root.$newName, 0777);
        if (!$res) {
            echo "Folder nor Created <br>";
        }
    }
    if (!is_dir($root.$newName."/images/")) $res = mkdir($root.$newName."/images", 0777);
    if (!is_dir($root.$newName."/style/")) $res = mkdir($root.$newName."/style", 0777);
    if (!is_dir($root.$newName."/cms/")) $res = mkdir($root.$newName."/cms", 0777);
   
    cmsAdminNew_settings($newName,$cmsVersion);

//            include("../includes/connect.php");
    include("cms_base/cms_page.php");
    $cmsName = $newName;
    $GLOBALS[cmsName] = $newName;
//            $newName = "test";

    
    
    // USER DATABASE
    $tableData = tableData("user");
    $res = cmsAdminNew_user($newName,$tableData);
  
    if (!$res) {
        echo "Error by create User-Database <br>";
        die;
    }
    echo ("USER-Database create<br>");

    
   
    $tableData = tableData("text");
    $res = cmsAdminNew_text($newName,$tableData);
    if (!$res) {
        echo "Error by create Text-Database <br>";
        die;
    }
    echo ("TEXT-Database create <br>");
    
    $tableData = tableData("pages");
    
    $res = cmsAdminNew_pages($newName,$tableData);
    if (!$res) {
        echo "Error by create Pages-Database <br>";
        die;
    }
    echo ("PAGES-Database create <br>");

    $tableData = tableData("images");
    $res = cmsAdminNew_images($newName,$tableData);
    if (!$res) {
        echo "Error by create Images-Database <br>";
        die;
    }
    echo ("IMAGES-Database create <br>");
    
    $tableData = tableData("content");
    $res = cmsAdminNew_content($newName,$tableData);
    if (!$res) {
        echo "Error by create Content-Database <br>";
        die;
    }
    echo ("CONTENT-Database create <br>");
    

    $res = cmsAdminNew_createLayout($newName,"layout_standard");
    if (!$res) {
        echo "Error by create Layout-Database <br>";
        die;
    }
    echo ("LAYOUT 'layout_leftNavi' -Database create <br>");

    $res = cmsAdminNew_category($newName);
    if (!$res) {
        echo "Error by create Category-Database <br>";
        die;
    }
    
    
    $data = array();
    $data[company] ="Company";
    $data[product] ="Produkte";
    $data[category] ="Kategorien";
    $data[email] ="eMail";
    $data[articles] ="Artikel";
    $data[dates] ="Termine";
    
    $data[project]   = "Projekte";
    $data[bookmarks] = "Bookmarks";
    
    foreach ($data as $code => $name) {
        $tableData = tableData($code);
        if (is_array($tableData)) {
            echo ("Create Table for $code $name <br>");         
            
            $res = cmsAdminNew_Data($newName,$code,$tableData);
            if (!$res) {
                echo "Error by create $name-Database <br>";
                die;
            }
            echo ("$name-Database create <br>");            
        } else {
            echo ("No TableData for $code $name <br>");
        }
    }
    
    
    return 1;
    
    
  
}

function cmsAdminNew_settings($newName,$cmsVersion) {
    $zub = "\r";
     $outFile = $newName."/cmsSettings.php";
     $saveText = "<?php".$zub;
     $saveText .= "global $"."cmsName,$"."cmsVersion;".$zub;
     $saveText .= '$cmsName = "'.$newName.'";'.$zub;
     $saveText .= '$cmsVersion = "'.$cmsVersion.'";'.$zub;
     $saveText .= '?>';
     echo ("SAVETEXT <br> $saveText<br>");
     saveText($saveText, $outFile);
}
        // put your code here

function cmsAdminNew_TableExist($newName,$code) {
    $query = "SELECT * FROM `".$newName."_cms_$code` ";
    $result = mysql_query($query);
    if (!$result) {
        //echo "NOT Exist `".$newName."_cms_$code` $query <br>";
        return "notExist";
    }
    $anz = mysql_num_rows($result);
    // echo " Exist `".$newName."_cms_$code` $anz <br>";
    return $anz;
}

function createTable($newName,$code,$tableData) {
    foreach ($tableData as $key => $value) {
        if ($query) $query.= ", ";
        $query .= "`$key` ";
        foreach($value as $nr => $data) {
            $query.= "$data ";
        }
        
    }
    
    $query = "CREATE TABLE `".$newName."_cms_".$code."` (".$query;
    
    $query .= ", INDEX(`id`) ) CHARACTER SET = utf8;";
    // echo ($query."<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in <br>$query <br>");
        return 0;
    }
    return 1;
    
}


function insertTable($newName,$code,$tableData,$insertData) {
    $query = "";
    foreach ($insertData as $key => $value ) {
        // echo ("$key => $value <br>");
        if ($tableData[$key]) {
            if ($query) $query .= ", ";
            $query .= "`$key`='$value' ";
        }                
    }
    if ($query) {
        $query = "INSERT INTO `".$newName."_cms_".$code."` SET ".$query;
        $result = mysql_query($query);
        if ($result) {
            $insertId = mysql_insert_id();
            echo ("Daten mit $insertId angelegt in $newName $code <br>");
            return 1;
        } else {
            echo ("Error in Query $query <br>");
            return 0;
        }
    }
}


function cmsAdminNew_user($newName,$tableData) {
    $code = "user";

    $exist = cmsAdminNew_TableExist($newName,$code);

    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_user` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_user` <br>");
            return 0;
        }
        $exist = 0;
    }
    
    if ($exist == 0) {
        $insertData = array("id"=>1,"userName"=>"superadmin","password"=>"nmzu70wsx","userLevel"=>9,"email"=>"sk@stefan-koelmel.com","vName"=>"Stefan","nName"=>"Kölmel");
        $insertResult = insertTable($newName,$code,$tableData,$insertData);
        if (!$insertResult) {
            return 0;
        }     
        $exist = 1;
    }
    if ($exist == 1) {
        $insertData = array("id"=>2,"userName"=>"cmsadmin","password"=>"$newName","userLevel"=>9,"email"=>"cmsadmin@stefan-koelmel.com","vName"=>"CMS","nName"=>"Admin");
        $insertResult = insertTable($newName,$code,$tableData,$insertData);
        if (!$insertResult) {
            
            return 0;        
        }        
    }
    return 1;
}

function cmsAdminNew_text($newName,$tableData) {
    $code = "text";
    $exist = cmsAdminNew_TableExist($newName,$code);

    
    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_text` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_user` <br>");
            return 0;
        }
        $exist = 0;
    }   
    return 1;

}


function cmsAdminNew_pages($newName,$tableData) {
    $code = "pages";
    $exist = cmsAdminNew_TableExist($newName,$code);

    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_$code` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_$code` <br>");
            return 0;
        }       
    }
    $exist = 0;
    if ($exist == 0) {        
        cms_page_create("index",$newData=array("title"=>"Startseite","navigation"=>1,"breadcrumb"=>1));
        $exist++;
    }

    if ($exist == 1) {
        cms_page_create("sitemap",$newData=array("title"=>"Sitemap","navigation"=>1,"breadcrumb"=>1));
        $exist++;
    }

    if ($exist == 2) {
        cms_page_create("impressum",$newData=array("title"=>"Impressum","navigation"=>1,"breadcrumb"=>1));
        $exist++;
    }

    if ($exist == 3) {
        cms_page_create("admin",$newData=array("title"=>"CMS Administration","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8));
        $exist++;
    }


    if ($exist == 4) {
        cms_page_create("admin_cmsCms",$newData=array("title"=>"CMS Verwaltung","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>4));
        $exist++;
    }

    if ($exist == 5) {
        cms_page_create("admin_data",$newData=array("title"=>"CMS Daten","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>4));
        $exist++;
    }


    if ($exist == 6) {
        cms_page_create("admin_cmsSettings",$newData=array("title"=>"CMS Einstellungen","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>5));
        $exist++;
    }

    if ($exist == 7) {
        cms_page_create("admin_cmsLayout",$newData=array("title"=>"CMS Layout","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>5));
        $exist++;
    }

    if ($exist == 8) {
        cms_page_create("admin_cmsImages",$newData=array("title"=>"CMS Bilder","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>5));
        $exist++;
    }


    if ($exist == 9) {
        cms_page_create("admin_cmsUser",$newData=array("title"=>"Benutzer","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));
        $exist++;
    }

    if ($exist == 10) {
        cms_page_create("admin_cmsDates",$newData=array("title"=>"Termine","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));
        $exist++;
    }


    if ($exist == 11) {
        cms_page_create("admin_cmsMail",$newData=array("title"=>"eMail Verwaltung","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));
        $exist++;
    }

    if ($exist == 12) {
        cms_page_create("layout_standard",$newData=array("title"=>"Standard Layout","navigation"=>0,"breadcrumb"=>0,"showLevel"=>0,"mainPage"=>0));
        $exist++;
    }

    return 1;
}

function cmsAdminNew_images($newName,$tableData) {
    
    $code = "images";
    $exist = cmsAdminNew_TableExist($newName,$code);

    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);         
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_$code` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_$code` <br>");
            return 0;
        }       
    }   
    return 1;
}

function cmsAdminNew_content($newName,$tableData) {
    
    $code = "content";
    $exist = cmsAdminNew_TableExist($newName,$code);

    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);         
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_$code` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_$code` <br>");
            return 0;
        }       
    }   
    return 1;
    
    
}

function cmsAdminNew_Data($newName,$code,$tableData) {
    $exist = cmsAdminNew_TableExist($newName,$code);
    if (!is_int($exist)) {
        $createQuery = createTable($newName,$code, $tableData);         
        if ($createQuery) {
            echo ("CREATE TABLE `".$newName."_cms_$code` <br>");
        } else {
            echo ("Fehler beim anlegen von `".$newName."_cms_$code` <br>");
            return 0;
        }       
    }   
    return 1;
}


function cmsAdminNew_category($newName) {
    $exist = cmsAdminNew_TableExist($newName,"category");

    if (!is_int($exist)) {
        $query = "";
        $query.= "CREATE TABLE `".$newName."_cms_category` (";

        $query.= "`id` int( 11 ) NOT NULL AUTO_INCREMENT ,";
        $query.= "`name` tinytext NOT NULL ,";
        $query.= "`info` text NOT NULL ,";
        $query.= "`mainCat` int( 11 ) NOT NULL ,";
        $query.= "`image` int( 11 ) NOT NULL ,";
        $query.= "`show` varchar( 1 ) NOT NULL ,";
        $query.= "KEY `id` ( `id` )";
        $query.= ") CHARACTER SET = latin1;";

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in $query <br>");
            return 0;
        }
    }
    return 1;
}


function cmsAdminNew_company($newName) {
    $exist = cmsAdminNew_TableExist($newName,"company");

    if (!is_int($exist)) {
        $query = "";
        $query.= "CREATE TABLE `".$newName."_cms_company` (";

        $query.= "`id` int(11) NOT NULL auto_increment, ";
        $query.= "`name` tinytext NOT NULL,";
        $query.= "`info` text NOT NULL,";
        $query.= "`image` int(11) NOT NULL,";
        $query.= "`url` tinytext NOT NULL,";
        $query.= "`show` varchar(1) NOT NULL default '1',";
        $query.= " KEY `id` (`id`)";
        $query.= ") CHARACTER SET = latin1;";

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in $query <br>");
            return 0;
        }
    }
    return 1;
}


function cmsAdminNew_product($newName) {
    $exist = cmsAdminNew_TableExist($newName,"product");

    if (!is_int($exist)) {
        $query = "";
        $query.= "CREATE TABLE `".$newName."_cms_product` (";

        $query.= "`id` int(11) NOT NULL auto_increment,";
        $query.= "`name` tinytext NOT NULL,";
        $query.= "`info` text NOT NULL,";
        $query.= "`company` int(11) NOT NULL,";
        $query.= "`category` int(11) NOT NULL,";
        $query.= "`image` int(11) NOT NULL,";
        $query.= "`show` varchar(1) NOT NULL default '1',";
        $query.= "`new` varchar(1) NOT NULL default '0',";
        $query.= "`highlight` varchar(1) NOT NULL default '0',";
        $query.= "`vk` float NOT NULL,";
        $query.= "`count` tinyint(4) NOT NULL,";
        $query.= "KEY `id` (`id`)) CHARACTER SET = latin1;";

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in $query <br>");
            return 0;
        }
    }
    return 1;
}



function cmsAdminNew_createLayout($newName,$layoutName) {

    $exist = cmsAdminNew_TableExist($newName,"content");

    if ($exist == 0) {


        // cms_page_create($layoutName,$newData=array("title"=>$layoutName,"navigation"=>0,"breadcrumb"=>0,"showLevel"=>8,"mainPage"=>0));
        //cms_page_create("admin_cmsDates",$newData=array("title"=>"Termine","navigation"=>1,"breadcrumb"=>1,"showLevel"=>8,"mainPage"=>6));

        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('$layoutName',  1, 0, 'header', '');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}



        // INSERT FRAME
        $frameArray = array("width1"=>180,"abs1"=>10);
        $frameArray = array2Str($frameArray);

        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('$layoutName',  2, 0, 'frame2', '$frameArray');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}
        $frameId = mysql_insert_id();

        // INSERT TO LEFT FRAME
        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('frame_".$frameId."_1',  1, 0, 'navi', '');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}

        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('frame_".$frameId."_1',  2, 0, 'login', '');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}

        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('frame_".$frameId."_1',  3, 0, 'social', '');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}

        // INSERT TO RIGHT FRAME
        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('frame_".$frameId."_2',  1, 0, 'content', '');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}


        $query = "INSERT INTO `".$newName."_cms_content` (`pageId`, `sort`, `showLevel`, `type`, `data`) VALUES ('$layoutName',  3, 0, 'footer', '');";
        $result = mysql_query($query);
        if (!$result) { echo ("Error in $query <br>"); return 0;}
    }
    return 1;

}

?>