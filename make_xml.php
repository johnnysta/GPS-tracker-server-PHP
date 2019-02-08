<?php
session_start();

require("adatbazis.inc");
$file = 'coordianates.xml';

//creating an XML file by retrieving the necessery data from database
//the data in XML will be displayed on Google map

$query = "SELECT * FROM coord WHERE user_id=$_SESSION["user_id"] AND entity=$_SESSION["selected_entity"]";
$result = $conn->query($query);
if (!$result) {
	die('Invalid query: ' .$conn->error());
}

file_put_contents($file, "<markers>\n", LOCK_EX);

while ($row = $result->fetch_assoc()){
	$formattedrow = "<marker lat=\"".$row['lat'] . "\" lng=\"" . $row['lng'] . "\" acc=\"" . $row['accuracy'] . "\"/>"."\r\n" ;
	file_put_contents($file, $formattedrow, FILE_APPEND | LOCK_EX);
}

file_put_contents($file, "</markers>\n", FILE_APPEND | LOCK_EX);


?>