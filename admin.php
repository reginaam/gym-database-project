<?php
include 'basesqlexecutors.php';

$mid = $_GET['mid'];
if (!$mid) {
	header("Location: index.php");
}

$tab = $_GET['tab'];
if (!$tab) {
	$tab = 0;
}

$sql = "select membership_id from gymadmin where membership_id=$mid";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
if (!oci_fetch_array($result)) {
	header("Location: index.php");
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

$u_name = $u_email = $u_phone = "";

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Personal section
	if (array_key_exists('personal', $_POST)) {
		$tab = 0;
		$anyerrors = false;
		$newname = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($newname)> 40) {
			$nameerror = "Name too long";
			$anyerrors = true;
		}
		$username = $newname;
		
		$newemail = htmlspecialchars($_POST['email']);
		if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
			$emailerror = "Enter a valid email";
			$anyerrors = true;
		}
		$email = $newemail;
		
		$newphone = $_POST['phone'];
		if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $newphone)) {
			$phoneerror = "Phone number must be in the format xxx-xxx-xxxx";
			$anyerrors = true;
		}
		$phone = $newphone;
		
		if (!$anyerrors) {
			$sql = "update gymuser set name='" . $newname . "', email='" . $newemail. "', phone_number='"  . $newphone . "' where membership_id=".$mid;
			$parse = OCI_Parse($db_conn, $sql);
			$r = oci_execute($parse);
			if(!$r) {
				$personalerror = "Error updating info";
			}
		}
	}
	else if (array_key_exists('newuser', $_POST)) {
		$tab = 2;
		$anyerrors = false;
		$u_name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($u_name)> 40) {
			$nameerror = "Name too long";
			$anyerrors = true;
		}
		$u_email = htmlspecialchars($_POST['email']);
		if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
			$emailerror = "Enter a valid email";
			$anyerrors = true;
		}
		$u_phone = htmlspecialchars($_POST['phone']);
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
	else if (array_key_exists('routinemetric', $_POST)) {
		$rtypeme = $_POST['type'];
		if ($rtypeme == 'maximum') {
			$typestr = "Max(intensity)";
		} else if ($rtypeme == 'minimum') {
			$typestr = "Min(intensity)";
		} else if ($rtypeme == 'average') {
			$typestr = "Avg(intensity)";
		}
		
		$ruserme = $_POST['user'];
		if ($ruserme == 'any') {
			$sql = "select $typestr from routine";
		} else if ($ruserme == 'athlete') {
			$sql = "select $typestr from routine, athlete where athlete.membership_id = routine.membership_id";
		} else if ($ruserme == 'trainer') {
			$sql = "select $typestr from routine, trainer where trainer.membership_id = routine.membership_id";
		}
		$result = OCI_Parse($db_conn, $sql);
		oci_execute($result);
		$row = oci_fetch_array($result);
		$routinemetric = round($row[0],2);
		$tab = 3;
	}
	else if (array_key_exists('bodypartmetric', $_POST)) { 
		$tab = 3;
		$btypeme = $_POST['type'];
		if ($btypeme == 'maximum') {
			$typestr = "Max(r.intensity)";
		} else if ($btypeme == 'minimum') {
			$typestr = "Min(r.intensity)";
		} else if ($btypeme == 'average') {
			$typestr = "Avg(r.intensity)";
		}
		
		$sql = "select e.body_part, $typestr from routine r, exercise e where r.intensity = e.intensity and r.routine_name = e.routine_name group by e.body_part order by e.body_part";
		$bpartresult = OCI_Parse($db_conn, $sql);
		oci_execute($bpartresult);
	}
	else if (array_key_exists('deleteClass', $_POST)) { 
		$classid = $_POST['classid'];
		$mid = $_POST['mid'];
		$sql = "delete GymClass where class_id = $classid";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		header("Location: admin.php?mid=$mid");
	}
}



?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title> Administration Home </title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="toggletab.js"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
	<link rel="stylesheet" href="toggletab.css">
	<link rel="stylesheet" href="forms.css">
	<style>
	.header {
		width: 100%;
		height: 50px;
		margin: 0 auto;
		margin-top: 10px;
		border-bottom: 3px solid #ff4545;
		text-align: center;
	}
	
	h2 {
		color: #ff4545;
		margin: 0 auto;
	}
	
	.newgymbutton {
		font-size: 25px;
		font-weight: bold;
		border: none;
		background-color: transparent;
		color: #a1a1a1;	
	}
	
	.newgymbutton:hover {
		color: #0eff00;	
		background-color: transparent;
		cursor: pointer;
	}
	
	.editgymbutton {
		border: none;
		background-color: transparent;
		color: #a1a1a1;	
	}
	
	.editgymbutton:hover {
		color: #4489ff;	
		background-color: transparent;
		cursor: pointer;
	}
	
	ul {
		text-align: left;
	}
	
	li {
		list-style-type: none;
		padding: 7px;
	}
	
	.gymlist {
		color: #4489ff;
	}
	
	.gymlist:nth-child(even) {
		background-color: #f1f1f1;
	}
	
	.gymlist:nth-child(odd) {
		background-color: #e2e7ff;
	}
	
	.classlist {
		color: #ff4545;
	}
	
	.classlist:nth-child(odd) {
		background-color: #f1f1f1;
	}

	.classlist:nth-child(even) {
		background-color: #ffe9e9;
	}
	
	.tab {
		width: 24.5%;
	}
	</style>
</head>
<body>
	<div class="header">
		<h2> Admin </h2>
		<p style="margin-top:5px;"><i>Welcome, <?php echo $username ?></i></p>
	</div>
	<div id="nav">
		<div class="tab <?php if ($tab == 0) echo 'selected';?>" id="personal"><p>Personal Info</p></div>
		<div class="tab <?php if ($tab == 1) echo 'selected';?>" id="gyms"><p>Manage Gyms</p></div>
		<div class="tab <?php if ($tab == 2) echo 'selected';?>" id="users"><p>Create Users</p></div>
		<div class="tab <?php if ($tab == 3) echo 'selected';?>" id="metrics"><p>Metrics</p></div>
	</div>
	<div class="view home" id="personal">
		<div class="innerview">
			<form method="post"> 
				<h3> Personal </h3>
				<span style="color:red;"><?php echo $personalerror ?></span><br>
				<label>Name</label><input name="name" type=text value="<?php echo $username ?>"><span><?php echo $nameerror ?></span><br><br>
				<label>Email</label><input name="email" type=text value=<?php echo $email ?>><span><?php echo $emailerror ?></span><br><br>
				<label>Phone number</label><input name="phone" type=text value=<?php echo $phone ?>><span><?php echo $phoneerror ?></span><br><br>
				<button type=submit name="personal">Update</button>
			</form>
		</div>
	</div>
	<div class="view" id="gyms">
		<div class="innerview">
		<?php 
			$sql = "select gym_name, gym_location from gym where membership_id = $mid";
			$parse = OCI_Parse($db_conn, $sql);
			$r = oci_execute($parse);
			if (!$r) {
				echo "<span style='color:red';> Could not get gym info";
			}
			else {
				echo "<h3 style='display: inline-block';> Gyms </h3><form method=get action='newgym.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><button type='submit' class='newgymbutton'>+</button></form><hr><ul style='width: 85%; margin: 0 auto;'>";
				while (($row = oci_fetch_array($parse, OCI_BOTH)) != false) {
					$gymname  = $row[0];
					$gymloc = $row[1];
					echo "<li class='gymlist'><p style='display:inline-block;'>$gymname, $gymloc</p><form method=get action='editgym.php' style='display:inline-block;'><input type=hidden name='gymname' value='$gymname'><input type=hidden name='gymloc' value='$gymloc'><input type=hidden name='mid' value=$mid><button type='submit' class='editgymbutton' ><i class='material-icons'>create</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Classes</p><form method=get action='newclass.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><input type=hidden name='gymname' value='$gymname'><input type=hidden name='gymloc' value='$gymloc'><button type='submit' class='newgymbutton'>+</button></form></li>";
					$sql = "select distinct gc.class_id, gc.name, gc.cost, t.name from gymclass gc, gymuser t where gc.gym_name = '$gymname' and gc.gym_location = '$gymloc' and gc.trainer_membership_id = t.membership_id order by gc.name";
					$parseclass = OCI_Parse($db_conn, $sql);
					oci_execute($parseclass);
					
					while (($classrow = oci_fetch_array($parseclass, OCI_BOTH)) != false) {
						$cid = $classrow[0];
						$classname = $classrow[1];
						$cost = $classrow[2];
						$tname = $classrow[3];
						echo "<li class='classlist'><p style='display:inline-block;'>$classname with $tname, $$cost</p><form method=get action='editclass.php' style='display:inline-block;'><input type=hidden name='classid' value='$cid'><input type=hidden name='mid' value='$mid'><button type='submit' class='editgymbutton'><i class='material-icons'>create</i></button></form>
						<form method=post style='display:inline-block;'><input type=hidden name='classid' value=$cid><input type=hidden name='mid' value=$mid><button class='editgymbutton' name='deleteClass'><i class='material-icons'>delete</i></button></form>
						<br></li>";
						
					}
					
					echo "</ul></li>";
				}
				echo "</ul>";
			}
		?>
		</div>
	</div>
	<div class="view" id="users">
		<div class="innerview">
			<h3> Users </h3>
			<p> Create a new user </p>
			<form method=post>
				<span style="color:red;"><?php echo $usererror ?></span><br>
				<label>Name </label><input name="name" type=text value=<?php echo $u_name ?>><span><?php echo $nameerror ?></span><br><br>
				<label>Email</label><input name="email" type=text value=<?php echo $u_email ?>><span><?php echo $emailerror ?></span><br><br>
				<label>Phone number</label><input name="phone" type=text value=<?php echo $u_phone ?>><span><?php echo $phoneerror ?></span><br><br>
				<label>User type </label><input type="radio" name="utype" value="athlete" checked>Athlete<br><input type="radio" name="utype" value="trainer">Trainer<br><input type="radio" name="utype" value="admin">Admin<br><br>
				<button type=submit name="newuser">Create user</button>
			</form>
		</div>
	</div>
	<div class="view" id="metrics">
		<div class="innerview">
			<h3> Useful Metrics </h3>
			<hr>
			<?php 
				$sql = "select Count(*) from athlete";
				$result = OCI_Parse($db_conn, $sql);
				oci_execute($result);
				$row = oci_fetch_array($result);
				$num = $row[0];
				echo "<p> There are $num athletes. </p>";
				$sql = "select Count(*) from trainer";
				$result = OCI_Parse($db_conn, $sql);
				oci_execute($result);
				$row = oci_fetch_array($result);
				$num = $row[0];
				echo "<p> There are $num trainers. </p>";
				$sql = "select Count(*) from gymadmin";
				$result = OCI_Parse($db_conn, $sql);
				oci_execute($result);
				$row = oci_fetch_array($result);
				$num = $row[0];
				echo "<p> There are $num admins. </p>";
			?>
			<hr>
			<form method="post">
				<p> Select the <select name="type"> 
									<option value='maximum' <?php if ($rtypeme == 'maximum') echo 'selected="selected"'; ?>>maximum</option>
									<option value='minimum' <?php if ($rtypeme == 'minimum') echo 'selected="selected"'; ?>>minimum</option>
									<option value='average' <?php if ($rtypeme == 'average') echo 'selected="selected"'; ?>>average</option>
								</select> intensity of routines created by 
								<select name="user"> 
									<option value='any' <?php if ($ruserme == 'any') echo 'selected="selected"'; ?>>all users.</option>
									<option value='athlete' <?php if ($ruserme == 'athlete') echo 'selected="selected"'; ?>>athletes.</option>
									<option value='trainer' <?php if ($ruserme == 'trainer') echo 'selected="selected"'; ?>>trainers.</option>
								</select>
				</p>
				<?php 
				if ($routinemetric) {
					echo "<p style='color: green;'> Answer: $routinemetric </p>";
				}
				?>
				<button type=submit name="routinemetric">Go</button>
			</form>
			<hr>
			<form method="post">
				<p> Select the <select name="type"> 
									<option value='maximum' <?php if ($btypeme == 'maximum') echo 'selected="selected"'; ?>>maximum</option>
									<option value='minimum' <?php if ($btypeme == 'minimum') echo 'selected="selected"'; ?>>minimum</option>
									<option value='average' <?php if ($btypeme == 'average') echo 'selected="selected"'; ?>>average</option>
								</select> intensity of routines organized by body part exercised.</p>
				<?php
					if ($btypeme) {
						echo "<table><tr><th>Body part</th><th>$btypeme intensity</th></tr>";
						while ($row = oci_fetch_array($bpartresult)) {
							$bpart = $row[0];
							$bmetr = $row[1];
							echo "<tr><td>$bpart</td><td>$bmetr</td></tr>";
						}
						echo "</table>";
					}
				?>
				<button type="submit" name="bodypartmetric">Go</button>
			</form>
			<hr>
			<p> Star athletes (athletes currently working on a routine of every available intensity):</p>
			<ul>
			<?php 
				$sql = "select name from gymuser where membership_id = ( select membership_id from athlete minus (select distinct membership_id from ((select distinct a.membership_id, r.intensity from athlete a, routine r) minus ((select distinct a.membership_id, r.intensity from athlete a, routine r where a.membership_id = r.membership_id) union (select distinct a.membership_id, w.intensity from athlete a, workon w where a.membership_id = w.membership_id)))))";
				$result = OCI_Parse($db_conn, $sql);
				oci_execute($result);
				while ($row = oci_fetch_array($result)) {
					$starname = $row[0];
					echo "<li> $starname </li>";
				}
			?>
			</ul>
		</div>
	</div>
</body>
</html>
