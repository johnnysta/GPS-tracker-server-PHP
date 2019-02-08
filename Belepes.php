<?php 
session_start();
$_SESSION["logged_in"]=FALSE;
$_SESSION["user_id"]="";

require("adatbazis.inc");

// define variables and set to empty values
$emailErr = $passwordErr = "";
$email = $password = $hash = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
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
    $hash = password_hash($_POST["password"], PASSWORD_DEFAULT); 
	}
	
	
	
	if($emailErr=="" && $passwordErr== "" )
	{
	if ($row=sorLekeres("felh","user_email",$email)){
		
		if (password_verify($_POST["password"], $row["user_pw"]))
		{
			$generalMessage="Hello ".$row["user_name"]."! You've logged in succesfully.";
			$_SESSION["user_id"]=$row["ID"];
			$_SESSION["user_name"]=$row["user_name"];
			$_SESSION["logged_in"]=TRUE;
			$_SESSION["status"]="logged_in";
			header ('Location: index.php');
		}
		else
			$generalMessage="Invalid password.";		
	}
	else
		{
			$generalMessage="There is no user with this e-mail.";
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
<title>Tracking - Sign in</title>
</head>

<body>  

<h1>Tracking - Sign in</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>  ">  
	<table>
		<tr>
			<td>E-mail:</td>
			<td> <input type="text" name="email" value="<?php echo $email;?>">
			<span class="error">* <?php echo $emailErr;?></span></td>
		</tr>
		<br><br>
		<tr>
			<td>Password:</td>
			<td> <input type="password" name="password">
			<span class="error">* <?php echo $passwordErr;?></span></td>
		</tr>
    </table>
  <br><br>
  <p><span class="error">* required field</span></p>
  <input type="submit" name="submit" value="Submit"> 
  <br><br>
  <?php echo $generalMessage;?>
</form>
 
<a href="/index.php">Back to Home</a>

</body>
</html>