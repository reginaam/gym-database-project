<!DOCTYPE html>

<html>

	<head>
		<title>View A Class Page</title>
	</head>

	<body>
		<div>
			<h1> Class Name:
			
			<?php
			include 'basesqlexecutors.php';
			$classID = $_GET['classID'];
			$sql = "select name, class_id from GymClass where class_id=$classID";
			$name = OCI_Parse($db_conn, $sql);
			oci_execute($name);
			$row = oci_fetch_array($name, OCI_BOTH);
			$name = $row[0];
			echo "<h3> $name </h3>";
			?>
			</h1>
			<p><b>Taught by: </b><i>
				<?php
					$sql = "select gu.name, gu.membership_id, gc.trainer_membership_id, gc.class_id from GymUser gu, GymClass gc where gu.membership_id=gc.trainer_membership_id and gc.class_id=$classID";
					$instructor = OCI_Parse($db_conn, $sql);
					oci_execute($instructor);
					while ($row = OCI_Fetch_Array($instructor, OCI_BOTH)) {
						echo $row["NAME"];
					}
				?>
			</i></p>
		</div>
		<hr/>
		
		<div>
			<h2>Time and Dates</h2>
			<p>
			<?php
				$sql= "select gc.class_id, gc.gym_name, gc.gym_location, gc.class_date, gc.start_time, gc.end_time from GymClass gc where gc.class_id=$classID";
				$schedule = OCI_Parse($db_conn, $sql);
				oci_execute($schedule);
				while ($row = OCI_Fetch_Array($schedule, OCI_BOTH)) {
					echo "<p><b>Location: </b><br>";
					echo $row["GYM_NAME"] . ", " . $row["GYM_LOCATION"] . "</p>";
					echo "<hr>";
					echo "<p><b>Date: </b><br>";
					echo $row["CLASS_DATE"] . "</p>";
					echo "<p><b>Time: </b><br>";
					echo $row["START_TIME"] . "->" . $row["END_TIME"] . "</p>";
				}
			?>
			</p>
			
		</div>

		<div>
			<h3>Routine and Intensity</h3>
			<p>
				<?php
					$sql = "select routine_name, class_id, intensity from Routine where class_id='$classID'";
					$intensity = OCI_Parse($db_conn, $sql);
					oci_execute($intensity);
					while ($row = OCI_Fetch_Array($intensity, OCI_BOTH)) {
						echo "<p><b>Routine name: </b><i>" . $row["ROUTINE_NAME"] . "</i><br>";
						echo "<b>Intensity: </b><i>" . $row["INTENSITY"] . "/10</i></p>";
					}
				?>
			</p>
			
			<p>
				<?php
					$sql = "select cost, class_id from GymClass where class_id=$classID";
					$cost = OCI_Parse($db_conn, $sql);
					oci_execute($cost);
					while ($row = OCI_Fetch_Array($cost, OCI_BOTH)) {
						echo "<p><b>Cost: </b><i>$" . $row["COST"] . "</i></p>";
					}
				?>
			</p>

		</div>

		<div>
			<form method="post" action="athlete.php">
				<input type="submit" value="Sign me up!" name="signUp">
			</form>
		</div>
	</body>

</html>