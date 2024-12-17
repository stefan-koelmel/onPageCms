<?php // charset:UTF-8

 class cms_importExport extends cms_importExport_base {

     function __init() {

        $this->serverPath = "http://www.schaufenster-ka.de/cms/";
        $this->phpGetFile = "getData.php";

        $data = array();
        $data[serverPath] = "http://www.schaufenster-ka.de/cms/";
        $data[phpGetFile] = "getData.php";
        $data[tables][startName]= "schaufenster";
        // $data[tables][contain]="date";
        return $data;
     }

    function convertData_own($tableName,$data) {
        $res = "no Conversion for KlappeAuf table '".$tableName."'";
        switch ($tableName) {
            case "klappe_alt_termine" :
                $res = $this->klappe_convertTermine($data);
                break;
            case "klappe_alt_location_index" :
                $res = $this->klappe_convertLocationIndex($data);
                break;

            case "klappe_alt_locations" :
                $res = $this->klappe_convertLocation($data);
                break;

            case "klappe_date_dates" :
                $res = $this->klappe_convertTemine_new($data);
                break;
        }
        return $res;
    }
    
  

  
 }




?>
