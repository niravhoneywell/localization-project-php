<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Insert New String</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
		function showData(){
			// let stringKey = document.forms[0].elements["StringKey"];
			let stringKey = document.forms[0].StringKey;
			let English = document.forms[0].English;
			let escaped_english = replaceSpecials(English.value);
			document.getElementById("iosData").innerHTML = "\"" + stringKey.value + "\" = \"" + escaped_english + "\";";
			document.getElementById("escapedEnglish").value = escaped_english;
		}

		function replaceSpecials(English){
			return English.replace(/\\\\/g, "\\").replace(/\\"/g, "\"").replace(/\\/g, "\\\\").replace(/\"/g, "\\\"").replace(/\\\\n/g, "\\n").replace(/'/g, "\\'");
		}

		function setCookie(cookieName, cookieValue) {
    		var today = new Date();
    		var expire = new Date();
			expire.setTime(today.getTime() + 3600000*24*365);
			document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString();
		}

		function readCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		}

		document.addEventListener('DOMContentLoaded', function() {
			if (selectedCard = readCookie("SelectedCard")){
				document.getElementById("SelectedCard").value = selectedCard;
			}
		});

		function isNumberKey(event)
       	{
			// Allow numbers and decimal character 
			var charCode = (event.which) ? event.which : event.keyCode;
			if (charCode != 46 && (charCode < 48 || charCode > 57))
				return false;

			return true;
       	}

		function validateCharacterForKey(event) {
			var charCode = (event.which) ? event.which : event.keyCode;

			if((charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122) ||
				 (charCode >= 48 && charCode <= 57) || charCode === 95) {
					return true;
				}

			return false;
		}

		function validateForm(){
			let appversion = document.getElementById("appversion").value;
			let stringKey = document.getElementById("StringKey").value;
			let englishString = document.getElementById("English").value;
			
			if (!appversion.match(/^(\d+\.)(\d+\.)(\d)$/g)){
				alert("Please enter valid App version, i.e. 4.2.0 or 4.4.1");
				return false
			}
			
			setCookie("savedAppVersion", appversion);

			// alert(stringKey.match(/^(([a-zA-Z0-9])+[_]*)+$/g));

			if (!stringKey.match(/^(([a-zA-Z0-9])+[_]*)+$/g)){
				alert("Please enter valid String Key, i.e. dashboard_header_text");
				return false
			}

			// alert(englishString.replace(/\\\\/g, "\\").replace(/\\"/g, "\"").replace(/\\/g, "\\\\").replace(/\"/g, "\\\""));
			document.getElementById("escapedEnglish").value = replaceSpecials(englishString);
			return true
		}

	</script>
	<?
		include 'menu.php';
		include 'connection.php';

		$StringKey = (isset($_POST['StringKey']) ? $_POST['StringKey'] : "");
		$English = (isset($_POST['English']) ? $_POST['English'] : "");
	?>
</head>
<body>
<table border="0" cellspacing="1" cellpadding="3">
	<form accept-charset="utf-8" name="form1" method="post" action="insert.php" onsubmit="return validateForm()">
		<tr>
			<td colspan="3"><strong>Insert Key-Value Into Localization Database</strong></td>
		</tr>
		<tr>
			<td>App version for String</td>
			<td>
		<?php
			//check if AppVersion is passed in form request
			//If not then fetch from database table
			if(!isset($_POST['appversion'])) {
				$getAppVersion_query = "SELECT * from CurrentAppVersion";

				if($search_result = mysqli_query($connection, $getAppVersion_query) and mysqli_num_rows($search_result) > 0) {
					if($row = mysqli_fetch_assoc($search_result)) {
						echo "<input name='appversion' id='appversion' required onkeypress='return isNumberKey(event)' value='".$row['appversion']."'>";
					} else {
						echo "<input name='appversion' id='appversion' required onkeypress='return isNumberKey(event)'>";
					}
				}
			} else {
				echo "<input name='appversion' id='appversion' required onkeypress='return isNumberKey(event)' value='".$_POST['appversion']."'>";	
			}
		?>
			</td>
		</tr>
		<tr>
			<td>Select Card Name</td>
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
			<td width="100">String key:</td>
			<td><input type="text" name="StringKey" id="StringKey" required onkeypress="return validateCharacterForKey(event)" style="width: 350px;" value="<?php echo $StringKey ?>" onkeyup="showData()"></td>
		</tr>
		<tr>
			<td>English value:</td>
			<td>
				<input type="text" name="English" id="English" required style="width: 350px; height: 20px; word-break: break-word; alight:top;" value="<?php echo htmlspecialchars($English); ?>" onkeyup="showData()">
				<input type="hidden" name="escapedEnglish" id="escapedEnglish">
			</td>
		</tr>
		<tr>
			<td colspan="2" id="iosData"></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" name="Submit" value="Submit"></td>
		</tr>
<?php

if(isset($_POST['appversion']) and isset($_POST['SelectedCard']) and isset($_POST['StringKey']) and isset($_POST['English']) and isset($_POST['escapedEnglish']))
{
	// Connect to server and select database.
	$connection = mysqli_connect("$host", "$username", "$password", "$db_name") or die("Connection failed."); 

	/* change character set to utf8 */
	if (!mysqli_set_charset($connection, "utf8")) {
		printf("Error loading character set utf8: %s\n", mysqli_error($connection));
		exit();
	} else {
		// printf("Current character set: %s\n", mysqli_character_set_name($connection));
	}

	// Get values from form
	$tbl_name = $_POST['SelectedCard']; // Table name 
	$app_version = $_POST['appversion']; // App version for string
	$StringKey=$_POST['StringKey'];
	$English=$_POST['escapedEnglish'];
	// $English=$_POST['escapedEnglish'];

	$StringKey_escaped = mysqli_real_escape_string($connection, $StringKey);
	$English_escaped = mysqli_real_escape_string($connection, $English);

	//Search english string in database for duplication
	if(isset($_POST["Submit"])) {
		$search_query = "SELECT * FROM $tbl_name WHERE English like '$English_escaped' escape '\''";
	} elseif(isset($_POST["forceSubmit"])) { //Check for Exact match in DATABASE
		$search_query = "SELECT * FROM $tbl_name WHERE English like BINARY '$English_escaped' escape '\''";
	} else {
		die("Form data invalidate. <a href='insert.php'>Back to main page</a>");
	}
	
	// print($search_query);

	if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
		$html_string = "<tr><td colspan='2'><font color='red'>Localization key-value pair is already available for this English String</font></td></tr>";

		$exact_match_found = false;

		while($row = mysqli_fetch_assoc($search_result))
		{
			$keys_array = array_keys($row);
			
			$isTranslated = "<font color='#35b21e'>(Translations available)</font>";
			
			for($i=0; $i<count($keys_array); $i++) {
				if($row[$keys_array[$i]] == "") {
					$isTranslated = "<font color='red'>(Not Translated)</font>";
				}
			}

			if($row['English'] == $English){
				$exact_match_found = true;
				$html_string = $html_string."<tr><td colspan=2><hr><font color='#35b21e'><b>This is exact match </font>$isTranslated</b></td></tr>";
				$html_string = $html_string."<tr><td><font color='#35b21e'>StringKey</td><td>".$row['StringKeys']."</font></td></tr>";
				$html_string = $html_string."<tr><td style='padding-bottom: 10px;'><font color='#35b21e'>English value</td><td>".$row['English']."</font></font></td></tr>";
			} else {
				$html_string = $html_string."<tr><td colspan=2><hr><b>$isTranslated</b></td></tr>";
				$html_string = $html_string."<tr><td>StringKey</td><td>".$row['StringKeys']."</td></tr>";
				$html_string = $html_string."<tr><td style='padding-bottom: 10px;'>English value</td><td>".$row['English']."</td></tr>";
			}
		}

		if($exact_match_found == false) {
			echo "<tr><td colspan='2' align='center'>No exact match found, you can force insert this value with: <input type='submit' name='forceSubmit' value='Force insert'></td></tr>";
		}

		echo $html_string;
	} else {
		// Insert data into mysql 
		$sql = "INSERT INTO $tbl_name(AppVersion, StringKeys, English) VALUES ('$app_version', '$StringKey_escaped', '$English_escaped')";
		$result = mysqli_query($connection, $sql);

		// if successfully insert data into database, displays message "Successful". 
		if($result) {
			echo "<tr><td colspan='2'>Record added successfully.</td></tr>";
			echo "<tr><td>StringKey</td><td>".$StringKey."</td></tr>";
			echo "<tr><td>English value</td><td>".$English."</td></tr>";
			echo "<tr><td>iOS String</td><td>\"".$StringKey."\" = \"".$English."\";</td></tr>";
		} else {
			echo "<tr><td colspan='2'><font size=4 color='red'>Failure: ".mysqli_error($connection)."</font></td></tr>";
		}
	}
	echo "<a href='insert.php'>Back to main page</a>";
}

?>
</form>
</td>
</tr>
</table>
</body>
</html>