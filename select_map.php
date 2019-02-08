<?php
session_start();
//echo $_SESSION["user_id"];
require("adatbazis.inc");

if ($_SERVER["REQUEST_METHOD"] == "POST"){


	$query = "SELECT * FROM coord WHERE user_id='".htmlspecialchars($_SESSION["user_id"]).
	"' AND entity='".htmlspecialchars($_POST["selected_entity"])."'";
	$result = $conn->query($query);
	if (!$result) {
	die('Invalid query: ' .$conn->error());
	}

	//creating xml file with coordinates, based on query:
	$file = 'coordianates.xml';
	file_put_contents($file, "<markers>\n", LOCK_EX);
	while ($row = $result->fetch_assoc()){
		$formattedrow = "<marker lat=\"".$row['lat'] . "\" lng=\"" . $row['lng'] . "\" acc=\"" . $row['accuracy'] . "\"/>"."\r\n" ;
		file_put_contents($file, $formattedrow, FILE_APPEND | LOCK_EX);
	}

	file_put_contents($file, "</markers>\n", FILE_APPEND | LOCK_EX);

	header ('Location: showmap.php'); 
	
}

	
?>

<!DOCTYPE HTML>  
<html>
<head>

</head>
<body>
	
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>  "> 	
	<table>
		<tr>
			<td>	Map to display for:</td>
			<td>
			<select name="selected_entity">
			<?php 
			$current_user = $_SESSION["user_id"];
			$lekerdezes="SELECT DISTINCT entity FROM coord WHERE user_id='$current_user'";
			$result=$conn->query($lekerdezes);
			if (!$result)
					die("sorLekeres hiba: ".$conn->error);
			$row = $result->fetch_assoc();
			while ($row = $result->fetch_assoc()){
				echo "<option value=\"".$row["entity"]."\"";
				//if ($azon==$egy_sor[0])	 print "SELECTED";
				echo ">".$row["entity"]."</option>\n";
				}
			?>
			</select>
			</td>
		</tr>
	</table> 
	<input type="submit" name="submit" value="Show/Refresh map">
</form>

</body>
</html>
