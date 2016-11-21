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
	
	$cnameerror = "";
	$insnameerror = "";
	$gymnameerror = "";
	$gymlocationerror = "";
	$dateerror = "";
	$starttimeerror = "";
	$endtimeerror = "";
	$rnameerror = "";
	$costerror = "";
	
//change date and time error checking later

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$cname = htmlspecialchars($_POST['cname'], ENT_QUOTES);
		if (strlen($cname) > 80) {
			$anyerrors = true;
			$cnameerror = "Class name too long";
		}
		
		$instructorName = htmlspecialchars($_POST['instructorName'], ENT_QUOTES);
		if (strlen($instructorName) > 80) {
			$anyerrors = true;
			$insnameerror = "Instructor name too long";
		}
		
		$gymName = htmlspecialchars($_POST['gymName'], ENT_QUOTES);
		if (strlen($gymName) > 80) {
			$anyerrors = true;
			$gymnameerror = "Gym name too long";
		}
		
		$gymLocation = htmlspecialchars($_POST['gymLocation'], ENT_QUOTES);
		if (strlen($gymLocation) > 80) {
			$anyerrors = true;
			$gymlocationerror = "Gym location too long";
		}
		
		$date = htmlspecialchars($_POST['date'], ENT_QUOTES);
		if (strlen($date) > 80) {
			$anyerrors = true;
			$dateerror = "Date location too long";
		}
		
		$startTime = $_POST['startTime'];
		if ($startTime != NULL && $startTime < 1) {
			$anyerrors = true;
			$starttimeerror = "Start time is a positive int";
		}
		
		$endTime = $_POST['endTime'];
		if ($endTime != NULL && $endTime < 1) {
			$anyerrors = true;
			$endtimeerror = "End time is a positive int";
		}
		
		$rname = htmlspecialchars($_POST['rname'], ENT_QUOTES);
		if (strlen($rname) > 80) {
			$anyerrors = true;
			$rnameerror = "Routine name too long";
		}
		
		$cost = $_POST['cost'];
		if ($cost != NULL && $cost < 1) {
			$anyerrors = true;
			$costerror = "Cost is a positive int";
		}
		
		if (!$anyerrors) {
			$url = "Location: selectclass.php?mid=$mid&";
			if ($cname != "" && $cname != NULL) $url .= "cname=$cname&";
			if ($instructorName != "" && $instructorName != NULL) $url .= "instructorName=$instructorName&";
			if ($gymName != "" && $gymName != NULL) $url .= "gymName=$gymName&";
			if ($gymLocation != "" && $gymLocation != NULL) $url .= "gymLocation=$gymLocation&";
			if ($date != "" && $date != NULL) $url .= "date=$date&";
			if ($rname != "" && $rname != NULL) $url .= "rname=$rname&";
			if ($startTime != NULL) $url .= "startTime=$startTime&";
			if ($endTime != NULL) $url .= "imax=$imax&";
			if ($cost != NULL) $url .= "cost=$cost&";
			$url = substr($url, 0, -1);
			
			header($url);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> Query classes </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
	<h3> Specify classes you would like to see</h3><br>
	<form method=post>
		<label> Class name contains: </label><span style="color:red;"><?php echo $cnameerror ?></span><input type="text" name="cname" value="<?php $cname ?>" placeholder="Any"><br><br>
		<label> Gym name contains: </label><span style="color:red;"><?php echo $gymnameerror ?></span><input type="text" name="gymName" value="<?php $gymName ?>" placeholder="Any"><br><br>
		<label> Gym location contains: </label><span style="color:red;"><?php echo $gymlocationerror ?></span><input type="text" name="gymLocation" value="<?php $gymLocation ?>" placeholder="Any"><br><br>
		<label> Date contains: </label><span style="color:red;"><?php echo $dateerror ?></span><input type="text" name="date" value="<?php $date ?>" placeholder="Any"><br><br>
		<label> Routine name contains: </label><span style="color:red;"><?php echo $rnameerror ?></span><input type="text" name="rname" value="<?php $rname ?>" placeholder="Any"><br><br>

		<label> Start time: </label><span style="color:red;"><?php echo $starttimeerror ?></span><input type="number" name="startTime" value=<?php $startTime ?> placeholder="Any"><br><br>
		<label> End time: </label><span style="color:red;"><?php echo $endtimeerror ?></span><input type="number" name="endTime" value=<?php $endTime ?> placeholder="Any"><br><br>
		<label> Cost: </label><span style="color:red;"><?php echo $costerror ?></span><input type="number" name="cost" value=<?php $cost ?> placeholder="Any"><br><br>

		<button type=submit> Search </button>
	</form>
</body>
</html>
