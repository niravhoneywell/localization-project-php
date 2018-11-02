<?php
	$host="localhost"; // Host name 
	$username="root"; // Mysql username 
	$password=""; // Mysql password 
	$db_name="LocalizationProject"; // Database name 

	header('Content-Type: text/html; charset=utf-8');

	// Connect to server and select database.
	$connection = mysqli_connect("$host", "$username", "$password", "$db_name") or die("Connection failed."); 

	/* change character set to utf8 */
	if (!mysqli_set_charset($connection, "utf8")) {
		printf("Error loading character set utf8: %s\n", mysqli_error($connection));
		exit();
	} else {
		// printf("Current character set: %s\n", mysqli_character_set_name($connection));
	}
?>