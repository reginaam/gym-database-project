<?php
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
	if (!$mid) {
		header("Location: interface.php");
	}
	
	$sql = "select membership_id from trainer where membership_id=$mid";
	$result = OCI_Parse($db_conn, $sql);
	oci_execute($result);
	if (!oci_fetch_array($result)) {
		header("Location: interface.php");
	}
	
	$errors = "";
	$nameerror = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($name) > 80) {
			$anyerrors = true;
			$nameerror = "Name too long";
		}
		
		$intensity = $_POST['intensity'];
		
        $sets = $_POST['sets'];
        
        $reps = $_POST['reps'];
        
        $class = $_POST['class'];
		
		if (!$anyerrors) {
			$sql = "insert into routine values('$name', '$intensity', '$sets', '$reps', '$mid', '$class')";
			$result = OCI_Parse($db_conn, $sql);
			$r = oci_execute($result);
			if (!$r) {
				$errors = "Failed to create Routine";
			} else {
				header("Location: trainer.php?mid=$mid");
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> New Routine </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
<h3> Create a new Routine </h3><br>
<form method=post>
	<span style="color:red;"><?php echo $errors ?></span>
	<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type="text" name="name"><br><br>
    <label> Intensity: </label><input type=int name="intensity" value="<?php echo $intensity ?>"><br><br>
    <label> Sets: </label><input type=int name="sets" value="<?php echo $sets ?>"><br><br>
    <label> Reps: </label><input type=int name="reps" value="<?php echo $reps ?>"><br><br>
    <label> Class ID: </label><input type=int name="class" value="<?php echo $class ?>"><br><br>
	<button type=submit> Create </button>
</form>
</body>
</html>