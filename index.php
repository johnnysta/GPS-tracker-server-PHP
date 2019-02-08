<?php session_start(); ?>
<?php
require("adatbazis.inc");
$file="coordianates.xml";
?>
<!DOCTYPE HTML>  
<html>
<head>

<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map_canvas {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
	  
		.list-group-item {
		float: right;
		}	
		
		.alert {
		display:inline-block;
		}
</style>
<title>Track anything</title>
<script type="text/javascript" src="downloadxml.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body> 
<div id="controls" class="container-fluid">
<?php 
//Display basic message based on status:
echo "<div class=\"list-inline\">";
echo "<b>";
if (isset($_SESSION["status"]))
	{
	switch ($_SESSION["status"]) 
		{
		case "newly_registered":
			echo "<div class=\"alert alert-success alert-dismissible\">";
			echo "<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"closeq\">&times;</a>";
			echo "&nbsp;You've successfully registered and logged in as user \"".$_SESSION["user_name"]."\" (ID: ".$_SESSION["user_id"]."). ";
			echo "</div>";
			break;
		case "logged_in":
			echo "<div class=\"alert alert-success alert-dismissible\">";
			echo "<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"closeq\">&times;</a>";
			echo "&nbsp;You've successfully logged in as user \"".$_SESSION["user_name"]."\" (ID: ".$_SESSION["user_id"]."). ";
			echo "</div>";
			break;
		}
	}
	else echo "&nbsp;Please log in to display tracking info. ";
echo "</b>";


if ($_SESSION["user_id"]=="")
	{
	echo "<a class=\"list-group-item list-group-item-info\" href=\"";
	echo "Regisztracio.php\"\>";
	echo "Sign up as new user</a> ";
	
	echo "<a class=\"list-group-item list-group-item-success\" href=\"";
	echo "Belepes.php\"\>";
	echo "Log in</a> ";
	echo "</div>";

	}
else
{
	echo "<a class=\"list-group-item list-group-item-info\" href=\"";
	echo "Logout.php\"\>";
	echo "Log out as user \"".$_SESSION["user_name"]."\"</a> ";
	
	echo "<a class=\"list-group-item list-group-item-warning\" href=\"";
	echo "ChangeLoginData.php\"\>";
	echo "Change login data</a> ";
	
	echo "<a class=\"list-group-item list-group-item-danger\" href=\"";
	echo "unregister.php\" onClick=\"return ";
	echo "window.confirm('Are you sure you want to delete all data related to this user?')\">";
	echo "Unregister</a> ";
	echo "</div>";
}

//Code for submit form that selects tracked entity.
//Actions are: show map or delete entity's enties entries from DB.

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$_SESSION["selected_entity"]=$_POST["selected_entity"];
	if (isset($_POST['show_button']))
		{
		//prepare show action
		$query = "SELECT * FROM coord WHERE user_id='".htmlspecialchars($_SESSION["user_id"]).
		"' AND entity='".htmlspecialchars($_POST["selected_entity"])."'";
		$result = $conn->query($query);
		if (!$result) {
		die('Invalid query: ' .$conn->error());
		}

		//creating xml file with coordinates, based on query:
		$file = "coordianates".$_SESSION["user_id"].$_SESSION["selected_entity"].".xml";
		file_put_contents($file, "<markers>\n", LOCK_EX);
		while ($row = $result->fetch_assoc()){
			$formattedrow = "<marker lat=\"".$row['lat'] . "\" lng=\"" . $row['lng'] . "\" acc=\"" .
			$row['accuracy'] . "\"/>"."\r\n" ;
			file_put_contents($file, $formattedrow, FILE_APPEND | LOCK_EX);
		}
		file_put_contents($file, "</markers>\n", FILE_APPEND | LOCK_EX);
		}
	else if (isset($_POST['clear_button']))
		{
		//clear action
		$query = "DELETE FROM coord WHERE user_id='".htmlspecialchars($_SESSION["user_id"]).
		"' AND entity='".htmlspecialchars($_POST["selected_entity"])."'";
		$result = $conn->query($query);
		if (!$result) {
			echo "Error deleting record: " . $conn->error;
			} 
		}
}

//display form with list of tracked entities for the current user and the show/clear buttons

if (isset($_SESSION["user_id"])&&$_SESSION["user_id"]!="")
	{
	$current_user = $_SESSION["user_id"];
	$lekerdezes="SELECT DISTINCT entity FROM coord WHERE user_id='$current_user'";
	$result=$conn->query($lekerdezes);
	if (!$result)
			die("sorLekeres hiba: ".$conn->error);
	if ($result->num_rows > 0)
		{
		echo "<form class=\"form-inline\" method=\"post\" action=\"".htmlspecialchars($_SERVER["PHP_SELF"])."\">";
		echo "<div class=\"form-group\">";
		echo "<label>Select tracked entity of user \"".$_SESSION["user_name"]."\":&nbsp;</label>";
		echo "<select class=\"form-control\" name=\"selected_entity\">";

		while ($row = $result->fetch_assoc())
			{
			echo "<option value=\"".$row["entity"]."\"";
			if (isset($_SESSION["selected_entity"])&&$_SESSION["selected_entity"]==$row["entity"]) print "SELECTED";
			echo ">".$row["entity"]."</option>\n";
			}
		echo "</select>";		

		echo "<button type=\"submit\" name=\"show_button\" class=\"btn btn-success\">Show map for selected entity</button>";
		echo "<button type=\"submit\" name=\"clear_button\" class=\"btn btn-warning\" onclick=\"return window.confirm(
		'Are you sure you want to clear all entries of the selected entity?')\">
		Clear track data of selected entity</button>";
		echo "</div>";
		echo "</form>";
		}
	}
?>
</div>

<div id="map_canvas" ></div> 

<script>  
	var map_canvas;
	var path;
	var last_point;
	
      function initMap() {
		  map_canvas = new google.maps.Map(document.getElementById('map_canvas'), {
          center: {lat: 47.67, lng: 19.12},
          zoom: 14
			});
				
			downloadUrl(<?php echo "\"".$file."\""?>, function(data) {
				var xml = xmlParse(data);
				var markers = xml.documentElement.getElementsByTagName("marker");
				
				//these rows crate the points array, and last_point object - these will be displayed on map
				var points = [];
				for (var i = 0; i < markers.length; i++) {
				var lat = parseFloat(markers[i].getAttribute("lat"));
				var lng = parseFloat(markers[i].getAttribute("lng"));
				var point = new google.maps.LatLng(lat,lng);
				points.push(point);
				}//finish loop
				last_point = points[markers.length-1];
	    
				path = new google.maps.Polyline({
					path: points,
					strokeColor: "#FF0000",
					strokeOpacity: 1.0,
					strokeWeight: 2
				});
				path.setMap(map_canvas);
				map_canvas.setCenter(last_point);
			}); //end download url
			
		}
		
		
</script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB05lFj7RVcz1UbGWYcvx_hCmw8utZrZO4&callback=initMap"
         async defer>
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
</html>
