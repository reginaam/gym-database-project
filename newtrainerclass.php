<?php
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
	if (!$mid) {
		header("Location: index.php");
	}
	
	$sql = "select membership_id from trainer where membership_id=$mid";
	$result = OCI_Parse($db_conn, $sql);
	oci_execute($result);
	if (!oci_fetch_array($result)) {
		header("Location: index.php");
	}
	
	$errors = "";
	$nameerror = "";
	$costerror = "";
	$dateerror = "";
	$stimeerror = "";
	$ftimeerror = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($name) > 80) {
			$anyerrors = true;
			$nameerror = "Name too long";
		}
		
		$strcost = $_POST['cost'];
		$cost = round(floatval($strcost),2);
		if ($cost == 0 and $strcost != "0") {
			$costerror = "Enter a valid cost.";
			$anyerrors = true;
		}
		
		$date = $_POST['date'];
		if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date) && !preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{2}$/", $date)) 
		{
			$dateerror = "Date must be in format yyyy-mm-dd or yy-mm-dd";
			$anyerrors = true;
		}
		
		$stime = $_POST['stime'];
		if (!preg_match("/^[0-2]{1}[0-9]{1}[0-5]{1}[0-9]{1}$/", $stime)) {
			$stimeerror = "Time must be in format HHMM";
			$anyerrors = true;
		}
		
		$ftime = $_POST['ftime'];
		if (!preg_match("/^[0-2]{1}[0-9]{1}[0-5]{1}[0-9]{1}$/", $ftime)) {
			$ftimeerror = "Time must be in format HHMM";
			$anyerrors = true;
		}
		
		$admin = $_POST['admin'];
        
        $gym = $_POST['gym'];
        
        $gymarr = explode(" *at* ", $gym);
        $gymname = $gymarr[0];
        $gymloc = $gymarr[1];
        
		$sql = "select class_id from gymclass order by class_id desc";
		$state = OCI_Parse($db_conn, $sql);
		$r = oci_execute($state);
		if (!$r) {
			$anyerrors = true;
		}
		if (!$anyerrors) {
			$row = oci_fetch_array($state);
			$newid = $row[0] + 1;
			
			$sql = "insert into gymclass values($newid, '$name', $cost, $admin, $mid, '$gymname', '$gymloc', '$date', '$stime', '$ftime')";
			$result = OCI_Parse($db_conn, $sql);
			$r = oci_execute($result);
			if (!$r) {
				$errors = "Failed to create class";
			} else {
				header("Location: trainer.php?mid=$mid&tab=1");
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> New Class </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
<h3> Create a new class</h3><br>
<form method=post>
	<span style="color:red;"><?php echo $errors ?></span>
	<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type="text" name="name"><br><br>
	<label> Cost: </label><span style="color:red;"><?php echo $costerror ?></span><input type="text" name="cost"><br><br>
	<label> Date: </label><span style="color:red;"><?php echo $dateerror ?></span><input type=text name="date" value=<?php echo $date ?>><br><br>
	<label> Start time: </label><span style="color:red;"><?php echo $stimeerror ?></span><input type=text name="stime" value=<?php echo $stime ?>><br><br>
	<label> End time: </label><span style="color:red;"><?php echo $ftimeerror ?></span><input type=text name="ftime" value=<?php echo $ftime ?>><br><br>
	<label> Admin: </label><select name="admin">
		<?php 
			$sql = "select name, gymAdmin.membership_id from gymuser, gymAdmin where gymAdmin.membership_id = gymuser.membership_id order by name";
			$result = OCI_Parse($db_conn, $sql);
			oci_execute($result);
			
			while (($row = oci_fetch_array($result)) != false) {
                $selected = "selected";
				echo "<option value='".$row[1]."' ".$selected.">".$row[0]."</option>";
			}
		?></select>
		<br><br>
<label> Gym: </label><select name="gym">
<?php
    $sql = "select gym_name, gym_location from gym order by gym_name";
    $gymresult = OCI_Parse($db_conn, $sql);
    oci_execute($gymresult);
    
    while (($row = oci_fetch_array($gymresult)) != false) {
    	$gymname = $row[0];
    	$gymloc = $row[1];
        echo "<option value='$gymname *at* $gymloc'>$gymname at $gymloc</option>";
    }
    ?></select>
<br><br>
	<button type=submit> Create </button>
</form>
</body>
</html>