<html>
<head>
</head>
<body>

<?php
//This file is called by the android device to add its coordinates to the db (should be changed to post request)

require("adatbazis.inc");

if (isset($_GET['lat'])&isset($_GET['lng'])&isset($_GET['acc'])&isset($_GET['id'])&isset($_GET['entity'])
	&isset($_GET['time'])) {
	
	
	echo 'Lat.: ';
	echo $_GET['lat'];
	echo ' Lng.: ';
	echo $_GET['lng'];
	echo ' Acc.: ';
	echo $_GET['acc'];
	echo ' Id: ';
	echo $_GET['id'];
	echo ' Entity: ';
	echo $_GET['entity'];
	echo ' Time: ';
	echo date('Y-m-d H:i:s',intval($_GET['time']));
	
	if ($row=sorLekeres("felh","ID",$_GET['id'])){
	if (coordinate_insert($row['ID'],$_GET['lat'],$_GET['lng'],$_GET['acc'],
		date('Y-m-d H:i:s',intval($_GET['time'])),$_GET['entity']))
		echo "Coordinate added.";
	else
		echo "Coordinate addition failed";		
	}
	else
		echo "No such user.";

} 

else

	print "Illegal arguments!";

?>


</body>

</html>