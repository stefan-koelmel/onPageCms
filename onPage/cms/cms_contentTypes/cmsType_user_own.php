<?php // charset:UTF-8class cmsType_user extends cmsType_user_base {        function show_register_action($contentData,$saveData) {               $registerAction = $contentData[data][registerAction];        $res = 0;                switch ($registerAction) {            case "createTest" :                 $res = $this->registerCreate_testPage($contentData,$saveData);                break;                        }                       return $res;    }        function registerCreate_testPage($contentData,$saveData) {        $res = "Testzugang anlegen ist noch nicht fertig";                $data = $contentData[data];        if (!is_array($data)) $data = array();                $userId = $saveData[id];        $vName = $saveData[vName];        $nName = $saveData[nName];        $uName = $saveData[uName];                        $test = 0;        if ($test) {            $userId = 14;        }                    if (!$userId) $error = "Keine Benutzer ID erhalten<br />kein Testzugang angelegt!";                                if ($error) {            $res = array();            $res[error] = $error;            return $res;        }                // $pageData = cms_page_getData("test_1");        global $pageData;        $mainPageId = $pageData[id];                               $newPageData = array();        $newPageData[name] = "test_".$userId;        $newPageData[title] = "Testseite von $vName $nName";        $newPageData[description] = "";        $newPageData[keywords] = "";        $newPageData[imageId] = "";        $newPageData[dynamic] = 0;        $newPageData[layout] = "layout_standard";        $newPageData[navigation] = 1;        $newPageData[breadcrumb] = 1;        $newPageData[sort] = "100";        $newPageData[showLevel] = 3;        $newPageData[toLevel] = 0;        $newPageData[mainPage] = $mainPageId;                $addData = array();        $addData[allowedUser] = "|$userId|";        $addData[forbiddenUser] = "";        $newPageData[data] = array2Str($addData);                        $createResult = cms_page_create($newPageData[name], $newPageData);                if (!$createResult) {            $res = array();            $res[error] = "Fehler beim anlegen der Testseite";            return $res;        }                // Unterseite anlegen        $newPageData = array();        $newPageData[name] = "test_sub_".$userId."_1";        $newPageData[title] = "Unterseite von Testseite";        $newPageData[description] = "";        $newPageData[keywords] = "";        $newPageData[imageId] = "";        $newPageData[dynamic] = 0;        $newPageData[layout] = "layout_standard";        $newPageData[navigation] = 1;        $newPageData[breadcrumb] = 1;        $newPageData[sort] = "100";        $newPageData[showLevel] = 3;        $newPageData[toLevel] = 0;        $newPageData[mainPage] = $createResult;                $addData = array();        $addData[allowedUser] = "|$userId|";        $addData[forbiddenUser] = "";        $newPageData[data] = array2Str($addData);                echo ("CreateResult = $createResult <br>");                $createResult = cms_page_create($newPageData[name], $newPageData);        if (!$createResult) {            $res = array();            $res[error] = "Fehler beim anlegen der Unterseite von Ihrer Testseite";            return $res;        }                $res[outPut] = "Testseiten wurden erfolgreich angelegt!";                    //        foreach ($saveData as $key => $value) {//            echo ("SAVE $key = $value <br>");//        }        return $res;    }        function user_edit_notLoggedIn_own($editContent, $frameWidth) {        $data = $editContent[data];        if (!is_array($data)) $data = array();                        $res = array();                                                $addData["text"] = "eMail Bestättigen";        $registerEmail = $data[registerEmail];        if ($registerEmail) $checked = "checked='checked'"; else $checked = "";        $addData["input"] = "<input type='checkbox' name='editContent[data][registerEmail]' $checked value='1' >\n";        if ($registerEmail) {            $addData[input] .= "eMail an Benutzer: ";            $addData[input] .= cmsEmail_selectEmail($data[mailRegister],"editContent[data][mailRegister]" ,array("empty"=>"keine Mail"),array() ,"name");                               }        $addData["mode"] = "Simple";        $res[] = $addData;                        $addData = array();        $addData[text] = "Aktion nach registrieren";        $addData[input] = $this->user_edit_select_registerAction($editContent,$frameWidth);        $addData[mode] = "Admin";                $res[] = $addData;        return $res;            }        function user_edit_select_registerAction($editContent,$frameWidth) {        $data = $editContent[data];        if (!is_array($data)) $data = array();                $res = "<select name='editContent[data][registerAction]' value='$data[registerAction]' class='cmsSelectType' >";                $val = $data[registerAction];                        $actions = array();        $actions["createTest"] = "Test-User Erstellen";                if (!$val) $select="selected='1'"; else $select = "";        $res .= "<option value='0' $select >keine Aktion</option>";                foreach ($actions as $key => $value) {            if ($val == $key) $select="selected='1'"; else $select = "";            $res .= "<option value='$key' $select >$value</option>";        }                                       $res .= "</select>";        return $res;            }        }?>