<?php

include 'basesqlexecutors.php';

$mid = $_GET['mid'];
$cid = $_GET['classid'];
if (!$mid || !$cid) {
	header("Location: index.php");
}

$sql = "select name, cost, trainer_membership_id, gym_name, gym_location from gymclass where class_id=$cid";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
$row = oci_fetch_array($result);
$name = $row[0];
$cost = $row[1];
$tid = $row[2];
$gym_name = $row[3];
$gym_loc = $row[4];

$errors = "";
$nameerror = "";
$costerror = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$anyerrors = false;
	$newname = htmlspecialchars($_POST['name'], ENT_QUOTES);
	if (strlen($newname) > 80) {
		$newname = $name;
		$nameerror = "Name too long";
		$anyerrors = true;
	} else $name = $newname;
	
	$strcost = $_POST['cost'];
	$newcost = round(floatval($strcost),2);
	if ($newcost == 0 and $strcost != "0") {
		$newcost = $cost;
		$costerror = "Enter a valid cost.";
		$anyerrors = true;
	}
	
	$newtrainer = $_POST['trainer'];
	if (!$anyerrors) {
		$sql = "update gymclass set name='$newname', cost=$newcost, trainer_membership_id=$newtrainer where class_id=$cid";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		if (!$r) {
			$errors = "Error updating info";
		} else {
			header("Location: admin.php?mid=$mid");
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> Edit Class </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
	<h3> Edit Class at <?php echo "$gym_name, $gym_loc"?></h3><br>
	<form method="post">
		<span style="color:red;"><?php echo $errors ?></span>
		<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type=text name="name" value="<?php echo $name ?>"><br><br>
		<label> Cost: </label><span style="color:red;"><?php echo $costerror ?></span><input type=text name="cost" value=<?php echo $cost ?>><br><br>
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
		<br><br>
		<button type="submit">Update</button>
	</form>
</body>
</html>