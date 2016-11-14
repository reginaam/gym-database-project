<?php

include 'basesqlexecutors.php';

$mid = $_GET['mid'];
$name = $_GET['routinename'];
$intensity = $_GET['intensity'];
if (!$mid || !$name || !$intensity) {
	header("Location: interface.php");
}

$sql = "select routine_name, intensity, sets, reps from routine where routine_name=$name AND intensity = $intensity";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
$row = oci_fetch_array($result);
$name = $row[0];
$intensity = $row[1];
$sets = $row[2];
$reps = $row[3];

$errors = "";
$nameerror = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$anyerrors = false;
	$newname = htmlspecialchars($_POST['name'], ENT_QUOTES);
	if (strlen($newname) > 80) {
		$newname = $name;
		$nameerror = "Name too long";
		$anyerrors = true;
	} else $name = $newname;
	
	$intensity = $_POST['intensity'];
    $sets = $_POST['sets'];
	$reps = $_POST['reps'];
	if (!$anyerrors) {
		$sql = "update routine set name='$newname', intensity=$intensity, sets=$sets, reps=$reps where name=$name AND intensity=$intensity";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		if (!$r) {
			$errors = "Error updating info";
		} else {
			header("Location: trainer.php?mid=$mid");
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> Edit Routine </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
<h3> Edit Routine: <?php echo "$name, $intensity"?></h3><br>
	<form method="post">
		<span style="color:red;"><?php echo $errors ?></span>
		<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type=text name="name" value="<?php echo $name ?>"><br><br>
        <label> Intensity: </label><input type=int name="intensity" value="<?php echo $intensity ?>"><br><br>
    <label> Sets: </label><input type=int name="sets" value="<?php echo $sets ?>"><br><br>
		<label> Reps: </label><input type=int name="reps" value="<?php echo $reps ?>"><br><br>
		<button type="submit">Update</button>
	</form>
</body>
</html>