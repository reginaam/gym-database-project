<?php
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
    $rname = $_GET['routinename'];
    $intensity = $_GET['intensity'];
	if (!$mid || !$rname || !$intensity) {
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
    $bodyerror = "";
    $benefiterror = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
		if (strlen($name) > 80) {
			$anyerrors = true;
			$nameerror = "Name too long";
		}
        
        $body = htmlspecialchars($_POST['body'], ENT_QUOTES);
        if (strlen($name) > 20) {
            $anyerrors = true;
            $bodyerror = "Body Part too long";
        }
        
        $benefit = htmlspecialchars($_POST['benefit'], ENT_QUOTES);
        if (strlen($name) > 500) {
            $anyerrors = true;
            $benefiterror = "Benefit too long";
        }
		
		if (!$anyerrors) {
			$sql = "insert into exercise values('$name', '$body', '$benefit', '$rname', '$intensity', '$mid')";
			$result = OCI_Parse($db_conn, $sql);
			$r = oci_execute($result);
			if (!$r) {
				$errors = "Failed to create Exercise";
			} else {
				header("Location: trainer.php?mid=$mid");
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> New Exercise </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
<h3> Create a new Exercise for <?php echo "$rname, $intensity"?></h3><br>
<form method=post>
	<span style="color:red;"><?php echo $errors ?></span>
	<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type="text" name="name"><br><br>
    <label> Body Part: </label><span style="color:red;"><?php echo $bodyerror ?></span><input type="text" name="body"><br><br>
    <label> Benefit: </label><span style="color:red;"><?php echo $benefiterror ?></span><input type="text" name="benefit"><br><br>
	<button type=submit> Create </button>
</form>
</body>
</html>