<?php
include 'basesqlexecutors.php';

$mid = $_GET['mid'];
if (!$mid) {
	header("Location: interface.php");
}

$result = executePlainSQL("select name, email, phone_number from gymuser where membership_id = $mid");
$row = oci_fetch_array($result);
$username = $row[0];
$email = $row[1];
$phone = $row[2];
?>
<!DOCTYPE html>
<html>
<head>
	<title> Administration Home </title>
</head>
<body>
	<p>Welcome, <?php echo $username ?></p>
	
	<div class="view" id="personal">
		<form method="post"> 
			<label>Name: </label><input name="name" type=text value=<?php echo $username ?>><br>
			<label>Email:</label><input name="email" type=text value=<?php echo $email ?>><br>
			<label>Phone number:</label><input name="email" type=text value=<?php echo $phone ?>><br>
		</form>
	</div>
	<div class="view" id="gyms"></div>
	<div class="view" id="users"></div>
</body>
</html>