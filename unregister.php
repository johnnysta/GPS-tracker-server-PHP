<?php 
session_start();
require("adatbazis.inc");
$file="coordianates.xml";

//First delete coordinates xml files from server:

//$file = "coordianates".$_SESSION["user_id"]."*".".xml";



foreach(glob("coordianates".$_SESSION["user_id"]."*".".xml") as $file )
    {
		echo "file: ".$file. PHP_EOL;
        if (!unlink($file))
			{
			echo "Error deleting file(s): ".$file. PHP_EOL;
			}
		else
			{
			echo "File(s) (".$file.") deleted successfully.". PHP_EOL;
			}
    }


//Then delete releted coordinates from coord table for this user:
$query = "DELETE FROM coord WHERE user_id='".htmlspecialchars($_SESSION["user_id"])."'";
		$result = $conn->query($query);
		if (!$result) 
			{
			echo "Error deleting record: " . $conn->error. PHP_EOL;
			}
		else
			{
			echo "Coordinates for user ".$_SESSION["user_name"]." were deleted successfully.". PHP_EOL;
			}
			
//Then delete the user from users table:		
$query = "DELETE FROM felh WHERE ID='".htmlspecialchars($_SESSION["user_id"])."'";
		$result = $conn->query($query);
		if (!$result) {
			echo "Error deleting record: " . $conn->error. PHP_EOL;
			}
		else
			{
			echo "User ".$_SESSION["user_name"]." was deleted successfully.". PHP_EOL;
			}			

//Then destroy session and its variables:
session_unset();
session_destroy();
?>
<!DOCTYPE HTML>  
<html>
<head>
</head>
<body> 
	<a href="/index.php">Back to Home</a>
</body>
</html>