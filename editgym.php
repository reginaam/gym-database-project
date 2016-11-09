<?php

include 'basesqlexecutors.php';

$mid = $_GET['mid'];
$gym_name = $_GET['gymname'];
$gym_loc = $_GET['gymloc'];
if (!$gym_name || !$gym_loc || !$mid) {
	header("Location: interface.php");
}

$sql = "select city, membership_id from gym where gym_name='".$gym_name."' and gym_location='".$gym_loc."'";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
$row = oci_fetch_array($result);
if ($row[1] != $mid) {
	header("Location: interface.php");
}
$city = $row[0];

$errors = "";
$cityerror = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$anyerrors = false;
	
	$newcity = $_POST['city'];
	if (strlen($newcity)> 40) {
		$cityerror = "City name is too long";
		$anyerrors = true;
	}
	
	$newadmin = $_POST['admin'];
	
	if (!$anyerrors) {
		$sql = "update gym set city='".$newcity."', membership_id=".$newadmin." where gym_name='".$gym_name."' and gym_location='".$gym_loc."'";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		if (!$r) {
			$errors = "Error in update.";
		} else {
			header("Location: admin.php?mid=$mid");
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> Edit Gym </title>
</head>
<body>
	<h3> Edit Class at <?php echo "$gym_name, $gym_loc"?></h3>
	<form method="post">
		<span style="color:red;"><?php echo $errors ?></span>
		<label> City: </label><span style="color:red;"><?php echo $cityerror ?></span><input type=text name="city" value="<?php echo $city ?>"><br>
		<label> Admin: </label><select name="admin"> 
		<?php 
			$sql = "select name, gymadmin.membership_id from gymuser, gymadmin where gymadmin.membership_id = gymuser.membership_id order by name";
			$result = OCI_Parse($db_conn, $sql);
			oci_execute($result);
			
			while (($row = oci_fetch_array($result)) != false) {
				$selected = "";
				if ($row[1] == $mid) {
					$selected = "selected";
				}
				echo "<option value='".$row[1]."' ".$selected.">".$row[0]."</option>";
			}
		?></select>
		<button type="submit">Update</button>
	</form>
</body>
</html>