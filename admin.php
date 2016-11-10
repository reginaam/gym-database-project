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

// Errors
$personalerror = "";
$usererror = "";
$nameerror = "";
$emailerror = "";
$phoneerror = "";

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Personal section
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
	else if (array_key_exists('newuser', $_POST)) {
		$anyerrors = false;
		$u_name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($u_name)> 40) {
			$nameerror = "Name too long";
			$anyerrors = true;
		}
		$u_email = htmlspecialchars($_POST['email'], ENT_QUOTES);
		if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
			$emailerror = "Enter a valid email";
			$anyerrors = true;
		}
		$u_phone = htmlspecialchars($_POST['phone'], ENT_QUOTES);
		if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $u_phone)) {
			$phoneerror = "Phone number must be in the format xxx-xxx-xxxx";
			$anyerrors = true;
		}
		$sql = "select membership_id from gymuser order by membership_id desc";
		$state = OCI_Parse($db_conn, $sql);
		$r = oci_execute($state);
		if (!$r) {
			$anyerrors = true;
		}
		if (!$anyerrors) {
			$row = oci_fetch_array($state);
			$newid = $row[0] + 1;
			
			$sql = "insert into gymuser values(".$newid.", '".$u_email."', '".$u_name."', '".$u_phone."')";
			$state = OCI_Parse($db_conn, $sql);
			$r = oci_execute($state);
			if (!$r) {
				$usererror = "Failed to create new user";
			} else {
				$usertype = $_POST['utype'];
				if ($usertype == "athlete") {
					$sql = "insert into athlete values($newid)";
					$state = OCI_Parse($db_conn, $sql);
					$r = oci_execute($state);
					if (!$r) {
						$usererror = "Failed to create new user";
					}
				} else if ($usertype == "trainer") {
					$sql = "insert into trainer values($newid)";
					$state = OCI_Parse($db_conn, $sql);
					$r = oci_execute($state);
					if (!$r) {
						$usererror = "Failed to create new user";
					}
				} else if ($usertype == "admin") {
					$sql = "insert into gymadmin values($newid)";
					$state = OCI_Parse($db_conn, $sql);
					$r = oci_execute($state);
					if (!$r) {
						$usererror = "Failed to create new user";
					}
				}
			}
		}
	}
}



?>
<!DOCTYPE html>
<html>
<head>
	<title> Administration Home </title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="toggletab.js"></script>
	<style>
		body {
			font-family: 'Helvetica';
			font-size: 15px;
		}
		
		#nav {
			width: 100%;
		}
		
		.tab {
			width: 33%;
			border-bottom: 2px solid #4489ff;
			font-size: 25px;
			color: #4489ff;
			background-color: white;
			display: inline-flex;
		}
		
		.tab p {
			margin-left: auto;
			margin-right: auto;
		}
		
		.tab:hover {
			background-color:#aac9ff;
			color: white;
		}
		
		.tab.selected {
			background-color: #4489ff;
			color: white;
		}
	</style>
</head>
<body>
	<p>Welcome, <?php echo $username ?></p>
	<div id="nav">
		<div class="tab selected" id="personal"><p>Personal Info</p></div>
		<div class="tab" id="gyms"><p>Manage Gyms</p></div>
		<div class="tab" id="users"><p>Create Users</p></div>
	</div>
	<div class="view home" id="personal">
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
					echo "<li>" . $row[0] . ", " . $row[1] . " <form method=get action='editgym.php'><input type=hidden name='gymname' value='" . $row[0] . "'><input type=hidden name='gymloc' value='" . $row[1] . "'><input type=hidden name='mid' value=$mid><button type='submit'>Edit Gym</button></form><br><p>Classes</p><form method=get action='newclass.php'><input type=hidden name='mid' value=$mid><input type=hidden name='gymname' value='". $row[0] ."'><input type=hidden name='gymloc' value='".$row[1]."'><button type='submit'>New Class</button></form><ul>";
					$sql = "select distinct gc.class_id, gc.name, gc.cost, t.name from gymclass gc, gymuser t where gc.gym_name = '".$row[0]."' and gc.gym_location = '". $row[1]."' and gc.trainer_membership_id = t.membership_id order by gc.name";
					$parseclass = OCI_Parse($db_conn, $sql);
					oci_execute($parseclass);
					
					while (($classrow = oci_fetch_array($parseclass, OCI_BOTH)) != false) {
						echo "<li>" . $classrow[1] . " with " . $classrow[3] . ", $" . $classrow[2] . "<form method=get action='editclass.php'><input type=hidden name='classid' value=" . $classrow[0] . "><input type=hidden name='mid' value=$mid><button type='submit'>Edit class</button></form><br></li>";
					}
					
					echo "</ul></li>";
				}
				echo "</ul>";
			}
		?>
	</div>
	<div class="view" id="users">
		<h3> Users </h3>
		<p> Create a new user </p>
		<form method=post>
			<span style="color:red;"><?php echo $usererror ?></span><br>
			<label>Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input name="name" type=text><br>
			<label>Email:</label><span style="color:red;"><?php echo $emailerror ?></span><input name="email" type=text><br>
			<label>Phone number:</label><span style="color:red;"><?php echo $phoneerror ?></span><input name="phone" type=text><br>
			<label>User type: </label><br><input type="radio" name="utype" value="athlete" checked>Athlete<br><input type="radio" name="utype" value="trainer">Trainer<br><input type="radio" name="utype" value="admin">Admin<br>
			<input type=submit name="newuser">
		</form>
	</div>
</body>
</html>