<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Missing String Report</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        function downloadCSV(selected_card,selected_language) {
            // alert("generateReport.php?SelectedCard="+selected_card+"&SelectedLanguage="+selected_language);
            var win = window.open("1_downloadMissingStringReport.php?SelectedCard="+selected_card+"&SelectedLanguage="+selected_language, '_blank');
            win.focus();
            // alert("hello");
        }
    </script>

    <!-- Import Menu -->
    <?
        include 'menu.php';
        include 'connection.php';

        // $selected_card = "";
        // $selected_language = "";
        
        if(isset($_POST["SelectedCard"])) {
            $selected_card = $_POST["SelectedCard"];
        }
        
        if(isset($_POST["SelectedLanguage"])) {
            $selected_language = $_POST["SelectedLanguage"];
        }
    
        // if(isset($_POST['generateButton'])) {
        //      generateReport($selected_card, $selected_language);
        // }
        // 
        // function generateReport($selected_card, $selected_language) {
        //     header("Content-type: text/csv");
        //     header("Content-Disposition: attachment; filename=".$selected_language.".csv");
        //     header("Pragma: no-cache");
        //     header("Expires: 0");

        //     $search_query = "SELECT StringKeys, English, `$selected_language` FROM $selected_card WHERE `$selected_language` = ''";
            
        //     $data = array();

        //     if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
        //         while($row = mysqli_fetch_assoc($search_result))
        //         {
        //             array_push($data, array($row['StringKeys'], $row['English'] ,""));
        //         }
        //     }

        //     $file = fopen('php://output', 'w');                              
        //     fputcsv($file, array('StringKey', 'English', $column_name));      
            
        //     foreach ( $data as $row ) {
        //         fputcsv($file, $row);              
        //     }
        //     exit(); 
        // }
    ?>
    <script>
    </script>
</head>
<body>
<form accept-charset="utf-8" name="form1" method="post">
    <table>
        <tr>
            <td>Select Card</td>
<?php
    $getCards_query = "SELECT * from CardsDetail";

    if($search_result = mysqli_query($connection, $getCards_query) and mysqli_num_rows($search_result) > 0) {
        echo "<td><select name='SelectedCard' id='SelectedCard' onchange='setCookie(\"SelectedCard\", this.value)'>";
        while($row = mysqli_fetch_assoc($search_result))
        {
            if ($selected_card == $row['tablename']) {
                echo "<option selected>".$row['tablename']."</option>";
            } else {
                echo "<option>".$row['tablename']."</option>";
            }
        }
        echo "</select>";
    } else {
        die("Can't fetch Cards Detail");
    }

    $getLanguages_query = "SELECT * from SupportedLanguages";
    if($search_result = mysqli_query($connection, $getLanguages_query) and mysqli_num_rows($search_result) > 0) {
        echo "<td><select name='SelectedLanguage' id='SelectedLanguage'>";
        while($row = mysqli_fetch_assoc($search_result))
        {
            echo $row['language'];
            if ($selected_language == $row['language']) {
                echo "<option selected>".$row['language']."</option>";
            } else {
                echo "<option>".$row['language']."</option>";
            }
        }
        echo "</select></td>";
    } else {
        die("Can't fetch Supported Languages");
    }

    echo "<td><input type='submit' name='Submit' value='Submit'></td>";
    ?>
</form>
<?
    if(isset($_POST["SelectedCard"])){
        echo "<td><a id='generateButton' name='generateButton' onclick='downloadCSV(\"".$selected_card."\", \"".$selected_language."\")'>Download CSV</button></td>";
    }
?>
</table>
<?php
    if(isset($_POST["Submit"]) && isset($_POST["SelectedCard"]) && isset($_POST["SelectedLanguage"])) {
        $tbl_name = $_POST["SelectedCard"];
        $column_name = $_POST["SelectedLanguage"];
        $search_query = "SELECT AppVersion, StringKeys, English, `$column_name` FROM $tbl_name WHERE `$column_name` IS NULL OR `$column_name` = '' ORDER BY AppVersion";
        
        // echo $search_query;

        if($search_result = mysqli_query($connection, $search_query)) {
            if (mysqli_num_rows($search_result) > 0) {
            
                echo "<table border=1>";
                echo "<tr><td colspan=3>Missing Strings for Language: <b>".$column_name."</b></td></tr>";
                echo "<tr><td><b>AppVersion</b></td><td><b>StringKey</b></td><td><b>English</b></td></tr>";
                while($row = mysqli_fetch_assoc($search_result))
                {
                    echo "<tr>";
                    echo "<td>".$row['AppVersion']."</td>";
                    echo "<td width='25%'>".$row['StringKeys']."</td>";
                    echo "<td style='max-width:45%;overflow-wrap: break-word; word-break: break-all;'>".$row['English']."</td>";
                    // echo "<td width='25%'>\"".$row['StringKeys']."\" = \"".$row[$column_name]."\"</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<br><br>No missing found in this Card.";
            }
        } else {
            echo "<br><br>ERROR: Could't fetch result";
        }
    }
?>
</body>
</html>