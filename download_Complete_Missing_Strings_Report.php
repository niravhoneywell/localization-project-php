<? include 'connection.php'; ?>

<?php
    if(isset($_GET["SelectedCard"])) {
        $selected_card = $_GET["SelectedCard"];
    }

    $app_version = array();
    
    if(isset($_GET["appversion"])) {
        $app_version = $_GET["appversion"];
    }

    // print_r($app_version_array);
    // exit();


    //Comment this header to debug without downloading file
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=".$selected_card.".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    $fetch_column_names_sql = "SHOW COLUMNS FROM $selected_card";
    
    $column_name_array = array();
    $select_query = "SELECT ";
    $where_clause = "WHERE";

    if( $result = mysqli_query($connection,$fetch_column_names_sql) ) {
        while($row = mysqli_fetch_array($result)){
            $column_name = $row['Field'];

            if($column_name != "AppVersion") {
                array_push($column_name_array, $column_name);
            }
            
            $select_query = $select_query."`$column_name`, ";
            $where_clause = $where_clause." `$column_name` IS NULL OR `$column_name` = '' OR";
        }
    } else {
        echo "Could not fetch column names from $selected_card";
        exit();
    }

    $select_query = trim($select_query,", ");
    $where_clause = trim($where_clause," OR");

    // echo "$select_query FROM $selected_card $where_clause";
    // exit();
    $search_query = "$select_query FROM $selected_card $where_clause";

    $data = array();

    if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
        while($row = mysqli_fetch_assoc($search_result))
        {
            $row_app_version_field = $row["AppVersion"];
            
            $shouldInclude = true;

            // print_r($app_version_array);
            // echo "<br>";
            
            if(!(isset($_GET["appversion"])) && !(is_null($row_app_version_field)) || !(empty($row_app_version_field))) {
                if(version_compare($app_version, $row_app_version_field) == -1) {
                    $shouldInclude = false;
                }
            }

            if($shouldInclude) {
                $element_array = array();
                
                    for($i = 0; $i < count($column_name_array); $i++) {
                        array_push($element_array,  $row[$column_name_array[$i]]);
                    }
        
                array_push($data, $element_array);
            }
        }
    }

    $file = fopen('php://output', 'w');                              
    fputcsv($file, $column_name_array);      
    
    foreach ( $data as $row ) {
        fputcsv($file, $row);
    }

    exit();
?>