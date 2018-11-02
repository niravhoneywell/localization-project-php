<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Insert key-value</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        function downloadLocalizableStrings(selected_card,selected_language) {
            // alert("download_iOS_Localizable_String_file.php?SelectedCard="+selected_card+"&SelectedLanguage="+selected_language);
            var win = window.open("download_iOS_Localizable_String_file.php?SelectedCard="+selected_card+"&SelectedLanguage="+selected_language, '_blank');
            win.focus();
            // alert("hello");
        }
    </script>
    <?
    include 'menu.php';
    include 'connection.php';

    if(isset($_POST["SelectedCard"])) {
        $selected_card = $_POST["SelectedCard"];
    }
    
    if(isset($_POST["SelectedLanguage"])) {
        $selected_language = $_POST["SelectedLanguage"];
    }
?>
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
        echo "<td><a id='generateButton' name='generateButton' onclick='downloadLocalizableStrings(\"".$selected_card."\", \"".$selected_language."\")'>Download .strings File</button></td>";
    }
?>
</table>

<?php
    if(isset($_POST["Submit"]) && isset($_POST["SelectedCard"]) && isset($_POST["SelectedLanguage"])) {
        $tbl_name = $_POST["SelectedCard"];
        $column_name = $_POST["SelectedLanguage"];
        $search_query = "SELECT StringKeys, English, `$column_name` FROM $tbl_name WHERE `$column_name` != '' ";
        
        // echo $search_query;

        if($search_result = mysqli_query($connection, $search_query)) {
            if (mysqli_num_rows($search_result) > 0) {
                echo "<h3>iOS Localizable.strings: <b>".$column_name."</b></h3>";
                while($row = mysqli_fetch_assoc($search_result))
                {
                    echo "\"".$row['StringKeys']."\" = \"".$row[$column_name]."\";<br>";
                }
            } else {
                echo "<br><br>No strings found in this Card.";
            }
        } else {
            echo "<br><br>ERROR: Could't fetch result";
        }
    }
?>
</body>
</html>