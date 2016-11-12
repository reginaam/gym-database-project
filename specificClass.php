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
			$className = executePlainSQL("select name, class_id from GymClass where class_id='$classID'");
			printClassName($className);
			?>
			</h1>
			<p><b>Taught by: </b><i>
				<?php
					$instructor = executePlainSQL("select gu.name, gu.membership_id, gc.trainer_membership_id, gc.class_id from GymUser gu, GymClass gc where gu.membership_id=gc.trainer_membership_id and gc.class_id='$classID'");
					printInstructor($instructor);
				?>
			</i></p>
		</div>
		<hr/>
		
		<div>
			<h2>Time and Dates</h2>
			<p>
			<?php
				$schedule = executePlainSQL("select fs.class_id, fs.class_date, fs.start_time, fs.end_time, gc.class_id, gc.gym_name, gc.gym_location from FollowSchedule fs, GymClass gc where gc.class_id=fs.class_id and gc.class_id='$classID'");
				printClassSchedule($schedule);
			?>
			</p>
			
		</div>

		<div>
			<h3>Routine and Intensity</h3>
			<p>
				<?php
					$intensity = executePlainSQL("select routine_name, class_id, intensity from Routine where class_id='$classID'");
					printRoutineInfo($intensity);
				?>
			</p>
			
			<p>
				<?php
					$cost = executePlainSQL("select cost, class_id from GymClass where class_id='$classID'");
					printCost($cost);
				?>
			</p>

		</div>

		<div>
			<form method="post" action="viewClasses.php">
				<input type="submit" value="Sign me up!">
			</form>
		</div>
	</body>

</html>

<?php
		
// Prints the names of all available classes as hyperlinks
function printClassName($result) {
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo $row["NAME"];
	}
}

// Prints the dates and start and end time of selected class
function printClassSchedule($result) {
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<p><b>Date: </b><br>";
		echo $row["CLASS_DATE"];
		echo "<p><b>Times:</b><br>";
		echo "Start: <i>" . $row["START_TIME"] . "</i><br>";
		echo "End: <i>" . $row["END_TIME"] . "</i></p>";
		echo "<p><b>Location: </b><br>";
		echo $row["GYM_NAME"] . ", " . $row["GYM_LOCATION"] . "</p>";
		echo "<hr>";
	}
}

// Prints the name of the instructor for the class
function printInstructor($result){
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo $row["NAME"];
	}
}


// Prints the routine info
function printRoutineInfo($result){
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<p><b>Routine name: </b><i>" . $row["ROUTINE_NAME"] . "</i><br>";
		echo "<b>Intensity: </b><i>" . $row["INTENSITY"] . "/10</i></p>";
	}
}


// Prints the cost of the class
function printCost($result){
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<p><b>Cost: </b><i>$" . $row["COST"] . "</i></p>";
	}
}


?>
