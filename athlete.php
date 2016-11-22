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

$sql = "select membership_id from athlete where membership_id=$mid";
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
	else if (array_key_exists('addClass', $_POST)) {
		$classID = $_POST['cid'];
		$sql = "insert into Attends values('$classID', $mid)";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		header("Location: athlete.php?mid=$mid&tab=1");
	}
	
	else if (array_key_exists('removeClass', $_POST)) {
		$classID = $_POST['cid'];
		$sql = "delete Attends where class_id = $classID and membership_id = $mid";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		header("Location: athlete.php?mid=$mid&tab=1");
	}

}



?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title> Athlete Home </title>
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
	
	.half {
		display: inline-block;
		width: 39%;
		vertical-align: top;
		padding: 0 5%;
	}
	</style>
</head>
<body>
	<div class="header">
		<h2> Athlete </h2>
		<p style="margin-top:5px;"><i>Welcome, <?php echo $username ?></i></p>
	</div>
	<div id="nav">
		<div class="tab <?php if ($tab == 0) echo 'selected';?>" id="personal"><p>Personal Info</p></div>
		<div class="tab <?php if ($tab == 1) echo 'selected';?>" id="gyms"><p>View Classes</p></div>
		<div class="tab <?php if ($tab == 2) echo 'selected';?>" id="routines"><p>Manage Routines</p></div>
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
		<div class="half">
			<?php
				$sql = "select gc.name, gu.name, gc.class_id, gu.membership_id, gc.trainer_membership_id, a.class_id, a.membership_id from GymClass gc, GymUser gu, Attends a where gu.membership_id=gc.trainer_membership_id and gc.class_id=a.class_id and a.membership_id=$mid";
				$parse = OCI_Parse($db_conn, $sql);
				$r = oci_execute($parse);
				if (!$r) {
					echo "<span style='color:red';> Could not get class info";
				}
				else {
				echo "<h3 style='display: inline-block';> Enrolled Classes </h3>";
				while ($row = oci_fetch_array($parse, OCI_BOTH)) {
					$className  = $row[0];
					$trainerName = $row[1];
					$classID = $row[2];

					echo "<li class='gymlist'><p style='display:inline-block;'>$className with $trainerName</p><form method=post style='display:inline-block;'><input type=hidden name='cid' value='$classID'><button class='editgymbutton' name='removeClass'><i class='material-icons'>-</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Info</p></li>";

					//select individual class info

					$sql = "select gc.gym_name, gc.gym_location, r.routine_name, gc.cost from GymClass gc left join Routine r on r.class_id=gc.class_id where gc.class_id=$classID";

					$parseclass = OCI_Parse($db_conn, $sql);
					oci_execute($parseclass);

					//display class info
					while ($classrow = oci_fetch_array($parseclass, OCI_BOTH)) {
						$gym_name = $classrow[0];
						$gym_location = $classrow[1];
						$class_date = $classrow[2];
						$class_start_time = $classrow[3];
						$class_end_time = $classrow[4];	
						$routine_name = $classrow[5];
						$class_cost = $classrow[6];

						echo "<li class='classlist'><p style='display:inline-block;'>
							Gym: $gym_name, $gym_location<br>
							Date: $class_date<br>
							Time: $class_start_time -> $class_end_time<br>
							Routine: $routine_name<br>
							Cost: $$class_cost<br>
						</p><br></li>";
					}
					
					echo "</ul></li>";
				}
				echo "</ul>";
			}
			?>
		</div>
		
		<div class="half">
		<?php 

			$nosql = "create view notattends as (select class_id from gymclass) minus (select class_id from attends where attends.membership_id = $mid)";
			$parse = OCI_Parse($db_conn, $nosql);
			oci_execute($parse);
			$sql = "select distinct gc.name, gu.name, gc.class_id, gc.gym_name, gc.gym_location, r.routine_name, gc.cost, gc.class_date, gc.start_time, gc.end_time from GymClass gc join notattends na on na.class_id = gc.class_id join GymUser gu on gu.membership_id=gc.trainer_membership_id left join Routine r on gc.class_id=r.class_id";
			$parse = OCI_Parse($db_conn, $sql);
			oci_execute($parse);

			$parse = OCI_Parse($db_conn, $sql);
				$r = oci_execute($parse);
				if (!$r) {
					echo "<span style='color:red';> Could not get class info";
				}
				else {
				echo "<h3 style='display: inline-block';> Other Classes</h3>
				<form method=get action='queryClasses.php' style='display:inline-block;'>
				<input type=hidden name='mid' value=" . $mid . ">
				<button type='submit' class='editgymbutton'><i class='material-icons'>find_in_page</i></button>
			</form>";
				while ($row = oci_fetch_array($parse, OCI_BOTH)) {
					$className  = $row[0];
					$trainerName = $row[1];
					$classID = $row[2];
					$gym_name = $row[3];

					$gym_location = $row[4];	
					$routine_name = $row[5];
					$class_cost = $row[6];
					$date = $row[7];
					$stime = $row[8];
					$ftime = $row[9];


					echo "<li class='gymlist'><p style='display:inline-block;'>$className with $trainerName</p><form method=post style='display:inline-block;'><input type=hidden name='cid' value='$classID'><button class='editgymbutton' name='addClass'><i class='material-icons'>+</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Info</p></li>";

						echo "<li class='classlist'><p style='display:inline-block;'>
							Gym: $gym_name, $gym_location<br>
							Date: $class_date<br>
							Time: $class_start_time -> $class_end_time<br>
							Routine: $routine_name<br>
							Cost: $$class_cost<br>
							Date: $date <br>
							Time: $stime -> $ftime <br>
						</p><br></li>";
						echo "</ul></li>";
					}
					echo "</ul>";

			}
			
		?>
		</div>
	</div>
	
	<div class="view" id="routines">
		<div class="half">
			<h3 style='display:inline-block;'> Your Routines </h3>
			<form method=get action='newroutine.php' style='display:inline-block;'>
				<input type=hidden name='mid' value=<?php echo $mid ?>>
				<button type='submit' class='newgymbutton'>+</button>
			</form>
			<ul style="padding-left: 0;">
			<?php 
				$sql = "select routine_name, intensity from routine where membership_id=$mid";
				$parse = OCI_Parse($db_conn, $sql);
				$r = oci_execute($parse);
				if (!$r) {
					echo "<span style='color:red;'> Could not retrieve routine info </span>";
				} else {
					while ($row = oci_fetch_array($parse, OCI_BOTH)) {
						$rname = $row[0];
						$rintensity = $row[1];
						echo "<li class='gymlist'><p style='display:inline-block;'>$rname, Intensity: $rintensity/10</p><form method=get action='editroutine.php' style='display:inline-block;'><input type=hidden name='routinename' value='$rname'><input type=hidden name='intensity' value='$rintensity'><input type=hidden name='mid' value=$mid><button type='submit' class='editgymbutton'><i class='material-icons'>create</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Exercises</p><form method=get action='newexercise.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><input type=hidden name='routinename' value='$rname'><input type=hidden name='intensity' value='$rintensity'><button type='submit' class='newgymbutton'>+</button></form></li>";
						$sql = "select exercise_name, body_part from exercise where routine_name='$rname' and intensity = $rintensity";
						$parseex = OCI_Parse($db_conn, $sql);
						oci_execute($parseex);
				
						while ($exrow = oci_fetch_array($parseex, OCI_BOTH)) {
							$exname = $exrow[0];
							$expart = $exrow[1];
							echo "<li class='classlist'><p style='display:inline-block;'>$exname (works: $expart)</p><form method=get action='editexercise.php' style='display:inline-block;'><input type=hidden name='exercisename' value='$exname'><input type=hidden name='bodypart' value='$expart'><input type=hidden name='mid' value=$mid><button type='submit' class='editgymbutton'><i class='material-icons'>create</i></button></form><br></li>";
						}
				
						echo "</ul></li>";
					}
				}
			?>
			</ul>
		</div>
		<div class="half">
			<h3 style='display:inline-block;'> Routines you follow </h3>
			<form method=get action='queryroutine.php' style='display:inline-block;'>
				<input type=hidden name='mid' value=<?php echo $mid ?>>
				<button type='submit' class='editgymbutton'><i class='material-icons'>find_in_page</i></button>
			</form>
			<ul style="padding-left: 0;">
				<?php 
					$sql = "select routine_name, intensity from workon where membership_id=$mid";
					$parse = OCI_Parse($db_conn, $sql);
					$r = oci_execute($parse);
					if (!$r) {
						echo "<span style='color:red;'> Could not retrieve routine info </span>";
					} else {
						while ($row = oci_fetch_array($parse, OCI_BOTH)) {
							$rname = $row[0];
							$rintensity = $row[1];
							echo "<li class='gymlist'><p style='display:inline-block;'>$rname, Intensity: $rintensity/10</p><hr><ul><li><p style='color:101010; display: inline-block;'>Exercises</p></li>";
							$sql = "select exercise_name, body_part from exercise where routine_name='$rname' and intensity = $rintensity";
							$parseex = OCI_Parse($db_conn, $sql);
							oci_execute($parseex);
				
							while ($exrow = oci_fetch_array($parseex, OCI_BOTH)) {
								$exname = $exrow[0];
								$expart = $exrow[1];
								echo "<li class='classlist'><p style='display:inline-block;'>$exname (works: $expart)</p><br></li>";
							}
				
							echo "</ul></li>";
						}
					}
				?>
			</ul>
		</div>
	</div>
</body>
</html>
