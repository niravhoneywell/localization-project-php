<? include 'connection.php'; ?>

<?php
    if(isset($_GET["SelectedCard"])) {
        $selected_card = $_GET["SelectedCard"];
    } else {
        die("Could not get selected card name");
    }

    if(isset($_GET["SelectedLanguage"])) {
        $selected_language = $_GET["SelectedLanguage"];
    } else {
        die("Could not get selected language");
    }

    if(isset($_GET["LanguageCode"])) {
        $language_code = $_GET["LanguageCode"];
    } else {
        die("Could not get selected language code");
    }
    
    // header("Content-type: application/octet-stream");
    // header("Content-Disposition: attachment; filename=".$selected_language.".strings");
    // header("Pragma: no-cache");
    // header("Expires: 0");
    // // header('Content-Length: '.filesize('file.txt'));

    $file_name = './Localization/'.'Localizable.strings';

    $search_query = "SELECT StringKeys, English, `$selected_language` FROM $selected_card WHERE `$selected_language` != '' ";

    if (!file_exists('./Localization')) {
        mkdir('./Localization', 0777, true);
    }
    
    $file = fopen($file_name, 'w') or die('Cannot open file:  '.$file_name);

    if ($file != NULL) {
        if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
            while($row = mysqli_fetch_assoc($search_result))
            {
                fwrite($file, "\"".$row['StringKeys']."\" = \"".$row[$selected_language]."\";\r\n");
            }
        }

        fclose($file);

        $zip = new ZipArchive;
        $tmp_file = $selected_card.".zip";

        if ($zip->open($tmp_file,  ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            //Second argument is path where you want to put file inside zip when it unzip as folder
            $zip->addFile($file_name, "/".$language_code.".lproj/".$selected_card.".strings");
            $zip->close();

            // echo 'Archive created! filename='.$tmp_file;
            // echo filesize($tmp_file);

            # send the file to the browser as a download
            if (file_exists($tmp_file)) {
                //Clear all header
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="'.basename($tmp_file).'"');
                header('Content-Length: '. filesize($tmp_file));
                readfile("./".$tmp_file);
            }
        } else {
            echo 'Failed creating zip!';
        }
    } else {
        echo 'Failed!';
    }

    unlink($file_name);
    unlink($tmp_file);

    exit();
?>

<!-- working functionality to download Localizable.strings. -->
<?php
    // if(isset($_GET["SelectedCard"])) {
    //     $selected_card = $_GET["SelectedCard"];
    // }

    // if(isset($_GET["SelectedLanguage"])) {
    //     $selected_language = $_GET["SelectedLanguage"];
    // }
    
    // header("Content-type: application/octet-stream");
    // header("Content-Disposition: attachment; filename=".$selected_language.".strings");
    // header("Pragma: no-cache");
    // header("Expires: 0");
    // // header('Content-Length: '.filesize('file.txt'));

    // $search_query = "SELECT StringKeys, English, `$selected_language` FROM $selected_card WHERE `$selected_language` != '' ";

    // $file = fopen('php://output', 'w');

    // if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
    //     while($row = mysqli_fetch_assoc($search_result))
    //     {
    //         fwrite($file, "\"".$row['StringKeys']."\" = \"".$row[$selected_language]."\";\r\n");
    //     }
    // }

    // fclose($file);
    // exit();
?>