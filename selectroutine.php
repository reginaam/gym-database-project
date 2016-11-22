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
	
	$rname = $_GET['rname'];
	$imax = $_GET['imax'];
	$imin = $_GET['imin'];
	$smin = $_GET['smin'];
	$smax = $_GET['smax'];
	$rmin = $_GET['rmin'];
	$rmax = $_GET['rmax'];
	$ename = $_GET['ename'];
	$bpart = $_GET['bpart'];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$rouname = $_POST['rname'];
		$rintensity = $_POST['intensity'];
		$sql = "insert into workon values('$rouname', $rintensity, $mid)";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		header("Location: athlete.php?mid=$mid&tab=2");
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
	<h3> Select a routine to work on. </h3>
	<ul style="width:50%; margin: 0 auto;">
		<?php 
			$sql = "select distinct r.routine_name, r.intensity from routine r left join exercise e on r.routine_name = e.routine_name and r.intensity = e.intensity where r.membership_id <> $mid";
			
			if ($rname) $sql .= " and upper(r.routine_name) like upper('%$rname%')";
			if ($imin) $sql .= " and r.intensity >= $imin";
			if ($imax) $sql .= " and r.intensity <= $imax";
			if ($smin) $sql .= " and r.sets >= $smin";
			if ($smax) $sql .= " and r.sets <= $smax";
			if ($rmin) $sql .= " and r.reps >= $rmin";
			if ($rmax) $sql .= " and r.reps <= $rmax";
			if ($ename) $sql .= " and upper(e.exercise_name) like upper('%$ename%')";
			if ($bpart) $sql .= " and upper(e.body_part) like upper('%$bpart%')";
			$sql .= " minus (select w.routine_name, w.intensity from workon w where w.membership_id = $mid)";
			$parse = OCI_Parse($db_conn, $sql);
			$r = oci_execute($parse);
			if (!$r) {
				echo "<span style='color:red;'> Could not retrieve routine info </span>";
			} else {
				$row = oci_fetch_array($parse, OCI_BOTH);
				if (!$row) {
					echo "No results found.";
				}
				while ($row) {
					$rname = $row[0];
					$rintensity = $row[1];
					echo "<li class='gymlist'><p style='display:inline-block;'>$rname, Intensity: $rintensity/10</p><form method=post style='display:inline-block;'><input type=hidden name='mid' value=$mid><input type=hidden name='rname' value='$rname'><input type=hidden name='intensity' value='$rintensity'><button class='editgymbutton'><i class='material-icons'>playlist_add</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Exercises</p></li>";
					$sql = "select exercise_name, body_part from exercise where routine_name='$rname' and intensity = $rintensity";
					$parseex = OCI_Parse($db_conn, $sql);
					oci_execute($parseex);
		
					while ($exrow = oci_fetch_array($parseex, OCI_BOTH)) {
						$exname = $exrow[0];
						$expart = $exrow[1];
						echo "<li class='classlist'><p style='display:inline-block;'>$exname (works: $expart)</p><br></li>";
					}
		
					echo "</ul></li>";
					$row = oci_fetch_array($parse, OCI_BOTH);
				}
			}
		?>
	</ul>
</body>
</html>