<? include 'connection.php'; ?>

<?php
    if(isset($_GET["SelectedCard"])) {
        $selected_card = $_GET["SelectedCard"];
    }

    if(isset($_GET["SelectedLanguage"])) {
        $selected_language = $_GET["SelectedLanguage"];
    }
    
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=".$selected_language.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $search_query = "SELECT StringKeys, English, `$selected_language` FROM $selected_card WHERE `$selected_language` IS NULL OR `$selected_language` = ''";

    $data = array();

    if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
        while($row = mysqli_fetch_assoc($search_result))
        {
            array_push($data, array($row['StringKeys'], $row['English'] ,""));
        }
    }

    $file = fopen('php://output', 'w');                              
    fputcsv($file, array('StringKey', 'English', $selected_language));      
    
    foreach ( $data as $row ) {
        fputcsv($file, $row);              
    }
    exit();
?>