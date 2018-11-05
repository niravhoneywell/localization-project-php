<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Insert key-value</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="validations.js"></script>
    <script>
        function downloadCSV() {
            //If form data is valid then proceed to download page.
            if(self.validateForm()) {
                $selected_card = document.getElementById("SelectedCard").value;
                $app_version = document.getElementById("appversion").value;
                // alert("download_Complete_Missing_Strings_Report.php?SelectedCard=" + $selected_card);
                var win = window.open("download_Complete_Missing_Strings_Report.php?SelectedCard="+$selected_card+"&appversion="+$app_version, '_blank');
                win.focus();
            }
        }

		document.addEventListener('DOMContentLoaded', function() {
			if (selectedCard = readCookie("SelectedCard")){
				document.getElementById("SelectedCard").value = selectedCard;
			}
        });
        
        function validateForm(){
			let appversion = document.getElementById("appversion").value;

            if (!appversion.match(/^(\d+\.)(\d+\.)(\d)$/g)){
				alert("Please enter valid App version, i.e. 4.2.0 or 4.4.1");
				return false
            }
            
			return true
		}


    </script>

    <!-- Import Menu -->
    <?
        include 'menu.php';
        include 'connection.php';
    ?>
    <script>
    </script>
</head>
<body>
<form accept-charset="utf-8" name="form1" method="post">
    <table cellpadding=5>
        <tr>
            <td>Select Card</td>
                <?php
                    $getCards_query = "SELECT * from CardsDetail";

                    if($search_result = mysqli_query($connection, $getCards_query) and mysqli_num_rows($search_result) > 0) {
                        echo "<td><select name='SelectedCard' id='SelectedCard' onchange='setCookie(\"SelectedCard\", this.value)'>";
                        while($row = mysqli_fetch_assoc($search_result))
                        {
                            echo "<option>".$row['tablename']."</option>";
                        }
                        echo "</select></td></tr>";
                    } else {
                        die("Can't fetch Cards Detail");
                    }
                ?>
    <tr>
        <td>App version for String</td>
        <td>
            <?php
                //check if AppVersion is passed in form request
                //If not then fetch from database table
                if(!isset($_GET['appversion'])) {
                    $getAppVersion_query = "SELECT * from CurrentAppVersion";

                    if($search_result = mysqli_query($connection, $getAppVersion_query) and mysqli_num_rows($search_result) > 0) {
                        if($row = mysqli_fetch_assoc($search_result)) {
                            echo "<input name='appversion' id='appversion' required onkeypress='return isNumberKey(event)' value='".$row['appversion']."'>";
                        } else {
                            echo "<input name='appversion' id='appversion' required onkeypress='return isNumberKey(event)'>";
                        }
                    }
                } else {
                    echo "<input name='appversion' id='appversion' required onkeypress='return isNumberKey(event)' value='".$_GET['appversion']."'>";	
                }
            ?>
		</td>
    </tr>
    <? echo "<tr><td colspan=2 align=center><a id='generateButton' name='generateButton' onclick='downloadCSV()'>Download CSV</button></td></tr>"; ?>
</form>
</table>
</body>
</html>