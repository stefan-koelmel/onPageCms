<?php
    session_start();

//    $cmsName = "admin";
//    $_SESSION[cmsName] = $cmsName;
//
//    include("cms/cms.php");
//
// http://xhamster.com/movies/1406815/big_french_boobs_slut_fucked_hard.html
// http://xhamster.com/movies/1411168/wake_up_honey_i_039_ve_got_a_morning_wood_for_you.html
// http://xhamster.com/movies/1408209/studio_bust_out.html
//  http://xhamster.com/movies/1409086/sexy_black_nurse.html
// http://xhamster.com/movies/669309/big_tits_compilation_2.html
//http://xhamster.com/movies/1412890/vanessa_leon_finds_a_cock_to_play_with2.html
//http://xhamster.com/movies/1409460/nikki_jackson_hot_for_teacher_2.html
// http://xhamster.com/movies/1406110/three_lucky_students.html
// http://xhamster.com/movies/1044692/Hot_chick_getting_monster_cock.html
// http://xhamster.com/movies/1409091/beautiful_anna_nova_fucked_so_hard.html
//
// http://xhamster.com/movies/1791194/hot_maid_carmen_luvana.html
// http://xhamster.com/movies/1794943/full_movie_10.html
// 
//    // show Header
//    cms_header_show();

    

?>

<header>
    <title>CMS - MAIN PAGE </title>
</header>
    <body>
        <?php
     
        include("includes/connect.php");
        include("cms_base/help.php");
        // include("includes/help.php");
        // include("includes/pageStyles.php");
        // echo ("Hier<br>");
        // show_array($_SESSION);
        $id = $_GET[id];
        if ($id) {
            edit_cms($id);                
        } else {
            $cmsList = cmsList();
            
            
            $userLevel = $GLOBALS[userLevel]; 
            if (!$userLevel) $userLevel = $_SESSION[userLevel];
            
            // echo ("<h1>UserLevel = $userLevel </h1>");
            if ($userLevel) {
                show_cms($userLevel);
            } else {
                index_show_login();
                
            }
            
            foreach ($cmsList as $cmsName => $cmsValue) {
                $ses = $_SESSION[$cmsName."_session"];
                if (is_array($ses)) {
                    $userLevel = $ses[userLevel];
                    $cmsId = $cmsValue[id];
                    echo ("<a href='$cmsName/index.php' target='cms_$cmsName'>");
                    echo ("CMS $cmsName");
                    echo ("</a>");
                    if ($userLevel > 7) {
                        echo (" <a href='index.php?id=$cmsId'>edit</a>");
                    }
                    echo ("<br />");
                }
            }
            
            
        }
        
        
    function cmsList() {
        $cmsList = array();
        $query = "SELECT * FROM `cms_settings`";
         
        $result = mysql_query($query);
        if (!$result) return $cmsList;
        while ($cms = mysql_fetch_assoc($result) ) {
            $name = $cms[name];
            $cmsList[$name] = $cms;            
        }
        return $cmsList;                
    }
        
           
        
        
    function edit_cms($id) {
        if ($id == "new") {
            if ($_POST[create]) {
                $name = $_POST[name];
                $longName = $_POST[longName];
                if (strlen($name)>=3 AND strlen($longName)>3) {

                    $notName = array();
                    $notName[admin] = 1;
                    $notName[includes] = 1;
                    $notName[cms] = 1;
                    $notName[sytle] = 1;
                    if ($notName[$name]) {
                        echo ("Name nicht gestattet '$name' <br>");
                        $create = 0;
                    } else {
                        $query = "SELECT * FROM `cms_settings` WHERE `name` = '$name' ";
                        $result = mysql_query($query);
                        $anz = mysql_num_rows($result);
                        echo ("ANZ = $anz $name <br>");
                        if ($anz == 0) {

                            echo ("create CMS with '$name' / '$longName' <br>");


                            $query = "INSERT INTO `cms_settings` SET `name` = '$name', `longName` = '$longName' ";
                            $query .= ", `layout`='layout_standard', `width`='1020', ";
                            $query .= "`cache`=1, `show`=1, `bookmarks`=0, `history`=8, `wireframe`=1, ";
                            $query .= "`wireframe_theme`='none', `normal_theme`='none', ";
                            $query .= "`editColor`= 'blue', `editMode`='onPage2', ";

                            $specialData = array();
                            $specialData[user] = 1;
                            $specialData[images] = 1;
                            $query .= "`specialData`='".array2str($specialData)."' ";


                            // v1	1 	0 	0 	0 	layout_standard	none	none	blue	onPage2	1020 			a:1:{s:4:"need";s:0:"";}	a:2:{s:4:"user";s:1:"1";s:6:"images";s:1:"1";}	NULL

                            $result = mysql_query($query);

                            if ($result) {
                                echo ("CMS Created in DataBase <br>");
                                $anz = 1;
                            } else {
                                echo ("Error in $query <br>");
                            }
                        } 

                        if ($anz == 1) {
                            include("cms_new/admin/cms_admin_new.php");
                            $res = cmsAdminNew($name,$longName);
                            if ($res) {
                                echo ("<h1>CMS $name angelegt </h1>");
                                echo ("<a href='index.php'>Reload</a>");
                                // reloadPage("index.php",10);
                                die();
                            }
                            
                        }                        
                    }
                }
            }


            echo ("Neues cms Anlegen <br>");

            echo ("<form method='post' >");
            echo ("Name = <input type='text' name='name' value='$name'><br>");
            echo ("Name Lang <input type='text' name='longName' value='$longName'><br>");
            echo ("<input type='submit' name='create' value='anlegen' ><br>");
            echo ("</form>");
            echo ("<a href='index.php'>abbrechen</a><br>");


        }


        $query = "SELECT * FROM `cms_settings` where `id` = $id ";
        $result = mysql_query($query);
        if ($result ) {
            while ($cmsData = mysql_fetch_assoc($result)) {
                include("cms_new/admin/cms_admin_new.php");
                $res = cmsAdminEdit($cmsData);
                            // if ($res) {
//                echo ("Bearbeiten <br>");
//                foreach ($cmsData as $key => $value) echo ("$key => $value <br>");

                
                //echo ("<h1>$name</h1>");
                // show_array($cmsData);
            }
        }
    }
    
    
    function show_cms($userLevel) {
        $unsetList = array("cmsName","cmsSettings","cmsVersion","pageState","lastPages","adminLanguages");
        
//        unset($_SESSION[cmsName]);
//        unset($_SESSION[cmsSettings]);
        
        foreach ($_SESSION as $key => $value) {
            switch ($key) {
                case "userLevel" : break;
                case "showLevel" : break;
                case "userId" : break;
                case "edit" : break;
                case "defaultText" : break;
                case "adminText" : break;
                case "editMode" : break;
                default :
                    if (strpos($key,"_session")) {
                        echo ("dont Unset $key <br>");
                    } else {
                    
                    // echo ("unset $key <br />");
                        unset($_SESSION[$key]);
                    }
            }
            //echo ("$key => $value <br />");                    
        }

        $query = "SELECT * FROM `cms_settings`";
        $result = mysql_query($query);
        echo ("<h1>cms SHOW</h1>");
        if ($result ) {
            while ($cmsData = mysql_fetch_assoc($result)) {
                $name = $cmsData[name];
                $show = $cmsData[show];
                $edit = 0;
                if ($show == 0) {
                    if ($GLOBALS[userLevel] > 8) {
                        // echo ("Show $show $GLOBALS[userLevel] <br>");
                        $show = 1;
                    }
                }
                if ($edit == 0) {
                    if ($GLOBALS[userLevel] > 8) {
                        // echo ("Show $show $GLOBALS[userLevel] <br>");
                        $edit = 1;
                    }
                }

                if ($show) {
                    echo ("<a href='$name/index.php?login=1'>$name</a> ");
                    if ($edit) {
                        echo ("<a href='index.php?id=$cmsData[id]'>edit</a> ");

                    }
                    echo ("<br>\n");
                }


                //echo ("<h1>$name</h1>");
                // show_array($cmsData);
            }
        }

        if ($GLOBALS[userLevel] > 8) {
            echo ("<a href='index.php?id=new'>neues cms</a> <br>");
        }
    }
    
    
    function index_login($loginData) {
        if (!is_array($loginData)) return "noData";

        $loginUser = $loginData[userName];
        $loginPass = $loginData[password];
        
        if (strlen($loginUser)<6) return "shortUser";
        if (strlen($loginPass)<4) return "shortPass";
        
        $query = "SELECT * FROM `cms_settings`";
        $result = mysql_query($query);
        if (!$result) return "noDatabase found";
    
        while ($cmsData = mysql_fetch_assoc($result)) {
            $cmsName = $cmsData[name];
            $show = $cmsData[show];
            
            echo ("Try to login to $cmsName $show <br>");
            
            $query = "SELECT * FROM `".$cmsName."_cms_user` WHERE `userName`='$loginUser' AND `password`='$loginPass'";
            $result = mysql_query($query);
            if (!$result) {
                echo("Error in Query $query <br>");
            } else {
                $anz = mysql_num_rows($result);
                if ($anz == 1) return (index_doLogin($result,$cmsName));
                if ($anz == 0) {
                    // Try eMail
                    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` WHERE `email`='$loginUser' AND `password`='$loginPass'";
                    $result = mysql_query($query);
                    $anz = mysql_num_rows($result);
                    if ($anz == 1) return (index_doLogin($result,$cmsName));
                } else {
                    echo ("Gefunden in '$cmsName' $anz <br>");
                }
            }
        }


        echo ("Try to Login User with $loginUser / $loginPass <br>");


        return 0;
    }
    
    function index_doLogin($result,$cmsName) {
        $userData = mysql_fetch_assoc($result);
    
        $userId = $userData[id];

        // SET lastLogin
        $query = "UPDATE `".$cmsName."_cms_user` SET `lastLogin`='".date("y-m-d h:i:s")."'";
        // 0000-00-00 00:00:00'
        $firstLogin =$userData[first_log];
        if ($firstLogin == "0000-00-00 00:00:00") $query .= " , `first_log`=now()";
        $query .= " WHERE `id`= $userId";

        $result = mysql_query($query);
        if (!$result) {
            echo ("Error in Query $query <br>");
            return 0;
        }

        $_SESSION[userLevel] = $userData[userLevel];
        $_SESSION[showLevel] = $userData[userLevel];
        $_SESSION[userId]    = $userData[id];
        if ($userData[userLevel] > 6) {
            $_SESSION[editable] = 1;
        //    $_SESSION[edit] = 0;
        }
        else $_SESSION[editable] = 0;
        $_SESSION[edit] = 0;

        return 1;
    }
    
            
            
    function reloadPage_own($goWebPage,$seconds=0) {
        // echo ("reloadPage($goWebPage,$seconds) <br>");
        if ($seconds > 0) {
            echo ("<script type='text/javascript'>function a(){window.location.href='".$goWebPage."';} setTimeout('a()',".($seconds*1000).");</script>");
        } else {
            echo ("<script type='text/javascript'>window.location.href='".$goWebPage."';</script>");
        }
    }


    function index_show_login() {
        echo ("<h1>myCMS</h1>");
        echo ("Login<br/>");
        $frameWidth = 400;
        
        if ($_POST) {
            $login = $_POST[login];
        
            $resLogin = index_login($login);
            if ($resLogin == 1) {
                echo ("Angemeldet");
                //show_array($_SESSION);

                reloadPage("index.php",1);
                return 0;
            } else {
                echo ("Fehler bei der Anmeldung - $resLogin <br>");
            }

        } 

        $border = 0;
        $background = "#eee";
        $borderColor = "#555";
        $padding = 0;
        $innerWidth = $frameWidth - (2*$border) - (2*$padding);



        $leftWidth = 200;
        $leftAlign = "right";

        $inputWidth = 300;
        if ($innerWidth<$inputWidth) {
            $leftWidth = $innerWidth;
            $leftAlign = "left";

            $inputWidth = $innerWidth ;
        }



        $style = "width:".$innerWidth."px;";
        if ($border) $style .= "border:".$border."px solid $borderColor;";
        $style.="padding:".$padding."px;";#
        if ($background) $style.="background:$background;";

        echo ("<div  class='cmsLogin' >"); //,$style);
        echo("<form method='post' class='login'>");
        echo("<span style='width:".$leftWidth."px;text-align:$leftAlign;display:inline-block;' >");
        echo("Benutzername oder eMail");
        echo("</span>");
        echo ("<input type='text' name='login[userName]' value='$login[userName]' style='width:".$inputWidth."px;margin-bottom:".$padding."px;' class='loginUserName' ><br>");

        echo("<span style='width:".$leftWidth."px;text-align:$leftAlign;display:inline-block;' >");
        echo("Password");
        echo("</span>");
        echo("<input type='password' name='login[password]' value='$login[password]' style='width:".$inputWidth."px;margin-bottom:".$padding."px;' class='loginUserPass' ><br>");

        echo("<input type='submit' class='inputButton login' name='login[login]' value='anmelden' > ");

        echo("</form>");

        echo ("</div>");
    
        
    }
    
        
        ?>
    </body>
</html>
