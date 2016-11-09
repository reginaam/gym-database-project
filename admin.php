<?php
include 'basesqlexecutors.php';

$mid = $_GET['mid'];
if (!$mid) {
	header("Location: interface.php");
}

// -----------------------
// Personal section
$result = executePlainSQL("select name, email, phone_number from gymuser where membership_id = $mid");
$row = oci_fetch_array($result);
$username = $row[0];
$email = $row[1];
$phone = $row[2];

// Personal errors
$personalerror = "";
$nameerror = "";
$emailerror = "";
$phoneerror = "";

// Personal form handling
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

// ------------------------------------
// Gym/classes section



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
			<h3> Personal </h3>
			<span style="color:red;"><?php echo $personalerror ?></span><br>
			<label>Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input name="name" type=text value="<?php echo $username ?>"><br>
			<label>Email:</label><span style="color:red;"><?php echo $emailerror ?></span><input name="email" type=text value=<?php echo $email ?>><br>
			<label>Phone number:</label><span style="color:red;"><?php echo $phoneerror ?></span><input name="phone" type=text value=<?php echo $phone ?>><br>
			<input type=submit name="personal">
		</form>
	</div>
	<div class="view" id="gyms">
		<?php 
			$sql = "select gym_name, gym_location from gym where membership_id = $mid";
			$parse = OCI_Parse($db_conn, $sql);
			$r = oci_execute($parse);
			if (!$r) {
				echo "<span style='color:red';> Could not get gym info";
			}
			else {
				echo "<h3> Gyms </h3><form method=get action='newgym.php'><input type=hidden name='mid' value=$mid><button type='submit'>New Gym</button></form><ul>";
				while (($row = oci_fetch_array($parse, OCI_BOTH)) != false) {
					echo "<li>" . $row[0] . ", " . $row[1] . " <form method=get action='editgym.php'><input type=hidden name='gymname' value='" . $row[0] . "'><input type=hidden name='gymloc' value='" . $row[1] . "'><button type='submit'>Edit Gym info</button></form><br><p>Classes</p><form method=get action='newclass.php'><input type=hidden name='mid' value=$mid><input type=hidden name='gymname' value='". $row[0] ."'><input type=hidden name='gymloc' value='".$row[1]."'><button type='submit'>New Class</button></form><ul>";
					$sql = "select distinct gc.class_id, gc.name, gc.cost, t.name from gymclass gc, gymuser t where gc.gym_name = '".$row[0]."' and gc.gym_location = '". $row[1]."' and gc.trainer_membership_id = t.membership_id order by gc.name";
					$parseclass = OCI_Parse($db_conn, $sql);
					oci_execute($parseclass);
					
					while (($classrow = oci_fetch_array($parseclass, OCI_BOTH)) != false) {
						echo "<li>" . $classrow[1] . " with " . $classrow[3] . ", $" . $classrow[2] . "<form method=get action='editclass.php'><input type=hidden name='classid' value=" . $classrow[0] . "><button type='submit'>Edit class</button></form><br></li>";
					}
					
					echo "</ul></li>";
				}
				echo "</ul>";
			}
		?>
	</div>
	<div class="view" id="users"></div>
</body>
</html>