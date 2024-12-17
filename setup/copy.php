<?php

    session_start();

    $copyList = $_SESSION[copyFile];
    if (!is_array($copyList)) {
        echo "0";
        die();
    }
    $copyNr = $_SESSION[copyNr];
    $copyCount = $_SESSION[copyCount];
    $copySize = $_SESSION[copySize];
    $copySizeReady = $_SESSION[copySizeReady];
    if (!$copySizeReady) $copySizeReady = 0;

    if ($copyNr > $copyCount) {
        echo "0";
        die();
    }

     if ($copyNr > count($copyList)) {
        echo "0";
        die();
    }

    $copyFile = $copyList[$copyNr];
    if (is_array($copyFile)) {
        $file = $copyFile[file];
        $size = $copyFile[size];
        $url  = $copyFile[url];
        $path = $copyFile[path];

        $fileContent = file_get_contents($url, FILE_USE_INCLUDE_PATH);
        if ($fileContent) {
            $out .= "$file"; // - $size -ready = $copySizeReady ";
            $fp = fopen($path, "w");
            fwrite($fp, $fileContent);
            fclose($fp);
        } else {
            $_SESSION[copyErrorCount]++;
            $out .= "FEHLER";
        }

        $copySizeReady = $copySizeReady + $size;
        $percent = $copySizeReady * 100 / $copySize;
        $percent = $copyNr * 100 / $copyCount;

        $copyNr++;

        $_SESSION[copyNr] = $copyNr;
        $_SESSION[copySizeReady] = $copySizeReady;
        $out .= "|";
        $out .= number_format($percent,2,".",".")."%";
        echo ($out);
        die();


    }
    if ($copyNr == count($copyList)) echo ("ready"); die();
    echo ("count = $copyCount nr = $copyNr listAnz".count($copyList)." $copyFile");
?>
    