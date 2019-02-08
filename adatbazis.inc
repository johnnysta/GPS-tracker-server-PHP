<?php 
$servername = "*";
$username = "*";
$password = "*";
$dbname = "*";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//echo "Connected successfully";


function sorLekeres($tabla,$oszlop,$adat)
	{
	global $conn;
	$lekerdezes="SELECT * FROM $tabla WHERE $oszlop='$adat'";
	$result=$conn->query($lekerdezes);
	if (!$result)
			die("sorLekeres hiba: ".$conn->error);
	$row = $result->fetch_assoc();
	return $row;
	}	 

//function jelszoEllenorzes($nev,$jelszo) - not used..
// {
// global $conn;
//$lekerdezes="SELECT * FROM felhasznalok WHERE fnev='$nev' AND fjelszo='$jelszo'";
// $eredmeny=mysql_query($lekerdezes,$conn);
//if (!$eredmeny)
//		die("jelszoEllenorzes: ".mysql_error());
// if (mysql_num_rows($eredmeny))		
//		return mysql_fetch_array($eredmeny);
// return false;		
//}	 

	 
function felhasznalo_felvetel($nev,$email,$jelszo)	 
	{
	global $conn;
	$lekerdezes="INSERT INTO felh (user_name, user_pw, user_email) VALUES ('$nev','$jelszo','$email')";
	$result=$conn->query($lekerdezes);
	if (!$result)
 		die("felhasznalo_felvetel hiba: ".$conn->error);
	return $conn->insert_id;
	}
 
function coordinate_insert($id,$lat,$lng,$acc,$time,$entity)	 
	{
	global $conn;
 	$lekerdezes="INSERT INTO coord (user_id, lat, lng, time, accuracy, entity) VALUES ('$id','$lat','$lng','$time','$acc','$entity')";
	$result=$conn->query($lekerdezes);
	if (!$result)
		return FALSE;
	else
		return TRUE; 		
	}
 
?>