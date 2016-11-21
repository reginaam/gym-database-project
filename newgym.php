<?php
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
	if (!$mid) {
		header("Location: index.php");
	}
	
	$sql = "select membership_id from gymadmin where membership_id=$mid";
	$result = OCI_Parse($db_conn, $sql);
	oci_execute($result);
	if (!oci_fetch_array($result)) {
		header("Location: index.php");
	}
	
	$errors = "";
	$nameerror = "";
	$locerror = "";
	$cityerror = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($name) > 80) {
			$anyerrors = true;
			$nameerror = "Name too long";
		}
		
		$loc = htmlspecialchars($_POST['loc'], ENT_QUOTES);
		if (strlen($loc) > 80) {
			$anyerrors = true;
			$locerror = "Location too long";
		}
		
		$city = htmlspecialchars($_POST['city'], ENT_QUOTES);
		if (strlen($city) > 40) {
			$anyerrors = true;
			$cityerror = "City too long";
		}
		
		if (!$anyerrors) {
			$sql = "insert into gym values('$name', '$loc', '$city', $mid)";
			$result = OCI_Parse($db_conn, $sql);
			$r = oci_execute($result);
			if (!$r) {
				$errors = "A gym with that name and location already exists.";
			} else {
				header("Location: admin.php?mid=$mid&tab=1");
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> New Gym </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
<h3> Create a new Gym </h3><br>
<form method=post>
	<span style="color:red;"><?php echo $errors ?></span>
	<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type="text" name="name"><br><br>
	<label> Location: </label><span style="color:red;"><?php echo $locerror ?></span><input type="text" name="loc"><br><br>
	<label> City: </label><span style="color:red;"><?php echo $cityerror ?></span><input type="text" name="city"><br><br>
	<button type=submit> Create </button>
</form>
</body>
</html>