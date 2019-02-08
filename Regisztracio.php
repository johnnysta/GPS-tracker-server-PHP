<?php 
session_start();
$_SESSION["logged_in"]=FALSE;
$_SESSION["user_id"]="";

require("adatbazis.inc");

// define variables and set to empty values
$emailErr = $passwordErr = $nameErr = $passwordConfErr = $generalMessage ="";
$email = $password = $conf_password = $hash = $password_conf_hash = $name = "";
$user_ID = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
  }

  
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
	    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format"; 
	}
  }
    
  if (empty($_POST["password"])) {
    $passwordErr = "Password is required";
  } else {
	if ($_POST["password"] != $_POST["conf_password"]){
		$passwordErr = "Password and Confirm pasword field values do not match.";
		}  
	$password = $_POST["password"];
    $hash = password_hash($_POST["password"], PASSWORD_DEFAULT); 
	}
	
  if (empty($_POST["conf_password"])) {
    $passwordConfErr = "Password confirmation is required";
  } else {
	  	if ($_POST["password"] != $_POST["conf_password"]){
		$passwordConfErr = "Password and Confirm pasword field values do not match.";
		}	  
	$conf_password = $_POST["conf_password"];
    $password_conf_hash = password_hash($_POST["conf_password"], PASSWORD_DEFAULT); 
	}
	
	
	if($emailErr=="" && $passwordErr=="" && $nameErr=="" && $passwordConfErr== "")
	{
	if (sorLekeres("felh","user_email",$email)){
		$generalMessage="A user with this e-mail already exists.";		
	}
	else
	{
		$user_ID= felhasznalo_felvetel($name,$email,$hash);
		if ($user_ID)	
			{
			$generalMessage="Registration of user ".$name." was successful. Please use user ID ".$user_ID." in the app's settings.";
			$_SESSION["user_id"]=$user_ID;
			$_SESSION["user_name"]=$name;
			$_SESSION["logged_in"]=TRUE;
			$_SESSION["status"]="newly_registered";
			header ('Location: index.php');
			}
		else
			$generalMessage="Adding new user to database was failed.";
	}
	}
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
 ?>
 
<!DOCTYPE HTML>   
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
<title>Tracking - Sign up</title>
</head>

<body>

<h1>Tracking - Sign up</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
	<table>
		<tr>
			<td>Name:</td>
			<td> <input type="text" name="name"  value="<?php echo $name;?>">
			<span class="error">* <?php echo $nameErr;?></span></td>
		</tr>
		<tr>
			<td>E-mail:</td>
			<td> <input type="text" name="email" value="<?php echo $email;?>">
			<span class="error">* <?php echo $emailErr;?></span></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password" value="<?php echo $password;?>">
			<span class="error">* <?php echo $passwordErr;?></span></td>
		</tr>
		<tr>
			<td>Confirm password:</td>
			<td><input type="password" name="conf_password" value="<?php echo $conf_password;?>">
			<span class="error">* <?php echo $passwordConfErr;?></span></td>
		</tr>
	</table>

	<p><span class="error">* required field</span></p>
	<input type="submit" name="submit" value="Submit"> 
	<br><br>
	<?php echo $generalMessage;?>
</form>
<a href="/index.php">Back to Home</a>

</body>
</html>
