<?php 
	include 'basesqlexecutors.php';
	
	$mid = $_GET['mid'];
	if (!$mid) {
		header("Location: index.php");
	}
	
	$rnameerror = "";
	$iminerror = "";
	$imaxerror = "";
	$sminerror = "";
	$smaxerror = "";
	$rminerror = "";
	$rmaxerror = "";
	$enameerror = "";
	$bparterror = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$anyerrors = false;
		
		$rname = htmlspecialchars($_POST['rname'], ENT_QUOTES);
		if (strlen($rname) > 80) {
			$anyerrors = true;
			$rnameerror = "Routine name too long";
		}
		
		$imin = $_POST['imin'];
		if ($imin != NULL && ($imin > 10 || $imin < 1)) {
			$anyerrors = true;
			$iminerror = "Intensity is an int between 1 and 10";
		}
		
		$imax = $_POST['imax'];
		if ($imax != NULL && ($imax > 10 || $imax < 1)) {
			$anyerrors = true;
			$imaxerror = "Intensity is an int between 1 and 10";
		}
		
		$smin = $_POST['smin'];
		if ($smin != NULL && $smin < 1) {
			$anyerrors = true;
			$sminerror = "Sets is a positive int";
		}
		
		$smax = $_POST['smax'];
		if ($smax != NULL && $smax < 1) {
			$anyerrors = true;
			$smaxerror = "Sets is a positive int";
		}
		
		$rmin = $_POST['rmin'];
		if ($rmin != NULL && $rmin < 1) {
			$anyerrors = true;
			$rminerror = "Reps is a positive int";
		}
		
		$rmax = $_POST['rmax'];
		if ($rmax != NULL && $rmax < 1) {
			$anyerrors = true;
			$rmaxerror = "Reps is a positive int";
		}
		
		$ename = htmlspecialchars($_POST['ename'], ENT_QUOTES);
		if (strlen($ename) > 80) {
			$anyerrors = true;
			$enameerror = "Exercise name too long";
		}
		
		$bpart = htmlspecialchars($_POST['bpart'], ENT_QUOTES);
		if (strlen($bpart) > 20) {
			$anyerrors = true;
			$bparterror = "Body part too long";
		}
		
		if (!$anyerrors) {
			$url = "Location: selectroutine.php?mid=$mid&";
			if ($rname != "" && $rname != NULL) $url .= "rname=$rname&";
			if ($imin != NULL) $url .= "imin=$imin&";
			if ($imax != NULL) $url .= "imax=$imax&";
			if ($smin != NULL) $url .= "smin=$smin&";
			if ($smax != NULL) $url .= "smax=$smax&";
			if ($rmin != NULL) $url .= "rmin=$rmin&";
			if ($rmax != NULL) $url .= "rmax=$rmax&";
			if ($ename != "" && $ename != NULL) $url .= "ename=$ename&";
			if ($bpart != "" && $bpart != NULL) $url .= "bpart=$bpart&";
			$url = substr($url, 0, -1);
			
			header($url);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> Query routines </title>
	<link rel="stylesheet" href="forms.css">
	<link rel="stylesheet" href="subforms.css">
</head>
<body>
	<h3> Specify routines you would like to see</h3><br>
	<form method=post>
		<label> Routine Name contains: </label><span style="color:red;"><?php echo $rnameerror ?></span><input type="text" name="rname" value="<?php $rname ?>" placeholder="Any"><br><br>
		<label> Intensity minimum: </label><span style="color:red;"><?php echo $iminerror ?></span><input type="number" name="imin" value=<?php $imin ?> placeholder="Any"><br><br>
		<label> Intensity maximum: </label><span style="color:red;"><?php echo $imaxerror ?></span><input type="number" name="imax" value=<?php $imax ?> placeholder="Any"><br><br>
		<label> Min sets: </label><span style="color:red;"><?php echo $sminerror ?></span><input type="number" name="smin" value=<?php $smin ?> placeholder="Any"><br><br>
		<label> Max sets: </label><span style="color:red;"><?php echo $smaxerror ?></span><input type="number" name="smax" value=<?php $smax ?> placeholder="Any"><br><br>
		<label> Min reps: </label><span style="color:red;"><?php echo $rminerror ?></span><input type="number" name="rmin" value=<?php $rmin ?> placeholder="Any"><br><br>
		<label> Max reps: </label><span style="color:red;"><?php echo $rmaxerror ?></span><input type="number" name="rmax" value=<?php $rmax ?> placeholder="Any"><br><br>
		<label> Exercise name contains: </label><span style="color:red;"><?php echo $enameerror ?></span><input type="text" name="ename" value="<?php $ename ?>" placeholder="Any"><br><br>
		<label> Body part: </label><span style="color:red;"><?php echo $bparterror ?></span><input type="text" name="bpart" value="<?php $bpart ?>" placeholder="Any"><br><br>
		<button type=submit> Search </button>
	</form>
</body>
</html>