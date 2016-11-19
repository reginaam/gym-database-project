<?php

include 'basesqlexecutors.php';

$mid = $_GET['mid'];
$name = $_GET['exercisename'];
$body = $_GET['bodypart'];
if (!$mid || !$name || !$body) {
	header("Location: index.php");
}

$istrainer = true;
$sql = "select membership_id from trainer where membership_id=$mid";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
if (!oci_fetch_array($result)) {
	$istrainer = false;
}

$sql = "select exercise_name, body_part, benefit from exercise where exercise_name='$name' and body_part ='$body'";
$result = OCI_Parse($db_conn, $sql);
oci_execute($result);
$row = oci_fetch_array($result);
$name = $row[0];
$body = $row[1];
$benefit = $row[2];

$errors = "";
$nameerror = "";
$bodyerror = "";
$benefiterror = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$anyerrors = false;
	$newname = htmlspecialchars($_POST['name'], ENT_QUOTES);
	if (strlen($newname) > 80) {
		$newname = $name;
		$nameerror = "Name too long";
		$anyerrors = true;
	} else $name = $newname;
    
    $newbody = htmlspecialchars($_POST['body'], ENT_QUOTES);
    if (strlen($newname) > 20) {
        $newbody = $body;
        $bodyerror = "Body Part too long";
        $anyerrors = true;
    } else $body = $newbody;
    
    $newbenefit = htmlspecialchars($_POST['benefit'], ENT_QUOTES);
    if (strlen($newname) > 500) {
        $newbenefit = $benefit;
        $benefiterror = "Benefit too long";
        $anyerrors = true;
    } else $benefit = $newbenefit;

	if (!$anyerrors) {
		$sql = "update exercise set exercise_name='$newname', body_part='$newbody', benefit='$newbenefit' where exercise_name='$name' and body_part = '$body'";
		$result = OCI_Parse($db_conn, $sql);
		$r = oci_execute($result);
		if (!$r) {
			$errors = "Error updating info";
		} else if ($istrainer) {
			header("Location: trainer.php?mid=$mid");
		} else {
			header("Location: athlete.php?mid=$mid");
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> Edit Exercise </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
<h3> Edit Exercise: <?php echo "$name, $body"?></h3><br>
	<form method="post">
		<span style="color:red;"><?php echo $errors ?></span>
		<label> Name: </label><span style="color:red;"><?php echo $nameerror ?></span><input type=text name="name" value="<?php echo $name ?>"><br><br>
		<label> Body Part: </label><span style="color:red;"><?php echo $bodyerror ?></span><input type=text name="body" value=<?php echo $body ?>><br><br>
        <label> Benefit: </label><span style="color:red;"><?php echo $benefiterror ?></span><input type=text name="benefit" value=<?php echo $benefit ?>><br><br>
		<button type="submit">Update</button>
	</form>
</body>
</html>