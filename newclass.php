<?php
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
	$gymname = $_GET['gymname'];
	$gymloc = $_GET['gymloc'];
	if (!$mid || !$gymname || !$gymloc) {
		header("Location: interface.php");
	}
	
	$sql = "select membership_id from gymadmin where membership_id=$mid";
	$result = OCI_Parse($db_conn, $sql);
	oci_execute($result);
	if (!oci_fetch_array($result)) {
		header("Location: interface.php");
	}
	
	$errors = "";
	$nameerror = "";
	$costerror = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($name) > 80) {
			$anyerrors = true;
			$nameerror = "Name too long";
		}
		
		$strcost = $_POST['cost'];
		$cost = round(floatval($strcost),2);
		if ($cost == 0 and $strcost != "0") {
			$costerror = "Enter a valid cost.";
			$anyerrors = true;
		}
		
		$trainer = $_POST['trainer'];
		
		$sql = "select class_id from gymclass order by class_id desc";
		$state = OCI_Parse($db_conn, $sql);
		$r = oci_execute($state);
		if (!$r) {
			$anyerrors = true;
		}
		if (!$anyerrors) {
			$row = oci_fetch_array($state);
			$newid = $row[0] + 1;
			
			$sql = "insert into gymclass values($newid, '$name', '$cost', $mid, $trainer, '$gymname', '$gymloc')";
			$result = OCI_Parse($db_conn, $sql);
			$r = oci_execute($result);
			if (!$r) {
				$errors = "Failed to create class";
			} else {
				header("Location: admin.php?mid=$mid");
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> New Class </title>
</head>
<body>
<h3> Create a new class for <?php echo $gymname ?></h3>
<form method=post>
	<span style="color:red;"><?php echo $errors ?></span>
	<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type="text" name="name"><br>
	<label> Cost: </label><span style="color:red;"><?php echo $costerror ?></span><input type="text" name="cost"><br>
	<label> Trainer: </label><select name="trainer"> 
		<?php 
			$sql = "select name, trainer.membership_id from gymuser, trainer where trainer.membership_id = gymuser.membership_id order by name";
			$result = OCI_Parse($db_conn, $sql);
			oci_execute($result);
			
			while (($row = oci_fetch_array($result)) != false) {
				$selected = "";
				if ($row[1] == $tid) {
					$selected = "selected";
				}
				echo "<option value='".$row[1]."' ".$selected.">".$row[0]."</option>";
			}
		?></select>
	<button type=submit> Create </button>
</form>
</body>
</html>