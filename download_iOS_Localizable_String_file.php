<? include 'connection.php'; ?>

<?php
    if(isset($_GET["SelectedCard"])) {
        $selected_card = $_GET["SelectedCard"];
    }

    if(isset($_GET["SelectedLanguage"])) {
        $selected_language = $_GET["SelectedLanguage"];
    }
    
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=".$selected_language.".strings");
    header("Pragma: no-cache");
    header("Expires: 0");
    // header('Content-Length: '.filesize('file.txt'));

    $search_query = "SELECT StringKeys, English, `$selected_language` FROM $selected_card WHERE `$selected_language` != '' ";

    $file = fopen('php://output', 'w');

    if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
        while($row = mysqli_fetch_assoc($search_result))
        {
            fwrite($file, "\"".$row['StringKeys']."\" = \"".$row[$selected_language]."\";\r\n");
        }
    }

    fclose($file);
    exit();
?>