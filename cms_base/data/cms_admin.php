<?php


    function cmsAdmin_checkTableExist($tableName) {
        $functionExist = function_exists("tableList");
        if (!$functionExist) {
            $fn = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/admin/cms_admin_settings.php";
            
        
            if (file_exists($fn)) {
                include($fn);
            } else {
                echo ("Not EXIST $fn <br />");
                return 0;                
            }
        }
        
        $cmsName = $GLOBALS[cmsName];
        $dbExist = cmsAdmin_checkTable($tableName, $cmsName);
        
        echo ("exist TRable $tableName = $dbExist <br>");
        return $dbExist;
        cms_contentType_show();
    }

    function cmsAdmin_data_getAll() {
        $res = array();
        $res["dates"] = "Termine";
        $res["company"] = "Hersteller";
        $res["product"] = "Produkte";
        $res["category"] = "Kategorien";
        $res["email"] = "eMail Verwaltung";
        $res["user"] = "Benutzer";
        $res["location"] = "Locations";
        $res["importExport"] = "Import & Export";
        $res["articles"] = "Artikel";
        $res["images"] = "Bilder";
        $res["project"] = "Projekte";

        return $res;
    }

    function cmsAdmin_activeData($out="list") {
        echo ("cmsAdmin_activeData($out)<br>");
        $res = array();

        $allData = cmsAdmin_data_getAll();

        $specialData = $_SESSION[cmsSettings][specialData];
        if (!is_array($specialData)) $specialData = str2Array($specialData);

        if (is_array($specialData)) {
            if ($out == "list") return $specialData;
            
            foreach ($specialData as $key => $value) {
                switch ($out) {
                    case "name" :
                        $res[$key] = array();
                        $res[$key][name] = $allData[$key];
                        break;

                    default :
                        $res[$key] = 1;
                }
            }
    
        }
        return $res;
    }


    function cmsAdmin_getData_forName($name) {





   
     }

    function cmsAdmin_getType_forFile($file) {
        $type = 0;
        switch ($file) {
            case "admin_company"          : $type = "company"; break;
            case "admin_cmsCompany"       : $type = "company"; break;
            
            case "admin_cmsMail"          : $type = "email"; break;
            case "admin_cmsUser"          : $type = "user"; break;
            case "admin_cmsDates"         : $type = "dates"; break;
            
            case "admin_product"          : $type = "product"; break;
            case "admin_product"          : $type = "product"; break;
            
            case "admin_category"         : $type = "category"; break;
            case "admin_cmsCategory"      : $type = "category"; break;
            
            case "admin_location"         : $type = "location"; break;
            case "admin_cmsLocation"      : $type = "location"; break;
            
            case "admin_importExport"     : $type = "importExport"; break;
            case "admin_cmsImportExport"  : $type = "importExport"; break;
            
            case "admin_articles"         : $type = "articles"; break;
            case "admin_cmsArticles"      : $type = "articles"; break;
            
            case "admin_project"       : $type = "project"; break;
            case "admin_cmsProject"       : $type = "project"; break;
            
            case "admin_images"        : $type = "images"; break;
            case "admin_cmsImages" : $type = "images"; break;

            case "admin_cmsSettings" : $type = "settings"; break;

            case "admin_cmsLayout" : $type = "layout"; break;
            

            default :
                echo ("unkownType $name <br>");
                $type = "unkown";
        }
        return $type;
    }
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
