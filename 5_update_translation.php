<!-- Import Menu -->
<?
    include 'connection.php';
    $message = '';
?>
<!DOCTYPE html>
<html>
<head>
<script src="validations.js"></script>
<script>
		document.addEventListener('DOMContentLoaded', function() {
			if (selectedCard = readCookie("SelectedCard")){
				document.getElementById("SelectedCard").value = selectedCard;
			}
		});
</script>
</head>
<body>
<?
    include 'menu.php';
    
    if (isset($_POST["upload"])) {
        if ($_FILES['product_file']['name']) {
            $filename = explode(".", $_FILES['product_file']['name']);
            if (end($filename) == "csv") {
            
            } else {
                $message = '<label class="text-danger">Please Select CSV File only</label>';
            }
        } else {
            $message = '<label class="text-danger">Please Select File</label>';
        }
    }
    
    if(isset($_POST["SelectedCard"])) {
        $selected_card = $_POST["SelectedCard"];
    }
?>
    <h3>Update Localization With CSV</h3>
    <?php echo $message; ?>
    <form method="post" enctype='multipart/form-data'>
    <table cellpadding=5 border=1>
        <tr>
            <td>Select Card</td>
            <td>
                <?php
                    $getCards_query = "SELECT * from CardsDetail";

                    if($search_result = mysqli_query($connection, $getCards_query) and mysqli_num_rows($search_result) > 0) {
                        echo "<select name='SelectedCard' id='SelectedCard' onchange='setCookie(\"SelectedCard\", this.value)'>";
                        while($row = mysqli_fetch_assoc($search_result))
                        {
                            echo "<option>".$row['tablename']."</option>";
                        }
                        echo "</select>";
                    } else {
                        die("Can't fetch Cards Detail");
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Please Select File(Only CSV Format)</td>
            <td><input type="file" name="product_file" /></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" name="upload" class="btn btn-info" value="Upload" />
            </td>
        </tr>
   </form>
<?php

if (isset($_POST["upload"])) {
    if ($_FILES['product_file']['name']) {
        $filename = explode(".", $_FILES['product_file']['name']);
        if (end($filename) == "csv") {
            $handle = fopen($_FILES['product_file']['tmp_name'], "r");

            //Retrive column names from first row of CSV file
            if($fieldsObj = fgetcsv($handle)) {
                $field_names_array = $fieldsObj;
                
                // print_r($field_names_array);
                echo "<hr>";

                //Loop through entries to update database.
                while ($dataObj = fgetcsv($handle, 0, ",", '"', "\"")) {
                    print_r($dataObj);
                
                    $update_query = "UPDATE $selected_card SET";

                    if(count($dataObj) >= 2 && $dataObj[0] != "") {
                        $column_name = "";
                        $column_value = "";
                        
                        //Ignore StringKey Column
                        for($i = 1; $i < count($dataObj); $i++) {
                            //Ignore English column in Update Query
                            if($field_names_array[$i] != "English" && $dataObj[$i] != "") {
                                $column_name = mysqli_real_escape_string($connection, $field_names_array[$i]);
                                
                                $csv_value = $dataObj[$i];
                                // $column_value = str_replace('"""', '"', $dataObj[$i]);
                                // $column_value = str_replace('""', '"', $column_value);
                                
                                $column_value = mysqli_real_escape_string($connection, $csv_value);
                                
                                echo "<hr>value from CSV = ".$csv_value." <br><br> after escaping = ".$column_value."<hr>";
                                
                                $update_query = $update_query." `$column_name` =  '$column_value', ";
                            }
                        }
        
                        $update_query = trim($update_query,", ");
                        $update_query = $update_query." WHERE `StringKeys` = '".$dataObj[0]."'";

                        echo $update_query;
                        echo "<hr>";

                        if(mysqli_query($connection, $update_query)) {
                            echo "<tr><td>".$dataObj[0]."</td></tr>";
                        } else {
                            echo "Error occurred while updating localization<br><br> $update_query";
                        }
                    }
                }
                
                fclose($handle);
            }
        }
    }
}
?>
   </table>
 </body>
</html>
