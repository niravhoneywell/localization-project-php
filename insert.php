<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Insert key-value</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<?php
	$StringKey = (isset($_POST['StringKey']) ? $_POST['StringKey'] : "");
	$English = (isset($_POST['English']) ? $_POST['English'] : "");
?>
	<table border="0" cellspacing="1" cellpadding="3">
		<form name="form1" method="post" action="insert.php">
		<tr>
			<td colspan="3"><strong>Insert Key-Value Into Localization Database </strong></td>
		</tr>
		<tr>
			<td width="100">String key:</td>
			<td><input type="text" name="StringKey" id="StringKey" style="width: 350px;" value="<?php echo $StringKey ?>"></td>
		</tr>
		<tr>
			<td>English value:</td>
			<td><textarea rows = "6" cols = "80" name="English" id="English"><?php echo $English ?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" name="Submit" value="Submit"></td>
		</tr>
<?php

$host="localhost"; // Host name 
$username="root"; // Mysql username 
$password=""; // Mysql password 
$db_name="LocalizationProject"; // Database name 
$tbl_name="CardsLocalizable"; // Table name 

if(isset($_POST['StringKey']) and isset($_POST['English']))
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
	$StringKey=$_POST['StringKey'];
	$English=$_POST['English'];
	
	$StringKey_escaped = mysqli_real_escape_string($connection, $StringKey);
	$English_escaped = mysqli_real_escape_string($connection, $English);

	//Search english string in database for duplication
	if(isset($_POST["Submit"])) {
		$search_query = "SELECT StringKeys, English FROM $tbl_name WHERE English like '$English_escaped' escape '\''";
	} elseif(isset($_POST["forceSubmit"])) {
		$search_query = "SELECT StringKeys, English FROM $tbl_name WHERE English like BINARY '$English_escaped' escape '\''";
	} else {
		die("Form data invalidate. <a href='insert.php'>Back to main page</a>");
	}
	
	
	// print($search_query);

	if($search_result = mysqli_query($connection, $search_query) and mysqli_num_rows($search_result) > 0) {
		$html_string = "<tr><td colspan='2'>Localization key-value pair is already available for this English String</td></tr>";

		$exact_match_found = false;

		while($row = mysqli_fetch_assoc($search_result))
		{
			if($row['English'] == $English){
				$exact_match_found = true;
				$html_string = $html_string."<tr><td colspan=2><font color='#35b21e'><b>This is exact match</b></font></td></tr>";
				$html_string = $html_string."<tr><td><font color='#35b21e'>StringKey</td><td>".$row['StringKeys']."</font></td></tr>";
				$html_string = $html_string."<tr><td style='padding-bottom: 10px;'><font color='#35b21e'>English value</td><td>".$row['English']."</font></font></td></tr>";
			} else {
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
		$sql = "INSERT INTO $tbl_name(StringKeys, English) VALUES ('$StringKey_escaped', '$English_escaped')";
		$result = mysqli_query($connection, $sql);

		// if successfully insert data into database, displays message "Successful". 
		if($result) {
			echo "<tr><td colspan='2'>Record added successfully.</td></tr>";
			echo "<tr><td>StringKey</td><td>".$StringKey."</td></tr>";
			echo "<tr><td>English value</td><td>".$English."</td></tr>";
		} else {
			echo "<tr><td colspan='2'><font size=4 color='red'>Failure: ".mysqli_error($connection)."</font></td></tr>";
		}
	}
	echo "<a href='insert.php'>Back to main page</a>";
}

?>
</form>
</table>
</body>
</html>