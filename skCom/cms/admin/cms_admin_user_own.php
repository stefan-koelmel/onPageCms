<?php // charset:UTF-8

class cmsAdmin_user extends cmsAdmin_user_base {
    
    
     function edit_show_own($tableName,$specialData) {

        $editShow = array();
        $editShow[id]      = array("name"=>"Benutzer Id","show"=>1,"showLevel"=>9,"type"=>"text","width"=>"small");
        $editShow[id][needed] = 0;

        $editShow[userName]      = array("name"=>"Benutzer Name","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[userName][needed] = 1;

        $editShow[password]      = array("name"=>"Passwort","show"=>1,"type"=>"password","width"=>"standard");
        $editShow[password][needed] = 1;

        $editShow[userLevel]      = array("name"=>"Benutzer-Ebene","show"=>1,"type"=>"dropdown","width"=>"standard","showData"=>array("maxLevel"=>$_SESSION[userLevel]));
        $editShow[userLevel][needed] = 1;

        $editShow[salut]      = array("name"=>"Anrede","show"=>1,"type"=>"dropdown","width"=>"small");
        $editShow[salut][needed] = 1;

        $editShow[vName]      = array("name"=>"Vorname","show"=>1,"type"=>"text","width"=>"50%");
        $editShow[vName][needed] = 1;
        $editShow[vName][next] = "nName";
        
        $editShow[nName]      = array("name"=>"Nachname","show"=>1,"type"=>"text","width"=>"50%");
        $editShow[nName][needed] = 1;

        $editShow[company]      = array("name"=>"Firma","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[company][needed] = 0;

        $editShow[street]      = array("name"=>"Straße" ,"show"=>1,"type"=>"text","width"=>"80%");
        $editShow[street][needed] = 0;
        $editShow[street][next] = "streetNr";

        $editShow[streetNr]      = array("name"=>"Hausnummer" ,"show"=>1,"type"=>"text","width"=>"20%");
        $editShow[streetNr][needed] = 0;


        $editShow[plz]      = array("name"=>"Plz","show"=>1,"type"=>"text","width"=>"20%");
        $editShow[plz][needed] = 1;
        $editShow[plz][next] = "city";

        $editShow[city]      = array("name"=>"Ort","show"=>1,"type"=>"text","width"=>"80%");
        $editShow[city][needed] = 1;

        $editShow[email]      = array("name"=>"eMail","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[email][needed] = 1;

        $editShow[phone]      = array("name"=>"Telefon","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[phone][needed] = 0;

        $editShow[fax]      = array("name"=>"Fax","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[fax][needed] = 0;

        $editShow[mobil]      = array("name"=>"Mobil","show"=>1,"type"=>"text","width"=>"standard");
        $editShow[mobil][needed] = 0;

        $editShow[show]      = array("name"=>"Zeigen","show"=>1,"type"=>"checkbox","width"=>"small");
        $editShow[show][needed] = 0;

        $editShow[first_log]      = array("name"=>"Erste Anmeldung","show"=>1,"type"=>"text","width"=>"standard","disabled"=>1);
        $editShow[first_log][needed] = 0;

        
        $editShow[lastLogin]      = array("name"=>"Letze Anmeldung","show"=>1,"type"=>"text","width"=>"standard","readonly"=>1);
        $editShow[lastLogin][needed] = 0;

        $editShow[sessionId]      = array("name"=>"SessionId","show"=>1,"type"=>"text","width"=>"standard","disabled"=>1,"readonly"=>1);
        $editShow[sessionId][needed] = 0;
        

        return $editShow;

  
    }
    
    function editButtons_own($buttonList,$saveData) {
        if ($saveData[id]) {
            $buttonList[save][name] = "Benutzer speichern";           
        } else {
            $buttonList[save][name] = "Benutzer anlegen";
        }
        return $buttonList;
    }



    


 
}











?>
