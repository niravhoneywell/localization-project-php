<? include 'connection.php'; ?>
<?
    //Remove Localization Directory
    function deleleLocalizationFolder(){
        $dir = "./Localization";
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
                        RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }
?>
<?php
    if(isset($_GET["SelectedCard"])) {
        $selected_card = $_GET["SelectedCard"];
    } else {
        die("Could not get selected card name");
    }

    $getLanguages_query = "SELECT * from SupportedLanguages";

    $column_name_array = array();

    if( $result = mysqli_query($connection, $getLanguages_query) ) {
        while($row = mysqli_fetch_array($result)){
            $language = $row['language'];
            $language_code = $row['languageCode'];

            //Exclude column other than languages
            if(($language != "AppVersion") && ($language != "StringKeys")) {
                $languageObj = (object) [];
                $languageObj->language = $language;
                $languageObj->languageCode = $language_code;
                array_push($column_name_array, $languageObj);
            }
        }
    } else {
        echo "Could not fetch column names from $selected_card";
        exit();
    }

    // print_r($column_name_array);
    // exit();

    $available_language_array = array();

    //Loop through all Language for Selected Card
    foreach ($column_name_array as $languageObj) {
        $selected_language = $languageObj->language;

        $file_name = './Localization/'.$selected_language.'.strings';

        $search_query = "SELECT StringKeys, `$selected_language` FROM $selected_card WHERE `$selected_language` != '' ";
        // echo $search_query."<BR>";
        // continue;

        if (!file_exists('./Localization')) {
            mkdir('./Localization', 0777, true);
        }
        
        if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
            //language is available for this card added to available_language_array
            array_push($available_language_array, $languageObj);

            //Open File if result found
            $file = fopen($file_name, 'w') or die('Cannot open file:  '.$file_name);
            
            if ($file != NULL) {
                while($row = mysqli_fetch_assoc($search_result))
                {
                    // echo "\"".$row['StringKeys']."\" = \"".$row[$selected_language]."\";<br>";
                    fwrite($file, "\"".$row['StringKeys']."\" = \"".$row[$selected_language]."\";\r\n");
                }
            
                fclose($file);
            }
        }
    }

    //Create Zip file to Download
    $zip = new ZipArchive;
    $tmp_file = $selected_card.".zip";

    if ($zip->open($tmp_file,  ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        
        foreach ($column_name_array as $languageObj) {
            $languageName = $languageObj->language;
            $languageCode = $languageObj->languageCode;

            $file_name = './Localization/'.$languageName.'.strings';

            //Second argument is path where you want to put file inside zip when it unzip as folder
            $zip->addFile($file_name, "/".$languageCode.".lproj/".$selected_card.".strings");
        }

        $zip->close();
    } else {
        die('Failed creating zip!');
    }

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
    } else {
        echo 'Failed!';
    }
    
    deleleLocalizationFolder();
    unlink($tmp_file);
    exit();
?>