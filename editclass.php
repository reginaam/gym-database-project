<?php

include 'basesqlexecutors.php';

$mid = $_GET['mid'];
$cid = $_GET['classid'];
if (!$mid || !$cid) {
	header("Location: index.php");
}

$istrainer = true;
$sql = "select membership_id from trainer where membership_id=$mid";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
if (!oci_fetch_array($result)) {
	$istrainer = false;
}

$sql = "select name, cost, trainer_membership_id, gym_name, gym_location, class_date, start_time, end_time from gymclass where class_id=$cid";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
$row = oci_fetch_array($result);
$name = $row[0];
$cost = $row[1];
$tid = $row[2];
$gym_name = $row[3];
$gym_loc = $row[4];
$date = $row[5];
$stime = $row[6];
$ftime = $row[7];

$errors = "";
$nameerror = "";
$costerror = "";
$dateerror = "";
$stimeerror = "";
$ftimeerror = "";

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
	
	$newdate = $_POST['date'];
	if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $newdate) && !preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{2}$/", $newdate)) 
	{
		$newdate = $date;
		$dateerror = "Date must be in format yyyy-mm-dd or yy-mm-dd";
		$anyerrors = true;
	} else $date = $newdate;
	
	$newstime = $_POST['stime'];
	if (!preg_match("/^[0-2]{1}[0-9]{1}[0-5]{1}[0-9]{1}$/", $newstime)) {
		$newstime = $stime;
		$stimeerror = "Time must be in format HHMM";
		$anyerrors = true;
	} else $stime = $newstime;
	
	$newftime = $_POST['ftime'];
	if (!preg_match("/^[0-2]{1}[0-9]{1}[0-5]{1}[0-9]{1}$/", $newftime)) {
		$newftime = $ftime;
		$ftimeerror = "Time must be in format HHMM";
		$anyerrors = true;
	} else $ftime = $newftime;
	
	$newtrainer = $_POST['trainer'];
	if (!$anyerrors) {
		$sql = "update gymclass set name='$newname', cost=$newcost, trainer_membership_id=$newtrainer, class_date='$newdate', start_time='$newstime', end_time = '$newftime' where class_id=$cid";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		if (!$r) {
			$errors = "Error updating info";
		} else if (!$istrainer) {
			header("Location: admin.php?mid=$mid&tab=1");
		} else {
			header("Location: trainer.php?mid=$mid&tab=1");
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
		<label> Date: </label><span style="color:red;"><?php echo $dateerror ?></span><input type=text name="date" value=<?php echo $date ?>><br><br>
		<label> Start time: </label><span style="color:red;"><?php echo $stimeerror ?></span><input type=text name="stime" value=<?php echo $stime ?>><br><br>
		<label> End time: </label><span style="color:red;"><?php echo $ftimeerror ?></span><input type=text name="ftime" value=<?php echo $ftime ?>><br><br>
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