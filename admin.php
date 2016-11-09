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

$personalerror = "";
$nameerror = "";
$emailerror = "";
$phoneerror = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (array_key_exists('personal', $_POST)) {
		$newname = $_POST['name'];
		if (strlen($newname)> 40) {
			$newname = $username;
			$nameerror = "Name too long";
		}
		else $username = $newname;
		$newemail = $_POST['email'];
		if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
			$emailerror = "Enter a valid email";
			$newemail = $email;
		}
		else $email = $newemail;
		$newphone = $_POST['phone'];
		if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $newphone)) {
			$phoneerror = "Phone number must be in the format xxx-xxx-xxxx";
			$newphone = $phone;
		}
		else $phone = $newphone;
		$sql = "update gymuser set name='" . $newname . "', email='" . $newemail. "', phone_number='"  . $newphone . "' where membership_id=".$mid;
		$parse = OCI_Parse($db_conn, $sql);
		$r = oci_execute($parse);
		if(!$r) {
			$personalerror = "Error updating info";
		}
	}
}

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
			<span style="color:red;"><?php echo $personalerror ?></span><br>
			<label>Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input name="name" type=text value="<?php echo $username ?>"><br>
			<label>Email:</label><span style="color:red;"><?php echo $emailerror ?></span><input name="email" type=text value=<?php echo $email ?>><br>
			<label>Phone number:</label><span style="color:red;"><?php echo $phoneerror ?></span><input name="phone" type=text value=<?php echo $phone ?>><br>
			<input type=submit name="personal">
		</form>
	</div>
	<div class="view" id="gyms"></div>
	<div class="view" id="users"></div>
</body>
</html>