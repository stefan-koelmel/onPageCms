<?PHP // charset:UTF-8
if (file_exists($path . $_GET['file'])) {
    $path = "";

    header("Content-type: application/octet-stream\n");
    header("Content-disposition: attachment; filename=\"" . $_GET['file'] . "\"\n");
    header("Content-transfer-encoding: binary\n");
    header("Content-length: " . filesize($path . $_GET['file']) . "\n");
    $fp = fopen($path . $_GET['file'], "r");
    fpassthru($fp);
    fclose ($fp);
} else {
    echo "datei existiert nicht";
}