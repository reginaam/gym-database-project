<?php 
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
	if (!$mid) {
		header("Location: index.php");
	}
	
	$sql = "select membership_id from athlete where membership_id=$mid";
	$result = OCI_Parse($db_conn, $sql);
	oci_execute($result);
	if (!oci_fetch_array($result)) {
		header("Location: index.php");
	}
	
	$cname = $_GET['cname'];
	$insname = $_GET['instructorName'];
	$gymname = $_GET['gymName'];
	$gymloc = $_GET['gymLocation'];
	$date = $_GET['date'];
	$rname = $_GET['rname'];
	$starttime = $_GET['startTime'];
	$endtime = $_GET['endTime'];
	$cost = $_GET['cost'];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$classID = $_POST['cid'];
		$sql = "insert into Attends values('$classID', $mid)";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		header("Location: athlete.php?mid=$mid&tab=1");
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<title> Select routine </title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
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
	<h3> Select a class to attend. </h3>
	<ul style="width:50%; margin: 0 auto;">
		<?php 
			$sql = "select distinct gc.name, gu.name, gc.class_id, gc.gym_name, gc.gym_location, r.routine_name, gc.cost, gu.membership_id, gc.trainer_membership_id, r.class_id from GymClass gc left join Routine r on r.class_id = gc.class_id join GymUser gu on gu.membership_id=gc.trainer_membership_id";

			if ($cname) $sql .= " and upper(gc.name) like upper('%$cname%')";
			if ($insname) $sql .= " and upper(gu.name) like upper('%$insname%')";
			if ($gymname) $sql .= " and upper(gc.gym_name) like upper('%$gymname%')";
			if ($gymloc) $sql .= " and upper(gc.gym_location) like upper('%$gymloc%')";
			if ($rname) $sql .= " and upper(r.routine_name) like upper('%$rname%')";
			if ($cost) $sql .= " and upper(gc.cost) like upper('%$cost%')";

			$parse = OCI_Parse($db_conn, $sql);
			$r = oci_execute($parse);
			if (!$r) {
				echo "<span style='color:red;'> Could not retrieve class info </span>";
			} else {
				$row = oci_fetch_array($parse, OCI_BOTH);
				if (!$row) {
					echo "No results found.";
				}
				while ($row) {
					$cName = $row[0];
					$insName = $row[1];
					$classID = $row[2];
					
					echo "<li class='gymlist'><p style='display:inline-block;'>$cName with $insName</p><form method=post style='display:inline-block;'><input type=hidden name='mid' value=$mid><input type=hidden name='cid' value='$classID'><button class='editgymbutton'><i class='material-icons'>playlist_add</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Info:</p></li>";

					$sql = "select distinct gc.gym_name, gc.gym_location, r.routine_name, gc.cost, gc.class_date, gc.start_time, gc.end_time from GymClass gc left join Routine r on r.class_id=gc.class_id where gc.class_id=$classID";
					$parseex = OCI_Parse($db_conn, $sql);
					oci_execute($parseex);
		
					while ($exrow = oci_fetch_array($parseex, OCI_BOTH)) {
						$gymName = $exrow[0];
						$gymLocation = $exrow[1];
						$routineName = $exrow[2];
						$classCost = $exrow[3];
						$date = $exrow[4];
						$stime = $exrow[5];
						$ftime = $exrow[6];

						echo "<li class='classlist'><p style='display:inline-block;'>
							Gym: $gymName, $gymLocation<br>
							Routine: $routineName<br>
							Cost: $$classCost<br>
							Date: $date <br>
							Time: $stime -> $ftime <br>
						</p><br></li>";
					}
		
					echo "</ul></li>";
					$row = oci_fetch_array($parse, OCI_BOTH);
				}
			}
		?>
	</ul>
</body>
</html>
